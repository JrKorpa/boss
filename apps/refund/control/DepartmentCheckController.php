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
class DepartmentCheckController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('department_check_search_form.html',array('view'=>new AppReturnGoodsView(new AppReturnGoodsModel(31)),'bar'=>Auth::getBar()));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
        if($_SESSION['userType']==1){
            $department = _Request::getInt('department')?_Request::getInt('department'):0;
        }else{
            if(isset($_REQUEST['department'])){
                $department = _Request::getInt('department')?_Request::getInt('department'):($_SESSION['qudao']?$_SESSION['qudao']:-1);
            }else{
                $department = _Request::getInt('department')?_Request::getInt('department'):($_SESSION['qudao']?current(explode(',', $_SESSION['qudao'])):-1);
            }
        }
		$args = array(
            'mod'   => _Request::get("mod"),
            'con'   => substr(__CLASS__, 0, -10),
            'act'   => __FUNCTION__,
			'return_id'	=> _Request::getInt("return_id"),
			'order_sn'	=> _Request::getString("order_sn"),
			'return_type'	=> _Request::getInt("return_type"),
			'start_time'	=> _Request::getString("start_time"),
			'end_time'	=> _Request::getString("end_time"),
			'department'	=> $department,
		);
		$page = _Request::getInt("page",1);
		$where = array(
            'check_status'=>4,
            'return_id'=>$args['return_id'],
            'order_sn'=>$args['order_sn'],
            'return_type'=>$args['return_type'],
            'start_time'=>$args['start_time'],
            'end_time'=>$args['end_time'],
            'department'=>$args['department'],
			
		);

                if($args['return_id']){
                        $where =array();
                        $where['return_id']=$args['return_id'];
                }

		$model = new AppReturnGoodsModel(31);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'department_check_search_page';
		$this->render('department_check_search_list.html',array(
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
		$result['content'] = $this->fetch('department_check_info.html',array(
			'view'=>new AppReturnGoodsView(new AppReturnGoodsModel(31))
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function editer ($params)
	{
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
        	die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }			
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
        $_model = new AppReturnGoodsModel($id,31);
        $do = $_model->getDataObject();
        $model = new AppReturnCheckModel(31);
        $is_check = $model->getCheckId($id);
        $a=new AppReturnCheckView(new AppReturnCheckModel(31));
     
        if($do['check_status'] != 3){
            $result['content'] = "该条退款记录事业部负责人还未操作";
        }elseif(!$is_check){
            $result['content'] = "该条退款记录现场财务已操作";
        }else {
            $result['content'] = $this->fetch('department_check_info.html',
            		array('id'=>$id ));
			
        }
		$result['title'] = '现场财务审核';
		Util::jsonExit($result);
	}
	

	
	 /**
     * 	update，更新信息
     */
    public function update($params) {
        $result = array('success' => 0, 'error' => '');
        /* 接参 */
        $check_status = _Post::getInt('check_status');
        //var_dump($check_status);exit;
        if ($check_status === null) {
            $result['error'] = '现场财务操作状态不能为空！';
            Util::jsonExit($result);
        }
        $deparment_finance_res = _Post::getstring('deparment_finance_res');
        if (empty($deparment_finance_res)) {
            $result['error'] = '现场财务操作备注不能为空！';
            Util::jsonExit($result);
        }

        $return_id = _Post::getInt('return_id');

        $deparment_id = $_SESSION['userId'];

        $check_user = $_SESSION['userName'];

        $check_time = date("Y-m-d H:i:s");

        /* 实例化 */
        $newmodel = new AppReturnGoodsModel($return_id, 32);
        $order_id = $newmodel->getvalue('order_id');

        $do = $newmodel->getDataObject();
        if (count($do) < 1) {
            $result['error'] = '退款申请不存在！';
            Util::jsonExit($result);
        }
        $checkmodel = new AppReturnCheckModel(32);
        $logmodel = new AppReturnLogModel(32);

       
         
        
    
		$salesModel = new SalesModel(27);//销售模块Model
		$salesModel->updateOrderIsZp($do['order_id']);//更改订单是否为赠品订单状态
       
        $res=$salesModel->updateOrderAccountRealReturnPrice($return_id);          



        /* 操作app_return_check表 */
        $checkid = $checkmodel->getCheckId($return_id);
        /* 取id */
        if (!empty($checkid)) {
            $checkmodel = new AppReturnCheckModel($checkid, 32);
            $olddo = $checkmodel->getDataObject();
            $checkdo = array(
                'id' => $checkid,
                'deparment_finance_id' => $_SESSION['userId'],
                'deparment_finance_status' => $check_status,
                'deparment_finance_res' => $deparment_finance_res,
                'deparment_finance_time' => $check_time,
            );

            //var_dump($checkdo);die;
            //echo $check;die;
            $res = $checkmodel->saveData($checkdo, $olddo);
        } else {

            $result['error'] = '修改失败';
            Util::jsonExit($result);
        }
        
       
        
        /* 操作app_return_goods表 */
        if ($check_status == 1) {
            $newdo = array(
                'check_status' => 4,
                'return_id' => $return_id,
            );
        } else {
            $newdo = array(
                'check_status' => 2,
                'return_id' => $return_id,
            );
        }
        $res = $newmodel->saveData($newdo, $do);
       
        //订单退款状态结束 
        if ($res) {       	 
            $order_id = $newmodel->getvalue('order_id');
            $res2=$newmodel->getReturnGoodsByWhere($order_id);    
            if(!$res2){     
              $newmodel->returnapply($order_id);
            }
        }        
		

        if ($res) {
            //订单操作日志
            $apiModel = new ApiRefundModel();
            $order_info = $apiModel->GetExistOrderSn($do['order_sn']);
            $insert_action = array();
            $insert_action ['order_id'] = $order_info ['id'];
            $insert_action ['order_status'] = $order_info ['order_status'];
            $insert_action ['shipping_status'] = $order_info ['send_good_status'];
            $insert_action ['pay_status'] = $order_info ['order_pay_status'];
            $insert_action ['remark'] = '退款/退货单'.$return_id.':现场财务已经审核';
            $insert_action ['create_user'] = $_SESSION ['userName'];
            $insert_action ['create_time'] = date('Y-m-d H:i:s');
            $res = $apiModel->AddOrderActionInfo($insert_action);

            $SalesModel=new SalesModel(27);
            $orderInfo = $SalesModel->GetOrderInfoById($order_id);
            if($orderInfo['send_good_status'] == 2 && $orderInfo['is_zp'] != 1){
                    $SalesModel->app_return_point_add($return_id);
            }

            $result['success'] = 1;
            if($res){            	
              $salesModel = new SalesModel(28);
              
	              $order_sn=$do['order_sn'];
	              $new_order_info = $salesModel->GetOrderInfo($order_sn);
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
	              					
	              			$result['error'] ="更改订单状态失败";
	              			Util::jsonExit($result);
	              		}
	              	}
	              	 
	              
	              }else{
	              	//只更改布产状态不更改订单状态
	              	$res4=$salesModel->EditOrderStatus($order_sn);
	              	if(!$res4){
	              		
	              		$result['error'] ="更改布产状态失败";
	              		Util::jsonExit($result);
	              	}
	              	 
	              }

                    if(in_array($new_order_info['delivery_status'],array(2,3,4))){
                        if(empty($salesModel->getUnReturnGoods($order_sn))){
                        	$salesModel->updateUnDeliveryStatus($order_sn);
                        }
                    }
	              
	             
	              $res5=$salesModel->EditOrderdexianhuoStatus($order_sn,$xianhuo);
	              
	              if(!$res5){	              	
	              	
	              	$result['error'] ="更改订单类型失败";
	              	Util::jsonExit($result);
	                }
             
               
              
            }
            
	         /* 操作app_return_log表 */
	        $logdo = array(
	            'return_id' => $return_id,
	            'even_time' => $check_time,
	            'even_user' => $check_user,
	            'even_content' => '现场财务意见' . $deparment_finance_res,
	        );		
	        $res = $logmodel->saveData($logdo, array());
	           
        } else {
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
	public function updateOrderAccountRealReturnPrice($param) {
	
	    $s_time = microtime();
	    $result = array('success' => 0, 'error' => '');
	    if(empty($param['order_id']) || empty($param['apply_return_amount'])){
	        $result['error'] = "订单号order_sn不能为空或更新的数据不能为空";
	        Util::jsonExit($result);
	    }
	    $model = new AppReturnCheckModel(32);
	
	    $order_account = $model->get_order_account($param['order_id']);
	
	    if(empty($order_account)){
	        $result['error'] = "没有该订单";
	        Util::jsonExit($result);
	    }
	    if(bccomp($order_account['money_paid'],$param['apply_return_amount'],5)<0){
	        $result['error'] = "退款金额不能大于订单已付款金额";
	        Util::jsonExit($result);
	    }
	     
	    //$order_all = $model->get_order_all($param['order_id']);
	    $return_info = $model->get_return($param['return_id']);
	    //$real_return_price = $model->get_real_retun_good($param['order_id']); //实退金额
	    //$order_detail = $model->get_order_detail($return_info['order_goods_id']); //
	    // $is_return = $order_detail['is_return'];
	    //$return_all = $model->get_app_retun_good($param['order_id']); //已经退款的商品
	    $return_all = $model->get_order_detail($return_info['order_goods_id']);
	
	    $price = 0;
	    if(!empty($return_all)){
	        foreach ($return_all as $k => $v){
	            if ($v['favorable_status'] == 3){
	                $p = $v['goods_price'] - $v['favorable_price'];
	                $price  +=  $p;
	            }
	            else {
	                $p = $v['goods_price'];
	                $price  +=  $p;
	            }
	        }
	    }
	    if(isset($param['order_goods_id']) && $param['order_goods_id']>0 && $param['apply_return_amount']>$price){
	        $result['error'] = "退款金额不能大于订单商品实际成交金额";
	        Util::jsonExit($result);
	
	    }
	     
	
	    //$money_unpaid = $order_amount - $order_account['money_paid'] + $real_return_price; //
	    $money_unpaid=bcadd($order_account['money_unpaid'],$param['apply_return_amount'],3);
	    //if ($money_unpaid < 0)
	    //    $money_unpaid = 0;
	
	    $real_return_price= bcadd($order_account['real_return_price'],$param['apply_return_amount'],3);
	
	    if($return_info['return_by']==1){
	        $order_amount = bcsub($order_account['order_amount'], $price,3); //订单总金额
	        $money_unpaid = bcsub($money_unpaid, $price,3);
	        $set = "`real_return_price`=$real_return_price,`money_unpaid`=$money_unpaid,`order_amount`=$order_amount";
	    }else{
	        $set = "`real_return_price`=$real_return_price,`money_unpaid`=$money_unpaid ";
	    }
	     
	    $sql = "UPDATE `app_order_account` SET $set WHERE `order_id`={$param['order_id']}";
	
	    $res =$model->db()-> query($sql);
	    	
	}
	
	public function updateOrderAccountRealReturnPrice2($param) {
	    $s_time = microtime();
	    $result = array('success' => 0, 'error' => '');
	    if(empty($param['order_id']) || empty($param['real_return_price'])){
	        $result['error'] = "订单号order_sn不能为空或更新的数据不能为空";
	        Util::jsonExit($result);
	    }
	    $model = new AppReturnCheckModel(32);
	
	    $order_account = $model->get_order_account($param['order_id']);
	
	    if(empty($order_account)){
	        $result['error'] = "没有该订单";
	        Util::jsonExit($result);
	    }
	    // $order_all = $model->get_order_all($param['order_id']);
	    $return_info = $model->get_return($param['return_id']);
	    $real_return_amount = $model->get_real_retun_good($param['order_id']); //实退金额

	    //所有财务已审核的退款总金额
	    $order_amount = bcsub($order_account['n_goods_amount'],$real_return_amount); //订单总金额
	    if($order_amount<0){
	        $result['error'] = "退款金额不能大于订单总金额";
	        Util::jsonExit($result);
	    }
	    //$real_return_price =$return_info['real_return_amount']+$order_account['real_return_amount'];
	    $money_unpaid = $order_amount - $order_account['money_paid'] + $real_return_amount; //
	    if ($money_unpaid < 0) $money_unpaid = 0;
	     
	    // $set = "`real_return_price`=$real_return_price,`money_unpaid`=$money_unpaid,`order_amount`=$order_amount";
	    $set = "`real_return_price`=$real_return_amount,`money_unpaid`=$money_unpaid,`order_amount`=$order_amount";
	     
	    $sql = "UPDATE `app_order_account` SET $set WHERE `order_id`={$param['order_id']}";
	    //$result['error'] =  $sql;
	    //Util::jsonExit($result);
	    $res =$model->db()-> query($sql);
	    	
	}
	 
    /**
     * 更新订单实退金额
     */
	public function updateOrderAccountRealReturnPrice3($param) {
	    $s_time = microtime();
	    $result = array('success' => 0, 'error' => '');
	    if(empty($param['order_id']) || empty($param['real_return_price'])){
	        $result['error'] = "订单号order_sn不能为空或更新的数据不能为空";
	        Util::jsonExit($result);
	    }
	    $model = new AppReturnCheckModel(32);
	
	    $order_account = $model->get_order_account($param['order_id']);
	
	    if(empty($order_account)){
	        $result['error'] = "没有该订单";
	        Util::jsonExit($result);
	    }
	    $order_all = $model->get_order_all($param['order_id']);
	
	    $return_info = $model->get_return($param['return_id']);
	     
	    $return_all = $model->get_app_retun_good($param['order_id']); //已经退款的商品
	    $real_return_price = $model->get_real_retun_good($param['order_id']); //实退金额
	    $order_detail = $model->get_order_detail($return_info['order_goods_id']); //
	    // $is_return = $order_detail['is_return'];
	     
	    $price = 0;
	    if($return_all){
	        foreach ($return_all as $k => $v){
	            if ($v['favorable_status'] == 3){
	                $p = $v['goods_price'] - $v['favorable_price'];
	                $price  +=  $p;
	            }
	            else {
	                $p = $v['goods_price'];
	                $price  +=  $p;
	            }
	        }
	    }
	     
	    if($return_info['return_by']==1){
	        $order_amount = bcsub($order_account['n_goods_amount'], $price); //订单总金额
	    }else{
	        $order_amount = bcsub($order_account['n_goods_amount'], $real_return_price); //订单总金额
	    }
	    //$order_amount = bcsub($order_account['n_goods_amount'], $real_return_price); //订单总金额
	    if($order_amount<0){
	        $result['error'] = "退款金额不能大于订单总金额";
	        Util::jsonExit($result);
	    }
	    //   $real_return_price =$return_info['real_return_amount']+$order_account['real_return_amount'];
	    $money_unpaid = $order_amount - $order_account['money_paid'] + $real_return_price; //
	    //if ($money_unpaid < 0) $money_unpaid = 0;
	     
	    $set = "`real_return_price`=$real_return_price,`money_unpaid`=$money_unpaid,`order_amount`=$order_amount";
	
	    $sql = "UPDATE `app_order_account` SET $set WHERE `order_id`={$param['order_id']}";
	
	    $res =$model->db()-> query($sql);
	    	
	}
    
}