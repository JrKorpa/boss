<?php
/**
*  -------------------------------------------------
*   @file		: WaitDistributionController.php
*   @link		:  www.kela.cn
*   @copyright	: 2014-2024 kela Inc
*   @author	: Laipiyang <462166282@qq.com>
*   @date		: 2015-01-11 10:55:30
*   @update	:
*  @待发货列表
*  -------------------------------------------------
*/
class WaitDistributionTsydController extends CommonController
{
    protected $smartyDebugEnabled = false;
    protected $whitelist = array('printBills');
    /**
     *  index，搜索框
     */
    public function index ($params)
    {
        $has_company = $this->hasCompany();
	    $this->getExperienceStore($has_company);  //获得体验店信息
    	$this->getCustomerSources();
    	$this->getSourceList();
        $this->getReferers();
        $SalesChannelsModel = new SalesChannelsModel(1);
        $SalesChannelsArr = $SalesChannelsModel->getSalesChannelsInfo('id,channel_name',Array('is_tsyd'=>1));
        $this->render('wait_distribution_search_form.html',array(
            'bar'=>Auth::getBar(),
        	'SalesChannelsArr'=>$SalesChannelsArr,	
        ));
    }

    public function showList($params){
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
            die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }        
        $has_company = $this->hasCompany();
        $shops = $this->getShops($has_company);
        $this->getCustomerSources();
        $this->getSourceList();
        $args = array(
                'mod'   => _Request::get("mod"),
                'con'   => substr(__CLASS__, 0, -10),
                'act'   => __FUNCTION__,
                'order_sn'=> isset($params['order_sn']) ? $params['order_sn'] : '',
                'style_sn'=> isset($params['style_sn']) ? $params['style_sn'] : '' ,
                'create_user'=> isset( $params['create_user'] ) ? $params['create_user'] : '',
                'customer_source_id'=> isset($params['customer_source_id']) ? $params['customer_source_id'] : '',
                'is_print_tihuo'=> isset($params['is_print_tihuo']) ? $params['is_print_tihuo'] : '' ,
        		'channel_class'=> isset($params['channel_class']) ? $params['channel_class'] : '',
                'sales_channels_id'=> isset($params['sales_channels_id']) ? $params['sales_channels_id'] : '',
                'create_time_start'=>isset($params['create_time_start']) ? $params['create_time_start'] : '',
                'create_time_end'=> isset($params['create_time_end']) ? $params['create_time_end'] : '',
                'delivery_status'=> isset($params['delivery_status']) ? $params['delivery_status'] : '',
               'delivery_address'=> isset($params['delivery_address']) ? $params['delivery_address'] : '',
               'referer'=> isset($params['referer']) ? $params['referer'] : '',
               'no_view'=> isset($params['no_view']) ? 1 : '',
               'shousuo'=> isset($params['shousuo']) ? $params['shousuo'] : '',
        );
        $where=array();
        /* $res = $this->ChannelListO();
        if ($res === true) {
        	//获取全部的有效的销售渠道
        	$SalesChannelsModel = new SalesChannelsModel(1);
        	$channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
        } else {
        	$channellist = $this->getchannelinfo($res);
        }
		 */
        if ($_SESSION['userType']==1) {
            //获取全部的有效的销售渠道
            $SalesChannelsModel = new SalesChannelsModel(1);
            $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
        } else {
            $res = explode(',', $_SESSION['qudao']);
            $channellist = $this->getchannelinfo($res);
        }

       //  $allSalesChannelsData = array();
       //  foreach ($channellist as $val) {
       //      $allSalesChannelsData[] = $val['id'];
       //  }
        $page = _Request::getInt("page",1);
        //批量 订单号搜索
        if($args['order_sn']){
        	//若 订单号中间存在空格 汉字逗号 替换为英文模式逗号
			$args['order_sn']=str_replace('，',' ',$args['order_sn']);
			$args['order_sn']=trim(preg_replace('/(\s+|,+)/',' ',$args['order_sn']));
			$where['order_sn']="'".str_replace(' ',"','",$args['order_sn'])."'";
        }
        if($args['customer_source_id']){
        	$where['customer_source_id'] = $args['customer_source_id'];
        }
        
        if($args['create_user']){
        	$where['create_user'] = $args['create_user'];
        }
        if($args['is_print_tihuo']!=''){
        	$where['is_print_tihuo'] = $args['is_print_tihuo'];
        }
        if($args['delivery_address']){
            $where['delivery_address'] = $args['delivery_address'];
            $delivery_address =$args['delivery_address'];
        }  
        if($args['referer']){
            $where['referer'] = $args['referer'];
        } 
        
        if($args['no_view']){
            $where['no_view'] = $args['no_view'];
        }  
           
        if($args['sales_channels_id']){
            $where['sales_channels_id'] = $args['sales_channels_id'];
        }  
        // 待配货订单列表和渠道无关
        // $sales_channels='';
        
        // 	if(!$args['sales_channels_id']){
        // 		if(!$args['channel_class']){
        // 			if ($_SESSION['userType']==1) {
        // 				//获取全部的有效的销售渠道
        // 				$SalesChannelsModel = new SalesChannelsModel(1);
        // 				$channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
        // 			} else {
        // 				$res = explode(',', $_SESSION['qudao']);
        // 				$channellist = $this->getchannelinfo($res);
        // 			}

        // 		}else{
        // 			$SalesChannelsModel = new SalesChannelsModel(1);
        // 			$channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", Array('channel_class'=>$args['channel_class']));
        // 		}
        // 		if($channellist){
        // 		     foreach($channellist as $key=>$val){
        // 			     $sales_channels.=$val['id'].',';
        // 		     }
        // 		     $where['sales_channels_id']=rtrim($sales_channels,',');
        // 		}
        // 	}else{
        // 		$where['sales_channels_id'] = $args['sales_channels_id'];
        // 	}

       
        if($args['create_time_start']){
        	$where['create_time_start'] = $args['create_time_start'];
        }
        if($args['create_time_end']){
        	$where['create_time_end'] = $args['create_time_end'];
        }

        //款号 批量搜索
        if($args['style_sn']){
        	//若 款号中间存在空格 汉字逗号 替换为英文模式逗号
			$args['style_sn']=str_replace('，',' ',$args['style_sn']);
			$args['style_sn']=trim(preg_replace('/(\s+|,+)/',' ',$args['style_sn']));
        	$where['style_sn']="'".str_replace(' ',"','",$args['style_sn'])."'";
        }
        $where['order_status'] = 2;
        $where['has_company'] = $has_company;
        $shops = array_column($shops,'shop_name');
        $where['shops'] = $shops;
        $where['shousuo'] = $args['shousuo'];
        $where['shops'] = $shops;
        // $delivery_status['delivery_status']=$delivery_status_str;
        $SalesModel = new SalesModel(27);
       // $result = ApiSalesModel::GetOrderListPages($style_sn,$customer_source_id,$create_time_end,$create_time_start,$sales_channels_id,$is_print_tihuo,$create_user,$delivery_status_str, $order_sn, $page , $page_size = 100);      //通过接口，获取 允许配货/配货中 状态的订单列表
        $result = $SalesModel->GetOrderTsydListPage($where, $page , $page_size = 100);      //通过接口，获取 允许配货/配货中 状态的订单列表
        
        $managemodel = new ManagementModel(1);
        //获取所有销售渠道
        $channels = $managemodel->getSalesChannels();
        foreach($channels as $k=>$v){
            $saleschannels[$v['id']] =$v['channel_name'];
        }
        //维修状态
        foreach($result['data'] as $k=>$v){
            $weixiu_status = $SalesModel->getWeixiustatusById($v['id']);
            $weixiu_status = array_values(array_column($weixiu_status,'weixiu_status'));
            $temp1 = array_intersect($weixiu_status, array(1,2,3,4,5,6,7));
            // $temp2 = array_diff($weixiu_status,array(1,2,3,4));
            $temp3 = array_diff($weixiu_status,array(5,6,7));

            if(empty($temp1)){
                $result['data'][$k]['th_weixiu'] ='未操作';

            }elseif(empty($temp3) || empty(array_filter($temp3))){
                 $result['data'][$k]['th_weixiu'] ='维修完毕';

            }else{
                 $result['data'][$k]['th_weixiu'] ='维修中';
            }
        }

        $pageData = $result;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'wait_distribution_search_page';
        $this->render('wait_distribution_search_list.html',array(
            'pa'=>Util::page($pageData),
            'page_list'=>$result,
            'delivery_address'=>$delivery_address,
            'saleschannels'=>$saleschannels
        ));
    }

    /** 获取订单部门 **/
    public function getDepartment() {
        $departmentModel = new DepartmentModel(1);
        $departmentInfo = $departmentModel->getDepartmentInfo("`id`,`name`", array('parent_id' => 3));

        $departmentData = array();
        foreach ($departmentInfo as $val) {
          $departmentData[$val['id']] = $val['name'];
        }
        $this->assign('departmentData', $departmentData);
    }

    /** 获取销售渠道 **/
    public function getSourceList() {
       /*  $SalesChannelsModel = new SalesChannelsModel(1);
        $getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
        //获取所有数据
        $allSalesChannelsData = array();
        foreach ($getSalesChannelsInfo as $val) {
            $allSalesChannelsData[$val['id']] = $val['channel_name'];
        } */
        //$res = $this->ChannelListO();

        if ($_SESSION['userType']==1) {
        	//获取全部的有效的销售渠道
        	$SalesChannelsModel = new SalesChannelsModel(1);
        	$channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
        } else {
            $res = explode(',', $_SESSION['qudao']);
        	$channellist = $this->getchannelinfo($res);
        }
       foreach ($channellist as $val) {
        	$allSalesChannelsData[$val['id']] = $val['channel_name'];
        }

        $this->assign('sales_channels_idData', $allSalesChannelsData);
    }
    
    
    /**
     * 根据一级分类获取销售渠道
     */
    public function getChannelIdByClass(){
    	$channel_class = _Request::get('channel_class');
    	$SalesChannelsModel = new SalesChannelsModel(1);
    	$data = $SalesChannelsModel->getSalesChannelsInfo('id,channel_name',Array('channel_class'=>$channel_class));
    	$this->render('option.html',array(
    			'data'=>$data,
    	));
    }

	public function getSourceName($source_id)
	{
		$SalesChannelsModel = new SalesChannelsModel(1);
		$info = $SalesChannelsModel->getChannelOwnId($source_id);
		print_r($info);exit;
	}

    /** 获取客户来源 **/
    public function getCustomerSources(){
        $CustomerSourcesModel = new CustomerSourcesModel(2);
        $arr = $CustomerSourcesModel->getSources();
        $CustomerSourcesList = array();
        foreach($arr as $val){
            $CustomerSourcesList[$val['id']] = $val['source_name'];
        }
       // var_dump($CustomerSourcesList)
        $this->assign('CustomerSourcesList', $CustomerSourcesList);
    }

    
    //获取体验店信息
    public function getExperienceStore($has_company){
        $shopmodel = new ShopCfgModel(1);
        $shops = $shopmodel->getStoreInfo($has_company);
        $results = array_intersect(array(58,445), $has_company);
        if(!empty($results)){
            array_unshift($shops, array('shop_name'=>'总部到店面'));
            array_unshift($shops, array('shop_name'=>'总部到客户'));
        }
        $this->assign('shops', $shops);
    }   
	
    
    //获取体验店信息
    public function getShops($has_company){
        $shopmodel = new ShopCfgModel(1);
        $shops = $shopmodel->getStoreInfo($has_company);
        $results = array_intersect(array(58,445), $has_company);
        if(!empty($results)){
            array_unshift($shops, array('shop_name'=>'总部到客户'));
        }
       return $shops;
    }   



    //updat by rong 废除旧的API方式
    /** 打印提货单 **/
    public function printBills_old(){
    $ke=new Kezi();
        $ids = _Request::get('_ids');   //订单号字符串
        $sign= _Request::get('sign');
        $order_sn_str = explode(',', $ids);

        $kuaidiModel = new ExpressModel(1);
        $SalesChannelsmodel = new SalesChannelsModel(1);
        $dd = new DictView(new DictModel(1));

        $html = '';

        foreach($order_sn_str AS $k => $v){
            $orderinfo = ApiSalesModel::GetPrintBillsInfo($v);      //通过接口，获取 订单信息
			/*
			if ($_SESSION['userName'] == 'admin')
			{
				echo "订单号".$v ."<br>";
				var_dump($orderinfo);exit;
			}
			*/
            if (isset($orderinfo['return_msg']['order_pay_type'])){
                //获取支付方式 拼接
                $order_pay_type = $orderinfo['return_msg']['order_pay_type'];
                if($order_pay_type){
                    $newmodel =  new PaymentModel($order_pay_type,2);
                    $orderinfo['return_msg']['order_pay_name'] = $newmodel->getValue('pay_name');
                }else{
                    $orderinfo['return_msg']['order_pay_name']='无';
                }
            }

            //订单来源
			if (isset($orderinfo['return_msg']['customer_source_id'])){
				$customer_source_id = $orderinfo['return_msg']['customer_source_id'];
				if($customer_source_id){
					$CustomerSourcesModel = new CustomerSourcesModel($customer_source_id , 1);
					$orderinfo['return_msg']['customer_source_id'] = $CustomerSourcesModel->getValue('source_name');
				}else{
					$orderinfo['return_msg']['customer_source_id']='——';
				}
			}

            if (isset($orderinfo['return_msg']['id'])){
                $order_id = $orderinfo['return_msg']['id'];
                //获取外部订单号
                $ret = ApiSalesModel::GetOutOrderInfoByOrderId($order_id);

                //打印回写订单日志
                $remark = "订单：".$orderinfo['return_msg']['order_sn']." 打印提货单(仓库待配货列表)";
                if(!empty($sign)){
                	$remark = "订单：".$orderinfo['return_msg']['order_sn']." 打印提货单(销售管理批量打印订单)";
                }
                ApiSalesModel::addOrderAction(
                    $orderinfo['return_msg']['order_sn'] ,
                    $_SESSION['userName'],
                    $remark);
            }

            if (!empty($ret['return_msg']['out_order_sn'])) {
                $out_order_sn = $ret['return_msg']['out_order_sn'];
            }  else {
                $out_order_sn = "";
            }
            if(!empty($orderinfo['return_msg']) && $orderinfo['return_msg']['distribution_type']==2){
		     if(empty($orderinfo['return_msg']['express_id'])){
			  exit("快递类型不能为空");
		   }
                   $orderinfo['return_msg']['express_id'] = $kuaidiModel->getNameById( $orderinfo['return_msg']['express_id'] ) ? $kuaidiModel->getNameById( $orderinfo['return_msg']['express_id'] ) : '——';

            $orderinfo['return_msg']['user_name'] = '--';
            //获取单据会员名字
            $user = ApiSalesModel::GetUserInfor( $orderinfo['return_msg']['user_id'] );
            if (isset( $orderinfo['return_msg']['user_id'] ) && !empty( $orderinfo['return_msg']['user_id'] )){
                if( $user['data'] != '未查询到此会员' )
                {
                    $orderinfo['return_msg']['user_name'] = $user['data']['member_name'];
                }
            }
            //获取单据明细
            $detail = ApiSalesModel::GetOrderDetailByOrderId($orderinfo['return_msg']['order_sn']);

            //获取配货单单据的配送类型
            if($orderinfo['return_msg']['distribution_type'] == 1){
                //如果下单的配送类型 为1 (数字字典 sales.distribution_type)。到门店 则在提货单的配送类型中。显示下订单的门店名
                /**
                * $channel = $SalesChannelsmodel->getSalesChannelsInfo("`id`,`channel_name`", array('id'=> $orderinfo['return_msg']['department_id']));
                * $orderinfo['return_msg']['department_id'] = $channel[0]['channel_name'];
                */

                $orderinfo['return_msg']['department_id'] = strstr($orderinfo['return_msg']['address'], '|' , true);

            }else{
                    $orderinfo['return_msg']['department_id'] = $dd->getEnum('sales.distribution_type', $orderinfo['return_msg']['distribution_type']);
            }
        //获取订单明细是否绑定货品信息 + 柜位号
        $WarehouseGoodsModel = new WarehouseGoodsModel(21);
        $GoodsWarehouseModel = new GoodsWarehouseModel(21);
        $WarehouseBoxModel = new WarehouseBoxModel(21);
        if(!empty($detail)){
            foreach($detail as $p_key => &$bing_val){

                //获取图片 拼接进数组
                $gallerymodel = new ApiStyleModel();
                $gallery_data = $gallerymodel->getProductGallery($bing_val['goods_sn'],1);
                //$gallery_data是一个二维数组
                if(isset($gallery_data[0]['thumb_img'])){
                    $detail[$p_key]['goods_img']=$gallery_data[0]['thumb_img'];
                }else{
                    $detail[$p_key]['goods_img']='';
                    //$detail[$p_key]['goods_img']='images/styles/201007/1279189436925042936.jpg';
                }

                $bing_val['box_id'] = '无';
                //是否绑定
                 $fields = " `goods_id` ";
                 $where = " `order_goods_id` = {$bing_val['id']} ";
                 $goods_id = $WarehouseGoodsModel->select2($fields, $where , $is_all = 1);
                 if($goods_id){
                    $bing_val['bing'] = 1; //有商品绑定
                    $bing_val['goods_id'] = $goods_id;
                     //柜位号
                     $ret = $GoodsWarehouseModel->select2(' `box_id` ', " `good_id` = '{$goods_id}' ", $is_all = 3);
                     if($ret){
                        $bing_val['box_id'] = $WarehouseBoxModel->select2(" `box_sn` ", " `id`={$ret} " , $is_all = 3); //有柜位号
                     }
                 }else{
                     $bing_val['bing'] = 0; //无商品绑定
                 }
                 $bing_val['kezi']=$ke->retWord($bing_val['kezi']);
                 
                 //获取售卖方式
                 $temInfo = ApiModel::pro_api('GetProductInfo', array('p_id' => $detail[$p_key]['id']));
                 $detail[$p_key]['is_alone'] = 0;
                 if(isset($temInfo['is_alone'])){
                     $detail[$p_key]['is_alone'] = $temInfo['is_alone'];
                 }
            }
            $detail_num = true;
        }else{
            $detail_num = false;
        }
        $this->assign('order_sn_str', $ids);
        //这里增加判断，如果配送类型是门店的话，配送方式要取address｜前面的体验店名称
        if ($orderinfo['return_msg']['distribution_type'] == 1) {
            $address = $orderinfo['return_msg']['address'];
            $pos = strpos($address, "|");
            $address = substr($address, 0,$pos);
            if ($address == "自营") {
                $newaddress = substr($orderinfo['return_msg']['address'], ($pos+1));
                $orderinfo['return_msg']['express_id'] = $newaddress;
            }else {
                $orderinfo['return_msg']['express_id'] = $address;
            }
        }

       // print_r($orderinfo['return_msg']);
        $html.= $this->fetch('foreach.html',array(
            'info' => $orderinfo['return_msg'],
            'dd' => $dd,  //数据字典
            'goods_list' => $detail,
            'box_view' =>new WarehouseBoxView(new WarehouseBoxModel(21)),
            'detail_num' => $detail_num,
            'out_order_sn'=>$out_order_sn,
        ));

            }else{
                echo "<div style='font-size:30px;margin-top:30px;text-align:center'>未查询到单号：<span style='color:red;'>{$v}</span> 的此订单！</div>";
            }
        }
        $this->render('print_bill.html', array(
            'html'=>$html,
        ));
    }




    /** 打印提货单 **/
    public function printBills(){
        ini_set('memory_limit','-1');
        set_time_limit(0);        
        
        $ke=new Kezi();
        $ids = _Request::get('_ids');   //订单号字符串
        $sign= _Request::get('sign');
        $order_sn_str = explode(',', $ids);
        $order_sn_str = array_unique($order_sn_str);
        $kuaidiModel = new ExpressModel(1);
        $SalesChannelsmodel = new SalesChannelsModel(1);
        $dd = new DictView(new DictModel(1));

        $salesmodel=new SalesModel(27);
        $bespokemodel= new BaseMemberInfoModel(17);
        $productinfomodel=new ProductInfoModel(13);
        $WarehouseGoodsModel = new WarehouseGoodsModel(21);
        $gallerymodel = new BaseStyleInfoModel(12);
        $html = '';
        
        
        ob_start(); 
        $kk=0;
        foreach($order_sn_str AS $k => $v){         
            $kk++;
           
            ob_clean();
            
            $orderinfo = $salesmodel->GetPrintBillsInfo($v);      //通过接口，获取 订单信息
            
            if(!empty($orderinfo['gift_id'])){
                if($orderinfo['create_time']<'2015-10-23 00:00:00'){
                    $gifts = $this->gifts;
                    $gift = '';
                    $gift_ids = explode(',',$orderinfo['gift_id']);
                    $gift_nums = explode(',',$orderinfo['gift_num']);
                    foreach ($gift_ids as $key=>$vo){
                        if(isset($gifts[$vo])){
                            $gift_num = !empty($gift_nums[$key])?$gift_nums[$key]:1;
                            $gift .= $gifts[$vo].$gift_num."个,";                            
                        } 
                    }
                    if($gift != ''){
                        $orderinfo['gift'] = trim($gift,',');
                    }
                }else{
                    $orderinfo['remark'] = '';
                }            
                
            }
            if (isset($orderinfo['order_pay_type'])){
                //获取支付方式 拼接
                $order_pay_type = $orderinfo['order_pay_type'];
                if($order_pay_type){
                    $newmodel =  new PaymentModel($order_pay_type,2);
                    $orderinfo['order_pay_name'] = $newmodel->getValue('pay_name');
                }else{
                    $orderinfo['order_pay_name']='无';
                }
            }

            //订单来源
            if (isset($orderinfo['customer_source_id'])){
                $customer_source_id = $orderinfo['customer_source_id'];
                if($customer_source_id){
                    $CustomerSourcesModel = new CustomerSourcesModel($customer_source_id , 1);
                    $orderinfo['customer_source_id'] = $CustomerSourcesModel->getValue('source_name');
                }else{
                    $orderinfo['customer_source_id']='——';
                }
            }
                       
            if (!empty($ret['out_order_sn'])) {
                $out_order_sn = $ret['out_order_sn'];
            }  else {
                $out_order_sn = "";
            }
            if(!empty($orderinfo)){
            	if($orderinfo['return_msg']['distribution_type']==2){
                 if(empty($orderinfo['express_id'])){
                     exit("订单：$v 快递类型不能为空");
                 }
            	}
                $orderinfo['express_id']=$kuaidiModel->getNameById( $orderinfo['express_id'] );
                $orderinfo['express_id'] =  $orderinfo['express_id'] ? $orderinfo['express_id']: '——';

                $orderinfo['user_name'] = '--';
                //获取单据会员名字
                /*
                $user = $bespokemodel->GetMemberByMember_id( $orderinfo['user_id'] );
                if (isset( $orderinfo['user_id'] ) && !empty( $orderinfo['user_id'] )){
                    if($user)
                    {
                        $orderinfo['user_name'] = $user['member_name'];
                    }
                }
                */
               //获取单据明细
               $detail = $salesmodel->GetOrderInfoByOrdersn($orderinfo['order_sn']);
               
               //获取配货单单据的配送类型
               if($orderinfo['distribution_type'] == 1){
                //如果下单的配送类型 为1 (数字字典 sales.distribution_type)。到门店 则在提货单的配送类型中。显示下订单的门店名
                

                    $orderinfo['department_id'] = strstr($orderinfo['address'], '|' , true);

                }else{
                    $orderinfo['department_id'] = $dd->getEnum('sales.distribution_type', $orderinfo['distribution_type']);
                }
                //获取订单明细是否绑定货品信息 + 柜位号
                
                if(!empty($detail)){
                    foreach($detail as $p_key => &$bing_val){

                        //获取图片 拼接进数组
                        //$gallerymodel = new BaseStyleInfoModel(12);
                        $gallery_data = $gallerymodel->GetStyleGalleryInfo($bing_val['goods_sn'],1);
                        //$gallery_data是一个二维数组
                        if(isset($gallery_data[0]['thumb_img'])){
                            $detail[$p_key]['goods_img']=$gallery_data[0]['thumb_img'];
                        }else{
                            $detail[$p_key]['goods_img']='';
                            //$detail[$p_key]['goods_img']='images/styles/201007/1279189436925042936.jpg';
                        }
                        $detail[$p_key]['cat_type_name']=isset($gallery_data[0]['cat_type_name']) ? $gallery_data[0]['cat_type_name'] : ""; 
                        $detail[$p_key]['box_id'] = '无';


                        $bing_val['bing'] = 0; //无商品绑定
                        $res=$WarehouseGoodsModel->getOrderGoodsAndBox($bing_val['id']);
                        if($res){
                            $bing_val['bing'] = 1; //有商品绑定
                            $bing_val['goods_id'] = $res['goods_id'];
                            $bing_val['warehouse'] = $res['warehouse'];
                            $bing_val['box_id']=$res['box_sn'];                           
                        } 
                        $bing_val['kezi']=$ke->retWord($bing_val['kezi']);

                        $temInfo=$productinfomodel->GetProductInfoByOrderID($detail[$p_key]['id']);
                        $detail[$p_key]['is_alone'] = 0;
                        if(isset($temInfo['is_alone'])){
                            $detail[$p_key]['is_alone'] = $temInfo['is_alone'];
                        }
                    }
                    $detail_num = true;
                }else{
                    $detail_num = false;
                }
                $this->assign('order_sn_str', $ids);
                //这里增加判断，如果配送类型是门店的话，配送方式要取address｜前面的体验店名称
                if ($orderinfo['distribution_type'] == 1) {
                    $address = $orderinfo['address'];
                    $pos = strpos($address, "|");
                    $address = substr($address, 0,$pos);
                    if ($address == "自营") {
                        $newaddress = substr($orderinfo['address'], ($pos+1));
                        $orderinfo['express_id'] = $newaddress;
                    }else {
                        $orderinfo['express_id'] = $address;
                    }
                }
               

               // print_r($orderinfo['return_msg']);
                $html.= $this->fetch('foreach.html',array(
                    'info' => $orderinfo,
                    'dd' => $dd,  //数据字典
                    'goods_list' => $detail,
                    //'box_view' =>new WarehouseBoxView(new WarehouseBoxModel(21)),
                    'detail_num' => $detail_num
                    
                ));

            }else{
                $html.= "<table class=\"PageNext\" cellspacing=\"0\" cellpadding=\"1\" width=\"100%\" border=\"0\"><tr><td><hr><b>未查询到单号/或者订单被取消或者关闭：<span style='color:red;'>{$v}</span></b> <hr></td></tr></table>";
            }

            $this->render('print_bill.html', array(
            'html'=>$html,
            ));
            
            flush();          
        }
         flush();    
    }



    //改变单据提货单的打印状态
    public function updatePrintTihuo($params){
        $result = array('success' => 0, 'error' => '程序异常,打印失败!请再试一遍');
        $sign = _Request::get("sign");
        $order_sn_str = $params['order_sn'];
        $order_sn_list= explode(',',$order_sn_str);
        $salesmodel=new SalesModel(27);
        foreach($order_sn_list as $order_sn){
            $orderinfo = $salesmodel->GetPrintBillsInfo($order_sn);      //通过接口，获取 订单信息
            if (isset($orderinfo['id'])){
                $order_id = $orderinfo['id'];
                //打印回写订单日志
                $remark = "订单：".$orderinfo['order_sn']." 打印提货单(仓库待配货列表)";
                if(!empty($sign)){
                    $remark = "订单：".$orderinfo['order_sn']." 打印提货单(销售管理批量打印订单)";
                }
                $orderLog = array(
                    'order_id'=>$order_id,
                    'order_status'=>$orderinfo['order_status'],
                    'shipping_status'=>$orderinfo['send_good_status'],
                    'pay_status'=>$orderinfo['order_pay_status'],
                    'create_user'=>$_SESSION['userName'],
                    'create_time'=>date('Y-m-d H:i:s'),
                    'remark'=>$remark ,
                );
                $salesmodel->insertOrderAction($orderLog);
            }
        }
        $res = ApiSalesModel::updatePrintTihuo($order_sn_str);
        $result = array('success' => 1, 'error' => '打印成功');
        Util::jsonExit($result);
    }


    /** 单据详情页 **/
    public function detail($params){
    	if(isset($params['ss']) && $params['ss'] == 1){
    		$this->DetailSearch($params); return ;
    	}
		$order_id = $params['id'];
		//$order_id = '1000000000000';
		//获取外部订单号
		/*$ret = ApiSalesModel::GetOutOrderInfoByOrderId($order_id);
		if (!empty($ret['return_msg']['out_order_sn'])) {
			$out_order_sn = $ret['return_msg']['out_order_sn'];
		} else {
			$out_order_sn = "";
		}*/

		//获取单据信息
		$orderInfo = ApiSalesModel::GetOrderInfoByOrderId($order_id);
		$data = $orderInfo['return_msg'];

		if(!count($data))
		{
			echo '订单号不存在或者您没有权限查看，请检查。';exit;
		}

		//获取单据会员名字
		if (isset( $data['user_id'] ) && !empty( $data['user_id'] )){
			$user = ApiSalesModel::GetUserInfor( $orderInfo['return_msg']['user_id'] );
			if( $user['data'] != '未查询到此会员' ){
				$data['user_name'] = $user['data']['member_name'];
			}
		}
		//获取销售渠道
		$SalesChannelsModel = new SalesChannelsModel(1);
		$getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", array('id'=>$data['department_id']));
		if(!empty($getSalesChannelsInfo))
		{
			$data['channel_name'] = $getSalesChannelsInfo[0]['channel_name'];
		}

		//获取客户来源
		$CustomerSourcesModel = new CustomerSourcesModel(2);
		$CustomerSourcesInfo = $CustomerSourcesModel->getCustomerSourcesList("source_name",array('id'=>$data['customer_source_id']));
		if(!empty($CustomerSourcesInfo))
		{
			$data['source_name'] = $CustomerSourcesInfo[0]['source_name'];
		}
		#检测是否有退款操作 有则不能操作
		$exit_tuikuan_info = ApiModel::sales_api('getOrder_infoByOrder_sn',array('order_sn'),array($orderInfo['return_msg']['order_sn']));
		if($exit_tuikuan_info){
			$exit_tuikuan = empty($exit_tuikuan_info['return_msg']['apply_return'])?1:$exit_tuikuan_info['return_msg']['apply_return'];
		}
		//老罗确认只用apply_return判断是否有退款操作
        //$exit_tuikuan = ApiModel::sales_api('isHaveGoodsCheck',array('order_sn'),array($orderInfo['return_msg']['order_sn']));
        //$exit_tuikuan = $exit_tuikuan['return_msg'];
		//var_dump($exit_tuikuan);exit;

    	/*
    	获取订购类型和公司配置表，根据订单订购类型获取显示的出库公司
    	1.深圳东方美宝网络科技有限公司 250
    	2.卓越代收款 90
    	3.无锡买卖宝信息技术有限公司 258
    	4.唯品会代销 170
    	5.深圳国银通宝有限公司 263
    	6.上海尚银信息科技有限公司 257
    	7.上海寺库电子商务有限公司 281
    
    	北京陌陌科技有限公司  264
    	陌陌商城比较特殊，陌陌商城的渠道目前是总公司的业务，但是从7月1日重新签合同 模式变了 到时候会就会属于深圳分公司的业务，也就是7月1日前它的渠道绑定总公司，7月1日开始陌陌商城渠道绑定成深圳分公司。
    	*/
    	//暂时写死 过半年估计可以去掉了 2015/6/27 星期六 放开 呵呵
    	$company = $this->get_company_html($data['order_pay_type'],$orderInfo['return_msg']['distribution_type'],$orderInfo['return_msg']['create_time']);
    	//var_dump($company);exit;
    
    	$this->render('wait_distribution_show.html',array(
    		'order_id' => $order_id,
    		'orderInfo' => $data,
    		'out_order_sn' => '',
    		'bar' => Auth::getViewBar(),
    		'exit_tuikuan'=>$exit_tuikuan,
    		'company' => $company
    	));
    }
    
    public function getOrderActionlist($params) {
        $order_sn = $params['order_sn'];
        
        $order_action = ApiModel::sales_api("getOrderActionListBySn",array("order_sn"), array($order_sn));
        $order_action = $order_action['return_msg'];

        $this->render('order_action_list.html',array(
			'order_action'=>$order_action,
        ));
    }
    
	/** 根据 order_id / order_sn 获取单据明细(带分页)  **/
	public function getGoodsListByOrder($params){
		$args = array(
			'mod'   => _Request::get("mod"),
			'con'   => substr(__CLASS__, 0, -10),
			'act'   => __FUNCTION__,
		);


		$page = _Request::getInt("page",1);
		$order_sn = $params['order_sn'];
		//1、获取订单的配货状态
        $has_company = $this->hasCompany();
		//$peihuo = ApiSalesModel::GetDeliveryStatus2($order_sn, ' a.`delivery_status`',$has_company);
		//$peihuo_status = $peihuo['return_msg']['delivery_status'];

		//$goods_list = ApiSalesModel::GetOrderDetailByOrderId($order_sn);
		$SalesModel = new SalesModel(27);
		$peihuo = ApiSalesModel::GetDeliveryStatus2($order_sn, ' a.`delivery_status`,a.`referer`',$has_company);
		$peihuo_status = $peihuo['return_msg']['delivery_status'];
		$referer = $peihuo['return_msg']['referer'];
		$goods_list=$SalesModel->GetOrderInfoArrByOrdersn($order_sn);
		//3、获取货品款号字符串，数量字符串
		$goods_sns = '';
		$goods_nums = '';
		foreach($goods_list as $k => $v){
		  if($v['is_finance']==2 ){
			$goods_sns .= ','.$v['goods_sn'];
			$goods_nums .= ','.$v['goods_count'];
            }
		}
		$pageData = $goods_list;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'wait_order_goods_info_detail';

		//4、检查是否有销售单，如果有已保存或者审核的单据，取出货号和绑定的订单明细
		$model = new WarehouseBillInfoSModel(21);

		$relGoodsid = $model->getGoodsidByOrderSn($order_sn);
		$exsis_S = 0;//销售单是否有效的
		$arr_order_id_goods = array();//array('明细id'=>货号)
		if ($relGoodsid['error'] ==0)
		{
			$exsis_S =1;//销售单有效则显示货号
			$goooog = $relGoodsid['data'];
			//将二维数组转换为一位数组
			foreach ($goooog as $val)
			{
				$arr_order_id_goods[$val['order_goods_id']] = $val['goods_id'];
			}
		}
		$this->render('order_detail.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$goods_list,
			'goods_sns'=>$goods_sns,
			'goods_nums'=>$goods_nums,
			'exsis_S'=>$exsis_S,
			'referer' => $referer,
			'arr_order_id_goods'=>$arr_order_id_goods
		));
    }

    /**
    * 开始销账
    * 1/提交的数据将 订单的明细id 与 输入的货号，提交过来。
    * 2/根据输入的货号，到warehouse_goods库去查询order_goods_id字段的值
    * 3/查询到的order_goods_id 与对应的订单明细id对比，比对上了，就是该订单绑定的货品，可以配货。对比不上则不是，不予配货
    */
    public function xiaozhang($params)
    {
		//定义淘宝和京东的库存
		$departmentid = 0;
		$warehouse_tm_arr = array(482=>'淘宝黄金',484=>'淘宝素金');
		$warehouse_jd_arr = array(483=>'京东黄金',485=>'京东素金');
		
		if(empty(str_replace(',' , '',$params['goods_ids'])))
		{
			$result['error'] = '请输入销账的货品';
			Util::jsonExit($result);
		}
		//var_dump($params);exit;
		if($params['from_company_id'] == "")
		{
			$result['error'] = '请选择出库公司';
			Util::jsonExit($result);
		}
		$company           = explode("|",$params['from_company_id']);
		$from_company_id   = $company[0];
		$from_company_name = $company[1];
		if ($params['distribution_type'] ==1 and $from_company_name=='总公司')
		{
			$result['error'] = '订单配送方式为门店不能从总公司出货';
			Util::jsonExit($result);
		}

		$result        = array('success' => 0, 'error' => '', 'compare' => 0);
		$order_id      = $params['order_id'];                    //订单id
		$order_sn      = $params['order_sn'];                    //订单号
		$order_money   = $params['order_money'];				 //订单商品总额
		$orderDetailId = $params['orderDetailId'];				 //订单货品明细id
        
		/** 【需求】张宇提  现在需要恢复以前销账规则先注释 销账准备start**/
		
		/**销账结束end***/

		//验证订单状态是否能操作
		$apisalesmodel =  ApiSalesModel::VerifyOrderStatus($order_sn);
		//接收，处理提交过来的货号
		$goods_id_str =  substr($params['goods_ids'],1,(strlen($params['goods_ids'])-1));
		$goods_id_arr = explode(',', $goods_id_str);
        $WarehouseGoodsModel = new WarehouseGoodsModel(21);
		//接收，处理提交过来的款号
		$goods_sns_str = ltrim($params['goods_sns'], ',');
		$goods_sns_arr = explode(',', $goods_sns_str);

		//接收，处理提交过来的订单明细id
		$orderDetailId_str = ltrim($orderDetailId, ',');
		$orderDetailId_arr = explode(',', $orderDetailId_str);
		//$result['error'] = "$goods_id_str-非法货号-$orderDetailId_str";
		//Util::jsonExit($result);
		/*
		if(count($goods_id_arr) != count($orderDetailId_arr))
		{  //如果提交的订单明细数量与 输入的货号数量不对等
			$result['error'] = '请填写所有的货号再销账！';
			Util::jsonExit($result);
		}
       */
		
		
		$goodsWarehouseModel = new GoodsWarehouseModel(21);
		$BillInfoPModel = new WarehouseBillInfoPModel(21);

		$goods_warehouse_error = '';    //检测是否上架错误提示语
		$warehouse_goods_error = '';    //错误提示语

		$goodsInfo = array();
		$goodsList = array();
		$warehouegoods = array();   //存储仓库货品 信息容器
		$all_price_goods = 0;//此字段用来存原始成本价的总金额---用来计算销售价格
		$zhuancang_goods_arr = array();

		$apiModel = new ApiStyleModel();
		$goods_total=0;
		$chengbenjia_total=0;
		$goods_list=array();
		$processorModel = new SelfProccesorModel(13);
		$salesModel = new SalesModel(27);
        foreach($goods_id_arr as $key => $goods_id)
        {
        	if($goods_id==''){
        		continue;
        	}
        	/*
			if(!$goods_id)
			{
				$result['error'] = '请填写所有的货号再销账！';
				Util::jsonExit($result);
			}
           */
			if(!is_numeric($goods_id))
			{
				$result['error'] = "非法货号：<span style='color:red;'>{$goods_id}</span> 不是纯数字";
				Util::jsonExit($result);
			}
			$order_detail_id=$orderDetailId_arr[$key];
			$order_info=$salesModel->getAppOrderInfoById($order_detail_id);
			$bc_id=$order_info['bc_id'];
			$is_stock_goods=$order_info['is_stock_goods'];
			$order_pay_status=$order_info['order_pay_status'];
			
			if($bc_id > 0) {
					$buchan_info = $processorModel->selectProductInfo("status","id={$bc_id}",2);
					//不需布产11，已取消10，已出厂9，作废8，部分出厂7，质检完成6，质检中5，生产中4，已分配3，待分配2,初始化1
					if($buchan_info['status']!=9 && $buchan_info['status'] !=11){
						if($is_stock_goods==0 || $order_pay_status==1){
							$result['error'] = "{$order_detail_id}此货3号不满足配货条件（不需布产/已出厂/[支付定金/财务备案/已付款且货品类型是现货]），不允许配货";
							Util::jsonExit($result);
						}
					}
					
					
			}elseif($is_stock_goods==0 || $order_pay_status == 1){
			        $result['error'] = "{$order_detail_id}此货号不满足配货条件（不需布产/已出厂/[支付定金/财务备案/已付款且货品类型是现货]），不允许配货";
					Util::jsonExit($result);
			}
			
		
			
			$goods_info = $WarehouseGoodsModel->getGoodsByGoods_id($goods_id);
			if(!count($goods_info))
			{
				$result['error'] = "货号：<span style='color:red;'>{$goods_id}</span> 不存在，不允许配货。";
				Util::jsonExit($result);
			}
			
			$goods_total+=$goods_info['mingyichengben'];//计算总价
			$chengbenjia_total+=$goods_info['chengbenjia'];// 
			//$goods_info['pifajia']=$goods_info['mingyichengben']*(1+0.08);
			$goods_info['detail_id']=$order_detail_id;
			$goods_info['order_sn']=$order_sn;			
			$goods_info['goods_price']=$goods_info['mingyichengben']*(1+0.08);
			$goods_list[]=$goods_info;
			
			//检测货品 是否是绑定这个订单的货
			$order_goods_id = $goods_info['order_goods_id'];
			//如果输入的货号 如果没有绑定的话，判断定的此款是否是可以不绑定也可以配货的

			//echo $goods_sns_arr[$key];exit;
			$goods_sn = $goods_sns_arr[$key];
			$style_info = $apiModel->GetStyleInfoBySn($goods_sn);

			if(count($style_info) && $style_info['bang_type'] == 2)//款存在并且是不需绑定的（低值款）
			{
				//判断输入的货号和定的款是否匹配，匹配的过
				if(strtoupper($goods_info['goods_sn']) != strtoupper($style_info['style_sn']))
				{
					$result['error'] = "货号：<span style='color:red;'>{$goods_id}</span> 和订单所定款不同，不允许销账。";
					Util::jsonExit($result);
				}
				$WarehouseGoodsModel->build_goods($orderDetailId_arr[$key],$goods_id);

			}else{
				if(!$goods_info['order_goods_id'])
				{
					$result['error'] = "货号：<span style='color:red;'>{$goods_id}</span> 没有绑定订单，不允许配货";
					Util::jsonExit($result);
				}
				//输入的货号如果有绑定的话 就判断下面的
				if($goods_info['order_goods_id'] != $orderDetailId_arr[$key])
				{
					$result['error'] = "货号：<span style='color:red;'>{$goods_id}</span> 绑定的订单信息与订单的明细对应不上，不允许销账!<br/>";
					Util::jsonExit($result);
				}
			}

			//验证输入的货号是否 库存状态
			if($goods_info['is_on_sale'] != 2)
			{
				$warehouse_goods_error .= "货号：<span style='color:red;'>{$goods_id}</span> 不是库存状态，不允许销账1111!<br/>";
			}
			
			//验证非淘宝的订单是否销了淘宝的货品
			//商品所在的仓库
			$warehouseid = $goods_info['warehouse_id'];
			if($departmentid != 2 && in_array($warehouseid,array_keys($warehouse_tm_arr)))
			{
				$warehouse_goods_error .= "货号：<span style='color:red;'>{$goods_id}</span> 
				非淘宝的订单，用了淘宝黄金/素金仓库的货品，下单不规范导致，请联系桥林是否允许售卖，该货品不允许销账!<br/>";
			}elseif($departmentid  != 71 && in_array($warehouseid,array_keys($warehouse_jd_arr)))
			{
				$warehouse_goods_error .= "货号：<span style='color:red;'>{$goods_id}</span> 
				非京东的订单，用了京东黄金/素金仓库的货品，下单不规范导致，请联系任强是否允许售卖，该货品不允许销账!<br/>";	
			}

			//根据货号，去拉取仓库里 warehoue_goods 里的商品信息，做写入销售单据明细用
			$warehouegoods_one= $WarehouseGoodsModel->getGoodsByGoods_id($goods_id);
			$warehouegoods_one['detail_id'] = $order_goods_id;
			/***【需求】张宇提  现在需要恢复以前销账规则先注释 销账准备start*****/
			/*
			$warehouegoods_one['goods_price'] = $order_goods_info[$order_goods_id]['goods_price'];//销账价格计算用 商品价格
			if($order_goods_info[$order_goods_id]['favorable_status'] == 3)
			{
				$warehouegoods_one['favorable_price'] = $order_goods_info[$order_goods_id]['favorable_price'];//销账价格计算用 优惠价格
			}
			else
			{
				$warehouegoods_one['favorable_price'] = 0;//销账价格计算用 优惠价格
			}
			*/
			/***end**/
			$all_price_goods += $warehouegoods_one['yuanshichengbenjia'];//销账价格计算用
            $warehouegoods[] =  $warehouegoods_one;

            ### 判断如果出库公司选中的时BDD深圳分公司，嘿嘿，不要意思，请你等会，我要去判断有没有总公司的货，有的话自动生成调拨单，给你把货从总部调到深圳分公司来 ### @BY CaoCao
			if($from_company_id == 445 && $goods_info['company_id'] == 58)	//445|BDD深圳分公司  并且 货品在总公司 58
			{
				//准备要转仓的货品
				$zhuancang_goods_arr[] = $goods_id;
			}
			else if($goods_info['company_id'] != $from_company_id)
			{
				$result['error'] = "货号：<span style='color:red;'>{$goods_id}</span> 不是所选出库公司的货品，不允许配货。";
				Util::jsonExit($result);
			}
			$goodsInfo[] = array('goods_id'=>$goods_id, 'is_delete'=>2);

        } /** END froeach **/

        if($goods_warehouse_error)
		{
            $result['error'] = $goods_warehouse_error;
            Util::jsonExit($result);
        }
        if($warehouse_goods_error)
		{
            $result['error'] = $warehouse_goods_error;
            Util::jsonExit($result);
        }
        
        /** 2015-12-26 zzm boss-1015 **/
        /*
        if(isset($_GET['compare']) && !empty($_GET['compare'])){
	        $warehouegoods_model = new WarehouseGoodsModel(21);
	        $total_mingyichenben = $warehouegoods_model->getTotalMingyichengben($goods_id_str);
	        if($order_money < $total_mingyichenben){
	        	$result['error'] = "订单金额低于总成本，是否继续？";
				$result['compare'] = 1;
				Util::jsonExit($result);
	        }
        }
      */

		/****对货品明细数据中 销售价格进行计算 并放入数组中  start****/
		//echo $order_money."<br>";
		$price_sum_last = 0;//用于存除最后一个商品的所以商品成本价格总和
		for ($i=0;$i<count($warehouegoods);$i++)
		{
			 if($i==count($warehouegoods)-1){
				 $warehouegoods[$i]['xiaoshoujia'] = $order_money-$price_sum_last;
			 }
			 else{
				 $warehouegoods[$i]['xiaoshoujia'] =  round(( $warehouegoods[$i]['yuanshichengbenjia']*$order_money)/$all_price_goods,2);
				 $price_sum_last += $warehouegoods[$i]['xiaoshoujia'];
			 }
			// echo  $warehouegoods[$i]['xiaoshoujia']."<br>" ;
		}
		
		//根据订单号查询有相关布产单是否出厂

		//var_dump($warehouegoods);exit;
		/***************end******************************************/

		/*** 【需求】张宇提  现在需要恢复以前销账规则先注释 销账准备start
		        对货品明细数据中 销售价格进行计算 并放入数组中  start
		 1/除最后一个商品：
		  销售价=商品金额 -商品优惠- 订单优惠（（货品原始采购成本/订单原始采购成本总额）*订单优惠；）  + 其他费用/订单商品数量----保留两位数字-四舍五入原则
		 2/最后一个商品：
		  销售价=商品金额 -商品优惠 - 订单优惠（订单优惠-其他商品订单优惠之和） + (其他费用-除最后一个商品其他费用之和)
		  说明：其他费用： 配送费用 + 保价费用+ 支付费用 + 包装费用 + 贺卡费用


		  现在销账没有考虑到退款,有退款的销账可能会出现销账金额不一致或者已退货的商品给销账了
		***********/
		/*
		$coupon_price	= $order_info['coupon_price'];//订单优惠
		$shipping_fee	= $order_info['shipping_fee'];//配送费用
		$insure_fee		= $order_info['insure_fee'];//保价费用
		$pay_fee		= $order_info['pay_fee'];//支付费用
		$pack_fee		= $order_info['pack_fee'];//包装费用
		$card_fee		= $order_info['card_fee'];//贺卡费用
		$order_amount	= $order_info['order_amount'];//订单总金额
	    $qita_fee       = $shipping_fee+$insure_fee+$pay_fee+$pack_fee+$card_fee;//该商品的均摊的其他费用
		$qita_sum_last  = 0;//除最后一个商品其他费用的总和
		$price_sum_last = 0;//用于商品的所以商品销售总和
		$order_fee      = 0;//订单优惠累计
		$str ='';
		//echo  $all_price_goods  ;exit;
		for ($i=0;$i<count($warehouegoods);$i++)
		{
			 if($i==count($warehouegoods)-1)
			 {
				 $warehouegoods[$i]['xiaoshoujia'] = $warehouegoods[$i]['goods_price']-$warehouegoods[$i]['favorable_price']-($coupon_price-$order_fee)+($qita_fee-$qita_sum_last);
				 $price_sum_last += $warehouegoods[$i]['xiaoshoujia'];//计算销账总金额
				$str .= ($i+1)."商品". $warehouegoods[$i]['goods_id']."销账金额：".$warehouegoods[$i]['xiaoshoujia']."<br>";
			 }
			 else
			{
				 $warehouegoods[$i]['xiaoshoujia'] =   $warehouegoods[$i]['goods_price']-$warehouegoods[$i]['favorable_price']-round($warehouegoods[$i]['yuanshichengbenjia']/$all_price_goods*$coupon_price,2)+round($qita_fee/count($warehouegoods),2);
				 $price_sum_last += $warehouegoods[$i]['xiaoshoujia'];//计算销账总金额
				 $qita_sum_last  += round($qita_fee/count($warehouegoods),2);//计算其他费用之和
				 $order_fee      += round($warehouegoods[$i]['yuanshichengbenjia']/$all_price_goods*$coupon_price,2);//计算订单优惠之和
				$str .= ($i+1)."商品". $warehouegoods[$i]['goods_id']."销账金额：".$warehouegoods[$i]['xiaoshoujia']."<br>";
			 }
		}
		if(strval($order_amount) != strval($price_sum_last))      //浮点型 由于精度的问题， 不能直接比较，两者扩大100倍再做比较
		{
		   $result['error'] = "<font color='red'>订单金额与销账金额不一致</font><br>".$str."销账总金额".$price_sum_last."<br>订单总金额：".$order_amount;
			Util::jsonExit($result);
		}
		*/
		/***************end*******************/
		$salesModel = new SalesModel(27);
		$wholesale_id=$salesModel->getWholesaleId($order_id);
		if(!$wholesale_id)
		{
			$result['error'] = "批发客户不能为空";
			Util::jsonExit($result);
		}
		$pifajia=$goods_total*(1+0.08);
         //配货发货
		$bill_info=array(
			'goods_num'	=>count($goods_list),
			'bill_note'=>'销账自动批发销售(经销商天生一对)',	
			'goods_total'=>$goods_total,
			'shijia'=>'0.0',
			'pifajia'=>$pifajia,
			'from_company_id'=>$from_company_id,
			'from_company_name'=>$from_company_name,
			'wholesale_id'=>$wholesale_id	,			
		);
       // $res = $BillInfoSModel->createBillInfoS($order_id, $order_sn, $goodsInfo, $order_money, $warehouegoods, $from_company_id, $from_company_name, $zhuancang_goods_arr);
		$res = $BillInfoPModel->createBillInfop($bill_info , $goods_list,$order_sn);
        if(!$res['success'])
		{
            $result['error'] = $res['error'];
            $result['success'] = 0;
        }
		else
		{
            $result = array('success'=>1, 'error'=>'销账成功'."  ".$showMsg , 'order_sn' => $order_sn, 'compare' => 0);
            //修改可销售商品状态
            $change=[];$where=[];
            foreach ($goods_id_arr as $k => $v)
			{
                $where[$k]['goods_id'] = $v;
                $change[$k]['is_sale'] = '0';	//下架
                $change[$k]['is_valid'] = '2';	//已销售
            }
            
            $ApiSalePolcy = new ApiSalepolicyModel();
            $ApiSalePolcy->setGoodsUnsell($change,$where);
            
            //同步新货号到订单明细
           foreach ($goods_list as $v){           	  
                    $detailAccountArr=$salesModel->getOrderAccountByDetailId($v['detail_id']);
					$order_id=$detailAccountArr['order_id'];
					$goods_price=$detailAccountArr['goods_price'];	
					$favorable_price=$detailAccountArr['favorable_price'];
					$order_amount=$detailAccountArr['order_amount']-$goods_price+$v['goods_price']+$favorable_price;//订单总金额
					$money_unpaid=$order_amount-$detailAccountArr['money_paid']+$detailAccountArr['real_return_price'];//订单未付
					$money_unpaid=$money_unpaid >= 0 ?$money_unpaid:0;
					$goods_amount=$detailAccountArr['goods_amount']-$goods_price+$v['goods_price'];
				    $data = array('goods_id'=>$v['goods_id'],'goods_price'=>$v['goods_price'],'favorable_price'=>0,'delivery_status'=>5);
				    $res1=$salesModel->updateAppOrderDetail($data,"id={$v['detail_id']}");
				    if($res1){
				        $res2=$salesModel->updateTable('app_order_account',array('goods_amount'=>$goods_amount,'order_amount'=>$order_amount,'money_unpaid'=>$money_unpaid),"id=".$detailAccountArr['id']);
                        $salesModel->AddOrderLog($detailAccountArr['order_sn'],"货号".$detailAccountArr['goods_id']."商品价格由{$goods_price}改成{$v['goods_price']},商品优惠清零");
				    }
           } 
            
            //
            /*----------------*/
        }
        Util::jsonExit($result);
    }

    /**
    * 待配货详情页里的 搜索功能 ......
    */
    public function DetailSearch($params){
           $order_sn = $params['order_sn'];
           $has_company = $this->hasCompany();
           $data = ApiSalesModel::GetDeliveryStatus2($order_sn, 'a.id',$has_company);
           if(!$data['error']){
                $order_id = $order_id = $data['return_msg']['id'];
                //获取外部订单号
                $ret = ApiSalesModel::GetOutOrderInfoByOrderId($order_id);
                if (!empty($ret['return_msg']['out_order_sn'])) {
                        $out_order_sn = $ret['return_msg']['out_order_sn'];
                }  else {
                        $out_order_sn = "";
                }
                //获取订单日志列表
                $order_action = ApiModel::sales_api("getOrderActionList",array("order_id"), array($order_id));
                $order_action = $order_action['return_msg'];
                //获取单据信息
                $orderInfo = ApiSalesModel::GetOrderInfoByOrderId($order_id);
                $data = $orderInfo['return_msg'];

                //检测单据是否已经配过货
                if($data['delivery_status'] == 5){      //已经配过货，就不让显示在搜索页
                    $result['success'] = 0;
                    $result['error'] = "订单 <span style='color:red;'>{$order_sn}</span> 已经配过货";
                    Util::jsonExit($result);
                }

                if(!count($data))
                {
                        echo '订单号不存在，请检查。';exit;
                }

                //获取单据会员名字
                if (isset( $data['user_id'] ) && !empty( $data['user_id'] )){
                        $user = ApiSalesModel::GetUserInfor( $orderInfo['return_msg']['user_id'] );
                        if( $user['data'] != '未查询到此会员' )
                        {
                                $data['user_name'] = $user['data']['member_name'];
                        }
                }
                //获取销售渠道
                $SalesChannelsModel = new SalesChannelsModel(1);
                $getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", array('id'=>$data['department_id']));
                if(!empty($getSalesChannelsInfo))
                {
                        $data['channel_name'] = $getSalesChannelsInfo[0]['channel_name'];
                }

                //获取客户来源
                $CustomerSourcesModel = new CustomerSourcesModel(2);
                $CustomerSourcesInfo = $CustomerSourcesModel->getCustomerSourcesList("source_name",array('id'=>$data['customer_source_id']));
                if(!empty($CustomerSourcesInfo))
                {
                        $data['source_name'] = $CustomerSourcesInfo[0]['source_name'];
                }
                #检测是否有退款操作 有则不能操作
                $exit_tuikuan = ApiModel::sales_api('isHaveGoodsCheck',array('order_sn'),array($orderInfo['return_msg']['order_sn']));
                //var_dump($exit_tuikuan);exit;

				$company = $this->get_company_html($data['order_pay_type'],$orderInfo['return_msg']['distribution_type'],$orderInfo['return_msg']['create_time']);


                $result['content'] = $this->fetch('wait_detail_search_top.html',array(
                        'order_id' => $order_id,
                        'order_action' => $order_action,
                        'orderInfo' => $data,
                        'out_order_sn' => $out_order_sn,
                        'bar' => Auth::getViewBar(),
                        'exit_tuikuan'=>$exit_tuikuan['return_msg'],
                        'company' => $company
                ));
                Util::jsonExit($result);
           }else{
                $result['success'] = 0;
                Util::jsonExit($result);
           }
    }
	//用来根据订购类型页面显示的公司列表
	public function get_company_html($order_pay_type,$distribution_type,$create_time)
	{
		//echo $order_pay_type;exit;
		//订单订购类型为陌陌商城 264 写死 制单时间在7月1号之前都是总公司 7月1号之后是深圳分公司
		/*  等所有订单（制单时间在7月份之前的） 都销账完成  然后 就可以删除了  start */
		/*if ($order_pay_type == 264 and $create_time <= '2015-06-30 23:59:59')
		{
			$company=$this->getCompanyList();
			$c = 58;
			//echo $c;exit;
			foreach ($company as $key=>$vv)
			{
					if($vv['id'] == $c)
					{
						$company = array($vv);
						break;
					}
					else
					{
						$company = array();
					}
			}
			//var_dump($company);exit;
			return $company;
		}*/
		/*****************end******************/
		$model_write = new WriteOffCompanyModel(21);
		$pay_type_list = $model_write->getwriteoffList();
		$pay_type_list_new = array();
		if (count($pay_type_list))
		{
			foreach ($pay_type_list as $key=>$val)
			{
				$No_auto[]= $val['pay_type_id'];
				$pay_type_list_new[$val['pay_type_id']] = $val;
			}
		}
        //获取所属公司
        // $has_company = $this->hasCompany();
		//var_dump($pay_type_list_new);exit;
		$company=$this->getCompanyList();
		//如果订单订购类型在配置订购类型中,则取得该订购类型对应公司
		if(in_array($order_pay_type,$No_auto))
		{

			foreach($company AS $kk => $vv)
			{
				if($vv['id'] == $pay_type_list_new[$order_pay_type]['company_id'])
				{
					$company = array($vv);
					break;
				}
				else
				{
					$company = array();
				}
			}
			//var_dump($company);exit;
		}
		else
		{
			//如果 总公司 发体验店的单子 才会把总公司去掉
			foreach($company as $key=>$v)
			{
				if($v['company_name']=='总公司' and $distribution_type == 1)
				{
					unset($company[$key]);
				}
			}
		}
		return $company;
	}

     /*
    * 获取用户所属公司
    *
    */
    public function hasCompany(){
        $userCompanyModel = new UserCompanyModel(1);
        $user_id=Auth::$userId;
        $has_company = $userCompanyModel->getUserCompanyList2(array('user_id'=>$user_id));
        $has_company = array_column($has_company,'company_id');
        return $has_company;
    }


    /*
    * 获取录单类型信息
    * 
    */
    public function getReferers(){
        $salesmodel=new SalesModel(27);
        $referers = $salesmodel->getAllReferers();
        $referers = array_column($referers,'referer');
        $this->assign('referers', $referers);
    }   

}/** END CLASS**/

?>
