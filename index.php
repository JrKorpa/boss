<?php
/**
 *  -------------------------------------------------
 *   @file		: .php
 *   @link		: 珂兰钻石 www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-10-22
 *   @update	:
 *  -------------------------------------------------
 */

define('APP_DEBUG', 0);//设置工作模式  true 开发模式   false  正式运营
define('LOG_API_CALL', 0);//设置是否记录api调用日志
require_once('frame/init.php');
$_module = isset ( $_GET['mod'] ) ? $_GET['mod'] : '' ;
$app = new App($_module);
$app->run();
?>