<?php
class jd_jdk_php_2
{
	var $key = "7DADAC9ED8F29A0790087849188EB2E1";
	var $secret = "f5a1e7f4c4e04fcfa2084842098e9c63";
	//var $access_token = "117eb8b5-8361-4e40-b331-91878aee8e16";
	var $access_token = "245edbbb-eb39-401d-8aea-24166965e2c9";
	var $respone = array("is_error" => false, "message" => "");
	var $new_jd = false;
	/**
	 * 
	 * by col
	 */
	function jd_jdk_php_2()
	{
	}

	/**
	 * 组建订单数据
	 * by col
	 */
	function make_order_info($taobao_order_id)
	{

		$re = $this -> get_api_order($taobao_order_id);
        //echo '<pre>';
        //print_r($re);
        //echo '</pre>';
        //exit;
        
		$tr = $this->info($taobao_order_id);
	
		if(trim($re->order_get_response->code))
		{
			$this -> respone["is_error"] = true;
			$this -> respone["message"] = trim($re->order_get_response->zh_desc);
			return $this -> respone;
		}

        if($re->order_get_response->order->orderInfo=='')
        {
            $this -> respone["is_error"] = true;
            $this -> respone["message"] = "订单号有误!!";
            return $this -> respone;
        }

//		if($re->order_get_response->order->orderInfo->order_state != "WAIT_SELLER_STOCK_OUT")
//		{
//			$this -> respone["is_error"] = true;
//			$this -> respone["message"] = "京东订单状态有误不能导入!!";
//			return $this -> respone;
//		}
		
		$r=$re->order_get_response->order->orderInfo;
		//$t=$tr->order_printdata_response->order_printdata->Consignee;
		/* 取得收货地址列表 */
		$order['order_id']     = trim($r->order_id);
		$order['department']     = "81";
		$order['country']       = 1;
		$order['vender_id']      = trim($r->vender_id);
		$order['pay_type']          = trim($r->pay_type);
		$order['delivery_type']         = trim($r->delivery_type);
		$order['order_total_price']         = trim($r->order_total_price);
		$order['order_amount']       = trim($r->order_seller_price);
		$order['freight_price']       = trim($r->freight_price);
		$order['seller_discount']           = trim($r->seller_discount);
		$order['invoice_info']           = trim($r->invoice_info);
		$order['order_state'] = trim($r->order_state);
		//$order['order_state_remark']     = trim($r->order_state_remark);
		$order["seller_memo"] = trim($r->order_remark);
		$order["order_start_time"] = trim($r->order_start_time);
		$order["province"] =$tr["province"];
		$order["post_fee"] =0;
		//$order['zipcode'] = trim($r->consignee_info);

		$order["city"] = $tr["city"];
		$order["county"] = "1";
		$order['consignee']= trim($tr["consignee"]);
		$order['district']= trim($tr["district"]);
		$order['email']= trim($tr["email"]);
		$order["mobile"] = trim($tr["mobile"]);
		$order['tel']           = trim($tr["mobile"]);
		$order["address"] = trim($tr["address"]);
		$order["postscript"] = "来自：京东，登陆京东相应店铺后台，用外部订单号找出相应订单，点出库打印面单，面单装到背胶袋里，贴在包装盒上发货！请物流尽快发货，谢谢！";
		$order["taobao_order_id"] = $taobao_order_id;
		$order['pay_name'] = "京东商城代收款";
		$order['inv_payee']="个人";
		$order['seller_memo']=$order["postscript"];

		/* 隐藏默认字段 */
		$order["from_ad"] = "008100820645";
		$order["shipping_id"] = "4";
		$order["shipping_name"] = "顺丰速运";
		$order['pay_id'] = "100";
		$order['pay_name'] = "京东代收款";
        //var_dump($order);exit;
       /* echo '<pre>';
        print_r($order);
        echo '</pre>';
        exit;*/


		if($this->new_jd)
		{
			$order = $this->new_order_info($order,$r);
		}
		$this -> respone["order"] = $order;
		/*
		// 统计优惠券数量
		$bonus = 0;
		if($r['promotion_details']['promotion_detail'])
		{
			foreach($r['promotion_details'][' promotion_detail'] as $item)
			{
				$pro_id = explode("-", $item -> promotion_id);
				if($pro_id[0] == "shopbonus")
				{
					$bonus += (float)$item -> discount_fee;
				}
			}
		}
		
		$bonus += (float)($r['item_info_list']['gift_point']/100);
		$bonus  = (float)$bonus/count($r['item_info_list']['item_total']);
		*/
		// 订单商品处理
        //订单的优惠
		foreach($r->item_info_list as $item)
		{
			$a = array(
				"goods_sn"	 => trim($item->product_no),
				"goods_name" => trim($item->sku_name),
				"shop_price" => trim($item->jd_price),
                "shop_prices"=> trim($item->jd_price*$item->item_total),
				"goods_number" => trim($item->item_total),
				"remark" => ""
			);
			$b = $a['goods_name'];
			$c = strpos($b, '团购');
			if($c!==false)
			{
			 $this -> respone["order"]["from_ad"]="008100820692";
			 $this -> respone["order"]["order_type"]="3";
			}

			if(strpos($b, '金条')!==false)
			{
				$this -> respone["order"]["order_type"]="5";
			}
			
			$goods_list[] = $a;
		}

        $temp=array();
        foreach($goods_list as $k=>$x){//把一个款多个数量的货 拆成多个1件货
            $g_arr = $x;
            if(intval($x['goods_number']) > 1)
            {
                $x_shop_price=$x['shop_prices'];
                $x_goods_number = $x['goods_number'];

                $goods_list[$k]['goods_number'] = 1;
                $goods_list[$k]['num'] = 1;
                $x_sum=0;
                $avg_price=number_format($x['shop_prices']/$x_goods_number,2,'.','');
                for($i=0;$i<$x_goods_number-1;$i++){
                    $goods_list[$k]['goods_price'] = $avg_price;
                    $temp[]=$goods_list[$k];
                    $x_sum += $avg_price;
                }
                $goods_list[$k]['goods_price'] = $x_shop_price - $x_sum;
                $temp[]=$goods_list[$k];
            }else{
                $goods_list[$k]['goods_price'] = $goods_list[$k]['shop_prices'];
                $temp[]=$goods_list[$k];
            }
        }
        $goods_list = $temp;
		$this -> respone["goods_list"] = $goods_list;
		return $this->respone;
		
	}

	/**
	 * 京东新店订单数据
	 * by ZhangLijuan
	 */
	
	function new_order_info($order,$r)
	{
		$info = array(
				"province"	=>	trim($r->consignee_info->province),
				"city"		=>	trim($r->consignee_info->city),
				"district"	=>	trim($r->consignee_info->county)
			);
		$tr = $this->jd_info($info);

		/*用户基本信息*/
		$order["province"]				 = $tr["province"];
		$order["city"]					 = $tr["city"];
		$order['district']				 = trim($tr["district"]);
		$order["county"]				 = "1";
		$order['consignee']				 = trim($r->consignee_info->fullname);
		//$order['email']					 = "gzzhangqinghua@jd.com";
        $order['email']                  = "dzfp@kela.cn";
		$order["mobile"]				 = trim($r->consignee_info->mobile);
		$order['tel']					 = trim($r->consignee_info->telephone);
		$order["address"]				 = trim($r->consignee_info->full_address);


		/*发票*/
		$order['need_inv']				 = empty($r->invoice_info)?0:1;

		$inv = explode(";",trim($r->invoice_info));


        if(isset($inv[1])&&isset($inv[2])){
            $order['inv_payee']		  		 = substr($inv[1],stripos($inv[1],':')+1);
            $order['inv_content']			 = substr($inv[2],stripos($inv[2],':')+1);//发票内容
        }


		/* 隐藏默认字段 */
		$order["from_ad"]				 = "008100822414";
		$pay_type						 = substr(trim($r->pay_type),0,1);
		if($pay_type == 1)//代收货款
		{
			$order["shipping_id"] = "22";
			$order["shipping_name"] = "京东快递";
			$order['order_pay_type'] = "246";
			$order['pay_name'] = "京东渠道－自有物流货到付款";
			$order["postscript"] = "来自：京东，登陆京东相应店铺后台，用外部订单号找出相应订单，点出库打印面单，面单贴在包装盒上发货！请物流尽快发货，谢谢！";
		}else{
            //if($r->order_payment>=500){
			if($r->order_seller_price>=500){
                $order["shipping_id"] = "4";
                $order["shipping_name"] = "顺丰速运";
            }else{
            	//
                $order["shipping_id"] = "19";
                $order["shipping_name"] = "中通快递";
                //$order["shipping_id"] = "4";
                //$order["shipping_name"] = "顺丰速运";                
            }

            $nowtime = date('Y-m-d H:i:s');
            if($nowtime >= '2019-01-26 00:00:00' && $nowtime <='2019-02-12 23:59:59'){
                $order["shipping_name"] = "顺丰速运";
                $order["shipping_id"] = "4";
            }
            
			$order['order_pay_type'] = "245";
			$order['pay_name'] = "京东结算－京东官网在线支付";
			$order["postscript"] = " 来自：京东，登陆京东相应店铺后台，用外部订单号找出相应订单，点出库附上快递单号即可，请物流尽快发货，谢谢！";
		}
		return $order;
	}

	/**
	 * by ZhangLijuan
	 */
	function jd_info($info)
	{

		$province = array("北京","天津","上海","重庆");
		if(in_array($info['province'],$province))
		{
			$info['district'] = $info['city'];
			$str = mb_substr($info['city'],-1,1,'utf-8');
			if($str == "区")
			{
				$info['city'] = "市辖区";
			}else{
				$info['city'] = $str;
			}
		}
		return $info;
	}


	/**
	 * 通过api查询订单信息
	 * by col
	 */
	function get_api_order($order_id)
	{
		require_once("JdClient.php");
		//require_once("request/order/NewOrderGetRequest.php");
		require_once("request/order/OrderGetRequest.php");
		$client = new JdClient($this->key, $this->secret, $this->access_token);
	/*	var_dump($this->key,$this->secret,$this->access_token);*/
		//exit;
		$req=new OrderGetRequest();
		$req->setOrderId($order_id);
        //接口是否过滤订单状态
	    //$req->setOrderState("WAIT_SELLER_STOCK_OUT");
		//$req->setOptionalFields("item_info_list");
		$resp=$client->execute($req);
        //echo '<pre>';
        //print_r($resp);
        //echo '</pre>';
        //exit;
		return $resp;
	}
	/**
	 * 通过api查询商品信息
	 * by col
	 */
	function get_api_goods()
	{
		require_once("JdClient.php");
		require_once("request/ware/WareIdsSearchRequest.php");
		require_once("request/ware/WareSearchRequest.php");
		$client = new JdClient($this->vender_id,$this ->vender_key,$this ->url);
		$fields=array(array("key"=>"product_no","value"=>"11231"));
		$req=new WareIdsSearchRequest();
		for($i=1;$i<89;$i++){
			$req->setPage($i);
			$req->setPageSize(10);
			//$req->setQueryFields($fields);
			$req->setWareState("selling");
			$req->setStartTime("2012-07-01 10:00:00");
			$req->setEndTime("2012-10-01 10:00:00");
			$resp=$client->execute($req);
			$list = json_decode($resp,true);
			$lists[$i] = $list['ware_ids_search_response']['ware_ids']['ware_id_list'];
		}
		header("Content-Disposition: attachment;filename=store_group_info.csv");
		echo iconv("utf-8", "gbk" , "商家id,商品id,商品京东价,商品名称,商品状态,商品货号,商品总库存,上下架时间") . "\n";
		foreach ($lists as $ll){
			$order_ids =$ll;
			$reqs=new WareSearchRequest();
			$reqs->setWareIdList($order_ids);
			$reqs->setOptionalFields("ware_id,product_no,ware_stocks_total,delisting_or_listing_time,ware_state,ware_name,jd_price");
			$resps=$client->execute($reqs);
			$listss = json_decode($resps,true);
			$data_infos = $listss['ware_search_response']['ware_search']['wares'];
			foreach ($data_infos as $item){
				echo iconv("utf-8", "gbk" , $item['vender_id'] . "," . $item['ware_id'] . "," . $item['jd_price'] . "," . $item["ware_name"] . "," .$item['ware_state'] . "," . $item['product_no'] . "," . $item['ware_stocks_total'] . "," . $item['delisting_or_listing_time']) . "\n";
			}
		}
		exit;
       return $data_infos;
	}
	function info($order_id)
	{
		/*
		$area_list = array(
			"LBP - 北京" => array("mobile" => "18611066368", "address" => "北京市朝阳区黑庄户乡双树村南里甲8号 （京东华北分拣中心）","consignee" => "孙佳佳【北京仓库员】","email" => "sunjiajia@jd.com","province"=>"北京市","city"=>"北京市" ),
			"LBP - 上海" => array("mobile" => "021-31280855/18821130855", "address" => "上海市嘉定区南翔镇惠申路405号进门右手边月台门","consignee" => "朱安舟【上海仓库员】","email" => "shzhuanzhou@360buy.com","province"=>"上海市","city"=>"上海市" ),
			"LBP - 广州" => array("mobile" => "13172072787", "address" => "广州市萝岗区南岗镇笔村京东华南第二期物流中心","consignee" => "张清华【广州仓库员】","email" => "gzzhangqinghua@360buy.com","province"=>"广东省","city"=>"广州市"),
            "LBP - 成都" => array("mobile" => "18328751359", "address" => "成都市新都区顺运路9号宝湾物流园3号库京东商城","consignee" => "鲍观友【成都仓库员】","email" => "","province"=>"四川省","city"=>"成都市"),
			"LBP - 武汉" => array("mobile" => "13377860112", "address" => "湖北省武汉市汉阳区沌口经济开发区枫树四路70号 四方物流(郭徐岭)京东商城二号库4#门","consignee" => "杨爱姣【武汉仓库员】","email" => "yangaijiao@360buy.com","province"=>"湖北省","city"=>"武汉市"),
			"LBP - 沈阳"  => array("mobile" => "18624012360", "address" => "辽宁省沈阳市沈北新区宏业街10号京东商城分拣中心","consignee" => "罗 浩【沈阳仓库员】","email" => "","province"=>"辽宁省","city"=>"沈阳市" ),
			"LBP - 西安"  => array("mobile" => "18901253960/18092530324", "address" => "西安市三桥镇天台八路西段美都工程机械园仓库31号门","consignee" => "司明康【仓库员】","email" => "","province"=>"陕西省","city"=>"西安市" ),
		);
		$order_print = $this->get_order_print($order_id);
		
		
		$area = trim($order_print ->order_printdata_response->order_printdata->cky2_name);
		return $area_list[$area];
		*/
		$gz_info = array("mobile" => "13172072787", "address" => "广州市萝岗区南岗镇笔村京东华南第二期物流中心","consignee" => "张清华【广州仓库员】","email" => "gzzhangqinghua@jd.com","province"=>"广东省","city"=>"广州市","district"=>"萝岗区");
		return $gz_info;
	}
	/**
	 * 获取订单面单
	 * by col
	 */
	function get_order_print($order_id)
	{
		require_once("JdClient.php");
		require_once("request/order/OrderPrintDataGetRequest.php");
		$client = new JdClient($this->key, $this->secret, $this->access_token);
		$req=new OrderPrintDataGetRequest();
		$req->setOrderId($order_id);
		$resp=$client->execute($req);
		return $resp;
	}
	
	/**
	 * 通过api查询订单信息
	 * by col
	 */
	function get_jd_order($order_id)
	{
		require_once("JdClient.php");
		require_once("request/order/OrderGetRequest.php");
		$client = new JdClient($this->key, $this->secret, $this->access_token);
		
		$req=new OrderGetRequest();
		$req->setOrderId($order_id);
		$req->setOrderState("WAIT_SELLER_STOCK_OUT,SEND_TO_DISTRIBUTION_CENER,DISTRIBUTION_CENTER_RECEIVED,WAIT_GOODS_RECEIVE_CONFIRM,RECEIPTS_CONFIRM,FINISHED_L,TRADE_CANCELED,LOCKED");
		$resp=$client->execute($req);
		return $resp;
	}
	
	/**
	 * 通过api发货LPB
	 * by YICHEN
	 */
	function take_jd_order_ship($order_id, $wuliu_sn)
	{
		require_once("JdClient.php");
		require_once("request/order/OrderLbpOutstorageRequest.php");
		$client = new JdClient($this->key, $this->secret, $this->access_token);
		
		$req=new OrderLbpOutstorageRequest();
		$req->setOrderId($order_id);
		$req->setLogisticsId("1");
		$req->setPackageNum("1");
		$req->setSendType("1");
		$req->setWaybill($wuliu_sn);
		$req->setTradeNo($wuliu_sn);
		$resp=$client->execute($req);
		return $resp;
	}
	/*
	获取物流公司 yichen
	*/
	function get_logistics()
	{
		require_once("JdClient.php");
		require_once("request/delivery/DeliveryLogisticsGetRequest.php");
		$client = new JdClient($this->key, $this->secret, $this->access_token);
		
		$req=new DeliveryLogisticsGetRequest();
		$resp=$client->execute($req);
		return $resp;
	}
}


?>