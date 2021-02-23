<?php
/**
 *  -------------------------------------------------
 *   @file		: api.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Zhangyuanyuan <1041522879@qq.com>
 *   @date		: 2015年1月19日
 *   @update	:
 *  -------------------------------------------------
 */
define('IN_API', true);
define('ROOT_PATH',str_replace('api.php', '', str_replace('\\', '/', __FILE__)));

$con = isset($_REQUEST['con']) ? addslashes($_REQUEST['con']) : '';
if(empty($con)){
    exit();
}
$apiFile = ROOT_PATH . 'apps/'.$con.'/api/api.php';
if(!file_exists($apiFile)){
    exit($apiFile." doesn't exists.");
}

$method = isset($_REQUEST['act']) ? addslashes($_REQUEST['act']) : "";
if(empty($method))
{
	die('no method');
}
if($method == 'index'){
    include(ROOT_PATH . 'apps/'.$con.'/api/index.php');
    exit;
}

require_once ROOT_PATH."frame/init.php";

$content_type = isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : '';
if (stripos($content_type,'application/json') !== false) {
    header('Content-Type: application/json');
    $json = json_decode(@file_get_contents('php://input'), true);
    if (!empty($json)) {
        $sign = isset($json['sign']) ? $json['sign'] : '';
        $filter = isset($json['filter']) ? json_encode($json['filter']) : '';
        $filter = str_replace(array("\\\"", "\\\\u"), array("\"", "\u"), $filter);
    } else {
        $sign = '';
        $filter = '';
    }
    
} else {

    $sign = isset($_POST["sign"]) ? trim($_POST["sign"]) : "";
    $filter = isset($_POST["filter"]) ? trim($_POST["filter"]) : "";
    $filter = str_replace(array("\\\"", "\\\\u"), array("\"", "\u"), $filter);
}

$data = Util::verifySign($con, $method, $filter, $sign);
if ($data === false) {
    $res = array("error" => 1, "error_msg" => 'HTTP/1.1 401 Unauthorized');
    die(json_encode($res));
}
/* 初始化 */
require_once(ROOT_PATH . 'apps/'.$con.'/api/config.php');
require_once(ROOT_PATH . 'apps/'.$con.'/api/app_mysql.php');
require_once($apiFile);
$api= new api($data);
if(false == method_exists($api,$method)){
    die('访问的类方法不存在！');
}else{
    $api->$method();
}