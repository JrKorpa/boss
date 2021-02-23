<?php 
/**
 *  -------------------------------------------------
 * 文件说明		仓库 数据导入
 * @file		: warehouse.php
 * @date 		: 2015-3-10 16:13:31
 * @author		: yangxt <yangxiaotong@163.com
 *  -------------------------------------------------
*/
header("Content-type:text/html;charset=utf-8");
require_once('ExportData2.class.php');

$new_conf = [
	'dsn'=>"mysql:host=192.168.1.93;dbname=warehouse_shipping",
	'user'=>"cuteman",
	'password'=>"QW@W#RSS33#E#",
];

$old_conf = [
	'dsn'=>"mysql:host=192.168.1.79;dbname=jxc",
	'user'=>"root",
	'password'=>"n+g1kMY#2]fZ",
];

$n_table = 'warehouse';	
$o_table = 'jxc_warehouse';	

$pass = false;		//记录导出状态
$old_id = false;	//保留旧表主键


// 旧表联查

$model = new ExportData($new_conf,$old_conf,$n_table,$o_table,$pass,$old_id);

$model->insert2Warehouse();







?>