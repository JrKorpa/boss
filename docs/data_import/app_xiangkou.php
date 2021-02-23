<?php 
/**
 *  -------------------------------------------------
 * 文件说明		款对镶口 数据导入
 * @file		: app_xiangkou.php
 * @date 		: 2015-3-10 16:13:31
 * @author		: yangxt <yangxiaotong@163.com
 *  -------------------------------------------------
*/
// header("Content-type:text/html;charset=utf-8");
require_once('ExportData2.class.php');


/*  数据更换  旧表:style_xiangkou   新表:app_xiangkou
====旧字段==================注释===============================新字段=====================================
	########			=>字段说明						===> x_id						主键		
	######## 			=>字段说明						===> style_id					款式ID
	######## 			=>字段说明						===> style_sn					款号		
	image_place 		=>字段说明						===> stone						镶口
	img_sort	 		=>字段说明						===> finger						手寸		
	img_ori	 			=>字段说明						===> main_stone_weight 			主石重		
	thumb_img 			=>字段说明						===> main_stone_num				主石数						
	middle_img 			=>字段说明						===> sec_stone_num				副石重数				
	big_img 		 	=>字段说明						===> sec_stone_weight_other		其他副石重
	big_img 		 	=>字段说明						===> sec_stone_num_other		其他副石数
	big_img 		 	=>字段说明						===> g18_weight					18K金重
	big_img 		 	=>字段说明						===> g18_weight_more			18K金重上公差
	big_img 		 	=>字段说明						===> g18_weight_more2			18K金重下公差
	big_img 		 	=>字段说明						===> gpt_weight					pt950金重
	big_img 		 	=>字段说明						===> gpt_weight_more			pt950金重上公差
	big_img 		 	=>字段说明						===> gpt_weight_more2			pt950金重下公差	
	big_img 		 	=>字段说明						===> sec_stone_price_other		其他副石成本价
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

$n_table = 'app_xiangkou';	
$o_table = 'style_xiangkou';	

$pass = true;		//记录导出状态
$old_id = false;	//保留旧表主键

//旧表查询条件
$where = false;

// 旧表联查
$table2 = false;
$no = [];
$field_arr = [
];


$model = new ExportData($new_conf,$old_conf,$n_table,$o_table,$pass,$old_id);

//设置导数量
$model->len = '10000';

//设置字段
$model->filter = [
'style_sn'=>'style_sn',
'stone'=>'stone',
'finger'=>'finger',
'main_stone_weight'=>'main_stone_weight',
'main_stone_num'=>'main_stone_num',
'sec_stone_weight'=>'sec_stone_weight',
'sec_stone_num'=>'sec_stone_num',
'sec_stone_weight_other'=>'sec_stone_weight_other',
'sec_stone_num_other'=>'sec_stone_num_other',
'g18_weight'=>'g18_weight',
'g18_weight_more'=>'g18_weight_more',
'g18_weight_more2'=>'g18_weight_more2',
'gpt_weight'=>'gpt_weight',
'gpt_weight_more'=>'gpt_weight_more',
'gpt_weight_more2'=>'gpt_weight_more2',
'sec_stone_price_other'=>'sec_stone_price_other'
];


//替换数据字典 'filed'=>['0'=>'1'],
$model->dict = [];

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