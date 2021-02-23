<?php
// v2,镶口，材质，指圈等数据
error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");
//此文件是where条件
include 'style_where.php';

$conOld = mysqli_connect('192.168.1.55','style_zyy','KELAzhangyuanyuan123','kela_style');
$conNew =  mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');

$old_style = 'style_style';	
$old_xiangkou = 'style_xiangkou';	

$new_table = "rel_style_attribute";
$attr_table_value = 'app_attribute_value';
// 老款式库
$product_type = 6;
$cat_type = 2;

//1、新数据库：！！！！！！！！！！！！！！！！！！！！！！！根据当前的配置对一下数据
$xiangkou_id = 1;//镶口属性id
$zhiquan_id = 5;//指圈属性id
$caizhi_attr_id = 3 ;//材质属性的id
$caizhi_value_info =array('18K'=>37,'PT950'=>39);//新数据库中材质的属性值
$caizhi_value =array('1'=>37,'2'=>39,'3'=>'37,39');//新数据库中材质的属性值

//2、新数据库：镶口，指圈对应的属性值
$sql = "select * from `front`." . $attr_table_value." where `attribute_id`=".$xiangkou_id." or `attribute_id`=".$zhiquan_id;
$res = mysqli_query($conNew,$sql);
$new_attr_value_data = array ();
while ( $row = mysqli_fetch_array($res) ){
    $attr_id = $row['attribute_id'];
    $value_id = $row['att_value_id'];
    $value_name = $row['att_value_name'];
    $new_attr_value_data[$attr_id][$value_name] = $value_id;//[属性id][属性值名称]=>属性值id
}

//var_dump($new_attr_value_data);
//4、获取旧数据的各种属性值
$date_time = date("Y-m-d H:i:s");
$field = " `cat_type_id`, `product_type_id`, `style_sn`, `attribute_id`, `attribute_value`, `show_type`, `create_time`, `create_user`, `info`, `style_id` ";

$old_table = 'style_style';
$sql = "SELECT count(*) FROM kela_style." . $old_table . "  WHERE `is_new` =1 ".$where;
$t = mysqli_query($conOld,$sql);
$cnt = mysqli_fetch_row($t);


$len = $cnt[0];
$forsize = ceil($len / 1000);
for($ii = 1; $ii <= $forsize; $ii ++){
    echo $ii."----------------------------------------------------------"."\n";
    $offset = ($ii - 1) * 1000;
    $sql = "SELECT *  FROM " . $old_style . "   WHERE `is_new`=1 ".$where."  limit $offset,1000";
    $res = mysqli_query($conOld,$sql);
    $old_style_data = array ();
    while ( $row = mysqli_fetch_array($res) ){
		$style_id = $row['style_id'];
		$style_sn = $row['style_sn'];
		$caizhi = $row['style_caizhi'];
        //材质
		$new_value_id = $caizhi_value[$caizhi];//可做材质；1为18K；2为PT950；3为18K&PT950
		$sql = "update ".$new_table." set `attribute_value`= '".$new_value_id."'  where `attribute_id`= ".$caizhi_attr_id." and `style_sn`='".$style_sn."' ";
        if(!mysqli_query($conNew,$sql)){
             echo "\n\n\n".$sql."\n\n\n";exit;
         }else {
             //echo mysqli_insert_id($conNew).'-'.$sql."\n";
         }
        //镶口
        $sql_3 = "SELECT `stone`,`finger`  FROM " . $old_xiangkou . "   WHERE  `style_id`=".$style_id;
        $res_3 = mysqli_query($conOld,$sql_3);

		$xk_arr = array();
		$zq_arr = array();
        while ( $row_3 = mysqli_fetch_array($res_3) ){
             $xk_zhi = $row_3['stone'];
             $zq_zhi = $row_3['finger'];
			 if($row_3['finger'] == "11-12-13"){
				 $zq_zhi = "11-13";
			 }
             if(!isset($new_attr_value_data[$xiangkou_id][$xk_zhi])){
                    continue;
             }
             if(!isset($new_attr_value_data[$zhiquan_id][$zq_zhi])){
                    continue;
             }
             $xk_arr[$xk_zhi] =  $new_attr_value_data[$xiangkou_id][$xk_zhi];//变成镶口对应的属性值id
             $zq_arr[$zq_zhi] =  $new_attr_value_data[$zhiquan_id][$zq_zhi];//变成手寸对应的属性值id
        }
       // var_dump($xk_arr);
       // var_dump($zq_arr);
        if(empty($xk_arr)){
            continue;
        }
        
        if(empty($zq_arr)){
            continue;
        }
        //镶口
        $xk_value_zhi = implode(",", $xk_arr);
        //手寸
        $zq_value_zhi = implode(",", $zq_arr);
        
        $sql_4 = "update ".$new_table." set `attribute_value`= '".$xk_value_zhi."'   WHERE `attribute_id`= ".$xiangkou_id." and `style_sn`='".$style_sn."'";
        if(!mysqli_query($conNew,$sql_4)){
             echo "\n\n\n".$sql."\n\n\n";exit;
         }else {
             //echo mysqli_insert_id($conNew).'-'.$sql."\n";
         }
         
        $sql_5 = "update ".$new_table." set `attribute_value`= '".$zq_value_zhi."'  WHERE `attribute_id`= ".$zhiquan_id." and `style_sn`='".$style_sn."'";
		if(!mysqli_query($conNew,$sql_5)){
			echo "\n\n\n".$sql."\n\n\n";exit;
		}else {
			echo $ii."\n";
		}
		
    }
}
echo "\n\n\n\n完成ok！";
die();

