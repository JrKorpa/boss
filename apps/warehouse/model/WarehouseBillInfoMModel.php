<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoMModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 17:24:09
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoMModel extends Model
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
	 *	pageList，分页列表
	 *
	 *	@url WarehouseBillInfoMController/search
	 */
	function pageList($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
		if(isset($where['bill_id']) && $where['bill_id'] != ''){
			$str .= "`bill_id` = {$where['bill_id']} AND ";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}


	/**
	 * 编辑调拨单时，获取相关的明细列表数据
	 * $bill_id Int 单据ID
	 * return array 明细
	 */
	public function getBillGoogsList($bill_id,$qry_type=''){
		$sql = "SELECT `a`.`prc_name`,`a`.`put_in_type`,`b`.`goods_id`, `b`.`goods_sn`, `c`.`bill_note`,  `b`.`sale_price`, `b`.`jinzhong`, `b`.`caizhi`, `b`.`yanse`, `b`.`jingdu`, `b`.`jinhao`, `b`.`yuanshichengben`, `b`.`zhengshuhao`,`b`.`goods_name`,`a`.`zhushilishu`,`a`.`fushilishu`,`a`.`fushizhong` FROM  `warehouse_bill_goods` AS `b` LEFT JOIN `warehouse_goods` AS `a` ON `a`.`goods_id` = `b`.`goods_id` AND `b`.`bill_type`='M' LEFT JOIN `warehouse_bill` AS `c` ON `b`.`bill_id` = `c`.`id` WHERE `b`.`bill_id`={$bill_id} ";
		if ($qry_type == "row") {
			return $this->db()->getRow($sql);
		}else {
			return $this->db()->getAll($sql);
		}
	}

	/** 添加单据 **/
	public function add_shiwu($bill_info,$goods,$goods_id_str){
		$gwModel = new GoodsWarehouseModel(21);
		$billModel = new WarehouseBillModel(22);
		$BoxGoodsLogModel = new BoxGoodsLogModel(22);
		//记录货品出入库记录
		$BoxGoodsLogModel->addLog(array_column($goods,0));
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//业务逻辑开始
			$sql = "INSERT INTO `warehouse_bill` (
			`bill_no`, `to_company_id`, `to_warehouse_id`,
			`from_company_id`,`goods_num`,`goods_total`,
			`goods_total_jiajia`,
			`bill_note`,`to_warehouse_name`,
			`to_company_name`,`from_company_name`,
			`create_user`,`create_time`,`bill_type`,`order_sn`,`label_price_total`
			) VALUES (
			'', {$bill_info['to_company_id']}, {$bill_info['to_warehouse_id']},
			{$bill_info['from_company_id']}, {$bill_info['goods_num']}, {$bill_info['goods_total']},
			{$bill_info['goods_total_jiajia']},
			'{$bill_info['bill_note']}','{$bill_info['to_warehouse_name']}',
			'{$bill_info['to_company_name']}',
			'{$bill_info['from_company_name']}','{$bill_info['create_user']}',
			'{$bill_info['create_time']}', '{$bill_info['bill_type']}','{$bill_info['order_sn']}',{$bill_info['label_price_total']}
			)";
			// file_put_contents('e:/8.sql',$sql);
			$pdo->query($sql);
			$id = $pdo->lastInsertId();

			//创建单据编号
			$bill_no = $billModel->create_bill_no('M',$id);
			$sql = "UPDATE `warehouse_bill` SET `bill_no`='{$bill_no}' WHERE `id`={$id}";
// 			file_put_contents('e:/8.sql',$sql);
			$pdo->query($sql);

			//插入明细（货号是唯一的，所以这里的货品数量是1）
			foreach ($goods as $gk => $gv) {
				$gv[10] = $gv[10]?$gv[10]:0;
				$sql = "INSERT INTO `warehouse_bill_goods` (
		  			`bill_id`, `bill_no`, `bill_type`, `goods_id`,
		  			`goods_sn`, `goods_name`, `num`, `jinzhong`,
		  			`caizhi`, `yanse`,`jingdu`,`jinhao`,
		  			`sale_price`, `zhengshuhao`, `addtime`
		  			, `jiajialv`, `label_price` 
		  			) VALUES (
		  			{$id}, '{$bill_no}', 'M', '{$gv[0]}',
		  			'{$gv[1]}', '{$gv[12]}', 1, '{$gv[3]}',
		  			'{$gv[4]}', '{$gv[8]}', '{$gv[9]}', {$gv[10]},
		  			'{$gv[2]}', '{$gv[11]}', '{$gv['addtime']}'
					, {$gv['jiajialv']}, {$gv[17]}
		  			) ";
		  			
// 		  		file_put_contents('e:/9.sql',$sql);
				$pdo->query($sql);
				//调拨单保存，实时的柜位下架，取消不用操作柜位，人工去做上架。审核也不做任何柜位操作。
				//获取调拨货品与所在仓库的默认柜位,货品下架到当前公司仓库的默认柜位
				$default_box = $gwModel->getDefaultBoxIdBygoods($gv[0]);

				//正确的流程
				if(!$default_box){
					// return array('success' => 0 , 'error'=> "货号：{$gv[0]} 不存在相应柜位信息,下架失败！导致制单失败");
					//如果不存在默认柜位，自动创建一条货品的默认柜位信息
					$res = $billModel->CheckAndCreateBox($gv[0]);
					if($res['success'] != 1){
						return array('success' => 0 , 'error' => $res['error']);
						$pdo->query('');
					}
					$default_box = $res['box_id'];
				}
				$sql = "UPDATE `goods_warehouse` SET `box_id` = {$default_box} , `create_user` = '' , `create_time` = '0000-00-00 00:00:00' WHERE `good_id` = '{$gv[0]}'";
				$pdo->query($sql);

			}

			//修改货品状态为调拨中
			$sql = "UPDATE `warehouse_goods` SET `is_on_sale`= 5, `box_sn` = '0-00-0-0' WHERE `goods_id` IN (".$goods_id_str.") and `is_on_sale`=2";
			//$pdo->query($sql);
			$changed=$pdo->exec($sql);  
			if($changed<>count(explode(",",$goods_id_str))) {  
				$pdo->rollback();//事务回滚
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
				return array('success'=> 0 , 'error'=>'货品状态不是库存中'.$goods_id_str);
			}

			//插入订单号+快递单号  附表--删掉
			$sql = "INSERT INTO `warehouse_bill_info_m` (`bill_id`,`ship_number`) VALUES ({$id}, '')";
			$pdo->query($sql);

			//写入warehouse_bill_status信息
			$update_time = date('Y-m-d H:i:s');
			$ip = Util::getClicentIp();
			$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$id}, '{$bill_no}', 1, '{$update_time}', '{$bill_info['create_user']}', '{$ip}') ";
			$pdo->query($sql);

			//销售政策使销售政策商品状态变为下架
			$model = new ApiSalepolicyModel();
			$_goods_id = str_replace("'","",$goods_id_str);
			$ary_goods_id = explode(",",$_goods_id);
			if(!$model->EditIsSaleStatus($ary_goods_id,0,6))
			{
				//修改可销售商品为下架 失败制造错误回滚。
				$pdo->query('');
			}

			//业务逻辑结束
		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 0 , 'error' =>'事物操作不成功，导致添加加价调拨单失败!!'. $e->getMessage());
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交

		// M单制单人制单后变为自动审核：(如有需要可以打开)
		$ret = true;
		// $ret = $this->checkBillInfoM($id, $bill_no, $bill_info, $goods_id_str);
		
		return array('success' => 1 , 'x_id' => $id, 'label'=>$bill_no, 'check' => $ret);
	}

	/**
	 * 编辑单据时，事物提交
	 * $del_id String 要删除明细表中的单据ID,	warehouse_bill_goods表中的 bill_id
	 * $goods Array 新接收的明细数据
	 * $bill_info array 新接收的单据主信息
	 * $bill_id INT 单据主信息的ID
	 * $bill_no String 单据的编号
	 */
	public function update_shiwu($del_bill_ids, $del_goods_ids,$goods, $bill_info, $bill_id, $bill_no){
		$gwModel = new GoodsWarehouseModel(21);
		$billModel = new WarehouseBillModel(22);
		$warehouseGoodsModel = new WarehouseGoodsModel(21);
		//记录货品出入库记录
		$BoxGoodsLogModel = new BoxGoodsLogModel(22);
		$BoxGoodsLogModel->addLog(array_column($goods,0));
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//将删除的货品状态还原
			$sql = "UPDATE `warehouse_goods` SET `is_on_sale`= 2 WHERE `goods_id` IN (".$del_goods_ids.") and `is_on_sale`=5";
			//$pdo->query($sql);
			$changed=$pdo->exec($sql);  
			if($changed<>count(explode(",",$del_goods_ids))) {  
					$pdo->rollback();//事务回滚
					$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
					return array('success'=> 0 , 'error'=>'货品状态不是调拨中'.$del_goods_ids);
			}


			#业务逻辑开始[清空原有的货品明细]
			$sql = "DELETE FROM `warehouse_bill_goods` WHERE `bill_id` = {$bill_id}";

			$pdo->query($sql);
			#插入新的明细
			$goods_id_str = "";
			foreach($goods as $k =>$v){
				$v[10] = !empty($v[10]) ? $v[10] : 0;
				$sql = "INSERT INTO `warehouse_bill_goods` (
					`bill_id`, `bill_no`, `bill_type`, `goods_id`,
					`goods_sn`, `goods_name`, `num`, `jinzhong`,
					`caizhi`, `yanse`, `jingdu`, `jinhao`,
					`sale_price`, `zhengshuhao`, `addtime` 
					, `jiajialv`,`label_price` 
					) VALUES (
						'{$bill_id}', '{$bill_no}', 'M' , '{$v[0]}',
						'{$v[1]}', '{$v[12]}', 1, '{$v[3]}',
						'{$v[4]}', '{$v[8]}', '{$v[9]}', {$v[10]},
						'{$v[2]}', '{$v[11]}', '{$v['addtime']}' 
						, {$v['jiajialv']}, {$v[17]}
					)";
				$pdo->query($sql);

				//调拨单保存，实时的柜位下架，取消不用操作柜位，人工去做上架。审核也不做任何柜位操作。
				//获取调拨货品与所在仓库的默认柜位,货品下架到当前公司仓库的默认柜位
				$default_box = $gwModel->getDefaultBoxIdBygoods($v[0]);

				//正确流程
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

				//取得所有货号
				if ($k)
				{
					$goods_id_str .= ",'".$v[0]."'";
				}
				else
				{
					$goods_id_str .= "'".$v[0]."'";
				}
			}
			$goods_id_str = trim($goods_id_str , ',');
			//将新添加的货品状态改为调拨中
			$sql = "UPDATE `warehouse_goods` SET `is_on_sale`= 5, `box_sn` = '0-00-0-0' WHERE `goods_id` IN (".$goods_id_str.") and `is_on_sale`=2";
			//$pdo->query($sql);
			$changed=$pdo->exec($sql);  
			if($changed<>count(explode(",",$goods_id_str))) {  
					$pdo->rollback();//事务回滚
					$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
					return array('success'=> 0 , 'error'=>'货品状态不是库存中'.$goods_id_str);
			}
			#跟新单据主信息（warehouse_bill）
			$sql = "UPDATE `warehouse_bill` SET `to_company_id`='{$bill_info['to_company_id']}', `to_warehouse_id`='{$bill_info['to_warehouse_id']}', `bill_note`='{$bill_info['bill_note']}',`goods_num`='{$bill_info['goods_num']}' , `goods_total`='{$bill_info['goods_total']}', `goods_total_jiajia`='{$bill_info['goods_total_jiajia']}' , `to_warehouse_name`='{$bill_info['to_warehouse_name']}', `to_company_name`='{$bill_info['to_company_name']}', `order_sn`='{$bill_info['order_sn']}', `label_price_total`={$bill_info['label_price_total']} WHERE `id`={$bill_id}";

			$pdo->query($sql);

			//销售政策使销售政策商品状态变为下架
			$model = new ApiSalepolicyModel();
			$goods_id_str = str_replace("'","",$goods_id_str);
			$goods_id_str = explode(",",$goods_id_str);

			$del_goods_ids = str_replace("'","",$del_goods_ids);
			$del_goods_ids = explode(",",$del_goods_ids);
			//已绑定的商品不可推送上架
			foreach($del_goods_ids AS $key => $val){
				$bing = $warehouseGoodsModel->select2($fields = ' `order_goods_id` ' , $where = " `goods_id` = '{$val}' " , $is_all = 1);
				if($bing != 0){
					unset($del_goods_ids[$key]);
				}
			}
			if(!$model->EditIsSaleStatus($goods_id_str, 0 , 6 , $del_goods_ids))
			{
				//修改可销售商品为下架 失败制造错误回滚。
				$pdo->query('');
			}
		}
		catch(Exception $e){//捕获异常
			// echo $sql;
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 0 , 'error' =>'事物操作不成功，导致添加调拨单失败');
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return array('success' => 1 , 'error' =>'修改成功');
	}

	/** 审核单据 **/
	public function checkBillInfoM($bill_id,$bill_no,$info,$goods_id_str)
	{
		$gwModel = new GoodsWarehouseModel(21);
		$warehouseGoodsModel = new WarehouseGoodsModel(21);
		$boxModel = new WarehouseBoxModel(21);
		
		$time = date('Y-m-d H:i:s');
			
		$to_warehouse_id = $info['to_warehouse_id'];
			//获取入库仓的默认柜位ID
		$boxModel = new WarehouseBoxModel(21);
		$default_box = $boxModel->select2(' `id` ' , $where = " warehouse_id = {$to_warehouse_id} AND `box_sn` = '0-00-0-0' AND `is_deleted` = 1 " , $is_all = 3);
		$default_box_id  = $default_box ? $default_box : 0 ;	//入库仓默认柜位
			
			
			
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			
			//调货单--入库时间需要显示入当前仓库的最新的审核时间--boss717
				$goods_id_arr = explode(",",$goods_id_str);
				if(!empty($goods_id_arr))
				foreach($goods_id_arr as $key => $val)
				{
					$sql = "select count(*) from goods_warehouse where warehouse_id = '{$to_warehouse_id}' ";
				
					$checkExist = $this->db()->getRow($sql);
					
					if(!$checkExist)
					{
						$sql = "INSERT INTO `goods_warehouse`(`good_id`, `warehouse_id`, `box_id` , `add_time`,`create_user`) VALUES ( {$val} , '{$to_warehouse_id}','{$default_box_id}', '{$time}','SYSTEM')";
						$pdo->query($sql);
					}
					else//已经存在就更新入库时间为当前仓库最新的审核时间 boss717
					{
						$sql = "update `goods_warehouse` set `add_time` = '{$time}',`warehouse_id` = {$to_warehouse_id}
						where `good_id` = {$val}";
						$pdo->query($sql);
					}
				}
				
				
				
			//将货品状态改为库存 所在仓 所在公司改变
			$sql = "UPDATE `warehouse_goods`  SET  `is_on_sale`= 2,`change_time`='{$time}', `warehouse_id`='{$info['to_warehouse_id']}',`warehouse`='{$info['to_warehouse_name']}',`company_id`='{$info['to_company_id']}',`company`='{$info['to_company_name']}' WHERE `goods_id` IN (".$goods_id_str.") and `is_on_sale`=5";
			//$pdo->query($sql);
			$changed=$pdo->exec($sql);  
			if($changed<>count(explode(",",$goods_id_str))) {  
					$pdo->rollback();//事务回滚
					$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
					//return array('success'=> 0 , 'error'=>'货品状态不是调拨中'.$goods_id_str);
					return false;
			}
			#更改主表状态 warehouse_bill			
			$user = $_SESSION['userName'];
			$sql = "UPDATE `warehouse_bill` SET `bill_status` =2, `check_time`='{$time}', `check_user` = '{$user}' WHERE `id`={$bill_id}";
			$pdo->query($sql);

			#写入warehouse_bill_status 表
			$ip = Util::getClicentIp();
			$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$bill_id}, '{$bill_no}', 2, '{$time}'
				 , '{$user}', '{$ip}')";
			$pdo->query($sql);

			//更改仓储货品的所在仓库信息
			$sql = "SELECT `to_warehouse_id`, `to_warehouse_name`, `to_company_id`, `to_company_name` FROM `warehouse_bill` WHERE `id` = {$bill_id}";
			$bill_info = $this->db()->getRow($sql);

			$sql = "UPDATE `warehouse_goods` SET `warehouse` = '{$bill_info['to_warehouse_name']}', `warehouse_id` = {$bill_info['to_warehouse_id']}, `company` = '{$bill_info['to_company_name']}' , `company_id` =  {$bill_info['to_company_id']} WHERE `goods_id` IN ({$goods_id_str}) ";
			$pdo->query($sql);

            //记录最后一次转仓时间（change_time），更改本库库龄为1天，写入库龄表warehouse_goods_age；
            $sql = "UPDATE `warehouse_goods_age` SET `self_age` = 1 WHERE `goods_id` IN({$goods_id_str})";
            $pdo->query($sql);

			//货品上架到入库仓的默认柜位

			$default_box = $boxModel->select2('`id`' , " `warehouse_id` = {$bill_info['to_warehouse_id']} AND `box_sn` = '0-00-0-0' AND `is_deleted` = 1 " , $is_all = 3);
			if ($default_box )
			{
				$sql = "UPDATE `goods_warehouse` SET `box_id` = {$default_box} , `warehouse_id` = {$bill_info['to_warehouse_id']}, `create_user` = '' , `create_time` = '0000-00-00 00:00:00' WHERE `good_id` IN ({$goods_id_str}) ";
				$pdo->query($sql);
			}

			//推送数据到可销售商品列表，跟新可销售货品的所在仓库和公司
			$arr = explode(',', $goods_id_str);
			$send_info = array();
			foreach($arr as $key => $val){
				$send_info[$key]['goods_id'] = trim($val, "'") ;
				$send_info[$key]['warehouse_id'] = $info['to_warehouse_id'];
				$send_info[$key]['company_id'] = $info['to_company_id'];
				$send_info[$key]['warehouse'] = $info['to_warehouse_name'];
				$send_info[$key]['company'] = $info['to_company_name'];
			}
			$salepolicyModel = new ApiSalepolicyModel();
			$putres = $salepolicyModel->AddAppPayDetail($send_info);
			if($putres['error']){
				$sql = '推送销售政策失败';
				$pdo->query('');	//推送失败 回滚
			}

			//销售政策使销售政策商品状态变为上架 （注意：已绑定的商品不可推送上架）
			$model = new ApiSalepolicyModel();
			$goods_id_str = str_replace("'","",$goods_id_str);
			$goods_id_str = explode(",",$goods_id_str);
			//已绑定的商品不可推送上架
			foreach($goods_id_str AS $key => $val){
				$bing = $warehouseGoodsModel->select2($fields = ' `order_goods_id` ' , $where = " `goods_id` = '{$val}' " , $is_all = 1);
				if($bing != 0){
					unset($goods_id_str[$key]);
				}
			}
			if(!$model->EditIsSaleStatus($goods_id_str, 1 , 1))
			{
				//修改可销售商品为上架 失败制造错误回滚。
				$sql = "修改可销售商品为上架 失败制造错误回滚。";
				$pdo->query('');
			}
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

	/**取消单据**/
	public function closeBillInfoM($bill_id,$bill_no,$goods_id_str){
		$warehouseGoodsModel = new WarehouseGoodsModel(21);
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

			//将货品状态还原为库存
			$sql = "UPDATE `warehouse_goods`  SET  `is_on_sale`= '2'  WHERE `goods_id` IN (".$goods_id_str.") and `is_on_sale`=5";
			//$pdo->query($sql);
			$changed=$pdo->exec($sql);  
			if($changed<>count(explode(",",$goods_id_str))) {  
					$pdo->rollback();//事务回滚
					$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
					//return array('success'=> 0 , 'error'=>'货品状态不是调拨中'.$goods_id_str);
					return false;
			}
			#更改主表状态 warehouse_bill 的 bill_status 改为1
			$time = date('Y-m-d H:i:s');
			$user = $_SESSION['userName'];
			$sql = "UPDATE `warehouse_bill` SET `bill_status` =3, `check_user`='{$user}', `check_time`= '{$time}' WHERE id={$bill_id}";
			$pdo->query($sql);

			#写入warehouse_bill_status 表
			$ip = Util::getClicentIp();
			$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$bill_id}, '{$bill_no}', 3, '{$time}'
				 , '{$user}', '{$ip}')";
			$pdo->query($sql);

			//销售政策使销售政策商品状态变为上架
			$model = new ApiSalepolicyModel();
			$goods_id_str = str_replace("'","",$goods_id_str);
			$goods_id_str = explode(",",$goods_id_str);
			//已绑定的商品不可推送上架
			foreach($goods_id_str AS $key => $val){
				$bing = $warehouseGoodsModel->select2($fields = ' `order_goods_id` ' , $where = " `goods_id` = '{$val}' " , $is_all = 1);
				if($bing != 0){
					unset($goods_id_str[$key]);
				}
			}
			if(!$model->EditIsSaleStatus($goods_id_str, 1 , 1))
			{
				//修改可销售商品为上架 失败制造错误回滚。
				$pdo->query('');
			}
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

	/**
	 * 根据公司id 获取当前公司底下的仓库 (warehouse_rel)有效的仓库
	 */
	public function getWarehouseByCompany($company_id){
		$sql = "SELECT `b`.`id`,`b`.`name`,`b`.`code` FROM `warehouse_rel` AS `a` INNER JOIN `warehouse` AS `b` ON `a`.`company_id`={$company_id} AND `a`.`warehouse_id` = `b`.`id` and b.is_delete = 1";
		return $this->db()->getAll($sql);
	}

	/**
	 * 根据仓库id 获取当前公司底下的仓库 (warehouse_rel)有效的仓库
	 */
// 	public function getWarehouseBywarehouse_id($company_id){
// 		$sql = "SELECT `b`.`id`,`b`.`name`,`b`.`code` FROM `warehouse_rel` AS `a` INNER JOIN `warehouse` AS `b` ON `a`.`company_id`={$company_id} AND `a`.`warehouse_id` = `b`.`id` and b.is_delete = 1";
// 		return $this->db()->getAll($sql);
// 	}
	
	
	
	/*
	 * 检测货品的状态 是否是库存状态
	 **/
	public function checkGoodsStutasByGoodsId($goods_id){
		$sql = "SELECT `is_on_sale` FROM `warehouse_goods` WHERE `goods_id`='{$goods_id}'";
		return $this->db()->getOne($sql);
	}

	/*
	* 检测调拨的货品 是否是出库公司的货品
	* @param $goods_id Str 要检测的货品货号
	* @param $company_id Int 公司的ID
	*/
	public function checkGoodsIsInFromCompany($goods_id, $company_id){
		$sql = "SELECT `company_id` FROM `warehouse_goods` WHERE `goods_id` = '{$goods_id}' ";
		$company = $this->db()->getOne($sql);

		if($company == $company_id){
			return true;
		}else{
			return false;
		}
	}

	/** 根据货号，获取商品信息 **/
	public function getGoodsInfoByGoodsID($goods_id){
		$sql = "SELECT `goods_id`,`goods_sn`, `goods_name`, `jinzhong`, `caizhi`,`zhushilishu`,`fushilishu`,`fushizhong`,`yanse`,`jingdu`,`jinhao`,`chengbenjia`,`mingyichengben`,`zhengshuhao`, `is_on_sale`, `order_goods_id` FROM `warehouse_goods` WHERE `goods_id`='{$goods_id}' LIMIT 1";
		return $this->db()->getRow($sql);
	}

	/**根据货号，获取 仓库中货品（warehouse_goods）的主键**/
	public function getIDBygoodsID($goods_id){
		$sql = "SELECT `id` FROM `warehouse_goods` WHERE `goods_id` = '{$goods_id}'";
		return $this->db()->getOne($sql);
	}

	/** 根据货号，获取订单明细id **/
	public function getGoodsIdByOrderGoodsID($goods_id){
		$sql = "SELECT order_goods_id  FROM `warehouse_goods` WHERE `goods_id`='{$goods_id}' LIMIT 1";
		return $this->db()->getOne($sql);
	}


	/**
	 * @param $from 出库公司
	 * @param $to	入库仓
	 * @return int	限制金额
	*
	 * 转仓规则
	* 店与店之间转仓限额15万；店面转上海钻交所库是25万；店面转总库，或者是总库转店面，总库转上海钻交所库就是40万
	* 1、不是总公司和深圳分公司的其他公司就是店与店之间的转仓。
	* 2、店面转总公司的  如果是钻交所的那两个库就是25万
	* 3、除了深圳分公司库外的其他公司转总公司（除钻交所）以及总公司转到除了深圳分公司外的其他公司的限制是40万
	* 4、总公司的其他库转上海钻交所库限制是40万，总公司除了上海钻交所外的其他库之间的流转不做金额限制。
	*/
	public function transferHouseRule($from,$to)
	{
		//总公司库(除上海钻交所)
		$sql = 'SELECT `id`,`warehouse_id` FROM `warehouse_rel` WHERE `company_id` = 58';
		$z_house = $this->db()->getAll($sql);
		$z_house = array_column($z_house,'warehouse_id','id');
		//剔除上海钻交所仓库
		unset($z_house[array_search('395',$z_house)]);
		unset($z_house[array_search('373',$z_house)]);
		//深圳分公司库
		$sql = 'SELECT `id`,`warehouse_id` FROM `warehouse_rel` WHERE `company_id` = 445';
		$sz_house = $this->db()->getAll($sql);
		$sz_house = array_column($sz_house,'warehouse_id','id');
		//上海钻交所库395,373
		$sh_house = ['395','373'];
		$norm = 0;

		//总公司58;深圳分公司445
		//店转店
		if(!in_array($from,['58','445']) && !in_array($to,array_merge($z_house,$sz_house,$sh_house)) ){
			$norm = 150000;
		}
		//店转上海钻交所
		if(!in_array($from,['58','445']) && in_array($to,$sh_house)){
			$norm = 250000;
		}
		//店面转总库
		if(!in_array($from,['58','445']) && in_array($to,$z_house)){
			$norm = 400000;
		}
		//总库转店面/上海钻交所
		if(in_array($from,['58']) && !in_array($to,array_merge($z_house,$sz_house))){
			$norm = 400000;
		}
		//var_dump($norm);die;
		return $norm;

	}

	/**
	 * 根据订单号，和货号，检测订单号和货号是否匹配
	 * 如果货号有绑定订单，但是绑定的不是当前的订单号，return 错误
	 * 如果货品没有绑定订单，那么跳过不管
	 */
	public function CheckOrderByGoods($order_sn , $goods_id_arr)
	{
		$result = array('success'=>0,'error'=>'');
		$goods_id_arr = implode("','" , $goods_id_arr);
		$goods_id_arr = "'".$goods_id_arr."'";

		$ApiModel = new ApiModel();
		$res = $ApiModel->sales_api('GetGoodsInfobyOrderSN', array('order_sn'),array($order_sn));
		if(empty($res['return_msg'])){
			$result['error'] = '未查询到此订单相关商品信息';
			return $result;
		}

		//获取订单的所有明细ID
		$order_detail_ids = array_column($res['return_msg'] , 'id');

		$sql = "SELECT `goods_id`,`order_goods_id` FROM `warehouse_goods` WHERE `goods_id` IN ({$goods_id_arr})";
		$data = $this->db()->getAll($sql);
		foreach($data as $k => $val){
			if(!$val['order_goods_id']){
				continue;
			}

			if(!in_array($val['order_goods_id'] , $order_detail_ids)){
				$result['error'] = "入库仓为待取库时<br/>货号：{$val['goods_id']} 不是订单 {$order_sn} 的明细，不能制调拨单";
				return $result;
			}
		}
		$result['success'] = 1;
		return $result;
	}

	/**
	 * 配货（销账）的时候，如果是深圳分公司销账，并且还是总公司的货，那么，就自动把这个货转仓到深圳公司(这个方法已经挪到了销账方法中)
	 */
	/*public function autoCreateBillM($goods_id_arr , $order_sn){
		$wmodel = new WarehouseModel(21);
		$gwModel = new GoodsWarehouseModel(21);
		//计算单据的成本价
		$goods_str = implode("','", $goods_id_arr);
		$goods_str = "'".$goods_str."'";
		$goodsModel = new WarehouseGoodsModel(21);
		$bill_chengbenjia = $goodsModel->select2('sum(`chengbenjia`)', "`goods_id` IN ($goods_str)", $is_all = 1);

		$num = count($goods_id_arr);

		$time = date('Y-m-d H:i:s');
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			$sql = "INSERT INTO `warehouse_bill` (
				`bill_no`,  `to_company_id`, `to_warehouse_id`,
				`from_company_id`,`goods_num`,`chengbenjia`,
				`bill_note`,`to_warehouse_name`,
				`to_company_name`,`from_company_name`,
				`create_user`,`create_time`,`bill_type`,`order_sn`, `bill_status` , `check_time` , `check_user`
				) VALUES (
				'' , 445 , 503,
				58 , {$num} , {$bill_chengbenjia} ,
				'销账自动调拨(总公司调拨至深圳分公司)' , '深圳分公司' ,
				'BDD深圳分公司' , '总公司' , '{$_SESSION['userName']}' , '{$time}' , 'M' , '{$order_sn}' , 2 , '{$time}' , '{$_SESSION['userName']}'
				)";
			$pdo->query($sql);
			$id = $pdo->lastInsertId();

			//创建单据编号
			$billModel = new WarehouseBillModel(22);
			$bill_no = $billModel->create_bill_no('M',$id);
			$sql = "UPDATE `warehouse_bill` SET `bill_no`='{$bill_no}' WHERE `id`={$id}";
			$pdo->query($sql);

			//写入明细
			foreach($goods_id_arr AS $key => $goods_id){
				$sql = "SELECT `goods_sn` , `goods_name` , `num` , `jinzhong` , `caizhi` , `yanse` , `jingdu` , `jinhao` , `chengbenjia` , `zhengshuhao` FROM `warehouse_goods` WHERE `goods_id` = '{$goods_id}' ";
				$goods_info = $this->db()->getRow($sql);
				//给金耗为空的赋值 0
				if($goods_info['jinhao'] == ''){
					$goods_info['jinhao'] = 0;
				}
				$sql = "INSERT INTO `warehouse_bill_goods` (
		  			`bill_id`, `bill_no`, `bill_type`, `goods_id`,
		  			`goods_sn`, `goods_name`, `num`, `jinzhong`,
		  			`caizhi`, `yanse`,`jingdu`,`jinhao`,
		  			`chengbenjia`, `zhengshuhao`, `addtime`
		  			) VALUES (
		  			{$id}, '{$bill_no}', 'M', '{$goods_id}',
		  			'{$goods_info['goods_sn']}', '{$goods_info['goods_name']}', 1, '{$goods_info['jinzhong']}',
		  			'{$goods_info['caizhi']}', '{$goods_info['yanse']}', '{$goods_info['jingdu']}', {$goods_info['jinhao']},
		  			'{$goods_info['chengbenjia']}', '{$goods_info['zhengshuhao']}', '{$time}') ";
				$pdo->query($sql);

				//变更仓库warehoue_goods 货品的所在仓库 所在公司
				$sql = "UPDATE `warehouse_goods` SET `company` = 'BDD深圳分公司' , `warehouse` = '深圳分公司' , `company_id` = 445, `warehouse_id` = 503 WHERE `goods_id` = '{$goods_id}'";
				$pdo->query($sql);

				$default_box = $gwModel->getDefaultBoxIdBygoods($goods_id);
				//江湖告急流程
				if($default_box){
					//货品上架到入库仓的默认柜位
					$default_box = $wmodel->GetDefaultBox(503);
					$sql = "UPDATE `goods_warehouse` SET `box_id` = {$default_box['id']} , `warehouse_id` =503 , `create_user` = '' , `create_time` = '0000-00-00 00:00:00' WHERE `good_id` = '{$goods_id}' ";
					$pdo->query($sql);
				}
			}
			//插入订单号+快递单号
			$sql = "INSERT INTO `warehouse_bill_info_m` (`bill_id`,`ship_number`) VALUES ({$id}, '')";
			$pdo->query($sql);

			//写入warehouse_bill_status信息
			$ip = Util::getClicentIp();
			$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$id}, '{$bill_no}', 1, '{$time}', '{$_SESSION['userName']}', '{$ip}') ";
			$pdo->query($sql);

			//推送数据到可销售商品列表，跟新可销售货品的所在仓库和公司
			$send_info = array();
			foreach($goods_id_arr as $key => $val){
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

			//销售政策使销售政策商品状态变为上架 （注意：已绑定的商品不可推送上架）
			$Apisalepolicymodel = new ApiSalepolicyModel();
			//已绑定的商品不可推送上架
			foreach($goods_id_arr AS $key => $val){
				$bing = $goodsModel->select2($fields = ' `order_goods_id` ' , $where = " `goods_id` = '{$val}' " , $is_all = 1);
				if($bing != 0){
					unset($goods_id_arr[$key]);
				}
			}
			if(!$Apisalepolicymodel->EditIsSaleStatus($goods_id_arr, 1 , 1))
			{
				//修改可销售商品为上架 失败制造错误回滚。
				$pdo->query('');
			}
		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 0 , 'error' => '自动生成调拨单事物不成功，导致配货失败');
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return array('success' => 1 , 'error' => '自动生成调拨单成功');
	}*/


		/** 手工补单据，没事儿别用 --JUAN **/
	public function addBillauto($bill_info,$goods,$price_arr = array()){

		$billModel = new WarehouseBillModel(22);
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//业务逻辑开始
			$goods_num=count($goods);


			if($bill_info['bill_type'] == 'M')
			{
				$sql = "INSERT INTO `warehouse_bill` (
				`bill_no`,`bill_status`, `to_company_id`, `to_warehouse_id`,
				`from_company_id`,`goods_num`,`goods_total`,
				`bill_note`,`to_warehouse_name`,
				`to_company_name`,`from_company_name`,
				`create_user`,`create_time`,`check_user`,`check_time`,`bill_type`,`order_sn`
				) VALUES (
				'', 2,{$bill_info['to_company_id']}, {$bill_info['to_warehouse_id']},
				{$bill_info['from_company_id']},$goods_num, 0,
				'{$bill_info['bill_note']}','{$bill_info['to_warehouse_name']}',
				'{$bill_info['to_company_name']}',
				'{$bill_info['from_company_name']}','{$_SESSION['userName']}',
				'{$bill_info['create_time']}','{$_SESSION['userName']}',
				'{$bill_info['create_time']}', '{$bill_info['bill_type']}','{$bill_info['order_sn']}'
				)";
			}
			if($bill_info['bill_type'] == 'S')
			{
				$sql = "INSERT INTO `warehouse_bill` (
				`bill_no`,`bill_status`,`from_company_id`,`goods_num`,`goods_total`,
				`bill_note`,`from_company_name`,
				`create_user`,`create_time`,`check_user`,`check_time`,`bill_type`,`order_sn`
				) VALUES (
				'', 2,{$bill_info['from_company_id']},$goods_num, 0,
				'{$bill_info['bill_note']}','{$bill_info['from_company_name']}','{$_SESSION['userName']}',
				'{$bill_info['create_time']}','{$_SESSION['userName']}',
				'{$bill_info['create_time']}', '{$bill_info['bill_type']}','{$bill_info['order_sn']}'
				)";
			}

			$pdo->query($sql);
			$id = $pdo->lastInsertId();

			//创建单据编号
			$bill_no = $billModel->create_bill_no($bill_info['bill_type'],$id);
			$sql = "UPDATE `warehouse_bill` SET `bill_no`='{$bill_no}' WHERE `id`={$id}";
			$pdo->query($sql);
			$mingyi = 0;
			$chengben = 0;
			$zshijia = 0;
			//插入明细

			foreach ($goods as $key => $goods_id) {
				$shijia = 0;
				if(count($price_arr))
				{
					$shijia = $price_arr[$key]?$price_arr[$key]:0;
				}
				$sql = "select goods_sn,goods_name,jinzhong,caizhi,yanse,jingdu,jinhao,yuanshichengbenjia,mingyichengben,zhengshuhao from  warehouse_goods where goods_id = ".$goods_id;
				$gv = $this->db()->getRow($sql);
				$gv['jinhao']=$gv['jinhao']?$gv['jinhao']:0;
				$sql = "INSERT INTO `warehouse_bill_goods` (
		  			`bill_id`, `bill_no`, `bill_type`, `goods_id`,
		  			`goods_sn`, `goods_name`, `num`, `jinzhong`,
		  			`caizhi`, `yanse`,`jingdu`,`jinhao`,
		  			`sale_price`,`shijia`, `zhengshuhao`, `addtime`
		  			) VALUES (
		  			{$id}, '{$bill_no}', '{$bill_info['bill_type']}', '{$goods_id}',
		  			'{$gv['goods_sn']}', '{$gv['goods_name']}', 1, '{$gv['jinzhong']}',
		  			'{$gv['caizhi']}', '{$gv['yanse']}', '{$gv['jingdu']}', {$gv['jinhao']},
		  			'{$gv['mingyichengben']}','{$shijia}', '{$gv['zhengshuhao']}', '{$bill_info['create_time']}') ";
				$pdo->query($sql);
				$mingyi += $gv['mingyichengben'];
				$chengben += $gv['yuanshichengbenjia'];
				$zshijia += $shijia;
                

                //if($bill_info['bill_type'] == 'S'){
					$sql_update_goods_status="update warehouse_goods set is_on_sale='3' where goods_id='{$goods_id}' and is_on_sale='2' ";
	                $count=$pdo->exec($sql_update_goods_status);                	
	                if($count<>1){
						$pdo->rollback();//事务回滚
						$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
						return array('success' => 0 , 'error' =>'事物操作不成功，导致添加单据失败 可能是货品状态不在库存中'.$goods_id);                	
	                }
                //}
			}
			$sql = "update warehouse_bill set goods_total = ".$mingyi.",shijia = ".$zshijia."  where id = {$id}";
			$pdo->query($sql);

		}
		catch(Exception $e){//捕获异常
			print_r($e);exit;
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 0 , 'error' =>'事物操作不成功，导致添加调拨单失败');
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return array('success' => 1);
	}
	
	
	//根据成本价比例获取货品价格
	public function getGoodsPrice($goods,$shijia){
		$sql="SELECT SUM(chengbenjia) AS total FROM warehouse_goods WHERE goods_id IN ({$goods})";
		$total=$this->db()->getOne($sql);
		$goods_arr =explode(',', $goods);
		$lastNum=count($goods_arr)-1;//用于判断是否是最后一个
		$price_arr=array();//保存返回的价格数组
		$lastPrice=$shijia;//用于计算最后一个价格
		foreach ($goods_arr as $key => $goods_id) {
			if($key!=$lastNum){
				$sql="SELECT chengbenjia AS total FROM warehouse_goods WHERE goods_id ={$goods_id}";
				$chengbenjia=$this->db()->getOne($sql);
				$price=$chengbenjia / $total * $shijia;
				$price=round($price,2);
				$price_arr[]=$price;
				$lastPrice=$lastPrice-$price;
			}else{
				$price_arr[]=$lastPrice;
			}
		}
		
		
		return $price_arr;
	}

	
	function checkGoods($goods_arr, $from_company_id){
		$result=array('error'=>0,'result'=>array());
		
		foreach ($goods_arr as $goods_id){
			$sql="SELECT order_goods_id,company_id,is_on_sale FROM warehouse_goods WHERE goods_id={$goods_id}";
			$row=$this->db()->getRow($sql);
			if($row['is_on_sale'] != 2){
				$result['error']=1;
				$result['result']=array('success' => 0 , 'error' =>'货号'.$goods_id.'不是库存中的货品');
				return $result;
			}
			
			if($row['order_goods_id'] != '' && $row['order_goods_id'] !='0'){
				$result['error']=1;
				$result['result']=array('success' => 0 , 'error' =>'货号'.$goods_id.'已经绑定订单');
				return $result;
			}
			
			if($from_company_id != $row['company_id']){
				$result['error']=1;
				$result['result']=array('success' => 0 , 'error' =>'货号'.$goods_id.'所在公司与出库公司不一致');
				return $result;
			}
		}
		return $result;
	}
	
	/** 根据货号，获取商品信息 **/
	public function getGoodsM($goods_id){
		$sql = "SELECT goods_id,is_on_sale,order_goods_id,company_id,company,warehouse_id,warehouse FROM `warehouse_goods` WHERE `goods_id`='{$goods_id}' LIMIT 1";
		return $this->db()->getRow($sql);
	}	

}?>