<?php
// 所有款产品线及款式分类属性，戒指
error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");

//SELECT pro_line FROM `style_style` WHERE  style_cat=1 group by pro_line 
$old_product_line = array(0,2,3,4,6,7,9);

$conOld = mysqli_connect('192.168.1.55','style_zyy','KELAzhangyuanyuan123','kela_style');
$conNew =  mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');

$new_table = "rel_cat_attribute";
$attr_table = 'app_attribute';
$attr_table_value = 'app_attribute_value';

// 老款式库
//$t_sql = "TRUNCATE TABLE  $new_table ";
//$t_res = mysqli_query($conNew,$t_sql);
//if($t_res){
//    echo $old_style . "数据已经清空\n\n\n\n";
//}

$attribute_data  = array(
		"镶口"=>'1',
		"材质"=>'2',
		"指圈"=>'3',
		"18K可做颜色"=>'4',
		"是否群镶"=>'is_weizuan',
		"证书"=>'zhengshu',
		"最大改圈范围"=>'style_gaiquan',
		"是否有副石"=>'is_fushi',
		"爪形态"=>'zhua_xingtai',
		"表面工艺"=>'jiebi_gongyi',
		"戒臂带钻"=>'bi_daizuan',
		"臂形态"=>'bi_xingtai',
		"爪带钻"=>'zhua_daizuan',
		"爪钉形状"=>'zhua_xingzhuang',
		"镶嵌方式"=>'style_xiangqian',
		"爪头数量"=>'zhua_num',
		"是否直爪"=>'is_zhizhua',
		"能否刻字"=>'is_kezi',	
		//"是否支持刻字"=>'could_world', v2
		//"是否支持改圈"=>'is_gaiquan',//v3 没有
 );
//必填
$is_require_arr =array("镶口"=>'1',"材质"=>'2',"指圈"=>'3',"18K可做颜色"=>'4');

//旧的产品线在新项目中产品的id 的key的对应
$new_pro_line = array (
        "0" => 5, // 其他(原名)
        "1" => 14,
        "2" => 4,
        "3" => 13, // 黄金饰品(原名)
        "4" => 6, // 结婚钻石饰品
        "5" => 6,          // "5" => array("item_name" => "钻石饰品"),
        "6" => 15,
        "7" => 17, // 彩宝及翡翠饰品(原名)
        "8" => 6,
        "9" => 16,
        "10" => 10,
        "11" => 12 
);
//查出上面这些属性的对应的id
$all_attr_arr = array();
$show_type_arr = array();

foreach($attribute_data as $key=>$val){
	$sql = "SELECT * FROM `".$attr_table."` where attribute_name= '".$key."'";
	$res = mysqli_query($conNew, $sql);
	$row = mysqli_fetch_row($res);
	if(empty($row)){
		continue;
	}
	$all_attr_arr[$row[1]] = $row[0];
	$show_type_arr[$row[1]] = $row[3];
}

$date_time = date("Y-m-d H:i:s");
$field = "`cat_type_id`, `product_type_id`, `attribute_id`, `is_show`, `is_default`, `is_require`, `status`, `attr_type`, `create_time`, `create_user`";

//新项目 10男戒;2女戒；11情侣戒
$cat_type_arr = array(1=>10,2=>2,3=>11); 
foreach ($old_product_line as $p_val){
    $old_product_type = $p_val;
    if($old_product_line != 2){
        $product_type = $new_pro_line[$old_product_type];//新项目的产品线
        foreach ($cat_type_arr as $c_val){
            $cat_type = $c_val;
            foreach($all_attr_arr as $key=>$val){
                 $attr_id = $val;
                 $attr_name = $key;
                 $show_type= $show_type_arr[$key];
                 $is_require = 0;
                 $attr_type =1;
                 if(array_key_exists($attr_name, $is_require_arr)){
                     $is_require = 1;
                     $attr_type =2;
                 }
                  $sql = "INSERT INTO `".$new_table."` (" . $field . ")  VALUES ('" . $cat_type . "', '" . $product_type . "','" . $attr_id . "','".$show_type."' ,1,$is_require,1,$attr_type,'".$date_time."',  'admin')";

                  if(!mysqli_query($conNew,$sql)){
                     echo "\n\n\n".$sql."\n\n\n";exit;
                 }else {
                     echo mysqli_insert_id($conNew)."\n";
                 }
             } 
        }
    }else{
        $sujin_type_arr = array(8,9,19);
        foreach ($sujin_type_arr as $sj_val){
            $product_type = $sj_val;
            foreach ($cat_type_arr as $c_val){
                $cat_type = $c_val;
                foreach($all_attr_arr as $key=>$val){
                     $attr_id = $val;
                     $attr_name = $key;
                     $show_type= $show_type_arr[$key];
                     $is_require = 0;
                     $attr_type =1;
                     if(array_key_exists($attr_name, $is_require_arr)){
                         $is_require = 1;
                         $attr_type =2;
                     }
                      $sql = "INSERT INTO `".$new_table."` (" . $field . ")  VALUES ('" . $cat_type . "', '" . $product_type . "','" . $attr_id . "','".$show_type."' ,1,$is_require,1,$attr_type,'".$date_time."',  'admin')";
                      if(!mysqli_query($conNew,$sql)){
                         echo "\n\n\n".$sql."\n\n\n";exit;
                     }else {
                         echo mysqli_insert_id($conNew)."\n";
                     }
                 } 
            }
        }
    }
}



echo "\n\n\n\n完成ok！";
die();
