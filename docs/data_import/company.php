<?php 
/**
 *  -------------------------------------------------
 * 文件说明		JXC公司 数据导入
 * @file		: warehouse.php
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
	'dsn'=>"mysql:host=192.168.0.251;dbname=test",
	'user'=>"yangfuyou",
	'password'=>"yangfuyou1q2w3e",
];

$n_table = 'company';	//新项目中的表
$o_table = 'jxc_processors';		//旧项目中的表


$pass = true;		//记录导出状态
$old_id = false;	//保留旧表主键

//在职
$where = "`m`.`pt_id` = 0";

$model = new ExportData($new_conf,$old_conf,$n_table,$o_table,$pass,$old_id);

//设置导数量
$model->len = '1000';

$model->filter = array(
'p_id'=>'id',	
'p_name'=>'company_name',
'p_sn'=>'company_sn',
'contact'=>'contact',
'phone'=>'phone',
'address'=>'address',
'kaihuhang'=>'bank_of_deposit',
'kaihuzhanghao'=>'account',
'fapiao'=>'receipt',
'is_qianzi'=>'is_sign',
'info'=>'remark',
);

$model->default = [
'create_time'=>time(),
'create_user'=>'1',
'is_deleted'=>'0',
'is_system'=>'0'
];


// $model->whitelist = ['company'=>'id'];

//////////////////////////////////////////////////////////////////////
//UPDATE `jxc_processors` SET `pass_status` = '0' WHERE `pt_id` = '0'
//TRUNCATE `company`
//获取旧表数据
$model->getOldDate($where,$pass);

$data = $model->old_data;
// print_r($data);exit;

if(empty($data)){
	echo "not data to update";
	exit;
}else{
	// print_r(count($data));exit;
	// print_r($data[0]);exit;
	$model->insertData();
}

?>