<?php
/**
 *  -------------------------------------------------
 *   @file		: AppReturnGoodsController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-13 11:05:03
 *   @update	:
 *  -------------------------------------------------
 */
class AppReturnGoodsController extends CommonController
{
	protected $smartyDebugEnabled = false;

	//换单原因
	public static $return_reason = array ('1' => '货不对版,需重新做', '2' => '指圈号不合适，且不能修改，要重新定制', //'3'=>'现货钻石7天内可以更换',
'4' => '裸钻没有订到，换钻石', '5' => '成品以旧换新', '6' => '其他' );

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('app_return_goods_search_form.html',array('bar'=>Auth::getBar(),'view'=>new AppReturnGoodsView(new AppReturnGoodsModel(31))));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
	//	if($_SESSION['userType']==1){
            $department = _Request::getInt('department')?_Request::getInt('department'):0;
        //}else{
          //  if(isset($_REQUEST['department'])){
                //$department = _Request::getInt('department')?_Request::getInt('department'):($_SESSION['qudao']?$_SESSION['qudao']:-1);
          //  }else{
                //$department = _Request::getInt('department')?_Request::getInt('department'):($_SESSION['qudao']?current(explode(',', $_SESSION['qudao'])):-1);
          //  }
        //}
		$args = array(
            'mod'   => _Request::get("mod"),
            'con'   => substr(__CLASS__, 0, -10),
            'act'   => __FUNCTION__,
			'return_id'	=> _Request::getInt("return_id"),
			'order_sn'	=> _Request::getString("order_sn"),
			'return_type'	=> _Request::getInt("return_type"),
			'start_time'	=> _Request::getString("start_time"),
			'end_time'	=> _Request::getString("end_time"),
			'finance_start_time'	=> _Request::getString("finance_start_time"),
			'finance_end_time'	=> _Request::getString("finance_end_time"),			
			'department'	=> $department,
			'apply_user'    => _Request::getString("apply_user"),
		);
		$page = _Request::getInt("page",1);
		$where = array(
            'return_id'=>$args['return_id'],
            'order_sn'=>$args['order_sn'],
            'return_type'=>$args['return_type'],
            'start_time'=>$args['start_time'],
            'end_time'=>$args['end_time'],
			'finance_start_time'	=> $args['finance_start_time'],
			'finance_end_time'	=> $args['finance_end_time'],	            
            'department'=>$args['department'],
            'apply_user'    => $args['apply_user'],
            'finance_status'=>1
        );

		$model = new AppReturnGoodsModel(31);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_return_goods_search_page';
		$this->render('app_return_goods_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
//		$result = array('success' => 0,'error' => '');
//		$result['content'] = $this->fetch('app_return_goods_info.html',array(
//			'view'=>new AppReturnGoodsView(new AppReturnGoodsModel(31)),
//            'return_reason'=>self::$return_reason,
//		));
//		$result['title'] = '退款申请页面';
//		Util::jsonExit($result);
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
        	die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }			
        $jumpurl = "index.php?mod=refund&con=AppReturnGoods&act=index";
        $menuModel = new MenuModel(1);
        $menu = $menuModel->getMenuId($jumpurl);
        $this->render('app_return_goods_info.html',array(
			'view'=>new AppReturnGoodsView(new AppReturnGoodsModel(31)),
            'return_reason'=>self::$return_reason,
            'tab_id'=>_Request::getInt('tab_id'),
            'menu' => $menu
		));
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_return_goods_info.html',array(
			'view'=>new AppReturnGoodsView(new AppReturnGoodsModel($id,31)),
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
        $model = new AppReturnGoodsModel(31);
        $info = $model->getInfoById($id);
        $apiModel = new ApiRefundModel();
        if($info['order_goods_id']>0){
            $goods_info = $apiModel->getGoodsSnByGoodsId($info['order_goods_id']);
            $info['order_goods_id'] = $goods_info['goods_sn'];
        }else{
            $info['order_goods_id'] = '';
        }
        if($info['apply_user_id']>0){
            $userModel = new UserView(new UserModel($info['apply_user_id'],1));
            $info['apply_user_id'] = $userModel->get_account();
        }else{
            $info['apply_user_id'] = '';
        }
        if($info['department']>0){
            $userModel = new SalesChannelsView(new SalesChannelsModel($info['department'],1));
            $info['department'] = $userModel->get_channel_name();
        }else{
            $info['department'] = '';
        }
        if($info['leader_id']>0){
            $userModel = new UserView(new UserModel($info['leader_id'],1));
            $info['leader_id'] = $userModel->get_account();
        }else{
            $info['leader_id'] = '';
        }
        if($info['goods_comfirm_id']>0){
            $userModel = new UserView(new UserModel($info['goods_comfirm_id'],1));
            $info['goods_comfirm_id'] = $userModel->get_account();
        }else{
            $info['goods_comfirm_id'] = '';
        }
        if($info['cto_id']>0){
            $userModel = new UserView(new UserModel($info['cto_id'],1));
            $info['cto_id'] = $userModel->get_account();
        }else{
            $info['cto_id'] = '';
        }
        if($info['deparment_finance_id']>0){
            $userModel = new UserView(new UserModel($info['deparment_finance_id'],1));
            $info['deparment_finance_id'] = $userModel->get_account();
        }else{
            $info['deparment_finance_id'] = '';
        }
        if($info['finance_id']>0){
            $userModel = new UserView(new UserModel($info['finance_id'],1));
            $info['finance_id'] = $userModel->get_account();
        }else{
            $info['finance_id'] = '';
        }
        $logModel = new AppReturnLogModel(31);
        $logInfo = $logModel->getLogInfoByReturnId($id);
        $this->render('app_return_goods_show.html',array(
			'view'=>$info,
			'logInfo'=>$logInfo,
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function inserts ($params)
	{
		$result = array('success' => 0,'error' =>'');
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;
		$olddo = array();
		$newdo=array();

		$newmodel =  new AppReturnGoodsModel(32);
		$res = $newmodel->saveData($newdo,$olddo);
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
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;

		$newmodel =  new AppReturnGoodsModel($id,32);

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
		$model = new AppReturnGoodsModel($id,32);
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
	 *	check，列表
	 */
	public function check ($params)
	{
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			//'参数' = _Request::get("参数");
            'order_sn' => _Request::getString ( 'order_sn' ),
            'is_ajax' => _Request::getInt ( 'is_ajax' ),
            'return_by' => _Request::getString ( 'return_by' ),

		);
		$order_sn = $args['order_sn'];
		$is_ajax = $args['is_ajax'];
		$return_by = $args['return_by'];
        
		$ReturnGoodsModel = new AppReturnGoodsModel (31);
		$order_info = $ReturnGoodsModel->get_order_info_by_order_sn ( $order_sn );
		if (empty ( $order_info )) {
			$result['error'] = "订单信息不存在";
		    Util::jsonExit($result);
		}

        if ($order_info['order_status']==4) {
            $result['error'] = "“".$order_sn."”此订单己关闭，无法操作退款";
            Util::jsonExit($result);
        }

        $order_id = $order_info['order_id'];
        //获取此订单的相关金额
        $order_account = $ReturnGoodsModel->get_monery_by_return_id($order_id);
		if($order_info['order_pay_status']==4){
            
        }else{
            if ($order_info ['order_pay_status']==1) {
                $result['error'] = '订单还未支付!';
                Util::jsonExit($result);
            }
            if($return_by==1 && $order_account['order_amount']==0){
                
            }else{
                if($order_account ['money_paid']==0.00){
                    $result['error'] = '订单已付金额为0不能进行退款!';
                    Util::jsonExit($result);
                }
            }
        }
        
		/*if ($order_info ['referer'] == 'QQ下单' || $order_info ['referer'] == 'QQ返利') {
			$res ['message'] = "非销售系统下单，到老退款系统退款!\r\n(http://www.kela.cn/shop001/return_goods.php?act=apply)";
			JsonModel::Je ( $res );
		}*/
		
		$content = '';
		$content ['order_sn'] = '订单号:' . $order_info ['order_sn'];
		$content ['money_paid'] = '订单已付金额:' . $order_account ['money_paid'];
		$content ['order_amount'] = '订单未付金额:' . $order_account ['money_unpaid'];

        $paid=$ReturnGoodsModel->getGoodsPaid($order_sn);
		$order_goods = $ReturnGoodsModel->get_order_details_by_order_id_and_is_return ( $order_info ['order_id'] );
        $goods = array();
        if ($order_goods) {
			foreach ( $order_goods as $og ) {
				$real_price = $og['favorable_status'] == 3?$og ['goods_price']-$og['favorable_price']:$og['goods_price'];          
                $goods_paid=$paid[$og['id']]['goods_paid'];
				$goods [] = array ('rec_id' => $og ['id'], 'goods_id'=>$og['goods_id'],'goods_price' => $real_price, 'goods_name' => $og ['goods_name'],'goods_paid' => $goods_paid,'is_cpdz' =>$og['is_cpdz'] );
			}
		}

		$result['success'] = 1;
		$result ['goods'] = $goods;
		$result ['goods_num'] = count ( $goods );
		$result ['content'] = $content;
		Util::jsonExit($result);
	}

	/**
	 *
	 * 退款申请提交 department
	 */
	public function insert() {
		$result = array('success' => 0,'error' =>'');
		$is_ajax = _Request::getInt ('is_ajax');
		$return_type = _Request::getString ('return_type');
		$return_by = _Request::getString ('return_by');
		$order_sn = _Request::getString ('order_sn');
		$order_goods_id_list = _Request::getList ('order_goods_id');
		$apply_amount_list = _Request::getList ('apply_amount');
		$price_fee_list = _Request::getList ('price_fee' );

		if (empty ( $return_by )) {
			$result ['error'] = '请选择退款方式!';
			Util::jsonExit($result);
		} else if (empty ( $return_type )) {
			$result ['error'] = '请选择退款类型!';
			Util::jsonExit($result);
		} else if (empty ( $order_sn )) {
			$result ['error'] = '请输入订单号!';
			Util::jsonExit($result);
		}else if(empty($order_goods_id_list)){
		    $result ['error'] = '请选择退款商品!';
		    Util::jsonExit($result);
		}		
		
        //逻辑
		$ReturnGoodsModel = new AppReturnGoodsModel (32);
		$salesModel = new SalesModel(32);
		$warehouseModel = new WarehouseModel(21);//仓库模块Model
		
		$order_info = $ReturnGoodsModel->get_order_info_by_order_sn ($order_sn);		
		if (empty ( $order_info )) {
			$result ['error'] = '订单号不存在!';
			Util::jsonExit($result);
		}
		
		$pdolist[32] = $ReturnGoodsModel->db()->db();
		try{
		    foreach ($pdolist as $pdo){
		        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
		        $pdo->beginTransaction(); //开启事务
		    }
		}catch (Exception $e){
		    $error = "操作失败，事物回滚！提示：系统批量开启事物时发生异常！";
		    Util::rollbackExit($error,$pdolist);
		}
		$return_id_list = array();
		foreach ($order_goods_id_list as $seq=>$order_goods_id){
		    $seq++;
		    $error_title = "序号【{$seq}】,";
		    
		    if(!isset($apply_amount_list[$order_goods_id])){
		        $error = '系统内部异常，退款金额无法获取，请联系技术人员处理!';
		        Util::rollbackExit($error,$pdolist);
		    }
		    $apply_amount = $apply_amount_list[$order_goods_id];//申请退款金额
		    
		    if(!isset($price_fee_list[$order_goods_id])){
		        $error = '系统内部异常，手续费无法获取，请联系技术人员处理!';
		        Util::rollbackExit($error,$pdolist);
		    }
		    $price_fee =  $price_fee_list[$order_goods_id];//手续费
		    
    		$goods_info = $salesModel->getAppOrderDetailByDetailId($order_goods_id);
    		if(empty($goods_info)){
    		    $error = $error_title.'退款商品不存在!';
    		    Util::rollbackExit($error,$pdolist);
    		}
            $order_id = $order_info['order_id'];   
            if($order_info['order_pay_status']==4){
                if($apply_amount != 0 && $price_fee != 0){
                    $error = $error_title.'财务备案的订单退款金额、手续费必须是0元!';
                    Util::rollbackExit($error,$pdolist);
                }
                if($return_by != 1){
                    $error = $error_title.'财务备案的订单退款方式只能选择退商品!';
                    Util::rollbackExit($error,$pdolist);
                }
                $apply_amount = (float) $apply_amount;
                $price_fee = (float) $price_fee;
            }else{
                if ($apply_amount=='') {
                    $error = $error_title.'请输入申请退款金额!';
                    Util::rollbackExit($error,$pdolist);
                }
                if($apply_amount<0 || (!empty($apply_amount) && !is_numeric($apply_amount))){
                    $error = $error_title.'申请金额不合法!';
                    Util::rollbackExit($error,$pdolist);
                }else{
                    $apply_amount = (float) $apply_amount;
                }
                if($price_fee<0 || (!empty($price_fee) && !is_numeric($price_fee))){
                    $error = $error_title.'手续费金额不合法!';
                    Util::rollbackExit($error,$pdolist);
                }else{
                    $price_fee = (float) $price_fee;
                }
                if($price_fee > $apply_amount*0.9){
                    $error = $error_title.'手续费不能大于申请金额的90%!';
                    Util::rollbackExit($error,$pdolist);
                }
            }
            
            
            $checkReturnGoods = $ReturnGoodsModel->checkReturnGoods($order_sn,$order_goods_id,1);
            if($checkReturnGoods){
                $error = $error_title."订单商品{$goods_info['goods_id']}已申请过 退货退款，不能再申请了！";
                Util::rollbackExit($error,$pdolist);
            }
    
    		if ($return_by == '1') {             
                $weixiuCheck = $ReturnGoodsModel->checkOrderWeixiuStatus($order_sn,$order_goods_id);
                if($weixiuCheck){
                    $error = $error_title.'订单正处于维修中，不能退商品!';
                    Util::rollbackExit($error,$pdolist);
                }
            }  
    
            //********
            //单个货品申请金额总和不能大于订单商品成交价
            //********
            //获取单个货品已申请未审核的退款金额   
            $uncheck_goods_return_amount=0;
            $uncheck_goods_array=$ReturnGoodsModel->getReturnAccountUncheck($order_sn,$order_goods_id);
            if($uncheck_goods_array && $uncheck_goods_array['apply_return_amount']>0){
                $uncheck_goods_return_amount=$uncheck_goods_return_amount+$uncheck_goods_array['apply_return_amount'];
            }
           
            if($uncheck_goods_return_amount>0){
                $error = $error_title.'请先审核未完的退款申请';
                Util::rollbackExit($error,$pdolist);
            }
    
            //获取单个货品已申请已审核的退款金额  
            $check_goods_return_amount=0;
            $check_goods_array=$ReturnGoodsModel->getReturnAccountCheck($order_sn,$order_goods_id);
            if($check_goods_array && $check_goods_array['apply_return_amount']>0){
                $check_goods_return_amount=$check_goods_return_amount+$check_goods_array['apply_return_amount'];
            }
           
            $goods_price=$goods_info['favorable_status']==3 ? $goods_info['goods_price']-$goods_info['favorable_price'] : $goods_info['goods_price'];
            $is_pay_zero=$ReturnGoodsModel->isPayZero($order_id);
            //按比例计算单个商品已付款余额
            $paid=$ReturnGoodsModel->getGoodsPaid($order_sn);
            $goods_paid=$paid[$order_goods_id]['goods_paid'];
            if($apply_amount>0 && $return_by<>'1' && $is_pay_zero==false && bcsub($goods_paid,$apply_amount,3)*1000<$goods_price*0.5*1000){
                $error = $error_title.'商品已付款余额不足商品成交价50%:'.$goods_price*0.5.'最大可申请退款金额不能超过'.($goods_paid-$goods_price*0.5);
                Util::rollbackExit($error,$pdolist);
            }
    
            if($apply_amount>0 && $return_by<>'1' && $is_pay_zero==true and in_array($order_info['send_good_status'],array(2,4,5)) && bcsub($goods_paid,$apply_amount,3)*1000<$goods_price*0.5*1000){
                $error = $error_title.'商品已付款余额不足商品成交价50%:'.$goods_price*0.5.'最大可申请退款金额不能超过'.($goods_paid-$goods_price*0.5);
                Util::rollbackExit($error,$pdolist);
            }        
            //不能超过商品已付款余额 
            if($apply_amount>0 && bccomp($apply_amount,$goods_paid,2)==1){
                $error = $error_title.'不能超过商品已付款余额'.$goods_paid;
                Util::rollbackExit($error,$pdolist);
            }        
    
    
            //过度期代码START
            //以下逻辑主要是过度期防止之前有退款未选择具体订单明细,导致退款过多
            //获取未审核的退款申请金额
            $uncheckarray=$ReturnGoodsModel->getReturnAccountUncheck($order_sn);
            $uncheck_return_amount=0;
            if($uncheckarray){
                $uncheck_return_amount=$uncheckarray['apply_return_amount'];
            }
            //获取未审核的退货申请金额
            $uncheckarray=$ReturnGoodsModel->getReturnGoodsAccountUncheck($order_sn);
            $uncheck_return_goods_amount=0;
            if($uncheckarray){
                $uncheck_return_goods_amount=$uncheckarray['apply_return_goods_amount'];
            }        
            if($return_by == '1'){            
            	$uncheck_return_goods_amount=$uncheck_return_goods_amount + $goods_price;
            }  
    
            if($return_by == '1' || $apply_amount==0 || $order_info['order_pay_status']==4){
    
            }else{
    	        //如果仍然有货未退完 (未发货即未生产的话必须保证有30%的货款够生产成本, 已发货的话防止客人退款不退货回来)
    	        $order_amount= bcsub($order_info['order_amount'],$uncheck_return_goods_amount,3);
    	        if($order_amount*1000>0 && $is_pay_zero==false){        	
    	            if( bcsub($order_info['money_paid'],bcadd(bcadd($order_info['real_return_price'],$uncheck_return_amount,3),$apply_amount,3),3) < bcadd($order_amount*0.5,'0',3)){
    	                $paid=$order_info['money_paid']-$order_info['real_return_price']-$uncheck_return_amount-$apply_amount; 
    	                $error = $error_title.'有订单明细未退货,目前申请退款过多,导致付款余额['.$paid.']不足货款['.$order_amount.']的50%';
    	                Util::rollbackExit($error,$pdolist);
    	            }  
    	        }              
    	        //申请退款总和不能超过订单实际付款金额
    	        if(bcadd($apply_amount,$uncheck_return_amount,3)*1000 > bcsub($order_info['money_paid'],$order_info['real_return_price'],3)*1000){
	                $error = $error_title.'申请退款总和不能超过订单实际付款金额';
	                Util::rollbackExit($error,$pdolist);
    	        }
    	    }    

		
    		//基本数据
    		if ($return_type == '1') {
    			$consignee = '';
    			$bank_name = '';
    			$return_card = '';
    			$mobile = '';
    			$zuandan_reason_id = _Request::getInt ( 'zuandan_reason_id' );
    			$return_res = _Request::getString ( 'return_res' );
    			$return_reason = self::$return_reason;
    			$return_res = "#" . $return_reason [$zuandan_reason_id] . "##" . $return_res;
    		} elseif ($return_type == '2') {
    			$consignee = _Request::getString ( 'consignee2', '' );
    			$bank_name = _Request::getString ( 'bank_name2', '' );
    			$return_card = _Request::getString ( 'return_card2' );
    			$mobile = _Request::getString('mobile2');
    			$return_res = _Request::getString ( 'return_res2' );
    		} elseif ($return_type == '3') {
    			$bank_name = '';
    			$consignee = _Request::getString ( 'consignee3', '' );
    			$return_card = '现金';
    			$mobile = _Request::getString ( 'mobile3' );
    			$return_res = _Request::getString ( 'return_res3' );
    		}
    		
    		//预定 使用优惠券
    		//$this->checkOrderBonus ( $order_info, $apply_amount, true );
    		//获取此订单的未退款的商品
    		$order_goods = $ReturnGoodsModel->get_order_details_by_order_id_and_is_return ( $order_info ['order_id'] );
            
    		//实退金额、应退金额
    		$real_return_amount = $apply_amount - $price_fee;
    		//$orderInfo= $salesModel->GetOrderInfoById($order_id);
    		$referer  = $order_info['referer'];
    		$order_sn = $order_info['order_sn'];
    		if($referer=='天生一对加盟商'){
    			//天生一对订单通过时需要都需要做一个判断，0<本次实退金额=<已付金额-实退金额-已配货商品的批发价-非已配货商品的（【原始零售价】*30%）才允许通过
    			
    			if($real_return_amount <= 0){
    				$error = $error_title.'实退金额必须大于0';
    				Util::rollbackExit($error,$pdolist);
    			}
    			$orderAccountInfo=$SalesModel->getOrderAccountInfoByOrderId($order_id);
    			$money_paid=$orderAccountInfo['money_paid'];//已付金额
    			$real_return_price1=$orderAccountInfo['real_return_price'];//实退金额
    		
    			
    			//订单已配货商品的批发价
    			$WarehouseModel=new WarehouseModel(21);
    			$pfj=$WarehouseModel->getGoodsPfj($order_id);
    			//非已配货商品的（【原始零售价】*30%）
    			$retail_price=$SalesModel->getOrderDetailRetailPriceByOrderId($order_id);
    			if($real_return_amount > $money_paid-$real_return_price1-$pfj-$retail_price*0.3){
    				$error = $error_title.'本次实退金额=<已付金额-实退金额-已配货商品的批发价-非已配货商品的（【原始零售价】*30%）才允许通过';;
    				Util::rollbackExit($error,$pdolist);
    			}
    
    		}
    		$return_goods_ids = $warehouseModel->getBillSInfoByOrderSn_New($order_sn,$order_goods_id);
    		if(!empty($return_goods_ids)){
    		    $return_goods_ids = array_column($return_goods_ids, 'goods_id');   
    		    $return_goods_ids = implode(',',$return_goods_ids);
    		}else{
    		    $return_goods_ids = '';
    		}
    		//入库处理
    		try{
        		$olddo=array();
        		$return_order = array ();
        		$return_order ['department'] = $order_info ['department_id'];
        		$return_order ['apply_user_id'] = $_SESSION ["userId"];
        		$return_order ['order_id'] = $order_id;
        		$return_order ['order_sn'] = $order_sn;
        		$return_order ['order_goods_id'] = $order_goods_id; //有商品
        		$return_order ['should_return_amount'] = $real_return_amount;
        		$return_order ['apply_return_amount'] = $apply_amount;
        		$return_order ['real_return_amount'] = $real_return_amount;
        		$return_order ['confirm_price'] = $price_fee;
        		
        		$return_order ['return_res'] = $return_res;
        		$return_order ['return_type'] = $return_type;
        		$return_order ['return_card'] = $return_card;
        		$return_order ['consignee'] = $consignee;
        		$return_order ['mobile'] = $mobile;
        		$return_order ['bank_name'] = $bank_name;
        		
        		$return_order ['apply_time'] = date ( "Y-m-d H:i:s" );
        		$return_order ['zhuandan_amount'] = 0;
        		$return_order['return_by'] = $return_by;
        		$return_order['return_goods_id'] = $return_goods_ids;
        		$tip = "退款单据写入";
        		$return_id = $ReturnGoodsModel->saveData($return_order,$olddo);
        		unset ( $return_order );
        		if($return_id !== false)
        		{
        		    $tip = "订单日志写入";
        		    $logInfo = array(
        		        'order_id'=>$order_id,
        		        'order_status'=>$order_info['order_status'],
        		        'shipping_status'=>$order_info['send_good_status'],
        		        'pay_status'=>$order_info['order_pay_status'],
        		        'create_time'=>date('Y-m-d H:i:s'),
        		        'create_user'=>$_SESSION['userName'],
        		        'remark'=>'退款：'.$real_return_amount.'元/退款流水号：'.$return_id
        		    );
        		    $salesModel->addOrderAction($logInfo);
        		}
        		//订单状态 退款状态
        		$tip = "订单退款状态更改";
        		if($order_info['apply_return']<>2){
        		    $ReturnGoodsModel->setreturn($order_id);
        		    $order_info['apply_return'] = 2;
        		}
        		
        		$tip = "主管自动审核";
        		$department_id = $order_info['department_id'];
        		$customer_source_id = $order_info['customer_source_id'];
        		if($department_id==71 && in_array($customer_source_id,array(2906,2980))){
        		    $res = $this->leaderCheckOne($return_id);
        		    if($res['success']==0){
        		        $error = $error_title.$res['error'];
        		        Util::rollbackExit($error,$pdolist);
        		    }
        		}
        		
        		$return_id_list[] = $return_id;
        		
    		}catch (Exception $e){    		    
    		    $error = "操作失败,提示：".$tip.'失败！'.$e->getMessage();
    		    Util::rollbackExit($error,$pdolist);
    		}
    		

		}//end foreach $order_goods_id_list
		
		try{		    
		    //批量提交事物
		    $tip = "批量提交事物";
		    foreach ($pdolist as $pdo){
		        $pdo->commit();
		        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
		    }
		    $result['success'] = 1;
		    Util::jsonExit($result);
		}catch (Exception $e){
		    $error = "操作失败,提示：".$tip.'失败！'.$e->getMessage();
    		Util::rollbackExit($error,$pdolist);
		} 

	}
	//主管审核
    public function leaderCheckOne($return_id)
    {
        $result = array('success' => 0,'error' =>'');
    
        $leader_res = "销售渠道为【京东销售部】 ,客户来源为【京东自营】【京东闪购】时，主管自动审核通过！";
        $leader_status = 1;

        $id = $return_id;
        $apiModel = new ApiRefundModel();
        $salesModel = new SalesModel(32);
        $returnGoodsModel = new AppReturnGoodsModel($id,32);
        $do = $returnGoodsModel->getDataObject();
        $order_sn = $do['order_sn'];
        $order_info = $returnGoodsModel->get_order_info_by_order_sn($order_sn);        
        if(empty($order_info)){
	        $result['error'] = "流水号为【{$id}】的退款单未关联订单！";
	        return $result;
	    }
        $order_id = $order_info['order_id'];
        
        $newdo = array();
        $_is_add_logs = 0;
        $is_or_no = '通过';
        $_tuiGoods = '';            	
        if (floatval($order_info['money_paid']-$order_info['real_return_price']) < floatval($do ['apply_return_amount'])) {
            $result['error'] = "申请金额[{$do['apply_return_amount']}]大于已付余额[".($order_info['money_paid']-$order_info['real_return_price'])."]！";
            return $result;
        }       
        // 将订单里的产品改为退货状态
        if ($do ['return_by']==1) {
            // 改变产品状态
            $salesModel->updateAppOrderDetail(array('is_return'=>1),"id={$do['order_goods_id']}" );
        }
        
        // 主管审核
        $newdo ['leader_res'] = $leader_res;
        $newdo ['return_id'] = $id;
        $newdo ['leader_id'] = $_SESSION ['userId'];
        $newdo ['leader_status'] = $leader_status;
        $newdo ['leader_time'] = date ( "Y-m-d H:i:s" );

        $returnGoodsModel->setValue('check_status', 1);
        $returnGoodsModel->save(true);
           
        if ($do ["return_by"] == 2) {
            // 库管添加
            $newdo ['goods_res'] = '系统操作,不需库管操作';
            $newdo ['goods_comfirm_id'] = $_SESSION ['userId'];
            $newdo ['goods_status'] = 1;
            $newdo ['goods_time'] = date ( "Y-m-d H:i:s" );

            $ScModel = new SalesChannelsModel($order_info['department_id'],1);
            $orderScInfo = $ScModel->getDataObject();
            $channel_class = $orderScInfo['channel_class'];//1线上，2线下

            $is_check = false;//是否走事业部审核
            $is_cto = false;//是否符合走事业部审核所以条件
            //$order_goods = $apiModel->getOrderDetailByOrderId($do['order_id']);
            $order_goods = $salesModel->getAppOrderDetailByDetailId($order_goods_id);
	        $luozuan_type_info = array('lz', 'caizuan_goods');
	        if($order_goods['is_stock_goods'] == '0' && in_array($order_goods['goods_type'], $luozuan_type_info)){
                $is_check = true;
	        }else if($order_info['referer'] == '婚博会' && in_array($order_goods['goods_type'], $luozuan_type_info)){
	            $is_check = true;
	        }
            
            if($channel_class == 2 && $is_check == true){
                $is_cto = true;
                $returnGoodsModel->setValue('check_status', 2);
            }else{
                $newdo ['cto_id'] = $_SESSION ['userId'];
                $newdo ['cto_status'] = 1;
                $newdo ['cto_res'] = '系统操作,不需事业部操作';
                $newdo ['cto_time'] = date ("Y-m-d H:i:s");

                //申请退款为0时仓库审核通过财务审核和现场财务审核自动通过boss-803
                if($do['apply_return_amount']==0){
                    $newdo ['deparment_finance_res'] = '系统操作,不需库管操作';
                    $newdo ['deparment_finance_id'] = $_SESSION ['userId'];
                    $newdo ['deparment_finance_status'] =1;
                    $newdo ['deparment_finance_time'] = date ( "Y-m-d H:i:s" );

                    $newdo ['finance_id'] = $_SESSION ['userId'];
                    $newdo ['finance_status'] = 1;
                    $newdo ['finance_res'] = '系统操作,不需事业部操作';
                    $newdo ['finance_time'] = date ( "Y-m-d H:i:s" );
                    $res2=$returnGoodsModel->getReturnGoodsByWhere1($order_id,$id);
                    if(!$res2){
                        $returnGoodsModel->returnapply($order_id);        
                    }        
                    $returnGoodsModel->setValue('check_status', 5);
                }else{        
                    $returnGoodsModel->setValue('check_status', 3);
                }
            }

            $returnGoodsModel->save(true);
            $_is_add_logs = 1;
        }    

        
        $checkModel = new AppReturnCheckModel(32);
        $res = $checkModel->saveData($newdo, array());
        // 添加备注
        $insertnote = array ();
        $insertnote ['return_id'] = $id;
        $insertnote ['even_time'] = date("Y-m-d H:i:s");
        $insertnote ['even_user'] = $_SESSION ['userName'];
        $insertnote ['even_content'] = '部门主管审核:' . $leader_res;
        $logModel = new AppReturnLogModel(32);
        $res = $logModel->saveData($insertnote, array());
        unset($insertnote);        
    
        // 判断是否需要 改变 库管状态
        if ($do ["return_by"] == 2) {
            // 库管添加备注
            $insertnote = array ();
            $insertnote ['return_id'] = $id;
            $insertnote ['even_time'] = date("Y-m-d H:i:s");
            $insertnote ['even_user'] = $_SESSION ['userName'];
            $insertnote ['even_content'] = '系统操作,不需库管操作';
            $logModel->saveData($insertnote, array());
            unset ( $insertnote );
            	
            if(!$is_cto){        
                // 事业部添加备注
                $insertnote = array ();
                $insertnote ['return_id'] = $id;
                $insertnote ['even_time'] = date("Y-m-d H:i:s");
                $insertnote ['even_user'] = $_SESSION ['userName'];
                $insertnote ['even_content'] = "退单".$id.'事业部负责人：非裸钻订单事业部负责人默认批准';
                $logModel->saveData($insertnote, array());
                unset ( $insertnote );
            }
        }
    
        //订单操作日志
        $insert_action = array ();
        $insert_action ['order_id'] = $order_id;
        $insert_action ['order_status'] = $order_info ['order_status'];
        $insert_action ['shipping_status'] = $order_info ['send_good_status'];
        $insert_action ['pay_status'] = $order_info ['order_pay_status'];
        $insert_action ['remark'] = '退款/退货单号:'.$id.$_tuiGoods.'，主管已经审核'.$is_or_no;
        $insert_action ['create_user'] = $_SESSION ['userName'];
        $insert_action ['create_time'] = date ( 'Y-m-d H:i:s' );
        $res = $salesModel->addOrderAction($insert_action);
        unset ( $insert_action );
    
        if ($do ["order_goods_id"] == 0  && $leader_status == 1) {
            //订单操作日志
            $insert_action = array ();
            $insert_action ['order_id'] = $order_id;
            $insert_action ['order_status'] = $order_info ['order_status'];
            $insert_action ['shipping_status'] = $order_info ['send_good_status'];
            $insert_action ['pay_status'] = $order_info ['order_pay_status'];
            $insert_action ['remark'] = "退单".$id.'退款/退货单号:'.$id.$_tuiGoods.'，库管默认审核';
            $insert_action ['create_user'] = $_SESSION ['userName'];
            $insert_action ['create_time'] = date ( 'Y-m-d H:i:s' );
            $res = $salesModel->addOrderAction($insert_action);
            unset ( $insert_action );
    
            if(!$is_cto){        
                //订单操作日志
                $insert_action = array ();
                $insert_action ['order_id'] = $order_id;
                $insert_action ['order_status'] = $order_info ['order_status'];
                $insert_action ['shipping_status'] = $order_info ['send_good_status'];
                $insert_action ['pay_status'] = $order_info ['order_pay_status'];
                $insert_action ['remark'] = '退款/退货单号:'.$id.$_tuiGoods.'，事业部默认审核';
                $insert_action ['create_user'] = $_SESSION ['userName'];
                $insert_action ['create_time'] = date ( 'Y-m-d H:i:s' );
                $res = $salesModel->addOrderAction($insert_action);
                unset ( $insert_action );
            }
        }        
    
        if($res !== false)
        {
            $result['success'] = 1;
        }
        else
        {
            $result['error'] = '审核失败';
        }
        return $result;

    }


	/**
	 *
	 * 退款申请提交 department
	 */
	public function insert_bak() {
		$result = array('success' => 0,'error' =>'');
		$is_ajax = _Request::getInt ( 'is_ajax' );
		$return_type = _Request::getString ( 'return_type' );
		$return_by = _Request::getString ( 'return_by' );
		$order_sn = _Request::getString ( 'order_sn' );
		$order_goods_id = _Request::getString ( 'order_goods_id' );
		if (empty ( $return_by )) {
			$result ['error'] = '请选择退款方式!';
			Util::jsonExit($result);
		} else if (empty ( $return_type )) {
			$result ['error'] = '请选择退款类型!';
			Util::jsonExit($result);
		} else if (empty ( $order_sn )) {
			$result ['error'] = '请输入订单号!';
			Util::jsonExit($result);
		}else if(empty($order_goods_id)){
		    $result ['error'] = '请选择退款商品!';
		    Util::jsonExit($result);
		}
		
        //逻辑
		$ReturnGoodsModel = new AppReturnGoodsModel (31);
		$salesModel = new SalesModel(31);
		$order_info = $ReturnGoodsModel->get_order_info_by_order_sn ( $order_sn );
		$goods_info = $salesModel->getAppOrderDetailByDetailId($order_goods_id);
		if (empty ( $order_info )) {
			$result ['error'] = '订单号不存在!';
			Util::jsonExit($result);
		}else if(empty($goods_info)){
		    $result ['error'] = '退款商品不存在!';
		    Util::jsonExit($result);
		}
        $order_id = $order_info['order_id'];   
        $apply_amount = _Request::getFloat ( 'apply_amount' );
        $price_fee = _Request::getFloat ( 'price_fee' );
        if($order_info['order_pay_status']==4){
            if($apply_amount != 0 && $price_fee != 0){
                $result ['error'] = '财务备案的订单退款金额、手续费必须是0元!';
                Util::jsonExit($result);
            }
            if($return_by != 1){
                $result ['error'] = '财务备案的订单退款方式只能选择退商品!';
                Util::jsonExit($result);
            }
        }else{
            $_apply_amount = ''.$apply_amount;
            if ($_apply_amount=='') {
                $result ['error'] = '请输入申请退款金额!';
                Util::jsonExit($result);
            }
            if($apply_amount != 0 && $apply_amount<0){
                $result ['error'] = '申请金额不合法!';
                Util::jsonExit($result);
            }
            if($price_fee != 0 && $price_fee<0){
                $result ['error'] = '手续费金额不合法!';
                Util::jsonExit($result);
            }
            if($price_fee > $apply_amount*0.9){
                $result ['error'] = '手续费不能大于申请金额的90%!';
                Util::jsonExit($result);
            }
        }
        
        
        $checkReturnGoods = $ReturnGoodsModel->checkReturnGoods($order_sn,$order_goods_id,1);
        if($checkReturnGoods){
            $result ['error'] = "订单商品{$goods_info['goods_id']}已申请过 退货退款，不能再申请了！";
            Util::jsonExit($result);
        }
		if ($return_by == '1') {             
            $weixiuCheck = $ReturnGoodsModel->checkOrderWeixiuStatus($order_sn,$order_goods_id);
            if($weixiuCheck){
                $result ['error'] = '订单正处于维修中，不能退商品!';
                Util::jsonExit($result);
            }
                            
            //获取未退货的商品数量
            $GoodsList = $ReturnGoodsModel->getGoodsList($order_info['order_id']);
            $num = count($GoodsList);
			 
            if($num == 1){
                //订单中剩余一个商品的时候不能有余款
				
				    $money_paid = $order_info['money_paid'];  //已付款金额
                    $real_return_price = $order_info['real_return_price']; //实退金额
                    $goods_amount = $order_info['goods_amount']; //商品总金额
                    $favorable_price = $order_info['favorable_price']; //商品优惠
                    $t_goods_price = $ReturnGoodsModel->getGoodsPrice($order_info['order_id']); //获取划红线的退款商品信息 
					 
                    $temp_price = 0.00;
                    if(!empty($t_goods_price)){ 
                      foreach ($t_goods_price as $k => $val){ 
                           $temp_price += $val['goods_price']; 
                      }
                    } 
                    //$temp_price = number_format($temp_price,2);  
					 
                     $t_price = $temp_price; // 实退商品金额
                    $t_favorable_price = $ReturnGoodsModel->getReturnGoodsfavor($order_info['order_id']);
				    
                    $t_favorable_price =  $t_favorable_price  ?  $t_favorable_price : 0.00; //扣除商品优惠金额
					 
                    $tuihuo_price = $ReturnGoodsModel->getNewReturn($order_goods_id,'goods_price'); // 退货商品金额
					 
		            $tuihuo_price = $tuihuo_price ?  $tuihuo_price  : 0.00;
					 
                    $tuihuo_favorable_price = $ReturnGoodsModel->getNewReturn($order_goods_id,'favorable_price');//退货商品优惠
					$tuihuo_favorable_price = $tuihuo_favorable_price ?  $tuihuo_favorable_price  : 0.00;
                  //退款金额<=已付金额-实退金额- ((商品总金额-商品总优惠-(实退商品金额-扣除商品优惠金额)-(退货商品金额-退货商品优惠))*0.3)
                  //  $shouldPrice = $money_paid-$real_return_price-(($goods_amount-$favorable_price-($t_price-$t_favorable_price)-($tuihuo_price-$tuihuo_favorable_price))* 0.3);
	    	   //echo "$money_paid-$real_return_price-(($goods_amount-$favorable_price-($t_price-$t_favorable_price)-($tuihuo_price-$tuihuo_favorable_price))* 0.3);";
				//11803.00-2077.00-((21803.00-0.00-(2,077.00-0)-(18637.00-0))* 0.3);
		  
				 /*
                 $price_1 = $money_paid-$real_return_price;
                 
                 $price_2 = $goods_amount-$favorable_price;
				 
                 $price_3 = $t_price-$t_favorable_price;	
				 
                 $price_4 = $tuihuo_price-$tuihuo_favorable_price;
				 
               	 $price_5 = $price_2 - $price_3-$price_4;
				  
		         $price_6 = $price_5 * 0.5;
			 
				 $shouldPrice = $price_1-$price_6;
				 */
				 $shouldPrice = $money_paid-$real_return_price;
				 //$str = "{$price_1}";
				 //$result ['error'] = $str;
				 //Util::jsonExit($result);
                 /*
                     if($shouldPrice == 0){
                        $result ['error'] = '该订单已不能再申请退款';
                        Util::jsonExit($result);
                    }
                 */   
                   if(bccomp($apply_amount,$shouldPrice)==1){
                        //$result ['error'] = '1.退款金额'.$apply_amount.'不能大于'.$shouldPrice.'(金额=已付金额-实退金额- ((商品总金额-商品总优惠-(实退商品金额-扣除商品优惠金额)-(退货商品金额-退货商品优惠))的50%)!';
                       $result ['error'] = '1.退款金额'.$apply_amount.'不能大于'.$shouldPrice.'(金额=已付金额-实退金额)';
                       Util::jsonExit($result);
                    }
				/*
                if($apply_amount > $order_info['money_paid']){
                    $result ['error'] = '申请金额不能大于已付金额!';
                    Util::jsonExit($result);
                }
				*/
//                if($apply_amount != $order_info['money_paid']){
//                    $result ['error'] = '订单中剩余一个商品的时候不能有余款!';
//                    Util::jsonExit($result);
//                }
            }else{
                
                if($order_info['order_pay_status']==4){
                 
                }else{                     
                
                    $money_paid = $order_info['money_paid'];  //已付款金额
                    $real_return_price = $order_info['real_return_price']; //实退金额
                    $goods_amount = $order_info['goods_amount']; //商品总金额
                    $favorable_price = $order_info['favorable_price']; //商品优惠
                    $t_goods_price = $ReturnGoodsModel->getGoodsPrice($order_info['order_id']); //获取划红线的退款商品信息 
					 
                    $temp_price = 0.00;
                   if(!empty($t_goods_price)){ 
                       foreach ($t_goods_price as $k => $val){ 
                            $temp_price += $val['goods_price']; 
                       }
                   } 
                    //$temp_price = number_format($temp_price,2);  
					 
                     $t_price = $temp_price; // 实退商品金额
                    $t_favorable_price = $ReturnGoodsModel->getReturnGoodsfavor($order_info['order_id']);
				    
                    $t_favorable_price =  $t_favorable_price  ?  $t_favorable_price : 0.00; //扣除商品优惠金额
					 
                    $tuihuo_price = $ReturnGoodsModel->getNewReturn($order_goods_id,'goods_price'); // 退货商品金额
					 
		            $tuihuo_price = $tuihuo_price ?  $tuihuo_price  : 0.00;
					 
                    $tuihuo_favorable_price = $ReturnGoodsModel->getNewReturn($order_goods_id,'favorable_price');//退货商品优惠
					$tuihuo_favorable_price = $tuihuo_favorable_price ?  $tuihuo_favorable_price  : 0.00;
                  //退款金额<=已付金额-实退金额- ((商品总金额-商品总优惠-(实退商品金额-扣除商品优惠金额)-(退货商品金额-退货商品优惠))*0.3)
                  //  $shouldPrice = $money_paid-$real_return_price-(($goods_amount-$favorable_price-($t_price-$t_favorable_price)-($tuihuo_price-$tuihuo_favorable_price))* 0.3);
	    	   $shouldPriceDesc= "$money_paid-$real_return_price-(($goods_amount-$favorable_price-($t_price-$t_favorable_price)-($tuihuo_price-$tuihuo_favorable_price))* 0.5);";
				//11803.00-2077.00-((21803.00-0.00-(2,077.00-0)-(18637.00-0))* 0.3);
				 
                 $price_1 = $money_paid-$real_return_price;
   
                 $price_2 = $goods_amount-$favorable_price;
				 
                 $price_3 = $t_price-$t_favorable_price;	
  
                 $price_4 = $tuihuo_price-$tuihuo_favorable_price;
                
               	 $price_5 = $price_2 - $price_3-$price_4;
               	 //$result ['error'] = "{$price_2} - {$price_3}-{$price_4}";
               	 //Util::jsonExit($result);
				 $price_6 = $price_5 * 0.5;
			 
				 $shouldPrice = $price_1-$price_6;

                 /*
                    if($shouldPrice == 0){
                        $result ['error'] = '该订单已不能再申请退款';
                        Util::jsonExit($result);
                    }
                    */ 
 
				 //校验订单退款金额
                   if(bccomp($apply_amount,$shouldPrice)==1){
                        $result ['error'] = '2.退款金额'.$apply_amount.'不能大于'.$shouldPrice.'(金额=已付金额-实退金额- ((商品总金额-商品总优惠-(实退商品金额-扣除商品优惠金额)-(退货商品金额-退货商品优惠))的50%)<br/>'.$shouldPrice.'='.$shouldPriceDesc;
                        Util::jsonExit($result);
                    }
                    
                    
                }
            }
		} elseif ($return_by == '2') {		   
		    
		    //1.可申请商品退款金额($allow_goods_price1) <= 订单已支付总金额-订单已申请退款金额
		    $returnGoodsPriceAll = $ReturnGoodsModel->getReturnGoodsPrice($order_sn);
		    $allow_goods_price1 = $order_info['money_paid'] - $returnGoodsPriceAll;
		    
		    //2.可申请商品退款金额($allow_goods_price2) < 订单商品实际成交价*50% - 历史已退商品总金额
		    $returnGoodsPrice = $ReturnGoodsModel->getReturnGoodsPrice($order_sn,$order_goods_id);
		    if($goods_info['favorable_status']==3){
		        $goods_pay_price = $goods_info['goods_price'] - $goods_info['favorable_price'];
		    }else{
		        $goods_pay_price = $goods_info['goods_price'];
		    }		   
		    $allow_goods_price2 = ($goods_pay_price*1000*0.5 - $returnGoodsPrice*1000)*0.001;
			
		    $allGoodsList = $salesModel->getAppOrderDetailsByOrderId($order_id);
		    $goods_pay_price_all = 0;
		    foreach ($allGoodsList as $g){
		        if($g['is_return']==1){
		            continue;
		        }
		        if($g['favorable_status']==3){
		            $goods_pay_price_all += $g['goods_price'] - $g['favorable_price'];
		        }else{
		            $goods_pay_price_all = $g['goods_price'];
		        }
		    }
		    $allow_goods_price3 = $order_info['money_paid'] - $returnGoodsPriceAll-$goods_pay_price_all*0.5;
		    //$allow_goods_price3 = abs($allow_goods_price3);
		    //1.可申请商品退款金额($allow_goods_price1) <= 订单已支付总金额-订单已申请退款金额
		    //2.可申请商品退款金额($allow_goods_price2) <= 订单商品实际成交价*50% - 历史已退商品总金额
		    //3.可申请商品退款金额($allow_goods_price3) <= 订单已支付金额-订单已申请退款金额-订单总商品实际成交价*50%
		    //以上1，2，3三种情况，根据最大可退款金额的最小值=min($allow_goods_price1,$allow_goods_price2,$allow_goods_price3)输出提示。
		    if($allow_goods_price3 < $allow_goods_price1 && $allow_goods_price3 < $allow_goods_price2){
		        
		        //$apply_amount < 订单总商品实际成交价*50% -(订单已支付金额-订单已申请退款金额)
		        if($allow_goods_price3 <0){
		            $allow_goods_price3 = 0;
		        }
		        if($apply_amount > $allow_goods_price3){
		            $allow_goods_price_desc = "申请退款金额 <= 订单已支付金额-订单已申请退款金额-订单总商品实际成交价*50% = {$allow_goods_price3} = {$order_info['money_paid']} - {$returnGoodsPriceAll} - {$goods_pay_price_all}*0.5";
		            $result ['error'] = "申请退款金额{$apply_amount}不能大于{$allow_goods_price3}元({$allow_goods_price_desc})";
		            Util::jsonExit($result);
		        }
		    }else{
    			
    			if($allow_goods_price1 >= $allow_goods_price2){
    			    //2.可申请商品退款金额($allow_goods_price2) <= 订单商品实际成交价*50% - 历史已退商品总金额
    			    if($apply_amount > $allow_goods_price2){
    			        $allow_goods_price_desc = "可申请商品退款金额 <= 该商品实际成交价*50% - 该商品已退总金额  = {$allow_goods_price2}={$goods_pay_price}*0.5-{$returnGoodsPrice}";
    			        $result ['error'] = "申请退款金额{$apply_amount}不能大于{$allow_goods_price2}元({$allow_goods_price_desc})";
    			        Util::jsonExit($result);
    			    }
    			}else{
    			    //1.可申请商品退款金额($allow_goods_price1) <= 订单已支付总金额-订单已申请退款金额
    			    if($apply_amount > $allow_goods_price1){
    			        $allow_goods_price_desc = "申请退款金额 <= 订单已支付金额-订单已申请退款金额 = {$allow_goods_price1} = {$order_info['money_paid']} - {$returnGoodsPriceAll}";
    			        $result ['error'] = "申请退款金额{$apply_amount}不能大于{$allow_goods_price1}元({$allow_goods_price_desc})";
    			        Util::jsonExit($result);
    			    }
    			} 
		    }  
			
			//$order_goods_id = 0;
            //可申请金额= 已付金额 -（订单总金额-优惠金额）*0.5
            $a = ($order_info['order_amount'] - $order_info['favorable_price'])*0.3;
            $b = $order_info['money_paid']*1;
//            $allow_price = bcsub($b,$a,3);
            $allow_price = ($b*1000-$a*1000)*0.001;
            if($allow_price <= 0){
                $result ['error'] = '该订单已不能再申请退款,订单可申请金额为0 (订单可申请金额 = 已付金额 -（订单总金额-优惠金额）*0.5)';
				Util::jsonExit($result);
            }
            if ($apply_amount > $allow_price) {
                $allow_price -= 0;
		//		$result ['error'] = '申请退款金额不能大于'.$allow_price.'!(可申请金额= 已付金额 -（订单总金额-优惠券金额）*0.5)';
		//		Util::jsonExit($result);
			}
		} else {
			$result ['error'] = '请查询退款方式!';
			Util::jsonExit($result);
		}
		 if($return_by == 1){
		    //校验商品退款金额begin
		    $returnGoodsPrice = $ReturnGoodsModel->getReturnGoodsPrice($order_sn,$order_goods_id);
		    if($goods_info['favorable_status']==3){
		        $goods_pay_price = $goods_info['goods_price'] - $goods_info['favorable_price'];
		    }else{
		        $goods_pay_price = $goods_info['goods_price'];
		    }
		    //可申请退款商品金额 = 实际商品支付金额-历史已退商品金额
		    $allow_goods_price = ($goods_pay_price*1000 - $returnGoodsPrice*1000)*0.001;
		    $allow_goods_price_desc = "可申请退款商品金额 = 实际商品支付金额-历史已退商品总金额  = {$allow_goods_price}={$goods_pay_price}-{$returnGoodsPrice}";
		    	
		    if($apply_amount > $allow_goods_price){
		        $result ['error'] = "申请退款金额{$apply_amount}不能大于{$allow_goods_price}元({$allow_goods_price_desc})";
		        Util::jsonExit($result);
		    }//校验商品退款金额	END	


		} 
	
        $order_id = $order_info['order_id'];
		//验证退款单权限
		//$this->checkMyOrder ( $order_info );
		
		//基本数据
		if ($return_type == '1') {
			$consignee = '';
			$bank_name = '';
			$return_card = '';
			$mobile = '';
			$zuandan_reason_id = _Request::getInt ( 'zuandan_reason_id' );
			$return_res = _Request::getString ( 'return_res' );
			$return_reason = self::$return_reason;
			$return_res = "#" . $return_reason [$zuandan_reason_id] . "##" . $return_res;
		} elseif ($return_type == '2') {
			$consignee = _Request::getString ( 'consignee2', '' );
			$bank_name = _Request::getString ( 'bank_name2', '' );
			$return_card = _Request::getString ( 'return_card2' );
			$mobile = _Request::getString('mobile2');
			$return_res = _Request::getString ( 'return_res2' );
		} elseif ($return_type == '3') {
			$bank_name = '';
			$consignee = _Request::getString ( 'consignee3', '' );
			$return_card = '现金';
			$mobile = _Request::getString ( 'mobile3' );
			$return_res = _Request::getString ( 'return_res3' );
		}
		
		//预定 使用优惠券
		//$this->checkOrderBonus ( $order_info, $apply_amount, true );
		//获取此订单的未退款的商品
		$order_goods = $ReturnGoodsModel->get_order_details_by_order_id_and_is_return ( $order_info ['order_id'] );
                
		//$left_goods_num = count ( $order_goods );
        //获取此订单的相关金额
        
		//$order_allow_left_price = $order_info['order_amount'] - $order_info ['goods_return_price'];
		
		//退商品
		/* if ($return_by == '1') {
			$isAllowReturn = false;
			$order_goods_info = array ();
			foreach ( $order_goods as $og ) {
				//只统计 未进行退款申请的商品
				if ($og ['id'] == $order_goods_id) {
					$order_goods_info = $og;
					$order_allow_left_price -= $og ['goods_price'];
					$isAllowReturn = true;
				}
			} */
			/* $return_order_goods_list = $ReturnGoodsModel->get_return_goods_by_order_goods_id ( $order_goods_id );
			if (! empty ( $return_order_goods_list )) {
				$isReturning = false;
				foreach ( $return_order_goods_list as $r_og ) {
			        $return_order_check_list = $ReturnGoodsModel->get_return_check_by_return_id ( $r_og['return_id'] );
					if (!empty($return_order_check_list)) {
                        if($return_order_check_list['leader_status']!= 2)
                            $isReturning = true;
					}
				}
				if ($isReturning) {
                    $result ['error'] = '订单商品已经进行退款操作!';
                    Util::jsonExit($result);
				}
			}
			if ($isAllowReturn === false) {
                $result ['error'] = '订单商品已经进行退款操作!';
                Util::jsonExit($result);
			} */
/* 			if ($isAllowReturn) {
                if($order_info['order_pay_status']==4){
                }else{

		    if($order_goods_info['favorable_status']==3){
			$goods_price = ($order_goods_info ['goods_price']*1000 - $order_goods_info['favorable_price']*1000)/1000;
	            }else{
                    	$goods_price = $order_goods_info ['goods_price'];
                    }
		    if ($apply_amount > $goods_price) {
                        $result ['error'] = '申请金额大于订单商品成交价!';
                        Util::jsonExit($result);
                    }
                }
			}
			$newmodel =  new AppReturnGoodsModel(32);
			$newmodel->setreturn($order_id);
		} */
		if ($return_type == '1') {
			//$this->checkZuandanAllow ( $left_goods_num, $order_info, $apply_amount, $order_allow_left_price );
		    $newmodel =  new AppReturnGoodsModel(32);
		    $newmodel->setreturn($order_id);
		    
		    $money_paid = $order_info['money_paid'];  //已付款金额
                    $real_return_price = $order_info['real_return_price']; //实退金额
                    $goods_amount = $order_info['goods_amount']; //商品总金额
                    $favorable_price = $order_info['favorable_price']; //商品优惠
                    $t_goods_price = $ReturnGoodsModel->getGoodsPrice($order_info['order_id']); //获取划红线的退款商品信息 
					 
                    $temp_price = 0.00;
                   if(!empty($t_goods_price)){ 
                   foreach ($t_goods_price as $k => $val){ 
                   $temp_price += $val['goods_price']; 
                     }
                   } 
                    //$temp_price = number_format($temp_price,2);  
					 
                     $t_price = $temp_price; // 实退商品金额
                    $t_favorable_price = $ReturnGoodsModel->getReturnGoodsfavor($order_info['order_id']);
				    
                    $t_favorable_price =  $t_favorable_price  ?  $t_favorable_price : 0.00; //扣除商品优惠金额
					 
                    $tuihuo_price = $ReturnGoodsModel->getNewReturn($order_goods_id,'goods_price'); // 退货商品金额
					 
		            $tuihuo_price = $tuihuo_price ?  $tuihuo_price  : 0.00;
					 
                    $tuihuo_favorable_price = $ReturnGoodsModel->getNewReturn($order_goods_id,'favorable_price');//退货商品优惠
					$tuihuo_favorable_price = $tuihuo_favorable_price ?  $tuihuo_favorable_price  : 0.00;
                  //退款金额<=已付金额-实退金额- ((商品总金额-商品总优惠-(实退商品金额-扣除商品优惠金额)-(退货商品金额-退货商品优惠))*0.3)
                  //  $shouldPrice = $money_paid-$real_return_price-(($goods_amount-$favorable_price-($t_price-$t_favorable_price)-($tuihuo_price-$tuihuo_favorable_price))* 0.3);
	    	  // echo "$money_paid-$real_return_price-(($goods_amount-$favorable_price-($t_price-$t_favorable_price)-($tuihuo_price-$tuihuo_favorable_price))* 0.3);";
				//11803.00-2077.00-((21803.00-0.00-(2,077.00-0)-(18637.00-0))* 0.3);
		 
				 
                 $price_1 = $money_paid-$real_return_price;
				 
                 $price_2 = $goods_amount-$favorable_price;
				 
                 $price_3 = $t_price-$t_favorable_price;	
				 
                 $price_4 = $tuihuo_price-$tuihuo_favorable_price;
				 
               	 $price_5 = $price_2 - $price_3-$price_4;
				  
				 $price_6 = $price_5 * 0.5;
			 
				 $shouldPrice = $price_1-$price_6;
				 /*
                     if($shouldPrice == 0){
                        $result ['error'] = '该订单已不能再申请退款';
                        Util::jsonExit($result);
                    }
                    */
                   if(bccomp($apply_amount,$shouldPrice)==1){
                     $result ['error'] = '退款金额'.$apply_amount.'不能大于'.$shouldPrice.'(金额=已付金额-实退金额- ((商品总金额-商品总优惠-(实退商品金额-扣除商品优惠金额)-(退货商品金额-退货商品优惠))的50%)!';
                        Util::jsonExit($result);
                    }
			
			
		}
		//实退金额、应退金额
		$real_return_amount = $apply_amount - $price_fee;
		
		
		$SalesModel=new SalesModel(27);
		$orderInfo=$SalesModel->GetOrderInfoById($order_id);
		$referer=$orderInfo['referer'];
		$order_sn=$orderInfo['order_sn'];
		if($referer=='天生一对加盟商'){
			//天生一对订单通过时需要都需要做一个判断，0<本次实退金额=<已付金额-实退金额-已配货商品的批发价-非已配货商品的（【原始零售价】*30%）才允许通过
			
			if($real_return_amount <= 0){
				$result['error'] = '实退金额必须大于0';
				Util::jsonExit($result);
			}
			$orderAccountInfo=$SalesModel->getOrderAccountInfoByOrderId($order_id);
			$money_paid=$orderAccountInfo['money_paid'];//已付金额
			$real_return_price1=$orderAccountInfo['real_return_price'];//实退金额
		
			
			//订单已配货商品的批发价
			$WarehouseModel=new WarehouseModel(21);
			$pfj=$WarehouseModel->getGoodsPfj($order_id);
			//非已配货商品的（【原始零售价】*30%）
			$retail_price=$SalesModel->getOrderDetailRetailPriceByOrderId($order_id);
			if($real_return_amount > $money_paid-$real_return_price1-$pfj-$retail_price*0.3){
				$result['error'] = '本次实退金额=<已付金额-实退金额-已配货商品的批发价-非已配货商品的（【原始零售价】*30%）才允许通过';
				Util::jsonExit($result);
			}
			
			/* $a=$money_paid-$real_return_price1-$pfj-$retail_price*0.3;
			$result['error'] = $a;
			Util::jsonExit($result); */
		}
       
		$olddo=array();
		$return_order = array ();
		$return_order ['department'] = $order_info ['department_id'];
		$return_order ['apply_user_id'] = $_SESSION ["userId"];
		$return_order ['order_id'] = $order_id;
		$return_order ['order_sn'] = $order_sn;
		$return_order ['order_goods_id'] = $order_goods_id; //有商品
		$return_order ['should_return_amount'] = $real_return_amount;
		$return_order ['apply_return_amount'] = $apply_amount;
		$return_order ['real_return_amount'] = $real_return_amount;
		$return_order ['confirm_price'] = $price_fee;
		
		$return_order ['return_res'] = $return_res;
		$return_order ['return_type'] = $return_type;
		$return_order ['return_card'] = $return_card;
		$return_order ['consignee'] = $consignee;
		$return_order ['mobile'] = $mobile;
		$return_order ['bank_name'] = $bank_name;
		
		$return_order ['apply_time'] = date ( "Y-m-d H:i:s" );
		$return_order ['zhuandan_amount'] = 0;
		$return_order['return_by'] = $return_by;
		
		$newmodel =  new AppReturnGoodsModel(32);
		$res = $newmodel->saveData($return_order,$olddo);
		//$id = $returngoodsModel->add_return_goods ( $return_order );
		unset ( $return_order );
		if($res !== false)
		{
            $lsh = '';
            $lsh = $res;//流水号
			// 确保设置为退款状态
			$newmodel->setreturn($order_id);
			//订单退款日志
			$orderModel = new ApiRefundModel();
			$logInfo = [
				'order_id'=>$order_id,
				'order_status'=>$order_info['order_status'],
				'shipping_status'=>$order_info['send_good_status'],
				'pay_status'=>$order_info['order_pay_status'],
				'create_user'=>$_SESSION['userName'],
				'remark'=>'退款：'.$real_return_amount.'元/退款流水号：'.$lsh
			];
			//写入订单日志
			$orderModel->mkOrderInfoLog($logInfo);

			$result['success'] = 1;
			$result['content'] = "申请成功";
			$result['tab_id'] = _Post::getInt('tab_id');
		}
		else
		{
			$result['error'] = '添加失败';
		}
		Util::jsonExit($result);
	}
    



    
    /**
	 *
	 * 转单权限验证
	 * @param array $order_info
	 * @param float $apply_amount
	 * @param float $total_amount
	 * @param int $left_goods_num
	 */
	public function checkZuandanAllow($left_goods_num, $order_account, $apply_amount, $goods_price) {
		//退商品
		//支付百分比 现在不进行退款百分比(支付都已经优惠了，退款不优惠)
		//$discount=ConfigModel::get_pay_role();
		$discount = 0.5;
		//订单里只有一个商品的时候不做该判断操作
		//($order_info ['money_paid'] - $order_info ['bonus'] - $apply_amount < $goods_price * $discount)) 
		if ($left_goods_num > 0 && ($order_account ['money_paid'] - $apply_amount < $goods_price * $discount)) {
			$p = $discount * 100;
            $result ['error'] = '余款不足支付订单的' . $p . '%，不能转单!!';
			Util::jsonExit($result);
		}
	}
}

?>
