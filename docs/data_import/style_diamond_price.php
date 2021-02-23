<?php 
/**
 *  -------------------------------------------------
 * 文件说明		款式库[钻石]数据导入
 * @file		: syte_base.php
 * @date 		: 2015-03-07 17:29:11
 * @author		: yangxt <yangxiaotong@163.com
 *  -------------------------------------------------
*/
header("Content-type:text/html;charset=utf-8");

require_once('ExportData.class.php');

//新项目数据库
$new_conf = [
	'dsn'=>"mysql:host=192.168.0.251;dbname=front",
	'user'=>"yangfuyou",
	'password'=>"yangfuyou1q2w3e",
];

// 旧项目数据库
$old_conf = [
	'dsn'=>"mysql:host=192.168.0.251;dbname=kela_style",
	'user'=>"yangfuyou",
	'password'=>"yangfuyou1q2w3e",
];

$n_table = 'app_diamond_price';	//新项目中的表
$o_table = 'style_price_info';	//旧项目中的表


//钻石
$where = "`type` = '1'";

$filter = array(
'guige_a'=>'guige_a',
'guige_b'=>'guige_b',
'price'=>'price'
);

$dict = [
];

//记录是否已经导入
$pass = true;	//true/false  如果设为true 请在旧表增加`pass_status`字段
//保存旧项目主键
$old_id = false; //true/false  如果设为true 请在新表增加`oldsys_id`字段


//////////////////////////////////////////////////////////////////////

$model = new ExportData($new_conf,$old_conf,$n_table,$o_table,$filter,0,0,$pass,$old_id);


// if($pass){
// 	$res = $model->addPassFiled();
// 	if(!$res){
// 		echo "添加'pass_status'字段失败";exit;
// 	}else{
// 		echo "添加'pass_status'字段成功";exit;
// 	}
// }


// if($old_id){
// 	$res2 = $model->addOldFiled();
// 	if(!$res2){
// 		echo "添加'oldsys_id'字段失败";exit;
// 	}
// }




//旧表主键
// print_r($model->old_pk);exit;
$old_data = $model->getOldDate($where,$pass);

if(empty($old_data)){
	echo "not data to update";
	exit;
}else{
	// print_r(count($old_data));exit;
	// print_r($old_data[0]);exit;
	$model->insertData($dict);
}




?>