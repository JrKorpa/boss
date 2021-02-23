<?php

class paipai_class{
	
	

/**
	 * 用外部单号支付订单
	 * by col
	 */
 function outer_order_pay($order_sn, $taobao_order_sn, $price=false)
	{
		require_once(ROOT_PATH . "includes/cls_yeji.php");  //add buy lulu
		// 用户使用的提交数据的方法。post 和 get均可；以及字符集
	    $this->setMethod("get");//post
	    $this->setCharset("utf-8");//gbk
	    // 以下部分用于设置用户在调用相关接口时url中"?"之后的各个参数，如上述描述中的a=1&b=2&c=3
	    $params = &PaiPaiOpenApiOauth::getParams();//注意，这里使用的是引用，故可以直接使用
	    $params["sellerUin"] = $this->uin;
	    $params["zhongwen"] = "cn";
	    $params["pageSize"] = "10";
	    $params["tms_op"] = "admin@855000017";
	    $params["tms_opuin"] = $this->uin;
	    $params["tms_skey"] = "@WXOgdqq16";
		$params["dealCode"] = $taobao_order_sn;
		$params['listItem'] =1;//显示订单商品
		//var_dump($params);exit;
		$this->setApiPath("/deal/getDealDetail.xhtml");
		 $xml = $this->invoke();
	    $xml = simplexml_load_string($xml);
	   // print_r($xml);
	  	//exit;
		
		
		
		// 错误代码
		if(trim($xml -> errorCode) !='0') {return false;}
		// 拍拍订单状态错误
		if(trim($xml->dealState) != "DS_WAIT_SELLER_DELIVERY") return false;
		$real_payment = (trim($xml -> dealPayFeeTotal) + trim($xml -> freight))/100;

		// 支付金额不等于实际支付金额
		if($price !== false && abs($real_payment - $price) > 1) return false;
		//echo "abc";exit;
		// 支付记录排重
		$cnt = $GLOBALS["db"] -> getOne("SELECT COUNT(*) FROM ecs_order_pay_action  WHERE attach_sn='".trim($xml->tenpayCode)."'");
		if($cnt) return false;

		// 取订单信息
		$order_info = $GLOBALS["db"] -> getRow("SELECT order_id, order_sn, order_time, order_amount, money_paid,consignee FROM ecs_order_info WHERE order_sn = '".$order_sn."'");
		// 增加支付记录
		$pay_action = array(
			"order_id" => $order_info["order_id"],
			"order_sn" => $order_sn,
			"order_time" => date("Y-m-d H:i:s"),
			"deposit" => $real_payment,
			"order_amount" => $order_info["order_amount"],
			"balance" => $order_info["order_amount"]-$real_payment,
			"pay_time" => date("Y-m-d H:i:s"),
			"pay_type" => "拍拍网店代收款",
			"order_consignee" => $order_info["consignee"],
			"attach_sn" => trim($xml->tenpayCode),
			"leader" => $_SESSION["admin_name"],
			"leader_check" => date("Y-m-d H:i:s"),
			"opter_name" => $_SESSION["admin_name"],
			"status" => "1",
			"department" => "2",
			"system_flg" => "2"
		);
		$GLOBALS["db"]->autoExecute('ecs_order_pay_action', $pay_action, 'INSERT');
		// 支付订单
		$sql = "UPDATE ecs_order_info SET money_paid=money_paid+$real_payment, order_amount = order_amount - $real_payment, pay_time='".time()."' WHERE order_id = '".$order_info["order_id"]."'";
		$GLOBALS["db"] -> query($sql);
		//add buy lulu  添加付款业绩
		$yeji_log = new Yeji($GLOBALS['db']);
		$yeji_log->doAdd($order_info["order_id"],1);
		/* 记录log */
		order_action($order_info['order_sn'], OS_CONFIRMED, $order['shipping_status'], $order['pay_status'], "快速付款－－淘宝订单号:".$taobao_order_sn.",支付宝交易号:".trim($taobao_order_info->trade->alipay_no));

		// 检查支付金额,如果完成付款,修改订单付款状态
		$sql = "update `ecs_order_info` set pay_status=if((`order_amount`=0), 2, 4) WHERE order_sn = '".$order_info["order_sn"]."'";
		$GLOBALS["db"] -> query($sql);
		
		return true;
	}
}
?>