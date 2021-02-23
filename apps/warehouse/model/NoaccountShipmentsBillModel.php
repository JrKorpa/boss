<?php
/**
 *  -------------------------------------------------
 *   @file		: NoaccountShipmentsBillModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-22 19:18:48
 *   @update	:
 *
 *	单据名称		单据状态	货品状态	货品维修状态	货品所属公司
 *  -------------------------------------------------
 * 	维修发货单		已保存		已销售		待发货			维修发货单的\发货方\
 *					已审核		已销售		维修完成		为空
 *					已取消		已销售		维修受理		维修发货单的\发货方\
 *  -------------------------------------------------
 */
class NoaccountShipmentsBillModel extends Model
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
	 *	@url NoaccountShipmentsBillController/search
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


	/** 根据货号 获取货品信息 **/
	public function getGoodsInfoByGoodsID($goods_id){
		$sql = "SELECT * FROM `warehouse_goods` WHERE `goods_id` = '{$goods_id}'";
		return $this->db()->getRow($sql);
	}



	/*********************************
	 function: add_info_c
	description:其他出库单添加
	para:data 二维数组 e_info
	return: true
	*********************************/

	public function add_info_r($data,$info)
	{
		//$billModel  = new WarehouseBillModel(21);
		//$info['bill_no'] =  $billModel->create_bill_no('R');
		$time = date('Y-m-d H:i:s');
		$user = $_SESSION['userName'];
		$ip = Util::getClicentIp();
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

			     //添加无账发货单单据
                //$sql = "INSERT INTO `warehouse_shipping`.`virtual_return_bill` (`id`, `g_id`, `bill_status`, `bill_type`, `create_user`, `create_time`, `out_company_id`, `out_company_name`, `out_warehouse_id`, `out_warehouse_name`, `express_sn`, `remark`) VALUES (NULL, '1', '{$info['bill_status']}', '{$info['bill_type']}', '{$info['create_user']}', '{$info['create_time']}', '{$info['out_company_id']}', '{$info['out_company_name']}', '{$info['out_warehouse_id']}', '{$info['out_warehouse_name']}', '{$info['express_sn']}', '{$info['remark']}')";
                //$pdo->query($sql);
                //$id = $pdo->lastInsertId();
                //插入单据明细
                foreach ($data as $key => $value) {
                    //if(empty($value['goods_id'])) $value['goods_id'] = 0;
                    //$sql = "INSERT INTO `warehouse_shipping`.`virtual_bill_goods` (`id`, `bill_id`, `virtual_id`, `business_type`, `order_sn`, `goods_id`, `style_sn`, `ingredient_color`, `gold_weight`, `torr_type`, `product_line`, `style_type`, `finger_circle`, `credential_num`, `main_stone_weight`, `main_stone_num`, `deputy_stone_weight`, `deputy_stone_num`, `resale_price`) VALUES (null, '".$id."','".$value['id']."', '".$value['business_type']."', '".$value['order_sn']."', '".$value['goods_id']."', '".$value['style_sn']."', '".$value['ingredient_color']."', '".$value['gold_weight']."', '".$value['torr_type']."', '".$value['product_line']."', '".$value['style_type']."', '".$value['finger_circle']."', '".$value['credential_num']."', '".$value['main_stone_weight']."', '".$value['main_stone_num']."', '".$value['deputy_stone_weight']."', '".$value['deputy_stone_num']."', '".$value['resale_price']."')";
                    $sql = "INSERT INTO `warehouse_shipping`.`virtual_return_bill` (`id`, `g_id`, `bill_status`, `bill_type`, `create_user`, `create_time`, `out_company_id`, `out_company_name`, `out_warehouse_id`, `out_warehouse_name`, `express_sn`, `remark`) VALUES (NULL, '".$value['id']."', '{$info['bill_status']}', '{$info['bill_type']}', '{$info['create_user']}', '{$info['create_time']}', '{$info['out_company_id']}', '{$info['out_company_name']}', '{$info['out_warehouse_id']}', '{$info['out_warehouse_name']}', '{$info['express_sn']}', '{$info['remark']}')";
                    //$pdo->query($sql);
                    $pdo->query($sql);
                    $id = $pdo->lastInsertId();

                    //更改货品所在公司 仓库
                    //$sql = "update warehouse_shipping.virtual_return_goods set place_company_id = '0', place_company_name = '', place_warehouse_id = '0', place_warehouse_name = '' where id = '".$value['id']."'";
                    //$pdo->query($sql);
                }
                //添加无账发货货品
                //$sql = "INSERT INTO `warehouse_shipping`.`virtual_return_goods` (`id`, `business_type`, `order_sn`, `return_status`, `style_sn`, `ingredient_color`, `gold_weight`, `torr_type`, `product_line`, `style_type`, `finger_circle`, `credential_num`, `main_stone_weight`, `main_stone_num`, `deputy_stone_weight`, `deputy_stone_num`, `resale_price`, `out_goods_id`, `place_company_id`, `place_company_name`, `place_warehouse_id`, `place_warehouse_name`, `guest_name`, `guest_contact`, `return_remark`, `without_apply_time`, `apply_user`, `exist_account_gid`) VALUES (NULL, '{$data['business_type']}', '{$data['order_sn']}', '{$data['return_status']}', '{$data['style_sn']}', '{$data['ingredient_color']}', '{$data['gold_weight']}', '{$data['torr_type']}', '{$data['product_line']}', '{$data['style_type']}', '{$data['finger_circle']}', '{$data['credential_num']}', '{$data['main_stone_weight']}', '{$data['main_stone_num']}', '{$data['deputy_stone_weight']}', '{$data['deputy_stone_num']}', '{$data['place_warehouse_id']}', '{$data['out_goods_id']}', '{$data['place_company_id']}', '{$data['place_company_name']}', '{$data['place_warehouse_id']}', '{$data['place_warehouse_name']}', '{$data['guest_name']}', '{$data['guest_contact']}', '{$data['return_remark']}', '{$data['without_apply_time']}', '{$data['apply_user']}', '{$data['exist_account_gid']}');";
                //$pdo->query($sql);
                //$g_id = $pdo->lastInsertId();
                //添加发货退货单
                //$sql = "INSERT INTO `warehouse_bill` (`bill_no`,`to_company_id`,`goods_num`,`goods_total`,`shijia`,`bill_note`,`create_user`,`create_time`,`bill_type`,`to_company_name`,`order_sn`,`to_warehouse_id` , `to_warehouse_name`)
                //VALUES ('{$info['bill_no']}','{$info['to_company_id']}','{$info['goods_num']}',{$info['goods_total']},'{$info['shijia']}','{$info['bill_note']}','{$info['create_user']}','{$info['create_time']}','{$info['bill_type']}','{$info['to_company_name']}','{$info['order_sn']}' , {$info['to_warehouse_id']} , '{$info['to_warehouse_name']}') ";

                //$pdo->query($sql);
                //$id = $pdo->lastInsertId();

                //计算单据编号 回写回去
                //$bill_no =  $billModel->create_bill_no('O',$id);
                //$sql = "UPDATE `warehouse_shipping`.`virtual_return_bill` SET `g_id` = '{$g_id}' WHERE `id` = {$id}";
                //$pdo->query($sql);
				//业务逻辑结束
			}catch(Exception $e){//捕获异常
                var_dump($sql);die;
				$pdo->rollback();//事务回滚
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
				return false;
			}
			$pdo->commit();//如果没有异常，就提交事务
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return $id;
	}

	/***
	 function:select
	description:查询数据根据单号
	***/
	public function select($id)
	{
		$sql = "select pro_id,chuku_type from warehouse_bill_info_c where bill_id = {$id}";
		return $this->db()->getRow($sql);
	}

	/** 根据货号，检测是否是当前单据的明细 **/
	public function checkDetail($goods_id , $bill_id){
		$sql = "SELECT `id` FROM `warehouse_bill_goods` WHERE `goods_id` = '{$goods_id}' AND `bill_id` = {$bill_id}";
		return $this->db()->getOne($sql);
	}

	/***
	 查询维修退货单明细
	***/
	public function get_data_c($id)
	{
		$sql = "select b.*,a.sale_price,a.shijia from warehouse_bill_goods a,warehouse_goods b where a.goods_id = b.goods_id AND bill_id = {$id}";
		//echo $sql;exit;
		return $this->db()->getAll($sql);
	}

	/*********************************
	 function: update_info_c
	description:其他出库单添加
	para:data 二维数组 e_info
	return: true
	*********************************/
	public function update_info_r ($data,$info,$del_goods_id)
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

			//将老明细的货品维修状态变更为 维修受理
			$sql = "UPDATE `warehouse_goods` SET `weixiu_status` = 3 WHERE `goods_id` IN ({$del_goods_id})";
			$pdo->query($sql);

			//修改其他出库单信息主表
			$sql = "UPDATE `warehouse_bill` SET `goods_num`={$info['goods_num']},`goods_total`={$info['goods_total']},`bill_note`='{$info['bill_note']}',`order_sn`='{$info['order_sn']}' WHERE `id`={$info['id']}";

			$pdo->query($sql);

			$str_goods_id = "";
			foreach ($data as $key=>$value)
			{
				$sql = "INSERT INTO `warehouse_bill_goods`(
				`bill_id`,`bill_no`,`bill_type`,
				`goods_id`,`goods_sn`,`jinzhong`,`zuanshidaxiao`,`zhengshuhao`,`sale_price`,
				`shijia`,`caizhi`,`jingdu`,`yanse`,`goods_name` , `addtime`) VALUES (
				{$info['id']},'{$info['bill_no']}','R',
				'{$value[0]}','{$value[1]}','{$value[2]}',
				'{$value[3]}','{$value[4]}','{$value[6]}',
				0,'{$value[10]}','{$value[17]}','{$value[18]}','{$value[19]}', '{$time}')";

				$pdo->query($sql);
				if ($key){
					$str_goods_id  .= ",'".$value[0]."'";
				}
				else{
					$str_goods_id  .= "'".$value[0]."'";
				}
			}
			$str_goods_id = trim($str_goods_id, ',');
			//变更货品的维修状态
			$sql = "UPDATE `warehouse_goods` SET `weixiu_status` = 5 WHERE `goods_id` IN ({$str_goods_id})";
			$pdo->query($sql);

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

	/**
	 * 编辑维修发货单时，获取相关的明细列表数据
	 * $bill_id Int 单据ID
	 * return array 明细
	 */
	public function getBillGoogsList($bill_id){
		$sql = "SELECT `b`.`goods_id`, `b`.`goods_sn`,  `b`.`goods_name`, `b`.`jinzhong`, `b`.`caizhi`, `b`.`yanse`, `b`.`jingdu`, `b`.`jinhao`, `b`.`sale_price`, `b`.`zhengshuhao`,`g`.`weixiu_warehouse_id`,`g`.`weixiu_warehouse_name` FROM `warehouse_bill` AS `a` LEFT JOIN `warehouse_bill_goods` AS `b` ON `a`.`id` = `b`.`bill_id` AND `a`.`bill_type`='R' left join warehouse_goods `g` on `b`.goods_id=`g`.goods_id  WHERE `a`.`id`={$bill_id} ";
		return $this->db()->getAll($sql);
	}


	/** 审核单据 **/
	public function checkBillInfoR($bill_id,$bill_no,$info,$goods_id_str){
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

			$sql = "UPDATE `warehouse_goods`  SET  `weixiu_status`= 4 WHERE `goods_id` IN (".$goods_id_str.")";
			$pdo->query($sql);

			#更改主表状态 warehouse_bill
			$time = date('Y-m-d H:i:s');
			$user = $_SESSION['userName'];
			$sql = "UPDATE `warehouse_bill` SET `bill_status` =2, `check_time`='{$time}', `check_user` = '{$user}' WHERE `id`={$bill_id}";
			$pdo->query($sql);

			#写入warehouse_bill_status 表
			$ip = Util::getClicentIp();
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


	/**取消单据**/
	public function closeBillInfoR($bill_id,$bill_no,$goods_id_str){
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//货品状态变更为维修受理
			$sql = "UPDATE `warehouse_goods`  SET  `weixiu_status`= 3  WHERE `goods_id` IN (".$goods_id_str.")";
			$pdo->query($sql);
			//var_dump($sql);exit;
			#更改主表状态 warehouse_bill 的 bill_status 改为3
			$time = date('Y-m-d H:i:s');
			$user = $_SESSION['userName'];
			$sql = "UPDATE `warehouse_bill` SET `bill_status` = 3 , `check_user`='{$user}', `check_time`= '{$time}' WHERE id={$bill_id}";
			$pdo->query($sql);
			#写入warehouse_bill_status 表
			$ip = Util::getClicentIp();
			$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$bill_id}, '{$bill_no}', 3, '{$time}' , '{$user}', '{$ip}')";
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

}?>