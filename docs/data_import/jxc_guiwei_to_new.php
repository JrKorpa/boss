<?php
/**
 *  -------------------------------------------------
 * 文件说明	将jxc的柜位统计 写入新系统
 * @file		: jxc_guiwei_to_new.php.php
 * @date 		: 2015-6-4 11:17:31
 * @author		: hulichao
 *  -------------------------------------------------
*/

	//初始化数据
	header("Content-type:text/html;charset=utf-8");
	date_default_timezone_set('Asia/Shanghai');
	require_once('MysqlDB.class.php');
	set_time_limit(0);
	ini_set('memory_limit','2000M');

	//连接数据库
	$conf_warehouse = [
		'dsn'     => "mysql:host=192.168.1.93;dbname=warehouse_shipping;",
		'user'    => "cuteman",
		'password'=> "QW@W#RSS33#E#",
		'charset' => 'utf8'
	];
	$conf_jxc = [
		'dsn'     => "mysql:host=192.168.1.79;dbname=jxc;",
		'user'    => "root",
		'password'=> "zUN5IDtRF5R@",
		'charset' => 'utf8'
	];
	$db_new = new MysqlDB($conf_warehouse);
	$db_old = new MysqlDB($conf_jxc);

	echo 'GAMES START >>>>>>';

	/************x 逻辑代码 x***********/

	//获取进销存的柜位列表
	$sql = "SELECT `a`.`goods_id`, `a`.`tmp_sn`, `b`.`wh_name`, `b`.`wh_id`, `b`.`p_id` FROM `jxc_goods` AS `a`, `jxc_warehouse` AS `b` WHERE `a`.`warehouse` = `b`.`wh_id` GROUP BY `a`.`tmp_sn`";
	$data_old = $db_old->getAll($sql);

	//获取新系统的总公司的柜位信息
	// $sql = "SELECT `a`.`warehouse_id`,`a`.`box_sn` FROM `warehouse_box` AS `a` LEFT JOIN `warehouse_rel` AS `b` ON `a`.`warehouse_id` = `b`.`warehouse_id` WHERE `b`.`company_id` = 58";
	$sql = "SELECT `a`.`warehouse_id`,`a`.`box_sn` FROM `warehouse_box` AS `a` LEFT JOIN `warehouse_rel` AS `b` ON `a`.`warehouse_id` = `b`.`warehouse_id`";
	$data_new = $db_new->getAll($sql);

	$new_box = array_column($data_new, 'box_sn');

	//写入warehouse_box
	$time = date('Y-m-d H:i:s');
	$sth = $db_new->prepare ( "INSERT INTO `warehouse_box` (`warehouse_id` , `box_sn` , `create_name` , `create_time` , `info`) VALUES (? , ? , 'SYSTEM_PANDIAN' , '{$time}' , '盘点洗数据')");

	//获取 老系统与 新系统 的柜位差集
	foreach($data_old AS $val1){
		//获取老系统 与 新系统 的柜位差集
		if( !in_array(trim($val1['tmp_sn']), $new_box) ){
			$error = "新系统没有，老系统有的柜位 : {$val1['tmp_sn']} ||| 该柜位在老系统：{$val1['wh_name']}({$val1['wh_id']})\r\n";
			echo $error;
			file_put_contents(__DIR__."/log/hlc".date("Y-m-d").".txt",$error,FILE_APPEND);

			//将柜位差集写入warehouse_box表
			$res = $sth->execute(array($val1['wh_id'] , $val1['tmp_sn']));
			if(!$res){
				$error = "write warehouse_box fall。柜位{$val1['tmp_sn']} / 仓库 {$val1['wh_name']}({$val1['wh_id']})\r\n";
				file_put_contents(__DIR__."/log/hlc_error".date("Y-m-d").".txt",$error,FILE_APPEND);
			}
			$error = "write warehouse_box success。柜位{$val1['tmp_sn']} / 仓库 {$val1['wh_name']}({$val1['wh_id']})\r\n";
			file_put_contents(__DIR__."/log/hlc_success".date("Y-m-d").".txt",$error,FILE_APPEND);
		}
	}

	echo 'GAMES OVER..........';

?> 