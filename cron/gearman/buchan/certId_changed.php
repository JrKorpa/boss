<?php
global $lib_path;
require_once($lib_path);

function on_certId_changed($data, $db) {

	$bc_id = $data['bc_id'];
	$cert_id = $data['zhengshuhao'];
	$sys_scope = $data['sys_scope'];
	$uid = $data['userId'];
	$uname = $data['userName'];
	$diamond_type = 1;
	
	$product_info = $db->getRow("select ifnull(is_quick_diy, 0) as is_quick_diy, `status`, from_type, p_id from product_info where id ={$bc_id} ");
	if (empty($product_info)) {
		return;
	}
	
	if (!empty($cert_id)) {
		$cert_id2 = preg_replace('/[a-zA-Z]{0,10}/', '', $cert_id);
		if ($cert_id == $cert_id2) {
			$sql ="select good_type from (
			select good_type, 1 as sort from front.diamond_info where cert_id='".$cert_id."'
			union
			select good_type, 2 as sort from front.diamond_info_all where cert_id='".$cert_id."' 
		) g order by g.sort asc limit 1;";
		} else {
			$sql ="select good_type from (
			select good_type, 1 as sort from front.diamond_info where cert_id='".$cert_id."' OR cert_id ='".$cert_id2."'
			union
			select good_type, 2 as sort from front.diamond_info_all where cert_id='".$cert_id."' OR cert_id ='".$cert_id2."'
		) g order by g.sort asc limit 1;";
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
	
	if ($product_info['from_type'] == 1) {
		$db->exec("update product_info set diamond_type = {$diamond_type} where id={$bc_id};");
		echo "bc {$bc_id} processed.".PHP_EOL;
		return;
	} 

	$is_quick_diy = $product_info['is_quick_diy'];
	if ($is_quick_diy == '1' && $diamond_type == 1) {
		//TODO: is_quick_diy was 1 and change diamond_type only.
		$db->exec("update product_info set diamond_type = {$diamond_type} where id={$bc_id};");
	} else if ($is_quick_diy == '1') {
		//TODO: set is_quick_diy = 0 and add log for this change;
		$sql = "update product_info set diamond_type = {$diamond_type}, is_quick_diy = 0 where id={$bc_id};
		insert into product_opra_log(`bc_id`, `status`, `remark`, `uid`, `uname`, `time`) values({$bc_id}, {$product_info['status']}, '钻石类型变更为【期货】，是否快速定制变更为【否】', {$uid}, '{$uname}', NOW());";
		$db->exec($sql);
	} else if ($diamond_type == 1) {
		//TODO: if diamond_type = 1, we should re-check the logic about is_quick_diy
		$db->exec("update product_info set diamond_type = {$diamond_type} where id={$bc_id};");
				
		$data['event'] = 'order_bcd_upserted';
		$data['bc_infos'] = array("{$bc_id}" => "{$product_info['p_id']}");
		$data['certId_changed'] = 1;
		$data['diamond_type'] = 1;
		$data['status'] = $product_info['status'];
		
		global $worker;
		$worker->dispatch("buchan", $sys_scope, $data);
	} else {
		$db->exec("update product_info set diamond_type = {$diamond_type} where id={$bc_id};");
	}
	
	echo "bc {$bc_id} processed.".PHP_EOL;
}


?>