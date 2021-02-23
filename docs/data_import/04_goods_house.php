<?php
/**
 *  -------------------------------------------------
 * 文件说明
 * @file        : 04_goods_house.php
 * @author      : yangxt <yangxiaotong@163.com>
 * @version     : 1.0
 *  -------------------------------------------------
*/
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require_once('MysqlDB.class.php');
set_time_limit(0);
ini_set('memory_limit','2000M');

$conf = [
	'dsn'=>"mysql:host=192.168.1.93;dbname=warehouse_shipping;",
	'user'=>"cuteman",
	'password'=>"QW@W#RSS33#E#",
	'charset'=>'utf8'
];

$db = new MysqlDB($conf);

$sql = "SELECT w.`goods_id`,w.`warehouse_id` AS `w_house`,g.`warehouse_id` AS `g_house` FROM `warehouse_goods` AS `w`,`goods_warehouse` AS `g` WHERE w.`goods_id` = g.`good_id` HAVING `w_house` <> `g_house`";
$data = $db->getAll($sql);
// print_r($data[0]);exit;

$sql = "UPDATE goods_warehouse SET warehouse_id = ? WHERE good_id = ?";
$stmt = $db->prepare($sql);
$s=0;$l=0;
foreach ($data as $row) {
	$do = array();
	$do[0] = $row['w_house'];
	$do[1] = $row['goods_id'];
	$res = $stmt->execute($do);
	//$stmt->debugDumpParams();
	if($res){
		$s++;
		echo $s."SUCCESS UPDATE `goods_warehouse` SET warehouse_id = ".$row['w_house']." WHERE `goods_id` = ".$row['goods_id']."\r\n";
	}else{
		$l++;
		echo "LOSE";
		file_put_contents(__DIR__."/log/update/G_house_".date('Ymd_H').".log",$row['goods_id']."\r\n",FILE_APPEND);
	}
}
echo iconv('UTF-8', 'GBK', "---===THE MISSION END===---\r\n");
echo iconv('UTF-8', 'GBK', "SUCCESS UPDATE ".$s." LINE \r\n");
echo iconv('UTF-8', 'GBK', "LOSE UPDATE ".$l." LINE \r\n");











?>