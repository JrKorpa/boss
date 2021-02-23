<?php
/*
@author: liulinyan
@date: 2015-10-28
@filename: order_auto_unlink_goods.php
@used:双十一过后,为付款的预售订单自动关闭,并且货品自动解绑
*/

header('Content-type: text/html; charset=utf-8');
date_default_timezone_set('Asia/Shanghai');
define('ROOT_PATH',str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));//定义目录

//引入数据库文件
include_once(ROOT_PATH.'/lib/PdoModel.php');
include_once(ROOT_PATH.'/lib/AutoUnlinkGoodsClassModel.php');
$model = new AutoUnlinkGoodsClassModel();

//是否只解绑预售单
$isyushou = 0;
$data = $model->getlist($isyushou);
if(empty($data))
{
	exit('没有需要解绑货品的订单');
}

foreach($data as $orderinfo)
{
	$actiontxt = '因为买家过了双十一还未付款,所以';
	$orderid = $orderinfo['id'];
	$ordersn = $orderinfo['order_sn'];
	$goodsid = $orderinfo['goods_id'];
	$detailid = $orderinfo['detailid'];
	//仓库商品自动解绑,因为这个双十一绑定货品的时候 只是做了绑定,并没有走下面的流程,所以不会涉及到其他的表里面的信息
	if(!empty($goodsid))
	{
		$res = $model->autounbind($goodsid,$detailid);
		if($res)
		{
			$actiontxt .= '货品'.$goodsid.'自动解绑<br/>';
		}
	}
	//订单自动关闭
	$result = $model->autocloseorder($orderid);
	if($result)
	{
		$actiontxt .='订单'.$ordersn.'自动关闭';
	}
	//记录下操作日志
	$model->addactionlog($orderid,$actiontxt);
	echo $actiontxt.'<br/>';
}


?>