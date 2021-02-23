<?php 
/**
 *  -------------------------------------------------
 * 文件说明		商品数据导入
 * @file		: warehouse_goods.php
 * @date 		: 2015-03-16 11:29:11
 * @author		: yangxt <yangxiaotong@163.com
 *  -------------------------------------------------
*/
header("Content-type:text/html;charset=utf-8");
require_once('w_ExportDataOld2New.class.php');
date_default_timezone_set('Asia/Shanghai');
set_time_limit(0);
ini_set('memory_limit','2000M');

//新项目数据库
$new_conf = [
	'dsn'=>"mysql:host=192.168.1.93;dbname=warehouse_shipping",
	'user'=>"cuteman",
	'password'=>"QW@W#RSS33#E#",
];

// 旧项目数据库
$old_conf = [
	'dsn'=>"mysql:host=192.168.1.79;dbname=jxc",
	'user'=>"root",
	'password'=>"n+g1kMY#2]fZ",
];

$n_table = 'warehouse_goods';	
$o_table = 'jxc_goods';

$pass = false;	//记录导出状态
$old_id = true;	//保留旧表主键

/*---------------*/
$model = new ExportData($new_conf,$old_conf,$n_table,$o_table,$pass,$old_id);

//####
	//去除不需要的字段
	$model->cannelField = [
	'warehouse','company','id'
	];

	//获取新表不同字段
	$res = $model->getDiffFields();
	// print_r($res);exit;

	//替换字段名称
	$model->filter = array(
	'storage_mode'=>'put_in_type',
	'shipin_type'=>'product_type',
	'kuanshi_type'=>'cat_type',
	'zhuchengse'=>'caizhi',
	'zhuchengsezhong'=>'jinzhong',
	'company'=>'company_id',
	'warehouse'=>'warehouse_id',
	'zhushiyanse'=>'yanse',
	'zhushijingdu'=>'jingdu',
	'zhushizhong'=>'zuanshidaxiao',
	'xianzaichengben'=>'mingyichengben',
	'tmp_sn'=>'box_sn'
	);

	$model->dict = [
		'put_in_type'=>['0'=>'1','1'=>'2','2'=>'3','3'=>'4'],
		'is_on_sale'=>['1'=>'2']
	];
	$model->default = [];

	$model->def_decimal = [
	'jinzhong','jietuoxiangkou','zuanshizhekou','zuanshidaxiao','zhuchengsezhongjijia',
	'zhuchengsemairudanjia','zhuchengsemairuchengben','zhuchengsejijiadanjia','zhushimairudanjia',
	'zhushimairuchengben','zhushijijiadanjia','fushizhong','fushimairuchengben','fushimairudanjia',
	'fushijijiadanjia','shi2zhong','shi2mairudanjia','shi2mairuchengben','shi2jijiadanjia','mairugongfeidanjia',
	'mairugongfei','jijiagongfei','danjianchengben','peijianchengben','qitachengben','zhushipipeichengben',
	'yuanshichengbenjia','chengbenjia','mingyichengben','caigou_chengbenjia','jiajialv','biaoqianjia','xianzaixiaoshou',
	'zuixinlingshoujia'
	];

////////////////////////////////////////////////////////////
//旧项目写入新项目
	// $base_path = addcslashes('D:\\www\\data_import\\goods_log\\','\\');
    $base_path = addcslashes('/data/www/cuteframe_boss/docs/data_import/goods_log/','\\');
	$res = $model->create2Tmp($base_path);
	if(!$res){
		echo "CREATE TMP TABLE FAIL";exit;
	}else{
		echo "CREATE TMP TABLE SUCCESS";exit;
	}
exit;
	// 获取旧表数据
	$model->getOldData();
	$data = $model->old_data;
	if(empty($data)){
		echo "not data to update";
		exit;
	}else{
		// print_r($data[0]);exit;
		$model->insertData();
	}


////////////////////////////////////////////////////////////////////
//新项目导入旧项目
	$res = $model->createGoodsTmp();
	// var_dump($res);exit;
	if(!$res){
		echo "CREATE tmo_goods TABLE FAIL";exit;
	}
	$data = $model->getNewData();
	// print_r($data);exit;
	if(empty($data)){
		echo "not data to update";
	}else{
		$model->insertData2Old($data);
	}








?>