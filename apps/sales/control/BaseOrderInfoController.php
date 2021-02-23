<?php

/**
 *  -------------------------------------------------
 *   @file		: BaseOrderInfoController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com
 *   @date		: 2015-01-28 12:36:56
 *   @update	:
 *  -------------------------------------------------
 */
class BaseOrderInfoController extends CommonController {

    protected $smartyDebugEnabled = false;
    protected $whitelist = array('printorder','printorder_dz','delorder');
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
     * 	index，搜索框
     */
    public function index($params) {
        $this->getDepartment();
        $this->getSourceList();
        $this->getCustomerSources();
        $paylist=$this->GetPaymentInfo();
        $orderModel = new BaseOrderInfoModel(51);
        $this->dd = new DictView(new DictModel(1));
		$this->getSourceList();
        $this->render('base_order_info_search_form.html', array(
            'bar' => Auth::getBar(), 'pay_type'=>$paylist,'referers'=>$orderModel->getReferers(),
                'dd' => $this->dd,'buchan_status'=>self::$buchan_status
            )
        );
    }

    public function getSourceList() {
        //渠道
		$model = new UserChannelModel(1);
		$data = $model->getChannels($_SESSION['userId'],0);
		if(empty($data)){
			die('请先联系管理员授权渠道!');
		}
		$this->assign('onlySale',count($data)==1);
        $this->assign('sales_channels_idData', $data);
    }

    /**
     * 	search，列表
     * 2015-11-20, modified by gengchao，根据BOSS-816重新整理了独立条件搜索和渠道的代码，有疑问看816的备注说明
     */
    public function search($params) {
        $isBespokeSnSearch = false;
        $this->getDepartment();
        $this->getSourceList();
        
        if(!empty($_REQUEST['order_department'])){
            $department_id=$_REQUEST['order_department'];
        }else{
            $department_id = "";
        }
        
       // TODO: 如果用户有超级管理员权限，则按用户选择的渠道进行查询；否则，渠道强制更新为用户所属渠道进行查询
        $res = $this->ChannelListO();
        if ($res !== true) {
            $department_id=implode(',', $res);            
            if(!empty($_REQUEST['order_department'])){
                if(count(explode(',', $_REQUEST['order_department']))==1){
                    if(in_array($_REQUEST['order_department'], $res)){
                        $department_id=$_REQUEST['order_department'];
                    }else{
                       exit('没有渠道权限');                                         
                    }   
                }  
            }           
        }

/*
 *      //由于增加了店长和销售顾问的区别
        //首先获取全部的实体店的渠道id
        $HB = $this->getShopHB();
        $HBid = array_column($HB,'id');
        $create_user=_Request::get('create_user');
        if(in_array($department_id,$HBid)){
            //这个渠道属于体验店 店长看全部 销售顾问看个人
            $HBleader = array_column($HB,'dp_leader_name','id');
            $dianzhang = explode(',',$HBleader[$department_id]);
            if(!in_array($_SESSION['realName'],$dianzhang)){
                //不是店长
                $department_id='';
                $create_user=$_SESSION['realName'];
            }

        }
*/
        
        $create_user =_Request::getString('create_user');
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'page_size' => _Request::getString('page_size', 25),
            'order_sn' => _Request::getString('order_sn'),
            'out_order_sn'=>_Request::getString('out_order_sn'),
            'create_user' =>$create_user,
            'start_time' => _Request::getString("start_time"),
            'end_time' => _Request::getString("end_time"),
            'pay_date_start_time' => _Request::getString("pay_date_start_time"),
            'pay_date_end_time' => _Request::getString("pay_date_end_time"),
            'order_status' => _Request::get("order_status"),
            'order_check_status' => _Request::get("order_check_status"),
            'order_pay_status' => _Request::get("order_pay_status"),
            'order_department' => $department_id,
            'buchan_status' => _Request::get("buchan_status"),
            'delivery_status' => _Request::get("delivery_status"),
            'send_good_status' => _Request::get("send_good_status"),
            'customer_source' => _Request::get("customer_source"),
            'consignee' => _Request::get("consignee"),
            'genzong' => _Request::get("genzong"),
            'mobile' => _Request::get("mobile"),
            'is_zp' => _Request::getString("is_zp"),
            'order_type' => _Request::getString("order_type"),
            'is_netsale' => _Request::getString("is_netsale"),
            'close_order'=>isset($_REQUEST['close_order'])&&$_REQUEST['close_order']=='1' ? 1:0,
            'pay_type'=>_Request::getString('pay_type')?_Request::getInt('pay_type'):'',
            'is_delete' => (!isset($_REQUEST['is_delete']) or isset($_REQUEST['is_delete']) && $_REQUEST['is_delete'] == '') ? 0 : _Request::getInt("is_delete"),
            //默认显示日期
            'referer' => _Request::getString("referer"),
            'bespoke_sn' => _Request::getString("bespoke_sn"),
            'channel_class' => _Request::getString("channel_class"),
            'recommender_sn' => _Request::getString("recommender_sn")
        );
		
        $page = _Request::getInt("page", 1);
        $where = array(
            'order_sn' => $args['order_sn'],
            'create_user' => $args['create_user'],
            'order_status' => $args['order_status'],
            'start_time' => $args['start_time'],
            'end_time' => $args['end_time'],
            'pay_date_start_time' => $args['pay_date_start_time'],
            'pay_date_end_time' => $args['pay_date_end_time'],
            'order_check_status' => $args['order_check_status'],
            'order_pay_status' => $args['order_pay_status'],
            'department_id' => $args['order_department'],
            'send_good_status' => $args['send_good_status'],
            'delivery_status' => $args['delivery_status'],
            'buchan_status' => $args['buchan_status'],
            'customer_source' => $args['customer_source'],
            'consignee' => $args['consignee'],
            'genzong' => $args['genzong'],
            'mobile' => $args['mobile'],
            'is_delete' => $args['is_delete'],
            'is_zp' => $args['is_zp'],
            'order_type' => $args['order_type'],
            'is_netsale' => $args['is_netsale'],
            'close_order' => $args['close_order'],
            'referer' => $args['referer'],
            'bespoke_sn' => $args['bespoke_sn'],
            'channel_class' => $args['channel_class'],
            'recommender_sn' => $args['recommender_sn'],
            'is_user_super' => $res//是否超级管理员或网销
        );
        if($args['pay_type'] != ''){
            $where['pay_type'] = $args['pay_type'];
        }

        $model = new BaseOrderInfoModel(51);		
        $goodsmodel = new AppOrderDetailsModel(27);
       
        
        if(!empty($where['is_netsale'])){
            if(empty($where['start_time'])||empty($where['end_time'])){
                exit('查询网销订单必须指定制单日期');
            }else{
                if(floor(strtotime($where['end_time'])-strtotime($where['start_time']))/86400 >32)
                exit('查询网销订单指定制单日期不能超过31天');
            }
        }


        $where_bak=$where;
        unset($where_bak['is_delete']);
        unset($where_bak['close_order']);
        unset($where_bak['is_zp']);
        if(count(array_filter($where_bak))==0){           
            exit('查询数据过多请指定更多查询条件');
        }
        if(count(array_filter($where_bak))==2 && !empty($where['start_time']) && !empty($where['end_time'])){           
            if(floor(strtotime($where['end_time'])-strtotime($where['start_time']))/86400 >366)
                exit('制单时间不能超过1年');
        }      
        
		$where['sale']=$model->getUserBespokeRoleByDepId($_SESSION['userName'],$where['department_id']);
        //如果是手机号，订单号(1个)，客户姓名 属于精确查找不会走其他限制
        if($where['order_sn'] || $where['consignee']||$where['mobile']){
        	$where = array(
        	    'send_good_status' => $args['send_good_status'],
        	    'delivery_status'=>$args['delivery_status']        	    
        	);
            
            if(!empty($args['mobile'])){
                $where['mobile']=$args['mobile'];
            }elseif(!empty($args['order_sn'])){
                $args['order_sn']=str_replace(" ",",",$args['order_sn']);
                $args['order_sn']=array_filter(explode(",",$args['order_sn']));
                $where['order_sn']=implode("','",$args['order_sn']);
                $args['order_sn']=implode(",",$args['order_sn']);
            }elseif(!empty($args['consignee'])){
                $where['consignee']=$args['consignee'];
            }
        }elseif(!empty($args['out_order_sn'])){
        	$args['out_order_sn']=str_replace(" ",",",$args['out_order_sn']);
        	$args['out_order_sn']=array_filter(explode(",",$args['out_order_sn']));
        	$out_order_sn_str=implode("','",$args['out_order_sn']);
        	$args['out_order_sn']=implode(",",$args['out_order_sn']);
	        $order_sn=$model->getOrdersnByOutsn($out_order_sn_str);
            if(empty($order_sn)){
                //外部订单号问题
                $where = array();
                $where['order_ids']='';
				$where['close_order'] = $args['close_order'];
            }else{
                $where = array();
                $order_sn= implode(",",array_column($order_sn,'id'));
                $where['order_ids']=$order_sn;
				$where['close_order'] = $args['close_order'];
            }
        }elseif(!empty($args['bespoke_sn'])){
	        $bespoke_id=$model->getBespokeidByBespokeSn($args['bespoke_sn']);
            if(empty($bespoke_id)){
                die("预约单号不存在!");
            }else{
                $isBespokeSnSearch = true;
                $where = array();
				$where['bespoke_id'] = $bespoke_id;
            }
    	}
        $data = $model->pageList($where, $page, $args['page_size'], false);
        $user_name = array();
        if ($data['data']['data']) {
            $customer_source_model = new CustomerSourcesModel(1);
            $_value = '';
            $departmentModel = new DepartmentModel(1);
            foreach ($data['data']['data'] as $k => $v) {                
                if (!empty($v['user_id'])) {
                    $memberInfo = $model->getMemberByMemberId($v['user_id']);                    
                    if (!empty($memberInfo)) {
                        $data['data']['data'][$k]['user_id'] = $memberInfo['member_name'];
                    } else {
                        $data['data']['data'][$k]['user_id'] = '';
                    }
                } else {
                    $data['data']['data'][$k]['user_id'] = '';
                }

                $customer_source_name = $customer_source_model->getCustomerSourcesList("`source_name`", array('id' => $v['customer_source_id']));
               
                if($isBespokeSnSearch){
                    $bespoke_sn = $args['bespoke_sn'];
                    $data['data']['data'][$k]['bespoke_sn'] = $bespoke_sn;
                }else{
                    $bespoke_sn = $model->getBespokeSnByBespokeId($v['bespoke_id']);
                    $data['data']['data'][$k]['bespoke_sn'] = $bespoke_sn;
                }

                if (count($customer_source_name) > 0) {
                    $data['data']['data'][$k]['customer_source_name'] = $customer_source_name[0]['source_name'];
                } else {
                    $data['data']['data'][$k]['customer_source_name'] = $_value;
                }
                $data['data']['data'][$k]['department_name'] = '';
                if($v['department_id']){
                   $data['data']['data'][$k]['department_name'] = $departmentModel->getNameById($v['department_id']);
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
                //var_dump($_SESSION['userName'] != $v['create_user'],$v['mobile'] != '',$_SESSION['userType'] != 1,$do['referer'] != '婚博会',!$this->checkPermissions());die;
                //if($_SESSION['userName'] != $v['create_user'] && $v['mobile'] != '' && $_SESSION['userType'] != 1 && $do['referer'] != '婚博会' && !$this->checkPermissions()){
                    //$data['data']['data'][$k]['mobile'] = substr($v['mobile'], 0, 7)."****";
                //}
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
        $payMentModel = new PaymentModel(1);
        $allPay = array_column($payMentModel->getAll(),'pay_name','id');
        $pageData = $data['data'];
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'base_order_info_search_page';
        $this->render('base_order_info_search_list.html', array(
            'pa' => Util::page($pageData),
            'allSalesChannelsData' => $allSalesChannelsData,
            'page_list' => $pageData,
            'all_price' => $data['all_price'],
            'pay_type'=>$allPay,
			'buchan_status'=>self::$buchan_status
        ));
    }

    /**
     * 	add，渲染添加页面
     */
    public function add() {
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
            die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }        
    	$crm_bespoke_id = _Request::get('yid');
    	if (empty($crm_bespoke_id)) {
	    	if (!($this->can_goto_sale())) {
	    		Util::bootboxAlert("请先领取预约单！");
	    	}
    	}
    	
        $this->getDepartment();
        $this->getSourceList();
        $this->getCustomerSources();
        $res = $this->ChannelListO();
       
        $paylist=$this->GetPaymentInfo();
        if ($res === true) {
            //获取全部的有效的销售渠道
            $SalesChannelsModel = new SalesChannelsModel(1);
            $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
        } else {
            $channellist = $this->getchannelinfo($res);
        }
        
        $bespokeInfo = array('customer_mobile' => '','customer' => '','department_id' => '','customer_source_id' => '','remark' => '');

        if (!empty($crm_bespoke_id)) {
            $order_model = new BaseOrderInfoModel(27);
            $bespokeInfo = $order_model->getbosksn($crm_bespoke_id);
            
            if (!empty($bespokeInfo)) {
            	$resp = $order_model->serveBespoke($crm_bespoke_id, $_SESSION['userName']);
            	if ($resp) {
            		$_SESSION['bespoke'] = $bespokeInfo;
            	} else {
            		if (!($this->can_goto_sale())) {
            			Util::bootboxAlert("请先领取预约单！");
            		}
            	}
            }
        }
        
        if (empty($bespokeInfo['customer_mobile']) && isset($_SESSION['bespoke'])) {
            $bespokeInfo = $_SESSION['bespoke'];
        }  
        $cartModel = new AppOrderCartModel(27);
        $cart_goods = $cartModel->get_cart_goods();
        
        $this->render('base_order_info_info.html', array(
            'view' => new BaseOrderInfoView(new BaseOrderInfoModel(27)), 'channellist' => $channellist,
            'shiyebu_id' => '', 'department_id' => '', 'cart_data' => $cart_goods, 'tab_id' => _Request::getInt('tab_id'), 'bespokeinfo' => $bespokeInfo,'paylist'=>$paylist
        ));
    }

    /**
     * 	add，渲染添加页面
     */
    public function addLogs() {
        $order_id = _Request::getInt('order_id');
        $this->render('app_order_action_info.html', array('order_id'=>$order_id,'action_id'=>'',
            'tab_id' => _Request::getInt('tab_id'),
        ));
    }

    /**
     * 	add，渲染添加页面
     */
    public function editLogs($params) {
        $order_id = _Request::getInt('_id');
        $id = intval($params["id"]);
        $baseInfoModel = new BaseOrderInfoModel(27);
        $action_info = $baseInfoModel->getOrderActionById($id);
        $this->render('app_order_action_info.html', array('order_id'=>$order_id,'action_id'=>$id,
            'tab_id' => _Request::getInt('tab_id'),'action_info'=>$action_info
        ));
    }

    /**
     * 	edit，渲染修改页面
     */
    public function edit($params) {
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
            die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }        
        $result = array('success' => 0, 'error' => '');
        $this->getDepartment();
        $this->getSourceList();
        $this->getCustomerSources();
        $id = intval($params["id"]);
        $res = $this->ChannelListO();
        $paylist=$this->GetPaymentInfo();

        if ($res === true) {
            //获取全部的有效的销售渠道
            $SalesChannelsModel = new SalesChannelsModel(1);
            $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
        } else {
            $channellist = $this->getchannelinfo($res);
        }

        $model = new BaseOrderInfoModel($id, 27);
        $viewModel = new BaseOrderInfoView($model);

        $departmentModel = new DepartmentModel(1);

        $causeInfo = $departmentModel->getDepartmentInfo("`id`,`name`", array('parent_id' => 1));
        //获取事业部
        $causeData=array();
        foreach ($causeInfo as $val) {
            $causeData[$val['id']] = $val['name'];
        }

        $departmentModel = new DepartmentModel(1);
        $doInfo = $model->getDataObject();
        $departmentid = $doInfo['department_id'];
        $order_status = $doInfo['order_status'];
        if($order_status == 2){
            echo "订单已审核不可编辑！";exit;
        }

        $bespokeInfo = array('customer_mobile' => '','customer' => '','department_id' => '','customer_source_id' => '','remark' => '');
        if(isset($_SESSION['bespoke'])){
            $bespokeInfo = $_SESSION['bespoke'];
        }

        $this->render('base_order_info_info.html', array(
            'view' => $viewModel, /* 'shiyebu_id'=>$causeInfo[0]['parent_id'], */
            'channellist' => $channellist,
            'cause' => $causeData, 'department_id' => $departmentid, 'bespokeinfo' => $bespokeInfo,
            'tab_id' => _Request::getInt('tab_id'),
            'paylist'=>$paylist,
        ));
    }

    /**
     * 	show，渲染查看页面
     */
    public function show($params) {
//     	echo 'show';exit; 
        //仓储那边的做穿透用的
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
				echo "<font size=5>该货号找不到归属订单，请查看是否是老系统的布产单".$params['order_goods_id'].";门店订单号：".$params['order_sn']."！</font>";exit;
			}

		}
		else
		{
            $id = intval($params["id"]);
        }
        
        if ((SYS_SCOPE == 'boss' && $id > 2617377) || (SYS_SCOPE == 'zhanting' && $id > 35472)) {
        	//AsyncDelegate::dispatch('order', array('event'=>'refresh_order','order_id' => $id));
        }
        
        //全部的物流
        $express = new ExpressView(new ExpressModel(1));
        $express = $express->getAllexp();

        $orderModel = new BaseOrderInfoModel($id, 27);
        $appOrderDetalModel = new AppOrderDetailsModel(27);
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

        $userInfo = $orderModel->getMemberByMemberId($order['user_id']);
        if (!empty($userInfo)) {
            $u_name = $userInfo['member_name'];
        } else {
            $u_name = '未知';
        }
       // //取出订单商品的所以可用的优惠
        //$modeld = new AppOrderDetailsModel(27);
        ////$goods_price = $modeld->getGoodsByOrderId(array('order_id'=>$id));
        //// if(empty($goods_price)){
        ////     $favorable_price_t=0;//商品优惠总金额
        //// }else{
        ////     $favorable_price_t = array_sum(array_column($goods_price,'favorable_price'));
        //// }
            
        
          
        //退货退款金额        
        if (empty($order_account)) {
            $order_account = array('money_unpaid' => 0, 'money_paid' => 0, 'order_price' => 0,'favorable_price'=>0,'t_favorable_price'=>0.00,'coupon_price'=>0,'shipping_fee'=>0,'insure_fee'=>0,'pay_fee'=>0,'pack_fee'=>0,'card_fee'=>0);
        }
        
        $return_goods_price1 = 0.00;
        if(!empty($goods_price)){
            foreach ($goods_price as $k => $val){
                $return_goods_price1 += $val['goods_price'];
            }
        }
        $return_goods_price1 = number_format($return_goods_price1,2);
        //$return_goods_price1 = $appOrderDetalModel->getReturnGoodsPrice($id,0,1);
        //$return_goods_price2 = $appOrderDetalModel->getReturnGoodsPrice($id,0,2);
        $order_account['return_goods_price1'] = $return_goods_price1;//退款退货总金额
        //$order_account['return_goods_price2'] = $return_goods_price2;//退款不退货总金额
        $t_favorable_price = $orderModel->getReturnGoodsfavor($id);
        $order_account['t_favorable_price'] =  number_format($t_favorable_price,2);
        
        $is_return = $orderModel->check($id);
        $SalesChannelsModel = new SalesChannelsModel(1);
        $order_account['is_return'] = $is_return;
            
        $getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
        //获取所有数据
        $allSalesChannelsData = array();
        foreach ($getSalesChannelsInfo as $val) {
            $allSalesChannelsData[$val['id']] = $val['channel_name'];
        }
        
        //获取批发客户 
        if(empty($address['wholesale_id'])){        	
        	$ChannelsArr=$SalesChannelsModel->getChannelIdById($order['department_id']);
        	if(!empty($ChannelsArr)){
        		$wholesale_id=$ChannelsArr['wholesale_id'];        		
        	}
        }else{
            $wholesale_id = $address['wholesale_id'];
        }
        if(!empty($wholesale_id)){
        	$SelfWarehouseGoodsModel =new SelfWarehouseGoodsModel(21);
        	$wholesale_name=$SelfWarehouseGoodsModel->getWholesaleArr($wholesale_id);
        	$address['wholesale_name']=$wholesale_name;
        }else{
        	$address['wholesale_name']='';
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
		
		
		$model=new AppOrderDetailsModel(27);
        $gifts=$model->getGiftByChannelId($order['department_id']);
		
		$apiModel = new ApiManagementModel();
        $classes = $apiModel->GetChannelClassByIds(explode(',',$_SESSION['qudao']));
		
		$online_offline = array();//获得操作人属于线上渠道还是线下渠道
		
		foreach ($classes as $class){
			$online_offline[] = $class['channel_class'];
		}
        $order_time = $orderModel->getOrderTime($id);
        if(!$order_time){
            $order_time = array('allow_shop_time'=>'0000-00-00 00:00:00');
        }

        $do = $orderModel->getDataObject();
        $mobile = $do['mobile'];

        $onlinexianhuo = false;
        //var_dump($order['department_id']);die;
        $onlin_check = $apiModel->GetChannelClassByIds($order['department_id']);
        if(isset($onlin_check[0]['channel_class']) && $onlin_check[0]['channel_class'] == 1) $onlinexianhuo = true;
        //if($_SESSION['userName'] != $do['create_user'] && $do['mobile'] != '' && $_SESSION['userType'] != 1 && $do['referer'] != '婚博会' && !$this->checkPermissions()){
            //$mobile = substr($do['mobile'], 0, 7)."****";
        //}
        $this->render('base_order_info_show.html', array(
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
            'dingzhible' => true,//!empty(Auth::getOperationAuth(ucfirst($params['con']),'dingzhi'))
            'xianhuo' => true,
			'gifts'=>$gifts,
            'mobile'=>$mobile,
			'online_offline' => $online_offline,
            'order_time' => $order_time,
            'onlinexianhuo'=>$onlinexianhuo
        ));
    }


    public function action_insert($param) {
        $result = array('success' => 0,'error' =>'');
        $order_id = _Request::getInt('_id');
        $logs_content = _Request::getString('logs_content');
        if(empty($logs_content)){
            $result['error'] = "备注不能为空！";
            Util::jsonExit($result);
        }
        //判断订单是否有效
        $orderModel= new BaseOrderInfoModel(27);
        $order_info = $orderModel->getOrderInfoById($order_id);
        if(empty($order_info)){
            $result['error'] = "此订单的数据不存在！";
            Util::jsonExit($result);
        }
        $insert_action = array();
        $insert_action ['order_id'] = $order_info ['id'];
		$insert_action ['order_status'] = $order_info ['order_status'];
		$insert_action ['shipping_status'] = $order_info ['send_good_status'];
		$insert_action ['pay_status'] = $order_info ['order_pay_status'];
		$insert_action ['remark'] = '<font color="red">'.$logs_content.'</font>';
		$insert_action ['create_user'] = $_SESSION ['userName'];
		$insert_action ['create_time'] = date ( 'Y-m-d H:i:s' );
        $orderModel= new BaseOrderInfoModel(28);
        $res = $orderModel->addOrderAction($insert_action);
        if($res !== false)
        {
            $result['success'] = 1;
        }
        else
        {
            $result['error'] = '添加失败';
        }
        Util::jsonExit($result);
    }

    public function action_update($param) {
        $result = array('success' => 0,'error' =>'');
        $action_id = _Request::getInt('id');
        $logs_content = _Request::getString('logs_content');
        if(empty($logs_content)){
            $result['error'] = "备注不能为空！";
            Util::jsonExit($result);
        }
        $insert_action = array();
        $insert_action['action_id'] = $action_id;
		$insert_action['remark'] = $logs_content;
        $orderModel= new BaseOrderInfoModel(28);
        $res = $orderModel->updateOrderAction($insert_action);
        if($res !== false)
        {
            $result['success'] = 1;
        }
        else
        {
            $result['error'] = '添加失败';
        }
        Util::jsonExit($result);
    }

    /**
     * 	insert，信息入库 入两个库  app_order_cart  app_order_details
     */
    public function insert($params) {
        $result = array('success' => 0, 'error' => '');
        //判断用户等信息
        $user_name = _Request::getString('user_name');
        $mobile = _Request::getString('mobile');
        $department_id = _Request::getInt('department_id');
        $customer_source_id = _Request::getInt('customer_source');
        $order_pay_type = _Request::getInt('order_pay_type');
        $recommended = _Request::getString('recommended');
        $recommender_sn = _Request::getString('recommender_sn');
        $is_real_invoice = isset($_REQUEST['is_real_invoice'])?1:0;

		//验证来源
		$customer_source_model = new CustomerSourcesModel(1);
		$customer_source_name = $customer_source_model->getCustomerSourcesList("`source_name`", array('id' => $customer_source_id));
		foreach($customer_source_name as $val){
				$customer_source_name=$val['source_name'];
		}
		if($customer_source_name == ''){
			$result['error'] = "客户来源不能为空！";
            Util::jsonExit($result);
		}
		$hb ='婚博会';
        //验证婚博
	    $hbh = strpos($customer_source_name,$hb);
		if($hbh == false){
			//验证预约
            $HB = $this->getShopHB($department_id);
            foreach($HB as $k => $v){
                $id = $v['id'];
                if($id == $department_id){
                    $leader = array_filter(array_unique(explode(',',$v['dp_leader_name']))); 
                    $people = array_filter(array_unique(explode(',',$v['dp_people_name']))); 
                    //print_r($leader);exit;
                    if(in_array($_SESSION['userName'],$leader)||in_array($_SESSION['userName'],$people)){
                    	if (!($this->can_goto_sale())) {
                                $result['error'] = "请先领取预约单！";
                                Util::jsonExit($result);
                    	}
                    	
                    	if(isset($_SESSION['bespoke']) && $_SESSION['bespoke']['department_id']!=$department_id){
                                $result['error'] = "订单销售渠道不是预约销售渠道，请重新下预约单！";
                                Util::jsonExit($result);            
                        }
                    }else{
                            $result['error'] = "该销售渠道没有此销售顾问，请联系店长添加预约销售顾问！";
                            Util::jsonExit($result);                        
                    }
                    break;
                }
            }
		} 
		
        if (empty(trim($user_name))) {
            $result['error'] = "请填写用户名";
            Util::jsonExit($result);
        }
        if (empty(trim($mobile))) {
            $result['error'] = "请填写手机号";
            Util::jsonExit($result);
        }

        if (empty(trim($department_id))) {
            $result['error'] = "请选择渠道部门";
            Util::jsonExit($result);
        }
        if ($customer_source_id < 1) {
            $result['error'] = "请选择客户来源";
            Util::jsonExit($result);
        }

        if (strlen($mobile)!=11) {
            $result['error'] = "手机号为11位";
            Util::jsonExit($result);
        }

        if (empty(trim($order_pay_type))) {
            $result['error'] = "支付类型必须选择";
            Util::jsonExit($result);
        }

        //如果选择了财务备案的支付类型则把订单支付状态改成财务备案
        $payment_list = $this->getPaymentsBeiAn();
        if(!empty($payment_list[$order_pay_type])){
            $order['order_pay_status']=4;
        }else{
            $order['order_pay_status']=1;
        }

        //获取会员id,调接口
        $where = array('member_phone' => $mobile);
        $apiModel = new ApiMemberModel();
        $user_info = $apiModel->getMemberByPhone($where);

        //当没有此用户时，重新创建一个用户
        if ($user_info['error'] == 1) {
            $new_user_data = array(
                'member_name' => $user_name,
                'member_phone' => $mobile,
                'member_age' => 20,
                'member_type' => 1,
                'department_id' => $department_id,
                'customer_source_id' => $customer_source_id,
                'order_remark' => _Request::getString('order_remark'),
                'reg_time' => time(),
                'make_order' => $_SESSION['userName'],
            );
            $res = $apiModel->createMember($new_user_data);
            if ($res['error'] > 0) {
                $result['error'] = 1;
                $result['error'] = "创建用户失败！";
                Util::jsonExit($result);
            }
            $user_id = $res['data'];
        } else {
            $user_id = $user_info['data']['member_id'];
        }
        //订单信息
        $is_zp = isset($_POST['is_zp'])?1:0;
        $orderModel = new BaseOrderInfoModel(27);
        do{
            $order_sn = $orderModel->getOrderSn();
            $con['order_sn'] = $order_sn;
            $tmp = $orderModel->getOrderInfoNewBysn($con);
            if(!$tmp){
                break;
            }
        }while(true);
        $order['order_sn'] = $order_sn;

        if(isset($_SESSION['bespoke']) && !empty($_SESSION['bespoke'])){
            $order['bespoke_id']=$_SESSION['bespoke']['bespoke_id'];
        }else{
            $order['bespoke_id']='';
        }
        $order['user_id'] = $user_id;
        $order['order_status'] = 1;
        $order['order_check_status'] = 1;
        $order['order_pay_type'] = $order_pay_type;
        $order['delivery_status'] = 1;
        $order['create_time'] = date("Y-m-d H:i:s");
        $order['create_user'] = $_SESSION['userName'];
        $order['modify_time'] = date("Y-m-d H:i:s");
        $order['customer_source_id'] = $customer_source_id;
        $order['department_id'] = $department_id; // channel_own_id
        $order['order_remark'] = _Request::getString('order_remark');
        $order['order_price'] = 0;
        $order['is_delete'] = 0;
        $order['consignee'] = $user_name;
        $order['recommended'] = $recommended;
        $order['recommender_sn'] = $recommender_sn;
        $order['mobile'] = $mobile;
        $order['is_xianhuo'] = 2;//未选择商品
        $order['is_zp'] = $is_zp;
        $order['referer'] = '展厅订单';
        $order['is_real_invoice'] = $is_real_invoice;
        //发票
        $invoice = array();
        $invoice['is_invoice'] = $is_real_invoice;
        $invoice['invoice_amount'] = 0;
        $invoice['create_time'] = date("Y-m-d H:i:s");

         //默认取货地址
            $model = new SalesChannelsModel(1);
            $channel_own_id = $model->getChannelByOwnId($order['department_id']);
            $peisong = array();
            $peisong['consignee'] = $order['consignee'];
            $peisong['express_id'] = 10;//快递公司id 默认上门取货
            $peisong['country_id'] = 1;
            $peisong['province_id'] = $channel_own_id['province_id'];
            $peisong['city_id'] = $channel_own_id['city_id'];
            $peisong['regional_id'] = $channel_own_id['regional_id'];
            $peisong['address'] = $channel_own_id['shop_address'];
            $peisong['tel'] = $channel_own_id['shop_phone'];
            $peisong['zipcode'] = '';
            $peisong['email']='';//?邮箱
            $peisong['distribution_type']=1;//门店
            $peisong['freight_no']='';//快递单号
             if(empty($channel_own_id['short_name'])){
                    $peisong['shop_type']=0;//体验店类型
                    $peisong['shop_name']='';//体验店名称
            }else{
                    $peisong['shop_type']=$channel_own_id['shop_type'];//体验店类型
                    $peisong['shop_name']=$channel_own_id['shop_name'];//体验店名称
            }
            $peisong['goods_id']=0;//商品id

        //保存所有数据
        $all_data = array('order' => $order, 'invoice' => $invoice ,'address'=>$peisong);
        $order_id = $orderModel->makeEmptyOrder($all_data);
        if ($order_id) {
            //增加操作日志
            $orderActionModel = new AppOrderActionModel(27);
            //操作日志
            $ation['order_status'] = 1;
            $ation['order_id'] = $order_id;
            $ation['shipping_status'] = 1;
            $ation['pay_status'] = 1;
            $ation['create_user'] = $_SESSION['userName'];
            $ation['create_time'] = date("Y-m-d H:i:s");
            $ation['remark'] = "生成订单";
            $res = $orderActionModel->saveData($ation, array());

            //添加订单发票日志
            $ation = array();
            $ation['order_status'] = 1;
            $ation['order_id'] = $order_id;
            $ation['shipping_status'] = 1;
            $ation['pay_status'] = 1;
            $ation['create_user'] = $_SESSION['userName'];
            $ation['create_time'] = date("Y-m-d H:i:s");
            $ation['remark'] = "订单添加发票信息";
            $res = $orderActionModel->saveData($ation, array());

            $result['success'] = 1;
            $result['x_id'] = $order_id;
            $result['order_sn'] = $order_sn;
            $result['tab_id'] = _Request::getInt('tab_id');
        } else {
            $result['error'] = "生成订单失败！";
        }
        Util::jsonExit($result);
    }

    /**
     * 	update，更新信息
     */
    public function update($params) {
    	
//     	echo "update";exit;
    	
        $result = array('success' => 0, 'error' => '');

        $id = _Post::getInt('id');
        $orderModel = new BaseOrderInfoModel($id, 27);
        $order_status = $orderModel->getValue('order_status');
        $mobiles = $orderModel->getValue('mobile');
        $consignee = $orderModel->getValue('consignee');
        $order_department_id = $orderModel->getValue('department_id');
        $customer_source_ids = $orderModel->getValue('customer_source_id');
        $order_pay_types = $orderModel->getValue('order_pay_type');
        $order_remarks = $orderModel->getValue('order_remark');
        $is_zps = $orderModel->getValue('is_zp');
        $recommendeds = $orderModel->getValue('recommended');

        //if ($order_status == 2) {
            //$result['error'] = "已审核的订单不可以编辑！";
            //Util::jsonExit($result);
        //}
        if ($order_status == 3) {
            $result['error'] = "取消的订单不可以编辑！";
            Util::jsonExit($result);
        }
        if ($order_status == 4) {
            $result['error'] = "关闭的订单不可以编辑！";
            Util::jsonExit($result);
        }

        //判断用户等信息
        $user_name = _Request::getString('user_name');
        $mobile = _Request::getString('mobile');

        $department_id = _Request::getInt('department_id');
        $customer_source_id = _Request::getInt('customer_source');
        $order_pay_type = _Request::getInt('order_pay_type');
        $recommended = _Request::getString('recommended');
        $is_zp = isset($_POST['is_zp'])?1:0;
        $is_real_invoice = isset($_REQUEST['is_real_invoice'])?1:0;

        if($order_status==2){
            if(($mobile == $mobiles) && ($consignee == $user_name) && ($department_id == $order_department_id) && ($customer_source_id == $customer_source_ids)){

            }else{
                    $result['error'] = "已审核的订单只能修改订购类型或备注或推荐人";
                    Util::jsonExit($result);
            }
        }

        if (empty(trim($user_name))) {
            $result['error'] = "请填写用户名";
            Util::jsonExit($result);
        }
        if (empty(trim($mobile))) {
            $result['error'] = "请填写手机号";
            Util::jsonExit($result);
        }

        if (empty(trim($department_id))) {
            $result['error'] = "请选择渠道部门";
            Util::jsonExit($result);
        }
        if ($customer_source_id < 1) {
            $result['error'] = "请选择客户来源";
            Util::jsonExit($result);
        }

        if (empty(trim($order_pay_type))) {
            $result['error'] = "支付类型必须选择";
            Util::jsonExit($result);
        }
        //如果选择了财务备案的支付类型则把订单支付类型改成财务备案
        $payment_list = $this->getPaymentsBeiAn();
        $order_pay_status = '';
        if(!empty($payment_list[$order_pay_type])){
            $order_pay_status=4;
        }

         //查看如果订单中已经添加了商品，不允许修改渠道部门
        $detailModel = new AppOrderDetailsModel(27);
        $detail_goods = $detailModel->getGoodsById($id);
        if(!empty($detail_goods) && $order_department_id != $department_id){
            $result['error'] = "此订单添加了商品，不可以再修改渠道部门！";
            Util::jsonExit($result);
        }
        //获取会员id,调接口
        $where = array('member_phone' => $mobile);
        $apiModel = new ApiMemberModel();
        $user_info = $apiModel->getMemberByPhone($where);

        //当没有此用户时，重新创建一个用户
        if ($user_info['error'] == 1) {

            $new_user_data = array(
                'member_name' => $user_name,
                'member_phone' => $mobile,
                'member_age' => 20,
                'member_type' => 1,
                'department_id' => $department_id,
                'customer_source_id' => $customer_source_id,
            );
            $res = $apiModel->createMember($new_user_data);
            $orderModel->setValue('user_id', $res['data']);
            if ($res['error'] > 0) {
                $result['error'] = 1;
                $result['error'] = "创建用户失败！";
                Util::jsonExit($result);
            }
        } else {
            /*if ($user_info['data']["member_phone"] == $mobile) {
                if ($user_info['data']["member_name"] != $user_name) {
                    $result['error'] = "手机号和名字需要一起修改！";
                    Util::jsonExit($result);
                }
            }*/
            $user_id = $user_info['data']['member_id'];
        }




        //订单信息
        $orderModel = new BaseOrderInfoModel($id, 28);
        $is_zp = isset($_POST['is_zp'])?1:0;
        if ($user_info['error'] == 1) {
            $orderModel->setValue('user_id', $res['data']);
        } else {
            $orderModel->setValue('user_id', $user_id);
        }
        $orderModel->setValue('consignee', $user_name);
        $orderModel->setValue('mobile', $mobile);
        $orderModel->setValue('department_id', $department_id);
        $orderModel->setValue('customer_source_id', $customer_source_id);
        $orderModel->setValue('order_remark', _Request::getString('order_remark'));
        $orderModel->setValue('is_zp', $is_zp);
        if($order_pay_status == 4){
            $orderModel->setValue('order_pay_status', $order_pay_status);
        }
        $orderModel->setValue('order_pay_type', $order_pay_type);
        $orderModel->setValue('recommended', $recommended);
        $orderModel->setValue('is_real_invoice', $is_real_invoice);
        $out_company   = 0;
        if(empty($is_real_invoice) && !empty($department_id)){		    
            $salesChannelsModel = new SalesChannelsModel(1);
            $salesChannelsInfo = $salesChannelsModel->getSalesChannelsInfo("id,channel_class",array('id'=>$department_id));
            $channel_class = 0;            
            if(isset($salesChannelsInfo[0]['channel_class'])){
                $channel_class = $salesChannelsInfo[0]['channel_class'];
            }
            if($channel_class == 2){	
                $companyModel = new CompanyModel(1);
                $out_company = $companyModel->select2("id","company_sn='5A'",3);    			
            }            
        }
        $orderModel->setValue('out_company', (int)$out_company);
        $res = $orderModel->save(true);
        if ($res) {
            //操作日志
            $action = array();
            $ation['order_id'] = $id;
            $ation['order_status'] = 1;
            $ation['shipping_status'] = 1;
            $ation['pay_status'] = 1;
            $ation['create_user'] = $_SESSION['userName'];
            $ation['create_time'] = date("Y-m-d H:i:s");
            $ation['remark'] = "修改订单";
            $logModel = new AppOrderActionModel(28);
            $logModel->saveData($action, array());
            $result['x_id'] = $id;
            $result['tab_id'] = _Request::getInt('tab_id');
            $result['success'] = 1;

        } else {
            $result['error'] = "修改订单失败！";
        }
        Util::jsonExit($result);
    }

    /**
     * 	delete，删除
     */
    public function delete($params) {
        $result = array('success' => 0, 'error' => '');
        $id = intval($params['id']);
        $model = new BaseOrderInfoModel($id, 28);
        $do = $model->getDataObject();
        $valid = $do['is_system'];
        if ($valid) {
            $result['error'] = "当前记录为系统内置，禁止删除";
            Util::jsonExit($result);
        }
        $model->setValue('is_deleted', 1);
        $res = $model->save(true);
        //联合删除？
        //$res = $model->delete();
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = "删除失败";
        }
        Util::jsonExit($result);
    }

    public function getDepartment() {
        $departmentModel = new DepartmentModel(1);
        $departmentInfo = $departmentModel->getDepartmentInfo("`id`,`name`", array('parent_id' => 3));

        $departmentData = array();
        foreach ($departmentInfo as $val) {
            $departmentData[$val['id']] = $val['name'];
        }
        $this->assign('departmentData', $departmentData);
    }

    public  function getShopHB($id=''){
        $model = new SalesChannelsModel(1);
        return  $model->getShopCid($id);
    }

    /**
     *  gendan，跟单人页面
     */
    public function gendan($params) {
        $ids = $params['_ids'];
        $model_s = new BaseOrderInfoModel(27);
        $model = new UserChannelModel(1);
        $makeOrder = array();
        if(!empty($ids)){
            foreach ($ids as $k => $id) {
                $dep_ids = $model_s->getOrderInfoById($id);
                $dep_id = $dep_ids['department_id'];
                $make_order = $model->get_user_channel_by_channel_id($dep_id);
                $mk = array_column($make_order,'account');
                $makeOrder = array_merge($makeOrder,$mk);
            }
            if(!empty($makeOrder)) $makeOrder = array_unique($makeOrder);
        }
        $this->render('base_order_gendan_info.html', array(
            'view' => new BaseOrderInfoView($model_s),
            'make_order' => $makeOrder,
            'tab_id' => _Request::getInt('tab_id'),
            '_ids' => implode(',', $ids)
        ));
    }

    /**
     *  gendanDo，分配跟单人
     */
    public function gendanDo($params) {
        $result = array('success' => 0,'error' =>'');
        $orderModel= new BaseOrderInfoModel(28);
        $ids = _Request::getString('_ids');
        $genzong = _Request::getString('genzong');
        if(!$ids){
            $result['error'] = "参数错误";
            Util::jsonExit($result);
        }
        if(empty($genzong)){
            $result['error'] = "跟单人不能为空！";
            Util::jsonExit($result);
        }
        $idsarr = explode(',', $ids);
        $res = $orderModel->updateOrderGenDanAction($idsarr,$genzong);
        if($res !== false)
        {
            $result['success'] = 1;
        }
        else
        {
            $result['error'] = '添加失败';
        }
        Util::jsonExit($result);
    }

    public function getCustomerSources() {
        //客户来源
        $CustomerSourcesModel = new CustomerSourcesModel(1);
        $CustomerSourcesList = $CustomerSourcesModel->getCustomerSourcesList("`id`,`source_name`");
        $this->assign('customer_source_list', $CustomerSourcesList);
    }

    /**
     * 	check 审核订单
     */
    public function check() {
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
            die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }
        $id = _Post::get('id');
        $result = array('success' => 0, 'error' => '');
        $model = new BaseOrderInfoModel($id, 28);
        $status = $model->getValue('order_status');
        $pay_status = $model->getValue('order_pay_status');
        $apply_close = $model->getValue('apply_close');
        $order_sn = $model->getValue('order_sn');
        $is_xianhuo = $model->getValue('is_xianhuo');
        $department = $model->getValue('department_id');
        $order_pay_type = $model->getValue('order_pay_type');
        $referer = $model->getValue('referer');
        //只有待审核状态才能审核
		if ($status == 4) {
            $result['error'] = '此订单已关闭，不能审核';
            Util::jsonExit($result);
        }
        if ($status != 1) {
            $result['error'] = '此订单已经审核，不能审核';
            Util::jsonExit($result);
        }
        if($apply_close == 1){
        	$result['error'] = '此订单已申请关闭，不能审核';
        	Util::jsonExit($result);
        }
        //判断地址表中 是否存在收获地址
        $ret = $model->getAddressByid($id);
        if (count($ret) < 1) {
            $result['error'] = "没有设置收获地址 不可以审核通过！";
            Util::jsonExit($result);
        }  else {
            $addressInfo = $model->getAddressInfo($ret[0]['id']);
            if(empty($addressInfo)){
                $result['error'] = "没有设置收获地址 不可以审核通过！";
                Util::jsonExit($result);
            }
        }
        //获取订单商品信息
        $orderDetailModel = new AppOrderDetailsModel(27);
        $goods_info = $orderDetailModel->getGoodsByOrderId(array('order_id' => $id));

		$is_zp=1;
        if (empty($goods_info)) {
            $result['error'] = '此订单还没有添加商品,请添加！';
            Util::jsonExit($result);
        }else{
            $empty_xiangqian = array();
            foreach($goods_info as $k=>$v){
                if( $v['is_zp'] != 1 && $v['goods_type'] != 'zp' && $v['xiangqian'] == ''){
                    $empty_xiangqian[] = $v['goods_sn'];
                }
				if($v['is_zp'] != 1){
					$is_zp=0;
				}
				if(!empty($v['cpdzcode'])){
				    $orderDetailModel->updateCpdzCode(array("use_status"=>3),"`code`='{$v['cpdzcode']}'");
				}
            }
            if(!empty($empty_xiangqian)){
                $result['error'] = '款号：'.implode(',',$empty_xiangqian).'没有填写镶嵌要求！';
                Util::jsonExit($result);
            }

        }
        //如果支付方式是财务备案将支付状态改成财务备案，但如果是京东部门且含有彩钻、裸钻商品，则为部分付款（支付定价）
        $payment_list = $this->getPaymentsBeiAn();
        $order = $model->getDataObject();
        $is_pay_part = $this->_isPayPart($order, $goods_info);
        if ($is_pay_part) {
            $pay_status = 2;
            $model->setValue('order_pay_status', $pay_status);
        } elseif (!empty($payment_list[$order_pay_type])){
            $pay_status = 4;
            $model->setValue('order_pay_status', $pay_status);
            $model->setValue('pay_date', date("Y-m-d H:i:s",time()));  //第一次点款时间
            if(!in_array(0,array_column($goods_info,'is_stock_goods'))&&$is_xianhuo==1){
                $model->setValue('delivery_status', 2);
            }
        }
        //审核后的状态为2
        $order_status = 2;

        $model->setValue('is_zp', $is_zp);
        $model->setValue('check_user', $_SESSION['userName']);
        $model->setValue('check_time', date('Y-m-d H:i:s'));
        $model->setValue('order_status', $order_status);
        //如果赠品单，订单支付状态变成已付款，第一次付款时间等于审核时间
        if($is_zp == 1){
            $pay_status = 3;
            $model->setValue('order_pay_status', $pay_status);
            $model->setValue('pay_date', date('Y-m-d H:i:s'));

			//获取未付金额
			$ret = $model->getAccountInfo($order_sn);
			if($ret['order_amount']==0){
				$finances = array_column($goods_info,'is_finance');
				$has_finance = empty(array_diff($finances, array('1'))) ? false : true;//获得赠品是否销账
				if ($has_finance){
					//赠品中有一个需要销账
					$model->setValue('delivery_status', 2);//配货状态自动变成【允许配货】
				}else{
					//赠品都不需要销账
					$model->setValue('delivery_status', 5); //配货状态需要自动变成【已配货】
					$model->setValue('send_good_status', 4); //发货状态需要自动 变成【允许发货】
				}
			}
        }
        
        $dep_name = $model->getNameByid($department);
        if ($model->save()) {
            if(strpos($dep_name,'店') === false && $referer == '补发单'){
                $ret = $model->getAccountInfo($order_sn);
                if($ret['order_amount']>=500){
                    $express_id = 4;
                }else{
                    $express_id = 19;
                }
                $model->updateExpressById($id,$express_id);
            }
            //订购类型是京东渠道-自有物流货到付款的 走京东快递
            if($order_pay_type == '246'){
                $express_id = 22;
                $model->updateExpressById($id,$express_id);
            }
            //财务备案支付方式的期货自动配货
            if(!empty($payment_list[$order_pay_type]) && $is_xianhuo==0){
                $result = $this->_allow_buchan($id);
                if ($result['success']==0) {
                    Util::jsonExit($result);
                }
            }

            //操作日志
            $ation['order_id'] = $id;
            $ation['order_status'] = $order_status;
            $ation['shipping_status'] = 1;
            $ation['pay_status'] = $pay_status;
            $ation['create_user'] = $_SESSION['userName'];
            $ation['create_time'] = date("Y-m-d H:i:s");
            $ation['remark'] = "审核订单";
            $model->addOrderAction($ation, $id);
            //往布产单中推送数据
            $result['success'] = 1;
            Util::jsonExit($result);
        } else {
            $result['error'] = '修改失败';
            Util::jsonExit($result);
        }
    }
    //判断是否是支付定金的订单，条件暂定：京东部门且含有彩钻、裸钻商品
    private function _isPayPart($order=array(), $order_infos=array()) {
        if ($order['department_id']==71) {
            foreach ($order_infos as $goods) {
                if ($goods['goods_type']=='lz' || $goods['goods_type']=='caizuan_goods') {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 	close 关闭订单
     */
    public function close() {
        $id = _Post::get('id');
        $result = array('success' => 0, 'error' => '');
        $model = new BaseOrderInfoModel($id, 28);
        $status = $model->getValue('order_status');
        $apply_close = $model->getValue('apply_close');
        $send_good_status = $model->getValue('send_good_status');
        $bespoke_id = $model->getValue('bespoke_id'); 
        $apply_return = $model->getValue('apply_return');
        $pay_status = $model->getValue('order_pay_status');
        //if($send_good_status!=1){
        //	$result['error'] = "订单已发货不能关闭";
        //	Util::jsonExit($result);
        //}
	/* 预约id可以为空 如婚博会或线上的订单 张园园*/
        if (empty($bespoke_id)) {
        	//$result['error'] = "预约id不能为空.";
        	//Util::jsonExit($result);
        }
        if ($status == 4) {
        	$result['error'] = "订单已经关闭";
        	Util::jsonExit($result);
        }
        if($apply_close != 1){
        	$result['error'] = "订单未申请关闭";
        	Util::jsonExit($result);
        }
        if($apply_return==2){
            $result['error'] = "订单正在退款中，不能关闭！";
            Util::jsonExit($result);
        }        
        if($send_good_status==2){
            //订单发货状态是已发货，必须退完货才能审核关闭
    	    $isReturnAll = $model->checkOrderGoodsReturnAll($id);
    	    if(!$isReturnAll){
        	    $result['error'] = "订单已经发货，请退完货后再关闭订单！";
        	    Util::jsonExit($result);
    	    }
        }else if($send_good_status==1){
            //未发货,支付方式是支付定金/已付款，必须取消点款或者退款才能关闭订单
            $accountInfo = $model->getOrderAccount($id); 
            //是否已全部退货
    	    $isReturnAll = $model->checkOrderGoodsReturnAll($id);
    	    //是否已全部退款
    	    $isReturnMoneyAll = $accountInfo['money_paid']==$accountInfo['real_return_price']?true:false;
    	    if($isReturnAll|| $isReturnMoneyAll){
    	        //已全部退完货，可以申请关闭
    	    }else if($pay_status==2 || $pay_status==3){
                $result['error'] = "支付定金/已付款，必须取消点款或者退款才能关闭订单！";
                Util::jsonExit($result);
            }
        }
  
        //关闭订单状态4
        $order_status = 4;
        $model->setValue('order_status', $order_status);
        $model->setValue('apply_close',0);
        $model->setValue('is_delete', 1);
        //找出这个单子所有的现货并把他们解除绑定并且上架
        $model->Bindxiajia($id,array('bind_type'=>2,'is_sale'=>1));
        if ($model->save()) {
    	    if($bespoke_id){
              /* 只有在bespoke_id 大于0 才去改变状态  */
    	      $AppOrderAddressModel=new AppOrderAddressModel(28);    
    	      $AppOrderAddressModel->updateBespokeDealStatus($bespoke_id);
    	    }
    	    //获取关联商品信息(货号列表)
    	    $orderDetailModel =new  AppOrderDetailsModel(28);
    	    $goodslist = $orderDetailModel->getGoodsByOrderId(array('order_id'=>$id));
    	    $goods_id_list = array_column($goodslist,'goods_id');
    	    $goods_id_list = implode('<br/>',$goods_id_list);
    	    
            //操作日志
            $ation['order_id'] = $id;
            $ation['order_status'] = $order_status;
            $ation['shipping_status'] = $send_good_status;
            $ation['pay_status'] = $pay_status;
            $ation['create_user'] = $_SESSION['userName'];
            $ation['create_time'] = date("Y-m-d H:i:s");
            $ation['remark'] = "订单申请关闭通过，货号自动解绑";
            if ($goods_id_list !='') {
                $ation['remark'].="<br/>关联货号:<br/>".$goods_id_list;
            }
            $model->addOrderAction($ation, $id);
            
            //对应订单的布产单,如果工厂还未开始生产(布产单布产状态是初始化\已分配\不需布产),布产单列表和详情的布产状态要变更成[已取消]，
            //而且布产单要生成一条新的日志
            $order_sn=$model->getValue('order_sn');
            $ProductInfoModel=new SelfProductInfoModel(14);
            $rel=$ProductInfoModel->updateBcStatus($order_sn);

            //查询订单中的明细是否有占用备货，有占用则取消占用
            $data = $orderDetailModel->selectBhInfo($order_sn);
            if(!empty($data)){
                foreach ($data as $val) {
                    $res = $orderDetailModel->countermandOccupy($val['detail_id']);
                    if($res == false){
                        $result['error'] = "取消占用备货失败！";
                        Util::jsonExit($result);
                    }
                }
            }
            
            $result['success'] = 1;
            Util::jsonExit($result);
        } else {
            $result['error'] = '修改关闭失败';
            Util::jsonExit($result);
        }
    }
    /**
     * 	close 申请关闭取消
     */
    public function rebutclose() {
    	$id = _Post::get('id');
    	$result = array('success' => 0, 'error' => '');
    	$model = new BaseOrderInfoModel($id, 28);
    	$status = $model->getValue('order_status');
    	$order_pay_status = $model->getValue('order_pay_status');
    	$apply_close = $model->getValue('apply_close');
		$send_good_status = $model->getValue('send_good_status');
    	if ($status==4) {
    		$result['error'] = "订单已审核关闭,不能申请关闭取消";
    		Util::jsonExit($result);
    	}
    	if($apply_close!=1){
    		$result['error'] = "订单未申请关闭";
    		Util::jsonExit($result);
    	}
    	$pay_status = $model->getValue('order_pay_status');
    	//关闭订单状态4
    	$model->setValue('order_status', $status);
    	$model->setValue('apply_close', 0);
    	$model->setValue('is_delete', 0);
    	if ($model->save()) {
    		//操作日志
    		$ation['order_id'] = $id;
    		$ation['order_status'] = $status;
    		$ation['shipping_status'] = $send_good_status;
    		$ation['pay_status'] = $pay_status;
    		$ation['create_user'] = $_SESSION['userName'];
    		$ation['create_time'] = date("Y-m-d H:i:s");
    		$ation['remark'] = "订单关闭取消";

    		$model->addOrderAction($ation, $id);
    		$result['success'] = 1;
    		Util::jsonExit($result);
    	} else {
    		$result['error'] = '修改失败';
    		Util::jsonExit($result);
    	}
    }
    //申请关闭 BOSS-612
    public function applyclose ($params)
    {
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
            die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }         
    	$result = array('success' => 0, 'error' => '','title'=>'申请关闭');
    	$id = intval($params["id"]);
    	$model = new BaseOrderInfoModel($id, 28);
    	$status = $model->getValue('order_status');
    	$order_pay_status = $model->getValue('order_pay_status');
		$apply_close = $model->getValue('apply_close');
		$send_good_status = $model->getValue('send_good_status');
		$apply_return = $model->getValue('apply_return');
        $department_id = $model->getValue('department_id');
		
		if ($status==4) {
		    $result['content'] = "订单已关闭！";
			Util::jsonExit($result);
		}
		if($apply_close==1){
    	    $result['content'] = "订单已申请关闭！";
    	    Util::jsonExit($result);
    	}
        if(empty($department_id)){
            $result['content'] = "订单渠道不明确！";
            Util::jsonExit($result);            
        }

        if($_SESSION['userType']<>1){
            $user_channel_model = new UserChannelModel(1);
            $user_channel_list = $user_channel_model->getChannels($_SESSION['userId'],0);
            if(empty($user_channel_list)){
                $result['content'] = "订单渠道不明确！";
                Util::jsonExit($result);               
            }else{
                if(!in_array($department_id,array_column($user_channel_list,'id'))){
                    $result['content'] = "没有订单渠道权限！";
                    Util::jsonExit($result);                      
                }
            }
        }   
		//非财务备案
		/* if ($order_pay_status !=4) {
			$account_info = $model->getOrderAccount($id);
			if(($account_info['money_paid'])!=0){
			    $result['content'] = "订单已付款不能申请关闭，请走退款/退货流程";
			    Util::jsonExit($result);
			}
		}  */
		if($apply_return==2){
		    $result['content'] = "订单正在退款中，不能关闭！";
		    Util::jsonExit($result);
		}
		if($send_good_status==2){
		    //订单发货状态是已发货，必须退完货才能审核关闭
    	    $isReturnAll = $model->checkOrderGoodsReturnAll($id);
    	    if(!$isReturnAll){
        	    $result['content'] = "订单已经发货，请退完货后再关闭订单！";
        	    Util::jsonExit($result);
    	    }
		}else if($send_good_status==1){
		    //未发货,支付方式是支付定金/已付款，必须取消点款或者退款才能关闭订单
            $accountInfo = $model->getOrderAccount($id); 
            //是否已全部退货
    	    $isReturnAll = $model->checkOrderGoodsReturnAll($id);
    	    //是否已全部退款
    	    $isReturnMoneyAll = $accountInfo['money_paid']==$accountInfo['real_return_price']?true:false;
    	    if($isReturnAll|| $isReturnMoneyAll){
    	        //已全部退完货，可以申请关闭
    	    }else if($order_pay_status==2 || $order_pay_status==3){
		        $result['content'] = "订单已付款不能申请关闭，请走退款/退货流程！";
		        Util::jsonExit($result);
		    }
		}
		/*$this->render('base_order_info_edit_check.html', array(
    	'view' => new BaseOrderInfoView($model)
    	));  */ 
		 $result['content'] = $this->fetch('base_order_info_edit_check.html', array(
    	'view' => new BaseOrderInfoView($model)
    	));
		Util::jsonExit($result);
    }
    //申请关闭信息入库BOSS-612
    public function insertclose ($params)
    {
    	$result = array('success' => 0, 'error' => '');
    	$id = intval($params["id"]);
    	$model = new BaseOrderInfoModel($id, 28);
    	$remark=$params["order_remark"];
    	$order_pay_status = $model->getValue('order_pay_status');
    	$order_status = $model->getValue('order_status');
    	$send_good_status = $model->getValue('send_good_status');
    	$apply_return = $model->getValue('apply_return');
    	$apply_close = $model->getValue('apply_close');
    	
    	if ($order_status==4) {
    	    $result['error'] = "订单已关闭！";
    	    Util::jsonExit($result);
    	}
    	if($apply_close==1){
    	    $result['error'] = "订单已申请关闭！";
    	    Util::jsonExit($result);
    	}
    	if($apply_return==2){
    	    $result['error'] = "订单正在退款中，不能关闭！";
    	    Util::jsonExit($result);
    	}
    	if($send_good_status==2){
    	    //订单发货状态是已发货，必须退完货才能审核关闭
    	    $isReturnAll = $model->checkOrderGoodsReturnAll($id);
    	    if(!$isReturnAll){
        	    $result['error'] = "订单已经发货，请退完货后再关闭订单！";
        	    Util::jsonExit($result);
    	    }
    	}else if($send_good_status==1){
    	    //未发货,支付方式是支付定金/已付款，必须取消点款或者退款才能关闭订单
            $accountInfo = $model->getOrderAccount($id); 
            //是否已全部退货
    	    $isReturnAll = $model->checkOrderGoodsReturnAll($id);
    	    //是否已全部退款
    	    $isReturnMoneyAll = $accountInfo['money_paid']==$accountInfo['real_return_price']?true:false;
    	    if($isReturnAll || $isReturnMoneyAll){
    	        //已全部退完货，可以申请关闭
    	    }else if($order_pay_status==2 || $order_pay_status==3){
    	        $result['error'] = "订单已付款不能申请关闭，请走退款/退货流程！";
    	        Util::jsonExit($result);
    	    }
    	}
    	$apply_close = 1;
    	$model->setValue('apply_close',$apply_close);
    	$res = $model->save();
    	if($res !== false){
    		//操作日志
    		$ation['order_id'] = $id;
    		$ation['shipping_status'] = 0;
    		$ation['order_status'] = $order_status;
    		$ation['pay_status'] = $order_pay_status;
    		$ation['shipping_status'] = $send_good_status;
    		$ation['remark'] = '申请关闭原因:'.$remark;
    		$ation['create_user'] = $_SESSION['userName'];
    		$ation['create_time'] = date("Y-m-d H:i:s");
			$model->addOrderAction($ation, $id);
			$result['success'] = 1;
			Util::jsonExit($result);
    	}else{
    		$result['error'] = '修改失败';
    		Util::jsonExit($result);
    	}
    }
    //订单日志
    public function showLogs() {
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            '_id' => _Request::get("id"),
            'appol' => _Request::get("appol"),
        );
        $shipping_status = array(0 => '未发货', 1 => '已发货', 2 => '已收货', 3 => '允许发货', 4 => '已到店');

        $page = _Request::getInt("page", 1);
        $where = array();
        $where['_id'] = $args['_id'];
        $model = new AppOrderActionModel(27);
        $orderModel = new BaseOrderInfoModel($where['_id'], 27);
        $where['hidden'] = $orderModel->getValue('hidden');
        $where['referer'] = $orderModel->getValue('referer');
		//$haveold = 0;
		$data = $model->pageList($where, $page, 25, false);
		//if(isset($where['_id']) && $where['_id'] <1935211 )
		//{
			
		//	$data = $model->pageOldList($where, $page, 25, false);
		//}
        //将刻字的特殊字符替换
        if($data['data']){
            $sups=$model->getAllSupplier();
            foreach ($data['data'] as $key => $value) {
                # code...
                $data['data'][$key]['remark'] = $this->replaceTsKezi($value['remark']);
                foreach ($sups as $k2 => $v) {
                    $data['data'][$key]['remark']=str_replace($v['name'],'***',$data['data'][$key]['remark']);
                } 

                $data['data'][$key]['remark'] =preg_replace("/跟单人由【(.*?)】改为【(.*?)】/is","跟单人由【***】改为【***】", $data['data'][$key]['remark'], 1);
                //$data['data'][$key]['remark'] =preg_replace("/跟单人：(.*?),/is","跟单人：*** ", $data['data'][$key]['remark'], 1);
                $data['data'][$key]['remark'] =preg_replace("/跟单人：((?!\,).)*/is","跟单人：*** ", $data['data'][$key]['remark'], 1);
            }
        }



        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'app_order_action_search_page';
        $this->render('app_order_action_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data,
            'shipping_status' => $shipping_status,
            'appol' => $args['appol'],
        ));
    }

    //订单日志
    public function showLogsed() {
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            '_id' => _Request::get("id"),
        );
        $shipping_status = array(0 => '未发货', 1 => '已发货', 2 => '已收货', 3 => '允许发货', 4 => '已到店');

        $page = _Request::getInt("page", 1);
        $where = array();
        $where['_id'] = $args['_id'];
        $basemodel = new BaseOrderInfoModel($where['_id'], 28);
        $where['hidden'] = $basemodel->getValue('hidden');
        $model = new AppOrderActionModel(27);
        $data = $model->getAll($where);

         $res=$this->fetch('app_order_action_search_list_fb.html',array('page_list'=>$data));
         Util::jsonExit($res);
    }

    public function getMemberByPhone() {
//     	echo 'getMemberByPhone';exit;
        $mobile = _Post::getInt('mobile');
        $where = array('member_phone' => $mobile);

        $apiModel = new ApiMemberModel();
        $user_info = $apiModel->getMemberByPhone($where);

        if ($user_info['error'] == 1) {
            Util::jsonExit(array('error' => 1));
        }

        $departmentModel = new DepartmentModel(1);
        $causeInfo = $departmentModel->getDepartmentInfo("`id`,`name`,`parent_id`", array('id' => $user_info['data']['department_id']));

        $user_info['data']['shiyebu_id'] = $causeInfo[0]['parent_id'];
        Util::jsonExit($user_info);
    }

    public function printorder() {
        //var_dump($_REQUEST);exit;
        $order_id = _Request::get('id');
        $model = new BaseOrderInfoModel(27);
        $orderinfo = $model->getOrderInfoById($order_id);
        // var_dump($orderinfo);exit;
        if ($orderinfo['order_status'] != 2) {
            $result['error'] = "只有已审核的订单才可以打印！";
            Util::jsonExit($result);
        }
        $model_address = new AppOrderAddressModel(27);
        $model_details = new AppOrderDetailsModel(27);
        $region = new RegionModel(1);
        $orderaccount = $model->getOrderAccount($order_id);
        $order_account = $model->getOrderPriceInfo($order_id);
        $orderinvoice = $model->getInvoiceByid($order_id);
        $orderaddressinfo = $model_address->getAddressById($order_id);
        // $orderaddressinfo['city_id']?$orderaddressinfo['city_id']:'';
        if (!empty($orderaddressinfo)) {
            $regionstr = $orderaddressinfo['province_id'] . ',' . $orderaddressinfo['city_id'] . ',' . $orderaddressinfo['regional_id'];
            $res = $region->getAddreszhCN($regionstr);
            $orderaddressinfo['country_name'] = $region->getRegionName($orderaddressinfo['country_id']);
            $orderaddressinfo['regionstr'] = $res . $orderaddressinfo['address'];
        }

        $orderdetailsinfo = $model_details->getGoodsInfoById($order_id);
        $zongjia = 0;
        foreach ($orderdetailsinfo as $key => $val) {
            if ($val['cart'] != '') {
                $orderdetailsinfo[$key]['pro']['石重'] = $val['cart'];
            }
            if ($val['cut'] != '') {
                $orderdetailsinfo[$key]['pro']['切工'] = $val['cut'];
            }
            if ($val['clarity'] != '') {
                $orderdetailsinfo[$key]['pro']['净度'] = $val['clarity'];
            }
            if ($val['color'] != '') {
                $orderdetailsinfo[$key]['pro']['颜色'] = $val['color'];
            }
            if ($val['zhengshuhao'] != '') {
                $orderdetailsinfo[$key]['pro']['证书号'] = $val['zhengshuhao'];
            }
            if ($val['caizhi'] != '') {
                $orderdetailsinfo[$key]['pro']['材质'] = $val['caizhi'];
            }
            if ($val['jinse'] != '') {
                $orderdetailsinfo[$key]['pro']['金色'] = $val['jinse'];
            }
            if ($val['jinzhong'] != '') {
                $orderdetailsinfo[$key]['pro']['金重'] = $val['jinzhong'];
            }
            if ($val['zhiquan'] != '') {
                $orderdetailsinfo[$key]['pro']['指圈'] = $val['zhiquan'];
            }
            if ($val['kezi'] != '') {
				$ke=new Kezi();
                $orderdetailsinfo[$key]['pro']['刻字'] =$ke->retWord($val['kezi']);//edit by zhangruiying文字替换成图片
            }
            if ($val['face_work'] != '') {
                $orderdetailsinfo[$key]['pro']['表面工艺'] = $val['face_work'];
            }
            if ($val['xiangqian'] != '') {
                $orderdetailsinfo[$key]['pro']['镶嵌要求'] = $val['xiangqian'];
            }
            $orderdetailsinfo[$key]['xiaoji'] = ($val['goods_price']-$val['favorable_price']) * $val['goods_count'];
            $zongjia+=$orderdetailsinfo[$key]['xiaoji'];
        }
        $print_time = date('Y-m-d H:i:s');
        //如果有尾款打印定制单，无尾款打印销售单
        //销售单
        //print_r($orderinfo);die;
        $orderinfo['title'] = "BDD货品销售单";
        $this->render('order_print.html', array(
            'print_time' => $print_time,
            'orderinfo' => $orderinfo ? $orderinfo : 0,
            'orderinvoice' => $orderinvoice ? $orderinvoice : 0,
            'orderaccount' => $orderaccount ? $orderaccount : 0,
            'order_account' => $order_account ? $order_account : 0,
            'addressinfo' => $orderaddressinfo ? $orderaddressinfo : 0,
            'detailsinfo' => $orderdetailsinfo ? $orderdetailsinfo : 0,
            'zongjia' => $zongjia,
        ));
    }

    //定制单
    public function printorder_dz() {
        $order_id = _Request::get('id');
        $model = new BaseOrderInfoModel(27);
        $orderinfo = $model->getOrderInfoById($order_id);
        // var_dump($orderinfo);exit;
        if ($orderinfo['order_status'] != 2) {
            $result['error'] = "只有已审核的订单才可以打印！";
            Util::jsonExit($result);
        }
        $model_address = new AppOrderAddressModel(27);
        $model_details = new AppOrderDetailsModel(27);
        $region = new RegionModel(1);
        $orderaccount = $model->getOrderAccount($order_id);
        $order_account = $model->getOrderPriceInfo($order_id);
        $orderinvoice = $model->getInvoiceByid($order_id);
        $orderaddressinfo = $model_address->getAddressById($order_id);
        // $orderaddressinfo['city_id']?$orderaddressinfo['city_id']:'';
        if (!empty($orderaddressinfo)) {
            $regionstr = $orderaddressinfo['province_id'] . ',' . $orderaddressinfo['city_id'] . ',' . $orderaddressinfo['regional_id'];
            $res = $region->getAddreszhCN($regionstr);
            $orderaddressinfo['country_name'] = $region->getRegionName($orderaddressinfo['country_id']);
            $orderaddressinfo['regionstr'] = $res . $orderaddressinfo['address'];
        }

        $orderdetailsinfo = $model_details->getGoodsInfoById($order_id);
        $zongjia = 0;
        foreach ($orderdetailsinfo as $key => $val) {
            if ($val['cart'] != '') {
                $orderdetailsinfo[$key]['pro']['石重'] = $val['cart'];
            }
            if ($val['cut'] != '') {
                $orderdetailsinfo[$key]['pro']['切工'] = $val['cut'];
            }
            if ($val['clarity'] != '') {
                $orderdetailsinfo[$key]['pro']['净度'] = $val['clarity'];
            }
            if ($val['color'] != '') {
                $orderdetailsinfo[$key]['pro']['颜色'] = $val['color'];
            }
            if ($val['zhengshuhao'] != '') {
                $orderdetailsinfo[$key]['pro']['证书号'] = $val['zhengshuhao'];
            }
            if ($val['caizhi'] != '') {
                $orderdetailsinfo[$key]['pro']['材质'] = $val['caizhi'];
            }
            if ($val['jinse'] != '') {
                $orderdetailsinfo[$key]['pro']['金色'] = $val['jinse'];
            }
            if ($val['jinzhong'] != '') {
                $orderdetailsinfo[$key]['pro']['金重'] = $val['jinzhong'];
            }
            if ($val['zhiquan'] != '') {
                $orderdetailsinfo[$key]['pro']['指圈'] = $val['zhiquan'];
            }
            if ($val['kezi'] != '') {
                $ke=new Kezi();
                $orderdetailsinfo[$key]['pro']['刻字'] =$ke->retWord($val['kezi']);//edit by zhangruiying文字替换成图片
            }
            if ($val['face_work'] != '') {
                $orderdetailsinfo[$key]['pro']['表面工艺'] = $val['face_work'];
            }
            if ($val['xiangqian'] != '') {
                $orderdetailsinfo[$key]['pro']['镶嵌要求'] = $val['xiangqian'];
            }
            $orderdetailsinfo[$key]['xiaoji'] = ($val['goods_price']-$val['favorable_price']) * $val['goods_count'];
            $zongjia+=$orderdetailsinfo[$key]['xiaoji'];
        }
        $print_time = date('Y-m-d H:i:s');
        //如果有尾款打印定制单，无尾款打印销售单
        //定制单
        $orderinfo['title'] = "BDD货品订制单";
        $this->render('order_printb5.html', array(
            'print_time' => $print_time,
            'orderinfo' => $orderinfo ? $orderinfo : 0,
            'orderinvoice' => $orderinvoice ? $orderinvoice : 0,
            'orderaccount' => $orderaccount ? $orderaccount : 0,
            'order_account' => $order_account ? $order_account : 0,
            'addressinfo' => $orderaddressinfo ? $orderaddressinfo : 0,
            'detailsinfo' => $orderdetailsinfo ? $orderdetailsinfo : 0,
            'zongjia' => $zongjia,
        ));

    }

    /*
     * 获取销售政策对应的商品
     */

    public function getSaleGoods() {
    	
//     	echo 'getSaleGoods';exit;
    	
        $error = 0;
        $result = array('success' => 0, 'error' => '');
        $goods_sn = _Request::getString('goods_sn');
        $department = _Request::getInt('department');

        if (empty($goods_sn)) {
            $result['error'] = 1;
            $result['content'] = "货号/款号不能为空!";
            Util::jsonExit($result);
        }
        if (empty($department)) {
            $result['error'] = 1;
            $result['content'] = "销售渠道不能为空!";
            Util::jsonExit($result);
        }
        $where = array('goods_id_in' => $goods_sn, 'department' => $department);
        $salePolicyModel = new ApiSalePolicyModel();
        $goods_info = $salePolicyModel->getAppSalepolicyGoodsInfo($where);
        if (empty($goods_info['data'])) {
            $result['error'] = 1;
            $result['content'] = "销售政策中没有此商品或已经下架!";
        } else {

            $result['error'] = 0;
            $result['content'] = "添加成功!";
        }
        Util::jsonExit($result);
    }

    /*
     * 获取销售政策对应的商品
     */

    public function getSaleGoods2() {
        $result = array('success' => 0, 'error' => '');
        $goods_sn = _Request::getString('goods_sn');
        $department = _Request::getInt('department');

        if (empty($goods_sn)) {
            $result['error'] = 1;
            $result['content'] = "货号/款号不能为空!";
            Util::jsonExit($result);
        }
        if (empty($department)) {
            $result['error'] = 1;
            $result['content'] = "销售渠道不能为空!";
            Util::jsonExit($result);
        }

        // $channel = 4;
        $where = array('goods_id_in' => $goods_sn, 'department' => $department);
        $salePolicyModel = new ApiSalePolicyModel();
        $data = $salePolicyModel->getAppSalepolicyGoodsInfo($where);
        if (empty($data['data'])) {
            $result['error'] = 1;
            $result['content'] = "销售政策中没有此货号信息";
            Util::jsonExit($result);
        }
        $cat_type = $data['data'][0]['category']; //款式分类
        $product_type = $data['data'][0]['product_type']; //产品线
        $is_xianhuo = $data['data'][0]['isXianhuo']; //1:现货 0:期货
        $chengjiaojia = $data['data'][0]['sale_price']; //销售价
        //如果是现货需要判断一下库存在下单
        $goods_info = array();
        if ($is_xianhuo == 1) {
            $apiWarehouseModel = new ApiWarehouseModel();
            $goods_info = $apiWarehouseModel->getWarehouseGoodsInfo(array('goods_id' => $goods_sn));
            if ($goods_info['error'] == 1 || empty($goods_info['data'])) {
                $result['error'] = 1;
                $result['content'] = "此货品没有库存";
                Util::jsonExit($result);
            }
            $new_goods_info = array();
            foreach ($goods_info['data'] as $val) {
                $new_goods_info['goods_name'] = $val['goods_name'];
                $new_goods_info['goods_sn'] = $val['goods_sn'];
                $new_goods_info['goods_cart'] = $val['zuanshidaxiao'];
                $new_goods_info['clarity'] = $val['clarity'];
                $new_goods_info['color'] = $val['yanse'];
                $new_goods_info['zhengshuhao'] = $val['zhengshuhao'];
                $new_goods_info['caizhi'] = $val['caizhi'];
                $new_goods_info['jinse'] = $val['jinse'];
                $new_goods_info['jinzhong'] = $val['jinzhong'];
                $new_goods_info['zhiquan'] = $val['shoucun'];
                $new_goods_info['kezi'] = $val['kezi'];
                $new_goods_info['xiangqian'] = $val['xiangqian'];
                $new_goods_info['chengjiaojia'] = $chengjiaojia;
                $new_goods_info['favorable_price'] = 0;
            }
        }

        //判断是裸钻,黄金，非黄金;展示不同的页面
        $styleModel = new ApiStyleModel();
        $goods_type = $styleModel->getProductLine($product_type);

        switch ($goods_type) {
            //足金
            case 'zj':
                if ($is_xianhuo == 1) {
                    $data_arr = $new_goods_info;
                } else {
                    $style_sn = $data['data'][0]['goods_sn'];
                    $styleModel = new ApiStyleModel();
                    $style_info = $styleModel->GetStyleAttribute(array('style_sn' => $style_sn));
                    if ($style_info['error'] == 1 || empty($style_info['data'])) {
                        $result['error'] = 1;
                        $result['content'] = "此款号没有属性";
                        Util::jsonExit($result);
                    }
                    //再所有属性中获取需要的
                    $data_arr = $this->getStyleAttribute($goods_sn, $style_info['data']);
                    $data_arr['chengjiaojia'] = $chengjiaojia;
                    $data_arr['favorable_price'] = 0;
                }

                $result['content'] = $this->fetch("order_xq_attribute.html", array(
                    'data_attr' => $data_arr,
                ));
                Util::jsonExit($result);
                break;
            //非足金
            case 'xq':
                if ($is_xianhuo == 1) {
                    $data_arr = $new_goods_info;
                } else {
                    $style_sn = $data['data'][0]['goods_sn'];
                    $styleModel = new ApiStyleModel();
                    $style_info = $styleModel->GetStyleAttribute(array('style_sn' => $style_sn));
                    //再所有属性中获取需要的
                    if ($style_info['error'] == 1 || empty($style_info['data'])) {
                        $result['error'] = 1;
                        $result['content'] = "此款号没有属性";
                        Util::jsonExit($result);
                    }
                    $data_arr = $this->getStyleAttribute($goods_sn, $style_info['data']);
                    $data_arr['chengjiaojia'] = $chengjiaojia;
                    $data_arr['goods_sn'] = $style_sn;
                    $data_arr['favorable_price'] = 0;
                }

                $result['content'] = $this->fetch("order_xq_attribute.html", array(
                    'data_attr' => $data_arr,
                ));
                Util::jsonExit($result);
                break;

            case 'lz':
                //裸钻
                $DiamondModel = new DiamondListModel();
                $goods_sn_arr = $DiamondModel->getRowByGoodSn($goods_sn);
                //var_dump($goods_sn_arr);die;
                if ($goods_sn_arr['error'] == 1 || empty($goods_sn_arr['data'])) {
                    $result['error'] = 1;
                    $result['content'] = "未查询到此裸钻";
                    Util::jsonExit($result);
                }
                $this->calc_dia_channel_price($goods_sn_arr['data']);
                $goods_sn_arr['data']['favorable_price'] = 0;
                $result['content'] = $this->fetch("order_lz_attribute.html", array(
                    'data_attr' => $goods_sn_arr['data'],
                ));

                $result['title'] = '裸钻';
                Util::jsonExit($result);
                break;
        }
    }

    /*
     * 获取销售政策对应的商品
     */

    public function getStyleAttribute($good_id, $style_info) {
        $sale_attribute_arr = array('goods_sn', 'cart', 'clarity', 'color', 'zhengshuhao', 'caizhi', 'jinse', 'jinzhong', 'zhiquan');
        //款式库货号组成：款号+材质+颜色+镶口+指圈
        $goods_id_arr = explode("-", $good_id);
        $goods_sn = $good_id;
        $caizhi = '';
        $color = '';
        $xiangkou = '';
        $zhiquan = '';

        //从货号上可以知道是属性
        $color_arr = array('W' => "白色", 'Y' => "黄色", 'R' => "红色", 'C' => "玫瑰色");
        $have_attribute = array('goods_sn' => $goods_sn, 'caizhi' => '', 'jinse' => '', 'cart' => '', 'zhiquan' => '', 'clarity' => '', 'color' => '', 'jinzhong' => '', 'zhengshuhao' => '');
        if (count($goods_id_arr) > 5) {
            $goods_sn = $goods_id_arr[0];
            $caizhi = $goods_id_arr[1];
            $color = $goods_id_arr[2];
            $xiangkou = $goods_id_arr[3] / 100;
            $zhiquan = $goods_id_arr[4];
            $have_attribute = array('goods_sn' => $goods_sn, 'caizhi' => $caizhi, 'jinse' => $color_arr[$color], 'cart' => $xiangkou, 'zhiquan' => $zhiquan, 'clarity' => '', 'color' => '', 'jinzhong' => '', 'zhengshuhao' => '');
        }


        $new_attribute_arr = array();
        foreach ($style_info as $key => $val) {
            $attribute_code = $val['attribute_code'];
            $attribute_value = $val['value'];
            if (in_array($attribute_code, $sale_attribute_arr)) {
                $new_attribute_arr[$attribute_code] = $attribute_value;
            }
        }

        //把没有是属性设成空
        foreach ($sale_attribute_arr as $val) {
            if (!array_key_exists($val, $new_attribute_arr)) {
                $new_attribute_arr[$val] = "";
            }
            if (array_key_exists($val, $have_attribute)) {
                $new_attribute_arr[$val] = $have_attribute[$val];
            }
        }
        return $new_attribute_arr;
    }


    /**
     * 查询订单信息
     */
    public function orderSearchByorder_sn(){
        $order_sn = _Post::getString('order_no');
        $model = new BaseOrderInfoModel(27);
        $res = $model->getOrderInfoBySn($order_sn);
        Util::jsonExit($res);
    }

    /*
     * 保存购物车中数据
     *
     */

    public function saveCartGoods() {
        $goods_id = _Request::getString('goods_id');
        $department = _Request::getInt('department');
        if (empty($goods_id)) {
            $result['error'] = "货号不能为空";
            Util::jsonExit($result);
        }

        //判断销售政策中是否有此款商品
        $where = array('department' => $department, 'goods_sn' => $goods_id);

        $apiModel = new ApiSalePolicyModel();
        $data = $apiModel->getGoodsInfo($where);
        if (empty($data['data'])) {
            $result['error'] = "销售政策中没有此货号信息！";
            Util::jsonExit($result);
        }

        $cat_type = $data['data'][0]['category']; //款式分类
        $product_type = $data['data'][0]['product_type']; //产品线
        $is_xianhuo = $data['data'][0]['isXianhuo']; //1:现货 0:期货
        $chengjiaojia = $data['data'][0]['sale_price']; //销售价
        //如果是现货则只能下一个
        $cartModel = new AppOrderCartModel(27);
        $cartList = $cartModel->get_cart_goods();
        if ($is_xianhuo == 1) {
            foreach ($cartList as $val) {
                if ($val['goods_id'] == $goods_id) {
                    $result['error'] = "此货品是现货，已经在购物车里！";
                    Util::jsonExit($result);
                }
            }

            //如果是现货需要判断一下库存在下单
            $apiWarehouseModel = new ApiWarehouseModel();
            $goods_info = $apiWarehouseModel->getWarehouseGoodsInfo(array('goods_id' => $goods_id));
            if ($goods_info['error'] == 1 || empty($goods_info['data'])) {
                $result['error'] = "此货品没有库存！";
                Util::jsonExit($result);
            }
        }

        //判断是裸钻,黄金，非黄金;展示不同的页面
        $styleModel = new ApiStyleModel();
        $goods_type = $styleModel->getProductLine($product_type);
        //获取商品数据
        $goods_sn = isset($_REQUEST['goods_sn']) ? $_REQUEST['goods_sn'] : '';
        $goods_name = isset($_REQUEST['goods_name']) ? $_REQUEST['goods_name'] : '';
        $cart = isset($_REQUEST['cart']) ? $_REQUEST['cart'] : '';
        $clarity = isset($_REQUEST['clarity']) ? $_REQUEST['clarity'] : '';
        $color = isset($_REQUEST['color']) ? $_REQUEST['color'] : '';
        $zhengshuhao = isset($_REQUEST['zhengshuhao']) ? $_REQUEST['zhengshuhao'] : '';
        $caizhi = isset($_REQUEST['caizhi']) ? $_REQUEST['caizhi'] : '';
        $jinse = isset($_REQUEST['jinse']) ? $_REQUEST['jinse'] : '';
        $jinzhong = isset($_REQUEST['jinzhong']) ? $_REQUEST['jinzhong'] : '';
        $zhiquan = isset($_REQUEST['zhiquan']) ? $_REQUEST['zhiquan'] : '';
        $kezi = isset($_REQUEST['kezi']) ? $_REQUEST['kezi'] : '';
        $face_work = isset($_REQUEST['face_work']) ? $_REQUEST['face_work'] : '';
        $xiangqian = isset($_REQUEST['xiangqian']) ? $_REQUEST['xiangqian'] : '';
        $cut = isset($_REQUEST['cut']) ? $_REQUEST['cut'] : '';
        $favorable_price = isset($_REQUEST['favorable_price']) ? $_REQUEST['favorable_price'] : '';
        if ($favorable_price) {
            if ($chengjiaojia <= $favorable_price) {
                $result['error'] = "优惠金额大于成交价！";
                Util::jsonExit($result);
            }
        }
        $goods_count = 1;
        $apiCartModel = new AppOrderCartModel(27);
        switch ($goods_type) {
            case 'xq';

                break;
            case 'zj':
                break;
            case 'lz';
                $DiamondModel = new DiamondListModel(19);
                $goods_arr = $DiamondModel->getRowByGoodSn($goods_id);

                if ($goods_arr['error'] == 1 || empty($goods_arr['data'])) {
                    $result['error'] = "未查询到此裸钻";
                    Util::jsonExit($result);
                }
                $this->calc_dia_channel_price($goods_arr['data']);
                $goods_id = $goods_arr['data']['goods_sn'];
                $goods_sn = $goods_arr['data']['goods_sn'];
                $goods_name = $goods_arr['data']['goods_name'];
                $chengjiaojia = $goods_arr['data']['shop_price'];
                $cart = $goods_arr['data']['carat'];
                $clarity = $goods_arr['data']['clarity'];
                $color = $goods_arr['data']['color'];
                $cut = $goods_arr['data']['cut'];
                $goods_count = $goods_arr['data']['goods_number'];
                $zhengshuhao = $goods_arr['data']['cert_id'];
                break;
        }
        //保存购物车
        $goods_info['session_id'] = DBSessionHandler::getSessionId();
        $goods_info['goods_id'] = $goods_id;
        $goods_info['goods_sn'] = $goods_sn;
        $goods_info['goods_name'] = $goods_name;
        $goods_info['goods_price'] = $chengjiaojia;
        $goods_info['goods_count'] = $goods_count;
        $goods_info['create_time'] = date("Y-m-d H:i:s");
        $goods_info['create_user'] = $_SESSION['userName'];
        $goods_info['modify_time'] = date("Y-m-d H:i:s");
        $goods_info['is_stock_goods'] = $is_xianhuo;
        $goods_info['goods_type'] = $goods_type;

        $goods_info['cut'] = $cut;
        $goods_info['cart'] = $cart ? $cart : 0;
        $goods_info['clarity'] = $clarity;
        $goods_info['color'] = $color;
        $goods_info['zhengshuhao'] = $zhengshuhao;
        $goods_info['caizhi'] = $caizhi;
        $goods_info['jinse'] = $jinse;
        $goods_info['jinzhong'] = $jinzhong ? $jinzhong : 0;
        $goods_info['zhiquan'] = $zhiquan ? $zhiquan : 0;
        $goods_info['kezi'] = $kezi;
        $goods_info['face_work'] = $face_work;
        $goods_info['xiangqian'] = $xiangqian;
        $goods_info['favorable_price'] = $favorable_price;

        $res = $cartModel->add_cart($goods_info);

        if ($res !== false) {
            $result['success'] = 1;
            $result['error'] = '添加成功';
        } else {
            $result['error'] = "添加商品失败";
        }
        Util::jsonExit($result);
    }

    //删除购物车中商品
    public function deleteCartGoods() {
        $result = array('success' => 0, 'error' => '');
        $cart_id = _Request::getInt('id');
        $cartModel = new AppOrderCartModel(27);
        $res = $cartModel->delete_cart_goods_by_id($cart_id);
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = "操作失败";
        }
        Util::jsonExit($result);
    }

	//允许布产
    public function allow_buchan($param) {
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
            die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }        
        $order_id = isset($param['id']) ? $param['id'] : 0;
        $order_ids = isset($param['_ids']) ? $param['_ids'] : array();
        if ($order_ids) {
            foreach($order_ids as $id) {
                $result = $this->_allow_buchan($id);
                // 有一个不成功，就提示失败
                if (empty($result['success'])) break;
            }
        } else {
            $result = $this->_allow_buchan($order_id);
        }
        Util::jsonExit($result);
    }

    //允许布产
    private function _allow_buchan($order_id) {
        $result = array('success' => 0, 'error' => '');
        $model = new BaseOrderInfoModel($order_id, 27);
        $orderInfo = $model->getDataObject();
        $status = $model->getValue('order_status');
        $order_sn = $model->getValue('order_sn');
        if($status != 2){
            $result['error'] = $order_sn."：未审核的订单不能允许布产";
            return $result;
        }
        $pay_status = $model->getValue('order_pay_status');

        //已付款的才可以允许布产
        if($pay_status == 1 && $orderInfo['referer'] != '婚博会'){
            $result['error'] = $order_sn."：未付款，不可以布产！";
            return $result;
        }

        //获取订单商品中的期货
        $detailModel = new AppOrderDetailsModel(27);
        //订单商品全部布产不允许布产
        $bc_status=$detailModel->getGoodsBcStatus($order_id);
        if(empty($bc_status)) {
            $result['error'] = $order_sn."：订单所有商品已经生成布产单,如果需要重新布产,请到布产列表重新提交！";
            return $result;
        }
        // 查询需要布产的期货准备布产
        $order_detail_data = $detailModel->getGoodsByOrderId(array('order_id'=>$order_id,'is_stock_goods'=>0,'is_buchan'=>2));
        //商品中是否有占用备货货品
        $is_beihuo = false;
        // 判断是否可以配货，替代布产
        foreach ($order_detail_data as $k=>$goods) {
            if (empty($goods['goods_sn'])) continue;

            //判断是否已经备货商品，是则跳过不需布产
            $bindBH = $detailModel->getOutOrderInfo($goods['id']);
            if(!empty($bindBH)){
                $is_beihuo = true;
                unset($order_detail_data[$k]);
                continue;
            }

            $peihuo = $detailModel->getOrderWarehouseGoods($goods);
            if (isset($peihuo['goods_id'])) {
                // 1. 改订单商品为现货，同时绑定配货goods_id
                $data = array('goods_id'=>$peihuo['goods_id'], 'is_stock_goods'=>1);
                $falg = $detailModel->updateOrderDetailsGoodsById($data, $goods['id']);
                if ($falg) {
                    // 2. 改仓库 order_goods_id = order_detail_id
                    $detailModel->bindOrderWarehouseGoods($goods['id'],  $peihuo['id']);
                    // 3. 记录日志订单商品转现货
                    $this->activeLog($orderInfo, "订单商品【{$goods['goods_sn']}】配货成功，已转为现货{$peihuo['goods_id']}");
                    unset($order_detail_data[$k]);
                }
            }
        }
        // 配货后如果没有期货则该订单为现货单，否则剩下的期货进行布产
        if (empty($order_detail_data) && !$is_beihuo) {
            // 1. 记录日志
            $this->activeLog($orderInfo, "订单所有期货都配货成功，已改订单为现货单！");
            // 2. 订单转为 现货类型
            $_model = new BaseOrderInfoModel($order_id, 28);
            $_model->setValue('is_xianhuo', 1);
            $_model->setValue('delivery_status', 2);
            $res = $_model->save();
            if ($res !== false) {
                $result['success'] = 1;
            } else {
                $result['error'] = $order_sn."：配货操作失败";
            }
        } else {
            // 1.配货后剩下的期货 进行布产
            $bc_ret = $this->AddBuchanDan($orderInfo,$order_detail_data);
            if($bc_ret['error'] == 1){
                $result['error'] = $bc_ret['data'];
                return $result;
            }

            //2.获取布产单号，回写入订单商品
            if($bc_ret['data']){
                $a = $bc_ret['data'];
                $detailsModel = new AppOrderDetailsModel(28);
                $pdo28 = $detailsModel->db()->db();
                try{
                $pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
                $pdo28->beginTransaction(); //开启事务
                foreach($a as $va){
                	$res1=$detailsModel->updateOrderDetailsBcidById($va['id'], $va['buchan_sn']);
                	if(!$res1){
                		$pdo28->rollback(); //事务回滚
                		$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                		$result['error'] = "商品明细Id".$va['id']."：布产操作失败";
                		return $result;
                	}
                }
                
                $pdo28->commit(); //事务提交
                $pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
               }
                catch(Exception $e){//捕获异常
                	//  print_r($e);exit;
                	$pdo28->rollback(); //事务回滚
                	$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动回滚
                	$row['error']=1;
                	$row['data']= "数据异常，布产操作失败。".$e;
                
                		
                }
                /*
                foreach($a as $va){
                    $detailsModel = new AppOrderDetailsModel($va['id'],28);
                    $detailsModel->setValue('bc_id', $va['buchan_sn']);
                    $detailsModel->save();
                }
                */
                $buchan_sn = implode(",",array_column($a,'final_bc_sn'));
                //写入日志
                $this->activeLog($orderInfo, "订单允许布产成功！布产单号为：".$buchan_sn);
            }

            // 3. 改订单布产状态
            $_model = new BaseOrderInfoModel($order_id, 28);
            $_model->setValue('effect_date', date("Y-m-d H:i:s"));
            $_model->setValue('buchan_status', 2);//变成允许布产
            $res = $_model->save();
            if ($res !== false) {
                $result['success'] = 1;
            } else {
                $result['error'] = $order_sn."：布产操作失败";
            }
        }

        return $result;
    }

    //获取购物车数据
    public function getCartGoods() {
    	
//     	echo 'getCartGoods';exit;
    	
        $cartModel = new AppOrderCartModel(27);
        $cart_list = $cartModel->get_cart_goods();
        
//         echo "<pre>";
//         print_r($cart_list);
        $this->render('order_cart_goods_list.html', array('cart_list' => $cart_list));
    }

    //清空购物车
    public function clearCartGoods() {
        $result = array('success' => 0, 'error' => '');
        $cartModel = new AppOrderCartModel(27);
        $res = $cartModel->clear_cart();
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = "操作失败";
        }
        Util::jsonExit($result);
    }

    //把定制商品进行布产
    public function AddBuchanDan($orderinfo,$detail_goods){
        $order_id           = $orderinfo['id'];
        $order_sn			= $orderinfo['order_sn'];
        $consignee			= $orderinfo['consignee'];
		$customer_source_id = $orderinfo['customer_source_id'];
		$department_id		= $orderinfo['department_id'];
        $order_remark = $orderinfo['order_remark'];
        $out_order_sn = '';//boss_1246
        if($customer_source_id == 2946){
            $model = new BaseOrderInfoModel(27);
            $ret = $model->getOurOrderSn($order_id);
            if(!empty($ret)){
                $out_order_sn = $ret[0]['out_order_sn'];
            }
        }
        
		//判断是否独立销售 is_alone
		//有指圈号(zhiquan)&&证书号(zhengshuhao)不是 K||c  基本上都是空托      ==>单独售卖
		//goods_type = lz 一定是祼钻                                        ==>单独售卖
		if($orderinfo['is_xianhuo'] == 0){
		    $is_lz_order = 0;
		    foreach ($detail_goods as $x){
		        if($x['goods_type'] == 'lz'){//判断订单里是否有祼钻
		            $is_lz_order = 1;
		        }
		    }
		    foreach ($detail_goods as $k=>$v){
		        $detail_goods[$k]['is_alone'] = 0;
		        preg_match('/^(W|M)-(\w+)-(\w{1})-(\d+)-(\d{2})$/',$detail_goods[$k]['goods_id'],$matches);
		        if($v['goods_type'] == 'lz'){//单独售卖
		            $detail_goods[$k]['is_alone'] = 1;
		        }elseif( !empty($matches))  {//戒托
		            $detail_goods[$k]['is_alone'] = 1;
		        }elseif( empty($v['zhengshuhao']) && $is_lz_order)  {//如果是祼钻订单则独立售卖
		            $detail_goods[$k]['is_alone'] = 1;
		        }elseif( empty($v['zhengshuhao']) && !$is_lz_order)  {//证书号为空 且非祼钻订单
		            
		        }elseif( strripos($v['zhengshuhao'],'K') !== false || strripos($v['zhengshuhao'],'C') !== false)  {//成品售卖
		            
		        }else{
		            $orderDetailModel = new AppOrderDetailsModel(27);
                    if(!empty(trim($v['zhengshuhao']))){
                        $goods_info = $orderDetailModel->getGoodsInfoByZhengshuhao($v['zhengshuhao'],$v['id']);
                        if($goods_info){//单独售卖
                            $detail_goods[$k]['is_alone'] = 1;
                        }
                    } 
		        }
		    }
		}
		$SelfProductInfoModel=new SelfProductInfoModel(13);
		$ProductInfoModel=new SelfProductInfoModel(14);
		$SelfDiamondModel=new SelfDiamondModel(19);
		
        //$processorApiModel = new ApiProcessorModel();
        //$diamondApiModel  = new ApiDiamondModel();
        //找到此订单是否已经存在布产的单
        $attr_names =array('cart'=>'主石单颗重','zhushi_num'=>'主石粒数','clarity'=>'主石净度','color'=>'主石颜色','cert'=>'证书类型','zhengshuhao'=>'证书号','caizhi'=>'材质','jinse'=>'金色','jinzhong'=>'金重','zhiquan'=>'指圈','kezi'=>'刻字','face_work'=>'表面工艺');
        if(!empty($detail_goods)){
            $goods_arr = array();
            foreach($detail_goods as $key=>$val){
                if($val['is_stock_goods'] == 1 && empty($val['is_peishi'])){
                    continue;
                }
                $detail_id = $val['id'];
                //查看此商品是否已经开始布产
                //$buchan_info = $processorApiModel->GetGoodsRelInfo($detail_id);
                /*
                $buchan_info = $processorApiModel->CheckGoodsProductInfo($detail_id,$order_sn);
                if(!empty($buchan_info['data'])){
                    continue;
                }
                */
                $buchan_info=$SelfProductInfoModel->CheckGoodsProductInfo($detail_id,$order_sn);
                if(!empty($buchan_info)){
                	continue;
                }
                /* if($buchan_info['error']==0){
                    continue;
                } */
                $new_style_info = array();
                foreach ($attr_names as $a_key=>$a_val){
                    $xmp['code'] = $a_key;
                    $xmp['name'] = $a_val;
                    $xmp['value'] = $val[$a_key];
                    $new_style_info[] = $xmp;
                }
                //boss_1246
                if($customer_source_id == 2946){//鼎捷需要

                    $new_style_info[] = array('code'=>'p_sn_out', 'name'=>'外部单号', 'value'=>$out_order_sn);
                }
                $goods_num = $val['goods_count'];
                
                if($val['is_peishi']==1){
                    $zhengshuhao = $val['zhengshuhao'];
                    /*
                    $ret = $diamondApiModel->getDiamondInfoByCertId($zhengshuhao);
                    if($ret['error']==0){
                        $goods_arr[$key]['diamond'] = $ret['data'];
                    }else{
                        $result['error'] = "货号为{$val['goods_id']}的商品证书号不存在";
                        Util::jsonExit($result);
                    }
                    */
                    
                    
                    $ret = $SelfDiamondModel->getDiamondInfoByCertId($zhengshuhao);
                    if($ret){
                    	$goods_arr[$key]['diamond'] = $ret;
                    }else{
                    	$result['error'] = "货号为{$val['goods_id']}的商品证书号不存在";
                    	Util::jsonExit($result);
                    }
                    
                }
                $diamodel = new SelfDiamondModel(19);
                $cert_id2 = preg_replace('/[a-zA-Z]{0,10}/', '', $val['zhengshuhao']);
                $goods_type = $diamodel->getGoodsTypeByCertId($val['zhengshuhao'],$cert_id2);
                if($val['zhengshuhao'] == ''){
                    $diamond_type = 1;
                }else{
                    if($goods_type ==2){
                        //期货钻
                         $diamond_type =2;
                    }else{
                        $diamond_type =1; 
                    }
                }
                /* 规则改动，暂时注释
                $diamodel = new SelfDiamondModel(11);
                $WarehouseModel = new SelfWarehouseGoodsModel(21);
                        $is_exists = $WarehouseModel->isExistsByGoodsId($val['goods_id']);
                        if(empty($val['goods_id']) && empty($val['zhengshuhao'])){
                                            //货号和证书号都为空，就是现货
                            $diamond_type =1;
                                
                        }else{
                            if($is_exists){
                            //现货钻
                               $diamond_type =1;

                            }else{
                                //货号没找到，通过证书号去裸钻列表查找判断是期货还是现货
                                $cert_id2 = preg_replace('/[a-zA-Z]{0,10}/', '', $val['zhengshuhao']);
                                $goods_type = $diamodel->getGoodsTypeByCertId($val['zhengshuhao'],$cert_id2);
                                if($goods_type==2){
                                    //期货钻
                                     $diamond_type =2;
                             
                                }else{
                                    //现货钻
                                   $diamond_type =1;
                                   }
                                }
                            }       */
                $goods_arr[$key]['origin_dia_type']=$diamond_type;
                $goods_arr[$key]['diamond_type']=$diamond_type;
                $goods_arr[$key]['p_id'] =	$detail_id;
                $goods_arr[$key]['p_sn'] =  $order_sn;
                $goods_arr[$key]['style_sn'] = $val['goods_sn'];
                $goods_arr[$key]['goods_name'] = $val['goods_name'];
                $goods_arr[$key]['bc_style'] = empty($val['bc_style'])?'普通件':$val['bc_style'];
                $goods_arr[$key]['xiangqian'] = $val['xiangqian'];
                $goods_arr[$key]['goods_type'] = $val['goods_type'];
                $goods_arr[$key]['cat_type'] = $val['cat_type'];
                $goods_arr[$key]['product_type'] = $val['product_type'];
                $goods_arr[$key]['num'] = $goods_num;
                $goods_arr[$key]['info'] = $val['details_remark'];
                $goods_arr[$key]['consignee'] = $consignee;
                $goods_arr[$key]['attr'] = $new_style_info;
				$goods_arr[$key]['customer_source_id'] = $customer_source_id;
				$goods_arr[$key]['channel_id'] = $department_id;
				//$goods_arr[$key]['create_user']=$orderinfo['create_user'];
                //把布产单的创建人从订单制单人修改成点击允许布产的操作人   add liuri by 20150605
				$goods_arr[$key]['create_user']=$_SESSION['userName'];
				$goods_arr[$key]['is_peishi'] = $val['is_peishi'];
				$goods_arr[$key]['is_alone'] = $val['is_alone'];
				$goods_arr[$key]['qiban_type'] = $val['qiban_type'];
                $goods_arr[$key]['out_order_sn'] = $out_order_sn;
                $goods_arr[$key]['caigou_info'] = $order_remark;

                //按照款号+材质+材质颜色+指圈+镶口来判断 是否快速定制
                $is_quick_diy = 0;
                //print_r($val);exit;
                if(!empty($val['goods_sn']) && !empty($val['caizhi']) && !empty($val['jinse']) && !empty($val['zhiquan']) && !empty($val['xiangkou'])){
                    $where  = " style_sn = '".$val['goods_sn']."' and caizhi = '".$val['caizhi']."' and caizhiyanse = '".$val['jinse']."' and  zhiquan = ".$val['zhiquan']." and xiangkou = ".$val['xiangkou']." ";
                    $res= $ProductInfoModel->get_app_style_quickdiy($where);
                    if(!empty($res)){
                        $is_quick_diy = 1;
                    }

                }
               // echo $is_quick_diy;exit;
                $goods_arr[$key]['is_quick_diy'] = $is_quick_diy;

                //end
            }
			//var_dump($goods_arr);exit;
            $res = array('data'=>'','error'=>0);
            //添加布产单
            if(!empty($goods_arr)){
                //$res = $processorApiModel->AddProductInfo($goods_arr);
               $res = $ProductInfoModel->AddProductInfo($goods_arr);
            }
            //$res['buchan_info'] = $buchan_info;
            //$res['goods_arr'] = $goods_arr;
            return $res;
        }
    }
    //修改赠品页面
    public function EditGift(){
        $order_id =_Request::getInt('_id');
        $model = new BaseOrderInfoModel(27);
        $giftaz =$model->getGifts($order_id);
        if(empty($giftaz)){
            $giftaz['gift_id']='';
            $giftaz['gift_num']='';
            $giftaz['remark']='';
        }
            $gifta = explode(',',$giftaz['gift_id']);
            $giftn = explode(',',$giftaz['gift_num']);
            $giftt =  array_combine($gifta,$giftn);
        $result['content'] = $this->fetch('base_order_info_gifts.html',array(
            'gifts'=>$this->gifts,'_id'=>$order_id,'gifta'=>$gifta,'giftt'=>$giftt,'giftremark'=>$giftaz['remark'],
        ));
        $result['title'] = "修改赠品";
        Util::jsonExit($result);
    }

    //修改赠品
    public function UpdateGift(){
        $result = array('success' => 0, 'error' => '');
        $order_id = _Request::getInt('order_id');
        $gift_ids = _Request::getList('gift_id');
        $gift_num = _Request::getList('gift_num');
        //print_r($order_id);
        //print_r($gift_ids);
        //print_r($gift_num);die;
        $gift_nums ='';
        foreach($gift_ids as $k=>$v){
            if(array_key_exists($v,$gift_num)){
                $gift_nums.=$gift_num[$v].',';
            }
        }
        $gift_nums=trim($gift_nums,',');
        $giftida2 = array('gift_id'=>implode(',',$gift_ids),'remark'=>_Request::getString('gift_remark'),'gift_num'=>$gift_nums);
        $gift_ids = array('gift_id'=>implode(',',$gift_ids),'order_id'=>$order_id,'gift_num'=>$gift_nums,'remark'=>_Request::getString('gift_remark'));
        $orderModel = new BaseOrderInfoModel(28);
        $giftida =$orderModel->getGifts($order_id);
        $res = $orderModel->updateGifts($order_id,$gift_ids);
        if(!$res){
            $result['error']="修改失败";
        }else{
            if(empty($giftida)){
                $giftida = array('gift_id'=>'','remark'=>'','gift_num'=>'');
            }
            $remarkl = $giftida['remark'];
            $remarkr = $giftida2['remark'];
            unset($giftida['remark'],$giftida2['remark']);
            //var_dump($giftida,$giftida2);die;
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
            if(!empty($giftida2)){
                $giftids2= explode(',',$giftida2['gift_id']);
                $giftidn2= explode(',',$giftida2['gift_num']);
                $giftstr2='';
                foreach($giftids2 as $k=>$v){
                    if(array_key_exists($v,$this->gifts)){
                        $giftstr2.=$this->gifts[$v].$giftidn2[$k].'个&nbsp;';
                    }
                }
            }else{
                $giftstr2='';
            }
            $str_list = '';
            if($giftstr != $giftstr2 || $remarkl != $remarkr){
                if($giftstr != $giftstr2){
                    if($giftstr == ''){
                        $giftstr = '无';
                    }
                    if($giftstr2 == ''){
                        $giftstr2 = '无';
                    }
                    $str_list .= "赠品：{$giftstr},<br/>改为{$giftstr2};<br/>";
                }
                if($remarkl != $remarkr){
                    if($remarkr == ''){
                        $remarkr = '无';
                    }
                    $str_list .= "赠品备注：{$remarkr}";
                }
                $order_info = $orderModel->getOrderInfoById($order_id);
                    $logInfo = array(
                    'order_id'=>$order_info['id'],
                    'order_status'=>$order_info['order_status'],
                    'shipping_status'=>$order_info['send_good_status'],
                    'pay_status'=>$order_info['order_pay_status'],
                    'create_user'=>$_SESSION['userName'],
                    'create_time'=>date("Y-m-d H:i:s"),
                    'remark'=>$str_list,
                );
                //写入订单日志
                $orderModel->addOrderAction($logInfo);
            }
            //print_r($srt_list);die;
            //var_dump($giftstr,$giftstr2);die;
            $result['success']=1;
        }
        Util::jsonExit($result);
    }
    //修改价格页面
    public function EditAccount(){
        $order_id =_Request::getInt('_id');
        $order_model = new BaseOrderInfoModel(27);
        $order_account = $order_model->getOrderAccount($order_id);
        $result['content'] = $this->fetch('base_order_info_account.html',array(
            '_id'=>$order_id,'order_account'=>$order_account
        ));
        $result['title'] = "修改赠品";
        Util::jsonExit($result);
    }
    //修改价格
    public function UpdateAccount(){
        $result = array('success' => 0, 'error' => '');
        $order_id= _Request::getInt('order_id');
        if(empty($order_id)){
            $result['error']="没有获取到订单禁止提交";
        }
        $udata['order_id']=$order_id;
        $udata['order_amount']= _Request::getInt('order_amount');
        $udata['goods_amount']= _Request::getInt('goods_amount');
        $udata['real_return_price']= _Request::getInt('real_return_price');
        $udata['favorable_price']= _Request::getInt('favorable_price');
        $udata['coupon_price']= _Request::getInt('coupon_price');
        $udata['shipping_fee']= _Request::getInt('shipping_fee');
        $udata['insure_fee']= _Request::getInt('insure_fee');
        $udata['pack_fee']= _Request::getInt('pack_fee');
        $udata['card_fee']= _Request::getInt('card_fee');
        $udata['money_paid']= _Request::getInt('money_paid');
        $udata['money_unpaid']= _Request::getInt('money_unpaid');
        $orderModel = new BaseOrderInfoModel(28);
        $res = $orderModel->updateOrderAccount($udata);
        if(!$res){
            $result['error']="修改失败";
        }else{
            $result['success']=1;
        }
        Util::jsonExit($result);
    }


    /**
     * 	dingzhi
     */
    public function dingzhi() {
        $id = _Post::get('id');
        $result = array('success' => 0, 'error' => '');
        $model = new BaseOrderInfoModel($id, 28);
        $status = $model->getValue('order_status');
        $apply_close = $model->getValue('apply_close');
        $channel= $model->getValue('department_id');
        //只有审核状态才能操作转为定制单
        if ($status != 2) {
            $result['error'] = '此订单未审核，不能操作';
            Util::jsonExit($result);
        }
        if($apply_close == 1){
        	$result['error'] = '此订单已申请关闭，不能操作';
        	Util::jsonExit($result);
        }
        $detailsModel = new AppOrderDetailsModel(27);
        $orderDetailsInfo = $detailsModel->getGoodsInfoById($id);
       
        $castable = 1; //能否转换
        $qihuo_exist = 0; //是否存在期货
        $api_sales_policy_model = new ApiSalePolicyModel();
        foreach ($orderDetailsInfo as $val){
            if($val['is_stock_goods']==0){
                $qihuo_exist = 1;
                 
                if($val['goods_type']=='style_goods' || $val['goods_type']=='warehouse_goods'){
                    $ck_result = $api_sales_policy_model->checkGoodsDingzhiCastable($val,$channel);
                    if (!$ck_result['flag']){
                        $castable = 0;
                        $result['error'] = $ck_result['error'];
                        break;
                    }
                }
            }
        }
        
        if (!$qihuo_exist) {
            $result['error'] = '此订单内无期货商品，不能操作';
            Util::jsonExit($result);
        }
        
        if(!$castable){
            Util::jsonExit($result);
        }
        
        //转为定制单
        $model->setValue('is_xianhuo', 0);
        if ($model->save() !== false) {
            $result['success'] = 1;
            Util::jsonExit($result);
        } else {
            $result['error'] = '修改失败';
            Util::jsonExit($result);
        }
    }



    /**
     * 	 xianhuo
     */
    public function xianhuo() {
        $id = _Post::get('id');
        $result = array('success' => 0, 'error' => '');
        $model = new BaseOrderInfoModel($id, 28);
        $status = $model->getValue('order_status');
        $apply_close = $model->getValue('apply_close');
        //只有审核状态才能操作转为现货单
        if ($status != 2) {
            $result['error'] = '此订单未审核，不能操作';
            Util::jsonExit($result);
        }
        if($apply_close == 1){
        	$result['error'] = '此订单已申请关闭，不能操作';
        	Util::jsonExit($result);
        }

        $detailsModel = new AppOrderDetailsModel(27);
        $orderDetailsInfo = $detailsModel->getGoodsInfoById($id);
        $flag = 0;
        foreach ($orderDetailsInfo as $val){
            if($val['is_stock_goods']==0){
                $flag = 1;
                break;
            }
        }
        if($flag){
            $result['error'] = '此订单内有期货商品，不能操作';
        	Util::jsonExit($result);
        }

        //转为现货单
        $model->setValue('is_xianhuo', 1);
        if ($model->save() !== false) {
            $result['success'] = 1;
            Util::jsonExit($result);
        } else {
            $result['error'] = '修改失败';
            Util::jsonExit($result);
        }
    }

    /**
     * 获取该销售渠道关联的支付方式
     * @param type $param
     */
    public function getPayMentList($param) {
        $channel_id = $param['channel_id'];
        $data = array('is_show'=>1,'content'=>'');
        if($channel_id){
            //获取所有直营店列表
            $zhiyingdianList = $this->getAllShop();
            $zhiyingdianIds = implode(',', array_column($zhiyingdianList,'id'));
            //获取直营店的销售渠道
            $channelModel = new SalesChannelsModel(1);
            $saleChannelList = $channelModel->getSaleChannelList($zhiyingdianIds);
            $saleChannelIds = array_column($saleChannelList,'id');
            if(in_array($channel_id, $saleChannelIds)){
                $model =  new RelChannelPayModel(1);
                $arr = $model->getPayMentList($channel_id);
                $length = count($arr);
                if($length==1){
                    $str = "<option value='".$arr[0]['pay_id']."' selected='selected'>".$arr[0]['pay_name']."</option>";
                    $data['is_show'] = 0;
                    $data['select'] = $arr[0]['pay_id'];
                }else if($length > 1){
                    $str = '<option value=""></option>';
                    foreach ($arr as $val){
                        $str .= "<option value='".$val['pay_id']."'>".$val['pay_name']."</option>";
                    }
                }else{
                    $paylist=$this->GetPaymentInfo();
                    $str = '<option value=""></option>';
                    foreach ($paylist as $val){
                        $str .= "<option value='".$val['pay_id']."'>".$val['pay_name']."</option>";
                    }
                }
            }else{
                $data['is_show'] = 99;
                $paylist=$this->GetPaymentInfo();
                $str = '<option value=""></option>';
                foreach ($paylist as $k=>$val){
                    $str .= "<option value='".$k."'>".$val."</option>";
                }
            }
            $data['content'] = $str;
        }
        echo json_encode($data);
    }

    public function CopeOrderInfo(){
        $result = array('success' => 0, 'error' => '');
        $id = _Post::get('id');
        if(empty($id)){
            $result['error']='获取订单id错误';
            Util::jsonExit($result);
        }

        $model = new BaseOrderInfoModel(27);
        $res =  $model->CopyOrderInfo($id);
        if($res){
            $result['success']=1;
            $result['error']=$res;
        }else{
            $result['error']='生成补发单失败';
        }
        Util::jsonExit($result);
    }



    public function AddPay ()
    {
        $result['content'] = $this->fetch('external_pay.html',array('order_id'=>_Request::getInt('_id')));
        $result['title'] = "修改金额";
        Util::jsonExit($result);
    }

    /**
     *	insert，信息入库
     */
    public function OutPayinsert ($params)
    {
        $result = array('success' => 0,'error' =>'');
        $error=array(1=>'外部订单出错',2=>'淘宝订单状态未处于等待发货状态不能付款',3=>'支付金额不等于实际支付金额',4=>'流水号已经支付过',5=>'获取BDD订单的信息失败',6=>'保存到财务失败',7=>'支付订单更改失败',8=>'BDD订单没有关联外部订单',9=>'外部订单信息不符合不能支付');
        $taobao_order_sn =_Request::getString('exter_order_num');
        $order_sn =_Request::getString('order_sn');
        $price =_Request::getFloat('exter_order_price');
        //获取BDD订单的信息
        $model = new BaseOrderInfoModel(28);
        $order_info =$model->getOrderInfoaByid(_Request::getInt('order_id'));
        if(empty($order_info)){
            $result['error']='获取BDD订单的信息失败';
            Util::jsonExit($result);
        }
        if($price>$order_info['money_unpaid']){
            $result['error']='支付金额超过BDD订单未付价格请核对！';
            Util::jsonExit($result);
        }
        if($order_info['order_status']!=2){
            $result['error']='BDD订单 未处于审核状态不允许支付';
            Util::jsonExit($result);
        }
        $from_type=$order_info['department_id'];
        $file_path = APP_ROOT."sales/modules/".$this->from_arr[$from_type]["api_path"]."/index.php";
        if(!file_exists($file_path)){
            $result['error']='接口文件不存在';
            Util::jsonExit($result);
        }
        require_once($file_path);
        $apiM = $this->from_arr[$from_type]["api_path"];
        $api_order = new $apiM();
        /* 支付 */
        //生成一个支付凭据
        $date = date("Ymd");
        $header='DK-KLSZFGS-'.$date;
        $receipt_id = rand(0,999);
        $nes = str_pad($receipt_id,4,'0',STR_PAD_LEFT);
        $bonus_code=$header.$nes;
        $order_info['bonus_code']=$bonus_code;

        $r = $api_order -> outer_order_pay($order_info, $taobao_order_sn, $price);
        if(!is_array($r)){
            $result['error']=$error[$r];
        }else{
            $result['success']=1;
            //写入日志
            $orderActionModel = new AppOrderActionModel(27);
            //操作日志
            $ation['order_status'] = $order_info['order_status'];
            $ation['order_id'] = $r['order_id'];
            $ation['shipping_status'] = $order_info['send_good_status'];
            $ation['pay_status'] = $r['pay_stu'];
            $ation['create_user'] = $_SESSION['userName'];
            $ation['create_time'] = date("Y-m-d H:i:s");
            $ation['remark'] = "外部订单[$taobao_order_sn]，通过外部订单支付了$price 元";
            $orderActionModel->saveData($ation, array());
            $result['success'] = 1;
            $result['error']=$order_sn;
        }
        Util::jsonExit($result);
    }

//获取该外部订单的应支付的金额
    public function GetOutPrice(){
        $out_order_sn=_Request::get('exter_order_num');
        //这里只限淘宝2
        $apiarr = $this->from_arr[2];
        $file_path = APP_ROOT."sales/modules/".$apiarr["api_path"]."/index.php";
        if(!file_exists($file_path)){
            return;
        }
        require_once($file_path);
        $apiM = $this->from_arr[2]["api_path"];
        $api_order = new $apiM();
        $info = $api_order->get_order_info($out_order_sn);
        $arr= array();
        if(trim($info -> code)){
            $arr['exter_order_price']='';
        }else{
            $price =(float) $info -> trade -> payment;
            $arr['exter_order_price']=$price;
        }
        $orderM=new BaseOrderInfoModel(27);
        $orderinfo  = $orderM->checkOrderByWhere($out_order_sn);
        if(!empty($orderinfo)){
            $arr['order_sn']=$orderinfo['order_sn'];
        }else{
            $arr['order_sn']='';
        }
        Util::jsonExit($arr);
    }


    //写入订单操作日志
    private function activeLog($orderInfo, $remark='') {
        $orderActionModel = new AppOrderActionModel(28);
        $ation['order_status'] = $orderInfo['order_status'];
        $ation['order_id'] = $orderInfo['id'];
        $ation['shipping_status'] = $orderInfo['send_good_status'];
        $ation['pay_status'] = $orderInfo['order_pay_status'];
        $ation['create_user'] = $_SESSION['userName'];
        $ation['create_time'] = date("Y-m-d H:i:s");
        $ation['remark'] = $remark;
        $orderActionModel->saveData($ation, array());
    }
    /**
     * 取消订单审核
     * @param $params
     */
    public function cancelCheck($params){
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
            die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }        
        $result = array('success' => 0,'error' =>'');
        $order_id = _Request::getInt("id");
        if(!$order_id){
            $result['error']  = "订单Id参数错误！";
            Util::jsonExit($result);
        }        
        $model = new BaseOrderInfoModel($order_id,27);
        $olddo = $model->getDataObject();
        if(empty($olddo)){
            $result['error']  = "订单查询失败！order_id={$order_id}";
            Util::jsonExit($result);
        }
        $order_status = $olddo['order_status'];
        $order_pay_status = $olddo['order_pay_status'];
        $delivery_status  = $olddo['delivery_status'];
        //$bc_id = $olddo['bc_id'];
        $is_xianhuo = $olddo['is_xianhuo'];
        $is_zp = $olddo['is_zp'];
        $send_good_status = $olddo['send_good_status'];
        if($order_status!=2){
            $result['error']  = "订单不是已审核状态，不能进行取消审核操作！";
            Util::jsonExit($result);
        }
        
        if(in_array($order_pay_status,array(2,3))){
            $result['error']  = "订单已经有付款记录，请先找财务取消点款，然后再来操作。";
            Util::jsonExit($result);
        }else if($order_pay_status==4){
            $result['error']  = "财务备案订单不允许取消审核。";
            Util::jsonExit($result);                    
        }
        
        $model->setValue('order_status',1);//等待审核状态
        $res = $model->save();
        if($res){
            
            $ation['order_id'] = $order_id;
            $ation['order_status'] = 1;
            $ation['shipping_status'] = $send_good_status;
            $ation['pay_status'] = $order_pay_status;
            $ation['create_user'] = $_SESSION['userName'];
            $ation['create_time'] = date("Y-m-d H:i:s");
            $ation['remark'] = "取消订单审核";
            $model->addOrderAction($ation, $order_id);
            
            $result['success']  = 1;
            Util::jsonExit($result);
        }else{
            $result['error']  = "取消订单审核失败。";
            Util::jsonExit($result);
        }
    }

    /**
     *  edit，添加客诉信息
     */
    public function add_complaint($params) 
    {
        $result = array('success' => 0, 'error' => '');
        $id = intval($params["id"]);

        $model_feedback = new AppOrderFeedbackModel(27);
        $feedbackList = $model_feedback->get_feedback_list();

        $model = new BaseOrderInfoModel($id, 27);
        $viewModel = new BaseOrderInfoView($model);

        $complaintModel = new AppOrderComplaintModel(27);
        $complaintInfo = $complaintModel->getComplaintInfobyOrder_id($id);

        $imginfo = array();
        if(!empty($complaintInfo)){

            $imginfo = array_column($complaintInfo, 'cl_url');
        }

        $this->render('base_order_info_complaint.html', array(
            'view' => $viewModel,
            'tab_id' => _Request::getInt('tab_id'),
            'feedbackList' => $feedbackList,
            'imginfo' => $imginfo
        ));
    }


    public function ins_complaint($params)
    {
        # code...
        $result = array('success' => 0, 'error' => '');

        $cl_feedback_id = _Post::getInt('cl_feedback_id');
        $cl_other = _Post::getString('cl_other');

        if($cl_feedback_id == '0'){

            $result['error'] = '提示：<span style="color:red">请选择客诉原因！</span>';
            Util::jsonExit($result);
        }

        if(trim($cl_other) == ''){

            $result['error'] = '提示：<span style="color:red">请填写客诉备注！</span>';
            Util::jsonExit($result);
        }

        $infoComplaint = array(
            'order_id'=>$params['id'],
            'cl_feedback_id'=>$cl_feedback_id,
            'cl_other'=>$cl_other,
            'cl_user'=>$_SESSION['userName'],
            'cl_time'=>date('Y-m-d H:i:s')
            );

        //图片文件上传
        $upload = new Upload();
        if(isset($_FILES['cl_url'])){

            $l_ext = Upload::getExt($_FILES['cl_url']['name']);
            if(!in_array($l_ext,$upload->img)){
                $result['error'] = "图片文件不符合类型！";
                Util::jsonExit($result);
            }
            $cl_url = $upload->toUP($_FILES['cl_url']);
            if(is_array($cl_url)){
                $infoComplaint['cl_url'] = $cl_url['url'];
            }else{
                $result['error'] = $cl_url;
                Util::jsonExit($result);
            }
        }

        $newmodel = new BaseOrderInfoModel(28);
        $res = $newmodel->ins_complaint_list($infoComplaint);

        if($res){
            $result['success'] = 1;
        }else{
            $result['error'] = '添加客诉情况失败!';
        }
        Util::jsonExit($result);
    }

    public function getChannelIdByClass() {
        //渠道
        $channel_class = _Request::get('channel_class');
        $model = new UserChannelModel(1);
        $data = $model->getChannels_class($_SESSION['userId'],0,$channel_class);
        if(empty($data)){
            die('请先联系管理员授权渠道!');
        }
        $this->render('option.html',array(
                'sales_channels_idData'=>$data
        ));
    }

    //判断当前用户是否为店长
    function checkPermissions(){
        $SalesChannelsModel = new SalesChannelsModel(1);
        $shopArr=$SalesChannelsModel->getShopCid();
        $userName = $_SESSION['userName'];
        $is_check=1;
        foreach ($shopArr as $k=>$v){
            //取得当前用户id,userName
            //判断当前用户是否为店长
            $dp_leader_name = explode(',', $v['dp_leader_name']);
    
            //销售顾问名称
            $dp_people_name = explode(',', $v['dp_people_name']);
            if(!in_array($userName, $dp_leader_name)&&in_array($userName, $dp_people_name)){
                $is_check=0;
            }
        }
        return $is_check;
    }
    
    function can_goto_sale() {
    	// 如果当前公司是直营或个体性质，则需要先领单
    	if (SYS_SCOPE == 'boss') {
    		$company_type = '1';
    	} else {
    		$companyId = $_SESSION['companyId'];
    		$companyModel = new CompanyModel(1);
    		$company_type = $companyModel->select2("company_type","id={$companyId}",3);
    	}
    	// 3为经销商性质
    	if ($company_type <> '3') {
    		if (!isset($_SESSION['bespoke'])) {
    			return false;
    		}
    	}
    	
    	return true;
    }



  //用于删除测试订单，暂时用用
    public function delorder() {
        if(isset($_POST['order_sn']) && !empty($_POST['order_sn'])){
            $model = new BaseOrderInfoModel(51);
            $order_sn = $_POST['order_sn'];
            $sql = "select * from app_order.base_order_info where order_sn='{$order_sn}'";
            $row = $model->db()->getRow($sql);
            if(empty($row)){
                exit('没有这个订单');
            }
            $consignee = $row['consignee'];
            if(strpos($consignee,'测试') === false){
               exit('不是测试订单，不能删除');
            }
            $order_id = $row['id'];

            //查询是否是否有状态是已销售的商品,并更新为库存
            $sql ="SELECT g.goods_id FROM app_order.app_order_details d,warehouse_shipping.warehouse_goods g WHERE g.goods_id = d.goods_id AND g.is_on_sale = 3 AND d.order_id={$order_id};";
            $row = $model->db()->getAll($sql);

            if(!empty($row)){
                $goods_id_str = join(',',array_column($row,'goods_id'));
                $sql = "update warehouse_shipping.warehouse_goods set is_on_sale =2,order_goods_id='' WHERE goods_id in($goods_id_str)";
                //echo $sql.'<br/>';
                $model->db()->query($sql);
            }



            $sql = "DELETE  FROM app_order.base_order_info WHERE order_sn='{$order_sn}' ";
            //echo $sql.'<br/>';
            $model->db()->query($sql);

            $sql = "DELETE  FROM app_order.app_order_account  WHERE order_id='{$order_id}' ";
            //echo $sql.'<br/>';
            $model->db()->query($sql);

            $sql = "DELETE  FROM app_order.app_order_details WHERE order_id='{$order_id}' ";
           // echo $sql.'<br/>';
            $model->db()->query($sql);

            //点款
            $sql = "DELETE  FROM finance.app_order_pay_action WHERE order_sn='{$order_sn}' ";
            //echo $sql.'<br/>';
            $model->db()->query($sql);

            $sql = "DELETE  FROM finance.app_receipt_pay WHERE order_sn='{$order_sn}' ";
            //echo $sql.'<br/>';
            $model->db()->query($sql);


            //查询是否退款
            $sql ="SELECT return_id FROM app_order.app_return_goods WHERE order_id={$order_id};";
            $row = $model->db()->getAll($sql);
            if(!empty($row)){
                $sql = "DELETE  FROM app_order.app_return_goods WHERE order_id={$order_id} ";
                //echo $sql.'<br/>';
                $model->db()->query($sql);

                $return_id_str = join(',',array_column($row,'return_id'));
                $sql = "DELETE  FROM app_order.app_return_check WHERE return_id in ({$return_id_str}) ";
                //echo $sql.'<br/>';
                $model->db()->query($sql);
            }


            //查询是否D单和S单
            $sql ="SELECT id FROM warehouse_shipping.warehouse_bill  WHERE order_sn='{$order_sn}' and bill_type in('D','S')";
            $row = $model->db()->getAll($sql);
            if(!empty($row)){
                $sql = "DELETE  FROM warehouse_shipping.warehouse_bill WHERE order_sn='{$order_sn}' and bill_type in('D','S')";
                //echo $sql.'<br/>';
                $model->db()->query($sql);

                $bill_id_str = join(',',array_column($row,'id'));
                $sql = "DELETE  FROM warehouse_shipping.warehouse_bill_goods WHERE bill_id in ({$bill_id_str}) ";
                //echo $sql.'<br/>';
                $model->db()->query($sql);
            }
            echo '删除成功';




        }else{
            $this->render('delete_order_form.html');
        }

    }
}

?>
