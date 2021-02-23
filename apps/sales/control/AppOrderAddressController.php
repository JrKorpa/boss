<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderAddressController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-30 16:37:26
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderAddressController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('app_order_address_search_form.html',array('bar'=>Auth::getBar()));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$args = array(
			'mod'	=> _Request::get("mod"),
            'memberid'=> _Request::get("member_id"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
            '_id' => _Request::getInt('_id'),

		);
       $res =  AppOrderAddressModel::GetMemberAddressInfo($args['memberid']);
        if($res['error']==0){
            $member_address = $res['data'];
            $region = new RegionModel(1);
            foreach($member_address as $key=>$val){
                $addressstr='';
                $regioninfo =$region->getRegionList("$val[mem_country_id],$val[mem_province_id],$val[mem_city_id],$val[mem_district_id]");
                foreach($regioninfo as $k=>$v){
                    $addressstr.= $v['region_name'].'  ';
                }
                $addressstr.=$val['mem_address'];
                $member_address[$key]['addressstr'] = $addressstr;
            }

        }else{
            $member_address=array();
        }
		$this->render('app_order_address_search_list.html',array(
/*			'pa'=>Util::page($pageData),*/
			'page_list'=>$member_address,/*'express'=>$express*/
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
        $reginModel = new RegionModel(1);
        $countdata = $reginModel->getRegionType(0);
		$result['content'] = $this->fetch('app_order_address_info.html',array(
			'data'=>array('mem_address_id'=>'','member_id'=>'','customer'=>'','mobile'=>'','mem_country_id'=>'','mem_province_id'=>'','mem_city_id'=>'','mem_district_id'=>'','mem_address'=>'','mem_is_def'=>0),
            '_id'=>_Post::getInt('_id'),'count'=>$countdata,'member_id'=>_Post::getInt('member_id')
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}



    public function  selectaddress(){
 
        $order_id = _Request::getInt('_id');
        $aomodel=new AppOrderAddressModel(27);
        $member_address=$aomodel->getOrderAddressinfo($order_id);
         
        $express = new ExpressView(new ExpressModel(1));
      //全部的物流
		$express = $express->getAllexp();

            //国家列表
            $Smodel = new ShopCfgModel(1);
             $shop = $Smodel->getAllShopCfg();
            
        $reginModel = new RegionModel(1);
        $countdata = $reginModel->getRegionType(0);
        $res =  AppOrderAddressModel::GetMemberAddressInfos($member_address['id']);
        $member_id = _Post::getInt('member_id');
        $OrderArr=$aomodel->getOrderArr($order_id);
        $referer=$OrderArr['referer'];
        //$wholesale_name=$member_address['wholesale_name'];
        //if($wholesale_name==''){ 
            $member_address['wholesale_name']='';
        	$SalesChannelsModel = new SalesChannelsModel(1);
        	$ChannelsArr=$SalesChannelsModel->getChannelIdById($OrderArr['department_id']);
        	if(!empty($ChannelsArr)){
        		$wholesale_id=$ChannelsArr['wholesale_id'];
        		if($wholesale_id !='' && $wholesale_id != 0){
        			$SelfWarehouseGoodsModel =new SelfWarehouseGoodsModel(21);
        			$wholesale_name=$SelfWarehouseGoodsModel->getWholesaleArr($wholesale_id);
        			$member_address['wholesale_name']=$wholesale_name;
        		}
        	//}
        }
        $SelfWarehouseGoodsModel =new SelfWarehouseGoodsModel(21);
        $wholesaleArr=$SelfWarehouseGoodsModel->getWholesaleAll();
        $result['content']=$this->fetch('app_order_address_info_2.html',array(
            'order_id'=>$order_id,'member_id'=>$member_id,'member_address'=>$member_address,'express'=>$express,'count'=>$countdata,'shop'=>$shop,'referer'=>$referer,'wholesaleArr'=>$wholesaleArr
        ));
        $result['title'] = '添加一个收货信息';
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
	$express = new ExpressView(new ExpressModel(1));
            //全部的物流
            $express = $express->getAllexp();
            $express = array_column($express,'exp_name','id');

        //国家列表
        $reginModel = new RegionModel(1);
        $countdata = $reginModel->getRegionType(0);
       $res =  AppOrderAddressModel::GetMemberAddressInfos($id);
        if($res['error']!=0){
            $data = array();
        }else{
            $data=$res['data'][0];
        }
		$result['content'] = $this->fetch('app_order_address_info.html',array(
			'data'=>$data,'count'=>$countdata,
			'tab_id'=>$tab_id,'express'=>$express,'dd'=> new DictView(new DictModel(1)),
            '_id'=>  _Request::getInt('order_id'),
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
		$this->render('app_order_address_show.html',array(
			'view'=>new AppOrderAddressView(new AppOrderAddressModel($id,27)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{

        
		$result = array('success' => 0,'error' =>'');
        $newdo=array(
            'customer'=> _Request::get('consignee'),
            'mobile'=> _Request::get('tel'),
            'mem_country_id'=> _Request::get('country_id'),
            'mem_province_id'=> _Request::get('province_id'),
            'mem_city_id'=> _Request::get('city_id'),
            'mem_district_id'=> _Request::get('regional_id'),
            'mem_address'=> _Request::get('address'),
            'member_id'=> _Request::get('member_id'),
            'mem_is_def'=>_Request::get('mem_is_def'),
        );

       $res=  AppOrderAddressModel::AddMemberAddressInfo($newdo);

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
	 *	update，更新信息
	 */
	public function update ($params)
	{


		$result = array('success' => 0,'error' =>'');
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');

		$id = _Post::getInt('id');

        $newdo=array(
            'id'=> _Request::getInt('id'),
            'customer'=> _Request::get('consignee'),
            'mobile'=> _Request::get('tel'),
            'mem_country_id'=> _Request::get('country_id'),
            'mem_province_id'=> _Request::get('province_id'),
            'mem_city_id'=> _Request::get('city_id'),
            'mem_district_id'=> _Request::get('regional_id'),
            'mem_address'=> _Request::get('address'),
            'member_id'=> _Request::get('id'),
            'mem_is_def'=> _Request::getInt('mem_is_def'),

        );
        $res = AppOrderAddressModel::PutMemberAddressInfo($newdo);
		if($res !== false)
		{
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;	
			$result['title'] = '修改成功';
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppOrderAddressModel($id,28);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

    public function getProvince(){
        $count_id = _Post::getInt('count');
        $reginModel = new RegionModel(1);
        $provincedata = $reginModel->getRegion($count_id);
        $res = $this->fetch('app_order_address_province_option.html',array('provincedata'=>$provincedata));
        echo $res;

    }

    public function takeovergoods(){
        $id = _Post::getInt('id');
        $order_id =_Post::getInt('order_id');
        $tab_id = _Request::getInt("tab_id");
        $member_id = _Post::getInt('member_id');
        $result = array('success' => 0,'error' => '');
        $express = new ExpressView(new ExpressModel(1));
        //全部的物流
        $express = $express->getAllexp();
        $result['content'] = $this->fetch('app_order_address_info_2.html',array(
           'order_id'=>$order_id,
            'tab_id'=>$tab_id,
            '_id'=> $id,
            'express'=>$express,
            'member_id'=>$member_id
        ));
        $result['title'] = '编辑';
        Util::jsonExit($result);

    }


    public function insertorderadd(){
            $result = array('success' => 0,'error' =>'');
            $distribution_type = _Request::getInt('distribution_type');
        
            $order_id =  _Request::getInt('order_id');
        
            $member_id =  _Request::getInt('member_id');
			
		// 2015-11-03 fix by lyy  boss629
		// 4、销售管理-订单管理-顾客订单，详情，送货地址信息中的【更改物流方式】，保存时需要判断：当订单销售渠道为【B2C销售部】且来源选择【中国移动积分】或者销售渠道为【银行销售部】且来源选择【交通银行】，快递物流允许选择【圆通速递】，其他的情况不允许保存，需提示“订单销售渠道和来源不支持圆通速递，请选择其他的快递物流”
		$express_id = _Request::getInt('express_id');
		

		
		$this->checkExpPermit($order_id,$express_id);

        $orderInfoModel = new BaseOrderInfoModel(27);
        $orderInfo = $orderInfoModel->getOrderInfoById($order_id);
/*
        //BOSS-1393 手工录入订单及手工维护快递公司，制单时间为1月16号-2月4号的顾客订单，不允许选择中通快递，制单时间为1月17号-2月4号的顾客订单，不允许选择圆通速递，快递公司自动变更，制单时间为1月16号-2月4号的顾客订单，以前程序自动识别为中通快递的自动变更为顺丰速运，不允许为中通快递；制单时间为1月17号-2月4号的顾客订单，以前程序自动识别为圆通速递的自动变更为顺丰速运，不允许为圆通速递，以下位置管控：
        if($orderInfo['create_time'] >= '2017-01-17 00:00:00' && $orderInfo['create_time'] <='2017-02-04 23:59:59' && $express_id == 19){
            //$express_id = 4;
            $result['error'] = '放假期间快递公司受限，请选择其他快递公司';
            Util::jsonExit($result);
        }
        if($orderInfo['create_time'] >= '2017-01-19 00:00:00' && $orderInfo['create_time'] <='2017-02-04 23:59:59' && $express_id == 12){
            //$express_id = 4;
            $result['error'] = '放假期间快递公司受限，请选择其他快递公司';
            Util::jsonExit($result);
        }
*/
            $model =new AppOrderAddressModel(28);
            $addressinfo = $model->getAddressByOrderid($order_id);
            if(empty($addressinfo)){
                $olddo =array();
            }else{
                $model =new AppOrderAddressModel($addressinfo['id'],28);
                $olddo=array(
                   'id'=>$addressinfo['id'],
                   'order_id'=>$addressinfo['order_id'],
                   'consignee'=>$addressinfo['consignee'],
                   'distribution_type'=>$addressinfo['distribution_type'],
                   'express_id'=>$addressinfo['express_id'],
                   'freight_no'=>$addressinfo['freight_no'],
                   'country_id'=>$addressinfo['country_id'],
                   'province_id'=>$addressinfo['province_id'],
                   'city_id'=>$addressinfo['city_id'],
                   'regional_id'=>$addressinfo['regional_id'],
                   'address'=>$addressinfo['address'],
                   'tel'=>$addressinfo['tel'],
                   'email'=>$addressinfo['email'],
                   'zipcode'=>$addressinfo['zipcode'],
                   'goods_id'=>$addressinfo['goods_id'],
                   'wholesale_id'=>$addressinfo['wholesale_id'],
                );
            }

            if($distribution_type==1){
                //到店
                $shop_id=_Request::getInt('shop_id');
                $shopModel = new ShopCfgModel(1);
                $shopinfo = $shopModel->getShopInfoByid($shop_id);
                if(empty($shopinfo['country_id'])||empty($shopinfo['country_id'])||empty($shopinfo['country_id'])||empty($shopinfo['country_id'])){
                    $result['error'] = '体验店地址不全请先在管理中心通用将体验店地址信息补全';
                    Util::jsonExit($result);
                }

                $SelfSalesChannelsModel = new SelfSalesChannelsModel(1);
                $wholesale_id=$SelfSalesChannelsModel->getWholesaleByCompany($shop_id);
               
                $newdo=array(
                    'order_id'=>$order_id,
                    'country_id'=>$shopinfo['country_id'],
                    'province_id'=>$shopinfo['province_id'],
                    'city_id'=>$shopinfo['city_id'],
                    'regional_id'=>$shopinfo['regional_id'],
                    'consignee'=>_Request::getString('consignee'),
                    'express_id'=>10,//配送到体验店的默认上门取货
                    'distribution_type'=>$distribution_type,
                    'address'=>$shopinfo['shop_name'].'|'.$shopinfo['shop_address'],
                    'tel'=>_Request::getString('tel'),
                    'shop_name'=>$shopinfo['shop_name'],
                    'shop_type'=>$shopinfo['shop_type'],
                	'wholesale_id'=>$wholesale_id,
                      
                );
                if(!empty($addressinfo)){
                    $newdo['id']= $addressinfo['id'];
                }

                $res =  $model->saveData($newdo,$olddo);

                if($res){
                    
                    $logInfo = array(
                        'order_id'=>$order_id,
                        'order_status'=>$orderInfo['order_status'],
                        'shipping_status'=>$orderInfo['send_good_status'],
                        'pay_status'=>$orderInfo['order_pay_status'],
                        'create_user'=>$_SESSION['userName'],
                        'create_time'=>date("Y-m-d H:i:s"),
                        'remark'=>'订单添加收货地址'
                    );
                    //写入订单日志
                    $orderInfoModel->addOrderAction($logInfo);
                    $result['success'] = 1;
                    Util::jsonExit($result);
                }
                $result['error'] = '收货信息没有发生变化';
                Util::jsonExit($result);
            }else{
                //到客户
                $res =  AppOrderAddressModel::GetMemberAddressInfo($member_id);
                $newdo=array(
                    'order_id'=>$order_id,
                    'consignee'=>_Request::getString('consignee'),
                    'distribution_type'=>$distribution_type,
                    'email'=>_Request::getString('email'),
                    'zipcode'=>_Request::getInt('zipcode'),
                    'express_id'=>$express_id,//物流信息
                    'country_id'=>_Request::getInt('country_id'),
                    'province_id'=>_Request::getInt('province_id'),
                    'city_id'=>_Request::getInt('city_id'),
                    'regional_id'=>_Request::getInt('regional_id'),
                    'address'=>_Request::getString('address'),
                    'tel'=>_Request::getString('tel'),
                );
                
                if($newdo['express_id']==10){
                	$result['error'] = '不能选择上门取货！';
                	Util::jsonExit($result);
                }
                if(!empty($addressinfo)){
                    $newdo['id']= $addressinfo['id'];
                }
                if($res['error']==0){
                    //查到了默认地址
                    $res =  $model->saveData($newdo,$olddo);
                    if($res){
                        $orderInfoModel = new BaseOrderInfoModel(27);
                        $orderInfo = $orderInfoModel->getOrderInfoById($order_id);
                        $logInfo = array(
                            'order_id'=>$order_id,
                            'order_status'=>$orderInfo['order_status'],
                            'shipping_status'=>$orderInfo['send_good_status'],
                            'pay_status'=>$orderInfo['order_pay_status'],
                            'create_user'=>$_SESSION['userName'],
                            'create_time'=>date("Y-m-d H:i:s"),
                            'remark'=>'订单添加收货地址'
                        );
                        //写入订单日志
                        $orderInfoModel->addOrderAction($logInfo);
                        $result['success'] = 1;
                        Util::jsonExit($result);
                    }
                    $result['error'] = '设置收货地址失败';
                    Util::jsonExit($result);
                }else{
                    //没有查到默认地址
                    $res =  $model->saveData($newdo,$olddo);
                        $newdoapi=array(
                        'customer'=> _Request::get('consignee'),
                        'mobile'=> _Request::get('tel'),
                        'mem_country_id'=> _Request::get('country_id'),
                        'mem_province_id'=> _Request::get('province_id'),
                        'mem_city_id'=> _Request::get('city_id'),
                        'mem_district_id'=> _Request::get('regional_id'),
                        'mem_address'=> _Request::get('address'),
                        'member_id'=>$member_id,
                        'mem_is_def'=>1,
                    );
                    $res=  AppOrderAddressModel::AddMemberAddressInfo($newdoapi);
                    if($res){
                        $orderInfoModel = new BaseOrderInfoModel(27);
                        $orderInfo = $orderInfoModel->getOrderInfoById($order_id);
                        $logInfo = array(
                            'order_id'=>$order_id,
                            'order_status'=>$orderInfo['order_status'],
                            'shipping_status'=>$orderInfo['send_good_status'],
                            'pay_status'=>$orderInfo['order_pay_status'],
                            'create_user'=>$_SESSION['userName'],
                            'create_time'=>date("Y-m-d H:i:s"),
                            'remark'=>'订单添加收货地址'
                        );
                        //写入订单日志
                        $orderInfoModel->addOrderAction($logInfo);
                        $result['success'] = 1;
                        Util::jsonExit($result);
                    }
                    $result['error'] = '设置收货地址失败';
                    Util::jsonExit($result);

                }


            }

    }
    
	public function getDistributionType(){
        $result = array('success' => 0,'error' =>'');
        $DistributionType = _Request::getInt('distribution_type');
        $order_id = _Request::getInt('order_id');
        $member_id = _Request::getInt('member_id');
        if(!$DistributionType){
            $result['error']="请选择配送方式";
            Util::jsonExit($result);
        }
        $apiM = new ApiMemberModel();
        $membeinfo=$apiM->GetMemberByMember_id($member_id);

        if($DistributionType==2){
            $express = new ExpressView(new ExpressModel(1));
            //全部的物流
            $express = $express->getAllexp();
            $express = array_column($express,'exp_name','id');
           //取出用户的默认地址如果没有就让添加一个
            $res =  AppOrderAddressModel::GetMemberAddressInfo($member_id);
            $region = new RegionModel(1);
            if($res['error']==0){
                //取到了用户的默认地址
                $member_address = $res['data'];
                $countdata = $region->getRegionType(0);
                $result['content'] = $this->fetch('address_member.html',array('member_address'=>$member_address[0],'count'=>$countdata,'express'=>$express));
                $result['success']=1;
                Util::jsonExit($result);
            }else{
                $orderModel = new BaseOrderInfoModel($order_id,27);
                $order_info=$orderModel->getDataObject();
                $member_address['customer']=$order_info['consignee'];
                $member_address['mobile']=$order_info['mobile'];
                $countdata = $region->getRegionType(0);
                $result['content'] = $this->fetch('address_member.html',array('count'=>$countdata,'express'=>$express,'member_address'=>$member_address));
                $result['success']=1;
                Util::jsonExit($result);
            }

        }else{
            $Smodel = new ShopCfgModel(1);
            $shop = $Smodel->getAllShopCfg();
            $shop = array_column($shop,'shop_name','id');
            $orderModel = new BaseOrderInfoModel($order_id,27);
            $order_info=$orderModel->getDataObject();
            $channelModel = new SalesChannelsModel(1);
            $channelinfo = $channelModel->getChannelOwnId($orderModel->getValue('department_id'));
            if(empty($channelinfo['shop_name'])){
                $channelinfo=array();
            }
            if(!empty($channelinfo)){
                $result['content'] = $this->fetch('mendian_address.html',array('shops'=>$shop,'shop'=>$channelinfo,'membeinfo'=>$order_info));
                $result['success']=1;
                Util::jsonExit($result);
            }else{
                $result['content'] = $this->fetch('mendian_address.html',array('shops'=>$shop,'shop'=>0,'membeinfo'=>$order_info));
                $result['success']=1;
                Util::jsonExit($result);
            }
        }

    }

    public function getShopInfo(){
        $result = array('success' => 0,'error' =>'');
        $shop_id = _Request::getInt('shop_id');
        if(empty($shop_id)){
            $result['error']  ="没有正确选择一个体验店";
            Util::jsonExit($result);
        }
        $shopModel = new ShopCfgModel($shop_id,1);
        $result['error']=$shopModel->getValue('shop_address');
        $result['success']=1;
        Util::jsonExit($result);
    }

    public function changeEx(){
        $order_id =_Request::getInt('_id');
        $result = array('success' => 0,'error' => '');
        //全部的物流
        $express = new ExpressView(new ExpressModel(1));
        $express = $express->getAllexp();
        $express =  array_column($express,'exp_name','id');
        //取出现在物流的id
        $addmodel =new AppOrderAddressModel(27);
        $address= $addmodel->getAddressByOrderid($order_id);
        if($address['express_id']==10){
        	$result['content']  ='到店取货不能选择快递';
        	Util::jsonExit($result);
        }
        $result['content'] = $this->fetch('app_order_express.html',array('order_id'=>$order_id,'express'=>$express,'express_info'=>$address));
        $result['title'] = '更改物流方式';
        Util::jsonExit($result);
    }

    public function UpdataEx(){
        $result = array('success' => 0,'error' =>'');
        $express_id = _Request::getInt('express_id');
        $address_id = _Request::getInt('address_id');
        
	
       
		// 2015-11-03 fix by lyy
		// 3、销售管理-订单管理-顾客订单，详情，送货地址信息中的【更改物流方式】，保存时需要判断：当订单销售渠道为【B2C销售部】且来源选择【中国移动积分】或者销售渠道为【银行销售部】且来源选择【交通银行】，快递物流允许选择【圆通速递】，其他的情况不允许保存，需提示“订单销售渠道和来源不支持圆通速递，请选择其他的快递物流”
		$order_id = _Request::getInt('order_id');     
		$this -> checkExpPermit($order_id,$express_id);
        $addmodel =new AppOrderAddressModel($address_id,27);
        $olddo = $addmodel->getDataObject();
        $orderM=new BaseOrderInfoModel($olddo['order_id'],27);

        //BOSS-1393 手工录入订单及手工维护快递公司，制单时间为1月16号-2月4号的顾客订单，不允许选择中通快递，制单时间为1月17号-2月4号的顾客订单，不允许选择圆通速递，快递公司自动变更，制单时间为1月16号-2月4号的顾客订单，以前程序自动识别为中通快递的自动变更为顺丰速运，不允许为中通快递；制单时间为1月17号-2月4号的顾客订单，以前程序自动识别为圆通速递的自动变更为顺丰速运，不允许为圆通速递，以下位置管控：
        $create_time = $orderM->getValue('create_time');
        if($create_time >= '2019-01-26 00:00:00' && $create_time <='2019-02-12 23:59:59' && $express_id == 19){
            //$express_id = 4;
            $result['error'] = '放假期间快递公司受限，请选择其他快递公司';
            Util::jsonExit($result);
        }
        /*if($create_time >= '2018-01-19 00:00:00' && $create_time <='2017-02-04 23:59:59' && $express_id == 12){
            //$express_id = 4;
            $result['error'] = '放假期间快递公司受限，请选择其他快递公司';
            Util::jsonExit($result);
        }*/

        $newdo = array(
            'id'=>$address_id,
            'express_id'=>$express_id,
        );
        
        $res = $addmodel->saveData($newdo,$olddo);
        if(!$res){
            $result['error']='修改失败';
        }else{
            //修改前快递公司
            $expressModel = new ExpressModel($olddo['express_id'],1);
            $expressName = $expressModel->getValue('exp_name');
            //修改后快递公司
            $expressModel = new ExpressModel($express_id,1);
            $express_Name = $expressModel->getValue('exp_name');
            //订单日志
            
            $logInfo = array(
                'order_id'=>$olddo['order_id'],
                'order_status'=>$orderM->getValue('order_status'),
                'shipping_status'=>$orderM->getValue('send_good_status'),
                'pay_status'=>$orderM->getValue('order_pay_status'),
                'create_user'=>$_SESSION['userName'],
                'create_time'=>date("Y-m-d H:i:s"),
                'remark'=>'由'.$expressName.'物流公司修改成'.$express_Name.'物流公司'
            );
            //写入订单日志
            $orderM->addOrderAction($logInfo);
            $result['success']=1;
        }
        Util::jsonExit($result);
    }
    //体验店类型动态的取体验店列表
    public function getShopList(){
        $shop_type = _Request::getInt('shop_type');
        $shmodel = new ShopCfgModel(1);
        $list = array_column($shmodel->getAllShopCfg(array('shop_type'=>$shop_type)),'shop_name','id');
        $this->render('shop_option.html',array('list'=>$list));
    }


	//公用检测支付渠道是否支持对应的快递公司 add by lyy 2015-11-03
	// 销售管理-订单管理-顾客订单，详情，送货地址信息中的【更改物流方式】，保存时需要判断：当订单销售渠道为【B2C销售部】且来源选择【中国移动积分】或者销售渠道为【银行销售部】且来源选择【交通银行】，快递物流允许选择【圆通速递】，其他的情况不允许保存，需提示“订单销售渠道和来源不支持圆通速递，请选择其他的快递物流”
    public function checkExpPermit($order_id,$express_id){
		return true;//BOSS-1394 所有渠道允许使用圆通速递
		$orderModel = new BaseOrderInfoModel($order_id, 27);
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
				if($express_id == 12)
				{
					$result['error'] = '订单销售渠道和来源不支持圆通速递，请选择其他的快递物流';
					Util::jsonExit($result); 
				} 
			 }
    }

    public function changeExBf(){
        $order_id =_Request::getInt('_id');
        $result = array('success' => 0,'error' => '');
        $aomodel=new AppOrderAddressModel(27);
        $member_address=$aomodel->getOrderAddressinfo($order_id);
        //取出现在物流的id
        $address= $aomodel->getAddressByOrderid($order_id);
        $result['content'] = $this->fetch('app_order_express_bf.html',array('order_id'=>$order_id,'express_info'=>$address,'member_address'=>$member_address));
        $result['title'] = '更改补发地址';
        Util::jsonExit($result);
    }

    //更改补发地址
    public function UpdataExBf(){
        $result = array('success' => 0,'error' =>'');
        $address_id = _Request::getInt('address_id');
        $order_id = _Request::getInt('order_id');
        $consignee2 = _Request::getString('consignee2');
        $tel2 = _Request::getString('tel2');
        $address2 = _Request::getString('address2');
        $addmodel =new AppOrderAddressModel($address_id,27);
        $olddo = $addmodel->getDataObject();
        $orderM=new BaseOrderInfoModel($olddo['order_id'],27);
        $newdo = array(
            'id'=>$address_id,
            'consignee2'=>$consignee2,
            'tel2'=>$tel2,
            'address2'=>$address2
        );
        $res = $addmodel->saveData($newdo,$olddo);
        if(!$res){
            $result['error']='修改失败';
        }else{
            $logInfo = array(
                'order_id'=>$olddo['order_id'],
                'order_status'=>$orderM->getValue('order_status'),
                'shipping_status'=>$orderM->getValue('send_good_status'),
                'pay_status'=>$orderM->getValue('order_pay_status'),
                'create_user'=>$_SESSION['userName'],
                'create_time'=>date("Y-m-d H:i:s"),
                'remark'=>'更换补发地址:'.$consignee2.','.$tel2.','.$address2
            );
            //写入订单日志
            $orderM->addOrderAction($logInfo);
            $result['success']=1;
        }
        Util::jsonExit($result);
    }

}

?>