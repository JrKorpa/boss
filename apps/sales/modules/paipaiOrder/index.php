<?php
/**
 * Created by JetBrains PhpStorm.
 * 说明：post 和 get方法都可以使用
 * sdk 入口文件
 * User: denniszhu
 * Date: 12-8-13
 * Time: 下午4:02
 * To change this template use File | Settings | File Templates.
 */
header("content-type:text/html; charset=utf-8");
define('ROOT_PATH', str_replace('includes/modules/paipaiOrder/index.php', '', str_replace('\\', '/', __FILE__)));
require_once'includes/modules/paipaiOrder/PaiPaiOpenApiOauth.php';
?>