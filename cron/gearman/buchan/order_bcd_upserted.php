<?php
global $lib_path;
require_once($lib_path);
		
function on_order_bcd_upserted($data, $db) {
	global $worker;
	$sys_scope = $data['sys_scope'];
	$det_id_arr = array();
	foreach($data['bc_infos'] as $bc_id => $det_id) {
		try {
			notify_bcd_upserted($bc_id, $det_id, $data, $db);
		} catch (Exception $ex) {
			echo $ex->getMessage().PHP_EOL;
			file_put_contents(__DIR__ . '/'.date('Ymd').'_error.log',  $data['msgId'].PHP_EOL, FILE_APPEND);
		} finally {
			$det_id_arr[] = $det_id;
		}
	}
    sleep(1);	
	$det_id_arr = implode(',', $det_id_arr);
	$order_ids = $db->getAll("select distinct order_id from app_order.app_order_details where id in ({$det_id_arr});");
	foreach($order_ids as $od) {
		$worker->dispatch("order", $sys_scope, array('event' => 'refresh_order',  'order_id' => $od['order_id']));
	}
}

function notify_bcd_upserted($bc_id, $order_detail_id, $data, $db) {
	
	$sys_scope = $data['sys_scope'];
	$certId_changed = isset($data['certId_changed']) ? $data['certId_changed'] : 0;
		
	// 1. 获取订单明细
	$detail = $db->getRow("select * from app_order.app_order_details where id = {$order_detail_id} and is_return = 0 ");
	if (empty($detail)) return;
	
	$db->exec("update app_order.app_order_details set bc_id = {$bc_id} where id = {$order_detail_id} and bc_id = 0");
	
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
		if (empty($style_potential_diy_list))  {		
			if (isset($data['reason'])) {
				$data['reason'] = $data['reason'].'，快速定制标志调整为【否】';
				$sql = "insert into product_opra_log(`bc_id`, `status`, `remark`, `uid`, `uname`, `time`) select id, `status`, '{$data['reason']}', {$data['userId']}, '{$data['userName']}', NOW() from product_info where id = {$bc_id} and is_quick_diy = 1;";
				$db->exec($sql);
			}
			$db->exec("update product_info set is_quick_diy = 0 where id = {$bc_id};"); //确保一定是0
			
			echo $bc_id . ': is_quick_diy = 0, break!'.PHP_EOL;
			return;
		}

		$xiangkou = preg_replace('/\\s+/', '', $detail['xiangkou']); 
		if (!empty($xiangkou)) {
			$xiangkou = floatval($xiangkou);
			foreach($style_potential_diy_list as $diy) {
				if (floatval($diy['xiangkou']) == $xiangkou) {
					$is_quick_diy = 1;
					break;
				}
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
			
			if (!empty($xiangkou)) {
				$xiangkou = floatval($xiangkou);
				foreach($style_potential_diy_list as $diy) {
					if (floatval($diy['xiangkou']) == $xiangkou) {
						$is_quick_diy = 1;
						break;
					}
				}	
			}
		}
	

	//}
	
	if ($is_quick_diy == 1) {
		// 结合证书号来判断
		if ($certId_changed == 0) {
			//$dia_type = get_diamond_type($detail['zhengshuhao'], $sys_scope, $db);  不能依赖裸钻列表的裸钻类型
			$dia_type = $db->getOne("select ifnull(diamond_type, 1) from product_info where id = {$bc_id};");
		} else {
			$dia_type = $data['diamond_type'];
		}
		
		if ($dia_type == 1) {
			$db->exec("update product_info set is_quick_diy = 1 where id = {$bc_id};");
			if ($certId_changed) {
				$sql = "insert into product_opra_log(`bc_id`, `status`, `remark`, `uid`, `uname`, `time`) values({$bc_id}, {$data['status']}, '钻石类型变更为【现货】，是否快速定制变更为【是】', {$data['userId']}, '{$data['userName']}', NOW());";
				$db->exec($sql);
			}
		} else {
			$is_quick_diy = 0;
		}
	} else {
		$is_quick_diy = 0;
	}
	
	if (!$is_quick_diy) {
		if (isset($data['reason'])) {
			$data['reason'] = $data['reason'].'，快速定制标志调整为【否】';
			$sql = "insert into product_opra_log(`bc_id`, `status`, `remark`, `uid`, `uname`, `time`) select id, `status`, '{$data['reason']}', {$data['userId']}, '{$data['userName']}', NOW() from product_info where id = {$bc_id} and is_quick_diy = 1;";
			$db->exec($sql);
		}
		$db->exec("update product_info set is_quick_diy = 0 where id = {$bc_id};"); //确保一定是0
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
