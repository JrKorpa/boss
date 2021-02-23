<?php
define('ROOT_PATH',str_replace('\\','/',realpath(dirname(__FILE__))));//定义目录
define('TB_API_URL', 'http://114.55.12.230');
define('API_AUTH_KEYS', json_encode(array(
    'taobaoapi'=> ':AoN8Rt9l5103s'
)));
//引入淘宝api文件
include_once(ROOT_PATH.'/taobaoapi.php');

include_once(API_ROOT.'PdoModel.php');
include_once(API_ROOT.'PayClassModel.php');

$payModel = new PayClassModel();
//获取参数
if (PHP_SAPI == 'cli') {
    $_REQUEST['taobaoid'] = $_SERVER['argv'][1];
}

//如果是单个的
$requestid = isset($_REQUEST['taobaoid'])?$_REQUEST['taobaoid']:'';

$yushou=0;
$ids = $payModel->getalloutids($yushou,$requestid);
if(empty($ids))
{
	exit('没有需要点款的淘宝订单');
}
$error=array(
	1=>'外部订单出错',
	2=>'淘宝订单状态未处于等待发货状态不能付款',
	3=>'支付金额不等于实际支付金额',
	4=>'流水号已经支付过',
	5=>'获取BDD订单的信息失败',
	6=>'保存到财务失败',
	7=>'支付订单更改失败',
	8=>'BDD订单没有关联外部订单',
	9=>'外部订单信息不符合不能支付'
);
foreach($ids as $taobaoinfo)
{
	$taobaoid = $taobaoinfo['outid'];
	$taobaoid = trim($taobaoid);
	$orderid = $taobaoinfo['order_id'];
	$order_sn = $taobaoinfo['order_sn'];
	//获取淘宝订单信息
	$orderinfo = $apiModel->get_order_info($taobaoid);
    //print_r($orderinfo);
	if(!empty($orderinfo->code))
	{
		echo '淘宝订单'.$taobaoid.'相关信息获取异常<br/>'.PHP_EOL;
		continue;
	}
	
	
	//如果淘宝状态不是等待发货状态,则淘宝订单状态错误
	if(trim($orderinfo->trade->status) != "WAIT_SELLER_SEND_GOODS")
	{
		echo '淘宝订单不是等待卖家发货状态,订单'.$taobaoid.'点款失败<br/>'.PHP_EOL;
		continue;
	}
	//获取淘宝订单中真正需要付款的值
	//$real_payment = (float)trim($orderinfo->trade->payment);
	//不能用payment 因为买家可能会用到积分抵扣
	if($orderinfo->trade->point_fee > 0)
	{
		$jfmoney = bcdiv($orderinfo->trade->point_fee,100,2);
	}else{
		$jfmoney =0;
	}
	$real_payment = bcadd($orderinfo->trade->payment,$jfmoney,2);
	
	//获取BDD订单的信息
	$kl_orderinfo = $payModel->getklorderinfo($order_sn);
	if(empty($kl_orderinfo))
	{
		echo '没有获取到BDD的订单为'.$order_sn.'的相关信息'.PHP_EOL;
		continue;
	}
	
	if($real_payment == $kl_orderinfo['money_paid'])
	{
		echo '淘宝订单,'.$taobaoid.'已经付清了款项,不需要再次点款了<br/>'.PHP_EOL;
		continue;
	}
	
	if($real_payment != $kl_orderinfo['money_unpaid'])
	{
		echo 'BDD后台未付款金额不等于淘宝实际付款金额,'.$taobaoid.'点款失败<br/>'.PHP_EOL;
		continue;
	}
	if($real_payment > $kl_orderinfo['money_unpaid']){
		echo '支付金额超过BDD订单未付价格请核对,订单'.$taobaoid.'点款失败<br/>'.PHP_EOL;
		continue;
	}
	if($kl_orderinfo['order_status']!=2){
		echo 'BDD订单 未处于审核状态不允许支付'.$taobaoid.'点款失败<br/>'.PHP_EOL;
		continue;
	}
		
	//从这里开始支付了
	//生成一个支付凭证$order_info['bonus_code']
	$bonus_code = $payModel->createcode();
	$res= $payModel->getPaySnExt($bonus_code);
	if(!empty($res))
	{
		echo 'BDD订单出现了重复支付的情况哟,淘宝订单'.$taobaoid.'点款失败<br/>'.PHP_EOL;
		continue;
	}
	
	//组装数据
	$pay_action = array(
		'Payaction'=>array(
			"order_id" => $kl_orderinfo["order_id"],
			"order_sn" => $kl_orderinfo['order_sn'],
			"order_time" =>$kl_orderinfo["create_time"],
			"deposit" => $real_payment,
			"order_amount" => $kl_orderinfo["order_amount"],
			"balance" => $kl_orderinfo["order_amount"]-$real_payment,
			"pay_time" => date("Y-m-d H:i:s"),
			"pay_type" =>24,
			"order_consignee" => $kl_orderinfo["consignee"],
			"attach_sn" => trim($orderinfo->trade->alipay_no),
			"leader" => 'admin',
			"leader_check" => date("Y-m-d H:i:s"),
			"opter_name" => 'admin',
			"status" => "1",
			//淘宝渠道
			"department" => "2",
			"system_flg" => "2",
			"out_order_sn"=>$taobaoid,
		),
        'AppReceiptPay' =>array(
            'order_sn' => $kl_orderinfo['order_sn'],
            'receipt_sn' =>$bonus_code,
            'customer' => $kl_orderinfo['consignee'],
            'department' => $kl_orderinfo['department_id'],
            'pay_fee'=> $real_payment,
            'pay_type' =>24,
            'pay_time' =>date("Y-m-d H:i:s"),
            'card_no' =>'',
            'card_voucher' =>'',
            'status' => 1,
            'print_num' => 0,
            'pay_user' => 'admin',
            'remark' => "淘宝代收款流水号为".$orderinfo->trade->alipay_no,
            'add_time' => date("Y-m-d H:i:s"),
            'add_user' => 'admin',
        ),
        'AppReceiptPayLog'=>array(
            'receipt_action' => '添加点款收据成功',
            'add_time' => date("Y-m-d H:i:s"),
            'add_user' => 'admin',
            ),
		);
		
		//保存到财务
		$flag = true;
		
		//1
		$payaction = $pay_action['Payaction'];
		$res1 = $payModel->autoinsert('finance.app_order_pay_action',$payaction);
		if($res1)
		{
			echo '录入app_order_pay_action表成功<br/>'.PHP_EOL;
		}else{
			$flag = false;
			echo '录入app_order_pay_action失败<br/>'.PHP_EOL;
		}
		//2
		$receiptpay = $pay_action['AppReceiptPay'];
		$res2 = $payModel->autoinsert('finance.app_receipt_pay',$receiptpay);
		if($res2)
		{
			echo '录入app_receipt_pay表成功<br/>'.PHP_EOL;
		}else{
			$flag = false;
			echo '录入app_receipt_pay失败<br/>'.PHP_EOL;	
		}
		//3
		$receiptpaylog = $pay_action['AppReceiptPayLog'];
		$receiptpaylog['receipt_id']=$res2;
		$res3 = $payModel->autoinsert('finance.app_receipt_pay_log',$receiptpaylog);
		if($res3)
		{
			echo '录入app_receipt_pay_log表成功<br/>'.PHP_EOL;
		}else{
			$flag = false;
			echo '录入app_receipt_pay_log失败<br/>'.PHP_EOL;	
		}
		
		if($flag)
		{
			// 支付订单更改其已付金额和未付金额已付全款更改其订单状态为已付款
			$payModel->updateOutOrder($real_payment,$kl_orderinfo['order_id']);
			$pay_stu = $payModel->changgestu($kl_orderinfo['order_id']);
			//操作日志
			$ation['order_status'] = $kl_orderinfo['order_status'];
			$ation['order_id'] = $kl_orderinfo['order_id'];
			$ation['shipping_status'] = $kl_orderinfo['send_good_status'];
			$ation['pay_status'] = $pay_stu;
			$ation['create_user'] = 'admin';
			$ation['create_time'] = date("Y-m-d H:i:s");
			$ation['remark'] = "外部订单[$taobaoid]，通过外部订单支付了$real_payment 元";
			//4
			$last = $payModel->autoinsert('app_order.app_order_action',$ation);
			if($last)
			{
				echo '订单'.$taobaoid.'点款成功<hr/>'.PHP_EOL;
			}else{
				echo '订单'.$taobaoid.'点款失败<hr/>'.PHP_EOL;
			}
		}
}
?>