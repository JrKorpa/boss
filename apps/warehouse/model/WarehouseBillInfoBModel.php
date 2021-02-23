<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoBModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-18 17:09:51
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoBModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = '';
		$this->pk='id';
		$this->_prefix='';
		        $this->_dataObject = array("id"=>" ",
			"kela_order_sn"=>"参考编号，BDD订单号",
			"pid"=>"加工商id",
			"in_warehouse_type"=>"入库方式 0、默认无。1.购买。2、委托加工。3、代销。4、借入");
		parent::__construct($id,$strConn);
	}


	/**
	* 事物提交插入
	*/
	public function insert_shiwu($bill, $goods)
	{
		$gwModel = new GoodsWarehouseModel(21);
		$billModel = new WarehouseBillModel(22);
		//记录货品下架出入库记录
		$boxGoodsLogModel = new BoxGoodsLogModel(22);
		$boxGoodsLogModel->addLog(array_column($goods,0));
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

			#写入 warehouse_bill表
			$sql = "INSERT INTO `warehouse_bill`(
				`bill_no`, `bill_type`, `goods_num`,
				`bill_note`,
				`create_user`, `create_time`, `to_warehouse_id`,
				`to_warehouse_name`, `to_company_id`, `to_company_name`,
				`from_company_id`, `from_company_name`, `order_sn`, `goods_total`,`shijia`
				,`pro_id`,`pro_name`,`put_in_type`) VALUES (
				'{$bill['bill_no']}', '{$bill['bill_type']}', '{$bill['goods_num']}',
				'{$bill['bill_note']}',
				'{$bill['create_user']}', '{$bill['create_time']}', '{$bill['to_warehouse_id']}',
				'{$bill['to_warehouse_name']}',0,'',
				{$bill['from_company_id']} , '{$bill['from_company_name']}' , '{$bill['order_sn']}' , '{$bill['goods_total']}','{$bill['shijia']}','{$bill['pro_id']}','{$bill['pro_name']}', '{$bill['put_in_type']}'
				)";
			$pdo->query($sql);
			$id = $pdo->lastInsertId();
			$bill_no = $billModel->create_bill_no($bill['bill_type'],$id);
			
			//新增的时候自动保存一条记录到结算商--20151022
			 
			$cat_type = 1;//货品类型默认为1
			if(!empty($goods[0][0]))
			{
				$goods_id = $goods[0][0];//取列表中第一个货号作为默认值
				
				$modelg = new WarehouseGoodsModel(21);
				$datact = $modelg->GetGoodsbyGoodid($goods_id);
				$cat_type = $datact["cat_type"];
				if($cat_type == "裸石")
					$cat_type = "2";
				elseif($cat_type == "差额")
					$cat_type = "6";
				else
					$cat_type = "3";//成品
			}
			
			// 5金额 自动取B单货号对应采购成本之和，
			$modelg = new WarehouseGoodsModel(21);
			$chengbenjia = 0;
			if(!empty($goods))
			{
				foreach($goods as $key => $vv)
				{
					$chengbenjia += $goods[$key][15];
				}
			}
			
			$sql = "insert into  `warehouse_bill_pay`(`bill_id`,`pro_id`,`pro_name`,`pay_content`,`pay_method`,`tax`,`amount`) values ('{$id}','{$bill['pro_id']}','{$bill['pro_name']}','{$cat_type}','1','2','{$chengbenjia}') ";
			$pdo->query($sql);
			
			
			
			$sql = "UPDATE `warehouse_bill` SET `bill_no`='{$bill_no}' WHERE `id`={$id}";
			$pdo->query($sql);
			#插入新的明细
			//是否结价
			$new_goods_id ="";
			//var_dump($goods);exit;
			foreach($goods as $k =>$v){
				if($v[13] == '无' ||!$v[13]){
					$v[13] = 0;
				}else if($v[13] == '已结价'){
					$v[13] = 1;
				}else if($v[13] == '未结价'){
					$v[13] = 2;
				}
				$sql = "INSERT INTO `warehouse_bill_goods` (
					`bill_id`, `bill_no`, `bill_type`, `goods_id`,
					`goods_name`, `goods_sn`, `in_warehouse_type`, `yanse`,
					`jingdu`, `zhengshuhao`, `num`, `account` ,`sale_price` ,`shijia`, `addtime`
					) VALUES (
						$id, '{$bill_no}', 'B' , '$v[0]',
						'{$v[1]}', '{$v[2]}', '$v[3]', '{$v[9]}' ,
						'{$v[10]}','{$v[11]}','{$v[12]}', '{$v[13]}',
						'{$v[14]}','{$v[15]}','{$v['addtime']}'
					)";
				$pdo->query($sql);
				if($k)
				{
					$new_goods_id .=",'".$v[0]."'";
				}
				else
				{
					$new_goods_id .="'".$v[0]."'";
				}
				$new_goods_id = ltrim($new_goods_id,',');
				//货品下架
				$default_box = $gwModel->getDefaultBoxIdBygoods($v[0]);
				if(!$default_box){
					// return array('success' => 0 , 'error'=> "货号：{$v[0]} 不存在相应柜位信息,下架失败！导致制单失败");
					//如果不存在默认柜位，自动创建一条货品的默认柜位信息
					$res = $billModel->CheckAndCreateBox($v[0]);
					if($res['success'] != 1){
						return array('success' => 0 , 'error' => $res['error']);
						$pdo->query('');
					}
					$default_box = $res['box_id'];
				}
				$sql = "UPDATE `goods_warehouse` SET `box_id` = {$default_box} , `create_user` = '' , `create_time` = '0000-00-00 00:00:00' WHERE `good_id` = '{$v[0]}'";
				$pdo->query($sql);
			}
			//将货品状态改为返厂中
			$sql = "UPDATE `warehouse_goods` SET `is_on_sale`=8, `box_sn` = '0-00-0-0' WHERE `goods_id` IN (".$new_goods_id.") and `is_on_sale` = 2";
			$num = $pdo->exec($sql);
			if($num != count(explode(",",$new_goods_id))){
			    $pdo->rollback();//事务回滚
			    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			    return array('success'=> 0 , 'error'=>'操作失败，可能货品不是库存状态');
			}
			/*
			#跟新附表信息（warehouse_bill_info_b）
			$sql = "INSERT INTO `warehouse_bill_info_b` (`bill_id`, `kela_order_sn`, `pid`,`in_warehouse_type`,`prc_name`) VALUES ($id, '{$info_b['kela_order_sn']}', {$info_b['pid']}, '{$info_b['in_warehouse_type']}','{$info_b['prc_name']}')";
			$pdo->query($sql);
			*/

			//写入warehouse_bill_status信息
			$update_time = date('Y-m-d H:i:s');
			$ip = Util::getClicentIp();
			$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$id}, '{$bill['bill_no']}', 1, '{$update_time}', '{$bill['create_user']}', '{$ip}') ";
			$pdo->query($sql);

			$model = new ApiSalepolicyModel();
			$new_goods_id = str_replace("'","",$new_goods_id);
			$new_goods_id = explode(",",$new_goods_id);
			if(!$model->EditIsSaleStatus($new_goods_id,0,2))
			{
				//修改可销售商品为下架 失败制造错误回滚。
				$pdo->query('');
			}
			$pdo->commit();//如果没有异常，就提交事务
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success'=> 1 , 'x_id' => $id , 'label'=>$bill_no);
		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success'=> 0 , 'error'=>'事物执行不成功，导致制单失败');
		}		
	}

	/** 获取退货返厂单的明细 B **/
	public function getGoodsList($bill_id){
		$sql = "SELECT `id`,`goods_id`,`goods_sn`,`goods_name`,`num`,`yanse`,`sale_price`,`shijia`,`in_warehouse_type`,`account` FROM `warehouse_bill_goods` WHERE `bill_id` = {$bill_id} AND `bill_type`='B'";
		return $this->db()->getAll($sql);
	}

/*  附表删掉
	
	public function getRowForBill_id($bill_id){
		$sql = "SELECT `id`, `pid`,`prc_name`,`kela_order_sn`,`in_warehouse_type` FROM  `warehouse_bill_info_b` WHERE `bill_id` = {$bill_id} LIMIT 1";
		return $this->db()->getRow($sql);
	}
*/
	/**
	* 事物提交跟新
	* $bill 提交跟新warehouse_bill 的信息
	* $info_b 提交跟新warehouse_bill_info_b 的信息
	* $del_goods_ids 提交跟新warehouse_bill_goods 的信息
	* $id $bill 的id / warehouse_bill_info_b 的bill_id / warehouse_bill_goods的 bill_id
	* @param $del_goods_ids	//要删除的明细ID
	* @param $old_goods_ids	//要删除的明细货号
	*/
	public function update_shiwu($bill, $goods, $del_goods_ids, $id,$old_goods_ids)
	{
		$gwModel = new GoodsWarehouseModel(21);
		$billModel = new WarehouseBillModel(22);
		//记录货品下架出入库记录
		$boxGoodsLogModel = new BoxGoodsLogModel(22);
		$boxGoodsLogModel->addLog(array_column($goods,0));
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

			//还原删除的货品状态 库存状态2
			$sql ="UPDATE `warehouse_goods` SET `is_on_sale`=2 where goods_id IN (".$old_goods_ids.") and `is_on_sale`=8 ";
			$pdo->query($sql);
			#跟新单据主信息（warehouse_bill）
			$sql = "UPDATE `warehouse_bill` SET `goods_num` = {$bill['goods_num']} , `bill_note` = '{$bill['bill_note']}',`goods_total` = {$bill['goods_total']},`shijia` = '{$bill['shijia']}', `pro_id` = '{$bill['pro_id']}', `pro_name` = '{$bill['pro_name']}',  `order_sn` = '{$bill['order_sn']}'  WHERE `id` = {$id}";
			$pdo->query($sql);

			#清空原有的明细 warehouse_bill_goods表
			$sql = "DELETE FROM `warehouse_bill_goods` WHERE `id` IN ({$del_goods_ids})";
			$pdo->query($sql);

			#插入新的明细
			$new_goods_ids = "";
			foreach($goods as $k =>$v){
				//是否结价0、默认无。1、未结价。2、已结价
				if($v['13'] == '无' ||!$v['13']){
					$v['13'] = 0;
				}
				if($v['13'] == '未结价'){
					$v['13'] = 1;
				}
				if($v['13'] == '已结价'){
					$v['13'] = 2;
				}
				$sql = "INSERT INTO `warehouse_bill_goods` (
					`bill_id`, `bill_no`, `bill_type`,`goods_id`,
					`goods_name`, `goods_sn`, `in_warehouse_type`, `yanse`,
					`jingdu`, `zhengshuhao`, `num`, `account`,
					 `sale_price`,`shijia` , `addtime`
					) VALUES (
						$id, '{$v['bill_no']}', 'B' , '$v[0]',
						'{$v[1]}', '{$v[2]}', '$v[3]', '{$v[9]}' ,
						'{$v[10]}','{$v[11]}','{$v[12]}', '{$v[13]}',
						'{$v[14]}' ,'{$v['15']}', '{$v['addtime']}'
					)";
				$pdo->query($sql);
				if ($k)
				{
					$new_goods_ids .= ",'".$v[0]."'";
				}
				else
				{
					$new_goods_ids .= "'".$v[0]."'";
				}

				//货品下架
				$default_box = $gwModel->getDefaultBoxIdBygoods($v[0]);
				if(!$default_box){
					// return array('success' => 0 , 'error'=> "货号：{$v[0]} 不存在相应柜位信息,下架失败！导致制单失败");
					//如果不存在默认柜位，自动创建一条货品的默认柜位信息
					$res = $billModel->CheckAndCreateBox($v[0]);
					if($res['success'] != 1){
						return array('success' => 0 , 'error' => $res['error']);
						$pdo->query('');
					}
					$default_box = $res['box_id'];
				}
				$sql = "UPDATE `goods_warehouse` SET `box_id` = {$default_box} , `create_user` = '' , `create_time` = '0000-00-00 00:00:00' WHERE `good_id` = '{$v[0]}'";
				$pdo->query($sql);
			}
			//新添加的货品状态改变 返厂中
			$sql ="UPDATE `warehouse_goods` SET `is_on_sale`=8, `box_sn` = '0-00-0-0' where goods_id IN (".$new_goods_ids.") and `is_on_sale`=2";
		    $num = $pdo->exec($sql);
			if($num != count(explode(",",$new_goods_ids))){
			    $pdo->rollback();//事务回滚
			    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			    return array('success'=> 0 , 'error'=>'操作失败，可能货品不是库存状态');
			}
			/*
			#跟新附表信息（warehouse_bill_info_b）
			$sql = "UPDATE `warehouse_bill_info_b` SET `pid`={$info_b['pid']} ,`kela_order_sn`='{$info_b['kela_order_sn']}', `in_warehouse_type`={$info_b['in_warehouse_type']},`prc_name`='{$info_b['prc_name']}' WHERE `bill_id`={$id}";
			$pdo->query($sql);
			*/
			/** 旧的明细销售上架 ， 新的明细销售下架 **/
			$model = new ApiSalepolicyModel();
			$new_goods_ids = str_replace("'","",$new_goods_ids);
			$new_goods_ids = explode(",",$new_goods_ids);

			$old_goods_ids = str_replace("'","",$old_goods_ids);
			$old_goods_ids = explode(",",$old_goods_ids);

			if(!$model->EditIsSaleStatus($new_goods_ids, 0, 5, $old_goods_ids))
			{
				//修改可销售商品为下架,老明细 自动上架 失败制造错误回滚。
				$pdo->query('');
			}
			$pdo->commit();//如果没有异常，就提交事务
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 1 , 'error' => '操作成功');
		}
		catch(Exception $e){//捕获异常
			//echo ($sql);exit;
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 0 , 'error' => '事物执行不成功，导致制单失败');
		}		
	}


	/** 审核单据 **/
	public function checkBillInfoB($bill_id, $bill_no,$goods_ids){
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			$time = date('Y-m-d H:i:s');
			//将货品状态改为已返厂
			$sql ="UPDATE `warehouse_goods` SET `is_on_sale` = 9, `chuku_time`='{$time}' where `goods_id` IN (".$goods_ids.") and `is_on_sale` = 8";
			//var_dump($sql);exit;
			$num = $pdo->exec($sql);
			if($num != count(explode(",",$goods_ids))){
			    $pdo->rollback();//事务回滚
			    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			    return false;
			}
            #记录出库时间，写入库龄表 warehouse_goods_age；
            //1、货品已返厂，记录出库时间endtime（货品库龄已结束，之后不再统计）；
            $sql = "UPDATE `warehouse_goods_age` SET `endtime` = '{$time}' WHERE `goods_id` IN (".$goods_ids.")";
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
			#跟新单据主信息（warehouse_bill）
			$user = $_SESSION['userName'];			
			$sql = "UPDATE `warehouse_bill` SET `bill_status`=2, `check_user`='{$user}', `check_time`='{$time}' WHERE `id`={$bill_id}";
			$pdo->query($sql);

			#写入状态表信息（warehouse_bill_status）
			$ip = $ip = Util::getClicentIp();
			$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$bill_id}, '{$bill_no}', 2, '{$time}', '{$user}', '{$ip}')";
			$pdo->query($sql);

			#推送财务结算数据
			$sql = "SELECT `b`.`bill_no`,`b`.`bill_type`,`b`.`put_in_type`,`b`.`create_time`,`b`.`pro_id`,`b`.`pro_name`,`b`.`order_sn`,b.`shijia`  FROM `warehouse_bill` as b WHERE `b`.`id` = ".$bill_id ;
			$info = $this->db()->getRow($sql);

			//入库方式：购买和加工的 向财务应付推送数据
			if($info['put_in_type'] =='1' || $info['put_in_type'] == '2'){
				$arr = array(
					'item_id'	=> $info['bill_no'],
					'order_id'	=> 0,
					'zhengshuhao' => '',
					'goods_status' => 8,
					'item_type'	=> '2',
					'company'=> 58,
					'prc_id'	=> $info['pro_id'],
					'prc_name'	=> $info['pro_name'],
					'prc_num'	=> $info['order_sn'],
					'type'=> 2,
					'pay_content' => '',
					'storage_mode'=> $info['put_in_type'],
					'make_time'	=> $info['create_time'],
					'check_time'=> $time ,
					'total'		=> $info['shijia']
				);
				$findata[] = $arr;

				$PayModel = new ApiFinanceModel();
				$res = $PayModel->AddAppPayDetail($findata);
				if($res['error']){
					//制造错误，抛出异常，形成回滚。
					$pdo->query('');
				}
			}
			$pdo->commit();//如果没有异常，就提交事务
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return true;		
		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}		
	}

	/** 取消单据 **/
	public function closeBillInfoB($bill_id, $bill_no,$goods_ids){
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//将货品状态还原库存
			$sql ="update `warehouse_goods` set `is_on_sale` = 2 where goods_id in (".$goods_ids.") and `is_on_sale` = 8";
			//var_dump($sql);exit;
			$num = $pdo->exec($sql);
			if($num != count(explode(',',$goods_ids))){
			    $pdo->rollback();//事务回滚
			    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			    return false;
			}
			#跟新单据主信息（warehouse_bill）
			$time = date('Y-m-d H:i:s');
			$user = $_SESSION['userName'];
			$sql = "UPDATE `warehouse_bill` SET `bill_status`=3, `check_user`='{$user}', `check_time`='{$time}'  WHERE id={$bill_id}";
			$pdo->query($sql);
			#写入状态表信息（warehouse_bill_status）
			$ip = $ip = Util::getClicentIp();
			$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$bill_id}, '{$bill_no}', 3, '{$time}', '{$user}', '{$ip}')";
			$pdo->query($sql);

			$model = new ApiSalepolicyModel();
			$goods_ids = str_replace("'","",$goods_ids);
			$goods_ids = explode(",",$goods_ids);
			if(!$model->EditIsSaleStatus($goods_ids,1 ,1))
			{
				//修改可销售商品为上架 失败制造错误回滚。
				$pdo->query('');
			}
			$pdo->commit();//如果没有异常，就提交事务
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return true;
		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		
	}

	/** 根据货号，查询单据明细 (新增) **/
	public function getGoodsInfoByGoodsIDAdd($goods_id){

		$sql = "SELECT `goods_id`, `goods_sn`, `goods_name`,`num`,`company_id`, `yanse`, `chengbenjia`, `put_in_type`, `is_on_sale` FROM  `warehouse_goods` WHERE `goods_id` = '{$goods_id}' LIMIT 1";
		// echo $sql;
		return $this->db()->getRow($sql);

	}

	/** 根据货号，查询单据明细 (编辑) **/
	public function getGoodsInfoByGoodsIDEdit($bill_id){
		$sql = "SELECT `a`.`goods_id`, `a`.`goods_sn`, `a`.`goods_name` , `a`.`num`, `a`.`yanse`, `a`.`chengbenjia`, `a`.`mingyijia`, `a`.`in_warehouse_type`, `a`.`xiaoshoujia`, `a`.`account` , `b`.`zhushiyanse` , `b`.`zhushilishu` , `b`.`zuanshidaxiao`, `b`.`fushilishu`, `b`.`fushizhong`, `b`.`jingdu`, `b`.`zhengshuhao`, `b`.`jiejia`,a.`account`  FROM  `warehouse_bill_goods` AS `a` INNER JOIN `warehouse_goods` AS `b` ON `a`.`goods_id` = `b`.`goods_id` WHERE `a`.`bill_id` = '{$bill_id}' ORDER BY `a`.`id` DESC";
		return $this->db()->getAll($sql);
	}

	/** 根据货号，获取货品的仓库id **/
	public function getGoodsInWarehouseByGoodsId($goods_id){
		$sql = "SELECT `warehouse_id` FROM `warehouse_goods` WHERE `goods_id` = '{$goods_id}' LIMIT 1";
		return $this->db()->getOne($sql);
	}



	/**打印详情  根据 $bill_id，获取加工商信息 **/
	public function getBillPay($bill_id){
		$sql = "SELECT `pro_id`,`pro_name`,`pay_content`,`amount`  FROM `warehouse_bill_pay`  WHERE `bill_id` = '{$bill_id}'";
		//echo $sql;exit;
		return $this->db()->getAll($sql);
	}

    //审核单据号补充尾差 尾差处理公共方法 收货单、其他收货单 id：单据id；chengbenjia_goods：单据成本价
    public function cha_deal($id, $chengbenjia_goods)
    {
        #收货单结算商尾差计算（金额限制需要加上====￥￥￥￥）检查是否有结算商--删除已有的结算商列表中的尾差记录
        //1、计算商品总成本价
        //2、计算结算商总成本
        //3、插入结算商尾差
        //4、修改单据价格总计、支付总计为计算的商品总成本
        $model_pay = new WarehouseBillPayModel(22);
        $model_pay->delete_cha($id);

        $zhifujia_prc = $model_pay->getAmount($id);
        $cha = $chengbenjia_goods - $zhifujia_prc;
        echo $chengbenjia_goods."---".$zhifujia_prc; exit;
        $new_array = array(
            'bill_id' => $id,
            'pro_id' => 366, //暂时是0
            'pro_name' => '入库成本尾差',
            'pay_content' => 6, //差 数据字典6
            'pay_method' => 1, //'记账'
            'tax' => 2, //数据字典 含 2
            'amount' => $cha,
        );

        if ($cha != 0) {
            #需要限制 自动尾差补充金额大小
            $re_p = $model_pay->saveData($new_array, array());
            if (!$re_p) {
                $result['error'] = "结算商尾差计算失败";
                Util::jsonExit($result);
            }
        }
    }
}/**END CLASS **/

?>