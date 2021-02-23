<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoPModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-23 21:35:23
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoPModel extends Model
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
	* 普通查询
	*/
	public function select2($fields = '*' , $where = ' 1 LIMIT 1 ' , $type = 'one'){
		$sql = "SELECT {$fields} FROM `warehouse_bill_info_p` WHERE {$where}";
		if($type == 'one'){
			return $this->db()->getOne($sql);
		}else if($type == 'row'){
			return $this->db()->getRow($sql);
		}else if($type == 'all'){
			return $this->db()->getAll($sql);
		}
	}

	/**
	* 联表查询 获取编辑时的单据明细
	*/
	public function GetDetailByBillId($bill_id){
		$sql = "SELECT `a`.`goods_id` , `a`.`goods_sn` , `a`.`goods_name` , `a`.`jinzhong` ,b.put_in_type, `a`.`jingdu` , `a`.`yanse` , `a`.`zhengshuhao` , `b`.`zuanshidaxiao` ,`a`.`sale_price`, `a`.`shijia` , `a`.`pifajia` , `a`.`shijia` - `a`.`pifajia` AS `chajia` , `a`.`num` ,`a`.`order_sn` ,a.pinhao,a.xiangci,a.p_sn_out,b.yuanshichengbenjia,b.jingxiaoshangchengbenjia, a.management_fee, `b`.`caizhi`, `b`.`zhushi`, `b`.`zhushilishu` , `b`.`fushi`, `b`.`fushizhong` , `b`.`fushilishu`, `b`.`shoucun` , `b`.`changdu` , `b`.`zongzhong`, `b`.`fushizhong`, (case `a`.`dep_settlement_type` when 1 then '未结算' when 2 then '已结算' when 3 then '已退货' else '' end) dep_settlement_type, `a`.`settlement_time`,`a`.`label_price` FROM  `warehouse_bill_goods` AS `a` INNER JOIN `warehouse_goods` AS `b` ON `a`.`goods_id` = `b`.`goods_id` WHERE  `a`.`bill_id` = {$bill_id} ORDER BY `a`.`id` asc";
		return $this->db()->getAll($sql);
	}

	/**
	* 添加
	*/
	public function add_shiwu($bill_info , $goods_list){
		$time = date('Y-m-d H:i:s');
		$user = $_SESSION['userName'];
		$billModel = new WarehouseBillModel(22);
		$gwModel = new GoodsWarehouseModel(22);
		$type = 'P';

		//记录货品下架出入库记录
		$boxGoodsLogModel = new BoxGoodsLogModel(22);
		$boxGoodsLogModel->addLog(array_column($goods_list,0));

		$put_in_type = '';
		if (isset($bill_info['put_in_type']) && !empty($bill_info['put_in_type'])) {
			$put_in_type = $bill_info['put_in_type'];
		}
		
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//业务逻辑开始
			//写入主表信息
			if (isset($bill_info['to_company_id'])) {
				$sql = "INSERT INTO `warehouse_bill` (`bill_no`, `bill_type`, `goods_num` , `bill_note` , `goods_total`,`shijia` , `pifajia` , `create_user` , `create_time` ,`from_company_id` , `from_company_name`, `to_customer_id`, `to_company_id`, `to_company_name`, `to_warehouse_id`, `to_warehouse_name`, `out_warehouse_type`,`label_price_total`,`p_type`,is_invoice) VALUES ('' , '{$type}' ,  {$bill_info['goods_num']} , '{$bill_info['bill_note']}' , {$bill_info['goods_total']} , {$bill_info['shijia']} , {$bill_info['pifajia']} , '{$user}' , '{$time}' , {$bill_info['from_company_id']} , '{$bill_info['from_company_name']}', {$bill_info['wholesale_user']}, {$bill_info['to_company_id']}, '{$bill_info['to_company_name']}', {$bill_info['to_warehouse_id']}, '{$bill_info['to_warehouse_name']}', '{$bill_info['out_warehouse_type']}', '{$bill_info['label_price_total']}', '{$bill_info['p_type']}','{$bill_info['is_invoice']}')";
			} else {
                $sql = "INSERT INTO `warehouse_bill` (`bill_no`, `bill_type`, `goods_num` , `bill_note` , `goods_total`,`shijia` , `pifajia` , `create_user` , `create_time` ,`from_company_id` , `from_company_name`, `to_customer_id`, `out_warehouse_type`,`label_price_total`,`p_type`,is_invoice) VALUES ('' , '{$type}' ,  {$bill_info['goods_num']} , '{$bill_info['bill_note']}' , {$bill_info['goods_total']} , {$bill_info['shijia']} , {$bill_info['pifajia']} , '{$user}' , '{$time}' , {$bill_info['from_company_id']} , '{$bill_info['from_company_name']}', {$bill_info['wholesale_user']}, '{$bill_info['out_warehouse_type']}', '{$bill_info['label_price_total']}', '{$bill_info['p_type']}','{$bill_info['is_invoice']}')";
			}			

			$pdo->query($sql);
			$id = $pdo->lastInsertId();
			$bill_no = $billModel->create_bill_no($type,$id);
			if ($put_in_type) {
				$sql = "UPDATE `warehouse_bill` SET `bill_no` = '{$bill_no}', `put_in_type`='{$put_in_type}' WHERE `id` = {$id}";
			} else {
				$sql = "UPDATE `warehouse_bill` SET `bill_no` = '{$bill_no}' WHERE `id` = {$id}";
			}
			$pdo->query($sql);
			//写入附表信息
			//$sql = "INSERT INTO `warehouse_bill_info_p` (`bill_id`) VALUES ({$id})";
			//$pdo->query($sql);
			//写入明细

			foreach($goods_list AS $key => $val){
		        $val[28]  = empty($val[28])?0:$val[28];//管理费 数字类型
                $val[7]  = empty($val[7])?0:$val[7];
				$sql = "INSERT INTO `warehouse_bill_goods` (
					`bill_id` , `bill_no` , `bill_type` , `goods_id` , `goods_sn` ,
					`goods_name` , `num` , `jinzhong` , `jingdu` , `yanse` , `zhengshuhao` ,
					`zuanshidaxiao` ,`sale_price`, `shijia` , `pifajia` ,  `addtime`,`pinhao`,`xiangci`,`p_sn_out`,`management_fee`,`label_price`
					) VALUES (
					{$id} , '{$bill_no}' , '{$type}' , '{$val[0]}' , '{$val[1]}',
					'{$val[20]}' , 1 , '{$val[2]}' , '{$val[18]}' , '{$val[19]}' , '{$val[4]}' ,
					'{$val[3]}' ,'{$val[5]}' , '{$val[7]}' , '{$val[6]}' , '{$time}','{$val[22]}','{$val[23]}','{$val[24]}','{$val[28]}','{$val[29]}'
					)";
				$pdo->query($sql);

				//改变货品仓储状态
				$sql = "UPDATE `warehouse_goods` SET `is_on_sale`= 10, `box_sn` = '0-00-0-0' WHERE `goods_id` = '{$val[0]}' and `is_on_sale`= 2";
				//$pdo->query($sql);
				$changed=$pdo->exec($sql);  
			    if($changed<>1) {  
						$pdo->rollback();//事务回滚
						$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
						return array('success'=> 0 , 'error'=>'货品状态不是库存中'.$val[0]);
			    }

				//货品下架
				$default_box = $gwModel->getDefaultBoxIdBygoods($val[0]);
				if(!$default_box){
					// return array('success' => 0 , 'error'=> "货号：{$val[0]} 不存在相应柜位信息,下架失败！导致制单失败");
					//如果不存在默认柜位，自动创建一条货品的默认柜位信息
					$res = $billModel->CheckAndCreateBox($val[0]);
					if($res['success'] != 1){
						return array('success' => 0 , 'error' => $res['error']);
						$pdo->query('');
					}
					$default_box = $res['box_id'];
				}
				$sql = "UPDATE `goods_warehouse` SET `box_id` = {$default_box} , `create_user` = '' , `create_time` = '0000-00-00 00:00:00' WHERE `good_id` = '{$val[0]}'";
				$pdo->query($sql);
				$new_goods_id[] = $val[0];				

				//更新销售政策为 下架状态
				//$sql = "UPDATE front.`base_salepolicy_goods` SET `is_sale`= 0 ,`is_valid`=2 WHERE `goods_id`='{$val[0]}'";
				//$pdo->query($sql);
			}
			//重新核算价格
			$sql = "select sum(sale_price) as goods_total,sum(shijia) as shijia,sum(pifajia) as pifajia from warehouse_bill_goods where bill_id ={$id}";
			$price_row = $this->db()->getRow($sql);
			$sql = "update warehouse_bill set goods_total='{$price_row['goods_total']}',shijia='{$price_row['shijia']}',pifajia='{$price_row['pifajia']}' where id='{$id}'";
			$pdo->query($sql);
			//写入warehouse_bill_status信息
			$update_time = date('Y-m-d H:i:s');
			$ip = Util::getClicentIp();
			$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$id}, '{$bill_no}', 1 , '{$time}', '{$user}', '{$ip}') ";
			$pdo->query($sql);
				
			/* $model = new ApiSalepolicyModel();
			if(!$model->EditIsSaleStatus($new_goods_id, 0 , 2))
			{
				//修改可销售商品为下架 失败制造错误回滚。
				$pdo->query('');
			} */

			//业务逻辑结束
		}
		catch(Exception $e){//捕获异常
			//echo $sql;die;
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 0 , 'error' =>'事物操作不成功，导致添加批发销售单失败'.$sql);
		}
			$pdo->commit();//如果没有异常，就提交事务
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 1 , 'x_id' => $id, 'label'=>$bill_no);
	}


	/**
	* 编辑
	*/
	public function update_shiwu($bill_info , $add_goods_list){
		$user = $_SESSION['userName'];
		$time = date('Y-m-d H:i:s');
		$type = 'P';

		//记录货品下架出入库记录
		$boxGoodsLogModel = new BoxGoodsLogModel(22);
		$boxGoodsLogModel->addLog(array_column($add_goods_list,0));

		//获取老明细的货号集合
		$warehouseGoodsModel = new WarehouseGoodsModel(21);
		$gmodel = new WarehouseBillGoodsModel(21);
		$gwModel = new GoodsWarehouseModel(22);
		$billModel = new WarehouseBillModel(22);
		$oldDetail = $gmodel->select2($fields = '`goods_id`,`bill_no`', $where = "`bill_id`={$bill_info['id']}" , $is_all = 3);
		$old_goods_ids = '';
		$bill_no = '';
		foreach($oldDetail AS $key => $val){
			$old_goods_ids .= "'".$val['goods_id']."',";
			$bill_no = $val['bill_no'];
		}
		$old_goods_ids = rtrim($old_goods_ids , ',');

		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//业务逻辑开始
			#1变更主表信息
			if (isset($bill_info['to_company_id'])) {
			     $sql = "UPDATE `warehouse_bill` SET `bill_note` = '{$bill_info['bill_note']}' , `goods_num` = {$bill_info['goods_num']} , `goods_total` = {$bill_info['goods_total']} ,`shijia` = {$bill_info['shijia']} , `pifajia` = {$bill_info['pifajia']}, `to_customer_id` = {$bill_info['wholesale_user']}, `to_company_id` = {$bill_info['to_company_id']}, `to_company_name` = '{$bill_info['to_company_name']}', `to_warehouse_id`={$bill_info['to_warehouse_id']}, `to_warehouse_name`= '{$bill_info['to_warehouse_name']}', `out_warehouse_type` = '{$bill_info['out_warehouse_type']}', `label_price_total` = '{$bill_info['label_price_total']}',`p_type` = '{$bill_info['p_type']}' WHERE `id` = {$bill_info['id']}";
			} else {
			     $sql = "UPDATE `warehouse_bill` SET `bill_note` = '{$bill_info['bill_note']}' , `goods_num` = {$bill_info['goods_num']} , `goods_total` = {$bill_info['goods_total']} ,`shijia` = {$bill_info['shijia']} , `pifajia` = {$bill_info['pifajia']}, `to_customer_id` = {$bill_info['wholesale_user']}, `out_warehouse_type` = '{$bill_info['out_warehouse_type']}', `label_price_total` = '{$bill_info['label_price_total']}',`p_type` = '{$bill_info['p_type']}' WHERE `id` = {$bill_info['id']}";
			}
			$pdo->query($sql);

			#变更附表信息
			// $sql = "UPDATE `warehouse_bill_info_p` SET `wholesale_user` = {$bill_info['wholesale_user']} WHERE `bill_id` = {$bill_info['id']}";
			// $pdo->query($sql);

			#2 删除老的明细，同时恢复老明细的仓储状态为库存
			$sql = "DELETE FROM `warehouse_bill_goods` WHERE `bill_id` = {$bill_info['id']}";
			$pdo->query($sql);
			$sql = "UPDATE `warehouse_goods` SET `is_on_sale` = 2 WHERE `goods_id` IN ($old_goods_ids) and `is_on_sale` =10";
			$pdo->query($sql);
            $SalesModel=new SalesModel(27);
			#3写入新的明细 ，变更仓储状态，仓储下架
			foreach($add_goods_list AS $key => $val){
				$order_sn=$val[21];
				$goods_id=$val[0];
				$detial_id=$SalesModel->getOrderDetailsIdBySn($order_sn,$goods_id);
				if(!$detial_id){
					$detial_id=0;
				}
                if($val[28] == ''){
                    $val[28] = 0;
                }
                $val[7]  = empty($val[7])?0:$val[7];
				$pinhao = $val[22];
				$xiangci = $val[23];
				$sql = "INSERT INTO `warehouse_bill_goods` (
					`bill_id` , `bill_no` , `bill_type` , `goods_id` , `goods_sn` ,
					`goods_name` , `num` , `jinzhong` , `jingdu` , `yanse` , `zhengshuhao` ,
					`zuanshidaxiao` , `sale_price`,`shijia` , `pifajia` ,  `addtime`,`order_sn`,`detail_id`,`pinhao`,`xiangci`,`p_sn_out`,`management_fee`,`label_price`
					) VALUES (
					{$bill_info['id']} , '{$bill_no}' , '{$type}' , '{$val[0]}' , '{$val[1]}',
					'{$val[20]}' , 1 , '{$val[2]}' , '{$val[18]}' , '{$val[19]}' , '{$val[4]}' ,
					'{$val[3]}' , '{$val[5]}','{$val[7]}' , '{$val[6]}' , '{$time}','{$order_sn}',$detial_id,'{$pinhao}','{$xiangci}','{$val[24]}','{$val[28]}','{$val[29]}'
					)";
				$pdo->query($sql);

				//改变货品仓储状态
				$sql = "UPDATE `warehouse_goods` SET `is_on_sale`= 10, `box_sn` = '0-00-0-0' WHERE `goods_id` = '{$val[0]}' and `is_on_sale` in (2,10)";
				//$pdo->query($sql);
				$changed=$pdo->exec($sql);  
/*			    if($changed<>1) {  
						$pdo->rollback();//事务回滚
						$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
						return array('success'=> 0 , 'error'=>'货品状态不是库存中或者销售中'.$val[0]);
			    }*/
				//江湖救急的处理
				/*$default_box = $gwModel->getDefaultBoxIdBygoods($val[0]);
				if($default_box){
					$sql = "UPDATE `goods_warehouse` SET `box_id` = {$default_box} , `create_user` = '' , `create_time` = '0000-00-00 00:00:00' WHERE `good_id` = '{$val[0]}'";
					$pdo->query($sql);
				}*/

				//正常流程
				//货品下架
				$default_box = $gwModel->getDefaultBoxIdBygoods($val[0]);
				if(!$default_box){
					// return array('success' => 0 , 'error'=> "货号：{$val[0]} 不存在相应柜位信息,下架失败！导致制单失败");
					//如果不存在默认柜位，自动创建一条货品的默认柜位信息
					$res = $billModel->CheckAndCreateBox($val[0]);
					if($res['success'] != 1){
						return array('success' => 0 , 'error' => $res['error']);
						$pdo->query('');
					}
					$default_box = $res['box_id'];
				}
				$sql = "UPDATE `goods_warehouse` SET `box_id` = {$default_box} , `create_user` = '' , `create_time` = '0000-00-00 00:00:00' WHERE `good_id` = '{$val[0]}'";
				$pdo->query($sql);
				$new_goods_id[] = $val[0];
			}
			//重新核算价格
			$sql = "select sum(sale_price) as goods_total,sum(shijia) as shijia,sum(pifajia) as pifajia from warehouse_bill_goods where bill_id ={$bill_info['id']}";
			$price_row = $this->db()->getRow($sql);
			$sql = "update warehouse_bill set goods_total='{$price_row['goods_total']}',shijia='{$price_row['shijia']}',pifajia='{$price_row['pifajia']}' where id={$bill_info['id']}";
			$pdo->query($sql);
			#4写入单据操作日志
			$ip = Util::getClicentIp();
			$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$bill_info['id']}, '{$bill_no}', 1 , '{$time}', '{$user}', '{$ip}') ";
			$pdo->query($sql);

			#5 老明细销售政策自动上架 ， 新明细销售政策自动下架
			/*
			$apimodel = new ApiSalepolicyModel();
			$old_goods_ids = str_replace("'","",$old_goods_ids);
			$old_goods_ids = explode(",",$old_goods_ids);
			//已绑定的商品不可推送上架
			foreach($old_goods_ids AS $key => $val){
				$bing = $warehouseGoodsModel->select2($fields = ' `order_goods_id` ' , $where = " `goods_id` = '{$val}' " , $is_all = 1);
				if($bing != 0){
					unset($old_goods_ids[$key]);
				}
			}
			if(!$apimodel->EditIsSaleStatus($new_goods_id, 0 , 2 , $old_goods_ids))
			{
				//修改可销售商品为下架 失败制造错误回滚。
				$pdo->query('');
			}
			//业务逻辑结束
			 * */
		}
		catch(Exception $e){//捕获异常
			//echo $sql;die;
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 0 , 'error' =>'事物操作不成功，导致修改批发销售单失败'.$sql);
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return array('success' => 1 , 'error' =>'修改成功');
	}

	/**
	* 审核
	*/
	public function checkBill($bill_id , $bill_no){
		$user = $_SESSION['userName'];
		$time = date('Y-m-d H:i:s');
		$type = 'P';

		$gdModel = new WarehouseBillGoodsModel(21);
		$goods = $gdModel->select2('`goods_id`,`shijia`', $where = "`bill_id` = {$bill_id}" , $is_all = 3);
		$goods_ids = '';
		foreach($goods as $key => $val){
			if($val['shijia'] <= 0){
                return array('success' => 0 , 'error' =>'货号'.$val['goods_id'].'批发价不能小于0!');
                exit;
			}
			$goods_ids .= "'".$val['goods_id']."',";
		}
		$goods_ids = rtrim($goods_ids , ',');
		
		// 签收公司信息
		$to_company = $this->db()->getRow("select to_company_id, to_company_name from `warehouse_bill` where `id`={$bill_id}");
		$to_company_id = empty( $to_company ) ? 0 : $to_company['to_company_id'];

        $out_warehouse_type = $this->db()->getOne("select out_warehouse_type from warehouse_bill WHERE `id`={$bill_id}");

		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//业务逻辑开始

			#更改主表状态 warehouse_bill
      	    $sql = "UPDATE `warehouse_bill` SET `bill_status` = 2, `check_time`='{$time}', `check_user` = '{$user}' WHERE `id`={$bill_id}";
			$pdo->query($sql);

			#写入warehouse_bill_status 表
			$ip = Util::getClicentIp();
			$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$bill_id}, '{$bill_no}', 2, '{$time}'
				 , '{$user}', '{$ip}')";
			$pdo->query($sql);

			#变更明细们的状态，变成已销售
			/*
			 *  boss-1371, 如果P单需要签收，则 更新P单货品为收货中状态，归属公司为批发客户的签收公司
			 */
			if ($to_company_id > 0) {
			    $sql = "UPDATE `warehouse_goods` SET `is_on_sale` = 1, `company_id` = {$to_company_id}, `company`='{$to_company['to_company_name']}' WHERE `goods_id` IN ({$goods_ids}) and `is_on_sale` =10";
			} else {
			    $sql = "UPDATE `warehouse_goods` SET `is_on_sale` = 3,`chuku_time`='{$time}' WHERE `goods_id` IN ({$goods_ids}) and `is_on_sale` =10";
			}
			//$pdo->query($sql);
			$changed=$pdo->exec($sql);  
			if($changed<>count(explode(",",$goods_ids))) {  
				$pdo->rollback();//事务回滚
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
				return array('success'=> 0 , 'error'=>'货品状态不是销售中'.$goods_ids);
			}
			//业务逻辑结束
            #记录出库时间，写入库龄表 warehouse_goods_age；
            //1、货品已出库，状态为已返厂，记录出库时间endtime（货品库龄已结束，之后不再统计）；
            $sql = "UPDATE `warehouse_goods_age` SET `endtime` = '{$time}' WHERE `goods_id` IN (".$goods_ids.")";
            $pdo->query($sql);

            //门店结算BOSS-1385
            //单据审核时：出库类型是【购买】，货品的门店结算方式默认是【已结算】，结算操作时间为单据的审核时间；出库类型是【借货】，门店结算方式是【未结算】
            if($out_warehouse_type == '1'){
                $dep_settlement_type = 2;
                $sql = "UPDATE `warehouse_bill_goods` SET `dep_settlement_type` = {$dep_settlement_type},`settlement_time` = '{$time}' WHERE `bill_id`= {$bill_id}";
            }else{
                $dep_settlement_type = 1;
                $sql = "UPDATE `warehouse_bill_goods` SET `dep_settlement_type` = {$dep_settlement_type} WHERE `bill_id`= {$bill_id}";
            }
            
            $pdo->query($sql);
			#记录出库时间，写入库龄表；
			/*$arr = explode(",",$goods_ids);
			foreach ($arr as $goods_id) {
				# code...
				$sql = "select `id`,`goods_id`,`addtime`,`change_time` from `warehouse_goods` where `goods_id` = {$goods_id}";
				$goods_info = $this->db()->getRow($sql);
				$sql = "select `id` from `warehouse_goods_age` where `goods_id` = {$goods_id}";
				$goods_age = $this->db()->getRow($sql);
				$newtime = date("Y-m-d H:i:s",time());
				$total_age = intval(((strtotime($newtime)-strtotime($goods_info['addtime']))/86400));
				$self_age = intval(((strtotime($newtime)-strtotime($goods_info['change_time']))/86400));
				if(!$goods_age){
					$sql = "insert into `warehouse_goods_age` (`warehouse_id`,`goods_id`,`endtime`,`total_age`,`self_age`)values(".$goods_info['id'].",".$goods_info['goods_id'].",'".$newtime."','".$total_age."','".$self_age."')";
				}else{
					$sql = "update `warehouse_goods_age` set `endtime` = '".$newtime."',`total_age` = '".$total_age."',`self_age` = '".$self_age."' where `id` = ".$goods_age['id']."";
				}
				$pdo->query($sql);
			}*/
		}
		catch(Exception $e){//捕获异常
			// echo $sql;die;
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 0 , 'error' =>'事物操作不成功，导致审核批发销售单失败');
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return array('success' => 1 , 'error' =>'审核成功');
	}

	/**
	* 取消单据
	*/
	public function closebill($bill_id , $bill_no){
		$user = $_SESSION['userName'];
		$time = date('Y-m-d H:i:s');
		$type = 'P';

		$warehouseGoodsModel = new WarehouseGoodsModel(21);
		$gdModel = new WarehouseBillGoodsModel(21);
		$goods = $gdModel->select2('`goods_id`', $where = "`bill_id` = {$bill_id}" , $is_all = 3);
		$goods_ids_str = '';
		$goods_ids_arr = array();
		foreach($goods as $key => $val){
			$goods_ids_str .= "'".$val['goods_id']."',";
			$goods_ids_arr[] = $val['goods_id'];
		}
		$goods_ids_str = rtrim($goods_ids_str , ',');
        $sql="select is_tsyd from warehouse_bill where id=".$bill_id;
        $is_tsyd=$this->db()->getOne($sql);
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//业务逻辑开始

			//将货品状态还原为库存
			$sql = "UPDATE `warehouse_goods`  SET  `is_on_sale`= '2'  WHERE `goods_id` IN (".$goods_ids_str.") and `is_on_sale`=10";
			//$pdo->query($sql);
			$changed=$pdo->exec($sql);  
			if($changed<>count(explode(",",$goods_ids_str))) {  
				$pdo->rollback();//事务回滚
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
				return array('success'=> 0 , 'error'=>'货品状态不是销售中'.$goods_ids_str);
			}
			
			#更改主表状态 warehouse_bill 的 bill_status 改为1
			$time = date('Y-m-d H:i:s');
			$user = $_SESSION['userName'];
			$sql = "UPDATE `warehouse_bill` SET `bill_status` =3, `check_user`='{$user}', `check_time`= '{$time}',confirm_delivery=0 WHERE id={$bill_id}";
			$pdo->query($sql);

			#天生一对订单，取消后改变订单明细配货状态；
			if($is_tsyd==1){
				$details = $gdModel->select2('`detail_id`', $where = "`bill_id` = {$bill_id}" , $is_all = 3);
				$detail_ids_str = '';	
				foreach($details as $key => $val){
					$detail_ids_str .= "'".$val['detail_id']."',";	
				}
				$detail_ids_str = rtrim($detail_ids_str , ',');
				$SalesModel=new SalesModel(28);
				$res=$SalesModel->updateAppOrderDetail(array('delivery_status'=>2,'send_good_status'=>1), "id in ({$detail_ids_str})");
				/*$pdo->rollback();//事务回滚
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
				return array('success'=> 0 , 'error'=>$res);*/
			    if(!$res){
			    	$pdo->rollback();//事务回滚
			   	   $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			   	   return array('success'=> 0 , 'error'=>'订单明细配货状态改变失败');
			   }
			}
			
			#写入warehouse_bill_status 表
			$ip = Util::getClicentIp();
			$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$bill_id}, '{$bill_no}', 3, '{$time}'
				 , '{$user}', '{$ip}')";
			$pdo->query($sql);

			//销售政策使销售政策商品状态变为上架
			$model = new ApiSalepolicyModel();
			//已绑定的商品不可推送上架
			foreach($goods_ids_arr AS $key => $val){
				$bing = $warehouseGoodsModel->select2($fields = ' `order_goods_id` ' , $where = " `goods_id` = '{$val}' " , $is_all = 1);
				if($bing != 0){
					unset($goods_ids_arr[$key]);
				}
			}
			if(!empty($goods_ids_arr)){
				$api_data = $model->EditIsSaleStatus($goods_ids_arr, 1 , 1);
				if( $api_data !== '操作成功')
				{
					//修改可销售商品为上架 失败制造错误回滚。
					$api_error = $api_data;
					$pdo->query('');
				}
			}

			//业务逻辑结束
		}
		catch(Exception $e){//捕获异常
			// echo $sql;die;
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			if(isset($api_data) && $api_data != ''){
				return array('success' => 0 , 'error' => $api_data);
			}
			return array('success' => 0 , 'error' =>'事物操作不成功，导致取消批发销售单失败');
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return array('success' => 1 , 'error' =>'取消成功');
	}


	//打印汇总 统计
	public function HuiZongTongJi($bill_id){
		//主成色统计
		$zhuchengsezhongxiaoji = 0;
		$sql = "SELECT sum(`a`.`jinzhong`) AS `jinzhong` , `a`.`caizhi` FROM `warehouse_bill_goods` AS `b` ,`warehouse_goods` AS `a` WHERE `b`.`goods_id` = `a`.`goods_id` AND `b`.`bill_id` = {$bill_id} GROUP BY `a`.`caizhi`";
		$row = $this->db()->getAll($sql);
		foreach ($row as $key => $val) {
			$zhuchengsezhongxiaoji += $val['jinzhong'];
			$zhuchengsetongji[] = $val;
		}
        $management_fee = 0;
        $sql = "SELECT sum(management_fee) AS `management_fee` FROM `warehouse_bill_goods` where `bill_id` = {$bill_id} ";
        $row = $this->db()->getRow($sql);
        if(!empty($row)){
            $management_fee = $row['management_fee'];
        }
		//主石统计
		$zhushilishuxiaoji = $zhushizhongxiaoji = 0;
		$sql = "SELECT sum(`b`.`zhushilishu`) AS `zhushilishu` , sum(`b`.`zuanshidaxiao`) AS `zuanshidaxiao` , `b`.`zhushi` FROM `warehouse_bill_goods` AS `a` ,`warehouse_goods` AS `b` WHERE `a`.`goods_id` = `b`.`goods_id` AND `a`.`bill_id` = {$bill_id} GROUP BY `b`.`zhushi`";
		$row = $this->db()->getAll($sql);
		foreach ($row as $key => $val) {
			$zhushilishuxiaoji += $val['zhushilishu'];
			$zhushizhongxiaoji += $val['zuanshidaxiao'];
			$zhushitongji[] = $val;
		}

		//副石统计
		$fushilishuxiaoji = $fushizhongxiaoji = 0;
		$sql = "SELECT sum(`b`.`fushilishu`) AS `fushilishu` , sum(`b`.`fushizhong`) AS `fushizhong` , `b`.`fushi` FROM `warehouse_bill_goods` AS `a` ,`warehouse_goods` AS `b` WHERE `a`.`goods_id` = `b`.`goods_id` AND `a`.`bill_id` = {$bill_id} GROUP BY `b`.`fushi`";
		$row = $this->db()->getAll($sql);
		foreach ($row as $key => $val) {
			$fushilishuxiaoji += $val['fushilishu'];
			$fushizhongxiaoji += $val['fushizhong'];
			$fushitongji[] = $val;
		}

		return $tongji = array(
			'zhuchengsezhongxiaoji' => $zhuchengsezhongxiaoji,
			'zhuchengsetongji' => $zhuchengsetongji,

			'zhushilishuxiaoji' => $zhushilishuxiaoji,
			'zhushizhongxiaoji' => $zhushizhongxiaoji,
			'zhushitongji' => $zhushitongji,

			'fushilishuxiaoji'=>$fushilishuxiaoji,
			'fushizhongxiaoji'=>$fushizhongxiaoji,
			'fushitongji'=>$fushitongji,
		    'management_fee'=>$management_fee
		);
	}
	
	
	
	
	
	/**
	 * 添加
	 */
	public function createBillInfop($bill_info , $goods_list,$order_sn){
		$time = date('Y-m-d H:i:s');
		$user = $_SESSION['userName'];
		$billModel = new WarehouseBillModel(22);
		$gwModel = new GoodsWarehouseModel(22);
		$type = 'P';
	
		//记录货品下架出入库记录
		$boxGoodsLogModel = new BoxGoodsLogModel(22);
		$boxGoodsLogModel->addLog(array_column($goods_list,'goods_id'));
	
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//业务逻辑开始
			//写入主表信息
	
			$sql = "INSERT INTO `warehouse_bill` (`bill_no`, `bill_type`, `goods_num` , `bill_note` , `goods_total`,`shijia` , `pifajia` , `create_user` , `create_time` ,`from_company_id` , `from_company_name`, `to_customer_id`,`is_tsyd`) VALUES ('' , '{$type}' ,  {$bill_info['goods_num']} , '{$bill_info['bill_note']}' , {$bill_info['goods_total']} , {$bill_info['shijia']} , {$bill_info['pifajia']} , '{$user}' , '{$time}' , {$bill_info['from_company_id']} , '{$bill_info['from_company_name']}', {$bill_info['wholesale_id']},1)";
	
	
			$pdo->query($sql);
			$id = $pdo->lastInsertId();
			$bill_no = $billModel->create_bill_no($type,$id);
			$sql = "UPDATE `warehouse_bill` SET `bill_no` = '{$bill_no}' WHERE `id` = {$id}";
			$pdo->query($sql);
			//写入附表信息
			//$sql = "INSERT INTO `warehouse_bill_info_p` (`bill_id`) VALUES ({$id})";
			//$pdo->query($sql);
			//写入明细
			$goods_id_str ="";
			foreach($goods_list AS $key => $val){
			    $goods_id_str .= $val['goods_id'].",";
				$pifajia=$val['mingyichengben']*(1+0.08);
				$sql = "INSERT INTO `warehouse_bill_goods` (
				`bill_id` , `bill_no` , `bill_type` , `goods_id` , `goods_sn` ,
				`goods_name` , `num` , `jinzhong` , `jingdu` , `yanse` , `zhengshuhao` ,
				`zuanshidaxiao` ,`sale_price`, `shijia` , `pifajia` ,  `addtime`,`order_sn`,`detail_id`
				) VALUES (
				{$id} , '{$bill_no}' , '{$type}' , '{$val['goods_id']}' , '{$val['goods_sn']}',
				'{$val['goods_name']}' , 1 , '{$val['jinzhong']}' , '{$val['jingdu']}' , '{$val['yanse']}' , '{$val['zhengshuhao']}' ,
				'{$val['zuanshidaxiao']}' ,'{$val['chengbenjia']}' , '$pifajia' , '{$val['mingyichengben']}' , '{$time}','{$val['order_sn']}',{$val['detail_id']}
				)";
				$pdo->query($sql);
	
				//改变货品仓储状态
				$sql = "UPDATE `warehouse_goods` SET `is_on_sale`= 10, `box_sn` = '0-00-0-0' WHERE `goods_id` = '{$val['goods_id']}' and `is_on_sale`= 2";
				//$pdo->query($sql);
				$changed=$pdo->exec($sql);
				if($changed<>1) {
				$pdo->rollback();//事务回滚
					$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
					return array('success'=> 0 , 'error'=>'货品状态不是库存中'.$val['goods_id']);
				}
	
							//货品下架
									$default_box = $gwModel->getDefaultBoxIdBygoods($val['goods_id']);
									if(!$default_box){
									// return array('success' => 0 , 'error'=> "货号：{$val[0]} 不存在相应柜位信息,下架失败！导致制单失败");
										//如果不存在默认柜位，自动创建一条货品的默认柜位信息
										$res = $billModel->CheckAndCreateBox($val['goods_id']);
										if($res['success'] != 1){
										return array('success' => 0 , 'error' => $res['error']);
												$pdo->query('');
										}
										$default_box = $res['box_id'];
									}
									$sql = "UPDATE `goods_warehouse` SET `box_id` = {$default_box} , `create_user` = '' , `create_time` = '0000-00-00 00:00:00' WHERE `good_id` = '{$val['goods_id']}'";
									$pdo->query($sql);
									$new_goods_id[] = $val['goods_id'];
	
		}
		//重新核算价格
		$sql = "select sum(sale_price) as goods_total,sum(shijia) as shijia,sum(pifajia) as pifajia from warehouse_bill_goods where bill_id ={$id}";
		$price_row = $this->db()->getRow($sql);
		$sql = "update warehouse_bill set goods_total='{$price_row['goods_total']}',shijia='{$price_row['shijia']}',pifajia='{$price_row['pifajia']}' where id='{$id}'";
		$pdo->query($sql);
		//写入warehouse_bill_status信息
		$update_time = date('Y-m-d H:i:s');
		$ip = Util::getClicentIp();
		$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$id}, '{$bill_no}', 1 , '{$time}', '{$user}', '{$ip}') ";
		$pdo->query($sql);
	
		//销售政策下架
		$model = new ApiSalepolicyModel();
		if(!$model->EditIsSaleStatus($new_goods_id, 0 , 2))
		{
		//修改可销售商品为下架 失败制造错误回滚。
										$pdo->query('');
		}
		//回写订单操作日志
		ApiSalesModel::addOrderAction($order_sn , $user , '配货成功,货号：'.trim($goods_id_str,","));
		//业务逻辑结束
		}
		catch(Exception $e){//捕获异常
		//echo $sql;die;
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 0 , 'error' =>'事物操作不成功，导致添加批发销售单失败'.$sql);
		}
    	$pdo->commit();//如果没有异常，就提交事务
    	$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
    	return array('success' => 1 , 'x_id' => $id, 'label'=>$bill_no);
	}
	
   function CheckBillGoods($bill_id){
   	 $sql="select order_sn from warehouse_bill_goods where bill_id=$bill_id and order_sn !='' group by order_sn";
   	 $orderArr=$this->db()->getAll($sql);	
   	 if(empty($orderArr)){
   	 	return array('success' => 1 , 'error' =>'');
   	 }
   	 
   	 $SalesModel=new SalesModel(27);
   	 $orderStr='';
   	 foreach ($orderArr as $k=>$v){
   	 	
   	 	$orderInfoArr=$SalesModel->getOrderAccountInfoByOrderSn($v['order_sn']);
   	    $money_paid=$orderInfoArr['money_paid'];//已付金额
   	    $real_return_price=$orderInfoArr['real_return_price'];//实退金额
   	    $order_amount=$orderInfoArr['order_amount'];//总金额
   	    
   	    //本次批发销售单同一个订单号的商品批发价之和
   	    $sql="select shijia,detail_id from warehouse_bill_goods where bill_id=$bill_id and order_sn ='{$v['order_sn']}'";  	    
   	    $goodsBillArr=$this->db()->getAll($sql);
   	    $pfj=0;//批发价
   	    $detialIdArr=array();
   	    foreach ($goodsBillArr as $value){
   	    	$pfj += $value['shijia'];
   	       if($value['detail_id']==''){
   	       	  return array('success' => 0 , 'error' =>"参数detail_id错误");
   	    	}
   	    	$detialIdArr[]=$value['detail_id'];
   	    }
   	    
   	    //同一个订单号已发货确认的商品的批发价之和
   	    $sql="select bg.shijia,bg.detail_id from warehouse_bill_goods as bg,warehouse_bill as b where bg.bill_id=b.id  and b.confirm_delivery=1 and bg.order_sn ='{$v['order_sn']}'";
   	    $goodsBillArr=$this->db()->getAll($sql);
   	    foreach ($goodsBillArr as $value1){
   	    	$pfj += $value1['shijia'];
   	    	if($value1['detail_id']==''){
   	    		return array('success' => 0 , 'error' =>"参数detail_id错误");
   	    	}
   	    	$detialIdArr[]=$value1['detail_id'];
   	    }
   	    
   	    $details_str=implode(',', $detialIdArr);
   	   	    
   	    $goods_price_total=$SalesModel->getOrderDetailPrice($details_str);  
   	    //return array('success' => 0 , 'error' =>$goods_price_total);
   	    $other_goods_price_total=$order_amount-$goods_price_total;//同一个订单中剩余商品的订单实际支付金
   	    $other_goods_price_total=$other_goods_price_total*0.3;  	  
   	    //记录不满足条件的订单号
   	    if($money_paid-$real_return_price-$pfj <$other_goods_price_total){
   	    	$orderStr.=$v['order_sn'].' ';
   	    }

   	 }
   	
   	 if($orderStr != ''){
   	 	return array('success' => 0 , 'error' =>"以下订单付款金额不符合条件，请点款后再来操作：".$orderStr);
   	 }else{
   	 	return array('success' => 1 , 'error' =>'');
   	 }
   	 
   	
   }
   
   
   /**
    * 确认发货
    */
   public function confirmBillP($bill_id){
        $sql="update warehouse_bill set confirm_delivery = 1 where id={$bill_id}";
        $re=$this->db()->query($sql);
        if($re){
        	$gdModel = new WarehouseBillGoodsModel(21);
        	$details = $gdModel->select2('`detail_id`', $where = "`bill_id` = {$bill_id}" , $is_all = 3);
			$detail_ids_str = '';	
			foreach($details as $key => $val){
				$detail_ids_str .= "'".$val['detail_id']."',";	
			}
			$detail_ids_str = rtrim($detail_ids_str , ',');
			$SalesModel=new SalesModel(28);
			$res=$SalesModel->updateAppOrderDetail(array('send_good_status'=>4), "id in ({$detail_ids_str})");
  
        	if(!$res){
        		return array('success' => 0 , 'error' =>'改变订单明细发货状态失败');
        	}
        	return array('success' => 1 , 'error' =>'确认发货成功');
        	
        }else{
        	return array('success' => 0 , 'error' =>'确认发货失败');
        }
   		
   }
	/** 根据货号，获取商品信息 **/
	public function getGoodsP($goods_id){
		$sql = "SELECT * FROM `warehouse_goods` WHERE `goods_id`='{$goods_id}' LIMIT 1";
		return $this->db()->getRow($sql);
	}	   
  
}?>
