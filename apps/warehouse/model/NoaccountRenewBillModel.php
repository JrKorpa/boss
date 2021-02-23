<?php
/**
 *  -------------------------------------------------
 *   @file		: NoaccountRenewBillModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-24 19:17:39
 *   @update	:
 *
 * 单据名称		单据状态	货品状态	货品维修状态	货品所属公司
 *-------------------------------------------------------------------------
 * 维修退货单	已保存		已销售 		维修申请 		维修退货单的\收货方\
 *				已审核		已销售 		维修受理 		维修退货单的\收货方\
 *				已取消		已销售 		维修取消 		为空
 *  -------------------------------------------------
 */
class NoaccountRenewBillModel extends Model
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
	 *	@url WarehouseBillInfoOController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
//		if($where['xxx'] != "")
//		{
//			$str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
//		}
//		if(!empty($where['xx']))
//		{
//			$str .= "`xx`='".$where['xx']."' AND ";
//		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}


	/** 根据货号，检测是否是当前单据的明细 **/
	public function checkDetail($goods_id , $bill_id){
		$sql = "SELECT `id` FROM `warehouse_bill_goods` WHERE `goods_id` = '{$goods_id}' AND `bill_id` = {$bill_id}";
		return $this->db()->getOne($sql);
	}

	/** 根据货号 获取货品信息 **/
	public function getGoodsInfoByGoodsID($goods_id){
		$sql = "SELECT * FROM `warehouse_goods` WHERE `goods_id` = '{$goods_id}'";
		return $this->db()->getRow($sql);
	}


	/*********************************
	 function: add_info_o
	description:维修退货单添加
	para:data 二维数组
	return: true
	*********************************/

	public function add_info_o($data,$info)
	{
		//$billModel  = new WarehouseBillModel(21);
		$time = date('Y-m-d H:i:s');
		$user = $_SESSION['userName'];
		$ip = Util::getClicentIp();
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

            //添加无账修退单单据
            $sql = "INSERT INTO `warehouse_shipping`.`virtual_return_bill` (`id`, `g_id`, `bill_status`, `bill_type`, `create_user`, `create_time`, `from_company_id`, `from_company_name`, `from_warehouse_id`, `from_warehouse_name`, `express_sn`, `remark`) VALUES (NULL, '1', '{$info['bill_status']}', '{$info['bill_type']}', '{$info['create_user']}', '{$info['create_time']}', '{$info['from_company_id']}', '{$info['from_company_name']}', '{$info['from_warehouse_id']}', '{$info['from_warehouse_name']}', '{$info['express_sn']}', '{$info['remark']}')";
            $pdo->query($sql);
            $id = $pdo->lastInsertId();
            //添加无账退货货品
            $sql = "INSERT INTO `warehouse_shipping`.`virtual_return_goods` (`id`, `business_type`, `order_sn`, `return_status`, `style_sn`, `caizhi`, `ingredient_color`, `gold_weight`, `torr_type`, `product_line`, `style_type`, `finger_circle`, `credential_num`, `main_stone_weight`, `main_stone_num`, `deputy_stone_weight`, `deputy_stone_num`, `resale_price`, `out_goods_id`, `place_company_id`, `place_company_name`, `place_warehouse_id`, `place_warehouse_name`, `guest_name`, `guest_contact`, `return_remark`, `without_apply_time`, `apply_user`, `exist_account_gid`) VALUES (NULL, '{$data['business_type']}', '{$data['order_sn']}', '{$data['return_status']}', '{$data['style_sn']}', '{$data['caizhi']}', '{$data['ingredient_color']}', '{$data['gold_weight']}', '{$data['torr_type']}', '{$data['product_line']}', '{$data['style_type']}', '{$data['finger_circle']}', '{$data['credential_num']}', '{$data['main_stone_weight']}', '{$data['main_stone_num']}', '{$data['deputy_stone_weight']}', '{$data['deputy_stone_num']}', '{$data['place_warehouse_id']}', '{$data['out_goods_id']}', '{$data['place_company_id']}', '{$data['place_company_name']}', '{$data['place_warehouse_id']}', '{$data['place_warehouse_name']}', '{$data['guest_name']}', '{$data['guest_contact']}', '{$data['return_remark']}', '{$data['without_apply_time']}', '{$data['apply_user']}', '{$data['exist_account_gid']}');";
            $pdo->query($sql);
            $g_id = $pdo->lastInsertId();
			//添加维修退货单
			//$sql = "INSERT INTO `warehouse_bill` (`bill_no`,`to_company_id`,`goods_num`,`goods_total`,`shijia`,`bill_note`,`create_user`,`create_time`,`bill_type`,`to_company_name`,`order_sn`,`to_warehouse_id` , `to_warehouse_name`)
			//VALUES ('{$info['bill_no']}','{$info['to_company_id']}','{$info['goods_num']}',{$info['goods_total']},'{$info['shijia']}','{$info['bill_note']}','{$info['create_user']}','{$info['create_time']}','{$info['bill_type']}','{$info['to_company_name']}','{$info['order_sn']}' , {$info['to_warehouse_id']} , '{$info['to_warehouse_name']}') ";

			//$pdo->query($sql);
			//$id = $pdo->lastInsertId();

			//计算单据编号 回写回去
			//$bill_no =  $billModel->create_bill_no('O',$id);
			$sql = "UPDATE `warehouse_shipping`.`virtual_return_bill` SET `g_id` = '{$g_id}' WHERE `id` = {$id}";
			$pdo->query($sql);

			//维修退货单明细添加
			//$str_goods_id = "";
			//var_dump($data );exit;
			/*foreach ($data as $key=>$value)
			{
				$sql = "INSERT INTO `warehouse_bill_goods`(
				`bill_id`,`bill_no`,`bill_type`,
				`goods_id`,`goods_sn`,`jinzhong`,`zuanshidaxiao`,`zhengshuhao`,`sale_price`,
				`shijia`,`caizhi`,`jingdu`,`yanse`,`goods_name` , `addtime`,`num`) VALUES (
				{$id},'{$bill_no}','O',
				'{$value['goods_id']}','{$value['goods_sn']}','{$value['jinzhong']}',
				'{$value['zuanshidaxiao']}','{$value['zhengshuhao']}',
				'{$value['sale_price']}','{$value['shijia']}','{$value['caizhi']}','{$value['jingdu']}','{$value['yanse']}','{$value['goods_name']}' , '{$time}',1)";

				$pdo->query($sql);
				if ($key){
					$str_goods_id  .= ",'".$value['goods_id']."'";
				}else{
					$str_goods_id  .= "'".$value['goods_id']."'";
				}
			}*/
			//货品状态改变
			//$sql = "UPDATE `warehouse_goods` SET `weixiu_status`= 2 , `weixiu_company_id` = {$info['to_company_id']} , `weixiu_company_name` = '{$info['to_company_name']}' , `weixiu_warehouse_id` = {$info['to_warehouse_id']}  , `weixiu_warehouse_name` = '{$info['to_warehouse_name']}' WHERE `goods_id` in (".$str_goods_id.")";
			//$pdo->query($sql);

			//写入日志
			//$sql = "INSERT INTO `warehouse_bill_status`(`bill_id`,`bill_no`,`status`,`update_time`,`update_user`,`update_ip`) VALUES ({$id},'{$bill_no}',1,'{$time}','{$user}','{$ip}')";
			//业务逻辑结束
		}catch(Exception $e){//捕获异常
           // var_dump($e);
			 //echo $sql;
		$pdo->rollback();//事务回滚
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return $id;
	}

	/***
	 维修退货单单明细
	***/
	public function get_data_o($id)
	{
		$sql = "select a.*,b.* from warehouse_bill_goods a,warehouse_goods b where a.goods_id = b.goods_id AND bill_id = {$id}";
		return $this->db()->getAll($sql);
	}

	/*********************************
	 function: update_info_c
	description:其他出库单添加
	para:data 二维数组 e_info
	return: true
	*********************************/
	public function update_info_o ($data,$info,$del_goods_id)
	{
		$time=date('Y-m-d H:i:s');
		$update_ip=Util::getClicentIp();
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//删除旧数据
			$sql = "DELETE FROM `warehouse_bill_goods` WHERE `bill_id` = {$info['id']}";
			$pdo->query($sql);

			//将老明细的货品维修状态变更
			$sql = "UPDATE `warehouse_goods` SET `weixiu_status` = 0 , `weixiu_company_id` = 0 , `weixiu_company_name` = '' , `weixiu_warehouse_id` = 0 , `weixiu_warehouse_name` = '' WHERE `goods_id` IN({$del_goods_id})";
			$pdo->query($sql);

			//修改退货维修信息主表
			$sql = "UPDATE `warehouse_bill` SET `goods_num`={$info['goods_num']},`goods_total`={$info['goods_total']},`bill_note`='{$info['bill_note']}' WHERE `id`={$info['id']}";

			$pdo->query($sql);
			$str_goods_id = "";
			foreach ($data as $key=>$value)
			{
				$sql = "INSERT INTO `warehouse_bill_goods`(
				`bill_id`,`bill_no`,`bill_type`,
				`goods_id`,`goods_sn`,`jinzhong`,`zuanshidaxiao`,`zhengshuhao`,`sale_price`,
				`shijia`,`caizhi`,`jingdu`,`yanse`,`goods_name` , `addtime`) VALUES (
				{$info['id']},'{$info['bill_no']}','O',
				'{$value['goods_id']}','{$value['goods_sn']}','{$value['jinzhong']}',
				'{$value['zuanshidaxiao']}','{$value['zhengshuhao']}',
				'{$value['sale_price']}','{$value['shijia']}','{$value['caizhi']}','{$value['jingdu']}','{$value['yanse']}','{$value['goods_name']}' , '{$time}')";
				$pdo->query($sql);
				if ($key)
				{
				$str_goods_id  .= ",'".$value['goods_id']."'";
				}
				else
				{
					$str_goods_id  .= "'".$value['goods_id']."'";
				}
		}

		//货品状态改变
		$sql = "UPDATE `warehouse_goods` SET `weixiu_status`=2 , `weixiu_company_id` = {$info['to_company_id']} , `weixiu_company_name` = '{$info['to_company_name']}' , `weixiu_warehouse_id` = {$info['to_warehouse_id']} , `weixiu_warehouse_name` = '{$info['to_warehouse_name']}'  WHERE `goods_id` in (".$str_goods_id.")";
		$pdo->query($sql);
		//添加日志
		$sql = "INSERT INTO `warehouse_bill_status`(`bill_id`,`bill_no`,`status`,`update_time`,`update_user`,`update_ip`) VALUES ({$info['id']},'{$info['bill_no']}',1,'{$time}','{$_SESSION['userName']}','{$update_ip}')";
		$pdo->query($sql);
		}catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;
	}


	/**取消单据**/
	public function closeBillInfoO($bill_id,$bill_no,$goods_id_str){
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//将货品状态还原为库存
			$sql = "UPDATE `warehouse_goods`  SET  `weixiu_status`= '1' , `weixiu_warehouse_id` = 0 , `weixiu_warehouse_name` = '' , `weixiu_company_id` = 0, `weixiu_company_name` = ''  WHERE `goods_id` IN (".$goods_id_str.")";
			$pdo->query($sql);
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
		}catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;
	}


	/**
	 * 编辑调拨单时，获取相关的明细列表数据
	 * $bill_id Int 单据ID
	 * return array 明细
	 */
	public function getBillGoogsList($bill_id){
		$sql = "SELECT `b`.`goods_id`, `b`.`goods_sn`,  `b`.`goods_name`, `b`.`jinzhong`, `b`.`caizhi`, `b`.`yanse`, `b`.`jingdu`, `b`.`jinhao`, `b`.`sale_price`, `b`.`zhengshuhao` FROM `warehouse_bill` AS `a` LEFT JOIN `warehouse_bill_goods` AS `b` ON `a`.`id` = `b`.`bill_id` AND `a`.`bill_type`='O' WHERE `a`.`id`={$bill_id} ";
		return $this->db()->getAll($sql);
	}

	/** 审核单据 **/
	public function checkBillInfoO($bill_id,$bill_no,$info,$goods_id_str){
		$time = date('Y-m-d H:i:s');
		$user = $_SESSION['userName'];
		$ip = Util::getClicentIp();
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

			$sql = "UPDATE `warehouse_goods` SET `weixiu_status`= 3 WHERE `goods_id` IN (".$goods_id_str.")";
			$pdo->query($sql);

			#更改主表状态 warehouse_bill
			$sql = "UPDATE `warehouse_bill` SET `bill_status` = 2, `check_time`='{$time}', `check_user` = '{$user}' WHERE `id`={$bill_id}";
			$pdo->query($sql);

			#写入warehouse_bill_status 表
			$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$bill_id}, '{$bill_no}', 2, '{$time}'
			, '{$user}', '{$ip}')";
			$pdo->query($sql);

		}catch(Exception $e){//捕获异常
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
	public function getWarehouseByCompany($company_id)
	{
		$sql = "SELECT `b`.`id`,`b`.`name`,`b`.`code` FROM `warehouse_rel` AS `a` INNER JOIN `warehouse` AS `b` ON `a`.`company_id`={$company_id} AND `a`.`warehouse_id` = `b`.`id` and b.is_delete = 1 and b.name like '%维修%'";
		return $this->db()->getAll($sql);
	}


}

?>
