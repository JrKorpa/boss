<?php 
/**
 *  -------------------------------------------------
 * 文件说明		款式商品2 数据导入
 * @file		: list_style_goods1.php
 * @date 		: 2015-3-10 16:13:31
 * @author		: yangxt <yangxiaotong@163.com
 *  -------------------------------------------------
*/
header("Content-type:text/html;charset=utf-8");
require_once('ExportData2.class.php');


/*  数据更换  旧表:style_goods_new   新表:list_style_goods
====旧字段==================注释===============================新字段=====================================
	########			=>字段说明							===> goods_id						主键		
	########			=>字段说明							===> product_type_id				产品线id		
	########			=>字段说明							===> cat_type_id					款式分类id		
	########			=>字段说明							===> style_id						款式id		
	########			=>字段说明							===> style_sn						款式编号		
	########			=>字段说明							===> style_name						款式名称		
	########			=>字段说明							===> goods_sn						产品编号		
	########			=>字段说明							===> shoucun						手寸		
	########			=>字段说明							===> xiangkou						镶口		
	########			=>字段说明							===> caizhi							材质		
	########			=>字段说明							===> yanse							颜色		
	########			=>字段说明							===> zhushizhong					主石重		
	########			=>字段说明							===> zhushi_num						主石数		
	########			=>字段说明							===> fushizhong1					副石1重		
	########			=>字段说明							===> fushi_num1						副石1数		
	########			=>字段说明							===> fushizhong2					副石2重		
	########			=>字段说明							===> fushi_num2						副石2数		
	########			=>字段说明							===> fushi_chengbenjia_other		其他副石副石成本价		
	########			=>字段说明							===> weight							材质金重		
	########			=>字段说明							===> jincha_shang					金重上公差		
	########			=>字段说明							===> jincha_xia						金重下公差		
	########			=>字段说明							===> dingzhichengben				定制成本		
	########			=>字段说明							===> is_ok							是否上架;0为下架;1为上架		
	########			=>字段说明							===> last_update					最后更新时间		
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

$n_table = 'list_style_goods';	
$o_table = 'style_goods_new_v3';	

$pass = true;		//记录导出状态
$old_id = false;	//保留旧表主键

//旧表查询条件
$where = "`p`.`is_new` = '1'";

// 旧表联查
$table2 = "style_style";
$no = ['style_id','style_id'];
$field_arr = [
'pro_line'=>'product_type_id'
];


$model = new ExportData($new_conf,$old_conf,$n_table,$o_table,$pass,$old_id);

//设置导数量
$model->len = '5000';

//设置字段
$model->filter = [
'style_sn'=>'style_sn',
'style_cat'=>'cat_type_id',
'style_name'=>'style_name',
'goods_sn'=>'goods_sn',
'shoucun'=>'shoucun',
'xiangkou'=>'xiangkou',
'caizhi'=>'caizhi',
'yanse'=>'yanse',
'zhushizhong'=>'zhushizhong',
'zhushi_num'=>'zhushi_num',
'fushizhong1'=>'fushizhong1',
'fushi_num1'=>'fushi_num1',
'fushizhong2'=>'fushizhong2',
'fushi_num2'=>'fushi_num2',
'fushi_chengbenjia_other'=>'fushi_chengbenjia_other',
'weight'=>'weight',
'jincha_shang'=>'jincha_shang',
'jincha_xia'=>'jincha_xia',
'is_ok'=>'is_ok',
'last_update'=>'last_update',
'dingzhichengben'=>'dingzhichengben'
];


//替换数据字典 'filed'=>['0'=>'1'],
$model->dict = [
'product_type_id'=>['0'=>'5','1'=>'14','2'=>'4','3'=>'13','4'=>'6','6'=>'15','7'=>'17','8'=>'6','9'=>'16','10'=>'10','11'=>'12']
];

//设置默认值
$model->default = [
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
// $model->updateNewItem('style_id','style_sn','base_style_info','1000');





?>