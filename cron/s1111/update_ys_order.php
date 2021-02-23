<?php
define('ROOT_PATH',str_replace('\\','/',realpath(dirname(__FILE__))));//定义目录
define('TB_API_URL', 'http://114.55.12.230');
define('API_AUTH_KEYS', json_encode(array(
    'taobaoapi'=> ':AoN8Rt9l5103s'
)));
include_once(ROOT_PATH.'/taobaoapi.php');
//引入订单操作类文件
include_once(API_ROOT.'PdoModel.php');
include_once(API_ROOT.'PayClassModel.php');
include_once(API_ROOT.'OrderClassModel.php');
include_once(ROOT_PATH.'/include/allconfig.php');
include_once(ROOT_PATH.'/include/giftconfig.php');
include_once(ROOT_PATH.'/include/getgift.php');

$payModel = new PayClassModel();
$orderModel = new OrderClassModel();
//获取参数
if (PHP_SAPI == 'cli') {
    $_REQUEST['taobaoid'] = $_SERVER['argv'][1];
}

//如果是单个的
$requestid = isset($_REQUEST['taobaoid'])?$_REQUEST['taobaoid']:'';

//获取所有成功抓取的预售的订单
$isyushou = 1;
$ids = $orderModel->getalloutids($isyushou,$requestid);
if(empty($ids))
{
	exit('没有找到成功录制的未点款的预售订单');
}
echo "<pre>";
foreach($ids as $taobaoinfo)
{
	$taobaoid = $taobaoinfo['outid'];
	$taobaoid = trim($taobaoid);
	
	$orderid = $taobaoinfo['order_id'];
	$order_sn = $taobaoinfo['order_sn'];
	//获取淘宝订单信息
	$orderinfo = $apiModel->get_order_info($taobaoid);
	if(!empty($requestid))
		print_r($orderinfo);

	if(!empty($orderinfo->code))
	{
		echo '淘宝订单'.$taobaoid.'相关信息获取异常<br/>';
		continue;
	}
	//如果淘宝状态不是等待发货状态,则表示还没有付尾款
	
	if(trim($orderinfo->trade->status) != "WAIT_SELLER_SEND_GOODS")
	{
		echo '预售订单'.$taobaoid.'还没有付尾款或金额未到账<br/>';
		//continue;
	}
	
	//如果能走到这一步 说明预售订单已经付清了尾款
	$orderModel->mysqli->autocommit(false);// 开始事务
	$trans_error = false;
	
	//修改管理表中的order_status的值
	$res = $orderModel->updatestatus($taobaoid,$order_sn);

    if($res===false){
    	$trans_error = true;
		echo '信息已经回滚了<br/>'.PHP_EOL;
		//$reason .= '信息已经回滚了<br/>';
		$orderModel->mysqli->rollback();   
		$orderModel->mysqli->autocommit(TRUE); 	
    	continue;
    }else{
    	echo "淘宝订单".$taobaoid."更新s11_order_info状态成功<br>".PHP_EOL;
    } 	
	//如果有用到优惠券的金钱 再次把订单的总金额进行变化 这个程序不负责点款, 如果货品有多个 则相应的改变每个商品的价格 和优惠金额
	
	//优惠券金额
	//@$favorable_price = $orderinfo->trade->orders->order->part_mjz_discount;
	//淘宝承担的优惠金额不用开票给买家
	$promotion_taobao = 0;

	if(!empty($orderinfo))
	{
		//$youhui_detail = array();
		$youhui_detail = !empty($orderinfo->trade->promotion_details->promotion_detail) ? $orderinfo->trade->promotion_details->promotion_detail : array();
		$favorable_price = 0;
		if(!empty($youhui_detail)){
			foreach ($youhui_detail as  $youhui) {
			 	$favorable_price = bcadd($favorable_price,$youhui->discount_fee,2);
			 	//$taobao_promotion 在配置文件中统一配置
	       	    if(in_array($youhui->promotion_name,$taobao_promotion)){
	       	   	    $promotion_taobao = bcadd($promotion_taobao,$youhui->discount_fee,2);
	       	    }		 	
			} 
	    }

		$orderamount = $orderModel->getordermoney($orderid);
		if(!empty($orderamount))
		{
			//修改订的的总金额
			$order_amount_from = $orderamount['order_amount'];
			$money_unpaid_from = $orderamount['money_unpaid'];
			$favorable_price_from = $orderamount['favorable_price'];

			$data['order_amount'] = $orderinfo->trade->payment;
			$data['money_unpaid'] = bcsub($orderinfo->trade->payment,$orderamount['money_paid'],2);		
			$data['favorable_price'] = bcsub($orderinfo->trade->price,$orderinfo->trade->payment,2);		
			//修改订单的金额 未付金额 优惠金额
			$res = $orderModel->updateordermoney($orderid,$data);
		    if($res===false){
		    	$trans_error = true;
				echo '信息已经回滚了<br/>'.PHP_EOL;
				//$reason .= '信息已经回滚了<br/>';
				$orderModel->mysqli->rollback();   
				$orderModel->mysqli->autocommit(TRUE); 	
		    	continue;
		    }else{
				echo '更新订单金额成功<br/>'.PHP_EOL;
			}
			//操作日志
			$ation= array();
			$ation['order_status'] = 2;
			$ation['order_id'] = $orderid;
			$ation['shipping_status'] = 1;
			$ation['pay_status'] = 2;
			$ation['create_user'] = 'admin';
			$ation['create_time'] = date("Y-m-d H:i:s");
			$ation['remark'] = "淘宝预售订单金额由".$order_amount_from."更新为".$data['order_amount'].",优惠金额由".$favorable_price_from."更新为".$data['favorable_price'].",未付金额由".$money_unpaid_from."更新为".$data['money_unpaid'];
			$res = $orderModel->autoinsert('app_order_action',$ation);
			if($res)
			{
				echo 'app_order_action操作日志添加成功<br/>'.PHP_EOL;
			}else{				
				echo 'app_order_action操作日志添加失败<br/>'.PHP_EOL;
				$orderModel->mysqli->rollback();   
				$orderModel->mysqli->autocommit(TRUE); 				
			}

            //发票金额
            $invoice_amount = $order_amount_from;            
			//平台红包 
			if(!empty($orderinfo->trade->coupon_fee)) 
			    $promotion_taobao = bcadd($promotion_taobao , bcdiv($orderinfo->trade->coupon_fee,100,2),2);
			//支付宝集分宝 
			if(!empty($orderinfo->trade->alipay_point)) 
			    $promotion_taobao = bcadd($promotion_taobao , bcdiv($orderinfo->trade->alipay_point,100,2),2);
			$new_invoice_amount = bcsub($orderinfo->trade->payment,$promotion_taobao,2);
            if($new_invoice_amount<>$invoice_amount){
                //修改发票金额
                $res = $orderModel->update_invoice_amount($orderid,$new_invoice_amount);
				if($res===false)
				{
					echo '订单发票金额更新失败<br/>'.PHP_EOL;
					echo '信息回滚<br/>'.PHP_EOL;
					$orderModel->mysqli->rollback();   
					$orderModel->mysqli->autocommit(TRUE); 
					continue;	
				}else{				
					echo '订单发票金额更新成功<br/>'.PHP_EOL;
					//$orderModel->mysqli->commit();
					//$orderModel->mysqli->autocommit(TRUE); 
				}
				//操作日志
				$ation= array();
				$ation['order_status'] = 2;
				$ation['order_id'] = $orderid;
				$ation['shipping_status'] = 1;
				$ation['pay_status'] = 2;
				$ation['create_user'] = 'admin';
				$ation['create_time'] = date("Y-m-d H:i:s");
				$ation['remark'] = "淘宝预售订单发票金额由".$order_amount_from."更新为".$new_invoice_amount;
				$res = $orderModel->autoinsert('app_order_action',$ation);
				if($res)
				{
					echo 'app_order_action操作日志添加成功<br/>'.PHP_EOL;
					//$orderModel->mysqli->commit();
					//$orderModel->mysqli->autocommit(TRUE); 					
				}else{				
					echo 'app_order_action操作日志添加失败<br/>'.PHP_EOL;
					echo '信息回滚<br/>'.PHP_EOL;
					$orderModel->mysqli->rollback();   
					$orderModel->mysqli->autocommit(TRUE); 	
					continue;			
				}									          
            }

            $taobao_order_details = $orderinfo->trade->orders->order;
            //循环遍历订单明细 更改订单明细的优惠金额 同时根据订单的支付时间添加赠品货号
			//订单满即送  根据付款时间送赠品
			$first_detail_id = 0;
            $boss_order_details = $orderModel->get_boss_order_detail($orderid);
            if(empty($boss_order_details) || empty($taobao_order_details)){
				echo '获取订单明细失败<br/>'.PHP_EOL;
				echo '信息回滚<br/>'.PHP_EOL;
				$orderModel->mysqli->rollback();   
				$orderModel->mysqli->autocommit(TRUE); 	
				continue;            	
            }

            $update_detail_flag = true;
            foreach ($boss_order_details as $bdetail) {
            	$first_detail_id = $bdetail['id'];
            	$detail_favorable_price =0;
            	foreach ($taobao_order_details as $tdetail) {
            		$t_goods_sn = $tdetail->outer_sku_id;
            		if($bdetail['goods_sn']==$t_goods_sn){
                        $detail_favorable_price = bcsub($tdetail->price,$tdetail->payment,2); 
            		    $res = $orderModel->update_order_detail_favorable_price($bdetail['id'],$detail_favorable_price);  
						if($res)
						{
							echo '更新订单明细优惠价格成功<br/>'.PHP_EOL;
							//$orderModel->mysqli->commit();
							//$orderModel->mysqli->autocommit(TRUE); 					
						}else{				
							echo '更新订单明细优惠价格失败<br/>'.PHP_EOL;
							$update_detail_flag = false;			
						}	            		
            		}
            	}
            }
            if($update_detail_flag===false){
				echo '信息回滚<br/>'.PHP_EOL;
				$orderModel->mysqli->rollback();   
				$orderModel->mysqli->autocommit(TRUE); 	
				continue;
            }
            //
            //更行订单点款日志数据 

			$ordersprice_gifts = array();			
			$ordersprice_gifts = getgiftbyprice($orderid,'admin',$orderinfo->trade->payment,$orderinfo->trade->pay_time,true);
			if(!empty($ordersprice_gifts)){
				/**********          赠品入库app_order_details表          ***********/
				foreach ($ordersprice_gifts as $z => $ordergiftdata) {
					//有赠品款号的入库
					if(!empty($ordergiftdata['order_gift'])){
						$ordergiftinfo = $ordergiftdata['order_gift'];				
						$res_ordergift = $orderModel->autoinsert('app_order_details',$ordergiftinfo);
						if($res_ordergift)
						{
							echo '订单赠品录入app_order_details成功<br/>'.PHP_EOL;
						}else{
							echo '订单赠品录入app_order_details失败<br/>'.PHP_EOL;
							echo '信息回滚<br/>'.PHP_EOL;
							$orderModel->mysqli->rollback();   
							$orderModel->mysqli->autocommit(TRUE); 	
							continue;							
						} 
				    }
				    //无赠品款号的入库 随机备注在订单明细的商品备注后面
				    if(!empty($ordergiftdata['details_remark'])){
		                $res = $orderModel->updateDetailZengpin($first_detail_id,$ordergiftdata['details_remark']);
				        if($res){
		                    echo '订单无款号赠品更新app_order_details备注成功<br/>'.PHP_EOL;
				        }else{
							echo '订单无款号赠品更新app_order_details备注失败<br/>'.PHP_EOL;	
							echo '信息回滚<br/>'.PHP_EOL;
							$orderModel->mysqli->rollback();   
							$orderModel->mysqli->autocommit(TRUE); 	
							continue;								       	
				        }
				    }		          
						
				}
			}
		}
		
	}
	$orderModel->mysqli->commit();	
	$orderModel->mysqli->autocommit(TRUE); 
}
?>