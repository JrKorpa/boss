<?php
	/**
	 * 清洗已经销售的货品柜位信息，清洗到所在仓库的默认柜位上
	 */
	header("Content-type:text/html;charset=utf-8");
	date_default_timezone_set('Asia/Shanghai');
	require_once('MysqlDB.class.php');
	set_time_limit(0);
	ini_set('memory_limit','2000M');

	$conf = [
		'dsn'=>"mysql:host=203.130.44.199;dbname=warehouse_shipping",
		'user'=>"cuteman",
		'password'=>"QW@W#RSS33#E#",
		'charset' => 'utf8'
	];

	$db = new MysqlDB($conf);

	echo 'GAMES START GO!GO!GO! >>>>>>>>>>>>>>';

	//获取 box_id = 0 的数据 （获取已销售的货品）
	$sql = "SELECT `a`.`warehouse_id` AS `a_warehouse_id` , `a`.`goods_id` , `b`.`warehouse_id` AS `b_warehouse_id` , `b`.`box_id` FROM `warehouse_goods` AS `a` , `goods_warehouse` AS `b` WHERE `a`.`goods_id` = `b`.`good_id` AND `b`.`box_id` = 0 AND `a`.`is_on_sale` = 3";
	$data = $db->getAll($sql);

	//获取仓库的默认柜位
	$sth = $db->prepare("SELECT `id` FROM `warehouse_box` WHERE `box_sn` = '0-00-0-0' AND `warehouse_id` = ? ");

	//修改货品的柜位信息 （清洗条件：已销售的货品）
	$uth = $db->prepare("UPDATE `warehouse_goods` AS `a` INNER JOIN `goods_warehouse` AS `b` ON `a`.`goods_id` = `b`.`good_id` SET `a`.`box_sn` = '0-00-0-0' , `b`.`box_id` = ? WHERE `a`.`goods_id` = ? AND `a`.`is_on_sale` = 3");

	foreach($data AS $val){
		if($val['a_warehouse_id'] == $val['b_warehouse_id']){
			#将货洗到所在仓库的默认柜位上
			$sth->execute(array($val['a_warehouse_id']));
			$res = $sth->fetch(PDO::FETCH_ASSOC);		//获取货品所在仓库的默认柜位ID
			$uth->execute( array($res['id'] , $val['goods_id']) );		//清洗warehouse_goods 表的box_sn 字段、goods_warehouse 的 box_id 字段
			echo $error = "{$val['goods_id']} 清洗成功\r\n";
			file_put_contents(__DIR__."/log/xs_success.log" ,$error , FILE_APPEND );
		}else{
			echo $error = "{$val['goods_id']} warehouse_goods / goods_warehouse 的 warehouse_id({$val['a_warehouse_id']} : {$val['b_warehouse_id']}) 对不上\r\n";
			file_put_contents(__DIR__."/log/xs_error.log", $error , FILE_APPEND);
		}
	}



	echo "GAMES OVER~~~~~~~~~";
