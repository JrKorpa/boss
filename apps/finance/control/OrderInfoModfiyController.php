<?php
/**
 *  -------------------------------------------------
 *   @file		: OrderInfoModfiyController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 11:04:15
 *   @update	:
 *
 *  -------------------------------------------------
 */
class OrderInfoModfiyController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{

        $user_company_id=$_SESSION['companyId'];
        $this->render('order_info_modfiy_info.html',array('user_company_id'=>$user_company_id));
        exit;
	}
	
	/**
	 *	repay
	 */
	public function repay ($params)
	{
        $res = array('error'=>1,'msg'=>'');
		$order_sn = _Post::getString('order_sn');
        if(empty($order_sn)){
			$res['msg'] ="订单号为空，请重新输入";
			Util::jsonExit($res);
		}
        
		$keys=array('order_sn');
		$vals=array($order_sn);
		$orderInfo=ApiModel::sales_api($keys,$vals,'GetOrderInfo');
     
        if(empty($orderInfo)){
			$res['msg'] ="订单号".$order_sn."不存在，请重新输入";
			Util::jsonExit($res);
		}
        if($orderInfo['delivery_status']==5 ||  $orderInfo['send_good_status']==2){
			$res['msg'] ="订单号".$order_sn."已配货或者已发货，不能取消点款";
			Util::jsonExit($res);
		}

		if(SYS_SCOPE=='zhanting' && !Auth::user_is_from_base_company()){
	        $user_company_model=new UserCompanyModel(1);
	        $user_company_list=$user_company_model->getUserCompanyList(array('user_id'=>$_SESSION['userId']));
	        if($user_company_list){
                 $user_company_list=array_column($user_company_list,'company_id');
                 $sale_channel_model=new SalesChannelsModel(1);
                 $channel_list=$sale_channel_model->getSalesChannel(array($orderInfo['department_id']));
                 if($channel_list){                                	
                     if(!empty($channel_list[0]['company_id'])){
                        if(!in_array($channel_list[0]['company_id'],$user_company_list)) {
							$res['msg'] ="没有订单所属渠道的公司权限";
							Util::jsonExit($res);
                        }
                     }else{ 
						$res['msg'] ="未设置订单所属渠道公司";
						Util::jsonExit($res); 
                     }
                 }else{
						$res['msg'] ="未找到订单所属渠道";
						Util::jsonExit($res);	                 	
                 }
	        }else{
				$res['msg'] ="用户没有任何公司权限";
				Util::jsonExit($res);	        	
	        }       

		}
		
        //处理定金点款数据
		$model = new AppReceiptDepositModel(29);
        $arr = $model->getInfoByOrderSn($order_sn);
        
        if(!empty($arr)){
            foreach($arr as $val){
                //更新定金为初始
                $model->updateDepositInfo($val['id']);
                //记录日志
                $_model = new AppReceiptDepositLogModel(30);
                $_newdo = array();
                $_newdo['receipt_id'] = $val['id'];
                $_newdo['receipt_action'] = '重新点款';
                $_newdo['add_time'] = date("Y-m-d H:i:s");
                $_newdo['add_user'] = $_SESSION['userName'];
                $_model->saveData($_newdo, array());
            }
        }
        
        //处理点款数据
        $pay_model = new AppReceiptPayModel(30);
        $_arr = $pay_model->getInfoByOrderSn($order_sn);
        if(!empty($_arr)){
            foreach($_arr as $val){
                //更新点款记录为作废
                $pay_model->updatePayInfo($val['id']);
                //记录日志
                $log_model = new AppReceiptPayLogModel(30);
                $_newarr = array();
                $_newarr['receipt_id'] = $val['id'];
                $_newarr['receipt_action'] = '作废';
                $_newarr['add_time'] = date("Y-m-d H:i:s");
                $_newarr['add_user'] = $_SESSION['userName'];
                $log_model->saveData($_newarr, array());
            }
        }
        
        //删除该订单点款记录
        //$pay_model->deletePayInfo($order_sn);
        //删除该订单提报记录
        $AppOrderPayActionModel = new AppOrderPayActionModel(30);
        $AppOrderPayActionModel->deleteOrderPayInfo($orderInfo['id']);
        //更新订单金额数据
        $keys=array('order_sn');
		$vals=array($order_sn);
		//$arr = ApiModel::sales_api($keys,$vals,'updateAccountInfo');
		//如果销售订单配货状态是【已配货】，不需要变更订单的 配货状态；如果 销售订单配货状态是【未配货、配货中、允许配货】（无效和配货缺货不知道有没有用），需要将订单配货状态更新为【未配货】
		
		if($orderInfo['delivery_status']==5){
			$arr = ApiModel::sales_api($keys,$vals,'updateAccountInfo_not_delivery_status');
		}
		else {
			$arr = ApiModel::sales_api($keys,$vals,'updateAccountInfo');
		}
		
        $_data = ApiModel::sales_api(array('order_id','select'),array($orderInfo['id'],'*'),'getOrderDetailByOrderId');
        if($_data){
            foreach ($_data as $val){
                ApiModel::sale_policy_api(array('update_data'), array(array(array('goods_id'=>$val['goods_id'],'is_sale'=>1,'is_valid'=>1))), 'UpdateAppPayDetail');
            }
        }
       
        if($arr!=FALSE){
        	//天生一对订单明细配货状态更新
        	$SalesModel=new SalesModel(28);
        	$re=$SalesModel->UpdateOrderDetailDeliveryStatus($order_sn);        	
        	//记录订单日志
            $keys = array('order_id','remark','create_user','create_time','order_status','shipping_status','pay_status');
            $vals = array($orderInfo['id'],'恢复支付操作',$_SESSION['userName'],date("Y-m-d H:i:s"),$orderInfo['order_status'],$orderInfo['send_good_status'],1);
            ApiModel::sales_api($keys,$vals,'addOrderAction');
            $res['error'] = 0;
        }else{
            $res['msg'] = "更新失败！";
        }
        Util::jsonExit($res);
	}
	
	/**
	 *	reback
	 */
	public function reback ($params)
	{
		$res = array('error'=>1,'msg'=>'');
		$order_sn = _Post::getString('order_sn');
        if(empty($order_sn)){
			$res['msg'] ="订单号".$order_sn."不存在，请重新输入";
			Util::jsonExit($res);
		}
        
		$keys=array('order_sn');
		$vals=array($order_sn);
		$orderInfo=ApiModel::sales_api($keys,$vals,'GetOrderInfo');
        if(empty($orderInfo)){
			$res['msg'] ="订单号".$order_sn."不存在，请重新输入";
			Util::jsonExit($res);
		}
        
		$returnGoodsInfo=ApiModel::sales_api($keys,$vals,'getReturnGoodsInfo');
        if($returnGoodsInfo!='failed'){
            if($returnGoodsInfo['return_type']==1){
                $res['msg'] ="该订单 ".$order_sn." 已经被转走了，不能退款！";
                Util::jsonExit($res);
            }
        }
        
        //删除退款操作
        $_res = ApiModel::sales_api(array('order_sn'),array($order_sn),'deleteReturnGoodsInfo');
        //更新订单的商品退货状态改为初始状态
        $arr = ApiModel::sales_api(array('order_sn'),array($order_sn),'updateOrderDetailByOrderSn');
        //更新订单实退金额
		$keys=array('order_sn','order_id');
		$vals=array($order_sn,$orderInfo['id']);
        $_arr = ApiModel::sales_api($keys,$vals,'modfiyAccountInfo');
        if($_arr == 'failed'){
            $res['msg'] = "金额更新失败！";
            Util::jsonExit($res);
        }
        
        if($arr!=FALSE){
            $keys = array('order_id','remark','create_user','create_time','order_status','shipping_status','pay_status');
            $vals = array($orderInfo['id'],'恢复退款操作',$_SESSION['userName'],date("Y-m-d H:i:s"),$orderInfo['order_status'],$orderInfo['send_good_status'],$orderInfo['order_pay_status']);
            ApiModel::sales_api($keys,$vals,'addOrderAction');
            $res['error'] = 0;
        }else{
            $res['msg'] = "更新失败！";
        }
        
        Util::jsonExit($res);
		
	}

	/**
	 *	取消申请关闭
	 */
	public function reapplycolse ($params)
	{
		$res = array('error'=>1,'msg'=>'');
		$order_sn = _Post::getString('order_sn');
		$keys=array('order_sn','apply_close');
		$vals=array($order_sn,0);
		$orderInfo=ApiModel::sales_api($keys,$vals,'UpdateOrderInfoModiy');

        if(isset($orderInfo) && ($orderInfo==0 || $orderInfo=='NULL')){
			$res['error'] =0;
			$res['msg'] ="订单号 ".$order_sn." 不存在，请重新输入";
			Util::jsonExit($res);
		}elseif($orderInfo['apply_close']==0){
 			$res['error'] =0;
			$res['msg'] ="此订单已是未申请状态，无需修改！";
			Util::jsonExit($res);           
        }

		$keys=array('order_id','order_status','pay_status','shipping_status','remark','create_time','create_user');
		$vals=array($orderInfo['id'],$orderInfo['order_status'],$orderInfo['order_pay_status'],$orderInfo['send_good_status'],'恢复申请关闭操作',date("Y-m-d H:i:s"),$_SESSION['userName']); 
		$orderInfo=ApiModel::sales_api($keys,$vals,'addOrderAction');        
        Util::jsonExit($res);
		
	}

	/**
	 *	已付款转为支付订金状态
	 */
	public function reyifukuan ($params)
	{
		$res = array('error'=>1,'msg'=>'');
		$order_sn = _Post::getString('order_sn');
		$keys=array('order_sn','order_pay_status');
		$vals=array($order_sn,2);
		$orderInfo=ApiModel::sales_api($keys,$vals,'UpdateOrderInfoModiy');

        if(isset($orderInfo) && ($orderInfo==0 || $orderInfo=='NULL')){
			$res['error'] =0;
			$res['msg'] ="订单号 ".$order_sn." 不存在，请重新输入";
			Util::jsonExit($res);
		}elseif($orderInfo==2){
			$res['error'] =0;
			$res['msg'] ="此订单已是支付订金状态，无需修改！";
			Util::jsonExit($res);            
        }elseif($orderInfo==3){
 			$res['error'] =0;
			$res['msg'] ="此订单还未付款！";
			Util::jsonExit($res);             
        }elseif($orderInfo==4){
 			$res['error'] =0;
			$res['msg'] ="此订单是账务备案,不能修改！";
			Util::jsonExit($res);             
        }elseif($orderInfo==5){
 			$res['error'] =0;
			$res['msg'] ="此订单未审核！";
			Util::jsonExit($res);             
        } elseif ($orderInfo==6) {
            //已配货的订单不能更新已付款转为支付订金状态
            $res['error'] =0;
			$res['msg'] ="已配货的订单不能更新已付款转为支付订金状态！";
			Util::jsonExit($res);    
        }

		$keys=array('order_id','order_status','pay_status','shipping_status','remark','create_time','create_user');
		$vals=array($orderInfo['id'],$orderInfo['order_status'],2,$orderInfo['send_good_status'],'恢复支付订单状态',date("Y-m-d H:i:s"),$_SESSION['userName']); 
		$orderInfo=ApiModel::sales_api($keys,$vals,'addOrderAction'); 

        Util::jsonExit($res);
		
	}

	/**
	 *	已审核改为未审核状态
	 */
	public function wei_status ($params)
	{
		$res = array('error'=>1,'msg'=>'');
		$order_sn = _Post::getString('order_sn');
		$keys=array('order_sn','order_status');
		$vals=array($order_sn,1);
		$orderInfo=ApiModel::sales_api($keys,$vals,'UpdateOrderInfoModiy');
        if(isset($orderInfo) && ($orderInfo==0 || $orderInfo=='NULL')){
			$res['error'] =0;
			$res['msg'] ="订单号 ".$order_sn." 不存在，请重新输入";
			Util::jsonExit($res);
		}
        if($orderInfo['order_status']==1){
			$res['error'] =0;
			$res['msg'] ="此订单已是未审核状态";
			Util::jsonExit($res);
		}
		if($orderInfo['order_pay_status'] != 1){
			$res['error'] =0;
			$res['msg'] ="此订单不是未付款，不能转为未审核";
			Util::jsonExit($res);
		}
        if($orderInfo['delivery_status']==5 ||  $orderInfo['send_good_status']==2){
			$res['msg'] ="订单号".$order_sn."已配货或者已发货，不能取消点款";
			Util::jsonExit($res);
		}
		
        $dd =new DictModel(1);
        $orderInfo['order_status']=$dd->getEnum('order.order_status',$orderInfo['order_status']);
		$keys=array('order_id','order_status','pay_status','shipping_status','remark','create_time','create_user');
		$vals=array($orderInfo['id'],1,$orderInfo['order_pay_status'],$orderInfo['send_good_status'],'订单状态从'.$orderInfo['order_status'].'改为待审核状态',date("Y-m-d H:i:s"),$_SESSION['userName']); 
		$orderInfo=ApiModel::sales_api($keys,$vals,'addOrderAction'); 
		$orderActionLogModel = new AppOrderActionLogModel(27);
		//操作日志
		if($orderInfo['id']){
			$ation['order_status'] = 1;
			$ation['order_id'] = $orderInfo['id'];
			$ation['shipping_status'] = $orderInfo['send_good_status'];
			$ation['pay_status'] = $orderInfo['order_pay_status'];
			$ation['create_user'] = $_SESSION['userName'];
			$ation['create_time'] = date("Y-m-d H:i:s");
			$ation['remark'] = '订单状态从'.$orderInfo['order_status'].'改为待审核状态';
			$res = $orderActionLogModel->saveData($ation, array());
		}
        Util::jsonExit($res);
		
	}
	
	/**
	 *	发货状态改为允许发货
	 */
	public function good_status ($params)
	{
		$res = array('error'=>1,'msg'=>'');
		$order_sn = _Post::getString('order_sn');
		$keys=array('order_sn','send_good_status');
		$vals=array($order_sn,4);
		$orderInfo=ApiModel::sales_api($keys,$vals,'UpdateOrderInfoModiy');
        
        if(isset($orderInfo) && ($orderInfo==0 || $orderInfo=='NULL')){
			$res['error'] =0;
			$res['msg'] ="订单号 ".$order_sn." 不存在，请重新输入";
			Util::jsonExit($res);
		}

        $dd =new DictModel(1);
        $orderInfo['send_good_status']=$dd->getEnum('order.send_good_status',$orderInfo['send_good_status']);

		$keys=array('order_id','order_status','pay_status','shipping_status','remark','create_time','create_user');
		$vals=array($orderInfo['id'],$orderInfo['order_status'],$orderInfo['order_pay_status'],4,'订单发货状态由'.$orderInfo['send_good_status'].'改为允许发货',date("Y-m-d H:i:s"),$_SESSION['userName']); 
		$orderInfo=ApiModel::sales_api($keys,$vals,'addOrderAction'); 

        Util::jsonExit($res);
		
	}
	
	/**
	 *	已付款改为账务备案
	 */
	public function cai_wu ($params)
	{
		$res = array('error'=>1,'msg'=>'');
		$order_sn = _Post::getString('order_sn');
		//$keys=array('order_sn','order_pay_status');
		//$vals=array($order_sn,4);
		//$orderInfo=ApiModel::sales_api($keys,$vals,'UpdateOrderInfoModiy');

		    $SalesModel=new SalesModel(28);		
			$orderInfo=$SalesModel->GetOrderInfoByOrderSn($order_sn);
	        
	        if(empty($orderInfo)){
				$res['error'] =0;
				$res['msg'] ="订单号 ".$order_sn." 不存在，请重新输入";
				Util::jsonExit($res);
			}
	
	        if($orderInfo['order_pay_status']!=1){
	            $res['error'] =0;
				$res['msg'] ="只有订单付款状态是:未付款，才可以修改成财务备案！";
				Util::jsonExit($res);
	        }
	       
	        $re=$SalesModel->UpdateOrderInfoModiy($order_sn,4);
	        if(!empty($re) && $re['error']==0){
	        	Util::jsonExit($re);
	        }
	        
	        
	        $dd =new DictModel(1);
	        $order_pay_status=$dd->getEnum('order.order_pay_status',$orderInfo['order_pay_status']);
	        $actionArr['order_id']=$orderInfo['id']?$orderInfo['id']:'';
	        $actionArr['order_status']=$orderInfo['order_status']?$orderInfo['order_status']:'';
	        $actionArr['shipping_status']=$orderInfo['send_good_status']?$orderInfo['send_good_status']:'';
	        $actionArr['pay_status']=4;
	        $actionArr['remark']='订单支付状态由'.$order_pay_status.'改为账务备案';
	      
	        
	        $re1=$SalesModel->addOrderAction($actionArr);
	        if(!empty($re1) && $re1['error']==0){
	        	Util::jsonExit($re1);
	        }
	
        $res['error'] =1;
        Util::jsonExit($res);
		
	}
}

?>
