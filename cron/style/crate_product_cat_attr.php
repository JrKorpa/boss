<?php
/*
 * 创建产品线，款式分类属性
 */
error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");
define('ROOT_PATH',str_replace('crate_style_info.php', '', str_replace('\\', '/', __FILE__)));
include('Pinyin.class.php');
//require_once(ROOT_PATH . 'config/shell_config.php');
$pinyin_obj = new Pinyin();

//获取原来款式基本信息
$con = mysqli_connect('192.168.1.52','develop','123456');
if(!$con){
	die("Can not connect con");
}

 $attribute_arr  = array(
	"戒指"=>array(
		"镶口"=>'xiangkou',
		"材质"=>'style_caizhi',
		"指圈"=>'2',
		"是否刻字"=>'is_kezi',
		"是否支持刻字"=>'could_world',
		"是否围钻"=>'is_weizuan',
		"爪形态"=>'zhua_xingtai',
		"镶嵌方式"=>'style_xiangqian',
		"爪头数量"=>'zhua_num',
		"是否直爪"=>'is_zhizhua',
		"爪钉形状"=>'zhua_xingzhuang',
		"爪带钻"=>'zhua_daizuan',
		"臂形态"=>'bi_xingtai',
		"戒臂表面工艺处理"=>'jiebi_gongyi',
		"是否有副石"=>'is_fushi',
		"是否支持改圈"=>'is_gaiquan',
		"最大改圈范围"=>'style_gaiquan',
		"18K可做颜色"=>'kezuo_yanse',
		"证书"=>'zhengshu'
	)
 );
 
 //展示方式：1文本框，2单选，3多选，4下拉
  $attr_type_arr  = array(
		"镶口"=>'2',
		"材质"=>'2',
		"指圈"=>'2',
		"是否刻字"=>'1',
		"是否支持刻字"=>'1',
		"是否围钻"=>'1',
		"爪形态"=>'1',
		"镶嵌方式"=>'1',
		"爪头数量"=>'1',
		"是否直爪"=>'1',
		"爪钉形状"=>'1',
		"爪带钻"=>'1',
		"臂形态"=>'1',
		"戒臂表面工艺处理"=>'1',
		"是否有副石"=>'1',
		"是否支持改圈"=>'1',
		"最大改圈范围"=>'1',
		"18K可做颜色"=>'1',
		"证书"=>'1'
 );

//款式属性
$con = mysqli_connect('192.168.1.63','yangfuyou','yangfuyou1q2w3e');
$con = mysqli_connect('localhost','root','');
if(!$con){
	die("Can not connect con");
}
mysqli_query($con,'SET NAMES UTF8');
//产品线，款式分类，与属性的对应关系
$attr_table = 'app_attribute';
$table = 'rel_cat_attribute';
mysqli_select_db($con,'front');

//获取所有的属性
$sql = "SELECT * FROM `".$attr_table."` ";
$res = mysqli_query($con, $sql);
$all_attr_arr = array();
while ($row = mysqli_fetch_array($res)){
    $all_attr_arr[$row['attribute_name']] = $row['attribute_id'];
}

$field = "`cat_type_id`, `product_type_id`, `attribute_id`, `is_show`, `is_default`, `is_require`, `status`, `attr_type`, `create_time`, `create_user`, `info`, `default_val`";

$date_time = date("Y-m-d H:i:s");
$cat_id =2;
$product_type_id = 1;
foreach ($attribute_arr['戒指'] as $key=>$val){
    $attr_name = $key;
    if(array_key_exists($attr_name, $all_attr_arr)){
         $attr_id = $all_attr_arr[$attr_name];//属性id
         $attr_type = $attr_type_arr[$attr_name];
         $sql= "INSERT INTO `".$table."` (".$field.")  VALUES ( '".$cat_id."','".$product_type_id."', $attr_id, 1,1,1,1,".$attr_type.",'".$date_time."', 'admin','','' )" ;
        mysqli_query($con, $sql);
    }
}
mysqli_close($con);
echo "产品分类及产品线，属性完成";
die;

$_style_cat = array(
"1" => array("cat_name" => "戒指", "code_name" => "R", "drift"=> "0.2", "attr" => array(
	"1" => array("item_name" => "指圈范围", "type" => "within", "datetype"=>"num"),
	"2" => array("item_name" => "宽度(cm)", "type" => "text", "datetype"=>"num"),
	//"3" => array("item_name" => "鉴定证书", "type" => "checkbox", "val" => $_style_cert)
)),
"2" => array("cat_name" => "吊坠", "code_name" => "P", "drift"=> "0.1", "attr" => array(
	"1" => array("item_name" => "吊坠高度(cm)", "type" => "text", "datetype"=>"num"),
	"3" => array("item_name" => "含扣", "type" => "checkbox"),
	"2" => array("item_name" => "吊坠宽度(cm)", "type" => "text", "datetype"=>"num"),
	//"4" => array("item_name" => "鉴定证书", "type" => "checkbox", "val" => $_style_cert)
)),
"3" => array("cat_name" => "项链", "code_name" => "N", "drift"=> "0.1", "attr" => array(
	"1" => array("item_name" => "链长(cm)", "type" => "text", "datetype"=>"num"),
	"2" => array("item_name" => "最外圈链长(cm)", "type" => "text", "datetype"=>"num"),
	//"3" => array("item_name" => "鉴定证书", "type" => "checkbox", "val" => $_style_cert),
	"4" => array("item_name" => "坠高（套链）(cm)", "type" => "text", "datetype"=>"num"),
	"5" => array("item_name" => "坠宽（套链）(cm)", "type" => "text", "datetype"=>"num")
)),
"4" => array("cat_name" => "耳钉", "code_name" => "D", "drift"=> "0.1", "attr" => array(
	"1" => array("item_name" => "耳钉宽度(cm)", "type" => "text", "datetype"=>"num"),
	"2" => array("item_name" => "耳钉高度(cm)", "type" => "text", "datetype"=>"num"),
	"3" => array("item_name" => "耳迫类型", "type" => "radio", "val" => $_style_ear_force),
	//"4" => array("item_name" => "耳迫材质", "type" => "radio", "val" => $_style_ear_force),
)),
"5" => array("cat_name" => "耳环", "code_name" => "H", "drift"=> "0.1" , "attr" => array(
	"1" => array("item_name" => "耳环宽度(cm)", "type" => "text", "datetype"=>"num"),
	"2" => array("item_name" => "耳环直径(cm)", "type" => "text", "datetype"=>"num")
)),
"6" => array("cat_name" => "耳坠", "code_name" => "Z", "drift"=> "0.1", "attr" => array(
	"1" => array("item_name" => "长度(cm)", "type" => "text", "datetype"=>"num"),
	"2" => array("item_name" => "耳迫", "type" => "radio", "val" => $_style_ear_force)
)),
"7" => array("cat_name" => "手镯", "code_name" => "B", "drift"=> "0.15","attr" => array(
	"1" => array("item_name" => "手镯长度(cm)", "type" => "text", "datetype"=>"num"),
	"2" => array("item_name" => "手镯宽度(cm)", "type" => "text", "datetype"=>"num"),
	"3" => array("item_name" => "手链扣", "type" => "text"),
	"4" => array("item_name"=> "直径(cm)", "type" => "text", "datetype"=>"num")
)),
"8" => array("cat_name" => "手链", "code_name" => "S", "drift"=> "0.15","attr" => array(
	"1" => array("item_name" => "手链长度(cm)", "type" => "text", "datetype"=>"num"),
	"2" => array("item_name" => "手链宽度(cm)", "type" => "text", "datetype"=>"num"),
	//"3" => array("item_name" => "鉴定证书", "type" => "checkbox", "val" => $_style_cert)
)),
"9" => array("cat_name" => "脚链", "code_name" => "F", "drift"=> "0.15"),
"13" => array("cat_name" => "其他", "code_name" => "Q")
);