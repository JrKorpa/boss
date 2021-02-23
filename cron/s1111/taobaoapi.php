<?php
header('Content-type: text/html; charset=utf-8');
date_default_timezone_set('Asia/Shanghai');
//引入淘宝api文件
define('API_ROOT',ROOT_PATH.'/lib/');
//目前只处理淘宝的   为了扩展预留为数组
$from_arr = array(
	2 => array("ad_name"=> "淘宝B店", "api_path" =>"taobaoOrderApi")
);
$from_type = 2;  //默认来自淘宝B店
$from_ad = "000200230544";
$apiname = $from_arr[$from_type]["api_path"];
$file_path = API_ROOT.$apiname.'/index.php';
//引入接口文件
require_once($file_path);
//实例化对象
$apiModel = new $apiname();
?>