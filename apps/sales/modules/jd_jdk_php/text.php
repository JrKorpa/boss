<?php 
/*
 * 
 * 调用封装的订单日志查询功能
 * $where 需要做urlencode
 * 
 * 
 */
define('IN_ECS', true);
include_once('./index.php');
date_default_timezone_set ('Asia/Shanghai');


$cc = new jd_jdk_php();

$data_info = $cc->get_api_goods();

?>