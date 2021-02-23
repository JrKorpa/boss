<?php
/****
给没有默认柜位的仓库添加默认柜位
****/
	header("Content-type:text/html;charset=utf-8");
	date_default_timezone_set('Asia/Shanghai');
	require_once('MysqlDB.class.php');
	set_time_limit(0);
	ini_set('memory_limit','2000M');

	$conf = [
		'dsn'=>"mysql:host=192.168.1.93;dbname=warehouse_shipping",
		'user'=>"cuteman",
		'password'=>"QW@W#RSS33#E#",
		'charset' => 'utf8'
	];
	$db = new MysqlDB($conf);
	echo 'GAMES START GO!GO!GO! >>>>>>>>>>>>>>';
	//获取所有的仓库
	$sql = "SELECT `id`,`name` FROM `warehouse`";
	$variable = $db->getAll($sql);

	$box_th = $db->prepare("SELECT `id` FROM `warehouse_box` WHERE `box_sn` = '0-00-0-0' AND `warehouse_id` = ? ");
	$time = date('Y-m-d H:i:s');
	$flush_th = $db->prepare("INSERT INTO `warehouse_box` (`warehouse_id`,`box_sn`,`create_name`,`create_time`,`info`) VALUES (? , '0-00-0-0' , 'SYSTEM', '{$time}' , '洗数据')");
	$hit = 0;		//计数
	foreach ($variable as $key => $value) {
		$box_th->execute(array($value['id']));
		$data = $box_th->fetch(PDO::FETCH_ASSOC);
		if($data == false){
			echo ++$hit.' ';
			$res = $flush_th->execute(array($value['id']));
			if(!$res){
				//执行失败
				echo $error = "{$value['name']} (warehouse_id:{$value['id']}) 添加默认柜位失败\r\n";
				file_put_contents(__DIR__."/log/flush_box_error.log", $error , FILE_APPEND);
			}else{
				//执行成功
				$lastID = $db->insertId();
				echo $error = "{$value['name']} (warehouse_id:{$value['id']}) 添加默认柜位成功 （生成柜位ID：{$lastID}\r\n";
				file_put_contents(__DIR__."/log/flush_box_success.log", $error , FILE_APPEND);
			}
		}
	}

	echo 'GAMES OVER>>>>>>>>>>>>>>>>>>>>>>>>>>>';
?>