<?php 
/**
 *  -------------------------------------------------
 * 文件说明		财务应付明细数据导入
 * @file		: app_deal_detail.php
 * @date 		: 2015-03-07 17:29:11
 * @author		: yangxt <yangxiaotong@163.com
 *  -------------------------------------------------
*/
header("Content-type:text/html;charset=utf-8");

require_once('ExportData.class.php');

//新项目数据库
$new_conf = [
	'dsn'=>"mysql:host=192.168.0.251;dbname=finance",
	'user'=>"yangfuyou",
	'password'=>"yangfuyou1q2w3e",
];

// 旧项目数据库
$old_conf = [
	'dsn'=>"mysql:host=192.168.0.251;dbname=kela_income",
	'user'=>"yangfuyou",
	'password'=>"yangfuyou1q2w3e",
];

$n_table = 'pay_jxc_order';	//新项目中的表
$o_table = 'pay_jxc_order';	//旧项目中的表


//旧表记录字段
$pass = true;	//true/false  如果设为true 请在旧表增加`pass_status`字段

$where = false;

$filter = array();

$dict = array();


//////////////////////////////////////////////////////////////////////

$model = new ExportData($new_conf,$old_conf,$n_table,$o_table,$filter,0,2000);

//旧表主键
// print_r($model->old_pk);exit;
$old_data = $model->getOldDate($where,$filter,$pass);

if(empty($old_data)){
	echo "not data to update";
	exit;
}else{
	// print_r($old_data[0]);exit;
	$model->insertData($dict);
}




?>