<?php 
/**
 *  -------------------------------------------------
 * 文件说明		款对工厂 数据导入
 * @file		: rel_style_factory.php
 * @date 		: 2015-03-07 17:29:11
 * @author		: yangxt <yangxiaotong@163.com
 *  -------------------------------------------------
*/
// header("Content-type:text/html;charset=utf-8");
require_once('ExportData2.class.php');


/*  数据更换  旧表:style_factory   新表:rel_style_factory
====旧字段==================注释===============================新字段=====================================
	f_id				=>字段说明						===> f_id			主键		
	style_id 			=>字段说明						===> style_id		款式ID
	######## 			=>字段说明						===> style_sn		款号		
	factory_id 			=>字段说明						===> factory_id		工厂id		
	factory_sn	 		=>字段说明						===> factory_sn		工厂模号		
	factory_fee	 		=>字段说明						===> factory_fee 	工厂费用		
	xiangkou 			=>字段说明						===> xiangkou		镶口						
	is_def 				=>字段说明						===> is_def			是否默认;0为否;1为是;				
	is_factory 		 	=>字段说明						===> is_factory		是否默认工厂；0为否 ；1为是
	######## 			=>字段说明						===> is_cancel		是否作废，1正常，2作废			
===========================================================================================================
*/	

$new_conf = [
	'dsn'=>"mysql:host=192.168.0.251;dbname=front",
	'user'=>"yangfuyou",
	'password'=>"yangfuyou1q2w3e",
];

$old_conf = [
	'dsn'=>"mysql:host=192.168.0.251;dbname=kela_style",
	'user'=>"yangfuyou",
	'password'=>"yangfuyou1q2w3e",
];

$n_table = 'rel_style_factory';	
$o_table = 'style_factory';	

$pass = true;		//记录导出状态
$old_id = false;	//保留旧表主键

//旧表查询条件
$where = false;

//旧表联查
$table2 = 'style_style';
$no = ['style_id','style_id'];
$field_arr = [
'style_sn'=>'style_sn'
];


$model = new ExportData($new_conf,$old_conf,$n_table,$o_table,$pass,$old_id);

//设置导数量
$model->len = '10000';

//设置需要提取字段,如果为空则提取全部
$model->filter = [
'factory_id'=>'factory_id',
'factory_sn'=>'factory_sn',
'factory_fee'=>'factory_fee',
'xiangkou'=>'xiangkou',
'is_def'=>'is_def',
'is_factory'=>'is_factory'
];


//替换数据字典 'filed'=>['0'=>'1'],
$model->dict = [];

//设置默认值
$model->default = [
'is_cancel'=>'1'
];


//////////////////////////////////////////////////////////////////////

//获取旧表数据
$model->getOldDate($where,$pass,$table2,$no,$field_arr);

$data = $model->old_data;

if(empty($data)){
	echo "not data to update";
	exit;
}else{
	// print_r(count($old_data));exit;
	// print_r($data[0]);exit;
	$model->insertData();
}

////////////////////////////////////////////////////////////////
//导完旧数据之后，更新 style_id 字段
// $model->updateNewItem('style_id','style_sn','base_style_info','10000');





?>