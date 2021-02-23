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

$con = mysqli_connect('localhost','root','');
if(!$con){
	die("Can not connect con");
}
mysqli_select_db($con,'kela_style');
mysqli_query($con,'SET NAMES UTF8');

// 钻石规格单价:guige_a-.guige_b主石规格; price价格; type = 1
$sql = "select guige_a,guige_b,price from style_price_info where type = 1;";
$res = mysqli_query($con, $sql);
$zuanshi_arr = array();
while ($row = mysqli_fetch_array($res)){
    $zuan_arr[] = $row;
}

	
//材质单价:price_type ：1=》18K；2=>PT950; price:价格; type = 2
//$price_type = $this -> filter['caizhi'];
$sql = "select price_type,price from style_price_info where type = 2;";
$res = mysqli_query($con, $sql);
$caizhi_arr = array();
while ($row = mysqli_fetch_array($res)){
    $caizhi_arr[] = $row;
}
	
//金损率:price_type:1男戒2女戒3情侣男戒4情侣女戒; jinsun_caizhi:2=>18K;1=>PT950; price:价格; type = 3
//1为男戒;2为女戒;3为情侣男戒;4为情侣女戒
	
$sql = "select jinsun_caizhi,price_type,price from style_price_info where type = 3 ;";
$res = mysqli_query($con, $sql);
$jinsun_arr = array();
while ($row = mysqli_fetch_array($res)){
    $jinsun_arr[] = $row;
}

mysqli_close($con);
//$con = mysqli_connect('192.168.1.63','yangfuyou','yangfuyou1q2w3e');
$con = mysqli_connect('localhost','root','');
if(!$con){
	die("Can not connect con");
}
mysqli_query($con,'SET NAMES UTF8');

//款式基本信息
$table = 'app_attribute';
mysqli_select_db($con,'front');
var_dump($zuan_arr);
foreach($zuan_arr as $val){

}

mysqli_close($con);
echo "属性完成";



?>