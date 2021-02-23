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
 	$newdb = new MysqlDB($new_conf);
 	$olddb = new MysqlDB($old_conf);

	$content = "货号,新系统单据,单据类型,新系统单据时间,老系统单据,单据类型,老系统单据时间,新系统货品状态,老系统货品状态\r\n";
 	//	$arr = file(__DIR__."/goods_log/goods.txt");
	$sql = "select goods_id from warehouse_goods where is_on_sale != 100";
	$arr = $newdb->getAll($sql);
	unlink(__DIR__."/goods_log/____goods.csv");
	file_put_contents(__DIR__."/goods_log/____goods.csv",$content,FILE_APPEND);
	foreach($arr as $key => $val)
	{
		$con = "";
		$sql = "SELECT `b`.`goods_id`,a.bill_type,a.`bill_no`,a.`bill_status`,a.create_time FROM `warehouse_bill` AS `a`,`warehouse_bill_goods` AS `b`  WHERE `a`.`id` = `b`.`bill_id`  and `a`.`bill_status`  in(1,2) and b.goods_id = ".$val['goods_id']." order by a.create_time desc limit 1 ";
		$newarr = $newdb->getRow($sql);
		$sql = "SELECT is_on_sale,goods_id from warehouse_goods where goods_id = ".$val['goods_id'];
		$newS = $newdb->getRow($sql);

		$sql = "SELECT `b`.`goods_id`,a.type,concat(a.type,a.order_id) as order_no,a.status,a.addtime FROM `jxc_order` AS `a`,`jxc_order_goods` AS `b`  WHERE `a`.`order_id` = `b`.`order_id`  and `a`.`status`  in(1,2) and b.goods_id = ".$val['goods_id']." order by a.addtime desc limit 1 ";
		$oldarr = $olddb->getRow($sql);
		$sql = "SELECT is_on_sale,goods_id from jxc_goods where goods_id = ".$val['goods_id'];
		$oldS = $olddb->getRow($sql);
		if(($oldS['is_on_sale'] == 100 && $newS['is_on_sale'] == 100) || ($oldS['is_on_sale'] == 100 && $newS['is_on_sale'] == '')|| ($oldS['is_on_sale'] != 100 && $newS['is_on_sale'] != 100)){

			$con .= $newS['goods_id']. ",";
			$con .= $newarr['bill_no'] . ",";
			$con .= $newarr['bill_type'].$newarr['bill_status'] . ",";
			$con .= $newarr['create_time'] . ",";
			$con .= $oldarr['order_no'] . ",";
			$con .= $oldarr['type'].$oldarr['status'] . ",";
			$con .= $oldarr['addtime'] . ",";
			$con .= $newStatus[$newS['is_on_sale']]. ",";
			$con .= $oldStatus[$oldS['is_on_sale']]. "\n";
			file_put_contents(__DIR__."/goods_log/____goods.csv",$con,FILE_APPEND);
		}
		echo $key.'-------------'.$con."\n";

	}
echo '------------over';exit;

 ?>