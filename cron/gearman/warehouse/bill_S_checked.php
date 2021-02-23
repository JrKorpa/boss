<?php

function on_bill_S_checked($data, $db) {

	if (!isset($data['order_sn']) || empty($data['order_sn'])) return false;
	
	$order_sn = $data['order_sn'];
	if ($order_sn) {
		echo 'start processing S bill:'.$order_sn.PHP_EOL;
		
		$bill_id = $db->getOne("select id from `warehouse_bill` where order_sn ='{$order_sn}' and bill_type='S';");
		if ($bill_id) {
			$db->exec(
"update goods_io g inner join warehouse_goods w on w.goods_id = g.goods_id and w.warehouse_id = g.warehouse_id
inner join (
	select wg.goods_id, b.check_time, b.bill_no from warehouse_bill b  inner join warehouse_bill_goods wg on wg.bill_id = b.id and b.bill_type ='S' 
	where b.id = {$bill_id}
) d on d.goods_id = g.goods_id
set g.out_time = d.check_time, g.out_bill_no = d.bill_no where g.out_time is null;");
		}
		
		//从待配货列表中删除该订单
		$db->exec("delete from order_distrib_todo where order_sn = '{$order_sn}';");
		
	}
}

?>