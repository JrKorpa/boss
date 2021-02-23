<?php 
/**
 *  -------------------------------------------------
 * 文件说明	将jxc的货绑定到柜位上 , (盘点时 由于前期导数据，未将货品与柜位信息导入过来，导致盘点时总是盘盈。现在对照老系统，将老系统的柜位信息，补录到新系统中来)
 * @file		: bing_goods_box.php
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
/*	$conf_warehouse = [
		'dsn'     => "mysql:host=127.0.0.1;dbname=warehouse_shipping;",
		'user'    => "root",
		'password'=> "root",
		'charset' => 'utf8'
	];
	$conf_jxc = [
		'dsn'     => "mysql:host=127.0.0.1;dbname=jxc;",
		'user'    => "root",
		'password'=> "root",
		'charset' => 'utf8'
	];*/
	$db_new = new MysqlDB($conf_warehouse);
	$db_old = new MysqlDB($conf_jxc);

	echo 'GAMES START >>>>>>';

	/************x 逻辑代码 x***********/
	//获取jxc系统里含有柜位的货品
	$sql = "SELECT `goods_id` , `warehouse` , `tmp_sn` FROM `jxc_goods` WHERE `tmp_sn` != ''";
	$old_goods = $db_old->getAll($sql);
	$old_goods_id = array_column($old_goods , 'goods_id');

	//合并新数组，已货号为键值
	$old_goods = array_combine($old_goods_id, $old_goods);
	// echo '<pre>';print_r($old_goods);echo '</pre>';die;

	//获取总公司，库存状态的货品
	// $sql = "SELECT `warehouse_id`,`goods_id` FROM `warehouse_goods` WHERE `company_id` = 58 AND `is_on_sale` = 2";
	$sql = "SELECT `a`.`warehouse_id`, `a`.`warehouse` ,`a`.`goods_id`, `c`.`box_sn` FROM `warehouse_goods` AS `a` LEFT JOIN `goods_warehouse` AS `b` ON `a`.`goods_id` = `b`.`good_id` RIGHT JOIN `warehouse_box` AS `c` ON `b`.`box_id` = `c`.`id` WHERE `a`.`company_id` = 58 AND `a`.`is_on_sale` = 2";
	$new_goods = $db_new->getAll($sql);
	$new_goods_id = array_column($new_goods , 'goods_id');

	//合并新数组，已货号为键值
	$new_goods = array_combine($new_goods_id, $new_goods);

	//获取新老货品数据的交集	(一维索引数组)
	$intersect = array_intersect($new_goods_id, $old_goods_id);

	$sql = "UPDATE `goods_warehouse` SET `box_id` = ? WHERE `good_id` = ?";
	$shh = $db_new->prepare($sql);
	//对交集做处理，将没有绑定柜位的货品绑定到柜位上
	foreach($intersect AS $goods_id){
		//如果货品的仓库位置没有变化，还在原来的位置 就进行绑定（因为仓库位置一变，那就有可能不在是原来的柜位了）
		//并且新系统里 货是在默认柜位上的 （因为不在默认柜位，说明他有柜位，要么是导数据到过啦，要么是重新上架了，这个不能覆盖）
		if( ($old_goods[$goods_id]['warehouse'] == $new_goods[$goods_id]['warehouse_id']) && ($new_goods[$goods_id]['box_sn'] == '0-00-0-0') ){
			/*echo $goods_id."\r\n";
			file_put_contents(__DIR__.'/log/bing_goods_box1_list.txt' , $goods_id."\r\n", FILE_APPEND);		//统计那些库存状态的在默认柜位上的货*/
			$box_id = GetBoxIdByBoxSn($old_goods[$goods_id]['tmp_sn']);

			if( $shh->execute(array($box_id , $goods_id)) ){
				$error = "货号：{$goods_id} 被绑定在：{$new_goods[$goods_id]['warehouse']}(ID:{$old_goods[$goods_id]['warehouse']}) 的柜位：{$old_goods[$goods_id]['tmp_sn']}\r\n";
				file_put_contents(__DIR__.'/log/bing_goods_box1_ok.txt' , $error, FILE_APPEND);
			}else{
				$error = "货号:{$goods_id} 绑定失败..\r\n";
				file_put_contents(__DIR__.'/log/bing_goods_box1_no.txt' , $error, FILE_APPEND);
			}
		}

	}

/*	$fp = fopen(__DIR__.'/log/bing_goods_box1_ok.txt', 'r+');
	fwrite($fp, '如果货品的仓库位置没有变化，还在原来的位置 就进行绑定（因为仓库位置一变，那就有可能不在是原来的柜位了）');
	fclose($fp);*/

	function GetBoxIdByBoxSn($box_sn){
		global $db_new;
		$sql = "SELECT `id` FROM `warehouse_box` WHERE `box_sn` = '{$box_sn}'";
		$box_id = $db_new->getOne($sql);
		if($box_id){
			return $box_id;
		}else{
			$error = "在warehouse_box中查不到{$box_sn}\r\n";
			file_put_contents(__DIR__.'/log/bing_goods_box1_no_box.txt' , $error, FILE_APPEND);
		}
	}

	echo 'GAMES OVER.................................';
?>