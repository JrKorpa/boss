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


/*  数据更换  旧表:jxc_warehouse   新表:warehouse
====旧字段==================注释===============================新字段=====================================
	wh_id				=>字段说明							===> id					主键		
	wh_name				=>字段说明							===> name				仓库名称		
	wh_sn				=>字段说明							===> code						
						=>字段说明							===> remark				备注		
	create_time			=>字段说明							===> create_time					
	create_user			=>字段说明							===> create_user					
	is_delete			=>字段说明							===> is_delete					
	lock				=>字段说明							===> lock					
===========================================================================================================
*/	

$new_conf = [
	'dsn'=>"mysql:host=192.168.0.251;dbname=warehouse_shipping",
	'user'=>"yangfuyou",
	'password'=>"yangfuyou1q2w3e",
];

$old_conf = [
	'dsn'=>"mysql:host=192.168.0.251;dbname=test",
	'user'=>"yangfuyou",
	'password'=>"yangfuyou1q2w3e",
];

$n_table = 'warehouse';	
$o_table = 'jxc_warehouse';	

$pass = true;		//记录导出状态
$old_id = false;	//保留旧表主键

//旧表查询条件
$where = "`m`.`status` <> 0";

// 旧表联查

$model = new ExportData($new_conf,$old_conf,$n_table,$o_table,$pass,$old_id);

//设置导数量
$model->len = '5000';


//设置字段
$model->filter = [
'wh_id'=>'id',
'type'=>'type',
'wh_name'=>'name',
'wh_sn'=>'code',
'status'=>'is_delete'
]; 


//设置默认值
$model->default = [
'create_user'=>'system',
'create_time'=>date('Y-m-d H:i:s'),
'lock'=>'0'
];


//////////////////////////////////////////////////////////////////////
//TRUNCATE `warehouse`
//UPDATE `jxc_warehouse` SET `pass_status` = '0'

//获取旧表数据
$model->getOldDate($where,$pass);

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