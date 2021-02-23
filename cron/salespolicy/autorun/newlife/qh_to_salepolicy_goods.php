<?php
/*
	@author: liulinyan
	@date: 2016-12-27
	@filename:w_to_salepolicy_goods.php
	@used:期货自动录入到可销售商品表中
*/
header('Content-type: text/html; charset=utf-8');
date_default_timezone_set('Asia/Shanghai');
define('ROOT_PATH',str_replace('\\','/',realpath(dirname(__FILE__))));//定义目录
define('API_ROOT',ROOT_PATH.'/lib/');
include_once(API_ROOT.'PdoModel.php');
include(API_ROOT.'OrderClassModel.php');
$Model = new OrderClassModel();

//获取所有的期货数据

//SELECT  a.*,b.`check_status` FROM list_style_goods as a ,`base_style_info` as b WHERE a.`style_id`=b.`style_id` and a.is_ok=1 limit 10000";
$alldata = $Model->getvialuegoods();
if(empty($alldata))
{
	die('没有需要跑的数据咯');
}

$i=0;
$j=0;
$all=0;
$nostr = '';
//期货在可销售商品表里面存在的情况  查找数据源的第一步就过滤了,所以不用管
foreach($alldata as $obj)
{
	$goods_id = $obj['goods_sn'];
	$data = array();
	$data = array(
		'goods_id'=>$obj['goods_sn'],
		'goods_sn'=>$obj['style_sn'],
		'goods_name'=>$obj['style_name'],
		'chengbenjia'=>$obj['dingzhichengben'],
		'xiangkou'=>$obj['xiangkou'],
		'caizhi'=>$obj['caizhi'],
		'finger'=>$obj['shoucun'],
		'yanse'=>$obj['yanse'],
		'stone'=>$obj['xiangkou'],
		'category'=>$obj['cat_type_id'],
		'product_type'=>$obj['product_type_id'],
		'isXianhuo'=>0,
		'is_base_style'=>$obj['is_base_style'],
		'cate_g'=>0,
		'is_policy'=>1,
		'is_valid'=>1,
		'is_sale'=>1,
		'type'=>1	
 	);
	if($Model->insertgoods($data)){
		$j++;	
	}
	$all++;
}
$tjstr = '需要操作的总共有'.$all.'条\r\n';
$tjstr .= '在可销售商品里面不存在有,'.$j.'已经录入进去了'.$j.'条\r\n';
echo $tjstr;
?>