<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoEModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-15 15:31:10
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoEModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = '';
        $this->_dataObject = array();
		parent::__construct($id,$strConn);
	}
	/*********************************
	function: add_info_e
	description:损益单添加
	para:data 二维数组 e_info
	return: true
	*********************************/

	public function add_info_e ($data,$info,$goods_str)
	{
		$billModel  = new WarehouseBillModel(21);
		$gwModel = new GoodsWarehouseModel(21);
		//记录货品下架出入库记录
		$boxGoodsLogModel = new BoxGoodsLogModel(22);
		$boxGoodsLogModel->addLog(array_column($data,0));
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

			//1、添加损益单
			$sql = "INSERT INTO `warehouse_bill` (`bill_no`,`from_company_id`,`goods_num`,`goods_total`,`shijia`,`bill_note`,`create_user`,`create_time`,`bill_type`,`to_warehouse_name`,`to_company_name`,`from_company_name`) VALUES ('{$info['bill_no']}','{$info['from_company_id']}','{$info['goods_num']}',{$info['chengbenjia']},'{$info['xiaoshoujia']}','{$info['bill_note']}','{$info['create_user']}','{$info['create_time']}','{$info['bill_type']}','','','{$info['from_company_name']}') ";
			$pdo->query($sql);
			$id = $pdo->lastInsertId();

			$billModel  = new WarehouseBillModel(22);
			$bill_no= $billModel->create_bill_no('E',$id);
			$sql = "UPDATE `warehouse_bill` SET `bill_no` = '{$bill_no}' WHERE `id`=$id";
			$pdo->query($sql);
			//2、损益明细添加
			#goods_id(0),put_in_type(1),jiejia(2),goods_sn(3),jinzhong(4),zhushilishu(5),zuanshidaxiao(6),chengbenjia(7),shijia(8),fushilishu(9),fushizhong(10),yanse(11),jingdu(12),zhengshuhao(13),goods_name(14)
			foreach ($data as $value)
			{
				$sql = "insert into `warehouse_bill_goods`(
					`bill_id`,`bill_no`,`bill_type`,`goods_id`,
					`goods_name`,`goods_sn`,`num`,`caizhi`,
					`jinzhong`,`yanse`,`zuanshidaxiao`,`sale_price`,
					`shijia`,`in_warehouse_type`,`account`,`addtime`) values (
					{$id},'{$bill_no}','E','{$value[0]}',
					'{$value[14]}','{$value[3]}','1','',
					'{$value[4]}','{$value[11]}','{$value[6]}','{$value[7]}',
					'{$value[8]}','{$value[1]}','{$value[2]}','{$info['create_time']}')";
					//	echo $sql;exit;
				$pdo->query($sql);

				//货品下架
				$default_box = $gwModel->getDefaultBoxIdBygoods($value[0]);
				if(!$default_box){
					// return array('success' => 0 , 'error'=> "货号：{$value[0]} 不存在相应柜位信息,下架失败！导致制单失败");
					//如果不存在默认柜位，自动创建一条货品的默认柜位信息
					$res = $billModel->CheckAndCreateBox($value[0]);
					if($res['success'] != 1){
						return array('success' => 0 , 'error' => $res['error']);
					}
					$default_box = $res['box_id'];
				}
				$sql = "UPDATE `goods_warehouse` SET `box_id` = {$default_box} WHERE `good_id` = '{$value[0]}'";
				$pdo->query($sql);
			}
			//3、改变货品状态(损益中)
			$sql ="UPDATE `warehouse_goods` SET `is_on_sale` =6, `box_sn` = '0-00-0-0' WHERE `goods_id` IN (".$goods_str.") and is_on_sale=2";
			$num = $pdo->exec($sql);
            if($num != count(explode(',',$goods_str))) {
                return array('success' => 0 , 'error' =>"操作失败，可能货品不是库存状态");
            }
			//修改可销售商品为下架
			$model = new ApiSalepolicyModel();
			$goods_str = str_replace("'","",$goods_str);
			$goods_str = explode(",",$goods_str);
			if(!$model->EditIsSaleStatus($goods_str,0,3))
			{
				//修改可销售商品为下架 失败制造错误回滚。
				$pdo->query('');
			}
			$pdo->commit();//如果没有异常，就提交事务
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success'=>true,'id'=>$id);
			//业务逻辑结束
		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 0 , 'error' =>"操作失败");
		}
		
	}
	/*********************************
	function: update_info_e
	description:损益单修改
	return: true
	*********************************/

	public function update_info_e ($data,$info,$goods_id)
	{
		$billModel  = new WarehouseBillModel(22);
		$gwModel = new GoodsWarehouseModel(21);
		//记录货品下架出入库记录
		$boxGoodsLogModel = new BoxGoodsLogModel(22);
		$boxGoodsLogModel->addLog(array_column($data,0));
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

			//1、删除已有的货品明细
			$sql = "DELETE FROM `warehouse_bill_goods` WHERE `bill_id` = {$info['id']}";
			$pdo->query($sql);
			//将已删除的货品状态还原
			if(!empty($goods_id)){
				$sql = "update `warehouse_goods` set `is_on_sale`=2 where `goods_id` in (".$goods_id.")";
				$pdo->query($sql);
			}

			//2、修改损益单
			$sql = "update `warehouse_bill` set `from_company_id` = {$info['from_company_id']},`from_company_name`='{$info['from_company_name']}',`goods_num`={$info['goods_num']},`goods_total`={$info['chengbenjia']},`shijia`={$info['xiaoshoujia']} where `id`={$info['id']}";
			$pdo->query($sql);

			//3、损益明细添加
			$new_goods_id = "";
			foreach ($data as $key=>$value)
			{
				/** 过滤啥也没填的，空的明细信息 **/
				if( ($value[0] == '') && ($value[1] == '') && ($value[2] == '')  && ($value[3] == '')  && ($value[4] == '')  && ($value[5] == '')  && ($value[6] == '')  && ($value[7] == '')  && ($value[8] == '')  && ($value[9] == '') ){
					unset($data[$key]);continue;
				}
				$sql = "INSERT INTO `warehouse_bill_goods`(
					`bill_id`,`bill_no`,`bill_type`,`goods_id`,
					`goods_name`,`goods_sn`,`num`,`caizhi`,
					`jinzhong`,`yanse`,`zuanshidaxiao`,`sale_price`,
					`shijia`,`in_warehouse_type`,`account`,`addtime`) VALUES (
					{$info['id']},'{$info['bill_no']}','E','{$value[0]}',
					'{$value[14]}','{$value[3]}','1','',
					'{$value[4]}','{$value[11]}','{$value[6]}','{$value[7]}',
					'{$value[8]}','{$value[1]}','{$value[2]}','{$info['create_time']}')";
					//	echo $sql;exit;
				$pdo->query($sql);

				//货品下架
				$default_box = $gwModel->getDefaultBoxIdBygoods($value[0]);
				if(!$default_box){
					// return array('success' => 0 , 'error'=> "货号：{$value[0]} 不存在相应柜位信息,下架失败！导致制单失败");
					//如果不存在默认柜位，自动创建一条货品的默认柜位信息
					$res = $billModel->CheckAndCreateBox($value[0]);
					if($res['success'] != 1){
						return array('success' => 0 , 'error' => $res['error']);
					}
					$default_box = $res['box_id'];
				}
				$sql = "UPDATE `goods_warehouse` SET `box_id` = {$default_box} WHERE `good_id` = '{$value[0]}'";
				$pdo->query($sql);

				if ($key)
				{
					$new_goods_id .= ",'".$value[0]."'";
				}
				else
				{
					$new_goods_id .= "'".$value[0]."'";
				}
			}
			//4、改变货品状态
			$sql = "UPDATE `warehouse_goods` SET `is_on_sale` =6, `box_sn` = '0-00-0-0' WHERE `goods_id` IN (".$new_goods_id.")";
			$pdo->query($sql);

			//修改可销售商品为下架
			$model = new ApiSalepolicyModel();
			$new_goods_id = str_replace("'","",$new_goods_id);
			$new_goods_id = explode(",",$new_goods_id);

			$goods_id = str_replace("'","",$goods_id);
			$goods_id = explode(",",$goods_id);
			if(!$model->EditIsSaleStatus($new_goods_id, 0 , 3 , $goods_id))
			{
				//修改可销售商品为下架.老明细自动上架 失败制造错误回滚。
				$pdo->query('');
			}
			$pdo->commit();//如果没有异常，就提交事务
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 1 , 'error'=> "操作成功");
			//业务逻辑结束
		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 0 , 'error'=> "程序事物执行失败，导致操作未成功");
		}
		
	}


	/***
	function:add_log
	description:添加单据操作日志
	***/
	public function add_log($data)
	{
		$sql = "INSERT INTO `warehouse_shipping`.`warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ('{$data['bill_id']}', '{$data['bill_no']}', '{$data['status']}', '{$data['update_time']}', '{$data['update_user']}', '{$data['update_ip']}');";
		return $this->db()->query($sql);
	}
	/***
	fun：check_info_e
	审核损益单
	***/
	public function check_info_e($id,$str_id)
	{
		$time = date('Y-m-d H:i:s');
		$pdo = $this->db()->db();//pdo对象
		try
		{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			// 1、货品改变状态 (7:已报损)      2、单据审核
			$sql = "UPDATE `warehouse_bill` SET `bill_status`=2, `check_user` = '{$_SESSION['userName']}', `check_time` = '{$time}' WHERE `id` ={$id}";
			$pdo->query($sql);
			$sql = "UPDATE `warehouse_goods` SET `is_on_sale`=7,`chuku_time` = '{$time}' WHERE `goods_id` IN (".$str_id.") and `is_on_sale`=6";
			$num = $pdo->exec($sql);
			if($num != count(explode(",",$str_id))){
			    $pdo->rollback();//事务回滚
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
               // return array('success' => 0 , 'error' =>'操作失败，可能货品不是益损中状态');
			   return false;
			}
            #记录出库时间，写入库龄表 warehouse_goods_age；
            //1、货品已返厂，记录出库时间endtime（货品库龄已结束，之后不再统计）；
            $sql = "UPDATE `warehouse_goods_age` SET `endtime` = '{$time}' WHERE `goods_id` IN (".$str_id.")";
            $pdo->query($sql);
			#记录出库时间，写入库龄表；
			/*$arr = explode(",",$str_id);
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
			//仓储货品下架（goods_warehouse 中的box_id 置0）
			$sql = "UPDATE `goods_warehouse` SET `box_id` = 0 , `create_time` = '0000-00-00 00:00:00' ,  `create_user` = '' WHERE `good_id` IN ({$str_id}) ";
			$pdo->query($sql);
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
	/***
	fun：cancel_info_e
	取消损益单
	***/
	public function cancel_info_e($id,$str_id)
	{
		$pdo = $this->db()->db();//pdo对象
		try
		{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			// )     1、单据取消 2、货品改变状态 (库存状态
			$sql = "UPDATE `warehouse_bill` SET `bill_status` = 3 WHERE `id` ={$id}";
			$pdo->query($sql);
			$sql = "UPDATE `warehouse_goods` SET `is_on_sale`=2 WHERE `goods_id` IN (".$str_id.") and `is_on_sale`=6";
			$num = $pdo->exec($sql);
            if($num != count(explode(",",$str_id))){
                $pdo->rollback();//事务回滚
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
               // return array('success' => 0 , 'error' =>'操作失败，可能货品不是益损中状态');
			   return false;
            }
			//修改可销售商品为上架
			$model = new ApiSalepolicyModel();
			$str_id = str_replace("'","",$str_id);
			$str_id = explode(",",$str_id);
			if(!$model->EditIsSaleStatus($str_id , 1 , 1))
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

	/** 根据货号，获取商品信息 **/
	public function getGoodsE($goods_id){
		$sql = "SELECT goods_id,is_on_sale,order_goods_id,company_id,company,warehouse_id,warehouse FROM `warehouse_goods` WHERE `goods_id`='{$goods_id}' LIMIT 1";
		return $this->db()->getRow($sql);
	}	
}

?>