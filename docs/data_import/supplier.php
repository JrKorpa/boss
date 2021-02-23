<?php 
/**
 *  -------------------------------------------------
 * 文件说明		供应商 数据导入
 * @file		: supplier.php
 * @date 		: 2015-03-07 17:29:11
 * @author		: yangxt <yangxiaotong@163.com
 *  -------------------------------------------------
*/
header("Content-type:text/html;charset=utf-8");
require_once('ExportData2.1.class.php');

date_default_timezone_set('Asia/Shanghai');
set_time_limit(0);
ini_set('memory_limit','2000M');

// 新项目数据库
$new_conf = [
	'dsn'=>"mysql:host=192.168.1.93;dbname=kela_supplier",
	'user'=>"cuteman",
	'password'=>"QW@W#RSS33#E#",
];


// 旧项目数据库
$old_conf = [
	'dsn'=>"mysql:host=192.168.1.79;dbname=jxc",
	'user'=>"root",
	'password'=>"n+g1kMY#2]fZ",
];

$n_table = 'app_processor_info';	//新项目中的表
$o_table = 'jxc_processors';		//旧项目中的表


$pass = true;		//记录导出状态
$old_id = false;	//保留旧表主键

//
$where = "`m`.`pt_id` > 0";

$model = new ExportData($new_conf,$old_conf,$n_table,$o_table,$pass,$old_id);

//设置导数量
$model->len = '3000';

$model->filter = array(
'p_id'=>'id',	
'p_sn'=>'code',
'p_name'=>'name',
'email'=>'pro_email',
'contact'=>'pro_contact',
'phone'=>'pro_phone',
'address'=>'pro_address',
'addtime'=>'create_time',
'kaihuzhanghao'=>'account',
'info'=>'info',
'payment'=>'balance_type',
'status'=>'status',
);

$model->default = [
'create_id'=>'1',
'create_user'=>'system',
'create_time'=>date('Y-m-d H:i:s')
];

//$model->dict = [
//	'status'=>['1'=>'1','0'=>'2'],
//];


//////////////////////////////////////////////////////////////////////
//获取旧表数据
$model->getOldDate($where,$pass);

$data = $model->old_data;
// print_r($data);exit;
//
//if(empty($data)){
//	echo "not data to update";
//	exit;
//}else{
//	// print_r(count($data));exit;
//	// print_r($data[0]);exit;
//	$model->insertData();
//}


//回写供应商申请表
 $model->insertRecordSupplier();







?>