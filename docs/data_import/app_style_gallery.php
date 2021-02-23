<?php 
/**
 *  -------------------------------------------------
 * 文件说明		款对图 数据导入
 * @file		: rel_style_factory.php
 * @date 		: 2015-3-10 16:13:31
 * @author		: yangxt <yangxiaotong@163.com
 *  -------------------------------------------------
*/
// header("Content-type:text/html;charset=utf-8");
require_once('ExportData2.class.php');


/*  数据更换  旧表:style_gallery   新表:app_style_gallery
====旧字段==================注释===============================新字段=====================================
	########			=>字段说明						===> g_id		主键		
	######## 			=>字段说明						===> style_id		款式ID
	######## 			=>字段说明						===> style_sn		款号		
	image_place 		=>字段说明						===> image_place	图片位置，100=网络上架，6=表现工艺，5=证书图,1=正立45°图,2=正立图,3=爪头图,4=爪尾图,8=内臂图,7=质检专用图	
	img_sort	 		=>字段说明						===> img_sort		图片排序		
	img_ori	 			=>字段说明						===> img_ori 		原图路径		
	thumb_img 			=>字段说明						===> thumb_img		缩略图						
	middle_img 			=>字段说明						===> middle_img		中图				
	big_img 		 	=>字段说明						===> big_img		大图
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

$n_table = 'app_style_gallery';	
$o_table = 'style_gallery';	

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
'image_place'=>'image_place',
'img_sort'=>'img_sort',
'img_ori'=>'img_ori',
'thumb_img'=>'thumb_img',
'middle_img'=>'middle_img',
'big_img'=>'big_img'
];


//替换数据字典 'filed'=>['0'=>'1'],
$model->dict = [];

//设置默认值
$model->default = [
];


//////////////////////////////////////////////////////////////////////

//获取旧表数据
// $model->getOldDate($where,$pass,$table2,$no,$field_arr);

// $data = $model->old_data;

// if(empty($data)){
// 	echo "not data to update";
// 	exit;
// }else{
// 	// print_r(count($old_data));exit;
// 	// print_r($data[0]);exit;
// 	$model->insertData();
// }

////////////////////////////////////////////////////////////////
//导完旧数据之后，更新 style_id 字段
$model->updateNewItem('style_id','style_sn','base_style_info','1000');





?>