<?php
	header("Content-type:text/html;charset=utf-8");
	date_default_timezone_set('Asia/Shanghai');
	require_once('MysqlDB.class.php');
	set_time_limit(0);
	ini_set('memory_limit','2000M');

	$conf = [
		/*'dsn'=>"mysql:host=203.130.44.199;dbname=app_order",
		'user'=>"cuteman",
		'password'=>"QW@W#RSS33#E#",
		'charset' => 'utf8'*/
		'dsn'     => "mysql:host=192.168.1.93;dbname=app_order;",
		'user'    => "cuteman",
		'password'=> "QW@W#RSS33#E#",
		'charset' => 'utf8'
	];
	$conf2 = [
		'dsn'     => "mysql:host=192.168.1.93;dbname=warehouse_shipping;",
		'user'    => "cuteman",
		'password'=> "QW@W#RSS33#E#",
		'charset' => 'utf8'
	];
	$db = new MysqlDB($conf);

	echo 'GAMES START GO!GO!GO! >>>>>>>>>>>>>>';

	$sql = "SELECT `a`.`order_sn` AS `订单号` ,
			CASE
			WHEN `a`.`send_good_status` = 1 THEN
				'未发货'
			WHEN `a`.`send_good_status` = 2 THEN
				'已发货'
			WHEN `a`.`send_good_status` = 3 THEN
				'收货确认'
			WHEN `a`.`send_good_status` = 4 THEN
				'允许发货'
			WHEN `a`.`send_good_status` = 5 THEN
				'已到店'
			END AS `发货状态`,
			case when `a`.`delivery_status` = 1 then '未配货'
			when `a`.`delivery_status` = 2 then '允许配货'
			when `a`.`delivery_status` = 3 then '配货中'
			WHEN `a`.`delivery_status` = 4 then '配货缺货'
			when `a`.`delivery_status` = 5 then '已配货'
			when `a`.`delivery_status` = 6 then '无效'
			 end AS `配货状态`,
			`a`.`check_time`
			FROM
				`base_order_info` AS `a`
			INNER JOIN `app_order_account` AS `b` ON `a`.`id` = `b`.`order_id`
			WHERE
				`a`.`order_status` = 2
			AND `a`.`delivery_status` IN (2, 3)
			AND `a`.`department_id` IN (
				1,2,3,4,5,6,7,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,30,31,32,34,36,38,39,40,41,42,43,44,45,46,47,48,49,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,73,74,75,76,77,78,79,81,82,83,84,85,86,88,89,91,93,94,95,96,97,98,99,100,101,102,103,104,105,106,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122
			)
			AND `a`.`apply_close` = '0'
			AND `a`.`order_status` = '2'
			AND `a`.`send_good_status` IN (2, 3, 5)";

	$data = $db->getAll($sql);		//获取允许配货的订单（并且他们的发货状态是 已发货/已到店）


	$db2 = new MysqlDB($conf2);
	$bill_m_th = $db2->prepare("SELECT `bill_no`, CASE WHEN `bill_status` = 1 THEN '保存' WHEN `bill_status` = 2 THEN '审核' END AS `status` FROM `warehouse_bill` WHERE `bill_type` = 'M' AND `bill_status` IN (1,2) AND `order_sn` = ? ORDER BY `id` DESC LIMIT 1");
	$bill_s_th = $db2->prepare("SELECT `bill_no`, CASE WHEN `bill_status` = 1 THEN '保存' WHEN `bill_status` = 2 THEN '审核' END AS `status` FROM `warehouse_bill` WHERE `bill_type` = 'S' AND `bill_status` IN (1,2) AND `order_sn` = ? ORDER BY `id` DESC LIMIT 1");
	$bill_d_th = $db2->prepare("SELECT `bill_no`, CASE WHEN `bill_status` = 1 THEN '保存' WHEN `bill_status` = 2 THEN '审核' END AS `status` FROM `warehouse_bill` WHERE `bill_type` = 'D' AND `bill_status` IN (1,2) AND `order_sn` = ?");
	foreach($data as $val){

		//获取订单之后做过的调拨单 （单号 + 状态）
		$bill_m_th->execute(array($val['订单号']));
		$res_m = $bill_m_th->fetch(PDO::FETCH_ASSOC);
		$error = "{$val['订单号']} 		{$val['发货状态']} 		{$val['配货状态']} 		";
		$error .= !empty($res_m) ? "调拨单:{$res_m['bill_no']}({$res_m['status']}) 		" : '无调拨单		' ;

		//获取订单之后的销售单 （单号+状态）
		$bill_s_th->execute(array($val['订单号']));
		$res_s = $bill_s_th->fetch(PDO::FETCH_ASSOC);
		$error .= !empty($res_s) ? "销售单:{$res_s['bill_no']}({$res_s['status']}) 		" : '无销售单		' ;

		//获取订单之后的销售单退货单 （单号+状态）
		$bill_d_th->execute(array($val['订单号']));
		$res_d = $bill_d_th->fetchAll(PDO::FETCH_ASSOC);
		if(!empty($res_d)){
			foreach($res_d AS $v){
				$error .= "销售退货单:{$v['bill_no']}({$v['status']}) | ";
			}
		}else{
			$error .=  '无销售单退货单' ;
		}


		echo $error .= "\r\n";
		file_put_contents(__DIR__."/tby.log", $error , FILE_APPEND);
	}
	echo 'GAMES OVERS >>>>>>>>>>>>>>';
 ?>