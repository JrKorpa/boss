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
class LeaderCheckController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $this->render('leader_check_search_form.html',array('view'=>new AppReturnGoodsView(new AppReturnGoodsModel(31)),'bar'=>Auth::getBar()));
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
        if(!empty($args['order_sn'])){
            $main_cert_no=$args['order_sn'];
            $args['order_sn']=preg_replace("/[sv]+/",',',$args['order_sn']);
            $args['order_sn']=str_replace(" ",',',$args['order_sn']);
            $args['order_sn']=str_replace("，",',',$args['order_sn']);
            $args['order_sn']=str_replace(",",',',$args['order_sn']);
            $item=explode(",",$args['order_sn']);
            $order_sn =[];
            foreach($item as $key => $val) {
                if ($val != '') {
                    $order_sn[]="'".$val."'";
                }
            }

            $args['order_sn']=implode(',',$order_sn);

        }
        $pageSize = _Request::getInt("page_size",10);
		$page = _Request::getInt("page",1);
		$where = array(
            'check_status' => 1,
            'return_id'=>$args['return_id'],
            'order_sn'=>$args['order_sn'],
            'return_type'=>$args['return_type'],
            'start_time'=>$args['start_time'],
            'end_time'=>$args['end_time'],
            'department'=>$args['department'],
        );

		$model = new AppReturnGoodsModel(31);
		$data = $model->pageList($where,$page,$pageSize,false);
        $pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'leader_check_search_page';
		$this->render('leader_check_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	check，主管审核
	 */
	public function check ($params)
	{
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
        	die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }			
		$ids = _Request::getList("_ids");
		$result = array('title'=>'主管审核','content'=>'');
		if(empty($ids)){
		    $result['content'] = "ids is empty！";
		    Util::jsonExit($result);
		}
		
        $model = new AppReturnCheckModel(31);
        $error_ids= array();
        foreach ($ids as $id){
            $is_check = $model->getCheckIdForLeader($id);
            if($is_check){
                $error_ids[]= $id;
                continue;
            }
        }
        if(empty($error_ids)){
            $ids = implode('|',$ids);
            $result['content'] = $this->fetch('leader_check_info.html',array(
                'ids'=>$ids                
            ));
        }else{
            $error_ids = implode(',',$error_ids); 
            $result['content'] = "流水号为{$error_ids}的退款记录主管已操作过！";
        }
		Util::jsonExit($result);
	}
    
	/**
	 *	审核、驳回
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$return_ids =  _Post::getString('return_ids');
		$return_ids = explode('|',$return_ids);
        $leader_res = _Post::getString('leader_res');
        if(empty($return_ids)){
            $result['error'] = '退款单流水号为空！';
            Util::jsonExit($result);
        }
        if(empty($leader_res)){
            $result['error'] = '业务负责人意见不能为空！';
            Util::jsonExit($result);
        }
        $leader_status = _Post::getInt('status');
        if($leader_status < 1){
            $result['error'] = '审核意见不能为空！';
            Util::jsonExit($result);
        }
        $returnGoodsModel = new AppReturnGoodsModel (32);
        $pdolist[32] = $returnGoodsModel->db()->db();
        try{
            foreach ($pdolist as $pdo){
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
                $pdo->beginTransaction(); //开启事务
            }
        }catch (Exception $e){
            $error = "操作失败，事物回滚！提示：系统批量开启事物时发生异常！";
            Util::rollbackExit($error,$pdolist);
        }
        
		foreach ($return_ids as $return_id){		    
		   $res = $this->leaderCheckOne($return_id, $leader_status, $leader_res);
		   if($res['success']==0){
		       $error = "流水号【{$return_id}】主管审核失败,提示：".$res['error'];
		       Util::rollbackExit($error,$pdolist);
		   }		    
		}
        
		try{
		    //Util::rollbackExit('test',$pdolist);
		    //批量提交事物
		    foreach ($pdolist as $pdo){
		        $pdo->commit();
		        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
		    }
		    $result['success'] = 1;
		    Util::jsonExit($result);
		}catch (Exception $e){
		    $error = "审核失败!".$e->getMessage();
		    Util::rollbackExit($error,$pdolist);
		}		
	}
	
	//主管审核
	public function leaderCheckOne($return_id,$leader_status,$leader_res)
	{
	    $result = array('success' => 0,'error' =>'');
	
	    $id = $return_id;
	    $salesModel = new SalesModel(32);
	    $returnGoodsModel = new AppReturnGoodsModel($id,32);
	    $do = $returnGoodsModel->getDataObject();
	    if(empty($do)){
	        $result['error'] = "流水号为【{$id}】的退款单不存在！";
	        return $result;
	    }
	    $order_goods_id = $do['order_goods_id'];
	    $order_sn = $do['order_sn'];
	    $order_info = $returnGoodsModel->get_order_info_by_order_sn($order_sn);
	    $order_id = $order_info['order_id'];
	    if(empty($order_info)){
	        $result['error'] = "流水号为【{$id}】的退款单未关联订单！";
	        return $result;
	    }

	    $newdo = array();
	    if ($leader_status == 2) {
	        // 主管审核:拒绝
	        $is_or_no = '驳回';
	        $_tuiGoods = '';
	        $newdo ['leader_res'] = $leader_res;
	        $newdo ['return_id'] = $id;
	        $newdo ['leader_id'] = $_SESSION ['userId'];
	        $newdo ['leader_status'] = $leader_status;
	        $newdo ['leader_time'] = date ( "Y-m-d H:i:s");

	        $res2=$returnGoodsModel->getReturnGoodsByWhere1($do['order_id'],$id);
	    
	        if(!$res2){
	            $returnGoodsModel->returnapply($do ['order_id']);
	        }

	    }else{    	    
    	    $_is_add_logs = 0;
    	    $is_or_no = '通过';
    	    $_tuiGoods = '';
    	    if (round(floatval($order_info['money_paid']-$order_info['real_return_price']),3) < round(floatval($do['apply_return_amount']),3)) {
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
    	    //重置库管审核字段
    	    $newdo ['goods_res'] = null;
    	    $newdo ['goods_comfirm_id'] = null;
    	    $newdo ['goods_status'] = 0;
    	    $newdo ['goods_time'] = null;
    	    
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
    	        //$order_goods = $salesModel->getAppOrderDetailsByOrderId($do['order_id']);
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
	    } //end if($leader_status == 2) { 
    	
	    $checkModel = new AppReturnCheckModel(32);
	    $checkInfo = $checkModel->getAppReturnCheckByReturnId($id,'id');
	    if(empty($checkInfo)){
	        $res = $checkModel->saveData($newdo,array());
	    }else{
	        $res = $checkModel->update($newdo, 'id='.$checkInfo['id']);
	    }
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
	    if ($do ["return_by"] == 2 && $leader_status == 1) {
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
	

}

?>
