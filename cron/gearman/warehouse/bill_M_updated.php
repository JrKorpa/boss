<?php

function on_bill_M_updated($data, $db) {

	if (!isset($data['bill_id']) || empty($data['bill_id'])) return false;
	
	$bill_id = $data['bill_id'];
	if ($bill_id > 0) {
		echo 'start processing M bill:'.$bill_id.PHP_EOL;
		$db->exec("
update warehouse_goods g inner join (
	SELECT g.goods_id from warehouse_bill b inner join warehouse_bill_goods g on g.bill_id = b.id
	where b.id = '{$bill_id}' and b.bill_type ='M' and b.bill_status = 1
) p on p.goods_id = g.goods_id
set g.is_on_sale = 5
where g.is_on_sale <> 5;");
	}
}

?>