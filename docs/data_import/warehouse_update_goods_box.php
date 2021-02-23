<?php
	/**
	* 检测goods_warehouse 数据box_id 字段是否为 0.是的话 查出该仓库的默认柜位ID  回写回来。     胡立潮
 	*/
 	# 初始化数据
	header("Content-type:text/html;charset=utf-8");
	date_default_timezone_set('Asia/Shanghai');
	require_once('MysqlDB.class.php');
	set_time_limit(0);
	ini_set('memory_limit','2000M');

 	# 连接数据库
/* 	$conf_warehouse = [

		'dsn'     => "mysql:host=192.168.1.63;dbname=warehouse_shipping;",
		'user'    => "yangfuyou",
		'password'=> "yangfuyou1q2w3e",
		'charset' => 'utf8'
	];*/

	$conf_warehouse = [
		'dsn'=>"mysql:host=192.168.1.93;dbname=warehouse_shipping",
		'user'=>"cuteman",
		'password'=>"QW@W#RSS33#E#",
		'charset' => 'utf8'
	];

 	$db = new MysqlDB($conf_warehouse);

 	# 逻辑代码
 	//获取goods_warehouse 表里box_id = 0 的数据
 	$sql = "SELECT `id`, `box_id` , `good_id` FROM `goods_warehouse` WHERE `box_id` = 0";
 	$data = $db->getAll($sql);
 	$i = 0;
 	foreach($data as $val){
 		if($val['box_id'] == 0){
 			++$i;
 			//记录哪些货品的柜位id是o的货品
 			$error = "{$i} ||| {$val['good_id']} 的goods_warehouse 中box_id 为0\r\n";
 			echo $error;
 			file_put_contents(__DIR__."/log/update/box_zero_".date('Ymd_H').".log",$error,FILE_APPEND);

 			//获取该货品所在仓库的默认柜位 回写回来
 			$sql = "SELECT `warehouse_id` FROM `warehouse_goods` WHERE `goods_id`='{$val['good_id']}'";
 			$warehouse_id = $db->getOne($sql);
 			if(!$warehouse_id){
 				$error = "{$i} ||| {$val['good_id']} 的warehouse_goods 中warehouse_id 没有数据\r\n";
	 			echo $error;
	 			file_put_contents(__DIR__."/log/update/warehouse_zero_".date('Ymd_H').".log",$error,FILE_APPEND);
	 			continue;
 			}
 			//获取默认柜位ID
 			$sql = "SELECT `id` FROM `warehouse_box` WHERE `warehouse_id` = {$warehouse_id} AND `box_sn` = '0-00-0-0'";
 			$box_id = $db->getOne($sql);
 			if(!$box_id){
 				$error = "仓库id:{$warehouse_id} 的仓库 没有默认柜位\r\n";
	 			echo $error;
	 			file_put_contents(__DIR__."/log/update/warehouse_no_default_".date('Ymd_H').".log",$error,FILE_APPEND);
	 			continue;
 			}

 			//回写信息
 			$sql = "UPDATE `goods_warehouse` SET `warehouse_id` = {$warehouse_id} , `box_id` = {$box_id} WHERE `good_id` = '{$val['good_id']}'";
 			$ok = $db->query($sql);
 			if(!$ok){
 				$error = "{$i} ||| {$val['good_id']} 回写goods_warehouse表 box_id = {$box_id} / warehouse_id = {$warehouse_id} 字段失败。\r\n";
	 			echo $error;
	 			file_put_contents(__DIR__."/log/update/return_write_fall".date('Ymd_H').".log",$error,FILE_APPEND);
	 			continue;
 			}
 		}
 	}

 ?>