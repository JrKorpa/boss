<?php 
/**
 *  -------------------------------------------------
 * 文件说明		更新商品状态  new_to_old
 * @file		: goods_new2old.php
 * @author		: yangxt <yangxiaotong@163.com>
 *  -------------------------------------------------
*/
header("Content-type:text/html;charset=utf-8");
require_once('ExportData2.1.class.php');
date_default_timezone_set('Asia/Shanghai');
set_time_limit(0);
ini_set('memory_limit','2000M');

$new_conf = [
	'dsn'=>"mysql:host=localhost;dbname=warehouse_shipping",
	'user'=>"root",
	'password'=>"yangxt",
];

$old_conf = [
	'dsn'=>"mysql:host=localhost;dbname=test",
	'user'=>"root",
	'password'=>"yangxt",
];

$new_db = new PDO($new_conf['dsn'], $new_conf['user'], $new_conf['password'],array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));
$old_db = new PDO($old_conf['dsn'], $old_conf['user'], $old_conf['password'],array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));

$sql = "SELECT `goods_id`,`goods_name`,`warehouse_id`,`warehouse`,`company`,`company_id`,`order_goods_id`,`is_on_sale` FROM `warehouse_goods` WHERE `is_on_sale` <> '2' AND `order_goods_id` <> '' AND `order_goods_id` <> '0'";
// print_r($sql);exit;
$obj = $new_db->query($sql);
$obj->setFetchMode(PDO::FETCH_ASSOC);
$data= $obj->fetchAll();
// echo '<pre>';print_r($data[0]);exit;
if(empty($data)){ 
	echo "NOT DATA TO UODATE !!!\r\n";exit;
}else{
	$s = 0; $l = 0;
	foreach ($data as $row) {
		// $sql = "SELECT `goods_id`,`is_on_sale`,`company_id`,`warehouse_id` from `jxc_goods` WHERE `goods_id` = '".$row['goods_id']."'";
		$sql = "SELECT `goods_id`,`is_on_sale` from `jxc_goods` WHERE `pass_status` = '1' AND `goods_id` = '1401109967'";
		// print_r($sql);exit;
		$obj = $old_db->query($sql);
		$obj->setFetchMode(PDO::FETCH_ASSOC);
		$res = $obj->fetch();
		if($res['is_on_sale'] != 2){
			$sql = "UPDATE `jxc_goods` SET `company` = '".$row['company_id']."',`warehouse`='".$row['warehouse_id']."',`is_on_sale` = '".$row['is_on_sale']."'";
			$sql .= ",`pass_status` = '2' WHERE `goods_id` = '".$row['goods_id']."'";
			//print_r($sql);exit;
			$req = $old_db->query($sql);
			if($req){
				$s++;
				echo iconv('UTF-8', 'GBK', "SUCCESS UPDATE `goods_id` = ".$row['goods_id']." --- ".$s."\r\n");
				}else{
				$l++;
				echo iconv('UTF-8', 'GBK', "LOSE UPDATE `goods_id` = ".$row['goods_id']." --- ".$l."\r\n");
				file_put_contents(__DIR__."/log/insert/new2old_goods".date('Ymd').".log",$sql."\r\n",FILE_APPEND);
			}
		}
	}
	file_put_contents(__DIR__."/log/insert/new2old_goods".date('Ymd').".log","\r\n---===THE MISSION END===---\r\n||||||||||||||||||||||||||||||||||||||||||||||\r\n||||||||||||||||||||||||||||||||||||||||||||||\r\n||||||||||||||||||||||||||||||||||||||||||||||\r\n\r\n\r\n",FILE_APPEND);
	//脚本执行完成
    echo iconv('UTF-8', 'GBK', "---===THE MISSION END===---\r\n");
    echo iconv('UTF-8', 'GBK', "SUCCESS UPDATE ".$s." LINE \r\n");
    echo iconv('UTF-8', 'GBK', "LOSE UPDATE ".$l." LINE \r\n");
}




?>