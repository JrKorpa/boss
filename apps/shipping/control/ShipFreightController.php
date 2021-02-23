<?php
/**
 *  -------------------------------------------------
 *   @file		: ShipFreightController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class ShipFreightController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array('print_express','orderExpress');
	protected $pay_type = array('0'=>'默认','1'=>'展厅订购','2'=>'货到付款');	/** 货到付款配置 (2015-4-13 那时，前端还没有数字字典) **/
    //接口文件数组
    protected $from_arr = array(
        2 => array("ad_name"=> "淘宝B店", "api_path" =>"taobaoOrderApi"),
        71 => array("ad_name"=> "京东SOP", "api_path" =>"jd_jde_php"),
        "taobaoC" => array("ad_name"=> "淘宝C店", "api_path" =>"taobaoOrderApi"),
        "jingdongA" => array("ad_name"=> "京东", "api_path" =>"jd_jdk_php_2"),
        "jingdongB" => array("ad_name"=> "京东/裸钻", "api_path" =>"jd_jda_php"),
        "jingdongC" => array("ad_name"=> "京东/金条", "api_path" =>"jd_jdb_php"),
        "jingdongD" => array("ad_name"=> "京东/名品手表", "api_path" =>"jd_jdc_php"),
        "jingdongE" => array("ad_name"=> "京东/欧若雅", "api_path" =>"jd_jdd_php"),
        "paipai" => array("ad_name"=> "拍拍网店", "api_path" =>"paipaiOrder"),
    );

    //外部订单的仓库
    protected $warehouse_arr=array(
        2=>'线上低值库',
        79=>'深圳珍珠库',
        184=>'黄金网络库',
        386=>'彩宝库',
        482=>'淘宝黄金',
        483=>'京东黄金',
        484=>'淘宝素金',
        485=>'京东素金',
        486=>'线上钻饰库',
        546=>'线上唯品会货品库',
        487=>'线上混合库',
        96=>'总公司后库',
        5=>'半成品库',
        342=>'黄金店面库',
        369=>'主站库',
        521=>'投资金条库',
        546=>'线上唯品会货品库',
    );

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('ship_freight_search_form.html',array('bar'=>Auth::getBar()));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			//'参数' = _Request::get("参数");
		);
		$page = _Request::getInt("page",1);
		$where = array();

		$model = new ShipFreightModel(43);
		$data = $model->pageList($where,$page,10,false);

		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'ship_freight_search_page';
		$this->render('ship_freight_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 * 查询订单信息
	 */
	public function orderSearch(){

		$order_no =_Request::get("order_no");
		$model = new ShipFreightModel(43);

		//获取订单明细ID
		$data = $model->getOrderDetailsId($order_no);

		if(empty($data)){
			echo "<div class='alert alert-info'>很抱歉,未查到该订单相关信息！</div>";
			exit;
		} else if($data[0]['apply_return'] == '2'){
			#检测是否有退款操作 有则不能操作
			echo "<div class='alert alert-info'>很抱歉,该订单已申请退款,请退回库房核实。</div>";
			exit;
		} else if ($data[0]['apply_close'] == '1') {
			#检测是否有退款操作 有则不能操作
			echo "<div class='alert alert-info'>订单号,".$order_no."审核关闭状态，不能操作</div>";
			exit;
		}
		
		$orderInfo = array_merge($data[0],$data[1]);

		$CustomerSourcesModel = new CustomerSourcesModel(1);
		$sources_name = $CustomerSourcesModel->getSourceNameById($orderInfo['customer_source_id']);
		$orderInfo['order_source'] = $sources_name ;//订单来源处理
		//支付方式处理
		$paymodel = new PaymentModel(1);
		$order_pay_type = $orderInfo['order_pay_type'];
		$orderInfo['order_pay_type'] = $paymodel->getNameById($orderInfo['order_pay_type']);
		//var_dump($orderInfo);exit;

        $out_order_sn = '';
        $out_order_sn = $data[4];
		$goodsInfo = $data[2];

	    $images = array();
		foreach ($goodsInfo as $key=>$val)
		{
			if (!array_key_exists($val['goods_sn'], $images)) {
				//$style_aip = ApiModel::style_api(array('style_sn'),array($val['goods_sn']),'GetStyleGallery');
				$style_aip = $model->GetStyleGallery($val['goods_sn']);
				if(isset($style_aip['thumb_img']))
				{//45°图
				   $val['thumb_img'] =  $style_aip['thumb_img'];
				}
				else
				{
					$val['thumb_img'] = '';
				}
				$images[$val['goods_sn']] = $val['thumb_img'];
			} else {
				$val['thumb_img'] = $images[$val['goods_sn']];
			}
			$goodsInfo[$key] = $val;
		}
		//var_dump($goodsInfo);exit;
		$order_invoice = $data[3];

		#取得销售单的商品数量
		//$ret=ApiModel::warehouse_api(array('order_sn'),array($order_no),'GetGoodsIdsByOrderSN');
		$ret=$model->GetGoodsIdsByOrderSN($order_no);
		//获取订单的赠品信息
		//$gift = ApiModel::sales_api(array('order_id'),array($orderInfo['id']),'getOrderGiftInfo');
		$SalesModel = new SalesModel(27);
		$gift = $SalesModel->getOrderGiftInfo($orderInfo['id']);
		$zengpin=array('remark'=>'','goods'=>'');
		if(isset($gift['gift_id']))
		{
			$zengpin['remark']=isset($gift['remark'])?$gift['remark']:'';

			$gift_id_arr = empty($gift['gift_id']) ? null : explode(',', $gift['gift_id']);
			$gift_num_arr = empty($gift['gift_num']) ? null : explode(',', $gift['gift_num']);

			foreach($gift_id_arr as $key => $val){
			   if(array_key_exists($val, $this->gifts)){
				  $zengpin['goods'] .= $this->gifts[$val].$gift_num_arr[$key].'个&nbsp';
			   }
			}
		}
		
		//查询订单中是否有需要销账的货号
		$all_zp=$model->getzengpinById($order_no);
		
		//add order action list
		$order_action = $model->getOrderActionLogLists($order_no);      

		$orderInfo['address'] = trim($orderInfo['address']);
		$region = new RegionModel(1);
		$express = new ExpressView(new ExpressModel(1));
        

        $invoice_notice= 'no';   //非电子发票 如果还未开发票 发货前需提示
	    if(!empty($order_invoice)){
	        if(SYS_SCOPE=='boss'){
	            $order_pay_type_limit= json_decode(INVOICE_ORDER_PAY_TYPE_LIMIT,true);  //需要开电子发票的支付方式
	            if($order_invoice['is_invoice']==1 && $order_invoice['invoice_status']!=2){
                    if(in_array($order_pay_type,$order_pay_type_limit) && $order_invoice['invoice_type'] !=3){
                        $invoice_notice= 'no';
                    }else{
                    	$invoice_notice= 'yes';
                    }

	            }
	    	}
	    	if(SYS_SCOPE=='zhanting'){
	    		if($order_invoice['is_invoice']==1 && $order_invoice['invoice_status']!=2){
	    		    $invoice_notice= 'yes';
	    		}	    		
	    	}	
        }
        $hidden = Util::zhantingInfoHidden($orderInfo);
		$this->render('ship_freight_search_list.html',array(
			'bar'=>Auth::getBar($code = '',$list=array(),$short=true),
			'express'=>$express,'data'=>$orderInfo,'order_invoice'=>$order_invoice,
			'region'=>$region,'goods'=>$goodsInfo,'order_action' => $order_action,'pay_type'=>$this->pay_type,'out_order_sn'=>$out_order_sn,'num'=>count($ret),
			'zengpin' => $zengpin,'all_zp'=>$all_zp,'invoice_notice'=> $invoice_notice,'hidden'=>$hidden 
		));
	}

	public function checkGoodsSN(){
		$order_no = _Post::getString('order_sn');
		$goods_sn = _Post::getString('goods_sn');
		
		//$inst = localCache::getInstance();
		//$data = $inst->get('__shipF_ckorder_'.$order_no);
		if (empty($data)) {
			$model = new ShipFreightModel(43);
			$data = $model->getGoodsSNByOrderSN($order_no);
			//$inst->set('__shipF_ckorder_'.$order_no, $data, count($data) >= 20 ? 5400 : 600);
		} 
		//print_r($data);
		echo in_array($goods_sn,$data) ? '1' : '0';
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('ship_freight_info.html',array(
			'view'=>new ShipFreightView(new ShipFreightModel(43))
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('ship_freight_info.html',array(
			'view'=>new ShipFreightView(new ShipFreightModel($id,43)),
			'tab_id'=>$tab_id
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$this->render('ship_freight_show.html',array(
			'view'=>new ShipFreightView(new ShipFreightModel($id,43)),
			'bar'=>Auth::getViewBar()
		));
	}



	/**
	 *	insert，信息入库  新的发货程序 ADD by gaopeng
	 */
	
	public function insert ($params)
	{
	    set_time_limit(0);
		//var_dump($_REQUEST);exit;
        $result = array('success' => 0,'error' =>'','invoiceTip'=>0);
		$olddo = array();
		
		$invoiceTip = _Request::get('invoiceTip');
		
		$newmodel =  new ShipFreightModel(44);        
		$newdo = $newmodel->mkNewdo();
		$newdo['create_id'] = $_SESSION['userId'];
		$newdo['create_name'] = $_SESSION['userName'];
		$newdo['create_time'] = time();
		$newdo['is_deleted'] = 0;
		$newdo['remark'] = '订单发货';
		$newdo['sender'] = '郭伟';
		$newdo['department'] = '物流部';

		//# 在【订单发货】环节设置发票提示：规则是订单金额100元以上且勾选了开票项的，
		//当点【发货】按钮时，如果发票信息未填写，系统提示：“此单还未开发票，
		//请确认是否发货"提示之后，【是】则发货成功，【否】则不发货。

		$order_sn = $newdo['order_no'];
		#检测是否有申请关闭、非已审核、非已付款  有则不能操作
		//$this->VerifyOrderStatus($newdo['order_no']);		
		if(empty($order_sn))
		{
		    $result['error'] = "订单号不能为空";
		    Util::jsonExit($result);
		}
		
		$wareHouseModel = new WarehouseModel(21);
		$salesModel = new SalesModel(27);
		$salesPolicyModel = new SalepolicyModel(17);
		
		$pdo17 = $salesPolicyModel->db()->db();
		$pdo21 = $wareHouseModel->db()->db();
		$pdo27 = $salesModel->db()->db();
		$pdo44 = $newmodel->db()->db();
		
		$pdo17->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
		$pdo17->beginTransaction(); //开启事务
		
		$pdo21->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
		$pdo21->beginTransaction(); //开启事务
		
		$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
		$pdo27->beginTransaction(); //开启事务
		
		$pdo44->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
		$pdo44->beginTransaction(); //开启事务
		//老罗要求暂时注释掉，只需用`apply_return`状态判断
// 		$checkTuikuan = $salesModel->checkOrderTuikuan($order_sn);
// 		if (!$checkTuikuan)
// 		{
// 		    $result['error'] = "此订单有未完成的退款申请，不能操作";
// 		    Util::jsonExit($result);
// 		}


        /*  取消BOSS-997提醒功能
		#如果是独立售卖 如果别一个单子还未审核 不能发货  需二个S都做了审核之后方可操作
		$orderDetailInfo = $salesModel->getOrderDetailsBySn($order_sn);
		$showMsg ='';
		foreach ($orderDetailInfo as $detailv){
		    //托一个单 钻一个单 两单相关联 销托的单时没有提示钻的单还没销 销即销账
		    //托发货的时候没有提示对应的裸钻没有销账，也没有限制，当钻没有销账的时候，托也不可以发货
		    if(strtoupper($detailv['goods_type']) == 'LZ' && !empty($detailv['zhengshuhao'])){//祼钻
		        //找出来对应的托  如果托不在本订单里
		        $tuoInfo = $salesModel->getGoodsInfoByZhengshuhao($detailv['zhengshuhao'],$detailv['order_id']);
		        if($tuoInfo){//托未销，不允发货
		            if(!$wareHouseModel->checkSbillByOrderSn($tuoInfo['order_sn'])){
		                $showMs = "<br/>证书号：".$detailv['zhengshuhao']." 的钻石有空托订单".$tuoInfo['order_sn']." 未销帐，请销帐之后再做发货操作！！！";
		                $result['error'] = $showMs;
		                Util::jsonExit($result);
		            }
		        }
		    }else{
		        preg_match('/^(W|M)\w{4,5}-(\w+)-(\w{1})-(\d+)-(\d{2})$/',$detailv['goods_id'],$matches);
		        if( !empty($matches) && $detailv['zhengshuhao'] && !empty($detailv['zhengshuhao']) )  {//戒托
		            //找出对应的钻
		            $tuoInfo = $salesModel->getGoodsInfoByZhengshuhao($detailv['zhengshuhao'],$detailv['order_id']);
		            if($tuoInfo){//钻未销  不允发货
		                if(!$wareHouseModel->checkSbillByOrderSn($tuoInfo['order_sn'])){
		                    $showMs = "<br/>款号：".$detailv['goods_sn']." 有祼钻订单".$tuoInfo['order_sn']." 未销帐，请销帐之后再做发货操作！！！";
		                    $result['error'] = $showMs;
		                    Util::jsonExit($result);
		                }
		            }
		        }
		    }
		}
		*/
		
		#检测是否有关闭操作 有则不能操作
		$is_close = $salesModel->getOrderInfoBySn($order_sn);
		if(empty($is_close)){
    		$result['error'] = "查询订单明细失败";
    		Util::jsonExit($result);
		}
		if ($is_close['apply_close']==1)
		{
    		$result['error'] = "订单号".$order_sn."审核关闭状态，不能操作";
    		Util::jsonExit($result);
		}
		if ($is_close['apply_return']==2)
		{
		    $result['error'] = "订单号".$order_sn."订单正在退款，不能进行发货操作，请退回库房核实";
		    Util::jsonExit($result);
        }
		if (!($is_close['order_pay_status'] == 3 || $is_close['order_pay_status'] == 4))
		{
		    $result['error'] = "订单号".$order_sn."支付状态不是已付款或财务备案状态，不能操作";
		    Util::jsonExit($result);
		}
		if ($is_close['order_status'] != 2)
		{
		    $result['error'] = "订单号".$order_sn."非已审核状态，不能操作";
		    Util::jsonExit($result);
		}
				
		if($is_close)
		{
			$newdo['channel_id'] =$is_close['department_id']?$is_close['department_id']:0;
			$newdo['out_order_id'] =$is_close['out_order_sn']?$is_close['out_order_sn']:0;
			$newdo['order_mount']=$is_close['order_amount']?$is_close['order_amount']:'0.00';
		}

		if($newdo['consignee'] == ''){
			$result['error'] ="收件人不能为空！";
			Util::jsonExit($result);
		}
		if( empty($newdo['cons_mobile']) && empty($newdo['cons_tel']=='')){
			$result['error'] ="手机/电话 必填一项！";
			Util::jsonExit($result);
		}
		if($newdo['cons_address'] == ''){
			$result['error'] ="收货地址不能为空！";
			Util::jsonExit($result);
		}
		if($newdo['freight_no']=='' && $newdo['express_id'] !=10 )
		{
			$result['error'] ="请选择输入快递号！";
			Util::jsonExit($result);
		}
		if($newdo['express_id']=='')
		{
			$result['error'] ="请在订单系统设置快递方式！";
			Util::jsonExit($result);
		}

		if($newdo['express_id'] != 10 && !empty($newdo['freight_no'])){
			$express_v =  new ExpressView(new ExpressModel($newdo['express_id'],1));
			$rule = $express_v->get_freight_rule();
			if($rule && !preg_match($rule,$newdo['freight_no'])){
				$result['error'] ="快递单号与快递公司不符！";
				Util::jsonExit($result);
			};
		}

		$model =  new ShipParcelModel(44);
		//快递单号 在包裹列表中是否重复($field,$where,$type=1)
		$exists_baoguo = $model->select2("express_sn","express_sn ='{$newdo['freight_no']}' and express_id ={$newdo['express_id']}",$type=2);
		if($exists_baoguo){
			$result['error'] = '包裹列表中已存在快递单号！';
			Util::jsonExit($result);
		}
		

		//不需销账赠品发货
		$is_zp=$newmodel->existsAllZp($order_sn);
		if($is_zp){
			$data = $salesModel->getBaseOrderInfoBySn($order_sn);
			//$data = ApiModel::sales_api(array('order_sn','fields'),array($ordre_sn, " `id`, `order_status` , `send_good_status` , `order_pay_status` "),'GetDeliveryStatus');
			if(!empty($data)){
				$order_id = $data['id'];
				$order_status = $data['order_status'];
				$send_good_status = $data['send_good_status'];
				$order_pay_status = $data['order_pay_status'];
				$time = date('Y-m-d H:i:s');
				$user = $_SESSION['userName'];
				$out_company=$data['out_company'];
				$is_real_invoice=$data['is_real_invoice'];
				$is_xianhuo=$data['is_xianhuo'];
		
		
			}else{
		
				$result['error'] ="订单信息查询失败";
				Util::jsonExit($result);
			}
		
			$res2 = $salesModel->updatOrderGoodsSend($order_sn);
			if (!$res2)
			{
				$pdo17->rollback(); //事务回滚
				$pdo17->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo21->rollback(); //事务回滚
				$pdo21->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo27->rollback(); //事务回滚
				$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo44->rollback(); //事务回滚
				$pdo44->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		
				$result['error'] ="订单发货状态修改失败";
				Util::jsonExit($result);
			}
			$exp_model = new ExpressModel(1);
			$expressName = $exp_model->getNameById($newdo['express_id']);
			$orderLog = array(
					'order_id'=>$order_id,
					'order_status'=>$order_status,
					'shipping_status'=>2,
					'pay_status'=>$order_pay_status,
					'create_user'=>$user,
					'create_time'=>$time,
					'remark'=>'已发货,'.$expressName,
			);
			$res3 = $salesModel->addOrderLog($orderLog);
			if(!$res3){
				$pdo17->rollback(); //事务回滚
				$pdo17->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo21->rollback(); //事务回滚
				$pdo21->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo27->rollback(); //事务回滚
				$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo44->rollback(); //事务回滚
				$pdo44->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		
				$result['error'] ="订单日志写入失败！";
				Util::jsonExit($result);
			}
		
		
			try{
				$pdo17->commit(); //事务提交
				$pdo17->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo21->commit(); //事务提交
				$pdo21->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo27->commit(); //事务提交
				$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo44->commit(); //事务提交
				$pdo44->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			}catch (Exception $e){
				$pdo17->rollback(); //事务回滚
				$pdo17->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo21->rollback(); //事务回滚
				$pdo21->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo27->rollback(); //事务回滚
				$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo44->rollback(); //事务回滚
				$pdo44->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$result['error'] ="系统异常！error code:".__LINE__;
				Util::jsonExit($result);
			}
			$result['success'] = 1;
			Util::jsonExit($result);
			exit;
		}
		
		
		
		/*****************检查货号是否正确 start**********************/
		$all_zp =$newmodel->getzengpinById($newdo['order_no']);
        if(!empty($all_zp)){        	
			//防止不输入货号 点击发货出现报错
			if (isset($_POST['sn_arr']) == false)
			{
				$result['error'] ="请先检验商品货号！";
				Util::jsonExit($result);
			}
			//已输入所有正确的货号
			$sn_check = array_unique(_Post::getList('sn_arr'));
			foreach ($sn_check as $k=>$v)
			{
				$sn_check[$k] = trim($v);
			}
			if(empty($sn_check))
			{
				$result['error'] ="请先检验商品货号！";Util::jsonExit($result);
			}
        }
			//获取已保存的销售单货号；和现在输入的做比对，		
			$goods_ids = $wareHouseModel->getGoodsIdsByOrderSn($order_sn);
			$new_goods_ids = array();
			if ($goods_ids)
			{
				foreach ($goods_ids as $val)
				{
					$new_goods_ids[] = trim($val['goods_id']);
				}
			}
			
			//var_dump($sn_check,8888);var_dump($new_goods_ids);exit;
			$diff = array_diff($new_goods_ids,$sn_check);
			if(!empty($diff))
			{
				$result['error'] ="还有商品未捡验（扫描）,不允许发货！";
				Util::jsonExit($result);
			}
			#验证销售单状态是否是保存状态，前者已经验证，验证货品状态是否是销售中
			//$ret2 = ApiModel::warehouse_api(array('goods_ids') , array(join("','",$new_goods_ids)) , 'check_goods_status');
			$ret2 = $wareHouseModel->checkGoodsIsSale($new_goods_ids);
			//货品符合条件的个数
			if ($ret2 != count($new_goods_ids))
			{
				$result['error'] ="货品状态不正确，一定要是销售中状态";
				Util::jsonExit($result);
			}
        


		/*****************检查货号 end*********************************************/
        /*** 订单发票提醒  begin**********/
        if(SYS_SCOPE=='boss')
            $order_pay_type_limit= json_decode(INVOICE_ORDER_PAY_TYPE_LIMIT,true);  //需要开电子发票的支付方式
        $order_invoice=$salesModel->getOrderInfoForInvoice($order_sn);
	    if(!empty($order_invoice)){
    		$invoiceError = '';
    		if(SYS_SCOPE=='boss'){
	    		if($order_invoice['is_invoice']==1 && $order_invoice['invoice_status']!=2 && $invoiceTip==1 && (!in_array($order_invoice['order_pay_type'],$order_pay_type_limit) || (in_array($order_invoice['order_pay_type'],$order_pay_type_limit) && $order_invoice['invoice_type']==3 ))   ){
	    		    $invoiceError .= "消息提醒：订单{$order_sn}需要手工开发票，但还未开票！";
	    		} 
	    	}	   		    
    		if(SYS_SCOPE=='zhanting'){
	    		if($order_invoice['is_invoice']==1 && $order_invoice['invoice_status']!=2 && $invoiceTip==1){
	    		    $invoiceError .= "消息提醒：订单{$order_sn}需要开发票，但还未开票！";
	    		} 
	    	}    	
    		if($invoiceError){
    		    $pdo17->rollback(); //事务回滚
    		    $pdo17->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    		    $pdo21->rollback(); //事务回滚
    		    $pdo21->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    		    $pdo27->rollback(); //事务回滚
    		    $pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    		    $pdo44->rollback(); //事务回滚
    		    $pdo44->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    		    $result['error'] = $invoiceError.' <br/> 点击确认继续发货！';
    		    $result['invoiceTip'] = $invoiceTip;
    		    Util::jsonExit($result);
    		}
	    }	   
	   /*** 订单发票提醒   end**********/
			
		/***********************发货需要做的操作 start******************************/
		$data = $salesModel->getBaseOrderInfoBySn($order_sn);
		//$data = ApiModel::sales_api(array('order_sn','fields'),array($ordre_sn, " `id`, `order_status` , `send_good_status` , `order_pay_status` "),'GetDeliveryStatus');
		$channel_class = 0;
		if(!empty($data)){
    		$order_id = $data['id'];
    		$order_status = $data['order_status'];
    		$send_good_status = $data['send_good_status'];
    		$order_pay_status = $data['order_pay_status'];
    		$time = date('Y-m-d H:i:s');
    		$user = $_SESSION['userName'];
    		$out_company=$data['out_company'];
    		$is_real_invoice=$data['is_real_invoice'];
    		$is_xianhuo=$data['is_xianhuo'];
    		$channel_class = $data['channel_class'];
    		
		}else{
		    
		    $result['error'] ="订单信息查询失败";
		    Util::jsonExit($result);
		}

		#1、审核销售单据

		//审核销售单 销售单状态  货品状态
		$data = array(
    		'order_sn'=>$order_sn,
    		'goods_ids'=>$new_goods_ids,
    		'user'=>$_SESSION['userName'],
    		'ip'=>Util::getClicentIp(),
    		'time'=>$time
    	);
		$res1 = $wareHouseModel->confirmSale($data);
		//$res= ApiModel::warehouse_api(array('order_sn','goods_ids','user','ip','time') , array($newdo['order_no'],join("','",$new_goods_ids),$_SESSION['userName'],Util::getClicentIp(),$time) , 'checkXiaoshou');
		if (!$res1 && !empty($all_zp))
		{   
		    $pdo17->rollback(); //事务回滚
		    $pdo17->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		    $pdo21->rollback(); //事务回滚
            $pdo21->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交            
            $pdo27->rollback(); //事务回滚
            $pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
            $pdo44->rollback(); //事务回滚
            $pdo44->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
            
			$result['error'] ="销售单审核失败";
			Util::jsonExit($result);
		}
		
		//订单主表（base_order_info) is_real_invoice=0的时候,添加M单、C单、L单，
		//设置S单company_id_from= XXX公司ID(base_order_info.out_company)，company_from = XXX公司名称
		
		/*2016-12-23 暂时去掉A公司功能**/
		/*
		if($is_real_invoice==0 && $out_company != 0 && $is_xianhuo==1){
			$companyModel=new CompanyModel(1);
			$companyName=$companyModel->getCompanyName($out_company);
			$res7=$wareHouseModel->addWarehouseBill($order_sn,$out_company,$companyName);
			/*
			$pdo17->rollback(); //事务回滚
			$pdo17->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			$pdo21->rollback(); //事务回滚
			$pdo21->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			$pdo27->rollback(); //事务回滚
			$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			$pdo44->rollback(); //事务回滚
			$pdo44->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
					
			$result['error'] =$res7;
			Util::jsonExit($result);
			*/
		/*
			if(!$res7){
				$pdo17->rollback(); //事务回滚
				$pdo17->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo21->rollback(); //事务回滚
				$pdo21->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo27->rollback(); //事务回滚
				$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo44->rollback(); //事务回滚
				$pdo44->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
					
				$result['error'] ="生成单据失败";
				Util::jsonExit($result);
			}
			
			
			$res6=$wareHouseModel->updateWarehouseGoods($order_sn,$out_company,$companyName);
			if(!$res6){
				$pdo17->rollback(); //事务回滚
				$pdo17->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo21->rollback(); //事务回滚
				$pdo21->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo27->rollback(); //事务回滚
				$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo44->rollback(); //事务回滚
				$pdo44->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
					
				$result['error'] ="销售S单更新信息失败";
				Util::jsonExit($result);
			}
			
			
		}
		*/
		
		
		
		# 2、修改订单发货状态、订单商品发货状态、回写快递单号
		//$res2 = ApiModel::sales_api(array('order_sn','freight_no'),array($newdo['order_no'],$newdo['freight_no']),'setOrderGoodsSend');
		$data = array(
		    'order_sn'=>$order_sn,
		    'freight_no'=>$newdo['freight_no']
		);
		$res2 = $salesModel->setOrderGoodsSend($data);
		if (!$res2)
		{
		    $pdo17->rollback(); //事务回滚
		    $pdo17->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		    $pdo21->rollback(); //事务回滚
		    $pdo21->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交		    
		    $pdo27->rollback(); //事务回滚
		    $pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		    $pdo44->rollback(); //事务回滚
		    $pdo44->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		    
			$result['error'] ="订单发货状态修改失败";
			Util::jsonExit($result);
		}
		$exp_model = new ExpressModel(1);
        //淘宝自动发货
        $relOrderList = $salesModel->getRelOrderByOrderid($order_id);
		//$relOrderSn = ApiModel::sales_api(array('order_id'),array($order_id),'getRelOrderSnByOrderid');
		if(!empty($relOrderList) && $is_close['department_id'] == 2){
    		    $path=APP_ROOT."shipping/modules/taobaoOrderApi/index.php";
    		    require_once($path);
    		    $tb = new taobaoOrderApi();
    			foreach($relOrderList as $relOrder)
    			{
        			$taobao_order_id = $relOrder['out_order_sn'];
          			$tb->LogisticsOnlineSendRequest($taobao_order_id, $newdo['freight_no'], $newdo['express_id']);
       				// $tb -> LogisticsOnlineConfirmRequest($taobao_order_id, $newdo['freight_no']);
    			}
				$expressName = $exp_model->getNameById($newdo['express_id']);
    			$orderLog = array(
    			    'order_id'=>$order_id,
    			    'order_status'=>$order_status,
    			    'shipping_status'=>2,
    			    'pay_status'=>$order_pay_status,
    			    'create_user'=>$user,
    			    'create_time'=>$time,
					'remark'=>"已发货;{$expressName}:{$newdo['freight_no']}",//2015-10-10  boss-189
    			   // 'remark'=>"外部订单[{$taobao_order_id}]:填写快递号为{$newdo['freight_no']}",
    			);
    			$res3 = $salesModel->addOrderLog($orderLog);
				/* ApiModel::sales_api(	//回写订单日志
            				array('order_no','create_user','remark'),
            				array($ordre_sn ,$user , "外部订单[$taobao_order_id]:填写快递号为$newdo[freight_no]") ,
            			'AddOrderLog'); */
		}else{		
            //订单日志添加上快递和快递单号
            
            $expressName = $exp_model->getNameById($newdo['express_id']);
            $orderLog = array(
                'order_id'=>$order_id,
                'order_status'=>$order_status,
                'shipping_status'=>2,
                'pay_status'=>$order_pay_status,
                'create_user'=>$user,
                'create_time'=>$time,
    		    'remark'=>'已发货,'.$expressName.':'.$newdo['freight_no'],
             );
            $res3 = $salesModel->addOrderLog($orderLog); 
		}

        $res33 = true;
        if($channel_class<>1)
        {
            $pointModel = new SelfModel(27);
            $res33 = $pointModel->update_order_point($order_id);
        }

		//$res3 订单日志写入结果
		if(!$res3 || !$res33){
		    $pdo17->rollback(); //事务回滚
		    $pdo17->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		    $pdo21->rollback(); //事务回滚
		    $pdo21->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		    $pdo27->rollback(); //事务回滚
		    $pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		    $pdo44->rollback(); //事务回滚
		    $pdo44->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		    
		    $result['error'] ="订单日志写入/累加积分失败！";
		    Util::jsonExit($result);
		}





		//#3、添加快递记录
		//edit by zhangruiying 上门取货不需要记录快递单号(因为前端下单可能会不默认上门取货，所以也加0的判断)
		if($newdo['express_id'] != 10 && $newdo['express_id'] != 0)
		{
			$res4 = $newmodel->saveData($newdo,$olddo);
			if (!$res4)
			{
			    $pdo17->rollback(); //事务回滚
			    $pdo17->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			    $pdo21->rollback(); //事务回滚
			    $pdo21->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			    $pdo27->rollback(); //事务回滚
			    $pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			    $pdo44->rollback(); //事务回滚
			    $pdo44->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交

				$result['error'] ="快递记录添加失败";
				Util::jsonExit($result);
			}
		}


        if(SYS_SCOPE=='boss'){
		        //开具电子发票
		        $order_id='0';
		        $invoice_amount='';
		        $invoice_title='个人';
		        $invoice_content='';
		        $invoice_num ='';
		        $order_invoice=$salesModel->getOrderInfoForInvoice($order_sn);
			    if(!empty($order_invoice)){
		    		$invoiceError = '';
		    		$invoice_amount = $order_invoice['invoice_amount'];
		    		$invoice_title  = $order_invoice['invoice_title'];
		    		$invoice_content = $order_invoice['invoice_content'];
		    		$order_id = $order_invoice['order_id'];    		
		    		if($order_invoice['is_invoice']==1 && $order_invoice['invoice_status']!=2 && in_array($order_invoice['order_pay_type'],$order_pay_type_limit) && $order_invoice['invoice_type']!=3){
					    if(bccomp($order_invoice['invoice_amount'],$order_invoice['order_amount'],2)==1){
                                $invoiceError = '开票金额大于订单金额,请核实';
					    }else{
						        include_once(APP_ROOT."shipping/modules/invoice_api/invoice.php");
						        include_once(APP_ROOT."shipping/modules/invoice_api/DESDZFP.class.php");
						        //$order_invoice = $salesModel->getOrderInfoForInvoice($order_sn);  
					            $inv_res = Invoice::makeOrder($order_invoice);
					            if(is_array($inv_res) && $inv_res['status']=='0000' && $inv_res['message']=='同步成功'){
			                        $invoice_num = $inv_res['fpqqlsh'];
					            }else{
			                        if(!empty($inv_res['status']) && !empty($inv_res['message']) && $inv_res['status']=='9106' && $inv_res['message']=='订单编号不能重复'){
			                        	$inv_res = Invoice::searchOrder($order_sn);
			                        	if(!empty($inv_res['result']) && $inv_res['result']=='success' && !empty($inv_res['list'][0]['c_fpqqlsh']))
			                                $invoice_num = $inv_res['list'][0]['c_fpqqlsh'];
			                            else
			                            	$invoiceError ='订单发票已存在,接口查询订单发票失败';
			                        }else{
			                        	if(empty($inv_res['message']))
			                        		$invoiceError = '发票接口异常';
			                        	else
			                        		$invoiceError = $inv_res['message'];
			                        }                    
					            }
				        }        		    
		    		}    		    
		    	
		    		if(!empty($invoiceError)){
		    		    $pdo17->rollback(); //事务回滚
		    		    $pdo17->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		    		    $pdo21->rollback(); //事务回滚
		    		    $pdo21->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		    		    $pdo27->rollback(); //事务回滚
		    		    $pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		    		    $pdo44->rollback(); //事务回滚
		    		    $pdo44->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		    		    $result['error'] = $invoiceError;
		    		    //$result['invoiceTip'] = $invoiceTip;
		    		    Util::jsonExit($result);
		    		}

		    		if(!empty($invoice_num)){
		                $update_invoice_data = array(
		                	                   'order_id'    => $order_id,
		                	                   'invoice_num' => $invoice_num,
		                	                   'invoice_type' => 2,
		                	                   );
		                $res1=$salesModel->update_app_order_invoice($update_invoice_data);

		                $add_base_invoice_info_data = array(
		                                        'invoice_num' => $invoice_num,
		                                        'price' => $invoice_amount,
		                                        'title' => $invoice_title,
		                                        'content' => $invoice_content,
		                                        'status' => 2,
		                                        'create_user' => $_SESSION['userName'],                                        
		                                        'create_time' => date('Y-m-d H:i:s'),
		                                        'use_user' => $_SESSION['userName'],
		                                        'use_time' => date('Y-m-d H:i:s'),
		                                        'order_sn' => $order_sn,
		                                        'type' =>1,  
		                	                    );
		                $res2=$salesModel->add_base_invoice_info($add_base_invoice_info_data);
			            $orderLog = array(
			                'order_id'=>$order_id,
			                'order_status'=>$order_status,
			                'shipping_status'=>2,
			                'pay_status'=>$order_pay_status,
			                'create_user'=>$_SESSION['userName'],
			                'create_time'=>date('Y-m-d H:i:s'),
			    		    'remark'=>'已开电子发票并回写订单信息,发票编号:'.$invoice_num,
			            );
			            $res3 = $salesModel->addOrderLog($orderLog);


		                if( !$res1 || !$res2 || !$res3){
			    		    $pdo17->rollback(); //事务回滚
			    		    $pdo17->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			    		    $pdo21->rollback(); //事务回滚
			    		    $pdo21->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			    		    $pdo27->rollback(); //事务回滚
			    		    $pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			    		    $pdo44->rollback(); //事务回滚
			    		    $pdo44->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			    		    $result['error'] = '回写订单发票失败';	    		    
			    		    Util::jsonExit($result);
		                }

		    		}
			    }
		        //开具电子发票 end
        }



		//防止事物提交时发生错误
		try{     
		    $pdo17->commit(); //事务提交
		    $pdo17->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    		$pdo21->commit(); //事务提交
    		$pdo21->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    		$pdo27->commit(); //事务提交
    		$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    		$pdo44->commit(); //事务提交
    		$pdo44->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		}catch (Exception $e){
		    $pdo17->rollback(); //事务回滚
		    $pdo17->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		    $pdo21->rollback(); //事务回滚
		    $pdo21->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		    $pdo27->rollback(); //事务回滚
		    $pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		    $pdo44->rollback(); //事务回滚
		    $pdo44->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		    $result['error'] ="系统异常！error code:".__LINE__;
		    Util::jsonExit($result);
		}
		if(!empty($invoice_num))
            $result['invoice_num'] = $invoice_num; 
		$result['success'] = 1;
	    //AsyncDelegate::dispatch('warehouse', array('event'=>'bill_S_checked', 'order_sn' => $order_sn));
		Util::jsonExit($result);
		/***********************发货需要做的操作 end  ******************************/
	}

	/**
	 *	insert，信息入库
	 */
	/*  旧的发货程序可以废除
	public function insert ($params)
	{
		//var_dump($_REQUEST);exit;
        $result = array('success' => 0,'error' =>'');
		$olddo = array();

		$newmodel =  new ShipFreightModel(44);

		$newdo = $newmodel->mkNewdo();
		$newdo['create_id'] = $_SESSION['userId'];
		$newdo['create_name'] = $_SESSION['userName'];
		$newdo['create_time'] = time();
		$newdo['is_deleted'] = 0;
		$newdo['remark'] = '订单发货';
		$newdo['sender'] = '郭伟';
		$newdo['department'] = '物流部';

		//# 在【订单发货】环节设置发票提示：规则是订单金额100元以上且勾选了开票项的，
		//当点【发货】按钮时，如果发票信息未填写，系统提示：“此单还未开发票，
		//请确认是否发货"提示之后，【是】则发货成功，【否】则不发货。


		#检测是否有申请关闭、非已审核、非已付款  有则不能操作
		$this->VerifyOrderStatus($newdo['order_no']);

		$is_close = ApiModel::sales_api(array('order_sn'),array($newdo['order_no']),'GetOrderInfoBySn');
		//var_dump($is_close['department_id']);
		if($is_close)
		{
			$newdo['channel_id'] =$is_close['department_id']?$is_close['department_id']:0;
			$newdo['out_order_id'] =$is_close['out_order_sn']?$is_close['out_order_sn']:0;
			$newdo['order_mount']=$is_close['order_amount']?$is_close['order_amount']:'0.00';
		}
		//var_dump($newdo);exit;
	//var_dump($is_close,$newdo['channel_id']);
		if($newdo['consignee'] == ''){
			$result['error'] ="收件人不能为空！";
			Util::jsonExit($result);
		}
		if(($newdo['cons_mobile'] =='') && ($newdo['cons_tel']=='')){
			$result['error'] ="手机/电话 必填一项！";
			Util::jsonExit($result);
		}
		if($newdo['cons_address'] == ''){
			$result['error'] ="收货地址不能为空！";
			Util::jsonExit($result);
		}
		if($newdo['freight_no']=='' && $newdo['express_id'] !=10 )
		{
			$result['error'] ="请选择输入快递号！";
			Util::jsonExit($result);
		}
		if($newdo['express_id']=='')
		{
			$result['error'] ="请在订单系统设置快递方式！";
			Util::jsonExit($result);
		}



		if($newdo['express_id'] != 10 && !empty($newdo['freight_no'])){
			$express_v =  new ExpressView(new ExpressModel($newdo['express_id'],1));
			$rule = $express_v->get_freight_rule();
			if($rule && !preg_match($rule,$newdo['freight_no'])){
				$result['error'] ="快递单号与快递公司不符！";
				Util::jsonExit($result);
			};
		}

		$model =  new ShipParcelModel(44);
		//快递单号 在包裹列表中是否重复($field,$where,$type=1)
		$exists_baoguo = $model->select2("express_sn","express_sn ='{$newdo['freight_no']}' and express_id ={$newdo['express_id']}",$type=2);
		if($exists_baoguo){
			$result['error'] = '包裹列表中已存在快递单号！';
			Util::jsonExit($result);
		}
		////////////////检查货号是否正确 start/////////////////////
		//防止不输入货号 点击发货出现报错
		if (isset($_POST['sn_arr']) == false)
		{
			$result['error'] ="请先检验商品货号！";
			Util::jsonExit($result);
		}
		//已输入所有正确的货号
		$sn_check = array_unique(_Post::getList('sn_arr'));
		foreach ($sn_check as $k=>$v)
		{
			$sn_check[$k] = trim($v);
		}
		if(empty($sn_check))
		{
			$result['error'] ="请先检验商品货号！";Util::jsonExit($result);
		}
		//获取已保存的销售单货号；和现在输入的做比对，
		$goods_ids = ApiModel::warehouse_api(array('order_sn'),array($newdo['order_no']),'GetGoodsIdsByOrderSN');
		$new_goods_ids = array();

		if ($goods_ids)
		{
			foreach ($goods_ids as $val)
			{
				$new_goods_ids[] = trim($val['goods_id']);
			}
		}
		//var_dump($sn_check,8888);var_dump($new_goods_ids);exit;
		$diff = array_diff($new_goods_ids,$sn_check);
		if(!empty($diff))
		{
			$result['error'] ="还有商品未捡验（扫描）,不允许发货！";
			Util::jsonExit($result);
		}
		#验证销售单状态是否是保存状态，前者已经验证，验证货品状态是否是销售中
		$ret2 = ApiModel::warehouse_api(array('goods_ids') , array(join("','",$new_goods_ids)) , 'check_goods_status');

		//货品符合条件的个数
		if ($ret2 != count($new_goods_ids))
		{
			$result['error'] ="货品状态不正确，一定要是销售中状态";
			Util::jsonExit($result);
		}

		//////////////////检查货号 end//////////////////////////

		/////////////////////////发货需要做的操作 start////////////////
		//回写订单日志
		$ordre_sn = trim($params['order_no']);
		$data = ApiModel::sales_api(array('order_sn','fields'),array($ordre_sn, " `id`, `order_status` , `send_good_status` , `order_pay_status` "),'GetDeliveryStatus');
		$order_id = $data['id'];
		$order_status = $data['order_status'];
		$send_good_status = $data['send_good_status'];
		$order_pay_status = $data['order_pay_status'];
		$shipping_status = $data['shipping_status'];
		$time = date('Y-m-d H:i:s');
		$user = $_SESSION['userName'];

		#1、审核销售单据

		//审核销售单 销售单状态  货品状态
		$res= ApiModel::warehouse_api(array('order_sn','goods_ids','user','ip','time') , array($newdo['order_no'],join("','",$new_goods_ids),$_SESSION['userName'],Util::getClicentIp(),$time) , 'checkXiaoshou');
		if ($res != 1)
		{
			$result['error'] ="销售单审核失败";
			Util::jsonExit($result);
		}
		# 2、修改订单发货状态、订单商品发货状态、回写快递单号
		$res2 = ApiModel::sales_api(array('order_sn','freight_no'),array($newdo['order_no'],$newdo['freight_no']),'setOrderGoodsSend');
		if (!$res2)
		{
			$result['error'] ="订单发货状态修改失败";
			Util::jsonExit($result);
		}
        	//淘宝自动发货
		if(!empty($order_id)){//防止0订单id找到所有外部订单
        		$relOrderSn = ApiModel::sales_api(array('order_id'),array($order_id),'getRelOrderSnByOrderid');
        		if(!empty($relOrderSn) && $is_close['department_id'] == 2){
            			foreach($relOrderSn as $relOrder){
                			$taobao_order_id = $relOrder['out_order_sn'];
                			$path=APP_ROOT."shipping/modules/taobaoOrderApi/index.php";
                			require_once($path);
                			$tb = new taobaoOrderApi();
                			$tb -> LogisticsOnlineSendRequest($taobao_order_id, $newdo['freight_no'], $newdo['express_id']);
               				// $tb -> LogisticsOnlineConfirmRequest($taobao_order_id, $newdo['freight_no']);

            			}
						ApiModel::sales_api(	//回写订单日志
                    				array('order_no','create_user','remark'),
                    				array($ordre_sn ,$user , "外部订单[$taobao_order_id]:填写快递号为$newdo[freight_no]") ,
                    			'AddOrderLog');
        		}
		}
                //订单日志添加上快递和快递单号
                $exp_model = new ExpressModel(1);
                $expressName = $exp_model->getNameById($newdo['express_id']);

		ApiModel::sales_api(	//回写订单日志
			array('order_no','create_user','remark'),
			array($ordre_sn ,$user , '已发货,'.$expressName.':'.$newdo['freight_no']) ,
			'AddOrderLog');
        //把发货时间回写给订单 add liuri by 20150604
        ApiModel::sales_api(array('order_id','update_fileds'), array($order_id,array('send_goods_time'=>date("Y-m-d H:i:s"))), 'updateOrderArr');
        //end
		#销售货品下架
		$res3 = ApiModel::salepolicy_api(
				array('goods_id', 'is_sale' , 'is_valid'),
				array($new_goods_ids, 0 , 2),
				'EditIsSaleStatus');
		if (!$res3)
		{
			$result['error'] ="订单销售货品下架失败";
			Util::jsonExit($result);
		}
		//var_dump($newdo);exit;
		#3、添加快递记录
		//edit by zhangruiying 上门取货不需要记录快递单号(因为前端下单可能会不默认上门取货，所以也加0的判断)
		if($newdo['express_id'] != 10 && $newdo['express_id'] != 0)
		{
			$res4 = $newmodel->saveData($newdo,$olddo);
			if (!$res4)
			{
				$result['error'] ="快递记录添加失败";
				Util::jsonExit($result);
			}
		}
		$result['success'] = 1;
		Util::jsonExit($result);
		///////////////////发货需要做的操作 end  //////////////////////
	}

	*/

	/* 打印快递单 */
	public function print_express($params)
	{
		$order_no = $params['order_no'];

        $SalesModel = new SalesModel(27);
        $res = $SalesModel->getOrderInfoBySn($order_no);
        if(empty($res)){
        	    echo "获取订单信息失败";exit;		       
        }
        $out_order_sn =$res['out_order_sn'];		
		//添加快递补寄 提前发货 url参数
		$express_id = isset($params['express_id'])?$params['express_id']:'';
		$express_order_id = isset($params['express_order_id'])?$params['express_order_id']:'';
		$model = new ShipFreightModel(43);
		$data = $model->getOrderDetailsId($order_no);
		$order = array_merge($data[0],$data[1]);
        //如果订单已发货，姓名电话地址取补发的
        if($order['send_good_status'] == 2 && !empty($order['tel2'])){        	
	            $order['consignee'] = $order['consignee2'];
	            $order['tel'] = $order['tel2'];
	            $order['address'] = $order['address2'];          
        }
		
		
		//若通过补寄过来的  快递方式不取数据表
		if($express_id){
			$order['express_id'] =$express_id;
		}
		/* 选择打印快递 */
		$shipping_p	=	isset($_REQUEST['print_ship']) && intval($_REQUEST['print_ship'])>0 ? intval($_REQUEST['print_ship']) : $order['express_id'];


		if(empty($order['express_id']))
		{
			echo "该订单没有选择快递公司";exit;
		}

		//测试顺丰 不是货到付款  1是货到付款
		//$shipping_p = $order['express_id'] = 19;
		// $order['p'] = 2;

		$expressmodel = new ExpressModel($shipping_p,1);
		$shipping = $expressmodel->getDataObject();
		if(empty($shipping))
		{
			echo "此快递方式不存在";exit;
		}


		if ($shipping['id'] == '10') {
			echo '此订单为上门取货，无需打印订单。';
			exit;
		}

        $file_path = APP_ROOT."shipping/modules/express_api/Express_api.php";
        require_once($file_path);
        $j_contact = EXPRESS_J_CONTACT;
        $j_tel = EXPRESS_J_TEL;
        if(!empty($order['order_pay_type']) && $order['order_pay_type']=='351'){
	        $j_contact = EXPRESS_J_COMPANY;
	        $j_tel = EXPRESS_J_TEL_AIKUCUN;  
	        $order['source_name'] = '';      	
        }

        if($express_id==4 || $express_id==18){
            $order['consignee'] =  Util::hidden_name($order['consignee']);    
            $order['tel'] =  Util::hidden_tel($order['tel']);   
        } 
        if(!empty($express_order_id)){
        	$res=Express_api::searchOrder($express_order_id,$shipping['id'],$out_order_sn);

        	if($res['result']==1){
				$this->render('mod_print_api.html',array(
					'shipping'=>$shipping,
					'express_id'=>$express_id,
					'express'=>$res,
					'order'=>$order,
				    'j_contact' => $j_contact,
                    'j_tel' => $j_tel,
				));
			}else	
			    exit($res['error']);      

			exit();      	
        }

		//快递单大小
		$shipping['print_bg_size'] = array('width' => '1024', 'height' => '600');

		$regModel = new RegionModel(1);
		/* 收件人省份信息 */
		$customer_province = $regModel->getReginName($order['province_id']);
		/* 收件人市信息 */
		$customer_city = $regModel->getReginName($order['city_id']);

			/* 标签信息 */
		$lable_box = array();
		$lable_box['t_order_amount'] = '';
		$lable_box['t_shop_country'] = '中国'; //网店-国家
		$lable_box['t_shop_city'] = '深圳市'; //网店-城市
		$lable_box['t_shop_province'] = '广东省'; //网店-省份
		$lable_box['t_shop_name'] = 'BDD'; //网店-名称
		$lable_box['t_shop_district'] = ''; //网店-区/县
		$lable_box['t_shop_tel'] = '4008980188'; //网店-联系电话
		if (time() >= strtotime('2017-01-25')) {
		  $lable_box['t_shop_address'] = '广东省深圳市龙岗区南湾街道布澜路31号李朗国际产业园B1栋东3层'; //网店-地址		
		} else {
		  $lable_box['t_shop_address'] = '广东省深圳市龙岗区南湾街道布澜路31号李朗国际产业园B8栋10楼'; //网店-地址
		}
		$lable_box['t_customer_country'] = '中国'; //收件人-国家
		$lable_box['t_customer_province'] = $customer_province; //收件人-省份
		$lable_box['t_customer_city'] = "<b>".$customer_city."</b>"; //收件人-城市
		$lable_box['t_customer_city_big'] ="<b style=\"font-family: 微软雅黑;  font-size:30px\" >".$customer_city."</b>"; //收件人-城市
		$lable_box['t_customer_province_big'] ="<b style=\"font-family: 微软雅黑;  font-size:30px\">".$customer_province."</b>"; //收件人-省份

		$customer_district = '';
		if($order['regional_id'] > 0)
		{
			$customer_district = $regModel->getReginName($order['regional_id']);
		}
		$customer_district = str_replace("\n", '' , $customer_district);		//提出信息中的回车，回车会导致打印的JS报错，打印不出发货单

		$lable_box['t_customer_district'] = "<b>".$customer_district."</b>"; //收件人-区/县
		$lable_box['t_customer_tel'] = $order['tel']; //收件人-电话
		//相同的电话不必要重复--2015-11-10
		$lable_box['t_customer_mobel'] = "";
		//$lable_box['t_customer_mobel'] = !empty($order['tel']) ? $order['tel'] : $order['tel']; //收件人-手机
		$lable_box['t_customer_post'] = $order['zipcode']; //收件人-邮编
       
        //如果订单已发货，姓名电话地址取补发的
        if($order['send_good_status'] == 2 && !empty($order['tel2'])){        	
	            $customer_province="";
	            $customer_city="";
	            $customer_district="";         
        }
		$lable_box['t_customer_address'] = $customer_province." ".$customer_city." ".$customer_district." ".trim(str_replace(","," ",$order['address'])); //收件人-详细地址
		$lable_box['t_customer_name'] = $order['consignee']; //收件人-姓名

		$gmtime_utc_temp = time(); //获取 UTC 时间戳
		$lable_box['t_year'] = date('Y', $gmtime_utc_temp); //年-当日日期
		$lable_box['t_months'] = date('m', $gmtime_utc_temp); //月-当日日期
		$lable_box['t_day'] = date('d', $gmtime_utc_temp); //日-当日日期

		$lable_box['t_order_no'] =$order['order_sn']; //订单号-订单
		$lable_box['t_order_best_time'] = ''; //送货时间-订单
		$lable_box['t_pigeon'] = '√'; //√-对号
		$lable_box['t_duigou'] = '√'; //√-对号
		//邮政 并且 货到付款
		//$lable_box['t_chahao'] = ($order["express_id"] == 9 && $order["pay_id"] != 1) ? "" : '×'; //×-号
		$lable_box['t_chahao'] = '×'; //×-号
		$lable_box['t_ems_dagou'] = '×'; //×-号
		$lable_box['t_custom_content'] = '太白营销部';
		$lable_box['lanjian'] = '755026'; //自定义内容

		if($shipping_p == 4){
			$lable_box['t_remark'] = '转寄协议客户，必须本人签收！';
		}else{
			$lable_box['t_remark'] = '务必本人签收<br />  请当快递面拆件验货！';
		}

		$lable_box['t_sf_signature'] = '郭伟'; //顺风寄件人签署
		// $lable_box['t_zt_qz'] = '008'; //中通签章
		$lable_box['t_zt_qz'] = '郭伟';
        $lable_box['t_z_ems'] = "";
		$lable_box['t_z_zto'] = "<img src=http://order.kela.cn/images/receipt/z_zto.png />"; //ZTO签章
		//$lable_box['t_express_no'] = $order['invoice_no'];
		$lable_box['t_ems_bx'] = '0.1%'; //EMS保险费率

		$t_order_amount = intval($order['order_amount']);
		$cn_arr = $this->money_to_cn($t_order_amount);
		//var_dump($cn_arr);exit;

		$lable_box['t_goods_name'] = '工艺品';
		$lable_box['t_sf_work_code'] = "0 6 4 4 8 6";//员工编号
		$sf_card = "7556559853";
		$lable_box['t_send_company'] = "BDD";
		$lable_box['t_send_user'] = '郭伟';
		$lable_box['t_sf_c_code'] = $lable_box['t_sf_y_code'] = $sf_card;

		/* 代收款快递单设置 */
		$lable_box['t_zt_cod'] = '';
		if($order['order_pay_type']==2)//货到付款
		{
			$lable_box['t_order_amount'] = $t_order_amount."元";
			$lable_box['t_zt_cod'] = "<strong>代收款".$t_order_amount."元</strong>";

			//顺风
			if($order['express_id'] == 4)
			{
				$lable_box['t_pigeon'] = '√'; //√-对号(勾选货到付款)
				$lable_box['t_sf_card'] = '7556559853'; //顺风代收款卡号
			}
		}
		else
		{
			//顺风
			if($order['express_id'] == 4)
			{
				$lable_box['t_pigeon'] = ''; //√-对号(不勾选货到付款)
				$lable_box['t_sf_card'] = ''; //顺风代收款卡号
			}
		}
		/* 订单总金额 */
		$order_total_money = intval($order['order_amount'] + $order['money_paid']);

		if($order['express_id']==9 && $order['order_pay_type'] != 2)
		{
			$lable_box['t_order_amount'] = $order_total_money."元";
		}

		//获取客户来源 2015-11-10 by lyy
		// BOSS-724 中通快递\顺丰速运\圆通速递\韵达快递,增加一项内容[客户来源]
		if(in_array($order['express_id'],array(4,12,19,40)))
		{
			$modelsl =  new SalesModel(27);
			$csn = $modelsl->getOrderSourceName($order_no);
			$lable_box['t_csn'] = $csn;			
		}
		// 获取客户来源结束
///////////////////////////////////////////////////////////////////////////
		
		

		// $lable_box['t_order_amount'] = $order_total_money."元";
		$lable_box['t_money_w'] = $cn_arr['w_cn'];
		$lable_box['t_money_q'] = $cn_arr['q_cn'];
		$lable_box['t_money_b'] = $cn_arr['b_cn'];
		$lable_box['t_money_s'] = $cn_arr['s_cn'];
		$lable_box['t_money_g'] = $cn_arr['g_cn'];



		//标签替换
		if($order['order_pay_type'] != 2)
		{
			if($order['express_id']==9 && !empty($shipping['config_online']))
			{
				$temp_config_lable = explode('||,||', $shipping['config_online']);
			}
			else
			{
				$temp_config_lable = explode('||,||', $shipping['config_lable']);
			}
		}
		else
		{
			if($order['province_id'] == 20 && $order['express_id'] == 9 && $shipping_p != 21)
			{
				$shipping['config_lable'] = "t_shop_name,网店-名称,106,32,74,114,b_shop_name||,||t_shop_tel,网店-联系电话,114,22,214,118,b_shop_tel||,||t_customer_name,收件人-姓名,81,31,306,273,b_customer_name||,||t_customer_mobel,收件人-手机,128,34,245,368,b_customer_mobel||,||t_customer_post,收件人-邮编,88,21,250,343,b_customer_post||,||t_customer_address,收件人-详细地址,285,63,91,303,b_customer_address||,||t_shop_address,网店-地址,260,62,60,154,b_shop_address||,||t_order_no,订单号-订单,129,32,119,260,b_order_no||,||t_order_amount,订单金额,71,31,479,171,b_order_amount||,||t_goods_name,商品名称,70,25,89,417,b_goods_name||,||t_money_w,大写金额(万),80,30,357,154,b_money_w||,||t_money_q,大写金额(仟),80,30,389,154,b_money_q||,||t_money_b,大写金额(佰),80,30,418,155,b_money_b||,||t_money_s,大写金额(拾),80,30,446,155,b_money_s||,||t_money_g,大写金额(个),73,30,477,156,b_money_g||,||t_sf_signature,顺丰寄件人签署,72,40,686,298,b_sf_signature||,||t_chahao,×-号,50,25,237,363,b_pigeon||,||t_z_ems,EMS签章,150,50,385,447,b_z_ems||,||t_remark,快递备注,163,39,599,467,b_remark||,||t_customer_tel,收件人-电话,137,34,90,368,b_customer_tel||,||t_months,月-当日日期,64,28,602,342,b_months||,||t_day,日-当日日期,55,27,653,343,b_day||,||";
			}

			$temp_config_lable = explode('||,||', $shipping['config_lable']);
		}

		if (!is_array($temp_config_lable))
		{
			$temp_config_lable[] = $shipping['config_lable'];
		}
		foreach ($temp_config_lable as $temp_key => $temp_lable)
		{
			if(empty($temp_lable))
			{
				continue;
			}
			$temp_info = explode(',', $temp_lable);
			if (is_array($temp_info))
			{
				$temp_info[1] = $lable_box[$temp_info[0]];
			}
			$temp_config_lable[$temp_key] = implode(',', $temp_info);
		}

		$shipping['config_lable'] = implode('||,||',  $temp_config_lable);

		//拼接订单总金额  支付方式待定
		// 若 支付方式为 货到付款 则显示金额
		// $pay_type = $model->GetDeliveryStatus( $order_no , $fields = " `order_pay_type` " );
		/*if($pay_type['order_pay_type'] == 2){
			$shipping['config_lable'].= "money,".$lable_box['t_order_amount']."||,||";
		}*/
		$this->render('mod_print.html',array(
			'shipping'=>$shipping,
			'express_id'=>$express_id,			
		));
	}

	/************************* ****
	****订单金额转中文大写
	*******************************/
	function money_to_cn($money)
	{
		$cn_arr = array('0'=>'零','1'=>'壹','2'=>'贰','3'=>'叁','4'=>'肆','5'=>'伍','6'=>'陆','7'=>'柒','8'=>'捌','9'=>'玖','10'=>'拾');
		$arr = array();
		$w_length = strlen($money)-4;
		$t_money_w = substr($money,0,$w_length);
		$t_money_q = substr($money,-4,1);
		$t_money_b = substr($money,-3,1);
		$t_money_s = substr($money,-2,1);
		$t_money_g = substr($money,-1);

		$arr['q_cn'] = $cn_arr[$t_money_q];
		$arr['b_cn'] = $cn_arr[$t_money_b];
		$arr['s_cn'] = $cn_arr[$t_money_s];
		$arr['g_cn'] = $cn_arr[$t_money_g];

		$w_str = "";
		if($w_length>0)
		{
			$w_q = substr($t_money_w,-4,1);
			$w_b = substr($t_money_w,-3,1);
			$w_s = substr($t_money_w,-2,1);
			$w_g = substr($t_money_w,-1,1);

			if($w_g>0)
			{
				$w_str = $cn_arr[$w_g];
			}
			if($w_s>0)
			{
				$w_str = $cn_arr[$w_s]."拾".$w_str;
			}
			if($w_b>0)
			{
				$w_str = $cn_arr[$w_b]."佰".$w_str;
			}
			if($w_q>0)
			{
				$w_str = $cn_arr[$w_q]."仟".$w_str;
			}
		}
		else
		{
			$w_str = '零';
		}

		$arr['w_cn'] = $w_str;
		return $arr;
	}
        // add order action log list
        function showLogs() {
            $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            '_id' => _Request::get("id"),
            );
             $shipping_status = array(0 => '未发货', 1 => '已发货', 2 => '已收货', 3 => '允许发货', 4 => '已到店');

            $page = _Request::getInt("page", 1);
            $where = array();
            //$where['_id'] = $args['_id'];
            $order_no = $args['_id'];
            $model = new ShipFreightModel(43);
            $data = $model->getOrderActionLogList();
            $pageData = $data;
            $pageData['filter'] = $args;
            $pageData['jsFuncs'] = 'ship_order_action_search_page';
            $this->render('ship_order_action_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data,
            'shipping_status' => $shipping_status,
        ));
        }

        //修改快递方式 +  展示模版
	public function updateShipMethod($params){
		$result = array('success' => 0,'error' => '');
		$order_sn = $params['order_sn'];

		//判断快递是否发货，发货了不能在这里修改快递方式
		$send_status = isset($params['send_status']) ? $params['send_status'] : 0;
		if($send_status == 3 || $send_status == 5 ){
			$result['content'] = '已收货和已到店，不能再此处修改快递方式';
			Util::jsonExit($result);
		}

		//取得快递公司信息
		$ex_model		= new ExpressModel(1);
		$info_express   = $ex_model->getAllExpress();
		$express_arr = array();
		foreach($info_express as $val){
			$express_arr[$val['id']] = $val['exp_name'];
		}

		/** 修改快递方式 逻辑实现代码 **/
		if(isset($params['a']) && !empty($params['a'])){
			$express_id = $params['express_id'];
			$model = new ShipFreightModel(43);
			
			// 2015-11-03 fix by lyy  boss629
			// 5、物流管理-快件管理-订单发货，点击【添加快递】【修改快递方式】，保存时需要判断：当订单销售渠道为【B2C销售部】且来源选择【中国移动积分】或者销售渠道为【银行销售部】且来源选择【交通银行】，快递物流允许选择【圆通速递】，其他的情况不允许保存，需提示“订单销售渠道和来源不支持圆通速递，请选择其他的快递物流”
			$order_id =  _Request::get('order_id');
			$arr1 = array('order_sn'=>$order_sn , 'express_id'=> $express_id,'freight_no'=>$params['freight_no'], 'order_id' => $order_id);
			
			if ($params['a'] == 'quikdistrib') {
				$orderModel = new SalesModel(27);
				$order = $orderModel->getOrderInfoById($order_id);

				$params['order_status'] = $order['order_status'];
				$params['order_pay_status'] = $order['order_pay_status'];
			
				//$order_address = $orderModel->getAddressByOrderSn($order_sn);
				$order_address = $model->select2('express_id, freight_no', "order_no='{$order_sn}'");
				if ($order_address) {
					$params['old_express_id'] = $order_address['express_id'];
					$params['old_freight_no'] = $order_address['freight_no'];
				} else {
					return $this->insert_ship($params);
				}
			}
			
			$this -> checkExpPermit($order_id,$express_id);
			
			$arr2 = array(
				'order_id' =>$params['order_id'],
				'order_status'=>$params['order_status'] ,
				'send_good_status'=>$params['send_good_status'] ,
				'order_pay_status'=>$params['order_pay_status'] ,
				'old_express_id'=>$params['old_express_id'] ,
				'time'=>date('Y-m-d H:i:s') ,
				'user'=>$_SESSION['userName'] ,
				'remark'=>'',
			);
			$str='';
			if($params['old_express_id']!=$express_id)
			{
				$old_kd =isset($express_arr[$params['old_express_id']])?$express_arr[$params['old_express_id']]:'';
				$new_kd =isset($express_arr[$express_id])?$express_arr[$express_id]:'';
				$str.= "快递方式：{$old_kd} 变更为 {$new_kd}";
			}
			if($params['old_freight_no']!=$params['freight_no'])
			{
				$str.= "快递单号：{$params['old_freight_no']} 变更为 {$params['freight_no']}";
			}
                        //更改ship_freight的快递单号，快件列表
           if($str==''){
           	 $result['error'] = "没做任何修改";
           	 Util::jsonExit($result);
           }
            $flag = $model->updateShipMethod2($order_sn,$params['freight_no'],$express_id);

			$arr2['remark']=$str;
			$res = $model->updateShipMethod($arr1 , $arr2);
			$send_goods_status=isset($params['send_good_status'])?$params['send_good_status']:0;
			
			if($res == 'success' && $flag){
				$result['success'] = 1;
				if($send_goods_status==1 || $send_goods_status==4){
					$result['status'] = 1;
				}else{
					$result['status'] = 0;
				}
				
				$result['error'] = '修改快递方式成功';
				$result['express_id'] = $express_id;
				$result['new_ship'] = $express_arr[$express_id];
			}else{
				$result['error'] = "修改快递方式失败";
			}

			Util::jsonExit($result);
		}

		/** 以下代码是展示模版功能 **/
		$id = isset($params['order_sn']) ? $params['order_sn'] : 0;
		$ret=ApiModel::sales_api(array('order_sn'), array($order_sn) ,'getOrderDetailsId');
		$ret =  array_merge($ret[0],$ret[1]);
		if($id == 0){
			$result['error'] = '参数错误';
			Util::jsonExit($result);
		}

		$result['content'] = $this->fetch('update_ship_method.html',array(
			'info_express'=>$express_arr,
			'data'=>$ret,
		));
		$result['title'] = '修改快递方式';
		Util::jsonExit($result);
	}

	/** 添加备注 **/
	public function addNote($params){
		$result = array('success' => 0,'error' => '');
		if(isset($params['dx']) && $params['dx'] == 'sub'){
			//写入数据
			$order_no = trim($params['order_no']);
			$create_user = $_SESSION['userName'];
			$remark = stripcslashes("<font color='red'>".trim($params['remark'])."</font>");
			//update by luochuanrong 废了旧的API方式
			//model = new ShipFreightModel(43);
			//$res = $model->AddOrderLog($order_no, $create_user , $remark);
		      $model=new SalesModel(27);
		      $order=$model->getBaseOrderInfoBySn($order_no);
		      $log=array(
		                'order_id'=>$order['id'],
		                'order_status'=>$order['order_status'],
		                'shipping_status'=>$order['send_good_status'],
		                'pay_status'=>$order['order_pay_status'],
		                'create_user'=>$create_user,
		                'create_time'=>date('Y-m-d H:i:s'),
		                'remark'=>$remark 
		                );
		      $res=$model->addOrderLog($log); 


			if($res){
				$result['success'] = 1;
				$result['error'] = '操作成功';
			}else{
				$result['error'] = '操作失败';
			}
			Util::jsonExit($result);
		}else{

			//展示添加页面
			$result['content'] = $this->fetch('add_order_log.html',array(
				'order_no' => $params['order_no']
			));
			$result['title'] = '添加订单操作';
			Util::jsonExit($result);
		}
	}

	/******************************************************
	fun:VerifyOrderStatus
	description:通过订单号验证获取信息，验证状态；
	 配货销账、FQC质检(通过或未通过只是在操作按钮是触发)、
	 1 检测是否有退款操作 有则不能操作
	 2 检测是否有关闭操作 有则不能操作
	 3 检测支付状态是否是已付款和财务付款状态，不是则不能操作
	 4 检测审核状态是否为已审核，不是则不能操作
	para:order_sn 订单号
	*******************************************************/
	public  function VerifyOrderStatus($order_sn)
	{
		if(empty($order_sn))
		{
			$result['error'] = "订单号不能为空";
			Util::jsonExit($result);
		}
		$exit_tuikuan = ApiModel::sales_api(array('order_sn'),array($order_sn),'isHaveGoodsCheck');
		if (!$exit_tuikuan)
		{
			$result['error'] = "此订单有未完成的退款申请，不能操作";
			Util::jsonExit($result);
		}
		#检测是否有关闭操作 有则不能操作
		$is_close = ApiModel::sales_api(array('order_sn'),array($order_sn),'GetOrderInfoBySn');
		//var_dump($is_close);exit;
		if ($is_close['apply_close']==1)
		{
			$result['error'] = "订单号".$order_sn."审核关闭状态，不能操作";
			Util::jsonExit($result);
		}
// 		if ($is_close['apply_return']==2)
//         {
//             $result['error'] = "订单号".$order_sn."订单正在退款，不能进行发货操作";
//             Util::jsonExit($result);
//         }
		if (!($is_close['order_pay_status'] == 3 || $is_close['order_pay_status'] == 4))
		{
			$result['error'] = "订单号".$order_sn."支付状态不是已付款或财务备案状态，不能操作";
			Util::jsonExit($result);
		}
		if ($is_close['order_status'] != 2)
		{
			$result['error'] = "订单号".$order_sn."非已审核状态，不能操作";
			Util::jsonExit($result);
		}
	}



/**
	 *	edit，渲染修改页面
	 */
	public function add_ship ($params)
	{
		//var_dump($_REQUEST);exit;
		$send_status = intval($params["send_status"]);
		//取得快递公司信息
		$ex_model		= new ExpressModel(1);
		$info_express   = $ex_model->getAllExpress();
		//取得部门信息
		$dep_model      = new DepartmentModel(1);
		$info_dep       = $dep_model->getList();

		//获取信息
		$order_sn =_Request::get('order_sn');
		$consignee =_Request::get('consignee');
		$address =_Request::get('address');
		$tel =_Request::get('tel');
        $consignee2 =_Request::get('consignee2');
        $address2 =_Request::get('address2');
        $tel2 =_Request::get('tel2');
        if($send_status == 2 && !empty($tel2)){
	        $consignee =$consignee2;
	        $tel =$tel2;
	        $address =$address2;
        }


		$order_amount =_Request::get('order_amount');
		$customer_source_id =_Request::get('customer_source_id');

		$order_id =_Request::get('id');
		$order_pay_status =_Request::get('order_pay_status');
		$order_status =_Request::get('order_status');
		foreach($info_dep as $k=>$v)
		{
			$info_dep[$k]['name1']=$v['name'];
			$info_dep[$k]['name']=str_repeat('&nbsp;',(count(explode('-',$v['tree_path']))-1)*3).$v['name'];
		}
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		//var_dump($send_status);exit;
		$result['content'] = $this->fetch('logistics_delivery_info.html',array(
			//'view'=>new ShipFreightView(new ShipFreightModel($id,43)),
			'info_express' =>$info_express,
			'info_dep'=>$info_dep,
			'send_status'=>$send_status,
			'order_sn'=>$order_sn,
			'consignee'=>$consignee,
			'address'=>$address,
			'tel'=>$tel,
			'order_amount'=>$order_amount,
			'customer_source_id'=>$customer_source_id,
			'order_id'=>$order_id,
			'order_pay_status'=>$order_pay_status,
			'order_status'=>$order_status,
			'tab_id'=>$tab_id
		));
		if($send_status==2){
			$result['title'] = '补寄快递';
		}else{
			$result['title'] = '提前发货快递';
		}

		Util::jsonExit($result);
	}


	public function insert_ship ($params)
	{
		//var_dump($_REQUEST);exit;
		$result = array('success' => 0,'error' =>'');
		$note = isset($_REQUEST['note'])?$_REQUEST['note']:'';

		$fapiao = _Request::get('fapiao');
		$send_status =_Request::get('send_status');
		$note_t = _Request::get('note_t');
		$freight_no = _Request::get('freight_no');

		if(!$freight_no){
			$result['error'] ="快递单号必填！";
			Util::jsonExit($result);
		}
		if($send_status==2 && !$note){
			$result['error'] ="发货缘由必须选择！";
			Util::jsonExit($result);
		}
		if(isset($note[0])){
			if($note[0]=='补寄发票' && !$fapiao){
				$result['error'] ="发票号不可为空！";
				Util::jsonExit($result);
			}
		}
		if(isset($note[0])){
			if($note[0]=='补寄发票' && !$fapiao){
				$result['error'] ="发票号不可为空！";
				Util::jsonExit($result);
			}
		}

		if($send_status!=2 && !$note_t){
			$result['error'] ="发货缘由必填！";
			Util::jsonExit($result);
		}

		if($note){
			$note_str =implode(',', $note);
			if($note[0]=='补寄发票'){
				$remark =$note_str.",发票号：".$fapiao;
			}else{
				$remark =$note_str;
			}
		}else{
			$remark=$note_t;
		}
		$express_info = explode('|', _Post::get('express_id'));
		$express_id = $express_info[0];
		if (count($express_info) == 2) {
			$express_name = $express_info[1];
		} 
		if($express_id != 10 && !empty($freight_no) && $_SESSION['userName'] !='admin'){
			$ex_model = new ExpressModel($express_id,1);
			$express_v =  new ExpressView($ex_model);
			if (empty($express_name)) $express_name = $express_v->get_exp_name();
			$rule = $express_v->get_freight_rule();
			if($rule && !preg_match($rule,$freight_no)){
				$result['error'] ="快递单号与快递公司不符！";
				Util::jsonExit($result);
			};
		}
		//var_dump($_REQUEST);exit;
		// 2015-11-03 fix by lyy  boss629
		// 5、物流管理-快件管理-订单发货，点击【添加快递】【修改快递方式】，保存时需要判断：当订单销售渠道为【B2C销售部】且来源选择【中国移动积分】或者销售渠道为【银行销售部】且来源选择【交通银行】，快递物流允许选择【圆通速递】，其他的情况不允许保存，需提示“订单销售渠道和来源不支持圆通速递，请选择其他的快递物流”
		$order_id =  _Request::get('order_id');
		$this -> checkExpPermit($order_id,$express_id);
		
		$olddo = array();
		if (_Request::get('a') == 'quikdistrib') {
			$orderModel = new SalesModel(27);
			$order = $orderModel->getOrderInfoBySn($params['order_sn']);
			$order_address = $orderModel->getAddressByOrderSn($params['order_sn']);
			
			if (empty($order_address['address'])) {
				$result['error'] ="没有收货地址，请去订单详情页添加";
				$result['error_code'] = 'address_is_empty';
				Util::jsonExit($result);
			}

			$newdo=array(
					'freight_no'		=> _Post::get('freight_no'),
					'order_no'		=> $params['order_sn'],
					'order_mount'		=> $order['order_amount'],
					'sender'		=> '郭伟',
					'department'			=> '物流部',
					'consignee'		=> $order_address ? $order_address['consignee'] : '',
					'cons_address'			=> $order_address ? $order_address['address'] : '',
					'express_id'			=> $express_id,
					'channel_id'			=> $order['customer_source_id'],
					'remark'			=> $remark,
					'create_time'			=>time(),
					'create_id'			=>Auth::$userId,
					'create_name'			=>$_SESSION['userName'],
			);
		} else {
			$newdo=array(
					'freight_no'		=> _Post::get('freight_no'),
					'order_no'		=> _Post::get('order_sn'),
					'order_mount'		=> $_REQUEST['order_amount'],
					'sender'		=> _Post::get('sender'),
					'department'			=>_Post::get('department'),
					'consignee'		=> _Post::get('consignee'),
					'cons_address'			=> _Post::get('cons_address'),
					'cons_tel'             => _Post::get('tel'),
					'express_id'			=> $express_id,
					'channel_id'			=> _Post::get('customer_source_id'),
					'remark'			=> $remark,
					'create_time'			=>time(),
					'create_id'			=>Auth::$userId,
					'create_name'			=>$_SESSION['userName'],
			);
		}
		//var_dump($newdo);exit;
		$newmodel =new ShipFreightModel(44);
		$model =  new ShipParcelModel(44);
		//快递单号 在包裹列表中是否重复($field,$where,$type=1)
		$exists_baoguo = $model->select2("express_sn","express_sn ='{$newdo['freight_no']}' and express_id ={$newdo['express_id']}",$type=2);
		if($exists_baoguo){
			$result['error'] = '包裹列表中已存在快递单号！';
			Util::jsonExit($result);
		}
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			if (_Request::get('a') == 'quikdistrib') {
				$salesModel = new SalesModel(28);
				$newdo['order_sn'] = $newdo['order_no'];
				$ret = $salesModel->updateAddressWay($newdo);
				if ($ret['error'] == 1) {
					$result['error'] = '添加失败';
					Util::jsonExit($result);
				}
				
				$order_status = $order['order_status'];
				$order_pay_status = $order['order_pay_status'];
			} else {
				$order_status = _Request::get('order_status');
				$order_pay_status = _Request::get('order_pay_status');
			}
			
			$order_id = _Request::get('order_id');
			$times =date("Y-m-d H:i:s",time());
			$keys2 = array('order_id' , 'order_status' , 'shipping_status' , 'pay_status' , 'create_time' , 'create_user' , 'remark');
			$vals2 = array($order_id , $order_status , $send_status , $order_pay_status , $times , $_SESSION['userName'] , "添加快递,缘由：{$remark},{$express_name}:{$newdo['freight_no']}");
			ApiModel::sales_api( $keys2 , $vals2 , 'addOrderAction');
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '添加失败';
		}
		Util::jsonExit($result);
	}

//多处调用，独立出来，使得修改的时候只需修改一个就行
	public function checkExpPermit($order_id,$express_id)
	{	return true;//BOSS-1394 所有渠道允许使用圆通速递
		// 2015-11-03 fix by lyy  boss629
		// 5、物流管理-快件管理-订单发货，点击【添加快递】【修改快递方式】，保存时需要判断：当订单销售渠道为【B2C销售部】且来源选择【中国移动积分】或者销售渠道为【银行销售部】且来源选择【交通银行】，快递物流允许选择【圆通速递】，其他的情况不允许保存，需提示“订单销售渠道和来源不支持圆通速递，请选择其他的快递物流”
		$orderModel = new SalesModel(27);
		$order = $orderModel->getOrderInfoById($order_id);
		// print_r($order);
		// die;
		 $sou_id = $order["customer_source_id"];
		 $dep_id = $order["department_id"];
		 
		 if(($dep_id == 13 and $sou_id == 566 ) or ($dep_id == 3 and $sou_id == 714 ))
		 {
			;//允许选择圆通也允许选择其他，什么都不做
		 }
		 else//其他情况不能选择圆通
		 {
		 	;
		 	/*
			if($express_id == 12)
			{
				$result['error'] = '订单销售渠道和来源不支持圆通速递，请选择其他的快递物流';
				Util::jsonExit($result); 
			} 
			*/ 
		 }
	}	 

	public function orderExpress()
	{	
		$order_sn = _Post::getString('order_sn');	
        $express_id=_Post::getInt('express_id');
        $express_order_id=_Post::getInt('express_order_id');
	    

        $data=array();
        $SalesModel = new SalesModel(27);
        $res=$SalesModel->getAddressByOrderSn($order_sn);
        if(empty($res)){
        	    $result['result']=0;
		        $result['error'] = '获取订单信息失败';
		        Util::jsonExit($result);
        }

        $order = $SalesModel->getOrderInfoBySn($order_sn);
        if(empty($order)){
        	    $result['result']=0;
		        $result['error'] = '获取订单信息失败';
		        Util::jsonExit($result);
        }        
        //if(empty($express_id))
        //        $express_id=$res['express_id'];
        if(empty($express_id)){
        	    $result['result']=0;
		        $result['error'] = '请选择快递公司';
		        Util::jsonExit($result);
        }
        
        $data['out_order_sn'] = $order['out_order_sn'];
        $data['order_sn'] = $order_sn;
        $data['j_company'] =EXPRESS_J_COMPANY;
        $data['j_contact']= $res['order_pay_type']==351 ? EXPRESS_J_COMPANY : EXPRESS_J_CONTACT;
        $data['j_tel']= $res['order_pay_type']==351 ? EXPRESS_J_TEL_AIKUCUN : EXPRESS_J_TEL;
        $data['j_address']=EXPRESS_J_ADDRESS;
        $data['goods_name']=EXPRESS_GOODS_NAME;
        $data['d_company']=$res['consignee'];
        $data['d_contact']=$res['consignee'];
        $data['d_tel']=$res['tel'];
        $data['province']=$res['province'];
        $data['city']=$res['city'];
        $data['district']=$res['district'];
        $data['d_address']=$res['address'];        
        if($res['send_good_status']==2&&!empty($res['tel2'])){ 
	        $data['province']='';
	        $data['city']='';
	        $data['district']='';               
	        $data['d_company']=$res['consignee2'];
	        $data['d_contact']=$res['consignee2'];
	        $data['d_tel']=$res['tel2'];
	        $data['d_address']=$res['address2'];
        }
        $file_path = APP_ROOT."shipping/modules/express_api/Express_api.php";
        require_once($file_path);
        $expresslistmodel=new ExpressListModel(43);
        if(empty($express_order_id)){
        	//$express_order_id=time().rand(10,99);
 		    $olddo = array();
			$newdo=array(
				'province'=>$data['province'],
				'city'=>$data['city'],
				'district'=>$data['district'],
				'address'=>$data['d_address'],
				'd_tel'=>$data['d_tel'],
				'd_contact'=>$data['d_contact'],
				"express_id"=>$express_id,				
				'create_time'=>date('Y-m-d H:i:s'),
				'create_user'=>$_SESSION['userName'],			
			);       	
			
			$express_order_id =$expresslistmodel->saveData($newdo,$olddo);            
        }         
        $res=Express_api::makeOrder($express_order_id,$express_id,$data);
        if($res['result']==1){
        	$expresslistmodel->updateExpressNO($express_order_id,$res['express_no']);
        }
        Util::jsonExit($res);
	}
	
}



?>
