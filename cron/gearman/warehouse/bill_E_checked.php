<?php

function on_bill_E_checked($data, $db) {

	if (!isset($data['bill_id']) || empty($data['bill_id'])) return false;
	
	$bill_id = $data['bill_id'];
	if ($bill_id > 0) {
		echo 'start processing E bill:'.$bill_id.PHP_EOL;
		$db->exec(
"update goods_io g inner join warehouse_goods w on w.goods_id = g.goods_id and w.warehouse_id = g.warehouse_id
inner join (
	select wg.goods_id, b.check_time, b.bill_no from warehouse_bill b  inner join warehouse_bill_goods wg on wg.bill_id = b.id and b.bill_type ='E' 
	where b.id = {$bill_id}
) d on d.goods_id = g.goods_id
set g.out_time = d.check_time, g.out_bill_no = d.bill_no where g.out_time is null;");
	}
}

?>