<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoWfModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-05-04 10:17:22
 *   @update	:
 * 	单据名称		单据状态	货品状态	货品维修状态	货品所属公司
 * 	维修转仓单		已保存		已销售		转仓中 5		维修配送单的\发货方\
 *					已审核		已销售		维修受理 2 		维修配送单的\收货方\
 *					已取消		已销售		维修受理 2		维修配送单的\发货方\
 *  -------------------------------------------------
 */
class WarehouseBillInfoWfModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'warehouse_bill_info_wf';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array();
		parent::__construct($id,$strConn);
	}

	//table插件获取货品信息
	public function getBillGoogsList($goods_id){
		//取已销售，并且维修状态是 维修受理的货品
		// $sql = "SELECT `goods_sn` , `goods_name` , `is_on_sale` , `jinzhong` , `zhushilishu` , `zuixinlingshoujia` , `fushilishu` , `fushizhong` , `yanse` , `jingdu` , `is_on_sale` FROM `warehouse_goods` WHERE `goods_id` = '{$goods_id}' AND `is_on_sale` = 3 AND `weixiu_status` = 3 LIMIT 1";
		$sql = "SELECT `goods_sn` , `goods_name` , `jinzhong` , `zhushilishu` , (`zuixinlingshoujia` * `num`) AS `lingshoujia` , `zuixinlingshoujia` , `fushilishu` , `fushizhong` , `yanse` , `jingdu` , `zhengshuhao` , `is_on_sale` , `weixiu_status` FROM `warehouse_goods` WHERE `goods_id` = '{$goods_id}' LIMIT 1";
		return $this->db()->getRow($sql);
	}

	//根据单据id，获取明细信息
	public function getBillGoogsListBybill($bill_id){
		$sql = "SELECT `a`.`goods_id` , `a`.`goods_sn` , `a`.`goods_name` , `a`.`jinzhong` , `a`.`jingdu` , `a`.`yanse` , `a`.`zhengshuhao` , `a`.`xiaoshoujia` , `b`.`zhushilishu` , (`b`.`zuixinlingshoujia` * `b`.`num`) AS `lingshoujia` , `b`.`zuixinlingshoujia` , `b`.`fushilishu` , `b`.`fushizhong` FROM `warehouse_bill_goods` AS `a` , `warehouse_goods` AS `b` WHERE `a`.`bill_id` = {$bill_id} AND `a`.`goods_id` = `b`.`goods_id`";
		return $this->db()->getAll($sql);
	}

	//根据检测某个货号是不是在指定的维修调拨明细中
	public function checkDetail($goods_id , $bill_id){
		$sql = "SELECT `id` FROM `warehouse_bill_goods` WHERE `goods_id` = '{$goods_id}' AND `bill_id` = {$bill_id}";
		return $this->db()->getOne($sql);
	}

	//普通查询
	public function select2($fields = '*' , $where = '1 limit 1' , $type ='one'){
		$sql = "SELECT {$fields} FROM `warehouse_bill_info_wf` WHERE {$where}";
		if($type == 'one'){
			return $this->db()->getOne($sql);
		}else if($type == 'row'){
			return $this->db()->getRow($sql);
		}else if($type == 'all'){
			return $this->db()->getAll($sql);
		}
	}



	//添加单据
	public function add_shiwu($bill_info , $goods){
		$user = $_SESSION['userName'];
		$time = date('Y-m-d H:i:s');
		$ip = Util::getClicentIp();
		$type = 'WF';
		$billModel = new WarehouseBillModel(22);
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			$sql = "INSERT INTO `warehouse_bill` (
				`bill_no` , `bill_type` , `order_sn` , `goods_num` , `to_warehouse_id` , `to_warehouse_name` , `from_company_id` , `from_company_name` ,  `bill_note` , `create_user` , `create_time` , `to_customer_id` , `to_company_id` , `to_company_name`,`goods_total`
				) VALUES (
				'','{$type}' , '{$bill_info['order_sn']}', {$bill_info['goods_num']} , {$bill_info['to_warehouse_id']} , '{$bill_info['to_warehouse_name']}' , {$bill_info['from_company_id']} , '{$bill_info['from_company_name']}' , '{$bill_info['bill_note']}' , '{$user}' , '{$time}' , {$bill_info['to_customer_id']} , {$bill_info['to_company_id']} , '{$bill_info['to_company_name']}',
				'{$bill_info['goods_total']}')";

			$pdo->query($sql);
			$id = $pdo->lastInsertId();

			//创建单据编号
			$bill_no = $billModel->create_bill_no($type,$id);
			$sql = "UPDATE `warehouse_bill` SET `bill_no`='{$bill_no}' WHERE `id`={$id}";
			$pdo->query($sql);

			$sql = "INSERT INTO `warehouse_bill_info_wf` (`bill_id` , `ship_number`) VALUES ({$id} , '{$bill_info['ship_number']}')";
			$pdo->query($sql);

			foreach($goods as $key => $row){
				$sql = "INSERT INTO `warehouse_bill_goods` (
						`bill_id` , `bill_no` , `bill_type` ,
						`goods_id` , `goods_sn` , `goods_name` , `jinzhong` ,
						`jingdu` , `yanse` , `zhengshuhao` , `sale_price` , `addtime`
					) VALUES (
						{$id} , '{$bill_no}' , '{$type}' ,
						'{$row[0]}' , '{$row[1]}' , '{$row[11]}' , '{$row[2]}' ,
						'{$row[9]}' , '{$row[8]}' , '{$row[10]}' , '{$row[5]}' , '{$time}'
					)";
				$pdo->query($sql);

				//待发货状态
				$sql = "UPDATE `warehouse_goods` SET `weixiu_status` = 6 WHERE `goods_id` = '{$row[0]}' AND `is_on_sale` = 3";
				$pdo->query($sql);
			}

			$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$id}, '{$bill_no}', 1, '{$time}', '{$user}', '{$ip}') ";
			$pdo->query($sql);

		}catch(Exception $e){//捕获异常
			echo $sql;
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 0 , 'error' =>'事物操作不成功，导致添加维修调拨单失败');
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return array('success' => 1 , 'x_id' => $id, 'label'=>$bill_no);
	}

	//编辑单据
	public function update_shiwu($bill_info , $goods){
		$user = $_SESSION['userName'];
		$time = date('Y-m-d H:i:s');
		$ip = Util::getClicentIp();
		$type = 'WF';

		$wgModel = new WarehouseBillGoodsModel(21);
		//获取修改前的明细
		$arr = $wgModel->select2($fields = '`goods_id`', $where = "`bill_id` = {$bill_info['id']}" , $is_all = 3);
		$detail_goods = '';
		foreach ($arr as $key => $val) {
			$detail_goods .= ",'".$val['goods_id']."'";
		}
		$detail_goods = ltrim($detail_goods , ',');

		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

			$sql = "UPDATE `warehouse_bill` SET `order_sn` = '{$bill_info['order_sn']}' , `bill_note` = '{$bill_info['bill_note']}' , `to_customer_id` = {$bill_info['to_customer_id']} , `goods_num` = {$bill_info['goods_num']},`goods_total`='{$bill_info['goods_total']}' WHERE `id` = {$bill_info['id']}";

			$pdo->query($sql);

			$sql = "UPDATE `warehouse_bill_info_wf` SET `ship_number` = '{$bill_info['ship_number']}' WHERE `bill_id` = {$bill_info['id']}";
			$pdo->query($sql);

			$sql = "DELETE FROM `warehouse_bill_goods` WHERE `bill_id` = {$bill_info['id']}";
			$pdo->query($sql);

			$sql = "UPDATE `warehouse_goods` SET `weixiu_status`= 3 WHERE `goods_id` IN ({$detail_goods})";
			$pdo->query($sql);

			foreach($goods as $key => $row){
				$sql = "INSERT INTO `warehouse_bill_goods` (
						`bill_id` , `bill_no` , `bill_type` ,
						`goods_id` , `goods_sn` , `goods_name` , `jinzhong` ,
						`jingdu` , `yanse` , `zhengshuhao` , `sale_price` , `addtime`
					) VALUES (
						{$bill_info['id']} , '{$bill_info['bill_no']}' , '{$type}' ,
						'{$row[0]}' , '{$row[1]}' , '{$row[11]}' , '{$row[2]}' ,
						'{$row[9]}' , '{$row[8]}' , '{$row[10]}' , '{$row[5]}' , '{$time}'
					)";
				$pdo->query($sql);

				//待发货状态
				$sql = "UPDATE `warehouse_goods` SET `weixiu_status` = 5 WHERE `goods_id` = '{$row[0]}' AND `is_on_sale` = 3";
				$pdo->query($sql);
			}

		}catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 0 , 'error' =>'事物操作不成功，导致修改维修调拨单失败');
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return array('success' => 1);
	}

	//审核单据
	public function ckeckBill($bill_id){
		$user = $_SESSION['userName'];
		$time = date('Y-m-d H:i:s');
		$ip = Util::getClicentIp();
		$type = 'WF';
		$billModel = new WarehouseBillModel($bill_id , 21);
		$bill_no = $billModel->getValue('bill_no');
		$to_warehouse_id = $billModel->getValue('to_warehouse_id');
		$to_warehouse_name = $billModel->getValue('to_warehouse_name');
		$to_company_id = $billModel->getValue('to_company_id');
		$to_company_name = $billModel->getValue('to_company_name');

		$model = new WarehouseBillGoodsModel(21);
		$goods_ids = '';
		$arr = $model->select2($fields = '`goods_id`', $where = "`bill_id` = {$bill_id}" , $is_all = 3);
		foreach ($arr as $key => $val) {
			$goods_ids .= ",'".$val['goods_id']."'";
		}
		$goods_ids = ltrim($goods_ids , ',');

		//获取收货方的公司和仓库信息
		$to_warehouse_id = $billModel->getValue('to_warehouse_id');
		$wrmodel = new WarehouseRelModel(21);
		$goods_next_here = $wrmodel->GetCompanyByWarehouse($fields = "`a`.`company_id` , `a`.`company_name` , `b`.`id` , `b`.`name`" , $where = "`b`.`id` = {$to_warehouse_id}" , 'getRow');

		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			$sql = "UPDATE `warehouse_bill` SET `bill_status` = 2 , `check_user` = '{$user}' , `check_time` = '{$time}' WHERE `id` = {$bill_id}";
			$pdo->query($sql);

			$sql = "UPDATE `warehouse_goods` SET `weixiu_status` = 3 WHERE `goods_id` IN ({$goods_ids})";
			$pdo->query($sql);

			//变更维修 的 销售货品现在所在的公司
			$sql = "UPDATE `warehouse_goods` SET `weixiu_company_id` = {$to_company_id}, `weixiu_company_name` = '{$to_company_name}' , `weixiu_warehouse_id` = {$to_warehouse_id}, `weixiu_warehouse_name` = '{$to_warehouse_name}' WHERE goods_id IN ($goods_ids)";
			$pdo->query($sql);

			$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$bill_id}, '{$bill_no}', 2, '{$time}', '{$user}', '{$ip}') ";
			$pdo->query($sql);

		}catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 0 , 'error' =>'事物操作不成功，导致操作失败');
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return array('success' => 1);
	}

	//取消单据
	public function closeBill($bill_id){
		$user = $_SESSION['userName'];
		$time = date('Y-m-d H:i:s');
		$ip = Util::getClicentIp();
		$type = 'WF';
		$billModel = new WarehouseBillModel($bill_id , 21);
		$bill_no = $billModel->getValue('bill_no');

		$model = new WarehouseBillGoodsModel(21);
		$goods_ids = '';
		$arr = $model->select2($fields = '`goods_id`', $where = "`bill_id` = {$bill_id}" , $is_all = 3);
		if(!empty($arr)){
			foreach ($arr as $key => $val) {
				$goods_ids .= ",'".$val['goods_id']."'";
			}
			$goods_ids = ltrim($goods_ids , ',');
		}

		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			$sql = "UPDATE `warehouse_bill` SET `bill_status` = 3 WHERE `id` = {$bill_id}";
			$pdo->query($sql);

			if(!empty($goods_ids)){		//如果单据没有明细，那就跳过这个执行
				$sql = "UPDATE `warehouse_goods` SET `weixiu_status` = 3 WHERE `goods_id` IN ({$goods_ids})";
				$pdo->query($sql);
			}

			$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$bill_id}, '{$bill_no}', 3, '{$time}', '{$user}', '{$ip}') ";
			$pdo->query($sql);

		}catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 0 , 'error' =>'事物操作不成功，导致操作失败');
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return array('success' => 1);
	}



}?>