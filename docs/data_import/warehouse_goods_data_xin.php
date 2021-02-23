<?php
	/**
	* 获取货品的最后单据和新老状态---JUAN
 	*/
 	# 初始化数据
	header("Content-type:text/html;charset=utf-8");
	date_default_timezone_set('Asia/Shanghai');
	require_once('MysqlDB.class.php');
	set_time_limit(0);
	ini_set('memory_limit','2000M');

 	# 连接数据库

//新项目数据库
$new_conf = [
	'dsn'=>"mysql:host=192.168.1.93;dbname=warehouse_shipping",
	'user'=>"cuteman",
	'password'=>"QW@W#RSS33#E#",
		'charset' => 'utf8'
];

// 旧项目数据库
$old_conf = [
	'dsn'=>"mysql:host=192.168.1.79;dbname=jxc",
	'user'=>"root",
	'password'=>"zUN5IDtRF5R@",
		'charset' => 'utf8'
];

$newStatus = array(
	'' => '无',
	'100' => '锁定',
	'1'	=> '收货中',
	'2'	=> '库存',
	'3' => '已销售',
	'4' => '盘点中',
	'5' => '调拨中',
	'6' => '损益中',
	'7' => '已报损',
	'8' => '返厂中',
	'9' => '已返厂',
	'10' => '销售中',
	'11' => '退货中',
	'12' => '作废'
);

$oldStatus = array(
	'' => '无',
	'100' => '锁定',
	'0'	=> '收货中',
	'1'	=> '库存',
	'2'	=> '已销售',
	'3' => '转仓中',
	'4' => '盘点中',
	'5' => '销售中',
	'7' => '已返厂',
	'8' => '退货中',
	'9' => '返厂中',
	'10' => '作废',
	'11' => '损益中',
	'12' => '已报损'
);
//入库单据
$newType = array('M','T','L','D','H');
$oldType = array('F','T','L','B','H','Z');

 	$newdb = new MysqlDB($new_conf);
 	$olddb = new MysqlDB($old_conf);

	$content = "货号,系统单据,单据类型,创建时间,入库公司,入库仓,实际货品公司,实际货品仓库,货品状态\r\n";
 	$arr = file(__DIR__."/goods_log/goods.txt");

	file_put_contents(__DIR__."/goods_log/____goods.csv",$content,FILE_APPEND);
	foreach($arr as $key => $val)
	{
		$con = "";
		$sql = "SELECT `b`.`goods_id`,a.bill_type,a.`bill_no`,a.create_time,a.to_company_name,a.to_warehouse_name FROM `warehouse_bill` AS `a`,`warehouse_bill_goods` AS `b`  WHERE `a`.`id` = `b`.`bill_id`  and `a`.`bill_status` = 2 and a.bill_type in('M','T','L','D','H') and b.goods_id = ".$val." order by a.create_time desc limit 1 ";
		$newarr = $newdb->getRow($sql);

		$sql = "select goods_id,company,warehouse,is_on_sale from warehouse_goods where goods_id = ".$val;
		$g = $newdb->getRow($sql);


		$sql = "SELECT `b`.`goods_id`,a.type,concat(a.type,a.order_id) as order_no,a.addtime,a.to_company,a.to_warehouse FROM `jxc_order` AS `a`,`jxc_order_goods` AS `b`  WHERE `a`.`order_id` = `b`.`order_id`  and `a`.`status`= 2  and a.type in('F','T','L','B','H','Z') and b.goods_id = ".$val." order by a.addtime desc limit 1 ";
		$oldarr = $olddb->getRow($sql);

		//if(($newS == 2 && $oldS == 1) || (!$newS) || (!$oldS) || ($newS != 100 && ($oldS != 100 && $oldS != ''))){

		$con .= $g['goods_id'] . ",";
		if($newarr['create_time'] > $oldarr['addtime'])
		{

			$con .= $newarr['bill_no'] . ",";
			$con .= $newarr['bill_type'] . ",";
			$con .= $newarr['create_time'] . ",";
			$con .= $newarr['to_company_name'] . ",";
			$con .= $newarr['to_warehouse_name'] . ",";
		}else{
			$con .= $oldarr['order_no'] . ",";
			$con .= $oldarr['type'] . ",";
			$con .= $oldarr['addtime'] . ",";
			$con .= $oldarr['to_company'] . ",";
			$con .= $oldarr['to_warehouse'] . ",";
		}
			$con .= $g['company'] . ",";
			$con .= $g['warehouse'] . ",";
			$con .= $g['is_on_sale'] . "\n";
			file_put_contents(__DIR__."/goods_log/____dgoods.csv",$con,FILE_APPEND);
		//}
		echo $key.'-------------'.$con."\n";

	}
echo '------------over';exit;

 ?>