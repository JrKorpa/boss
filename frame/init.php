<?php
/*
*  -------------------------------------------------
*   @file		: init.php
*   @link		:  www.kela.cn
*   @copyright	: 2014-2024 kela Inc
*   @author		: Laipiyang <462166282@qq.com>
*   @date		: 2014-04-28
*   @update		:
*  -------------------------------------------------
*/
/*
*  -------------------------------------------------
*   框架入口文件
*  -------------------------------------------------
*/
//session_start();

header('Content-Type: text/html; charset=utf-8');//设置系统输出编码

date_default_timezone_set('Asia/Shanghai');//设置时区

//根据不同模式做调试信息的处理
defined("APP_DEBUG") or define("APP_DEBUG", false);


if(APP_DEBUG)
{
// 	error_reporting(E_ALL);//错误报告设置
	error_reporting(E_ALL ^ E_NOTICE);
	ini_set('display_errors',1);//是否显示PHP错误信息，1显，0不显;
}
else
{
	ini_set('display_errors',0);//是否显示PHP错误信息，1显，0不显;
}

ini_set('session.gc_maxlifetime', 36000);//10小时过期

//安全过滤
require_once "common/phpsafe.php";
//加载常量定义
require_once "common/define.php";
//加载通用业务配置
require_once "common/data_config.php";
//加载核心类文件
require_once KELA_CLASS.'/Kela.class.php';
Kela::start();
?>