<?php

function boss_on_refresh_pifajia($data, $db) {
	calc_pfj__($db, '', '', true);
}

function calc_pfj__($db, $cert_id, $from_ad, $force_all = false) {	
	
	if (!empty($cert_id)) {
		$sql = "select count(1) from front.diamond_info where cert_id = '{$cert_id}'; ";
	} else if (!empty($from_ad)) {
		$sql = "select count(1) from front.diamond_info where from_ad = '{$from_ad}'; ";
	} else {
		$sql = "select count(1) from front.diamond_info where pifajia = 0 or pifajia_mode = '0000'; ";
	}
	
	if ($force_all) {
		$sql = "select count(1) from front.diamond_info where status = 1;";
	}
	
	$total = $db->getOne($sql);
	if ($total == 0) {
		echo 'no pifajia to do'.PHP_EOL;
		return;
	}
	
	$config_count = $db->getOne("select count(1) from front.diamond_pf_jiajialv where `status` = 1");
	if ($config_count == 0) {
		echo 'no pifajia config exits'.PHP_EOL;
		return;
	}
	
	echo 'start to calc pifajia...'.PHP_EOL;
	
	$jia_config = array();
	$sql_list = array();
	
	$limit = 0;
	$topN = 100;
	while($limit < $total) {
		
		if (!empty($cert_id)) {
			$sql = "select carat, from_ad, color, clarity, cert, cert_id, good_type, chengben_jia, pifajia, pifajia_mode, guojibaojia, source_discount, shape from front.diamond_info where cert_id = '{$cert_id}'";
		} else if (!empty($from_ad)) {
			$sql = "select carat, from_ad, color, clarity, cert, cert_id, good_type, chengben_jia, pifajia, pifajia_mode, guojibaojia, source_discount, shape from front.diamond_info where from_ad = '{$from_ad}' limit {$limit},{$topN}; ";
		} else {
			$sql = "select carat, from_ad, color, clarity, cert, cert_id, good_type, chengben_jia, pifajia, pifajia_mode, guojibaojia, source_discount, shape from front.diamond_info where pifajia = 0 or pifajia_mode = '0000' limit {$limit},{$topN}; ";
		}

		if ($force_all) {
			$sql = "select carat, from_ad, color, clarity, cert, cert_id, good_type, chengben_jia, pifajia, pifajia_mode, guojibaojia, source_discount, shape from front.diamond_info where status = 1 limit {$limit},{$topN}; ";
		}
		
		$diamonds = $db->getAll($sql);
		if (empty($diamonds)) {
			echo 'no pifajia to do'.PHP_EOL;
			break;
		}
	
		foreach($diamonds as $dia) {
			if (!isset($jia_config[$dia['from_ad']])) {
				$jia_config[$dia['from_ad']] = get_pifajia_config__($dia['from_ad'], $db);
			} 
			
			if (empty($jia_config[$dia['from_ad']])) {
				continue;
			}

			$old_pifajia = $dia['pifajia'];
			$old_pifajia_mode = $dia['pifajia_mode'];
			inner_calc_pifajia__($jia_config[$dia['from_ad']], $dia, $db);
			
			if ($old_pifajia <> $dia['pifajia'] || $old_pifajia_mode <> $dia['pifajia_mode']) {
				$sql_list[]="UPDATE front.`diamond_info` `dia` SET `dia`.`pifajia` ='".$dia['pifajia']."', `dia`.`pifajia_mode`='".$dia['pifajia_mode']."' WHERE `dia`.`cert_id`='".$dia['cert_id']."'";
			}
			
			if (count($sql_list) >= 20) {
				$db->query(implode(' ;', $sql_list));
				$sql_list = array();
			}
		}
		unset($dia);
		$limit += $topN;
	}
	
	if (!empty($sql_list)) {
		$db->query(implode(' ;', $sql_list));
	}
	
	echo 'finish calc pifajia'.PHP_EOL;
}
	
function get_pifajia_config__($from_ad, &$db) {
	$sql_jia="SELECT carat_min,carat_max,jiajialv,color,clarity,cert, good_type FROM front.diamond_pf_jiajialv where status=1 and from_ad = '".$from_ad."';";
	$jia=$db->getAll($sql_jia);
		
	uasort($jia, function($a, $b) {
		// 2. 比较颜色
		if (!empty($a['color']) && empty($b['color'])) return -1;
		if (empty($a['color']) && !empty($b['color'])) return 1;
		
		// 3. 比较净度
		if (!empty($a['clarity']) && empty($b['clarity'])) return -1;
		if (empty($a['clarity']) && !empty($b['clarity'])) return 1;
		
		// 4. 比较证书类型
		if (!empty($a['cert']) && empty($b['cert'])) return -1;
		if (empty($a['cert']) && !empty($b['cert'])) return 1;
		
		// 5. 比较货品类型
		if (!empty($a['good_type']) && empty($b['good_type'])) return -1;
		if (empty($a['good_type']) && !empty($b['good_type'])) return 1;
		
		$a['carat_min'] = floatval($a['carat_min']);
		$b['carat_min'] = floatval($b['carat_min']);
		// 1. 比较石重
		if ($a['carat_min'] < $b['carat_min']) return -1;
		if ($a['carat_min'] > $b['carat_min']) return 1;
		
		return 0;
	});
	
	return $jia;
}

function inner_calc_pifajia__(&$jia, &$dia, &$db) {
	$carat = $dia['carat'];
	if (empty($dia['good_type'])) $dia['good_type'] = '2';

	list($guojibaojia, $discount) = get_guojibaojia_and_discount__($dia, $db);
	if (empty($guojibaojia) || empty($discount)) {
		$dia['pifajia'] = 0; //算不出来
		$dia['pifajia_mode'] = '';
		echo $dia['cert_id'].': *'.PHP_EOL;
		return;
	}

	global $real_us2rmb_rate;
	foreach($jia as $k => $v) {
		$carat_matched = $carat >= $v['carat_min'] && $carat < $v['carat_max'];
		if (!$carat_matched) continue;
		
		$color_match = $dia['color'] == $v['color'] ;
		$clarity_match = $v['clarity'] == $dia['clarity'];
		$cert_match =  $v['cert'] == $dia['cert'];
		$good_type_match = $v['good_type'] == $dia['good_type'];
		
		//1111
		if ($color_match && $clarity_match && $cert_match && $good_type_match) {
			$pifajia = round($guojibaojia * $carat * (100 - $discount) * $v['jiajialv'] * 0.01 * $real_us2rmb_rate);
			$dia['pifajia'] = $pifajia;
			$dia['pifajia_mode'] = '1111';
			echo $dia['cert_id'].': 1111'.PHP_EOL;
			break;
		}
		
		$good_type_null = empty($v['good_type']);
		
		//1110
		if ($color_match && $clarity_match && $cert_match && $good_type_null) {
			$pifajia = round($guojibaojia * $carat * (100 - $discount) * $v['jiajialv'] * 0.01 * $real_us2rmb_rate);
			$dia['pifajia'] = $pifajia;
			$dia['pifajia_mode'] = '1110';
			echo $dia['cert_id'].':1110'.PHP_EOL;
			break;
		}
		
		$cert_null = empty($v['cert']);
		
		//1101
		if ($color_match && $clarity_match && $cert_null && $good_type_match) {
			$pifajia = round($guojibaojia * $carat * (100 - $discount) * $v['jiajialv'] * 0.01 * $real_us2rmb_rate);
			$dia['pifajia'] = $pifajia;
			$dia['pifajia_mode'] = '1101';
			echo $dia['cert_id'].':1101'.PHP_EOL;
			break;
		}
		
		//1100
		if ($color_match && $clarity_match && $cert_null&& $good_type_null) {
			$pifajia = round($guojibaojia * $carat * (100 - $discount) * $v['jiajialv'] * 0.01 * $real_us2rmb_rate);
			$dia['pifajia'] = $pifajia;
			$dia['pifajia_mode'] = '1100';
			echo $dia['cert_id'].':1100'.PHP_EOL;
			break;
		}
		
		$clarity_null = empty($v['clarity']);
		//1011
		if ($color_match && $clarity_null && $cert_match && $good_type_match) {
			$pifajia = round($guojibaojia * $carat * (100 - $discount) * $v['jiajialv'] * 0.01 * $real_us2rmb_rate);
			$dia['pifajia'] = $pifajia;
			$dia['pifajia_mode'] = '1011';
			echo $dia['cert_id'].':1011'.PHP_EOL;
			break;
		}
		
		//1010
		if ($color_match && $clarity_null && $cert_match && $good_type_null) {
			$pifajia = round($guojibaojia * $carat * (100 - $discount) * $v['jiajialv'] * 0.01 * $real_us2rmb_rate);
			$dia['pifajia'] = $pifajia;
			$dia['pifajia_mode'] = '1010';
			echo '1010'.PHP_EOL;
			break;
		}
		
		//1001
		if ($color_match && $clarity_null && $cert_null && $good_type_match) {
			$pifajia = round($guojibaojia * $carat * (100 - $discount) * $v['jiajialv'] * 0.01 * $real_us2rmb_rate);
			$dia['pifajia'] = $pifajia;
			$dia['pifajia_mode'] = '1001';
			echo $dia['cert_id'].':1001'.PHP_EOL;
			break;
		}
		
		//1000
		if ($color_match && $clarity_null && $cert_null && $good_type_null) {
			$pifajia = round($guojibaojia * $carat * (100 - $discount) * $v['jiajialv'] * 0.01 * $real_us2rmb_rate);
			$dia['pifajia'] = $pifajia;
			$dia['pifajia_mode'] = '1000';
			echo $dia['cert_id'].':1000'.PHP_EOL;
			break;
		}
		
		$color_null = empty($v['color']);
		//0111
		if ($color_null && $clarity_match && $cert_match && $good_type_match) {
			$pifajia = round($guojibaojia * $carat * (100 - $discount) * $v['jiajialv'] * 0.01 * $real_us2rmb_rate);
			$dia['pifajia'] = $pifajia;
			$dia['pifajia_mode'] = '0111';
			echo $dia['cert_id'].':0111'.PHP_EOL;
			break;
		}
		
		//0110
		if ($color_null && $clarity_match && $cert_match && $good_type_null) {
			$pifajia = round($guojibaojia * $carat * (100 - $discount) * $v['jiajialv'] * 0.01 * $real_us2rmb_rate);
			$dia['pifajia'] = $pifajia;
			$dia['pifajia_mode'] = '0110';
			echo $dia['cert_id'].':0110'.PHP_EOL;
			break;
		}
		
		//0101
		if ($color_null && $clarity_match && $cert_null && $good_type_match) {
			$pifajia = round($guojibaojia * $carat * (100 - $discount) * $v['jiajialv'] * 0.01 * $real_us2rmb_rate);
			$dia['pifajia'] = $pifajia;
			$dia['pifajia_mode'] = '0101';
			echo $dia['cert_id'].':0101'.PHP_EOL;
			break;
		}
		
		//0100
		if ($color_null && $clarity_match && $cert_null && $good_type_null) {
			$pifajia = round($guojibaojia * $carat * (100 - $discount) * $v['jiajialv'] * 0.01 * $real_us2rmb_rate);
			$dia['pifajia'] = $pifajia;
			$dia['pifajia_mode'] = '0100';
			echo $dia['cert_id'].':0100'.PHP_EOL;
			break;
		}
		
		//0011
		if ($color_null && $clarity_null && $cert_match && $good_type_match) {
			$pifajia = round($guojibaojia * $carat * (100 - $discount) * $v['jiajialv'] * 0.01 * $real_us2rmb_rate);
			$dia['pifajia'] = $pifajia;
			$dia['pifajia_mode'] = '0011';
			echo $dia['cert_id'].':0011'.PHP_EOL;
			break;
		}
		
		//0010
		if ($color_null && $clarity_null && $cert_match && $good_type_null) {
			$pifajia = round($guojibaojia * $carat * (100 - $discount) * $v['jiajialv'] * 0.01 * $real_us2rmb_rate);
			$dia['pifajia'] = $pifajia;
			$dia['pifajia_mode'] = '0010';
			echo $dia['cert_id'].':0010'.PHP_EOL;
			break;
		}
		
		//0001
		if ($color_null && $clarity_null && $cert_null && $good_type_match) {
			$pifajia = round($guojibaojia * $carat * (100 - $discount) * $v['jiajialv'] * 0.01 * $real_us2rmb_rate);
			$dia['pifajia'] = $pifajia;
			$dia['pifajia_mode'] = '0001';
			echo $dia['cert_id'].':0001'.PHP_EOL;
			break;
		}
		
		//0000
		if ($color_null && $clarity_null && $cert_null && $good_type_null){
			$pifajia = round($guojibaojia * $carat * (100 - $discount) * $v['jiajialv'] * 0.01 * $real_us2rmb_rate);
			$dia['pifajia'] = $pifajia;
			$dia['pifajia_mode'] = '0000';
			echo $dia['cert_id'].':0000'.PHP_EOL;
			break;
		}
	}

	if (!isset($dia['pifajia'])) {
		$dia['pifajia'] = 0; //算不出来
		$dia['pifajia_mode'] = '';
		echo $dia['cert_id'].': *'.PHP_EOL;
	}
}

function get_guojibaojia_and_discount__(&$dia, &$db) {
	$guojibaojia = floatval($dia['guojibaojia']);
	$discount = floatval($dia['source_discount']);
	
	/*
    如果国际报为空，就去裸钻国际报价取；
    源折扣为空，对应的源折扣=100-(成本价/1.043/后台维护的汇率/国际报价/钻重/0.01)
	*/
	if ($guojibaojia <> 0 && $discount <> 0) { 
		echo '1: guojibaojia:'.$guojibaojia.', discount:'.$discount.PHP_EOL;
		return array($guojibaojia, $discount);
	}

	if ($guojibaojia == 0) {
		$shape_map = array('1' => 'BR', '14' => 'PS');
		if (!isset($shape_map[$dia['shape']])) return array(false, false);
		$sql = "select price from front.diamond_price where shape='{$shape_map[$dia['shape']]}' and color='{$dia['color']}' and clarity='{$dia['clarity']}' and `min`<= '{$dia['carat']}' and '{$dia['carat']}' <= `max` order by addtime desc limit 1";
		$guojibaojia = $db->getOne($sql);
	}

	if (floatval($guojibaojia) > 0  && $discount == 0) {
		global $us2rmb_rate;

		$discount = floatval($dia['chengben_jia']) / 1.043;
		$discount = $discount / $us2rmb_rate;
		$discount = $discount / $guojibaojia;
		$discount = $discount / $dia['carat'];
		$discount = $discount / 0.01;
		$discount =  100 - $discount;
	}

	echo '2:guojibaojia:'.$guojibaojia.', discount:'.$discount.PHP_EOL;
	return array($guojibaojia, $discount); 
}


?>