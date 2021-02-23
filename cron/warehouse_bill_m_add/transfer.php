<?php
/**
 * 脚本功能：清洗数据
 * 2016年1月1日后，部分数据有漏，此为再次调拨，
 * 调拨完成后再用M单加价销售至原分公司。
 * @author William Huang
 * Entrance
 */

error_reporting(E_ALL ^ E_DEPRECATED);
date_default_timezone_set("Asia/Shanghai");
header("Content-type:text/html;charset=utf8;");

define('ROOT_PATH', str_replace('transfer.php', '', str_replace('\\', '/', __FILE__)));

require(ROOT_PATH."transfer_model.php"); 
require(ROOT_PATH."transfer_api.php"); 
require(ROOT_PATH."app_mysql.php"); //数据库操作对象

$config = array();
$config['db_type'] = 'mysql';
$config['db_port'] = 3306;
$config['db_name'] = 'warehouse_shipping';

/*$config['db_host'] = 'localhost';
$config['db_user'] = 'root';
$config['db_pwd'] = '';*/

// $config['db_host'] = '192.168.0.95';
$config['db_host'] = '192.168.1.59';
$config['db_user'] = 'cuteman';
$config['db_pwd'] = 'QW@W#RSS33#E#';

$db = new KELA_API_DB($config);

// 限制读取的仓库数:(参数0为正式跑，不输入参数时默认为1，为试跑)
$run_type = isset($argv[1]) ? ($argv[1]) : $_GET['num']; 
if (!isset($run_type)) {
    $run_type = 1;
}

// Entrance：
new autoTransfer($run_type);

