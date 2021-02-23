<?php

function zhanting_on_refresh_3rd_pifajia($data, $db) {
		
	if (!isset($data['goods_id']) || empty($data['goods_id'])) return false;
	
	$goods_id = $data['goods_id'];

	$p_sql = 
"select ifnull(g.shijia, 0) + ifnull(g.management_fee, 0) as y_chengbenjia, ifnull(g.management_fee, 0) as fee from warehouse_bill_goods g
inner join warehouse_bill b on b.id = g.bill_id
inner join warehouse_goods w on w.goods_id = g.goods_id
where b.bill_type ='P' and b.bill_status in (2,4) and b.to_company_id = w.company_id AND g.goods_id ='{$goods_id}' order by b.id desc limit 1;";
	$p_row = $db->getRow($p_sql);
	
	if (!empty($p_row)) {
		$sql = "update warehouse_goods set jingxiaoshangchengbenjia = '{$p_row['y_chengbenjia']}', management_fee = '{$p_row['fee']}' where goods_id = '{$goods_id}';";
		$db->exec($sql);
	}
}

?>