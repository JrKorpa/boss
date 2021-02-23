<?php
/**
 *  -------------------------------------------------
 * 文件说明		给没有柜位信息的货品生成默认柜位
 * @file		:
 * @date 		: 2015/5/21
 * @author		: hlc
 *  -------------------------------------------------
*/
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require_once('MysqlDB.class.php');
set_time_limit(0);
ini_set('memory_limit','2000M');

//仓储数据库
/*$conf_warehouse = [
	'dsn'     => "mysql:host=192.168.1.63;dbname=warehouse_shipping;",
	'user'    => "yangfuyou",
	'password'=> "yangfuyou1q2w3e",
	'charset' => 'utf8'
	'dsn'     => "mysql:host=127.0.0.1;dbname=warehouse_shipping;",
	'user'    => "root",
	'password'=> "root",
	'charset' => 'utf8'
];*/

$conf_warehouse = [
	'dsn'=>"mysql:host=192.168.1.93;dbname=warehouse_shipping",
	'user'=>"cuteman",
	'password'=>"QW@W#RSS33#E#",
	'charset' => 'utf8'
];
$db = new MysqlDB($conf_warehouse);

$data = array();
/*
获取差集 方法1
//1、获取库存状态的货品信息
$sql = "SELECT `goods_id` FROM `warehouse_goods` WHERE `is_on_sale` = 2";
$data1 = $db->getAll($sql);

//2 获取goods_warehosue 信息
$sql = "SELECT `good_id` FROM `goods_warehouse`";
$data2 = $db->getAll($sql);

$data = array_diff($data1, $data2);*/

// 获取差集方法2 （效率比较低）
/*$sql = "SELECT `goods_id` FROM `warehouse_goods` WHERE `is_on_sale` != 1 AND `goods_id` NOT IN (SELECT `good_id` FROM `goods_warehouse`)";
$data = $db->getAll($sql);*/

//获取差集方法3
$sql = "SELECT `goods_id` FROM `warehouse_goods` WHERE `is_on_sale` != 1";		//收货中 货 就不管啦
$data1 = $db->getAll($sql);
$i = 0;
foreach($data1 as $kk => $vv){
	$sql = "SELECT `good_id` FROM `goods_warehouse` WHERE `good_id` = '{$vv['goods_id']}'";
	$ex = $db->getOne($sql);
	if(!$ex){
		++$i;
		$data[]['goods_id'] = $vv['goods_id'];
		echo 'num:'.$i."||| goods_id:{$vv['goods_id']}\r\n";
	}
}

file_put_contents(__DIR__."/log/insert/no_box_goods_num".date('Ymd_H').".log","没有柜位信息的货品数量：".$i."\r\n",FILE_APPEND);		//统计没有柜位信息的数量

if(empty($data)){
	echo '所有的货品都有了柜位信息.........';
	die;
}

$user = 'SYSTEM';
$time = date('Y-m-d H:i:s' , time());

//3 自动生成默认柜位
foreach ($data as $key=>$val)
{
	file_put_contents(__DIR__."/log/insert/no_box_goods".date('Ymd_H').".log",$val['goods_id']."\r\n",FILE_APPEND);		//统计没有柜位信息的货品
	//1、检测是货品否有柜位信息
	$sql = "SELECT `box_id` FROM `goods_warehouse` WHERE `good_id` = '{$val['goods_id']}' LIMIT 1";
	$exists = $db->getOne($sql);
	if($exists){
		echo "{$val['goods_id']}跳过\r\n";
		file_put_contents(__DIR__."/log/insert/exsis_box_goods".date('Ymd_H').".log",$val['goods_id']."\r\n",FILE_APPEND);		//记录已经有柜位信息的货品
		continue;	//如果存在goods_warehouse 信息 ，则跳过不管
	}

	//2、获取货品所在仓库
	$sql = "SELECT `warehouse_id` FROM `warehouse_goods` WHERE `goods_id` = '{$val['goods_id']}'";
	$warehouse_id = $db->getOne($sql);
	if(!$warehouse_id){
		echo "{$val['goods_id']}在warehouse_goods中没有存 warehouse_id 字段"."\r\n";
		file_put_contents(__DIR__."/log/insert/not_warehouse_id_goods_".date('Ymd_H').".log","{$val['goods_id']}在warehouse_goods中没有存 warehouse_id 字段"."\r\n",FILE_APPEND);
		continue;
	}

	//3、获取货品所在仓库的默认柜位
	$sql = "SELECT `id` FROM `warehouse_box` WHERE `warehouse_id` = {$warehouse_id} AND `box_sn` = '0-00-0-0'";
	$box_id = $db->getOne($sql);

	//4、仓库不存在默认柜位，那么就自动生成一个
	if(!$box_id)
	{
		$sql = "INSERT INTO `warehouse_box` (`warehouse_id` , `box_sn` , `create_name` , `create_time` , `info`) VALUES ({$warehouse_id} , '0-00-0-0' , '{$user}' , '{$time}' , '系统制单自动创建默认柜位')";
		$ok = $db->query($sql);
		if(!$ok)
		{
			$error = "warehouse_id:{$warehouse_id} 创建默认柜位失败";
			echo $error."\r\n";
			file_put_contents(__DIR__."/log/insert/create_box_fall_".date('Ymd_H').".log",$error.'______'.$sql."\r\n",FILE_APPEND);
		}
		$box_id = $db->insertId();
	}

	$sql = "INSERT INTO `goods_warehouse` (`good_id` , `warehouse_id` , `box_id` , `add_time` , `create_time` , `create_user`) VALUES ('{$val['goods_id']}' , {$warehouse_id} , {$box_id} , '{$time}' , '{$time}' , '{$user}')";
	$ok2= $db->query($sql);
	if(!$ok2)
	{
		$error = "{$val['goods_id']} INSERT goods_warehosue FALSE";
		echo $error.'\r\n';
		file_put_contents(__DIR__."/log/insert/insert_goods_warehouse_fall_".date('Ymd_H').".log",$error.'______'.$sql."\r\n",FILE_APPEND);

	}
	echo "{$val['goods_id']} INSERT goods_warehosue  SUCCESS\r\n";

}
echo 'ok....';

?>