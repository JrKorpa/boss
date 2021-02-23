<?php

/**
 *  -------------------------------------------------
 *   @file		: AppOrderPayActionController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-19 18:16:49
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderPayActionController extends CommonController {

    protected $smartyDebugEnabled = false;
    protected $whitelist = array('printorder','printorder_dz');

    /**
     * 	index，搜索框
     */
    public function index($params) {
        $SalesChannelsModel = new SalesChannelsModel(1);
        if($_SESSION['userType'] == 1){
            $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
        }else{
            $ids = explode(',', $_SESSION['qudao']);
            $channellist = $SalesChannelsModel->getSalesChannel($ids);
        }
        $this->render('app_order_pay_action_search_form.html', array('bar' => Auth::getBar(),'sales_channels_idData' => $channellist));
    }

    /**
     * 	search，列表
     */
    public function search($params) {
	   $department = _Request::getInt('order_department')?_Request::getInt('order_department'):0;
       if($_SESSION['userType']!=1){
			$model = new UserChannelModel(1);
			$data = $model->getChannels($_SESSION['userId'],0);
			$myDepartment = array();
			foreach($data as $key => $val){
				$myDepartment[]=$val['id'];
			}
			if(empty($department)){
				$department = implode(",",$myDepartment);
			}else{
				if(!in_array($department,$myDepartment)){
					$department = implode(",",$myDepartment);
				}
			}
        }
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'order_sn' => _Request::getString('order_sn'),
            'consignee' => _Request::getString('consignee'),
            'order_pay_status' => _Request::getInt('order_pay_status'),
            'mobile' => _Request::getInt('mobile'),
            'start_time' => _Request::getString('start_time'),
            'end_time' => _Request::getString('end_time'),
            'order_status' => _Request::getInt('order_status'),
            'order_id' => _Request::getString('order_id'),
        );

        $page = _Request::getInt("page", 1);
        $where = array();
        $where['order_id'] = $args['order_id'];
        $where['order_sn'] = $args['order_sn'];
        $where['consignee'] = $args['consignee'];
        $where['mobile'] = $args['mobile'];
        $where['start_time'] = $args['start_time'];
        $where['end_time'] = $args['end_time'];
        $where['order_status'] = $args['order_status'];
        $where['page'] = $page;
        $where['page_size'] = 25;
        $where['order_pay_status'] = $args['order_pay_status'];
        $where['no_order_status'] = 1;//不取无效的
		if($args['order_sn'] == '' && $args['consignee']=='' && $args['mobile'] == ''){
			$where['order_department'] = $department;
		}

        $model = new ApiOrderModel(29);
        $data = $model->pageList($where);

        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'app_order_pay_action_search_page';
        $this->render('app_order_pay_action_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data
        ));
    }

    /**
     * 	add，渲染添加页面
     */
    public function add() {
        $paymentModel = new PaymentModel(1);
        $paymentList = $paymentModel->getList();
		$saleChannelmodel = new UserChannelModel(1);
		//协助收款
        $qudao = array();
        if(_Request::getString('flag')){
            //$qudao = $saleChannelmodel->getChannels(13206,0);
            $qudao = $saleChannelmodel->getChannels($_SESSION['userId'],0);
        }
        $result['content'] = $this->fetch('app_order_pay_action_info_add.html', array(
            'paymentList'=>$paymentList,'qudaoList'=>$qudao
        ));
        Util::jsonExit($result);
    }

	/**
	*	payPrice, 点款
	*/
    public function payPrice($param) {
        $result = array('success' => 0, 'error' => '');
        $order_id = _Request::getInt('id');
        
		$paymentModel = new PaymentModel(1);
		$paymentList = $paymentModel->getList();
        if(!empty($paymentList)){
            foreach ($paymentList as $k => $v) {
                if($v['is_enabled'] == 0){
                    unset($paymentList[$k]);
                }
            }
        }
        $jumpurl = "index.php?mod=finance&con=AppOrderPayAction&act=index";
        $menuModel = new MenuModel(1);
        $menu = $menuModel->getMenuId($jumpurl);   
        $model = new ApiOrderModel(29);
        $data = $model->getOrderList($order_id);
        $result['title'] = '错误';
        //当前订单所属渠道
        $department_id = $data['department_id'];
        //当前用户所处渠道
		$saleChannelmodel = new UserChannelModel(1);
		$qudao = $saleChannelmodel->getChannels($_SESSION['userId'],0);
		//$qudao = $saleChannelmodel->getChannels(13206,0);
		if(empty($qudao)){
			die('请先联系管理员授权渠道!');
		}
        
        $qudaoList = array();
        $flag = 0;
        if(!in_array($department_id, array_column($qudao,'id'))){
            $qudaoList = $qudao;
            $flag = 1;
        }
        if($data['order_status'] ==1){
            die("此订单未审核");
        }elseif ($data['order_status'] ==3) {
            die("此订单已取消");
        }elseif ($data['order_status'] ==4) {
            die("此订单已关闭");
        }elseif($data['order_pay_status']==3){
            die("此订单已付全款");
        }elseif($data['apply_close']==1){
			die("此订单已申请关闭，不能审核");
		}elseif($data['money_unpaid']==0){
            $this->render('app_order_pay_action_info_sure.html', array(
                'order_id' => $order_id,
                'menu' => $menu,
            ));
            $result['title'] = '点款确认';
        }else{
            $this->render('app_order_pay_action_info.html', array(
                'order_id' => $order_id,
                'qudaoList' => $qudaoList,
                'flag'=>$flag,
                'paymentList'=>$paymentList,
                'data'=>$data,
                'menu' => $menu,
            ));
            $result['title'] = '点款';
        }

    //elseif(!empty($data['out_order_sn'])){
    //        die("存在外部单号不能点款");
    //    }

        //Util::jsonExit($result);
//            $result['content'] = $this->fetch('app_order_pay_action_info_sure.html', array(
//                'order_id' => $order_id,
//            ));
//            $result['title'] = '点款确认';
//        }else{
//            $result['content'] = $this->fetch('app_order_pay_action_info.html', array(
//                'order_id' => $order_id,
//                'paymentList'=>$paymentList,
//                'data'=>$data,
//            ));
//            $result['title'] = '点款';
//        }
//
//        Util::jsonExit($result);
    }

    /**
     * 	edit，渲染修改页面
     */
    public function edit($params) {
        $id = intval($params["id"]);
        $tab_id = intval($params["tab_id"]);
        $result = array('success' => 0, 'error' => '');
        $result['content'] = $this->fetch('app_order_pay_action_info.html', array(
            'view' => new AppOrderPayActionView(new AppOrderPayActionModel($id, 29)),
            'tab_id' => $tab_id
        ));
        $result['title'] = '编辑';
        Util::jsonExit($result);
    }

    /**
     * 	show，渲染查看页面
     */
    public function show($params) {
        $id = intval($params["id"]);
        $this->render('app_order_pay_action_show.html', array(
            'view' => new AppOrderPayActionView(new AppOrderPayActionModel($id, 29)),
            'bar' => Auth::getViewBar()
        ));
    }

    /**
     * 0元点款
     * @param type $param
     */
    public function payZero($param) {
        $result = array('success' => 0, 'error' => '');
        $order_id = _Request::getInt('id');
        $orderModel = new ApiOrderModel();
        $data = $orderModel->getOrderList($order_id);
        
		/*boss-457*/
        /*
		if (!in_array($_SESSION['userName'], array('董小华','徐飞燕','张敏敏','admin','张卿','林陶夏','聂伯健'))) {
			$result['error'] = '对不起，您没有该操作权限';
            Util::jsonExit($result);
		}*/
		
        if($data['order_status'] ==1){
            $result['error'] = '此订单未审核，不能操作';
            Util::jsonExit($result);
        }elseif ($data['order_status'] ==3) {
            $result['error'] = '此订单已取消，不能操作';
            Util::jsonExit($result);
        }elseif ($data['order_status'] ==4) {
            $result['error'] = '此订单已关闭，不能操作';
            Util::jsonExit($result);
        }elseif($data['order_pay_status']==3){
            $result['error'] = '此订单已付全款，不能操作';
            Util::jsonExit($result);
        }elseif($data['order_pay_status']==2){
            $result['error'] = '此订单为支付定金，不能操作';
            Util::jsonExit($result);
        }elseif($data['apply_close']==1){
            $result['error'] = '此订单已申请关闭，不能操作';
            Util::jsonExit($result);
		}
        
		if($data['referer']=='天生一对加盟商'){
			$result['error'] = '经销商订单应该不允许0元点款';
			Util::jsonExit($result);
		}
        //更新已付和未付，订单支付状态
        $pay_date = "0000-00-00 00:00:00";
        $orderAccountInfo = $orderModel->updateOrderInfoPayDate($order_id,$pay_date);
        
        
        if($orderAccountInfo != 'success'){
            $result['error'] = '付款失败！'.$orderAccountInfo;
            Util::jsonExit($result);
        }else{
            //写入订单日志
            $logInfo = array(
                'order_id'=>$order_id,
                'order_sn'=>$data['order_sn'],
                'order_status'=>$data['order_status'],
                'shipping_status'=>$data['send_good_status'],
                'pay_status'=>2,
                'create_user'=>$_SESSION['userName'],
                'remark'=>'点款成功:0元。'
            );
            $orderModel->mkOrderInfoLog($logInfo);
            $result['success'] = 1;
            Util::jsonExit($result);
        }
    }
 
    /**
     * 赠品0元点款
     * @param type $param
     */
    public function payZero_zp($param) {
        $result = array('success' => 0, 'error' => '');
        $order_id = _Request::getInt('id');
        $orderModel = new ApiOrderModel();
        $data = $orderModel->getOrderList($order_id);
        
        if($data['order_status'] ==1){
            $result['error'] = '此订单未审核，不能操作';
            Util::jsonExit($result);
        }elseif ($data['order_status'] ==3) {
            $result['error'] = '此订单已取消，不能操作';
            Util::jsonExit($result);
        }elseif ($data['order_status'] ==4) {
            $result['error'] = '此订单已关闭，不能操作';
            Util::jsonExit($result);
        }elseif($data['order_pay_status']==3){
            $result['error'] = '此订单已付全款，不能操作';
            Util::jsonExit($result);
        }elseif($data['order_pay_status']==2){
            $result['error'] = '此订单为支付定金，不能操作';
            Util::jsonExit($result);
        }elseif($data['apply_close']==1){
            $result['error'] = '此订单已申请关闭，不能操作';
            Util::jsonExit($result);
		}
        
        $where=array();
        $where['order_id']=$order_id;
        $getOrder = $orderModel->getOrderGoods($where);
        foreach($getOrder as $k=>$v){
            if($v['goods_type']!='zp'){
                $result['error'] = '订单商品中存在非赠品，不能操作赠品0元点款！';
                Util::jsonExit($result);                
            }
        }

        //更新已付和未付，订单支付状态
        $orderAccountInfo = $orderModel->updateOrderInfoPayDate($order_id);
        
        
        if($orderAccountInfo != 'success'){
            $result['error'] = '付款失败！'.$orderAccountInfo;
            Util::jsonExit($result);
        }else{
            //添加裸钻下架
            $order_where =array('order_id'=>$order_id,'select'=>'*');
            $order_detail_data = $orderModel->getOrderGoods($order_where);
            $luozuan_arr = array();
            foreach ($order_detail_data as $key => $value) {
                if($value['goods_type']=='lz'){
                    $luozuan_arr[$key]['goods_id'] = $value['goods_id'];
                }
            }
            //$apiModel = new ApiModel();
            $keys['goods_id'] = 'goods_id';
            $vals['goods_id'] = $luozuan_arr;
            ApiModel::diamond_api($keys,$vals,'updateDiamondInfo');
            //写入订单日志
            $logInfo = array(
                'order_id'=>$order_id,
                'order_sn'=>$data['order_sn'],
                'order_status'=>$data['order_status'],
                'shipping_status'=>$data['send_good_status'],
                'pay_status'=>2,
                'create_user'=>$_SESSION['userName'],
                'remark'=>'赠品0元点款成功:0元。'
            );
            $orderModel->mkOrderInfoLog($logInfo);
            $result['success'] = 1;
            Util::jsonExit($result);
        }
    }    
    
    /**
     * 	insert，信息入库
     */
    public function inserts($params) {
        $result = array('success' => 0, 'error' => '');
        $buchan_flag = false;
        $order_id = _Request::getInt('order_id');
        $orderModel = new SalesModel(28);
         $apiSalepolicyModel = new BaseSalepolicyGoodsModel(17);          
        //$orderModel = new ApiOrderModel();
        $orderInfo = $orderModel->getOrderList($order_id);
                    
        $pay_date = '0000-00-00 00:00:00';
        $order_sn = $orderInfo['order_sn'];
        $order_time = $orderInfo['create_time'];
        $order_amount = $orderInfo['order_amount'];
        $referer = $orderInfo['referer'];
        //定金收据号
        $deposit_sn_arr = _Request::getList('deposit_sn');
        //转单流水号
        $zhuandan_no = _Request::getList('zhuandan_no');
        //金额
        $deposit = _Post::getList('order_deposit');
        foreach($deposit as $val){
            if(empty($val)){
                $result['error'] = '支付金额不能为空';
                Util::jsonExit($result);
            }
        }
        $flag = _Post::getString('flag');
        if($flag){
            $departmentList = _Post::getList('department');
            foreach($departmentList as $val){
                if(empty($val)){
                    $result['error'] = '协助销售渠道不能为空';
                    Util::jsonExit($result);
                }
            }
        }
		$AppOrderPayActionModel = new AppOrderPayActionModel(30);
         //订单首次付款时判断
        $order_pay_status = $orderInfo['order_pay_status'];
        $department_id = $orderInfo['department_id'];
                    
        //订单未支付，并且不是淘宝渠道2
        if($order_pay_status == 1 && $department_id!=2 && $orderInfo['referer']!='老系统订单'){
            //判断此订单中的货品是否有效，非裸钻的现货如已经下架了，那么提示不能添加了
                    
            $detailInfo = $orderModel->getOrderDetailByOrderId(array('order_id'=>$order_id));

            $error_info = array();
            $un_pay_arr = array('lz','qiban','zp');
            foreach ($detailInfo as $val){
                $goods_id = $val['goods_id'];
                $is_xianhuo = $val['is_stock_goods'];
                $goods_type = $val['goods_type'];
             
				//add by liulinyan 2015-11-07 for boss-693 判断裸钻彩钻目前是否是上架状态
				//紧急临时注释
				/*if($goods_type == 'lz')
				{
					$lzarr = $AppOrderPayActionModel->getRowByGoodSn($goods_id);
					if(empty($lzarr))
					{
						$error_info[$goods_id] = '的裸钻未查询到！';
						continue;
					}elseif($lzarr[0]['status']==2){
						$error_info[$goods_id] = '的裸钻已经下架或者被售出，不可售卖了，请找库管核实！';
						continue;
					}
				}
				if($goods_type == 'cz')
				{
					$czarr = $AppOrderPayActionModel->getRowByGoodSnOrCertId($goods_id);
					if(empty($czarr))
					{
						$error_info[$goods_id] = '的彩钻未查询到！';
						continue;
					}elseif($czarr[0]['status']==2){
						$error_info[$goods_id] = '的彩钻已经下架或者被售出，不可售卖了，请找库管核实！';
						continue;
					}
				}*/
				//如果是起版的不用去查销售政策
                if($is_xianhuo ==0){
                    continue;
                }
				
                if(!in_array($goods_type, $un_pay_arr)){
                    /* $data = $apiSalepolicyModel->getBaseSaleplicyGoods($goods_id);
					if(empty($data))
					{
						 $error_info[$goods_id] = '销售政策中没有此货号！';
                         continue;
					}elseif($data['is_sale'] == 0)
					{
						$error_info[$goods_id] = '可销售商品已经下架！';
                        continue;
					} */
                }
            }

            if($error_info){
                $error_str = '';
                foreach ($error_info as $key=>$val){
                    $error_str .= $key.":".$val."<br>";
                }
                $result['error'] = $error_str;
                Util::jsonExit($result);
            }
        }
        //判断如果出现重复单号需要提示
		$depositList=array();
        $all_dingjin_flag = 1;
        if(!empty($deposit_sn_arr)){
            $temp_arr = array();
			foreach($deposit_sn_arr as $key=>$val){
                if(empty($val)){
                    $all_dingjin_flag = 0;
                    continue;
                }
				if(array_key_exists($val, $temp_arr)){
                    $result['error'] = '此定金号：'.$val.',不可以重复利用! ';
                    Util::jsonExit($result);
				}else{
                    $temp_arr[$val] =$val;
                    $depositModel = new AppReceiptDepositModel(29);
                    $is_have_deposit_sn = $depositModel->getRowList($val);
                    if(empty($is_have_deposit_sn)){
                        $result['error'] = '不存在该定金收据单号';
                        Util::jsonExit($result);
                    }
                    if($orderInfo['department_id']!=$is_have_deposit_sn['department']){
                        $result['error'] = '定金收据单号销售渠道和订单号销售渠道不统一';
                        Util::jsonExit($result);
                    }
                    if($is_have_deposit_sn['status']!=1){
                        $result['error'] = '定金收据状态出错！';
                        Util::jsonExit($result);
                    }
                    if($is_have_deposit_sn['pay_fee']!=$deposit[$key]){
                        $result['error'] = '定金收据金额出错！';
                        Util::jsonExit($result);
                    }
                    if($is_have_deposit_sn['order_sn']!='' && $is_have_deposit_sn['order_sn']!=$orderInfo['order_sn']){
                        $result['error'] = "定金收据号".$val.",是用来支付订单".$orderInfo['order_sn']."!";
                        Util::jsonExit($result);
                    }
                    $depositList[$val]=$is_have_deposit_sn;
				}
			}	
		}
        
        
         $newmodel = new AppOrderPayActionModel(30);            
        //验证转单
        if(!empty($zhuandan_no)){
            $temp_arr = array();
			foreach($zhuandan_no as $key=>$val){
                if(empty($val)){
                    $all_dingjin_flag=0;
                    continue;
                }
                
             //$orderApi = new ApiOrderModel();
                $_arr = $orderModel->checkReturnGoods($val);
                //$retData = $_arr['data'];
                $retData = $_arr;
				
                if($retData != '没有查到相应的信息'){
                    if($retData['check_status']>=4 && $retData['return_type']==1){
                    }else{
                        $result['error'] = '退款流水号'.$val.'现场财务必须审核而且退款类型是转单，流水才能在新订单点款';
                        Util::jsonExit($result);
                    }
                }else{
                        $result['error'] = "转单流水号{$val}不存在.";
                        Util::jsonExit($result);
                }
                /*
                因为目前支持老订单转单，所以功能限制取消
                if($_arr['error']>0){
                    $result['error'] = '此转单流水号：'.$val.'是不合法的，请输入正确流水号! ';
                    Util::jsonExit($result);
                }
                */

                $real_return_amount = $retData['real_return_amount'];//此流水实退金额
                $order_deposit = $deposit[$key];//此次退款金额
                $deposit_sum = $newmodel->getZhuandan_sn_deposit($val);

				if($order_deposit + $deposit_sum > $real_return_amount){
                    $result['error'] = '此转单流水号：'.$val.',转单总金额不能超过转单实退金额! ';
                    Util::jsonExit($result);
				}else{
                    $temp_arr[$val] =$val;
				}
			}	
		}
        
        
        $amount_deposit = array_sum($deposit);
        if($amount_deposit <= 0){
            $result['error'] = '支付金额不能小于0元';
            Util::jsonExit($result);
        }

        
       
		// 订金点款比例必须大于等于50%:
        if($order_pay_status==1 && $referer != '婚博会' && $referer != '天生一对加盟商'){
            //$companyId = $_SESSION['companyId'];
            $companyType=1;           
            $channelModel=new SalesChannelsModel(1);
            $companyids=$channelModel->getSalesChannel(array($orderInfo['department_id']));
            $companyId=0;
            if($companyids)
                $companyId=$companyids[0]['company_id'];
            $companyModel = new CompanyModel(1);            
            $comInfo = $companyModel->select2("id,company_type","id={$companyId}",2);
            if(empty($companyId) || empty($comInfo)){
                $result['error'] = "异常，系统不能识别当前订单销售渠道所属公司！";
                Util::jsonExit($result);
            }
            $companyType = $comInfo['company_type'];
            //经销商不受50%点款限制 boss_1509

            if(!$all_dingjin_flag && $companyType<>3){
                //付款不能小于总金额的一半
                $perlimit=0.5;
                $perlimit_str='50';
                if(time()>strtotime("2017-11-15")){
                    $perlimit=1.0;
                    $perlimit_str='100'; 
                }    
                $tmp_amount  = $order_amount*$perlimit;
                $tmp_amount -= $orderInfo['money_paid'];
                if($amount_deposit < $tmp_amount){
                    $result['error'] = "支付金额不能小于：".$tmp_amount."(订单总金额的".$perlimit_str."%-已付金额)";
                    Util::jsonExit($result);
                } 
            }
        }
        
        //天生一对加盟商的订单，第一次点款金额必须大于等于订单总金额的30%  BOSS-1133
        if($referer == '天生一对加盟商'){
            $tsyd_percent = $order_amount>0?$amount_deposit/$order_amount:1;
            if($order_pay_status==1 && $tsyd_percent<0.3){
                $result['error'] = "天生一对加盟商的订单，第一次点款金额不能小于总金额的30%";
                Util::jsonExit($result);
            }
        }
        //未付金额
        $balance = $orderInfo['money_unpaid'];
        $remark = _Post::getList('action_note');
        $pay_time = _Post::getList('pay_time');
        foreach ($pay_time as $val){
            if(empty($val)){
                $result['error'] = '支付时间不能为空';
                Util::jsonExit($result);
            }
            if($val>date("Y-m-d")){
                $result['error'] = '支付时间不能大于当前时间';
                Util::jsonExit($result);
            }
        }
        //取得渠道归属
        $department = $orderInfo['department_id'];
        $channelModel = new SalesChannelsModel(1);
        $code = $channelModel->getChannelOwnCode($department,1);
        if(!$code){
            $result['error'] = '销售渠道的渠道归属为空，需要编辑销售渠道！';
            Util::jsonExit($result);
        }
        $pay_type = _Post::getList('pay_type');
        $counter = 0;
        foreach($pay_type as $key=>$val){
            if($val<1){
                $result['error'] = '支付类型不能为空';
                Util::jsonExit($result);
            }
            if(($val==272 || $val==280) && $zhuandan_no[$key]==''){
                $result['error'] = '转单单号不能为空！';
                Util::jsonExit($result);
            }
            if($val == 224){
                $counter++;
            }
        }
        /**
         * 积分商城要求添加的代码
         */
        if( $counter > 0 && count($pay_type) != $counter ){
            $result['error'] = '支付类型选择了”商品转赠品“不能在选其它的支付类型';
            Util::jsonExit($result);
        } 
        $appOrderPayActionModel = new AppOrderPayActionModel(30);
        $isTouchGifts = $appOrderPayActionModel->isTouchGifts($order_id);
        if($isTouchGifts == 3 && count($pay_type) == $counter){
            $result['error'] = '之前点款选择了其它支付类型，不能在选择“商品转赠品”';
            Util::jsonExit($result);
        }
        if($isTouchGifts == 2 && $counter == 0){
            $result['error'] = '支付类型选择了”商品转赠品“不能在选其它的支付类型';
            Util::jsonExit($result);
        }
       
        $pay_fee = _Post::getList('order_deposit');
        $card_no = _Post::getList('card_no');
        $card_voucher = _Post::getList('card_voucher');
        $user_id = $AppOrderPayActionModel->GetMemberByMember_id($orderInfo['user_id']);

        $order_consignee = $orderInfo['consignee']?$orderInfo['consignee']:'';
        $proof_sn = _Post::getList('deposit_sn');

        $status = 1;
        $pay_checker = $_SESSION['userName'];
        $pay_check_time = date("Y-m-d");
        $system_flg = 1;

        if($balance<$amount_deposit){
            $result['error'] = '支付金额不能大于应付金额.';
            Util::jsonExit($result);
        }
        $olddo = array();
        $newmodel = new AppOrderPayActionModel(30);
        $log_model = new AppReceiptPayLogModel(30);
        $_doModel = new AppReceiptPayModel(30);
        $BespokeDeal = new AppBespokeInfoModel(18);
        
        $pdolist[30] = $newmodel->db()->db(); 
        $pdolist[20] = $BespokeDeal->db()->db();
        $pdolist[28] = $orderModel->db()->db();

        try{

        try{
            foreach ($pdolist as $pdo){
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
                $pdo->beginTransaction(); //开启事务
            }
        }catch(Exception $e){
            $msg = "批量事物开启失败";
            Util::rollbackExit($msg,$pdolist);
        }
        $orderActionArr = array();
        foreach ($deposit_sn_arr as $k=>$val){
            $balance -= $deposit[$k];
            $newdo = array();
            $newdo['order_id'] = $order_id;
            $newdo['order_sn'] = $order_sn;
            $newdo['order_time'] = $order_time;
            $newdo['deposit'] = $deposit[$k];
            $newdo['order_amount'] = $order_amount;
            $newdo['balance'] = $balance;
            $newdo['remark'] = $remark[$k];
            $newdo['pay_time'] = $pay_time[$k];
            $newdo['order_consignee'] = $order_consignee;
            $newdo['proof_sn'] = $val;
            if($flag){
                $newdo['department'] = $departmentList[$k];
            }else{
                $newdo['department'] = $department;
            }
            $newdo['status'] = $status;
            //$newdo['pay_check_time'] = $pay_check_time;
            $newdo['opter_name'] = $pay_checker;
            $newdo['system_flg'] = $system_flg;
            $newdo['pay_type'] = $pay_type[$k];
            $newdo['zhuandan_sn'] = $zhuandan_no[$k];
            $newdo['create_date'] = microtime();
            $res = $newmodel->saveData($newdo, $olddo);
            if(!$res){
                $msg = "提交失败";
                Util::rollbackExit($msg,$pdolist);
            }
            if($pay_date == '0000-00-00 00:00:00'){
                $pay_date = $pay_time[$k];
            }
            if($pay_date > $pay_time[$k]){
                $pay_date = $pay_time[$k];
            }             
            if(!empty($val)){
                $_modelList = new AppReceiptDepositModel(30);
                $id = $_modelList->getIdBySn($val);
                $_modelList = new AppReceiptDepositModel($id, 30);
                $_modelList->setValue('status', 2);
                $_res = $_modelList->save(true);
                 if(!$_res){
        			$msg = "保存失败";;
        			Util::rollbackExit($msg,$pdolist);
                 }else{
                    $_model = new AppReceiptDepositLogModel(30);
                    $_newdo = array();
                    $_newdo['receipt_id'] = $id;
                    $_newdo['receipt_action'] = '定金收据使用成功';
                    $_newdo['add_time'] = date("Y-m-d H:i:s");
                    $_newdo['add_user'] = $_SESSION['userName'];
                    $_res = $_model->saveData($_newdo, $olddo);
                    if(!$_res){
            			$msg = "保存失败";;
            			Util::rollbackExit($msg,$pdolist);
                    }
                }
            }
            $djModel = new AppReceiptDepositModel(29);
            $receipt_sn = $djModel->create_receipt($code,'DK');
                    
            $_do = array();
            $_do['order_sn'] = $order_sn;
            $_do['receipt_sn'] = $receipt_sn;
            $_do['customer'] = $order_consignee;
            if($flag){
                $_do['department'] = $departmentList[$k];
            }else{
                $_do['department'] = $department;
            }
            $_do['pay_fee'] = $pay_fee[$k];
            $_do['pay_type'] = $pay_type[$k];
            $_do['pay_time'] = $pay_time[$k];
            $_do['card_no'] = $card_no[$k];
            $_do['card_voucher'] = $card_voucher[$k];
            $_do['status'] = 1;
            $_do['print_num'] = 0;
            $_do['pay_user'] = $_SESSION['userName'];
            $_do['remark'] = $remark[$k];
            $_do['add_time'] = date("Y-m-d H:i:s");
            $_do['add_user'] = $_SESSION['userName']; 
            
            $_doRet = $_doModel->saveData($_do, array()); 
            
            if($_doRet === false){	              
                $msg = "保存失败";;
                Util::rollbackExit($msg,$pdolist);
	        }
            
            if($_doRet !==FALSE){ 
                $_newarr = array();
                $_newarr['receipt_id'] = $_doRet;
                $_newarr['receipt_action'] = '添加点款收据成功';
                $_newarr['add_time'] = date("Y-m-d H:i:s");
                $_newarr['add_user'] = $_SESSION['userName'];
                $log_model->saveData($_newarr, array());
                $log_model->save(true);
            }
            $zhuijia_remark = '';
            if($remark[$k]){
                $zhuijia_remark = "($remark[$k])";
            }
            $logInfo = array(
                'order_id'=>$newdo['order_id'],
                'order_status'=>$orderInfo['order_status'],
                'shipping_status'=>$orderInfo['send_good_status'],
                'create_user'=>$_SESSION['userName'],
                'remark'=>'点款成功:'.$pay_fee[$k].'元。'.$zhuijia_remark
            );
            $orderActionArr[] = $logInfo;

            //积分添加
           /* $data = $this->editOrderPointAndReturnId($zhuandan_no[$k],$order_id);
            if(empty($data['status'])){
                Util::rollbackExit($data['msg'],$pdolist);
            }*/
        }
        /**
         * 积分模块添加
         * 如果点款选择了“商品转赠品” 再次点款不能选择其它的支付类型
         * 224 商品转赠品 的代码
         */
        if($counter > 0 ){
            if($orderInfo['is_zp'] == 0){
                $resOrderDetails = $orderModel->updateAppOrderDetail(['is_zp'=>1],"order_id = {$order_id}");
                if(!$resOrderDetails){
                    $msg = "订单明细改成赠品失败";
                    Util::rollbackExit($msg,$pdolist);
                }
                $resOrderInfo = $orderModel->updateBaseOrderInfo(['is_zp'=>1],"id = {$order_id}");
                if(!$resOrderInfo){
                    $msg = "订单改成赠品失败";
                    Util::rollbackExit($msg,$pdolist);
                }
            }
        }
        //更新已付和未付，订单支付状态
        $orderAccountInfo = $orderModel->updateOrderInfoByOrderId($order_id,$amount_deposit,$pay_date); 
                
        if(!$orderAccountInfo){ 
            $msg = "保存失败";;
            Util::rollbackExit($msg,$pdolist);
        }
        $result['hello'] = $order_sn."-----";
        //再查一遍订单
        $_orderInfo = $orderModel->GetOrderInfo($order_id);
       
        //判断未付金额是否为0，如果为0，就把支付状态改成已付款base_order_info.order_pay_status=3  2015-10-14 boss-386 liruzong
        /* if($_orderInfo['money_unpaid']==0){
        	$res2 = $orderModel->updateBaseOrderInfoPayStatus($order_id);
        	if(!$res2){
        		$msg = "更改支付状态失败";
        		Util::rollbackExit($msg,$pdolist);
        	}
        	$_orderInfo['order_pay_status'] = 3;//付全款
        }     */

        //转单：网销订单 预约处理
        $net_pay_types = $pay_type;
        $net_pay_type= array_shift($net_pay_types);
        if ($net_pay_type && $net_pay_type==272) {
            $net_zhuandan_no = $zhuandan_no[0];
            // 取转单对应的订单，转单流水号是退款id
            $net_order = $orderModel->getOrderInfoByZhuandanNo($net_zhuandan_no);
            $is_net_saler = $BespokeDeal->checkUserIsNetSaler($net_order['create_user']);
            if ($is_net_saler && $net_order['send_good_status']==1 && !empty($net_order['bespoke_id'])) {
                $flag = $orderModel->updateBaseOrderInfoBespokeId($order_id, $net_order['bespoke_id']);
                if(!$flag){
                    $msg = "转单流水号的订单预约号绑定到新订单失败！";
                    Util::rollbackExit($msg,$pdolist);
                } elseif ($orderInfo['bespoke_id']) { // 新订单老预约单
                    $net_orders = $orderModel->getOrderInfoByBespokeId($orderInfo['bespoke_id']);
                    if (empty($net_orders)) {
                        $data = array('bespoke_status'=>3);
                    } else {
                        // 存在付款的非赠品单就算预约成交
                        $data = array('deal_status'=>2);
                        foreach ($net_orders as $item) {
                            if ($item['is_zp']==0 && $item['order_pay_status']>1) {
                                $data = array('bespoke_status'=>2, 'queue_status'=>4, 're_status'=>1, 'withuserdo'=>1, 'deal_status'=>1);
                                break;
                            }
                        }
                    }
                    $flag = $BespokeDeal->updateBespokeDealStatus($orderInfo['bespoke_id'], $data);
                    if(!$flag){
                        $msg = "转单流水号的订单预约号绑定到新订单失败！";
                        Util::rollbackExit($msg,$pdolist);
                    }
                }
            }
        } else {
            //更新预约成交状态
            if($order_amount>0 && !empty($orderInfo['bespoke_id'])){
                $value = $BespokeDeal->updateBespokeDealStatus($orderInfo['bespoke_id'], array('deal_status'=>1));
                if(!$value){
                    $msg = "新订单的预约单成交状态变更失败";
                    Util::rollbackExit($msg,$pdolist);
                }
            }
        }

        
        //写入订单日志 AppOrderPayActionModel
        foreach ($orderActionArr as $val){
            $val['pay_status'] = $_orderInfo['order_pay_status'];            
            $req =  $orderModel->mkOrderLog($val);           
            if(!$req){
	             $msg = "保存失败";
	             Util::rollbackExit($msg,$pdolist);
            }
           
        }
        
        //查询订单明细
        $order_where =array('order_id'=>$order_id,'select'=>'*');
        $order_detail_data = $orderModel->getOrderDetailByOrderId($order_where);
        
        if($referer =="天生一对加盟商"){
            //天生一对加盟商的订单,点款成功后更新app_order_details.delivery_status配货状态
            foreach ($order_detail_data as $vo){
                $data = array();
                $data['delivery_status'] = $vo['delivery_status'];
                if($vo['delivery_status']==1){
                    if($vo['is_stock_goods']==1){
                        $data['delivery_status'] = 2;//现货，允许配货
                    }else{
                        //期货，已经出厂或不许布产，配货状态为 允许配货
                        if($vo['buchan_status']==9 || $vo['buchan_status']==11){
                            $data['delivery_status'] = 2;//允许配货
                        }
                    }
                    //更新订单明细的配货状态
                    $res = $orderModel->updateAppOrderDetail($data,'id='.$vo['id']);
                    if(!$res){
                        $msg = "更新订单配货状态失败！";
                        Util::rollbackExit($msg,$pdolist);
                    }
                }
                 
            }
        }
        
        //当订单的支付状态为未操作时       
        if($orderInfo['order_pay_status']==1){           
            $new_detail_data = array();
            foreach ($order_detail_data as $key=>$val){
                $new_detail_data[$key]['is_sale'] = 0;
                $new_detail_data[$key]['is_valid'] = 2;
                $new_detail_data[$key]['goods_id'] = $val['goods_id'];
            }
           
            switch ($orderInfo['is_xianhuo']){
                case 1:
                    //现货下架
                    $res = $this->updateSalepolicy($new_detail_data);
                    //绑定货
                    foreach ($order_detail_data as $key=>$val){
                        $this->bindWarehouseGoods(array('order_goods_id'=>$val['id'],'goods_id'=>$val['goods_id'],'bind_type'=>1));
                    }
                    break;
                case 0://定制单
                    $jj = 0;
                    $xianhuo_detail = array();
                    foreach($order_detail_data as $val){
                        $is_stock_goods = $val['is_stock_goods'];
                        if($is_stock_goods == 1){//现货下架
                           $xianhuo_detail[$jj]['is_sale'] = 0;
                           $xianhuo_detail[$jj]['is_valid'] = 2;
                           $xianhuo_detail[$jj]['goods_id'] = $val['goods_id'];
                            $jj++;
                             //绑定货
                           $this->bindWarehouseGoods(array('order_goods_id'=>$val['id'],'goods_id'=>$val['goods_id'],'bind_type'=>1));
                        }
                    }
                    //如果是现货那么需要把现货下架
                    if($xianhuo_detail){
                         //现货下架
                        $res = $this->updateSalepolicy($xianhuo_detail);
                    }                    
                    break;
            }
             //再查一遍订单
            $_orderInfo = $orderModel->GetOrderInfo($order_id);
                $val['order_status'] = $_orderInfo['order_status'];
            //添加裸钻下架
            $luozuan_arr = array();
            foreach ($order_detail_data as $key => $value) {
                if($value['goods_type']=='lz'){
                    $luozuan_arr[$key]['goods_id'] = $value['goods_id'];
                }
            }
            //$apiModel = new ApiModel();
            $keys['goods_id'] = 'goods_id';
            $vals['goods_id'] = $luozuan_arr;
            ApiModel::diamond_api($keys,$vals,'updateDiamondInfo');
        }
        
        //再查一遍订单状态
        $new_order_info = $orderModel->getOrderList($order_id);
        //当已经变成全款时，现货允许配货
        if($new_order_info['order_pay_status']==3){
            if($orderInfo['is_xianhuo'] == 1){
                //$this->changeXianhuo($orderInfo);  
                //2015-10-17 liruzong
                $res3=$orderModel->EditOrderdeliveryStatus($orderInfo['order_sn']);
                if(!$res3){
                	$msg = "允许配货失败";
                	Util::rollbackExit($msg,$pdolist);
                }
            }else{
                //如果是定制单中定制产品，需要把判断货品的布产状态都为已经出场
                $is_peihuo = false;
                /* $order_where =array('order_id'=>$order_id,'select'=>'id,goods_id,goods_count,is_stock_goods,buchan_status,is_return');
                $order_detail_data = $orderModel->getOrderDetailByOrderId($order_where); */
                if(!empty($order_detail_data)){
                     $is_peihuo = true;
                }               

                switch ($orderInfo['is_xianhuo']){
                    case 1:
                        //现货下架
                        $res = $this->updateSalepolicy($new_detail_data);
                        //绑定货
                        foreach ($order_detail_data as $key=>$val){
                            $this->bindWarehouseGoods(array('order_goods_id'=>$val['id'],'goods_id'=>$val['goods_id'],'bind_type'=>1));
                        }
                        break;
                    case 0://定制单
                        $jj = 0;
                        $xianhuo_detail = array();
                        foreach($order_detail_data as $val){
                            $is_stock_goods = $val['is_stock_goods'];
                            if($is_stock_goods == 1){//现货下架
                               $xianhuo_detail[$jj]['is_sale'] = 0;
                               $xianhuo_detail[$jj]['is_valid'] = 2;
                               $xianhuo_detail[$jj]['goods_id'] = $val['goods_id'];
                                $jj++;
                                 //绑定货
                               $this->bindWarehouseGoods(array('order_goods_id'=>$val['id'],'goods_id'=>$val['goods_id'],'bind_type'=>1));
                            }
                        }
                        //如果是现货那么需要把现货下架
                        if($xianhuo_detail){
                             //现货下架
                            $res = $this->updateSalepolicy($xianhuo_detail);
                        }                        
                        break;
                }               
                
                
                //添加裸钻下架   
				//update by liulinyan 2015-11-07 fro boss-693
                $cz_arr = array();
                $luozuan_arr = array();
                foreach ($order_detail_data as $key => $value) {
                    if($value['goods_type']=='lz'){
                        $luozuan_arr[$key]['goods_id'] = $value['goods_id'];
                    }elseif($value['goods_type'] =='cz'){
						$cz_arr[$key]['id'] = $value['goods_id'];	
					}
                }
                //$apiModel = new ApiModel();
                $keys['goods_id'] = 'goods_id';
                $vals['goods_id'] = $luozuan_arr;
                ApiModel::diamond_api($keys,$vals,'updateDiamondInfo');

				//
                //$apiModel = new ApiModel();
                $keys['id'] = 'id';
                $vals['id'] = $cz_arr;
                ApiModel::diamond_api($keys,$vals,'updateDiamondColorInfo');

                foreach($order_detail_data as $tmp){
                    $is_stock_goods = $tmp['is_stock_goods'];
                    $buchan_status = $tmp['buchan_status'];
                    if($is_stock_goods == 0 && $buchan_status!=9 && $buchan_status!=11 && $tmp['is_return']!=1){
                        $is_peihuo = false;
                    }
                }
                if($is_peihuo){
                	//2015-10-17 liruzong
                	$res4=$orderModel->EditOrderdeliveryStatus($orderInfo['order_sn']);
                	if(!$res4){
                		$msg = "允许配货失败";
                		Util::rollbackExit($msg,$pdolist);                    		
                	}
                   //$this->changeXianhuo($orderInfo);//则定制商品都为已出厂，并且付款完成，则订单变成允许配货 
                }
               
            } 
        }
        //天生一对加盟商的订单,点款成功后期货订单自动生成布产单 boss-1133
        if($referer=="天生一对加盟商"){
            if($buchan_flag ==false){
                $buchan_flag = true;
                $res = $this->AddBuchanDan($orderInfo,$order_detail_data);
                if($res['error']){
                    Util::rollbackExit($res['msg'],$pdolist);
                }
            }
        }
        try{
            foreach ($pdolist as $pdo){
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
                $pdo->commit(); 
            }
        }catch(Exception $e){
            $msg = "操作失败！批量事物执行失败";
            Util::rollbackExit($msg,$pdolist);
        }

        }catch(Exception $e){
            $msg = "操作失败，请重新操作！";
            Util::rollbackExit($msg,$pdolist);
        }

            
        $result['success'] = 1;
	    Util::jsonExit($result);
    }

    /**
     * 	update，点款确认
     */
    public function update($params) {
        $result = array('success' => 0, 'error' => '');
        
        $order_id = _Request::getInt('order_id');
        $orderModel = new ApiOrderModel();
        $orderInfo = $orderModel->getOrderList($order_id);
        
        //当订单的支付状态为未操作时
           
        if($orderInfo['order_pay_status']==1){
            $order_where =array('order_id'=>$order_id,'select'=>'*');
            $order_detail_data = $orderModel->getOrderGoods($order_where);
            $new_detail_data = array();
            foreach ($order_detail_data as $key=>$val){
                $new_detail_data[$key]['is_sale'] = 0;
                $new_detail_data[$key]['is_valid'] = 2;
                $new_detail_data[$key]['goods_id'] = $val['goods_id'];
            }

            switch ($orderInfo['is_xianhuo']){
                case 1:
                    //现货下架
                    $res = $this->updateSalepolicy($new_detail_data);
                    //绑定货
                    foreach ($order_detail_data as $key=>$val){
                        $this->bindWarehouseGoods(array('order_goods_id'=>$val['id'],'goods_id'=>$val['goods_id'],'bind_type'=>1));
                    }
                    break;
                case 0://定制单
                    $jj = 0;
                    $xianhuo_detail = array();
                    foreach($order_detail_data as $val){
                        $is_stock_goods = $val['is_stock_goods'];
                        if($is_stock_goods == 1){//现货下架
                           $xianhuo_detail[$jj]['is_sale'] = 0;
                           $xianhuo_detail[$jj]['is_valid'] = 2;
                           $xianhuo_detail[$jj]['goods_id'] = $val['goods_id'];
                            $jj++;
                             //绑定货
                           $this->bindWarehouseGoods(array('order_goods_id'=>$val['id'],'goods_id'=>$val['goods_id'],'bind_type'=>1));
                        }
                    }
                    //如果是现货那么需要把现货下架
                    if($xianhuo_detail){
                         //现货下架
                        $res = $this->updateSalepolicy($xianhuo_detail);
                    }

                    //$this->AddBuchanDan($orderInfo,$order_detail_data);
                    break;
            }
        }
        //更新订单付款状态
        $res = $orderModel->updateOrderPayStatus($order_id);
        //再查一遍订单状态
        $new_order_info = $orderModel->getOrderList($order_id);
        //当已经变成全款时，现货允许配货
        if($new_order_info['order_pay_status']==3){
            if($orderInfo['is_xianhuo'] == 1){
                $this->changeXianhuo($orderInfo);
            }else{
                //如果是定制单中定制产品，需要把判断货品的布产状态都为已经出场
                $is_peihuo = false;
                $order_where =array('order_id'=>$order_id,'select'=>'id,goods_id,goods_count,is_stock_goods,buchan_status,is_return');
                $order_detail_data = $orderModel->getOrderGoods($order_where);
                if(!empty($order_detail_data)){
                     $is_peihuo = true;
                }

                foreach($order_detail_data as $tmp){
                    $is_stock_goods = $tmp['is_stock_goods'];
                    $buchan_status = $tmp['buchan_status'];
                    if($is_stock_goods == 0 && $buchan_status!=9 && $buchan_status!=11 && $tmp['is_return']!=1){
                        $is_peihuo = false;
                    }
                }
                if($is_peihuo){
                   $this->changeXianhuo($orderInfo);//则定制商品都为已出厂，并且付款完成，则订单变成允许配货 
                }

            }

        }
        
        $logInfo = array(
            'order_id'=>$order_id,
            'order_status'=>$new_order_info['order_status'],
            'shipping_status'=>$new_order_info['send_good_status'],
            'pay_status'=>$new_order_info['order_pay_status'],
            'create_user'=>$_SESSION['userName'],
            'remark'=>'点款成功:确认已付全款'
        );
        //写入订单日志
        $orderModel->mkOrderInfoLog($logInfo);
        
        if ($res !== false) {
            $result['success'] = 1;
            $result['title'] = '修改此处为想显示在页签上的字段';
        } else {
            $result['error'] = '修改失败';
        }
        Util::jsonExit($result);
    }

    /**
     * 	delete，删除
     */
    public function delete($params) {
        $result = array('success' => 0, 'error' => '');
        $id = intval($params['id']);
        $model = new AppOrderPayActionModel($id, 30);
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

    //现货商品：把所有是商品
    public  function changeXianhuo($orderInfo) {
        //现货单改变状态
        $orderModel = new ApiOrderModel();
        if($orderInfo['delivery_status']==1){
            //配货状态没有改变的
            $where = array('order_sn'=>array($orderInfo['order_sn']),'delivery_status'=>2);
            $orderModel->EditOrderdeliveryStatus($where);
        }
    }

    //更改可销售商品状态：改为下架
    public function updateSalepolicy($data) {
        $salePolicyModel = new ApiSalePolicyModel();
        $salePolicyModel->UpdateAppPayDetail($data);
    }

    //现货仓储绑定
    public function bindWarehouseGoods($data){
         //现货需要：查看此商品是否已经绑定仓储
         $warehouseModel = new ApiWarehouseModel();

         $warehouseModel->BindGoodsInfoByGoodsId($data);
    }
    //把定制商品进行布产
    public function AddBuchanDan($orderinfo,$detail_goods){
        $order_sn			= $orderinfo['order_sn'];
        $consignee			= $orderinfo['consignee'];
        $customer_source_id = $orderinfo['customer_source_id'];
        $department_id		= $orderinfo['department_id'];
    
        $processorApiModel = new ApiProcessorModel();
        $salesModel = new SalesModel(28);
        //找到此订单是否已经存在布产的单
        $attr_names =array('cart'=>'石重','clarity'=>'净度','color'=>'颜色','zhengshuhao'=>'证书号','caizhi'=>'材质','jinse'=>'金色','jinzhong'=>'金重','zhiquan'=>'指圈','kezi'=>'刻字','face_work'=>'表面工艺');
        if(!empty($detail_goods)){
            $goods_arr = array();
            foreach($detail_goods as $key=>$val){
                if($val['is_stock_goods'] == 1 && empty($val['is_peishi'])){
                    continue;
                }
                $detail_id = $val['id'];
                //查看此商品是否已经开始布产
                $buchan_info = $processorApiModel->GetGoodsRelInfo($detail_id,$order_sn);
                //print_r($buchan_info);exit();
                if(!empty($buchan_info['data'])){
                    continue;
                }

                $new_style_info = array();
                foreach ($attr_names as $a_key=>$a_val){
                    $xmp['code'] = $a_key;
                    $xmp['name'] = $a_val;
                    $xmp['value'] = $val[$a_key];
                    $new_style_info[]= $xmp;
                }
                $goods_num = $val['goods_count'];
    
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
                $goods_arr[$key]['diamond_type'] = '0';
                //$goods_arr[$key]['qiban_type'] = '2';//默认
                $goods_arr[$key]['origin_dia_type'] = '0';
                //end
            }
            //var_dump($goods_arr);exit;
            //添加布产单
            if(!empty($goods_arr)){
                $res = $processorApiModel->AddProductInfo($goods_arr);
                if($res['error']==1){
                    return array("error"=>1,"msg"=>$res['data']);
                } 
                try{
                    //1.回写布产信息
                    $buchan_sn_str="";
                    foreach ($res['data'] as $vo){ 
                        $buchan_sn_str.= $vo['final_bc_sn'].",";
                        //回写订单明细布产ID
                        $data1 = array("bc_id"=>$vo['buchan_sn']);
                        $salesModel->updateAppOrderDetail($data1,"id={$vo['id']}");
                        //回写订单主表，布产状态允许布产
                        $data2 = array('buchan_status'=>2);                    
                        $salesModel->updateBaseOrderInfo($data2,"order_sn='{$order_sn}'");
                    }
                    //2添加订单操作日志
                    $remark = "财务点款后自动布产，布产单号:".trim($buchan_sn_str,",");
                    $salesModel->AddOrderLog($order_sn,$remark);                    
                }catch (Exception $e){
                    return array('error'=>1,"msg"=>"布产失败，请重新尝试！");
                }
            }
            //$res['buchan_info'] = $buchan_info;
            //$res['goods_arr'] = $goods_arr;
        }
        return array('error'=>0,'msg'=>'布产成功');
    }      
    
    /**
     * 打印现货单
     * @param type $param
     */
    public function printorder($param) {
        $order_id = _Request::get('id');
        $model = new ApiOrderModel(29);
        $orderinfo = $model->getOrderList($order_id);
        
        if ($orderinfo['order_status'] != 2) {
            $result['order_status'] = $orderinfo;
            $result['error'] = "只有已审核的订单才可以打印！";
            Util::jsonExit($result);
        }
        
        $orderaddressinfo = $model->getAddressById($order_id);
        if (!empty($orderaddressinfo)) {
            $regionstr = $orderaddressinfo['province_id'] . ',' . $orderaddressinfo['city_id'] . ',' . $orderaddressinfo['regional_id'];
            $region = new RegionModel(1);
            $res = $region->getAddreszhCN($regionstr);
            $orderaddressinfo['country_name'] = $region->getRegionName($orderaddressinfo['country_id']);
            $orderaddressinfo['regionstr'] = $res . $orderaddressinfo['address'];
        }
        $orderdetailsinfo = $model->getOrderGoods(array('order_id'=>$order_id));
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
            if($val['favorable_status'] == 3){
                $xiaojit = ($val['goods_price']-$val['favorable_price']) * $val['goods_count'];
            }else{
                $xiaojit = $val['goods_price'] * $val['goods_count'];
            }
            $orderdetailsinfo[$key]['xiaoji'] = $xiaojit;
            $zongjia+=$orderdetailsinfo[$key]['xiaoji'];
        }
        $print_time = date('Y-m-d H:i:s');
        $order_account = $model->getOrderPriceInfo($order_id);
        $orderinvoice = $model->getInvoiceById($order_id);
        $giftida = $model->getGifts($order_id);
        
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
        $giftida['giftstr']=$giftstr;
        //销售单
        //print_r($orderinfo);die;
        $orderinfo['title'] = "BDD货品销售单";
		
		$SalesChannelsModel = new SalesChannelsModel(1);
		$channel = $SalesChannelsModel->getSalesChannel(array($orderinfo['department_id']));
		$channel = current($channel) ? current($channel):null;
        $this->render('order_print.html', array(
            'print_time' => $print_time,
            'gift' => $giftida,
            'orderinfo' => $orderinfo ? $orderinfo : 0,
            'orderinvoice' => $orderinvoice ? $orderinvoice : 0,
            'order_account' => $order_account ? $order_account : 0,
            'addressinfo' => $orderaddressinfo ? $orderaddressinfo : 0,
            'detailsinfo' => $orderdetailsinfo ? $orderdetailsinfo : 0,
            'zongjia' => $zongjia,
			'channel'=>$channel,
        ));
    }
    
    
    /**
     * 打印定制单
     * @param type $param
     */
    public function printorder_dz($param) {
        $order_id = _Request::get('id');
        $model = new ApiOrderModel(29);
        $orderinfo = $model->getOrderList($order_id);
        
        if ($orderinfo['order_status'] != 2) {
            $result['order_status'] = $orderinfo;
            $result['error'] = "只有已审核的订单才可以打印！";
            Util::jsonExit($result);
        }
        
        $orderaddressinfo = $model->getAddressById($order_id);
        if (!empty($orderaddressinfo)) {
            $regionstr = $orderaddressinfo['province_id'] . ',' . $orderaddressinfo['city_id'] . ',' . $orderaddressinfo['regional_id'];
            $region = new RegionModel(1);
            $res = $region->getAddreszhCN($regionstr);
            $orderaddressinfo['country_name'] = $region->getRegionName($orderaddressinfo['country_id']);
            $orderaddressinfo['regionstr'] = $res . $orderaddressinfo['address'];
        }
        $orderdetailsinfo = $model->getOrderGoods(array('order_id'=>$order_id));
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
            if($val['favorable_status'] == 3){
                $xiaojit = ($val['goods_price']-$val['favorable_price']) * $val['goods_count'];
            }else{
                $xiaojit = $val['goods_price'] * $val['goods_count'];
            }
            $orderdetailsinfo[$key]['xiaoji'] = $xiaojit;
            $zongjia+=$orderdetailsinfo[$key]['xiaoji'];
        }
        $print_time = date('Y-m-d H:i:s');
        $order_account = $model->getOrderPriceInfo($order_id);
        $orderinvoice = $model->getInvoiceById($order_id);
        $giftida = $model->getGifts($order_id);
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
        $giftida['giftstr']=$giftstr;
        //如果有尾款打印定制单，无尾款打印销售单
        //定制单
        $orderinfo['title'] = "BDD货品订制单";
		
		$SalesChannelsModel = new SalesChannelsModel(1);
		$channel = $SalesChannelsModel->getSalesChannel(array($orderinfo['department_id']));
		$channel = current($channel) ? current($channel):null;
		
        $this->render('order_printb5.html', array(
            'print_time' => $print_time,
            'gift' => $giftida,
            'orderinfo' => $orderinfo ? $orderinfo : 0,
            'orderinvoice' => $orderinvoice ? $orderinvoice : 0,
            'orderaccount' => $orderinfo ? $orderinfo : 0,
            'order_account' => $order_account ? $order_account : 0,
            'addressinfo' => $orderaddressinfo ? $orderaddressinfo : 0,
            'detailsinfo' => $orderdetailsinfo ? $orderdetailsinfo : 0,
            'zongjia' => $zongjia,
			'channel' => $channel,
        ));

    }

    /**
     * @param $returnId
     * @return array
     * 获取老订单的积分
     */
    private function editOrderPointAndReturnId($returnId,$order_id){
        $result = ['status'=>1,'msg'=>''];
        if(empty($returnId)){
            return $result;
        }
        $saleModel = new SalesModel(28);
        $data = $saleModel->getOrderAccountByZhuandanNo($returnId);
        if(!$data){
            $result['msg'] = '流水号无法匹配';
            $result['status'] = 0;
            return $result;
        }
        $data = $saleModel->udpateOrderAccountPoint($order_id,$data['current_point'],$data['old_point']);
        if(!$data){
            $result['msg'] = '积分修改失败';
            $result['status'] = 0;
            return $result;
        }
        return $result;
    }

}

?>