<?php
/*
auto: liulinyan
date: 2015-10-23
filename: qx.php
used: 银饰吊坠清洗自动绑定
货品*/
header('Content-type: text/html; charset=utf-8');
define('ROOT_PATH',str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));//定义目录
define('API_ROOT',ROOT_PATH.'/lib/');
include_once(API_ROOT.'PdoModel.php');
include_once(API_ROOT.'QxClassModel.php');
$Qxclass = new QxClassModel();
$data = $Qxclass->getlist();
if(empty($data))
{
	exit('没有数据咯');	
}
//找出所有吊坠的货品

//目前这样的订单都只有一件货 所以匹配好了就可以改订单的类型了
foreach($data as $obj)
{
	$orderid = $obj['id'];
	//
	$detailid = $obj['detailid'];
	$goodssn = $obj['goods_sn'];
	//根据款号把货品id拿出来
	
	//测试
	//$goodssn = 'KLRM027488';
	$goodsinfo = $Qxclass->getgoodsinfo($goodssn);
	if(empty($goodsinfo))
	{
		continue;
	}
	$goods_id = $goodsinfo['goods_id'];
	$caizhi = $goodsinfo['caizhi'];
	//修改app_order_details中的订单详情中的商品
	$Qxclass->updateorderdetail($detailid,$goods_id,$caizhi);
	echo '修改了app_order_details中的'.$detailid.'绑定货品'.$goods_id.'修改材质为'.$caizhi.'<br/>';
	//修改订单为现货单
	$Qxclass->updateXianhuo(1,$orderid);
	echo '修改订单id'.$orderid.'为现货单'.'<br/>';
	//修改仓库里面的货品绑定订单
	$Qxclass->updategoodsorderid($goods_id,$detailid);
	echo '修改仓库里面的货品绑定订单详细id'.$detailid.'<br/>';
}
?>