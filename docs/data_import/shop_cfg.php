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

require_once('ExportData.class.php');

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

$n_table = 'shop_cfg';	//新项目中的表
$o_table = 'ecs_shop_cfg';	//旧项目中的表



$where = false;

$filter = array(
'shop_name'=>'shop_name',
'short_name'=>'short_name',
'shop_address'=>'shop_address',
'shop_phone'=>'shop_phone',
'shop_time'=>'shop_time',
'shop_traffic'=>'shop_traffic',
'shop_des'=>'shop_dec'
);

$dict = array();

$pass = false;	//true/false  如果设为true 请在旧表增加`pass_status`字段

$old_id = false; //true/false  如果设为true 请在新表增加`oldsys_id`字段


//////////////////////////////////////////////////////////////////////

$model = new ExportData($new_conf,$old_conf,$n_table,$o_table,$filter,0,0,$pass);


if($pass){
	$res = $model->addPassFiled();
	if(!$res){
		echo "添加'pass_status'字段失败";exit;
	}
}

if($old_id){
	$res = $model->addOldFiled();
	if(!$res){
		echo "添加'oldsys_id'字段失败";exit;
	}
}

//旧表主键
// print_r($model->old_pk);exit;
$old_data = $model->getOldDate($where,$pass);

if(empty($old_data)){
	echo "not data to update";
	exit;
}else{
	// print_r($old_data[0]);exit;
	$model->insertData($dict);
}




?>