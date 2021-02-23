<?php
/**
 *  -------------------------------------------------
 *   @file		: .php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: zhangruiying
 *   @date		:2015/4/21
 *   @update	:
 *  -------------------------------------------------
 */
header("Content-Type:text/html;charset=UTF-8");
ini_set("soap.wsdl_cache_enabled", "0"); //清空原有的wsdl缓存
$wsdl = "tofactory.wsdl";
if(strtolower(substr($_SERVER['QUERY_STRING'], -4)) == 'wsdl'){
	header("Content-type: text/xml");
	readfile($wsdl);
        exit;
}
$data = file_get_contents("php://input");
if($data)
{
	file_put_contents('/data/www/cuteframe_boss/api/logs/data.log', json_encode($data).PHP_EOL, FILE_APPEND);
	$file = './class/FactoryClass_hybird.php';
	$ext = is_file($file);
	if($ext)
	{
		require_once ($file);
	}
	else
	{
		die('接口文件不存在');
	}
	$server=new SoapServer($wsdl,array('trace'=> true,'exceptions'=>true,'cache_wsdl'=>WSDL_CACHE_NONE));
	$server->setClass('FactoryClass');
	$server->handle();
}
?>
