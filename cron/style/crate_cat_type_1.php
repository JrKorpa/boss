<?php
error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");
define('ROOT_PATH',str_replace('crate_style_info.php', '', str_replace('\\', '/', __FILE__)));
include('Pinyin.class.php');
//require_once(ROOT_PATH . 'config/shell_config.php');
$pinyin_obj = new Pinyin();

$con = mysqli_connect('192.168.70.251','yangfuyou','yangfuyou1q2w3e');
if(!$con){
	die("Can not connect handleFrom");
}
//mysqli_query('SET NAMES UTF8',$con);
//款式分类
$table = 'app_cat_type';
mysqli_select_db($con,'front');

$t_sql = "TRUNCATE TABLE $table ";
$t_res = mysqli_query($con, $t_sql);
if($t_res){
    echo "数据已经清空";
}
$style_cat = array(	"戒指",	"吊坠",	"项链",	"耳钉",	"耳环",	"耳坠",	"手镯",	"手链",	"其他");
$field = "`cat_type_name`, `cat_type_code`, `note`, `parent_id`, `tree_path`, `pids`, `childrens`, `display_order`, `cat_type_status`, `is_system`";

//判断已经有数据则不在添加
$sql = "SELECT *  FROM `".$table."` WHERE `cat_type_name`='全部' ";
$res = mysqli_query($con,$sql);
$row = mysqli_fetch_row($res);
if(count($row)>0){
    echo '数据已经存在,请先清空';
    die;
}

//先插入一个全部
$num = count($style_cat);

$sql= "INSERT INTO `".$table."` (".$field.")  VALUES ( '全部', 'all', '', 0, '0', '', ".$num.", 0, 1, 1)" ;
mysqli_query($con,$sql);
$id = mysqli_insert_id($con);

foreach ( $style_cat as $val){
    $i =1;//排序
   $code =  $pinyin_obj->getQianpin($val);
   $sql= "INSERT INTO `".$table."` (".$field.")  VALUES ( '".$val."', '".$code."', '', 1, '0-1', '', 0, ".$i.", 1, 1)" ;
   mysqli_query($con,$sql);
   $cat_type_id = mysqli_insert_id($con);
   $pid = "1,".$cat_type_id;
   
   $sql ="UPDATE `".$table."` SET `pids`='".$pid."' WHERE `cat_type_id`=$cat_type_id";
   mysqli_query($con, $sql);
   $i++;
}
echo '表已经生成!';


