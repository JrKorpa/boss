<?php 
/**
 *  -------------------------------------------------
 * 文件说明		系统用户数据导入
 * @file		: user.php
 * @date 		: 2015-03-07 17:29:11
 * @author		: yangxt <yangxiaotong@163.com
 *  -------------------------------------------------
*/
header("Content-type:text/html;charset=utf-8");

require_once('ExportData2.class.php');

//新项目数据库
$new_conf = [
	'dsn'=>"mysql:host=192.168.40.251;dbname=cuteframe",
	'user'=>"yangfuyou",
	'password'=>"yangfuyou1q2w3e",
];

// 旧项目数据库
$old_conf = [
	'dsn'=>"mysql:host=192.168.40.251;dbname=test",
	'user'=>"yangfuyou",
	'password'=>"yangfuyou1q2w3e",
];

$n_table = 'user';	//新项目中的表
$o_table = 'users';	//旧项目中的表


$pass = true;		//记录导出状态
$old_id = false;	//保留旧表主键

//在职
$where = "'password' <> '离职'";

// 旧表联查
$table2 = false;
$no = [];
$field_arr = [];

$model = new ExportData($new_conf,$old_conf,$n_table,$o_table,$pass,$old_id);

//设置导数量
$model->len = '5000';

$model->filter = array(
'user_name'=>'account',
'user_pwd'=>'password',
'real_name'=>'real_name',
'phone'=>'phone',
'sex'=>'gender',
'email'=>'email',
'uin'=>'uin'
);

//替换数据字典 'filed'=>['0'=>'1'],
$model->dict = [
'sex'=>['0'=>'0','1'=>'0','2'=>'1']
];


//设置默认值
$model->default = [
'user_type'=>'3',
'is_deleted'=>'0',
'gender'=>'0',
'birthday'=>'1990-01-01',
'join_date'=>'2014-02-28',
'is_on_work'=>'1'
];




//////////////////////////////////////////////////////////////////////
//获取旧表数据
$model->getOldDate($where,$pass,$table2,$no,$field_arr);

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