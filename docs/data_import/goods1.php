<?php 
/**
 *  -------------------------------------------------
 * 文件说明		商品数据导入
 * @file		: goods.php
 * @date 		: 2015-03-07 17:29:11
 * @author		: yangxt <yangxiaotong@163.com
 *  -------------------------------------------------
*/
header("Content-type:text/html;charset=utf-8");

require_once('ExportGoodsData1.class.php');

//新项目数据库
$new_conf = [
	'dsn'=>"mysql:host=203.130.44.199;dbname=warehouse_shipping",
	'user'=>"cuteman",
	'password'=>"QW@W#RSS33#E#",
];

// 旧项目数据库
$old_conf = [
	'dsn'=>"mysql:host=localhost;dbname=test",
	'user'=>"root",
	'password'=>"yangxt",
];

//UPDATE `jxc_goods` SET pass_status = '0' 
$o_table = 'jxc_goods';			//旧项目中的表
$n_table = 'warehouse_goods';	

$where = "`g`.`is_on_sale` = '1'";
$filter = array(
'id'=>'id',	
'yuanshichengbenjia'=>'yuanshichengbenjia',	
'goods_id'=>'goods_id',
'goods_sn'=>'goods_sn',
'goods_name'=>'goods_name',
'is_on_sale'=>'is_on_sale',
'num'=>'num',
'company'=>'company_id',
'warehouse'=>'warehouse_id',
'storage_mode'=>'put_in_type',
'shipin_type'=>'product_type',
'kuanshi_type'=>'cat_type',
'zhuchengse'=>'caizhi',
'zhuchengsezhong'=>'jinzhong',
'jinhao'=>'jinhao',
'zhushiyanse'=>'yanse',
'zhushijingdu'=>'jingdu',
'shoucun'=>'shoucun',
'zhengshuhao'=>'zhengshuhao',
'zhushizhong'=>'zuanshidaxiao',
'jietuoxiangkou'=>'jietuoxiangkou',
'chengbenjia'=>'chengbenjia',
'xianzaichengben'=>'mingyichengben',
'tmp_sn'=>'box_sn'
);
//
$dict = [
	'put_in_type'=>['0'=>'1','1'=>'2','2'=>'3','3'=>'4'],
	'is_on_sale'=>['1'=>'2']
];
$default = [];
$pass = true;
$old_id = true; 
/*---warehouse_goods---END---------*/


//////////////////////////////////////////////////////////////////////

$model = new ExportData($new_conf,$old_conf,$n_table,$o_table,$filter,0,5000,$pass,$old_id); 


//旧表主键
// print_r($model->old_pk);exit;
$old_data = $model->getOldDate($where,$pass);

if(empty($old_data)){
	echo "not data to update";
	exit;
}else{
	// print_r(count($old_data));exit;
	// print_r($old_data[0]);exit;
	$model->insertData($dict,1,$default);
}




?>