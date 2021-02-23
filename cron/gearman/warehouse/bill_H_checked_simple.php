<?php

function zhanting_on_bill_H_checked($data, $db) {

	if (!isset($data['bill_id']) || empty($data['bill_id'])) return false;
	
	$bill_id = $data['bill_id'];
	if ($bill_id > 0) {
		echo 'start processing H bill:'.$bill_id.PHP_EOL;
		// TODO: 不是用字面的pifajia字段，而是shijia，从P单详情上看到是shijia字段表示批发价
		$db->exec("
update warehouse_goods g inner join (
	SELECT g.goods_id, b.to_company_id from warehouse_bill b inner join warehouse_bill_goods g on g.bill_id = b.id
	where b.id = '{$bill_id}' and b.bill_type ='H' and b.bill_status = 2
) h on h.goods_id = g.goods_id 
set g.jingxiaoshangchengbenjia = 0, g.management_fee = 0
where g.company_id = h.to_company_id;");
	}
}

?>