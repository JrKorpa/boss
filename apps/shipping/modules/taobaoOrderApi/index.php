<?php
/**
  *==================================
  * @FileName:
  * @CreateTime: 2012/2/29
  * @LastModifed:
    淘宝SDK应用
  * @Author:col
  *===================================
**/
class taobaoOrderApi
{
	/* 淘宝B店 */
	var $TAOBAO_APP_KEY = "12364339";
	var $TAOBAO_SECRETKEY = "046c481f480462907718bd1cce7f2403";
	var $from_ad = "000200230544";
	var $top_session = "";
	var $respone = array("is_error" => false, "message" => "");
	
	var $shipping_list = array(
			"4" => "SF",
			"9" => "EMS",
			"12" => "YTO",
			"14" => "STO",
			"19" => "ZTO"
		);
	/**
	 *
	 * by col
	 */
	function __construct()
	{
		$this->taobaoOrderApi();
	}
	/**
	 * 
	 * by col
	 */
	function taobaoOrderApi()
	{
		if(1 || empty( $this->top_session))
		{
		/*	$sql ="SELECT * FROM ecs_shop_config WHERE id = '8817'";
			$row = $GLOBALS["db"] -> getRow($sql);*/
			$this -> top_session = '6100d1365b14a9f369117178293b09ac4e23011879c6a0292449256';
		}
	}

	/**
	 * 
	 * by col
	 */
	function _construct()
    {
        $this->taobaoOrderApi();
    }

		//回写淘宝flag 和淘宝备注
	//
	//by lulu
	//
	//
	//$taobao_id  淘宝单号
	//$taobao_memo  淘宝备注
	//$taobao_flag  淘宝标记 1红旗 0灰旗。。。

	function update_taobao_memo($taobao_id,$taobao_memo,$taobao_flag)
	{
/*
	    require_once(ROOT_PATH."includes/modules/taobaoOrderApi/TopClient.php");
		require_once(ROOT_PATH."includes/modules/taobaoOrderApi/RequestCheckUtil.php");
		require_once(ROOT_PATH."includes/modules/taobaoOrderApi/request/TradeMemoUpdateRequest.php");
		
		$c = new TopClient;
		$c -> appkey = $this -> TAOBAO_APP_KEY;
		$c -> secretKey = $this -> TAOBAO_SECRETKEY;
		$req = new TradeMemoUpdateRequest;
		$req->setFlag($taobao_flag);
		$req->setMemo($taobao_memo);
		$req->setTid($taobao_id);
		$c->execute($req, $this->top_session);
*/
	    return $this->sendRequest('update_taobao_memo', array('taobao_id','taobao_memo', 'taobao_flag'), array($taobao_id, $taobao_memo, $taobao_flag));
	}



	/**
	 * 组建订单数据
	 * by col
	 */
	function make_order_info($taobao_order_id)
	{
		$res = $this -> get_api_order($taobao_order_id);
		if(trim($res -> code))
		{
			$this -> respone["is_error"] = true;
			$this -> respone["message"] = trim($res -> sub_msg);
			return $this -> respone;
		}


//		if($res->trade->status != "WAIT_SELLER_SEND_GOODS")
//		{
//			$this -> respone["is_error"] = true;
//			$this -> respone["message"] = "淘宝订单状态有误不能导入!!";
//			return $this -> respone;
//		}

        $tb_items = array();
        if (is_array($res->trade->orders->order)) {
            $tb_items = $res->trade->orders->order;
        } else {
            $tb_items[] =  $res->trade->orders->order;
        }
        		
		/* 取得收货地址列表 */
        //快递信息
       $order['shipping_name']=trim($tb_items[0]->logistics_company);
        if(in_array($order['shipping_name'],$this->shipping_list)){
            $order['shipping_id']=array_search($order['shipping_name'],$this->shipping_list);
        }

		$order['consignee']     = trim($res->trade->receiver_name);//收货人
		$order['country']       = 1;
		$order['province']      = trim($res->trade->receiver_state);//省
		$order['city']          = trim($res->trade->receiver_city);//市
		$order['district']      = trim($res->trade->receiver_district);//区
		$order['email']         = trim($res->trade->buyer_email);//邮箱
		$order['address']       = trim($res->trade->receiver_state)." ".trim($res->trade->receiver_city)." ".trim($res->trade->receiver_district)." ".trim($res->trade->receiver_address);//详细地址
		$order['zipcode']       = trim($res->trade->receiver_zip);
		$order['tel']           = trim($res->trade->receiver_phone)=="" ? trim($res->trade->receiver_mobile) : trim($res->trade->receiver_phone);
		$order['mobile']        = trim($res->trade->receiver_mobile);
		$order['sign_building'] = "";
		$order['best_time']     = "";
		$order['gift']			= "";
        $order['order_state']= trim($res->trade->status);
		//$order["need_inv"] =  trim($res->trade->payment) >= 500 ? 1 : 0;
		$order["need_inv"] =  1;//需求ORDER-415 要求默认勾选发票//需要发票
		$order["taobao_order_id"] = $taobao_order_id;

		// 发票抬头, 默认写个人
		//$order["inv_payee"] = trim($res->trade->invoice_name) ? trim($res->trade->invoice_name) : trim($res->trade->receiver_name);
		$order["inv_payee"] = $order["need_inv"]? "个人" : "";

		$seller_memo = trim($res->trade->seller_memo)?" [客服留言：".trim($res->trade->seller_memo)."]":'';
		$order["postscript"] = trim($res->trade->buyer_message).$seller_memo;
		$order['shipping_target']="个人";

		/* 隐藏默认字段 */
		$order['department']     = "2";
		$order["from_ad"] = $this -> from_ad;
		$order["shipping_id"] = "4";
		$order["shipping_name"] = "顺丰速运";
		$order['pay_id'] = "24";
		$order['pay_name'] = "淘宝代收款";
		$order['seller_memo'] = $seller_memo;
		$order['money_paid'] = 0;

		// 统计优惠券数量
		/*$bonus = 0;
		if($res -> trade -> promotion_details -> promotion_detail)
		{
			foreach($res -> trade -> promotion_details -> promotion_detail as $item)
			{
				$pro_id = explode("-", $item -> promotion_id);
				$pro_id[0] = str_replace("$","",$pro_id[0]);
				if($pro_id[0] == "shopbonus" || $pro_id[0] == "tmallmjs" || trim($pro_id[0]) == "TmalltspPart")
				{
					$bonus += (float)$item -> discount_fee;
				}
			}
		}
		//$bonus += (float)($res -> trade -> point_fee/100);
		if(count($res->trade->orders->order) > 1)
		{
			$bonus  = (float)$bonus/count($res->trade->orders->order);
		}
		else
		{
			$bonus = 0;
		}*/
//print_r($res);exit;
		// 订单商品处理
		$goods_list = array();
		$gift_b = '';
		$goods_amount = 0;
        $order['seller_discount']=0;
        $order['order_total_price']=0;
        foreach($res->trade->promotion_details->promotion_detail as $youhui){
            $order['seller_discount']+= floatval($youhui->discount_fee);
        }

       

        

		foreach($tb_items as $item)
		{
			//$total_money    =   trim($item->payment) - $bonus;
            $total_num      =   intval($item->num);
			$total_money = floatval($item->total_fee);
            $favorable_price_t = floatval($item->discount_fee)-floatval($item->adjust_fee)+floatval($item->part_mjz_discount);
            $shop_price_t = floatval($item->price)*$total_num;
			if($item->part_mjz_discount)
			{
				$total_money = round($total_money,2)-floatval($item->part_mjz_discount);
			}
			$item_price =   round($total_money/$total_num, 2);
            $favorable_price_i =   round($favorable_price_t/$total_num, 2);
			$item_price_i =   round($shop_price_t/$total_num, 2);

			$goods_amount += $total_money;
			//$a = array(
			//	"goods_sn"	 => trim($item->outer_iid),
			//	//"goods_sn"	=> trim($item->outer_sku_id),
			//	"goods_name" => trim($item->title),
			//	"shop_price" => $item_price,
			//	"shop_prices" => $total_money,
			//	"num" => trim($item->num),
			//	"goods_number" => trim($item->num),
			//	"remark" => trim($item->sku_properties_name),
			//	"tb_url" => $item->oid
			//);
            $a = array(
                "goods_sn"	 => trim($item->outer_iid),
                //"goods_sn"	=> trim($item->outer_sku_id),
                "goods_name" => trim($item->title),
                "shop_price" => $item_price_i,
                "favorable_pricea" => $favorable_price_t,
                "favorable_price" => $favorable_price_i,
                "zhenshi" =>$item_price,
                "shop_prices" => $total_money,
                "num" => trim($item->num),
                "goods_number" => trim($item->num),
                "remark" => trim($item->sku_properties_name),
                "tb_url" => $item->oid
            );

			// 匹配金重
			preg_match_all("/千足金约:(.*)/", $a["remark"], $out);
			$gold_weight_info = empty($out[1][0])?'':$out[1][0];
			if($gold_weight_info)
			{
				//$a["gold"] = "24k";
				$a["gold"] = "足金";
				$gold_weight_info = str_replace(array("约","："),"", $gold_weight_info);
				$gold_weight_info = explode("-", $gold_weight_info);
				$a["gold_weight"] = floatval($gold_weight_info[0]);
			}
			else
			{
				$properties = trim($item->sku_properties_name);
				$_arr = explode(";", $properties);
				$pro_arr = explode(":", $_arr[0]);

				/* PT950 吊坠、项链、耳钉 */
				if($pro_arr[1] == "PT950" || $pro_arr[1] == "pt950")
				{
					$a['gold']   =   "PT950";
					$a['gold_weight'] =   str_replace(array("约","g"), "", $pro_arr[2]);
				}

			}
			//print_r($a);
			//die (__FILE__." Row:".__LINE__);

			//订单中商品标题如果尾字母有B且此商品标题中含有“吊坠”二字，自动在赠品备注中添加：赠黑皮绳
			if(substr($a['goods_name'],-1) == 'B' && strstr($a['goods_name'],"吊坠"))
			{
				$gift_b = "  赠黑皮绳";
			}

            $order['order_total_price']+=$item->price*$total_num;
                //当adjust_fee大于0的时候相当于内部加钱
                //小于0的时候相当于内部优惠
                $order['seller_discount']-=floatval($item->adjust_fee);
			$goods_list[] = $a;
		}

		$order['post_fee'] = count($goods_list) >0 ? trim($res->trade->post_fee) : 0;

		//双11活动
		$ntime = $res->trade->created;//淘宝下单时间
		if($ntime >= '2014-11-11 00:00:00' && $ntime < '2014-11-12 00:00:00')
		{
			$order_payment = $res->trade->payment;
			if($order_payment >= 2999 && $order_payment < 5999)
			{
				$order['gift'] .= "  赠：0.2克金条";
			}elseif($order_payment >= 5999){
				$order['gift'] .= "  赠：双11赠品礼盒KLQX02746";
			}
		}

		if($ntime >= '2014-11-11 00:00:00' && $ntime < '2014-11-11 08:00:00')
		{
			$order['gift'] .= "  赠：BDD卡包";
		}

		$order['gift'] .= $gift_b;//拼接赠黑皮绳
		$order['goods_amount'] = $goods_amount;
		$order['order_amount'] = $goods_amount+	$order['post_fee'];
		$this -> respone["order"] = $order;
		//print_r($order);exit;

		$temp=array();
		foreach($goods_list as $k=>$x){//把一个款多个数量的货 拆成多个1件货
			$goods_list[$k]['is_real']    =   1;
			$g_arr = $x;
			if(intval($x['goods_number']) > 1)
			{
				$x_shop_price=$x['favorable_pricea'];
				$x_goods_number = $x['goods_number'];

				$goods_list[$k]['goods_number'] = 1;
				$goods_list[$k]['num'] = 1;
				$x_sum=0;
				$avg_price=number_format($x['favorable_pricea']/$x_goods_number,2,'.','');
				for($i=0;$i<$x_goods_number-1;$i++){
					//$goods_list[$k]['market_price'] = $avg_price;
                    $goods_list[$k]['favorable_price']=$avg_price;
                    $goods_list[$k]['zhenshi'] =$goods_list[$k]['shop_price']-$goods_list[$k]['favorable_price'];
					$temp[]=$goods_list[$k];
					$x_sum += $avg_price;
				}
				$goods_list[$k]['favorable_price'] = $x_shop_price - $x_sum;
                $goods_list[$k]['zhenshi'] =$goods_list[$k]['shop_price']-$goods_list[$k]['favorable_price'];
				$temp[]=$goods_list[$k];
			}else{
                $goods_list[$k]['favorable_price']=$goods_list[$k]['favorable_price'];
				//$goods_list[$k]['market_price'] = $goods_list[$k]['shop_prices'];
				$temp[]=$goods_list[$k];
			}
		}
		$goods_list = $temp;
		$this -> respone["goods_list"] = $goods_list;
		return $this->respone;
	}
	/**
	 * 用外部单号支付订单
	 * by col
	 */
	function outer_order_pay($order_info, $taobao_order_sn, $price=false)
	{
		$taobao_order_info = $this -> get_order_info($taobao_order_sn);
		// 错误代码
		if(trim($taobao_order_info -> code)) return 1;
		// 淘宝订单状态错误
        //等待卖家发货状态
		if(trim($taobao_order_info->trade->status) != "WAIT_SELLER_SEND_GOODS") return 2;
	//	$real_payment = trim($taobao_order_info -> trade -> payment);// + trim($taobao_order_info -> trade -> point_fee)/100;
		// 支付金额不等于实际支付金额
		//if($price !== false && abs($real_payment - $price) > 1) return 3;
        $model = new BaseOrderInfoModel(28);
        //通过接口查看支付重复问题
         $res = $model->getPaySnExt((string) $taobao_order_info->trade->alipay_no);
        if($res['error']==0){
            if($res['data']!=0){
                foreach($res['data'] as $key=>$val){
                    $ret = $model->getOrderInfoById($val['order_id']);
                    if(!in_array($ret['order_status'],array(3,4))){
                        return 4;
                    }
                }
            }
        }
		$pay_action = array(
			"order_id" => $order_info["order_id"],
			"order_sn" => $order_info['order_sn'],
			"order_time" =>$order_info["create_time"],
			"deposit" => $price,
			"order_amount" => $order_info["order_amount"],
			"balance" => $order_info["order_amount"]-$price,
			"pay_time" => date("Y-m-d H:i:s"),
			"pay_type" => "淘宝代收款流水号为".$taobao_order_info->trade->alipay_no,
			"order_consignee" => $order_info["consignee"],
			"attach_sn" => trim($taobao_order_info->trade->alipay_no),
			"leader" => $_SESSION["userName"],
			"leader_check" => date("Y-m-d H:i:s"),
			"opter_name" => $_SESSION["userName"],
			"status" => "1",
            //淘宝渠道
			"department" => "2",
			"system_flg" => "2"
		);
       //通过接口保存到财务
        $res = $model->cerateOrderPayAction($pay_action);
        if($res['error']>0){
            return 6;
        }
		// 支付订单更改其已付金额和未付金额已付全款更改其订单状态为已付款
        $price = floatval($price);
        $rea = $model->updateOutOrder($price,$order_info['order_id']);
        if(empty($rea)){
            return 7;
        }

		return array('order_id'=>$order_info['order_id'],'pay_stu'=>$rea);
	}

	/**
	 * 通过api查询订单列表
	 * $start_time 开始时间 (查询时间为交易创建时间)
	 * $end_time   结束时间
	 * $status	   交易状态
	 * $page	   当前页数
	 * $page_size  分页大小
	 * $type	   交易类型
	 * $use_has_nest 分页类型
	 * by haibo
	 */
	 function get_order_list($start_time, $end_time, $status, $page=1, $page_size=50, $type='', $use_has_next=false)
	 {
		/* require_once(ROOT_PATH."includes/modules/taobaoOrderApi/TopClient.php");
		require_once(ROOT_PATH."includes/modules/taobaoOrderApi/RequestCheckUtil.php");
		require_once(ROOT_PATH."includes/modules/taobaoOrderApi/request/TradesSoldGetRequest.php");
		
		$c = new TopClient;
		$c->appkey = $this->TAOBAO_APP_KEY;
		$c->secretKey = $this->TAOBAO_SECRETKEY;
		$req = new TradesSoldGetRequest;

		$req->setFields("buyer_nick,tid,num_iid,num");
		
		$req->setStartCreated($start_time);
		$req->setEndCreated($end_time);
		$req->setStatus($status);
		$req->setPageNo($page);
		$req->setPageSize($page_size);
		
		if($use_has_nest)
		{
			$req->setUseHasNext("true");
		}

		if($type != '')
		{
			$req->setType($type);
		}
		

		$resp = $c->execute($req, $this->top_session);
		
		return $resp; */
	    return $this->sendRequest('get_order_list', array('start_time','end_time', 'status', 'page','page_size','type','use_has_next'), array($start_time, $end_time, $status,$page,$page_size,$type,$use_has_next));
	 }

	 /**
	  * 获取淘宝订单详情（所有信息）
	  * by haibo
	  */
	function get_order_info($tid)
	{	
	    /*
		require_once(APP_ROOT."sales/modules/taobaoOrderApi/TopClient.php");
		require_once(APP_ROOT."sales/modules/taobaoOrderApi/RequestCheckUtil.php");
		require_once(APP_ROOT."sales/modules/taobaoOrderApi/request/TradeFullinfoGetRequest.php");

		$c = new TopClient;
		$c -> appkey = $this -> TAOBAO_APP_KEY;
		$c -> secretKey = $this -> TAOBAO_SECRETKEY;
		


		$req = new TradeFullinfoGetRequest;
		$req -> setFields("seller_nick, buyer_nick, title, type, created, tid, seller_rate,buyer_flag, buyer_rate, status, payment, adjust_fee, post_fee, total_fee, pay_time, end_time, modified, consign_time, buyer_obtain_point_fee, point_fee, real_point_fee, received_payment, commission_fee, buyer_memo, seller_memo, alipay_no,alipay_id,buyer_message, pic_path, num_iid, num, price, buyer_alipay_no, receiver_name, receiver_state, receiver_city, receiver_district, receiver_address, receiver_zip, receiver_mobile, receiver_phone,seller_flag, seller_alipay_no, seller_mobile, seller_phone, seller_name, seller_email, available_confirm_fee, has_post_fee, timeout_action_time, snapshot_url, cod_fee, cod_status, shipping_type, trade_memo, is_3D,buyer_email,buyer_area, trade_from,is_lgtype,is_force_wlb,is_brand_sale,buyer_cod_fee,discount_fee,seller_cod_fee,express_agency_fee,invoice_name,service_orders,credit_cardfee,step_trade_status,step_paid_fee,orders,promotion_details,area_id");
		$req -> setTid($tid);
		$res = $c -> execute($req,$this -> top_session);

		return $res;
		*/
	    return $this->sendRequest('get_order_info', array('tid'), array($tid));
	}

	function get_order_info_payment($tid)
	{	
	    /*
		require_once(ROOT_PATH."includes/modules/taobaoOrderApi/TopClient.php");
		require_once(ROOT_PATH."includes/modules/taobaoOrderApi/RequestCheckUtil.php");
		require_once(ROOT_PATH."includes/modules/taobaoOrderApi/request/TradeFullinfoGetRequest.php");
		
		$c = new TopClient;
		$c -> appkey = $this -> TAOBAO_APP_KEY;
		$c -> secretKey = $this -> TAOBAO_SECRETKEY;
		


		$req = new TradeFullinfoGetRequest;
		$req -> setFields(" status, payment, adjust_fee, post_fee, total_fee, pay_time, end_time, modified, consign_time, buyer_obtain_point_fee, point_fee, real_point_fee, received_payment, commission_fee");
		$req -> setTid($tid);
		$res = $c -> execute($req,$this -> top_session);
		
		return $res;
		*/
	    return $this->sendRequest('get_order_info_payment', array('tid'), array($tid));
	}
	
	/**
	  * 淘宝订单备注
	  * by haibo
	  */
	function add_remark($tid, $remark="", $flag=4)
	{
	    /*
		require_once(ROOT_PATH."includes/modules/taobaoOrderApi/TopClient.php");
		require_once(ROOT_PATH."includes/modules/taobaoOrderApi/RequestCheckUtil.php");
		require_once(ROOT_PATH."includes/modules/taobaoOrderApi/request/TradeMemoAddRequest.php");
		
		$c = new TopClient;
		$c -> appkey = $this -> TAOBAO_APP_KEY;
		$c -> secretKey = $this -> TAOBAO_SECRETKEY;

		$req = new TradeMemoAddRequest;

		$req->setTid($tid);
		$req->setMemo($remark);
		$req->setFlag($flag);

		$res = $c->execute($req , $this -> top_session);
		*/
	    return $this->sendRequest('add_remark', array('tid','remark','flag'), array($tid, $remark, $flag));
	}
	
	/***************************************
	****** 获取省份信息
	****** by haibo
	***************************************/
	function get_order_region($province, $city, $district)
	{
		$arr = array();

		/* 省 */
		$sql = " select region_id from ecs_region where region_name like '".$province."%' ";
		$arr['p_id']    =   $GLOBALS['db']->getOne($sql);

		/* 市 */
		if($arr['p_id'] > 0)
		{
			$sql = " select region_id from ecs_region where region_name like '".$city."%' and parent_id='".$arr['p_id']."' ";
			$arr['c_id']    =   $GLOBALS['db']->getOne($sql);
			
			/* 区 */
			if($arr['c_id'] > 0)
			{
				$sql = " select region_id from ecs_region where region_name like '".$district."%' and parent_id='".$arr['c_id']."' ";
				$arr['d_id']    =   $GLOBALS['db']->getOne($sql);
			}

		}

		return $arr;

	}

	/**
	 * 通过api查询订单信息
	 * by col
	 */
	function get_api_order($tid)
	{
	    /*
		require_once(APP_ROOT."sales/modules/taobaoOrderApi/TopClient.php");
		require_once(APP_ROOT."sales/modules/taobaoOrderApi/RequestCheckUtil.php");
		require_once(APP_ROOT."sales/modules/taobaoOrderApi/request/TradeFullinfoGetRequest.php");
		
		$c = new TopClient;
		$c -> appkey = $this -> TAOBAO_APP_KEY;
		$c -> secretKey = $this -> TAOBAO_SECRETKEY;
		
		$req = new TradeFullinfoGetRequest;
		$req -> setFields("tid,status,created,invoice_name,buyer_message,shipping_type,buyer_area,buyer_nick,pic_path,alipay_no,payment,receiver_name,receiver_state,receiver_city,receiver_district,receiver_address,receiver_zip,receiver_mobile,receiver_phone,buyer_email,point_fee,post_fee,orders,promotion_details,seller_flag,seller_memo,buyer_flag,buyer_memo");
		$req -> setTid($tid);
		
		$res = $c -> execute($req,$this -> top_session);

		if(@$res -> trade -> promotion_details -> promotion_detail && count($res->trade->orders->order) > 1)
		{
			foreach($res -> trade -> promotion_details -> promotion_detail as $item)
			{
				$pro_id = explode("-", $item -> promotion_id);
				if($pro_id[0] == "shopbonus" || $pro_id[0] == "tmallmjs" )
				{
					$res -> trade -> payment = (float)$res -> trade -> payment - (float)($item -> discount_fee);
				}
			}
		}
		$res -> trade -> payment = (float)$res -> trade -> payment - (float)($res -> trade -> point_fee/100);
		//print_r($res);exit;
		return $res;
		*/
	    return $this->sendRequest('get_api_order', array('tid'), array($tid));
	}
	/**
	 * 改变淘宝订单发货状态
	 * by col
	 */
	function LogisticsOnlineSendRequest($tid, $invoice_no, $ccode)
	{
	    /*
		require_once(APP_ROOT."shipping/modules/taobaoOrderApi/TopClient.php");
		require_once(APP_ROOT."shipping/modules/taobaoOrderApi/RequestCheckUtil.php");
		require_once(APP_ROOT."shipping/modules/taobaoOrderApi/request/LogisticsOfflineSendRequest.php");
		// 物流方式不在配置内
		if(!array_key_exists($ccode, $this -> shipping_list)) return;

		$c = new TopClient;
		$c -> appkey = $this -> TAOBAO_APP_KEY;
		$c -> secretKey = $this -> TAOBAO_SECRETKEY;
		$req = new LogisticsOfflineSendRequest;
		$req->setTid($tid);
		$req->setOutSid(trim($invoice_no));
		$req->setCompanyCode($this -> shipping_list[$ccode]);
		$resp = $c->execute($req,$this -> top_session);
        file_put_contents('shiping.txt',$resp.PHP_EOL,FILE_APPEND);
        if(array_key_exists('shipping',$resp)){
            if($resp->shipping->is_success==true){
                $not = '淘宝订单号：'.$tid.'写入快递单号'.$invoice_no."成功,时间为".date('y-m-d H:i:s');
            }else{
                $not = '淘宝订单号：'.$tid.'写入快递单号'.$invoice_no."失败 时间为".date('y-m-d H:i:s');
            }
            file_put_contents(APP_ROOT."shipping/modules/taobaoOrderApi/logs/".date('Y-m')."taobao.send.txt",$not.PHP_EOL,FILE_APPEND);
        }
        if(array_key_exists('code',$resp)){
            $not = '淘宝订单号：'.$tid.'写入快递单号'.$invoice_no."失败 失败编号为".$resp->code.'时间为'.date('y-m-d H:i:s');
            file_put_contents(APP_ROOT."shipping/modules/taobaoOrderApi/logs/".date('Y-m')."taobao.send.txt",$not.PHP_EOL,FILE_APPEND);
        }
        
		file_put_contents(APP_ROOT."shipping/modules/taobaoOrderApi/logs/".date('Y-m-d')."_LogisticsOfflineSendRequest.txt",json_encode($resp),FILE_APPEND);
		if(!$resp->is_success){
			$tb_error = $tid."-----code:".$resp->code."-----msg:".$resp->msg."-----sub_code:".$resp->sub_code."----all:".json_encode($resp)."\r\n";
			$of = fopen(APP_ROOT."shipping/modules/taobaoOrderApi/logs/".date('Y-m-d')."_result.txt","a");//创建并打开
			if($of){
				fwrite($of,$tb_error."\n");
			}
			fclose($of);
		}
		return;
		*/
	    return $this->sendRequest('LogisticsOnlineSendRequest', array('tid','invoice_no','ccode'), array($tid, $invoice_no, $ccode));
	}
	/**
	 * 确认发货通知接口
	 * by col
	 */
	function LogisticsOnlineConfirmRequest($tid, $invoice_no)
	{
	    /*
		require_once(APP_ROOT."shipping/modules/taobaoOrderApi/TopClient.php");
		require_once(APP_ROOT."shipping/modules/taobaoOrderApi/RequestCheckUtil.php");
		require_once(APP_ROOT."shipping/modules/taobaoOrderApi/request/LogisticsOnlineConfirmRequest.php");

		$c = new TopClient;
		$c -> appkey = $this -> TAOBAO_APP_KEY;
		$c -> secretKey = $this -> TAOBAO_SECRETKEY;
		$req = new LogisticsOnlineConfirmRequest;
		$req->setTid($tid);
		$req->setOutSid(trim($invoice_no));
		$resp = $c->execute($req,$this -> top_session);
        if(array_key_exists('shipping',$resp)){
            if($resp->shipping->is_success==true){
                $not = '淘宝订单号：'.$tid.'写入快递单号'.$invoice_no."成功,时间为".date('y-m-d H:i:s');
            }else{
                $not = '淘宝订单号：'.$tid.'写入快递单号'.$invoice_no."失败 时间为".date('y-m-d H:i:s');
            }
            file_put_contents(APP_ROOT."shipping/modules/taobaoOrderApi/logs/".date('Y-m')."taobao.send.txt",$not.PHP_EOL,FILE_APPEND);
        }
        if(array_key_exists('code',$resp)){
            $not = '淘宝订单号：'.$tid.'写入快递单号'.$invoice_no."失败 失败编号为".$resp->code.'时间为'.date('y-m-d H:i:s');
            file_put_contents(APP_ROOT."shipping/modules/taobaoOrderApi/logs/".date('Y-m')."taobao.send.txt",$not.PHP_EOL,FILE_APPEND);
        }
		file_put_contents(APP_ROOT."shipping/modules/taobaoOrderApi/logs/".date('Y-m-d')."_LogisticsOnlineConfirmRequest.txt",json_encode($resp),FILE_APPEND);
		return;
		*/
	    return $this->sendRequest('LogisticsOnlineConfirmRequest', array('tid','invoice_no'), array($tid, $invoice_no));
	}

	/**
	* 获取非红旗订单 即：未录入BDD系统订单
	*/
	function getTaobaoOrderList_1111($s_date,$e_date,$page_no)
	{
	    /*
		require_once(ROOT_PATH."includes/modules/taobaoOrderApi/TopClient.php");
		require_once(ROOT_PATH."includes/modules/taobaoOrderApi/RequestCheckUtil.php");
		require_once(ROOT_PATH."includes/modules/taobaoOrderApi/request/TradesSoldGetRequest.php");

		$c = new TopClient;
		$c -> appkey = $this -> TAOBAO_APP_KEY;
		$c -> secretKey = $this -> TAOBAO_SECRETKEY;


		$req = new TradesSoldGetRequest();
		$req -> setFields("seller_nick, buyer_nick, title, type, created, tid, seller_rate,buyer_flag, buyer_rate, status, payment, adjust_fee, post_fee, total_fee, pay_time, end_time, modified, consign_time, buyer_obtain_point_fee, point_fee, real_point_fee, received_payment, commission_fee, buyer_memo, seller_memo, alipay_no,alipay_id,buyer_message, pic_path, num_iid, num, price, buyer_alipay_no, receiver_name, receiver_state, receiver_city, receiver_district, receiver_address, receiver_zip, receiver_mobile, receiver_phone,seller_flag, seller_alipay_no, seller_mobile, seller_phone, seller_name, seller_email, available_confirm_fee, has_post_fee, timeout_action_time, snapshot_url, cod_fee, cod_status, shipping_type, trade_memo, is_3D,buyer_email,buyer_area, trade_from,is_lgtype,is_force_wlb,is_brand_sale,buyer_cod_fee,discount_fee,seller_cod_fee,express_agency_fee,invoice_name,service_orders,credit_cardfee,step_trade_status,step_paid_fee,orders,promotion_details,area_id");

		$req->setPageNo($page_no);
		$req->setStatus("WAIT_SELLER_SEND_GOODS");//买家已付款状态
		$req->setStartCreated($s_date);//淘宝双十一当天 从00点开始抓单		
		$req->setEndCreated($e_date);
		$req->setUseHasNext('true');//设置下一页提示
		$resp = $c->execute($req,$this -> top_session);
		return $resp;
		*/
	    return $this->sendRequest('getTaobaoOrderList_1111', array('s_date','e_date','page_no'), array($s_date, $e_date,$page_no));
	}
	
	function sendRequest($act, $keys, $vals) {
	    $url = TB_API_URL.'/index.php?act='.$act;
	     
	    $args=array();
	    foreach($keys as $k=>$v){
	        $v=trim($v);
	        if(!empty($v)){
	            $args[$keys[$k]]=$vals[$k];
	        }
	    }
	     
	    $args['x_epoch'] = time();
	    $args['x_exp'] = 5;
	    ksort($args);
	     
	    $filter = json_encode($args);
	    $auth_key = Util::get_defined_array_var('API_AUTH_KEYS', 'taobaoapi');
	    $signed_data = array('filter'=> $filter, 'sign' => md5($act. $filter . $auth_key));
	     
	    $resp = Util::httpCurl($url,$signed_data,false,true,30);   
	    $respObject = json_decode($resp);

	    return $respObject;
	}
}
?>
