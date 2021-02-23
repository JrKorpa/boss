<?php
/**
 *  -------------------------------------------------
 * 文件说明		同步商品状态及信息
 * @file		: warehouse_goods.php
 * @date 		: 2015-03-16 11:29:11
 * @author		: yangxt <yangxiaotong@163.com
 *  -------------------------------------------------
 */
header("Content-type:text/html;charset=utf-8");
require_once('00_ExportData.class.php');
date_default_timezone_set('Asia/Shanghai');
set_time_limit(0);
ini_set('memory_limit','2000M');

//新项目数据库
$new_conf = [
    'dsn'=>"mysql:host=192.168.1.93;dbname=warehouse_shipping",
    'user'=>"cuteman",
    'password'=>"QW@W#RSS33#E#",
];

// 旧项目数据库
$old_conf = [
    'dsn'=>"mysql:host=192.168.1.79;dbname=jxc",
    'user'=>"root",
    'password'=>"n+g1kMY#2]fZ",
];

$n_table = 'warehouse_goods';
$o_table = 'jxc_goods';

$pass = false;	//记录导出状态
$old_id = true;	//保留旧表主键

/*---------------*/
$model = new ExportData($new_conf,$old_conf,$n_table,$o_table,$pass,$old_id);

$res = $model->intersectGoods();
if($res){
    echo iconv('UTF-8', 'GBK', "SUCCESS\r\n");
}else{
    echo iconv('UTF-8', 'GBK', "LOSE\r\n");
}








?>