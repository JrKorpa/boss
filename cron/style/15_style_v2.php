<?php
// v2款属性
error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");
//此文件是where条件
include 'style_where.php';

$conOld = mysqli_connect('192.168.1.55','style_zyy','KELAzhangyuanyuan123','kela_style');
$conNew =  mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');

$old_style = 'style_style';	
$new_table = "rel_style_attribute";
$attr_table = 'app_attribute';
$attr_table_value = 'app_attribute_value';
// 老款式库

/*$t_sql = "TRUNCATE TABLE `front`.$new_table ";
$t_res = mysqli_query($conNew,$t_sql);
if($t_res){
    echo $old_style . "数据已经清空\n\n\n\n";
}*/

$product_type = 6;
$cat_type = 2;

/*主石形状、副石、副石形状
是否群镶、镶嵌方式、爪数量、是否直爪、爪钉形状、爪带钻、
、可否支持改圈、戒臂形态、戒臂带钻、戒臂表面工艺处理、是否支持刻字、证书
可做材质、镶口、颜色

//没有列的：颜色，是否有副石
注意：戒臂带钻 就是：否有副石
是否围钻 就是否群镶

v3没有；
最大改圈范围

*/
$attribute_data  = array(
		"镶口"=>'1',
		"材质"=>'2',
		"指圈"=>'3',
		"能否刻字"=>'is_kezi',	
		"镶嵌方式"=>'style_xiangqian',
		"爪形态"=>'zhua_xingtai',//v3没有
		"18K可做颜色"=>'kezuo_yanse',
		"是否群镶"=>'is_weizuan',
		"证书"=>'zhengshu',//v2里没有单新项目有数据
		"最大改圈范围"=>'style_gaiquan',
		"是否有副石"=>'is_fushi',
		"表面工艺"=>'jiebi_gongyi',
		"戒臂带钻"=>'bi_daizuan',
		"臂形态"=>'bi_xingtai',
		"爪带钻"=>'zhua_daizuan',
		"爪钉形状"=>'zhua_xingzhuang',
		"爪头数量"=>'zhua_num',
    //"是否支持刻字"=>'could_world', v1
		//"是否直爪"=>'is_zhizhua',//v2没有
		//"是否支持改圈"=>'is_gaiquan',//v3 没有
 );


//查出上面这些属性的对应的id
/*$sql = "SELECT * FROM `".$attr_table."` ";
$res = mysqli_query($con, $sql);
$all_attr_arr = array();
while ($row = mysqli_fetch_array($res)){
    $all_attr_arr[$row['attribute_name']] = $row['attribute_id'];
}*/

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
$field = " `cat_type_id`, `product_type_id`, `style_sn`, `attribute_id`,  `show_type`, `create_time`, `create_user`, `info`, `style_id` ";

$old_table = 'style_style';
$sql = "SELECT count(*) FROM " . $old_table . "  WHERE `is_new` =1 ".$where;
$t = mysqli_query($conOld,$sql);
$cnt = mysqli_fetch_row($t);

$len = $cnt[0];
$forsize = ceil($len / 1000);
for($ii = 1; $ii <= $forsize; $ii ++){
    echo $ii."----------------------------------------------------------"."\n";
    $offset = ($ii - 1) * 1000;
    $sql = "SELECT *  FROM " . $old_style . "   WHERE `is_new`=1 ".$where." limit $offset,1000";
    $res = mysqli_query($conOld,$sql);
    $old_style_data = array ();
    while ( $row = mysqli_fetch_array($res) ){
		$style_id = $row['style_id'];
		$style_sn = $row['style_sn'];
		$sex = $row['style_for_cat'];
		//style_for_cat ：1男戒;2女戒；3情侣戒
		//新项目 10男戒;2女戒；11情侣戒
		$cat_type_arr = array(1=>10,2=>2,3=>11); 
		$cat_type = $cat_type_arr[$sex]; 
		foreach($all_attr_arr as $key=>$val){
			$attr_id = $val;
			$show_type= $show_type_arr[$key];
			 $sql = "INSERT INTO `".$new_table."` (" . $field . ")  VALUES ('" . $cat_type . "', '" . $product_type . "', '" . $style_sn . "','" . $attr_id . "','".$show_type."' ,'".$date_time."',  'admin', '','1')";

			 if(!mysqli_query($conNew,$sql)){
				echo "\n\n\n".$sql."\n\n\n";exit;
			}else {
				echo mysqli_insert_id($conNew)."\n";
			}
		}
    }
}
echo "\n\n\n\n完成ok！";
die();
