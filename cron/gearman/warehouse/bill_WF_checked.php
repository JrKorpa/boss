<?php

function zhanting_on_bill_WF_checked($data, $db) {

	if (!isset($data['bill_id']) || empty($data['bill_id'])) return false;
	
	$bill_id = $data['bill_id'];
	if ($bill_id > 0) {
		echo 'start processing WF bill:'.$bill_id.PHP_EOL;
		
		$bill = $db->getRow("select bill_no, company_from, to_company_id from warehouse_bill where id = {$bill_id}");
		if (!$bill) return;
		
		$sync_to_ishop = false;
		if ($bill['company_from'] == 'ishop') {
		    // 如果单据由门店系统发起，则需要将审核状态同步到门店系统
		    $sync_to_ishop = true;   
		} else if (!empty($bill['to_company_id'])) {
		    // TODO：总部发起的WF单，根据出库公司过滤，则需要将该单同步到门店系统
		    $company_type = $db->getOne("select company_type from cuteframe.company where id=".$bill['to_company_id']);
		    $sync_to_ishop = in_array($company_type, ['3', '4']);
		}
		
		if ($sync_to_ishop) {
		    echo 'try to send msg to ishop.'.PHP_EOL;
		    
		    $payload = ['event' => 'sync_bill', 'bill_id' => $bill_id, 'bill_no' => $bill['bill_no']];
		    if ($bill['company_from'] == 'ishop') {
		        $payload['erp_checked'] = 1;
		    }
		    
		    require_once __DIR__.'/../Worker.php';
		    global $ishop_job_server;
		    
		    echo 'try to send msg to ishop for WF checked.'.PHP_EOL;
		    
		    $wk = new Worker($ishop_job_server,[],true);
		    
		    $wk->dispatch('ishop', 'ishop', $payload);
		    
		    echo 'msg has been send to ishop!'.PHP_EOL;
		}
	}
}

?>
