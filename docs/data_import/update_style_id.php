<?php 
/**
 *  -------------------------------------------------
 * 文件说明		款对公费数据导入
 * @file		: syte_base.php
 * @date 		: 2015-03-07 17:29:11
 * @author		: yangxt <yangxiaotong@163.com
 *  -------------------------------------------------
*/
header("Content-type:text/html;charset=utf-8");

require_once('ExportData2.class.php');
//新项目数据库
$new_conf = [
	'dsn'=>"mysql:host=192.168.40.251;dbname=front",
	'user'=>"yangfuyou",
	'password'=>"yangfuyou1q2w3e",
];

// 旧项目数据库
$old_conf = [
	'dsn'=>"mysql:host=192.168.40.251;dbname=kela_style",
	'user'=>"yangfuyou",
	'password'=>"yangfuyou1q2w3e",
];

$n_table = 'app_style_fee';	//新项目中的表
$o_table = 'style_style';	//旧项目中的表

$model = new ExportData($new_conf,$old_conf,$n_table,$o_table);



$model->updateNewItem('style_id','style_sn','base_style_info','10000');


?>