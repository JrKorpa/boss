<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderDetailsController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 21:06:55
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderDetailsController extends CommonController
{
	protected $smartyDebugEnabled = true;
    protected $whitelist = array('getGoodsByOrderId','getKeziInfo','printRepairOrder','getDiamandInfoAjax');
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{

		//Util::M('app_order_details','app_order',27);	//生成模型后请注释该行
		//Util::V('app_order_details',27);	//生成视图后请注释该行
		//$this->render('app_order_details_search_form.html');
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$args = array(
			'mod' => _Request::get("mod"),
			'con' => substr(__CLASS__, 0, -10),
			'act' => __FUNCTION__,
			'_id' => _Request::get("order_id"),
		);
		$order_id = _Request::get("order_id");
		
		$page = _Request::getInt("page",1);
		$where = array(
            'order_id' => $args['_id']
		);	
		
		$model = new AppOrderDetailsModel(27);
        $apistyleModel = new ApiStyleModel();
        $diaModelApi = new ApiDiamondModel();
        $warehouseModel = new SelfWarehouseGoodsModel(22);
		$data = $model->get_order_goods($where,$page,20,false);
        $kezimodel = new KeziModel();
        if($data['data'] != ''){
            foreach ($data['data'] as $k => $v) {
                $cert_id = $v['zhengshuhao'];
                $goods_id = $v['goods_id'];
                $ext_goods_sn = $v['ext_goods_sn'];
                $data['data'][$k]['thumb_img'] = '';
                $con['style_sn'] = $v['goods_sn'];
                $info = $apistyleModel->GetStyleGallery($con);
                if(!empty($info['big_img'])){//45°图
                    $data['data'][$k]['big_img'] = $info['big_img'];
                    $data['data'][$k]['style_sn'] = $info['style_sn'];
                }
                //刻字
                $keziList = $kezimodel->getKeziData();
        		$kezi = $v['kezi'];

        		if($keziList){
        			foreach($keziList as $key => $val){
                                    $kezi = str_replace($key,'<img src="'.$val.'"  width="24"/>',$kezi);
        			}
        		}
        		$data['data'][$k]['kezi']=$this->replaceTsKezi($kezi);
                //根据证书号带出证书类型；
                $certid = $v['cert'];
                if($cert_id != '' && $certid == ''){
                    $diainfo = $diaModelApi->getDiamondInfoByCertId($cert_id);
                    if($diainfo['error'] != 1){
                        $certid = isset($diainfo['data']['cert'])?$diainfo['data']['cert']:'';
                    }
                }
                //根据货号带出证书类型
                //if($certid == '' && $goods_id != ''){
                    //$goodsInfo = $warehouseModel->getWarehouseGoodsRow("zhengshuleibie","`goods_id` in('{$goods_id}','{$ext_goods_sn}') and zhengshuleibie is not null");
                    //$certid = isset($goodsInfo['zhengshuleibie'])?$goodsInfo['zhengshuleibie']:'';
                //}
                //根据货号带出证书类型
                if($certid == '' && $goods_id != ''){
                    $goodsInfo = $warehouseModel->getWarehouseGoodsRow("zhengshuleibie","`goods_id` = '{$goods_id}' and zhengshuleibie is not null");
                    $certid = isset($goodsInfo['zhengshuleibie'])?$goodsInfo['zhengshuleibie']:'';
                }
                //根据原始货号带出证书类型
                if($certid == '' && $goods_id == '' && $ext_goods_sn != ''){
                    $goodsInfo = $warehouseModel->getWarehouseGoodsRow("zhengshuleibie","`goods_id` = '{$ext_goods_sn}' and zhengshuleibie is not null");
                    $certid = isset($goodsInfo['zhengshuleibie'])?$goodsInfo['zhengshuleibie']:'';
                }

                $data['data'][$k]['cert'] = $certid;
                //查询是否有占用备货明细
                $bhInfo = $model->getOutOrderInfo($v['id']);
                $data['data'][$k]['is_zhanyong'] = '否';
                $data['data'][$k]['p_sn'] = '';
                if(!empty($bhInfo)){
                    $data['data'][$k]['is_zhanyong'] = '是';
                    $data['data'][$k]['p_sn'] = implode("|", array_column($bhInfo,'p_sn'));
                }
            }
            
        }

        $orderInfoModel = new BaseOrderInfoModel($args['_id'], 27);
        $apply_close = $orderInfoModel->getValue('apply_close');
        $apply_return = $orderInfoModel->getValue('apply_return');
        $referer = $orderInfoModel->getValue('referer');  
        $delivery_status=$orderInfoModel->getValue('delivery_status');  
        $order_status =  $orderInfoModel->getValue('order_status');
        $customer_source_id = $orderInfoModel->getValue('customer_source_id');
        //$customer_source_id == 2034 唯品会B2C
        if($order_status==1 && $customer_source_id == 2034){
            $this->updateVipPickOrderDetail($order_id,$data['data']);
        }  

       $datas = $model->get_retrun_goods($where, $page, 20, false);
       $return_goods = array();
       foreach ($datas as $k => $v){
          $return_goods[$v['order_goods_id']]  = $v;
       }      
                
        foreach ($data['data'] as $k => $v) { 
            $key = $v['id'];
            if(!empty($return_goods[$key]) && $return_goods[$key]['check_status'] >= 4 && $data['data'][$k]['is_return'] == 1){
                $data['data'][$k]['is_return'] = 1;
            }else{
                $data['data'][$k]['is_return'] = 0;
            }
            $returnGoodsPrice1 =$model->getReturnGoodsPrice($v['order_id'],$v['id'],1);
            $returnGoodsPrice2 =$model->getReturnGoodsPrice($v['order_id'],$v['id'],2);
            if($returnGoodsPrice1>0 || $returnGoodsPrice2>0){
                $data['data'][$k]['return_goods_price'] = ($returnGoodsPrice1+$returnGoodsPrice2)." <br/>退款退货:{$returnGoodsPrice1}<br/>退款不退货:{$returnGoodsPrice2}";
            }else{
                $data['data'][$k]['return_goods_price'] = '0.00';
            }
            
        }        
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'app_order_details_search_page';
        $this->render('app_order_details_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data,
        	'referer'=>$referer,
            'delivery_status'=>$delivery_status,
            'hidden'=>Util::zhantingInfoHidden($orderInfoModel->getDataObject())
        ));
    }
    /**
     * 同步唯品会订单
     * @param unknown $order_id
     * @param unknown $goodslist
     * @return boolean
     */
    protected function updateVipPickOrderDetail($order_id,$goodslist=array()){        
        $warehouseModel = new SelfWarehouseGoodsModel(22);
        $vipOrderDetail = $warehouseModel->getVipPickOrderDetail(array('order_id'=>$order_id));
        if(empty($vipOrderDetail)){
            return true;
        }
        $model = new AppOrderDetailsModel(27);
        $vip_order_detail_id = $vipOrderDetail['order_detail_id'];
        if(empty($goodslist)){
           $data = $model->get_order_goods("order_id={$order_id}",1,20,false);
           $goodslist = $data['data'];           
        }
        $detail_ids = array_column($goodslist,"id");
        try{
            foreach ($goodslist as $goods){
                if(!in_array($vip_order_detail_id,$detail_ids)){
                    $newdo = array(
                        'order_detail_id'=>$goods['id'],
                        'goods_id'=>$goods['goods_id']                    
                    );
                   return $warehouseModel->updateVipPickOrderDetail($newdo,"order_id={$order_id}");
    
                }
                break;
            }
        }catch (Exception $e){
            return false;
        }
        return true;
    }

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
            die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        } 		
		$result = array('success' => 0,'error' => '');
		$id = _Post::getInt('_id');
		$orderModel = new BaseOrderInfoModel($id,27);
		$channel_id = $orderModel->getValue("department_id");
        //获取购物车中数据
        $model=new AppOrderDetailsModel(27);
        $sale_way=$model->getOrdergiftchannel(_Post::getInt('_id'));
      
        $giftModel = new ApiGiftManModel();
        $where = array(
            'status' => 1,//删除状态
            'sale_way'=>$sale_way,
             
        );
    
        $gifts_info = $giftModel->GetGiftManList($where);
        $gifts_info = $gifts_info['error'] > 0 ? array('data' => array()) : $gifts_info;
        $huangouModel = new HuangouGoodsModel(27);
        $huangou_goods = $huangouModel->getHuangouGoodsList(array('status'=>1,'channel_id'=>$channel_id),"id,style_sn");
		$result['content'] = $this->fetch('app_order_details_info.html',array(
			'view'=>new AppOrderDetailsView(new AppOrderDetailsModel(27)),
			'_id'=>_Post::getInt('_id'),
            'gifts_info'=> $gifts_info,
		    'huangou_goods'=>$huangou_goods
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}


	/**
	 * 添加新品，渲染添加页面
	 */
	public function addGoods ()
	{
	    $result = array('success' => 0,'error' => '');
	    $order_id = _Post::getInt('_id');
	    $orderInfoModel = new BaseOrderInfoModel($order_id,27);
	    $department_id = $orderInfoModel->getValue('department_id');
	    $salesChannelModel = new SalesChannelsModel($department_id,1);
	    $channel_class = $salesChannelModel->getValue('channel_class');
	    if($channel_class != 1){
	        $result['content'] = '该订单不是线上渠道';
	        $result['title'] = '错误';
	        Util::jsonExit($result);
	    }
	    $newmodel =  new AppOrderDetailsModel(27);
	     
	    $clarity = $newmodel->getClarityList();
	    $color = $newmodel->getColorList();
	    $caizhi = $newmodel->getCaizhiList();
	    $jinse = $newmodel->getJinse();
	    $face_work = $newmodel->getFaceworkList();
	    $xiangqian = $newmodel->getXiangqianList(false);
	    $buchan_type = $newmodel->getBuchanList();
	    $cert_type = $newmodel->getCertTypeList();
	
	    $where['id']=$order_id;
	    $OrderDetails = $newmodel->getGoodsInfoByDetailsId($where);
	
	    $keziModel = new KeziModel();
	    if(!empty($OrderDetails)){
	        $kezi = $keziModel->retWord($OrderDetails['kezi']);
	    }else{
	        $kezi = '';
	    }
	
	
	    $result['content'] = $this->fetch('app_order_details_info_goods.html',array(
	        'order_id'=>$order_id,
	        'clarity'=>$clarity,
	        'color'=>$color,
	        'caizhi'=>$caizhi,
	        'jinse'=>$jinse,
	        'cert_type'=>$cert_type,
	        'face_work'=>$face_work,
	        'xiangqian'=>$xiangqian,
	        'buchan_type'=>$buchan_type,
	        'orderdetails'=>$OrderDetails,
	        'kezi'=>$kezi,
	         
	    ));
	    $result['title'] = '添加新品';
	    Util::jsonExit($result);
	}	
	/**
	 * 添加新品 保存
	 * @param unknown $param
	 */
	public function insertGoods($param) {
	    $result = array('success' => 0,'error' => '');
	    $order_id = _Request::getInt('order_id');
	
	    //判断订单是否有效
	    $orderModel= new BaseOrderInfoModel(27);
	    $order_info = $orderModel->getOrderInfoById($order_id);
	
	    if(empty($order_info)){
	        $result['error'] = "此订单的数据不存在！";
	        Util::jsonExit($result);
	    }
	    if($order_info['is_delete']==1){
	        $result['error'] = "此订单已删除！";
	        Util::jsonExit($result);
	    }
	    if($order_info['order_status']!=1){
	        $result['error'] = "此订单状态已经审核或取消或关闭！";
	        Util::jsonExit($result);
	    }
	    if($order_info['order_pay_status']!=1){
	        $result['error'] = "此订单已支付！";
	        Util::jsonExit($result);
	    }
	
	    $goods_id = _Request::getString('goods_id','');
	    $goods_sn = _Request::getString('goods_sn','');
	    $goods_name = _Request::getString('goods_name','');
	    $chengjiaojia = _Request::get('chengjiaojia');
	    $cart = _Request::get('cart');
	    $zhushi_num = _Request::get('zhushi_num');
	    $xiangkou = _Request::get('xiangkou');
	    $clarity = _Request::getString('clarity');
	    $color = _Request::getString('color');
	    $cert = _Request::getString('cert');
	    $zhengshuhao = _Request::getString('zhengshuhao');
	    $caizhi = _Request::getString('caizhi');
	    $jinse = _Request::getString('jinse');
	    $jinzhong = _Request::get('jinzhong');
	    $zhiquan = _Request::get('zhiquan');
	    $kezi = _Request::getString('kezi');
	    $face_work = _Request::getString('face_work');
	    $xiangqian = _Request::getString('xiangqian');
	    $info = _Request::getString('info');
	    $order_type = _Request::getString('order_type');
	    if(empty($goods_sn)){
	        $result['error'] = "款号不能为空！";
	        Util::jsonExit($result);
	    }
	    if(!empty($cart) && !is_numeric($cart)){
	        $result['error'] = "主石单颗重不合法，必须为数字！";
	        Util::jsonExit($result);
	    }
	    if(!empty($zhushi_num) && !Util::isNum($zhushi_num)){
	        $result['error'] = "主石粒数不合法，必须为正整数！";
	        Util::jsonExit($result);
	    }
	    if(!empty($zhiquan) && !is_numeric($zhiquan)){
	        $result['error'] = "指圈不合法，必须为数字！";
	        Util::jsonExit($result);
	    }
	    if(!empty($jinzhong) && !is_numeric($jinzhong)){
	        $result['error'] = "金重不合法，必须为数字！";
	        Util::jsonExit($result);
	    }
	    if(!empty($chengjiaojia) && !is_numeric($chengjiaojia)){
	        $result['error'] = "成交价不合法，必须为数字！";
	        Util::jsonExit($result);
	    }
	
	    //判断款号是否有效
	    $styleModel = new ApiStyleModel();
	    if($goods_sn && strtoupper($goods_sn) !='DIA' && strtoupper($goods_sn) !='CAIZUAN'){
	        //update by liuinyan 20151228 for boss_10031
	        //判断这个货品在商品列表有没有
	        $warehouseModel = new ApiWarehouseModel();
	        $goods_arr = array('goods_id'=>$goods_id);
	        $warehouseinfo = $warehouseModel->getWarehouseGoodsInfo($goods_arr);
	        	
	        $styleinfo = $styleModel->getStyleInfo($goods_sn);
	        if($warehouseinfo['error']==0)
	        {
	            //如果商品在商品列表里面存在  并且货号填写的款号 和改货号保存在仓库里面的款号一致 就什么都不做,
	            //否则按照原来的流程继续走
	            $ginfo = $warehouseinfo['data']['data'];
	            if($ginfo['order_goods_id'] > 0 || !empty($ginfo['order_goods_id']))
	            {
	                //没有此款号
	                $result['error'] = "此货号已经绑定了其他的订单,detailsid为:".$ginfo['order_goods_id'];
	                Util::jsonExit($result);
	            }elseif($goods_sn != $ginfo['goods_sn']){
	                //两者款号不一致
	                $result['error'] = "填写的款号和该货品在系统里面的款号不一致！";
	                Util::jsonExit($result);
	            }
	        }elseif(empty($styleinfo['data'])){
	            //没有此款号
	            $result['error'] = "此款号不存在！";
	            Util::jsonExit($result);
	        }elseif($styleinfo['data']['check_status']!=3){
	            $result['error'] = "只有已审核的款才可以下单！";
	            Util::jsonExit($result);
	        }
	    }else{
            if($goods_id != ''){
                $stockarrcheck=$orderModel->getGoodsStockCheck($goods_id);
                if($stockarrcheck){
                    $result['error'] = "亲~ 此裸钻已经绑定订单";
                    Util::jsonExit($result);
                }
            }
        }
	
	    //验证证书号必须为数字
	    /*if($zhengshuhao !=''){
	     if(!is_numeric($zhengshuhao)){
	     $result['error'] ='证书号必须为数字,请不要附带文字描述或证书类型GIA一类的字符';
	     Util::jsonExit($result);
	     }
	     $baseOrderInfoModel = new BaseOrderInfoModel(51);
	     $isReal = $baseOrderInfoModel->checkZhengshuhao($zhengshuhao);
	     if($isReal === false){
	     $result['error'] ='证书号不存在';
	     Util::jsonExit($result);
	     }
	     }*/
	
	    //刻字验证
	    if(isset($kezi) && !empty($kezi)){
	        $kezi = $this->checkKeziStr($goods_sn,$kezi);
	    }
	
	    $goods_details = array();
	    $goods_detail[0]['goods_id']=$goods_id;
	    $goods_detail[0]['goods_sn']=$goods_sn;
	    $goods_detail[0]['ext_goods_sn']=$goods_id;
	    $goods_detail[0]['goods_name']=$goods_name;
	    $goods_detail[0]['goods_price']=$chengjiaojia;
	    $goods_detail[0]['goods_count']=1;
	    $goods_detail[0]['create_time'] = date("Y-m-d H:i:s");
	    $goods_detail[0]['create_user'] = $_SESSION['userName'];
	    $goods_detail[0]['modify_time'] = date("Y-m-d H:i:s");
	    $goods_detail[0]['details_status'] = 1;
	    $goods_detail[0]['is_stock_goods'] = $order_type;
	    $goods_detail[0]['details_remark'] = $info;
	    $goods_detail[0]['cut'] = '';
	    $goods_detail[0]['cart'] = $cart?$cart:0;
	    $goods_detail[0]['zhushi_num'] = $zhushi_num?$zhushi_num:0;
	    $goods_detail[0]['clarity'] = $clarity;
	    $goods_detail[0]['color'] = $color;
	    $goods_detail[0]['cert'] = $cert;
	    $goods_detail[0]['zhengshuhao'] = $zhengshuhao;
	    $goods_detail[0]['caizhi'] = $caizhi;
	    $goods_detail[0]['jinse'] = $jinse;
	    $goods_detail[0]['jinzhong'] = $jinzhong?$jinzhong:0;
	    $goods_detail[0]['zhiquan'] = $zhiquan?$zhiquan:0;
	    $goods_detail[0]['kezi'] = $kezi;
	    $goods_detail[0]['face_work'] = $face_work;
	    $goods_detail[0]['xiangqian'] = $xiangqian;
	    $goods_detail[0]['xiangkou'] = $xiangkou;
	
	    $goods_detail[0]['cat_type'] = 0;
	    $goods_detail[0]['product_type'] = 0;
	    if($goods_sn == 'DIA'){
	        $goods_detail[0]['goods_type'] = 'lz';
	    }elseif($goods_sn == 'CAIZUAN'){
	        $goods_detail[0]['goods_type'] = 'caizuan_goods';
	    }else{
	        $goods_detail[0]['goods_type'] = 'style_goods';
	    }
	
	    $goods_detail[0]['kuan_sn'] = '';
	    $goods_detail[0]['dia_type'] = 2;
	
	    //订单默认是现货单，当前的商品是定制并且订单的当前状态是现货，则需要修改订单为定制单
	    $order = array();
	    $order['order_id'] =  $order_id;
	    $order['is_edit'] =  0;
	    $goods_amount = 0;
	    foreach ($goods_detail as $val){
	        $goods_amount += $val['goods_price'];
	        if($order_info['is_xianhuo'] != 0){
	            $order['is_xianhuo'] = 1;
	            $order['is_edit'] =  1;
	        }
	    }
	    //获取订单总金额
	    $order_account_arr = $orderModel->getOrderAccount($order_id);
	    $order_amount = $order_account_arr['order_amount'];
	    //未付的钱
	    $unaid_order_price = $order_amount + $goods_amount;
	    //订单金额
	    $money['money_unpaid'] = $unaid_order_price;//未付
	    $money['order_amount'] = $unaid_order_price;//订单总金额 = 未付
	    $money['money_paid'] = 0;//已付
	    $money['goods_amount'] = $order_account_arr['goods_amount'] + $goods_amount;//商品价格
	
	    //操作日志
	    $ation['order_id'] =  $order_id;
	    $ation['order_status'] = 1;
	    $ation['shipping_status'] = 1;
	    $ation['pay_status'] = 1;
	    $ation['create_user'] = $_SESSION['userName'];
	    $ation['create_time'] = date("Y-m-d H:i:s");
	    $ation['remark'] = "添加商品:". $goods_id ."-". $goods_sn;
	    //保存所有数据
	    $all_data = array('order'=>$order,'goods'=>$goods_detail,'money'=>$money,'action'=>$ation);
	    $res = $orderModel->makeNewOrderGoods($all_data);
	    if($res !== false){
	        $result['success'] = 1;
	        $result['error'] = '添加成功';
	    }else{
	        $result['error'] = "添加商品失败";
	    }
	    Util::jsonExit($result);
	}
	
	


	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$tab_id = _Post::getInt('tab_id');//主记录对应的列表页签id
		$result = array('success' => 0,'error' => '');
		//根据明细查主表id
		$model = new AppOrderDetailsModel($id,27);
		$_id = $model->getvalue('order_id');
		$result['content'] = $this->fetch('app_order_details_info.html',array(
			'view'=>new AppOrderDetailsView(new AppOrderDetailsModel($id,27)),
			'tab_id'=>$tab_id,
			'_id'=>$_id //主表id
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}


    /**
	 *	apply_favorable，渲染修改页面
	 */
	public function apply_favorable ($params)
	{
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
            die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        } 		
		$id = intval($params["id"]);
		$tab_id = _Post::getInt('tab_id');//主记录对应的列表页签id
		$result = array('success' => 0,'error' => '','title'=>'申请优惠');
		//根据明细查主表id
        
        $newmodel =  new AppOrderDetailsModel($id,28);
        $diamondModel = new SelfDiamondModel(19);
        $OrderDetails = $newmodel->getDataObject();
        if(empty($OrderDetails)){
            $result['content'] ="当前选中的订单商品信息不存在!";
            Util::jsonExit($result);
        }
        if ($OrderDetails['allow_favorable']!=1) {
            $result['content'] ="此商品已经是最低价，不允许申请优惠!";
            Util::jsonExit($result);
        }
        $is_zp = $OrderDetails['is_zp'];
        $goods_type = $OrderDetails['goods_type'];
        $cert_id = $OrderDetails['zhengshuhao'];
        //查询 裸钻是否是双十一活动   begin by gaopeng            
        if($goods_type=='lz' && !empty($cert_id)){
            $is_ssy=$diamondModel->selectDiamondSSY("count(*)","cert_id='{$cert_id}'",3);
            if($is_ssy){
                $result['content'] ="此商品为双十一特价钻不能申请优惠！";
                Util::jsonExit($result);
            }
        }//查询 裸钻是否是双十一活动 end
        
        if($is_zp==1){
            $result['content'] ="赠品无法申请优惠!";
            Util::jsonExit($result);
        }
        $goods_type = $OrderDetails['goods_type'];
        $carat = $OrderDetails['policy_id'];
        $salepolicy_id =$OrderDetails['policy_id'];
        if(!empty($salepolicy_id)){
            $where = array(
                'policy_id'=> $salepolicy_id,
            );
            $apiModel = new ApiSalePolicyModel();
            $data = $apiModel->SalePolicyInfo($where);

            if(!empty($data['data'])){
                if($data['data']['is_favourable']==2){
                    //如果是禁止调价的销售政策则返回
                    $result['content']='销售政策：'.$data['data']['policy_name'].'不可申请优惠';
                    $result['title'] = '申请优惠';
                    Util::jsonExit($result);
                }
            }
        }
		
		//update by liulinyan lly
		$baseModel = new BaseOrderInfoModel(28);
		$tmporderinfo = $baseModel->getOrderInfoById($OrderDetails['order_id']);
		//这里不可能存在订单不存在的情况 所以直接拿取值
		$departmentid = $tmporderinfo['department_id'];
		
		$result['content'] = $this->fetch('app_order_details_favorable_info.html',array(
            'view'=>new AppOrderDetailsView($newmodel),
			'tab_id'=>$tab_id,
            'id'=>$id,
            'carat'=>$carat,
            'goods_type'=>$goods_type,
			'departmentid'=>$departmentid 
		));
		$result['title'] = '申请优惠';
		Util::jsonExit($result);
	}



    /**
	 *	apply_coupon，渲染修改(积分商城代金券)页面
	 */
	public function apply_daijinquan ($params)
	{
		$id = intval($params["id"]);
		$tab_id = _Post::getInt('tab_id');//主记录对应的列表页签id
		$result = array('success' => 0,'error' => '','title'=>'代金券优惠');
		//根据明细查主表id
        
        $newmodel =  new AppOrderDetailsModel($id,28);
        $diamondModel = new SelfDiamondModel(19);
        $OrderDetails = $newmodel->getDataObject();
        if(empty($OrderDetails)){
            $result['content'] ="当前选中的订单商品信息不存在!";
            Util::jsonExit($result);
        }
        if ($OrderDetails['allow_favorable']!=1) {
            $result['content'] ="此商品已经是最低价，不允许申请代金券优惠!";
            Util::jsonExit($result);
        }
        $is_zp = $OrderDetails['is_zp'];
        $goods_type = $OrderDetails['goods_type'];
        $cert_id = $OrderDetails['zhengshuhao'];
        //查询 裸钻是否是双十一活动   begin by gaopeng            
        if($goods_type=='lz' && !empty($cert_id)){
            $is_ssy=$diamondModel->selectDiamondSSY("count(*)","cert_id='{$cert_id}'",3);
            if($is_ssy){
                $result['content'] ="此商品为双十一特价钻不能申请代金券优惠！";
                Util::jsonExit($result);
            }
        }//查询 裸钻是否是双十一活动 end
        
        if($is_zp==1){
            $result['content'] ="赠品无法申请代金券优惠!";
            Util::jsonExit($result);
        }
        $goods_type = $OrderDetails['goods_type'];
        $carat = $OrderDetails['policy_id'];
        $salepolicy_id =$OrderDetails['policy_id'];
        if(!empty($salepolicy_id)){
            $where = array(
                'policy_id'=> $salepolicy_id,
            );
            $apiModel = new ApiSalePolicyModel();
            $data = $apiModel->SalePolicyInfo($where);

            if(!empty($data['data'])){
                if($data['data']['is_favourable']==2){
                    //如果是禁止调价的销售政策则返回
                    $result['content']='销售政策：'.$data['data']['policy_name'].'不可申请代金券优惠';
                    $result['title'] = '代金券优惠';
                    Util::jsonExit($result);
                }
            }
        }
		
		//update by liulinyan lly
		$baseModel = new BaseOrderInfoModel(28);
		$tmporderinfo = $baseModel->getOrderInfoById($OrderDetails['order_id']);
		//这里不可能存在订单不存在的情况 所以直接拿取值
		$departmentid = $tmporderinfo['department_id'];
		
		$result['content'] = $this->fetch('app_order_details_daijinquan_info.html',array(
            'view'=>new AppOrderDetailsView($newmodel),
			'tab_id'=>$tab_id,
            'id'=>$id,
            'carat'=>$carat,
            'goods_type'=>$goods_type,
			'departmentid'=>$departmentid 
		));
		$result['title'] = '代金券优惠';
		Util::jsonExit($result);
	}


   /**
	 *	apply_jifen，渲染修改(积分商城使用积分码)页面
	 */
	public function apply_jifenma ($params)
	{
		$id = intval($params["id"]);
		$tab_id = _Post::getInt('tab_id');//主记录对应的列表页签id
		$result = array('success' => 0,'error' => '','title'=>'积分码');
		//根据明细查主表id
        
        $newmodel =  new AppOrderDetailsModel($id,28);
        $diamondModel = new SelfDiamondModel(19);
        $OrderDetails = $newmodel->getDataObject();
        if(empty($OrderDetails)){
            $result['content'] ="当前选中的订单商品信息不存在!";
            Util::jsonExit($result);
        }
       
        $is_zp = $OrderDetails['is_zp'];
        $goods_type = $OrderDetails['goods_type'];
 
        if($is_zp==1){
            $result['content'] ="赠品不能积分码!";
            Util::jsonExit($result);
        }
        $goods_type = $OrderDetails['goods_type'];
        $carat = $OrderDetails['policy_id'];
       
		
		//update by liulinyan lly
		$baseModel = new BaseOrderInfoModel(28);
		$tmporderinfo = $baseModel->getOrderInfoById($OrderDetails['order_id']);
		//这里不可能存在订单不存在的情况 所以直接拿取值
		$departmentid = $tmporderinfo['department_id'];
		
		$result['content'] = $this->fetch('app_order_details_jifenma_info.html',array(
            'view'=>new AppOrderDetailsView($newmodel),
			'tab_id'=>$tab_id,
            'id'=>$id,
            'carat'=>$carat,
            'goods_type'=>$goods_type,
			'departmentid'=>$departmentid 
		));
		$result['title'] = '积分码';
		Util::jsonExit($result);
	}


    /**
	 *	cunpon_apply，优惠券使用
	 */
	public function coupon_apply ($params)
	{
		$id = intval($params["id"]);
        $tab_id = _Post::getInt('tab_id');//主记录对应的列表页签id
		$result = array('success' => 0,'error' => '');
		//根据明细查主表id
        $orderModel = new BaseOrderInfoModel(27);
        $orderInfo = $orderModel->getOrderInfoById($id);
        if($orderInfo['order_pay_status']==3){
            $result['content'] = "已审核的订单不能使用优惠券！";
            Util::jsonExit($result);
        }
        $detailModel = new AppOrderDetailsModel(27);
        if($detailModel->getOrderCouponPrice($id)>0){
            $result['content'] = "该订单已使用优惠券！";
            Util::jsonExit($result);
        }
		$result['content'] = $this->fetch('app_order_details_coupon_info.html',array(
            'view'=>new AppOrderDetailsView($detailModel),
			'tab_id'=>$tab_id,
            'id'=>$id
		));
		$result['title'] = '申请优惠';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{

        $result = array('success' => 0,'error' => '');
		$id = intval($params["id"]);
        $domodel = new AppOrderDetailsModel($id,27);
        $detailView = new AppOrderDetailsView($domodel);
        $order_id = $domodel->getValue('order_id');
        $basemodel = new BaseOrderInfoModel($order_id,27);
        $baseinfo = $basemodel->getDataObject();
        $page = _Request::getInt("page",1);
        $where = array(
        		'id' =>$id
        );
        $model = new AppOrderDetailsModel(27);
        $data = $model->get_order_goods($where,$page,20,false);
        $goods_detail=current($data['data']);
        $keziModel = new KeziModel();
        $kezi = $keziModel->retWord($detailView->get_kezi());
        $result['content'] = $this->fetch('app_order_details_show.html',array(
            'view'=>$detailView,
            'kezi'=>$kezi,
        	'goods_detail'=>$goods_detail,
            'hidden'=>Util::zhantingInfoHidden($baseinfo)
        ));
        $result['title'] = '订单商品详细查看';
        Util::jsonExit($result);
	}


	/**
	 *	buchan_edit，渲染查看页面
	 */
	public function buchan_edit ($params)
	{
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
        	die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }	    
		$result = array('success' => 0,'content'=>'','title' => '布产信息修改');	
		
		$id = _Request::getInt('id',0);
		if(empty($id)){
		    $result['content'] = "ID is empty!";
		    Util::jsonExit($result);
		}
		
		$goodsAttrModel = new GoodsAttributeModel(17);
		$newmodel =  new AppOrderDetailsModel($id,27);
		$productModel = new SelfProductInfoModel(14);
		$orderDetail = $newmodel->getDataObject();

		if(empty($orderDetail)){
		    $result['content'] = "商品明细查询失败!";
		    Util::jsonExit($result);
		}
        $bhInfo = $newmodel->getOutOrderInfo($id);
        if(!empty($bhInfo)){
            $result['content'] = "亲~，货品已占用备货名额，请先取消占用备货名额后修改，谢谢(●'◡'●)";
            Util::jsonExit($result);
        }
		$bc_id    = $orderDetail['bc_id'];
		$order_id = $orderDetail['order_id'];
		if($bc_id==0){
		    $this->editOrderGoods($params);
		    //$result['content'] = "订单商品还未布产!";
		    //Util::jsonExit($result);
		}
		$buchanInfo = $productModel->getBuchanInfo($bc_id);
		if(empty($buchanInfo)){
		    $result['content'] = "订单商品未找到布产ID为 {$bc_id}的布产单!";
		    Util::jsonExit($result);
		} 
		$applyInfo = $productModel->getProductApplyByDetailId($id);
		if(!empty($applyInfo)){
		    if($applyInfo['apply_name']==$_SESSION['userName']){
		        $result['content'] = "你已提交过一笔布产单修改申请，还未处理，请等待审批完成后再申请!上次申请时间：{$applyInfo['apply_time']} ";
		    }else{
		        $result['content'] = "已有用户提交过一笔布产单修改申请，还未处理，请等待审批完成后再申请! 上次申请人：{$applyInfo['apply_name']},申请时间：{$applyInfo['apply_time']}";
		    }		    
		    Util::jsonExit($result);
		}
		$goodsAttrs['clarity'] = $goodsAttrModel->getClarityList();
		$goodsAttrs['color'] = $goodsAttrModel->getColorList();
		$goodsAttrs['caizhi'] = $goodsAttrModel->getCaizhiList();
		$goodsAttrs['jinse'] = $goodsAttrModel->getJinseList();
		$goodsAttrs['facework'] = $goodsAttrModel->getFaceworkList();
		$goodsAttrs['xiangqian'] = $goodsAttrModel->getXiangqianList();
		$goodsAttrs['buchanType'] = $goodsAttrModel->getBuchanTypeList();
		$goodsAttrs['cert'] = $goodsAttrModel->getCertList();

        //获取商品款式属性维护信息
        $apiStyle = new ApiStyleModel();
        $goods_attr = $apiStyle->GetStyleAttribute(array('style_sn'=>$orderDetail['goods_sn']));
        //表面工艺，根据款式维护属性控制
        if(!empty($goods_attr['data'][27]['value'])){
            $face_work_split = explode(',',$goods_attr['data'][27]['value']);
            $face_work = array();
            foreach($face_work_split as $vo){
                if(trim($vo)!=''){
                    $face_work[$vo]=$vo;
                }
            }

            $goodsAttrs['facework'] = $face_work;
        }

        $baseOrderModel = new BaseOrderInfoModel($order_id, 27);
        $buchanInfo['order_status'] = $baseOrderModel->getValue('order_status');
        $keziModel = new KeziModel();
        $kezi = isset($buchanInfo['kezi'])?$buchanInfo['kezi']:'';
        $kezi = $keziModel->retWord($kezi); 
        $buchanInfo['keziShow'] = $this->replaceTsKezi($kezi);//布产单刻字信息
		$result['content'] = $this->fetch('buchan_info_edit.html',array(
            'buchanInfo'=>$buchanInfo,
		    'orderDetail'=>$orderDetail,
	        'goodsAttrs'=>$goodsAttrs,

		));
		Util::jsonExit($result);
	}

	/**
	 *	buchan_edit，渲染查看页面
	 */
	public function buchan_update ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = _Request::getInt("id",0);
		if($id==0){
		    $result['error'] = "id is empty!";
		    Util::jsonExit($result);
		}
		$goods_type = _Request::get('goods_type');//商品类型

		$args=array(
				'cart'=>_Request::get('cart'),//主石单颗重
		        'zhushi_num'=>_Request::get('zhushi_num'),//主石粒数
				'clarity'=>_Request::get('clarity'),
				'caizhi'=>_Request::get('caizhi'),
				'color'=>_Request::get('color'),
				'zhengshuhao'=>_Request::get('zhengshuhao'),
				'face_work'=>_Request::get('face_work'),
				'jinse'=>_Request::get('jinse'),
				'jinzhong'=>_Request::get('jinzhong'),
				'zhiquan'=>_Request::get('zhiquan'),
				'kezi'=>_Request::get('kezi'),
				'xiangqian'=>_Request::get('xiangqian'),
				'xiangkou'=>_Request::get('xiangkou'),
				'bc_style'=>_Request::get('bc_style'),
		        'special'=>_Request::get('special'),
		        'cert'=>_Request::get('cert'),		    
		);
		$checkData = $args;	
        $res = $this->checkOrderGoodsData($checkData);
        if($res['success']==0){
            $result['error'] = $res['error'];
            Util::jsonExit($result);
        }else{
            $args = $res['data'];
        }
        $model = new AppOrderDetailsModel($id,27);
        $orderDetail = $model->getDataObject();
        if(empty($orderDetail)){
            $result['error'] = "订单商品明细查询失败!";
            Util::jsonExit($result);
        }
        $order_id = $orderDetail['order_id'];  
        $bc_id    = $orderDetail['bc_id'];
        $goods_type = $orderDetail['goods_type'];
        $style_sn = $orderDetail['goods_sn'];
        //验证刻字
        if(!empty($args['kezi']))
        {
            $args['kezi'] = $this->checkKeziStr($style_sn,$args['kezi']);
        }
        $productModel = new SelfProductInfoModel(14);
		$buchanInfo = $productModel->getBuchanInfo($bc_id);
		if(empty($buchanInfo)){
		    $result['error'] = "布产ID为 {$bc_id}的布产单不存在!";
		    Util::jsonExit($result);
		}
		$applyInfo = $productModel->getProductApplyByDetailId($id);
	    if(!empty($applyInfo)){
		    if($applyInfo['apply_name']==$_SESSION['userName']){
		        $result['error'] = "你已提交过一笔布产单修改申请，还未处理，请等待审批完成后再申请!上次申请时间：{$applyInfo['apply_time']} ";
		    }else{
		        $result['error'] = "已有用户提交过一笔布产单修改申请，还未处理，请等待审批完成后再申请! 上次申请人：{$applyInfo['apply_name']},申请时间：{$applyInfo['apply_time']}";
		    }		    
		    Util::jsonExit($result);
		}
        if($goods_type !='lz' ){
            if(preg_match("/镶嵌4C/is",$args['xiangqian'])){
                $args['is_peishi'] = 2;
            }else{
                $args['is_peishi'] = 0;
            }
        }        
  
		$names =array('id'=>'订单详情id','cart'=>'主石单颗重','zhushi_num'=>'主石粒数','clarity'=>'主石净度','color'=>'主石颜色','cert'=>'证书类型','zhengshuhao'=>'证书号','caizhi'=>'材质','jinse'=>'金色','jinzhong'=>'金重','xiangkou'=>'镶口','zhiquan'=>'指圈','kezi'=>'刻字','face_work'=>'表面工艺','xiangqian'=>'镶嵌要求','bc_style'=>'布产类型','special'=>'特别要求','is_peishi'=>'是否支持4C配钻');
		$newdo['id']=$id;
		$apply_info = array();
		$old_info = array();

		foreach($names as $key=>$name){	 
		    if(!isset($buchanInfo[$key])){
		        $buchanInfo[$key] = '';
		    }
		    $old_info[]=array('name'=>$name,'code'=>$key,'value'=>$buchanInfo[$key]);
			if(isset($args[$key]) && $buchanInfo[$key] != $args[$key]){				   
				$apply_info[]=array('name'=>$name,'code'=>$key,'value'=>$args[$key]);
			}			

		}
        //$result['error'] = var_export($old_info,true).'<hr/>'.var_export($apply_info,true);
        //Util::jsonExit($result);  
		$addApplyInfoFlag = empty($apply_info)?false:true;
		if($addApplyInfoFlag===true){
    		$applyInfoData = array(
    		    'detail_id'=>$id,
    		    'apply_id'=>$_SESSION['userId'],
    		    'apply_name'=>$_SESSION['userName'],
    		    'apply_info'=>serialize($apply_info),
    		    'old_info'=>serialize($old_info),
    		    'order_sn' => $buchanInfo['p_sn'],
    		    'goods_status' => $buchanInfo['status'],
    		    'style_sn' => $buchanInfo['style_sn'],
    		    'special' => $args['special'],
    		    'apply_time' => date('Y-m-d H:i:s')
    		);				
    		$res = $productModel->addProductApplyInfo($applyInfoData);
    		if(!$res){
    		    $result['error'] = "提交布产单修改失败!";
                Util::jsonExit($result);
    		}else{
    		    $addApplyInfoFlag = true;
    		} 
		}
        //记录布产日志
        if($addApplyInfoFlag === true){
            $orderInfoModel = new BaseOrderInfoModel(28);
            $orderInfo = $orderInfoModel->getOrderInfoById($order_id);
            $str = '';
            foreach ($old_info as $k => $v) {
                foreach ($apply_info as $x => $y) {
                    if($v['code'] == $y['code'] && $v['value'] != $y['value'] && ($y['value'] != '--' || $y['value'] == ' ')){
                        $tmp_str = $v['value'];
                        if($v['value'] == ''){
                            $tmp_str = '空';
                        }
                        $str .= $v['name'].'['.$tmp_str.'] 申请修改为 <span style="color:red">'.$y['value'].'</span><br />';
                    }
                }
            }
            $logInfo = array(
                'order_id'=>$order_id,
                'order_status'=>$orderInfo['order_status'],
                'shipping_status'=>$orderInfo['send_good_status'],
                'pay_status'=>$orderInfo['order_pay_status'],
                'create_user'=>$_SESSION['userName'],
                'create_time'=>date("Y-m-d H:i:s"),
                'remark'=>'货号：'.$orderDetail['goods_id'].',申请修改布产信息:<br/>'.$str
            );            
            //写入订单日志
            $orderInfoModel->addOrderAction($logInfo);
        }
        $result['success'] = 1;
        Util::jsonExit($result);           
	}
	
	/**
	 * 检查订单商品，布产单的属性字段是否合法
	 * @param unknown $data
	 */
    protected function checkOrderGoodsData($args){
        $result = array('success' => 0,'error' => '');
        $is_stock_goods = !empty($args['is_stock_goods'])?1:0;
        $xianqian = !empty($args['xiangqian'])?$args['xiangqian']:"";
        //主石单颗重验证
        if(!empty($args['cart']) && !is_numeric($args['cart'])){
            $result['error']="主石单颗重不合法，主石单颗重必须为数字!";
            return $result;
        }else if(isset($args['cart'])){            
            $args['cart'] = $args['cart']/1;
        }
        //主石粒数验证
        if(!empty($args['zhushi_num']) && !preg_match("/^\d+$/",$args['zhushi_num'])){
            $result['error']="主石粒数不合法，主石粒数必须为正整数!";
            return $result;
        }else if(isset($args['zhushi_num'])){
            $args['zhushi_num'] = $args['zhushi_num']/1;
        }
        //$result['error']= var_export($args,true);
        //return $result;
        //期货单 且镶嵌方式 不是不需工厂镶嵌 且 不是布产单修改
        if($is_stock_goods==0 && $xianqian<>'不需工厂镶嵌'){
            if(isset($args['cart']) && isset($args['zhushi_num'])){
                if(($args['cart']==0 && $args['zhushi_num']>0) ||($args['cart']>0 && $args['zhushi_num']==0)){
                    $result['error']="主石单颗重和主石粒数不合要求，两者要么同时大于0，要么同时为空或0!";
                    $result['error'] .="<br/><font color='red'>如需更改主石粒数，请联系款式库专员-易霞</font>";
                    return $result;
                }
            }
        }
        //镶口
        if(!empty($args['xiangkou']) && !is_numeric($args['xiangkou'])){
            $result['error']="镶口不合法，镶口必须为数字!";
            return $result;
        }else if(isset($args['xiangkou'])){
            $args['xiangkou'] = $args['xiangkou']/1;
            //镶口是否合法
            if($is_stock_goods==0 && $xianqian<>'不需工厂镶嵌'){
                if(!empty($args['xiangkou']) && isset($args['cart'])){
                    if(!$this->GetStone((float)$args['xiangkou'],(float)$args['cart'])){
                        $result['error'] = "镶口和石重不匹配";
                        return $result;
                    }
                }
            }
        }
             
        //金重
        /*
        if(!empty($args['jinzhong']) && !is_numeric($args['jinzhong'])){
            $result['error']="金重不合法，金重必须为数字!";
            return $result;
        }else if(isset($args['jinzhong'])){
            $args['jinzhong'] = $args['jinzhong']/1;
        }*/
        
        //指圈
        if(!empty($args['zhiquan']) && !is_numeric($args['zhiquan'])){
            $result['error']="指圈不合法，指圈必须为数字!";
            return $result;
        }else if(isset($args['zhiquan'])){
            $args['zhiquan'] = $args['zhiquan']/1;
        }
        //证书号
        if(!empty($args['zhengshuhao']) && !preg_match("/^[\-|a-z|A-Z|0-9|\|]+$/is",$args['zhengshuhao'])){
            $result['error']="证书号不合法，证书号只能包含【字母】【数字】【英文竖线】,英文竖线作为多个证书号分隔符。";
            return $result;
        }
        //证书类型验证
        if($is_stock_goods==0){
            if(!empty($args['zhengshuhao']) && isset($args['cert']) && ($args['cert']=="" ||$args['cert']=="无")){
                $result['error']="证书类型不能为空或无，填写了证书号必须填写有效的证书类型";
                return $result;
            }
        }
        $result['success'] = 1;
        $result['data'] = $args;
        return $result;
    }
    /**
     * 订单商品优惠金额申请
     * @param type $param
     */
    public function apply_insert($param) {
        $id = _Post::getInt('id');
        $detailModel = new AppOrderDetailsModel($id,28);
        $order_goods_id = $detailModel->getValue('id');
        $goods_id = $detailModel->getValue('goods_id');
        $goods_sn = $detailModel->getValue('goods_sn');
        $carat = $detailModel->getValue('cart');
        $goods_price = $detailModel->getValue('goods_price');
        $cert = $detailModel->getValue('cert');
        $cert_id = $detailModel->getValue('cert_id');
        $goods_type = $detailModel->getValue('goods_type');
        $cert_id = $detailModel->getValue('zhengshuhao');
        $order_id = $detailModel->getValue('order_id');
        $create_user = $detailModel->getValue('create_user');
        $old_favorable_price = $detailModel->getValue('favorable_price');
        $old_favorable_status = $detailModel->getValue('favorable_status');
		$daijinquan_price = (float)$detailModel->getValue('daijinquan_price');

		//如果是淘宝订单
		//update by liulinyan 20151125 for BOSS-846
		$baseModel = new BaseOrderInfoModel($order_id,28);
		$departmentid = $baseModel->getValue('department_id');
		//这里不可能存在订单不存在的情况 所以直接拿取值
        /*if($goods_type == 'lz'){*/
            $mima = _Request::getString('discount_mima');
            $favorable_price = _Request::getFloat('favorable_price');
            $user_id = $_SESSION['userId'];
            //$type = $this->getDiamondType($carat);
            //处理优惠类型
            $saData['goods_type'] = $goods_type;//商品类型
            $saData['cert_id']    = $cert_id;//证书号
            $saData['goods_id']   = $goods_id;//货号
            $saData['goods_sn']   = $goods_sn;//款号
            $saData['carat']      = $carat;//石重
            $type = $this->getGoodsType($saData);
            $diamondModel = new DiamondListModel();
            $where = array('type'=>$type,'user_id'=>$user_id,'mima'=>$mima);
            $zd_info = $diamondModel->checkMimaVaildPrice($where);//判断优惠是否超过制单人打折权限
            
            if($zd_info['error'] ==1){
                $result['error'] = $zd_info['data'];
                Util::jsonExit($result);
            }
            $zd_zhekou = $zd_info['data']['zhekou'];
            if($zd_zhekou <1){
                $discount = 1- $zd_zhekou;
                //$zd_favorable_money = intval($goods_price * $discount);
                $zd_favorable_money =bcmul($goods_price,$discount,3);

            }
            //如果输入的金额大于了自己的最低折扣金额，这取打折密码优惠
            if( bccomp($favorable_price,$zd_favorable_money)==1){
                $data = $diamondModel->checkMimaVaild($where);
                //var_dump($data);die;
                if($data['error'] ==1){
                    $result['error'] = $data['data'];
                    Util::jsonExit($result);
                }
                $zhekou = $data['data']['zhekou'];
                $grant_id = $data['data']['id'];
                $favorable_money = 0;
                if($zhekou <1){
                    $discount = 1- $zhekou;
                    //$favorable_money = intval($goods_price * $discount);
                    $favorable_money =bcmul($goods_price,$discount,3);

                }

                if(bccomp($favorable_price,$favorable_money)==1){
                    $result['error'] = "您输入的优惠金额:".$favorable_price."不能大于折扣金额".$favorable_money;
                    Util::jsonExit($result);
                }
            }

            $have_favorable_price = 0;
            if($old_favorable_status == 3){
                //已审核通过的金额才属于优惠的
                $have_favorable_price = $old_favorable_price;
            }
            //此次优惠的钱+已经优惠的钱 >此商品的金额
           if($departmentid==2){ 
	            if($favorable_price + $have_favorable_price>$goods_price){
	                $result['error'] = '优惠金额不能大于商品金额！';
	                Util::jsonExit($result);
	            }
           }else{
	           	if(bccomp(bcadd($favorable_price,$daijinquan_price),$goods_price,2)==1){
	           		$result['error'] = '优惠金额不能大于商品金额！';
	           		Util::jsonExit($result);
	           	}
           }
            $baseOrderModel = new BaseOrderInfoModel(27);
            $order_Account = $baseOrderModel->getOrderAccount($order_id);
            if($departmentid==2){
                if(bccomp($order_Account['money_unpaid'] , $favorable_price,2)==-1){
                    $result['error'] = "您输入的优惠金额:".$favorable_price."不能大于应付尾款金额";
                    Util::jsonExit($result);
                }
            }else{
                if(bccomp(bcadd($order_Account['money_unpaid'],$have_favorable_price) , bcadd($favorable_price,$daijinquan_price),2)==-1){
                    $result['error'] = "您输入的优惠金额:".$favorable_price."不能大于应付尾款金额+此商品已优惠金额";
                    Util::jsonExit($result);
                }
            }


            if($zd_favorable_money < $favorable_price){
                //操作日志
                $shop_price = $goods_price - $favorable_price;
                $log_data['order_id'] = $order_id;
                $log_data['grant_id'] = $grant_id;
                $log_data['order_detail_id']  = $id;
                $log_data['market_price'] = $goods_price;
                $log_data['shop_price'] = $shop_price;
                $log_data['cert_id'] = $cert_id;
                //修改使用状态
                $udate_data['grant_id'] = $grant_id;
                $udate_data['order_goods_id'] = $order_goods_id;
                $udate_data['goods_sn'] = $goods_sn;
                $udate_data['goods_price'] = $goods_price;
                $udate_data['real_price'] = $goods_price;
                $udate_data['cert'] = $cert;
                $udate_data['cert_id'] = $cert_id;
                $udate_data['use_user_id'] = $_SESSION['userId'];
                $udate_data['use_user'] = $_SESSION['userName'];
                $udate_data['usetime'] = date("Y-m-d H:i:s");
                $udate_data['status'] = 2;

                $log_where = array('insert_data'=>$log_data,'update_data'=>$udate_data);
                $tmp = $diamondModel->updateDiscountMima($log_where);
            }
            
            /*if($tmp['error']==0){*/
            	/*
            	//修改订单金额：把此商品的原来的优惠的钱从订单中减去，重新计算订单金额：
	            if($old_favorable_status ==3 && $old_favorable_price!= 0){//只有原来的钱是审核通过，并且，才去重新继续钱
	            	//获取此订单的金额
	            	$baseOrderModel = new BaseOrderInfoModel(27);
	            	$account_arr = $baseOrderModel->getOrderAccount($order_id);
	            	$account_id = $account_arr['id'];
	            	$accountModel = new AppOrderAccountModel($account_id,28);

	            	$money['id'] = $account_id;
	            	$money['order_amount'] = $account_arr['money_unpaid'] + $old_favorable_price;//订单总金额 = 未付 + 原来优惠的钱
	            	$money['money_unpaid'] = $account_arr['money_unpaid'] + $old_favorable_price;//未付 + 原来优惠的钱
	            	$money['favorable_price'] = $account_arr['favorable_price'] - $old_favorable_price;//商品优惠 - 原来优惠的钱

	            	$accountModel->saveData($money, $account_arr);
	            }*/


                //修改优惠金额
            	if($departmentid==2)
            	{
            		$detailModel->setValue('favorable_price', $old_favorable_price+$favorable_price);
            		$detailModel->setValue('favorable_status', 3);
            	}else{
            		//保存优惠金额
            		$detailModel->setValue('favorable_price', $favorable_price+$daijinquan_price);
            		$detailModel->setValue('favorable_status', 3);
            	}

            	$detailModel->save(true);
            	
                //获取此订单的金额
                $baseOrderModel = new BaseOrderInfoModel(27);
                $account_arr = $baseOrderModel->getOrderAccount($order_id);
                $account_id = $account_arr['id'];
                $accountModel = new AppOrderAccountModel($account_id,28);
                //把订单金额/未付  - 优惠金额
                //区分淘宝的
				if($departmentid==2)
				{
					$money['order_amount'] = $account_arr['order_amount']- $favorable_price;
					$money['money_unpaid'] = $account_arr['money_unpaid'] - $favorable_price;
					$money['favorable_price'] = $account_arr['favorable_price'] + $favorable_price;
				}else{	
					//订单金额 = 订单金额+之前的优惠金额-这次的优惠
					$orderamount = $account_arr['order_amount']+$old_favorable_price-$favorable_price-$daijinquan_price ;
					$money['order_amount'] = $orderamount;
					$order_unpaid = $account_arr['money_unpaid']+$old_favorable_price-$favorable_price-$daijinquan_price;
					$money['money_unpaid'] = $order_unpaid;
					$money['favorable_price'] = $account_arr['favorable_price']-$old_favorable_price+$favorable_price+$daijinquan_price;//商品优惠
					
				}
				$money['id'] = $account_id;
                $accountModel->saveData($money, $account_arr);
				
				//修改发票金额
				$account_arr = $baseOrderModel->getOrderAccount($order_id);
				$Imodel =new AppOrderInvoiceModel(28);
				$Imodel->updateIprice($account_arr['order_amount'],$order_id);

                //更新订单明细积分信息
				try {
                    $order_info = $detailModel->getOrderInfo($id);
				    $pointRules = Util::point_api_get_config($order_info['department_id'], $order_info['mobile']);

				}
				catch (Exception $e) {
				    //无法确认积分规则，则暂时不更新，由最终赠送时再在处理
				}
                if (!empty($pointRules) && $pointRules['is_enable_point']) {
                    $pointModel = new SelfModel(27);
                    $pointModel->update_orderdetail_point($id, $pointRules);
                }
                Util::jsonExit(array('success'=>1,'error'=>'操作成功'));
            /*}else{
                $result['error'] = '操作失败';
                Util::jsonExit($result);
            }*/
        /*}else{

            //非裸钻优惠
            $favorable_price = _Post::getFloat('favorable_price');
            //$check_user = _Post::getInt('check_user');
            $check_user_name = _Post::getString('check_user_name');
			$favorable_status = $detailModel->getValue('favorable_status');
// 			if($favorable_status==2){
// 				$detailModel->setValue('favorable_status', 1);
// 				$detailModel->delete_favorable_price($order_id);
// 			}
            if(!$id){
                $result['error'] = '没有该商品';
                Util::jsonExit($result);
            }
            if($check_user_name==''){
                $result['error'] = '没有指定审批人';
                Util::jsonExit($result);
            }
            $userModel = new UserModel(1);
            $user_id = $userModel->getAccountId($check_user_name);
            if(!$user_id){
                $result['error'] = '系统中没有该用户';
                Util::jsonExit($result);
            }
            $detailsModel = new AppOrderDetailsModel($id,28);
            $data = $detailsModel->getOrderInfo($id);
            $have_favorable_price = 0;
            if($data['favorable_status'] == 3){
                //已审核通过的金额才属于优惠的
                $have_favorable_price = $data['favorable_price'];
            }
           
          if($departmentid==2){ 
          	//此次优惠的钱+已经优惠的钱 >此商品的金额
            if($favorable_price + $have_favorable_price>$data['goods_price']){
                $result['error'] = '优惠金额不能大于商品金额！';
                Util::jsonExit($result);
            }
           }else{
           	if($favorable_price > $data['goods_price']){
           		$result['error'] = '优惠金额不能大于商品金额！';
           		Util::jsonExit($result);
           	}
           }
            //获取此订单的金额
            $baseOrderModel = new BaseOrderInfoModel(27);
            $account_arr = $baseOrderModel->getOrderAccount($order_id);
            $account_id = $account_arr['id'];

            if($account_arr['money_unpaid'] < $favorable_price){
                $result['error'] = "您输入的优惠金额:".$favorable_price."不能大于应付尾款金额";
                Util::jsonExit($result);
            }
           /*
            //修改订单金额：把此商品的原来的优惠的钱从订单中减去，重新计算订单金额：
            if( $old_favorable_status ==3 && $old_favorable_price!= 0 && $departmentid !=2 )
			{//只有原来的钱是审核通过，才去重新继续钱
            	$accountModel = new AppOrderAccountModel($account_id,28);

            	$money['id'] = $account_id;
            	$money['order_amount'] = $account_arr['money_unpaid'] + $old_favorable_price;//订单总金额 = 未付 + 原来优惠的钱
            	$money['money_unpaid'] = $account_arr['money_unpaid'] + $old_favorable_price;//未付 + 原来优惠的钱
            	$money['favorable_price'] = $account_arr['favorable_price'] - $old_favorable_price;//商品优惠 - 原来优惠的钱

            	$accountModel->saveData($money, $account_arr);
            }
           */
            /*$orderModel = new BaseOrderInfoModel(27);
            $orderPriceInfo = $orderModel->getDetailPriceInfo($order_id);

            //print_r($orderPriceInfo['favorable_price']);die;
            if($order_id){
                $orderinfo = $orderModel->getOrderInfoById($order_id);
                if($orderinfo){
                    $consignee = $orderinfo['consignee'];
                }
            }
            if($departmentid==2){
             $_favorable_price = $favorable_price + $orderPriceInfo['favorable_price'];
            }else{
            	$_favorable_price = $favorable_price;
            }
            if($_favorable_price>$account_arr['goods_amount']){
                $result['error'] = '优惠金额不能大于订单金额！';
                Util::jsonExit($result);
            }
            $data['check_user_id'] = $user_id;
            $data['check_user'] = $check_user_name;
            $data['consignee'] = $consignee;
            $data['create_user'] = $create_user;
            $data['favorable_price'] = $favorable_price;
            $data['create_time'] = date("Y-m-d H:i:s");
            $data['check_status'] = ($departmentid==2)?2:1;

            
			if($departmentid==2)
			{
				$detailsModel->setValue('favorable_price', $old_favorable_price+$favorable_price);
            	$detailsModel->setValue('favorable_status', 3);
			}else{
				//保存优惠金额
           	 	$detailsModel->setValue('favorable_price', $favorable_price);
				$detailsModel->setValue('favorable_status', 2);	
			}
            $detailsModel->save(true);
			
			if($departmentid == 2)
			{
				//把订单金额/未付  - 优惠金额
				$accountModel = new AppOrderAccountModel($account_id,28);
                $money['id'] = $account_id;
                $money['order_amount'] = $account_arr['order_amount'] - $favorable_price;//订单总金额
                $money['money_unpaid'] = $account_arr['money_unpaid'] - $favorable_price;//未付
                $money['favorable_price'] = $account_arr['favorable_price'] + $favorable_price;//商品优惠
                $accountModel->saveData($money, $account_arr);
                $result['success'] = 1;
			}else{
				//把优惠金额写入到优惠政策中
				$salePolicyModel = new ApiSalePolicyModel();
				$res = $salePolicyModel->addFavorablePrice(array('add_data'=>$data));
				if($res['error'] == 0){
					$result['success'] = 1;
				}else{
					$result['error'] = '添加失败';
				}
			}
            Util::jsonExit($result);
        }*/
		
		
		
    }

    /**
     * 取消商品申请优惠
     * @param type $param
     */
    public function off_apply_favorable($param) {
        $id = _Post::getInt('id');
        $detailModel = new AppOrderDetailsModel($id,28);
        $order_goods_id = $detailModel->getValue('id');
        $carat = $detailModel->getValue('cart');
        $goods_price = $detailModel->getValue('goods_price');
        $goods_type = $detailModel->getValue('goods_type');
        $cert_id = $detailModel->getValue('zhengshuhao');
        $order_id = $detailModel->getValue('order_id');
        $create_user = $detailModel->getValue('create_user');
        $old_favorable_price = $detailModel->getValue('favorable_price');
        $old_favorable_status = $detailModel->getValue('favorable_status');

        if($old_favorable_price==0){
                $result['error'] = "该货品未申请优惠！";
                Util::jsonExit($result);
        }

        if($goods_type == 'lz'){
            //$mima = _Request::getString('discount_mima'); favorable_price
            //$favorable_price = _Request::getFloat('favorable_price');
            $user_id = $_SESSION['userId'];
            $type = $this->getDiamondType($carat);
            $diamondModel = new DiamondListModel();

            $where = array('type'=>$type,'user_id'=>$user_id,'order_goods_id'=>$order_goods_id);

            $data = $diamondModel->getDiscountByGoods_id($where);

            if($data['error'] ==1){
                $result['error'] = $data['data'];
                Util::jsonExit($result);
            }
            //$zhekou = $data['data']['zhekou'];
            $grant_id = $data['data']['id'];

            //操作日志
            //$shop_price = $goods_price - $favorable_price;
            $log_data['order_id'] = $order_id;
            $log_data['grant_id'] = $grant_id;
            $log_data['order_detail_id']  = $id;
            $log_data['market_price'] = '';
            $log_data['shop_price'] = '';
            $log_data['cert_id'] = $cert_id;
            //修改使用状态
            $udate_data['grant_id'] = $grant_id;
            $udate_data['order_goods_id'] = '0';
            $udate_data['goods_sn'] = '';
            $udate_data['goods_price'] = '0.00';
            $udate_data['real_price'] = '0.00';
            $udate_data['cert'] = '';
            $udate_data['cert_id'] = '';
            $udate_data['use_user_id'] = '0';
            $udate_data['use_user'] = '';
            $udate_data['usetime'] = '00-00-00 00:00:00';
            $udate_data['status'] = 1;

            $log_where = array('insert_data'=>$log_data,'update_data'=>$udate_data);
            $tmp = $diamondModel->updateDiscountMimas($log_where);
            if($tmp['error']==0){
            	//修改订单金额：把此商品的原来的优惠的钱从订单中减去，重新计算订单金额：
	            //if($old_favorable_status ==3 && $old_favorable_price!= 0){//只有原来的钱是审核通过，并且，才去重新继续钱
	            	//获取此订单的金额

	            	$baseOrderModel = new BaseOrderInfoModel(27);
	            	$account_arr = $baseOrderModel->getOrderAccount($order_id);
	            	$account_id = $account_arr['id'];
	            	$accountModel = new AppOrderAccountModel($account_id,28);

	            	$money['id'] = $account_id;
	            	$money['order_amount'] = $account_arr['money_unpaid']+$old_favorable_price;//订单总金额 = 未付 + 原来优惠的钱
	            	$money['money_unpaid'] =$account_arr['money_unpaid']+$old_favorable_price;//未付 + 原来优惠的钱
	            	$money['favorable_price'] = $account_arr['favorable_price']-$old_favorable_price;//商品优惠 - 原来优惠的钱

	            	$accountModel->saveData($money, $account_arr);
	            //}


                //修改优惠金额
                $detailModel->setValue('favorable_price', 0);
                $detailModel->setValue('favorable_status', 1);
                $res = $detailModel->save(true);


                //获取此订单的金额
                $baseOrderModel = new BaseOrderInfoModel(27);
                $account_arr = $baseOrderModel->getOrderAccount($order_id);
                $account_id = $account_arr['id'];
                $accountModel = new AppOrderAccountModel($account_id,28);
                //把订单金额/未付  - 优惠金额
                $money['id'] = $account_id;
                $money['order_amount'] = $account_arr['order_amount'];//订单总金额
                $money['money_unpaid'] = $account_arr['money_unpaid'];//未付
                //$money['favorable_price'] = $account_arr['favorable_price'] + $old_favorable_price;//商品优惠
                $money['favorable_price'] = 0;//商品优惠
                $accountModel->saveData($money, $account_arr);

                Util::jsonExit(array('success'=>1,'error'=>'操作成功'));
            }else{
                $result['error'] = '操作失败';
                Util::jsonExit($result);
            }
        }else{

            //非裸钻优惠
            $favorable_price = _Post::getFloat('favorable_price');
            //$check_user = _Post::getInt('check_user');
            $check_user_name = _Post::getString('check_user_name');
			$favorable_status = $detailModel->getValue('favorable_status');
// 			if($favorable_status==2){
// 				$detailModel->setValue('favorable_status', 1);
// 				$detailModel->delete_favorable_price($order_id);
// 			}
            if(!$id){
                $result['error'] = '没有该商品';
                Util::jsonExit($result);
            }
            //if($check_user_name==''){
                //$result['error'] = '没有指定审批人';
                //Util::jsonExit($result);
            //}
            //$userModel = new UserModel(1);
            //$user_id = $userModel->getAccountId($check_user_name);
            //if(!$user_id){
                //$result['error'] = '系统中没有该用户';
                //Util::jsonExit($result);
            //}
            $detailsModel = new AppOrderDetailsModel($id,28);
            $data = $detailsModel->getOrderInfo($id);

            $detailsModel->setValue('favorable_price', 0);
            $detailsModel->setValue('favorable_status', 1);
            //$have_favorable_price = 0;
            //if($data['favorable_status'] == 3){
                //已审核通过的金额才属于优惠的
                //$have_favorable_price = $data['favorable_price'];
            //}
            //此次优惠的钱+已经优惠的钱 >此商品的金额
            //if($favorable_price + $have_favorable_price>$data['goods_price']){
                //$result['error'] = '优惠金额不能大于商品金额！';
                //Util::jsonExit($result);
            //}
            //获取此订单的金额
            $baseOrderModel = new BaseOrderInfoModel(27);
            $account_arr = $baseOrderModel->getOrderAccount($order_id);
            $account_id = $account_arr['id'];

            //修改订单金额：把此商品的原来的优惠的钱从订单中减去，重新计算订单金额：
            //if($old_favorable_status ==3 && $old_favorable_price!= 0){//只有原来的钱是审核通过，才去重新继续钱

            	$accountModel = new AppOrderAccountModel($account_id,28);

            	$money['id'] = $account_id;
            	$money['order_amount'] = $account_arr['money_unpaid'];//订单总金额 = 未付 + 原来优惠的钱
            	$money['money_unpaid'] = $account_arr['money_unpaid'];//未付 + 原来优惠的钱
            	$money['favorable_price'] = 0;//商品优惠 - 原来优惠的钱

            	$accountModel->saveData($money, $account_arr);
            //}

            //$orderModel = new BaseOrderInfoModel(27);
            //$orderPriceInfo = $orderModel->getDetailPriceInfo($order_id);

            //print_r($orderPriceInfo['favorable_price']);die;
            //if($order_id){
                //$orderinfo = $orderModel->getOrderInfoById($order_id);
                //if($orderinfo){
                    //$consignee = $orderinfo['consignee'];
                //}
            //}
            //$_favorable_price = $favorable_price + $orderPriceInfo['favorable_price'];
            //if($_favorable_price>$account_arr['goods_amount']){
                //$result['error'] = '优惠金额不能大于订单金额！';
                //Util::jsonExit($result);
            //}
            $data['check_user_id'] = '0';
            $data['check_user'] = '';
            $data['consignee'] = '';
            $data['create_user'] = '';
            $data['favorable_price'] = '0';
            $data['create_time'] = '00-00-00 00:00:00';
            $data['check_status'] = 1;

            //取消保存优惠金额
            $detailsModel->setValue('favorable_price', 0);
            $detailsModel->setValue('favorable_status', 1);
            $detailsModel->save(true);
            //取消把优惠金额写入到优惠政策中
            $salePolicyModel = new ApiSalePolicyModel();
            $res = $salePolicyModel->addFavorablePrice_chengpin(array('add_data'=>$data));
            if($res['error'] == 0){
                $result['success'] = 1;
            }else{
                $result['error'] = '取消失败';
            }
            Util::jsonExit($result);
        }
    }

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		return false;
		$result = array('success' => 0,'error' =>'');
		$goods_sn		=  _Post::get('goods_sn');
		$order_id		=  _Post::get('_id');
		$goods_name		=  _Post::get('goods_name');
		$goods_price	=  _Post::get('goods_price');
		$details_remark	=  _Post::get('details_remark');
		$date_time		= date('Y-m-d H:i:s',time());
		$olddo = array();
		$newdo=array(
			'order_id'=>$order_id,
			'goods_id'=>'123456',
			'goods_sn'=>$goods_sn,
			'goods_price'=>$goods_price,
			'details_remark'=>$details_remark,
			'create_time'=>$date_time,
			'create_user'=>$_SESSION['realName'],
			'modify_time'=>$date_time,
			'goods_name'=>$goods_name,
			'goods_count'=>1

			);

		$newmodel =  new AppOrderDetailsModel(28);
		$res = $newmodel->saveData($newdo,$olddo);
		//修改订单金额 查询货品总金额
		$all_money = $newmodel->goods_all_money($order_id);
		$order_model = new BaseOrderInfoModel($order_id,28);
		$order_model->setValue('order_price',$all_money);

		if(($res && $order_model->save())!== false)
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
		$id = _Post::getInt('id');
		//echo '<pre>';
		//print_r ($_POST);
		//echo '</pre>';
		//exit;
		$goods_sn		=  _Post::get('goods_sn');
		$goods_name		=  _Post::get('goods_name');
		$goods_price	=  _Post::get('goods_price');
		$details_remark	=  _Post::get('details_remark');
		$order_id		=  _Post::get('_id');
		$date_time		= date('Y-m-d H:i:s',time());
		$newmodel =  new AppOrderDetailsModel($id,28);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'id'=>$id,
			'goods_id'=>'123456',
			'goods_sn'=>$goods_sn,
			'goods_price'=>$goods_price,
			'details_remark'=>$details_remark,
			'modify_time'=>$date_time,
			'goods_name'=>$goods_name,
		);

		$res = $newmodel->saveData($newdo,$olddo);

		$all_money = $newmodel->goods_all_money($order_id);
		$order_model = new OrderModel($order_id,28);
		$order_model->setValue('order_price',$all_money);

		if($res  && $order_model->save()!== false)
		{
			$result['success'] = 1;
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
	public function deleteGift($params)
	{
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
        	die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }		
		$result = array('success' => 0,'error' => '');
		foreach ($params['ids'] as $id){
			$model = new AppOrderDetailsModel($id,28);
			$order_id = $model->getValue('order_id');
			$baseOrderModel = new BaseOrderInfoModel($order_id,28);

			if($model->getValue('is_zp') != 1 ){
				$result['error'] = "此商品不是赠品不可以操作！！";
				Util::jsonExit($result);
			}
			
			if($baseOrderModel->getValue('order_status')>2){
					$result['error'] = "此订单已经无效！";
					Util::jsonExit($result);
			}
			
			if($baseOrderModel->getValue('delivery_status') >= 5 ){
				$result['error'] = "此订单配货状态不符合要求！";
				Util::jsonExit($result);
			}
			if($baseOrderModel->getValue('send_good_status') != 1 ){
				$result['error'] = "此订单已发货！";
				Util::jsonExit($result);
			}
			$this->delete(array('id' => $id, 'batch' => true));
			
			//操作日志
			$ation['order_id'] = $order_id;
			$ation['order_status'] = $baseOrderModel->getValue('order_status');
			$ation['shipping_status'] = $baseOrderModel->getValue('send_good_status');//2015-12-25 zzm boss-1007
			$ation['pay_status'] = $baseOrderModel->getValue('order_pay_status');
			$ation['create_user'] = $_SESSION['userName'];
			$ation['create_time'] = date("Y-m-d H:i:s");
			$ation['remark'] = "删除赠品款号：" . $model->getValue('goods_sn') . "， 名称：" . $model->getValue('goods_name') ."，原因：" . $params['info'] ;
			$orderActionModel = new AppOrderActionModel(27);
			$res = $orderActionModel->saveData($ation, array());
		
		}
		$result['success'] = 1;
		$result['content'] = "删除赠品成功";
		Util::jsonExit($result);
				
	}
	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
            die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        } 		
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);

		//判断订单中商品数量  商品数量最少不可以小于1
		$model = new AppOrderDetailsModel($id,28);
		$order_id = $model->getValue('order_id');
        $Ddata =$model->getDataObject();
        $kuan_sn = $model->getValue('kuan_sn');
        $goods_price = $model->getValue('goods_price');
        $favorable_price = $model->getValue('favorable_price');
        $favorable_status = $model->getValue('favorable_status');//当前商品当前优惠状态：3为审核通过的
        $goods_type = $model->getValue('goods_type');//当前商品当前优惠状态：3为审核通过的
        $is_cpdz = $model->getValue('is_cpdz');
        $cpdzcode = $model->getValue('cpdzcode');
        $daijinquan_code = $model->getValue('daijinquan_code');
        $jifenma_code = $model->getValue('jifenma_code');

        //获取订单商品
        $detail_info = $model->getGoodsByOrderId(array('order_id'=>$order_id));
                
        $detail_goods_number = count($detail_info);
        $baseOrderModel = new BaseOrderInfoModel($order_id,28);
		$status = $baseOrderModel->getValue('order_status');
        $order_pay_status = $baseOrderModel->getValue('order_pay_status');
		if($status==2){
			if($detail_goods_number ==1 ){
				$result['error'] = "已审核的订单至少要有一件货品！";
				Util::jsonExit($result);
			}else if(!empty($kuan_sn) && $detail_goods_number ==2){
			    $result['error'] = "已审核的订单至少要有一件货品(天生一对至少保留两件)！";
			    Util::jsonExit($result);
			}
		}

        //如果时天生一对需要删掉一个要一对一起删除
        if(!empty($kuan_sn)){
            $res = $model->deleteByWhere(array('kuan_sn'=>$kuan_sn));
        }else{
            $res = $model->delete();            
        }
		if($res)
		{
		    //解除成品定制码
		    if(!empty($cpdzcode)){
		        $unbindData = array('order_detail_id'=>null,'use_status'=>1);
		        $model->updateCpdzCode($unbindData,"`code`='{$cpdzcode}'");
		    }
            //计算删除商品的价格和优惠
            $datail_goods_arr = array('detail_info'=>$detail_info,'kuan_sn'=>$kuan_sn,'goods_price'=>$goods_price,'favorable_price'=>$favorable_price,'goods_type'=>$goods_type,'favorable_status'=>$favorable_status);
            $del_data = $this->calculateDetailGoods($datail_goods_arr);
            //获取此订单的金额           
            $account_arr = $baseOrderModel->getOrderAccount($order_id);
             //更新订单金额
            $update_arr =  array('order_account'=>$account_arr,'del_goods_price'=>$del_data['del_goods_price'],'del_favorable_price'=>$del_data['del_favorable_price'],'goods_number'=>$del_data['goods_number']);
            $this->updateOrderAcount($update_arr,$order_id);
            //现货把货品解绑并且上架           
            if($Ddata['is_stock_goods']==1&&$Ddata['goods_id']!=''){
                $warehouseModel = new ApiWarehouseModel();
                $salepolicyM = new ApiSalePolicyModel();
                $reat = $warehouseModel->BindGoodsInfoByGoodsId(array('order_goods_id'=>$Ddata['id'],'goods_id'=>$Ddata['goods_id'],'bind_type'=>2));
                $info = array();
                $info[0]['is_sale'] = 1;
                $info[0]['is_valid'] = 2;
                $info[0]['goods_id'] =$Ddata['goods_id'];
                $salepolicyM->UpdateAppPayDetail($info);
            }

            if($model->getValue('goods_type')=='qiban'){
                $apiModel = new ApiWarehouseModel();
                $data = array('addtime'=>$model->getValue('goods_id'),'opt'=>'','order_sn'=>'','customer'=>'');
                $apiModel->updatePurchaseGoodsInfo($data);
            }

            //重新查询订单现货期货数目
            $detail_info = $model->getGoodsByOrderId(array('order_id'=>$order_id));
            $is_stock_goods_arr = array_column($detail_info,"is_stock_goods");
            $in_pay_status = array(3, 4);//已付款/财务备案/
            if($detail_goods_number==0){
                $baseOrderModel->setValue('is_xianhuo',2);//未配货
                $baseOrderModel->save();
            }else if(!in_array("0",$is_stock_goods_arr) && $status == 2 && in_array($order_pay_status, $in_pay_status)) {//订单中的商品只有现货，并且订单审核状态为【已审核】，支付状态为【财务备案/已付款】，订单为允许配货
                $baseOrderModel->setValue('is_xianhuo',1);//现货
                $baseOrderModel->setValue('delivery_status',2);//若都是现货,恢复到允许配货 zzm 2015-12-24 boos-1008
                $baseOrderModel->save();
            }else if(in_array("0",$is_stock_goods_arr) && $status == 2 && in_array($order_pay_status, $in_pay_status)){//2.订单中的商品有期货，所有期货的布产状态是已出厂/不需布产，并且订单审核状态为【已审核】，支付状态为【财务备案/已付款】，订单为允许配货
                if(!empty($detail_info)){
                    $is_pei = true;
                    $in_buchan_status = array(9, 11);//已出厂/不需布产
                    foreach ($detail_info as $detailinfo) {
                        if($detailinfo['is_stock_goods'] == 0 && !in_array($detailinfo['buchan_status'], $in_buchan_status)){
                            $is_pei = false;//不符合条件
                        }
                    }
                    //如果商品中有期货并且期货的布产状态都为已出厂和不需布产则，订单的配货状态改为允许配货
                    if($is_pei){
                        $baseOrderModel->setValue('delivery_status',2);
                        $baseOrderModel->save();
                    }
                }

            }else if(count($detail_info) == 0){//3.订单中没有商品，订单为未配货
                $baseOrderModel->setValue('delivery_status',1);
                $baseOrderModel->save();
            }

            //删除后 订单明细中对应的积分码改成未使用
            if(!empty($jifenma_code)){
                $model->update_jifenma_status($jifenma_code);
            }
            //删除后 订单明细中对应的代金券兑换码改成未使用
            if(!empty($daijinquan_code)){
                $update_daijinquan_status_data = array('used_time'=>null ,'order_sn'=>'','bespoke_sn'=>'' ,'is_used'=>0,'daijinquan_code'=>$daijinquan_code);
                Util::point_api_update_daijinquan($update_daijinquan_status_data);
            } 
            
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = "删除失败";
		}
		
		//我不管你添加的是什么 我都做一次订单赠品检查
		$upModel = new BaseOrderInfoModel(27);
		$upModel->updateorderiszp($order_id);
		if (empty($params['batch']))
			Util::jsonExit($result);
	}
    public function getDiamandInfoAjax(){
        //$sn = '2186522094';
        $sn = _Request::get('sn');
        $apiDiamond = new ApiDiamondModel();
        $result = $apiDiamond->getDiamondInfoByCertId($sn);
        Util::jsonExit($result);
    }
	
	 public function addGift($params){
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
        	die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }	 	
		$order_id = intval($params['id']);
		$gifts = $params['gifts'];
		
		foreach ($gifts as $gift){
			for ($i = 0 ; $i < $gift['num'] ; $i++){
				$gift['id'] =  $params['id'];
				$gift['batch'] = true;
				$this->saveOrderGoods($gift);
			}
		}
		$result['success'] = 1;
		$result['content'] = "添加赠品成功";
		Util::jsonExit($result);
	 }
	
     /*
     * 保存购物车中数据
     *
     */
    public function saveOrderGoods($params){
        $order_id = intval($params['id']);
        $goods_type = isset($params['goods_type'])?$params['goods_type']:'';
        $goods_id = isset($params['goods_id'])?$params['goods_id']:'0';
        $edits =  isset($params['edits']) ? $params['edits'] : '';
		$policy_id = isset($params['policy_id'])?$params['policy_id']:'0';
        $edits = !empty($edits)?json_decode(base64_decode($edits)):array();
        $original_point = []; //原始积分
        $discount_point = []; //折扣积分
        $reward_point = []; //奖励积分

        //$result['error'] = var_export($edits,true);
        //Util::jsonExit($result);
        if($goods_type == 'luozuan'){
            $goods_type = 'lz';
        }

        if(empty($order_id)){
            $result['error'] = "数据有误！";
            Util::jsonExit($result);
        }

        $orderModel= new BaseOrderInfoModel(27);
        $detailModel = new AppOrderDetailsModel(27);

        //判断订单是否有效
        $order_info = $orderModel->getOrderInfoById($order_id);
        if(empty($order_info)){
            $result['error'] = "此订单的数据不存在！";
            Util::jsonExit($result);
        }
        if($order_info['is_delete']==1){
            $result['error'] = "此订单已删除！";
            Util::jsonExit($result);
        }
        if($order_info['order_status']!=1){
			if ( $goods_type != 'zengpin_goods') {
				$result['error'] = "此订单状态已经审核或取消或关闭！";
				Util::jsonExit($result);
			}else if ($order_info['order_status'] > 2){
				$result['error'] = "此订单已经无效！";
				Util::jsonExit($result);
			}
        }
        if($order_info['order_pay_status']!=1 && $goods_type != 'zengpin_goods' ){
            $result['error'] = "此订单已支付！";
            Util::jsonExit($result);
        }
		
		if($order_info['delivery_status'] >= 5 ){
            $result['error'] = "此订单配货状态不符合要求！";
            Util::jsonExit($result);
        }
		
		if($order_info['send_good_status'] != 1 ){
            $result['error'] = "此订单已发货！";
            Util::jsonExit($result);
        }

       $Company_staff_name = $this->getTydTypeByDep($order_info['department_id']);

        if($goods_type == 'zengpin_goods'){
            $style_sn = $params['goods_sn'];
            $apistyleModel = new ApiStyleModel();
            $style_info = $apistyleModel->GetStyleInfo($style_sn);
        }

        if($goods_type == 'caizuan_goods'){
            $goods_id = !empty($params['goods_sn']) ? $params['goods_sn']:_Post::get('zhengshuhao');
            $where_goods = array('goods_id'=>$goods_id,'order_id'=>$order_id);
            $data_goods = $detailModel->checkOrderGoodsOnly($where_goods);
            if($data_goods){
                 $result['error'] = '您购物车中的商品:'.$goods_id.'，在此订单中，都已经存在！';
                 Util::jsonExit($result);
            }

            $dmodel = new DiamondListModel();
            $data = $dmodel->getCaiZuaninfo($goods_id);
            if($data['data'] =='under'){
                $result['error'] = "您搜索的商品已经下架!";
                Util::jsonExit($result);
            }        
            if($data['data'] == '未查询到此彩钻'){
                $result['error'] = "未查到此彩钻信息！";
                Util::jsonExit($result);
            }
            $caizuan_info = $data['data'];
            //价格保护
            if(empty(floatval($caizuan_info['price']))){
                $result['error'] = "彩钻价格不能为空！";
                Util::jsonExit($result);
            }
            //证书号唯一判断
            if(empty($caizuan_info['cert_id'])){
                $result['error'] = "彩钻证书号不能为空！";
                Util::jsonExit($result);
            }
        }

        if($goods_type == 'qiban'){
            //起版下单
            $apiModel = new ApiWarehouseModel();
            $qibanInfo = $apiModel->getPurchaseGoodsInfo(array('addtime'=>$goods_id));
            if($qibanInfo['error']==1){
                $result['error'] = 1;
                $result['content'] = $qibanInfo['data'];
                Util::jsonExit($result);
            }
        }
        
        
        if($goods_type != 'zengpin_goods'){
            if(empty($goods_id)){
                $result['error'] = "货号不能为空！";
                Util::jsonExit($result);
            }
        }
        $channel = $order_info['department_id'];
        $goods_sn='';
        $goods_name='';
        $cart = '0';
        $zhushi_num = '0';
        $clarity= '';
        $color= '';
        $xiangkou='';
        $zhengshuhao= '';
        $cert = '';
        $caizhi= '';
        $jinse= '';
        $jinzhong= '';
        $zhiquan= '';
        $kezi= '';
        $face_work= '';
        $xiangqian= '';
        $cut = '';
        $chengjiaojia = 0;
        $is_xianhuo = 1;
        $goods_count =1;
		$info='';
        $details_remark = _Request::get('info');
        $new_goods_type='';
        $tuo_type = '成品';
        $cpdzcode = "";
        //var_dump($goods_type); exit;
        if($goods_type == 'style_goods' || $goods_type == 'warehouse_goods'){
            $goods_sn = _Post::get('goods_sn');
            $goods_name = _Post::get('goods_name');
            $chengjiaojia = _Post::getFloat('chengjiaojia');
            $cart = _Post::get('cart');
            $zhushi_num = _Post::get('zhushi_num');
            $xiangkou = _Post::get('xiangkou');
            $clarity = _Post::get('clarity');
            $color = _Post::get('color');
            $zhengshuhao = _Post::get('zhengshuhao');
            $caizhi = _Post::get('caizhi');
            $jinse = _Post::get('jinse');
            $jinzhong = _Post::get('jinzhong');
            $zhiquan = _Post::get('zhiquan');
            $kezi = _Post::get('kezi');
            $face_work = _Post::get('face_work');
            $xiangqian = _Post::get('xiangqian');
            $info = _Post::get('info');
            $tuo_type = _Post::get('tuo_type');
            $cert = _Post::get('cert');
            $shape = _Post::getString('shape');
            $price_key = _Post::getString('price_key');//成品定制专用 当前价格标识
            $goods_key = _Post::getString('goods_key');//成品订单专用 货品唯一标识
            $cpdzcode = _Post::getString('cpdzcode');
            //特殊字段进行数据安全校验
            $checkData = array(
                'zhengshuhao'=>$zhengshuhao,
                'cert'=>$cert,
                'zhiquan'=>$zhiquan,
                'jinzhong'=>$jinzhong,
                'xiangqian'=>$xiangqian
            ); 
            //期货
            if(strpos($goods_id,'-')!==false){
                $checkData['cart'] = $cart;
                $checkData['zhushi_num'] = $zhushi_num;
                $checkData['xiangkou'] = $xiangkou;
                $checkData['is_stock_goods'] = 0;
            }else{
                $checkData['is_stock_goods'] = 1;
            }




            $res = $this->checkOrderGoodsData($checkData);
            if($res['success']==0){
                $result['error'] = $res['error'];
                Util::jsonExit($result);
            }
            if($kezi){
                $kezi = $this->checkKeziStr($goods_sn,$kezi);
            }
			$s_where['goods_id'] = $goods_id;
			$s_where['channel'] = $channel;
			$s_where['policy_id'] = $params['policy_id'];

			if(strpos($goods_id,'-')!==false)
			{
				$appSalepolicyGoodsModelR =new AppSalepolicyGoodsModel(15);
				if($tuo_type=="成品"){
				    $xiangqian = "工厂配钻，工厂镶嵌";
				    if($xiangkou>0 && (empty($color) || empty($clarity) || empty($cart))){
				        $result['error'] = "镶口大于0时，颜色，净度，主石单颗重均不能为空！";
				        Util::jsonExit($result);
				    }
				    //成品定制码为【空】，必须走成品定制价格 
				    if(empty($cpdzcode)){
				        //tuo_type，goods_key，clarity，color，cert 成品定制固有查询参数
    				    $s_where['tuo_type'] = 1;
    				    $s_where['goods_key'] = $goods_key;
    				    $s_where['clarity'] = $clarity;
    				    $s_where['color'] = $color;
    				    $s_where['cert'] = $cert?$cert:'无';   
    				    $s_where['carat'] = $cart;
				    }
                    unset($s_where['policy_id']);
				}else{
				    if($xiangqian=="工厂配钻，工厂镶嵌"){
				        $result['error'] = "当金托类型为空托时，镶嵌方式不能选【工厂配钻，工厂镶嵌】！";
				        Util::jsonExit($result);
				    }				    
				}   
				//空托主石单颗重 主石粒数全部重置成 0
				//$cart = 0;//主石单颗重
				//$zhushi_num = 0;//主石粒数
				//说明是期货
				//if(empty($cpdzcode)){
	                if($tuo_type=='成品'){
				        //tuo_type，goods_key，clarity，color，cert 成品定制固有查询参数
    				    $s_where['tuo_type'] = 1;
    				    $s_where['goods_key'] = $goods_key;
    				    $s_where['clarity'] = $clarity;
    				    $s_where['color'] = $color;
    				    $s_where['cert'] = $cert?$cert:'无';   
    				    $s_where['carat'] = $cart;		                
	                    $sdata = $appSalepolicyGoodsModelR->getChenpingdingzhiList($s_where);
	                }else
	                    $sdata = $appSalepolicyGoodsModelR->pageQihuoList($s_where,1,1);
	                if(!empty($sdata['error'])){
	                    $result['error'] = $sdata['error']."  如有任何疑问请联系".$Company_staff_name;
	                    Util::jsonExit($result);
	                }
					if(empty($sdata['data'][0]))
					{
						$result['error'] = "您输入的虚拟货号不存在呢，如有任何疑问请联系".$Company_staff_name;
						Util::jsonExit($result);
					}elseif(empty($sdata['data'][0]['sprice'])){
						$result['error'] = "您输入的货品没有找到销售政策，如有任何疑问请联系".$Company_staff_name;
						Util::jsonExit($result);	
					}	
				//}
				/* if(!empty($cpdzcode)){
				    $cpdzCodePriceInfo = $detailModel->getBaseCpdzCodeInfo($cpdzcode);
				    if(empty($cpdzCodePriceInfo)){
				        $result['error'] = "成品定制码{$cpdzcode}不存在!";
				        Util::jsonExit($result);
				    }else if($cpdzCodePriceInfo['use_status']<>1){
				        $result['error'] = "成品定制码{$cpdzcode}已被使用!";
				        Util::jsonExit($result);
				    }
				    //$result['error'] = var_export($sdata,true);
				    //Util::jsonExit($result);
				    //将成品定制码价格   存入查询结果集内
				    $sdata['data'][0]['sprice'][0]['sale_price'] = $cpdzCodePriceInfo['price'];
				} */
				
			}else{
				
				$goodsAttrModel = new GoodsAttributeModel(17);
				$caizhiarr = $goodsAttrModel->getCaizhiList();
				$yansearr  = $goodsAttrModel->getJinseList();
				$appSalepolicyGoodsModelR =new AppSalepolicyGoodsModel(15);
				//经销商的需要增加公司的过滤
				if( SYS_SCOPE == 'zhanting' )
				{
					$is_company_check = Auth::user_is_from_base_company();
					if(!$is_company_check){
						$s_where['company_id_list'] = $_SESSION['companyId'];
					}
				}
				$sdata = $appSalepolicyGoodsModelR->pageXianhuoList($s_where,1,1,$caizhiarr,$yansearr,true);
				if(isset($sdata['error']) && $sdata['error']== 1 )
				{
					$str = $sdata['content'];
					$result['error'] = $str."\n如有任何疑问请联系".$Company_staff_name;
					Util::jsonExit($result);
				}
				if(empty($sdata['data'][0]['sprice']))
				{
					$str = "您输入的货品没有找到销售政策";
					$result['error'] = $str."\n如有任何疑问请联系".$Company_staff_name;
					Util::jsonExit($result);
				}
				//把产品线和款式分类转换为id
				if(is_numeric($sdata['data'][0]['product_type'])){
					$product_type = $sdata['data'][0]['product_type'];	
				}else{
					$product_type = $appSalepolicyGoodsModelR->getproductid($sdata['data'][0]['product_type']);	
				}
				if(is_numeric($sdata['data'][0]['category'])){
					$cat_type = $sdata['data'][0]['category'];	
				}else{
					$cat_type = $appSalepolicyGoodsModelR->getcatid($sdata['data'][0]['category']);	
				}
				$tuo_type = $sdata['data'][0]['tuo_type'];				
				$tuo_type = $this->dd->getEnum('warehouse_goods.tuo_type',$tuo_type);
			}
			if(!empty($cpdzcode)){
			    $cpdzCodePriceInfo = $detailModel->getBaseCpdzCodeInfo($cpdzcode);
			    if(empty($cpdzCodePriceInfo)){
			        $result['error'] = "成品定制码{$cpdzcode}不存在!";
			        Util::jsonExit($result);
			    }else if($cpdzCodePriceInfo['use_status']<>1){
			        $result['error'] = "成品定制码{$cpdzcode}已被使用!";
			        Util::jsonExit($result);
			    }
			    //$result['error'] = var_export($sdata,true);
			    //Util::jsonExit($result);
			    //将成品定制码价格   存入查询结果集内
			    $sdata['data'][0]['sprice'][0]['sale_price'] = $cpdzCodePriceInfo['price'];
			}
			$tmpdata = array();
			$hpobj = $sdata['data'][0]; //一个货号只会出现一行
			$saleprice = $hpobj['sprice'];
			$allpolicyids = $hpobj['policy_id_split'];
			$gid = $hpobj['goods_id'];
			
			$tmpdata['data'] = array();
			$tmpdata['data']['sprice'][$gid] = array();
			foreach($saleprice as $k=>$priceobj)
			{
				$tmpd = array();
				$tmpd = array(
					'isXianhuo' => $hpobj['isXianhuo'],
					'sale_price' => $priceobj['sale_price'],
					'goods_sn' => $hpobj['goods_sn'],
					'goods_name' => $hpobj['goods_name'],
					'category' => $hpobj['category'],
					'product_type' => $hpobj['product_type'],
					'policy_id' => $allpolicyids[$k],
					'goods_id' => $hpobj['goods_id']
				);
				array_push($tmpdata['data'],$tmpd);
				$tmpdata['data']['sprice'][$gid][$allpolicyids[$k]]=$priceobj;
			}
			$tmpdata['error']=0;
			$data = $tmpdata;
			//组装数据完成
			/*接着走下面的流程*/
            $is_xianhuo = $data['data'][0]['isXianhuo'];
            $chengjiaojia = $data['data'][0]['sale_price'];
			if($is_xianhuo<1)
			{
				$product_type = $data['data'][0]['product_type'];
				$cat_type =  $data['data'][0]['category'];
			}
            //货品下单
            $apiWarehouseModel = new ApiWarehouseModel();
            //现货 1
            if($is_xianhuo ==1){
                $new_goods_type = 1;//现货
            }  else {
                $new_goods_type = 2;//期货
            }

            //判断此订单中的现货只能添加一个，裸钻期货也只能添加一个
            if($is_xianhuo == 1){
                $where_goods = array('goods_id'=>$goods_id,'order_id'=>$order_id);
                $data_goods = $detailModel->checkOrderGoodsOnly($where_goods);
                if($data_goods){
                     $result['error'] = '您购物车中的商品:'.$goods_id.'，在此订单中，都已经存在！';
                     Util::jsonExit($result);
                }
            }
            //期货成品 校验 取价值
            if($is_xianhuo == 0 && $tuo_type=="成品" && empty($cpdzcode)) {
                //成品定制价格 以 post的值为准，然后再根据价格验证码验证数据安全
                $chengjiaojia = $params['chengjiaojia'];
                $new_price_key = md5($xiangkou.'&'.$clarity.'&'.$color.'&'.$shape.'&'.$cert);
                if($price_key <> $new_price_key){
                    $result['error'] = '请点击取价，获取成品定制价格！';
                    Util::jsonExit($result);
                }
            }
            //积分
            /*
            if($new_goods_type ==1){ //现货
                if(($xiangqian == '工厂配钻，工厂镶嵌' || $xiangqian == '成品') && !empty($cart)){
                    $original_point[] = $chengjiaojia;
                    $discount_point[] = $chengjiaojia;
                    $reward_point[] = $this->getRewardPoint($crm_reward_point,$cert,$goods_sn,$cart,$discount_point[0]);
                }
            } else { //期货
                if($tuo_type == '成品' && !empty($cart) ){
                    $original_point[] = $chengjiaojia;
                    $discount_point[] = $chengjiaojia;
                    $reward_point[] = $this->getRewardPoint($crm_reward_point,$cert,$goods_sn,$cart,$discount_point[0]);
                }
            }*/
        }elseif($goods_type == 'lz'){
            //裸钻下单
            $new_goods_type = 3;
            //裸钻只能下一个
            $where_goods = array('goods_id'=>$goods_id,'order_id'=>$order_id);
            $data_goods = $detailModel->checkOrderGoodsOnly($where_goods);
            if($data_goods){
                 $result['error'] = '您购物车中的商品:'.$goods_id.'，在此订单中，都已经存在！';
                 Util::jsonExit($result);
            }
            $xiangqian = _Post::get('xiangqian');
            if(empty($xiangqian)){
                $result['error'] = "镶嵌方式必须填写";
                Util::jsonExit($result);
            }
        }elseif($goods_type == 'qiban'){
            //起版下单
            $new_goods_type = 4;
            $goods_type ='qiban' ;
            //裸钻只能下一个
            $where_goods = array('goods_id'=>$goods_id,'order_id'=>$order_id);
            $data_goods = $detailModel->checkOrderGoodsOnly($where_goods);
            if($data_goods){
                 $result['error'] = '您购物车中的商品:'.$goods_id.'，在此订单中，都已经存在！';
                 Util::jsonExit($result);
            }
        }elseif($goods_type == 'caizuan_goods'){
            $new_goods_type = 5;//期货
            $caizuan_info;
            $goods_sn = 'CAIZUAN';
            $goods_id = $params['goods_sn'];
            $goods_name = '彩钻';
            $chengjiaojia = $params['chengjiaojia'];
            $cart = floatval($params['cart']);
            $zhushi_num = 1;
            $xiangkou = floatval($params['xiangkou']);
            $clarity = $params['clarity'];
            $color = $params['color'];
            $zhengshuhao = $params['zhengshuhao'];
            $caizhi = $params['caizhi'];
            $jinse = $params['jinse'];
            $jinzhong = floatval($params['jinzhong']);
            $zhiquan = $params['zhiquan'];
            $kezi = $params['kezi'];
            $face_work = $params['face_work'];
            $xiangqian = $params['xiangqian'];
            $info = $params['info'];
            $cert = $caizuan_info['cert'];
            $is_xianhuo=0;
            $cat_type=0;
            $product_type=0;
        }elseif($goods_type == 'zengpin_goods'){
            $goods_sn = _Request::getString('goods_sn');
            $goods_name = _Request::getString('goods_name');
            $chengjiaojia = _Request::getFloat('chengjiaojia');
            //$original_point[] = $chengjiaojia;
            //$discount_point[] = $original_point[0];
            //$reward_point[] = $this->getRewardPoint($crm_reward_point,null,$goods_sn,0,$discount_point[0]);
            //$cart = _Request::getFloat('cart');
            //$xiangkou = _Request::getFloat('xiangkou');
            //$clarity = _Request::getFloat('clarity');
            //$color = $params['color'];
            //$zhengshuhao = $params['zhengshuhao'];
            //$caizhi = $params['caizhi'];
            //$jinse = $params['jinse'];
            //$jinzhong = floatval($params['jinzhong']);
            $zhiquan = _Request::getFloat('zhiquan');
            //$kezi = $params['kezi'];
            //$face_work = $params['face_work'];
            //$xiangqian = $params['xiangqian'];
            $info = _Request::getString('info');
            $is_huangou = _Request::getInt('is_huangou');            
            if($is_huangou == 1){
                $hg_channel = _Request::getInt("hg_channel");
                if($hg_channel != $channel){
                    $result['error'] = "当前换购商品的销售渠道与订单的销售渠道不一致！";
                    Util::jsonExit($result);
                }
                $chengjiaojia_min = _Request::getFloat("chengjiaojia_min");
                $goods_price = _Request::getFloat("goods_price");
                $cpdzcode = "JJHG";
                if($chengjiaojia<$chengjiaojia_min){
                    $result['error'] = "成交价不得低于换购价！";
                    Util::jsonExit($result);
                }
            }
            //if($kezi){
                //$kezi = $this->checkKeziStr($goods_sn,$kezi);
            //}

            //货品下单
            $apiWarehouseModel = new ApiWarehouseModel();
            //现货 1
            if($is_xianhuo ==1){
                $new_goods_type = 1;//现货
            }  else {
                $new_goods_type = 2;//期货
            }
        }

        if(in_array("face_work",$edits) && empty($face_work)){
            $result['error'] = "请设置表面工艺";
            Util::jsonExit($result);
        }     
        if(in_array("xiangqian",$edits)){
            if(empty($xiangqian)){
               $result['error'] = "请选择镶嵌要求";
               Util::jsonExit($result);
            }elseif($goods_type !='lz' && !preg_match("/成品|不需工厂/is",$xiangqian)){
               if($xiangkou>0 && (empty($cart)||empty($clarity)||empty($color)||empty($zhushi_num))) {
                   $result['error'] = "选择工厂镶嵌时：主石单颗重、主石粒数、主石净度、主石颜色都不能为空！";
                   Util::jsonExit($result);
               }else if(preg_match("/4C/is",$xiangqian)){
                    if (empty($zhengshuhao)) {
                        $result['error'] = "选择镶嵌4C裸钻时，证书号不 能为空！";
                        Util::jsonExit($result);
                    }
                    //如果 镶嵌方式为 [镶嵌4C裸钻]且为定制空托，则 is_peishi=2;
                    $goods_detail[0]['is_peishi'] = 2;
                    
                    //验证4C空托配石的证书号必须在裸钻库存在（暂不区分是否下架）
                    $apiDiamondModel = new ApiDiamondModel();
                    $ret = $apiDiamondModel->getDiamondInfoByCertId($zhengshuhao);
                    if($ret['error']==1){
                        $result['error'] = "证书号不存在！仅针对镶嵌方式选择【镶嵌4C裸钻】";
                        Util::jsonExit($result);
                    }
                
               }


                //当镶嵌方式选择【需工厂镶嵌、客户先看钻再返厂镶嵌】时，证书号必填
                //选择【镶嵌4C裸钻、工厂配钻工厂镶嵌】，证书号字段不能填
               if(in_array($xiangqian,['需工厂镶嵌','客户先看钻再返厂镶嵌']) && empty($zhengshuhao)){
                   $result['error'] = "需工厂镶嵌、客户先看钻再返厂镶嵌，证书号必填";
                   Util::jsonExit($result);
               }else if(in_array($xiangqian,['工厂配钻，工厂镶嵌']) && !empty($zhengshuhao)){
                   $result['error'] = "工厂配钻，工厂镶嵌，证书号不能填";
                   Util::jsonExit($result);
               }

            }
            
        }

        if(!empty($xiangkou)){
            //镶口不为空，石重可编辑时，验证镶口和石重是否匹配
            if(in_array('cart',$edits) && !$this->GetStone((float)$xiangkou,(float)$cart)){
                $result['error'] = "镶口和石重不匹配";
                Util::jsonExit($result);
            }
        }
        //如果证书号可编辑，验证证书号必须是整数
        $zhengshuhao = trim($zhengshuhao);
        /*if(in_array('zhengshuhao',$edits) && $zhengshuhao !=''){
            if(!is_numeric($zhengshuhao)){            
                $result['error'] ='证书号必须为数字,请不要附带文字描述或证书类型GIA一类的字符';
                Util::jsonExit($result);
            }
        }*/
        //验证指圈 begin
        if(in_array('zhiquan',$edits) && $goods_sn !=''){
            $apiStyle = new ApiStyleModel();
            $ret = $apiStyle->GetStyleAttribute(array('style_sn'=>$goods_sn));
            $attr = $ret['error']==1?array():$ret['data'];
            
            $zhiquan_old = '';
            $goods_id_arr = explode('-',$goods_id);            
            if(count($goods_id_arr)==5){
                $zhiquan_old = isset($goods_id_arr[4])?$goods_id_arr[4]:$zhiquan_old;
            }else{
                $apiWarehouseModel = new ApiWarehouseModel();
                $house_goods = $apiWarehouseModel->getWarehouseGoodsInfo(array('goods_id'=>$goods_id));
                $house_goods = $house_goods['error']==1?array():$house_goods['data']['data'];
                if(!empty($house_goods)){
                    $zhiquan_old = $house_goods['shoucun'];
                }
            }
            
            if(!empty($zhiquan_old) ){
                if(count($goods_id_arr)==5){//期货
                    if(abs($zhiquan_old-$zhiquan)>0.5){
                        $result['error'] = "指圈大小不合要求，参考范围:".($zhiquan_old-0.5).'-'.($zhiquan_old+0.5);
                        Util::jsonExit($result);
                    }
                }else if(!empty($attr[31]) && $attr[31]['value'] != ""){
                        $str = $attr[31]['value'];
                        if(preg_match('/可增([0-9]+?)个手寸/is',$str,$arr)){
                            if($zhiquan-$zhiquan_old>$arr[1] || $zhiquan-$zhiquan_old<0){
                                $result['error'] = "款式库中已设置：指圈只可以增加".$arr[1].'（温馨提示原始指圈为'.$zhiquan_old.')';
                                Util::jsonExit($result);
                            }
                        }else if(preg_match('/可增减([0-9]+?)个手寸/is',$str,$arr)){
                            if(abs($zhiquan_old-$zhiquan)>$arr[1]){
                                $result['error'] = "款式库中已设置：指圈只可以增减".$arr[1].'（温馨提示原始指圈为'.$zhiquan_old.')';
                                Util::jsonExit($result);
                            }
                        }else if(preg_match('/不可改圈/is',$str)){
                            $result['error'] = "款式库中已设置：".$str;
                            Util::jsonExit($result);
                        }
                }else{
    
                        if(abs($zhiquan_old-$zhiquan)>2){
                            $result['error'] = "指圈大小不合要求，参考范围:".($zhiquan_old-2).'-'.($zhiquan_old+2);
                            Util::jsonExit($result);
                        }
          
                }
            }
        
        }//验证指圈 end
        $goods_from = 0;//订单最好单独一列标记是门店现货还是非门店还是总公司现货方便后期数据分析用
        if($goods_type != 'zengpin_goods'){
            switch ($new_goods_type){
                //货品数据
                case 1:
                    $goods_info = $apiWarehouseModel->getWarehouseGoodsInfo(array('goods_id'=>$goods_id));

                    $DiamondModel = new DiamondListModel(19);
					$xiangkou = $DiamondModel->getXiangKou($goods_info['data']['data']['zuanshidaxiao']);
					$goods_info['data']['data']['xiangkou']=$xiangkou;
                    if($goods_info['error']==1 || empty($goods_info['data'])){
                        $result['error'] = "没有库存数据";
                        Util::jsonExit($result);
                    }

                    break;
				//款式数据
                case 2:
                     $style_sn = $data['data'][0]['goods_sn'];
                     $styleModel = new ApiStyleModel();
                     $data_info = $styleModel->GetStyleAttribute(array('style_sn'=>$style_sn));

                     //再所有属性中获取需要的
                     if($data_info['error']==1){
                        $result['error'] = $data_info['data'];
                        Util::jsonExit($result);
                    }
                    break;

                //裸钻数据
                case 3:
                    $DiamondModel = new DiamondListModel(19);
                    $goods_arr = $DiamondModel->getRowByGoodSnOrCertId($goods_id);
                    if($goods_arr['error']==1){
                    	$result['content'] = isset($goods_sn_arr['content'])  ? $goods_sn_arr['content'] : "未查询到此裸钻";
                        Util::jsonExit($result);
                     }
                     $goods_arr['data']['0']['xiangkou']=0;
                     $this->calc_dia_channel_price($goods_arr['data']);
                     //双十一特价钻，价格查询 begin
                     if(count($goods_arr['data'])==1){
                         $selfDiamondModel     = new SelfDiamondModel(19);
                         $ssyRow = $selfDiamondModel->selectDiamondSSY('*',"goods_id='{$goods_id}' or cert_id='{$goods_id}'",2);
                         if(!empty($ssyRow)){
                             $goods_arr['data']['0']['shop_price'] = $ssyRow['special_price'];
                         }
                     }//双十一特价钻，价格查询 
                     $lz_arr = array();
                     //因为裸钻有天生一对，所以添加时可以添加2个
                     foreach($goods_arr['data'] as $key=>$val){
                        $lz_arr[$key]['goods_id'] = $val['goods_sn'];
                        $lz_arr[$key]['goods_sn'] = 'DIA';
                        $lz_arr[$key]['ext_goods_sn'] = $val['goods_sn'];
                        $lz_arr[$key]['goods_name'] = $val["carat"]."克拉/ct ".$val["clarity"]."净度 ".$val["color"]."颜色 ".$val["cut"]."切工";
                        $lz_arr[$key]['goods_price'] = $val['shop_price'];
                        $lz_arr[$key]['cart'] = $val['carat'];
                        $lz_arr[$key]['zhushi_num'] = 1;
                        $lz_arr[$key]['goods_price'] = $val['shop_price'];
                        $lz_arr[$key]['clarity'] = $val['clarity'];
                        $lz_arr[$key]['color'] = $val['color'];
                        $lz_arr[$key]['cut'] = $val['cut'];
                        $lz_arr[$key]['goods_count'] = $val['goods_number'];
                        $lz_arr[$key]['zhengshuhao'] = $val['cert_id'];
                        $lz_arr[$key]['cert'] = $val['cert'];
                        $lz_arr[$key]['kuan_sn'] = $val['kuan_sn'];
                        $lz_arr[$key]['xiangkou'] = 0;
                         $is_xianhuo =  $val['good_type'];
                        if($is_xianhuo != 1){
                            $is_xianhuo = 0;
                        }
                        $stockarrcheck=$orderModel->getGoodsStockCheck($lz_arr[$key]['goods_id']);
                        if($stockarrcheck){
                            $result['error'] = "亲~ 此裸钻已经绑定订单";
                            Util::jsonExit($result);
                        }
                        //根据货品是否在本公司库存判断是否现货
                        //新-》裸钻：总公司和下单门店和其他门店的现货都是现货单，其他情况的都是期货单
                        //（订单最好单独一列标记是门店现货还是非门店还是总公司现货方便后期数据分析用）
                        if($is_xianhuo==1){
                            if(!empty($order_info['company_id'])){
                                $stockarr=$orderModel->getGoodsStockBygoodsid($lz_arr[$key]['goods_id']);
                                if(empty($stockarr)){
                                    $is_xianhuo=0;
                                }else{
                                    if($order_info['company_id']==$stockarr['company_id']){
                                        //下单门店现货
                                        $goods_from = 1;
                                    }elseif($stockarr['company_id']==58){
                                        //总公司现货
                                        $goods_from = 2;
                                    }else{
                                        //其他门店现货
                                        $goods_from = 3;
                                    }
                                }
                                    //$is_xianhuo=0;    
                            }else{
                                $is_xianhuo=0;
                            }
                        }
                        $lz_arr[$key]['is_xianhuo'] =  $is_xianhuo;
                        $lz_arr[$key]['goods_from'] =  $goods_from;

                        //$lz_arr[$key]['original_point'] = $val['shop_price'];
                        //$lz_arr[$key]['discount_point'] = $lz_arr[$key]['original_point'];
                        //裸钻的只需要计算证书类型的规则
                        //$lz_arr[$key]['reward_point']   = $this->getRewardPoint($crm_reward_point,$val['cert'],'', $lz_arr[$key]['cart'],$lz_arr[$key]['discount_point']);
                     }
                     break;
                 //起版
                case 4:
                    $goods_id = $qibanInfo['data']['addtime'];
                    $goods_sn = $qibanInfo['data']['kuanhao'];
                    $goods_name = "起版";
                    $chengjiaojia = $qibanInfo['data']['price'];
                    $color = $qibanInfo['data']['yanse'];
                    $zhengshuhao = $qibanInfo['data']['zhengshu'];
                    $xiangkou = $qibanInfo['data']['xiangkou'];
                    $cart = $qibanInfo['data']['specifi'];
                    $caizhi = isset($qibanInfo['data']['jinliao'])?$this->dd->getEnum('purchase.qiban_jinliao',$qibanInfo['data']['jinliao']):"";
                    $jinse = isset($qibanInfo['data']['jinse'])?$this->dd->getEnum('purchase.qiban_jinse',$qibanInfo['data']['jinse']):"";
                    $face_work = isset($qibanInfo['data']['gongyi'])?$this->dd->getEnum('purchase.qiban_gongyi',$qibanInfo['data']['gongyi']):"";
                    $details_remark = $qibanInfo['data']['info'];
                    $xiangqian = isset($qibanInfo['data']['xuqiu'])?$this->dd->getEnum('purchase.qiban_xuqiu',$qibanInfo['data']['xuqiu']):"";
                    $zhiquan = isset($qibanInfo['data']['shoucun'])?$qibanInfo['data']['shoucun']:0;
                    $cat_type = 0;
                    $product_type = 0;
                    $clarity = $qibanInfo['data']['jingdu'];;
                    $zhushi_num = $qibanInfo['data']['zhushi_num'];
                    $cert = $qibanInfo['data']['cert'];
                    $tuo_type = '';
                    //起版积分
                    //$original_point[] = $chengjiaojia;
                    //$discount_point[] = $original_point[0];
                    //$reward_point[] = $this->getRewardPoint($crm_reward_point,$cert,'',$cart,$discount_point[0]);
                    break;
                //彩钻
                case 5:
                    $xiangqin = '不需工厂镶嵌';
                    //彩钻积分
                    //$original_point[] = $chengjiaojia;
                    //$discount_point[] = $original_point[0];
                    //$reward_point[] = $this->getRewardPoint($crm_reward_point,$cert,'',$cart,$discount_point[0]);
                    break;
                }
            }

        //订单信息
        if($new_goods_type == 3){
            $xiangqian = $params['xiangqian'];
			$is_peishi = intval($params['is_peishi']);
            foreach ($lz_arr as $key=>$val){
                $goods_sn = $val['goods_id'];
                $orderModel->diaSoldOut($goods_sn);
                $lz_arr[$key]['create_time'] = date("Y-m-d H:i:s");
                $lz_arr[$key]['create_user'] = $_SESSION['userName'];
                $lz_arr[$key]['modify_time'] = date("Y-m-d H:i:s");
                $lz_arr[$key]['details_status'] = 1;
                $lz_arr[$key]['is_stock_goods'] = $val['is_xianhuo'];
                $lz_arr[$key]['details_remark'] = $details_remark;
                $lz_arr[$key]['goods_type'] =$goods_type;
                $lz_arr[$key]['caizhi'] = '';
                $lz_arr[$key]['jinse'] = '';
                $lz_arr[$key]['jinzhong'] = 0;
                $lz_arr[$key]['zhiquan'] = 0;
                $lz_arr[$key]['kezi'] = '';
                $lz_arr[$key]['face_work'] = '';
                $lz_arr[$key]['xiangqian'] = '';
                $lz_arr[$key]['cat_type'] = 0;
                $lz_arr[$key]['product_type'] = 0;
				$lz_arr[$key]['favorable_price'] = 0;
				$lz_arr[$key]['favorable_status'] = 1;
				$lz_arr[$key]['xiangqian'] =$xiangqian;
				$lz_arr[$key]['is_peishi'] =$is_peishi;
				$lz_arr[$key]['is_cpdz'] = 0;
                $lz_arr[$key]['goods_from'] = $val['goods_from'];
            }
            $goods_detail = $lz_arr;
        }else{
            $goods_detail[0]['goods_id']=$goods_id;
            $goods_detail[0]['goods_sn']=$goods_sn;
            $goods_detail[0]['ext_goods_sn']=$goods_id;
            $goods_detail[0]['goods_name']=$goods_name;
            $goods_detail[0]['goods_price']=$chengjiaojia;
            $goods_detail[0]['goods_count']=$goods_count;
            $goods_detail[0]['create_time'] = date("Y-m-d H:i:s");
            $goods_detail[0]['create_user'] = $_SESSION['userName'];
            $goods_detail[0]['modify_time'] = date("Y-m-d H:i:s");
            $goods_detail[0]['details_status'] = 1;
            $goods_detail[0]['is_stock_goods'] = $is_xianhuo;
            $goods_detail[0]['policy_id']=$policy_id;
            //$goods_detail[0]['original_point'] = $original_point[0];//原始积分
            //$goods_detail[0]['discount_point'] = $discount_point[0];//折扣积分
            //$goods_detail[0]['reward_point'] = $reward_point[0]; // 奖励积分
            if($new_goods_type==4){
                $goods_detail[0]['is_stock_goods'] = 0;
            }
            $goods_detail[0]['details_remark'] = $details_remark;
            $goods_detail[0]['goods_type'] =$goods_type;

            $goods_detail[0]['cut'] = $cut;
            $goods_detail[0]['cart'] = $cart?$cart:0;
            $goods_detail[0]['zhushi_num'] = $zhushi_num;
            $goods_detail[0]['clarity'] = $clarity;
            $goods_detail[0]['color'] = $color;
            $goods_detail[0]['zhengshuhao'] = $zhengshuhao;
            $goods_detail[0]['cert'] = $cert;
            $goods_detail[0]['caizhi'] = $caizhi;
            $goods_detail[0]['jinse'] = $jinse;
            $goods_detail[0]['jinzhong'] = $jinzhong ? $jinzhong:0;
            $goods_detail[0]['zhiquan'] = $zhiquan?$zhiquan:0;
            $goods_detail[0]['kezi'] = $kezi;
            $goods_detail[0]['face_work'] = $face_work;
            $goods_detail[0]['xiangqian'] = $xiangqian;
            $goods_detail[0]['tuo_type'] = $tuo_type;
            if($is_xianhuo==0 && $tuo_type=='成品' && $goods_type!='lz'){
                $goods_detail[0]['is_cpdz'] = 1;               
            }     
            $goods_detail[0]['cpdzcode'] = $cpdzcode;    
            if($goods_type == 'zengpin_goods'){
                $goods_detail[0]['product_type'] = $style_info['data']['product_type'];
                $goods_detail[0]['cat_type'] = $style_info['data']['style_type'];
                
                if(!empty($is_huangou)){
                    $goods_detail[0]['goods_price'] = $goods_price;
    				$goods_detail[0]['favorable_price'] = $goods_price-$chengjiaojia;
    				$goods_detail[0]['favorable_status'] = 3;
    				$goods_detail[0]['goods_type'] = 'style_goods';
    				$goods_detail[0]['is_zp'] = 0;
    				$goods_detail[0]['xiangqian'] = '成品';
                }else{
                    $goods_detail[0]['favorable_price'] = $chengjiaojia;
                    $goods_detail[0]['favorable_status'] = 3;
                    $goods_detail[0]['goods_type'] = 'zp';
                    $goods_detail[0]['is_zp'] = 1;
                    $goods_detail[0]['xiangqian'] = '不需工厂镶嵌';
                }
               
                $goods_detail[0]['is_peishi'] = 0;
                $goods_detail[0]['goods_sn'] = $params['goods_sn'];
				
            }else{
                $goods_detail[0]['cat_type'] = $cat_type;
                $goods_detail[0]['xiangkou'] = $xiangkou;
                $goods_detail[0]['product_type'] = $product_type;
                $val['favorable_price'] = 0;
                $val['favorable_status'] = 1;
            }
			

            $goods_detail[0]['kuan_sn'] = '';
            $goods_detail[0]['goods_from'] = $goods_from;
        }
        
        //订单默认是现货单，当前的商品是定制并且订单的当前状态是现货，则需要修改订单为定制单
        $order = array();
        $order['order_id'] =  $order_id;
        $order['is_edit'] =  0;
        $goods_amount = 0;
        $favorable_amount = 0;

        foreach ($goods_detail as $key=>$val){
            $goods_detail[$key]['goods_sn'] = strtoupper(trim($val['goods_sn']));
            $goods_amount += $val['goods_price'];
            $favorable_amount += (!isset($val['favorable_price']) || empty($val['favorable_price']) ? 0 : floatval($val['favorable_price']));
            if($order_info['is_xianhuo'] != 0){
                $order['is_xianhuo'] = $is_xianhuo;
                $order['is_edit'] =  1;
            }

           //起版类型更新
            $purchasemodel = new SelfPurchaseModel(23);
            $qiban_exists = $purchasemodel->getQiBanInfosByGoodsId($goods_detail[$key]['goods_id']);
            /* if($goods_detail[$key]['goods_sn'] =='QIBAN' && $qiban_exists){
                   $goods_detail[$key]['qiban_type'] = 0;
            }else{
                    if($proInfos['style_sn'] !='QIBAN'){
                        if(empty($qiban_exists)){
                           $goods_detail[$key]['qiban_type'] = 2;
                        }else{
                            $goods_detail[$key]['qiban_type'] = 1;
                        }
                    }   
            }
           */         
            if($goods_detail[$key]['goods_sn'] =='QIBAN' && $qiban_exists){
                 $goods_detail[$key]['qiban_type'] = 0;
            }else if($goods_detail[$key]['goods_sn'] !='QIBAN'){
                if(empty($qiban_exists)){
                   $goods_detail[$key]['qiban_type'] = 2;
                }else{
                    $goods_detail[$key]['qiban_type'] = 1;
                }   
            }
            
            if($val['goods_type'] == 'caizuan_goods'){
                $goods_detail[$key]['xiangqian'] = '不需工厂镶嵌';
            }

            //判断是现货钻 1、期货钻 2 boss_1287
            if($goods_type == 'qiban' || $goods_type == 'caizuan_goods'){//起版、彩钻默认是期货
                $goods_detail[$key]['dia_type'] = 2;
            }else{
                if($goods_detail[$key]['is_stock_goods'] == 1){//现货
                    $goods_detail[$key]['dia_type'] = 1;
                }elseif($goods_detail[$key]['is_stock_goods'] == 0 && $goods_detail[$key]['zhengshuhao'] == ''){//期货
                    $goods_detail[$key]['dia_type'] = 1;
                }elseif($goods_detail[$key]['is_stock_goods'] == 0 && $goods_detail[$key]['zhengshuhao'] != ''){
                    $diamondModel = new SelfDiamondModel(19);
                    $zhengshuhaot = str_replace(array("GIA", "EGL","AGL"), "", $goods_detail[$key]['zhengshuhao']);
                    $check_dia = $diamondModel->getDiamondInfoByCertId($zhengshuhaot);
                    if(!empty($check_dia) && isset($check_dia['good_type'])){
                        if($check_dia['good_type'] == 1){
                            $goods_detail[$key]['dia_type'] = 1;
                        }elseif($check_dia['good_type'] == 2){
                            $goods_detail[$key]['dia_type'] = 2;
                        }else{
                            $goods_detail[$key]['dia_type'] = 0;
                        }
                    }else{
                        $goods_detail[$key]['dia_type'] = 1;
                    }
                }else{
                    $goods_detail[$key]['dia_type'] = 0;
                }//判断是现货钻 1、期货钻 2
            }
        }

        if($new_goods_type==4){
            $order['is_edit'] =  1;
            $order['is_xianhuo'] = 0;
        }
        if($new_goods_type==5){
            $order['is_xianhuo'] = 0;
        }


        //获取订单总金额
        $order_account_arr = $orderModel->getOrderAccount($order_id);
        $order_amount = $order_account_arr['order_amount'];
		$favorable_price = $order_account_arr['favorable_price'];
        //未付的钱
        $unaid_order_price = $order_amount + $goods_amount;
        //订单金额
        $money['money_unpaid'] = $unaid_order_price;//未付
        $money['order_amount'] = $unaid_order_price;//订单总金额 = 未付
        $money['money_paid'] = 0;//已付
        $money['goods_amount'] = $order_account_arr['goods_amount'] + $goods_amount;//商品价格

		if($goods_type == 'zengpin_goods'){
				//未付的钱
				$unaid_order_price = $order_amount;
				//订单金额
				$money['goods_amount'] = $order_account_arr['goods_amount'] + $goods_amount;//商品价格
				if($is_huangou==1){
				    $money['favorable_price'] = $favorable_price+$favorable_amount;				    
				}else{
				    $money['favorable_price'] = $favorable_price + $goods_amount;				
				}
				$money['order_amount'] = $money['goods_amount']-$money['favorable_price'];//订单总金额 = 未付				
				$money['money_paid'] = $order_account_arr['money_paid'];//已付
				$money['money_unpaid'] =bcadd(bcsub($money['order_amount'],$money['money_paid'],2),$order_account_arr['real_return_price'],2);//未付
		}

        //操作日志
		$ation['order_id'] = $order_info['id'];
        $ation['order_status'] = $order_info['order_status'];
        $ation['shipping_status'] = $order_info['send_good_status'];//2015-12-24 zzm boss-1007
        $ation['pay_status'] = $order_info['order_pay_status'];
        $ation['create_user'] = $_SESSION['userName'];
        $ation['create_time'] = date("Y-m-d H:i:s");
        $ation['remark'] = $goods_type != 'zengpin_goods' ? "添加商品" : "增加赠品款号：{$goods_sn}， 名称：{$goods_name}，原因：" . (isset($params['gift_reason']) ? $params['gift_reason'] : '');
		
		//$orderActionModel = new AppOrderActionModel(27);
		//$res = $orderActionModel->saveData($ation, array());
			
        //var_dump($goods_detail);die;

        //保存所有数据
        $all_data = array('order'=>$order,'goods'=>$goods_detail,'money'=>$money,'action'=>$ation);
        //print_r($all_data);exit; 
        $res = $orderModel->makeNewOrderGoods($all_data);


        if($res !== false){
            //数据回写
            if($goods_type=='qiban'){
                $apiModel->updatePurchaseGoodsInfo(array('addtime'=>$goods_id,'opt'=>$_SESSION['userName'],'order_sn'=>$order_info['order_sn'],'customer'=>$order_info['consignee']));
            }
            if(is_numeric($res) && $res>0){
                try {
                    $pointRules = Util::point_api_get_config($order_info['department_id'], $order_info['mobile']);
                }
                catch (Exception $e) {
                    //无法确认积分规则，则暂时不更新，由最终赠送时再在处理
                }
                if (!empty($pointRules) && $pointRules['is_enable_point']) {
                    $pointModel = new SelfModel(27);
                    $pointModel->update_orderdetail_point($res, $pointRules);
                }
             }   
			$result['success'] = 1;
            $result['error'] = '添加成功';
		}else{
			$result['error'] = "添加商品失败";

		}
		
		//我不管你添加的是什么 我都做一次订单赠品检查
		$orderModel->updateorderiszp($order_id);
		//批量添加时， 不需要停止退出
		if (empty($params['batch'])){
			Util::jsonExit($result);
		}

     }

     /*
      * 获取此订单的商品
      */
     public function getGoodsByOrderId(){
         $result = array('success' => 0, 'error' => '');
         $cartModel = new AppOrderCartModel(27);
         $cart_data = $cartModel->get_cart_goods();
        $result['content'] = $this->fetch('order_cart_goods_list.html', array(
            'cart_data' => $cart_data,
        ));

        $result['title'] = '购物车';
        Util::jsonExit($result);
     }

     /*
      * 获取裸钻的商品
      */
     public function seeDiaGoods(){
         $result = array('success' => 0, 'error' => '');
         $goods_sn = _Request::getString('goods_sn');
         $order_id = _Request::getInt('id');
         if(empty($goods_sn)){
            $result['error'] = 1;
            $result['content'] = "货号或证书号不能为空!";
            Util::jsonExit($result);
         }
         //获取订单信息
         $orderModel= new BaseOrderInfoModel(27);
         $order_info = $orderModel->getOrderInfoById($order_id);
         $channel_id = $order_info['department_id'];
         $diamondListModel = new DiamondListModel(19);         
         //$goods_sn_arr = $DiamondModel->getRowByGoodSn($goods_sn);
         $goods_sn_arr = $diamondListModel->getRowByGoodSnOrCertId($goods_sn);

         if($goods_sn_arr['error']==1){
	            $result['error'] = 1;
	            $result['content'] = isset($goods_sn_arr['content']) ? $goods_sn_arr['content'] : "未查询到此裸钻";
	            Util::jsonExit($result);
         }
         
         $this->calc_dia_channel_price($goods_sn_arr['data']);
        
         //var_dump($goods_sn_arr);die();
         if($goods_sn_arr['data']['0']['status']==2){
             $result['error'] = 1;
             $result['content'] = "当前查询的裸钻已下架！";
             Util::jsonExit($result);
         }

         //双十一价格查询 begin
         $diamondModel     = new SelfDiamondModel(19);
         $ssyRow = $diamondModel->selectDiamondSSY('*',"goods_id='{$goods_sn}' or cert_id='{$goods_sn}'",2);
         if(!empty($ssyRow)){
             $goods_sn_arr['data']['0']['shop_price'] = $ssyRow['special_price'];
         }//双十一价格查询 end
         
		 $goods_sn_arr['data']['0']['xiangkou']=0;
         $tianshengyidui = 0;//默认不是天生一对
         if(count($goods_sn_arr['data'])>1){
             $tianshengyidui = 1;
         }
        //$xiangqian =array('工厂配钻，工厂镶嵌'=>'工厂配钻，工厂镶嵌','不需工厂镶嵌'=>'不需工厂镶嵌','需工厂镶嵌'=>'需工厂镶嵌','客户先看钻再返厂镶嵌'=>'客户先看钻再返厂镶嵌','成品'=>'成品','半成品'=>'半成品');
        $app_order_detail_model = new AppOrderDetailsModel(27);
        $xiangqian = $app_order_detail_model->getXiangqianList(false);
        //$result['content'] = $goods_sn_arr['data'];

        $edits = array('xiangqian');
        $result['content'] = $this->fetch("order_luozuan_attribute.html",array(
            'data' => $goods_sn_arr['data'],
            'tianshengyidui'=> $tianshengyidui,
            'xiangqian'=>$xiangqian,
            'edits' =>$edits,
            'channel_id'=>$channel_id,
            'mobile' => $order_info['mobile']
        ));

        $result['title'] = '裸钻';
        Util::jsonExit($result);
     }


     /*
      * 获取起版的商品
      */
     public function getQibanGoods(){
        $result = array('success' => 0, 'error' => '','title'=>'起版');
        $goods_sn = _Request::getString('goods_sn');
        $order_id = _Request::getInt('id');
        if(empty($goods_sn)){
            $result['content'] = "货号不能为空!";
            Util::jsonExit($result);
        }
        $apiModel = new ApiWarehouseModel();
        $qibanInfo = $apiModel->getPurchaseGoodsInfo(array('addtime'=>$goods_sn));
       
        if($qibanInfo['error']==1){
            $result['content'] = $qibanInfo['data'];            
            Util::jsonExit($result);
        }else if(!empty($qibanInfo['data']) && is_array($qibanInfo['data'])){
            $qibanInfo = $qibanInfo['data'];
        }else{
            $result['content'] = "起版信息查询失败！";
            Util::jsonExit($result);
        }
         $orderModel= new BaseOrderInfoModel(27);
         $order_info = $orderModel->getOrderInfoById($order_id);
         $channel = $order_info['department_id'];
        //$result['content'] = var_export($qibanInfo,true);
        //Util::jsonExit($result);  
        $qibanInfo['xuqiu'] = isset($qibanInfo['xuqiu'])?$this->dd->getEnum('purchase.qiban_xuqiu',$qibanInfo['xuqiu']):"";
        $qibanInfo['jinliao'] = isset($qibanInfo['jinliao'])?$this->dd->getEnum('purchase.qiban_jinliao',$qibanInfo['jinliao']):"";
        $qibanInfo['jinse'] = isset($qibanInfo['jinse'])?$this->dd->getEnum('purchase.qiban_jinse',$qibanInfo['jinse']):"";
        $qibanInfo['gongyi'] = isset($qibanInfo['gongyi'])?$this->dd->getEnum('purchase.qiban_gongyi',$qibanInfo['gongyi']):"";
        $result['content'] = $this->fetch("order_qiban_attribute.html",array(
            'qibanInfo' => $qibanInfo,
            'channel_id' => $channel,
            'mobile' => $order_info['mobile']
        ));
        Util::jsonExit($result);
     }
     /**
      * 换购商品查看
      * @param unknown $params
      */
     public function getHuangouGoods($params){
         $result = array('success' => 0, 'error' => '','title'=>'添加换购商品');
         $huangou_goods_id = _Request::getInt('huangou_goods_id');
         $order_id = _Request::getInt('id');

         if(empty($huangou_goods_id)){
             $result['content'] = "换购商品ID不能为空!";
             Util::jsonExit($result);
         }
                
         $model = new HuangouGoodsModel(27);
         //array("id"=>$huangou_goods_id,'status'=>1)
         $goodsInfo = $model->getHuangouGoodsInfo(array("id"=>$huangou_goods_id,'status'=>1));   
         if(empty($goodsInfo)){
             $result['content'] = "查询不到相关换购商品信息！";
             Util::jsonExit($result);
         }

         $orderModel= new BaseOrderInfoModel(27);
         $order_info = $orderModel->getOrderInfoById($order_id);
         $channel = $order_info['department_id'];
         $result['content'] = $this->fetch("order_huangougoods_attribute.html",array(
             'goodsInfo' => $goodsInfo,
             'channel_id' => $channel,
             'mobile' => $order_info['mobile'],
             'tuo_type' => '成品'
         ));
         Util::jsonExit($result);
     }

     /*
      * 获取销售政策对应的商品
      */
     public function getSaleGoods(){
         $tuo_type = _Request::getString('tuo_type','');
         $result = array('success' => 0, 'error' => '','tuo_type'=>$tuo_type);
         $goods_sn = _Request::getString('goods_sn');
         $order_id = _Request::getInt('id');

         if(empty($goods_sn)){
            $result['error'] = 1;
            $result['content'] = "货号不能为空!";
            Util::jsonExit($result);
         }

         //判断订单是否有效
        $orderModel= new BaseOrderInfoModel(27);
        $order_info = $orderModel->getOrderInfoById($order_id);
        $detailModel = new AppOrderDetailsModel(27);
        
         if(empty($order_info)){
            $result['error'] = 1;
            $result['content'] = "此订单的数据不存在！";
            Util::jsonExit($result);
         }
     
        $channel = $order_info['department_id'];
        $Company_staff_name = $this->getTydTypeByDep($channel);
        $channel_class = 0;
        //获取销售渠道基本信息（主要获取销售渠道类型，线下线上）
        $salesChannelsModel = new SalesChannelsModel(1);
        $salesChannelsInfo = $salesChannelsModel->getSalesChannelsInfo("id,channel_class",array('id'=>$channel));
        if(isset($salesChannelsInfo[0]['channel_class'])){
            $channel_class = $salesChannelsInfo[0]['channel_class'];
        }
        // $channel = 4;
		$s_where['goods_id'] = $goods_sn;
		$s_where['channel'] = $channel;
		$isQihuochengpin = 0;
		if(strpos($goods_sn,'-')!==false){
			$appSalepolicyGoodsModelR =new AppSalepolicyGoodsModel(15);
			//说明是期货
			$sdata = $appSalepolicyGoodsModelR->pageQihuoList($s_where,1,1);
			if(empty($sdata['data'][0]))
			{
				$str = "您输入的虚拟货号不存在呢";
				$result['error'] = 1;
				$result['content'] = $str." \n如有任何疑问请联系".$Company_staff_name;
				Util::jsonExit($result);
			}elseif(empty($sdata['data'][0]['sprice'])){
				$str = "您输入的货品没有找到销售政策";
				$result['error'] = 1;
				$result['content'] = $str." \n如有任何疑问请联系".$Company_staff_name;
				Util::jsonExit($result);	
			}
            if(!empty($sdata['data'][0]['company_type_id'])){
                $userCompany_type_id=$appSalepolicyGoodsModelR->userByCompany();
                $company_type_ids= explode(',',trim($sdata['data'][0]['company_type_id'],','));               
                if(!empty($company_type_ids) && !in_array($userCompany_type_id, array_filter($company_type_ids))){
					$result['error'] = 1;
					$result['content'] = "此款不支持定制，若有疑问请联系易霞核实".$userCompany_type_id;
					Util::jsonExit($result);                    
                }
            }

			$isQihuochengpin = $sdata['data'][0]['is_chengpin'];
			//$isQihuochengpin = 1;
		}else{
			//找现货咯
			$goodsAttrModel = new GoodsAttributeModel(17);
			$caizhi = $goodsAttrModel->getCaizhiList();
			$yanse  = $goodsAttrModel->getJinseList();
			$appSalepolicyGoodsModelR =new AppSalepolicyGoodsModel(15);
			//经销商的需要增加公司的过滤
			if( SYS_SCOPE == 'zhanting' )
			{
				$is_company_check = Auth::user_is_from_base_company();
				if(!$is_company_check){
					$s_where['company_id_list'] = $_SESSION['companyId'];
				}
			}
			$sdata = $appSalepolicyGoodsModelR->pageXianhuoList($s_where,1,1,$caizhi,$yanse,true);
			if(isset($sdata['error']) && $sdata['error']== 1 )
			{
				$str = $sdata['content'];
				$result['error'] = 1;
				$result['content'] = $str." \n如有任何疑问请联系".$Company_staff_name;
				Util::jsonExit($result);
			}
			if(empty($sdata['data'][0]['sprice']))
			{
				$str = "您输入的货品没有找到销售政策";
				$result['error'] = 1;
				$result['content'] = $str." \n如有任何疑问请联系".$Company_staff_name;
				Util::jsonExit($result);
			}
			$isQihuochengpin = 2;
			
		}
		
		//能走到这里说没过了销售政策这关了
		$tmpdata = array();
		$hpobj = $sdata['data'][0]; //一个货号只会出现一行
		$saleprice = $hpobj['sprice'];
		$allpolicyids = $hpobj['policy_id_split'];
		$gid = $hpobj['goods_id'];
		
		$tmpdata['data'] = array();
		$tmpdata['data']['sprice'][$gid] = array();
		foreach($saleprice as $k=>$priceobj)
		{
			$tmpd = array();
			$tmpd = array(
				'isXianhuo' => $hpobj['isXianhuo'],
				'sale_price' => $priceobj['sale_price'],
				'goods_sn' => $hpobj['goods_sn'],
				'goods_name' => $hpobj['goods_name'],
				'category' => $hpobj['category'],
				'product_type' => $hpobj['product_type'],
				'policy_id' => $allpolicyids[$k],
                'goods_id' => $hpobj['goods_id'],
                'caizhi' => $hpobj['caizhi'],
			);
			array_push($tmpdata['data'],$tmpd);
			$tmpdata['data']['sprice'][$gid][$allpolicyids[$k]]=$priceobj;
		}
		$tmpdata['error']=0;
		$data = $tmpdata;
		
		//
        $arr = array_column($data['data'],'sale_price','policy_id');
        $max =max($arr);
        $policy_id=array_search($max,$arr);
        foreach($data['data'] as $key=>$val){
            if(isset($val['policy_id']) && $val['policy_id']==$policy_id){
                $is_xianhuo = $data['data'][$key]['isXianhuo'];
                $chengjiaojia = $data['data'][$key]['sale_price'];
                $goods_name = $data['data'][$key]['goods_name'];
                $goods_id = $data['data'][$key]['goods_id'];
            }
         }
        $apiWarehouseModel = new ApiWarehouseModel();
        $default_factory = false;
        $style_sn = $data['data'][0]['goods_sn'];//款号
        $apiStyle = new ApiStyleModel();
        $styleInfo=$apiStyle->getStyleInfo($style_sn);
        $product_type = '';//产品线
        $style_type =''; //款式分类       
        if(!empty($styleInfo['data']) && is_array($styleInfo['data'])){
            $styleInfo = $styleInfo['data'];
            $product_type = $styleInfo['product_type'];
            $style_type   = $styleInfo['style_type'];
        }
        $style_attr = $apiStyle->GetStyleAttribute(array('style_sn'=>$style_sn));
        //表面工艺，根据款式维护属性控制
         $face_work = array();
        if(!empty($style_attr['data'][27]['value'])){
            $face_work_split = explode(',',$style_attr['data'][27]['value']);
            foreach($face_work_split as $vo){
                if(trim($vo)!=''){
                    $face_work[$vo]=$vo;
                }
            }
        }

        //获取crm积分规则 type :   2.奖励积分规则
        switch ($is_xianhuo){
            //货品数据
            case 1:
                $goods_info = $apiWarehouseModel->getWarehouseGoodsInfo(array('goods_id'=>$goods_sn));
                if($goods_info['error']==1){
                    $result['error'] = 1;
                    $result['content'] = "没有此货号信息";
                }else{
                    $new_goods_info['data']=$this->getWarehouseData($goods_info['data']['data']);
                    //表单元素编辑权限判断 begin                      
                    //默认线下按照下单规范new-272,开放部分字段编辑权限
                    $cp_type = $new_goods_info['data']['tuo_type'];//1成品，2空托女戒，3空托 
                    if($cp_type == 1){
                        //现货成品，只有指圈，刻字，备注是可以编辑
                        $edits =array('zhiquan','kezi','info');
                        $default_factory = '成品';
                        if(empty($new_goods_info['data']['xiangqian'])){                             
                            $new_goods_info['data']['xiangqian']=$default_factory;
                            if (in_array($goods_info['data']['data']['product_type1'], array('裸石','彩钻'))){
                            	$new_goods_info['data']['xiangqian']= '';
                            	$default_factory = '';
                            }
                        } 
                    }else if($cp_type > 1){
                        //现货空拖，只有石重，颜色，净度，证书号，指圈，刻字，备注，镶嵌要求，表面工艺是可以编辑
                        $edits =array('cart','color','clarity','zhengshuhao','cert','zhiquan','kezi','xiangqian','face_work','info');
                    }else{
                        //未知
                        $edits =array('cart','color','clarity','zhengshuhao','cert','zhiquan','kezi','xiangqian','face_work','info');
                    }                        
                    //1经销商销售渠道(108)，2.线上销售渠道，3.线下销售渠道非钻石女戒  这3种情况放开编辑权限
                    if($channel==108 || $channel_class==1 || ($channel_class==2 && ($product_type!=6 || $style_type!=2))){
                         //线上销售渠道，开放所有字段编辑权限(当前共13个字段),如果有新增字段，请把name值加入edits
                         $edits = array('cart','color','jinse','caizhi','jinzhong','xiangkou','clarity','zhengshuhao','zhiquan','kezi','xiangqian','face_work','info');
                    } 
                              
                    if(in_array('zhiquan',$edits)){
                        if(isset($style_attr['data'][31]['value']) && trim($style_attr['data'][31]['value']) == "不可改圈"){
                            unset($edits[array_search("zhiquan",$edits)]);//删除指圈修改权限
                        }
                    }


                    //根据款号获取主石粒数，如果没有获取到，则主石粒数可编辑
                    $zhushi_num = $detailModel->getZhushiNum($goods_info['data']['data']['goods_sn']);
                    if($zhushi_num != 0){
                        $new_goods_info['data']['zhushi_num'] = $zhushi_num;
                    }else{
                        array_push($edits,'zhushi_num');
                    }
                    //print_r($edits);
                    /* if(empty($new_goods_info['cert'])){
                        $edits[] = "cert";
                    } */
                    //表单元素编辑权限判断 end
                    
                    /*
                    $DiamondModel = new DiamondListModel(19);
					$xiangkou = $DiamondModel->getXiangKou($new_goods_info['data']['zuanshidaxiao']);
					$new_goods_info['data']['xiangkou']=$xiangkou;
                   */

				   	$goods_attr = $detailModel->getGoodsAttr();

				   	//如果这件货是空托，则镶嵌要求不能选成品
                    $tuo_type = $goods_info['data']['data']['tuo_type'];
                    if($tuo_type != 1){
                       unset($goods_attr['xiangqian'][5]);
                    }

                    if(!empty($face_work)){
                        $goods_attr['face_work'] = $face_work;
                    }
                    $result['content'] = $this->fetch("order_style_attribute.html",array(
                        'goods_id'=>$goods_id,
                        'channel_id' =>$channel,
                        'data_attr' => $new_goods_info,
                        'chengjiaojia'=> $chengjiaojia,
                        'goods_name'=>$goods_name,
                        'goods_attr'=>$goods_attr,
                        'isXianhuo'=>$is_xianhuo,
                        'policy_id'=>$policy_id,
                        'prices'=>$data['data']['sprice'],
                        'edits'=>$edits,
                        'default_factory'=>$default_factory,
                        'isQihuochengpin'=>$isQihuochengpin,
                        'mobile' => $order_info['mobile'],
                        'tuo_type' => $cp_type == 1 ? '成品' : '空托', //1成品，2空托女戒，3空托
                        'caizhi' => $new_goods_info['data']['caizhi'],
                        'product_type' => $product_type
                    ));
                }
                Util::jsonExit($result);
                break;
            //款式数据
            case 0:
                $goods_attr = $detailModel->getGoodsAttr(false); 
                if(!empty($face_work)){
                    $goods_attr['face_work'] = $face_work;
                }
                if($style_attr['error']==1){
                    $result['error'] = 1;
                    $result['content'] = $style_attr['data'];
                    Util::jsonExit($result);
                }else{
                    //期货  指圈修改权限验证 begin
                    //默认线下按照下单规范new-272,开放部分字段编辑权限
                    //只有石重，颜色，净度，证书号，指圈，刻字，备注，镶嵌要求，表面工艺是可以编辑的，其他内容都不允许修改
                    $edits =array('cart','zhushi_num','color','clarity','zhengshuhao','zhiquan','kezi','xiangqian','face_work','info');
                    if($channel_class==1){
                        //线上销售渠道，开放所有字段编辑权限(当前共13个字段),如果有新增字段，请把name值加入edits
                        $edits =array('cart','zhushi_num','color','jinse','caizhi','jinzhong','xiangkou','clarity','zhengshuhao','zhiquan','kezi','xiangqian','face_work','info');
                    }
                   /* if(in_array('zhiquan',$edits)){
                        if(!empty($style_info['data'])&&!empty($style_info['data'][31]) && trim($style_info['data'][31]['value']) == "不可改圈"){
                            unset($edits[array_search("zhiquan",$edits)]);//删除指圈修改权限
                        }
                    } */              
                   
                }
                $edits[] = "cert";
                $edits[] = "tuo_type";//金托类型
                
                //print_r($data['data']['sprice']);
                //exit;
                //再所有属性中获取需要的
                $style_info['data'] = $detailModel->getStyleAttribute($goods_sn, $style_attr['data']);
                $result['content'] = $this->fetch("order_style_attribute.html",array(
                    'goods_id'=>$goods_id,
                    'channel_id' =>$channel,
                    'data_attr' => $style_info,
                    'chengjiaojia'=> $data['data'][0]['sale_price'],
                    'goods_name'=> $goods_name,
                    'goods_attr'=>$goods_attr,
                    'policy_id'=>$policy_id,
                    'prices'=>$data['data']['sprice'],
                    'edits'=>$edits,
                    'default_factory'=>$default_factory,
                    'isXianhuo'=>0,
                    'isQihuochengpin'=>$isQihuochengpin,
                    'mobile' => $order_info['mobile'],
                    'tuo_type' => $tuo_type,
                    'caizhi' => $new_goods_info['data']['caizhi'],
                    'product_type' => $new_goods_info['data']['product_type']
                ));
               Util::jsonExit($result);
               break;
        }

     }


    /*
      * 获取销售政策对应的商品
      */
     public function getZengPinGoods(){
         $result = array('success' => 0, 'error' => '');
         $style_sn = _Request::getString('goods_id');
         //print_r($_REQUEST);die;
         $order_id = _Request::getInt('id');
         if(empty($style_sn)){
            $result['error'] = 1;
            $result['content'] = "款号不能为空!";
            Util::jsonExit($result);
         }
         //判断订单是否有效
        $orderModel= new BaseOrderInfoModel(27);
        $order_info = $orderModel->getOrderInfoById($order_id);

         if(empty($order_info)){
            $result['error'] = 1;
            $result['content'] = "此订单的数据不存在！";
            Util::jsonExit($result);
         }
        $giftModel = new ApiGiftManModel();
        $where = array(
            'goods_number' => $style_sn,//款号
        );
        $dataRet = $giftModel->GetGiftByUsefullSn($where);
         if(empty($dataRet)){
            $result['error'] = 1;
            $result['content'] = "赠品信息不存在，请联系物控部!";
            Util::jsonExit($result);
         }
        $result['content'] = $this->fetch("order_style_zengpin.html",array(
            'style_attr' => $dataRet['data']
        ));
        $result['title'] = '赠品';
        Util::jsonExit($result);
     }


    public function getCaiZuan(){
        $result = array('success' => 0, 'error' => '');
        $style_sn = _Request::getString('goods_id');
        //print_r($_REQUEST);die;
        $order_id = _Request::getInt('id');
        if(empty($style_sn)){
            $result['error'] = 1;
            $result['content'] = "货号不能为空!";
            Util::jsonExit($result);
        }
        //判断订单是否有效
        $orderModel= new BaseOrderInfoModel(27);
        $order_info = $orderModel->getOrderInfoById($order_id);

        if(empty($order_info)){
            $result['error'] = 1;
            $result['content'] = "此订单的数据不存在！";
            Util::jsonExit($result);
        }
        $channel = $order_info['department_id'];
        $dmodel = new DiamondListModel();
        
        $data = $dmodel->getCaiZuaninfo($style_sn);

		if($data['data'] =='under'){
	        	$result['error'] = 1;
	        	$result['content'] = "您搜索的商品已经下架!";
	        	Util::jsonExit($result);
	        }        
		if($data['data'] == '未查询到此彩钻'){
	            $result['error'] = 1;
		    	$result['content'] = "未查到此彩钻信息！";
	            Util::jsonExit($result);
	        }
        $detailModel = new AppOrderDetailsModel(27);
        //鉴定是否重复

        $res = $detailModel->getGoodsByOrderId(array('order_id'=>$order_id));
        if(!empty($res)){
            $goods_sns = array_column($res,'goods_sn');
            if(in_array($style_sn,$goods_sns)){
                $result['error'] = 1;
                $result['content'] = "一个订单不能存在相同的彩钻！";
                Util::jsonExit($result);
            }
        }

        $goods_attr = $detailModel->getCaiZuanAttr();
        $result['content'] = $this->fetch("order_style_caizuan.html",array(
            'style_attr' => $data['data'],
            'goods_attr' => $goods_attr,
            'channel_id' => $channel,
            'mobile' => $order_info['mobile']
        ));

        $result['title'] = '彩钻';
        Util::jsonExit($result);
    }

      /*
      * 获取销售政策对应的商品
      */
/*     public function getStyleAttribute($good_id,$style_info){
        $sale_attribute_arr = array('goods_sn','cart','clarity','color','zhengshuhao','caizhi','jinse','jinzhong','zhiquan','xiangkou','shape');
        //款式库货号组成：款号+材质+颜色+镶口+指圈
        $goods_id_arr = explode("-", $good_id);
        $goods_sn = $goods_id_arr[0];
        $caizhi = $goods_id_arr[1];
        $color = $goods_id_arr[2];
        $xiangkou = $goods_id_arr[3]/100;
        $zhiquan = $goods_id_arr[4];
        //从货号上可以知道是属性
        $color_arr = array('W'=>"白",'Y'=>"黄",'R'=>"玫瑰金",'C'=>'分色');
        $have_attribute = array(
            'goods_sn'=>$goods_sn,
            'caizhi'=>$caizhi,
            'jinse'=>$color_arr[$color],
            'cart'=>$xiangkou,
            'color'=>$color,
            'zhiquan'=>$zhiquan,
            'xiangkou'=>$xiangkou
        );
        $new_attribute_arr = array();
        foreach ($style_info as $key=>$val){
            $attribute_code = isset($val['attribute_code'])?$val['attribute_code']:'';
            $attribute_value =isset($val['value'])?$val['value']:'';
            if(in_array($attribute_code, $sale_attribute_arr)){
                $new_attribute_arr[$attribute_code] = $attribute_value;
            }
       }
       //把没有是属性设成空
       foreach ($sale_attribute_arr as $val){
           if(!array_key_exists($val, $new_attribute_arr)){
               $new_attribute_arr[$val]="";
           }
           if(array_key_exists($val, $have_attribute)){
                 $new_attribute_arr[$val] = $have_attribute[$val];
            }
       }
       $model = new AppOrderDetailsModel(28);
       $sql = "select a.stone_ca,a.stone_attr from front.rel_style_stone a inner join front.base_style_info b on a.style_id=b.id where b.style_sn='{$goods_sn}' and a.stone_ca in(1,2) and stone_position=1";
       $stoneInfo = $model->db()->getRow($sql);
       if(empty($stoneInfo)){
           $new_attribute_arr['shape'] = "";
       }else{
           if($stoneInfo['stone_ca']==1){
               $new_attribute_arr['shape'] = "圆形";
           }else{
               $stoneAttr = unserialize($stoneInfo['stone_attr']); 
               print_r($stoneAttr);
           }
       }
       return $new_attribute_arr;
    } */

    //只有定制可以重复定制
    public function orderDetailGoodsUnique($where){
        $goods_type = $where['goods_type'];
        if($goods_type !="style_goods" ){
            $orderDetailModel = new AppOrderDetailsModel(27);
            $order_details = $orderDetailModel->getGoodsByOrderId($where);
            if($order_details){
                $result['error'] = "该货号已经下单了！";
                Util::jsonExit($result);
            }
        }
    }

    //判断订单中裸钻的折扣密码是否正确
    public function checkDiamondMima(){
        $mima = _Request::getString('mima');
        $detail_id = _Request::getInt('detail_id');

        $detailModel = new AppOrderDetailsModel($detail_id,28);
        /*if($detailModel->getValue('favorable_status')>1){
            $result = array('error'=>1,'content'=>'该商品已申请优惠！');
            Util::jsonExit($result);
        }*/
        $carat = $detailModel->getValue('cart');
        $goods_price = $detailModel->getValue('goods_price');
        $user_id = $_SESSION['userId'];
        $type = $this->getDiamondType($carat);

        //判断此密码是否有效
        $diamondModel = new DiamondListModel();
        $where = array('type'=>$type,'user_id'=>$user_id,'mima'=>$mima);

        $data = $diamondModel->checkMimaVaild($where);


        if($data['error']==1){
            $result = array('error'=>1,'content'=>$data['data']);
        }else{
            //根据获取的折扣计算优惠的价格
            $zhekou = $data['data']['zhekou'];
            $money = 0;
            if($zhekou <1){
                $money = intval($goods_price * (1-$zhekou));
            }else{
				$money = intval($goods_price-($goods_price*$zhekou));
			}

            $result = array('error'=>0,'grant_id'=>$data['data']['id'],'money'=>$money);
        }
         Util::jsonExit($result);
    }



    /*
     * 根据石重判断裸钻属于那种类型
     * '1'=>'裸钻50分以下折扣','2'=>'裸钻50分(含)-1.5克拉折扣','3'=>'裸钻1.5克拉(含)以上折扣'
     */
    public function getDiamondType($cart){
        if($cart<0.5){
            $type = 1;
        }else if($cart>=0.5 && $cart<1){
            $type = 2;
        }else if($cart>=1 && $cart<1.5){
            $type = 3;
        }else{
            $type = 4;
        }
        return $type;
    }
	//检测购物车商品是否为最新价格
    public function checkCartGoods(){
		
        $cartModel = new AppOrderCartModel(28);
        $cart_goods = $cartModel->get_cart_goods();
		$cart_ids = _Request::getList('cart_id');
		$price_is_different = 0;
		$different = "";
		foreach ($cart_goods as $val){
            if(in_array($val['id'], $cart_ids)){

                if($val['is_4c']==1){
				    //默认裸石搜索
				    $new_diamond=ApiModel::diamond_api(array('cert_id','status'),array($val['zhengshuhao'],1),'GetDiamondList');
				    if ($new_diamond['error']){		            
				         $price_is_different = 2;
				         $different = "亲，您稍晚了一步，裸钻(证书号:{$val['zhengshuhao']})已被预定或下架，请到裸石列表重新搜索！";
				         
				    }
				 }else if ($val['is_4c']==2){
				    //4c快捷搜索
				    $new_diamond=ApiModel::diamond_api(array('cert_id','status'),array($val['zhengshuhao'],1),'GetDiamondList');
				    if($new_diamond['error']){
    				    $filter_data = json_decode($val['filter_data'], true);
    				    $new_diamond=ApiModel::diamond_api($filter_data['keys'],$filter_data['vals'],'GetDiamondList');
    				    if (!$new_diamond['error']){
    				        $data = current($new_diamond['data']['data']);
    				        $price_is_different = 1;
    				        $different = "亲，您稍晚了一步，裸钻(证书号:{$val['zhengshuhao']})已被预定或下架,系统找到了与该类型(相同4C条件)相匹配的其它商品最低价格 ". $data['shop_price'] ."(当前商品价格" . $val["goods_price"] . ") ，是否采用最新商品继续下单？";
    				    }else{
    				        $price_is_different = 2;
    				        $different = "亲，您稍晚了一步，裸钻(证书号:{$val['zhengshuhao']})已被预定或下架，同时现有库存没有与该类型相匹配的其它商品，请到4C快捷搜索列表重新搜索！";
    				    }
				    }
				}else if ($val['is_4c']==3){
				    //地区特价钻活动
				    $new_diamond=ApiModel::diamond_api(array('cert_id','status'),array($val['zhengshuhao'],1),'GetDiamondList');
				    if($new_diamond['error']){
    				    $filter_data = json_decode($val['filter_data'], true);
    				    $new_diamond=ApiModel::diamond_api($filter_data['keys'],$filter_data['vals'],'GetDiamondList');
    				    if (!$new_diamond['error']){
    				        $data = current($new_diamond['data']['data']);
    			            $price_is_different = 1;
    				        $different = "亲，您稍晚了一步，裸钻(证书号:{$val['zhengshuhao']})已被预定或下架,系统找到了与该类型相匹配的其它商品 ，是否采用最新商品继续下单？";
    				    }else{
    				        $price_is_different = 2;
    				        $different = "亲，您稍晚了一步，裸钻(证书号:{$val['zhengshuhao']})已被预定或下架，同时现有库存没有与该类型相匹配的其它商品，请到地区特价钻活动列表重新搜索！";
    				    }
				    }
				}

            }
            if($price_is_different){
                break;
            }
        }   
		if ($price_is_different){
			$result['error'] = 1;
			$result['code'] = $price_is_different;
            $result['content'] = $different;
            Util::jsonExit($result);
		}else{
			$result['error'] = 0;
			Util::jsonExit($result);
		}
		
	}
    //保存购物车
    public function saveCartGoods()
	{
	
        $result = array('success' => 1,'error' => '');
        $id = _Request::getString('id');
        $cart_ids = _Request::getList('cart_id');
		$peishi = _Request::getList('peishi');
        if(empty($id)){
            $result['error'] = 1;
            $result['content'] = '订单数据有问题';
            Util::jsonExit($result);
        }
        $orderModel = new BaseOrderInfoModel($id,28);
        $department = $orderModel->getValue('department_id');
        $order_is_xianhuo = $orderModel->getValue('is_xianhuo');

        //排除如果部门不相同则需要清空购物车
        $cartModel = new AppOrderCartModel(28);
        $cart_goods = $cartModel->get_cart_goods();
        $diamondModel = new SelfDiamondModel(19);
        $CStyleModel = new CStyleModel(19);
        //遍历购物车中的数据，找到选中的数据
        $select_cart_goods = array();
        $select_cart_goods_id = array();
        $kuan_sn_arr = array();
        foreach ($cart_goods as $val){
			
            if(in_array($val['id'], $cart_ids)){
				
				$val['is_peishi'] = isset($peishi[array_search($val['id'],$cart_ids)]) ? $peishi[array_search($val['id'],$cart_ids)] : 0;//是否配石
				//is_4c=(1,2,3),is_4c值大于0的全部为裸钻，否则为非裸钻
				//is_4c=1 裸石列表搜索，is_4c=2 4c快捷搜索，is_4c=3 成都婚博会特价钻
				if ($val['is_4c']){
				    //检测裸钻
				    $filter_data['keys'] = array('cert_id','status');
					$filter_data['vals'] = array($val['zhengshuhao'],1);
					$new_diamond=ApiModel::diamond_api($filter_data['keys'],$filter_data['vals'],'GetDiamondList');
				    //裸钻不存在或已下架
				    if($new_diamond['error']){
				        if($val['is_4c']==1){
				            $result['error'] = 1;
                            $result['content'] = "证书号为{$val['zhengshuhao']}的裸钻已下架";
                            Util::jsonExit($result);
				        }else{
				            //根据【4c条件】或【地区特价钻活动条件】重新搜索新商品
				            $filter_data = json_decode($val['filter_data'], true);
				            $new_diamond = ApiModel::diamond_api($filter_data['keys'],$filter_data['vals'],'GetDiamondList');
				        }
				    }
				    					
					if (!$new_diamond['error']){
						$this->calc_dia_channel_price($new_diamond['data']['data']);
						foreach($new_diamond['data']['data'] as $key=>$value){
							$val["goods_id"]=$value['goods_sn'];
							$val["goods_sn"]='DIA';
							$val["goods_price"]=$value['shop_price'];
							$val["is_stock_goods"]=$value['good_type']==1?1:0;
							$val["goods_count"]=1;
							$val["create_time"]=  date("Y-m-d H:i:s");
							$val["modify_time"]= date("Y-m-d H:i:s");
							$val["create_user"]=$_SESSION['userName'];
							$val["cart"]=$value['carat'];
							$val["zhushi_num"]=1;
							$val["cut"]=$value['cut'];
							$val["tuo_type"]='成品';
							$val["cert"] = $value['cert'];
							$val["clarity"]=$value['clarity'];
							$val["color"]=$value['color'];
							$val["goods_type"]='lz';
							$val["kuan_sn"]=$value['kuan_sn'];
							$val["product_type"]=0;
							$val["cat_type"]=0;
							$val["zhengshuhao"]=$value['cert_id'];
							$val["goods_name"]=$value["carat"]."克拉/ct ".$value["clarity"]."净度 ".$value["color"]."颜色 ".$value["cut"]."切工";
						    break;
						}						
					}else{
					    $result['error'] = 1;
                        $result['content'] = "证书号为{$val['zhengshuhao']}的裸钻已下架";
                        Util::jsonExit($result);
					}
					//双十一活动 begin by gaopeng
					if($val['is_4c']==1){
					    //重新查询双十一价格
					    $ssyRow = $diamondModel->selectDiamondSSY("*","cert_id='{$val['zhengshuhao']}'",2);
					    if(!empty($ssyRow)){
					        $val['goods_price'] = $ssyRow['special_price'];
					    }
					}//双十一活动 end
					//8周年 begin
					if($val['is_4c']==3){
					    $filter_data = json_decode($val['filter_data'],true);
					    $special_price = $filter_data['data']['special_price'];
					    $city          = $filter_data['data']['city'];
						$title          = $filter_data['data']['title'];
					    $favorable_price = $val['goods_price'] - $special_price;
					    $val["details_remark"] ="{$title},{$city}地区特价{$special_price}";
					}//8周年 end					

				}
				$val['is_cpdz'] = $val['goods_type']<>'lz' && $val['tuo_type']=='成品' && $val['is_stock_goods']==0 ?1:0;
				$select_cart_goods[] = $val;
                //$select_cart_goods[0]['jinse']  = $val['jinse'];				
                //$select_cart_goods[0]['caizhi'] = $val['caizhi'];				
                $select_cart_goods_id[$val['goods_id']] = $val['goods_id'];
                if(!empty($val['kuan_sn'])){
                    $kuan_sn_arr[$val['kuan_sn']]= $val['kuan_sn'];
                }
            }
        }
        if(empty($select_cart_goods)){
            $result['error'] = 1;
            $result['content'] = '选中数据有问题';
            Util::jsonExit($result);
        }
        //如果选中的商品中有天生一对，如果只选中了一个，那么另一个也需要选中
        foreach ($cart_goods as $val){
            $kuan_sn = $val['kuan_sn'];
            $goods_id = $val['goods_id'];
            if(empty($kuan_sn)){
                continue;
            }

            if(array_key_exists($kuan_sn, $kuan_sn_arr) && !array_key_exists($goods_id, $select_cart_goods_id)){
                $select_cart_goods[] = $val;
            }
        }

        $is_falg = $cartModel->is_department($department, $select_cart_goods);

        if(!$is_falg){
            $result['error'] = 1;
            $result['content'] = '您购物车中的商品的销售渠道和此订单的渠道部门不一致';
            Util::jsonExit($result);
        }

        //判断此订单中的现货只能添加一个，裸钻期货也只能添加一个
        $data_goods = array();
        $is_have_arr = array();
        $detailModel = new AppOrderDetailsModel(27);
        
        foreach ($select_cart_goods as $val){
            $where_goods = array('goods_id'=>$val['goods_id'],'order_id'=>$id);
            if($val['is_stock_goods'] == 1 || $val['goods_type'] == 'lz' || $val['goods_type'] == 'caizuan_goods')
            $data_goods = $detailModel->checkOrderGoodsOnly($where_goods);

            if($data_goods){
                $is_have_arr[] = $val['goods_id'];
            }
        }

        if($is_have_arr){
            $is_have_goods = implode(',', $is_have_arr);
            $result['error'] = 1;
            $result['content'] = '您购物车中的商品:'.$is_have_goods.'，在此订单中，都已经存在！';
            Util::jsonExit($result);
        }
       $goods_price = 0;
        foreach ($select_cart_goods as $val){
             $goods_price +=$val['goods_price'];
         }
        //计算选中数据的价格，判断一下是否有期货
        //查处对应policyid
        /* $apgids = implode(",",array_column($select_cart_goods,'policy_goods_id'));
        if($apgids){
            $apiSmodel = new ApiSalePolicyModel();
            $res =  $apiSmodel->AppSalePolicyGoodsById(array('_ids'=>$apgids));
            if(!empty($res['data']) && is_array($res['data'])){
	            $rea = array_column($res['data'],'policy_id','goods_id');
	            foreach($select_cart_goods as $key=>$val){
	                $select_cart_goods[$key]['policy_id']=$rea[$val['goods_id']];
	            }
            }
        } */
      
        //商品通过购物车加入订单后，如没有石重，但是有镶口，则石重默认等于镶口(NEW-2248)
        foreach ($select_cart_goods as $key=>$val){
            if((!$val['cart'] || $val['cart']<=0) && $val['xiangkou'] && $val['xiangkou'] > 0){
                $val['cart'] = $val['xiangkou'];
            }
            if($val['goods_id']){
                $select_cart_goods[$key]['ext_goods_sn'] = $val['goods_id'];
            }
            if($val['goods_type'] == 'qiban'){
			    $select_cart_goods[$key]['qiban_type'] = $val['goods_sn']=='QIBAN'?0:1; //无款起版与有款起版
            }else{
			    $select_cart_goods[$key]['qiban_type'] = 2; //购物车里面的产品起版类型都为非起版
            }

            if(preg_match("/lz|caizuan/is",$val['goods_type'])){
                $select_cart_goods[$key]['xiangqian'] = '不需工厂镶嵌';
                $select_cart_goods[$key]['xiangkou'] = '0';
                $select_cart_goods[$key]['zhiquan'] = '0';
                $select_cart_goods[$key]['zhushi_num'] = '1';
            }else if($select_cart_goods[$key]['is_stock_goods'] == 0){
                //期货，从款式 提取主石粒数   gaopeng
                $stoneList = $CStyleModel->getStyleStoneByStyleSn($val['goods_sn']);
                $zhushi_num = 0;
                if(!empty($stoneList[1])){
                    $zhushiList = $stoneList[1];//主石列表
                    foreach ($zhushiList as $zhushi) {
                        $zhushi_num += $zhushi['zhushi_num'];
                    }
                }
                $select_cart_goods[$key]['zhushi_num'] = $zhushi_num;
            }
          
            //判断是现货钻 1、期货钻 2 boss_1287
            if($select_cart_goods[$key]['is_stock_goods'] == 1){//现货
                $select_cart_goods[$key]['dia_type'] = 1;
            }elseif($select_cart_goods[$key]['is_stock_goods'] == 0 && $select_cart_goods[$key]['zhengshuhao'] == ''){//期货
                $select_cart_goods[$key]['dia_type'] = 1;
            }elseif($select_cart_goods[$key]['is_stock_goods'] == 0 && $select_cart_goods[$key]['zhengshuhao'] != ''){
                $zhengshuhaot = str_replace(array("GIA", "EGL","AGL"), "", $select_cart_goods[$key]['zhengshuhao']);
                $check_dia = $diamondModel->getDiamondInfoByCertId($zhengshuhaot);
                if(!empty($check_dia) && isset($check_dia['good_type'])){
                    if($check_dia['good_type'] == 1){
                        $select_cart_goods[$key]['dia_type'] = 1;
                    }elseif($check_dia['good_type'] == 2){
                        $select_cart_goods[$key]['dia_type'] = 2;
                    }else{
                        $select_cart_goods[$key]['dia_type'] = 0;
                    }
                }else{
                    $select_cart_goods[$key]['dia_type'] = 1;
                }
            }else{
                $select_cart_goods[$key]['dia_type'] = 0;
            }//判断是现货钻 1、期货钻 2
        }

        $orderModel->addNewOrderDetail($id,$select_cart_goods);//增加商品到购物车
        $orderModel->changeOrderIsxianhuoNew(array('order_id' => $id));
        //取出现在的商品的价格重新计算
        $money = array();
        $accountInfo = $orderModel->getOrderAccount($id);
        $money['order_amount'] = $accountInfo['order_amount'] + $goods_price ;
        $money['money_paid'] = 0 ;
        $money['goods_amount'] = $accountInfo['goods_amount'] + $goods_price;
        $money['money_unpaid'] = $accountInfo['money_unpaid'] + $goods_price;
        $money['shipping_fee'] = $accountInfo['shipping_fee'];
        $money['order_id'] = $id;
        $res = $orderModel->updateOrderAccount($money);
        //修改发票金额
        $Imodel =new AppOrderInvoiceModel(28);
        $Imodel->updateIprice($money['order_amount'],$id);
		
		$orderModel->updateorderiszp($id);
        if($res){
           //删除购物车中选中的数据
           foreach ($cart_ids as $val){
              $cartModel->delete_cart_goods_by_id($val);
           }
            //$cartModel->clear_cart();
            $this->getCartGoods();
            die;
        }else{
            $result['error'] = 1;
            $result['content'] = '操作失败';
        }
        Util::jsonExit($result);
    }
    
    //获取购物车中数据
    function getCartGoods(){
        $cartModel = new AppOrderCartModel(27);
        $cart_goods = $cartModel->get_cart_goods();
        $SalesChannelsModel = new SalesChannelsModel(1);
        $diamondModel = new SelfDiamondModel(19);
 		$is_4c_cart_goods = array();
		$not_4c_cart_goods = array();
		$is_4c_cart_goods3 = array();		
        foreach ($cart_goods as $key=>$val){
           $cart_id = $val['id'];
           $cert_id = $val['zhengshuhao']; 
           $is_4c   = (int)$val['is_4c'];
           $filter_data = empty($val['filter_data'])?array():json_decode($val['filter_data'],true);
     	   if($is_4c){
     	       $val = $diamondModel->selectDiamondInfo("*","cert_id='{$cert_id}'",2);
     	       //print_r($val);
               if(!empty($val)){
                   $val['id']    = $cart_id;
                   $val['is_4c'] = $is_4c;
               }else{
                   continue;
               }
               if($is_4c==1 || $is_4c==2){
                   //查询双十一裸钻价格 begin by gaopeng
                   if($is_4c == 1){
                       $ssyRow = $diamondModel->selectDiamondSSY("*","cert_id='{$cert_id}'",2);
                       if(!empty($ssyRow)){
                           $val['shop_price'] = $ssyRow['special_price'];  
                       }
                   }//查询双十一裸钻价格 end
                   $is_4c_cart_goods[]= $val;
               }else if($is_4c==3){
                   $val['special_price'] = '';
                   if(isset($filter_data['data']['special_price'])){
                       $val['special_price'] = $filter_data['data']['special_price'];
                   }
                   $is_4c_cart_goods3[]=$val;
               }
               //print_r($is_4c_cart_goods);
		   }else{
		       if($val['goods_type'] != 'lz' && !empty($val['department_id'])){
		           $cart_goods[$key]['department_name'] = $SalesChannelsModel->getNameByid($val['department_id']);
				   $val['department_name'] = $cart_goods[$key]['department_name'];
		       }else{
		           $cart_goods[$key]['department_name'] = '-';
				   $val['department_name'] = '-';
		       }
			   $not_4c_cart_goods[] = $val;
		   }
		   
        }
        $this->render('app_order_details_cart_info.html',  array(
                'cart_goods'=>$cart_goods, 
                'is_4c_cart_goods'=>$is_4c_cart_goods,
                'is_4c_cart_goods3'=>$is_4c_cart_goods3,
                'not_4c_cart_goods'=>$not_4c_cart_goods                 
            )
        );
    }

    //删除购物车中的数据
    function deleteCartGoods(){
        $result = array('success' => 1,'error' => '');
        $cartModel = new AppOrderCartModel(28);
        //$id = _Request::getInt('id');
        $cart_ids = _Request::getList('cart_id');

        foreach ($cart_ids as $val){
             $cartModel->delete_cart_goods_by_id($val);
        }

        $this->getCartGoods();
    }
	
	//跟新购物车中的数据
    function refreshCartGoods(){
        
        $result = array('success' => 1,'error' => '');
        $cart_ids = _Request::getList('cart_id');
		foreach ($cart_ids as $id){
			$cartModel =new AppOrderCartModel($id,28);
			$cart_good = $cartModel->getDataObject();//get_cart_goods_by_id($id);
			$allow_update_flag = false;
			//根据4c条件重新搜索新商品
			if (!empty($cart_good['filter_data']) && ($cart_good['is_4c']==2 ||$cart_good['is_4c']==3)){
			    $new_diamond=ApiModel::diamond_api(array('cert_id','status'),array($cart_good['zhengshuhao'],1),'GetDiamondList');
			    if($new_diamond['error']){
			        $filter_data = json_decode($cart_good['filter_data'], true);
			        $new_diamond=ApiModel::diamond_api($filter_data['keys'],$filter_data['vals'],'GetDiamondList');
			        $allow_update_flag = true;
			    }else{
			        $allow_update_flag = false;
			    }
			    
			}			
			
			if ($allow_update_flag == true){
			    $data = current($new_diamond['data']['data']);
			    if($cart_good["goods_id"] != $data['goods_sn']){
			    	$this->calc_dia_channel_price($data);
			        //unset($cart_good["id"]);
			        $new_cart_good["id"] = $cart_good["id"];
			        $new_cart_good["goods_id"]=$data['goods_sn'];
			        $new_cart_good["goods_price"]=$data['shop_price'];
			        $new_cart_good["is_stock_goods"]=$data['good_type'];
			        $new_cart_good["modify_time"]= date("Y-m-d H:i:s");
			        $new_cart_good["cart"]=$data['carat'];
			        $new_cart_good["cut"]=$data['cut'];
			        $new_cart_good["clarity"]=$data['clarity'];
			        $new_cart_good["color"]=$data['color'];
			        $new_cart_good["goods_type"]='lz';
			        $new_cart_good["kuan_sn"]=$data['kuan_sn'];
			        $new_cart_good["zhengshuhao"]=$data['cert_id'];
			        $new_cart_good["goods_name"]=$data["carat"]."克拉/ct ".$data["clarity"]."净度 ".$data["color"]."颜色 ".$data["cut"]."切工";
			
			        //如果商品在购物车中不重复，则更新为新的
			        /* $cart_goods = $cartModel->get_cart_goods("id,goods_id");
			        $need_update_flag = true;
			
			        foreach($cart_goods as $cart_good)
			        {
			            if($cart_good['goods_id'] == $new_cart_good["goods_id"]){
			                $need_update_flag =false;//已经存在同样商品则不需要更新了
			                if ($cart_good['id'] != $new_cart_good["id"]){
			                    $cartModel->delete_cart_goods_by_id($id);//已经存在这个商品在购物车里，而且不是自己这一条则删除
			                }
			
			            }
			        }
			        if ($need_update_flag){
			            $cartModel->update_cart_by_id($new_cart_good,$cart_good);
			        } */
			        $cartModel->update_cart_by_id($new_cart_good,$cart_good);
			    }
			}else{
			    //$cartModel->delete_cart_goods_by_id($id);//没有这样匹配条件的则进行删除
			}
		}
        $this->getCartGoods();
    }

	/**
     * 验证优惠券是否是有效
     * @param type $coupon_code
     * @return type
     */
    public function checkCouponCode($coupon_code,$is_jax=0) {
        $policyModel = new ApiSalePolicyModel();
        if($is_jax){
            return $policyModel->checkCouponCode(array('coupon_code'=>$coupon_code));
        }
        if(isset($_REQUEST['coupon_code']) && _Request::getString('coupon_code')){
            Util::jsonExit($policyModel->checkCouponCode(array('coupon_code'=>_Request::getString('coupon_code'))));
		}
	}

	/**
     * 验证代金券是否是有效
     * @param type $coupon_code
     * @return type
     */
    public function checkDaijinquanCode() {
    	$daijinquan_code = _Request::getString('daijinquan_code');
    	$departmentid = _Request::getString('departmentid');
        $detail_id = _Request::getInt('detail_id');
    	if(empty($daijinquan_code)){
    		Util::jsonExit(array('success'=>0,'error'=>'兑换不能为空'));
    	}
        $res = Util::point_api_get_daijinquan($daijinquan_code);

        $orderDetailsModel = new AppOrderDetailsModel(28);
        $order_info = $orderDetailsModel->getOrderInfo($detail_id);  
        $discount_point = $order_info['goods_price'];
        $favorable_price = $order_info['favorable_price'];
        if($order_info['favorable_status'] == 3){
            $discount_point = bcsub($discount_point, $favorable_price, 2);
        }
        if(!empty($res)){
            $res = json_decode($res, true);
            //print_r($res);
            if(is_array($res)){
                if($departmentid != $res['channel_id']) 
                    Util::jsonExit(array('success'=>0,'error'=>'兑换码使用渠道与当前订单渠道不一致!')); 
                if($res['is_used']!=0)
                    Util::jsonExit(array('success'=>0,'error'=>'兑换码已被使用!'));
                if($res['exchange_sn']!=$daijinquan_code) 
                    Util::jsonExit(array('success'=>0,'error'=>'没有找到兑换码信息!')); 
                $discount_point = bcsub($discount_point, $res['exchange_item'], 2);
                $point_string = "预计赠送标准积分：".$discount_point; 
                $res['point_string'] = $point_string; 
                Util::jsonExit(array('success'=>1,'error'=>'','return_msg'=>$res));       
            }else{
            	Util::jsonExit(array('success'=>0,'error'=>'没有找到兑换码信息!'));
            }
        }else
            Util::jsonExit(array('success'=>0,'error'=>'没有找到兑换码信息!'));
    	
        
	}

	/**
     * 验证代金券是否是有效
     * @param type $coupon_code
     * @return type
     */
    public function checkJifenmaCode() {
    	$result = array('success'=>0,'error'=>'');
    	$jifenma_code = _Request::getString('jifenma_code');
    	$departmentid = _Request::getString('departmentid');
    	if(empty($jifenma_code) || empty($departmentid)){
    		$result['error'] = "未传入积分码参数或者渠道参数";
    		Util::jsonExit($result);     		
    	}
    	$point_model = new PointCodeModel(1);
    	$res=$point_model->pageList(array('point_code'=>$jifenma_code));

    	if(!empty($res['data'])){
    		$jifenma_info = $res['data'][0];
    		if($jifenma_info['status']<>0){
	    		$result['error'] = "积分码:".$jifenma_code."已被使用";
	    		Util::jsonExit($result);     		     			
    		}
    		if($jifenma_info['channel_id']<>$departmentid){
	    		$result['error'] = "不符合积分码使用渠道";
	    		Util::jsonExit($result);     		     			
    		}
    		$result['success'] = 1;
    		$result['error'] = $jifenma_info['use_proportion']."%";
            Util::jsonExit($result); 
    	}else{
    		$result['error'] = "未找到积分码信息";
    		Util::jsonExit($result); 
    	}         	
	}


	/**
     * 订单优惠券使用
     * @param type $param
     */
    public function coupon_insert($param) {
        $id = _Post::getInt('id');
        $coupon_code = _Post::getString('coupon_code');
        if(!$coupon_code){
            $result['error'] = '优惠券编号不能为空！';
            Util::jsonExit($result);
        }
        $data = $this->checkCouponCode($coupon_code,1);
        if(!$data){
            $result['error'] = '优惠券编号不合法！';
            Util::jsonExit($result);
        }

        if($id<1){
            $result['error'] = '没有该订单号！';
            Util::jsonExit($result);
        }
        $coupon_price = $data['coupon_price'];

        $orderDetailsModel = new AppOrderDetailsModel(28);
        $res = $orderDetailsModel->updateAccountInfo(array('order_id'=>$id,'coupon_price'=>$coupon_price));
        if($res){
            $apiPolicyModel = new ApiSalePolicyModel();
            $orderInfoModel = new BaseOrderInfoModel($id,27);
            $apiPolicyModel->updateCouponInfo(array('coupon_code'=>$coupon_code,'order_sn'=>$orderInfoModel->getValue('order_sn')));
            $result['success'] = 1;
		}
		else
		{
			$result['error'] = '添加失败';
		}
		Util::jsonExit($result);
    }
 

	/**
     * 订单优代金券使用 保存
     * @param type $param
     */
    public function daijinquan_insert($param) {
        $detail_id = _Post::getInt('id');
        $daijinquan_code = _Post::getString('daijinquan_code');
        $departmentid =  _Post::getString('departmentid');
        if(!$daijinquan_code){
            $result['error'] = '代金券兑换码不能为空！';
            Util::jsonExit($result);
        }
        $daijinquan_price =  (float)_Post::getString('daijinquan_price');
        if(empty($daijinquan_price)){
            $result['error'] = '代金券金额不合理！';
            Util::jsonExit($result);
        }        
       
        if(empty($detail_id)){
            $result['error'] = '没有该订单号！';
            Util::jsonExit($result);
        }

        $res = Util::point_api_get_daijinquan($daijinquan_code);
        if(!empty($res)){
            $res = json_decode($res, true);
            //print_r($res);
            if(is_array($res)){
                if($departmentid != $res['channel_id']){ 
                    $result['error'] = '兑换码使用渠道与当前订单渠道不一致!';
                    Util::jsonExit($result);
                }
                if($res['is_used']!=0){
                    $result['error'] = '兑换码已被使用!';
                    Util::jsonExit($result);
                }                    
                if($res['exchange_sn']!=$daijinquan_code){
                    $result['error'] = '没有找到兑换码信息!';
                    Util::jsonExit($result);                	
                }                 
            }else{
            	Util::jsonExit(array('success'=>0,'error'=>'没有找到兑换码信息!'));
            }
        }else{
            $result['error'] = '没有找到兑换码信息!';
            Util::jsonExit($result);        	
        }       

        $orderDetailsModel = new AppOrderDetailsModel(28);
        $res = $orderDetailsModel->updateDaijinquanAccount(array('detail_id'=>$detail_id,'daijinquan_code'=>$daijinquan_code,'daijinquan_price'=>$daijinquan_price));
        if($res===true){
            $bespoke_sn = 0 ;
            $order_info = $orderDetailsModel->getOrderInfo($detail_id);
            if(!empty($order_info['bespoke_id'])){
                $bespokeInfo = $orderDetailsModel->getIdBespokeInfo($order_info['bespoke_id']);
                $bespoke_sn = $bespokeInfo['bespoke_sn'];
            }

            $update_daijinquan_status_data = array('used_time'=>date('Y-m-d H:i:s') ,'order_sn'=> $order_info['order_sn'],'bespoke_sn'=>$bespoke_sn ,'is_used'=>1,'daijinquan_code'=>$daijinquan_code);
            //print_r($update_daijinquan_status_data );
            Util::point_api_update_daijinquan($update_daijinquan_status_data);
            // 更新积分
            try {
                $pointRules = Util::point_api_get_config($order_info['department_id'], $order_info['mobile']);
            }
            catch (Exception $e) {
                //无法确认积分规则，则暂时不更新，由最终赠送时再在处理
            }
            if (!empty($pointRules) && $pointRules['is_enable_point']) {
                $pointModel = new SelfModel(27);
                $pointModel->update_orderdetail_point($detail_id, $pointRules);
            }
            $result['success'] = 1;
		}
		else
		{
			$result['error'] = $res;
		}
		Util::jsonExit($result);
    }

	/**
     * 订单积分码 保存
     * @param type $param
     */
    public function jifenma_insert($param) {
        $detail_id = _Post::getInt('id');
        $jifenma_code = _Post::getString('jifenma_code');
        if(!$jifenma_code){
            $result['error'] = '积分码不能为空！';
            Util::jsonExit($result);
        }
        $jifenma_point =  (float)_Post::getString('jifenma_point');
        if(empty($jifenma_point)){
            $result['error'] = '赠送积分不能为空！';
            Util::jsonExit($result);
        }        
        if(empty($detail_id)){
            $result['error'] = '没有该订单号！';
            Util::jsonExit($result);
        }
       

        $orderDetailsModel = new AppOrderDetailsModel(28);
        $res = $orderDetailsModel->updateJifenma(array('detail_id'=>$detail_id,'jifenma_code'=>$jifenma_code,'jifenma_point'=>$jifenma_point));
        //$orderDetailsModel->update_orderdetail_point($detail_id);
        if($res===true){
            $result['success'] = 1;
		}
		else
		{
			$result['error'] = $res;
		}
		Util::jsonExit($result);
    }


    /**
	 * editorder，渲染查看页面
	 */
	public function editOrderGoods ($params)
	{
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
            die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        } 		    
		$result = array('success' => 0,'error' => '','title'=>'修改商品');
		$goodsAttrModel = new GoodsAttributeModel(17);

		$goodsAttrs['clarity'] = $goodsAttrModel->getClarityList();
		$goodsAttrs['color'] = $goodsAttrModel->getColorList();
		$goodsAttrs['caizhi'] = $goodsAttrModel->getCaizhiList();
		$goodsAttrs['jinse'] = $goodsAttrModel->getJinseList();
		$goodsAttrs['facework'] = $goodsAttrModel->getFaceworkList();
		$goodsAttrs['xiangqian'] = $goodsAttrModel->getXiangqianList();
		$goodsAttrs['buchanType'] = $goodsAttrModel->getBuchanTypeList();
		$goodsAttrs['cert'] = $goodsAttrModel->getCertList();
		$goodsAttrs['peishi'] = array(1=>"是",0=>'否');




		$id = _Request::getInt('id',0);
		$newmodel =  new AppOrderDetailsModel($id,27);
		//$where['id']=$id;
		$orderDetail = $newmodel->getDataObject();
        //print_r($orderDetail);
		if(empty($orderDetail)){
		   $result['content'] = "商品信息不存在！";
           Util::jsonExit($result);
		}
		
		$order_id = $orderDetail['order_id'];
		//获取订单基本信息
		$orderModel= new BaseOrderInfoModel(27);
		$order_info = $orderModel->getOrderInfoById($order_id);
		if(empty($order_info)){
		    $result['content'] = "订单信息查询失败！";
		    Util::jsonExit($result);
		}
		$department_id = $order_info['department_id'];
		$channel_class = 0;
	    //获取销售渠道基本信息（主要获取销售渠道类型，线下线上）
        $salesChannelsModel = new SalesChannelsModel(1);
        $salesChannelsInfo = $salesChannelsModel->getSalesChannelsInfo("id,channel_class",array('id'=>$department_id));
        if(isset($salesChannelsInfo[0]['id'])){
            $channel_class = $salesChannelsInfo[0]['channel_class'];
        }
		$orderDetail['caizhi'] = strtoupper($orderDetail['caizhi']);
		$is_xianhuo = empty($orderDetail['is_stock_goods'])?0:1;
		$goods_type = $orderDetail['goods_type'];

		//获取商品款式属性维护信息
		$apiStyle = new ApiStyleModel();
		$goods_attr = $apiStyle->GetStyleAttribute(array('style_sn'=>$orderDetail['goods_sn']));
		
		$product_type = '';//产品线
		$style_type =''; //款式分类
		$styleInfo=$apiStyle->getStyleInfo($orderDetail['goods_sn']);
	    if(!empty($styleInfo['data']) && is_array($styleInfo['data'])){
            $styleInfo = $styleInfo['data'];
            $product_type = $styleInfo['product_type'];
            $style_type   = $styleInfo['style_type'];
        }
		
        $edits = array();//可编辑的表单元素
        //针对裸钻处理
        if($goods_type =='lz'){            
            $edits = array('xiangqian');
        }else{
            //现货、期货处理
            if($is_xianhuo){
                 //根据商品货号获取库房商品单条记录（主要tuo_type）
                 $apiWarehourse = new ApiWarehouseModel();
                 $where = array('goods_id'=>$orderDetail['goods_id']);
                 $WHGoods = $apiWarehourse->getWarehouseGoodsInfo($where);
                 $WHGoods = !empty($WHGoods['data']['data'])?$WHGoods['data']['data']:array();
                //$cp_type = 1成品，2空托女戒，3空托 ， false不存在
                 $cp_type = isset($WHGoods['tuo_type'])?(int)$WHGoods['tuo_type']:false;
                 $cert = isset($WHGoods['zhengshuleibie'])?$WHGoods['zhengshuleibie']:'';//证书类型
                 if($cp_type == 1){
                     //现货成品，只有指圈，刻字，备注是可以编辑
                     $edits =array('zhiquan','kezi');
                     if(empty($orderDetail['xiangqian'])){
                         $orderDetail['xiangqian']='工厂配钻，工厂镶嵌';
                     }
                 }else if($cp_type > 1){
                     //现货空拖，只有石重，颜色，净度，证书号，指圈，刻字，备注，镶嵌要求，表面工艺是可以编辑
                     $edits =array('cart','color','clarity','zhengshuhao','zhiquan','kezi','xiangqian','face_work','info');
                 }else{
                     //未知
                 }
            }else{
                 $jinse = $newmodel->getJinse(false);
                 //如果是期货(定制)，就当空托来处理 ？【待产品确定】
                 $cp_type = 3;
                 //只有石重，颜色，净度，证书号，指圈，刻字，备注，镶嵌要求，表面工艺是可以编辑
                 $edits =array('cart','color','clarity','zhengshuhao','zhiquan','kezi','xiangqian','face_work','info');
                     
            }            
        }
        //表面工艺，根据款式维护属性控制
         if(!empty($goods_attr['data'][27]['value'])){
            $face_work_split = explode(',',$goods_attr['data'][27]['value']);
            $face_work = array();
            foreach($face_work_split as $vo){
                if(trim($vo)!=''){
                    $face_work[$vo]=$vo;
                }
            }

             $goodsAttrs['facework'] = $face_work;
        }

        //如果这件货是空托，则镶嵌要求不能选成品
        $tuo_type = $orderDetail['tuo_type'];
        if($tuo_type != '成品'){
            unset($goodsAttrs['xiangqian']['成品']);
        }

        if($department_id==108 || $channel_class==1 || ($channel_class==2 && ($product_type!=6 || $style_type!=2))){
             //线上销售渠道，开放所有字段编辑权限(当前共13个字段),如果有新增字段，请把name值加入edits
             $edits =array('cart','color','jinse','caizhi','jinzhong','xiangkou','clarity','zhengshuhao','zhiquan','kezi','xiangqian','face_work','info');
        }
        //指圈修改权限判断
        if(in_array('zhiquan',$edits) && $is_xianhuo){
            if(isset($goods_attr['data'][31]['value'])&& trim($goods_attr['data'][31]['value']) == "不可改圈"){
                unset($edits[array_search("zhiquan",$edits)]);//删除指圈修改权限
            }
        }
        if($is_xianhuo!=1 && $goods_type!='lz'){
            $edits[] = "cert";//期货非裸钻  证书类型 可编辑
        }
        //成品定制商品编辑权限
        if( $orderDetail['is_cpdz']=='1'){
           $edits =array('zhiquan','kezi','face_work','info');
        }
        //替换刻字串中的特殊字符转换；
        $keziModel = new KeziModel();
        $kezi = $keziModel->retWord($orderDetail['kezi']);
        $kezi = $this->replaceTsKezi($kezi);
        $orderDetail['keziShow'] = $kezi;
		$result['content'] = $this->fetch('app_order_details_goods_edit.html',array(
				'view'=>new AppOrderDetailsView($newmodel),
		        'orderDetail'=>$orderDetail,
		        'goodsAttrs'=>$goodsAttrs,		        
		        'edits'=>$edits
		));
		$result['title'] = '商品信息修改';
		Util::jsonExit($result);
	}
    
    /**
     * 添加镶口匹配主石重规则
     * @param type $stone
     * @return string
     */
    private function GetStone($xiangkou,$stone) {
        $stone = $stone * 1000;
        $xiangkou = $xiangkou * 1000;
        $stone = intval($stone);
        if(empty($xiangkou)){
            if (($stone >= 0 && $stone < 100) || $stone > 10000) {
                return true;
            }
        }
        if($xiangkou==100){
            if ($stone >= 100 && $stone <= 150) {
                return true;
            }
        }
        if(abs($stone-$xiangkou)<=50){
            return true;
        }
        return false;        
    }
    

    /**
	 *	updateOrder，渲染查看页面
	 */
    public function updateOrderGoods ($params)
    {
        $result = array('success' => 0,'error' => '');
        $id = _Request::getInt('id');
        $newdo=array(
            'id'=>_Request::get('id'),
            'cart'=>_Request::get('cart'),
            'zhushi_num'=>_Request::get('zhushi_num'),
            'clarity'=>_Request::get('clarity'),
            'caizhi'=>_Request::get('caizhi'),
            'color'=>_Request::get('color'),
            'cert'=>_REQUEST::get('cert'),
            'zhengshuhao'=>_Request::get('zhengshuhao'),
            'face_work'=>_Request::get('face_work'),
            'jinse'=>_Request::get('jinse'),
            'jinzhong'=>_Request::get('jinzhong'),
            'zhiquan'=>_Request::get('zhiquan'),
            'kezi'=>_Request::get('kezi'),
            'xiangqian'=>_Request::get('xiangqian'),
            'xiangkou'=>_Request::getFloat('xiangkou'),
        );
        //校验基本属性
        $checkData = $newdo ;
        
        $edits = _Post::getList('edits');//获取可编辑字段
        //删除不可编辑元素
        foreach($newdo as $key =>$vo){
            if(!in_array($key,$edits)){
                unset($newdo[$key]);
                unset($_POST[$key]);
            }
        }
        $newdo['id'] = $id;
        
        $goods_type = _Request::get('goods_type');
        
        //订单商品信息
        $newmodel =  new AppOrderDetailsModel($id,28);
        $olddo = $newmodel->getDataObject();
        if(empty($olddo)){
            $result['error'] = "商品信息不存在！";
            Util::jsonExit($result);
        }

        //boss_1733货品类型是非裸钻并且是期货
        //证书号为空，证书类型不能选择EGL
        //如果证书类型选择了EGL ，在保存时，提示“EGL钻石只能配现货”
        //var_dump($olddo);die;
        $WarehouseGoodsModel = new SelfWarehouseGoodsModel(21);
        if($olddo['is_stock_goods'] == 0 && $olddo['goods_type'] != 'lz'){
            //var_dump($checkData);die;
            if(empty($checkData['zhengshuhao']) && in_array($checkData['cert'],array('EGL','AGL'))){
                //var_dump($olddo);die;
                $result['error'] = "EGL/AGL钻石只能配现货";
                Util::jsonExit($result);
            }
            if(!empty($checkData['zhengshuhao'])){
                $check_cert = $WarehouseGoodsModel->getCertByZhengshuhao($checkData['zhengshuhao']);
                if(empty($check_cert) && ($checkData['cert'] == 'EGL' || $checkData['cert'] == 'AGL')){
                    $result['error'] = "EGL/AGL钻石只能配现货";
                    Util::jsonExit($result);
                }
                //var_dump(!empty($check_cert),$check_cert['zhengshuleibie'] != 'EGL',$checkData['cert'] == 'EGL');die;
                if(!empty($check_cert) && !in_array($check_cert['zhengshuleibie'],array('EGL','AGL')) && in_array($checkData['cert'],array('EGL','AGL'))){
                    $result['error'] = "EGL/AGL钻石只能配现货";
                    Util::jsonExit($result);
                }
            }
        }
        
        $detail_id  = $olddo['id'];
        $is_xianhuo = $olddo['is_stock_goods'] == 1 ? 1 : 0;//现货期货判断
        $goods_id   = $olddo['goods_id'];
        $goods_type = $olddo['goods_type'];

        foreach($checkData as $key =>$vo){
            if(($is_xianhuo==0 && $key=="zhushi_num") || $key=="xiangqian"){
                continue;
            }
            if(!in_array($key,$edits)){
                unset($checkData[$key]);
            }
        }
        $checkData['is_stock_goods'] = $is_xianhuo;
        $res = $this->checkOrderGoodsData($checkData);
        if($res['success']==0){
            $result['error'] = $res['error'];
            Util::jsonExit($result);
        }
        //if($cat_type == 2){//戒指
        if(isset($_POST['face_work'])&&empty($newdo['face_work'])){
            $result['error'] = "请设置表面工艺";
            Util::jsonExit($result);
        }
        if(isset($_POST['xiangqian'])){
            if(empty($newdo['xiangqian'])){
               $result['error'] = "请选择镶嵌要求";
               Util::jsonExit($result);
            }elseif($goods_type!='lz'&&!preg_match("/成品|不需工厂/is",$newdo['xiangqian'])){
               if(empty($newdo['cart'])||empty($newdo['clarity'])||empty($newdo['color'])) {
                   $result['error'] = "选择工厂镶嵌时,石重、净度、颜色必填！";
                   Util::jsonExit($result);
               }else if(preg_match("/4C/is",$newdo['xiangqian'])){
                    if (empty($newdo['zhengshuhao'])) {
                        $result['error'] = "证书号不 能为空！仅针对镶嵌方式选择【镶嵌4C裸钻】";
                        Util::jsonExit($result);
                    }
                    //如果 镶嵌方式为 [镶嵌4C裸钻]且为定制空托，则 is_peishi=2;
                    $newdo['is_peishi'] = 2;                    
                    /* $res = $newmodel->checkCertIdHasBindFor4C($newdo['zhengshuhao'],$detail_id);
                    if(is_array($res) && !empty($res)){
                        $result['error'] = "证书号已被其它商品绑定(订单号：{$res['order_sn']},货号{$res['goods_id']})";
                        Util::jsonExit($result);
                    } */
                    //验证4C空托配石的证书号必须在裸钻库存在
                    $apiDiamondModel = new ApiDiamondModel();
                    $res = $apiDiamondModel->getDiamondInfoByCertId($newdo['zhengshuhao']);
                    if($res['error']==1){
                        $result['error'] = "证书号【{$newdo['zhengshuhao']}】不存在！镶嵌方式选择【镶嵌4C裸钻】后，证书号必须在裸钻库存在！";
                        Util::jsonExit($result);
                    }
                    
               }else{
                   $newdo['is_peishi'] = 0;

               }
               //虚拟货号下单 当金托类型为空托时，镶嵌方式不能选【工厂配钻，工厂镶嵌】
               if(strpos($goods_id,'-')!==false){
                   if($olddo['tuo_type']=='空托'){
					    if($newdo['xiangqian']=="工厂配钻，工厂镶嵌"){
					        $result['error'] = "当金托类型为空托时，镶嵌方式不能选【工厂配钻，工厂镶嵌】！";
					        Util::jsonExit($result);
					    }
                   }
               }               
            }
        }
        //}
        //如果是裸钻 且支4C配钻，验证证书号是否存在
        if($goods_type=='lz' && $olddo['is_peishi']==1){
            if(empty($newdo['zhengshuhao'])){
                $result['error'] = "证书号不能为空！当裸钻支持4C配钻时，证书号必填！";
                Util::jsonExit($result);
            }else{
                $apiDiamondModel = new ApiDiamondModel();
                $res = $apiDiamondModel->getDiamondInfoByCertId($newdo['zhengshuhao']);
                if($res['error']==1){
                    $result['error'] = "证书号{$newdo['zhengshuhao']}在裸钻库不存在！当裸钻支持4C配钻时，证书号必须在裸钻库存在";
                    Util::jsonExit($result);
                }
            }
            
        }

        //刻字验证，检查刻字是否符合条件
        if(isset($_POST['kezi'])){

            $newdo['kezi'] = $this->checkKeziStr($olddo['goods_sn'],$_POST['kezi']);
        }
        //验证指圈 begin
        if(isset($_POST['zhiquan'])){

            $apiStyle = new ApiStyleModel();
            $ret = $apiStyle->GetStyleAttribute(array('style_sn'=>$olddo['goods_sn']));
            $attr = $ret['error'] ==1 ?array():$ret['data'];

            $zhiquan = isset($newdo['zhiquan'])?$newdo['zhiquan']:'';

            $zhiquan_old = '';
            $goods_id_arr = explode('-',$olddo['goods_id']);            
            if(count($goods_id_arr)==5){
                $zhiquan_old = isset($goods_id_arr[4])?$goods_id_arr[4]:$zhiquan_old;
            }else{
                $apiWarehouseModel = new ApiWarehouseModel();
                $goods_info = $apiWarehouseModel->getWarehouseGoodsInfo(array('goods_id'=>$olddo['goods_id']));
                $goods_info = empty($goods_info['data']['data'])?array():$goods_info['data']['data'];
                if(!empty($goods_info)){
                    $zhiquan_old = $goods_info['shoucun'];
                }
            }
            //获取指圈范围,款式库有指定大小的情况
            if(!empty($zhiquan_old)){
                if(count($goods_id_arr)==5){
                    if(abs($zhiquan_old-$zhiquan)>0.5){
                        $result['error'] = "指圈大小不合要求，参考范围:".($zhiquan_old-0.5).'-'.($zhiquan_old+0.5);
                        Util::jsonExit($result);
                    }
                }else if(!empty($attr[31]) && $attr[31]['value'] != ""){
                    $str = $attr[31]['value'];
                    if(preg_match('/可增([0-9]+?)个手寸/is',$str,$arr)){
                        if($zhiquan-$zhiquan_old>$arr[1] || $zhiquan-$zhiquan_old<0){
                            $result['error'] = "款式库中已设置：指圈只可以增加".$arr[1].'（温馨提示原始指圈为'.$zhiquan_old.')';
                            Util::jsonExit($result);
                        }
                    }else if(preg_match('/可增减([0-9]+?)个手寸/is',$str,$arr)){
                        if(abs($zhiquan_old-$zhiquan)>$arr[1]){
                            $result['error'] = "款式库中已设置：指圈只可以增减".$arr[1].'（温馨提示原始指圈为'.$zhiquan_old.')';
                            Util::jsonExit($result);
                        }
                    }else if(preg_match('/不可改圈/is',$str)){
                        $result['error'] = "款式库中已设置：".$str;
                        Util::jsonExit($result);
                    }
                }else{       
                    if(abs($zhiquan_old-$zhiquan)>2){
                        $result['error'] = "指圈大小不合要求，参考范围:".($zhiquan_old-2).'-'.($zhiquan_old+2);
                        Util::jsonExit($result);
                    }
    
                }
            }
           
    
        }//验证指圈 end
		$newdo['is_stock_goods'] = $is_xianhuo;
        $res = $newmodel->saveData($newdo,$olddo);    
        if($res!== false) {
            $result['error'] = '操作成功！';
            $result['success'] = 1;
        }else{
            $result['error'] = '操作失败';
        }
        Util::jsonExit($result);    
    }

    public function getKeziInfo(){
    	$style_sn = _Request::get('style_sn');
        $apiStyleModel = new ApiStyleModel();
        $result = $apiStyleModel->getStyleInfo($style_sn);
        $style_type = "";
        if($result['error'] == 0){
            $style_type = isset($result['data']['style_type'])?$result['data']['style_type']:"";
        }
        $where = array('style_type'=>$style_type);
        $keziModel = new KeziModel();
        $a = $keziModel->getKezi($where);
        echo 'jsonpcallback('.json_encode($a).')';
        exit;
    }

    //仓储的数据格式和款式的不一样需要转换一下
    public function getWarehouseData($info) {
        $new_goods_info = array();
        //$info['caizhi']="18K玫瑰金";
         if(!empty($info['caizhi'])){
            preg_match('/[0-9a-z]+/i',$info['caizhi'],$caizhi);
            $info['jinse'] = "";
            if($caizhi){
                $info['jinse'] = substr($info['caizhi'],strlen($caizhi[0]));
                $info['caizhi'] = strtoupper($caizhi[0]);
            }
        }

        $new_goods_info['goods_name'] = $info['goods_name'];
        $new_goods_info['goods_sn'] = $info['goods_sn'];
        $new_goods_info['cart'] = $info['zuanshidaxiao'];
        $new_goods_info['zhushi_num'] = $info['zhushilishu'];
        $new_goods_info['clarity'] = strtoupper($info['jingdu']);
        $new_goods_info['color'] = strtoupper($info['yanse']);
        $new_goods_info['zhengshuhao'] = $info['zhengshuhao'];
        $new_goods_info['caizhi'] = $info['caizhi'];
        $new_goods_info['zuanshidaxiao'] = $info['zuanshidaxiao'];
        $new_goods_info['jinse'] = $info['jinse'];
        $new_goods_info['jinzhong'] = $info['jinzhong'];
        $new_goods_info['zhiquan'] = $info['shoucun'];
        $new_goods_info['xiangkou'] = $info['jietuoxiangkou'];
        $new_goods_info['favorable_price'] = 0;
        $new_goods_info['face_work'] = '';
        $new_goods_info['tuo_type'] = $info['tuo_type'];
        $new_goods_info['cert'] = $info['zhengshuleibie'];
        return $new_goods_info;
    }

    /*
     * 修改订单价格
     * luna
     */
    public function updateOrderAcount($data,$order_id) {
        $detail_goods_number = $data['goods_number'];
        $del_goods_price = $data['del_goods_price'];
        $del_favorable_price = $data['del_favorable_price'];
        $order_account_arr = $data['order_account'];
      //  var_dump($order_account_arr,$data);

        $money = array();
        $account_id = $order_account_arr['id'];
        $accountModel = new AppOrderAccountModel($account_id,28);
        //如果商品都删掉了，那么订单是所有价格全部清0
        if($detail_goods_number ==0){
           // $money['order_id'] = $order_id;
            $money['id'] = $account_id;
            $money['order_amount'] = 0;//订单总金额
            $money['goods_amount'] = 0;//商品价格
            $money['money_paid'] = 0;//已付
            $money['money_unpaid'] = 0;//未付
            $money['coupon_price'] = 0;//订单优惠
            $money['favorable_price'] = 0;//商品优惠
        }else{
           // $money['order_id'] = $order_id;
            $money['id'] = $account_id;
            $money['order_amount'] = $order_account_arr['order_amount'] - $del_goods_price + $del_favorable_price;//订单总金额
            $money['goods_amount'] =$order_account_arr['goods_amount'] - $del_goods_price;//商品价格
            $money['money_unpaid'] = $order_account_arr['money_unpaid'] - $del_goods_price + $del_favorable_price;//未付
            $money['money_paid'] = $order_account_arr['money_paid'] ;//已付
            $money['coupon_price'] = $order_account_arr['coupon_price'];//订单优惠
            $money['favorable_price'] = $order_account_arr['favorable_price'] - $del_favorable_price;//商品优惠
        }

        //更新订单金额
        if(!empty($money)){
           // $olddo = $accountModel->getDataObject();
            $accountModel->saveData($money, $order_account_arr);
            //发票金额修改
            $oModel = new AppOrderInvoiceModel(28);
            $oModel->updateIprice($money['order_amount'],$order_id);
        }
    }

    /*
     * 计算此订单应减去的金额和优惠
     * luna
     */
    public function calculateDetailGoods($data){
         //删除商品时减去订单金额需要减去删掉的商品
        $detail_info = $data['detail_info'];
        $kuan_sn = $data['kuan_sn'];
        $goods_price = $data['goods_price'];
        $favorable_price = $data['favorable_price'];
        $goods_type = $data['goods_type'];
        $favorable_status = $data['favorable_status'];

        $del_goods_price = 0;
        $del_favorable_price = 0;
        $goods_number = count($detail_info);
        if(!empty($kuan_sn)){
            foreach ($detail_info as $val){
                //当前选中的天生一对和商品中的天生一对款号相同的,因为都删除了，所以价格要一起减去
                if($kuan_sn == $val['kuan_sn']){
                    $del_goods_price +=$val['goods_price'];
                    $del_favorable_price = $val['favorable_price'];
                    $goods_number--;
                }
            }
        }else{
        	if($goods_type =='lz'){//裸钻的优惠不需要审核
        		$del_goods_price = $goods_price;
        		$del_favorable_price = $favorable_price;
        		$goods_number--;
        	}else{//非裸钻的，优惠只有审核通过的才可以
        		$del_goods_price = $goods_price;
        		$del_favorable_price = 0;
        		if($favorable_status == 3){
        			$del_favorable_price = $favorable_price;
        		}

        		$goods_number--;
        	}

        }

        return array('del_goods_price'=>$del_goods_price,'del_favorable_price'=>$del_favorable_price,'goods_number'=>$goods_number);
    }

    /**
     *3、欧版戒刻字要求
     *50位以内的任何字符都可以刻
     *4、  非欧版戒刻字要求
     *（1）最多六位字符(一个汉字也当一个字符，)
     *（2）汉字，数字，字母（支持大小写），标点符号（中英文符号状态下都可以刻），页面显示的特殊符号
     * 标点符号包含：~ • ！@ # $ % ^ & * ( ) _ - + = { }【 】| 、 ： ；“ ”‘’ 《》 ， 。 ？ 、\  /  . < > 空格
     * @hxw
     */
    public function checkKeziStr($style_sn,$kezi)
    {
        $apiStyle = new ApiStyleModel();
        $styleAttrInfo = $apiStyle->GetStyleAttribute(array('style_sn'=>$style_sn));
        $attrinfo = $styleAttrInfo['error'] == 1 ? array() : $styleAttrInfo['data'];
        //刻字验证
        $keziModel = new KeziModel();
        $allkezi = $keziModel->getKeziData();
        //是否欧版戒 92
        if(isset($attrinfo[92]['value']) && !empty($attrinfo[92]['value']) && trim($attrinfo[92]['value'] == '是')){
            $str_count = $keziModel->pdKeziData($kezi,$allkezi,1);
            if($str_count['str_count']>=50){
                $result['error'] = "<span style='color:red';>欧版戒只能刻50位以内的任何字符！<span/>";
                Util::jsonExit($result);
            }
            $kezi = $str_count['kezi'];
        }else{
            $str_count = $keziModel->pdKeziData($kezi,$allkezi);
            if($str_count['str_count']>6){
                $result['error'] = "<span style='color:red';>非欧版戒只能刻最多6位字符！（一个汉字为一个字符）<span/>";
                Util::jsonExit($result);
            }
            if($str_count['err_bd'] != ''){
                $result['error'] = "<span style='color:red';>非欧版戒下列字符不可以刻：".$str_count['err_bd']."<span/>";
                Util::jsonExit($result);
            }
            $kezi = $str_count['kezi'];
        }
        return $kezi;
    }


    /**
     * 	dingzhi
     *  商品：现货转定制
     *  订单没有已配货的才可以商品此可以转成定制商品
     */
    public function dingzhi() {
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
        	die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }        
        $result = array('success' => 0, 'error' => '');
        
        $ids = _Post::getList('ids');
        if(empty($ids)){
            $result['error'] = '请选择需要转定制的商品!';
            Util::jsonExit($result);
        }
        $order_id = 0;
        $channel  = 0;
        $apiSalepolicy = new ApiSalePolicyModel();
        $apiWarehouse = new ApiWarehouseModel();
        $baseOrderModel = null;
        //验证是否符合转定制条件
        foreach($ids as $id){
            $model = new AppOrderDetailsModel($id, 28);
            $detailGoods = $model->getDataObject();     
            //var_dump($detailGoods);die;      
            $delivery_status1 = $detailGoods['delivery_status'];
            $cert = $detailGoods['cert'];
            $goods_id=$detailGoods['goods_id'];
            $style = $detailGoods['goods_sn'];
            $face_work = $detailGoods['face_work'];

            //当下期货或者现货转定制时，且款号非DIA，【表面工艺】字段必填
            if($style != "DIA" &&  empty($face_work)){
                $result['error'] ="此货品{$goods_id}表面工艺为空，不能转定制";
                Util::jsonExit($result);
            }

            //天生一对订单明细：如果已经配货，不能转定制
            if($delivery_status1==5){
            	$result['error'] ="此货品{$goods_id}已经配货，不能转定制";
            	Util::jsonExit($result);
            }


            //如果现货证书类型为EGL,无法转定制，提示“成品定制不支持镶嵌EGL裸钻”
            if($cert=='EGL' || $cert=='AGL'){
                $result['error'] ="此货品{$goods_id}成品定制不支持镶嵌EGL/AGL裸钻";
                Util::jsonExit($result);
            }
            if(!$order_id){
                $order_id = $detailGoods['order_id'];
                $baseOrderModel = new BaseOrderInfoModel($order_id,28);
                $order_info = $baseOrderModel->getDataObject();
                $delivery_status = $order_info['delivery_status'];//配货状态
                $order_status = $order_info['order_status'];//订单状态
                $channel= $order_info['department_id'];
                if($delivery_status == 5){//已配货时不可以修改的
                    $result['error'] = '此订单配货状态为：已配货！请联系仓储人员取消此订单配货';
                    Util::jsonExit($result);
                }
                if($order_status== 4){
                    $result['error'] = '此订单已经关闭，不可以操作！';
                    Util::jsonExit($result);
                }
                
            }            
           
            if($detailGoods['goods_type'] != 'lz' && mb_strtoupper($detailGoods['goods_sn']) != 'DIA'){
				$goods_sn = $detailGoods['goods_sn'];
                $check_status = $model->getCheckStatus($goods_sn);
                if($check_status != 3){
                	$result['error'] = '款号为'.$goods_sn.'的款式没有审核通过，不可以操作！';
                	Util::jsonExit($result);
                }
            }
            
            $ck_result = $apiSalepolicy->checkGoodsDingzhiCastable($detailGoods, $channel);
            if (!$ck_result['flag']){
                $result['error'] = $ck_result['error'];
                Util::jsonExit($result);
            }
        }
        //开始转定制
        $error = array();
        foreach($ids as $id){
            $model = new AppOrderDetailsModel($id, 28);
            $detailGoods = $model->getDataObject();
            $goods_id = $detailGoods['goods_id'];
            //期货单直接过，不用转定制
            if(!$detailGoods['is_stock_goods']){
                $baseOrderModel->setValue('is_xianhuo', 0);//订单状态变成：期货单
                $baseOrderModel->save();
                continue;
            }

            //判断是现货钻 1、期货钻 2 boss_1287
            $goods_type = $detailGoods['goods_type'];
            if($goods_type == 'qiban' || $goods_type == 'caizuan_goods'){//起版、彩钻默认是期货
                $dia_type = 2;
            }else{
                if($detailGoods['is_stock_goods'] == 1){//现货
                    $dia_type = 1;
                }elseif($detailGoods['is_stock_goods'] == 0 && $detailGoods['zhengshuhao'] == ''){//期货
                    $dia_type = 1;
                }elseif($detailGoods['is_stock_goods'] == 0 && $detailGoods['zhengshuhao'] != ''){
                    $diamondModel = new SelfDiamondModel(19);
                    $zhengshuhaot = str_replace(array("GIA", "EGL","AGL"), "", $detailGoods['zhengshuhao']);
                    $check_dia = $diamondModel->getDiamondInfoByCertId($zhengshuhaot);
                    if(!empty($check_dia) && isset($check_dia['good_type'])){
                        if($check_dia['good_type'] == 1){
                            $dia_type = 1;
                        }elseif($check_dia['good_type'] == 2){
                            $dia_type = 2;
                        }else{
                            $dia_type = 0;
                        }
                    }else{
                        $dia_type = 1;
                    }
                }else{
                    $dia_type = 0;
                }//判断是现货钻 1、期货钻 2
            }
            if($detailGoods['xiangqian']=='成品'){
            	$model->setValue('xiangqian','工厂配钻，工厂镶嵌');
            }
            $model->setValue('dia_type', $dia_type);//更改钻石类型
            $model->setValue('is_stock_goods', 0);//商品变成定制
            $model->setValue('goods_id', '');//把现货给删了
            if($goods_type<>'lz')
                $model->setValue('zhengshuhao', '');//把证书号清空
            
            //天生一对订单明细配货状态更新
            if($detailGoods['delivery_status']==2 && ($detailGoods['buchan_status']!=9 && $detailGoods['buchan_status']!=11)){
            	$model->setValue('delivery_status', 1);
            }
            if ($model->save() !== false && $baseOrderModel) {
                //同时修改订单信息
                $baseOrderModel->setValue('delivery_status', 1);//配货状态：变成未操作
                $baseOrderModel->setValue('is_xianhuo', 0);//订单状态变成：期货单
                $baseOrderModel->save();
                //解绑上架
                $baseOrderModel->Bindxiajia($id,array('bind_type'=>2,'is_sale'=>1));
            
                //解绑下架
                $reat = $apiWarehouse->BindGoodsInfoByGoodsId(array('order_goods_id'=>$id,'goods_id'=>$detailGoods['goods_id'],'bind_type'=>2));
                $info = array();
                $info[0]['is_sale'] = 1;
                $info[0]['is_valid'] = 2;
                $info[0]['goods_id'] =$detailGoods['goods_id'];
                $apiSalepolicy->UpdateAppPayDetail($info);
                // 添加订单日志
                $order_info = $baseOrderModel->getDataObject();
                $this->activeLog($order_info, '订单'.$order_info['order_sn'].'详情：现货'.$goods_id.'转期货');
            } else {
                $error[]  = $goods_id.'转定制失败';
                Util::jsonExit($result);
            }        
        }
        if(empty($error)){
            $result['success'] = 1;
            $result['content'] = "操作成功！";
        }else{
            $result['error'] = '操作失败，请重新尝试！<hr>【'.implode('】【',$error).'】';
        }
        Util::jsonExit($result);                
    }



    /**
     * 	商品：期货转现货
     *  商品如果已经允许布产了，那么生成布产单，就需要取消布产单
     *  同时此订单中如果所有商品都是现货，且已付款，要修改订单的配货状态：变成允许配货
     *
     */
    /*
    public function xianhuo() {
        $id = _Post::get('id');
        $result = array('success' => 0, 'error' => '');
        $model = new AppOrderDetailsModel($id, 28);
        $detailGoods = $model->getDataObject();
        $order_id = $detailGoods['order_id'];
        $buchan_stauts = $detailGoods['buchan_status'];
        $goods_type = $detailGoods['goods_type'];
        if($goods_type == 'qb'){
            $result['error'] = "此商品是起版商品不可以操作！";
            Util::jsonExit($result);
        }

        $arr_buchan = array(2=>'待分配',3=>'已分配');
        if($buchan_stauts == 2 | $buchan_stauts == 3){
            $info = $arr_buchan[$buchan_stauts];//提示信息
            $result['error'] = '此商品布产状态为：'.$info."!请先去布产列表取消布产单，再来操作！";
            Util::jsonExit($result);
        }
        //获取订单数据
        $baseOrderModel = new BaseOrderInfoModel($order_id,28);
        $order_info = $baseOrderModel->getDataObject();
        $order_pay_status = $order_info['order_pay_status'];//支付状态
        $order_status = $order_info['order_status'];//订单状态
        $order_buchan_status = $order_info['buchan_status'];//订单状态
        if($order_buchan_status ==2){//
            $result['error'] = "此订单布产状态为：已布产!请联系运营人员先去布产列表取消布产单，再来操作！";
            Util::jsonExit($result);
        }
        if($order_status== 4){
            $result['error'] = '此订单已经关闭，不可以操作！';
            Util::jsonExit($result);
        }
        if($detailGoods['goods_type']=='style_goods'){
            $ApiStyleModel = new ApiStyleModel();
            $getStyleInfo=$ApiStyleModel->getStyleInfo($detailGoods['goods_sn']);
            if(isset($getStyleInfo['data'])&&empty($getStyleInfo['data'])){
                $result['error'] = '该货品不存在！';
                Util::jsonExit($result);            
            }
            if(!empty($getStyleInfo['data']['is_made'])){
                $result['error'] = '该货品不能改为现货！';
                Util::jsonExit($result);            
            }
        }

        //转为现货单
        $model->setValue('is_stock_goods', 1);
        if ($model->save() !== false) {
            //操作订单状态
            //
            if($order_pay_status == 3 || $order_pay_status ==4){//订单已付款或财务备案
                $all_goods = $model->getGoodsByOrderId(array('order_id'=>$order_id));
                $is_xianhuo = 1;//默认现货
                $is_buchan = true;
                foreach ($all_goods as $val){
                    if($val['is_stock_goods'] ==0){
                        $is_xianhuo = 0;
                        if($val['buchan_status']<=9){
                            $is_buchan = false;
                        }
                    }
                }
            }
            $baseOrderModel->setValue('is_xianhuo', $is_xianhuo);//订单状态
            if($is_xianhuo == 1){//现货单
                $baseOrderModel->setValue('delivery_status', 1);//配货状态：变成未操作
            }else{//期货单
                if($is_buchan){//定制单的话，如果期货都已出厂，那么配货状态也需要改变
                     $baseOrderModel->setValue('delivery_status', 1);//配货状态：变成未操作
                }
            }

            $result['success'] = 1;
            Util::jsonExit($result);
        } else {
            $result['error'] = '修改失败';
            Util::jsonExit($result);
        }
    }
*/
    public function EditValenceDelete(){
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
            die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }    	
        $result = array('success' => 0,'error' => '');
        $detail_id = _Request::getInt('id');
        $order_id = _Request::getInt('order_id');
        $det_m=new AppOrderDetailsModel(28);
        $order_m = new BaseOrderInfoModel(28);
        $res = $order_m->getOrderPriceInfo($order_id);
        $detiali = $det_m->getGoodsInfoByDetailsId(array('id'=>$detail_id));
        $detiala = $det_m->getGoodsInfoById($order_id);
        $c=count($detiala);
           $result['content'] = $this->fetch('app_order_details_ede.html',array(
               'view'=>new AppOrderDetailsView(new AppOrderDetailsModel(27)),
               'detail'=>$detiali,
               'detiala'=>$detiala,
               'orderinfo'=>$res,
               'c'=>$c,
           ));
           $result['title'] = '删除货品并合并价格';
           Util::jsonExit($result);

    }



    //删除一件货品并且把这个货品的价格和优惠分摊到其他的商品上去
    public function ValenceDelete(){
        $result = array('success' => 0, 'error' => '');
        $detail_id = _Request::getInt('detail_id');
        $order_id = _Request::getInt('order_id');
        if(empty($detail_id)){
            $result['error'] = '货品不存在';
            Util::jsonExit($result);
        }
        $goods_price= _Request::getList('goods_price');
        $favorable_price= _Request::getList('favorable_price');
        $key = array_keys($goods_price);
        foreach($key as $k=>$v){
            $key[$k]=array();
            $key[$k]['id']=$v;
            $key[$k]['goods_price']=$goods_price[$v];
            $key[$k]['favorable_price']=$favorable_price[$v];
        }

        $det_m= new AppOrderDetailsModel($detail_id,27);
        $Ddata = $det_m->getDataObject();
        $detiala = $det_m->getGoodsInfoById($order_id);
        $c = count($detiala);
        if(!($c>1)){
            $result['error'] = '这个订单不存在两个以上的商品禁止删除合并';
            Util::jsonExit($result);
        }

        $det_m= new AppOrderDetailsModel(28);
        $res =  $det_m->ValenceDelete($key,$detail_id);
        if($res){
            //如果成功就把该货品解绑下架（现货）
            if($Ddata['is_stock_goods']==1&&$Ddata['goods_id']!=''){
                $warehouseModel = new ApiWarehouseModel();
                $salepolicyM = new ApiSalePolicyModel();
                $reat = $warehouseModel->BindGoodsInfoByGoodsId(array('order_goods_id'=>$Ddata['id'],'goods_id'=>$Ddata['goods_id'],'bind_type'=>2));
                $info = array();
                $info[0]['is_sale'] = 1;
                $info[0]['is_valid'] = 2;
                $info[0]['goods_id'] =$Ddata['goods_id'];
                $salepolicyM->UpdateAppPayDetail($info);
            }
            $result['success'] = 1;
            Util::jsonExit($result);
        }
        $result['error'] = '删除失败';
        Util::jsonExit($result);

    }

    //增加商品价格功能页面
    public function RaiseGoodsPrice($params){
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
            die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }     	
        $orderM = new BaseOrderInfoModel(27);
        $acountinfo = $orderM->getOrderAccount(_Request::getInt('order_id'));
        $id = intval($params["id"]);
        $newmodel =  new AppOrderDetailsModel($id,28);
        $OrderDetails = $newmodel->getDataObject();
		/* update by liulinyan 20151009 for BOSS-327
        $is_zp = $OrderDetails['is_zp'];
        if($is_zp==1){
            $result['title'] = '修改商品价格';
            $result['content'] ="赠品无法修改价格!";
            Util::jsonExit($result);
        }*/
        $result['content'] = $this->fetch('app_order_details_rai.html',array(
            'view'=>new AppOrderDetailsView(new AppOrderDetailsModel(_Request::getInt('id'),27)),
            'acountinfo'=>$acountinfo,
        ));
        $result['title'] = '增加货品价格';
        Util::jsonExit($result);
    }
    //功能实现
    public function UpdateGoodsPrice(){
        $det['detail_id']=_Request::getInt('detail_id');
        $det['order_id']=_Request::getInt('order_id');
        $det['xzprice']=_Request::getFloat('xzprice');
        $det['goods_name']=_Request::getString('goods_name');
        if($det['xzprice']<0){
            $result['error'] = '只能涨价,请重新填写';
            Util::jsonExit($result);
        }
        $orderM=new BaseOrderInfoModel($det['order_id'],27);
        $order_pay_status = $orderM->getvalue('order_pay_status');
        if($order_pay_status > 1){
            $result['error'] = '订单付款状态：未付款，才可以修改';
            Util::jsonExit($result);
        }
        $DM=new AppOrderDetailsModel(28);
        $res = $DM->XzGoodsPrice($det);
        if($res){
            $orderActionModel = new AppOrderActionModel(27);
            //操作日志
            $ation['order_status'] = $orderM->getvalue('order_status');
            $ation['order_id'] = $det['order_id'];
            $ation['shipping_status'] = $orderM->getvalue('send_good_status');
            $ation['pay_status'] = $orderM->getvalue('order_pay_status');
            $ation['create_user'] = $_SESSION['userName'];
            $ation['create_time'] = date("Y-m-d H:i:s");
            $ation['remark'] = $det['goods_name']."商品金额增加".$det['xzprice']."元";
            $res = $orderActionModel->saveData($ation, array());
            $result['success'] = 1;
            Util::jsonExit($result);
        }
        $result['error'] = '删除失败';
        Util::jsonExit($result);
    }

    /**
     * editorderpriceshow，修改商品价格渲染查看页面
     */
    public function editDetailsPrice ($params)
    {

        $id = intval($params["id"]);
        $newmodel =  new AppOrderDetailsModel($id,28);
        $OrderDetails = $newmodel->getDataObject();

        $order_id = $OrderDetails['order_id'];
        $baseOrderModel = new BaseOrderInfoModel($order_id,28);
        $OrderInfo = $baseOrderModel->getDataObject();

        $order_status = $OrderInfo['order_status'];
        $order_pay_status = $OrderInfo['order_pay_status'];
        $is_zp = $OrderDetails['is_zp'];
        if($is_zp==1){
            $result['title'] = '修改商品价格';
            $result['content'] ="赠品无法修改价格!";
            Util::jsonExit($result);
        }
        if($order_status!=1){
            $result['title'] = '修改商品价格';
            $result['content'] ="订单状态：为待审核，才可以修改";
            Util::jsonExit($result);
        }
        if($order_pay_status!=1){
            $result['title'] = '修改商品价格';
            $result['content'] ="订单付款状态：未付款，才可以修改";
            Util::jsonExit($result);
        }

        $result['content'] = $this->fetch('app_order_details_price_edit.html',array(
                'view'=>new AppOrderDetailsView(new AppOrderDetailsModel($id,27)),
                'orderdetails'=>$OrderDetails
        ));
        $result['title'] = '修改商品价格';
        Util::jsonExit($result);
    }

    /**
     * editorderprice，修改商品价格
     */
    public function updateOrderDetailsPrice ($params)
    {
        $result = array('success' => 0,'error' =>'');
        $orderinfo = array('goods_id' => '货号','goods_sn' =>'款号','goods_price' => '商品价格','favorable_price' =>'优惠金额','favorable_status' => '优惠审核状态');

        $id = intval($params["id"]);
        $goods_id       =  _Post::get('goods_id');
        $goods_sn     =  _Post::get('goods_sn');
        $goods_price    =  _Post::get('goods_price');
        $favorable_price =  _Post::get('favorable_price');

        $newmodel =  new AppOrderDetailsModel($id,28);
        $orderModel= new BaseOrderInfoModel(27);
        $olddo = $newmodel->getDataObject();
        $order_id = $olddo['order_id'];
        $newdo = array(
            'id'=>$id,
            'goods_id'=>$goods_id,
            'goods_sn'=>$goods_sn,
            'goods_price'=>$goods_price,
            'favorable_price'=>$favorable_price,
            'favorable_status'=>3
        );
        $newmodel->saveData($newdo,$olddo);
        //修改订单金额
        $ret = $newmodel->calculateOrderMoney($order_id);
        if($ret){
            $order_info = $orderModel->getOrderInfoById($order_id);
            $remark = '';

            foreach ($newdo as $k => $v) {
                foreach ($olddo as $x => $y) {
                    if($k == $x && $v != $y){
                        $remark .= $orderinfo[$x].$y.'改为'.$v.'<br />';
                    }
                }
            }

           if($remark){
                $logInfo = array(
                'order_id'=>$order_info['id'],
                'order_status'=>$order_info['order_status'],
                'shipping_status'=>$order_info['send_good_status'],
                'pay_status'=>$order_info['order_pay_status'],
                'create_user'=>$_SESSION['userName'],
                'create_time'=>date("Y-m-d H:i:s"),
                'remark'=>"商品:".$olddo['goods_id'].$remark,
            );

            //写入订单日志
            $orderModel->addOrderAction($logInfo);

           }

            $result['success'] = 1;
            $result['error'] = '修改成功';
        }else{
            $result['error'] = '修改失败';
        }
        Util::jsonExit($result);
    }
    
    
    /**
     *	xianhuo，渲染转为现货
     */
    public function xianhuo ()
    {
    	
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
        	die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }    	
    	//获取购物车中数据
        $ids = _Post::getList('ids');   
        $order_sn = _Post::getString('order_sn');
        if(empty($order_sn)){
        	$result['title'] = '转为现货';
        	$result['content'] = '后台程序异常!';
        	Util::jsonExit($result);
        }
        if(empty($ids)){
        	$result['title'] = '转为现货';
            $result['content'] = '请选择需要转现货的商品!';
            Util::jsonExit($result);
        }
        $idstr=implode(',', $ids);
        $where= " and  aod.id in (".$idstr.")";
        $AppOrdermodel =  new AppOrderDetailsModel(28);
        $list= $AppOrdermodel->getOrderArr($where);
       // var_dump($list);exit;        
    	$result['content'] = $this->fetch('app_order_xianhuo_info.html',array(
    			'orderdetails'=>$list,
    			'order_sn' => $order_sn
    		
    	));
    	$result['title'] = '转为现货';
    	Util::jsonExit($result);
    }

    /**
     *  线上渠道xianhuo，渲染转为现货
     */
    public function on_linexianhuo ()
    {
        //获取购物车中数据
        $result = array('title'=>'线上渠道转现货','content'=>'');
        $ids = _Post::getList('ids');   
        $order_sn = _Post::getString('order_sn');
        if(empty($order_sn)){
            $result['content'] = '后台程序异常!';
            Util::jsonExit($result);
        }
        if(empty($ids)){
            $result['content'] = '请选择需要转现货的商品!';
            Util::jsonExit($result);
        }
        $idstr=implode(',', $ids);
        $where= " and  aod.id in (".$idstr.")";
        $AppOrdermodel =  new AppOrderDetailsModel(28);
        $list= $AppOrdermodel->getOrderArr($where);      
        $result['content'] = $this->fetch('app_order_online_xianhuo_info.html',array(
                'orderdetails'=>$list,
                'order_sn' => $order_sn
            
        ));
        Util::jsonExit($result);
    }
    
    public function zhuanxianhuo($params){
    	$result = array('success' => 0,'error' =>'');
    	$goods_id_arr=$params['jxh_goods_id'];
    	$orderDetailId_arr=$params['orderDetailId'];
    	$order_sn=$params['order_sn'];
    	if(empty($order_sn)){
    		$result['error'] = '后台程序异常';
    		Util::jsonExit($result);
    	}
    	if(count($goods_id_arr) != count($orderDetailId_arr))
    	{  
    		//如果提交的订单明细数量与 输入的货号数量不对等
    	    $result['error'] = '后台程序异常';
    	    Util::jsonExit($result);
    	}
    	
    	if (count($goods_id_arr) != count(array_unique($goods_id_arr)))
    	{
    		$result['error'] = '一个货号不能同时匹配多个货品';
    		Util::jsonExit($result);
    	}
    	
    	
    	$WarehouseGoodsModel = new SelfWarehouseGoodsModel(21);
    	$SalesChannelsModel = new SelfSalesChannelsModel(1);
    	$model = new AppOrderDetailsModel(27);
    	$appSalepolicyGoodsModel =new AppSalepolicyGoodsModel(17);
    	
    	
    	$salesModel = new SelfSalesModel(28);
    	$wareHouseModel = new SelfWarehouseGoodsModel(22);
    	$SelfProductInfoModel = new SelfProductInfoModel(14);
    	$pdo28 = $salesModel->db()->db();
    	$pdo22 = $wareHouseModel->db()->db();
    	$pdo14 = $SelfProductInfoModel->db()->db();
    	
    	$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
    	$pdo28->beginTransaction(); //开启事务
    	
    	$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
    	$pdo22->beginTransaction(); //开启事务
    	
    	$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
    	$pdo14->beginTransaction(); //开启事务
    	
    	//防止事物提交时发生错误
    	try{
    	foreach($goods_id_arr as $key => $goods_id){
    	  if(!$goods_id)
    		{
    			$pdo28->rollback(); //事务回滚
    			$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo22->rollback(); //事务回滚
    			$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo14->rollback(); //事务回滚
    			$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$result['error'] = '请填写所有的货号再转现！';
    			Util::jsonExit($result);
    		}
    		
    		if(!is_numeric($goods_id))
    		{
    			$pdo28->rollback(); //事务回滚
    			$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo22->rollback(); //事务回滚
    			$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo14->rollback(); //事务回滚
    			$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$result['error'] = "非法货号：<span style='color:red;'>{$goods_id}</span> 不是纯数字";
    			Util::jsonExit($result);
    		}
    		
    		//获取订单商品信息
    		$orderDetailArr=$model->getOrderDetailArr($orderDetailId_arr[$key]);
    		//天生一对订单明细：如果已经配货，不能转现货
    		if($orderDetailArr['delivery_status']==5){
    			$pdo28->rollback(); //事务回滚
    			$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo22->rollback(); //事务回滚
    			$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo14->rollback(); //事务回滚
    			$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$result['error'] = "款号为{$orderDetailArr['goods_sn']}的商品已经配货";
    			Util::jsonExit($result);
    		}
    		if($orderDetailArr['bc_id']){
    			$ProductInfoModel = new SelfProductInfoModel(13);
    			$res7=$ProductInfoModel->getSatausById($orderDetailArr['bc_id']);
    			//echo $res7;exit;
    			if($res7 != 1 && $res7 != 2){
    				$pdo28->rollback(); //事务回滚
    				$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    				$pdo22->rollback(); //事务回滚
    				$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    				$pdo14->rollback(); //事务回滚
    				$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    				$result['error'] = "款号为{$orderDetailArr['goods_sn']}的期货已经分配工厂";
    				Util::jsonExit($result);
    			}
    			
    		}
    		
    		//不是期货的跳过
    		if($orderDetailArr['is_stock_goods'] != 0){

    			//continue;
    			
    			$pdo28->rollback(); //事务回滚
    			$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo22->rollback(); //事务回滚
    			$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo14->rollback(); //事务回滚
    			$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$result['error'] = "货号为 {$goods_id} 的货品不是期货！";
    			Util::jsonExit($result);
    		}
    		
    		
    		//获取仓库货品信息
    		$goodsArr = $WarehouseGoodsModel->getGoodsArr($goods_id);
    		
    		
    		$is_on_sale = $goodsArr['is_on_sale'];
    		if(empty($goodsArr)){
    			$pdo28->rollback(); //事务回滚
    			$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo22->rollback(); //事务回滚
    			$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo14->rollback(); //事务回滚
    			$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$result['error'] = "没有货号为 {$goods_id} 的货品！";
    			Util::jsonExit($result);
    		}
    		if($is_on_sale != 2 && $is_on_sale != 5){
    			$pdo28->rollback(); //事务回滚
    			$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo22->rollback(); //事务回滚
    			$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo14->rollback(); //事务回滚
    			$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$result['error'] = "货号为 $goods_id 的货品不是库存或者调拨中！";
    			Util::jsonExit($result);
    		}
    		
    		//获取货品入库公司ID
    		$to_company_id=$WarehouseGoodsModel->getToCompanyId($goods_id);
    		
    		
    		
    		
    		//订单销售渠道
    		$company_id=$SalesChannelsModel->getCompanyId($orderDetailArr['department_id']);
    		
    		if($is_on_sale==5){
    			//货品调拨入库仓所属公司Id
    			
    			if($to_company_id != $company_id){
    				$pdo28->rollback(); //事务回滚
    				$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    				$pdo22->rollback(); //事务回滚
    				$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    				$pdo14->rollback(); //事务回滚
    				$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    				$result['error'] = '货品调拨入库仓所属公司不是和订单销售渠道同一个公司！';
    				Util::jsonExit($result);
    			}
    		}

    	 //商品类型不是裸钻的需要判断是否有对应销售渠道政策
    	 if($orderDetailArr['goods_type'] != 'lz'){
    		//根据货号和渠道查询
    	     //找现货咯
    	     $goodsAttrModel = new GoodsAttributeModel(17);
    	     $caizhi = $goodsAttrModel->getCaizhiList();
    	     $yanse  = $goodsAttrModel->getJinseList();
    	     //经销商的需要增加公司的过滤
    	     $s_where['goods_id'] = $goods_id;
    	     $s_where['channel'] = $orderDetailArr['department_id'];
    	     if( SYS_SCOPE == 'zhanting' )
    	     {
    	         $is_company_check = Auth::user_is_from_base_company();
    	         if(!$is_company_check){
    	             $s_where['company_id_list'] = $_SESSION['companyId'];
    	         }
    	     }
    	     $sdata = $appSalepolicyGoodsModel->pageXianhuoList($s_where,1,1,$caizhi,$yanse,true);
    	     $goods_check_error = false;
    	     if(isset($sdata['error']) && $sdata['error']== 1 ){  
    	         $goods_check_error= "货号{$goods_id},".$sdata['content'];
    	     }else if(empty($sdata['data'][0]['sprice'])){
    	         $goods_check_error = "货号{$goods_id}在当前销售渠道下找不到销售政策";
    	     }
    	     if($goods_check_error!==false)
    	     {
    		    //$SalepolicyArr=$appSalepolicyGoodsModel->getSalepolicyArr($goods_id,$orderDetailArr['department_id']);
    		    //print_r($orderDetailArr['department_id']);exit;
    		    //if(empty($SalepolicyArr)){
    			$pdo28->rollback(); //事务回滚
    			$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo22->rollback(); //事务回滚
    			$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo14->rollback(); //事务回滚
    			$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$result['error'] = $goods_check_error;
    			Util::jsonExit($result);
    		}
    	 }	
    		if(($goodsArr['cat_type1'] == '裸石' || $goodsArr['cat_type1'] == '彩钻') && $goodsArr['zhengshuhao'] != ''){
    			if($goodsArr['zhengshuhao'] !=  $orderDetailArr['zhengshuhao']){
    				$pdo28->rollback(); //事务回滚
    				$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    				$pdo22->rollback(); //事务回滚
    				$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo14->rollback(); //事务回滚
    				$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    				$result['error'] = "款式分类为裸石或彩钻的商品{$goods_id}的证书号与对应的证书号必须一样";
    				Util::jsonExit($result);
    			}
    			
    		}else{
    			if($goodsArr['goods_sn'] !=  $orderDetailArr['goods_sn']){
    				$pdo28->rollback(); //事务回滚
    				$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    				$pdo22->rollback(); //事务回滚
    				$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    				$pdo14->rollback(); //事务回滚
    				$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    				$result['error'] = "货号为{$goods_id}对应的款号必须一样";
    				Util::jsonExit($result);
    			}
    		}
    		
    		
    		//转商品表为现货状态
    		$res1=$salesModel->updateXianhuo($orderDetailId_arr[$key],$goods_id);
    		if(!$res1){
    			$pdo28->rollback(); //事务回滚
    			$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo22->rollback(); //事务回滚
    			$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo14->rollback(); //事务回滚
    			$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$result['error'] = "货号为{$goods_id}转现:订单商品表改变状态失败";
    			Util::jsonExit($result);
    		}
    		
    		//仓储管理->商品列表里的货号绑定订单号
    		$res2=$wareHouseModel->updateOrderGoodsId($goods_id,$orderDetailId_arr[$key]);
    		if(!$res2){
    			$pdo28->rollback(); //事务回滚
    			$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo22->rollback(); //事务回滚
    			$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo14->rollback(); //事务回滚
    			$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$result['error'] = "货号为{$goods_id}转现:仓库货品表绑定货号失败";;
    			Util::jsonExit($result);
    		}
    		
    		$buchan_remark='';//布产单取消订单日志
    		if($orderDetailArr['bc_id']){
    			$ProductInfoModel = new SelfProductInfoModel(13);
    			$res7=$ProductInfoModel->getSatausById($orderDetailArr['bc_id']);
    			//echo $res7;exit;
    			if($res7 == 1 || $res7 == 2){
    				$res8=$SelfProductInfoModel->updateBcStatusById($orderDetailArr['bc_id'],"订单{$order_sn}详情；期货转现货，货号为".$goods_id);
    				$res9=$salesModel->UpdateOrderDetailStatus($orderDetailId_arr[$key],10);
    				$buchan_remark.=',布产状态改为已取消';
    				if(!$res8 && !$res9){
    					$pdo28->rollback(); //事务回滚
    					$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    					$pdo22->rollback(); //事务回滚
    					$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    					$pdo14->rollback(); //事务回滚
    					$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    					$result['error'] = "款号为{$orderDetailArr['goods_sn']}的期货布产单取消失败";
    					Util::jsonExit($result);
    				}
    				
    			}
    			 
    		}
    		
    		
    		//推送到订单 的日志
    		$res6=$salesModel->AddOrderLog($order_sn,"订单{$order_sn}详情；期货转现货，货号为".$goods_id.$buchan_remark);
    		if(!$res6){
    			$pdo14->rollback(); //事务回滚
    			$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			 
    			$pdo22->rollback(); //事务回滚
    			$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo28->rollback(); //事务回滚
    			$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			 
    			 
    			$result['error'] ="添加订单日志失败";
    			Util::jsonExit($result);
    		}
    		

    		
    	}
    		
    	   		
    		

    		$new_order_info = $salesModel->GetOrderInfo($order_sn);
    		//天生一对：如果订单支付状态为支付订单/财务备案/已付款，如果商品的配货状态为未配货，商品配货状态更新为【允许配货】，如果为已配货，应该不允许转现货；未付款不需要更新
    		if($orderDetailArr['delivery_status']==1 && $new_order_info['order_pay_status']>1){
    			$re10=$salesModel->updateOrderDetialDelivery($orderDetailId_arr[$key],2);
    			if(!$re10){
    				$pdo28->rollback(); //事务回滚
    				$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    				$pdo22->rollback(); //事务回滚
    				$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    				$pdo14->rollback(); //事务回滚
    				$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    				
    				$result['error'] ="款号为{$orderDetailArr['goods_sn']}的期货配货状态更新失败";
    				Util::jsonExit($result);
    			}
    		}
    		
    		
    		$order_id=$new_order_info['id'];
    		$order_detail_data = $salesModel->getOrderDetailByOrderId($order_id);
    		$is_peihuo = false;
    		$isxianhuo= false;
    		if(!empty($order_detail_data)){
    			$xianhuo= 1;
    			$is_peihuo = true;
    		}
    		foreach($order_detail_data as $tmp){
    			//排除：现货货号 + 不需布产的 + 已出厂 + 已退货
    			if($tmp['is_stock_goods'] == 0 && $tmp['buchan_status'] !=9 && $tmp['buchan_status'] != 11 && $tmp['is_return'] != 1 ){
    				$is_peihuo = false;
    			}
    			//判断订单所属的商品是否有期货
    			if($tmp['is_stock_goods'] == 0 && $tmp['is_return'] != 1){
    				$xianhuo= 0;
    			}
    			
    		}
    		
    		//print_r($new_order_info);exit;
    		//当已经变成全款时或者财务备案
    		if($new_order_info['order_pay_status']==3 || $new_order_info['order_pay_status']==4){

    			if($is_peihuo && $new_order_info['delivery_status']==1){
    				//更改订单状态
    				$res3=$salesModel->EditOrderdeliveryStatus($order_sn);//更改订单的配货状态为允许配货 更改订单的布产状态为已出厂
    				if(!$res3){
    					$pdo28->rollback(); //事务回滚
		    			$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		    			$pdo22->rollback(); //事务回滚
		    			$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		    			$pdo14->rollback(); //事务回滚
		    			$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    		
    					$result['error'] ="更改订单状态失败";
    					Util::jsonExit($result);
    				}
    			}
    			
    		
    		}else{
    			//只更改布产状态不更改订单状态
    			$res4=$salesModel->EditOrderStatus($order_sn);
    			if(!$res4){
    				$pdo28->rollback(); //事务回滚
	    			$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
	    			$pdo22->rollback(); //事务回滚
	    			$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
	    			$pdo14->rollback(); //事务回滚
	    			$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
	    			$result['error'] ="更改布产状态失败";
    				Util::jsonExit($result);
    			}
    			
    			
    			
    			
    		}

    		
    			$res5=$salesModel->EditOrderdexianhuoStatus($order_sn,$xianhuo);
    			if(!$res5){
    				$pdo28->rollback(); //事务回滚
    				$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    				$pdo22->rollback(); //事务回滚
    				$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    				$pdo14->rollback(); //事务回滚
    				$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    		
    				$result['error'] ="更改订单类型失败";
    				Util::jsonExit($result);
    			}
    		    			
    			$pdo28->commit(); //事务提交
    			$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo22->commit(); //事务回滚
    			$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo14->commit(); //事务回滚
    			$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$result['success'] = 1;
    			$result['info'] = "转现成功";
    			Util::jsonExit($result);
    		}catch (Exception $e){	
    			$pdo28->rollback(); //事务回滚
    			$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo22->rollback(); //事务回滚
    			$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo14->rollback(); //事务回滚
    			$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$result['error'] ="系统异常！error code:".$e;
    			Util::jsonExit($result);
    		}
    	
    	Util::jsonExit($result);
    	
    }

    public function on_linezhuanxianhuo($params){
        $result = array('success' => 0,'error' =>'');
        $goods_id_arr=$params['jxh_goods_id'];
        $orderDetailId_arr=$params['orderDetailId'];
        $order_sn=$params['order_sn'];
        if(empty($order_sn)){
            $result['error'] = '后台程序异常';
            Util::jsonExit($result);
        }
        if(count($goods_id_arr) != count($orderDetailId_arr))
        {  
            //如果提交的订单明细数量与 输入的货号数量不对等
            $result['error'] = '后台程序异常';
            Util::jsonExit($result);
        }
        
        if (count($goods_id_arr) != count(array_unique($goods_id_arr)))
        {
            $result['error'] = '一个货号不能同时匹配多个货品';
            Util::jsonExit($result);
        }
        
        
        $WarehouseGoodsModel = new SelfWarehouseGoodsModel(21);
        $SalesChannelsModel = new SelfSalesChannelsModel(1);
        $model = new AppOrderDetailsModel(27);
        $appSalepolicyGoodsModel =new AppSalepolicyGoodsModel(17);
        
        
        $salesModel = new SelfSalesModel(28);
        $wareHouseModel = new SelfWarehouseGoodsModel(22);
        $SelfProductInfoModel = new SelfProductInfoModel(14);
        $pdo28 = $salesModel->db()->db();
        $pdo22 = $wareHouseModel->db()->db();
        $pdo14 = $SelfProductInfoModel->db()->db();
        
        $pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
        $pdo28->beginTransaction(); //开启事务
        
        $pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
        $pdo22->beginTransaction(); //开启事务
        
        $pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
        $pdo14->beginTransaction(); //开启事务
        
        //防止事物提交时发生错误
        try{
        foreach($goods_id_arr as $key => $goods_id){
          if(!$goods_id)
            {
                $pdo28->rollback(); //事务回滚
                $pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo22->rollback(); //事务回滚
                $pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo14->rollback(); //事务回滚
                $pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $result['error'] = '请填写所有的货号再转现！';
                Util::jsonExit($result);
            }
            
            if(!is_numeric($goods_id))
            {
                $pdo28->rollback(); //事务回滚
                $pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo22->rollback(); //事务回滚
                $pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo14->rollback(); //事务回滚
                $pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $result['error'] = "非法货号：<span style='color:red;'>{$goods_id}</span> 不是纯数字";
                Util::jsonExit($result);
            }
            
            //获取订单商品信息
            $orderDetailArr=$model->getOrderDetailArr($orderDetailId_arr[$key]);
            //天生一对订单明细：如果已经配货，不能转现货
            if($orderDetailArr['delivery_status']==5){
                $pdo28->rollback(); //事务回滚
                $pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo22->rollback(); //事务回滚
                $pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo14->rollback(); //事务回滚
                $pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $result['error'] = "款号为{$orderDetailArr['goods_sn']}的商品已经配货";
                Util::jsonExit($result);
            }
            if($orderDetailArr['bc_id']){
                $ProductInfoModel = new SelfProductInfoModel(13);
                $res7=$ProductInfoModel->getSatausById($orderDetailArr['bc_id']);
                //echo $res7;exit;
                if($res7 != 1 && $res7 != 2){
                    $pdo28->rollback(); //事务回滚
                    $pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                    $pdo22->rollback(); //事务回滚
                    $pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                    $pdo14->rollback(); //事务回滚
                    $pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                    $result['error'] = "款号为{$orderDetailArr['goods_sn']}的期货已经分配工厂";
                    Util::jsonExit($result);
                }
                
            }
            
            //不是期货的跳过
            if($orderDetailArr['is_stock_goods'] != 0){

                //continue;
                
                $pdo28->rollback(); //事务回滚
                $pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo22->rollback(); //事务回滚
                $pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo14->rollback(); //事务回滚
                $pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $result['error'] = "货号为 {$goods_id} 的货品不是期货！";
                Util::jsonExit($result);
            }
            
            
            //获取仓库货品信息
            $goodsArr = $WarehouseGoodsModel->getGoodsArrOnLine($goods_id);
            
            
            $is_on_sale = $goodsArr['is_on_sale'];
            if(empty($goodsArr)){
                $pdo28->rollback(); //事务回滚
                $pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo22->rollback(); //事务回滚
                $pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo14->rollback(); //事务回滚
                $pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $result['error'] = "（淘宝B店、淘宝裸钻库、京东SOP）没有货号为 {$goods_id} 的货品！";
                Util::jsonExit($result);
            }
            if($is_on_sale != 2 && $is_on_sale != 5){
                $pdo28->rollback(); //事务回滚
                $pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo22->rollback(); //事务回滚
                $pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo14->rollback(); //事务回滚
                $pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $result['error'] = "货号为 $goods_id 的货品不是库存或者调拨中！";
                Util::jsonExit($result);
            }
            
            //获取货品入库公司ID
            $to_company_id=$WarehouseGoodsModel->getToCompanyId($goods_id);
            
            
            
            
            //订单销售渠道
            $company_id=$SalesChannelsModel->getCompanyId($orderDetailArr['department_id']);
            
            if($is_on_sale==5){
                //货品调拨入库仓所属公司Id
                
                if($to_company_id != $company_id){
                    $pdo28->rollback(); //事务回滚
                    $pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                    $pdo22->rollback(); //事务回滚
                    $pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                    $pdo14->rollback(); //事务回滚
                    $pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                    $result['error'] = '货品调拨入库仓所属公司不是和订单销售渠道同一个公司！';
                    Util::jsonExit($result);
                }
            }

         //商品类型不是裸钻的需要判断是否有对应销售渠道政策
         /*if($orderDetailArr['goods_type'] != 'lz'){
            //根据货号和渠道查询
             //找现货咯
             $goodsAttrModel = new GoodsAttributeModel(17);
             $caizhi = $goodsAttrModel->getCaizhiList();
             $yanse  = $goodsAttrModel->getJinseList();
             //经销商的需要增加公司的过滤
             $s_where['goods_id'] = $goods_id;
             $s_where['channel'] = $orderDetailArr['department_id'];
             if( SYS_SCOPE == 'zhanting' )
             {
                 $is_company_check = Auth::user_is_from_base_company();
                 if(!$is_company_check){
                     $s_where['company_id_list'] = $_SESSION['companyId'];
                 }
             }
             $sdata = $appSalepolicyGoodsModel->pageXianhuoList($s_where,1,1,$caizhi,$yanse,true);
             $goods_check_error = false;
             if(isset($sdata['error']) && $sdata['error']== 1 ){  
                 $goods_check_error= "货号{$goods_id},".$sdata['content'];
             }else if(empty($sdata['data'][0]['sprice'])){
                 $goods_check_error = "货号{$goods_id}在当前销售渠道下找不到销售政策";
             }
             if($goods_check_error!==false)
             {
                //$SalepolicyArr=$appSalepolicyGoodsModel->getSalepolicyArr($goods_id,$orderDetailArr['department_id']);
                //print_r($orderDetailArr['department_id']);exit;
                //if(empty($SalepolicyArr)){
                $pdo28->rollback(); //事务回滚
                $pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo22->rollback(); //事务回滚
                $pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo14->rollback(); //事务回滚
                $pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $result['error'] = $goods_check_error;
                Util::jsonExit($result);
            }
         }  */
            if(($goodsArr['cat_type1'] == '裸石' || $goodsArr['cat_type1'] == '彩钻') && $goodsArr['zhengshuhao'] != ''){
                if($goodsArr['zhengshuhao'] !=  $orderDetailArr['zhengshuhao']){
                    $pdo28->rollback(); //事务回滚
                    $pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                    $pdo22->rollback(); //事务回滚
                    $pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo14->rollback(); //事务回滚
                    $pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                    $result['error'] = "款式分类为裸石或彩钻的商品{$goods_id}的证书号与对应的证书号必须一样";
                    Util::jsonExit($result);
                }
                
            }else{
                if($goodsArr['goods_sn'] !=  $orderDetailArr['goods_sn']){
                    $pdo28->rollback(); //事务回滚
                    $pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                    $pdo22->rollback(); //事务回滚
                    $pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                    $pdo14->rollback(); //事务回滚
                    $pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                    $result['error'] = "货号为{$goods_id}对应的款号必须一样";
                    Util::jsonExit($result);
                }
            }
            
            
            //转商品表为现货状态
            $res1=$salesModel->updateXianhuo($orderDetailId_arr[$key],$goods_id);
            if(!$res1){
                $pdo28->rollback(); //事务回滚
                $pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo22->rollback(); //事务回滚
                $pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo14->rollback(); //事务回滚
                $pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $result['error'] = "货号为{$goods_id}转现:订单商品表改变状态失败";
                Util::jsonExit($result);
            }
            
            //仓储管理->商品列表里的货号绑定订单号
            $res2=$wareHouseModel->updateOrderGoodsId($goods_id,$orderDetailId_arr[$key]);
            if(!$res2){
                $pdo28->rollback(); //事务回滚
                $pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo22->rollback(); //事务回滚
                $pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo14->rollback(); //事务回滚
                $pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $result['error'] = "货号为{$goods_id}转现:仓库货品表绑定货号失败";;
                Util::jsonExit($result);
            }
            
            $buchan_remark='';//布产单取消订单日志
            if($orderDetailArr['bc_id']){
                $ProductInfoModel = new SelfProductInfoModel(13);
                $res7=$ProductInfoModel->getSatausById($orderDetailArr['bc_id']);
                //echo $res7;exit;
                if($res7 == 1 || $res7 == 2){
                    $res8=$SelfProductInfoModel->updateBcStatusById($orderDetailArr['bc_id'],"订单{$order_sn}详情；期货转现货，货号为".$goods_id);
                    $res9=$salesModel->UpdateOrderDetailStatus($orderDetailId_arr[$key],10);
                    $buchan_remark.=',布产状态改为已取消';
                    if(!$res8 && !$res9){
                        $pdo28->rollback(); //事务回滚
                        $pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                        $pdo22->rollback(); //事务回滚
                        $pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                        $pdo14->rollback(); //事务回滚
                        $pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                        $result['error'] = "款号为{$orderDetailArr['goods_sn']}的期货布产单取消失败";
                        Util::jsonExit($result);
                    }
                    
                }
                 
            }
            
            
            //推送到订单 的日志
            $res6=$salesModel->AddOrderLog($order_sn,"订单{$order_sn}详情；期货转现货，货号为".$goods_id.$buchan_remark);
            if(!$res6){
                $pdo14->rollback(); //事务回滚
                $pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                 
                $pdo22->rollback(); //事务回滚
                $pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo28->rollback(); //事务回滚
                $pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                 
                 
                $result['error'] ="添加订单日志失败";
                Util::jsonExit($result);
            }
            

            
        }
            
                
            

            $new_order_info = $salesModel->GetOrderInfo($order_sn);
            //天生一对：如果订单支付状态为支付订单/财务备案/已付款，如果商品的配货状态为未配货，商品配货状态更新为【允许配货】，如果为已配货，应该不允许转现货；未付款不需要更新
            if($orderDetailArr['delivery_status']==1 && $new_order_info['order_pay_status']>1){
                $re10=$salesModel->updateOrderDetialDelivery($orderDetailId_arr[$key],2);
                if(!$re10){
                    $pdo28->rollback(); //事务回滚
                    $pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                    $pdo22->rollback(); //事务回滚
                    $pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                    $pdo14->rollback(); //事务回滚
                    $pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                    
                    $result['error'] ="款号为{$orderDetailArr['goods_sn']}的期货配货状态更新失败";
                    Util::jsonExit($result);
                }
            }
            
            
            $order_id=$new_order_info['id'];
            $order_detail_data = $salesModel->getOrderDetailByOrderId($order_id);
            $is_peihuo = false;
            $isxianhuo= false;
            if(!empty($order_detail_data)){
                $xianhuo= 1;
                $is_peihuo = true;
            }
            foreach($order_detail_data as $tmp){
                //排除：现货货号 + 不需布产的 + 已出厂 + 已退货
                if($tmp['is_stock_goods'] == 0 && $tmp['buchan_status'] !=9 && $tmp['buchan_status'] != 11 && $tmp['is_return'] != 1 ){
                    $is_peihuo = false;
                }
                //判断订单所属的商品是否有期货
                if($tmp['is_stock_goods'] == 0 && $tmp['is_return'] != 1){
                    $xianhuo= 0;
                }
                
            }
            
            //print_r($new_order_info);exit;
            //当已经变成全款时或者财务备案
            if($new_order_info['order_pay_status']==3 || $new_order_info['order_pay_status']==4){

                if($is_peihuo && $new_order_info['delivery_status']==1){
                    //更改订单状态
                    $res3=$salesModel->EditOrderdeliveryStatus($order_sn);//更改订单的配货状态为允许配货 更改订单的布产状态为已出厂
                    if(!$res3){
                        $pdo28->rollback(); //事务回滚
                        $pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                        $pdo22->rollback(); //事务回滚
                        $pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                        $pdo14->rollback(); //事务回滚
                        $pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
            
                        $result['error'] ="更改订单状态失败";
                        Util::jsonExit($result);
                    }
                }
                
            
            }else{
                //只更改布产状态不更改订单状态
                $res4=$salesModel->EditOrderStatus($order_sn);
                if(!$res4){
                    $pdo28->rollback(); //事务回滚
                    $pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                    $pdo22->rollback(); //事务回滚
                    $pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                    $pdo14->rollback(); //事务回滚
                    $pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                    $result['error'] ="更改布产状态失败";
                    Util::jsonExit($result);
                }
                
                
                
                
            }

            
                $res5=$salesModel->EditOrderdexianhuoStatus($order_sn,$xianhuo);
                if(!$res5){
                    $pdo28->rollback(); //事务回滚
                    $pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                    $pdo22->rollback(); //事务回滚
                    $pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                    $pdo14->rollback(); //事务回滚
                    $pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
            
                    $result['error'] ="更改订单类型失败";
                    Util::jsonExit($result);
                }
                            
                $pdo28->commit(); //事务提交
                $pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo22->commit(); //事务回滚
                $pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo14->commit(); //事务回滚
                $pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $result['success'] = 1;
                $result['info'] = "转现成功";
                Util::jsonExit($result);
            }catch (Exception $e){  
                $pdo28->rollback(); //事务回滚
                $pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo22->rollback(); //事务回滚
                $pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo14->rollback(); //事务回滚
                $pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $result['error'] ="系统异常！error code:".$e;
                Util::jsonExit($result);
            }
        
        Util::jsonExit($result);
        
    }

    /**
     * 打印维修取货单
     */
    public function printRepairOrder($params){
        $ids   = _Request::get('_ids');
        if(empty($ids)){
            die('请选择需要打印的商品!');
        }
        $ids = explode(',',$ids);
        $goods_list = array();
        $apiStyleModel = new ApiStyleModel();
        $warehouseModel = new SelfWarehouseGoodsModel(22);
        $goods_ids = '';
        $goods_sns = '';
        foreach ($ids as $key=>$id){            
            //验证并查询数据            
            $model = new AppOrderDetailsModel($id,27);
            $data  = $model->getDataObject();            
            if(empty($data)){
                die("商品信息查询失败，部分商品可能已经被删除，请刷新商品列表重新选择");
            }
            $order_id = $data['order_id'];
            //验证订单状态
            if($key == 0){
                $baseOrderInfoModel = new BaseOrderInfoModel($order_id,27);
                $baseOrderInfo = $baseOrderInfoModel->getDataObject();
                if(empty($baseOrderInfo)){
                    die("所选商品所在的订单基本信息查询失败");
                }
                //查询收货人
                $addressModel = new AppOrderAddressModel(27);
                $address = $addressModel->getAddressByOrderid($order_id);
                if(!empty($address['consignee'])){
                    $baseOrderInfo['consignee'] = $address['consignee'];
                }
                $order_status     = $baseOrderInfo['order_status'];
                $order_pay_status = $baseOrderInfo['order_pay_status'];
                $apply_close      = $baseOrderInfo['apply_close'];
                $department_id    = $baseOrderInfo['department_id'];
                //订单【已审核】【未关闭】，支付状态【非"未付款"】，才允许打印维修取货单
                if($order_status!=2 || $order_pay_status==1 || $apply_close==1){
                    die("订单状态不对：订单必须是【已审核】【未关闭】【非未付款】才允许打印维修取货单");
                }
                $salesChannelsModel = new SalesChannelsModel($department_id,1);
                $baseOrderInfo['department'] = $salesChannelsModel->getValue('channel_name');
            }
            //查询是否绑定货号
            $bindGoods = $warehouseModel->getWarehouseGoodsRow('goods_id,box_sn',"order_goods_id='{$id}'");
            if(!empty($bindGoods['goods_id'])){
                $data['bind_goods_id'] = $bindGoods['goods_id'];
                $data['bind_box_sn']   = $bindGoods['box_sn'];
            }else{
                $data['bind_goods_id'] = '';
                $data['bind_box_sn']   = '';
            }
            //查询图片(裸钻不需要查询)
            if(!empty($data['goods_sn']) && strtoupper($data['goods_sn'])!='DIA'){
                $picInfo = $apiStyleModel->GetStyleGallery(array('style_sn'=>$data['goods_sn']));
                $data['big_img'] = !empty($picInfo['big_img'])?$picInfo['big_img']:'';
            }else{            
                $data['big_img'] = '';
            } 
            $goods_ids.='【'.$data['goods_id'].'】';
            $goods_sns.='【'.$data['goods_sn'].'】';
            $goods_list[] = $data;
        }
        $this->render('app_order_details_print_repair_order.html',array(
            'goods_list' =>$goods_list,
			'order_info' =>$baseOrderInfo,
            'goods_ids'  =>$goods_ids,
            'goods_sns'  =>$goods_sns
        ));
        
    }
    /**
     * 打印维修取货单日志写入
     */
    public function addPrintRepairOrderLog($params){
        
        $result = array('success' => 0,'error' =>'','title'=>'打印维修取货单');
        
        $order_id =  _Request::get('order_id');
        $goods_ids = _Request::get('goods_ids');
        $goods_sns = _Request::get('goods_sns');
        $baseOrderInfoModel = new BaseOrderInfoModel($order_id,27);
        $baseOrderInfo = $baseOrderInfoModel->getDataObject();
        if(empty($baseOrderInfo)){
            $result['error'] = "订单信息查询失败：order_id:{$order_id}";
            Util::jsonExit($result);
        }
        $model = new AppOrderActionModel(27);
        $newdo = array(
            "order_id"=>$order_id,
            "order_status"=>$baseOrderInfo['order_status'],
            "shipping_status"=>$baseOrderInfo['send_good_status'],
            "pay_status"=>$baseOrderInfo['order_pay_status'],
            "create_user"=>$_SESSION['userName'],
            "create_time"=>date('Y-m-d H:i:s'),
            "remark"=>"打印维修取货单，货号{$goods_ids},款号{$goods_sns}"            
        );
        $res = $model->saveData($newdo,array());
        if($res){
            $result['success'] = 1;
            Util::jsonExit($result);
        }else{
            $result['error'] = "日志写入失败";
            Util::jsonExit($result);
        }
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

    /*//根据商品信息判断取哪种优惠方式；
    $saData['goods_type'] = $goods_type;//商品类型
    $saData['cert_id']    = $cert_id;//证书号
    $saData['goods_id']   = $goods_id;//货号
    $saData['goods_sn']   = $goods_sn;//款号
    $saData['carat']      = $carat//石重 */
    public function getGoodsType($data)
    {
        $diainfo = array();
        $cert_id = $data['cert_id'];
        $type = 0;
        $diaModelApi = new ApiDiamondModel();
        if($data['goods_type'] == 'lz'){
            //裸钻根据证书类型判断；
            if($cert_id){
                $diainfo = $diaModelApi->getDiamondInfoByCertId($cert_id);
            }
            if($diainfo['error'] != 1){
                $certid = $diainfo['data']['cert'];
                $carat  = $diainfo['data']['carat'];
                $good_type = $diainfo['data']['good_type'];
                if($certid == 'HRD-S'){
                    if($carat<0.5){
                        $type = 5;
                    }else if($carat>=0.5 && $carat<1){
                        $type = 6;
                    }else if($carat>=1 && $carat<1.5){
                        $type = 7;
                    }else{
                        $type = 8;
                    }
                }elseif($certid == 'HRD-D'){
                    $type = 9;
                }else{
                    if($good_type == 1){
                        if($carat<0.5){
                            $type = 1;
                        }else if($carat>=0.5 && $carat<1){
                            $type = 2;
                        }else if($carat>=1 && $carat<1.5){
                            $type = 3;
                        }else{
                            $type = 4;
                        }
                    }else{
                        if($carat<0.5){
                            $type = 12;
                        }else if($carat>=0.5 && $carat<1){
                            $type = 13;
                        }else if($carat>=1 && $carat<1.5){
                            $type = 14;
                        }else{
                            $type = 15;
                        }
                    }
                }
            }
        }else{
            //普通空拖、成品根据商品信息、款式归属判断；
            if($cert_id){
                $diainfo = $diaModelApi->getDiamondInfoByCertId($cert_id);
                if($diainfo['error'] != 1){
                    $certid = $diainfo['data']['cert'];
                    //判断是否镶嵌HRD-D裸石
                    if($certid == 'HRD-D'){
                        $type = 10;
                    }
                }
            }
            //款号判断是否天生一对系列产品
            $style_sn = $data['goods_sn'];
            if($style_sn != '' && $type != 10){
                $apiStyle = new ApiStyleModel();
                $styleinfo = $apiStyle->getStyleInfo($style_sn);
                if($styleinfo['error'] != 1){
                    $xilie = $styleinfo['data']['xilie'];
                    if($xilie){
                        $xiliearr = array_filter(explode(',', $xilie));
                        if(!empty($xiliearr)){
                            if(in_array('8', $xiliearr)){
                                $type = 10;
                            }else if(in_array('24', $xiliearr)){
                                $type = 16;
                            }else if(in_array('27', $xiliearr)){
                                $type = 17;
                            }

                        }
                    }
                }
            }
            //普通空拖、成品
            if(!in_array($type,[10,16,17])){
                $type = 11;
            }
        }
        return $type;//返回裸钻优惠类型；
    }

    //定制成品取价；
    public function getChengpindingzhiPrice($params='')
    {
        $result = array('success'=>0,'error'=>'');
        $style_sn = _Post::getString('style_sn');
        $xiangkou = _Post::getfloat('xiangkou',0);
        $clarity = _Post::getString('clarity');
        $color = _Post::getString('color');
        $shape = _Post::getString('shape');
        $cert = _Post::getString('cert');
        $goods_id = _Post::getString('goods_id');
        $channel_id = _Post::getString('channel_id');
        $tuo_type = _Post::getString('tuo_type');
        $is_chengpin = _Post::getString('is_chengpin');
        $carat = _Post::getString('carat');
        if(empty($style_sn)){
            $result['error'] = "款号不能为空";
            Util::jsonExit($result);
        }
        /* if(empty($xiangkou)){
            $result['error'] = "镶口不能为空！";
            Util::jsonExit($result);
        } */
        //$is_chengpin=0 表示成品空托需配钻
        //$is_chengpin=1 表示成品空托不许配钻
        if($is_chengpin==0){
             if($xiangkou >0 && empty($clarity)){
                $result['error'] = "净度不能为空！提示：镶口大于0时，净度，颜色 必填";
                Util::jsonExit($result);
            }
            if($xiangkou >0 && empty($color)){
                $result['error'] = "颜色不能为空！提示：镶口大于0时，净度，颜色 必填";
                Util::jsonExit($result);
            } 
            if(empty($shape)){
                $result['error'] = "当前商品款号【{$style_sn}】没有主石形状，无法匹配钻石";
                Util::jsonExit($result);
            }
        }else{
            if((!empty($clarity) || !empty($color) || $carat>0) && $xiangkou==0){
                $result['error'] = "镶口为0时，主石大小，主石净度，主石颜色 必须为空";
                Util::jsonExit($result);
            }
        }
        

        $_xiangkou = $xiangkou;
        $_cert = empty($cert)?'无':$cert;
        $_clarity = $clarity;
        $_color = $color;
        
        $model = new AppOrderDetailsModel(27);
        $shapeArr = $model->getShapeList();
        $shapeArr = array_flip($shapeArr);
        $_shape = isset($shapeArr[$shape])?$shapeArr[$shape]:'';//形状ID
        
        /* if($is_chengpin==0 && empty($_shape)&& !preg_match("/\|/is",$shape)){
            $result['error'] = "取价失败，此款镶口不支持成品定制！<br/>提示:当前商品款号【{$style_sn}】对应的主石形状【{$shape}】不在成品定制价格表中";
            Util::jsonExit($result);
        } */
        $appSalepolicyGoodsModelR =new AppSalepolicyGoodsModel(15);
        $swhere = array(
            'xiangkou'=>$_xiangkou,
            'color'=>$_color,
            'shape'=>$_shape,//形状ID
            'cert'=>$_cert,
            'clarity'=>$_clarity,
            'goods_id'=>$goods_id,
            'channel'=>$channel_id,
        );

        //$sdata  = $appSalepolicyGoodsModelR->pageQihuoList($swhere,1,1);
        $sdata = $appSalepolicyGoodsModelR->getChenpingdingzhiList($swhere);
        //$sdata = $appSalepolicyGoodsModelR->getChenpingdingzhi($swhere);
        if($sdata['error']!==false || empty($sdata['data'][0]['sprice'])){
            //$result['error'] = $sdata['error'];
            $result['error'] = "不支持成品定制，如需成品定制，请找店长要成品定制码";
            Util::jsonExit($result);
        }
        $pricelist = $sdata['data'][0]['sprice'];
        $priceListHtml = $this->fetch('get_salepolicy_price_list.html',array(
            'pricelist'=>$pricelist            
        ));
        $sale_price  =$pricelist[0]['sale_price'];        	        	
        $policy_name = $pricelist[0]['policy_name'];
        $policy_id = $pricelist[0]['policy_id'];

        $price_key = md5($xiangkou.'&'.$clarity.'&'.$color.'&'.$shape.'&'.$cert);
        $success_msg = "取价成功！商品价格 <b style='color:red'>{$sale_price}</b> 元<hr/>{$priceListHtml}";
        //$success_msg.="匹配属性:镶口【{$xiangkou}】,颜色【{$color}】,净度【{$clarity}】,形状【{$shape}】,证书类型【{$cert}】<br/>{$priceListHtml}";
        
        $result['success'] = 1;
        $result['successMsg'] = $success_msg;
        $result['pricelistHtml'] = $priceListHtml;
        $result['price'] = $sale_price;
        $result['priceKey'] = $price_key;
        $result['policy_id'] = $policy_id;
        Util::jsonExit($result);
    }

    //取消占用名额
    public function countermandOccupy()
    {
        $result = array('success'=>0,'error'=>'');
        $detail_id = _Post::getString('detail_id');
        $model = new AppOrderDetailsModel($detail_id,27);
        $do = $model->getDataObject();
        $baseModel = new BaseOrderInfoModel($do['order_id'],27);
        $baseinfo = $baseModel->getDataObject();
        $res = $model->countermandOccupy($detail_id);
        if($res!==false){
            //成功记录日志
            $remark = "明细：(".$do['goods_sn']." ".$do['cart']." ".$do['color']." ".$do['clarity']." ".$do['caizhi']." ".$do['jinse']." ".$do['zhiquan'].") 取消占用备货名额";
            $array=array(
                        'order_id'=>$baseinfo['id'],
                        'order_status'=>$baseinfo['order_status'],
                        'shipping_status'=>$baseinfo['send_good_status'],
                        'pay_status'=>$baseinfo['order_pay_status'],
                        'create_time'=>date('Y-m-d H:i:s'),
                        'create_user'=>Auth::$userName,
                        'remark'=>$remark
                    );
            $baseModel->addOrderAction($array);
            $result['success'] = 1;
            Util::jsonExit($result);
        }
        $result['error'] = "取消占用失败！";
        Util::jsonExit($result);
    }

    
    public function getCpdzCodePrice($params){
        $result = array('success'=>0,'error'=>'','price'=>0);
        
        $cpdzcode = _Request::get("cpdzcode");
        $model = new AppOrderDetailsModel(27);
        $data = $model->getBaseCpdzCodeInfo($cpdzcode);
        if(empty($data)){
            $result['error'] = "成品定制码{$cpdzcode}不存在!";
            Util::jsonExit($result);
        }else if($data['use_status']<>1){
            $result['error'] = "成品定制码{$cpdzcode}已被使用!";
            Util::jsonExit($result);
        }
        $result['success'] = 1;
        $result['price'] = $data['price']; 
        Util::jsonExit($result);
    }

    //下单找不到销售政策，直营店 经销商托管店 提示找黄旭玲 经销商托管店找钟志标
    public function getTydTypeByDep($dep_id)
    {
        //下单找不到销售政策，直营店 经销商托管店 提示找黄旭玲 经销商托管店找钟志标
        $salesChannelsModel = new SalesChannelsModel(1);
        $Company_staff_name = '钟志标';
        if(!empty($dep_id)){
            $dep_type = $salesChannelsModel->getChannelOwnId($dep_id,2);
            if(!empty($dep_type)){
                if($dep_type['shop_type'] == 1 || $dep_type['shop_type'] == 3){
                    $Company_staff_name = '黄旭玲';
                }elseif($dep_type['shop_type'] == 2){
                    $Company_staff_name = '钟志标';
                }else{

                }
            }
        }
        return $Company_staff_name;
    }


    private function curlPost($url,$data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        if($output === false){
            return curl_error($ch);
        }
        curl_close($ch);
        $item = json_decode($output,true);
        return $output;
    }

    /**
     * 获取积分折扣
     * @params $list 折扣数组
     * @params cert 证书类型
     * @params cart 石头重
     * 
     */
    //TODO 现在只做了按渠道区折扣  没有公司属性的 要补上
    private function getDiscountPoint(array $list,$cert,$cart,$point){
        if(count($list) == 0){
            return array();
        }
        /**
         * 如何有渠道按照渠道折扣  没有渠道按照公式属性折扣
         */
        /* foreach($list as $key=>$value){
            $diamond_range_left = 0;
            $diamond_range_right = 0;
            if(!empty($value['diamond_range_left'])){
                $diamond_range_left = $value['diamond_range_left'];
            }
            if(!empty($value['diamond_range_right'])){
                $diamond_range_right = $value['diamond_range_right'];
            }
            if($diamond_range_left<=$cart && $cart<$diamond_range_right){
                return $value['point_percent'];
            }
        } */
    }

    /**
     * 获取奖励积分
     */
    private function getRewardPoint(array $list,$cert,$style_sn,$cart,$point){
        $rawPoint = 0;
        if(count($list) == 0){
            return 0;
        }
        /**
         * 奖励积分 分款式和证书类型  证书类型优先
         */
        if(!empty($cert)){
            if(empty($cart)){
                return 0;
            }
            foreach($list['cert'] as $key=>$value){
                if($value['cert'] == $cert){
                    $diamond_range_left = 0;
                    $diamond_range_right = 0;
                    if(!empty($value['diamond_range_left'])){
                        $diamond_range_left = $value['diamond_range_left'];
                    }
                    if(!empty($value['diamond_range_right'])){
                        $diamond_range_right = $value['diamond_range_right'];
                    }
                    if($diamond_range_left<=$cart && $cart<$diamond_range_right){
                        $rawPoint= ceil($point/$value['point_percent']);
                    }
                }
            }
        }else if(!empty($style_sn)){
            foreach($list['style_sn'] as $key=>$value){
                if($value['goods_sn'] == $style_sn){
                    $rawPoint= ceil($point/$value['point_percent']);
                }
            }
        }
        return $rawPoint;
    }

    /*
    *   调用接口规则 计算订单商品优惠后积分数据
    */
    public function caculateFavorablePoint(){
    	$favorablePrice = _Request::getFloat('favorable_price');
    	$order_detail_id = _Request::getString('order_detail_id');

    	if(empty($favorablePrice)) {
    		Util::jsonExit(array('success'=>0,'error'=>'优惠金额必须为数字且不能为空或者为0!'));
    	}
    	if(!is_numeric($favorablePrice)) {
    		Util::jsonExit(array('success'=>0,'error'=>'优惠金额只能为数字'));
    	}
    	if(empty($order_detail_id)) {
    		Util::jsonExit(array('success'=>0,'error'=>'订单明细参数不能为空'));
    	}

        $orderDetailsModel = new AppOrderDetailsModel($order_detail_id,28);
        $order_info = $orderDetailsModel->getOrderInfo($order_detail_id);
        if(empty($order_info)){
            Util::jsonExit(array('success'=>0,'error'=>'没有找到订单信息!'));
        }
        $departmentid = $order_info['department_id'];
        $goodsPrice= $orderDetailsModel->getValue('goods_price');
        $carat = (float)$orderDetailsModel->getValue('cart');
        $styleSn = $orderDetailsModel->getValue('goods_sn');
        $certType = $orderDetailsModel->getValue('cert');
        $goodsType = $orderDetailsModel->getValue('goods_type');
        $isStock =  $orderDetailsModel->getValue('is_stock_goods');
        $xiangqianType =  $orderDetailsModel->getValue('xiangqian');
        $jietuoType =  $orderDetailsModel->getValue('tuo_type');
        $daijinquanPrice = empty($orderDetailsModel->getValue('daijinquan_price')) ? 0 : $orderDetailsModel->getValue('daijinquan_price');
        $zhuandanCash = empty( $orderDetailsModel->getValue( 'zhuandan_cash' ) ) ? 0 : $orderDetailsModel->getValue( 'zhuandan_cash' );
        $xiangkou = $orderDetailsModel->getValue("xiangkou");
        $tuo_type = $orderDetailsModel->getValue('goods_type');
        if($tuo_type != 'lz'){
            $warehouseGoodsInfo = $orderDetailsModel->getWarehouseGoodsInfo($orderDetailsModel->getValue('goods_id'));
            if($warehouseGoodsInfo&&$warehouseGoodsInfo['tou_type'] == 1){
                $tuo_type = 'style_goods';
            }
            else {
                $tuo_type = 'jietuo';
            }
        }
        /**
		 * 黄金产品（材质是足金，产品线为普通黄金，定价黄金）没有积分
		 */
		$caizhi = $orderDetailsModel->getValue('caizhi', '');
		$product_type = $orderDetailsModel->getValue('product_type', 0);
		if($product_type == 7 || $product_type == 13 || strpos($caizhi, '足金') !== false) {
			Util::jsonExit(array('success'=>1,'error'=>'黄金产品（材质是足金，产品线为普通黄金，定价黄金）没有积分!'));
		}
	    /**
		 * 黄金产品（材质是足金，产品线为普通黄金，定价黄金）没有积分
		 */
        $pointRules = null;
        try {
            $pointRules= Util::point_api_get_config($departmentid, $order_info['mobile']);
            /*if(!$pointRules['is_enable_point']) {
                Util::jsonExit(array('success'=>0,'error'=>'该渠道没有开启积分功能!'));
            }*/
        }
        catch (Exception $e) {
            Util::jsonExit(array('success'=>0,'error'=>'没有找到积分基础配置信息!'));
        }
        $point_string = '';
        if(!empty($pointRules) && $pointRules['is_enable_point'])
        {
            list($discount_point, $reward_point) = Util::point_api_calculate_order_detail_point($pointRules, $goodsPrice, $favorablePrice, $daijinquanPrice,
                $zhuandanCash, $certType, $carat, $styleSn, $goodsType, $isStock, $xiangqianType, $jietuoType, $xiangkou,$tuo_type);
            $point_string = "预计赠送标准积分：".$discount_point."，奖励积分：" . $reward_point."，总积分：". ($discount_point + $reward_point);
            if($pointRules['activity_rate']['multiple'] > 1) {
                $point_string .= "。参与活动【{$pointRules['activity_rate']['activity_name']}】积分得到【{$pointRules['activity_rate']['multiple']}】倍翻倍奖励";
            }
        }
        Util::jsonExit(array('success'=>1,'error'=>$point_string));
    }


    /**
     * 调用接口规则 订单下单前计算订单商品预计能送多少积分
     * @param float $goods_price
     * @param float $favorable_price
     * @param integer $cert
     * @param float $cart
     * @param string $style_sn
     * @param integer $departmentid
     * @param string $goods_type
     * @param string $tuo_type
     * @param string $xiangqian
     * @param string $mobile
     */
    public function caculatePoint() {
        $goodsPrice= _Request::getFloat('goods_price', 0);
        $favorablePrice= _Request::getFloat('favorable_price', 0);
    	$certType = _Request::getString('cert');
    	$carat= _Request::getFloat('cart',0);
    	$styleSn = _Request::getString('style_sn');
    	$departmentid = _Request::getString('departmentid');
    	$goodsType = _Request::getString('goods_type');
    	$isStock = _Request::getString('is_stock_goods');
    	$jietuoType = _Request::getString('tuo_type');
        $xiangqianType = _Request::getString('xiangqian');
        $xiangkou = _Request::getFloat('xiangkou',0);
        /**
		 * 黄金产品（材质是足金，产品线为普通黄金，定价黄金）没有积分
		 */
		$caizhi = _Request::getString('caizhi', "");
        $product_type = _Request::getString('product_type', 0);
		if($product_type == 7 || $product_type == 13 || strpos($caizhi, '足金') !== false) {
			Util::jsonExit(array('success'=>1,'error'=>'黄金产品（材质是足金，产品线为普通黄金，定价黄金）没有积分!'));
		}
	    /**
		 * 黄金产品（材质是足金，产品线为普通黄金，定价黄金）没有积分
		 */

    	/**
    	 * @var string $mobile
    	 */
    	$mobile = _Request::getString("mobile");

    	if(empty($goodsPrice)){
    		Util::jsonExit(array('success'=>0,'error'=>'成交金额必须为数字且不能为空或者为0!'));
    	}

        //获取CRM折扣积分规则
        try {
            $pointRules= Util::point_api_get_config($departmentid, $mobile);
            if(!$pointRules['is_enable_point']) {
                Util::jsonExit(array('success'=>1,'error'=>'该渠道未启用积分!'));
            }
        }
        catch (Exception $e) {
            Util::jsonExit(array('success'=>1,'error'=>'积分商城接口服务异常!'));
        }
        
        list($discount_point, $reward_point) = Util::point_api_calculate_order_detail_point($pointRules, $goodsPrice, $favorablePrice, 0, 
                0, $certType, $carat, $styleSn, $goodsType, $isStock, $xiangqianType, $jietuoType, $xiangkou,$jietuoType);

        $point_string = "预计赠送标准积分：".$discount_point."，奖励积分：".$reward_point."，总积分：". ($discount_point+$reward_point);
        if($pointRules['activity_rate']['multiple'] > 1) {
            $point_string .= "。参与活动【{$pointRules['activity_rate']['activity_name']}】积分得到【{$pointRules['activity_rate']['multiple']}】倍翻倍奖励";
        }
        Util::jsonExit(array('success'=>1,'error'=>$point_string));        

    }

}
