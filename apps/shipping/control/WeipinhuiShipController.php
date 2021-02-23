<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoHController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-21 21:31:17
 *   @update	:
 *  -------------------------------------------------
 */
class WeipinhuiShipController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist  = array('downloads');

	/**
	 *	index，搜索框
	 */
	 public function index ($params)
	{
		$this->render('weipinhui_ship_search_form.html',array('bar'=>Auth::getBar()));
	}


	/**
	 *	add，渲染添加页面
	 */
	public function batch_ship ()
	{
		$result = array('success' => 0,'error' => '');
		//3,允许批量点发货,不符合条件的订单要提示“订单号×××条件不符，不允许操作”，点击确定后，列表自动清空条件不符的订单号
		$order_no =_Request::get("order_sn");
		$order_no_arr=preg_split("/\n/",$order_no);
		$model = new ShipFreightModel(43);
		$SalesModel27=new SalesModel(27);
		foreach($order_no_arr as $val){
			//获取订单明细
			$result['order_sn']=$val;
			//$data = $model->getOrderinfo_row($val);
			$data =$SalesModel27->GetOrderInfoByOrderSn($val);
			//var_dump($data);exit;
			if(!isset($data['order_sn']) && empty($data['order_sn'])){
				$result['error'] .= "很抱歉,未查到该订单{$val}相关信息！\n<br>";
				//Util::jsonExit($result);
			}else{
				//1,只有销售订单客户来源是【唯品会B2C】，
				if($data['customer_source_id']!=2034){
					$result['error'].= "订单{$val}客户来源有误,必须是唯品会B2C！\n<br>";
					//Util::jsonExit($result);
				}
				//2,且销售订单配货状态是[已配货]状态,且发货状态是[未发货][允许发货]【收货确认】【已到店】，
				if($data['delivery_status']!=5){
					$result['error'].= "订单{$val}配货状态错误,必须是已配货状态！\n<br>";
					//Util::jsonExit($result);
				}
				if($data['send_good_status']==2){
					$result['error'].= "订单{$val}发货状态错误,必须是[未发货][允许发货][收货确认][已到店]！\n<br>";
					//Util::jsonExit($result);
				}
			}
			
			
		}
		if(!empty($result['error'])){
			//var_dump($result['error']);exit;
			Util::jsonExit($result);
		}
		//2、批量操作成功后，销售单，自动审核，审核人为批量发货操作人，审核时间为批量发货操作时间
		
		//3、批量操作成功后，销售订单发货状态更新为【已发货】，且更新销售订单日志，内容为“已发货，唯品会订单批量发货”
		
		$SalesModel=new SalesModel(28);
		$WarehouseModel21=new WarehouseModel(21);
		$WarehouseModel=new WarehouseModel(22);
		$pdo28 = $SalesModel->db()->db();
		$pdo22 = $WarehouseModel->db()->db();
		 
		$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
		$pdo28->beginTransaction(); //开启事务
		 
		$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
		$pdo22->beginTransaction(); //开启事务
	try{
		foreach($order_no_arr as $val){
		//回写订单日志
		$ordre_sn = $val;
		
		//$data = ApiModel::sales_api(array('order_sn','fields'),array($ordre_sn, " `id`, `order_status` , `send_good_status` , `order_pay_status` "),'GetDeliveryStatus');
		$data =$SalesModel27->GetOrderInfoByOrderSn($ordre_sn,"`id`, `order_status` , `send_good_status` , `order_pay_status`");
		$order_id = $data['id'];
		$order_status = $data['order_status'];
		$send_good_status = $data['send_good_status'];
		$order_pay_status = $data['order_pay_status'];
		//$shipping_status = $data['shipping_status'];
		$time = date('Y-m-d H:i:s');
		$user = $_SESSION['userName'];
		
		//var_dump($val);exit;
		#1、审核销售单据
		//获取已保存的销售单货号，
		//$goods_ids = ApiModel::warehouse_api(array('order_sn'),array($val),'GetGoodsIdsByOrderSN');
		$goods_ids=$WarehouseModel21->getGoodsIdsByOrderSn($ordre_sn);
		//var_dump($goods_ids);exit;
		$new_goods_ids = array();
		if ($goods_ids)
		{
			foreach ($goods_ids as $v)
			{
				$new_goods_ids[] = trim($v['goods_id']);
			}
		}
		//审核销售单 销售单状态  货品状态
		//$res= ApiModel::warehouse_api(array('order_sn','goods_ids','user','ip','time') , array($val,join("','",$new_goods_ids),$_SESSION['userName'],Util::getClicentIp(),$time) , 'checkXiaoshou');
		$res=$WarehouseModel->confirmSale(array('order_sn'=>$val,'goods_ids'=>$new_goods_ids,'user'=>$_SESSION['userName'],'ip'=>Util::getClicentIp(),'time'=>$time));
		if ($res != 1)
		{
			$pdo28->rollback(); //事务回滚
			$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			$pdo22->rollback(); //事务回滚
			$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			$result['error'] ="订单{$val}销售单审核失败";
			Util::jsonExit($result);
		}
		# 2、修改订单发货状态、订单商品发货状态、
		//$res2 = ApiModel::sales_api(array('order_sn'),array($val),'setOrderGoodsSend');
		$res2 =$SalesModel->setOrderGoodsSend(array('order_sn'=>$val));
		if (!$res2)
			{
				$pdo28->rollback(); //事务回滚
				$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo22->rollback(); //事务回滚
				$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$result['error'] ="订单{$val}发货状态修改失败";
				Util::jsonExit($result);
		}
		/*
		ApiModel::sales_api(	//回写订单日志
		array('order_no','create_user','remark'),
		array($ordre_sn ,$user , '已发货，唯品会订单批量发货') ,'AddOrderLog');
		
		*/
		
		 $res3=$SalesModel->AddOrderLog(array('order_id'=>$order_id,'order_status'=>$order_status,'shipping_status'=>2,'pay_status'=>$order_pay_status,'create_user'=>$user,'create_time'=>$time,'remark'=>'已发货，唯品会订单批量发货'));		 
		 if (!$res3)
		 {
		 	$pdo28->rollback(); //事务回滚
		 	$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		 	$pdo22->rollback(); //事务回滚
		 	$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		 	$result['error'] ="订单{$val}回写订单日志失败";
		 	Util::jsonExit($result);
		 }
		}
		$pdo28->commit(); //事务提交
		$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		$pdo22->commit(); //事务回滚
		$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		$result['success'] = 1;
		//AsyncDelegate::dispatch('warehouse', array('event'=>'bill_S_checked', 'order_sn' => $order_sn));
		Util::jsonExit($result);
	 }catch (Exception $e){
	 	$pdo28->rollback(); //事务回滚
	 	$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
	 	$pdo22->rollback(); //事务回滚
	 	$pdo22->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
	 	$result['error'] ="系统异常！error code:".$e;
	 	Util::jsonExit($result);
	 }
        
		
	}


}

?>