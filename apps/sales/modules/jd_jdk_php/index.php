<?php
class jd_jdk_php
{
	var $vender_id = "10066";
	var $vender_key = "1B9ACCCED5DE6B928E22947F4CDE89A8";
	var $url = "http://gw.shop.360buy.com/routerjson";
	var $respone = array("is_error" => false, "message" => "");
	/**
	 * 
	 * by col
	 */
	function jd_jdk_php()
	{
	}
	/**
	 * 
	 * by col
	 */
	function __construct()
    {
        $this->jd_jdk_php();
    }
	/**
	 * 组建订单数据
	 * by col
	 */
	function make_order_info($taobao_order_id)
	{
	    $sql="select taobao_order_id from ecs_order_info where taobao_order_id='$taobao_order_id' and order_status!='5'";
		$GLOBALS['db'] -> must_master = true;
		$m=$GLOBALS['db']->getOne($sql);
		if($m!="")
		{
		  $this -> respone["is_error"] = true;
		  $this -> respone["message"] = "该订单已存在!!";
		  return $this -> respone;
		}
		$res = $this -> get_api_order($taobao_order_id);
		$re=json_decode($res,true);
		$tr = $this->info($taobao_order_id);
		if(trim($re["code"]))
		{
			$this -> respone["is_error"] = true;
			$this -> respone["message"] = trim($re["sub_msg"]);
			return $this -> respone;
		}
		if($re['new_order_get_response']['order']['orderInfo']['order_state'] != "WAIT_SELLER_STOCK_OUT")
		{
			$this -> respone["is_error"] = true;
			$this -> respone["message"] = "京东订单状态有误不能导入!!";
			return $this -> respone;
		}
		
		$r=$re['new_order_get_response']['order']['orderInfo'];
		$t=$tr['order_printdata_response']['order_printdata']['Consignee'];
		//var_dump($t);exit;
		/* 取得收货地址列表 */
		$order['order_id']     = trim($r['order_id']);
		$order['department']     = "10";
		$order['country']       = 1;
		$order['vender_id']      = trim($r['vender_id']);
		$order['pay_type']          = trim($r['pay_type']);
		$order['delivery_type']         = trim($r['delivery_type']);
		$order['order_total_price']         = trim($r['order_total_price']);
		$order['order_payment']       = trim($r['order_payment']);
		$order['freight_price']       = trim($r['freight_price']);
		$order['seller_discount']           = trim($r['seller_discount']);
		$order['invoice_info']           = trim($r['invoice_info']);
		$order['order_state'] = trim($r['order_state']);
		$order['order_state_remark']     = trim($r['order_state_remark']);
		$order["order_remark"] = trim($r["order_remark"])=="" ? 0 : 1;
		$order["order_start_time"] = trim($r["order_start_time"]);
		$order["province"] = trim($tr["province"]);
		//$order['zipcode'] = trim($r["consignee_info"]);
		$order["city"] = trim($tr["consignee_info"]["city"]);
		$order["county"] = "1";
		$order['consignee']= trim($tr["consignee"]);
		$order['email']= trim($tr["email"]);
		$order["mobile"] = trim($tr["mobile"]);
		$order['tel']           = trim($tr["mobile"]);
		$order["address"] = trim($tr["address"]);
		$order["postscript"] = "来自：京东，登陆京东相应店铺后台，用外部订单号找出相应订单，点出库打印面单，面单装到背胶袋里，贴在包装盒上发货！请物流尽快发货，谢谢！";
		$order["taobao_order_id"] = $taobao_order_id;
		$order['pay_name'] = "京东商城代收款";
		$order['shipping_target']="个人";
		
		/* 隐藏默认字段 */
		$order["from_ad"] = "001000160645";
		$order["shipping_id"] = "4";
		$order["shipping_name"] = "顺丰速运";
		$order['pay_id'] = "100";
		$order['pay_name'] = "京东代收款";
        //var_dump($order);exit;
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
		$goods_list = array();

		foreach($r["item_info_list"] as $item)
		{
			$a = array(
				"goods_sn"	 => trim($item["product_no"]),
				"goods_name" => trim($item["sku_name"]),
				"shop_price" => trim($item["jd_price"]),
				"num" => trim($item["item_total"]),
				"remark" => ""
			);
			$b = $a['goods_name'];
			$c = strpos($b, '团购');
			if($c!==false)
			{
			 $this -> respone["order"]["from_ad"]="001000160692";
			 $this -> respone["order"]["order_type"]="3";
			}

			if(strpos($b, '金条')!==false)
			{
				$this -> respone["order"]["order_type"]="5";
			}
			
			$goods_list[] = $a;
		}

		$this -> respone["goods_list"] = $goods_list;
		return $this->respone;
		
	}
	/**
	 * 通过api查询订单信息
	 * by col
	 */
	function get_api_order($order_id)
	{
		require_once("JdClient.php");
		require_once("request/order/NewOrderGetRequest.php");
		$client = new JdClient($this->vender_id,$this ->vender_key,$this ->url);
		$req=new NewOrderGetRequest();
		$req->setOrderId($order_id);
		$req->setOrderState("WAIT_SELLER_STOCK_OUT");
		//$req->setOptionalFields("item_info_list");
		$resp=$client->execute($req);
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
		$area_list = array(
			"LBP - 北京" => array("mobile" => "15210718620", "address" => "北京市朝阳区黑庄户乡双树村南甲8号京东商城华北分拣中心","consignee" => "沈耀涛【仓库员】","email" => "shenyaotao@360buy.com","province"=>"北京市","city"=>"北京市" ),
			"LBP - 上海" => array("mobile" => "021-31280855/18821130855", "address" => "上海市嘉定区南翔镇惠申路405号进门右手边月台门","consignee" => "朱安舟【仓库员】","email" => "shzhuanzhou@360buy.com","province"=>"上海市","city"=>"上海市" ),
			"LBP - 广州" => array("mobile" => "13172072787", "address" => "广州市萝岗区南岗镇笔村京东华南第二期物流中心","consignee" => "张清华【仓库员】","email" => "gzzhangqinghua@360buy.com","province"=>"广东省","city"=>"广州市"),
            "LBP - 成都" => array("mobile" => "18328751359", "address" => "成都市新都区顺运路9号宝湾物流园3号库京东商城","consignee" => "鲍观友【仓库员】","email" => "","province"=>"四川省","city"=>"成都市"),
			"LBP - 武汉" => array("mobile" => "13377860112", "address" => "湖北省武汉市汉阳区沌口经济开发区枫树四路70号 四方物流(郭徐岭)京东商城二号库4#门","consignee" => "杨爱姣【仓库员】","email" => "yangaijiao@360buy.com","province"=>"湖北省","city"=>"武汉市"),
			"LBP - 沈阳"  => array("mobile" => "18624012360", "address" => "辽宁省沈阳市沈北新区宏业街10号京东商城分拣中心(POP收货组)","consignee" => "罗 浩【仓库员】","email" => "","province"=>"辽宁省","city"=>"沈阳市" ),
			"LBP - 西安"  => array("mobile" => "18901253960/18092530324", "address" => "西安市三桥镇天台八路西段美都工程机械园仓库31号门","consignee" => "司明康【仓库员】","email" => "","province"=>"陕西省","city"=>"西安市" ),
		);
		$order_print = $this->get_order_print($order_id);
		$order_print = json_decode($order_print);
		$area = trim($order_print ->order_printdata_response->order_printdata->cky2_name);
		return $area_list[$area];
	}
	/**
	 * 获取订单面单
	 * by col
	 */
	function get_order_print($order_id)
	{
		require_once("request/order/OrderPrintDataGetRequest.php");
		$client = new JdClient($this->vender_id,$this ->vender_key,$this ->url);
		$req=new OrderPrintDataGetRequest();
		$req->setOrderId($order_id);
		$resp=$client->execute($req);
		return $resp;
	}
}


?>