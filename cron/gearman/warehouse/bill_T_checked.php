<?php

function on_bill_T_checked($data, $db) {

	if (!isset($data['bill_id']) || empty($data['bill_id'])) return false;
	
	$bill_id = $data['bill_id'];
	if ($bill_id > 0) {
		echo 'start processing T bill:'.$bill_id.PHP_EOL;
		$db->exec(
"insert into goods_io(goods_id,warehouse_id,in_time,birth_time, in_bill_no) 
select wbg.goods_id, b.to_warehouse_id as warehouse_id, b.check_time as in_time, g.addtime as birth_time, b.bill_no from warehouse_bill_goods wbg 
INNER JOIN warehouse_bill b on b.id = wbg.bill_id and b.bill_type ='T'
inner join warehouse_goods g on g.goods_id = wbg.goods_id
where b.id = {$bill_id};");
	}
}

?>