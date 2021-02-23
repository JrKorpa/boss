<?php 
/**
 *  -------------------------------------------------
 * 文件说明		体验店数据导入
 * @file		: app_deal_detail.php
 * @date 		: 2015-03-07 17:29:11
 * @author		: yangxt <yangxiaotong@163.com
 *  -------------------------------------------------
*/
header("Content-type:text/html;charset=utf-8");

require_once('ExportData2.class.php');

//新项目数据库
$new_conf = [
	'dsn'=>"mysql:host=192.168.0.251;dbname=cuteframe",
	'user'=>"yangfuyou",
	'password'=>"yangfuyou1q2w3e",
];

// 旧项目数据库
$old_conf = [
	'dsn'=>"mysql:host=192.168.0.251;dbname=finance",
	'user'=>"yangfuyou",
	'password'=>"yangfuyou1q2w3e",
];

$n_table = 'sales_channels';	//新项目中的表
$o_table = 'ecs_department_channel';	//旧项目中的表


$pass = true;		//记录导出状态
$old_id = false;	//保留旧表主键

//条件
$where = false;

$model = new ExportData($new_conf,$old_conf,$n_table,$o_table,$pass,$old_id);

//设置导数量
$model->len = '1000';

//设置字段
$model->filter = [
'dep_name'=>'channel_name'
];


//////////////////////////////////////////////////////////////////////

$model->getOldDate($where,$pass);

$data = $model->old_data;


if(empty($data)){
	echo "not data to update";
	exit;
}else{
	// print_r(count($old_data));exit;
	// print_r($data[0]);exit;
	$model->insertData();
}




?>