<?php
/*
自动绑定金条的订单货品
绑定成功吧货品变成现货单
*/
header('Content-type: text/html; charset=utf-8');
date_default_timezone_set('Asia/Shanghai');
define('ROOT_PATH',str_replace('\\','/',realpath(dirname(__FILE__))));//定义目录
include_once(ROOT_PATH.'/taobaoapi.php');
//检查淘宝订单是否存在

//引入订单操作类文件
include_once(API_ROOT.'PdoModel.php');
include_once(API_ROOT.'OrderNewClassModel.php');
$orderModel = new OrderNewClassModel();

//获取需要绑定货品的订单明细
$alldata = $orderModel->get_needbdgs_order();
if(empty($alldata)){
	echo '没有需要绑定货品的订单咯';
	die();	
}
foreach($alldata as $orderinfo)
{
	$orderid = $orderinfo['id'];
	$ordergoodsid = $orderinfo['detailid'];
	$gsn = $orderinfo['goods_sn'];
	$status = $orderinfo['order_status'];
	$ginfo = $orderModel->getbdgoods($gsn);
	//找到货品绑定
	if(!empty($ginfo)){
		$goodsid = $ginfo['goods_id'];
		$orderModel->upbdgoods($goodsid,$ordergoodsid);
		//修改订单的明细,修改订单为现货单
		$orderModel->uporderdetail($orderid,$ordergoodsid,$goodsid);
		//增加日志
		$ation['order_status'] = $status;
		$ation['order_id'] = $orderid;
		$ation['shipping_status'] = 1;
		$ation['pay_status'] = 1;
		$ation['create_user'] = 'admin';
		$ation['create_time'] = date("Y-m-d H:i:s");
		$ation['remark'] = "订单自动绑定货品".$goodsid.',订单类型自动变为现货单';
		$orderModel->autoinsert('app_order_action',$ation);
	}else{
		echo '没有知道库存状态没有绑定订单的款号为'.$gsn.'的货品了';
		continue;
	}
//	die();
}
?>
