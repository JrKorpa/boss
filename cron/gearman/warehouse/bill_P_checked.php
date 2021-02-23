<?php

function zhanting_on_bill_P_checked($data, $db) {

	if (!isset($data['bill_id']) || empty($data['bill_id'])) return false;
	
	$bill_id = $data['bill_id'];
	if ($bill_id > 0) {
		echo 'start processing P bill:'.$bill_id.PHP_EOL;
		// TODO: 不是用字面的pifajia字段，而是shijia，从P单详情上看到是shijia字段表示批发价
		$db->exec("
update warehouse_goods g inner join (
	SELECT g.goods_id, ifnull(g.shijia, 0) as yeta_chengbenjia, ifnull(g.management_fee, 0) as fee, b.to_company_id from warehouse_bill b inner join warehouse_bill_goods g on g.bill_id = b.id
	where b.id = '{$bill_id}' and b.bill_type ='P' and b.bill_status in (2, 4)
) p on p.goods_id = g.goods_id 
set g.jingxiaoshangchengbenjia = p.yeta_chengbenjia + p.fee, g.management_fee = p.fee
where g.company_id = p.to_company_id;");

		$db->exec(
"update goods_io g inner join warehouse_goods w on w.goods_id = g.goods_id and w.warehouse_id = g.warehouse_id
inner join (
	select wg.goods_id, b.check_time, b.bill_no from warehouse_bill b inner join warehouse_bill_goods wg on wg.bill_id = b.id and b.bill_type ='P' 
	where b.id = {$bill_id}
) d on d.goods_id = g.goods_id
set g.out_time = d.check_time, g.out_bill_no = d.bill_no where g.out_time is null;");
		
		$bill = $db->getRow("select bill_no,to_company_id from warehouse_bill where id=".$bill_id);		
		if (empty($bill['to_company_id'])) return true;
		
	    // TODO：总部发起的P单，根据公司过滤，则需要将该单同步到门店系统
	    $company_type = $db->getOne("select company_type from cuteframe.company where id=".$bill['to_company_id']);
	    if (in_array($company_type, ['3', '4'])) {
		
    	    require_once __DIR__.'/../Worker.php';
    	    global $ishop_job_server;
    	    
    	    echo 'try to send msg to ishop for P checked.'.PHP_EOL;
    	    
    	    $wk = new Worker($ishop_job_server,[],true);
    	    $wk->dispatch('ishop', 'ishop', ['event' => 'sync_bill', 'bill_id' => $bill_id, 'bill_no' => $bill['bill_no'] ]);	
    	    
    	    echo 'msg has been send to ishop!'.PHP_EOL;
	    }
	}
}

function boss_on_bill_P_checked($data, $db) {
	if (!isset($data['bill_id']) || empty($data['bill_id'])) return false;
	
	$bill_id = $data['bill_id'];
	if ($bill_id > 0) {
		echo 'start processing P bill:'.$bill_id.PHP_EOL;
		
		$db->exec(
"update goods_io g inner join warehouse_goods w on w.goods_id = g.goods_id and w.warehouse_id = g.warehouse_id
inner join (
	select wg.goods_id, b.check_time, b.bill_no from warehouse_bill b  inner join warehouse_bill_goods wg on wg.bill_id = b.id and b.bill_type ='P' 
	where b.id = {$bill_id}
) d on d.goods_id = g.goods_id
set g.out_time = d.check_time, g.out_bill_no = d.bill_no where g.out_time is null;");
	}
}


?>
