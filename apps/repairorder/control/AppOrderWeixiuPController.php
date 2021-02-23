<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderWeixiuPController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166422@qq.com>
 *   @date		: 2015-01-41 17:16:36
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderWeixiuPController extends Controller
{
	protected $smartyDebugEnabled = true;
	
	public function index($params){
		
	}
	public function batch(){	
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
        	die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }			
		$this->render('app_order_weixiu_batch.html',array(
			
			));
	}
	
	/**
	 batch_order,批量下单
	 **/
	public function batch_order()
	{
		$result = array('success' => 0,'error' => '');
		$ids = $_REQUEST['_ids'];
		if(empty($ids)){
			$result['error']='请输入你要操作的维修单';
			Util::jsonExit($result);
		}
		$idsArr=explode(',', $ids);
		
		$model = new AppOrderWeixiuModel(42);	
		$WarehouseModel = new WarehouseModel(21);
		$AppProcessorInfoModel=new AppProcessorInfoModel(13);
		$SalesModel = new SalesModel(28);
        $model_log = new AppOrderWeixiuLogModel(41);
		
		$pdo42 = $model->db()->db();
		$pdo13 = $AppProcessorInfoModel->db()->db();
		$pdo27 = $SalesModel->db()->db();
        $pdo41 = $model_log->db()->db();
		
		$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
		$pdo42->beginTransaction(); //开启事务
		 
		$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
		$pdo13->beginTransaction(); //开启事务
		
		$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
		$pdo27->beginTransaction(); //开启事务

        $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
        $pdo41->beginTransaction(); //开启事务
		
		
		try{
		
		foreach($idsArr as $id){
			$status = 4;
			$weixiuArr=$model->getWeixiuOrderInfo($id);
			$rec_id=$weixiuArr['rec_id'];
			$check=$this->checkStatus($weixiuArr['status'],$status);
			
			if($check['success']!=1)
			{
				$pdo42->rollback(); //事务回滚
				$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo13->rollback(); //事务回滚
				$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo27->rollback(); //事务回滚
				$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo41->rollback(); //事务回滚
                $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$check['error']=$id.":".$check['error'];
				Util::jsonExit($check);
			}
			$buchan = $weixiuArr['rec_id'];
			$order_sn =$weixiuArr['order_sn'];
			$repair_factory = $weixiuArr['repair_factory'];
			$goods_id = $weixiuArr['goods_id'];
			#2、如果货号不为空;是否售后维修,则货品状态已销售=是1、货品状态非已销售(或者查不到)=否0
			$api_res  = $WarehouseModel->getWarehouseGoodsByGoodsid($goods_id);
			if(empty($api_res)){
				$pdo42->rollback(); //事务回滚
				$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo13->rollback(); //事务回滚
				$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo27->rollback(); //事务回滚
				$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo41->rollback(); //事务回滚
                $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$result['error'] = $id.'对应的货号'.$goods_id.'在系統中不存在!!';
				Util::jsonExit($result);
			}else{
				$goods_info = $api_res;
			}
				
	
			$remark = _Request::get('remark_log');//	操作日志备注
	
			$arr_log = array(
					'do_id'			=>$id,
					'user_name'		=>$_SESSION['userName'],
					'do_type'		=>'app_order_weixiu,update'
			);
			//下单按钮  状态  下单时间  备注
			$str='';
	
				$str.="status=$status"; 
				$str.=",repair_man=".$_SESSION['userId'];
				$str.=",factory_time='".date("Y-m-d H:i:s")."'";
				$str.=",remark='维修单确认,布产号：".$buchan.';'.$remark."'";

			$res=$model->updateWeixiu($str,$id);
			
			if($res !== false)
			{
	
				//添加维修日志 1
				$arr_log['content'] ="维修单下单,布产号：".$buchan.';'.$remark;
			    $res1=$model_log->saveData($arr_log,array());
			    if(!$res1){
			    	$pdo42->rollback(); //事务回滚
			    	$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			    	$pdo13->rollback(); //事务回滚
			    	$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			    	$pdo27->rollback(); //事务回滚
			    	$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                    $pdo41->rollback(); //事务回滚
                    $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			    	$result['error'] = '添加维修日志失败!';
			    	Util::jsonExit($result);
			    }
			    
	
				//添加订单日志 2
				$dd = new DictView(new DictModel(1));
				$weixiu_status = $dd->getEnum('weixiu.status',$status);
				$pro_name ='';
				$pro_list = $AppProcessorInfoModel->getProList();
				foreach ($pro_list as $val)
				{
					if($val['id']==$repair_factory){
						$pro_name =$val['name'];
					}
				}
				//获取维修 内容
				$repair_act = $weixiuArr['repair_act'];
				$arr_act = explode(',', $repair_act);
				$str_act = "";
				foreach ($arr_act as $val)
				{
					$str_act .= $dd->getEnum('weixiu.action',$val).",";
				}
				
				if($order_sn!=''){
					$res2 =	$SalesModel->AddOrderLog($order_sn,$_SESSION['userName'],'批量维修单操作，维修流水号：'.$id.',维修工厂：'.$pro_name.',布产号：'.$buchan.',维修状态:'.$weixiu_status);
					if(!$res2){
						$pdo42->rollback(); //事务回滚
						$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
						$pdo13->rollback(); //事务回滚
						$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
						$pdo27->rollback(); //事务回滚
						$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                        $pdo41->rollback(); //事务回滚
                        $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
						$result['error'] = '添加订单日志失败!';
						Util::jsonExit($result);
					}
				}

			 if($rec_id!=''){	 
				//改变布产单维修状态
				$res3 = $AppProcessorInfoModel->editWeixiuStatus(substr($rec_id,2),$status);
				if(!$res3){
					$pdo42->rollback(); //事务回滚
					$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
					$pdo13->rollback(); //事务回滚
					$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
					$pdo27->rollback(); //事务回滚
					$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                    $pdo41->rollback(); //事务回滚
                    $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
					$result['error'] = '改变布产单维修状态失败!';
					Util::jsonExit($result);
				}
				//改变订单维修状态
				
				
				  $res4 = $SalesModel->EditOrderGoodsInfo(substr($rec_id,2),$status);
				 
				}
				//$ret = $model->change_weixiu_status($api_res['return_msg'][0]['order_goods_id'],array('weixiu_status'=>$status));
				if($goods_info['order_goods_id']){
					$res5= $SalesModel->EditOrderGoodsStatusById($goods_info['order_goods_id'],$status);
					if(!$res5){
						$pdo42->rollback(); //事务回滚
						$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
						$pdo13->rollback(); //事务回滚
						$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
						$pdo27->rollback(); //事务回滚
						$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                        $pdo41->rollback(); //事务回滚
                        $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
						$result['error'] = '改变订单维修状态失败!';
						Util::jsonExit($result);
					}
				}
				//添加布产日志 3
				if($buchan){
					$res6 =	$AppProcessorInfoModel->AddOrderLog(array('status'=>$status,'rec_id'=>$buchan,'create_user_id'=>$_SESSION['userId'],'create_user'=>$_SESSION['userName'],'remark'=>'批量维修单操作，维修流水号：'.$id.',维修工厂：'.$pro_name.',维修内容：'.$str_act.$remark.',维修状态：'.$weixiu_status));
	                if(!$res6){
	                	$pdo42->rollback(); //事务回滚
	                	$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
	                	$pdo13->rollback(); //事务回滚
	                	$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
	                	$pdo27->rollback(); //事务回滚
	                	$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                        $pdo41->rollback(); //事务回滚
                        $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
	                	$result['error'] = '添加布产日志失败!';
	                	Util::jsonExit($result);
	                }
				}
				
			}
			else
			{
				$pdo42->rollback(); //事务回滚
				$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo13->rollback(); //事务回滚
				$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo27->rollback(); //事务回滚
				$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo41->rollback(); //事务回滚
                $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$result['error'] = "操作失败";
				Util::jsonExit($result);
			}
		}
	      
		
		  $pdo42->commit(); //事务提交
		  $pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		  $pdo13->commit(); //事务回滚
		  $pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		  $pdo27->commit(); //事务回滚
		  $pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
          $pdo41->commit(); //事务回滚
          $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
          $result['success'] = 1;
		  Util::jsonExit($result);
		}catch (Exception $e){
			$pdo42->rollback(); //事务回滚
			$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			$pdo13->rollback(); //事务回滚
			$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			$pdo27->rollback(); //事务回滚
			$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
            $pdo41->rollback(); //事务回滚
            $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			$result['error'] ="系统异常！error code:".__LINE__;
			Util::jsonExit($result);
		}	
	}
	
	/**
	 batch_order,批量收货
	 **/
	public function batch_goods()
	{
	$result = array('success' => 0,'error' => '');
		$ids = $_REQUEST['_ids'];
		if(empty($ids)){
			$result['error']='请输入你要操作的维修单';
			Util::jsonExit($result);
		}
		$idsArr=explode(',', $ids);
		
		$model = new AppOrderWeixiuModel(42);	
		$WarehouseModel = new WarehouseModel(21);
		$AppProcessorInfoModel=new AppProcessorInfoModel(13);
		$SalesModel = new SalesModel(27);
        $model_log = new AppOrderWeixiuLogModel(41);
		
		$pdo42 = $model->db()->db();
		$pdo13 = $AppProcessorInfoModel->db()->db();
		$pdo27 = $SalesModel->db()->db();
        $pdo41 = $model_log->db()->db();
		
		$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
		$pdo42->beginTransaction(); //开启事务
		 
		$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
		$pdo13->beginTransaction(); //开启事务
		
		$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
		$pdo27->beginTransaction(); //开启事务

        $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
        $pdo41->beginTransaction(); //开启事务
		
		
		try{
		
		foreach($idsArr as $id){
			$status = 6;
			$weixiuArr=$model->getWeixiuOrderInfo($id);
			$rec_id=$weixiuArr['rec_id'];
			
			$modelret = new AppOrderWeixiuModel($id,42);
			
			
			
			$check=$this->checkStatus($weixiuArr['status'],$status);
			
			if($check['success']!=1)
			{
				$pdo42->rollback(); //事务回滚
				$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo13->rollback(); //事务回滚
				$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo27->rollback(); //事务回滚
				$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo41->rollback(); //事务回滚
                $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$check['error']=$id.":".$check['error'];
				Util::jsonExit($check);
			}
			$buchan = $weixiuArr['rec_id'];
			$order_sn =$weixiuArr['order_sn'];
			$repair_factory = $weixiuArr['repair_factory'];
			$goods_id = $weixiuArr['goods_id'];
			#2、如果货号不为空;是否售后维修,则货品状态已销售=是1、货品状态非已销售(或者查不到)=否0
			$api_res  = $WarehouseModel->getWarehouseGoodsByGoodsid($goods_id);
			if(empty($api_res)){
				$pdo42->rollback(); //事务回滚
				$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo13->rollback(); //事务回滚
				$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo27->rollback(); //事务回滚
				$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo41->rollback(); //事务回滚
                $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$result['error'] = $id.'对应的货号'.$goods_id.'在系統中不存在!!';
				Util::jsonExit($result);
			}else{
				$goods_info = $api_res;
			}
				
	
			$remark = _Request::get('remark_log');//	操作日志备注
	
			$arr_log = array(
					'do_id'			=>$id,
					'user_name'		=>$_SESSION['userName'],
					'do_type'		=>'app_order_weixiu,update'
			);
			//下单按钮  状态  下单时间  备注
			$str='';
	
				$str.="status=$status"; 
				$str.=",repair_man=".$_SESSION['userId'];
				
			// 2015-10-23增加 by lyy 2015-10-27条件判断增加
			$retm = $modelret->getValue('re_end_time');
			if(empty($retm)||$retm == "0000-00-00 00:00:00")
			{
				$str.=",re_end_time='".date("Y-m-d H:i:s")."'";
				// 质检次数:只有当完毕时间为空再去点击收货或者批量收货,
				// 次数需要再加1,完毕时间不为空去点击收货或者批量收货,
				// 次数不需要加1,--2015-10-24
				// $model->EditQctimes($id,1);
				$str.=",qc_times=qc_times+1";
			}				
			
			
				
			// 2015-10-24增加 
			$str.=",qc_status='1'";
			 
			
				$str.=",receiving_time='".date("Y-m-d H:i:s")."'";
				$str.=",remark='维修单确认,布产号：".$buchan.';'.$remark."'";

			$res=$model->updateWeixiu($str,$id);
			
			if($res !== false)
			{
	
				//添加维修日志 1
				$arr_log['content'] ="维修单收货,布产号：".$buchan.';'.$remark;
			    $res1=$model_log->saveData($arr_log,array());
			    if(!$res1){
			    	$pdo42->rollback(); //事务回滚
			    	$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			    	$pdo13->rollback(); //事务回滚
			    	$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			    	$pdo27->rollback(); //事务回滚
			    	$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                    $pdo41->rollback(); //事务回滚
                    $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			    	$result['error'] = '添加维修日志失败!';
			    	Util::jsonExit($result);
			    }
			    
	
				//添加订单日志 2
				$dd = new DictView(new DictModel(1));
				$weixiu_status = $dd->getEnum('weixiu.status',$status);
				$pro_name ='';
				$pro_list = $AppProcessorInfoModel->getProList();
				foreach ($pro_list as $val)
				{
					if($val['id']==$repair_factory){
						$pro_name =$val['name'];
					}
				}
				//获取维修 内容
				$repair_act = $weixiuArr['repair_act'];
				$arr_act = explode(',', $repair_act);
				$str_act = "";
				foreach ($arr_act as $val)
				{
					$str_act .= $dd->getEnum('weixiu.action',$val).",";
				}

				if($order_sn != ''){
                    $res2 = $SalesModel->AddOrderLog($order_sn,$_SESSION['userName'],'批量维修单操作，维修流水号：'.$id.',维修工厂：'.$pro_name.',布产号：'.$buchan.',维修状态:'.$weixiu_status);
                    if(!$res2){
                        $pdo42->rollback(); //事务回滚
                        $pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                        $pdo13->rollback(); //事务回滚
                        $pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                        $pdo27->rollback(); //事务回滚
                        $pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                        $pdo41->rollback(); //事务回滚
                        $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                        $result['error'] = '添加订单日志失败!';
                        Util::jsonExit($result);
                    }
                }

				//改变布产单维修状态
				//2015-10-27需要加上不为空判断
				if(!empty($rec_id))
				{
				    $res3 = $AppProcessorInfoModel->editWeixiuStatus(substr($rec_id,2),$status);
    				if(!$res3){
    					$pdo42->rollback(); //事务回滚
    					$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    					$pdo13->rollback(); //事务回滚
    					$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    					$pdo27->rollback(); //事务回滚
    					$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                        $pdo41->rollback(); //事务回滚
                        $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
    					$result['error'] = '改变布产单维修状态失败!';
    					Util::jsonExit($result);
    				}
				}
				//改变订单维修状态
			    
				if($rec_id!=''){
				  $res4 = $SalesModel->EditOrderGoodsInfo(substr($rec_id,2),$status);
				}
				
				//$ret = $model->change_weixiu_status($api_res['return_msg'][0]['order_goods_id'],array('weixiu_status'=>$status));
				if($goods_info['order_goods_id']){
					$res5= $SalesModel->EditOrderGoodsStatusById($goods_info['order_goods_id'],$status);
					if(!$res5){
						$pdo42->rollback(); //事务回滚
						$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
						$pdo13->rollback(); //事务回滚
						$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
						$pdo27->rollback(); //事务回滚
						$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                        $pdo41->rollback(); //事务回滚
                        $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
						$result['error'] = '改变订单维修状态失败!';
						Util::jsonExit($result);
					}
				}
				//添加布产日志 3
				if($buchan){
					$res6 =	$AppProcessorInfoModel->AddOrderLog(array('status'=>$status,'rec_id'=>$buchan,'create_user_id'=>$_SESSION['userId'],'create_user'=>$_SESSION['userName'],'remark'=>'批量维修单操作，维修流水号：'.$id.',维修工厂：'.$pro_name.',维修内容：'.$str_act.$remark.',维修状态：'.$weixiu_status));
	                if(!$res6){
	                	$pdo42->rollback(); //事务回滚
	                	$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
	                	$pdo13->rollback(); //事务回滚
	                	$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
	                	$pdo27->rollback(); //事务回滚
	                	$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                        $pdo41->rollback(); //事务回滚
                        $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
	                	$result['error'] = '添加布产日志失败!';
	                	Util::jsonExit($result);
	                }
				}
				
			}
			else
			{
				$pdo42->rollback(); //事务回滚
				$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo13->rollback(); //事务回滚
				$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo27->rollback(); //事务回滚
				$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo41->rollback(); //事务回滚
                $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$result['error'] = "操作失败";
				Util::jsonExit($result);
			}
		}
	      
		
		  $pdo42->commit(); //事务提交
		  $pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		  $pdo13->commit(); //事务回滚
		  $pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		  $pdo27->commit(); //事务回滚
		  $pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
          $pdo41->commit(); //事务回滚
          $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
          $result['success'] = 1;
		  Util::jsonExit($result);
		}catch (Exception $e){
			$pdo42->rollback(); //事务回滚
			$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			$pdo13->rollback(); //事务回滚
			$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			$pdo27->rollback(); //事务回滚
			$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
            $pdo41->rollback(); //事务回滚
            $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			$result['error'] ="系统异常！error code:".$e;
			Util::jsonExit($result);
		}	
	}
	
	
	/**
	 batch_goods,批量完成
	 **/
	public function batch_complete()
	{
	    $result = array('success' => 0,'error' => '');
		$ids = $_REQUEST['_ids'];
		if(empty($ids)){
			$result['error']='请输入你要操作的维修单';
			Util::jsonExit($result);
		}
		$idsArr=explode(',', $ids);
		
		$model = new AppOrderWeixiuModel(42);	
		$WarehouseModel = new WarehouseModel(21);
		$AppProcessorInfoModel=new AppProcessorInfoModel(13);
		$SalesModel = new SalesModel(27);
        $model_log = new AppOrderWeixiuLogModel(41);
		
		$pdo42 = $model->db()->db();
		$pdo13 = $AppProcessorInfoModel->db()->db();
		$pdo27 = $SalesModel->db()->db();
        $pdo41 = $model_log->db()->db();
		
		$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
		$pdo42->beginTransaction(); //开启事务
		 
		$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
		$pdo13->beginTransaction(); //开启事务
		
		$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
		$pdo27->beginTransaction(); //开启事务

        $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
        $pdo41->beginTransaction(); //开启事务
		
		
		try{
		
		foreach($idsArr as $id){
			$status = 5;
			$weixiuArr=$model->getWeixiuOrderInfo($id);
			$rec_id=$weixiuArr['rec_id'];
			$check=$this->checkStatus($weixiuArr['status'],$status);
			
			if($check['success']!=1)
			{
				$pdo42->rollback(); //事务回滚
				$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo13->rollback(); //事务回滚
				$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo27->rollback(); //事务回滚
				$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo41->rollback(); //事务回滚
                $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$check['error']=$id.":".$check['error'];
				Util::jsonExit($check);
			}
			$buchan = $weixiuArr['rec_id'];
			$order_sn =$weixiuArr['order_sn'];
			$repair_factory = $weixiuArr['repair_factory'];
			$goods_id = $weixiuArr['goods_id'];
			#2、如果货号不为空;是否售后维修,则货品状态已销售=是1、货品状态非已销售(或者查不到)=否0
			$api_res  = $WarehouseModel->getWarehouseGoodsByGoodsid($goods_id);
			if(empty($api_res)){
				$pdo42->rollback(); //事务回滚
				$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo13->rollback(); //事务回滚
				$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo27->rollback(); //事务回滚
				$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo41->rollback(); //事务回滚
                $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$result['error'] = $id.'对应的货号'.$goods_id.'在系統中不存在!!';
				Util::jsonExit($result);
			}else{
				$goods_info = $api_res;
			}
				
	
			$remark = _Request::get('remark_log');//	操作日志备注
	
			$arr_log = array(
					'do_id'			=>$id,
					'user_name'		=>$_SESSION['userName'],
					'do_type'		=>'app_order_weixiu,update'
			);
			//下单按钮  状态  下单时间  备注
			$str='';
	
				$str.="status=$status"; 
				$str.=",repair_man=".$_SESSION['userId'];
				$str.=",re_end_time='".date("Y-m-d H:i:s")."'";
				// 2015-10-24增加 
				$str.=",qc_status=1";
				// 质检次数:点击完毕或者批量完毕,次数再加1,--2015-10-24
				$str.=",qc_times=qc_times+1";
				
				$str.=",remark='维修单确认,布产号：".$buchan.';'.$remark."'";
 
			$res=$model->updateWeixiu($str,$id);
			
			if($res !== false)
			{
	
				//添加维修日志 1
				$arr_log['content'] ="维修单完毕,布产号：".$buchan.';'.$remark;
			    $res1=$model_log->saveData($arr_log,array());
			    if(!$res1){
			    	$pdo42->rollback(); //事务回滚
			    	$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			    	$pdo13->rollback(); //事务回滚
			    	$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			    	$pdo27->rollback(); //事务回滚
			    	$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                    $pdo41->rollback(); //事务回滚
                    $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			    	$result['error'] = '添加维修日志失败!';
			    	Util::jsonExit($result);
			    }
			    
	
				//添加订单日志 2
				$dd = new DictView(new DictModel(1));
				$weixiu_status = $dd->getEnum('weixiu.status',$status);
				$pro_name ='';
				$pro_list = $AppProcessorInfoModel->getProList();
				foreach ($pro_list as $val)
				{
					if($val['id']==$repair_factory){
						$pro_name =$val['name'];
					}
				}
				//获取维修 内容
				$repair_act = $weixiuArr['repair_act'];
				$arr_act = explode(',', $repair_act);
				$str_act = "";
				foreach ($arr_act as $val)
				{
					$str_act .= $dd->getEnum('weixiu.action',$val).",";
				}

				if($order_sn != ''){
                    $res2 = $SalesModel->AddOrderLog($order_sn,$_SESSION['userName'],'批量维修单操作，维修流水号：'.$id.',维修工厂：'.$pro_name.',布产号：'.$buchan.',维修状态:'.$weixiu_status);
                    if(!$res2){
                        $pdo42->rollback(); //事务回滚
                        $pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                        $pdo13->rollback(); //事务回滚
                        $pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                        $pdo27->rollback(); //事务回滚
                        $pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                        $pdo41->rollback(); //事务回滚
                        $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                        $result['error'] = '添加订单日志失败!';
                        Util::jsonExit($result);
                    }
                }
				
				//改变布产单维修状态
				//2015-10-27需要加上不为空判断
				if(!empty($rec_id))
				{
				$res3 = $AppProcessorInfoModel->editWeixiuStatus(substr($rec_id,2),$status);	
				
				if(!$res3){
					$pdo42->rollback(); //事务回滚
					$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
					$pdo13->rollback(); //事务回滚
					$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
					$pdo27->rollback(); //事务回滚
					$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                    $pdo41->rollback(); //事务回滚
                    $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
					$result['error'] = '改变布产单维修状态失败!';
					Util::jsonExit($result);
				}
				}
				
				//改变订单维修状态
			   
				if($rec_id!=''){
				  $res4 = $SalesModel->EditOrderGoodsInfo(substr($rec_id,2),$status);
				}
				
				//$ret = $model->change_weixiu_status($api_res['return_msg'][0]['order_goods_id'],array('weixiu_status'=>$status));
				if($goods_info['order_goods_id']){
					$res5= $SalesModel->EditOrderGoodsStatusById($goods_info['order_goods_id'],$status);
					if(!$res5){
						$pdo42->rollback(); //事务回滚
						$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
						$pdo13->rollback(); //事务回滚
						$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
						$pdo27->rollback(); //事务回滚
						$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                        $pdo41->rollback(); //事务回滚
                        $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
						$result['error'] = '改变订单维修状态失败!';
						Util::jsonExit($result);
					}
				}
				//添加布产日志 3
				if($buchan){
					$res6 =	$AppProcessorInfoModel->AddOrderLog(array('status'=>$status,'rec_id'=>$buchan,'create_user_id'=>$_SESSION['userId'],'create_user'=>$_SESSION['userName'],'remark'=>'批量维修单操作，维修流水号：'.$id.',维修工厂：'.$pro_name.',维修内容：'.$str_act.$remark.',维修状态：'.$weixiu_status));
	                if(!$res6){
	                	$pdo42->rollback(); //事务回滚
	                	$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
	                	$pdo13->rollback(); //事务回滚
	                	$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
	                	$pdo27->rollback(); //事务回滚
	                	$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                        $pdo41->rollback(); //事务回滚
                        $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
	                	$result['error'] = '添加布产日志失败!';
	                	Util::jsonExit($result);
	                }
				}
				
			}
			else
			{
				$pdo42->rollback(); //事务回滚
				$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo13->rollback(); //事务回滚
				$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo27->rollback(); //事务回滚
				$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                $pdo41->rollback(); //事务回滚
                $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$result['error'] = "操作失败";
				Util::jsonExit($result);
			}
		}
	      
		
		  $pdo42->commit(); //事务提交
		  $pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		  $pdo13->commit(); //事务回滚
		  $pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		  $pdo27->commit(); //事务回滚
		  $pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
          $pdo41->commit(); //事务回滚
          $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
          $result['success'] = 1;
		  Util::jsonExit($result);
		}catch (Exception $e){
			$pdo42->rollback(); //事务回滚
			$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			$pdo13->rollback(); //事务回滚
			$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			$pdo27->rollback(); //事务回滚
			$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
            $pdo41->rollback(); //事务回滚
            $pdo41->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			$result['error'] ="系统异常！error code:".__LINE__;
			Util::jsonExit($result);
		}	
	}
	
	/*
	 *add by zhangruiying
	 * 检查状态操作
	 *  2015/6/17
	 */
	function checkStatus($old_status,$new_status)
	{
		$result = array('success' =>1,'error' => '');
		if($old_status==7)
		{
			if($new_status==7)
			{
				$result['error']='该维修单已取消不能重复取消！';
			}
			else
			{
				$result['error']='该维修单已取消不能进行其它操作';
			}
			$result['success']=0;
		}
		elseif($old_status==6)
		{
			if($new_status!=7)
			{
				$result['error']='该维修单已收货不能进行该操作！';
				$result['success']=0;
			}
		}
		elseif($old_status==5)
		{
			if($new_status!=7 and $new_status!=6)
			{
				$result['error']='该维修单已维修完毕不能进行该操作！';
				$result['success']=0;
			}
		}
		return $result;
	
	}
	
	//添加日志
	public function add_weixiu_log($arr_log)
	{
		$model_log = new AppOrderWeixiuLogModel(41);
		return $model_log->saveData($arr_log,array());
	
	}
	
	//获取加工商信息
	public function get_pro_list($arr = array())
	{
		$model = new ApiDataModel();
		return $model->GetSupplierList($arr);
	}
}

?>