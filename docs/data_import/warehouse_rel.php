<?php 
/**
 *  -------------------------------------------------
 * 文件说明		公司仓库关系 数据导入
 * @file		: warehouse_rel.php
 * @date 		: 2015-3-10 16:13:31
 * @author		: yangxt <yangxiaotong@163.com
 *  -------------------------------------------------
*/
header("Content-type:text/html;charset=utf-8");
require_once('ExportData2.class.php');

$new_conf = [
	'dsn'=>"mysql:host=192.168.1.93;dbname=warehouse_shipping",
	'user'=>"cuteman",
	'password'=>"QW@W#RSS33#E#",
];

$old_conf = [
	'dsn'=>"mysql:host=192.168.1.79;dbname=jxc",
	'user'=>"root",
	'password'=>"n+g1kMY#2]fZ",
];

$n_table = 'warehouse_rel';	
$o_table = 'jxc_warehouse';	

$pass = false;		//记录导出状态
$old_id = false;	//保留旧表主键

//表查询条件
$where = false;

$model = new ExportData($new_conf,$old_conf,$n_table,$o_table,$pass,$old_id);

//设置导数量
$model->len = '20000';

//设置字段
$model->filter = [
'wh_id'=>'warehouse_id',
'p_id'=>'company_id'
];


//替换数据字典 'filed'=>['0'=>'1'],
$model->dict = [
];

//设置默认值
$model->default = [
'create_time'=>date('Y-m-d H:i:s')
];


// 旧表联查
$table2 = "jxc_processors";
$no = ['p_id','p_id'];
$field_arr = [
'p_name'=>'company_name'
];

//////////////////////////////////////////////////////////////////////
//UPDATE `jxc_warehouse` SET `pass_status` = '0'
//获取旧表数据
$model->getOldDate($where,$pass,$table2,$no,$field_arr);

$data = $model->old_data;

if(empty($data)){
	echo "not data to update";
	exit;
}else{
//	 print_r(count($old_data));
//	 echo "\r\n";
//	 print_r($data[0]);exit;
	$model->insertData();
}



?>