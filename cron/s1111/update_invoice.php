<?php
header('Content-type: text/html; charset=utf-8');
ini_set('memory_limit', '1024M');
set_time_limit(0);
define('ROOT_PATH',str_replace('\\','/',realpath(dirname(__FILE__))));//定义目录
define('TB_API_URL', 'http://114.55.12.230');
define('API_AUTH_KEYS', json_encode(array(
    'taobaoapi'=> ':AoN8Rt9l5103s'
)));
//定义淘宝平台承担的优惠券费用

//header("Content-type: text/html; charset=utf-8");
//引入淘宝api文件
include_once(ROOT_PATH.'/taobaoapi.php');

//引入订单操作类文件
include_once(API_ROOT.'PdoModel.php');
include_once(API_ROOT.'OrderClassModel.php');
//引入规则文件
include_once(ROOT_PATH.'/include/newrule.php');
//引入赠品配置文件
include(ROOT_PATH.'/include/giftconfig.php');
//引入赠品操作文件
include(ROOT_PATH.'/include/getgift.php');
//引入各种配置文件
include(ROOT_PATH.'/include/allconfig.php');


//设置各个开关
$open_trade_status = true;    //是否开启淘宝订单状态过滤
$open_check = true;           //是否开启淘宝订单抓取过滤
$open_ys_status = true;      //是否开启预售订单

//获取订单拿取货品所有的仓库
$orderModel = new OrderClassModel();
$warehouseid = $orderModel->getgoodswarehouse();
//获取订单拿取货品所有的快递数组
$shipping_list = $orderModel->getshippintlist();

if (PHP_SAPI == 'cli') {
    $_REQUEST['taobaoid'] = $_SERVER['argv'][1];    
}
//如果是单个的
$requestid = isset($_REQUEST['taobaoid'])?$_REQUEST['taobaoid']:'';

$orderModel = new OrderClassModel();

$res = $orderModel->get_all_res1($requestid);
if(empty($res))
    exit('没有数据了');
echo "<pre>";
foreach ($res as $order) {
	$taobao_order = $apiModel->get_order_info($order['outid']);
	if(empty($taobao_order)){
        echo "没有获取到淘宝订单信息<br>".PHP_EOL;
		continue;
	}
	if(!empty($requestid))
	    print_r($taobao_order);
	$new_invoice_amount = 0;
	if(!empty($taobao_order->trade->promotion_details->promotion_detail)){		
        $promotion_detail = $taobao_order->trade->promotion_details->promotion_detail;
        foreach ($promotion_detail as $k_d => $promotion) {        	
       	   if(in_array($promotion->promotion_name,$taobao_promotion)){       	   
       	   	    $new_invoice_amount = $new_invoice_amount + $promotion->discount_fee;
       	   }
        }

	}


	if(!empty($taobao_order->trade->coupon_fee))
		$new_invoice_amount = bcadd($new_invoice_amount,bcdiv($taobao_order->trade->coupon_fee,100,2),2);
	if(!empty($taobao_order->trade->alipay_point))
		$new_invoice_amount = bcadd($new_invoice_amount,bcdiv($taobao_order->trade->alipay_point,100,2),2);
    $new_invoice_amount = bcsub($taobao_order->trade->payment,$new_invoice_amount,2);

    if(bccomp($order['invoice_amount'],$new_invoice_amount,2)!=0){
    	$orderModel->update_order_invoice_amount($order['order_id'],$new_invoice_amount);
    }		
}

?>