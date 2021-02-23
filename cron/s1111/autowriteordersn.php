<?php
header('Content-type: text/html; charset=utf-8');
date_default_timezone_set('Asia/Shanghai');
define('ROOT_PATH',str_replace('\\','/',realpath(dirname(__FILE__))));//定义目录
define('TB_API_URL', 'http://114.55.12.230');
define('API_AUTH_KEYS', json_encode(array(
    'taobaoapi'=> ':AoN8Rt9l5103s'
)));
include_once(ROOT_PATH.'/taobaoapi.php');
//检查淘宝订单是否存在

//引入订单操作类文件
include_once(API_ROOT.'PdoModel.php');
include_once(API_ROOT.'OrderClassModel.php');
$orderModel = new OrderClassModel();
$data = $orderModel->getreturntaobaoinfo();

if(empty($data))
{
	exit('全部订单已经回写完毕');
}

$alerdy = array();
foreach($data as $orderinfo)
{
	
	$taobaoid = $orderinfo['out_order_sn'];
	if(!in_array($taobaoid,$alerdy))
	{
		array_push($alerdy,$taobaoid);
	}else{
		continue;
	}
	$order_sn = $orderinfo['order_sn'];
	$info = $apiModel->get_order_info($taobaoid);
	if(!empty($info->code))
	{
		//exit ('没有找到订单id为:'.$orderid.'的信息');
		echo  '没有找到订单id为:'.$orderid.'的信息'.PHP_EOL;
		continue;
	}
	$taobao_memo = !empty($info->trade->seller_memo) ? trim($info->trade->seller_memo) :'';
	
	
	if(empty($taobao_memo))
	{
		$seller_memo = '亢思迪,无,无,KLBZ';
	}else{
		$seller_memo = $taobao_memo;
	}
	$newbz='';
	//检查是否有回写了备注了
	if(strpos($seller_memo,$order_sn)!=false)
	{
		echo '该订单已经有回写的备注了.<br/>'.PHP_EOL;
		continue;
	}else{
		
		$tt = explode(',',$seller_memo);
		$count = count($tt);
		if($count <= 2)
		{
			$newbz = $seller_memo.$order_sn;
		}else{
			$dr = $count-2;
			$tt[$dr] = $tt[$dr].$order_sn;
			$newbz = implode(',',$tt);
		}
	}
	$apiModel->update_taobao_memo($taobaoid,$newbz,1);
	$orderModel->updateisreturn($taobaoid,$order_sn,1);	
}
?>