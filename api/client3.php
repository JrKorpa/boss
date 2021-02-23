<?php
/**
 *  -------------------------------------------------
 *   @file		: .php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: zhangruiying
 *   @date		:
 *   @update	:
 *  -------------------------------------------------
 */
header("Content-Type:text/html;charset=UTF-8");
ini_set("soap.wsdl_cache_enabled", "0"); //清空原有的wsdl缓存
$client = new SoapClient("http://boss.kela.cn/api/server.php?wsdl",array('trace'=> true,'exceptions'=>true,'cache_wsdl'=>WSDL_CACHE_NONE));
$client->soap_defencoding = 'utf-8';
$client->xml_encoding = 'utf-8';
try{
	 $auth = array(
        'sign'=>md5('BC9259411BC925945'),//加密串
	    'fid'=>$_REQUEST['fid'],//工厂ID
		'bc_id'=>$_REQUEST['bc_id']
        );
	$result=$client->getInfoById($auth);
	print_r($result);
}catch(Exception $e) {
	print_r ($e);
}
?>