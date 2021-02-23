<?php

require_once('MysqlDB.class.php');
$db_conf = [
	'dsn'=>"mysql:host=192.168.1.132;dbname=warehouse_shipping",
	'user'=>"cuteman",
	'password'=>"QW@W#RSS33#E#",
	'charset' => 'utf8'
];

$limit = 100;
$offset = 0;

$db = new MysqlDB($db_conf);
$db->exec("update warehouse_goods set jingxiaoshangchengbenjia = 0, management_fee = 0 where company_id in (58, 515);");
while(1) {
	$data = $db->getAll("select goods_id, company_id from warehouse_goods where company_id not in (58, 515) and put_in_type <> 5 and is_on_sale not in (12,7,8,9) order by goods_id desc limit {$offset},{$limit};");
	$num = 0;
	$batch_sql = '';
	foreach($data as $val) {
		$num++;
		
		// 查找最新有效的P单
		$goods_id = $val['goods_id'];
		$company_id = $val['company_id'];
		
		$p_sql = 
"select ifnull(g.shijia, 0) + ifnull(g.management_fee, 0) as y_chengbenjia, ifnull(g.management_fee, 0) as fee from warehouse_bill_goods g
inner join warehouse_bill b on b.id = g.bill_id
where b.bill_type ='P' and b.bill_status in (2,4) and b.to_company_id = '{$company_id}' AND g.goods_id ='{$goods_id}' order by b.id desc limit 1;";
	    $p_row = $db->getRow($p_sql);
		if (empty($p_row)) {
			$p_sql = 
"select ifnull(g.shijia, 0) + ifnull(g.management_fee, 0) as y_chengbenjia, ifnull(g.management_fee, 0) as fee from warehouse_bill_goods g
inner join warehouse_bill b on b.id = g.bill_id
where b.bill_type ='P' and b.bill_status in (2,4) and g.goods_id ='{$goods_id}' order by b.id desc limit 1;";
			$p_row = $db->getRow($p_sql);
		}
		
		if (empty($p_row)) {
			$p_sql = 
"select ifnull(g.shijia, 0) as y_chengbenjia, 0 as fee from warehouse_bill_goods_pre_2016 g
inner join warehouse_bill_pre_2016 b on b.id = g.bill_id
where b.bill_type ='P' and b.bill_status in (2,4) and g.goods_id ='{$goods_id}' order by b.id desc limit 1;";
			$p_row = $db->getRow($p_sql);
		}
		
		if (!empty($p_row)) {
			$batch_sql .= "update warehouse_goods set jingxiaoshangchengbenjia = {$p_row['y_chengbenjia']}, management_fee = {$p_row['fee']} where goods_id = '{$goods_id}';";
		}
		
		if ($num % 50 == 0) {
			$db->exec($batch_sql);
			$batch_sql = '';
		}
		
		echo "offset:{$offset}, goods_id:{$goods_id} !";
	}
	
	if ($num < 100) {
		if ($batch_sql != '') {
			$db->exec($batch_sql);
		}
		break;
	} else {
		$offset += $num;
	}
}


?>