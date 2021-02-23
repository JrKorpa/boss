<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOldOrderController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 21:06:55
 *   @update	:
 *  -------------------------------------------------
 */
class AppOldOrderController extends CommonController
{
	protected $smartyDebugEnabled = true;
    protected $whitelist = array('printorder','printorder_dz');
    protected $pay_type = array('0'=>'默认','1'=>'展厅订购','2'=>'货到付款');
	protected static $buchan_status = array('1'=>'未操作','2'=>'已布产','3'=>'生产中','4'=>'已出厂','5'=>'不需布产');
    protected $from_arr = array(
        2 => array("ad_name"=> "淘宝B店", "api_path" =>"taobaoOrderApi"),
        "taobaoC" => array("ad_name"=> "淘宝C店", "api_path" =>"taobaoOrderApi"),
        "jingdongA" => array("ad_name"=> "京东", "api_path" =>"jd_jdk_php_2"),
        "jingdongB" => array("ad_name"=> "京东/裸钻", "api_path" =>"jd_jda_php"),
        "jingdongC" => array("ad_name"=> "京东/金条", "api_path" =>"jd_jdb_php"),
        "jingdongD" => array("ad_name"=> "京东/名品手表", "api_path" =>"jd_jdc_php"),
        "jingdongE" => array("ad_name"=> "京东/欧若雅", "api_path" =>"jd_jdd_php"),
        "jingdongF" => array("ad_name"=> "京东SOP", "api_path" =>"jd_jde_php"),
        "paipai" => array("ad_name"=> "拍拍网店", "api_path" =>"paipaiOrder")
    );

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('app_old_order_search_form.html',array('bar'=>Auth::getBar()));
	}

    /**
     * 	search，列表
     */
    public function search($params) {

        $page = _Request::getInt("page", 1);
        $pagesize = _Request::getInt("pagesize", 15);        
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'page'=>  $page,
            'pagesize'=>  $pagesize,
            'order_sn' => _Request::getString("order_sn"),
            'consignee'=>_Request::getString('consignee'),
            'mobile' => _Request::getString("mobile"),
            'myrad' => _Request::getString("myrad"),
        );

        $where = array(
            'order_sn' => $args['order_sn'],
            'consignee' => $args['consignee'],
            'mobile' => $args['mobile'],
        );

        if($where['order_sn']=='' && $where['mobile']==''){
            die("必须输入订单号或手机号码查询");
        }

        $model = new AppOldOrderModel(27);
        $BaseOrdermodel = new BaseOrderInfoModel(27);
        $goodsmodel = new AppOrderDetailsModel(27);
        if($args['myrad']==1){
            $data = $model->pageList($where, $page, $pagesize);
        }else{
            $data = $BaseOrdermodel->pageList($where, $page, $pagesize, false);
            if($data['data']['data']){
                $customer_source_model = new CustomerSourcesModel(1);
                foreach ($data['data']['data'] as $k => $v) {
                    $customer_source_name = $customer_source_model->getCustomerSourcesList("`source_name`", array('id' => $v['customer_source_id']));

                    if (count($customer_source_name) > 0) {
                        $data['data']['data'][$k]['customer_source_name'] = $customer_source_name[0]['source_name'];
                    } else {
                        $data['data']['data'][$k]['customer_source_name'] = '';
                    }
                    
                    if($v['buchan_status'] == 2){
                        $orderGoods = $goodsmodel->getGoodsByOrderId(array('order_id' => $v['id']));
                        $v['buchaning'] = true;
                        $v['buchanmsg'] = '';
                        foreach($orderGoods as $og){
                            if($og['is_stock_goods'] == 0 && $og['bc_id'] ==0){
                                $v['buchanmsg'] .= "款号 {$og['goods_sn']} 还未生成布产单.";
                                $v['buchaning'] = false;
                            }
                        }
                    }else{
                        $v['buchanmsg'] = '';
                        $v['buchaning'] = true;
                    }
                    $data['data']['data'][$k]['buchanmsg'] = $v['buchanmsg'];
                    $data['data']['data'][$k]['buchaning'] = $v['buchaning'];

                    $data['data']['data'][$k]['hidden'] = Util::zhantingInfoHidden($v);
                }
            }

            //获取全部的有效的销售渠道
            $SalesChannelsModel = new SalesChannelsModel(1);
            $getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
            //获取所有数据 
            $allSalesChannelsData = array();
            foreach ($getSalesChannelsInfo as $val) {
                $allSalesChannelsData[$val['id']] = $val['channel_name'];
            }
            
            //订购类型
            $payMentModel = new PaymentModel(1);
            $allPay = array_column($payMentModel->getAll(),'pay_name','id');
        }

        $pageData = $data['data'];
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'app_old_order_search_page';
        $this->render('app_old_order_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $pageData,
            'dd' => new DictModel(1),
            'pay_type'=>$allPay,
            'buchan_status'=>self::$buchan_status,
            'allSalesChannelsData' => $allSalesChannelsData,
            'myrad' => $args['myrad'],
        ));
    }

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
        /* 订单状态 */
        $order_status=array(0=>'未审核',1=>'已审核',2=>'审核未通过',3=>'无效',4=>'退货',5=>'关闭');
        /* 配送状态 */
        $shipping_status=array(0=>'未发货',1=>'已发货',2=>'已收货',3=>'允许发货',4=>'已到店');
        /* 支付状态 */
        $pay_status=array(0=>'未付款',1=>'付款中',2=>'已付款',3=>'网络付款',4=>'支付定金',5=>'财务备案');

        /*$result = array('success' => 0,'error' => '');
        $where=array();
		$where['order_id'] = intval($params["order_id"]);
        $model = new AppOldOrderModel(27);
        $data = $model->getOrder_goodsByOrder_id($where);
        //print_r($data['data']);exit;
        $this->render('app_old_order_show.html',array('bar'=>Auth::getBar(),'data'=>$data['data'],'order_status'=>$order_status,'shipping_status'=>$shipping_status,'pay_status'=>$pay_status));*/

        if(!empty($params['order_sn'])){
            $model =  new BaseOrderInfoModel(27);
            $id = $model->getOrderidBysn($params['order_sn']);
			if(!$id)
			{
				echo "订单号不存在";exit;
			}
        }
		else if(!empty($params['order_goods_id']))
		{
			//仓储货号上需要连接到订单，根据明细id查询订单id
			$model =  new AppOrderDetailsModel($params['order_goods_id'],27);
			$id = $model->getValue('order_id');
			if(!$id)
			{
				echo "<font size=5>该货号找不到归属订单，请查看是否是老系统的布产单".$params['order_goods_id']."！</font>";exit;
			}

		}
		else
		{
            $id = intval($params["id"]);
        }
        //全部的物流
        $express = new ExpressView(new ExpressModel(1));
        $express = $express->getAllexp();

        $orderModel = new BaseOrderInfoModel($id, 27);
        $address = $orderModel->getorderAddresinfo($id);

        //订单金额数据统计
        $order_price_info = $orderModel->getOrderPriceInfo($id);
        //获取地区名称信息
        $region = new RegionModel(1);
        
        $addressstr = '';
        if (!empty($address)) {
            $regioninfo = $region->getRegionList("$address[country_id],$address[province_id],$address[city_id],$address[regional_id]");
            foreach ($regioninfo as $key => $val) {
                $addressstr.= $val['region_name'] . '  ';
            }
            $addressstr.=$address['address'];
            $address['addressstr'] = $addressstr;
            

        } else {
            $address = array();
        }
        $order = $orderModel->getOrderInfoById($id);
        $order_account = $orderModel->getOrderAccount($id);
        $goods_price = $orderModel->getGoodsPrice($id);
        $departmentModel = new DepartmentModel(1);
        $order['department_name'] = $departmentModel->getNameById($order['department_id']);
        $customerSourcesModel = new CustomerSourcesModel(1);
        $customer_source_info = $customerSourcesModel->getCustomerSourceById($order['customer_source_id']);
        $order['customer_source_name'] = $customer_source_info['source_name'];

        $retUser = $orderModel->getMember_Info_userId($order['user_id']);
		$userInfo=array();//add by zhangruiying
        if ($retUser['error'] > 0) {
            $u_name = $retUser['data'];
        } else {
            $userInfo = $retUser['data'];
            $u_name = $userInfo['member_name'];
        }
        $price = 0;
         if(!empty($goods_price)){ 
             foreach ($goods_price as $k => $val){ 
                 $price += $val['goods_price']; 
             }
         }
         
         $order_account['t_price'] = $price;
        if (empty($order_account)) {
            $order_account = array('money_unpaid' => 0, 'money_paid' => 0, 'order_price' => 0,'favorable_price'=>0,'coupon_price'=>0,'shipping_fee'=>0,'insure_fee'=>0,'pay_fee'=>0,'pack_fee'=>0,'card_fee'=>0,'t_price'=>$price);
        }

        $SalesChannelsModel = new SalesChannelsModel(1);
        $getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
        //获取所有数据
        $allSalesChannelsData = array();
        foreach ($getSalesChannelsInfo as $val) {
            $allSalesChannelsData[$val['id']] = $val['channel_name'];
        }

            $giftida =  $orderModel->getGifts($id);
            if(!empty($giftida)){
                $giftids= explode(',',$giftida['gift_id']);
                $giftidn= explode(',',$giftida['gift_num']);
                $giftstr='';
                foreach($giftids as $k=>$v){
                    if(array_key_exists($v,$this->gifts)){
                        $giftstr.=$this->gifts[$v].$giftidn[$k].'个&nbsp;';
                    }
                }
            }else{
                $giftstr='';
            }
       // $PayModel = new PaymentModel(1);
        $DM=new AppOrderDetailsModel(27);
        $cdata = $DM->getGoodsByOrderId(array('order_id'=>$id));
        $count=0;
        if(!empty($cdata)){
            foreach($cdata as $ke=>$va){
                $count+=$va['goods_count'];
            }
        }
        $paylist=$this->GetPaymentInfo();
        $giftida['giftstr']=$giftstr;
        $this->render('app_old_order_show.html', array(
            'express' => $express,
            'view' => new BaseOrderInfoView($orderModel),
            'order_account' => $order_account,
            'address' => $address,
            'u_name' => $u_name,
            'order' => $order,
            'gift'=>$giftida,
            'user_info' => $userInfo,
            'allSalesChannelsData' => $allSalesChannelsData,
            'order_price_info' => $order_price_info,
            'count'=>$count,
            'paylist'=>$paylist,
            'bar' => Auth::getViewBar(),
            'dingzhible' => true //!empty(Auth::getOperationAuth(ucfirst($params['con']),'dingzhi'))
        ));
	}

	/**
	 *	showAppOld，渲染查看页面，老系统
	 */
	public function showAppOld ($params)
	{
        /* 订单状态 */
        $order_status=array(0=>'未审核',1=>'已审核',2=>'审核未通过',3=>'无效',4=>'退货',5=>'关闭');
        /* 发货状态 */
        $shipping_status=array(0=>'未发货',1=>'已发货',2=>'已收货',3=>'允许发货',4=>'已到店');
        /* 支付状态 */
        $pay_status=array(0=>'未付款',1=>'付款中',2=>'已付款',3=>'网络付款',4=>'支付定金',5=>'财务备案');
        //配送状态
        $delivery_status=array(0=>'未配货',1=>'配货中',2=>'配货缺货',3=>'已配货');
        //商品状态
        $goods_status = array('0'=>'未操作','1'=>'未跟单','2'=>'正在生产','3'=>'已出产','4'=>'不需布产','5'=>'维修中','8'=>'待审核');

        $result = array('success' => 0,'error' => '');
        $where=array();
		$where['order_id'] = intval($params["order_id"]);
        $model = new AppOldOrderModel(27);
        $data = $model->getOrder_goodsByOrder_id($where);
        if($data['data']['order_list']){
            foreach($data['data']['order_list'] as $k=>$v){
                if(strstr($v['certid'],'HRD-D')){
                    $data['data']['order_list'][$k]['kuan_sn']=1;
                }else{
                    $data['data']['order_list'][$k]['kuan_sn']='';
                }
                $url_img=$model->getStyleGallerBygoods_sn($v['goods_sn']);
                if($url_img['error']<1){
                    $data['data']['order_list'][$k]['big_img']=$url_img['data']['big_img'];
                }else{
                    $data['data']['order_list'][$k]['big_img']='';
                }
            }
        }
        //print_r($data['data']);exit;
        $this->render('app_old_order_showAppOld.html',array('bar'=>Auth::getBar(),'data'=>$data['data']['order_list'],'order_status'=>$order_status,'shipping_status'=>$shipping_status,'pay_status'=>$pay_status,'goods_status'=>$goods_status,'action_list'=>$data['data']['action_list']));

	}
}

?>