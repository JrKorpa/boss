<?php

global $lib_path;
require_once($lib_path .'/Utils.class.php');

function on_style_attr_changed($data, $db) {

	$style_id = $data['style_id'];
	$style_sn = $data['style_sn'];
	
	// 1: xiangkou, 3: caizhi, 5: zhiquan, 33: caizhiyanse
	$style_attrs = $db->getAll(
"SELECT r.attribute_id, a.attribute_code, v.att_value_id, v.att_value_name from front.rel_style_attribute r
inner join front.app_attribute a on a.attribute_id = r.attribute_id
inner join front.app_attribute_value v on v.attribute_id = a.attribute_id and LOCATE(concat(',',v.att_value_id,','), concat(',',r.attribute_value)) > 0
where r.style_id = '{$style_id}' and r.attribute_id in (1, 3, 5, 33);
");
	$style_attrs = Utils::indexArray($style_attrs, 'attribute_code');

	$yanse = '';
	$caizhi = '';
	$zhiquan = '';
	$xiangkou = '';
	
	foreach($style_attrs as $code => $attrs) {
		if ($code == 'xiangkou') {
			$xiangkou = resolve_range($attrs);
		} else if ($code == 'caizhi') {
			$caizhi = array();
			foreach($attrs as $at) {
				if ($at['att_value_name'] == '18K') $caizhi[] = 1;
				if ($at['att_value_name'] == 'PT950') $caizhi[] = 2;
			}
			
		} else if ($code == 'zhiquan') {
			$zhiquan = resolve_range($attrs);
		} else if ($code == 'caizhiyanse') {
			$yanse = resolve_color($attrs);
		}
	}

	$down = array();
	$up = array();
	
	$goods_style_attrs = $db->getAll("SELECT goods_id, goods_sn, shoucun, xiangkou,caizhi, yanse, is_ok from front.list_style_goods where style_id = '{$style_id}'");
	foreach($goods_style_attrs as $a) {
		if ((empty($yanse) && !empty($a['yanse'])) || (!empty($yanse) && !in_array($a['yanse'], $yanse))) {
			if ($a['is_ok'] == 1) $down[] = $a['goods_id'];
			continue;
		}
		
		if ((empty($caizhi) && !empty($a['caizhi'])) || (!empty($caizhi) && !in_array($a['caizhi'], $caizhi))) {
			if ($a['is_ok'] == 1) $down[] = $a['goods_id'];
			continue;
		}
		
		$curr_xk = floatval($a['xiangkou']);
		if (!empty($xiangkou)) {
			$found = false;

			foreach($xiangkou as $xk) {
				if (is_array($xk)) {
					if ($curr_xk >= $xk[0] && $curr_xk <= $xk[1]) {
						$found = true;
						break;
					}
				} else {
					if ($curr_xk == $xk) {
						$found = true;
						break;
					}
				}
			}
			
			if (!$found) {
				if ($a['is_ok'] == 1) $down[] = $a['goods_id'];
				continue;
			}
		} else if ($curr_xk > 0) {
			if ($a['is_ok'] == 1) $down[] = $a['goods_id'];
			continue;
		}
		
		$curr_zq = floatval($a['shoucun']);
		if (!empty($zhiquan)) {
			$found = false;

			foreach($zhiquan as $zq) {
				if (is_array($zq)) {
					if ($curr_zq >= $zq[0] && $curr_zq <= $zq[1]) {
						$found = true;
						break;
					}
				} else {
					if ($curr_zq == $zq) {
						$found = true;
						break;
					}
				}
			}
			
			if (!$found) {
				if ($a['is_ok'] == 1) $down[] = $a['goods_id'];
				continue;
			}
		} else if ($curr_zq > 0) {
			if ($a['is_ok'] == 1) $down[] = $a['goods_id'];
			continue;
		}
		
		if ($a['is_ok'] == 0) $up[] = $a['goods_id'];
	}
	
	$sql = '';
	if (!empty($up)) {
		$up = array_unique($up);
		$sql .= 'update front.list_style_goods set is_ok = 1 where goods_id in ('.implode(',', $up).');';
	}
	if (!empty($down)) {
		$down = array_unique($down);
		$sql .= 'update front.list_style_goods set is_ok = 0 where goods_id in ('.implode(',', $down).');';
	}

	if (!empty($sql)) $db->exec($sql);
	echo 'finish update for style:'.$style_sn.', up '.count($up). ', down '.count($down) .PHP_EOL;
}
	

function resolve_color($style_color_attrs) {
	
	$style_colors = [
		'白' => 1,
		'黄' => 2,
		'玫瑰金' => 3,
		'分色' => 4,
		'彩金' => 5,
		'玫瑰黄' => 6,
		'玫瑰白' => 7,
		'黄白' => 8
	];
	
	$result = array();
	foreach($style_color_attrs as $s) {
		if (isset($style_colors[$s['att_value_name']])) {
			$result[] = $style_colors[$s['att_value_name']];
		}
	}
	
	return $result;
}

function resolve_range($list) {
	$result = array();
	foreach($list as $l) {
		if (strpos($l['att_value_name'],'-') !== false) {
			$arr = explode('-', $l['att_value_name']);
			$result[] = array_map(function($value) {
				return floatval($value);
			}, $arr);
		} else {
			$result[] = floatval( $l['att_value_name'] );
		}
	}
	
	return $result;
}


?>