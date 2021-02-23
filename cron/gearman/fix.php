<?php

$db_conf = [
	'boss' => [
		'dsn'=>"mysql:host=192.168.1.192;dbname=kela_supplier",
		'user'=>"cuteman",
		'password'=>"QW@W#RSS33#E#",
		'charset' => 'utf8'
		],
	'zhanting' => [
		'dsn'=>"mysql:host=192.168.1.132;dbname=kela_supplier",
		'user'=>"cuteman",
		'password'=>"QW@W#RSS33#E#",
		'charset' => 'utf8'
	]	
];

include 'MysqlDB.class.php';

$boss_db = new MysqlDB($db_conf['zhanting']);

$rows = $boss_db->getAll("SELECT p.id, p.p_id from product_info p 
inner join app_order.app_order_details d on d.bc_id = p.id
where add_time >= '2017-04-01' and from_type = 2 and p.`status` in (2,3,4) and style_sn <> 'DIA'");

foreach($rows as $r) {
	notify_bcd_upserted($r['id'], $r['p_id'], array('sys_scope' => 'zhanting'), $boss_db);
}


function notify_bcd_upserted($bc_id, $order_detail_id, $data, $db) {
	
	$sys_scope = $data['sys_scope'];
	$certId_changed = isset($data['certId_changed']) ? $data['certId_changed'] : 0;
		
	// 1. 获取订单明细
	$detail = $db->getRow("select * from app_order.app_order_details where id = {$order_detail_id} and is_return = 0 ");
	if (empty($detail)) return;
	
	/*
	0. 如果款号是DIA，默认不处理，即默认为0；
	1. 如果有虚拟货号，则直接根据虚拟货号判断该货号是否支持快速定制；
	2. 如果1不满足，但有款号，则结合订单属性判断；	
	3. 上述逻辑后，如果"支持快速定制"，则结合裸钻的类型来做最终判断
	*/
	
	if ($detail['goods_sn'] == 'DIA') return;
	
	$is_quick_diy = 0;
	/*if (strpos($detail['goods_id'], '-') > 1) {
		// 按虚拟货号
		$is_quick_diy = $db->getOne("select ifnull(is_quick_diy, 0) from front.list_style_goods where goods_sn ='{$detail['goods_id']}'");
	} else {
	*/
		// 按款号+属性
		$goods_sn = preg_replace('/\\s+/', '', $detail['goods_sn']);
		$jinse = preg_replace('/\\s+/', '', $detail['jinse']);
		$caizhi = preg_replace('/\\s+/', '', $detail['caizhi']);
		$zhiquan = preg_replace('/\\s+/', '', $detail['zhiquan']); 
		
		$style_potential_diy_list = $db->getAll("select xiangkou from front.app_style_quickdiy where `status` = 1 and style_sn = '{$goods_sn}' and caizhi ='{$caizhi}' and caizhiyanse='{$jinse}' and zhiquan = '{$zhiquan}'; ");
		if (empty($style_potential_diy_list))  return;

		$xiangkou = preg_replace('/\\s+/', '', $detail['xiangkou']); 
		if (empty($xiangkou)) $xiangkou = 0;
		
		$xiangkou = floatval($xiangkou);
		foreach($style_potential_diy_list as $diy) {
			if (floatval($diy['xiangkou']) == $xiangkou) {
				$is_quick_diy = 1;
				break;
			}
		}
		
		if (!$is_quick_diy) {
			
			$carat = preg_replace('/\\s+/', '', $detail['cart']); 

			$matched = preg_match('/([0-9]+\.?[0-9]*)/', $carat, $matches);
			if ($matched) {
				$carat = floatval( $matches[1] );
			}

			$xiangkou_list = $db->getAll("select * from front.diy_xiangkou_config where style_sn = '{$goods_sn}'");
			foreach($xiangkou_list as $xk) {
				if (floatval($xk['carat_lower_limit']) <= $carat && $carat <= floatval($xk['carat_upper_limit'])) {
					$xiangkou = $xk['xiangkou'];
					break;
				}
			}
			
			if (empty($xiangkou)) $xiangkou = 0;
		}
		
		$xiangkou = floatval($xiangkou);
		foreach($style_potential_diy_list as $diy) {
			if (floatval($diy['xiangkou']) == $xiangkou) {
				$is_quick_diy = '1';
				break;
			}
		}
	//}
	
	if ($is_quick_diy == '1') {
		// 结合证书号来判断
		if ($certId_changed == 0) {
			$dia_type = get_diamond_type($detail['zhengshuhao'], $sys_scope, $db);
		} else {
			$dia_type = $data['diamond_type'];
		}
		
		if ($dia_type == 1) {
			$db->exec("insert into fix_diy(bc_id, value) values({$bc_id}, 1)");
		} else {
			$is_quick_diy = 0;
			$db->exec("insert into fix_diy(bc_id, value) values({$bc_id}, 0)");
		}
	} else {
		$is_quick_diy = 0;
		$db->exec("insert into fix_diy(bc_id, value) values({$bc_id}, 0)");
	}
	
	echo $bc_id . ': is_quick_diy ='.$is_quick_diy . PHP_EOL;
}

function get_diamond_type($cert_id, $sys_scope, $db) {
	$diamond_type = 1;
	if (!empty($cert_id)) {
		$cert_id2 = preg_replace('/[a-zA-Z]{0,10}/', '', $cert_id);
		if ($cert_id == $cert_id2) {
			$sql ="select good_type from front.diamond_info_all where cert_id='".$cert_id."'";
		} else {
			$sql ="select good_type from front.diamond_info_all where cert_id='".$cert_id."' OR cert_id ='".$cert_id2."'";
		}
		
		if ($sys_scope == 'zhanting') {
			global $db_conf;
			$boss_db = new MysqlDB($db_conf['boss']);
			$good_type = $boss_db->getOne($sql);
			$boss_db->dispose();
		} else {
			$good_type = $db->getOne($sql);
		}

		if ($good_type == 1) {
			$diamond_type = 1;
		} else {
			$diamond_type = 2;
		}
	}
	return $diamond_type;
}

?>
