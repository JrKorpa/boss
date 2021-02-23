<?php

function zhanting_on_bill_H_closed($data, $db) {

	if (!isset($data['bill_id']) || empty($data['bill_id'])) return false;
	
	$bill_id = $data['bill_id'];
	/*
	if ($bill_id > 0) {
		echo 'start processing H bill:'.$bill_id.PHP_EOL;
		
		$to_company_id = $data['to_company_id'];
		if (in_array($to_company_id, array(58, 515))) {
							$db->exec("
				update warehouse_goods g inner join (
					SELECT g.goods_id, b.to_company_id from warehouse_bill b inner join warehouse_bill_goods g on g.bill_id = b.id
					where b.id = '{$bill_id}' and b.bill_type ='H' and b.bill_status = 2
				) h on h.goods_id = g.goods_id 
				set g.jingxiaoshangchengbenjia = 0, g.management_fee = 0
				where g.company_id = h.to_company_id;");
		} else {
				$goods_ids = preg_split('/,/', $data['goods_ids'], -1, PREG_SPLIT_NO_EMPTY);
				foreach($goods_ids as $gid) {
									try {
										$db->exec("
					update warehouse_goods g left join (
						SELECT g.goods_id, ifnull(g.shijia, 0) as yeta_chengbenjia, ifnull(g.management_fee, 0) as fee, b.from_company_id, b.to_company_id from warehouse_bill_goods g inner join warehouse_bill b on g.bill_id = b.id
						where g.goods_id = {$gid} and b.bill_type ='P' and b.to_company_id = '{$to_company_id}' and b.bill_status in (2, 4) order by b.create_time desc limit 1
					) p on p.goods_id = g.goods_id and p.to_company_id = g.company_id
					set g.jingxiaoshangchengbenjia = ifnull(p.yeta_chengbenjia, 0) + ifnull(p.fee, 0), g.management_fee = ifnull(p.fee, 0)
					where g.goods_id = {$gid};");
									} catch(Exception $ex) {
										file_put_contents(date('Ymd').'-bill_H_checked.log',  json_encode(array('goods_id'=> $gid, 'bill_id' => $bill_id, 'error'=> $ex->getMessage())).PHP_EOL, FILE_APPEND);
									}
				}
		}
		
			$db->exec(
		"insert into goods_io(goods_id,warehouse_id,in_time,birth_time,in_bill_no) 
		select wbg.goods_id, b.to_warehouse_id as warehouse_id, b.check_time as in_time, g.addtime as birth_time, b.bill_no as in_bill_no from warehouse_bill_goods wbg 
		INNER JOIN warehouse_bill b on b.id = wbg.bill_id and b.bill_type ='H'
		inner join warehouse_goods g on g.goods_id = wbg.goods_id
		where b.id = {$bill_id};");
	}
	*/
	//company_from
	$bill = $db->getRow("select bill_no,company_from from warehouse_bill where id=".$bill_id);
	if ($bill['company_from'] == 'ishop') {
	    // H单如果是由门店系统发起，则需要将审核状态通知门店
	    require_once __DIR__.'/../Worker.php';
	    global $ishop_job_server;
	    
	    echo 'try to send msg to ishop for H checked.'.PHP_EOL;
	    
	    $wk = new Worker($ishop_job_server,[],true);
	    $wk->dispatch('ishop', 'ishop', ['event' => 'sync_bill', 'bill_id' => $bill_id, 'bill_no' => $bill['bill_no'],'erp_check_resp'=>3 ]);
	    
	    echo 'msg has been send to ishop!'.PHP_EOL;
	}
}

?>
