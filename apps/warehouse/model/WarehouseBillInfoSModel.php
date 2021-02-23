<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoSModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: HULICHAO <474333983@qq.com>
 *   @date		: 2015-01-18 17:09:51
 *   @update	:
	//销售单的model  liyanhong
 *  -------------------------------------------------
 */
class WarehouseBillInfoSModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = '';
		$this->pk='id';
		$this->_prefix='';
		$this->_dataObject = array();
		parent::__construct($id,$strConn);
	}

	/**
	* 1/生成销售单
	* 2/改变货品仓储状态
	* 3/改变销售政策状态
	* 4/配货单状态变更 [数据库app_order 中的 base_order_info表中 delivery_status改为 5 已配货]
	* @param $order_id String 订单ID
	* @param $order_sn String 订单编号
	* @param $goodsInfo Array 仓储货号字符串 与 order_goods_id 的对应关系的 “二维数组”
	* 格式： array(
	*			0=>array('goods_id'=>123456, 'order_goods_id'=>12 ),
	*			1=>array('goods_id'=>789, 'order_goods_id'=>11 ),
	*			.........................
	*		)
	* @param $order_money Float 订单总金额
	* @param $warehouegoods  配货的货品信息
	*/
	public function createBillInfoS($order_id, $order_sn, $goodsInfo, $order_money, $warehouegoods, $from_company_id, $from_company_name, $zhuancang_goods_arr){
		$successStatus = array('success' => 0, 'error' => '');
		$gwModel = new GoodsWarehouseModel(22);
		$model = new WarehouseBillModel(22);

		//记录货品下架出入库记录
		$boxGoodsLogModel = new BoxGoodsLogModel(22);
		$boxGoodsLogModel->addLog(array_column($goodsInfo,'goods_id'));

		//计算单据的成本价
		$goods_str = implode("','", $zhuancang_goods_arr);
		$goods_str = "'".$goods_str."'";
		$wmodel = new WarehouseModel(21);
		$goodsModel = new WarehouseGoodsModel(21);
		$bill_chengbenjia = $goodsModel->select2('sum(`mingyichengben`)', "`goods_id` IN ($goods_str)", $is_all = 1);
		$zhuancang_num = count($zhuancang_goods_arr);

		$time = date('Y-m-d H:i:s');
		$time_s = date('Y-m-d H:i:s',time()+1);
		$user = $_SESSION['userName'];
		$type = 'S';
		//$goods_num = count($goodsInfo);

		//根据订单号获取订单相关信息
		$orderInfo = ApiSalesModel::GetDeliveryStatus($order_sn , " order_status,order_pay_status,send_good_status ");
		$order_status = $orderInfo['return_msg']['order_status'];	//订单状态
		$order_pay_status = $orderInfo['return_msg']['order_pay_status'];		//支付状态
		$send_good_status = $orderInfo['return_msg']['send_good_status'];		//发货状态

		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务			

			//配货（销账）的时候，如果是深圳分公司销账，并且还是总公司的货，那么，就自动把这个货转仓到深圳公司
			if(count($zhuancang_goods_arr)){
				$sql = "INSERT INTO `warehouse_bill` (
					`bill_no`,  `to_company_id`, `to_warehouse_id`,
					`from_company_id`,`goods_num`,`goods_total`,
					`bill_note`,`to_warehouse_name`,
					`to_company_name`,`from_company_name`,
					`create_user`,`create_time`,`bill_type`,`order_sn`, `bill_status` , `check_time` , `check_user`
					) VALUES (
					'' , 445 , 503,
					58 , {$zhuancang_num} , {$bill_chengbenjia} ,
					'销账自动调拨(总公司调拨至深圳分公司)' , '深圳分公司' ,
					'BDD深圳分公司' , '总公司' , '{$_SESSION['userName']}' , '{$time}' , 'M' , '{$order_sn}' , 2 , '{$time}' , '{$_SESSION['userName']}'
					)";
				$pdo->query($sql);
				$id = $pdo->lastInsertId();

				//创建单据编号
				$bill_no = $model->create_bill_no('M',$id);
				$sql = "UPDATE `warehouse_bill` SET `bill_no`='{$bill_no}' WHERE `id`={$id}";
				$pdo->query($sql);

				$chengben_jiajia = 0;
				//写入明细
				foreach($zhuancang_goods_arr AS $key => $goods_id){
					$sql = "SELECT `goods_sn` , `goods_name` , `num` , `jinzhong` , `caizhi` , `yanse` , `jingdu` , `jinhao` , `mingyichengben` , `zhengshuhao`, `cat_type1` FROM `warehouse_goods` WHERE `goods_id` = '{$goods_id}' ";
					$goods_info = $this->db()->getRow($sql);
					//给金耗为空的赋值 0
					if($goods_info['jinhao'] == ''){
						$goods_info['jinhao'] = 0;
					}

					$model_jiajia = new WarehouseBillInfoYJiajialvModel(22);
					$jiajialv = $model_jiajia->getJiajialvByStyleTypeName( $goods_info['cat_type1'] );

					$sql = "INSERT INTO `warehouse_bill_goods` (
			  			`bill_id`, `bill_no`, `bill_type`, `goods_id`,
			  			`goods_sn`, `goods_name`, `num`, `jinzhong`,
			  			`caizhi`, `yanse`,`jingdu`,`jinhao`,
			  			`sale_price`, `zhengshuhao`, `addtime`, `jiajialv` 
			  			) VALUES (
			  			{$id}, '{$bill_no}', 'M', '{$goods_id}',
			  			'{$goods_info['goods_sn']}', '{$goods_info['goods_name']}', 1, '{$goods_info['jinzhong']}',
			  			'{$goods_info['caizhi']}', '{$goods_info['yanse']}', '{$goods_info['jingdu']}', {$goods_info['jinhao']},
			  			'{$goods_info['mingyichengben']}', '{$goods_info['zhengshuhao']}', '{$time}', $jiajialv) ";
					$pdo->query($sql);

					//变更仓库warehoue_goods 货品的所在仓库 所在公司
					$sql = "UPDATE `warehouse_goods` SET `company` = 'BDD深圳分公司' , `warehouse` = '深圳分公司' , `company_id` = 445, `warehouse_id` = 503 WHERE `goods_id` = '{$goods_id}'";
					$pdo->query($sql);

					$chengben_jiajia += ($goods_info['mingyichengben'] * (1 + $jiajialv/100));
				}

				$sql = "UPDATE `warehouse_bill` SET `goods_total_jiajia` ='{$chengben_jiajia}' WHERE `id`={$id}";
				// file_put_contents('e:/10.sql',$sql);
				$pdo->query($sql);

				//写入warehouse_bill_status信息
				$ip = Util::getClicentIp();
				$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$id}, '{$bill_no}', 1, '{$time}', '{$_SESSION['userName']}', '{$ip}') ";
				$pdo->query($sql);

				//推送数据到可销售商品列表，跟新可销售货品的所在仓库和公司
				/*
				$send_info = array();
				foreach($zhuancang_goods_arr as $key => $val){
					$send_info[$key]['goods_id'] = trim($val, "'") ;
					$send_info[$key]['warehouse_id'] = 503;
					$send_info[$key]['company_id'] = 445;
					$send_info[$key]['warehouse'] = '深圳分公司';
					$send_info[$key]['company'] = 'BDD深圳分公司';
				}
				
				$salepolicyModel = new ApiSalepolicyModel();
				$putres = $salepolicyModel->AddAppPayDetail($send_info);

				if($putres['error']){
					$pdo->query('');	//推送失败 回滚
				}
				*/

				//销售政策使销售政策商品状态变为上架 （注意：已绑定的商品不可推送上架）
				//$Apisalepolicymodel = new ApiSalepolicyModel();
				//已绑定的商品不可推送上架
				foreach($zhuancang_goods_arr AS $key => $val){
					$bing = $goodsModel->select2($fields = ' `order_goods_id` ' , $where = " `goods_id` = '{$val}' " , $is_all = 1);
					if($bing != 0){
						unset($zhuancang_goods_arr[$key]);
					}
				}
				
				//AsyncDelegate::dispatch("warehouse", array('event' => 'bill_M_checked', 'bill_id' => $id));
			}


			//生成销售单
		
			//var_dump($company_type);var_dump($sale_price);exit;
			$goods_num = count($warehouegoods);				
			$sql = "INSERT INTO `warehouse_bill` (`bill_no`, `bill_type`, `bill_status`, `goods_num`, `from_company_id`,`from_company_name`, `order_sn`, `goods_total`,  `shijia`, `create_user`, `create_time`) VALUES ('', '{$type}', 1, {$goods_num},{$from_company_id},'{$from_company_name}', '{$order_sn}', 0,  $order_money, '{$user}', '{$time_s}' )";

			$pdo->query($sql);
			$id = $pdo->lastInsertId();

			$bill_no = $model->create_bill_no($type,$id);
			$sql = "UPDATE `warehouse_bill` SET `bill_no` = '{$bill_no}' WHERE `id` = {$id}";
			$pdo->query($sql);

			$sql = "INSERT INTO `warehouse_bill_info_s` (`bill_id`) VALUES ({$id})";
			$new_goods_id = array();
			
			//查询公司类型  1直营店 2个体店 3经销商
			$sql = "select company_type from cuteframe.company where id={$from_company_id}";
			$company_type = $this->db()->getOne($sql);
			foreach ($warehouegoods as $key => $val) {
			    //如果类型是直营店，零售价显示货品的名义成本，如果类型是个体店或经销商，零售价取货品的批发价
			    $sale_price = $val['mingyichengben'];
			    if(SYS_SCOPE=="zhanting" && ($company_type ==2 or $company_type ==3)){
			        $a_price = (float)$val['jingxiaoshangchengbenjia'];//批发价+管理费 之和
			        $b_price = (float)$val['management_fee'];//管理费
			        $sale_price = $a_price - $b_price;
			    }
			    
				//生成销售单明细
				$sql = "INSERT INTO `warehouse_bill_goods`(
					`bill_id`, `bill_no`, `bill_type`, `goods_id`,
					`goods_sn`, `goods_name`, `num`,
					`caizhi`, `jinzhong`, `yanse`, `zuanshidaxiao`,
					`yuanshichengben`, `sale_price`,`shijia`, `addtime`,`detail_id`,`zhengshuhao`) VALUES (
					{$id}, '{$bill_no}', '{$type}', '{$val['goods_id']}',
					'{$val['goods_sn']}', '{$val['goods_name']}', {$val['num']},
					'{$val['caizhi']}', '{$val['jinzhong']}', '{$val['yanse']}', '{$val['zuanshidaxiao']}',
					'{$val['yuanshichengbenjia']}', '{$sale_price}', '{$val['xiaoshoujia']}', '{$time}','{$val['detail_id']}','{$val['zhengshuhao']}'
					)";
				$pdo->query($sql);
                
				$sql = "update warehouse_goods set order_goods_id='{$val['detail_id']}' where goods_id='{$val['goods_id']}'";
				$pdo->query($sql);
				//改变货品仓储状态 //update by lcr  确保更改的是在库存状态下的货号 避免并发状态下的重复销账
				$sql = "UPDATE `warehouse_goods` SET `is_on_sale`= 10, `box_sn` = '0-00-0-0' WHERE `goods_id` = '{$val['goods_id']}' and is_on_sale=2";
                
				$count=$pdo->exec($sql);
                if($count<>1){
							$pdo->rollback();//事务回滚
							$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交					
							$successStatus['error'] = '销账失败：货品不是库存状态 请检查订单是否已经销账 '. $val['goods_id'];
							return $successStatus;
                }   

				//货品下架
				$default_box = $gwModel->getDefaultBoxIdBygoods($val['goods_id']);
				if(!$default_box){
					// return array('success' => 0 , 'error'=> "货号：{$val['goods_id']} 不存在相应柜位信息,下架失败！导致制单失败");
					//如果不存在默认柜位，自动创建一条货品的默认柜位信息
					$res = $model->CheckAndCreateBox($val['goods_id']);
					if($res['success'] != 1){
						return array('success' => 0 , 'error' => $res['error']);
						$pdo->query('');
					}
					$default_box = $res['box_id'];
				}
				$sql = "UPDATE `goods_warehouse` SET `box_id` = {$default_box}, `warehouse_id` = {$val['warehouse_id']}, `create_user` = '' , `create_time` = '0000-00-00 00:00:00' WHERE `good_id` = '{$val['goods_id']}'";
				$pdo->query($sql);

				$new_goods_id[] = $val['goods_id'];
			}
			/*
			$model = new ApiSalepolicyModel();
			if(!$model->EditIsSaleStatus($new_goods_id,0,2))
			{
				//修改可销售商品为下架 失败制造错误回滚。
				$pdo->query('');
			}*/
            /*
			//(2)调用接口配货单状态变更
			$derdeliveryStatus = ApiSalesModel::EditOrderdeliveryStatus(array($order_sn),$status=5,$time,$user);
			if(!isset($derdeliveryStatus['error']))
			{
				$successStatus['error'] = '销账失败：配货单状态变更 接口异常';
				return $successStatus;
			}*/

			$sql="UPDATE app_order.base_order_info SET delivery_status='5' WHERE order_sn='{$order_sn}'";
			$count=$pdo->exec($sql);
                if($count<>1){
							$pdo->rollback();//事务回滚
							$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交					
							$successStatus['error'] = '销账失败：更新订单配货状态失败.';
							return $successStatus;
                }			

		}catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交			
			$successStatus['error'] = '销账失败：生成销售单程序异常，联系技术人员处理'.json_encode($e);
			return $successStatus;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		//回写订单操作日志
		ApiSalesModel::addOrderAction($order_sn , $user , '配货成功');
		return array('success' => 1);
	}

    /**
    * 1/生成销售单
    * 2/改变货品仓储状态
    * 3/改变销售政策状态
    * 4/配货单状态变更 [数据库app_order 中的 base_order_info表中 delivery_status改为 5 已配货]
    * @param $order_sn String 订单编号
    */
    public function createBillInfoSFromZhmd($order_sn, $from_company_id, $from_company_name, $user_name){
        $successStatus = array('success' => 0, 'error' => '');
        $gwModel = new GoodsWarehouseModel(22);
        $model = new WarehouseBillModel(22);

        $time = date('Y-m-d H:i:s');
        $time_s = date('Y-m-d H:i:s',time()+1);
        $user = $user_name;
        $type = 'S';

        //根据订单号获取订单相关信息
        $orderInfo = ApiSalesModel::GetDeliveryStatus($order_sn , " id, order_status,order_pay_status,send_good_status ");
        
        $order_id = $orderInfo['return_msg']['id'];//订单ID
        $order_status = $orderInfo['return_msg']['order_status'];   //订单状态
        $order_pay_status = $orderInfo['return_msg']['order_pay_status'];       //支付状态
        $send_good_status = $orderInfo['return_msg']['send_good_status'];       //发货状态

        $orderAccountInfo = ApiSalesModel::GetOrderAccountRow($order_id , " order_amount ");
        $order_amount = $orderAccountInfo['return_msg']['order_amount'];//订单总金额

        $orderDetailInfo = ApiSalesModel::GetDetailByOrderID($order_id);
        $warehouegoods = $orderDetailInfo['return_msg'];//订单商品信息

        $pdo = $this->db()->db();//pdo对象
        try{
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务       
            //生成销售单
            $goods_num = count($warehouegoods);
            $sql = "INSERT INTO `warehouse_bill` (`bill_no`, `bill_type`, `bill_status`, `goods_num`, `from_company_id`,`from_company_name`, `order_sn`, `goods_total`,  `shijia`, `create_user`, `create_time`, `check_user`, `check_time`) VALUES ('', '{$type}', 2, {$goods_num}, {$from_company_id}, '{$from_company_name}', '{$order_sn}', 0,  '{$order_amount}', '{$user}', '{$time_s}', '{$user}', '{$time_s}')";
        
            $pdo->query($sql);
            $id = $pdo->lastInsertId();

            $bill_no = $model->create_bill_no($type, $id);
            $sql = "UPDATE `warehouse_bill` SET `bill_no` = '{$bill_no}' WHERE `id` = {$id}";
            $pdo->query($sql);

            $sql = "INSERT INTO `warehouse_bill_info_s` (`bill_id`) VALUES ({$id})";
            $new_goods_id = array();
            
            //查询公司类型  1直营店 2个体店 3经销商
            $sql = "select company_type from cuteframe.company where id={$from_company_id}";
            $company_type = $this->db()->getOne($sql);
            foreach ($warehouegoods as $key => $value) {
                $sql = "select * from warehouse_goods where `goods_id` = '".$value['goods_id']."'";
                $val = $this->db()->getRow($sql);
                if(empty($val)){
                    $pdo->query("");
                }
                //如果类型是直营店，零售价显示货品的名义成本，如果类型是个体店或经销商，零售价取货品的批发价
                $sale_price = $val['mingyichengben'];
                if($company_type ==2 || $company_type ==3){//SYS_SCOPE=="zhanting" && 
                    $a_price = (float)$val['jingxiaoshangchengbenjia'];//批发价+管理费 之和
                    $b_price = (float)$val['management_fee'];//管理费
                    $sale_price = $a_price - $b_price;
                }

                //实价 xiaoshoujia
                if($value['favorable_status']==3){
                    $xiaoshoujia =  $value['goods_price']-$value['favorable_price'] ;
                }else{
                    $xiaoshoujia = $value['goods_price'] ;
                }
                
                //生成销售单明细
                $sql = "INSERT INTO `warehouse_bill_goods`(
                    `bill_id`, `bill_no`, `bill_type`, `goods_id`,
                    `goods_sn`, `goods_name`, `num`,
                    `caizhi`, `jinzhong`, `yanse`, `zuanshidaxiao`,
                    `yuanshichengben`, `sale_price`,`shijia`, `addtime`,`detail_id`,`zhengshuhao`) VALUES (
                    {$id}, '{$bill_no}', '{$type}', '{$val['goods_id']}',
                    '{$val['goods_sn']}', '{$val['goods_name']}', {$val['num']},
                    '{$val['caizhi']}', '{$val['jinzhong']}', '{$val['yanse']}', '{$val['zuanshidaxiao']}',
                    '{$val['yuanshichengbenjia']}', '{$sale_price}', '{$xiaoshoujia}', '{$time}','{$val['id']}','{$val['zhengshuhao']}'
                    )";
//file_put_contents('1.txt',$sql,FILE_APPEND);
                $pdo->query($sql);
                
                $sql = "update warehouse_goods set order_goods_id='{$val['id']}' where goods_id='{$val['goods_id']}'";
                $pdo->query($sql);
                //改变货品仓储状态 //update by lcr  确保更改的是在库存状态下的货号 避免并发状态下的重复销账
                $sql = "UPDATE `warehouse_goods` SET `is_on_sale`= 3, `box_sn` = '0-00-0-0' WHERE `goods_id` = '{$val['goods_id']}' and is_on_sale=2";
                $count=$pdo->exec($sql);
                if($count<>1){
                    $pdo->rollback();//事务回滚
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交                    
                    $successStatus['error'] = '货品不是库存状态 请检查订单是否已经销账 '. $val['goods_id'];
                    return $successStatus;
                }
                //货品下架
                $default_box = $gwModel->getDefaultBoxIdBygoods($val['goods_id']);
                if(!$default_box){
                    //如果不存在默认柜位，自动创建一条货品的默认柜位信息
                    $res = $model->CheckAndCreateBox($val['goods_id']);
                    if($res['success'] != 1){
                        return array('success' => 0 , 'error' => $res['error']);
                        $pdo->query('');
                    }
                    $default_box = $res['box_id'];
                }
                $sql = "UPDATE `goods_warehouse` SET `box_id` = {$default_box}, `warehouse_id` = {$val['warehouse_id']}, `create_user` = '' , `create_time` = '0000-00-00 00:00:00' WHERE `good_id` = '{$val['goods_id']}'";
                $pdo->query($sql);
                $new_goods_id[] = $val['goods_id'];
            }

            //更新订单状态
            $sql="UPDATE `app_order`.`base_order_info` SET `delivery_status`='5' WHERE `order_sn`='{$order_sn}'";
            $count=$pdo->exec($sql);
            if($count<>1){
                $pdo->rollback();//事务回滚
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交                    
                $successStatus['error'] = '更新订单配货状态失败.';
                return $successStatus;
            }
        }catch(Exception $e){//捕获异常
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交            
            $successStatus['error'] = '生成销售单程序异常，联系技术人员处理'.json_encode($e);
            return $successStatus;
        }
        $pdo->commit();//如果没有异常，就提交事务
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        //回写订单操作日志
        ApiSalesModel::addOrderAction($order_sn , $user , '配货成功');
        return array('success' => 1);
    }

	/** 根据order_sn 检测是否含有对应的销售单 **/
	public function checkSbillByOrderSn($order_sn){
		$sql = "SELECT `id`,`bill_status` FROM `warehouse_bill` WHERE `order_sn` = '{$order_sn}'";
		return $this->db()->getRow($sql);
	}

	/***
		检查是否有销售单/根据订单号检查销售单是否存在
	***/
	public function xiaoshou_exit($order_sn)
	{
		$sql = "select count(id) from warehouse_bill where order_sn ='{$order_sn}' and bill_type='S' and bill_status='1' ";
		return $this->db()->getOne($sql);
	}

	/*******
		检查是否有销售单，如果有则查处货号和明细id
	********/
	public function getGoodsidByOrderSn($order_sn)
	{
		$result = array('data'=>array(),'error'=>1,'msg'=>'错误');
		//只考虑一张销售单的情况 目前
		$sql = "SELECT `bill_no` FROM `warehouse_bill` WHERE `order_sn` = '{$order_sn}' and bill_type='S' and (bill_status='1' or bill_status='2') ";
		$res = $this->db()->getOne($sql);
		//有销售单
		if ($res)
		{
		//	$sql = "SELECT g.goods_id,g.order_goods_id FROM `warehouse_bill_goods` AS bg,`warehouse_goods` AS g WHERE bg.goods_id=g.goods_id AND bg.bill_no ='{$res}' AND g.order_goods_id != '' ";
			$sql = "SELECT g.goods_id,g.order_goods_id FROM `warehouse_bill_goods` AS bg,`warehouse_goods` AS g WHERE bg.goods_id=g.goods_id AND bg.bill_no ='{$res}' ";
			//echo $sql;exit;
			$r = $this->db()->getAll($sql);
			if ($r)
			{
				$result['error'] = 0;
				$result['data']  = $r;
				$result['msg']	 = "哈哈正确";
			}
			else
			{
				$result['error'] = 2;
				$result['msg']	 = "销售单存在，但是数据异常。";
			}
		}
		//没有销账
		else
		{
			$result['error'] = 3;
			$result['msg']	 = "销售单不存在";
		}
		return $result;
	}

	/***
	fun：cancel_info_s
	     质检不通过根据订单号 取消销售单
	***/
	public function cancel_info_s($order_sn,$data=null)
	{
		$error = ['status' => false , 'error' => '事物执行不成功，取消销售单失败'];
		$pdo = $this->db()->db();//pdo对象
		try
		{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			$sql="select g.goods_id from `warehouse_goods` AS g,`warehouse_bill` AS o,`warehouse_bill_goods` AS og  WHERE  o.order_sn ='{$order_sn}' and o.bill_type='S' and o.id = og.bill_id AND og.goods_id = g.goods_id  AND o.bill_status =1 AND g.is_on_sale =10 ";
			$res=$pdo->query($sql);	
			//var_dump(count($res->fetchAll()));		
			$selectCount=count($res->fetchAll());
			if($res===false || $selectCount==0){						
				$pdo->rollback();//事务回滚
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
				$error['error'] = '销售单的明细货品仓储状态不是“销售中”，不能取消';
				return $error;
            }

			$sql = "UPDATE `warehouse_goods` AS g,`warehouse_bill` AS o,`warehouse_bill_goods` AS og SET g.`is_on_sale` =2 WHERE  o.order_sn ='{$order_sn}' and o.bill_type='S' and o.id = og.bill_id AND og.goods_id = g.goods_id  AND o.bill_status =1 AND g.is_on_sale =10 ";			//var_dump($sql);exit;
			$resCount=$pdo->exec($sql);
			if($selectCount != $resCount){				
				$pdo->rollback();//事务回滚
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
				$error['error'] = '变更销售单明细仓储状态为：库存，不成功。';
				return $error;			
			}			
			//$sql = "UPDATE `warehouse_goods` AS g,`warehouse_bill` AS o,`warehouse_bill_goods` AS og SET g.`is_on_sale` =2 WHERE  o.order_sn ='{$order_sn}' and o.bill_type='S' and o.id = og.bill_id AND og.goods_id = g.goods_id  AND o.bill_status =1 AND g.is_on_sale =10 ";			//var_dump($sql);exit;
			//$pdo->query($sql);
			$sql = "UPDATE `warehouse_bill` SET bill_status=3 WHERE order_sn ='{$order_sn}' AND bill_type='S' AND bill_status=1 ";
			$resCount=$pdo->exec($sql);
			if($resCount==0){				
				$pdo->rollback();//事务回滚
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
				$error['error'] = '变更销售单单据状态为：取消，不成功。';
				return $error;					
			}				

			$model = new ApiSalepolicyModel();
			//查出绑定detail_id
			$sql = "select `goods_id` from `warehouse_bill` AS o,`warehouse_bill_goods` AS og  WHERE o.order_sn ='{$order_sn}' and o.bill_type='S' and o.id=og.bill_id ";
			$res = $this->db()->getAll($sql);
			$new_goods_id=[];
			foreach($res as $val){
				$new_goods_id[]=$val["goods_id"];
			}
			$model = new ApiSalepolicyModel();
			if(!$model->EditIsSaleStatus($new_goods_id, 1 ,1))
			{
				//修改可销售商品为上架 失败制造错误回滚。
				$pdo->query('');
			}
		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return $error;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return ['status' => true , 'error' => ''];
	}
		/***
	fun：check_info_s
	     物流发货 订单号 审核销售单
	***/
	public function check_info_s($order_sn)
	{
		$pdo = $this->db()->db();//pdo对象
		try
		{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务 修改货品状态改为已销售
			$sql = "UPDATE `warehouse_goods` AS g,`warehouse_bill` AS o,`warehouse_bill_goods` AS og SET g.`is_on_sale` =3 WHERE  o.order_sn ='{$order_sn}' and o.bill_type='S' and o.id = og.bill_id AND og.goods_id = g.goods_id  AND o.bill_status =1 AND g.is_on_sale =10 ";			//var_dump($sql);exit;
			$pdo->query($sql);
			$sql = "update `warehouse_bill` set bill_status=2 where order_sn ='{$order_sn}' and bill_type='S' and bill_status=1 ";
			$pdo->query($sql);

		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;
	}

	/***************************************************************
	function:getXiaoshouInfo
	description:根据销售单号查询销售单信息
	***************************************************************/
	public function getXiaoshouInfo($bill_no)
	{

		$sql = "SELECT wb.id,wb.bill_no,wb.from_company_id,wb.order_sn,wb.shijia as zongshijia,wb.bill_status,wb.check_time,wbg.goods_id,wbg.shijia,wbg.yuanshichengben FROM warehouse_bill AS wb LEFT JOIN  warehouse_bill_goods AS wbg ON wb.bill_no = wbg.bill_no WHERE wb.bill_no='{$bill_no}' ";

		//echo $sql;exit;
		return $this->db()->getAll($sql);
	}

	public function getBillXiaoshou($bill_id) {
		$sql = "SELECT `a`.`prc_name`,`a`.`put_in_type`,`b`.`goods_id`, `b`.`goods_sn`,  `b`.`chengbenjia`, `b`.`jinzhong`, `b`.`caizhi`, `b`.`yanse`, `b`.`jingdu`, `b`.`jinhao`, `b`.`chengbenjia`, `b`.`zhengshuhao`,`b`.`goods_name`,`a`.`zhushilishu`,`a`.`fushilishu`,`a`.`fushizhong` FROM  `warehouse_bill_goods` AS `b` LEFT JOIN `warehouse_goods` AS `a` ON `a`.`goods_id` = `b`.`goods_id` AND `b`.`bill_type`='S' WHERE `b`.`bill_id`={$bill_id} ";
		return $this->db()->getRow($sql);
	}
	/****************************************************************************
	fucntion : addGoods
	description:向销售单中添加商品
	para: goods_info:需要添加的货号信息 order_info:需要修改商品的销售价
	******************************************************************************/
	public function addGoods($goods_info,$order_info)
	{
		#1、将商品状态修改（销售中）
		#2、保存到商品明细中
		#3、修改该销售单的总数和销售单明细表中的销售金额
		#4、销售政策中该商品下架
		#5、仓储货品下架
		//var_dump($order_info);exit;
		$pdo     = $this->db()->db();//pdo对象
		$id      = $order_info[0]['id'];
		$bill_no = $order_info[0]['bill_no'];
		$bill_status=$order_info[0]['bill_status'];
		$from_company_id = $order_info[0]['from_company_id'];
		
		if($bill_status==1){
			$is_on_sale=10;
		}elseif ($bill_status==2){
			$is_on_sale=3;
		}else{
			$is_on_sale=2;
		}
		$time    = date("Y-m-d H:i:s");
		//查询公司类型  1直营店 2个体店 3经销商
		$sql = "select company_type from cuteframe.company where id={$from_company_id}";
		$company_type = $this->db()->getOne($sql);
		//如果类型是直营店，零售价显示货品的名义成本，如果类型是个体店或经销商，零售价取货品的批发价
		try
		{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			#1、将商品状态修改（销售中）
			//update by luochuanrong
			$sql = "update warehouse_goods set is_on_sale = {$is_on_sale} where goods_id = '{$goods_info['goods_id']}' and is_on_sale=2";
			//$pdo->query($sql);
			$changed=$pdo->exec($sql);  
			if($changed<>1) {  
					$pdo->rollback();//事务回滚
					$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
					//return array('success'=> 0 , 'error'=>'货品状态不是库存中');
					return false;
			} 			
			#2、保存到商品明细中
			$sale_price = $goods_info['mingyichengben'];
			if(SYS_SCOPE=="zhanting" && ($company_type ==2 or $company_type ==3)){
			    $a_price = (float)$goods_info['jingxiaoshangchengbenjia'];//批发价+管理费 之和
			    $b_price = (float)$goods_info['management_fee'];//管理费
		        $sale_price = $a_price - $b_price;
			}
			#`detail_id` 货品中的订单明细id
			$sql = "INSERT INTO `warehouse_bill_goods`(
				`bill_id`, `bill_no`, `bill_type`, `goods_id`,
				`goods_sn`, `goods_name`, `num`,
				`caizhi`, `jinzhong`, `yanse`, `zuanshidaxiao`,
				`yuanshichengben`, `sale_price`,`shijia`, `addtime`,`detail_id`) VALUES (
				{$id}, '{$bill_no}', 'S', '{$goods_info['goods_id']}',
				'{$goods_info['goods_sn']}', '{$goods_info['goods_name']}', {$goods_info['num']},
				'{$goods_info['caizhi']}', '{$goods_info['jinzhong']}', '{$goods_info['yanse']}', '{$goods_info['zuanshidaxiao']}',
				'{$goods_info['yuanshichengbenjia']}', '{$sale_price}', '0', '{$time}','0'
				)";
			//echo $sql;exit;
			$pdo->query($sql);

			#3、修改该销售单货品数量和销售单明细表中的销售金额
			foreach ($order_info as $key=>$val)
			{
				 $sql  = "update `warehouse_bill_goods` set shijia = '{$val['xiaoshoujia_goods']}' where goods_id = {$val['goods_id']} and bill_id={$id}";
				//file_put_contents('d://2.txt',$sql."\r\n",FILE_APPEND);
				//echo $sql."<br>";
				$pdo->query($sql);
			}
			$sql = "update `warehouse_bill` set goods_num=goods_num+1 where id={$id}";
			$pdo->query($sql);

			#4、销售政策中该商品下架
			$goods_id = $goods_info['goods_id'];
			$model    = new ApiSalepolicyModel();
			if(!$model->EditIsSaleStatus(array($goods_id),0,2))
			{
				$pdo->rollback();//事务回滚
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
				return false;
			}

			#5、货品下架
			$gwModel	  = new GoodsWarehouseModel(21);
			$default_box  = $gwModel->getDefaultBoxIdBygoods($goods_id);
			if($default_box)
			{
				$sql = "UPDATE `goods_warehouse` SET `box_id` = {$default_box} , `create_user` = '' , `create_time` = '0000-00-00 00:00:00' WHERE `good_id` = '{$goods_id }'";
				$pdo->query($sql);
			}
		}
		catch(Exception $e){//捕获异常
			echo $sql;
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;

	}

}/**END CLASS **/
?>
