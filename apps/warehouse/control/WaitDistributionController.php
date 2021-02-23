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
class WaitDistributionController extends CommonController
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
        $this->render('wait_distribution_search_form.html',array(
            'bar'=>Auth::getBar(),
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
        	   'normal_view'=> isset($params['normal_view']) ? $params['normal_view']  : '',
               'shousuo'=> isset($params['shousuo']) ? $params['shousuo'] : '',
        );
        $where = $args;
        $page = _Request::getInt("page",1);
        //批量 订单号搜索
        if($args['order_sn']){
        	//若 订单号中间存在空格 汉字逗号 替换为英文模式逗号
			$args['order_sn']=str_replace('，',' ',$args['order_sn']);
			$args['order_sn']=trim(preg_replace('/(\s+|,+)/',' ',$args['order_sn']));
			$where['order_sn']="'".str_replace(' ',"','",$args['order_sn'])."'";
        }
        if($args['delivery_status']){
        	$where['delivery_status_str']=$args['delivery_status'];
        }else{
        	$where['delivery_status_str'] = '2,3';
        }
        $delivery_address =$args['delivery_address'];
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

        if(SYS_SCOPE == 'zhanting'){
            $where['hidden'] = '0';
        }
        //$where['shops'] = $shops;
        // $delivery_status['delivery_status']=$delivery_status_str;
        $SalesModel = new SalesModel(27);
       // $result = ApiSalesModel::GetOrderListPages($style_sn,$customer_source_id,$create_time_end,$create_time_start,$sales_channels_id,$is_print_tihuo,$create_user,$delivery_status_str, $order_sn, $page , $page_size = 100);      //通过接口，获取 允许配货/配货中 状态的订单列表
        $result = $SalesModel->GetOrderListPage($where, $page , $page_size = 50);      //通过接口，获取 允许配货/配货中 状态的订单列表
        
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
                 $where = " `order_goods_id` = '{$bing_val['id']}' ";
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
        
        $orderlist=array();

        //ob_start(); 
        $kk=0;
        foreach($order_sn_str AS $k => $v){    
            $orderdata=array();     
            $kk++;
           
            //ob_clean();
            
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
                if(SYS_SCOPE=='boss'){
                    $order_pay_type_limit= json_decode(INVOICE_ORDER_PAY_TYPE_LIMIT,true);  //需要开电子发票的支付方式        
                    if(in_array($orderinfo['order_pay_type'],$order_pay_type_limit) && $orderinfo['invoice_type']!=3)
                        $orderinfo['invoice_type']=2;
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
            	if($orderinfo['distribution_type']==2 ){
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
                
                $boxsum=1;
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
                        $res=$WarehouseGoodsModel->getOrderGoodsAndBox($bing_val['order_detail_id']);
                        if($res){
                            $bing_val['bing'] = 1; //有商品绑定
                            $bing_val['goods_id'] = $res['goods_id'];
                            $bing_val['warehouse'] = $res['warehouse'];
                            $bing_val['box_id']=$res['box_sn'];
                            if($boxsum==1){
                                $boxsum++;
                                $orderdata['box_sn']=$res['box_sn'];
                            }                           
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
               
              
               /*
               // print_r($orderinfo['return_msg']);
                $html.= $this->fetch('foreach.html',array(
                    'info' => $orderinfo,
                    'dd' => $dd,  //数据字典
                    'goods_list' => $detail,
                    //'box_view' =>new WarehouseBoxView(new WarehouseBoxModel(21)),
                    'detail_num' => $detail_num
                    
                ));*/
                $orderdata['info']=$orderinfo;
                $orderdata['goods_list']=$detail;
                $orderdata['detail_num']=$detail_num;
            }else{
                //$html.= "<table class=\"PageNext\" cellspacing=\"0\" cellpadding=\"1\" width=\"100%\" border=\"0\"><tr><td><hr><b>未查询到单号/或者订单被取消或者关闭：<span style='color:red;'>{$v}</span></b> <hr></td></tr></table>";
                $orderdata['info']=$orderinfo;
            }
            /*
            $this->render('print_bill.html', array(
            'html'=>$html,
            ));*/
            $orderlist[]=$orderdata;

            //flush();          
        }
         //flush(); 
        // echo "<pre>";
        // print_r($orderlist); 


         usort($orderlist, function($a, $b) {
              $al = $a['box_sn'];
              $bl = $b['box_sn'];
                    if(empty($al))
                         $al='ZZ';
                    if(empty($bl))
                         $bl='ZZ';
                    if ($al == $bl)
                       return 0;
                    return ($al > $bl) ? 1 : -1;
             }); 
         //    echo "--------------------------------"; 
        // print_r($orderlist); 
        ob_start();      
        foreach ($orderlist as $key => $v) {
            ob_clean();            
            if(!empty($v['info'])){
                $html.= $this->fetch('foreach.html',array(
                    'info' => $v['info'],
                    'dd' => $dd,  //数据字典
                    'goods_list' => $v['goods_list'],
                    //'box_view' =>new WarehouseBoxView(new WarehouseBoxModel(21)),
                    'detail_num' => $v['detail_num']
                    
                ));
            }else{
                $html.= "<table class=\"PageNext\" cellspacing=\"0\" cellpadding=\"1\" width=\"100%\" border=\"0\"><tr><td><hr><b>未查询到单号/或者订单被取消或者关闭：<span style='color:red;'>{$v}</span></b> <hr></td></tr></table>";
            }    
            $this->render('print_bill.html', array(
            'html'=>$html,
            ));
            flush(); 
         } 
    }



    //改变单据提货单的打印状态
    public function updatePrintTihuo($params){
        $result = array('success' => 0, 'error' => '程序异常,打印失败!请再试一遍');
        $sign = _Request::get("sign");
        $order_sn_str = $params['order_sn'];
        $order_sn_list= explode(',',$order_sn_str);
        $salesmodel=new SalesModel(27);
        $order_id_list = array();
        foreach($order_sn_list as $order_sn){
            $orderinfo = $salesmodel->GetPrintBillsInfo($order_sn);      //通过接口，获取 订单信息
            if (isset($orderinfo['id'])){
                $order_id = $orderinfo['id'];
                //打印回写订单日志
                $remark = "订单：".$orderinfo['order_sn']." 打印提货单(仓库待配货列表)";
                if($sign==3){
                    $remark = "订单：".$orderinfo['order_sn']." 打印提货单(唯品会批量打印订单)";
                }else if($sign!=''){
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
                $res = $salesmodel->updatePrintTihuo($orderinfo['order_sn']);
                $salesmodel->insertOrderAction($orderLog);
                $order_id_list[]= $order_id; 
            }
        }
        //$res = ApiSalesModel::updatePrintTihuo($order_sn_str);
        //$res = $salesmodel->updatePrintTihuo($order_sn_str);
        $result = array('success' => 1, 'error' => '打印成功');
        //AsyncDelegate::dispatch("warehouse", array('event' => 'tihuo_bill_printed', 'order_ids' => $order_id_list));
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
        $hidden = Util::zhantingInfoHidden($data);
    	//var_dump($company);exit;
    	$templ = isset($params['qkd']) && $params['qkd'] == 1 ? 'shop_distribution_show.html': 'wait_distribution_show.html';
    	$this->render($templ,array(
    		'order_id' => $order_id,
    		'orderInfo' => $data,
    		'out_order_sn' => '',
    		'bar' => Auth::getViewBar(),
    		'exit_tuikuan'=>$exit_tuikuan,
    		'company' => $company,
            'hidden'=>$hidden
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
		$peihuo = ApiSalesModel::GetDeliveryStatus2($order_sn, ' a.`delivery_status`,a.`referer`,a.hidden',$has_company);
        $hidden = true;
        if(!empty($peihuo['return_msg'])){
            $hidden = Util::zhantingInfoHidden($peihuo['return_msg']);
        }
		$peihuo_status = $peihuo['return_msg']['delivery_status'];
		$referer = $peihuo['return_msg']['referer'];
		$goods_list = ApiSalesModel::GetOrderDetailByOrderId($order_sn);
		$WarehouseGoodsModel = new WarehouseGoodsModel(21);
		//3、获取货品款号字符串，数量字符串
		$goods_sns = '';
		$goods_nums = '';
		foreach($goods_list as $k => $v){
		   $goods_list[$k]['stone_list'] = array();
           $order_detail_id = $v['id'];
           $is_cpdz = $v['is_cpdz'];
           $t_goods_ids = array();
           if($is_cpdz==1){
               $goods_info = $WarehouseGoodsModel->select2("goods_id,pinpai,zhushitiaoma,company_id","order_goods_id='{$order_detail_id}'",2);
               if(!empty($goods_info)){
                   $goods_id  = $goods_info['goods_id'];
                   $company_id = $goods_info['company_id'];
                   $pinpai_arr = explode('/',trim($goods_info['pinpai']));
                   $zhushitiaoma_arr = explode('/',trim($goods_info['zhushitiaoma']));
               }
               if(!empty($goods_info['pinpai'])){
                   foreach ($pinpai_arr as $p){                       
                       $error ="【提示】成品定制货号【{$goods_info['goods_id']}】品牌字段填写的证书号有问题请联系入库人员核实! ";
                       $strWhere = "zhengshuhao='{$p}' and is_on_sale=2 and company_id={$company_id}";
                       $stone_info = $WarehouseGoodsModel->select2("*",$strWhere,2);
                       if(empty($stone_info) ){
                           $error .= "证书号<span style='color:red;'>{$p}</span>找不到符合销账条件的货号！";
                           $goods_list[$k]['stone_list'][]=$error;
                       }else{
                           $stone_info['order_goods_id'] = $order_detail_id;
                           $goods_list[$k]['stone_list'][] = $stone_info;
                           $t_goods_ids[] = $stone_info['goods_id'];
                       }
                   }
               } 
               /*
               if(!empty($goods_info['zhushitiaoma'])){
                   foreach ($zhushitiaoma_arr as $t_k=>$t_goods_id){
                       if(in_array($t_goods_id,$t_goods_ids)){
                           unset($zhushitiaoma_arr[$t_k]);
                       }
                   }
                   foreach ($zhushitiaoma_arr as $p){
                       $strWhere = "goods_id='{$p}' and is_on_sale=2 and company_id={$company_id}";
                       $stone_info = $WarehouseGoodsModel->select2("*",$strWhere,2);
                       $error ="【提示】成品定制货号【{$goods_info['goods_id']}】主石条码字段填写的货号有问题请联系入库人员核实! ";
                       if(empty($stone_info)){
                           $error .= "货号<span style='color:red;'>{$p}</span>不存在！";
                           $goods_list[$k]['stone_list'][] = $error;                            
                       }else if($stone_info['company_id']!=$company_id){
                           $error .= "货号<span style='color:red;'>{$p}</span>所在公司【{$stone_info['company']}】不对！";
                           $goods_list[$k]['stone_list'][] = $error;                            
                       }else if($stone_info['is_on_sale']!= 2){
                           $error .= "货号<span style='color:red;'>{$p}</span>不是库存中！";
                           $goods_list[$k]['stone_list'][] = $error;
                       }else{                           
                           $stone_info['order_goods_id'] = $order_detail_id;
                           $goods_list[$k]['stone_list'][] = $stone_info;
                       }
                       
                   }
               }*/
           
           }
		  if($v['is_finance']==2 ){
			$goods_sns .= ','.$v['goods_sn'];
			$goods_nums .= ','.$v['goods_count'];
          }
		}
		//print_r($goods_list);
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
			'peihuo_status' => $peihuo_status,
			'referer' => $referer,
			'arr_order_id_goods'=>$arr_order_id_goods,
            'hidden'=>$hidden
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
		if (isset($params['distribution_type'])&&$params['distribution_type'] ==1 and $from_company_name=='总公司')
		{
			$result['error'] = '订单配送方式为门店不能从总公司出货';
			Util::jsonExit($result);
		}

		$result        = array('success' => 0, 'error' => '','submits'=>0, 'compare' => 0);
		$order_id      = $params['order_id'];                    //订单id
		$order_sn      = $params['order_sn'];                    //订单号
		$order_money   = $params['order_money'];				 //订单商品总额
		$orderDetailId = $params['orderDetailId'];				 //订单货品明细id
		$submits = _Request::get('submits');
		$stone_list_spit = _Post::get('stone_list');
		$stone_list_spit = explode(',',$stone_list_spit);

		$stone_list = array();
		foreach ($stone_list_spit as $k1=>$v1){
		    $arr = explode('|',$v1);
		    if(count($arr)==3){
		        $stone_list[$arr[0]][$arr[1]]=$arr[2];
		    }
		}

		/** 【需求】张宇提  现在需要恢复以前销账规则先注释 销账准备start**/
		/*
		$goods_list = ApiModel::sales_api('getOrderDetailsId',array('order_sn'),array($order_sn));//获取的是订单信息和明细
		$order_info = $goods_list['return_msg']['0'];//订单信息
		$order_goods_info = $goods_list['return_msg']['2'];//订单明细

		foreach ($order_goods_info as $key=>$val)
		{
			unset($order_goods_info[$key]);
			$order_goods_info[$val['id']] = $val;
		}
		*/
		/**销账结束end***/

		//验证订单状态是否能操作
		$orderinfo =  ApiSalesModel::VerifyOrderStatus($order_sn);
		//接收，处理提交过来的货号
		$goods_id_str = ltrim($params['goods_ids'], ',');
		$goods_id_arr = explode(',', $goods_id_str);
        $WarehouseGoodsModel = new WarehouseGoodsModel(21);
		//接收，处理提交过来的款号
		$goods_sns_str = ltrim($params['goods_sns'], ',');
		$goods_sns_arr = explode(',', $goods_sns_str);

		//接收，处理提交过来的订单明细id
		$orderDetailId_str = ltrim($orderDetailId, ',');
		$orderDetailId_arr = explode(',', $orderDetailId_str);
		
		//如果是祼钻或者戒托，判断是否有漏销的情况
		//$orderDetailInfo = ApiSalesModel::GetOrderDetailByOrderId($order_sn);
		$orderDetailInfo = $orderinfo['items'];
		$showMsg ='';
		$wmodel = new WarehouseBillInfoSModel(21);
		$processorModel = new SelfProccesorModel(13);
		$t_has_zp = 0;
		$departmentid = $orderinfo['order']['department_id'];
		$order_item_dict = array();
		foreach ($orderDetailInfo as $detailv)
		{			
			$order_item_dict[$detailv['id']] = $detailv;
			
			$bc_id = (int)$detailv['bc_id'];
		    $is_return = (int)$detailv['is_return'];
		    if($detailv['is_zp']==1 && $detailv['is_finance']==2){
		        $t_has_zp = 1;
		    }   
		    
		    /*boss-977 托钻漏销功能提醒 功能没做好。。现要求注释这段代码
		     * 
		    //托一个单 钻一个单 两单相关联 销托的单时没有提示钻的单还没销 销即销账
		    //托发货的时候没有提示对应的裸钻没有销账，也没有限制，当钻没有销账的时候，托也不可以发货
		    if(strtoupper($detailv['goods_type']) == 'LZ'){//祼钻
		        //找出来对应的托  如果托不在本订单里
		        $tuoInfo = ApiSalesModel::getGoodsInfoByZhengshuhao($detailv['zhengshuhao'],$detailv['order_id']);
		        if($tuoInfo){//托未销，钻可销 且提示托单号
		            if(!$wmodel->checkSbillByOrderSn($tuoInfo['order_sn'])){
		                $showMsg = "<br/><span style='color:red;'>友情提醒：证书号".$detailv['zhengshuhao']." 的钻石有空托订单".$tuoInfo['order_sn']." 未销帐，请勿漏销！！！</span>";
		            }
		        }
		    }else{
		        preg_match('/^(W|M)\w{4,5}-(\w+)-(\w{1})-(\d+)-(\d{2})$/',$detailv['goods_id'],$matches);
    		    if( !empty($matches) && $detailv['zhengshuhao'] )  {//戒托
    		        //找出对应的钻
    		        $tuoInfo = ApiSalesModel::getGoodsInfoByZhengshuhao($detailv['zhengshuhao'],$detailv['order_id']);
    		        if($tuoInfo){//钻未销  不允许销
    		            if(!$wmodel->checkSbillByOrderSn($tuoInfo['order_sn'])){
        		            $showMsg = "<br/><span style='color:red;'>友情提醒：款号".$detailv['goods_sn']." 有祼钻订单".$tuoInfo['order_sn']." 未销帐，请勿漏销！！！</span>";
    		            }
    		        }
    		    }
		    }
		    
		    */
		}
		if(count($goods_id_arr) != count($orderDetailId_arr))
		{  //如果提交的订单明细数量与 输入的货号数量不对等
			$result['error'] = '请填写所有的货号再销账！';
			Util::jsonExit($result);
		}

		if (count($goods_id_arr) != count(array_unique($goods_id_arr)))
		{
			$result['error'] = '一个货号不能同时匹配多个货品';
			Util::jsonExit($result);
		}
		
		$goodsWarehouseModel = new GoodsWarehouseModel(21);
		$BillInfoSModel = new WarehouseBillInfoSModel(21);

		$goods_warehouse_error = '';    //检测是否上架错误提示语
		$warehouse_goods_error = '';    //错误提示语

		$goodsInfo = array();
		$goodsList = array();
		$warehouegoods = array();   //存储仓库货品 信息容器
		$all_price_goods = 0;//此字段用来存原始成本价的总金额---用来计算销售价格
		$zhuancang_goods_arr = array();

		$styleModel = new SelfStyleModel(17);
        $salesModel = new SalesModel(27);
		$apiModel = new ApiStyleModel();
		$t_goods_list = array();	
        $is_mapping_ditil_ware = array();//销账货号与订单明细是否匹配（总公司到客户、不需要绑定销账的货品除外）
        foreach($goods_id_arr as $key => $goods_id)
        {
			if(!$goods_id)
			{
				$result['error'] = '请填写所有的货号再销账！';
				Util::jsonExit($result);
			}

			if(!is_numeric($goods_id))
			{
				$result['error'] = "非法货号：<span style='color:red;'>{$goods_id}</span> 不是纯数字";
				Util::jsonExit($result);
			}

			$goods_info = $WarehouseGoodsModel->getGoodsByGoods_id($goods_id);
			if(!count($goods_info))
			{
				$result['error'] = "货号：<span style='color:red;'>{$goods_id}</span> 不存在，不允许配货。";
				Util::jsonExit($result);
			}		
			//检测货品 是否是绑定这个订单的货
			$order_goods_id = $goods_info['order_goods_id'];
			$goods_info['detail_id'] = $order_goods_id;
			//如果输入的货号 如果没有绑定的话，判断定的此款是否是可以不绑定也可以配货的

			//echo $goods_sns_arr[$key];exit;
			$goods_sn = $goods_sns_arr[$key];
            $isSanShengSanShi = $styleModel->isSanShengSanShi($goods_sn);
		    if(empty($submits) && $isSanShengSanShi){
		        $result['submits'] = 1;//提交次数
		        $result['error'] = "“三生三世”产品，请核对防伪标是否配齐!";
		        Util::jsonExit($result);
		    }
			$style_info = $apiModel->GetStyleInfoBySn($goods_sn);
			//$order_item=$salesModel->getAppOrderDetailsById($orderDetailId_arr[$key]);
			$order_item = $order_item_dict[$orderDetailId_arr[$key]];
			if($order_item['favorable_status']==3){
			    $goods_info['xiaoshoujia'] =  $order_item['goods_price']-$order_item['favorable_price'] ;
			}else{
			    $goods_info['xiaoshoujia'] = $order_item['goods_price'] ;
			}
			
			if(count($style_info) && $style_info['bang_type'] == 2)//款存在并且是不需绑定的（低值款）
			{
				//判断输入的货号和定的款是否匹配，匹配的过
				if(strtoupper($goods_info['goods_sn']) != strtoupper($style_info['style_sn']))
				{
					$result['error'] = "货号：<span style='color:red;'>{$goods_id}</span> 和订单所定款不同，不允许销账。";
					Util::jsonExit($result);
				}
				$WarehouseGoodsModel->build_goods($orderDetailId_arr[$key],$goods_id);
                if(empty($goods_info['detail_id']))
                    $goods_info['detail_id']=$orderDetailId_arr[$key];   

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
				
                if($goods_info['cat_type1'] == '裸石' || $goods_info['cat_type'] == '裸石' || $goods_info['cat_type1'] == '彩钻'  || $goods_info['cat_type'] == '彩钻'){
                    if($order_item && $order_item['zhengshuhao']<>'' && preg_replace('/[A-Za-z.-]/','',preg_replace('/\s/is','',$order_item['zhengshuhao'])) <> preg_replace('/[A-Za-z.-]/','',preg_replace('/\s/is','',$goods_info['zhengshuhao'])) ){
                        $result['error'] = '输入货号的证书号和所下订单证书号不一致，不可以销账！';
                        Util::jsonExit($result);                       
                    }
                } 

                //验证\is_mapping_ditil_ware
                if($params['distribution_type'] == 2){
                    //判断销账货号字段有：颜色、净度、主成色、指圈、订单明细主石单颗重~订单明细主石单颗重+0.05、货号金重+配件金重in（订单明细金重~订单明细金重+0.05），
                    //只要订单明细中任意一个匹配不成功，则提示“颜色、净度、主成色、指圈、订单明细主石单颗重~订单明细主石单颗重+0.05、货号金重+配件金重in（订单明细金重~订单明细金重+0.05）匹配不成功，确认是否销账”，确认则销账成功，取消则停留原页面
                    $zhuchengse = $order_item['caizhi'].$order_item['jinse'];
                    $diff_zhuanshidaxiao = fasle;
                    $zuanshidaxiao_xiangshang = bcadd($order_item['cart'],0.05,3);
                    if(bccomp($goods_info['zuanshidaxiao'],$order_item['cart']) == 1 && bccomp($goods_info['zuanshidaxiao'],$zuanshidaxiao_xiangshang) == -1){
                        $diff_zhuanshidaxiao = true;
                    }
                    $diff_jinzhong = fasle;
                    $jinzhong_xiangshang = bcadd($order_item['cart'],0.05,3);
                    $jinzhong_zong = bcadd($goods_info['jinzhong'],$goods_info['peijianjinchong'],3);
                    if(bccomp($jinzhong_zong,$order_item['jinzhong']) == 1 && bccomp($jinzhong_zong,$jinzhong_xiangshang) == -1){
                        $diff_jinzhong = true;
                    }
                    if($goods_info['zhushiyanse'] != $order_item['color'] || $goods_info['zhushijingdu'] != $order_item['clarity'] || $goods_info['caizhi'] != $zhuchengse || $goods_info['shoucun'] != $order_item['zhiquan'] || !$diff_zhuanshidaxiao || !$diff_jinzhong){
                        $is_mapping_ditil_ware[] = $key+1;
                    }
                }
			}

			//验证输入的货号是否 库存状态
			if($goods_info['is_on_sale'] != 2)
			{
				$warehouse_goods_error .= "货号：<span style='color:red;'>{$goods_id}</span> 不是库存状态，不允许销账!<br/>";
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
			//$warehouegoods_one= $WarehouseGoodsModel->getGoodsByGoods_id($goods_id);
			//$goods_info['detail_id'] = $order_goods_id;
			
			//是否成品定制
			$is_cpdz = empty($order_item['is_cpdz'])?0:1;
			if($is_cpdz==1){
			    $t_goods_ids = array();
			    $t_yuanshichengbenjia_all = $goods_info['yuanshichengbenjia'];
		        $t_xiaoshoujia= $goods_info['xiaoshoujia'] ;

			    if(!empty($goods_info['pinpai'])){
			        $pinpai_arr = explode('/',trim($goods_info['pinpai']));
			        foreach ($pinpai_arr as $p){
			            $strWhere = "zhengshuhao='{$p}' and is_on_sale=2 and company_id={$from_company_id}";
			            $goods_info1 = $WarehouseGoodsModel->select2("*,count(*) as c",$strWhere,2);
			            $error ="商品信息品牌字段填写的证书号有问题请联系入库人员核实!<br/>";
			            if(empty($goods_info1['c'])){
			                $error .= "成品定制货号<span style='color:red;'>{$goods_id}</span>镶嵌的裸石证书号【{$p}】不符合销账条件！".$strWhere;
			                $result['error'] = $error;
			                Util::jsonExit($result);
			            }else if($goods_info1['c']>1){
			                $error .= "成品定制货号<span style='color:red;'>{$goods_id}</span>镶嵌的裸石证书号【{$p}】有多个({$goods_info1['c']}个重复证书号)！";
			                $result['error'] = $error;
			                Util::jsonExit($result);
			            } else{
			                $t_goods_id = $goods_info1['goods_id'];
			                if(isset($stone_list[$goods_id][$t_goods_id])){
			                    $t_goods_id_new = $stone_list[$goods_id][$t_goods_id];
			                    if($t_goods_id_new <> $t_goods_id){
			                        $error = "裸石货号【{$t_goods_id}】填写的销账货号【{$t_goods_id_new}】错误！";
			                        $result['error'] = $error;
			                        Util::jsonExit($result);
			                    }
			                }else{
			                    $error = "裸石货号【{$t_goods_id}】没有提供销账货号！";
			                    $result['error'] = $error;
			                    Util::jsonExit($result);
			                }
			                $goods_info1['detail_id'] = $order_goods_id;
			                $t_yuanshichengbenjia_all += $goods_info1['yuanshichengbenjia'];//销账价格计算用
			                $t_goods_list[$goods_id]['items'][] = $goods_info1;
			                $t_goods_ids[] = $t_goods_id;
			            }
			            unset($goods_info1);
			        }
			    }
			
                /*  boss_1668只稽核品牌 不稽核主石条码
			    if(!empty($goods_info['zhushitiaoma'])){
			        $zhushitiaoma_arr = explode('/',trim($goods_info['zhushitiaoma']));
			        foreach ($zhushitiaoma_arr as $t_k=>$t_goods_id){
			            if(in_array($t_goods_id,$t_goods_ids)){
			                unset($zhushitiaoma_arr[$t_k]);
			            }
			        }
			        foreach ($zhushitiaoma_arr as $p){
			            $strWhere = "goods_id='{$p}' and is_on_sale=2 and company_id={$from_company_id}";
			            $goods_info1 = $WarehouseGoodsModel->select2("*,count(*) as c",$strWhere,2);
			            $error ="商品信息主石条码字段填写的货号有问题请联系入库人员核实!<br/>";
			            if(empty($goods_info1['c'])){
			                $error .= "成品定制货号<span style='color:red;'>{$goods_id}</span>镶嵌的裸石货号【{$p}】不符合销账条件";
			                $result['error'] = $error;
			                Util::jsonExit($result);
			            }else if($goods_info1['c']>1){
			                $error .= "成品定制货号<span style='color:red;'>{$goods_id}</span>镶嵌的裸石货号【{$p}】有多个({$goods_info1['c']}个货号)！";
			                $result['error'] = $error;
			                Util::jsonExit($result);
			            } else{
			                $t_goods_id = $goods_info1['goods_id'];
			                if(isset($stone_list[$goods_id][$t_goods_id])){
			                    $t_goods_id_new = $stone_list[$goods_id][$t_goods_id];
			                    if($t_goods_id_new <>$t_goods_id){
			                        $error = "裸石货号【{$t_goods_id}】填写的销账货号【{$t_goods_id_new}】错误！";
			                        $result['error'] = $error;
			                        Util::jsonExit($result);
			                    }
			                }else{
			                    $error = "裸石货号【{$t_goods_id}】没有提供销账货号！";
			                    $result['error'] = $error;
			                    Util::jsonExit($result);
			                }
			                $goods_info1['detail_id'] = $order_goods_id;
			                $t_yuanshichengbenjia_all += $goods_info1['yuanshichengbenjia'];//销账价格计算用
			                $t_goods_list[$goods_id]['items'][] = $goods_info1;
			                 
			            }
			            unset($goods_info1);
			        }
			    }*/
			    $t_goods_list[$goods_id]['yuanshichengbenjia_all'] = $t_yuanshichengbenjia_all;
			}//成品定制校验完毕

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
			$all_price_goods += $goods_info['yuanshichengbenjia'];//销账价格计算用
            $_warehouegoods[] =  $goods_info;

            ### 判断如果出库公司选中的时BDD深圳分公司，嘿嘿，不要意思，请你等会，我要去判断有没有总公司的货，有的话自动生成调拨单，给你把货从总部调到深圳分公司来 ### @BY CaoCao
 			if($from_company_id == 445 && $goods_info['company_id'] == 58)	//445|BDD深圳分公司  并且 货品在总公司 58
			{
				//准备要转仓的货品
				$zhuancang_goods_arr[] = $goods_id;
			} 
			else if($goods_info['company_id'] != $from_company_id)
			{
				$result['error'] = "货号：<span style='color:red;'>{$goods_id}</span> 不是所选出库公司的货品，不允许配货。".$from_company_id;
				Util::jsonExit($result);
			}
			$goodsInfo[] = array('goods_id'=>$goods_id, 'is_delete'=>2);

        } /** END froeach **/
        //is_mapping_ditil_ware
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
        if(isset($_GET['compare']) && !empty($_GET['compare'])){
            $error_str = '';
	        $warehouegoods_model = new WarehouseGoodsModel(21);
	        $total_mingyichenben = $warehouegoods_model->getTotalMingyichengben($goods_id_str);
	        if($order_money < $total_mingyichenben){
	        	//$error_str .= "提示1：订单金额低于总成本，{$order_money} < {$total_mingyichenben}。<br/>";
                $error_str .= "提示1：订单金额低于总成本；<br/>";
	        }elseif(!empty($is_mapping_ditil_ware)){
                $error_str .= "提示2：第".implode(",", $is_mapping_ditil_ware)."行货品 （颜色、净度、主成色、指圈、订单明细主石单颗重向上公差0.05、货号金重+配件金重IN【订单明细金重向上公差0.05】）不匹配；<br/>";
            }
            if($error_str){
                $result['error'] = $error_str."<span style='color:red;'>确认是否继续？</span>";
                $result['compare'] = 1;
                Util::jsonExit($result);
            }
        }

		/****对货品明细数据中 销售价格进行计算 并放入数组中  start****/
		//echo $order_money."<br>";
       /*  $warehouegoods = $_warehouegoods;
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
		print_r($warehouegoods);exit; */
       // $zhuancang_goods_arr = array();
        $warehouegoods = array();
		foreach ($_warehouegoods as $w_k=>$w_v){
		    if($t_has_zp==1){
		       $w_v['xiaoshoujia'] = round($w_v['yuanshichengbenjia']/$all_price_goods*$order_money,2);
		    }
		    $goods_id = $w_v['goods_id'];
		    //成品定制价格均分
		    if(!empty($t_goods_list[$goods_id]) && !empty($t_goods_list[$goods_id]['items'])){
		        $t_goods_list2 = $t_goods_list[$goods_id]['items'];
		        $t_yuanshichengbenjia_all = $t_goods_list[$goods_id]['yuanshichengbenjia_all'];
		        $t_goods_list2[] = $w_v;
		        krsort($t_goods_list2);
		        foreach ($t_goods_list2 as $k=>$g){
		            $g['xiaoshoujia'] = $g['yuanshichengbenjia']/$t_yuanshichengbenjia_all * $w_v['xiaoshoujia'];
		            $g['xiaoshoujia'] = round($g['xiaoshoujia'],2);
		            if($from_company_id == 445 && $g['company_id'] == 58 && !in_array($g['goods_id'],$zhuancang_goods_arr))	//445|BDD深圳分公司  并且 货品在总公司 58
		            {
		                $zhuancang_goods_arr[] = $g['goods_id'];
		            }
		            $warehouegoods[] = $g;
		            //echo $goods_id.'---1-'.$g['goods_id'].'-'.$g['xiaoshoujia'].'<br/>';
		        }
		    }else{
		        //echo $goods_id.'---2-'.$w_v['goods_id'].'-'.$w_v['xiaoshoujia'].'<br/>';
		        $warehouegoods[] = $w_v;
		    }
		}
		//print_r($warehouegoods);
		//exit;
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
        //配货发货
        $res = $BillInfoSModel->createBillInfoS($order_id, $order_sn, $goodsInfo, $order_money, $warehouegoods, $from_company_id, $from_company_name, $zhuancang_goods_arr);

        if(!$res['success'])
		{
            $result['error'] = $res['error'];
            $result['compare'] = 0;
        }
		else
		{
			$result = array('success'=>1, 'error'=>'销账成功'."  ".$showMsg , 'order_sn' => $order_sn, 'compare' => 0);
            //修改可销售商品状态
            /* $change=[];$where=[];
            foreach ($goods_id_arr as $k => $v)
			{
                $where[$k]['goods_id'] = $v;
                $change[$k]['is_sale'] = '0';	//下架
                $change[$k]['is_valid'] = '2';	//已销售
            }
            $ApiSalePolcy = new ApiSalepolicyModel();
            $ApiSalePolcy->setGoodsUnsell($change,$where); */
            
            //同步新货号到订单明细            
            $sqls = array();
            foreach ($goods_id_arr as $kk=> $v){
            	$data = array('goods_id'=>$v);
            	$sqls [] = $salesModel->getSqlForUpdateAppOrderDetail($data,"id={$orderDetailId_arr[$kk]}");
            }
            
            if (!empty($sqls)) $salesModel->db()->query(implode(';', $sqls));
            if(!empty($params['is_qk_distrib']) && $params['is_qk_distrib']=='1')
            {   $pointModel = new SelfModel(27);
                $pointModel->update_order_point($order_id);
            }
            //AsyncDelegate::dispatch("warehouse", array('event' => 'bill_S_created', 'order_sn' => $order_sn, 'order_id' => $order_id, 'is_qk_distrib' => isset($params['is_qk_distrib']) ? $params['is_qk_distrib'] : 0, 'user' => $_SESSION['userName']));
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
                //$order_action = ApiModel::sales_api("getOrderActionList",array("order_id"), array($order_id));
                //$order_action = $order_action['return_msg'];
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

                $hidden = Util::zhantingInfoHidden($data);
                $result['content'] = $this->fetch('wait_detail_search_top.html',array(
                        'order_id' => $order_id,
                        'order_action' => array(),
                        'orderInfo' => $data,
                        'out_order_sn' => $out_order_sn,
                        'bar' => Auth::getViewBar(),
                        'exit_tuikuan'=>$exit_tuikuan['return_msg'],
                        'company' => $company,
                        'hidden'=>$hidden
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
				    //TODO: 支持门店自己销账
					//$company = array();
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
    
    public function getShopShipping($params) {
    	$order_sn = $params['order_sn'];
    	if(empty($order_sn)){
    		return;
    	}

		$keys=array('order_sn');
		$vals=array($order_sn);
		$ret=ApiModel::sales_api('getShopOrderShip',$keys,$vals);
        if ($ret['error'] == 0) {
			$order = $ret['return_msg'][0];
			$addr = $ret['return_msg'][1];
			$fapiao = $ret['return_msg'][2];
			$ex_model		= new ExpressModel(1);
			$info_express   = $ex_model->getAllExpress();
        	$this->render('ship.html',array(
        		'order_sn' => $order_sn,
        		'order_id' => $order['id'],
        		'send_good_status' => $order['send_good_status'],
        		'delivery_status' => $order['delivery_status'],
        		'express_id' => $addr['express_id'],
        		'freight_no'=> $addr['freight_no'],	
        		'order_invoice' => $fapiao,
        		'expresslist' => $info_express
        	));
        } 
    }
    
}/** END CLASS**/

?>
