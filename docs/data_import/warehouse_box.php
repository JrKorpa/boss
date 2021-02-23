<?php 
/**
 *  -------------------------------------------------
 * 文件说明		商品柜位数据导入
 * @file		: warehouse_box.php
 * @date 		: 2015-03-07 17:29:11
 * @author		: yangxt <yangxiaotong@163.com
 *  -------------------------------------------------
*/
header("Content-type:text/html;charset=utf-8");

/**
 *	-------------------------------------------------
 *	本脚本执行一次即可。
 *  运行前先清空新库 柜位表 
 *	TRUNCATE warehouse_box
 *	-------------------------------------------------
 */

require_once('ExportGoodsData.class.php');
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

$n_table = 'warehouse_box';	
$o_table = 'jxc_goods';

$where = "`is_on_sale` = '1' AND `tmp_sn` <> '0-00-0-0'";
$filter = array(
'warehouse'=>'warehouse_id',
'tmp_sn'=>'box_sn',
);
$dict = [];
$default = [
'create_time'=>date('Y-m-d H:i:s'),
'create_name'=>'system',
'is_deleted'=>'1',
'info'=>'旧数据导入'
];

$pass = true;	//导出状态
$old_id = false; //记录旧主键
//////////////////////////////////////////////////////////////////////
$model = new ExportData($new_conf,$old_conf,$n_table,$o_table,$filter,0,0,$pass,$old_id); 
//写入默认柜
$model->insertWarehouseBox();

//旧表主键
$old_data = $model->getOldDate($where,$pass,1);

if(empty($old_data)){
	echo "not data to update";
	exit;
}else{
	$model->insertData($dict,1,$default);
}




?>