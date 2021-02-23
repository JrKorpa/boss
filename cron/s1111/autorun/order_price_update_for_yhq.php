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
define('API_ROOT',ROOT_PATH.'/lib/');
//目前只处理淘宝的   为了扩展预留为数组
$from_arr = array(
	2 => array("ad_name"=> "淘宝B店", "api_path" =>"taobaoOrderApi")
);
$from_type = 2;  //默认来自淘宝B店
$apiname = $from_arr[$from_type]["api_path"];
$file_path = API_ROOT.$apiname.'/index.php';
//引入接口文件
require_once($file_path);
//实例化淘宝类
$apiModel = new $apiname();

//引入订单操作类文件
include_once(API_ROOT.'PdoModel.php');
include_once(API_ROOT.'OrderClassModel.php');
//引入数据库文件
$orderModel = new OrderClassModel();
//获取所有有优惠券的订单
$alldata = $orderModel->getallyhqorder();
if(empty($alldata))
{
	exit('没有这样的订单了');
}

foreach($alldata as $k=>$info)
{
	$orderid = $info['orderid'];
	$taobaoid = $info['out_order_sn'];
	
	echo '淘宝订单'.$taobaoid.'<br/>';
	
	$orderinfo = $apiModel->getorderinfo($taobaoid);
	if(trim($orderinfo -> code))
	{
		echo '获取淘宝订单'.$taobaoid.'异常';
		continue;
	}
	//淘宝订单商品信息
	$goodsarr = $orderinfo->trade->orders->order;
	foreach($goodsarr as $goodsinfo)
	{
		//商品优惠金额
		$tmpgoodssn = $goodsinfo->outer_sku_id =="" ? $goodsinfo->outer_iid:$goodsinfo->outer_sku_id;
		$goodsallyh = 0;
		//特殊处理php对小数点不支持的情况
		$goodsallyh = bcadd($goodsallyh, $goodsinfo->discount_fee,2);
		//加上使用了优惠券的金额
		$goodsallyh = bcadd($goodsallyh, $goodsinfo->part_mjz_discount,2);
		$num = $goodsinfo->num;
		$goodsyh = bcdiv($goodsallyh,$num,2);
		$details['goods_price'] = $goodsinfo->price;
		$details['order_id']=$orderid;
		$details['goods_sn'] = $tmpgoodssn;
		$details['favorable_price'] = $goodsyh;
		
		//修改商品的信息
		echo '修改订单详细里面的价格:<br/>';
		$sql ="update app_order_details set goods_price ='".$details['goods_price']."',";
		$sql .=" favorable_price=$goodsyh where order_id='".$details['order_id']."' and goods_sn='".$tmpgoodssn."'";
		
		echo '内部订单'.$info['order_sn'].'订单详情商品款号为'.$tmpgoodssn.'的记录信息:'.'<br/>';
		echo '商品的价格由'.$info['goods_price'].'变成了'.$details['goods_price'].'<br/>';
		echo '商品的优惠金额有'.$info['favorable_price'].'变成了'.$details['favorable_price'].'<br/>';
		echo $sql.'<br/>';
	}
	//邮费
	$jifen = $orderinfo->point_fee;
	if($jifen>0)
	{
		$jifenmoney = bcdiv($jifen,100,2);
	}else{
		$jifenmoney=0;	
	}
	$ordermoney = bcadd($orderinfo->trade->payment,$jifenmoney,2);
	//修改app_order_account表 订单优惠为0
	//修改app_order_account表 商品优惠为
	$account['order_amount'] = $ordermoney;
	$account['order_id'] = $orderid;
	$account['coupon_price'] = 0;
	$account['favorable_price'] = $goodsallyh;
	$account['shipping_fee'] = $orderinfo->trade->post_fee;
	$sql = " update app_order_account set coupon_price=0,order_amount='".$ordermoney."',";
	$sql .=" coupon_price=0,favorable_price='".$goodsallyh."',";
	$sql .=" shipping_fee ='".$account['shipping_fee']."' where order_id='".$orderid."'";
	echo $sql.'<br/>';
	
	echo '内部订单'.$info['order_sn'].'的金额信息:<br/>';
	echo '订单总金额由'.$info['order_amount'].'变成了'.$account['order_amount'].'<br/>';
	echo '商品的总优惠金额由'.$info['favorable_price'].'变成了'.$account['favorable_price'].'<br/>';
	echo '邮费油'.$info['shipping_fee'].'变成了'.$account['shipping_fee'].'<br/>';
	echo '<hr/>';
}
?>