<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseOrderInfoController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2019-06-21 17:54:25
 *   @update	:
 *  -------------------------------------------------
 */
class VopBaseOrderInfoController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $whitelist = array('sendGoods','printExpress','getExpress');	

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$error_warnning = $_SESSION['error_warnning'];
		$_SESSION['error_warnning'] = null;
		$this->render('base_order_info_search_form1.html',array('bar'=>Auth::getBar(),'error_warnning'=>$error_warnning));
	}

	/**
	 *	search，列表
	 */
	/*
	public function search ($params)
	{
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			//'参数' = _Request::get("参数");


		);
		//print_r($params);

		$order_sn = $params["order_sn"];
		if(empty($order_sn))
			exit('必须输入订单号');
		

		$where = array();
		if(!empty($params['order_sn']))
            $where['order_sn'] = trim($params['order_sn']);
		$model = new BaseOrderInfoModel(27);
		$data = $model->pageList($where,$page,10,false);
		if(empty($data)){
			exit('订单不存在');
		}
			
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'base_order_info_search_page';
		$this->render('base_order_info_search_list.html',array(			
			'page_list'=>$data
		));
	}
	*/

	/**
	 *	search1，列表
	 */
	public function search1 ($params)
	{
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			//'参数' = _Request::get("参数");


		);

		$order_sn = $params["order_sn"];
		if(empty($order_sn)){
			$_SESSION['error_warnning'] ='请输入订单号';
			exit('');
		}
		

		$where = array();
		if(!empty($params['order_sn']))
            $where['order_sn'] = trim($params['order_sn']);
		$model = new BaseOrderInfoModel(27);
		$data = $model->pageList($where,$page,10,false);
		if(empty($data)){
			$_SESSION['error_warnning'] ='找不到订单信息';
			exit('');
		}

		$goods_id_list = "";

		$goods_num = $model->getGoodsNum($order_sn);
		$list_div = $this->fetch('base_order_info_search_list.html',array(
			'page_list'=>$data
		));
		$this->render('base_order_info_search_form2.html',array('list_div'=>$list_div,'order_sn'=>$order_sn,'goods_num'=>$goods_num,'goods_id_list'=>$goods_id_list));
	}	

	/**
	 *	search1，列表
	 */
	public function search2 ($params)
	{
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			//'参数' = _Request::get("参数");


		);

		$order_sn = $params["order_sn"];
		$goods_id = $params["goods_id"];
		$goods_id_list = empty($params["goods_id_list"]) ? $goods_id : $params["goods_id_list"] ."_".$goods_id;
		if(empty($order_sn))
			exit('必须输入订单号');
		

		$where = array();
		if(!empty($params['order_sn']))
            $where['order_sn'] = trim($params['order_sn']);
		$model = new BaseOrderInfoModel(27);
		$data = $model->pageList($where,$page,10,false);
		if(empty($data)){
			exit('订单不存在');
		}

		$goods_num = $model->getGoodsNum($order_sn);
		$list_div = $this->fetch('base_order_info_search_list.html',array(
			'page_list'=>$data
		));
		$this->render('base_order_info_search_form2.html',array('list_div'=>$list_div,'order_sn'=>$order_sn,'goods_num'=>$goods_num,'goods_id_list'=>$goods_id_list));
	}







	/**
	 *	edit，渲染修改页面
	 */
	public function sendGoods($params)
	{

        //$params['id']= 2822415;
		$id = _Request::get("goods_id_list");
        $order_sn = _Request::get("order_sn");
        if(empty($order_sn)){
        	exit('未传入订单号');
        }

		$SalesModel = new SalesModel(27);
        $order_list = $SalesModel->getAppOrderDetails($order_sn);
        if(empty($order_list)){
        	exit('未找到订单明细');
        }


		$goods_list = array(); 
		if(!empty($id)){	        
	        $ids = explode("_",$id);
	        
	        foreach ($ids as $key => $goods) {
	
	            	$goods_info = $SalesModel->getGoods($goods);            	
	            	if($goods_info){
	            		/*
	                    if($goods_info['is_on_sale']!=2)
	                        exit("发货货号[{$goods}]不是库存状态");  
	                    if(!empty($goods_info['order_goods_id']))
	                    	exit("发货货号[{$goods}]已绑定订单不能发货");
	                    */	  
	            	}else{
	            		exit("发货货号[{$goods}]不存在");
	            	}
                    
                    if(!in_array($goods,array_column($order_list,'goods_id'))){
                        exit("订单明细没有绑定货号{$goods}"); 
                    }


	            	$goods_list[] = $goods;

	        }
        }



		$tab_id = _Request::getInt("tab_id");

       



        
        $data=$SalesModel->getAddressByOrderSn($order_sn);
        if(empty($data)){        	
		    exit('获取订单地址信息失败');
        }

        $order = $SalesModel->getOrderInfoBySn($order_sn);
        if(empty($order)){
        	exit('获取订单信息失败');     	  
        }

        
        
        
        $out_order_sn = explode(",", $order['out_order_sn']);
        if(count($out_order_sn)>1)
        	exit('一个BOSS订单不能出现2个唯品会外部订单');
        require_once KELA_PATH.'/jitx/vipapis/jitx/JitXServiceClient.php';
        //已发货直接打快递单
        
		
        $service=\vipapis\jitx\JitXServiceClient::getService();
        $ctx=\Osp\Context\InvocationContextFactory::getInstance();                                 
        $ctx->setAppKey(VOP_APP_KEY);
        $ctx->setAppSecret(VOP_APP_SECRET);
        $ctx->setAppURL("https://gw.vipapis.com");
            
        $req = new \vipapis\jitx\GetOrdersByOrderSnRequest(['vendor_id'=>VOP_VENDOR_ID,'order_sns'=>$out_order_sn]);
        try{
			   $res = $service->getOrdersByOrderSn($req);
		}catch(Exception $ex){		    	
		       exit(json_encode($ex));	
		}
			

		if(!empty($res->orders)){
				$jitx_order_list = $res->orders;
				$jitx_order = $jitx_order_list[0];
				$transport_no = $jitx_order->transport_no;
				$order_status = $jitx_order->order_status;
				$carrier_code = $jitx_order->carrier_code;
				$order_goods = $jitx_order->order_goods;
				$out_order_sn = $jitx_order->order_sn;
				$delivery_warehouse = $jitx_order->delivery_warehouse;

           
                try{
                    	$goods_info = array();
                    	foreach ($order_goods as $key => $goods) {
                    		$goods_info[] = $goods->product_name. ' ' .$goods->size. '*'.$goods->quantity;
                    	}
                        $PrintDetail = new \vipapis\jitx\PrintDetail(
                            array(
                            'order_sn' => $out_order_sn,
                            'transport_no' => $transport_no,
                            'box_no' =>1,
                            'carrier_code' => $carrier_code,
                            'total_package' =>1,
                            'goods_info' => $goods_info,
                            )
                        );
                        //print_r($PrintDetail);
                        $req = new \vipapis\jitx\GetOrderLabelRequest(['vendor_id'=>VOP_VENDOR_ID,'print_details'=>[$PrintDetail]]);

                        $res = $service->getPrintTemplate($req);             
                        $res_arr = json_decode($res[0]->order_label);
                        $res = array();
                        foreach ($res_arr as $key => $v) {
                            $res[$v->fieldCode] = $v->fieldValue;
                        }
                        
                        if(empty($res)){
                        	exit('获取快递面单异常');
                        }

		                $express = array();
		                $express['res'] = $res;                    

                        if(in_array($order_status,['22','23','97_22'])){
							//已发货，取出快递并打印面单
						    //echo $order_status
					        //print_r($res); 
					        
							$this->render('mod_print_api.html',array(
										'express'=>$express					
							));
							exit();
								
						}

                        
						//未发货	
						if(!empty($goods_list)){
							//有输入货号 校验BOSS货号 审核调拨单
							$SalesModel->checkBillM($order_sn,$goods_list);
						}
                        
                        
                        //BOSS发货相关操作
						$salesModel = new SalesModel(27);
						$shipFreightModel = new ShipFreightModel(43);		
						$pdo27 = $salesModel->db()->db();
						$pdo43 = $shipFreightModel->db()->db();			
						$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
						$pdo27->beginTransaction(); //开启事务			
						$pdo43->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
						$pdo43->beginTransaction(); //开启事务
			            try{
			                $shipFreightModel->saveData(array(
			                               'order_no'=>$order_sn,
			                               'freight_no' =>$transport_no,
			                               'express_id' =>41,
			                               'consignee'=>$order['consignee'],
			                               'cons_address'=>$data['address'],
			                               'cons_mobile'=>$order['mobile'],  
			                               'create_time'=>time(),
			                               'create_name'=>  $_SESSION['userName'],
			                               'out_order_id'=>$order['out_order_sn'],
			                               'channel_id'=>$order['department_id'],
			                               ),
			                	           array());

						    $orderLog = array(
						                'order_id'=>$order['id'],
						                'order_status'=>1,
						                'shipping_status'=>2,
						                'pay_status'=>1,
						                'create_user'=>$_SESSION['userName'],
						                'create_time'=>date('Y-m-d H:i:s'),
						    		    'remark'=>'订单已发货, 快递单号:'.$transport_no,
						    );			   
						    $salesModel->addOrderLog($orderLog);   

			                $old_data = array('id'=>$order['id']);
			                $new_data = array('id'=>$order['id'],'send_good_status'=>2); 
			                $order_model = new BaseOrderInfoModel($order['id'],27);        	
			                $res2 = $order_model->saveData($new_data,$old_data);

			                $pdo27->commit(); //事务提交
							$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
							$pdo43->commit(); //事务提交
							$pdo43->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交	
			            }catch(Exception $e){
							$pdo27->rollback(); //事务回滚
							$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
							$pdo43->rollback(); //事务回滚
							$pdo43->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交            	
			            	exit('boss更新订单发货信息失败'.$e->getMessage());
			            }

			            //唯品会发货
                        $package_list = array(); 
                        foreach ($order_goods as $key => $goods) {
                            $package_list[] = new \vipapis\jitx\PackageDetail(['barcode'=>$goods->barcode,'quantity'=>$goods->quantity]);
                        }
                        
                        $package_time =time(); 
                        $ship_list = array();

                        $ship_list[] = new \vipapis\jitx\Ship(
                               ['order_sn'=>$out_order_sn,
                                'delivery_warehouse'=>  $delivery_warehouse,
                                'total_package'=>1,
                                'packages'=>[
                                	new \vipapis\jitx\Package(
                                		 ['box_no'=>1,
                                		 'oqc_date'=>$package_time,
                                		 'transport_no'=>$transport_no,
                                		 'package_no'=> 1,
                                         'details'=> $package_list,
                                		])],
                               ] 
                        );
                        

                        $req = new \vipapis\jitx\ShipRequest(['vendor_id'=>VOP_VENDOR_ID,'ships'=>$ship_list]);
                
                        //echo "<pre>";
                        $res = $service->ship($req);
                        //echo "<pre>";        
                        //print_r($res);
                        if(!empty($res->failed_list)){
                        	$msg_list = $res->failed_list;
                        	$msg = $msg_list[0];
                            exit($msg->msg);
                            //exit();
                        }


						$this->render('mod_print_api.html',array(
										'express'=>$express					
						));
						exit();


                } catch(\Osp\Exception\OspException $e){
                        //var_dump($e);
                        //echo "<pre>";
                        //print_r($e);
                        //echo $e->getReturnMessage();
                        exit($e->getReturnMessage());
                }   
		}else{
                exit('未获取到唯品会订单信息');
		}	



	}



	/**
	 *	edit，渲染修改页面
	 */
	/*
	public function updateStatus ($params)
	{
		$id = intval($params["id"]);
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('base_order_info_info.html',array(
			'view'=>new BaseOrderInfoView(new BaseOrderInfoModel($id,27)),
			'tab_id'=>$tab_id
		));
		//print_r($params);
		$result['title'] = '打单发货';
		Util::jsonExit($result);
	}*/

	
}

?>