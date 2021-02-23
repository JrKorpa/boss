<?php
//新旧款式库副石信息对比
header("Content-type:text/html;charset=utf8;");
set_time_limit(0);
error_reporting(E_ALL);
include('stone_config.php');
$oldData = getOld();
$newData = getNew();

$diff = array();
foreach($oldData as $key=>$val){
	if(isset($newData[$key])){
		$tmp = $val['sec_stone_attr'];
		$stone_cat = $val['sec_stone_cat'];
		$newsec_stone_attr = $newData[$key]['sec_stone_attr'];
		
		$temp = array();
		if(!empty($tmp)){
			foreach($tmp as $v){
				if(is_array($v)){
					foreach($v as $vv){
						$temp[] = $vv;
					}
				}else{
					$temp[] = $v;
				}
			}
			$flag = array_diff($temp,$newsec_stone_attr);
			if(!empty($flag)){
				$diff[$key] = $val;
			}
		}
	}else{
		$diff[$key] = $val;
	}	
}

echo '<table border="1" cellspacing="1" cellpadding="1" width="100%">
		<tbody>
			<tr>
				<td></td>
				<td>老款副石属性</td>
				<td>新款副石属性</td>
			</tr>';
$echo_str = '';

foreach($diff as $ke=>$va){
	$echo_str .= '<tr>
			<td>'.$va['style_sn'].'</td>';
	/*老款主石信息输出  start*/
	$echo_str .= '<td>主石类别:'.$_style_sec_stone_cat[$va['sec_stone_cat']]['stone_name'].'<br/>';
	foreach($_style_sec_stone_cat[$va['sec_stone_cat']]['attr'] as $k=>$attr){
		if($attr['type']=='text'){
			$echo_str .= $attr['item_name'].':';
			$echo_str .= $va['sec_stone_attr'][$k]?$va['sec_stone_attr'][$k]:"";
			$echo_str .= '<br/>';
		}else if($attr['type']=='within'){
			$start_attr = '';
			$end_attr = '';
			if(!empty($va['sec_stone_attr'][$k])){
				$start_attr = current($va['sec_stone_attr'][$k]);
				$end_attr = end($va['sec_stone_attr'][$k]);
			}
			$echo_str .= $attr['item_name'].':'.$start_attr.'-'.$end_attr.'<br/>';
		}else if($attr['type']=='radio'){
			$radio_str = '';
			if(!empty($va['sec_stone_attr'][$k])){
				$radio_str = $attr['val'][$va['sec_stone_attr'][$k]]['item_name'];
			}
			$echo_str .= $attr['item_name'].':'.$radio_str.'<br/>';
		}
	}
	$echo_str .= '</td>';
	/*老款主石信息输出  end*/
	/*新款主石信息输出  start*/
	if(array_key_exists($ke,$newData)){
		$echo_str .= '<td>主石类别:'.$_style_sec_stone_cat[$newData[$ke]['sec_stone_cat']]['stone_name'].'<br/>';
		$newVal = $newData[$ke]['sec_stone_attr'];
		$weight = '重量(CT)：'.$newVal['weight'].'<br>';
		$number = '数量(颗)：'.$newVal['number'].'<br>';
		$xiangkou_start = '';
		if(array_key_exists('xiangkou_start',$newVal)){
			$xiangkou_start = $newVal['xiangkou_start'];
		}
		$xiangkou_end = '';
		if(array_key_exists('xiangkou_end',$newVal)){
			$xiangkou_end = $newVal['xiangkou_end'];
		}
		$xiangkou = '镶口范围(CT)：'.$xiangkou_start.'-'.$xiangkou_end.'<br>';
		$_clarity_zhushi = '';
		if(array_key_exists('clarity_fushi',$newVal)){
			if(array_key_exists($newVal['clarity_fushi'],$_style_stone_clarity)){
				$_clarity_zhushi = $_style_stone_clarity[$newVal['clarity_fushi']]['item_name'];
			}
		}
		$clarity_fushi = '净度：'.$_clarity_zhushi.'<br>';
		$_color_zhushi = '';
		if(array_key_exists('color_fushi',$newVal)){
			if(array_key_exists($newVal['color_fushi'],$_style_stone_colors)){
				$_color_zhushi = $_style_stone_colors[$newVal['color_fushi']]['item_name'];
			}
		}
		$color_fushi = '颜色：'.$_color_zhushi.'<br>';
		$_shape_zhushi = '';
		if(array_key_exists('shape_zhushi',$newVal)){
			$_shape_zhushi = $_style_stone_clarity[$newVal['shape_zhushi']]['item_name'];
		}
		$shape_zhushi = '形状：'.$_shape_zhushi.'<br>';
		$_chicun_zhenzhu = '';
		if(array_key_exists('color_zhushi',$newVal)){
			$_chicun_zhenzhu = $newVal['chicun'];
		}
		$chicun_zhenzhu = '尺寸(MM)：'.$_chicun_zhenzhu.'<br>';
		$_chicun = '';
		if(array_key_exists('chicun_start',$newVal) && array_key_exists('chicun_end',$newVal)){
			$_chicun = $newVal['chicun_start'].'-'.$newVal['chicun_end'];
		}
		$chicun = '尺寸(MM)：'.$_chicun.'<br>';
		$zhenzhu_type = '';
		if(array_key_exists('zhenzhu_type',$newVal)){
			$zhenzhu_type = $_style_stone_clarity[$newVal['zhenzhu_type']]['item_name'];
		}
		$zhenzhu = '珍珠分类：'.$zhenzhu_type.'<br>';
		$zhenzhu_shape = '';
		if(array_key_exists('zhenzhu_shape',$newVal)){
			$zhenzhu_shape = $_style_pearl_shape[$newVal['zhenzhu_shape']]['item_name'];
		}
		$zhenzhu .= '珍珠形状：'.$zhenzhu_shape.'<br>';
		$zhenzhu_color = '';
		if(array_key_exists('zhenzhu_color',$newVal)){
			$zhenzhu_color = $_style_main_stone_color[$newVal['zhenzhu_color']]['item_name'];
		}
		$zhenzhu .= '珍珠颜色：'.$zhenzhu_color.'<br>';
		$zhenzhu_face = '';
		if(array_key_exists('zhenzhu_face',$newVal)){
			$zhenzhu_face = $_style_pearl_face_work[$newVal['zhenzhu_face']]['item_name'];
		}
		$zhenzhu .= '表皮：'.$zhenzhu_face.'<br>';
		$zhenzhu_light = '';
		if(array_key_exists('zhenzhu_light',$newVal)){
			$zhenzhu_light = $_style_pearl_light[$newVal['zhenzhu_light']]['item_name'];
		}
		$zhenzhu .= '光泽：'.$zhenzhu_light.'<br>';
		$zhenzhu_product = '';
		if(array_key_exists('zhenzhu_product',$newVal)){
			$zhenzhu_product = $_style_pearl_product[$newVal['zhenzhu_product']]['item_name'];
		}
		$zhenzhu .= '产地：'.$zhenzhu_product.'<br>';
		$zhenzhu_mpear = '';
		if(array_key_exists('zhenzhu_mpear',$newVal)){
			$zhenzhu_mpear = $_style_Mpearl[$newVal['zhenzhu_mpear']]['item_name'];
		}
		$zhenzhu .= '母贝种类：'.$zhenzhu_mpear.'<br>';
		$_caizuan_color = '';
		if(array_key_exists('caizuan_color',$newVal)){
			$_caizuan_color = $_style_color_cstone[$newVal['caizuan_color']]['item_name'];
		}
		$caiZuanColor = '彩钻颜色：'.$_caizuan_color.'<br>';	
		$content = '';
		switch ($newData[$ke]['sec_stone_cat']) {
			case 1:
				$content = $weight . $number . $clarity_fushi . $color_fushi;
				break;
			case 2:
				$content = $weight . $number . $clarity_zhushi . $color_zhushi;
				break;
			case 3:
				$content = $chicun_zhenzhu.$chicun;
				break;
			case 4:
			case 5:
			case 6:
			case 7:
			case 8:
			case 9:
			case 10:
			case 11:
			case 12:
			case 13:
			case 14:
			case 15:
			case 16:
			case 17:
			case 18:
			case 19:
			case 20:
			case 21:
			case 22:
			case 23:
			case 24:
				$content = $weight . $number;
				break;
			default :
				$content = '';
				break;
		}
		$echo_str .= $content.'</td>';
	}else{
		$echo_str .= '<td></td>';
	}
	/*新款主石信息输出  end*/
}
echo $echo_str;
echo '</tbody></table>';

function getNew(){
	$db_new = mysqli_connect('192.168.1.93', 'cuteman', 'QW@W#RSS33#E#', 'app_order');
	//$db_new = mysqli_connect('127.0.0.1', 'root', '', 'front');
	$sql = "set names utf8;";
	$arr = mysqli_query($db_new, $sql);

	$sql = "select ss.style_id,si.style_sn,stone_cat as sec_stone_cat,ss.stone_attr as sec_stone_attr from `front`.`rel_style_stone` as ss,`front`.`base_style_info` as si where ss.style_id=si.style_id and stone_position=2 and stone_cat<>0 and is_new=0;";
	$arr = mysqli_query($db_new, $sql);
	$data= array();
	while($row=mysqli_fetch_assoc($arr)){
		$row['sec_stone_attr'] = unserialize($row['sec_stone_attr']);
		$data[$row['style_id']] = $row;
	}

	return $data;
}



function getOld(){
	$db_old = mysqli_connect('192.168.1.55', 'style_zyy', 'KELAzhangyuanyuan123', 'kela_style');
	//$db_old = mysqli_connect('127.0.0.1', 'root', '', 'kela_style');
	$sql = "set names utf8;";
	$arr = mysqli_query($db_old, $sql);

	//style
	$sql = "select style_id,style_sn,sec_stone_cat,sec_stone_attr from `kela_style`.`style_style` where sec_stone_cat<>0 and is_new=0;";
	$arr = mysqli_query($db_old, $sql);
	$data= array();
	while($row=mysqli_fetch_assoc($arr)){
		$row['sec_stone_attr'] = unserialize($row['sec_stone_attr']);
		$data[$row['style_id']] = $row;
	}
	return $data;
}



















