<?php
/**
 *  -------------------------------------------------
 *   @file		: TsydJxsDeliveryController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2016-01-28 17:56:56
 *   @update	:
 *  -------------------------------------------------
 */
class TsydJxsDeliveryController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $whitelist = array('printBills','print_express');
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('tsyd_jxs_delivery_search_form.html',array('bar'=>Auth::getBar()));
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
			'to_customer_id' => _Request::getstring("to_customer_id"),
            'bill_no' => _Request::getstring("bill_no"),
		    'create_user' => _Request::getstring("create_user"),
            'create_time_start' => _Request::getstring("create_time_start"),
            'create_time_end' => _Request::getstring("create_time_end"),		    
		    'is_print'=>_Request::getString('is_print'),
		    'print_user'=>_Request::getstring('print_user'),
		    'print_time_start'=>_Request::getstring('print_time_start'),
		    'print_time_end'=>_Request::getstring('print_time_end'),
		    'sort_wholesale'=>_Request::getstring('sort_wholesale'),
		    'sort_create_time'=>_Request::getstring('sort_create_time')
		);
		$bill_no_split = explode("\n",$args['bill_no']);		
		//获取最终合法订单编号
		$bill_no_list = array();
		foreach($bill_no_split as $vo){
		    //获取不为空和不重复的订单id
		    if(trim($vo)!='' && !in_array($vo,$bill_no_list)){
		        $bill_no_list[] = trim($vo);
		    }
		}
		$page = _Request::getInt("page",1);
		$where = array(

            'to_customer_id' => $args['to_customer_id'],
            'bill_no' => $bill_no_list,
		    'create_user'=>$args['create_user'],
            'create_time_start' => $args['create_time_start'],
            'create_time_end' => $args['create_time_end'],
		    'is_print'=>$args['is_print'],
		    'print_user'=>$args['print_user'],
		    'print_time_start'=>$args['print_time_start'],
		    'print_time_end'=>$args['print_time_end'],
		    'sort_wholesale'=>$args['sort_wholesale'],
		    'sort_create_time'=>$args['sort_create_time']
            );

		$model = new WarehouseModel(21);
		$data = $model->getTsydJxsAllBill_on($where,$page,10,false);
        //echo '<pre>';
        //print_r($data);die;
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'tsyd_jxs_delivery_search_page';
		$this->render('tsyd_jxs_delivery_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('tsyd_jxs_delivery_info.html',array(
			'view'=>new TsydJxsDeliveryView(new TsydJxsDeliveryModel(21))
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
		$result['content'] = $this->fetch('tsyd_jxs_delivery_info.html',array(
			'view'=>new TsydJxsDeliveryView(new TsydJxsDeliveryModel($id,21)),
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
		$this->render('tsyd_jxs_delivery_show.html',array(
			'view'=>new TsydJxsDeliveryView(new TsydJxsDeliveryModel($id,21)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');

        $ids = _Request::getList('ids');
        $express_id = _Request::getInt('express_id');
        $freight_no = _Request::getString('freight_no');

        if(empty($ids))
        {
            $result['error'] = "提示：请选择至少一个批发单！";
            Util::jsonExit($result);
        }
        if(empty($express_id))
        {
            $result['error'] = "提示：快递公司不能为空！";
            Util::jsonExit($result);
        }
        if(empty($freight_no))
        {
            $result['error'] = "提示：快递单号不能为空！";
            Util::jsonExit($result);
        }

        //取得收货信息；
        $modelWarehouse = new WarehouseModel(22);
        $salesModel = new SalesModel(28);
        $newmodel =  new ShipFreightModel(56);
        $pdo28 = $salesModel->db()->db();
        $pdo56 = $newmodel->db()->db();
        $pdo22 = $modelWarehouse->db()->db();
        $pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
        $pdo28->beginTransaction(); //开启事务
        $pdo56->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
        $pdo56->beginTransaction(); //开启事务
         
        $pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
        $pdo22->beginTransaction(); //开启事务
        try{
        $order_detail_All = $modelWarehouse->selectWarehouseBillGoods("DISTINCT `order_sn`,detail_id,goods_id,goods_name", "`bill_id` in(".implode(",", $ids).")");
        $diss_info = array();//取出其中一个订单号的发货信息；
        $detail_id_str = '';
        if(!empty($order_detail_All)){
        	$order_snAll = array_column($order_detail_All, 'order_sn');
            foreach ($order_detail_All as $k=>$row) {
                # code...
                if($row['detail_id']&&$row['order_sn']){
                    $detail_id_str.= $row['detail_id'].",";
                    if(empty($diss_info)){

                       $diss_info = $salesModel->getDissInfoOrder_sn($row['order_sn']);
                    }
                }else{
                	$pdo28->rollback(); //事务回滚
                	$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                	$pdo56->rollback(); //事务回滚
                	$pdo56->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                	$pdo22->rollback(); //事务回滚
                	$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                	$result['error'] = "提示：批发单中的订单号或者订单明细id为空！";
                	Util::jsonExit($result);
                } 
                
                
              $res3= $salesModel->updateOrderInfos($row,$express_id,$freight_no); 
             /*  $pdo28->rollback(); //事务回滚
              $pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
              $pdo56->rollback(); //事务回滚
              $pdo56->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
              $pdo22->rollback(); //事务回滚
              $pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
              $result['error'] = '更新订单日志和快递公司失败'.$res3;
              Util::jsonExit($result); */
              if(!$res3){
              	$pdo28->rollback(); //事务回滚
              	$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
              	$pdo56->rollback(); //事务回滚
              	$pdo56->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
              	$pdo22->rollback(); //事务回滚
              	$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
              	$result['error'] = '更新订单日志和快递公司失败';
              	Util::jsonExit($result);
              }
            }
        }else{
        	$pdo28->rollback(); //事务回滚
        	$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
        	$pdo56->rollback(); //事务回滚
        	$pdo56->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
        	$pdo22->rollback(); //事务回滚
        	$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
        	$result['error'] = "提示：批发单明细错误！";
        	Util::jsonExit($result);
        }

        if(empty($diss_info)){
        	$pdo28->rollback(); //事务回滚
        	$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
        	$pdo56->rollback(); //事务回滚
        	$pdo56->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
        	$pdo22->rollback(); //事务回滚
        	$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
            $result['error'] = "提示：批发单中的订单未找到发货地址信息！";
            Util::jsonExit($result);
        }

         //取得单据号
        $bill_noInfo = $modelWarehouse->selectWarehouseBill("`bill_no`", "`id` in(".implode(",", $ids).")");
        $bill_str = implode(array_column($bill_noInfo, 'bill_no'), ",");

		$olddo = array();
		$newdo=array(
            'order_no' => $bill_str,
            'freight_no' => $freight_no,
            'express_id' => $express_id,
            'remark' => '经销商发货',
            'is_print' => 2,
            'consignee' => $diss_info['consignee'],
            'cons_address' => $diss_info['address'],
            'cons_mobile' => $diss_info['mobile'],
            'cons_tel' => $diss_info['tel'],
            'order_mount' => 1,
            'print_date' => date('Y-m-d H:i:s'),
            'sender' => $_SESSION['userName'],
            'department' => 111,
            'note' => '',
            'create_id' => $_SESSION['userId'],
            'create_name' => $_SESSION['userName'],
            'create_time' => time(),
            'channel_id' => $diss_info['department_id'],
            'out_order_id' => 111,
			'is_tsyd'=>1,	
				
            );

		
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
            //如果发货成功，则将批发单对应订单明细商品的发货状态该为已发货；
            $res1=$salesModel->updateOrderSendStatus(trim($detail_id_str, ","));
            if($res1){
            	foreach ($order_snAll as $order_sn){
            		if($order_sn){
            		  $res2=$salesModel->updateBaseOrderSendStatus($order_sn);       		  
            		  if(!$res2){
            		  	$pdo28->rollback(); //事务回滚
            		  	$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
            		  	$pdo56->rollback(); //事务回滚
            		  	$pdo56->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
            		  	$pdo22->rollback(); //事务回滚
            		  	$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
            		  	$result['error'] = '更新订单发货状态和配货状态失败';
            		  	Util::jsonExit($result);
            		  }
            		}
            	}
            	
            }else{
            	$pdo28->rollback(); //事务回滚
            	$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
            	$pdo56->rollback(); //事务回滚
            	$pdo56->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
            	$pdo22->rollback(); //事务回滚
            	$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
            	$result['error'] = '更新订单明细商品的发货状态货失败';
            	Util::jsonExit($result);
            }
           
            //更改批发销售单状态为已审核；
            $bill_str_s = implode(array_column($bill_noInfo, 'bill_no'), "','");
            $res4=$modelWarehouse->updateBillStatusByBillno($bill_str_s);
            if(!$res4){
            	$pdo28->rollback(); //事务回滚
            	$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
            	$pdo56->rollback(); //事务回滚
            	$pdo56->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
            	$pdo22->rollback(); //事务回滚
            	$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
            	$result['error'] = '更新订单发货状态和配货状态失败';
            	Util::jsonExit($result);
            }
            $pdo28->commit(); //事务提交
			$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			$pdo56->commit(); //事务提交
			$pdo56->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			$pdo22->commit(); //事务提交
			$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			$result['success'] = 1;
			Util::jsonExit($result);
		}
		else
		{ 
			$pdo28->rollback(); //事务回滚
			$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			$pdo56->rollback(); //事务回滚
			$pdo56->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			$pdo22->rollback(); //事务回滚
			$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			$result['error'] = '添加失败';
			Util::jsonExit($result);
			
		}
		
      }catch (Exception $e){	
    			$pdo28->rollback(); //事务回滚
    			$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$pdo22->rollback(); //事务回滚
    			$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    			$result['error'] ="系统异常！error code:".$e;
    			Util::jsonExit($result);
      }	
		
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
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;

		$newmodel =  new TsydJxsDeliveryModel($id,22);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
		);

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;	
			$result['title'] = '修改此处为想显示在页签上的字段';
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
		$model = new TsydJxsDeliveryModel($id,22);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		//联合删除？
		//$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

    /**
     *  up_delivery，渲染发货页面
     */
    public function up_delivery ($params)
    {
        $ids = $params["_ids"];
        $id = '';
        $tab_id = _Request::getInt("tab_id");
        $result = array('success' => 0,'error' => '');
        $modelWarehouse = new WarehouseModel(21);
        $dataP = $modelWarehouse->selectWarehouseBill("`to_customer_id`", "`id` in(".implode(",", $ids).")");

        $toCId = array_flip(array_column($dataP, 'to_customer_id'));
    
        if(count($toCId) <> 1){

            echo "提示：<span style='color:red;'>所选批发单不属同一个批发客户不允许发货！</span>";
            exit();
        }

        //取得单据号
        $dataPId = $modelWarehouse->selectWarehouseBill("`id`,`bill_no`", "`id` in(".implode(",", $ids).")");

        //取得一个订单号
        $order_sn = $modelWarehouse->selectWarehouseBillGoods("`order_sn`", "`bill_id` in(".implode(",", $ids).") limit 1",3);
        
        //获取快递公司
        $exp_list = $this->getExpressList();     
        $result['content'] = $this->fetch('tsyd_jxs_delivery_info.html',array(
            'view'=>new TsydJxsDeliveryView(new TsydJxsDeliveryModel($id,21)),
            'tab_id'=>$tab_id,
            'dataArray'=>$dataPId,
            'exp_list'=>$exp_list,
        	'order_sn'=>$order_sn,	
        ));
        $result['title'] = '批量发货';
        Util::jsonExit($result);
    }

    /**
     *  getExpressList，获取所有有用的快递公司
     */
    public function getExpressList()
    {
        $expressModel = new ExpressModel(1);
        $exp_list = $expressModel->getAllExpress();
        $data = array();

        foreach($exp_list as $v){

            $data[$v['id']]=$v['exp_name'];
        }
        return $data;
    }
    
    /**
    * 打印提货单 
    * @param unknown $params
    */
    public function printBills($params)
    {
    	$bill_id = _Request::getString('_ids');    
    	$bill_id_arr = explode(",",$bill_id);
    	$html = '';
    	
    	$model = new WarehouseModel(21);
    	$salesModel = new SalesModel(27);
    	$styleModel = new SelfStyleModel(11);
    	$processorModel = new SelfProcessorModel(13);
    	$ke=new Kezi();
    	foreach($bill_id_arr as $vo){
    	    $bill_info = $model->getTsydWarehouseBill($vo);
    	    if(empty($bill_info)){
    	        continue;
    	    }
    	    $goods_list = $model->getTsydOrderDetailList($vo);
    	    foreach ($goods_list as $key=> &$goods){
    	            //获取图片 拼接进数组
    	            $img = $styleModel->selectStyleGallery("thumb_img","style_sn='{$goods['goods_sn']}' and image_place=1",3);
   	                $goods['goods_img'] = $img?$img:'';   	        
    	            $goods['box_id'] = '无';
    	            //是否绑定
       	            $goods_id = $model->selectWarehouseGoods("goods_id","order_goods_id='{$goods['id']}'",3);
       	            if($goods_id){
    	                $goods['bind_goods'] = 1; //有商品绑定
    	                $goods['goods_id'] = $goods_id;
    	                //柜位号
    	                $box_id = $model->selectGoodsWarehouse('`box_id`', " `good_id` ='{$goods_id}'",3);
    	                if($box_id){
    	                    $goods['box_id'] = $model->selectWarehouseBox(" `box_sn` ","`id`={$box_id} " ,3); //有柜位号
    	                }
    	            }else{
    	                $goods['bind_goods'] = 0; //无商品绑定
    	            }
    	            $goods['kezi']=$ke->retWord($goods['kezi']);
    	             
    	            //获取售卖方式
    	            $is_alone = $processorModel->selectProdcutInfo("is_alone","p_id={$goods['id']}",1);
    	            $goods['is_alone'] = (int)$is_alone;
    	    }
    	    $html.= $this->fetch('print_bills_foreach.html',array(
    	        'goods_list' => $goods_list,
    	        'bill_info'  =>$bill_info
    	    ));
    	}
    	
    	$this->render('print_bills.html', array(
    	    'html'=>$html
    	));
    }
    
    public function printBillLogs($params){
        $result = array('success' => 0,'error' => '');
        
        $bill_id_split = _Post::get('bill_id');
        $bill_id_list = explode(",",$bill_id_split);
        
        $model = new WarehouseModel(21);
        
        $create_user = $_SESSION['userName'];
        $create_time = date("Y-m-d H:i:s");
        foreach ($bill_id_list as $bill_id){
            $do = array(
                'bill_id'=>$bill_id,
                'create_user'=>$create_user,
                'create_time'=>$create_time            
            );
            $ret = $model->selectWarehouseBillPrint("count(1)","bill_id={$bill_id}",3);
            if($ret){ 
                unset($do['bill_id']);
                $sql = $model->updateSql("warehouse_bill_print",$do,"bill_id=".$bill_id);            
            }else{
                $sql= $model->insertSql($do,"warehouse_bill_print");
            }
            $model->db()->query($sql);
        }
        $result['success'] = 1;
        Util::jsonExit($result);
    }
	
    /* 打印快递单 */
    public function print_express($params)
    {
    	$order_no = $params['order_no'];
    	
    	//添加快递补寄 提前发货 url参数
    	$express_id = isset($params['express_id'])?$params['express_id']:'';
    	$model = new ShipFreightModel(43);
    	$data = $model->getOrderDetailsId($order_no);
    	$order = array_merge($data[0],$data[1]);
    
    
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
    	$lable_box['t_shop_name'] = ''; //网店-名称
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
    
    	$lable_box['t_customer_address'] = $customer_province." ".$customer_city." ".$customer_district." ".trim(str_replace(","," ",$order['address'])); //收件人-详细地址
    	$lable_box['t_customer_name'] = $order['consignee']; //收件人-姓名
    
    	$gmtime_utc_temp = time(); //获取 UTC 时间戳
    	$lable_box['t_year'] = date('Y', $gmtime_utc_temp); //年-当日日期
    	$lable_box['t_months'] = date('m', $gmtime_utc_temp); //月-当日日期
    	$lable_box['t_day'] = date('d', $gmtime_utc_temp); //日-当日日期
    
    	//$lable_box['t_order_no'] =$order['order_sn']; //订单号-订单
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
    	// BOSS-724 中通快递\顺丰速运\圆通速递,增加一项内容[客户来源]
    	if(in_array($order['express_id'],array(4,12,19)))
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
    
    
	 /* 打印快递单 */
    /* public function print_express($params)
    {
    	$toCId = $params['toCId'];
		if(empty($toCId)){
			die('没有批发客户');
		}
    	$salesModel=new SalesModel(27);
		$WarehouseModel=new WarehouseModel(21);
    	$rows=$salesModel->getAddressByWholesaleId($toCId);
		$wholesale_name=$WarehouseModel->getWholesaleName($toCId);
    	if(empty($wholesale_name)){
			die('批发客户错误');
		}
		$rows['wholesale_name']=$wholesale_name;
    	
    	$this->render('print_express.html',array(
    			'rows' => $rows,    			
    			'is_muti'=>0
    	));
      
    	
    } */
    
}

?>