<?php

function zhanting_on_bill_P_signed($data, $db) {

	if (!isset($data['bill_id']) || empty($data['bill_id'])) return false;
	
	$bill_id = $data['bill_id'];
	if ($bill_id > 0) {
		echo 'start processing P bill for jingxiaoshangchengbenjia:'.$bill_id.PHP_EOL;
		// TODO: 不是用字面的pifajia字段，而是shijia，从P单详情上看到是shijia字段表示批发价
		$db->exec("
update warehouse_goods g inner join (
	SELECT g.goods_id, ifnull(g.shijia, 0) as yeta_chengbenjia, ifnull(g.management_fee, 0) as fee, b.to_company_id from warehouse_bill b inner join warehouse_bill_goods g on g.bill_id = b.id
	where b.id = '{$bill_id}' and b.bill_type ='P' and b.bill_status in (2, 4)
) p on p.goods_id = g.goods_id 
set g.jingxiaoshangchengbenjia = p.yeta_chengbenjia + p.fee, g.management_fee = p.fee
where g.company_id = p.to_company_id and g.jingxiaoshangchengbenjia <> (p.yeta_chengbenjia + p.fee);");
	}
}


?>