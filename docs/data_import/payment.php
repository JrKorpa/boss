<?php 
/**
 *  -------------------------------------------------
 * 文件说明		支付方式明细数据导入
 * @file		: app_deal_detail.php
 * @date 		: 2015-03-07 17:29:11
 * @author		: yangxt <yangxiaotong@163.com
 *  -------------------------------------------------
*/
header("Content-type:text/html;charset=utf-8");

require_once('ExportData2.1.class.php');

//新项目数据库
$new_conf = [
	'dsn'=>"mysql:host=localhost;dbname=cuteframe",
	'user'=>"root",
	'password'=>"yangxt",
];

// 旧项目数据库
$old_conf = [
	'dsn'=>"mysql:host=localhost;dbname=test",
	'user'=>"root",
	'password'=>"yangxt",
];

$n_table = 'payment';		//新项目中的表
$o_table = 'ecs_payment';	//旧项目中的表

$pass = true;
$old_id = false;	//保留旧表主键

$where = false;

$model = new ExportData($new_conf,$old_conf,$n_table,$o_table,$pass,$old_id);


$model->filter = array(
'pay_id'=>'id',
'pay_name'=>'pay_name',
'pay_code'=>'pay_code',
'enabled'=>'is_enabled',
'is_cod'=>'is_cod',
'is_display'=>'is_display',
'is_web'=>'is_web',
);
$model->default = [
'addby_id'=>'1',
'is_deleted'=>'0',
'add_time'=>time(),
'pay_fee'=>'0'
];


//////////////////////////////////////////////////////////////////////
//获取旧表数据
$model->getOldDate($where,$old_id);

$data = $model->old_data;

if(empty($data)){
	echo "not data to update";
	exit;
}else{
	// print_r(count($data));exit;
	// print_r($data[0]);exit;
	$model->insertData();
}



?>