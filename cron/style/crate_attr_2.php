<?php
/*
 * 创建属性
 */
error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");
define('ROOT_PATH',str_replace('crate_style_info.php', '', str_replace('\\', '/', __FILE__)));
include('Pinyin.class.php');
//require_once(ROOT_PATH . 'config/shell_config.php');
$pinyin_obj = new Pinyin();

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



 //'1=>''文本框'',2=>''单选'',3=>''多选'',4=>''下拉列表''',
 $attr_show_type_arr  = array(
		"镶口"=>'3',
		"材质"=>'3',
		"指圈"=>'3',
		"是否刻字"=>'4',
		"是否支持刻字"=>'4',
		"是否围钻"=>'4',
		//"爪形态"=>'4',
		"镶嵌方式"=>'4',
		"爪头数量"=>'4',
		"是否直爪"=>'4',
		"爪钉形状"=>'4',
		"爪带钻"=>'4',
		"臂形态"=>'4',
		"戒臂表面工艺处理"=>'3',
		"是否有副石"=>'4',
		//"是否支持改圈"=>'4',
		"最大改圈范围"=>'4',
		"18K可做颜色"=>'3',
		"证书"=>'3'
 );

//$con = mysqli_connect('192.168.70.251','yangfuyou','yangfuyou1q2w3e');
$con = mysqli_connect('localhost','root','');
if(!$con){
	die("Can not connect con");
}
mysqli_query($con,'SET NAMES UTF8');

//款式基本信息
$table = 'app_attribute';
mysqli_select_db($con,'front');

$t_sql = "TRUNCATE TABLE $table ";
$t_res = mysqli_query($con, $t_sql);
if($t_res){
    echo "数据已经清空";
}

 $sql= "INSERT INTO `".$table."` (".$field.")  VALUES ( '".$attr_name."',  '".$attr_code."', $show_type, 1, '".$date_time."', 'admin','' )" ;
    mysqli_query($con, $sql);


$style_cat = array(	"戒指"=>2,	"吊坠",	"项链",	"耳钉",	"耳环",	"耳坠",	"手镯",	"手链",	"其他");
$field = " `attribute_name`, `attribute_code`, `show_type`, `attribute_status`, `create_time`, `create_user`, `attribute_remark`";

$date_time = date("Y-m-d H:i:s");
foreach ($attribute_arr['戒指'] as $key=>$val){
    $attr_name = $key;
    $attr_code =  $pinyin_obj->getQianpin($key);
    $show_type = $attr_show_type_arr[$attr_name];
    $sql= "INSERT INTO `".$table."` (".$field.")  VALUES ( '".$attr_name."',  '".$attr_code."', $show_type, 1, '".$date_time."', 'admin','' )" ;
    mysqli_query($con, $sql);
}


$_style_cat = array(
/*"1" => array("cat_name" => "戒指", "code_name" => "R", "drift"=> "0.2", "attr" => array(
	"1" => array("item_name" => "指圈范围", "type" => "within", "datetype"=>"num"),
	"2" => array("item_name" => "宽度(cm)", "type" => "text", "datetype"=>"num"),
	//"3" => array("item_name" => "鉴定证书", "type" => "checkbox", "val" => $_style_cert)
)),*/
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
	//"9" => array("cat_name" => "脚链", "code_name" => "F", "drift"=> "0.15"),
	//"13" => array("cat_name" => "其他", "code_name" => "Q")
);

$other_show_type_arr = array("text"=>1,"radio"=>2); 
//其他分类的属性
foreach($_style_cat as $val){
	foreach($val as $k_val){
		foreach($k_val as $i_val){
		$attr_name = $i_val['item_name'];
		
		$attr_code =  $pinyin_obj->getQianpin(str_replace("(cm)","",$attr_name));
		$show_type = $other_show_type_arr[$i_val['type']];
		$sql= "INSERT INTO `".$table."` (".$field.")  VALUES ( '".$attr_name."',  '".$attr_code."', $show_type, 1, '".$date_time."', 'admin','' )" ;
		mysqli_query($con, $sql);
		}
	}
}


mysqli_close($con);
echo "属性完成";



?>