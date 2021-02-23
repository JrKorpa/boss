<?php
//v3款式信息
error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");

header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=钻石饰品.xls");

$conOld = mysqli_connect('192.168.1.55','style_zyy','KELAzhangyuanyuan123','kela_style');
//$conNew =  mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');

//旧款式的属性字段对应新的属性字段
$old_attr_new_attr = array(
    'style_sn'=>'style_sn',
    'style_name'=>'style_name',
    'pro_line'=>'product_type',
    'style_cat'=>'style_type',//v3的数据都是戒指
    'create_time'=>'create_time',
    'last_update'=>'modify_time',
    'zuhe_time'=>'cancel_time',
    'is_confirm'=>'check_status',
    'is_chaihuo'=>'dismantle_status',
);

//获取原来款式基本信息
$old_table = 'style_style';
$old_factory = 'style_factory';


$table_title = "款号\t款式名称\t添加时间\t最后更新时间\t畅销度\t系列\t产品分类\t生产厂商\t是否默认工厂\t有无镶口\t模号\t材质\t颜色\t指圈\n";
//取钻石饰品数据
$sql = "SELECT a.*,b.* FROM kela_style." . $old_table . " as a left join kela_style.".$old_factory." as b on a.style_id = b.style_id where pro_line = 4 and `zuofei_type` =1 ";
//echo $sql;
//echo "<br>";
    $res = mysqli_query($conOld, $sql);
    $old_data = array();
    while ($row = mysqli_fetch_array($res)){
        $old_data[]= $row;
    }
    
	//一个款对应的工厂都相同则显示一条
	$new_data = array();
	foreach($old_data as $val){
		$style_id = $val['style_id'];
		$factory_id = $val['factory_id'];
		if($factory_id){
			$new_data[$style_id][$factory_id]= $val;
		}else{
			$new_data[$style_id][0]= $val;
		}
		
	}
     
	//合并工厂数据
	//工厂数据：生产厂商、是否默认工厂（是，否;有无镶口（有，无）（同一个工厂多个模号，只要有一个模号镶口为0，有无镶口就显示“无”），
    //模号（模号：镶口|模号：镶口|如有更多信息以此类推）
	foreach($old_data as $val){
		$style_id = $val['style_id'];
		$factory_id = $val['factory_id'];
		if($factory_id){
			if(array_key_exists($factory_id,$new_data[$style_id])){
				$mohao_data[$style_id][$factory_id] = $mohao_data[$style_id][$factory_id].';'.$val['xiangkou'].'|'.$val['factory_sn'];
			}else{
				$mohao_data[$style_id][$factory_id] = $val['xiangkou'].'|'.$val['factory_sn'];	
			}
		}
	}

	$cat_type_arr=array(1=>"戒指",2=>"吊坠",3=>"项链",4=>"耳钉",5=>"耳环",6=>"耳坠",7=>"手镯",8=>"手链",9=>"脚链",13=>"其他");//新项目中款式
	//---------------------------------------输出数据----------------------------------------
   // echo "<pre>";
	//print_r($new_data);
	
	echo iconv('UTF-8','GBK',$table_title);
	
	//遍历数据
	foreach($new_data as $s_key=>$ss_val){
		foreach($ss_val as $f_key=>$val){
			//款式基本信息：款号、款式名称、添加时间、最后更新时间、畅销度、系列、 产品分类
			$style_id = $s_key;
			$is_new = $val['is_new'];
			
			$style_name = $val['style_name'];
			$style_sn = $val['style_sn'];
			$add_time = $val['create_time'];
			$update_time = $val['last_update'];
			$cat_name = $cat_type_arr[$val['style_cat']];
			$changxiao ="";
			$xilie = "";

			$factory_id = $f_key;
			if($factory_id){
				
				$sql2 = "select p_name from jxc_processors where p_id =".$factory_id;
				$res2 = mysqli_query($conOld,$sql2);
				
				$row2 = mysqli_fetch_row($res2);
				$factory_name = $row2['0']; 
				$factory_str = $factory_name;
				$is_def_str= $val['is_def']==1 ? "是":"否";
				$xiangkou_str = $val['xiangkou']==0 ? "空":$val['xiangkou'];
				$mohao_str =$mohao_data[$style_id][$factory_id] ;


			}else{
				$factory_str = "";
				$is_def_str= "";
				$xiangkou_str = "";
				$mohao_str ="";
			}

			//材质
		   $metail_info = unserialize($val["metal_info"]);
		  
		   $caizhi_str = "";
		   if(!empty($metail_info)){
				
				if($a1 = getCaiZhi( $metail_info,1)){
					$caizhi_arr[$s_key][$a1] = $a1;
				}
				if($a2 = getCaiZhi( $metail_info,2)){
					$caizhi_arr[$s_key][$a2] = $a2;
				}
				if($a3 = getCaiZhi( $metail_info,3)){
					$caizhi_arr[$s_key][$a3] = $a3;
				}
				if($a4 = getCaiZhi( $metail_info,4)){
					$caizhi_arr[$s_key][$a4] = $a4;
				}
				if($a5 = getCaiZhi( $metail_info,5)){
					$caizhi_arr[$s_key][$a5] = $a5;
				}
				if($a6 = getCaiZhi( $metail_info,6)){
					$caizhi_arr[$s_key][$a6] = $a6;
				}
				if($a7 = getCaiZhi( $metail_info,7)){
					$caizhi_arr[$s_key][$a7] = $a7;
				}
				if($a8 = getCaiZhi( $metail_info,8)){
					$caizhi_arr[$s_key][$a8] = $a8;
				}
				if($a9 = getCaiZhi( $metail_info,9)){
					$caizhi_arr[$s_key][$a9] = $a9;
				}
				if($a10 = getCaiZhi( $metail_info,10)){
					$caizhi_arr[$s_key][$a10] = $a10;
				}
				
				
			
				if($caizhi_arr){
					$caizhi_str = implode(",",$caizhi_arr[$s_key]);
				}
		   }

	 
		   //颜色
		   $yanse_str = "";

		   //指圈
		   $style_cat_info = unserialize($val["style_cat_attr"]);
		   $finger_str = "";
		   if(!empty($style_cat_info)){
				if(array_key_exists(1, $style_cat_info)){
					$min_finger = $style_cat_info[1]['min'];
					$max_finger = $style_cat_info[1]['max'];
					$finger_str = "'".$min_finger."-".$max_finger;
				}
			}

			echo $style_sn."\t".iconv('UTF-8','GBK',$style_name)."\t".$add_time."\t".$update_time."\t".$changxiao."\t".$xilie."\t".iconv('UTF-8','GBK',$cat_name)."\t".iconv('UTF-8','GBK',$factory_str)."\t".iconv('UTF-8','GBK',$is_def_str)."\t".iconv('UTF-8','GBK',$xiangkou_str)."\t".iconv('UTF-8','GBK',$mohao_str)."\t".iconv('UTF-8','GBK',$caizhi_str)."\t".$yanse_str."\t".$finger_str."\n";
			
			
		}
	}




	   
        


exit;

// 根据金托信息判断此素金属于哪种类型：8  K金 ,9  PT; 19  银
//旧的数据：k金：1,8,2; pt:3,4,10; 银:5,6,9
function get_sujin_cat_type($data){
    if($data[1]['selected'] ==1 || $data[8]['selected'] ==1 || $data[2]['selected'] ==1){
        return 8;
    }
    if($data[3]['selected'] ==1 || $data[4]['selected'] ==1 || $data[10]['selected'] ==1){
       return 9; 
    }
    if($data[5]['selected'] ==1 || $data[6]['selected'] ==1 || $data[9]['selected'] ==1){
        return 19;
    }
}


function getCaiZhi( $data,$type){
    $_style_gold_type = array(
        "1" => array("gold_name" => "9K", "price"=>"140", "gold_color"=>",1,2,3,", "loss"=>"0.15", "middle" => "10"),
        "8" => array("gold_name" => "14K", "price"=>"217", "gold_color"=>",1,2,3,", "loss"=>"0.15", "middle" => "13"),
        "2" => array("gold_name" => "18K", "price"=>"280", "gold_color"=>",1,2,3,", "loss"=>"0.15", "middle" => "13"),
        "3" => array("gold_name" => "PT900", "price"=>"460", "gold_color"=>",1,", "loss"=>"0.15", "middle" => "20"),
        "4" => array("gold_name" => "PT950", "price"=>"465", "gold_color"=>",1,", "loss"=>"0.15", "middle" => "20"),
        "10" => array("gold_name" => "PT999", "price"=>"338.5", "gold_color"=>",1,", "loss"=>"0.15", "middle" => "20"),
        "5" => array("gold_name" => "S925", "price"=>"", "gold_color"=>",1,"),
        "6" => array("gold_name" => "S990", "price"=>"", "gold_color"=>",1,"),
        "7" => array("gold_name" => "千足金", "price"=>"375", "gold_color"=>",2,"),//这个不属于素金
        "9" => array("gold_name" => "千足银", "price"=>"", "gold_color"=>",1,")
    );
    
    if(isset($data[$type]['selected']) && $data[$type]['selected']==1){
        return $_style_gold_type[$type]['gold_name'];
    }else{
        return '';
    }
}
