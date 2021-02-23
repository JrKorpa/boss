<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductInfoController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 21:15:51
 *   @update	:
 *  ------	
sel_factory-------------------------------------------
 */
class ProductInfoController extends CommonController
{
	protected $smartyDebugEnabled = true;
    //add by zhangruiying
    protected $whitelist = array('combineXQPrint','DownloadCsv','DownloadCsvNew','piliang_print_jiagong','printBills','bath_print_bill','alterTime','DownloadCsvs');
	protected $dd;
	protected $from_type=array(1=>'采购单',2=>'订单');
	protected $is_extended=array('距出厂不足两天','超期未出厂','其它');
	protected $yanse=array('D','D-E','E','E-F','F','F-G','G','G-H','H','H+','H-I','I','I-J','J','J-K','K','K-L','L','M','白色','黑色','金色');
	protected $jingdu=array('FL','IF','VVS','VVS1','VVS2','VS','VS1','VS2','SI','SI1','SI2','I','I1','I2','P P1','无');
	//add by zhangruiying
	public function getDataFarmat()
	{
		 $args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'bc_sn'	=> _Request::get("bc_sn"),
			'p_sn'	=> _Request::get("p_sn"),
            'p_sn_out'  => _Request::get("p_sn_out"),
			'style_sn'	=> _Request::get("style_sn"),
			'fac_number'=> _Request::get("fac_number"),
			'prc_id'	=> _Request::get("prc_id"),
			'status'	=> _Request::get("status"),
            'consignee' => _Request::getString('consignee'),
			'opra_uname'=>_Request::getString('opra_uname'),
		 	'production_manager_name'=>_Request::getString('production_manager_name'),
			'buchan_fac_opra[]'=>_Request::getList("buchan_fac_opra"),
			'esmt_time_start'=>_Request::get("esmt_time_start"),
			'esmt_time_end'=>_Request::get("esmt_time_end"),
			'order_time_start'=>_Request::get("order_time_start"),
			'order_time_end'=>_Request::get("order_time_end"),
            'channel_class'    => _Request::get("channel_class"),
			'channel_id'	=> _Request::get("channel_id"),
			'customer_source_id' => _Request::get("customer_source_id"),
			'xiangqian' => _Request::getString('xiangqian'),
			'is_alone' => _Request::getString('is_alone'),
			'from_type'=>_Request::getString('from_type'),
			'is_extended'=>_Request::get("is_extended"),
			'page_size' => _Request::get('page_size')?_Request::get('page_size'):10,
			'orderby'=>_Request::get('__order'),
			'desc_or_asc'=>_Request::get('__desc_or_asc'),
			'qiban_type'=>_Request::get('qiban_type'),
			'diamond_type'=>_Request::get('diamond_type'),
			'to_factory_time_start'=>_Request::get("to_factory_time_start"),
			'to_factory_time_end'=>_Request::get("to_factory_time_end"),
			'wait_dia_starttime_start'=>_Request::get("wait_dia_starttime_start"),
			'wait_dia_starttime_end'=>_Request::get("wait_dia_starttime_end"),
			'wait_dia_endtime_start'=>_Request::get("wait_dia_endtime_start"),
			'wait_dia_endtime_end'=>_Request::get("wait_dia_endtime_end"),
			'wait_dia_finishtime_start'=>_Request::get("wait_dia_finishtime_start"),
			'wait_dia_finishtime_end'=>_Request::get("wait_dia_finishtime_end"),
			'oqc_pass_time_start'=>_Request::get("oqc_pass_time_start"),
			'oqc_pass_time_end'=>_Request::get("oqc_pass_time_end"),
			'rece_time_start'=>_Request::get("rece_time_start"),
			'rece_time_end'=>_Request::get("rece_time_end"),
		 	'referer'=>_Request::getString('referer'),
		    'is_quick_diy'=>_Request::get('is_quick_diy'),
		    'is_combine'=>_Request::get('is_combine'),
		    'combine_goods_id'=>_Request::get('combine_goods_id'),
            'product_not_ordersn'=>_Request::get('product_not_ordersn'),
			);

		$args['bc_sn']=str_replace('，',' ',$args['bc_sn']);
		$args['bc_sn']=trim(preg_replace('/(\s+|,+)/',' ',$args['bc_sn']));

		$args['p_sn']=str_replace('，',' ',$args['p_sn']);
		$args['p_sn']=trim(preg_replace('/(\s+|,+)/',' ',$args['p_sn']));

        $args['p_sn_out']=str_replace('，',' ',$args['p_sn_out']);
        $args['p_sn_out']=trim(preg_replace('/(\s+|,+)/',' ',$args['p_sn_out']));
        
        $args['combine_goods_id']=str_replace('，',' ',$args['combine_goods_id']);
        $args['combine_goods_id']=trim(preg_replace('/(\s+|,+)/',' ',$args['combine_goods_id']));
        
		$args['is_peishi'] = _Request::get('is_peishi');
		$args['peishi_status'] = _Request::get('peishi_status');
		
		return $args;

	}
	//客户来源
    public function getCustomerSources()
	{
        $CustomerSourcesModel = new CustomerSourcesModel(1);
        $CustomerSourcesList = $CustomerSourcesModel->getCustomerSourcesList("`id`,`source_name`");
        $this->assign('customer_source_list', $CustomerSourcesList);
    }
	//渠道
    public function getChannel()
	{
        $SalesChannelsModel = new SalesChannelsModel(1);
        $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
		$this->assign('channellist', $channellist);
    }
	//add end
	public function index ($params)    
	{
		$this->getChannel();
		$this->getCustomerSources();
        $facmodel = new AppProcessorInfoModel(13);
        $SalesModel = new SalesModel(51);
        $referers=$SalesModel->getReferers();
		$process = $facmodel->getProList();
		$this->render('documentary_list_search_form.html',array(
			'bar'=>Auth::getBar(),
			'process' => $process,
			'user_type'=>$this->from_type,
			'is_extended'=>$this->is_extended,
			'referers'=>$referers,
		    'opra_uname'=>$_SESSION['userName']
		));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		//edit by zhangruiying
		$args=$this->getDataFarmat();
		$args['opra_uname'] = $_SESSION['userName'];
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		//end
		$model = new ProductInfoModel(14);
		$data = $model->pageList($args,$page,$args['page_size'],false);
		//用于布产单号搜索按顺序排序
		if(strlen(trim($args['bc_sn']))>1){
		    $arr=array_unique(explode(" ",$args["bc_sn"]));
		    $copyArr=[];
		    for ($i = 0; $i <count($arr); $i++) {
		       for($j=0;$j<count($data['data']);$j++){
		           if($arr[$i]==$data['data'][$j]['bc_sn']){
		               $copyArr[]=$data['data'][$j];
		           } 
		       }
		    }
		    $data['data']=$copyArr;
		} 
		$count = $model->pageList2($args);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'product_info_search_page_gendan';
		$table=new TableView('product_info_search_list_gendan','id');
		$table->CheckBox('_ids[]','id');
		$view=new ProductInfoView(new ProductInfoModel(13));
		$table->SetFieldConf($view->getFC());
		$table->SetSort($args['orderby'],$args['desc_or_asc']);
		$table->setTitle('bc_sn');
		$data=$this->FormatData($data);
		echo $table->ShowList($data,$pageData,$count);
		exit;

	}

	/**
	 *	index，搜索框
	 */
	public function indexProduct($params)
	{
//		Util::M('product_opra_log','front',13);	//生成模型后请注释该行
//		Util::V('product_opra_log',13);	//生成视图后请注释该行
		$this->getChannel();
		$this->getCustomerSources();
		//获取供应商列表
		$facmodel = new AppProcessorInfoModel(13);
		$process = $facmodel->getProList();
		$SalesModel = new SalesModel(51);
		$referers=$SalesModel->getReferers();
		
		//获取用户列表
		$user = new UserModel(2);
		$userlist = $user->getUserInfo();
		//获取跟单人
		$gendanModel = new ProductFactoryOprauserModel(13);
		$gen_list = $gendanModel->select2($fields = ' distinct opra_uname ,opra_user_id' , $where = ' 1 ' , $type = 'all');
		$this->render('product_info_search_form.html',array(
			'bar'=>Auth::getBar(),
			'process' => $process,
			'user_list'=>$gen_list,
			'userlist'=>$userlist,
			'userName'=>$_SESSION['userName'],
			'user_type'=>$this->from_type,
			'is_extended'=>$this->is_extended,
			'referers'=>$referers,
		));
	}

	/**
	 *	search，列表
	 */
	public function searchProduct($params)
	{
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$args=$this->getDataFarmat();
		$model = new ProductInfoModel(13);
		$data = $model->pageList($args,$page,$args['page_size'],false);
		$count = $model->pageList2($args);
		//用于布产单号搜索按顺序排序
		if(strlen($args['bc_sn'])>1){
		    $arr=array_unique(explode(" ",$args["bc_sn"]));
		    $copyArr=[];
		    for ($i = 0; $i <count($arr); $i++) {
		        for($j=0;$j<count($data['data']);$j++){
		            if($arr[$i]==$data['data'][$j]['bc_sn']){
		                $copyArr[]=$data['data'][$j];
		            }
		        }
		    }
		    $data['data']=$copyArr;
		} 
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'product_info_search_page';
		$table=new TableView('product_info_search_list','id');
		$table->CheckBox('_ids[]','id');
		$view=new ProductInfoView(new ProductInfoModel(13));
		$table->SetFieldConf($view->getFC());
		$table->SetSort($args['orderby'],$args['desc_or_asc']);
		$table->setTitle('bc_sn');
		$data=$this->FormatData($data);
		echo $table->ShowList($data,$pageData,$count);
		exit;
	}
	//add by zhangruiying
	function FormatData($data)
	{
		if($data)
		{
			$view=new ProductInfoView(new ProductInfoModel(13));
			foreach($data['data'] as $key=>$v)
			{
				$data['data'][$key]['from_type']=isset($this->from_type[$v['from_type']])?$this->from_type[$v['from_type']].$v['p_sn']:'';
				$data['data'][$key]['online']=$this->dd->getEnum('sales_channels_class',$view->get_channel_class($v['channel_id']));
				$data['data'][$key]['channel_id']=$view->get_channel_name($v['channel_id']);
				$data['data'][$key]['customer_source_id']=$view->get_customer_name($v['customer_source_id']);
				$data['data'][$key]['status']=$this->dd->getEnum('buchan_status',$v['status']);
				$data['data'][$key]['qiban_type']=$this->dd->getEnum('qiban_type',$v['qiban_type']);
				$data['data'][$key]['diamond_type']=$this->dd->getEnum('diamond_type',$v['diamond_type']);
				$data['data'][$key]['buchan_fac_opra']=$this->dd->getEnum('buchan_fac_opra',$v['buchan_fac_opra']);
				$data['data'][$key]['is_combine']=$v['is_combine']==1?'是':'否';
				if($v['esmt_time']<date('Y-m-d') and in_array($v['status'],array(4,7)))
				{
					$data['data'][$key]['esmt_time']="<b style=\"color:red;\">{$v['esmt_time']}</b>";
				}
				else if($v['esmt_time']<date('Y-m-d',time()+3*86400) and in_array($v['status'],array(4,7)) and $v['esmt_time']>=date('Y-m-d'))
				{
					$data['data'][$key]['esmt_time']="<b style=\"color:green;\">{$v['esmt_time']}</b>";

				}
				if(empty($v['time']))
				{
					$data['data'][$key]['time']=$v['edit_time']?$v['edit_time']:$v['add_time'];
				}
			}
			return $data;
		}
		return array();
	}

	/**
	 * 渲染 现货查询页面
	 */
	public function hasSreach()
	{
		$bc_id = _Post::getInt('bc_id');
		$style_sn = _Post::getString('style_sn');
		$order_gd_id = _Post::getInt('order_gd_id');
		//获取订单号
		$Productmodel = new ProductInfoModel($bc_id,13);
		$warehouseModel = new WarehouseModel(21);
		
		$order_sn = $Productmodel->getValue('p_sn');
        $warehouseList = $warehouseModel->getAllWarehouse();
        $zhuchengseList   = $warehouseModel->getChuchengseList();
		
		$bar = Auth::getViewBar();
		$combineXQShow = 0;//【组合镶嵌按钮】权限 1显示  0隐藏
		if(preg_match('/act\=combineXQ/is',$bar)){
		    $combineXQShow = 1;
		}
		$model = new AppProcessorInfoModel(13);
		$this->render('product_has_goods_search.html',[
			'view'=>$model,'bc_id'=>$bc_id,'order_gs_id'=>$order_gd_id,
			'goods_sn'=>$style_sn,
			'order_sn' => $order_sn,
            'yanse' => $this->yanse,
            'jingdu' => $this->jingdu,
		    'combineXQShow'=>$combineXQShow,
		    'warehouseList'=>$warehouseList,
		    'zhuchengseList'=>$zhuchengseList
		]);
	}

	/**
	 * 货品查询
	 */
	public function goodsSreach($params)
	{
		unset($params['mod']);unset($params['con']);unset($params['act']);
		unset($params['bc_id']);unset($params['order_gs_id']);
		$params = array_filter($params);
                if(array_key_exists("goods_id", $params)) {  //isset不好使
                    $params['goods_id'] = trim($params['goods_id']);
                }
                if (array_key_exists("shoucun", $params)) {
                    $params['shoucun'] = trim($params['shoucun']);
                }
                if (array_key_exists("zhushidaxiao_1", $params)) {
                    $params['zhushidaxiao_1'] = trim($params['zhushidaxiao_1']);
                }
                if (array_key_exists("zhushidaxiao_2", $params)) {
                    $params['zhushidaxiao_2'] = trim($params['zhushidaxiao_2']);
                }
                if (array_key_exists("zhuchengse_1", $params)) {
                    $params['zhuchengse_1'] = trim($params['zhuchengse_1']);
                }
                if (array_key_exists("zhuchengse_2", $params)) {
                    $params['zhuchengse_2'] = trim($params['zhuchengse_2']);
                }

		$result = array('success' => 0,'error' => '','goods_info'=>array());
		$houseModel = new ApiWarehouseModel();
		$data = $houseModel->getGoodsInfo($params);
		if(!is_array($data[0]) || empty($data[0])){
			$result['error'] = "未查到商品";
		}else{
			$result['success'] = 1;
			$result['goods_info'] = $data;
		}
		$this->render('product_has_goods_list.html',[
			'success'=>$result['success'],'error'=>$result['error'],
			'goods'=>$result['goods_info']
		]);
		// Util::jsonExit($result);
	}

	/**
	 * 取消布产,绑定商品,商品下架
	 */
	public function cannelBC(){

		$bc_id = _Post::getInt('bc_id');
		$goods_id = _Post::getString('goods_id');
		$order_gs_id = _Post::getInt('order_gs_id');
		$order_sn = _Post::getString('order_sn');
		
		$wareHouseModel = new WarehouseModel(22);
		$salesModel = new SalesModel(28);
		$salesPolicyModel = new SalepolicyModel(18);
		$model = new ProductInfoModel(14);
		
		$pdolist[22] = $wareHouseModel->db()->db();
		$pdolist[28] = $salesModel->db()->db();
		$pdolist[18] = $salesPolicyModel->db()->db();
		$pdolist[14] = $model->db()->db();
	    try{
    	    foreach ($pdolist as $pdo){
    	        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
    	        $pdo->beginTransaction(); //开启事务
    	    }
        }catch (Exception $e){
            $error = "操作失败，事物回滚！提示：系统批量开启事物时发生异常！";
            Util::rollbackExit($error,$pdolist);
        }
        //查询所选货品是否可以绑定
        $wh_goods = $wareHouseModel->selectWarehouseGoods("is_on_sale,order_goods_id","goods_id='{$goods_id}'",2);
        if(empty($wh_goods)){
            $error ="货品{$goods_id}不存在";
            Util::rollbackExit($error,$pdolist);
        }else if($wh_goods['is_on_sale'] !=2){
            $error ="货品{$goods_id}不是库存状态，不能绑定";
            Util::rollbackExit($error,$pdolist);
        }else if(!empty($wh_goods['order_goods_id'])){
            if($order_gs_id==$wh_goods['order_goods_id']){
                $error ="货品{$goods_id}已被本布产单绑定过，不能再次绑定";
                Util::rollbackExit($error,$pdolist);
            }
        }
        
		//绑定商品
		//检查布产单订单是否已绑定,已绑定过，自动解绑
		$bind_goods = $wareHouseModel->selectWarehouseGoods("goods_id","order_goods_id='{$order_gs_id}'",2);
		$unBindRemark = "";
	    if(!empty($bind_goods['goods_id'])){
	        
	        $unBindRemark = ",原货号{$bind_goods['goods_id']}自动解绑";	        
	        $data = array('order_goods_id'=>'');
	        $res = $wareHouseModel->updateWarehouseGoods($data,"order_goods_id='{$order_gs_id}'");
	        if(!$res){
	            $error ="操作失败,事物回滚!提示：解绑原货号失败";
	            Util::rollbackExit($error,$pdolist);
	        }
	        
	    }
	    //绑定新货号
		$data = array('order_goods_id'=>$order_gs_id);
		$res = $wareHouseModel->updateWarehouseGoods($data, "goods_id='{$goods_id}'");
		if(!$res){
		    $error ="绑定商品失败";
		    Util::rollbackExit($error,$pdolist);
		}		
		//商品下架
		/* $res2 = $salesPolicyModel->EditIsSaleStatus($goods_id,'0','2');		
		if(!$res2 || $res2==-1){			
			if($res2==-1){
				$error ="此商品已经下架，或者已经销售";;
				Util::rollbackExit($error,$pdolist);
			}						    
			$error ="商品下架失败";
			Util::rollbackExit($error,$pdolist);
		} */
		
		//修改布产单的布产状态
		$res3=$model->channelBC($bc_id);
		if(!$res3){		
			$error ="修改布产单的布产状态失败";
			Util::rollbackExit($error,$pdolist);
		}
		
		
		//修改订单的明细的 布产状态
		//判断是布产单是否有关联货品 若关联 更新布产操作状态到 货品详情表 BY CAOCAO
		$rec = $model->judgeBcGoodsRel($bc_id);
		if(!empty($rec)){
			$res4=$salesModel->UpdateOrderDetailStatus($order_gs_id);
			if(!$res4){					
				$error ="修改订单的明细的布产状态失败";
				Util::rollbackExit($error,$pdolist);
			}
		}
		//添加 不需布产日志
		$bc_sn = $model->get_bc_sn($bc_id);
		$bindRemark = "布产单".$bc_sn."不需布产；绑定现货".$goods_id;
		$buchanRemark = $bindRemark.$unBindRemark;		
		$res5=$model->addBcGoodsLog($bc_id,$buchanRemark);
		if(!$res5){				
			$error ="推送到不需布产失败".$res5;
			Util::rollbackExit($error,$pdolist);
		}
		
		$proArr=$model->getfromtype($bc_id);
		
		//布产类型是订单时 begin
		if($proArr['from_type']==2){
		    
		    //更新订单商品明细 begin
		    $data = array('goods_id'=>$goods_id);
		    $res = $salesModel->updateOrderDetail($data,"id={$order_gs_id}");
		    if(!$res){
		        $error ="同步新货号到订单商品明细失败";
		        Util::rollbackExit($error,$pdolist);
		    }
		    
    		//推送到订单 的日志
    		$orderLogRemark = $buchanRemark;
    		$res6=$salesModel->AddOrderLog($order_sn,$orderLogRemark);
    		if(!$res6){	
    			$error ="添加订单日志失败";
    			Util::rollbackExit($error,$pdolist);
    		}
		
			//再查一遍订单状态
			$new_order_info = $salesModel->GetOrderInfo($order_sn);
		    //天生一对加盟商的订单,不需布产后 判断是否改变订单明细的配货状态为 允许配货  2016-02-16
    		if($new_order_info['referer'] == "天生一对加盟商"){  
    		    if(in_array($new_order_info['order_pay_status'],array(2,3,4))){             
        		    $data = array('delivery_status'=>2);
        		    $res = $salesModel->updateOrderDetail($data,"id={$order_gs_id}");
        		    if(!$res){
        		        $error ="更新天生一对加盟商订单更改配货状态失败";
        		        Util::rollbackExit($error,$pdolist);
        		    }
    		    }    
            }
                    
			//当已经变成全款时或者财务备案
			if($new_order_info['order_pay_status']==3 || $new_order_info['order_pay_status']==4){
                $order_id=$new_order_info['id'];
                $is_peihuo = false;				
                $order_detail_data = $salesModel->getOrderDetailByOrderId($order_id);
                if(!empty($order_detail_data)){
                	$is_peihuo = true;
                }
                 
                // var_dump($order_detail_data);
                foreach($order_detail_data as $tmp){
                	$is_stock_goods = $tmp['is_stock_goods'];
                	$buchan_status = $tmp['buchan_status'];
                	$is_return = $tmp['is_return'];
                	
                	//排除：现货货号 + 不需布产的 + 已出厂
                	if($is_stock_goods == 0 && $buchan_status !=9 && $buchan_status != 11 && $is_return != 1 ){
                		$is_peihuo = false;
                	}
                }
                if($is_peihuo && $new_order_info['delivery_status']==1){
                	//更改订单状态
                   $res7=$salesModel->EditOrderdeliveryStatus($order_sn);//更改订单的配货状态为已配货 更改订单的布产状态为已出厂
                   if(!$res7){
                	   	$error ="更改订单状态失败";
                	   	Util::rollbackExit($error,$pdolist);
                   }
                }
	
			 }else{
                //只更改布产状态更改订单状态
                $res8=$salesModel->updateOrderBCStatus($order_sn);
                if(!$res8){						   		
                    $error ="更改布产状态失败";
                    Util::rollbackExit($error,$pdolist);
                }
			 }
	   }//布产类型是订单时 end 
	   try{
	       //批量提交事物
	       foreach ($pdolist as $pdo){
	           $pdo->commit();
	           $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
	       }
	       $result['success'] = 1;
	       $result['info'] = "操作成功,该布产产品已选择货号：".$goods_id;
	       Util::jsonExit($result);	   
	   }catch (Exception $e){
	       $error = "操作失败，事物回滚！提示：系统批量提交事物时发生异常！";
	       Util::rollbackExit($error,$pdolist);
	   }
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('product_info_info.html',array(
			'view'=>new ProductInfoView(new ProductInfoModel($id,13))
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}
		
	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		//这里是供应商管理那边所接受数据参数
		$id=isset($params['id'])?intval($params["id"]):$_GET['bc_id'];
		$procModel=new CProcessorModel(14);
		$res = $procModel->upateJinzhongByBcID($id);
		
		$model = new ProductInfoModel($id,13);
		$row = $model->getDataObject();
		if(empty($row)){
		    exit("布产单查询失败！");
		}
		

		
		$from_type = $row['from_type'];
		$order_detail_id = $row['p_id'];
		$buchan_status = $row['status'];
		$order_sn = $row['p_sn'];
		$style_sn = $row['style_sn'];
		
		$where['bc_id'] = $id;
		//工厂流水
		$facOpraModel = new ProductFactoryOpraModel(13);
		$fac_opra_list = $facOpraModel->pageList($where);
		//oqc质检情况
		$oqcOpraModel = new ProductOqcOpraModel(13);
		$oqc_opra_list = $oqcOpraModel->pageList($where);
		//出货情况
		$shipModel = new ProductShipmentModel(13);
		$shipment_list = $shipModel->pageList($where);
		foreach ($shipment_list as $k=> $v){
			$shipment_list[$k]['oqc_result']=$v['oqc_result']==1?"质检通过"	:"质检未过";
			$newmodel = new ProductFqcConfModel($v['oqc_no_type'],13);
			$cat_name1=$newmodel->getValue('cat_name');
			$shipment_list[$k]['oqc_no_type']=$cat_name1?$cat_name1:'';
			$newmodel = new ProductFqcConfModel($v['oqc_no_reason'],13);
			$cat_name2=$newmodel->getValue('cat_name');
			$shipment_list[$k]['oqc_no_reason']=$cat_name2?$cat_name2:'';
		}
		

        //获取客户姓名 如果是采购单，则存取的是制单人
        $SalesModel = new SalesModel(28);
        $warehouseModel = new WarehouseModel(21); 
        $p_sn = $model->getStyleSnById(array('bc_id'=>$id));
        $consignee = $p_sn['consignee'];

		$bc_note = $this->getFactoryStyle($row);
		//将布产提示,工厂模号和默认工厂写入布产信息表
		$p_data = array(
		    'def_factory_sn'=>$bc_note['factory_sn'],
		    'def_factory_name'=>$bc_note['factory_name']		    
		);
		$res = $model->update($p_data,"id={$id}");
		//edit end
		//$bc_note['factory_name']=empty($row['prc_name'])?$bc_note['factory_name']:$row['prc_name'];
        $bc_note['factory_name']=empty($row['prc_name'])?$bc_note['factory_id']:$row['prc_id'];
		//图片模板
		$gallery_data=$this->getStyleAllImages($style_sn,$id);
		//如果接口没取到图读取上传的图片
		$gallery_html = $this->fetch('product_gallery.html',array(
			'bc_note' =>$bc_note,		//布产提示信息
			'res_pic_list' =>$gallery_data
		));
		//布产单绑定货号查询(BOSS-398需求，如果是订单类型，需要显示绑定的货号)
		$bind_goods_id = "";
		if($from_type ==2 && !empty($order_detail_id)){    
    		$bindRow = $warehouseModel->getBCGoodsHasBind($order_detail_id);
		    if(!empty($bindRow['goods_id'])){
		        $bind_goods_id = $bindRow['goods_id'];
		    }
		}
		$styleselfmodel = new StyleModel(11);
		//取出款号属性信息
		$style_zhuaxz = $styleselfmodel->getAttrByStyleSn($style_sn, "爪钉形状");
		$style_attr = array(
			'style_zhuaxz' => $style_zhuaxz,
		);
		$productInfo4CModel = new ProductInfo4CModel($id,13);
		$productInfo4C = $productInfo4CModel->getDataObject();	
        //如果布产单不符合4C证书号更改条件的，删除权限按钮
		$bar = Auth::getViewBar();	
		//bar 权限过滤 begin
		if(empty($productInfo4C)){		    
		   $bar = preg_replace('/<div class="btn-group">((?!div).)*edit_4c.*?<\/div>/is','',$bar);
		}
		//现货组合镶嵌按钮过滤
		$bar = preg_replace('/<div class="btn-group">((?!div).)*act\=combineXQ".*?<\/div>/is','',$bar);
		//【修改组合镶嵌】按钮过滤
		if(in_array($buchan_status,array(1,11,7,9))){
		    $bar = preg_replace('/<div class="btn-group">((?!div).)*act\=editCombineXQ.*?<\/div>/is','',$bar);
		}
		//bar 权限过滤 end

		$referer='';
        $channel_class = '';
		if($from_type ==2 && !empty($order_sn)){
			$rel=$SalesModel->getOrderCloseArr($order_sn);
			//订单来源
			$refererArr=$SalesModel->getBaseOrderInfoByOrderSn('referer',$order_sn);
            $channel_class = $SalesModel->getOrderClass($order_sn);
            $channel_class = $channel_class == '1' ? '线上':'线下';
			$referer = $refererArr['referer'];
			
			if($rel != 0){
				$is=$rel;
			}else{
				if(!empty($order_detail_id)){					
					 $rel1=$SalesModel->getOrderReturnArr($order_sn,$order_detail_id);					
					 $is=$rel1;
					
				}else{
					$is=0;
				}
			}
			
		}else{
			$is=0;
		}
		$this->render('product_info_show.html',array(
			'bar'=>$bar,
			'view'=>new ProductInfoView($model),
		    'productInfo4C'=>$productInfo4C,
            'channel_class' =>$channel_class,
			'fac_opra_list'	=> $fac_opra_list,
			'oqc_opra_list'	=> $oqc_opra_list,
			'shipment_list'	=> $shipment_list,
			'gallery_html' => $gallery_html,
			'from_type' => $from_type,
        	'consignee' => $consignee,
			'bind_goods_id'=>$bind_goods_id,
			'ist'=>$is,	
			'referer'=>$referer,
			'style_attr' => $style_attr,
		));

	}
	function check_remote_file_exists($url){
		$curl = curl_init($url);
		// 不取回数据
		curl_setopt($curl, CURLOPT_NOBODY, true);
		// 发送请求
		$result = curl_exec($curl);
		$found = false;
		// 如果请求没有发送失败
		if ($result !== false) {
			// 再检查http响应码是否为200
			$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			if ($statusCode == 200) {
				$found = true;
			}
		}
		curl_close($curl);
		return $found;
	}
	//add by zhangruiying
	function UploadImg($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params["id"]);
		$where['bc_id'] = $id;
		//获取款号
		$productModel = new ProductInfoModel(13);
		$style_sn = $productModel->getStyleSnById($where);
		$style_sn = $style_sn['style_sn'];
		$gallerymodel = new ApiStyleModel();
		$gallery_data = $gallerymodel->getProductGallery($style_sn,1);
		if(isset($gallery_data[0]['thumb_img']))
		{
			if(!empty($gallery_data[0]['thumb_img']) and $this->check_remote_file_exists($gallery_data[0]['thumb_img']))
			{
				$result['content']='布产图片已存在不允许重复添加';
			}
		}
		else
		{
			$imgModel=new ProductInfoImgModel(13);
			$gallery_data=$imgModel->getImgList($id);
			if(!empty($gallery_data))
			{
				$result['content']='布产图片已存在不允许重复添加';
			}
			else
			{
				//布产单ID
				$result['content'] = $this->fetch('add_img.html',array(
					'id' => $id,
				));
				$result['title'] = '添加图片';
			}

		}
                //记录操作日志
                $logModel = new ProductOpraLogModel(14);
                //$logModel->addLog($id,3,"生成配石单".$last_id);
                $logModel->addLog($id,"布产单上传图片");
		Util::jsonExit($result);
	}
	function InsertImg($params)
	{
		$result = array('success' => 0,'error' => '');
		$id=$params['id'];

		if(empty($_FILES))
		{
			$result['error']='请上传图片后再提交';
		}
		else
		{
			$imgs=$_FILES['img'];


			$upload=new Upload(0.5,array('jpg','png','gif','jpeg'));
			$upload->save_path='./public/upload/img/productinfo/';
			$upload_resault=$upload->uploadfile($imgs);
			$arr=array();
			$error='';
			foreach($upload_resault as $key=>$v)
			{
				if(is_array($v))
				{
					$temp=array();
					$temp['save_path']=$v['path'];
					$temp['save_name']=$v['name'];
					$temp['product_info_id']=$id;
					$temp['create_user_id']=$_SESSION['userId'];
					$temp['create_user_name']=$_SESSION['userName'];
					$arr[]=$temp;
				}
				else
				{
					//$error.=$v."<br>";
                                        $error .= $v;
				}
			}
			if(!empty($arr))
			{
				$imgModel=new ProductInfoImgModel(13);
				$res=$imgModel->insert($arr);
				if($res!=false)
				{
					$result['success'] =1;
				}
				else
				{
					$result['error']='添加到数据库失败请联系开发人员';
				}
			}
			if($error!=''){

				$result['error']=$error;
			}

		}
		Util::jsonExit($result);
	}
	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = $params['id'];

		$newmodel =  new ProductInfoModel($id,14);
		$newmodel->setValue('num',$params['num']);
		$newmodel->setValue('info',$params['info']);
		$res = $newmodel->save();

		if($res !== false)
		{
			$attrModel =  new ProductInfoAttrModel(14);
			$attrModel->delGoodsAttr($id);//删除原来的数据，再新增一次。
			foreach($params as $key => $val)
			{
				if(is_array($val))
				{
					$attr_arr = array(
						'g_id' => $id,
						'code' => $key,
						'name' => $val[1],
						'value' => $val[0]
					);
					$attrModel->saveData($attr_arr,array());
				}
			}

			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

/*
	//分配跟单人
	public function sel_opra_uname($params)
	{
		//var_dump($_REQUEST);exit;
		$id = intval($params["id"]);
		$c = _Request::get("c");

		$result = array('success' => 0,'error' => '');

		$model = new ProductInfoModel($id,14);
		$status = $model->getValue('status');
*/
			/***************
		****开始生产之前都可以操作分配跟单人
		如果是以分配状态需要清空分配的工厂
		1053 	已分配 	3
		1051 	待分配 	2
		1049 	初始化 	1
		****************/
/*		if($status != 1 && $status !=2 && $status !=3)//只有初始化状态下才能分配跟单人
		{
			$result['title'] = '分配跟单人';
			$result['error'] = "分配工厂前才可以分配跟单人";
			$result['content'] = "分配工厂前才可以分配跟单人";
			Util::jsonExit($result);
		}
		if ($status == 3)
		{
			$model->setValue('prc_id',0);
			$model->setValue('prc_name','');
			$result_l = $model->save(true);
			if(!$result_l)
			{
				$result['title'] = '分配跟单人';
				$result['error'] = "工厂清除失败";
				$result['content'] = "工厂清除失败，请重新分配";
			}
		}


		if($c != "sub")//显示添加页面
		{
			//获取跟单人列表
			$gen_list = $this->getGendanList();
			$result['content'] = $this->fetch('sel_opra_uname.html',array(
				'id' => $id,
				'gen_list'	=> $gen_list
			));
			$result['title'] = '分配跟单人';
			Util::jsonExit($result);
		}

		$params['from_type'] = $model->getValue('from_type');
		$res = $model->sel_opra_uname($params);

		if($res){
			$result['success'] = 1;
		}else{
			$result['error'] = "分配失败";
		}
		Util::jsonExit($result);

	}*/
		
    /**
     * 4C配钻选择共产是检查
     */
    private function _checkPeishiFor4C($bcInfo){
        $result = array('content'=>'','title'=>'分配工厂');
        if(empty($bcInfo)){
            $result['content'] = "布产记录为空,error：".__LINE__;
            Util::jsonExit($result);
        }
        $id = $bcInfo['id'];
        $bc_sn = $bcInfo['bc_sn'];
        $is_peishi = $bcInfo['is_peishi'];
        $bucan_status = $bcInfo['status'];
        $order_detail_id = $bcInfo['p_id'];//app_order_details 的主键ID值
        $order_sn = $bcInfo['p_sn'];
        $consignee= $bcInfo['consignee'];
        //裸钻4C布产单
        if($is_peishi==1){
            $productInfo4CModel = new ProductInfo4CModel($id,14);
            $data = $productInfo4CModel->getDataObject();
            if(empty($data)){
                $result['content'] = "4C裸钻布产记录查询失败,error：".__LINE__;
                Util::jsonExit($result);
            }else if($data['peishi_status']==0){
                $result['content'] = "当前裸石还未完成4C配钻,请先配钻！提示：需要等待钉钻部对该裸石进行4C配钻后，才可以分配工厂";
                Util::jsonExit($result);
            }
        
        }
        else if($is_peishi==2){
            $zhengshuhao = '';//查询当前空托的证书号
            $productAttrModel = new ProductInfoAttrModel(13);
            $dataAttr = $productAttrModel->getGoodsAttr($id);
            //print_r($dataAttr);
            foreach($dataAttr as $val){
                if($val['code']=="zhengshuhao"){
                    $zhengshuhao = $val['value'];
                }
            }
            if($zhengshuhao==''){
                $result['content'] = "当前空托的证书号没有填写！";
                Util::jsonExit($result);
            }
            
            //检查空托是否配石
            $productInfo4CModel = new ProductInfo4CModel(14);
            $checkHasPeishi = $productInfo4CModel->getRow('*',"kt_order_detail_id='{$order_detail_id}'");
            if($checkHasPeishi){
                return ;
            }
            
            //检查证书号对应裸钻是否已经下单            
            $salesModel = new SalesModel(27);
            $diamond_info = array() ;
            //匹配裸钻 begin
            $order_detail_list = $salesModel->getOrderDetailsFor4C($zhengshuhao,false);
            foreach($order_detail_list as $vo){
                if($vo['consignee'] ==$consignee){
                    $diamond_info = $vo;
                }
                if($order_sn ==$vo['order_sn']){
                    //与空托订单号一样的裸钻，优先匹配
                    break;
                }
            }

            if(empty($diamond_info)){
                $result['content'] = "当前空拖关联的裸钻【证书号：<font color='red'>{$zhengshuhao}</font>】还未下单。";
                Util::jsonExit($result);
            }else if($diamond_info['is_peishi']<1){
                $result['content'] = "当前空拖关联的裸钻【证书号：<font color='red'>{$zhengshuhao}</font>】 不支持4C配钻，此空托镶嵌方式可能有误！当下解决方案：将空托镶嵌方式改为“非镶嵌4C裸钻”方式，订单按非4C流程走";
                Util::jsonExit($result);
            }
            //匹配裸钻 end 
                   
            if(empty($checkHasPeishi)){
                $result['content'] = "当前空拖关联的裸石还未完成4C配钻，请先配钻！。提示：需要等待钉钻部对此空托镶嵌的裸石【证书号：<font color='red'>{$zhengshuhao}</font>】进行4C配钻后，才可以分配工厂";
                Util::jsonExit($result);
            }
        	  
        }
    }
    
    /**
     * 保存分配工厂
     * @param unknown $params
     */
    public function sel_factory_save($params){
        
        $result = array('success' => 0,'error' => '');
        
        $id = _Request::getInt('id');//布产单ID
        $prc = _Request::getString('prc');
        $prc_arr = explode('|',$prc);
        if(empty($id)){
            $result['error'] = "id is empty!";
            Util::jsonExit($result);
        }
        if(empty($prc_arr) || count($prc_arr)<>2){
            $result['error'] = "分配工厂不能为空";
            Util::jsonExit($result);
        }
        
        $factory = array('id'=>$prc_arr[0],'name'=>$prc_arr[1]); 

        $model = new ProductInfoModel(14);
        $salesModel = new SalesModel(27);
        
        $pdolist[14] = $model->db()->db();
        $pdolist[27] = $salesModel->db()->db();//销售订单模块数据库27
        try{
            //开启事物
            foreach ($pdolist as $pdo){
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
                $pdo->beginTransaction(); //开启事务
            }
            $res = $this->selFactorySaveOne($id, $factory,"分配工厂");
            if($res['success']==0){
                $error = $res['error'];
                Util::rollbackExit($error,$pdolist);
            }            
            //$error = "success";
            //Util::rollbackExit($error,$pdolist);
            //批量提交事物
            foreach ($pdolist as $pdo){
                $pdo->commit();
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
            }
        }catch (Exception $e){
            $error = "分配工厂失败2!".$e->getMessage();
            Util::rollbackExit($error,$pdolist);
        }
        $result['success'] = 1;
        Util::jsonExit($result);
    } 
    
	/**
	* 分配工厂
	* 1/工厂绑定了默认跟单人，绑定工厂的同时 也绑定了跟单人
	* 2/没有款的，分配工厂时，不用去读接口了，拉出所有工厂列表让它选，
	* 3/选工厂时，选中的工厂没有跟单人，打死不让过~~~~~~~~
	* @date 2015-04-21 BY CAOCAO
	*/
	public function sel_factory($params){

		$result = array('success' => 0,'error' => '');
		$id = intval($params["id"]);		//布产单ID
		$c = _Request::get("c");
		$model = new ProductInfoModel($id,14);		
		$productInfoData = $model->getDataObject();
		$this->_checkPeishiFor4C($productInfoData);
		
		//布产列表，采购类型的布产单，不允许点击【分配工厂】，只能在采购列表或者采购布产列表分配工厂
		$from_type = $model->getValue('from_type');
		if($from_type==1)
		{
			$result['title'] = '分配工厂';
			$result['content'] = "采购类型布产单，不允许点击【分配工厂】，只能在采购列表或者采购布产列表分配工厂！";
			Util::jsonExit($result);
		}

		$status = $model->getValue('status');
		/***************
		****开始生产之前都可以操作分配跟单人,如果是以分配状态需要清空分配的工厂
		1532 	已分配 	11
		1053 	已分配 	3
		1051 	待分配 	2
		1049 	初始化 	1
		****************/
		if($status != 1 && $status !=2 && $status !=3 && $status !=11)		//只有初始化、待分配、已分配、不需布产状态下才能分配工厂
		{
			$result['title'] = '分配工厂';
			$result['error'] = "当前单据状态不对，不能分配工厂";
			$result['content'] = "当前单据状态不对，不能分配工厂";
			Util::jsonExit($result);
		}



		//获取供应商列表
		$style_sn = $model->getValue('style_sn');
		if(SYS_SCOPE=='zhanting'  && $style_sn=='DIA'){
                $res = $model->checkCompanyType(array($id));
				if($res['success'] == 0)
				{
					$result['title'] = '批量分配工厂';
					$result['content'] = $res['error'];
					Util::jsonExit($result);
				}
				if($res['company_type']==3)
                    $fac_list = $model->getFactoryUserByID(639); //个体店裸钻布产单指定分配给工厂优星钻石（上海）有限公司
				else
				    $fac_list =	$model->getFactoryUserByID(510);    //个体店裸钻布产单指定分配给批发合作商
		}else{

				$apiModel = new ApiStyleModel();
				if(strtolower($style_sn)=="dia" || $style_sn ==""){
				    $fac_list = array();
				}else{
				    $fac_list = $apiModel->getFactryInfo($style_sn);
				}			
				$proFacUserModel = new ProductFactoryOprauserModel(13);
				if(empty($fac_list)){
					//如果布产的货品款号为空，那么分配的工厂的列表，从绑定了跟单人的工厂里选
					$facModel = new ProductFactoryOprauserModel(13);
					$fac_list = $facModel->select2($fields = '`prc_id`,`opra_user_id`,`opra_uname`,`production_manager_id`,`production_manager_name`' , $where = ' 1 ' , $type = 'all');
					foreach($fac_list AS $key => $val){
						$facModel = new AppProcessorInfoModel($val['prc_id'] , 13);
						$fac_list[$key]['factory_id'] = $val['prc_id'];
						$fac_list[$key]['factory_name'] = $facModel->getValue('name');
						$fac_list[$key]['code'] = $facModel->getValue('code');
						$fac_list[$key]['factory_sn'] = '';			//调用接口获取
						$fac_list[$key]['gendan'] = $proFacUserModel->select2($fields = '`opra_uname`' , $where = " `prc_id` = {$val['prc_id']} " , $type = 'one');
					}
				}else{
					//【布产类型】是【订单】时 过滤分配工厂数据
					if ($from_type == 2){
						//查看是否有起版号
						$sales = new ApiSalesModel();		
						$qb_id = $sales->getQiBanIdByWhere($model->getValue('p_sn'),$model->getValue('style_sn'));
						if(!empty($qb_id)){
							//有起版号
							$purchaseApi = new ApiPurchaseModel();
							$qiban_info = $purchaseApi->GetQiBianGoodsByQBId($qb_id);
							if (!empty($qiban_info) && $qiban_info['kuanhao'] != 'QIBAN'){
								foreach($fac_list AS $key => $val){
								//工厂列表只需要显示起版号中的工厂和款式库中的默认工厂
								   if ($qiban_info['gongchang_id'] != $val['factory_id'] && $val['is_factory'] == 0){
									  unset($fac_list[$key]);
								   }
								}
							}
						
						}else{
							//非起版号下单，分配的时候只需要显示该款在款式库中的默认工厂即可
							 foreach($fac_list AS $key => $val){
								if ($val['is_factory'] == 0){
									unset($fac_list[$key]);
								} 
							}
						}
					}
				
					foreach($fac_list AS $key1 => $val1){			//获取跟单人
						$fac_list[$key1]['gendan'] = $proFacUserModel->select2($fields = '`opra_uname`' , $where = " `prc_id` = {$val1['factory_id']} " , $type = 'one');
					}
				}		
        }
        
		//分配工厂数据需要过滤重复数据
		foreach($fac_list AS $key => $val){
			$new_fac_list[$val['factory_id']] = $val;
		}
		$fac_list = $new_fac_list;

		
		$result['content'] = $this->fetch('sel_factory.html',array(
			'id' => $id,
			'fac_list'	=> $fac_list
		));
		$result['title'] = '分配工厂';
		Util::jsonExit($result);
		
	}

	//批量分配工厂 ---页面显示  lyh 2015/4/28
	public function sel_factory_pl($params)
	{
		$ids = _Request::getList('_ids');
		$model = new ProductInfoModel(13);
		$proFacUserModel = new ProductFactoryOprauserModel(13);

		#1、检测所有状态及只能同款批量操作
		$res = $model->IsStatusFactory($ids);
		if($res['success'] == 0)
		{
			$result['title'] = '批量分配工厂';
			$result['content'] = $res['error'];
			Util::jsonExit($result);
		}
		//不能操作采购单据
		$types_num = $model->IsfromtypeCaigou($ids);
		if($types_num>0)
		{
			$result['title'] = '批量分配工厂';
			$result['content'] = '采购类型布产单，不允许点击【分配工厂】，只能在采购列表或者采购布产列表分配工厂！';
			Util::jsonExit($result);
		}
		if(SYS_SCOPE=='zhanting'  && $res['style_sn']=='DIA'){
                $res = $model->checkCompanyType($ids);			   
				if($res['success'] == 0)
				{
					$result['title'] = '批量分配工厂';
					$result['content'] = $res['error'];
					Util::jsonExit($result);
				}
				if($res['company_type']==3)
                    $fac_list = $model->getFactoryUserByID(639); //个体店裸钻布产单指定分配给工厂优星钻石（上海）有限公司
				else
				    $fac_list =	$model->getFactoryUserByID(510);    //个体店裸钻布产单指定分配给批发合作商
		}else{	
				#2、获取供应商列表及跟单人(无法查到款的工厂，则取所有带跟单人的工厂)
				$apiModel = new ApiStyleModel();
				$fac_list = $apiModel->getFactryInfo($res['style_sn']);
				//如果查不到此款的工厂信息，取全部跟单人的工厂
				if(empty($fac_list))
				{
					$fac_list = $model->getAllFactoryUser();
				}
				else
				{
					foreach($fac_list AS $key1 => $val1)
					{
						$fac_list[$key1]['gendan'] = $proFacUserModel->select2($fields = '`opra_uname`' , $where = " `prc_id` = {$val1['factory_id']} " , $type = 'one');
					}
				}
		}
        //print_r($fac_list);
		$result['content'] = $this->fetch('sel_factory_pl.html',array(
			'fac_list'	=> $fac_list,
			'ids'=> $ids
		));
		$result['title'] = '批量分配工厂';
		Util::jsonExit($result);
	}
	/**
	 * 批量分配工厂
	 * gaopeng
	 * @param unknown $params
	 */
	public function sel_factory_pl_save($params)
	{
		$result = array('success' => 0,'error' => '');
		//布产单ID
        $ids = _Request::getList('_ids');
        $prc = _Request::getString('prc');
        $prc_arr = explode('|',$prc);
        if(empty($ids)){
            $result['error'] = "_ids is empty!";
            Util::jsonExit($result);
        }
        if(empty($prc_arr) || count($prc_arr)<>2){
            $result['error'] = "分配工厂不能为空";
            Util::jsonExit($result);
        }
        
        $factory = array('id'=>$prc_arr[0],'name'=>$prc_arr[1]); 

        $model = new ProductInfoModel(14);
        $salesModel = new SalesModel(27);
        $pdolist[14] = $model->db()->db();
        $pdolist[27] = $salesModel->db()->db();//销售订单模块数据库27
        try{
            //开启事物
            foreach ($pdolist as $pdo){
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
                $pdo->beginTransaction(); //开启事务
            }
            foreach ($ids as $id){
                $res = $this->selFactorySaveOne($id, $factory,"批量分配工厂");
                if($res['success']==0){
                    $error = $res['error'];
                    Util::rollbackExit($error,$pdolist);
                }
            }
            
            //$error = "success";
            //Util::rollbackExit($error,$pdolist);
            //批量提交事物
            foreach ($pdolist as $pdo){
                $pdo->commit();
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
            }
        }catch (Exception $e){
            $error = "分配工厂失败2!".$e->getMessage();
            Util::rollbackExit($error,$pdolist);
        }
        $result['success'] = 1;
        Util::jsonExit($result);
	}
	/**
	 *分配工厂
	 * @param unknown $data
	 */
	protected function selFactorySaveOne($id,$factory,$type="分配工厂"){
	
	    $result = array('success' => 0,'error' => '');
	
	    $peishiListModel = new PeishiListModel(14);
	    $opraLogModel = new ProductOpraLogModel(14);
	    $model = new ProductInfoModel($id,14);
	    $attrModel = new ProductInfoAttrModel(14);
	    $productInfoData = $model->getDataObject();
	    $this->_checkPeishiFor4C($productInfoData);
	
	    $bc_sn = $model->getValue('bc_sn');
	    $p_sn = $model->getValue('p_sn');
	    $xiangqian = $model->getValue('xiangqian');
	    //布产列表，采购类型的布产单，不允许点击【分配工厂】，只能在采购列表或者采购布产列表分配工厂
	    $from_type = $model->getValue('from_type');
	    if($from_type==1)
	    {
	        $result['error'] = "采购类型布产单，不允许点击【分配工厂】，只能在采购列表或者采购布产列表分配工厂！";
	        return $result;
	    }
	
	    /***** 处理分配工厂逻辑 *****/
	    $disFacData = array(
	        "prc_id" => $factory['id'],
	        "prc_name" => $factory['name'],
	        "bc_id" => $id,
	        "from_type" => $from_type,
	        "bc_sn" => $bc_sn,
	        'p_sn' => $p_sn
	    );
	    $res = $model->DistributionFac($disFacData);
	    if($res['success']==0){
	        $result['error'] = $res['error'];
	        return $result;
	    }
	    $res = $peishiListModel->createPeishiList($id,'insert',$type);
	    if($res['success']==0){
	        $result['error'] = $res['error'];
	    }else{
	        $result['success'] = 1;	
	    }    
	    return $result;
	
	} 
	//开始生产
	public function to_factory($params)
	{
		$id = intval($params["id"]);
		$model = new ProductInfoModel($id,14);
		$pro_id = $model->getValue('pro_id');
		$from_type = $model->getValue('from_type');
		if(in_array($pro_id,[452,416]) && $from_type == 2){
           $this->to_factory_edit($params);
           exit;
        }
                $view = new ProductInfoView($model);
                $p_sn = trim( $view->get_p_sn() );
				$flag = stripos($p_sn, CGD_PREFIX);
                if ($flag === 0){
                    $rece_id = substr($p_sn,strlen(CGD_PREFIX));
				}
                $uid = $_SESSION['userId'];
                $uname = $_SESSION['userName'];
		$is_send=isset($params['is_send'])?intval($params['is_send']):0;
		$status = $model->getValue('status');
		if($status != 3)//只有已分配状态下才能分配跟单人
		{
			$result['success'] =0;
			$result['error'] = "只有已分配状态下才能开始生产。";
			Util::jsonExit($result);
		}
		//开始生产计算工厂标准出厂时间
		#1、获取该布产单据的加工商标准出厂加时信息
		$model_pw = new AppProcessorWorktimeModel(13);
		$prc_id = $model->getValue('prc_id');
		$from_type = $model->getValue('from_type');
		$order_type = $from_type==1?2:1;
		//计算标准出厂时间   --之前的逻辑
		/*
		$gendan_info = $model_pw->getInfoById($prc_id);
		if(empty($gendan_info))
		{
			$normal_time = time()+(3600*24);
			$time = date("Y-m-d",$normal_time);
		}else{
			$time = $model_pw->js_normal_time($gendan_info['normal_day'],$gendan_info['is_rest']);
		}
		$model->setValue('esmt_time',$time);  */

		$res1 = $this->updateEsmttimeById($id,$order_type);
		if($res1 ==false){
			$result['error'] ='更新出厂时间失败';
			Util::jsonExit($result);
		}

        //add by zhangruiying 添加接单时间
        $model->setValue('order_time',date('Y-m-d H:i:s'));
        $model->setValue('edit_time',date('Y-m-d H:i:s'));
        //add end
		$model->setValue('status',4);
		//变更布产单的生产状态为开始生产
		$model->setValue('buchan_fac_opra', 2);
		$pdo=$model->db()->db();
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
		$pdo->beginTransaction();//开启事务
		$res = $model->save(true);
		if($res !== false){

			//判断是布产单是否有关联货品 若关联 更新布产操作状态到 货品详情表 BY linian
			$rec = $model->judgeBcGoodsRel($id);
			if(!empty($rec)){
				$keys =array('update_data');
				$vals =array(array(array('id'=>$rec['goods_id'],'buchan_status'=>4)));
				$ret = ApiModel::sales_api($keys, $vals, 'UpdateOrderDetailStatus');
			}
			//记录操作日志
			$logModel = new ProductOpraLogModel(14);
			//$logModel->addLog($id,4,"布产单接单并开始生产");
			$logModel->addLog($id,"布产单接单并开始生产",4);
                        
                         //推送到采购日志去
                        if (isset($rece_id) && !empty($rece_id)){
							$bc_sn = $model->get_bc_sn($id);
                            $remark = '采购布产列表，布产单'.$bc_sn.'接单并开始生产；';
                            ApiModel::purchase_api(array('rece_id','status','remark','uid','uname'), array($rece_id,3,$remark,$uid,$uname), 'AddPurchaseLog');
                        }
                
			//如果选择了推送到工厂则调节口
			if($is_send)
			{
				$res=$this->send_to_factory($id);
				if($res['success']==0)
				{
					$result['success'] =0;
					$result['error'] = $res['error'];
				}
				else
				{
					$result['success'] =1;
				}
			}
			else
			{
				$result['success'] = 1;
			}
			$pdo->commit();
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
		}else{
			$pdo->rollback();
			$result['success'] =0;
			$result['error'] = "操作失败";
		}
               
		Util::jsonExit($result);
	}
	//检测是否有需要发送到工厂的布产单add by zhangruiying
	public function CheckSendFactory($params)
	{
		$result=array('success'=>0,'error'=>'');
		$ids=$params['_ids'];
		$model=new ProductInfoModel(13);
		$res=$model->CheckIsToFactory($ids,array('452','416'));
		if($res)
		{
			$result['success']=1;
		}
		Util::jsonExit($result);
	}
	//显示发送到工厂页面add by zhangruiying
	public function	StartProductionEdit($params)
	{
		$result = array('success' => 0,'error' => '');
		$ids =isset($params["_ids"])?$params["_ids"]:array();
		if(empty($ids))
		{
			$result['content']='没有选择操作数据';
			Util::jsonExit($result);
		}
		$model=new ProductInfoModel(13);
		$data=$model->getToFactoryList($ids,$prc_ids=array(452,416),2);
		$result['content'] = $this->fetch('send_to_factory.html',array(
		'data'=>$data,
		'ids'=>implode(',',$ids)
		));
		$result['title'] = '推送工厂生产';
		Util::jsonExit($result);

	}
	//批量开始生产edit by zhangruiying由一个状态错误整个无法开始生产变为状态错语的跳过并提示其它照常执行
	public function to_factory_pl($params)
	{
		$ids = $params['_ids'];
		if(empty($ids))
		{
			$result['error'] = "请至少选择一个布产单再提交";
			Util::jsonExit($result);
		}		
		$result = array('success'=>0,'error'=>'','is_refresh'=>0);
		$model = new ProductInfoModel(13);
		$data=$model->getToFactoryList($ids);
		$msg='你选择了{$m}个布产单，成功{$n}条，失败{$k}条<br />';
		if(empty($data))
		{
			$result['error'] = "订单状态必须是已分配才能开始生产。";
		}
		else
		{
			$success_ids=array_column($data,'id');
			$error_ids=array_diff($ids,$success_ids);//订单状态不对的
			if(!empty($error_ids))
			{
				$msg.=implode(',',$error_ids).'订单状态错误，只有已分配的才能开始生产！<br />';
			}
			$res = $model->to_factory_pl($success_ids);//可以生产的更改生产状态
			if($res['success'] == 0)
			{
				if(!empty($res['error']))
					$result['error']=$res['error'];
				else
				    $result['error'] ='操作失败请联系开发人员';
			}
			else
			{
				$msg=str_replace(array('{$m}','{$n}','{$k}'),array(count($ids),count($success_ids),count($error_ids)),$msg);
				$result['error'] = $msg;
				$send_ids=isset($params['send_ids'])?$params['send_ids']:'';
				if(!empty($send_ids))
				{
					$send_ids=explode(',',$send_ids);
					$send_error='工厂接口返回状态<br />';
					foreach($send_ids as $v)
					{
						$res=$this->send_to_factory($v);
						if($res['success']!=1)
						{
						    $bc_sn = $model->get_bc_sn($v, false);
							$send_error.= $bc_sn.':'.$res['error']."<br />";
						}
					}

					$result['error'].=$send_error;
				}
				$result['is_refresh']=1;
			}

		}

//		if(!$model->IsStatusFenpei($ids))
//		{
//			$result['error'] = "只有已分配状态下才能开始生产。";
//			Util::jsonExit($result);
//		}
//		$res = $model->to_factory_pl($success_ids);

//		if($res['success'] == 0)
//		{
//			$result['error'] = $res['error'];
//			Util::jsonExit($result);
//		}
//		else
//		{
//			$result['error'] = '批量操作成功';
//			$result['success'] = 1;
//		}
		Util::jsonExit($result);
	}
	//获取跟单人列表
	public function getGendanList()
	{
		$roleUserModel = new RoleUserModel(1);
		//取角色为工厂跟单人的用户
		$gen_list = $roleUserModel->getRoleUserList(4);

		return $gen_list;
	}
        public function searchOpraLog($params)
	{
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'_id' => _Request::get("_id"),
		);
		$page = _Request::getInt("page",1);
		$where = array();
		$where['_id'] = $args['_id'];
		$model = new ClothProductionTrackingLogModel(13);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'ClothProductionTrackingLog_search_page';
		$this->render('rel_opra_log.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
		));
	}

	public function getImg($style_sn,$id,$type=1)
	{
		$gallerymodel = new ApiStyleModel();
		$gallery_data = $gallerymodel->getProductGallery($style_sn,1);
		if(!isset($gallery_data[0]['thumb_img']))
		{
			$gallery_data=array();
			$imgModel=new ProductInfoImgModel(13);
			$temp=$imgModel->getImgList($id);
			if(!empty($temp))
			{
				$gallery_data[0]=$temp;
			}
		}
		else
		{
			//图片是否真实存在
			if($this->check_remote_file_exists($gallery_data[0]['thumb_img'])==false)
			{
				$imgModel=new ProductInfoImgModel(13);
				$temp=$imgModel->getImgList($id);
				if(!empty($temp))
				{
					$gallery_data[0]=$temp;
				}
				else
				{
					$gallery_data=array();
				}
			}
		}
		return $gallery_data;

	}

	public function getStyleAllImages($style_sn,$id=0) {
		$gallerymodel = new ApiStyleModel();
		$row=$gallerymodel->getStyleGalleryList($style_sn);
		if(empty($row))
		{
			$imgModel=new ProductInfoImgModel(13);
			$temp=$imgModel->getImgList($id);
			if(!empty($temp))
			{
				$row[0]=$temp;
			}
		}
		return $row;
	}

 /**
     * 获取【有款】 布产订单对应的 工厂和 模号，【无款】按照以前默认逻辑获取
     * @param array
     * $row(id=>'product_info的主键',from_type=>'来源类型',
     * style_sn=>'款式',p_sn=>'订单编号',prc_id=>'工厂Id',prc_name=>'工厂名称')
     * @return
     */
	public function getFactoryStyle($row=array()){
		$from_type = $row['from_type'];
		$style_sn  = $row['style_sn'];//款式编号
		$order_sn  = $row['p_sn'];//订单编号
		$factory_id= $row['prc_id'];//分配工厂Id
		$bc_note    =  array();

		$styleApi = new ApiStyleModel();
		$purchaseApi = new ApiPurchaseModel();

		$sales = new ApiSalesModel();
		
		$purchaseModel = new ProductInfoPurchaseModel(13);
		
		if ($from_type == 2){
			//2 为订单布产单
			if(empty($factory_id)){
				//如果还没有分配工厂
				$qb_id = $sales->getQiBanIdByWhere($order_sn,$style_sn);

				if(!empty($qb_id)){
					//起版号下单
					$qiban_info = $purchaseApi->GetQiBianGoodsByQBId($qb_id);
					if($order_sn != '' && $style_sn !='' && $style_sn!='QIBAN'){
					   //有款起版
						if(!empty($qiban_info)){
							$factory = $styleApi->GetFactory($qiban_info['gongchang_id'],$style_sn);
							if (!empty($factory) && $factory['is_factory'] == 1){
								//如果起版单中工厂维护的是款号的默认工厂,根据石重算镶口
								$attrModel = new ProductInfoAttrModel(13);
								$attr = $attrModel->getGoodsAttr($row['id']);
								foreach($attr as $k=>$v)
								{
									if(in_array($v['code'],array('cart')))
									{
										$weight =$v['value'];//拿到石重
									}
								}
								$bc_note = $purchaseModel->GetFactoryStyleFromXiangKou($style_sn,$weight);//根据石重/镶口 求工厂信息;			
							}else{
								//如果起版单中工厂维护的是款号的非默认工厂，则带出起版号录入的工厂，模号
								$bc_note['factory_sn']   = $qiban_info['fuzhu'];
								$bc_note['factory_name'] = $qiban_info['gongchang_id'];//gongchang
							}
						}


				   }else if ($style_sn == 'QIBAN'){
						//无款起版
						if(!empty($qiban_info)){
							$bc_note['factory_sn']   = $qiban_info['fuzhu'];
							$bc_note['factory_name'] = $qiban_info['gongchang_id'];//gongchang
						}else{
							$bc_note['factory_sn']   = '';
							$bc_note['factory_name'] = '';
						}
				   }
				}else{
					//非起版号下单
					$attrModel = new ProductInfoAttrModel(13);
					$attr = $attrModel->getGoodsAttr($row['id']);
					foreach($attr as $k=>$v)
					{
						if(in_array($v['code'],array('cart')))
						{
							$weight =$v['value'];//拿到石重
						}
					}
					$bc_note = $purchaseModel->GetFactoryStyleFromXiangKou($style_sn,$weight);
				}
			}else{
				//已经分配工厂
				$factory = $styleApi->GetFactory($factory_id,$style_sn);
				if (!empty($factory) && $factory['is_factory'] == 1){
					//如果分配工厂是款号的默认工厂,根据石重算镶口
					$attrModel = new ProductInfoAttrModel(13);
					$attr = $attrModel->getGoodsAttr($row['id']);
					foreach($attr as $k=>$v)
					{
						if(in_array($v['code'],array('cart')))
						{
							$weight = $v['value'];//拿到石重
						}
					}
					$bc_note = $purchaseModel->GetFactoryStyleFromXiangKou($style_sn,$weight);//根据石重/镶口 求工厂信息;			
				}else{
					//如果分配工厂是款号的非默认工厂，则带出起版号录入的工厂，模号
					$qb_id = $sales->getQiBanIdByWhere($order_sn,$style_sn);
					$qiban_info = $purchaseApi->GetQiBianGoodsByQBId($qb_id);
					if(!empty($qiban_info)){
						if ($style_sn == 'QIBAN'){
							$bc_note['factory_sn']   = '';//无款起版模号为空
							$bc_note['factory_name'] = $qiban_info['gongchang_id'];//gongchang
						}else{
							$bc_note['factory_sn']   = $qiban_info['fuzhu'];
							$bc_note['factory_name'] = $qiban_info['gongchang_id'];//gongchang
						}
					}else{
						$bc_note['factory_sn']   = '';
						$bc_note['factory_name'] = '';
					}
				}
			}
		}else if ($from_type == 1 ){
			//1 为采购布产单
			//采购布产单，直接根据分配的工厂计算镶口。

			$attrModel = new ProductInfoAttrModel(13);
			$attr = $attrModel->getGoodsAttr($row['id']);
			foreach($attr as $k=>$v)
			{
				if(in_array($v['code'],array('xiangkou')))
				{
					$xiangkou =$v['value'];//拿到镶口
				}
				
				if(in_array($v['code'],array('zuanshidaxiao')))
				{
					$zuanshidaxiao =$v['value'];//拿到钻石大小
				}
			}
			if (empty($xiangkou)){
				//从钻石大小文字中提取数值
				if (preg_match('/(\d+)\.?(\d+)?/is',$zuanshidaxiao,$match)){
					$xiangkou = $match[0];
				}
			}
			
			$bc_note = $purchaseModel->GetFactoryStyleFromXiangKou($style_sn,$xiangkou,$factory_id);
			
		}
		
		if(!count($bc_note))
		{
		   $bc_note= $this->GetBcNote($row);
		}
	   return $bc_note;
	}
	
    /*
	//打印加工流水单
	//EDITBY ZHANGRUIYING打印和批量打印合并，优化代码，后期需要把FOREACH里的查询进一步优化
	//time  2015/6/4
	*/
    public function piliang_print_jiagong($params)
	{
		$ids = _Request::get('id');
		if(empty($ids))
		{
			$ids =trim($params['_ids']);
		}
        $newmodel = new ProductInfoAttrModel(13);
		$logModel = new ProductOpraLogModel(14);
		$model = new ProductInfoModel(13);
		$SalesModel = new SalesModel(27);
		$styleselfmodel = new StyleModel(11);
        //$WarehouseModel = new WarehouseModel(21);
        $bc_sn = explode(",", $ids);
		//表面工艺图
		$list=array();
		$img_type=array('光面'=>'gm','磨砂'=>'gm','拉丝'=>'ls','光面+磨砂'=>'gm_ms','光面+拉丝'=>'gm_ls');
		$kezimodel = new Kezi();
        foreach ($bc_sn as $key => $id) {
				$data_arr=array();

                $res = $newmodel->getGoodsAttr($id);
                $atrrdata = array();
                foreach($res as $val){
                    $atrrdata[$val['code']] = $val['value'];
                }
                if(empty($atrrdata['jinse'])){
                	$atrrdata['jinse']=isset($atrrdata['18k_color']) ? $atrrdata['18k_color'] : '';
                }
                if(empty($atrrdata['color'])){
                	$atrrdata['color']=isset($atrrdata['yanse']) ? $atrrdata['yanse'] : '';
                } 
                if(empty($atrrdata['cart'])){
                	$atrrdata['cart']=isset($atrrdata['zuanshidaxiao']) ? $atrrdata['zuanshidaxiao'] : '';
                }
                if(empty($atrrdata['goods_name'])){
                	$atrrdata['goods_name']=isset($atrrdata['g_name']) ? $atrrdata['g_name'] : '';
                }                
                if(empty($atrrdata['bc_style'])){
                	$atrrdata['bc_style']=isset($atrrdata['bc_type']) ? $atrrdata['bc_type'] : '';
                } 

                $data_arr['atrrdata']=$atrrdata;

				$data_arr['data']= $model->Select2('*', " id=$id",'row');

                //boss-1113----------
                //布产单的来源要是采购单，副石1、2取数逻辑就不变，如果是布产单来源是销售订单，副石1、2取数逻辑就按照下面的需求做
                /*if($data_arr['data']['from_type'] == 2){//谭碧玉：去掉这个判断 2017年9月7日
                    $bc_where = $data_arr['data'];
                    $attrinfo = $SalesModel->getOrderAttrInfoByBc_sn($bc_where);
                    if(!empty($attrinfo) && $attrinfo['ext_goods_sn'] != ''){
                        $checkgoods = $WarehouseModel->checkGoodsByGoods_id($attrinfo['ext_goods_sn']);
                        if(!empty($checkgoods)){
                            if($checkgoods['fushizhong']>0.01 || $checkgoods['shi2zhong']>0.01){
                                $atrrdata['fushi_zhong_total1'] = $checkgoods['fushizhong'];
                                $atrrdata['fushi_num1'] = $checkgoods['fushilishu'];
                                $atrrdata['fushi_zhong_total2'] = $checkgoods['shi2zhong'];
                                $atrrdata['fushi_num2'] = $checkgoods['shi2lishu'];
                                $atrrdata['fushi_zhong_total3'] = $checkgoods['shi3zhong'];
                                $atrrdata['fushi_num3'] = $checkgoods['shi3lishu'];
                            }
                        }
                    }
                }*/
                //var_dump($atrrdata);die;
                //-------------------end*/
                $data_arr['atrrdata']=$atrrdata;
				$style_sn = $data_arr['data']['style_sn'];
				//获取产品图片
				$gallery_data=$this->getImg($style_sn,$data_arr['data']['id'],1);
				$data_arr['data']['goods_img']=isset($gallery_data[0]['thumb_img'])?$gallery_data[0]['thumb_img']:'';
				//获取工厂模号
				$note=$this->getFactoryStyle(array('id'=>$id,'p_sn'=>$data_arr['data']['p_sn'],'style_sn'=>$style_sn,'prc_id'=>$data_arr['data']['prc_id'],'from_type'=>$data_arr['data']['from_type']));
				$data_arr['data']['factory_name'] =$data_arr['data']['prc_id']?$data_arr['data']['prc_name']:$note['factory_name'];
				$data_arr['data']['factory_sn'] =isset($note['factory_sn'])?$note['factory_sn']:'';
				$data_arr['bc_time']=$model->getBcTime($data_arr['data']['id']);
                //刻字
                if(isset($data_arr['atrrdata']['kezi']) and !empty($data_arr['atrrdata']['kezi'])){
                    $data_arr['atrrdata']['kezi']=$kezimodel->retWord($data_arr['atrrdata']['kezi']);

				}
				//款式属性信息
				$style_zhuaxz = $styleselfmodel->getAttrByStyleSn($style_sn, "爪钉形状");
				$style_attr = array(
					'style_zhuaxz' => $style_zhuaxz,
				);
				$data_arr['style_attr'] = $style_attr;
                $pSnArr=$model->getOrderSnsByBcsn($id);
                //$data_arr['sql']=$pSnArr;
                $p_sn=$pSnArr['p_sn'];
                $re=$SalesModel->isTsydOrder($p_sn);
                if($re){
                	$data_arr['isTsyd']=1;
                }else{
                	$data_arr['isTsyd']=0;
                }
				//记录操作日志
				$logModel->addLog($id,"订单：{$data_arr['data']['p_sn']}打印加工流水单");
				$list[]=$data_arr;
				
				
            }
//var_dump($list);die;
			$this->render('print_jiagong.html', array(
						'view'=>new ProductInfoView($model),
						'data_arr'=>$list,
						'img_type'=>$img_type
				));

        }
    /*
	//打印订单提货单
	public function printBills()
	{
		//获取订单号
		$id = _Request::get('id');   //订单号字符串
		$bc_id = _Request::get('bc_id');
		$from_type = _Request::get('from_type');
		if($from_type==1){
			echo "采购单不可以打印提货单！";
			exit;
		}
		//new 数据字典 渠道 快递 out_order_sn
		$kuaidiModel = new ExpressModel(1);
		$SalesChannelsmodel = new SalesChannelsModel(1);
		//通过接口，获取 订单信息
		$orderinfo = ApiSalesModel::GetPrintBillsInfo($id);
		if(isset($orderinfo['return_msg']) and !empty($orderinfo['return_msg'])){
			//获取支付方式 拼接
			if (isset($orderinfo['return_msg']['order_pay_type']) and !empty($orderinfo['return_msg']['order_pay_type'])){
					$newmodel =  new PaymentModel($orderinfo['return_msg']['order_pay_type'],2);
					$orderinfo['return_msg']['order_pay_name'] = $newmodel->getValue('pay_name');
			}
			//订单来源
			if (isset($orderinfo['return_msg']['customer_source_id']) and !empty($orderinfo['return_msg']['customer_source_id']))
			{
					$CustomerSourcesModel = new CustomerSourcesModel($orderinfo['return_msg']['customer_source_id'] , 1);
					$orderinfo['return_msg']['customer_source_id'] = $CustomerSourcesModel->getValue('source_name');

			}
			if (isset($orderinfo['return_msg']['id']) and !empty($orderinfo['return_msg']['id'])){
				//获取外部订单号
				$ret = ApiSalesModel::GetOutOrderInfoByOrderId($orderinfo['return_msg']['id']);
				$orderinfo['return_msg']['out_order_sn']=!empty($ret['return_msg']['out_order_sn'])?$ret['return_msg']['out_order_sn']:'';
			}
			if(isset($orderinfo['return_msg']['express_id']) and !empty($orderinfo['return_msg']['express_id']))
			{
				$orderinfo['return_msg']['express_id'] = $kuaidiModel->getNameById( $orderinfo['return_msg']['express_id'] );
			}
			$orderinfo['return_msg']['user_name'] = '--';
			//获取单据会员名字
			
			//获取单据明细
			$detail = ApiSalesModel::GetOrderDetailByOrderId($orderinfo['return_msg']['order_sn']);
			$imgModel=new ProductInfoImgModel(13);
			$gallerymodel = new ApiStyleModel();//绑定信息
			//edit by zhangruiying
			$kezimodel = new Kezi();
	       	foreach($detail as $key=> &$val)
			{
	       		//获取图片 拼接进数组
	       		$gallery_data =$this->getImg($val['goods_sn'],$bc_id,1);
	       		$detail[$key]['goods_img']=isset($gallery_data[0]['thumb_img'])?$gallery_data[0]['thumb_img']:'';
				//检测是否绑定
				$goods_bing = ApiWarehouseModel::GetBingInfo($val['id']);
				$detail[$key]['bing'] = 0;
				if(!empty($goods_bing)){
					$detail[$key]['bing'] = 1;
					$detail[$key]['goods_id']= $goods_bing['goods_id'];
					$detail[$key]['box_sn'] = $goods_bing['box_sn']?$goods_bing['box_sn']:'无';
				}
				 if(isset($detail[$key]['kezi']) and !empty($detail[$key]['kezi'])){
                    $detail[$key]['kezi']=$kezimodel->retWord($detail[$key]['kezi']);
                }
     
                //获取售卖方式
                $temInfo = ApiModel::process_api( array('p_id'),array($detail[$key]['id']),'GetProductInfo');
                $detail[$key]['is_alone'] = 0;
                if(isset($temInfo['is_alone'])){
                    $detail[$key]['is_alone'] = $temInfo['is_alone'];
                }
				
			}
			//edit end
				//获取配货单单据的配送类型
				if($orderinfo['return_msg']['distribution_type'] == 1){
					//如果下单的配送类型 为1 (数字字典 sales.distribution_type)。到门店 则在提货单的配送类型中。显示下订单的门店名
					$orderinfo['return_msg']['department_id'] = strstr($orderinfo['return_msg']['address'], '|' , true);

				}
				else{
					$orderinfo['return_msg']['department_id'] = $this->dd->getEnum('sales.distribution_type', $orderinfo['return_msg']['distribution_type']);
				}
				//这里增加判断，如果配送类型是门店的话，配送方式要取address｜前面的体验店名称
				if ($orderinfo['return_msg']['distribution_type'] == 1) {
					$address = $orderinfo['return_msg']['address'];
					$pos = strpos($address, "|");
					$address = substr($address, 0,$pos);
					if ($address == "自营")
					{
						$newaddress = substr($orderinfo['return_msg']['address'], ($pos+1));
						$orderinfo['return_msg']['express_id'] = $newaddress;
					}
					else
					{
						$orderinfo['return_msg']['express_id'] = $address;
					}
				}
				//回写订单日志
				if($orderinfo['return_msg']['order_sn'])
				{
					$order_sn = $orderinfo['return_msg']['order_sn'];
					$remark = "订单：".$order_sn." 打印提货单(供应商布产单详情打印)";
					$logModel = new ProductOpraLogModel(14);
					$logModel->addLog($bc_id,$remark);
				}
				$this->render('print_bill.html', array(
					'info' => $orderinfo['return_msg'],
					'goods_list' => $detail
				));

			}
			else
			{
				echo "<div style='font-size:30px;margin-top:30px;text-align:center'>未查询到单号：<span style='color:red;'>{$id}</span> 的此订单！</div>";
			}


	}*/

	//打印订单提货单
	public function printBills()
	{
		//获取订单号
		$id = _Request::get('id');   //订单号字符串
		$bc_id = _Request::get('bc_id');
		$from_type = _Request::get('from_type');
		if($from_type==1){
			echo "采购单不可以打印提货单！";
			exit;
		}
       	$kuaidiModel = new ExpressModel(1);
       	$SalesChannelsmodel = new SalesChannelsModel(1);
       	$dd = new DictView(new DictModel(1));
       	$salesmodel=new SalesModel(27);
        $gallerymodel = new BaseStyleInfoModel(12);
        $productinfomodel=new ProductInfoModel(13);
        $logModel = new ProductOpraLogModel(14); 

		$ke = new Kezi();
	

       		$ProductInfoModel = new ProductInfoModel($bc_id,13);
       		$v =$ProductInfoModel->getValue('p_sn');
       		$from_type =$ProductInfoModel->getValue('from_type');

       		if($from_type==1){
	       		echo "<div style='font-size:30px;margin-top:30px;text-align:center'>采购布产单不可以打印提货单！：<span style='color:red;'>{$id}</span> ！</div>";
				exit;
       		}
           
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
           
            /*
            if (!empty($ret['out_order_sn'])) {
                $out_order_sn = $ret['out_order_sn'];
            }  else {
                $out_order_sn = "";
            }
             */
            $html = '';
            if(!empty($orderinfo)){
            	if($orderinfo['distribution_type']==2){
                  if(empty($orderinfo['express_id'])){
                       exit("订单：$v 快递类型不能为空");
                  }
            	}
                $orderinfo['express_id']=$kuaidiModel->getNameById( $orderinfo['express_id'] );
                $orderinfo['express_id'] =  $orderinfo['express_id'] ? $orderinfo['express_id']: '——';
                /*
                $orderinfo['user_name'] = '--';
                //获取单据会员名字
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
                $WarehouseModel = new WarehouseModel(21);
              
                if(!empty($detail)){
                    foreach($detail as $p_key => &$bing_val){

                        //获取图片 拼接进数组
                        
                        $gallery_data = $gallerymodel->GetStyleGalleryInfo($bing_val['goods_sn'],1);
                        //$gallery_data是一个二维数组
                        if(isset($gallery_data[0]['thumb_img'])){
                            $detail[$p_key]['goods_img']=$gallery_data[0]['thumb_img'];
                        }else{
                            $detail[$p_key]['goods_img']='';
                            //$detail[$p_key]['goods_img']='images/styles/201007/1279189436925042936.jpg';
                        }
                        $detail[$p_key]['cat_type_name']=isset($gallery_data[0]['cat_type_name']) ? $gallery_data[0]['cat_type_name'] : ""; 
                        $bing_val['box_id'] = '无';


                        $bing_val['bing'] = 0; //无商品绑定
                        $res=$WarehouseModel->getOrderGoodsAndBox($bing_val['id']);
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
                $this->assign('order_sn_str', $id);
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

               
                $html.= $this->fetch('foreach.html',array(
                    'info' => $orderinfo,
                    'dd' => $dd,  //数据字典
                    'goods_list' => $detail,                    
                    'detail_num' => $detail_num                    
                ));

            }else{
                $html.= "<table class=\"PageNext\" cellspacing=\"0\" cellpadding=\"1\" width=\"100%\" border=\"0\"><tr><td><hr><b>未查询到单号/或者订单被取消或者关闭：<span style='color:red;'>{$v}</span></b> <hr></td></tr></table>";
            }
            $this->render('bath_print_bill.html', array('html'=>$html,'bc_ids'=>$bc_id));
    } 


	////  打印按钮，更改带配货列表的打印状态4064,4063
	public function checkDayinStatus($params){
		$result = array('success'=> 0 , 'error' => '打印订单提货单程序异常');
        
		$order_sn = isset($params['order_sn'])?$params['order_sn']:'';
        $bc_sn    = isset($params['bc_sn'])?$params['bc_sn']:'';
        
        $salesmodel=new SalesModel(27);
        $productinfomodel=new ProductInfoModel(13);
        $logModel = new ProductOpraLogModel(14);
        
        if (!empty($bc_sn)) {
            /* TODO: bc_sn 实际为布产id
            $bc_sn_arr = explode(",", $bc_sn); //bc_id list
            $new_bc_sn = '';
            foreach($bc_sn_arr as $k => $v){
                $new_bc_sn .= "'".BCD_PREFIX.$v."',";
            }

            $bc_sn = substr($new_bc_sn,0,-1);
            */
            $model = new ProductInfoModel(13);
            //$data = $model->getOrderSnByBcsn($bc_sn);
            $data = $model->getOrderSnByIds($bc_sn);
            
            foreach($data as $key => $val) {
                $order_sn = $val['p_sn'];
                $bc_id    = $val['id'];
                if(!empty($order_sn)){
                    $res = ApiModel::sales_api(array('order_sn') , array($order_sn) , 'updatePrintTihuo');
                    //通过接口，获取 订单信息
                    $orderinfo = $salesmodel->GetPrintBillsInfo($order_sn);
                }
                //订单日志 和布产日志
                if (isset($orderinfo['id'])){
                    $order_id = $orderinfo['id'];
                    //打印回写订单日志
                    $remark = "订单：".$orderinfo['order_sn']." 打印提货单(供应商布产打印)";                    
                    $orderLog = array(
                        'order_id'=>$order_id,
                        'order_status'=>$orderinfo['order_status'],
                        'shipping_status'=>$orderinfo['send_good_status'],
                        'pay_status'=>$orderinfo['order_pay_status'],
                        'create_user'=>$_SESSION['userName'],
                        'create_time'=>date('Y-m-d H:i:s'),
                        'remark'=>$remark ,
                    );
                    $salesmodel->addOrderLog2($orderLog); 
                
                    //添加回写订单日志                    
                    $remark = "订单：".$order_sn." 打印提货单(供应商布产打印)";
                    $logModel = new ProductOpraLogModel(14);
                    $logModel->addLogNew($bc_id,$remark);
                }
            }
        }
        

        if ($res['error'] == 0) {
            $result = array('success' => 1, 'error' => '打印成功');
            Util::jsonExit($result);
        }else {
            Util::jsonExit($result);
        }

	}

       /**
	 *add by zhangruiying添加备注页面
	 */
	public function remark($params)
	{
                $id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
                $tab_id = _Request::getInt("tab_id");
		$result['content'] = $this->fetch('remark.html',array(
			'view'=>new ClothProductionTrackingView(new ClothProductionTrackingModel($id,13)),
                        'tab_id'=>$tab_id
		));
		$result['title'] = '布产备注';
		Util::jsonExit($result);
	}
    public function updateRemark($params)
	{
		$result = array('success' => 0,'error' =>'');
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');
		$id = _Post::getInt('id');
		$newmodel =  new ClothProductionTrackingModel($id,14);
		$olddo = $newmodel->getDataObject();
        $info=_Post::getString('info');
		if(empty(preg_replace('/\s/','',$info)))
		{
			$result = array('success' => 0,'error' =>'备注不能为空');
			Util::jsonExit($result);
		}
        //添加操作日志
		$info = "<font color=red>".$info."</font>";//字体标红 JUAN
        $logModel = new ProductOpraLogModel(14);
		$res=$logModel->addLog($id,$info);
		if($res !== false)
		{

			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;
			$result['title'] = '备注添加成功';
		}
		else
		{
			$result['error'] = '备注添加失败';
		}
		Util::jsonExit($result);
	}
	
	public function DownloadCSVNew(){
	    set_time_limit(0);
	    header('Content-Type: application/vnd.ms-excel');
	    header("Content-Disposition: attachment;filename=productInfoListNew.csv");
	    header('Cache-Control: max-age=0');
	     
	    $where=$this->getDataFarmat();
	    if(!isset($_REQUEST['opra_uname']))
	    {
	        $where['opra_uname'] = $_SESSION['userName'];
	    }
	    $model = new ProductInfoModel(13);
	    $salesModel = new SalesModel(28);
	    $util=new Util();
		$view = new ProductInfoView(new ProductInfoModel(13));
		$styleselfmodel = new StyleModel(11);
		$warehouseModel = new WarehouseModel(22);
	    $wait_dia_report=_Request::get('wait_dia_report');

	    $title=array('布产单号','布产来源','分配工厂时间','工厂接单日期','标准出厂时间','送钻日期','销售渠道','客户来源','客户姓名','款号','数量','工厂名称','跟单人','制单人','主石单颗重','主石粒数','颜色','净度','证书号','镶嵌要求','材质','金色','金重','指圈','刻字','表面工艺','布产备注','布产类型','布产状态','生产状态','工厂交货时间','模号','证书类型','主石类型','主石形状','副石类型','副石形状','属性','款式属性信息',
	        '生产经理','采购 /订单备注','线上/线下','钻石库存状态','钻石所在仓库','配钻状态','配钻日期','实际等钻结束时间','最后操作日期','操作备注','起版类型','钻石类型','等钻时间','OQC质检通过时间','订单录单来源','外部单号','是否快速定制','是否组合镶嵌','组合镶嵌现货托',
	       '副石1总重/粒数','副石2总重/粒数','副石3总重/粒数','标准金重范围','入库单','货品数量');
	    foreach ($title as $k => $v) {
	       $title[$k]=iconv('utf-8', 'GB18030', $v);
	    }
	    echo "\"".implode("\",\"",$title)."\"\r\n";
	    $page = 1;
	    $pageSize=30;
	    $pageCount=1;
	    $recordCount = 0;

        $dic_array_buchan_status=$this->dd->getEnumArray('buchan_status');
        $dic_array_buchan_fac_opra=$this->dd->getEnumArray('buchan_fac_opra');
        $dic_array_sales_channels_class=$this->dd->getEnumArray('sales_channels_class');
        $dic_array_peishi_status=$this->dd->getEnumArray('peishi_status');
        $dic_array_is_on_sale=$this->dd->getEnumArray('warehouse.goods_status');
        $dic_array_qiban_type=$this->dd->getEnumArray('qiban_type');
        $dic_array_diamond_type=$this->dd->getEnumArray('diamond_type');       

	    $list_sql=$model->getsql($where);	   
        $sqlsubfiled="`ia`.`value` p_sn_out,(select time from product_opra_log where remark like '%工厂操作：送钻，备注：送钻操作' and bc_id=main.id order by time desc limit 1) as songzuan_time,
 (select peishi_status from  peishi_list  where rec_id=main.id order by peishi_status desc limit 1) as peishi_status2,
 (select b.add_time from peishi_list a inner join peishi_list_log b on a.id=b.peishi_id where rec_id=main.id and peishi_status=4 order by b.add_time desc limit 1) as peishi_time ";
        $list_sql=str_replace("`ia`.`value` p_sn_out",$sqlsubfiled, $list_sql);        

	    while($page <= $pageCount){	       
	        $data = $model->db()->getPageListForExport($list_sql,array(),$page,$pageSize,false,$recordCount);
	        $page ++;	              
	        if(!empty($data['data'])){	            
	            $recordCount = $data['recordCount'];
	            $pageCount = $data['pageCount'];
	            $data = $data['data'];	            
	            if(!is_array($data) || empty($data)){
	                continue;
	            }
	            foreach($data as $d){
					$temp=array();
					$style_sn = $d['style_sn'];
	                //石重cart	颜色color	净度clarity	证书号zhengshuhao 材质caizhi	金色jinse	金重jinzhong	指圈zhiquan	刻字kezi	表面工艺face_work
	                $sql = "select code,value,name from product_info_attr where g_id='{$d['id']}'";//edit by zhangruiying
	                $attr_d = $model->db()->getAll($sql);
	                $zhengshuhao = "";
	                foreach ($attr_d as $v)
	                {
	                    if($v['code']=="zhengshuhao"){
	                        $zhengshuhao = $v['value'];
	                    }
	                    $d[$v['code']] = $v['value'];
					}
					//获取布产单绑定的收货单及货品数量
					$warehouse_info = $warehouseModel->getWarehouseNumByid($d['bc_sn'],false);
					if(!empty($warehouse_info)){
						$bill_nos = array_column($warehouse_info,'bill_no');
						$nums = array_column($warehouse_info,'num');
						$bill_nos = implode('|',$bill_nos);
						$nums = array_sum($nums);
					}
	                $temp['bc_sn']=$d['bc_sn'];
	                $temp['p_sn']=$d['p_sn'];
	                if(preg_match("/^\d+$/is",$temp['p_sn'])){
	                    $temp['p_sn'] = "'".$temp['p_sn'];
	                }
	                //分配工厂时间：取布产单字段N16
	                $temp['to_factory_time'] = $d['to_factory_time'];
	                $temp['order_time']=$d['order_time'];
	                $temp['esmt_time']=$d['esmt_time'];	 
	                $temp['songzuan_time']=$d['songzuan_time'];	                
	                $temp['channel_id']=$view->get_channel_name($d['channel_id']);
	                $temp['customer_source_id']=$view->get_customer_name($d['customer_source_id']);


	                $temp['consignee']=$d['consignee'];
	                $temp['style_sn']=$d['style_sn'];
	                $temp['num']=$d['num'];
	                $temp['prc_name']=$d['prc_id'];//$d['prc_name']
	                $temp['opra_uname']="";//$d['opra_uname']
	                $temp['make_name']=$d['create_user'];//制单人
	                $temp['cart']=isset($d['cart'])?$d['cart']:'';
	                if(empty($temp['cart']))
	                {
	                    $temp['cart']=isset($d['diamond_size'])?$d['diamond_size']:'';
	                }
	                if(empty($temp['cart']))
	                {
	                    $temp['cart']=isset($d['zuanshidaxiao'])?$d['zuanshidaxiao']:'';
	                }
                    $temp['zhushi_num']=isset($d['zhushi_num'])?$d['zhushi_num']:'';
	                $temp['color']=isset($d['color'])?$d['color']:'';
	                if(empty($temp['color']))
	                {
	                    $temp['color']=isset($d['yanse'])?$d['yanse']:'';
	                }
	
	                $temp['clarity']=isset($d['clarity'])?$d['clarity']:'';
	                if(empty($temp['clarity']))
	                {
	                    $temp['clarity']=isset($d['neatness'])?$d['neatness']:'';
	                }
	                if(empty($temp['clarity']))
	                {
	                    $temp['clarity']=isset($d['zuanshijingdu'])?$d['zuanshijingdu']:'';
	                }
	                if(empty($temp['clarity']))
	                {
	                    $temp['clarity']=isset($d['jingdu'])?$d['jingdu']:'';
	                }
	                $temp['zhengshuhao'] = $zhengshuhao;
	                if(preg_match("/^\d+$/is",$temp['zhengshuhao'])){
	                    $temp['zhengshuhao'] = "'".$temp['zhengshuhao'];
	                }
	                $temp['xiangqian']=$d['xiangqian'];
	                $temp['caizhi']=isset($d['caizhi'])?$d['caizhi']:'';
	                $temp['jinse']=isset($d['jinse'])?$d['jinse']:'';
	                if(empty($temp['jinse']))
	                {
	                    $temp['jinse']=isset($d['18k_color'])?$d['18k_color']:'';
	                }
	                $temp['jinzhong']=isset($d['jinzhong'])?$d['jinzhong']:'';
	                $temp['zhiquan']=isset($d['zhiquan'])?$d['zhiquan']:'';
	                $temp['kezi']=isset($d['kezi'])?$d['kezi']:'';
	                if(empty($temp['kezi']))
	                {
	                    $temp['kezi']=isset($d['work_con'])?$d['work_con']:'';
	                }
	                $temp['face_work']=isset($d['face_work'])?$d['face_work']:'';
	                if(empty($temp['face_work']))
	                {
	                    $temp['face_work']=isset($d['biaomiangongyi'])?$d['biaomiangongyi']:'';
	                }
	                $temp['info']=$d['info'];


	                
	                $temp['bc_style']=$d['bc_style'];
	                $temp['status']=$this->getEunm($dic_array_buchan_status,$d['status']);//$this->dd->getEnum('buchan_status',$d['status']);
	                $temp['buchan_fac_opra']=$this->getEunm($dic_array_buchan_fac_opra,$d['buchan_fac_opra']);//$this->dd->getEnum('buchan_fac_opra',$d['buchan_fac_opra']);
	                
	                
	                $temp['rece_time']=$d['rece_time'];
	                $factory_id=$d['prc_id']?$d['prc_id']:" ";
	                if($wait_dia_report=='1'){
	                	if($d['buchan_fac_opra']=='4' || $d['buchan_fac_opra']=='5')
	                		continue;
	                	if(!empty($d['songzuan_time']) && $d['songzuan_time']!='0000-00-00 00:00:00')
	                		continue;
	                	if(empty($temp['zhengshuhao'])){
	                		if($d['peishi_status2']=='3')
	                			continue;
	                		if($d['xiangqian']=='不需工厂镶嵌')
	                			continue;
	                	} 
	                }
	                /*
	                //获取工厂模号
	                if(empty($d['def_factory_sn']) || empty($d['def_factory_name'])){
    	                $note=$this->getFactoryStyle(array('id'=>$d['id'],'p_sn'=>$d['p_sn'],'style_sn'=>$d['style_sn'],'prc_id'=>$d['prc_id'],'from_type'=>$d['from_type']));
    	                $p_data = array(
    	                    'def_factory_sn'=>$note['factory_sn'],
    	                    'def_factory_name'=>$note['factory_name']
    	                );
    	                //$res = $model->update($p_data,"id={$d['id']}");
    	                $temp['def_factory_sn']=isset($note['factory_sn'])?$note['factory_sn']:'';	                    
	                }else{
	                    $temp['def_factory_sn'] = $d['def_factory_sn'];
	                }*/
                    $temp['def_factory_sn'] = $d['def_factory_sn'];

	                //对取到的所有数据进行款式属性补全
	                $title_add='';
	                //证书类型
	                $temp['cert']=isset($d['cert'])?$d['cert']:'';
                    //主石类型
                    $temp['zhushi_cat']=isset($d['zhushi_cat'])?$d['zhushi_cat']:'';
	                //主石形状
	                $temp['zhushi_shape']=isset($d['zhushi_shape'])?$d['zhushi_shape']:'';
                    //副石类型
                    $temp['fushi_cat']=isset($d['fushi_cat'])?$d['fushi_cat']:'';
	                //副石形状
                    $temp['fushi_shape']=isset($d['fushi_shape'])?$d['fushi_shape']:'';
	                foreach($attr_d as $k=>$attr)
	                {
	                    $title_add.=$attr['name'].':'.$attr['value']."\r\n";
                        if($attr['code']=='cert'){
                            $temp['cert']=$attr['value'];
                        }
                        if($attr['code']=='zhushi_shape'){
                            $temp['zhushi_shape']=$attr['value'];
                        }
                        if($attr['code']=='fushi_shape'){
                            $temp['fushi_shape']=$attr['value'];
                        }
	                }
	                $temp['attr']=$title_add;
                     //取出款号属性信息
					$style_zhuaxz = $styleselfmodel->getAttrByStyleSn($style_sn, "爪钉形状");
					$temp['style_attr'] = '爪钉形状:'.$style_zhuaxz;
                    /* 
	                //订单表先关信息查询
	                $orderInfo = array();
	                $order_sn = $d['p_sn'];
	                $order_id = '';
	                if($order_sn != ""){
	                    $orderInfo = $salesModel->getBaseOrderInfoByOrderSn('id,referer',$order_sn);
	                    $order_id  = isset($orderInfo['id'])?$orderInfo['id']:'';
	                }*/
	                //生产经理：取布产单字段 N15
	                $temp['production_manager_name'] = $d['production_manager_name'];

	                //'采购备注',N1
	                $temp['caigou_info'] = $d['caigou_info'];
	                //'线上/线下',N2
	                $temp['channel_class'] = $this->getEunm($dic_array_sales_channels_class,$d['channel_class']); //$this->dd->getEnum('sales_channels_class',$d['channel_class']);

	                preg_match("/\d{5,}/is",$zhengshuhao,$zsh_arr);
	                $zhengshuhao = !empty($zsh_arr[0])?$zsh_arr[0]:'';
	                $temp['is_on_sale']="";//钻石库存状态，N3
	                $temp['warehouse']="";//'钻石所在仓库',N4
	                if($zhengshuhao !=''){
	                     $sql ="select is_on_sale,warehouse from warehouse_shipping.warehouse_goods where zhengshuhao ='{$zhengshuhao}' order by id limit 1";
	                     $row = $model->db()->getRow($sql);
	                     if(!empty($row)){
    	                     //钻石库存状态，N3
	                         $temp['is_on_sale'] = $this->getEunm($dic_array_is_on_sale,$row['is_on_sale']);//$this->dd->getEnum('warehouse.goods_status',$row['is_on_sale']);
    	                     //'钻石所在仓库',N4
    	                     $temp['warehouse']= $row['warehouse'];
	                     }
	                }              
	                
	                /*
	                //'配钻状态',N5
	                $sql = "select peishi_status from peishi_list where rec_id={$d['id']} order by peishi_status desc limit 1";
	                $peishi_status =  $model->db()->getOne($sql);
	                $temp['peishi_status'] = $this->dd->getEnum('peishi_status',$peishi_status);
	                //'配钻日期',N6
	                $sql = "select b.add_time from peishi_list a inner join peishi_list_log b on a.id=b.peishi_id where rec_id={$d['id']} and peishi_status=4 order by b.add_time desc limit 1";
	                $temp['peishi_time'] = $model->db()->getOne($sql);
	                //'送钻日期',N7
	                $sql="select time from product_opra_log where remark like '%工厂操作：送钻，备注：送钻操作' and bc_id={$d['id']} order by time desc limit 1";
	                $temp['songzuan_time'] = $model->db()->getOne($sql);
	                */

                    $temp['peishi_status'] =$this->getEunm($dic_array_peishi_status,$d['peishi_status2']);//$this->dd->getEnum('peishi_status',$d['peishi_status2']) : "";
                    $temp['peishi_time']=$d['peishi_time'];
                        

	                //实际等钻结束时间
	                $temp['wait_dia_endtime'] = $d['wait_dia_endtime'];
	                //最后操作日期N16
	                $temp['time'] = $d['time'];
	                //'操作备注',N8
	                $temp['opra_remark'] = $d['opra_remark'];
	                //'起版类型',N9
	                $temp['qiban_type'] = $this->getEunm($dic_array_qiban_type,$d['qiban_type']);//$this->dd->getEnum('qiban_type',$d['qiban_type']);
	                //'钻石类型',N10
	                $temp['diamond_type'] = $this->getEunm($dic_array_diamond_type,$d['diamond_type']); //$this->dd->getEnum('diamond_type',$d['diamond_type']);
	                //'等钻时间',N11
	                $temp['wait_dia_starttime'] = $d['wait_dia_starttime'];
	                //'OQC质检通过时间',N12
	                $temp['oqc_pass_time'] = $d['oqc_pass_time'];
	                //'订单录单来源',N13
	                $temp['referer'] = $d['referer'];
	                //'外部单号'N14
	                $temp['p_sn_out'] = $d['p_sn_out'];
	                $temp['is_quick_diy'] = $d['is_quick_diy']==1?"是":"否";
	                
	                $temp['is_combine'] = $d['is_combine']==1?'是':'否';
	                $temp['combine_goods_id'] = "'".$d['combine_goods_id'];

	                /*
	                if($order_id >0 ){
	                    $outOrderSnArr = $salesModel->GetOutOrderSn($order_id);
                        $temp['p_sn_out'] = implode("|",$outOrderSnArr);
	                    if(preg_match("/^\d+$/is",$temp['p_sn_out'])){
	                        $temp['p_sn_out'] = "'".$temp['p_sn_out'];
	                    }
	                }else{
	                    $temp['p_sn_out'] = '';
	                }*/ 

		    	    //$temp['special'] = isset($d['special'])?$d['special']:''; //特别备注
	                $attrKeyVal = array_column($attr_d, 'value','code');
	                $temp['fushi1'] = !empty($attrKeyVal['fushi_num1'])?$attrKeyVal['fushi_zhong_total1'].'ct/'.$attrKeyVal['fushi_num1'].'p':'';
	                $temp['fushi2'] = !empty($attrKeyVal['fushi_num2'])?$attrKeyVal['fushi_zhong_total2'].'ct/'.$attrKeyVal['fushi_num2'].'p':'';
	                $temp['fushi3'] = !empty($attrKeyVal['fushi_num3'])?$attrKeyVal['fushi_zhong_total3'].'ct/'.$attrKeyVal['fushi_num3'].'p':'';
	                $temp['biaozhun_jinzhong'] = $d['biaozhun_jinzhong_min'].'-'.$d['biaozhun_jinzhong_max'];
					$temp['bill_no'] = !empty($bill_nos) ? $bill_nos : 0 ;
					$temp['goods_num'] = !empty($nums) ? $nums : 0 ;
					foreach ($temp as $k => $v) {
	                    $temp[$k] = iconv('utf-8', 'GB18030', $v);
	                }
	                echo "\"".implode("\",\"",$temp)."\"\r\n";
	            }
	        }
	    }
	}
	public function DownloadCSV(){
	    set_time_limit(0);
	    header('Content-Type: application/vnd.ms-excel');
	    header("Content-Disposition: attachment;filename=productInfoList.csv");
	    header('Cache-Control: max-age=0');
	
	    $where=$this->getDataFarmat();
	    if(!isset($_REQUEST['opra_uname']))
	    {
	        $where['opra_uname'] = $_SESSION['userName'];
	    }
	    $model = new ProductInfoModel(13);
	    $productAttrModel = new ProductInfoAttrModel(13);
	    $salesModel = new SalesModel(28);
		$util=new Util();
		$styleselfmodel = new StyleModel(11);
	    $view = new ProductInfoView(new ProductInfoModel(13));
	   $buchan_status_arr = $buchan_fac_opra_arr = array();
        $buchan_status = $this->dd->getEnumArray('buchan_status');
        foreach ($buchan_status as $key => $value) {
            $buchan_status_arr[$value['name']]=$value['label'];
        }
        $buchan_fac_opra = $this->dd->getEnumArray('buchan_fac_opra');
        foreach ($buchan_fac_opra as $key => $value) {
            $buchan_fac_opra_arr[$value['name']]=$value['label'];
        }
	
	    $title=array('布产单号','布产来源','销售渠道','客户来源','客户姓名','款号','数量','工厂名称','跟单人','制单人','主石单颗石重','主石粒数','颜色','净度','证书号','镶嵌要求','镶口','材质','金色','金重','指圈','刻字','表面工艺','布产备注','布产类型','布产状态','生产状态','标准出厂时间','工厂接单日期','工厂交货时间','模号',
            '证书类型','主石类型','主石形状','副石类型','副石形状','属性','款式属性信息','是否快速定制','是否组合镶嵌','组合镶嵌现货托','采购/订单备注','特别备注','副石1总重/粒数','副石2总重/粒数','副石3总重/粒数','标准金重范围');
	    foreach ($title as $k => $v) {
	        $title[$k]=iconv('utf-8', 'GB18030', $v);
	    }
	    echo "\"".implode("\",\"",$title)."\"\r\n";
	    $page = 1;
	    $pageSize=30;
	    $pageCount=1;
	    $recordCount = 0;
	    $list_sql=$model->getsql($where);
	    while($page <= $pageCount){
	        $data = $model->db()->getPageListForExport($list_sql,array(),$page,$pageSize,false,$recordCount);
	        $page ++;
	        if(!empty($data['data'])){	            
	            $recordCount = $data['recordCount'];
	            $pageCount = $data['pageCount'];
	            $data = $data['data'];
	            if(!is_array($data) || empty($data)){
	                continue;
	            }
	            foreach($data as $d){
	                $temp=array();
	                //石重cart	颜色color	净度clarity	证书号zhengshuhao 材质caizhi	金色jinse	金重jinzhong	指圈zhiquan	刻字kezi	表面工艺face_work
	                $sql = "select code,value,name from product_info_attr where g_id='{$d['id']}'";//edit by zhangruiying
	                $attr_d = $model->db()->getAll($sql);
					$zhengshuhao = "";
					$style_sn = $d['style_sn'];
	                foreach ($attr_d as $v)
	                {  
	                    if($v['code']=="zhengshuhao"){
	                        $zhengshuhao = $v['value'];
	                    }
	                    $d[$v['code']] = $v['value'];
	                }
	                $temp['bc_sn']=$d['bc_sn'];
	                $temp['p_sn']=$d['p_sn'];
	                if(preg_match("/^\d+$/is",$temp['p_sn'])){
	                    $temp['p_sn'] = "'".$temp['p_sn'];
	                }
	                $temp['channel_id']=$view->get_channel_name($d['channel_id']);
	                $temp['customer_source_id']=$view->get_customer_name($d['customer_source_id']);
	                $temp['consignee']=$d['consignee'];
	                $temp['style_sn']=$style_sn;
	                $temp['num']=$d['num'];
	                $temp['prc_name']=$d['prc_id'];//$d['prc_name']
	                $temp['opra_uname']="";//$d['opra_uname']
	                $temp['make_name']=$d['create_user'];//制单人
	                $temp['cart']=isset($d['cart'])?$d['cart']:'';
	                if(empty($temp['cart']))
	                {
	                    $temp['cart']=isset($d['diamond_size'])?$d['diamond_size']:'';
	                }
	                if(empty($temp['cart']))
	                {
	                    $temp['cart']=isset($d['zuanshidaxiao'])?$d['zuanshidaxiao']:'';
	                }
	                $temp['zhushi_num']=isset($d['zhushi_num'])?$d['zhushi_num']:'';
	                $temp['color']=isset($d['color'])?$d['color']:'';
	                if(empty($temp['color']))
	                {
	                    $temp['color']=isset($d['yanse'])?$d['yanse']:'';
	                }
	
	                $temp['clarity']=isset($d['clarity'])?$d['clarity']:'';
	                if(empty($temp['clarity']))
	                {
	                    $temp['clarity']=isset($d['neatness'])?$d['neatness']:'';
	                }
	                if(empty($temp['clarity']))
	                {
	                    $temp['clarity']=isset($d['zuanshijingdu'])?$d['zuanshijingdu']:'';
	                }
	                if(empty($temp['clarity']))
	                {
	                    $temp['clarity']=isset($d['jingdu'])?$d['jingdu']:'';
	                }
	                $temp['zhengshuhao'] = $zhengshuhao;
	                if(preg_match("/^\d+$/is",$temp['zhengshuhao'])){
	                    $temp['zhengshuhao'] = "'".$temp['zhengshuhao'];
	                } 
	                $temp['xiangqian']=$d['xiangqian'];
                    $temp['xiangkou']=isset($d['xiangkou'])?$d['xiangkou']:'';
	                $temp['caizhi']=isset($d['caizhi'])?$d['caizhi']:'';
	                $temp['jinse']=isset($d['jinse'])?$d['jinse']:'';
	                if(empty($temp['jinse']))
	                {
	                    $temp['jinse']=isset($d['18k_color'])?$d['18k_color']:'';
	                }
	                $temp['jinzhong']=isset($d['jinzhong'])?$d['jinzhong']:'';
	                $temp['zhiquan']=isset($d['zhiquan'])?$d['zhiquan']:'';
	                $temp['kezi']=isset($d['kezi'])?$d['kezi']:'';
	                if(empty($temp['kezi']))
	                {
	                    $temp['kezi']=isset($d['work_con'])?$d['work_con']:'';
	                }
	                $temp['face_work']=isset($d['face_work'])?$d['face_work']:'';
	                if(empty($temp['face_work']))
	                {
	                    $temp['face_work']=isset($d['biaomiangongyi'])?$d['biaomiangongyi']:'';
	                }
	                $temp['info']=$d['info'];
	                $temp['bc_style']=$d['bc_style'];
	                $temp['status']=isset($buchan_status_arr[$d['status']])?$buchan_status_arr[$d['status']]:"";
                    $temp['buchan_fac_opra']=isset($buchan_fac_opra_arr[$d['buchan_fac_opra']])?$buchan_fac_opra_arr[$d['buchan_fac_opra']]:"";
	                $temp['esmt_time']=$d['esmt_time'];
	                $temp['order_time']=$d['order_time'];
	                $temp['rece_time']=$d['rece_time'];
	                $factory_id=$d['prc_id']?$d['prc_id']:" ";
	                //获取工厂模号
	                //$note=$this->getFactoryStyle(array('id'=>$d['id'],'p_sn'=>$d['p_sn'],'style_sn'=>$d['style_sn'],'prc_id'=>$d['prc_id'],'from_type'=>$d['from_type']));
	                //$temp['m_sn']=isset($note['factory_sn'])?$note['factory_sn']:'';
	                //获取工厂模号
	                if(empty($d['def_factory_sn'])){
	                    $note=$this->getFactoryStyle(array('id'=>$d['id'],'p_sn'=>$d['p_sn'],'style_sn'=>$d['style_sn'],'prc_id'=>$d['prc_id'],'from_type'=>$d['from_type']));
	                    $p_data = array(
	                        'def_factory_sn'=>$note['factory_sn'],
	                        'def_factory_name'=>$note['factory_name']
	                    );
	                    $res = $model->update($p_data,"id={$d['id']}");
	                    $temp['def_factory_sn']=isset($note['factory_sn'])?$note['factory_sn']:'';
	                }else{
	                    $temp['def_factory_sn'] = $d['def_factory_sn'];
	                }
	                //对取到的所有数据进行款式属性补全   
	                $title_add='';
                    //证书类型
                    $temp['cert']=isset($d['cert'])?$d['cert']:'';
                    //主石类型
                    $temp['zhushi_cat']=isset($d['zhushi_cat'])?$d['zhushi_cat']:'';
                    //主石形状
                    $temp['zhushi_shape']=isset($d['zhushi_shape'])?$d['zhushi_shape']:'';
                    //副石类型
                    $temp['fushi_cat']=isset($d['fushi_cat'])?$d['fushi_cat']:'';
                    //副石形状
                    $temp['fushi_shape']=isset($d['fushi_shape'])?$d['fushi_shape']:'';
	                foreach($attr_d as $k=>$attr)
	                {
	                    $title_add.=$attr['name'].':'.$attr['value']."\r\n";

                        if($attr['code']=='cert'){
                            $temp['cert']=$attr['value'];
                        }
                        if($attr['code']=='zhushi_shape'){
                            $temp['zhushi_shape']=$attr['value'];
                        }
                        if($attr['code']=='fushi_shape'){
                            $temp['fushi_shape']=$attr['value'];
                        }
	                }
					$temp['attr']=$title_add;	
					//取出款号属性信息
					$style_zhuaxz = $styleselfmodel->getAttrByStyleSn($style_sn, "爪钉形状");
					$temp['style_attr'] = '爪钉形状:'.$style_zhuaxz;
	                $temp['is_quick_diy'] = $d['is_quick_diy']==1?"是":"否";
	                $temp['is_combine'] = $d['is_combine']==1?'是':'否';
	                $temp['combine_goods_id'] = "'".$d['combine_goods_id'];
	                $temp['caigou_info'] = isset($d['caigou_info'])?$d['caigou_info']:''; //特别备注
	                $temp['special'] = isset($d['special'])?$d['special']:''; //特别备注
  			$attrKeyVal = array_column($attr_d, 'value','code');
	                $temp['fushi1'] = !empty($attrKeyVal['fushi_num1'])?$attrKeyVal['fushi_zhong_total1'].'ct/'.$attrKeyVal['fushi_num1'].'p':'';
	                $temp['fushi2'] = !empty($attrKeyVal['fushi_num2'])?$attrKeyVal['fushi_zhong_total2'].'ct/'.$attrKeyVal['fushi_num2'].'p':'';
	                $temp['fushi3'] = !empty($attrKeyVal['fushi_num3'])?$attrKeyVal['fushi_zhong_total3'].'ct/'.$attrKeyVal['fushi_num3'].'p':'';
	                $temp['biaozhun_jinzhong'] = $d['biaozhun_jinzhong_min'].'-'.$d['biaozhun_jinzhong_max'];
	                foreach ($temp as $k => $v) {
	                    $temp[$k] = iconv('utf-8', 'GB18030', $v);
	                }
	                echo "\"".implode("\",\"",$temp)."\"\r\n";
	            }
	        }
	    }
	}	

    public function DownloadCSVS(){
        set_time_limit(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=productInfoLists.csv");
        header('Cache-Control: max-age=0');
    
        $where=$this->getDataFarmat();
        if(!isset($_REQUEST['opra_uname']))
        {
            $where['opra_uname'] = $_SESSION['userName'];
        }
        $model = new ProductInfoModel(13);
        $view = new ProductInfoView(new ProductInfoModel(13));
        $title=array('布产单号','布产来源','销售渠道','客户姓名','款号','数量','工厂名称','主石单颗石重','主石粒数','颜色','净度','证书号','镶嵌要求','镶口','材质','金色','金重','指圈','布产备注','布产类型','布产状态','生产状态','标准出厂时间','工厂接单日期','工厂交货时间','证书类型','属性','是否快速定制','采购/订单备注','特别备注');
        foreach ($title as $k => $v) {
            $title[$k]=iconv('utf-8', 'GB18030', $v);
        }
        echo "\"".implode("\",\"",$title)."\"\r\n";
        $page = 1;
        $pageSize=60;
        $pageCount=1;
        $recordCount = 0;
        $list_sql=$model->getsql($where);
        $buchan_status_arr = $buchan_fac_opra_arr = array();
        $buchan_status = $this->dd->getEnumArray('buchan_status');
        foreach ($buchan_status as $key => $value) {
            $buchan_status_arr[$value['name']]=$value['label'];
        }
        $buchan_fac_opra = $this->dd->getEnumArray('buchan_fac_opra');
        foreach ($buchan_fac_opra as $key => $value) {
            $buchan_fac_opra_arr[$value['name']]=$value['label'];
        }
        while($page <= $pageCount){
            $data = $model->db()->getPageListForExport($list_sql,array(),$page,$pageSize,false,$recordCount);
            $page ++;
            if(!empty($data['data'])){              
                $recordCount = $data['recordCount'];
                $pageCount = $data['pageCount'];
                $data = $data['data'];
                if(!is_array($data) || empty($data)){
                    continue;
                }
                foreach($data as $d){
                    $temp=array();
                    //石重cart    颜色color 净度clarity   证书号zhengshuhao 材质caizhi 金色jinse 金重jinzhong  指圈zhiquan   刻字kezi  表面工艺face_work
                    $sql = "select code,value,name from product_info_attr where g_id='{$d['id']}'";//edit by zhangruiying
                    $attr_d = $model->db()->getAll($sql);
                    $zhengshuhao = "";
                    $style_sn = $d['style_sn'];
                    foreach ($attr_d as $v)
                    {  
                        if($v['code']=="zhengshuhao"){
                            $zhengshuhao = $v['value'];
                        }
                        $d[$v['code']] = $v['value'];
                    }
                    $temp['bc_sn']=$d['bc_sn'];
                    $temp['p_sn']=$d['p_sn'];
                    if(preg_match("/^\d+$/is",$temp['p_sn'])){
                        $temp['p_sn'] = "'".$temp['p_sn'];
                    }
                    $temp['channel_id']=$view->get_channel_name($d['channel_id']);
                    $temp['consignee']=$d['consignee'];
                    $temp['style_sn']=$style_sn;
                    $temp['num']=$d['num'];
                    $temp['prc_name']=$d['prc_id'];//$d['prc_name']
                    $temp['cart']=isset($d['cart'])?$d['cart']:'';
                    if(empty($temp['cart']))
                    {
                        $temp['cart']=isset($d['diamond_size'])?$d['diamond_size']:'';
                    }
                    if(empty($temp['cart']))
                    {
                        $temp['cart']=isset($d['zuanshidaxiao'])?$d['zuanshidaxiao']:'';
                    }
                    $temp['zhushi_num']=isset($d['zhushi_num'])?$d['zhushi_num']:'';
                    $temp['color']=isset($d['color'])?$d['color']:'';
                    if(empty($temp['color']))
                    {
                        $temp['color']=isset($d['yanse'])?$d['yanse']:'';
                    }
    
                    $temp['clarity']=isset($d['clarity'])?$d['clarity']:'';
                    if(empty($temp['clarity']))
                    {
                        $temp['clarity']=isset($d['neatness'])?$d['neatness']:'';
                    }
                    if(empty($temp['clarity']))
                    {
                        $temp['clarity']=isset($d['zuanshijingdu'])?$d['zuanshijingdu']:'';
                    }
                    if(empty($temp['clarity']))
                    {
                        $temp['clarity']=isset($d['jingdu'])?$d['jingdu']:'';
                    }
                    $temp['zhengshuhao'] = $zhengshuhao;
                    if(preg_match("/^\d+$/is",$temp['zhengshuhao'])){
                        $temp['zhengshuhao'] = "'".$temp['zhengshuhao'];
                    } 
                    $temp['xiangqian']=$d['xiangqian'];
                    $temp['xiangkou']=isset($d['xiangkou'])?$d['xiangkou']:'';
                    $temp['caizhi']=isset($d['caizhi'])?$d['caizhi']:'';
                    $temp['jinse']=isset($d['jinse'])?$d['jinse']:'';
                    if(empty($temp['jinse']))
                    {
                        $temp['jinse']=isset($d['18k_color'])?$d['18k_color']:'';
                    }
                    $temp['jinzhong']=isset($d['jinzhong'])?$d['jinzhong']:'';
                    $temp['zhiquan']=isset($d['zhiquan'])?$d['zhiquan']:'';
                    $temp['info']=$d['info'];
                    $temp['bc_style']=$d['bc_style'];
                    $temp['status']=isset($buchan_status_arr[$d['status']])?$buchan_status_arr[$d['status']]:"";
                    $temp['buchan_fac_opra']=isset($buchan_fac_opra_arr[$d['buchan_fac_opra']])?$buchan_fac_opra_arr[$d['buchan_fac_opra']]:"";
                    $temp['esmt_time']=$d['esmt_time'];
                    $temp['order_time']=$d['order_time'];
                    $temp['rece_time']=$d['rece_time'];
                    $factory_id=$d['prc_id']?$d['prc_id']:" ";
                    //证书类型
                    $temp['cert']=isset($d['cert'])?$d['cert']:'';
                    $title_add='';
                    foreach($attr_d as $k=>$attr)
                    {
                        $title_add.=$attr['name'].':'.$attr['value']."\r\n";
                    }
                    $temp['attr']=$title_add;   
                    $temp['is_quick_diy'] = $d['is_quick_diy']==1?"是":"否";
                    $temp['caigou_info'] = isset($d['caigou_info'])?$d['caigou_info']:''; //特别备注
                    $temp['special'] = isset($d['special'])?$d['special']:''; //特别备注
                    foreach ($temp as $k => $v) {
                        $temp[$k] = iconv('utf-8', 'GB18030', $v);
                    }
                    echo "\"".implode("\",\"",$temp)."\"\r\n";
                }
            }
        }
    }   

        public function DownloadCsv_bak0212()
        {
		$where=$this->getDataFarmat();
		if(!isset($_REQUEST['opra_uname']))
		{
			//echo $_SESSION['userName'];exit;
			$where['opra_uname'] = $_SESSION['userName'];
		}
		//var_dump($where);exit;
		$model = new ProductInfoModel(13);
		$data = $model->getdownload($where);
		//var_dump($data);exit;
                $util=new Util();
                $title=array('布产单号','布产来源','销售渠道','客户来源','客户姓名','款号','数量','工厂名称','跟单人','制单人','石重','颜色','净度','证书号','镶嵌要求','材质','金色','金重','指圈','刻字','表面工艺','布产备注','布产类型','布产状态','生产状态','标准出厂时间','工厂接单问题','工厂交货时间','模号','属性');
				$view = new ProductInfoView(new ProductInfoModel(13));
				$arr=array();
                foreach($data as $key=>$v)
                {
				$title_add='';
                $temp=array();
                $temp['bc_sn']=$v['bc_sn'];
                $temp['p_sn']=$v['p_sn'];
				$temp['channel_id']=$view->get_channel_name($v['channel_id']);
				$temp['customer_source_id']=$view->get_customer_name($v['customer_source_id']);
				$temp['consignee']=$v['consignee'];
				$temp['style_sn']=$v['style_sn'];
				$temp['num']=$v['num'];
				$temp['prc_name']=$v['prc_name'];
				$temp['opra_uname']=$v['opra_uname'];
				$temp['make_name']=$v['create_user'];//制单人
				$temp['cart']=isset($v['cart'])?$v['cart']:'';
				if(empty($temp['cart']))
				{
					$temp['cart']=isset($v['diamond_size'])?$v['diamond_size']:'';
				}
				if(empty($temp['cart']))
				{
					$temp['cart']=isset($v['zuanshidaxiao'])?$v['zuanshidaxiao']:'';
				}
				$temp['color']=isset($v['color'])?$v['color']:'';
				if(empty($temp['color']))
				{
					$temp['color']=isset($v['yanse'])?$v['yanse']:'';
				}

				$temp['clarity']=isset($v['clarity'])?$v['clarity']:'';
				if(empty($temp['clarity']))
				{
					$temp['clarity']=isset($v['neatness'])?$v['neatness']:'';
				}
				if(empty($temp['clarity']))
				{
					$temp['clarity']=isset($v['zuanshijingdu'])?$v['zuanshijingdu']:'';
				}
				if(empty($temp['clarity']))
				{
					$temp['clarity']=isset($v['jingdu'])?$v['jingdu']:'';
				}

				$temp['zhengshuhao']=isset($v['zhengshuhao'])?$v['zhengshuhao']:'';
				if(empty($temp['zhengshuhao']))
				{
					$temp['zhengshuhao']=isset($v['certificate_no'])?$v['certificate_no']:'';
				}
				$temp['xiangqian']=$v['xiangqian'];
				$temp['caizhi']=isset($v['caizhi'])?$v['caizhi']:'';
				$temp['jinse']=isset($v['jinse'])?$v['jinse']:'';
				if(empty($temp['jinse']))
				{
					$temp['jinse']=isset($v['18k_color'])?$v['18k_color']:'';
				}
				$temp['jinzhong']=isset($v['jinzhong'])?$v['jinzhong']:'';
				$temp['zhiquan']=isset($v['zhiquan'])?$v['zhiquan']:'';
				$temp['kezi']=isset($v['kezi'])?$v['kezi']:'';
				if(empty($temp['kezi']))
				{
					$temp['kezi']=isset($v['work_con'])?$v['work_con']:'';
				}
				$temp['face_work']=isset($v['face_work'])?$v['face_work']:'';
				if(empty($temp['face_work']))
				{
					$temp['face_work']=isset($v['biaomiangongyi'])?$v['biaomiangongyi']:'';
				}
				$temp['info']=$v['info'];
				$temp['bc_style']=$v['bc_style'];
				$temp['status']=$this->dd->getEnum('buchan_status',$v['status']);
				$temp['buchan_fac_opra']=$this->dd->getEnum('buchan_fac_opra',$v['buchan_fac_opra']);
				$temp['esmt_time']=$v['esmt_time'];
				$temp['order_time']=$v['order_time'];
				$temp['rece_time']=$v['rece_time'];
				$factory_id=$v['prc_id']?$v['prc_id']:" ";
				/*
				$ori_str=array('style_sn'=>$v['style_sn'],'factory_id'=>$factory_id,'is_default'=>1);
				ksort($ori_str);
				$ori_str=json_encode($ori_str);
				$data=array("filter"=>$ori_str,"sign"=>md5('style'.$ori_str.'style'));
				$ret=Util::httpCurl(Util::getDomain().'/api.php?con=style&act=GetFactryInfo',$data);
				$ret=json_decode($ret,true);
				$temp['m_sn']='';
				if(!empty($ret['return_msg']))
				{
					$temp['m_sn']=$ret['return_msg'][0]['factory_sn'];
				}
				*/
				//获取工厂模号
				 $note=$this->getFactoryStyle(array('id'=>$v['id'],'p_sn'=>$v['p_sn'],'style_sn'=>$v['style_sn'],'prc_id'=>$v['prc_id'],'from_type'=>$v['from_type']));
				 $temp['m_sn']=isset($note['factory_sn'])?$note['factory_sn']:'';
               // $temp['style_sn']=$v['style_sn'];
                //$temp['buchan_status']=$this->dd->getEnum('buchan_status',$v['status']);
				foreach($v['attr'] as $k=>$attr)
				{
					$title_add.=$attr['name'].':'.$attr['value']."\r\n";
				}
				$temp['attr']=$title_add;
                $arr[]=$temp;
                }
                $util->downloadCsv('productInfoList',$title,$arr);

        }

       /* 
        //批量打印提货单
       public function bath_print_bill($params){
       	//var_dump($_REQUEST);exit;

       	$ids = _Request::get('_ids');   //订单号字符串

       	$order_sn_str = explode(',', $ids);

       	$kuaidiModel = new ExpressModel(1);
       	$SalesChannelsmodel = new SalesChannelsModel(1);
       	$dd = new DictView(new DictModel(1));

       	$html = '';
		$kezimodel = new Kezi();
       	foreach($order_sn_str AS $k => $bc_id){
       		//获取订单号
       		//$id = _Request::get('id');   //订单号字符串
       		$ProductInfoModel = new ProductInfoModel($bc_id,13);
       		$id =$ProductInfoModel->getValue('p_sn');
       		$from_type =$ProductInfoModel->getValue('from_type');

       		if($from_type==1){
	       		echo "<div style='font-size:30px;margin-top:30px;text-align:center'>采购布产单不可以打印提货单！：<span style='color:red;'>{$id}</span> ！</div>";
				exit;
       		}
       		//new 数据字典 渠道 快递
       		$kuaidiModel = new ExpressModel(1);
       		$SalesChannelsmodel = new SalesChannelsModel(1);
       		//通过接口，获取 订单信息
       		$orderinfo = ApiSalesModel::GetPrintBillsInfo($id);
       		//var_dump($orderinfo);exit;
       		if(!empty($orderinfo['return_msg'])){
			//获取支付方式 拼接
       		if (isset($orderinfo['return_msg']['order_pay_type'])){
       			$order_pay_type = $orderinfo['return_msg']['order_pay_type'];
       			if($order_pay_type){
       				$newmodel =  new PaymentModel($order_pay_type,2);
       				$orderinfo['return_msg']['order_pay_name'] = $newmodel->getValue('pay_name');
       			}else{
       				$orderinfo['return_msg']['order_pay_name']='——';
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
                $orderinfo['return_msg']['out_order_sn']=!empty($ret['return_msg']['out_order_sn'])?$ret['return_msg']['out_order_sn']:'';
       		}
       			$orderinfo['return_msg']['express_id'] = $kuaidiModel->getNameById( $orderinfo['return_msg']['express_id'] ) ? $kuaidiModel->getNameById( $orderinfo['return_msg']['express_id'] ) : '——';

       			$orderinfo['return_msg']['user_name'] = '--';
       			//获取单据会员名字
       			

       			//获取单据明细
       			$detail = ApiSalesModel::GetOrderDetailByOrderId($orderinfo['return_msg']['order_sn']);
       			foreach($detail as $key=> &$val){
       				$gallery_data =$this->getImg($val['goods_sn'],$bc_id,1);
       				$detail[$key]['goods_img']=isset($gallery_data[0]['thumb_img'])?$gallery_data[0]['thumb_img']:'';
					$detail[$key]['bing'] = 0; //无商品绑定
					//检测是否绑定
					$goods_bing = ApiWarehouseModel::GetBingInfo($val['id']);
					if(!empty($goods_bing)){
						$detail[$key]['bing'] = 1; //有商品绑定
						$detail[$key]['goods_id'] = $goods_bing['goods_id'];
						$detail[$key]['box_sn'] = $goods_bing['box_sn'] ? $goods_bing['box_sn'] : '无';
					}
					if(isset($detail[$key]['kezi']) and !empty($detail[$key]['kezi'])){
                    $detail[$key]['kezi']=$kezimodel->retWord($detail[$key]['kezi']);
					}
					//获取售卖方式
					$temInfo = ApiModel::process_api( array('p_id'),array($detail[$key]['id']),'GetProductInfo');
					$detail[$key]['is_alone'] = 0;
					if(isset($temInfo['is_alone'])){
					    $detail[$key]['is_alone'] = $temInfo['is_alone'];
					}
       			}
       			//获取配货单单据的配送类型
       			if($orderinfo['return_msg']['distribution_type'] == 1){
       				//如果下单的配送类型 为1 (数字字典 sales.distribution_type)。到门店 则在提货单的配送类型中。显示下订单的门店名

       				$orderinfo['return_msg']['department_id'] = strstr($orderinfo['return_msg']['address'], '|' , true);
       			}else{
       				$orderinfo['return_msg']['department_id'] = $this->dd->getEnum('sales.distribution_type', $orderinfo['return_msg']['distribution_type']);
       			}

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
                         //添加回写订单日志
                        if(isset($orderinfo['return_msg']['order_sn']) && !empty($orderinfo['return_msg']['order_sn'])) {
                             $order_sn = $orderinfo['return_msg']['order_sn'];
                            //$time = date('Y-m-d H:i:s');
                            // $user = $_SESSION['userName'];
                            $remark = "订单：".$order_sn." 打印提货单(供应商布产批量打印)";
                            $logModel = new ProductOpraLogModel(14);
                            $logModel->addLog($bc_id,$remark);
                        }
       			$html.= $this->fetch('foreach.html', array(
       					'info' => $orderinfo['return_msg'],
       					'goods_list' => $detail,
       			));

       		}else{
       			echo "<div style='font-size:30px;margin-top:30px;text-align:center' class='kela_close'>未查询到单号：<span style='color:red;'>{$id}</span> 的此订单！</div>";
       		}

       	}
       	$this->render('bath_print_bill.html', array(
       		'html'=>$html,
       	));

       }
       */

        //批量打印提货单
       public function bath_print_bill($params){
       	//var_dump($_REQUEST);exit;
        ini_set('memory_limit','-1');
        set_time_limit(0);
       	$ids = _Request::get('_ids');   //订单号字符串

       	$bc_ids = explode(',',$ids);

       	$kuaidiModel = new ExpressModel(1);
       	$SalesChannelsmodel = new SalesChannelsModel(1);
       	$dd = new DictView(new DictModel(1));
       	$salesmodel=new SalesModel(27);
        //$bespokemodel= new BaseMemberInfoModel(17);
        $productinfomodel=new ProductInfoModel(13);
        $gallerymodel = new BaseStyleInfoModel(12);
        $WarehouseModel = new WarehouseModel(21);
        $logModel = new ProductOpraLogModel(14);


       	$html = '';
		$ke = new Kezi();
		ob_start(); 
		$orders=array();
       	foreach($bc_ids AS $k => $bc_id){
       		//获取订单号
       		//$id = _Request::get('id');   //订单号字符串
       		$ProductInfoModel = new ProductInfoModel($bc_id,13);
       		$v =$ProductInfoModel->getValue('p_sn');
       		$from_type =$ProductInfoModel->getValue('from_type');

       		if($from_type==1){
	       		echo "<div style='font-size:30px;margin-top:30px;text-align:center'>采购布产单不可以打印提货单！：<span style='color:red;'>{$id}</span> ！</div>";
				exit;
       		}
            if(in_array($v,$orders))
                continue;
            else 
                $orders[]=$v; 
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
                if(SYS_SCOPE=='boss'){
                	$order_pay_type_limit= json_decode(INVOICE_ORDER_PAY_TYPE_LIMIT,true) ;  //需要开电子发票的支付方式        
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
          
            if(!empty($orderinfo)){
            	if($orderinfo['distribution_type']==2){
                 if(empty($orderinfo['express_id'])){
                     exit("订单：$v 快递类型不能为空");
                 }
            	}
                $orderinfo['express_id']=$kuaidiModel->getNameById( $orderinfo['express_id'] );
                $orderinfo['express_id'] =  $orderinfo['express_id'] ? $orderinfo['express_id']: '——';

                $orderinfo['user_name'] = '--';
                /*
                //获取单据会员名字
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
                        
                        $gallery_data = $gallerymodel->GetStyleGalleryInfo($bing_val['goods_sn'],1);
                        //$gallery_data是一个二维数组
                        if(isset($gallery_data[0]['thumb_img'])){
                            $detail[$p_key]['goods_img']=$gallery_data[0]['thumb_img'];
                        }else{
                            $detail[$p_key]['goods_img']='';
                            //$detail[$p_key]['goods_img']='images/styles/201007/1279189436925042936.jpg';
                        }
                        $detail[$p_key]['cat_type_name']=isset($gallery_data[0]['cat_type_name']) ? $gallery_data[0]['cat_type_name'] : ""; 
                        $bing_val['box_id'] = '无';


                        $bing_val['bing'] = 0; //无商品绑定
                        $res=$WarehouseModel->getOrderGoodsAndBox($bing_val['order_detail_id']);
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
                    'detail_num' => $detail_num                    
                ));

            }else{
                $html.= "<table class=\"PageNext\" cellspacing=\"0\" cellpadding=\"1\" width=\"100%\" border=\"0\"><tr><td><hr><b>未查询到单号/或者订单被取消或者关闭：<span style='color:red;'>{$v}</span></b> <hr></td></tr></table>";
            }

            $this->render('bath_print_bill.html', array(
            'html'=>$html,
            'bc_ids'=>$ids,    
            ));
            
            flush();          
        }
         flush();    

    }        










	   //add by zhangruiying
	   function to_factory_edit($params)
	   {
		    $id = intval($params["id"]);
			$result = array('success' => 0,'error' => '');
		    $tab_id = _Request::getInt("tab_id");
			$result['content'] =$this->fetch('to_factory_send.html',array(
				'tab_id'=>$tab_id,
				'id'=>$id
			));
			$result['title'] = '开始生产';
			Util::jsonExit($result);
	   }
	   /**************************************************************
	   function:baoguan
	   description:报关，增加一条操作日志
	   ***************************************************************/
	   public function baoguan($params)
	   {
		    $result = array('success'=>0,'error'=>'');
		   	//记录操作日志
			$id = $params['id'];
			//$model = new ProductInfoModel($id,14);
			//$status = $model->getValue('status');
			$logModel = new ProductOpraLogModel(14);
			//$res = $logModel->addLog($id,4,"报关");
			$res = $logModel->addLog($id,"布产单报关");
			if(!$res)
			{
				$result['error'] = "操作失败";
				Util::jsonExit($result);
			}
			$result['success'] = 1;
			Util::jsonExit($result);
	   }

	/**************************************************************************
	fun:re_buchan
	description:重新布产
	para:@array('0'=>布产id,.....)
	
	选项按钮：1》重新生产、2》继续生产、3》缺货转生产

	页面打开条件：
	条件1：布产状态：不需布产 或 已出厂( status： 11 ，9 )
	条件2：如果布产类型为订单 (from_type :2)，订单的配货状态为：未配货 或 允许配货; (delivery_status:1,2)，发货状态：未发货 send_good_status 1；采购类型不做限制

	提交验证条件：
	条件1：如果选择这2个按钮 ----1》重新生产、2》继续生产 ---，布产状态必须是已出厂，
	条件2：如果选择 3》缺货转生产，布产状态必须为不需布产
	条件3：如果布产类型为订单 (from_type :2)，订单的配货状态为：未配货 或 允许配货 (delivery_status:1,2)

	操作步骤：
	1》重新生产
	a、布产状态：已分配（status：3）、生产状态：未操作（buchan_fac_opra:1)、order_time 接单时间、esmt_time 标准出厂时间 、rece_time 工厂交货时间 清空;remark (重新布产),edit_time (当前时间)
	b、订单更新日志，备注（布产BCXXX重新生产），配货状态修改为未配货
	c、布产日志添加
	d:删除所有工厂出货明细
	e:配石标红

	2》继续生产
	a、还原布产状态？？（如果不是部分出厂就还原为生产中）
	b、还原生产状态？？ （开始生产状态）
	c、订单更新日志，备注（布产BCXXX继续生产），配货状态修改为未配货
	d、rece_time 工厂交货时间 清空;remark (重新布产),edit_time (当前时间)
	e、布产日志添加
	f、如果存在多次出厂，只删除最后一笔工厂出货明细
	g、配石标红

	3》缺货转生产
	a、布产状态初始化、生产状态未操作
	b、订单日志
	c、订单配货状态修改为未配货
	d、布产更新最后操作时间和备注
	e、布产添加日志

	最后都增加布产次数（默认为1）
	OQC增加次数
	配石增加次数
	*****************************************************************************/
	public function re_buchan($params)
	{   
	    $result['title'] = '批量重新布产';
	    $ids = _Post::getList('_ids');
		
		if(empty($ids)){		    
		    $result['content'] ="批量重新布产Id为空";
		    Util::jsonExit($result);
		}
		
		$result['content'] = $this->fetch('re_buchan.html',array(
			'ids'=> $ids
		));
		$result['title'] = '批量重新布产';
		Util::jsonExit($result);
	}
    
	/*重新布产提交操作**/
	public function re_buchan_save($params)
	{
		$result = array('success'=>0,'error'=>'');
		$buchanTypeNames = array(
		    1=>'重新布产',
		    2=>'继续生产',
		    3=>'缺货转生产 ',
		);
		$ids         = _Post::getList('_ids');
		$buchan_type = _Post::get('buchan_type','');

		if(empty($ids)){
		    $result['error'] = "请选择需要重新布产的布产号!";
		    Util::jsonExit($result);
		}else if(empty($buchan_type)){
		    $result['error'] = "请选择布产类型!";
		    Util::jsonExit($result);
		}
		 
	    $salesModel          = new SalesModel(27);
	    $productOpraLogModel = new ProductOpraLogModel(13);
	    $productShipmentModel = new ProductShipmentModel(13);
	    $warehouseModel       = new WarehouseModel(21); 
	    
	    $pdolist[13] = $productOpraLogModel->db()->db();
	    $pdolist[27] = $salesModel->db()->db();
	    $pdolist[21] = $warehouseModel->db()->db();
	    
	    foreach ($pdolist as $pdo){
	        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
	        $pdo->beginTransaction(); //开启事务
	    }
		foreach($ids as $id){    		    
            $productInfoModel = new ProductInfoModel($id,13);
            $productInfoOld   = $productInfoModel->getDataObject();
            
		    $status    = $productInfoOld['status'];
		    $from_type = $productInfoOld['from_type'];
		    $order_sn  = $productInfoOld['p_sn'];
		    $goods_detail_id = $productInfoOld['p_id'];
		    $bc_sn     = $productInfoOld['bc_sn'];
		    $order_time= $productInfoOld['order_time'];
		    $esmt_time = $productInfoOld['esmt_time'];
		    $rece_time = $productInfoOld['rece_time'];
		    $bc_times  = (int)$productInfoOld['buchan_times'];
		    $bc_status_name = $this->dd->getEnum('buchan_status',$status);
		    if ($from_type == 2){
		        $baseOrderInfo = $salesModel->getBaseOrderInfoByOrderSn("*",$order_sn);
		        if (empty($baseOrderInfo))
		        {
		            $result['error'] = "【{$bc_sn}】布产单关联的订单{$order_sn}有异常！提示：未查询到此订单信息";
		            Util::jsonExit($result);
		        }
		        $order_id = $baseOrderInfo['id'];
		    }
		    /********************************数据校验 begin**************************/
		    if($buchan_type == 1 || $buchan_type == 2){
		        /**
		         * 1.【重新布产】和【继续布产】验证,只有布产状态是【已出厂】才允许提交成功
		         * 
		         * */     
		        if($status != 9){
		            $result['error'] = "【{$bc_sn}】布产状态【{$bc_status_name}】不对。提示：只有布产单状态为【已出厂】才允许".$buchanTypeNames[$buchan_type];
		            Util::jsonExit($result);
		        }
		        
		    }else if($buchan_type == 3){
		        /**
		         * 【缺货转生产】验证
		         * 3 布产原因【缺货转生产】，只有布产状态是【不需布产】才允许提交成功，
		         * 提交后销售订单、布产列表、查看全部布产单、布产监控、采购布产列表、配石列表部分状态和内容需要更新
		         */
		        if($status != 11){
		            $result['error'] = "【{$bc_sn}】布产状态【{$bc_status_name}】不对。提示：只有布产单状态为【不需布产】才允许缺货转生产";
		            Util::jsonExit($result);
		        }
		    
		    }
		    if ($from_type == 2){		    
		        if (!in_array($baseOrderInfo['delivery_status'],array(1,2,3)))
		        {
		            $delivery_status_name = $this->dd->getEnum('sales.delivery_status',$baseOrderInfo['delivery_status']);
		            $result['error'] = "【{$bc_sn}】订单【{$order_sn}】配货状态【{$delivery_status_name}】不对。提示：只有订单配货状态为【未配货】【配货中】【允许配货】才允许".$buchanTypeNames[$buchan_type];
		            Util::jsonExit($result);
		        }
		        if($baseOrderInfo['send_good_status'] !=1){
		            $send_good_status_name = $this->dd->getEnum('order.send_good_status',$baseOrderInfo['send_good_status']);
		            $result['error'] = "【{$bc_sn}】订单【{$order_sn}】发货状态【{$send_good_status_name}】不对。订单发货状态为【未发货】才允许".$buchanTypeNames[$buchan_type];
		            Util::jsonExit($result);
		        }
		    }
		    /********************************数据校验 end**************************/
		    
		    /********************************数据处理 begin**************************/		    		    
		    if($buchan_type == 1){
		        $bc_status  = 3;//布产状态：已分配
		        $buchan_fac_opra =1; //生产状态：未操作
		        $delivery_status =1;//配货状态：未配货 
		        
		        $order_time = "0000-00-00 00:00:00";//工厂接单时间
		        $esmt_time  = "0000-00-00 00:00:00";//标准出厂时间
		        $rece_time  = "0000-00-00 00:00:00";//工厂交货时间
 		        $remark = "布产单【{$bc_sn}】重新提交布产-重新布产【布产单已出厂,工厂需重新接单生产】";
		        
		    }else if($buchan_type == 2){
		        $bc_status = 4;//布产状态：生产中
		        $buchan_fac_opra =2; //生产状态：开始生产
		        $delivery_status =1;//配货状态：未配货	
		        $rece_time  = "0000-00-00 00:00:00";//工厂交货时间
		        $remark = "布产单【{$bc_sn}】重新提交布产-继续生产 【布产单已出厂,工厂无需重新接单,需重新生产】";
		    }else{
		        $bc_status = 1;//布产状态：初始化
		        $buchan_fac_opra =1; //生产状态：未操作
		        $delivery_status =1;//配货状态：未配货
		        $remark = "布产单【{$bc_sn}】重新提交布产-缺货转生产 【布产单不需布产，缺货转生产】";
		        		        
		    }
		    
		    
		    if($buchan_type==3){
		        //如果是【缺货转生产】+【订单销售模式】，就进行货品解绑 begin
		        //注意该快代码务必写在订单日志写入之前（主要与订单备注$remark有关）
		        //是否绑定货品
		        $res = $warehouseModel->getBCGoodsHasBind($goods_detail_id);
		        if(!empty($res)){
		            $goods_id = $res['goods_id'];
		            $data = array(
		                'order_goods_id'=>0,
		                'is_on_sale'    =>2,//库存状态
		            );
		            $res = $warehouseModel->updateWarehouseGoods($data,"order_goods_id='{$goods_detail_id}'");
		            if(!$res){
		                $error = "操作失败，事物回滚！提示：【{$bc_sn}】解绑货号{$goods_id}失败 error:".__LINE__;
		                Util::rollbackExit($error,$pdolist);
		            }
		            $remark = "布产单【{$bc_sn}】重新提交布产-缺货转生产【布产单不需布产，缺货转生产】,货号【{$goods_id}】自动解绑";
		             
		        }else{
		            $error = "操作失败，事物回滚！提示：【{$bc_sn}】订单商品货号主键ID{$goods_detail_id}未绑定现货 error:".__LINE__;
		            Util::rollbackExit($error,$pdolist);
		        }
		        //货品解绑 end
		    }
		    //重置布产信息
		    $producInfoNew = array(
		        'id'   =>$id,
		        'buchan_times'=>$bc_times+1,//布产次数递加1
		        'status'=>$bc_status,//已分配
		        'order_time'=>$order_time,//工厂接单时间
		        'buchan_fac_opra'=>$buchan_fac_opra,//工厂生产状态
		        'esmt_time' =>$esmt_time,//标准出厂时间
		        'rece_time' =>$rece_time,//工厂交货时间
		        'remark'    =>$remark,
		        'edit_time' =>date('Y-m-d H:i:s'),//重新布产时间
		    );
		    $res = $productInfoModel->saveData($producInfoNew,$productInfoOld);
		    if(!$res){
		        $error = "操作失败，事物回滚！提示：【{$bc_sn}】重置布产信息失败 error:".__LINE__;
		        Util::rollbackExit($error,$pdolist);
		    }
		    //布产日志添加
		    $data=array(
		        'bc_id'		=> $id,
		        'status'	=> $bc_status,//当前布产状态
		        'remark'	=> $remark,
		        'uid'		=> Auth::$userId?Auth::$userId:0,
		        'uname'		=> Auth::$userName?Auth::$userName:'第三方',
		        'time'		=> date('Y-m-d H:i:s')
		    );		    
		    $res = $productOpraLogModel->saveData($data,array());
		    if(!$res){
		        $error = "操作失败，事物回滚！提示：【{$bc_sn}】重新布产日志写入失败  error:".__LINE__;
		        Util::rollbackExit($error,$pdolist);
		    }
		    //删除出货记录
		    $res = $productShipmentModel->deleteByBcId($id);
		    if(!$res){
		        $error = "操作失败，事物回滚！提示：【{$bc_sn}】删除工厂历史出货明细失败 error:".__LINE__;
		        Util::rollbackExit($error,$pdolist);
		    }
		    
		    if($from_type == 2){
		        //天生一对加盟商的订单,重新布产后,配货状态是允许配货(2)，需要更新为【未配货】(1)
		        if($baseOrderInfo['referer'] == "天生一对加盟商"){
		            if(in_array($baseOrderInfo['order_pay_status'],array(2,3,4))){
		                $data = array('delivery_status'=>1);
		                $res = $salesModel->updateOrderDetail($data,"bc_id={$id} and delivery_status=2");
		                if(!$res){
		                    $error = "操作失败，事物回滚！提示：天生一对加盟商订单【{$bc_sn}】重置配货状态失败 error:".__LINE__;
		                    Util::rollbackExit($error,$pdolist);
		                }
		            }
		        }
		        
		        //获取订单商品明细下布产状态最小值
		        $orderDetails = $salesModel->getOrderDetailsByOrderId('id,buchan_status',$order_id);
		        $bc_status_list = array_column($orderDetails,'buchan_status',"id");	  
		        $bc_status_list[$goods_detail_id] = $bc_status;
		        $order_bc_status = min($bc_status_list);
		        if($order_bc_status==10 || $order_bc_status==11){
		            $order_bc_status=5;
		        }else if($order_bc_status==9){
		            $order_bc_status=4;
		        }else if(in_array($order_bc_status,array(4,5,6,7,8))){
		            $order_bc_status=3;
		        }else if(in_array($order_bc_status,array(1,2,3))){
		            $order_bc_status=2;
		        }
		        
		        //订单配货状态和订单布产状态更改
		        $data = array(
		            'buchan_status'=>$order_bc_status,//订单布产状态
		            'delivery_status'=>$delivery_status//配货状态
		        );
		        $res = $salesModel->updateBaseOrderInfo($data,"order_sn='{$order_sn}'");
		        if(!$res){
		            $error = "操作失败，事物回滚！提示：【{$bc_sn}】订单【{$order_sn}】配货状态重置修改失败 error:".__LINE__;
		            Util::rollbackExit($error,$pdolist);
		        }
		        
		        //订单商品列表布产状态修改
		        $data = array(
		            'buchan_status'=>$bc_status,
		        );
		        $res = $salesModel->updateOrderDetail($data,"bc_id={$id}");
		        if(!$res){
		            $error = "操作失败，事物回滚！提示：【{$bc_sn}】订单商品布产状态修改失败 error:".__LINE__;
		            Util::rollbackExit($error,$pdolist);
		        }		        	       
		        //订单日志写入
		        $data = array(
		            'order_id'=>$baseOrderInfo['id'],
		            'order_status'=>$baseOrderInfo['order_status'],
		            'shipping_status'=>1,//发货状态 【未发货】
		            'pay_status'=>$baseOrderInfo['order_pay_status'],
		            'create_user'=>Auth::$userName,
		            'create_time'=>date("Y-m-d H:i:s"),
		            'remark'=>$remark,
		        );
		        $res = $salesModel->insertOrderAction($data);
		        if(!$res){
		            $error = "操作失败，事物回滚！提示：【{$bc_sn}】订单日志写入失败 error:".__LINE__;
		            Util::rollbackExit($error,$pdolist);
		        }		        
		        
		    }
		    
		    /********************************数据处理 end**************************/
		}
    	try{	
            //批量提交事物
            foreach ($pdolist as $pdo){
                //$pdo->rollback();
                $pdo->commit();
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
            }
            $result['success'] = 1;
            Util::jsonExit($result);
            
        }catch (Exception $e){
            $error = "操作失败，事物回滚！";
            Util::rollbackExit($error,$pdolist);
        }      		
		
	}	
	
	public function edit_4c(){
	    
	    $id = _Request::getInt('id');
	    $result = array('success' => 0,'error' => '','title'=>'修改证书号');
	    
	    $model = new ProductInfo4CModel($id,13);
	    $productInfoModel = new ProductInfoModel(13);
	    $salesModel = new SalesModel(27);
	    
	    $data = $model->getDataObject();
	    if(empty($data)){
	        $result['content'] = "当前布产单不支持4C配钻！";
	        Util::jsonExit($result);
	    }
	    
	    $zhengshuhao_org = $data['zhengshuhao_org'];
	    $order_sn        = $data['order_sn'];
	    
	    $apiDiamondModel = new ApiDiamondModel();
	    $ret = $apiDiamondModel->getDiamondInfoByCertId($zhengshuhao_org);
	    if($ret['error']==1){
	        $result['content'] ='原证书号不存在';
	        Util::jsonExit($result);
	    }else{
	        $dataOrg = $ret['data'];
	    }
	    
	    $consignee = $productInfoModel->Select2("consignee","id={$id}","one");
	    if(empty($data['kt_order_detail_id']) && empty($data['kt_bc_sn'])){	    	    
    	    //根据证书号和收货人匹配空托
    	    $order_detail_list = $salesModel->getOrderDetailsFor4C($zhengshuhao_org);
    	    $data['kt_bc_sn'] = "无";
    	    if(!empty($order_detail_list)){
    	        foreach($order_detail_list as $vo){
    	            if($vo['consignee'] ==$consignee){
    	                $data['kt_bc_sn'] = $productInfoModel->get_bc_sn($vo['bc_id'], false);
    	            }
    	            if($order_sn ==$vo['order_sn']){
    	                //与裸钻订单号一样的空托，优先匹配
    	                break;
    	            }
    	        }
    	    }
	    }
	    
	    
        $goodsAttrModel = new GoodsAttributeModel(17);
	    $dataAttr['cut_arr']      = $goodsAttrModel->getCutList();//切工
	    $dataAttr['shape_arr']    = $goodsAttrModel->getShapeList();//形状
	    $result['content'] = $this->fetch('product_info_edit_4c.html',array(
	        'data'=>$data,
	        'dataOrg'=>$dataOrg,
	        'dataAttr'=>$dataAttr,
	        'consignee'=>$consignee//收货人
	    ));
	    Util::jsonExit($result);
	}	
	/*
	*修改出厂时间
	*
	*/
	public function alterTime(){

		//非授权用户不允许修改
		$id = _Request::getInt('id');
		$promodel = new ProductInfoModel($id,13);
		$status = $promodel->getValue('status');
		$status_pass = array(1,2,3,4,5,6,7);
		//已出厂之前才可以修改
		if(!in_array($status, $status_pass)){
			$result['title'] = '标准出厂时间只有在已出厂之前才允许修改!';
			Util::jsonExit($result);
		}
		$result = array('success' => 0,'error' => '');
                $tab_id = _Request::getInt("tab_id");
		$result['content'] = $this->fetch('altertime.html',array(
			'view'=>new ProductInfoView(new ProductInfoModel($id,13)),
   //        'tab_id'=>$tab_id
		));
		$result['title'] = '更新出厂时间';
		Util::jsonExit($result);
	}

	/*
	*保存出厂时间和修改原因
	*
	*/
	public function alterTimeSave(){

		$id = _Post::getInt('id');
		$reason = _Post::getString('info');
		$esmt_time= _Post::getString('esmttime');
		if($reason ==''){
			$result['error'] ='出厂时间原因必须填写!';
			Util::jsonExit($result);
		}
		if($esmt_time ==''){
			$result['error'] ='没有选择出厂时间';
			Util::jsonExit($result);
		}
		$promodel = new ProductInfoModel($id,14);
		$data = $promodel->getDataObject();

		//已出厂前才能修改
		if(!in_array($data['status'], array(1,2,3,4,5,6))){
			$result['error'] ='布产状态为已出厂不能修改出厂时间';
			Util::jsonExit($result);
		}
		$res1 = $promodel->updateEsmttime($id,$esmt_time,1);  //更新时间
		$logModel = new ProductOpraLogModel(14);
 		$res2 = $logModel->addLogNew($id,"布产单,原始时间：".$data['esmt_time'].",修改后时间：".$esmt_time.",修改原因：".$reason);
 		if($res1 && $res2){
 			$result['success'] =1;
 		}else{
 			$result['error'] ='出厂时间更新失败';
 		}
		Util::jsonExit($result);

	}


	/*
	*更加供应商ID更新出厂时间
	*
	*/
	public function updateEsmttimeById($id,$order_type=1,$update=true){

		$newmodel = new AppProcessorWorktimeModel(14);
		$productModel = new ProductInfoModel($id,14);
		$stylemodel = new StyleModel(11);
		$purchasemodel = new PurchaseModel(23);
		$proInfos = $productModel->getBuChanInfoById($id);
			//更新出厂时间:未出厂 && 出厂时间大于当前时间
			if(in_array($proInfos['status'], array('1','2','3','4','5','6'))){
				$infos = $newmodel->getProcessorInfoByProId($proInfos['prc_id'],$order_type);
				$qiban_exists = $purchasemodel->getQiBanInfosByStyle_Sn($proInfos['style_sn'],$proInfos['p_sn']);
				if($order_type ==1){
					//客订单
					/*
					if($proInfos['style_sn'] =='QIBAN' && $qiban_exists){
					//无款起版
						$cycle = $infos['wkqbzq'];
					
					}else{
						//成品:款式库存在,起版列表没有
						if($proInfos['style_sn'] !='QIBAN'){
							//起版列表信息
							$qiban_exists = $purchasemodel->getQiBanInfosByStyle_Sn($proInfos['style_sn'],$proInfos['p_sn']);
							if(empty($qiban_exists)){
								//成品(更新)
								$cycle = $infos['normal_day'];
							}else{
								//有款起版(更新)
								$cycle = $infos['ykqbzq'];
							}
						}	
					}
					*/
					if($proInfos['qiban_type']==0){
						$cycle = $infos['wkqbzq'];
					}elseif($proInfos['qiban_type']==1){
						$cycle = $infos['ykqbzq'];
					}else{
						$cycle = $infos['normal_day'];
					}
				}else{
					//备货单
					
					$is_style = $purchasemodel->getStyleInfoByCgd($proInfos['p_sn']);
					if($is_style ==1){
						//采购列表  --有款采购
						$cycle = $infos['ykqbzq'];
					}elseif($is_style ==0){
						//采购列表  --无款采购
						$cycle = $infos['wkqbzq'];
					}else{
						//采购列表  --标准采购
						$cycle = $infos['normal_day'];
					}
				}

				// $add_days = time() +intval($cycle)*3600*24;
				for($i=0;$i<=$cycle;$i++){
					$day = date('Y-m-d',strtotime('+'.$i.' day',time()));
						//放假日期
					if(strpos($infos['holiday_time'],$day) !== false){
							// $add_days +=3600*24; 
							++$cycle;
							continue;
						}
						//暂时只能获得周末休息天数(默认周天休息)
						switch ($infos['is_rest']) {
							case '1':
								break;
							case '2':
								//有周末就后延后一天
								if(date('w',strtotime($day))== 0){
									// $add_days +=3600*24;
									$cycle = $cycle+1;
								}
								break;
							default:
								if(date('w',strtotime($day))== 6 || date('w',strtotime($day))== 0){
									// $add_days +=3600*24;
									$cycle = $cycle+1;
								}
								break;
						}											
					//周末上班
					if(strpos($infos['is_work'],$day) !== false && strpos('60',date('w',strtotime($day))) !== false){
							// $add_days =$add_days-3600*24;
							--$cycle;				
						}
				
			
				}
                if($update==false){                    
                    return $day;
                }
					// $esmt_time =date('Y-m-d',$add_days);
					$res = $productModel->updateEsmttime($id,$day,1);
					if(!$res){
						return false;
					}
			}
		return true;
	}

    /**
     * 布产配石BOSS-1090
     * 布产单详情页增加按钮【配石】弹出框输入【配石货号】 【备注】布产单表kela_supplier.product_info 
     * 增加字段peishi_goods_id保存（校验输入的配石货号必须是商品列表里的货号）
     */
    public function the_stone($params)
    {
        $result = array('success' => 0,'error' => '');
        $id = intval($params["id"]);
        $tab_id = _Request::getInt("tab_id");
        $model = new ProductInfoModel($id,13);

        $result['content'] = $this->fetch('the_stone.html',array(
            'view'=>new ProductInfoView($model),
            'tab_id'=>$tab_id
        ));
        $result['title'] = '布产单配石';
        Util::jsonExit($result);
    }

    /**
     * 布产配石BOSS-1090
     * 布产单详情页增加按钮【配石】弹出框输入【配石货号】 【备注】布产单表kela_supplier.product_info 
     * 增加字段peishi_goods_id保存（校验输入的配石货号必须是商品列表里的货号）
     */
    public function upStoneGoods($params)
    {
        $result = array('success' => 0,'error' =>'');
        $id = intval($params["id"]);//布产单号
        $info = _Post::getString('info');
        $peishi_goods_id = _Post::getString('peishi_goods_id');//配石货号

        $model = new ProductInfoModel($id,13);
        //$do = $model->getDataObject();

        if($peishi_goods_id == '')
        {
            $result['error'] = "提示：配石货号不能为空！";
            Util::jsonExit($result);
        }

        if(empty(preg_replace('/\s/','',$info)))
        {
            $result['error'] = "提示：请填写备注信息！";
            Util::jsonExit($result);
        }

        $warehouseModel = new WarehouseModel(21);//仓库模型
        $checkWarehouseGoods = $warehouseModel->isExistsByGoodsId($peishi_goods_id);

        if(empty($checkWarehouseGoods))
        {
            $result['error'] = "提示：输入的配石货号不是仓储商品列表里的货号！";
            Util::jsonExit($result);
        }
        
        //保存配石货号
        $model->setValue('peishi_goods_id',$peishi_goods_id);
        $res = $model->save();

        if($res !== false){

            //添加订单操作日志
            $order_info = "配石货号：".$peishi_goods_id."/<font color=red>".$info."</font>";//字体标红
            $logModel = new ProductOpraLogModel(14);
            $logModel->addLog($id,$order_info);

            $result['success'] = 1;
            $result['title'] = '提示：配石添加成功！';
        }else{
            $result['error'] = '提示：配石添加失败！';
        }
        Util::jsonExit($result);
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
    
    
    
    function allConfirm($params){  
    	$id= $params['id'];    
    	
    	if(is_array($id)){
    		$idStr = implode(',', $id);
    		$this->updateAssign($idStr);//批量送钻
    	}else{
    		$this->updateAssign($id);		//单个布产送钻
    	}
    }
    function Confirm($params){
    	
       $id= $params['id'];    
    	
    	if(is_array($id)){
    		$idStr = implode(',', $id);
    		$this->updateAssign($idStr);//批量送钻
    	}else{
    		$this->updateAssign($id);		//单个布产送钻
    	}
    }
    
    function updateAssign($idStr){
    	$result = array('success' => 0,'error' => '');
    	$Productmodel=new ProductInfoModel(14);
    	$salesmodel = new SalesModel(28);
    	$pdo14=$Productmodel->db()->db();
    	$pdo28=$salesmodel->db()->db();
    	$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
    	$pdo14->beginTransaction(); //开启事务
    	$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
    	$pdo28->beginTransaction(); //开启事务
    	
    	$ids = explode(',', $idStr);
    	if(empty($ids)){
    		$result['error'] = "数据错误";
    		Util::jsonExit($result);
    	}
    	try{
    	foreach($ids as $key =>$id)
    	{
    		
    		//$model = new ProductInfoModel($id,13);	
    		$prductInfo=$Productmodel->getBuChanInfoById($id);
    		$status = $prductInfo['status'];
    		$bc_no=$prductInfo['bc_sn'];
    		$production_manager_name=$prductInfo['production_manager_name'];
    		if($status != 2){
    			$pdo28->rollback(); //事务回滚
    			$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo14->rollback(); //事务回滚
    			$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$result['error'] = "布产单".$bc_no."布产状态不是待分配状态，请先筛选布产单状态!".$status;
    			Util::jsonExit($result);
    		}
    		
    		if($production_manager_name != $_SESSION['userName'] && $_SESSION['userName'] != 'admin'){
    			$pdo28->rollback(); //事务回滚
    			$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo14->rollback(); //事务回滚
    			$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$result['error'] = "布产单".$bc_no."生产经理不是本人，没有权限进行确认分配，请根据生产经理筛选!";
    			Util::jsonExit($result);
    		}
    		
    		
    		$res=$Productmodel->updateStatus($id, 3);
    		if(!$res){
    			$pdo28->rollback(); //事务回滚
    			$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo14->rollback(); //事务回滚
    			$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$result['error'] = "布产单".$bc_no."确认分配失败!";
    			Util::jsonExit($result);
    		}
    		
    		$res=$Productmodel->addBcGoodsLog($id, "布产单".$bc_no."确认分配");
    		if(!$res){
    			$pdo28->rollback(); //事务回滚
    			$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo14->rollback(); //事务回滚
    			$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$result['error'] = "布产单".$bc_no."生成日志失败!";
    			Util::jsonExit($result);
    		}
    		$from_type=$prductInfo['from_type'];
    		if($from_type==2){
    			$p_id=$prductInfo['p_id'];
    		    $res=$salesmodel->UpdateOrderDetailStatus($p_id,3);
    		    if(!$res){
    		    	$pdo28->rollback(); //事务回滚
    		    	$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    		    	$pdo14->rollback(); //事务回滚
    		    	$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    		    	$result['error'] = "布产单".$bc_no."更新订单明细布产状态失败!";
    		    	Util::jsonExit($result);
    		    }
    		}
    		
    	}
    
    	$pdo28->commit(); //事务提交
    	$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    	$pdo14->commit(); //事务回滚
    	$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    	$result['success'] = 0;
    	$result['error'] = "确认分配成功";
    	Util::jsonExit($result);
    }catch (Exception $e){	
    			$pdo28->rollback(); //事务回滚
    			$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo14->rollback(); //事务回滚
    			$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$result['error'] ="系统异常！error code:".$e;
    			Util::jsonExit($result);
    }	
    	
    }
    
    
   //生产出厂
    public function to_shipment ($params)
    {
    
    	$id = intval($params["id"]);//布产单ID
    	$result = array('success' => 0,'error' => '','title' => '生产出厂');
    	#循环判断 布产单只有生产中和部分出厂两个状态下才能进行OQC质检

    		$model = new ProductInfoModel($id,14);
    		$status = $model->getValue('status');
    		$from_type = $model->getValue('from_type');
    		$buchan_fac_opra = $model->getValue('buchan_fac_opra');
    		if($status != 4 && $status != 7)//布产单只有生产中和部分出厂两个状态下才能进行出货
    		{
    		    $bc_sn = $model->get_bc_sn($id);
    			$result['content'] = "布产单".$bc_sn."状态不对。提示：只有布产状态为【生产中】或【部分出厂】才允许出厂";
    			Util::jsonExit($result);
    		}
    		/*
    		if(SYS_SCOPE=='zhanting' && $buchan_fac_opra != 4){
    			//oqc质检未过
    		    $bc_sn = $model->get_bc_sn($id);
    			$result['content'] = "布产单".$bc_sn." OQC必须质检通过，才允许出厂。";
    			Util::jsonExit($result);
    		}*/
    
    	
    
    	/** 获取顶级导航列表**/
    	$newmodel = new ProductFqcConfModel(13);
    	$top_menu = $newmodel->get_top_menu();
    	//var_dump($id_s);exit;
    	/* 		$this->render('product_shipment_info_pl.html',array(
    	 //'dd' => new DictView(new DictModel(1)),
    	 'id_s' => join(",",$ids),
    	 'from_type'=>$from_type,
    	 'top_menu'=>$top_menu,
    	 'title'=>'工厂出货'
    	));  */
    
    	$result['content'] = $this->fetch('product_shipment_info.html',array(
    			'id' => $id,
    			'from_type'=>$from_type,
    			'top_menu'=>$top_menu,
    	));
    	Util::jsonExit($result);
    }
    /*
     * 修改快速定制
     */
    public function editQuickDiy($params)
    {
        $result['title'] = "修改快速定制";
        
        $id = _Request::getInt('id');
        $model = new ProductInfoModel($id,13);
        $view = new ProductInfoView($model);

        if(!$view->get_id()){
            $result['content'] = "修改对象不存在!";
            Util::jsonExit($result);
        }
        
        $result['content'] = $this->fetch('quick_diy_edit.html',array(
            'view' => $view,
        ));
        Util::jsonExit($result);
    }
    /*
     * 修改保存快速定制
     */
    public function updateQuickDiy($params)
    {
        $result = array('success'=>0,'error'=>'');        
        $id = _Request::getInt('id');
        $model = new ProductInfoModel($id,13);
        $logModel = new ProductOpraLogModel(14);
        $olddo = $model->getDataObject();                
        if(empty($olddo)){
            $result['error'] = "修改对象不存在!";
            Util::jsonExit($result);
        }
        $newdo = array(
            'id' => $id,
            'is_quick_diy'=>_Post::getInt('is_quick_diy'),                        
        ); 
        $remark = _Post::getString('remark');
        if($olddo['is_quick_diy']==$newdo['is_quick_diy']){
            $result['error'] = "当前请求修改的快速定制状态与原始状态一样!";
            Util::jsonExit($result);
        }
        if($remark==""){
            $result['error'] = "修改原因不能为空!";
            Util::jsonExit($result);
        }
        $res = $model->saveData($newdo, $olddo);
        if($res !==false){
            $old_quick_diy = $olddo['is_quick_diy']==1?"是":"否";
            $new_quick_diy = $newdo['is_quick_diy']==1?"是":"否";
            $remark = "更改是否快速定制,由【{$old_quick_diy}】更改为【{$new_quick_diy}】,更改原因:".$remark;
            $res2 = $logModel->addLogNew($id,$remark);            
            $result['success'] = 1;
        }else{
            $result['error'] = "修改失败!";
        }        
        Util::jsonExit($result);
    }
    
    /**
     * 组合镶嵌默认页弹窗
     * @param unknown $params
     */
    public function combineXQ($params){
        $result = array('title'=>'现货组合镶嵌','content'=>'');
        
        $bc_id = _Request::getInt('id');
        $goods_id = _Request::getString('goods_id');
        if(empty($goods_id)){
            $result['content']= "空托货号为空,请重新选择商品。";
            Util::jsonExit($result);
        }
        $model = new ProductInfoModel($bc_id,13);
        $attrModel = new ProductInfoAttrModel(13);
        $procInfoModel = new AppProcessorInfoModel(13);
        
        $xiangqian = $model->getValue('xiangqian');   
        $is_peishi=0;
                
        $where="g_id ={$bc_id} and (code='cart' or code='diamond_size' or code='zuanshidaxiao')";
        $cart = $attrModel->select2('value' , $where ,'one');
        if($xiangqian=="工厂配钻，工厂镶嵌"||$xiangqian=="客户先看钻再返厂镶嵌"||$xiangqian=="需工厂镶嵌"||$xiangqian=="镶嵌4C裸钻")
        {
            if(($cart!='' && $cart!='0' && $cart!='空' && $cart!='无'))
            {
                $is_peishi=1;
            }
        }
        
        $factory_list = $procInfoModel->getProList();
        
        $result['content'] = $this->fetch('combinexq_add.html',array(
            'id' => $bc_id,
            'is_peishi'=>$is_peishi,
            'goods_id' =>$goods_id,
            'factory_list'=>$factory_list,            
        ));
        
        Util::jsonExit($result);
    }
    /**
     * 组合镶嵌保存
     * @param unknown $params
     */
    public function combineXQSave($params){
        $result = array('error'=>'','success'=>0);
        
        $bc_id = _Request::getInt('id');
        $goods_id = _Request::getString('goods_id');
        $factory = _Request::getString('factory');
        if(empty($goods_id)){
            $result['error']='请选择商品！';
            Util::jsonExit($result);
        }
        if(empty($factory)){
            $result['error']='请选择工厂！';
            Util::jsonExit($result);
        }else{
            $factoryArr = explode('|',$factory);
            if(count($factoryArr)<>4){
                $result['error']='工厂信息参数错误！';
                Util::jsonExit($result);
            }
        }
        $factory_id = $factoryArr[0]; //工厂ID
        $factory_name = $factoryArr[1];//工厂名称
        $gendan = $factoryArr[2];//跟单人
        $production_manager_name = $factoryArr[3];//生产经理人
        //仓库数据库
        $warehouseModel = new WarehouseModel(22);
        $model = new ProductInfoModel($bc_id,14);
        $attrModel = new ProductInfoAttrModel(14);
        $procInfoModel = new AppProcessorInfoModel(14);
        $opraLogModel = new ProductOpraLogModel(14);
        $salesModel = new SalesModel(27);
        $peishiListModel = new PeishiListModel(14);
        
        $from_type = $model->getValue('from_type');//布产单类型 1采购 2订单
        $order_sn = $model->getValue('p_sn');//订单号
        $detail_id = $model->getValue('p_id');//订单商品记录ID
        $bc_sn = $model->getValue('bc_sn');//布产号
        $from_type = $model->getValue('from_type');//布产号
        if($from_type==2 && $order_sn==""){
            $result['error'] = "数据异常，布产单关联的客订单号为空！";
            Util::jsonExit($result);
        }                
        $goods_info = $warehouseModel->selectWarehouseGoods('*',"goods_id='{$goods_id}'");
        if(empty($goods_info)){
            $result['error'] = "货号{$goods_id}不存在！";
            Util::jsonExit($result);
        }else if($goods_info['is_on_sale']<>2){
            $result['error'] = "货号{$goods_id}不是库存状态！";
            Util::jsonExit($result);
        }else if(!empty($goods_info['order_goods_id'])){
            $result['error'] = "货号{$goods_id}已绑定过订单！";
            Util::jsonExit($result);
        }

        $pdolist[14] = $model->db()->db();
        $pdolist[22] = $warehouseModel->db()->db();
        $pdolist[27] = $salesModel->db()->db();
        try{
            foreach ($pdolist as $pdo){
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
                $pdo->beginTransaction(); //开启事务
            }
        }catch (Exception $e){
            $error = "操作失败，事物回滚！提示：系统批量开启事物时发生异常！";
            Util::rollbackExit($error,$pdolist);
        }
        /*
                         布产状态：不需布产11，已取消10，已出厂9，作废8，部分出厂7，质检完成6，质检中5，生产中4，已分配3，待分配2,初始化1
         * */
        $nowtime = date('Y-m-d H:i:s');
        try{
            
            $tip = "更新布产单信息";
            $model->setValue('is_combine',1);//是否组合镶嵌
            $model->setValue('combine_goods_id',$goods_id);//组合镶嵌现货托
            $model->setValue('status',4);//布产状态更新为生产中4
            $model->setValue('buchan_fac_opra',2);//开始生产2
            $model->setValue('to_factory_time',$nowtime);//分配工厂时间
            $model->setValue('order_time',$nowtime);//工厂接单时间
            $model->setValue('esmt_time','0000-00-00');//标准出厂时间    
            $model->setValue('prc_id',$factory_id);//工厂ID
            $model->setValue('prc_name',$factory_name);//工厂名称
            $model->setValue('opra_uname',$gendan);//跟单人
            $model->setValue('production_manager_name',$production_manager_name);//经理人
            
            $res = $model->save();             

            $tip = "添加布产日志";
            $remark = "组合镶嵌绑定现货托，货号：{$goods_id}，分配工厂：{$factory_id}";//factory_name
            $opraLogModel->addLog($bc_id,$remark);
            
            if($from_type==2 && $order_sn<>""){
                
                
                $tip = "更新订单布产状态";
                $data = array('buchan_status'=>3);//订单布产状态更新为 生产中
                $salesModel->updateBaseOrderInfo($data,"order_sn='{$order_sn}'");
                
                $tip = "更新订单明细布产状态"; 
                $data = array('buchan_status'=>4);//订单明细 布产状态更新为 生产中4
                $salesModel->updateOrderDetail($data,"id={$detail_id}");
                
                $tip = "货品{$goods_id}绑定订单";
                $data = array('order_goods_id'=>$detail_id);
                $warehouseModel->updateWarehouseGoods($data,"goods_id='{$goods_id}'");                 
                
                $tip = "添加订单日志";
                $remark = "布产单【{$bc_sn}】组合镶嵌绑定现货托，货号：{$goods_id}";
                $salesModel->AddOrderLog($order_sn,$remark);
                
            }

            $tip = "生成配石单";
            $res = $peishiListModel->createPeishiList($bc_id,'insert',"现货组合镶嵌");
            if($res['success']==0){
                $msg = "生成配石单失败!".$res['error'];
                Util::rollbackExit($msg,$pdolist);
            }
            
            
            $tip = "批量提交事物";
            foreach ($pdolist as $pdo){
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
                $pdo->commit(); //开启事务
            }
            $this->updateEsmttimeById($bc_id,$from_type);
            $result['success'] = 1;
            Util::jsonExit($result);
        }catch (Exception $e){
            $msg = "保存失败，".$tip."失败！请重新尝试，仍未解决，请联系技术人员处理！";
            Util::rollbackExit($msg,$pdolist);
        }

    }
    
    /**
     * 组合镶嵌编辑
     * @param unknown $params
     */
    public function editCombineXQ($params){
        
        $result = array('title'=>'修改现货组合镶嵌','content'=>'');
        
        $bc_id = _Request::getInt('id');
        $goods_id = _Request::getString('goods_id');
        if(empty($bc_id)){
            $result['content']= "参数错误, id 为空!";
            Util::jsonExit($result);
        }
        $model = new ProductInfoModel($bc_id,13);
        $view = new ProductInfoView($model);
        $buchan_status = $view->get_status();
        /*
                        布产状态：不需布产11，已取消10，已出厂9，作废8，部分出厂7，质检完成6，质检中5，生产中4，已分配3，待分配2,初始化1
         * */
        if(in_array($buchan_status,array(1,11,7,9))){
            $result['content']= "布产状态为 【初始化】、【不需布产】、【部分出厂】、【已出厂】  的布产单 不允许操作。";
            Util::jsonExit($result);
        }
        $procInfoModel = new AppProcessorInfoModel(13);        
        $factory_list = $procInfoModel->getProList();
        
        $result['content'] = $this->fetch('combinexq_edit.html',array(
            'id' => $bc_id,
            'view'=>$view,
            'factory_list'=>$factory_list,
        ));
        
        Util::jsonExit($result);
    }
    /**
     * 组合镶嵌编辑保存
     * @param unknown $params
     */
    public function updateCombineXQ($params){
        $result = array('error'=>'','success'=>0);
        /*
                               布产状态：不需布产11，已取消10，已出厂9，作废8，部分出厂7，质检完成6，质检中5，生产中4，已分配3，待分配2,初始化1
         * */
        $bc_id = _Request::getInt('id');
        $is_combine = _Request::getInt('is_combine');
        $factory = _Request::getString('factory');
        $combine_goods_id = _Request::getString('goods_id');
        $remark = _Request::getString('remark');
        if(empty($bc_id)){
            $result['error']='id 为空！';
            Util::jsonExit($result);
        }
        if(trim($remark)==""){
            $result['error']='修改原因不能为空！';
            Util::jsonExit($result);
        }        
        $model = new ProductInfoModel($bc_id,14);
        $warehouseModel = new WarehouseModel(22);
        $attrModel = new ProductInfoAttrModel(14);
        $procInfoModel = new AppProcessorInfoModel(14);
        $opraLogModel = new ProductOpraLogModel(14);
        $salesModel = new SalesModel(27);
        $olddo = $model->getDataObject();
        

        if($olddo['from_type']==2 && $olddo['p_sn']==""){
            $result['error'] = "数据异常，布产单关联的客订单号为空！";
            Util::jsonExit($result);
        }
        $order_sn = $olddo['p_sn'];  
        $detail_id = $olddo['p_id'];
        $bc_sn = $olddo['bc_sn'];
        $from_type = $olddo['from_type'];
        
        $bc_log_str = "修改组合镶嵌:";
        $newdo = array('id'=>$olddo['id']);//组装布产单信息修改数据 $newdo
        $baseOrderInfoData = array();//订单信息修改数据
        $appOrderDetailData = array();//订单明细修改数据
        if($is_combine==1){
            if(empty($factory)){
                $result['error']='请选择工厂！';
                Util::jsonExit($result);
            }else{
                $factoryArr = explode('|',$factory);
                if(count($factoryArr)<>4){
                    $result['error']='工厂信息参数错误！';
                    Util::jsonExit($result);
                }
            }
            if(empty($combine_goods_id)){
                $result['error']='现货托不能为空！';
                Util::jsonExit($result);
            }
            $factory_id = $factoryArr[0]; //工厂ID
            $factory_name = $factoryArr[1];//工厂名称
            $gendan = $factoryArr[2];//跟单人
            $production_manager_name = $factoryArr[3];//生产经理人
            $nowtime = date('Y-m-d H:i:s');
           
            
            if($is_combine <> $olddo['is_combine']){
                $is_combine_old = $olddo['is_combine']==1?'是':'否';
                $is_combine_new = $is_combine==1?'是':'否';                
                $newdo['is_combine'] = $is_combine;
                $bc_log_str .= "是否组合镶嵌由【{$is_combine_old}】改为【{$is_combine_new}】<br/>";
            }
            if($combine_goods_id <> $olddo['combine_goods_id']){
                $goods_info = $warehouseModel->selectWarehouseGoods('*',"goods_id='{$combine_goods_id}'");
                if(empty($goods_info)){
                    $result['error'] = "货号{$combine_goods_id}不存在！";
                    Util::jsonExit($result);
                }else if($goods_info['is_on_sale']<>2){
                    $result['error'] = "货号{$combine_goods_id}不是库存状态！";
                    Util::jsonExit($result);
                }else if(!empty($goods_info['order_goods_id'])){
                    $result['error'] = "货号{$combine_goods_id}已绑定过订单！";
                    Util::jsonExit($result);
                }else if($goods_info['goods_sn']<>$olddo['style_sn']){
                    $result['error'] = "货号{$combine_goods_id}不符合要求，不是同款的货品！请输入款号为{$olddo['style_sn']}的货号";
                    Util::jsonExit($result);
                }
                $newdo['combine_goods_id'] = $combine_goods_id;
                $bc_log_str .= "现货托由【{$olddo['combine_goods_id']}】改为【{$combine_goods_id}】<br/>";
            }
            if($factory_name<>$olddo['prc_name']){
                $newdo['prc_id'] = $factory_id;
                $newdo['prc_name'] = $factory_name;                
                $bc_log_str .= "分配工厂由【{$olddo['prc_name']}】改为【{$factory_name}】<br/>";                
            }
            if($gendan <>$olddo['opra_uname']){
                $newdo['opra_uname'] = $gendan;
                $bc_log_str .= "跟单人由【{$olddo['opra_uname']}】改为【{$gendan}】<br/>";
            }
            if($production_manager_name<>$olddo['production_manager_name']){
                $newdo['production_manager_name'] = $production_manager_name;
                $bc_log_str .= "生产经理人由【{$olddo['production_manager_name']}】改为【{$production_manager_name}】<br/>";
            }
            if($olddo['status'] <> 4){
                $newdo['status'] = 4;
                $baseOrderInfoData['buchan_status'] = 3;
                $appOrderDetailData['buchan_status'] = 4;
                $buchan_status_old = $this->dd->getEnum('buchan_status',$olddo['status']);
                $bc_log_str .= "布产状态由【{$buchan_status_old}】改为【生产中】<br/>";
            }
            if($olddo['buchan_fac_opra'] <>2 && $olddo['is_combine']==0){
                $newdo['buchan_fac_opra'] = 2;
                $buchan_fac_opra_old = $this->dd->getEnum('buchan_fac_opra',$olddo['buchan_fac_opra']);
                $bc_log_str .= "生产状态由【{$buchan_fac_opra_old}】改为【开始生产】<br/>";
            } 
            if($factory_name<>$olddo['prc_name'] || $olddo['is_combine']==0){
                $newdo['to_factory_time'] = $nowtime;
                $newdo['order_time'] = $nowtime;
                $newdo['esmt_time'] = $nowtime;
            } 

        }else{
            $factory_id = 0; //工厂ID
            $factory_name = "";//工厂名称
            $gendan = "";//跟单人
            $production_manager_name='';
            if($olddo['is_combine']==0){
                $result['success'] = 1;
                Util::jsonExit($result);
            }
            if($is_combine <> $olddo['is_combine']){
                $is_combine_old = $olddo['is_combine']==1?'是':'否';
                $is_combine_new = $is_combine==1?'是':'否';
                $newdo['is_combine'] = $is_combine;
                $newdo['combine_goods_id'] = '';
                $bc_log_str .= "是否组合镶嵌由【{$is_combine_old}】改为【{$is_combine_new}】<br/>";
            }
            if($factory_name<>$olddo['prc_name']){
                $newdo['prc_id'] = $factory_id;
                $newdo['prc_name'] = $factory_name;
                $bc_log_str .= "分配工厂由【{$olddo['prc_name']}】改为【{$factory_name}】<br/>";
            }
            if($gendan <>$olddo['opra_uname']){
                $newdo['opra_uname'] = $gendan;
                $bc_log_str .= "跟单人由【{$olddo['prc_name']}】改为【{$gendan}】<br/>";
            }
            if($production_manager_name <>$olddo['production_manager_name']){
                $newdo['production_manager_name'] = $production_manager_name;
                $bc_log_str .= "生产经理人由【{$olddo['production_manager_name']}】改为【{$production_manager_name}】<br/>";
            }
            if($olddo['status']<>1){
                $newdo['status'] = 1;
                $newdo['to_factory_time'] = '000-00-00 00:00:00';
                $newdo['order_time'] = '000-00-00 00:00:00';
                $newdo['esmt_time'] = '000-00-00 00:00:00';
                $baseOrderInfoData['buchan_status'] = 1;
                $appOrderDetailData['buchan_status'] = 1;
                $buchan_status_old = $this->dd->getEnum('buchan_status',$olddo['status']);
                $bc_log_str .= "布产状态由【{$buchan_status_old}】改为【初始化】<br/>";
            }
            if($olddo['buchan_fac_opra']<>1){
                 $newdo['buchan_fac_opra'] = 1;
                 $buchan_fac_opra_old = $this->dd->getEnum('buchan_fac_opra',$olddo['buchan_fac_opra']);
                 $bc_log_str .= "生产状态由【{$buchan_fac_opra_old}】改为【未操作】<br/>";
            } 
            
        }
        //开启事物
        $pdolist[14] = $model->db()->db();
        $pdolist[22] = $warehouseModel->db()->db();
        $pdolist[27] = $salesModel->db()->db();
        try{
            foreach ($pdolist as $pdo){
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
                $pdo->beginTransaction(); //开启事务
            }
        }catch (Exception $e){
            $error = "操作失败，事物回滚！提示：系统批量开启事物时发生异常！";
            Util::rollbackExit($error,$pdolist);
        }
        try{
            //print_r($newdo);exit;
            $tip = "更新布产单信息";                      
            $res = $model->saveData($newdo, $olddo);
            $tip = "添加布产日志";
            $bc_log_str = $bc_log_str." 修改原因:".$remark;
            $opraLogModel->addLog($bc_id,$bc_log_str);
        
            if($olddo['from_type']==2 && $olddo['p_sn']<>""){        
                if(!empty($baseOrderInfoData)){
                   $tip = "更新订单布产状态";
                   $salesModel->updateBaseOrderInfo($baseOrderInfoData,"order_sn='{$order_sn}'");
                }
                if(!empty($appOrderDetailData)){
                    $tip = "更新订单明细布产状态";
                    $salesModel->updateOrderDetail($appOrderDetailData,"id={$detail_id}");
                }
                                
                if($olddo['combine_goods_id']<>$combine_goods_id){
                    if($olddo['combine_goods_id']<>''){
                        $tip = "货号{$olddo['combine_goods_id']}解绑订单";
                        $data = array('order_goods_id'=>'0');
                        $warehouseModel->updateWarehouseGoods($data,"goods_id='{$olddo['combine_goods_id']}'");
                        
                        $tip = "添加订单日志时,货号解绑日志写入";
                        $remark = "布产单【{$bc_sn}】修改组合镶嵌：解绑货号：{$olddo['combine_goods_id']}";
                        if($combine_goods_id<>''){
                            $tip = "货号{$combine_goods_id}绑定订单";
                            $data = array('order_goods_id'=>$olddo['p_id']);
                            $warehouseModel->updateWarehouseGoods($data,"goods_id='{$combine_goods_id}'");
                            
                            $remark.="; 重新绑定现货托，货号{$combine_goods_id}";
                        }
                        $salesModel->AddOrderLog($order_sn,$remark);
                    }else if($combine_goods_id<>''){
                        $tip = "货号{$combine_goods_id}绑定订单";
                        $data = array('order_goods_id'=>$olddo['p_id']);
                        $warehouseModel->updateWarehouseGoods($data,"goods_id='{$combine_goods_id}'");
                        
                        $tip = "添加订单日志";
                        $remark = "布产单【{$bc_sn}】修改组合镶嵌：绑定现货托，货号：{$combine_goods_id}";
                        $salesModel->AddOrderLog($order_sn,$remark);
                    }
                    
                }
        
            }
            $tip = "批量提交事物";
            foreach ($pdolist as $pdo){
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
                $pdo->commit(); //开启事务
            }
            if(isset($newdo['esmt_time']) && $newdo['esmt_time'] <> '000-00-00 00:00:00'){
                $this->updateEsmttimeById($bc_id,$from_type);
            }
            $result['success'] = 1;
            Util::jsonExit($result);
        }catch (Exception $e){
            $msg = "保存失败，".$tip."失败！请重新尝试，仍未解决，请联系技术人员处理！";
            Util::rollbackExit($msg,$pdolist);
        }
    }
    //组合镶嵌标签打印
    public function combineXQPrint(){        
        $id = _Request::getInt('id');
        if(empty($id)){
            exit('id 为空');
        }        
        $model = new ProductInfoModel($id,14);
        $attrModel = new ProductInfoAttrModel(14);        
        $data = $model->getDataObject();
        $arrlist = $attrModel->getGoodsAttr($id);
        foreach ($arrlist as $vo){
           $data[$vo['code']] = $vo['value'];
        }
        $this->render('combinexq_print.html',array(
            'data'=>$data,
        ));
        
    }
    /**
     * 查询款式库中的副石信息
     * @param unknown $params
     */
    public function getStyleFushi($params){
        
        $result = array('success'=>0,'error'=>'','data'=>array());        
        $bc_id  = _Request::get('bc_id');
        $style_sn = _Request::get('style_sn');
        $attrModel = new ProductInfoAttrModel(14);
        $zhiquan = '';
        $carat = '';
        $xiangkou = '';
        $attrlist = $attrModel->getGoodsAttr($bc_id);
        foreach ($attrlist as $vo){
            if($vo['code']=="zhiquan"){
                $zhiquan = $vo['value'];
            }else if($vo['code']=="cart"){
                $carat = $vo['value'];
            }else if($vo['code']=="xiangkou"){
                $xiangkou = $vo['value'];
            }
        }
        if($carat> 0 && $carat!==''){
            $xiangkou = $carat;
        }
        $styleModel = new StyleModel(11);
        if($xiangkou<>'' && $xiangkou>=0){    
            $fushiInfo = $styleModel->getStyleFushi($style_sn, $xiangkou, $zhiquan); 
        }
        //$fushiInfo = $styleModel->getStyleFushi('KLRW032684',0.005,7);
        if(!empty($fushiInfo)){ 
            $result['success'] = 1;
            $result['data'] = $fushiInfo;
        }
        Util::jsonExit($result);
    }

	/**
	 * 获取布产入库的入库单号和货品数量
	 */
	public function getInWarehouseInfo(){
		$result = array('success'=>0,'error'=>'','data'=>array());
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'buchan_sn' => _Request::get('buchan_sn'),
		);
		$warehouseModel = new WarehouseModel(22);
		if(!empty($args['buchan_sn'])){
			$data = $warehouseModel->getWarehouseNumByid(addslashes($args['buchan_sn']));
		}
		if(isset($data) && !empty($data)){
			$result['success'] = 1;
			$result['data'] = $data;
		}
		Util::jsonExit($result);
	}

    private function getEunm($dic,$key){
                $return = '';
                if(empty($key))
                      return '';
                foreach ($dic as $val )
                {
                        if($val['name']==$key)
                        {
                                $return = $val['label'];
                                break;
                        }
                }
                return $return;
    }

}?>