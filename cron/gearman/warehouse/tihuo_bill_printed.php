<?php

function on_tihuo_bill_printed($data, $db) {

	if (!isset($data['order_ids']) || empty($data['order_ids'])) return false;
	
	$order_ids = $data['order_ids'];
	if ($order_ids) {
		echo 'start updating order distribution todo list '.PHP_EOL;
		
		//从待配货列表中更新提货打印标致
		$str = implode(',', $order_ids);
		$db->exec("update warehouse_shipping.order_distrib_todo set is_print_tihuo = 1 where order_id in {$str};");
		
	}
}

?>