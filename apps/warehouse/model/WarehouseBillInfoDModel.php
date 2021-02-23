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
class WarehouseBillInfoDModel extends Model
{

	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'warehouse_bill_info_d';
		$this->_dataObject = array("id"=>"主键","bill_id"=>"单据ID");
		parent::__construct($id,$strConn);
	}


	/**
	 * 退货	： 事物提交插入
	 * @param $bill		主表信息(array)
	 * @param $goods	退货商品(二维数组)
	 * @return bool
	 */
	public function insert_shiwu($bill,$goods)
	{
		$billmodel = new WarehouseBillModel(22);
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//退货单号

			$billModel = new WarehouseBillModel(22);
			$bill_fileds = $billModel->getField();
			$str1 = ''; $str2 = '';
			foreach ($bill as $k => $v) {
				if(!in_array($k,$bill_fileds)){
					return false;
				}else{
					$str1 .= "'$k',";
					$str2 .= "'$v',";
				}
			}
			#写入 warehouse_bill表
			$sql = "INSERT INTO `warehouse_bill`('bill_no','bill_type'".substr($str1,0,-1).") VALUES ('','D'".substr($str2,0,-1).")";
			$pdo->query($sql);
			#写入D表
			$id = $pdo->lastInsertId();
			$bill_no = $billmodel->create_bill_no('D' , $id);
			$sql = "UPDATE `warehouse_bill` SET `bill_no` = '{$bill_no}' WHERE `id` = {$id}";
			$pdo->query($sql);
			$sql = "INSERT INTO `warehouse_bill_info_d` SET `bill_id`={$id}";
			$pdo->query($sql);

			#商品明细
			$billGoodsModel = new WarehouseBillGoodsModel(22);
			$goods_fileds = $billGoodsModel->getField();
			$str1 = ''; $str2 = '';
			foreach ($goods as $g) {
				if(!is_array($g)){
					return false;
					exit;
				}
				foreach ($g as $k => $v) {
					if(!in_array($k,$goods_fileds)){
						return false;
					}else{
						$str1 .= "'$k',";
						$str2 .= "'$v',";
					}
				}
				#写入 warehouse_bill_goods表
				//bill_id=>$id,bill_no=>$bill_no,bill_type=>D,
				$sql = "INSERT INTO `warehouse_bill_goods`('bill_id','bill_no','bill_type'".substr($str1,0,-1).") VALUES ('".$id."','".$bill_no."','D'".substr($str2,0,-1).")";
				$pdo->query($sql);
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

	/**
	 * 校验商品销售状态
	 */
	public function checkGoodsSaleStatus($goods){
		if(empty($goods) || !is_array($goods)){
			return false;
		}
		$res = false;
		foreach ($goods as $v) {
			$sql = 'SELECT `is_on_sale` FROM `warehouse_goods` WHERE `goods_id` = '.$v['goods_id'];
			$goods_status = DB::cn(21)->getOne($sql);
			if($goods_status != 3){ //3=已销售【数据字典：warehouse.goods_status】
				return false;exit;
			}else{
				$res = true;
			}
		}
		return $res;
	}

	/** 根据货号，查询warehouse_goods 仓库里的货品信息 **/
	public function getGoodsInfoByGoodsID($goods_id)
	{
		$sql = "SELECT `goods_id` , `goods_sn` , `jinzhong` , `zuanshidaxiao` ,`zhengshuhao` ,`mairugongfei`,`zuixinlingshoujia`,`shoucun`,`changdu`,`caizhi`,`zhushi`,`zhushilishu`,`fushi`,`fushilishu`, `chengbenjia` , `fushizhong` , `yanse` ,`zongzhong`,  `goods_name` , `jingdu` , `is_on_sale`,`company_id`, `order_goods_id` FROM `warehouse_goods` WHERE `goods_id`='{$goods_id}' LIMIT 1";

		return $this->db()->getRow($sql);
	}
	//添加退货销售单
	public function add_shiwu($bill_info,$goods_list)
	{
		$result = array('success' => 0,'error' =>'');
		$yuanshichengben =0;$goods_total=0;$tuihuojia=0;

		//验证销售单
		$sql = "SELECT count(1) FROM warehouse_bill where `order_sn` = '{$bill_info['order_sn']}' AND `bill_status` = 2 AND `bill_type` = 'S'";
		$Is_exit_xiao = $this->db()->getOne($sql);
		if($Is_exit_xiao<1)
		{
			$result['error'] = "订单号".$bill_info['order_sn']."不存在有效的销售单";
			Util::jsonExit($result);
		}
		//获取该订单对应的销售单中的货号
		$sql = "SELECT wbg.goods_id FROM warehouse_bill as wb left join warehouse_bill_goods as wbg on wb.id=wbg.bill_id where  wb.`order_sn` = '{$bill_info['order_sn']}' AND wb.`bill_status` = 2 AND wb.`bill_type` = 'S' ";
		$goods_ids_arr = $this->db()->getAll($sql);
		$goods_ids     = array_column($goods_ids_arr,'goods_id');

		foreach ($goods_list as $key=>$val)
		{
			$sql = "select goods_id,goods_sn,goods_name,caizhi,jinzhong,zhushiyanse,zuanshidaxiao,yuanshichengbenjia,mingyichengben from warehouse_goods where goods_id = '{$val['goods_id']}'";
			$arr = $this->db()->getRow($sql);
			if(empty($arr))
			{
			   $result['error'] = $val['goods_id'].'货号不存在';
				Util::jsonExit($result);
			}
			//检查订单和货号的销售单是否存在
			if(!in_array($val['goods_id'],$goods_ids))
			{
			    $result['error'] = $val['goods_id'].'货号不存在订单'.$bill_info['order_sn']."对应的销售单中";
				Util::jsonExit($result);
			}
			$sql = "SELECT wbg.sale_price FROM warehouse_bill as wb left join warehouse_bill_goods as wbg on wb.id=wbg.bill_id where  wb.`order_sn` = '{$bill_info['order_sn']}' AND wb.`bill_status` = 2 AND wb.`bill_type` = 'S' AND wbg.goods_id='{$val['goods_id']}' ";
            $out_res= $this->db()->getRow($sql);
			if(!isset($val['sale_price']) && !empty($out_res)){
			    $val['sale_price'] = $out_res['sale_price'];
			}
			$goods_list[$key] = array_merge($val, $arr);
			$yuanshichengben += $arr['yuanshichengbenjia'];
			$goods_total += $val['sale_price'];
			$tuihuojia += $val['shijia'];
			/*
			if($_SESSION['userType']==1 && $val['shijia']!=$out_res['shijia']){
			    $result['error'] = $val['goods_id'].'货号退货价和销售价不一致';
				Util::jsonExit($result);
			}*/
		}
		$time = date('Y-m-d H:i:s');
		$user = $_SESSION['userName'];
		if($user == 'admin')
		{
			$user = 'system';
		}
		$billModel = new WarehouseBillModel(22);
		$ip = Util::getClicentIp();
		$type = 'D';

		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//业务逻辑开始
			//写入主表信息
			$sql = "INSERT INTO `warehouse_bill` (`bill_no`, `bill_type`, `bill_status`, `order_sn`, `goods_num`, `to_warehouse_id`, `to_warehouse_name`, `to_company_id`, `to_company_name`, `from_company_id`, `from_company_name`, `bill_note`, `yuanshichengben`, `goods_total`, `shijia`, `check_user`, `check_time`, `create_user`, `create_time`) VALUES ('','D',1,'{$bill_info['order_sn']}','{$bill_info['goods_num']}','{$bill_info['to_warehouse_id']}','{$bill_info['to_warehouse_name']}','{$bill_info['to_company_id']}','{$bill_info['to_company_name']}','0','','{$bill_info['bill_note']}','{$yuanshichengben}','{$goods_total}','{$tuihuojia}',null,'0000-00-00 00:00:00','{$user}','{$time}')";

			$pdo->query($sql);
			$id = $pdo->lastInsertId();
			$bill_no = $billModel->create_bill_no($type,$id);
			$sql = "UPDATE `warehouse_bill` SET `bill_no` = '{$bill_no}' WHERE `id` = {$id}";
			$pdo->query($sql);
			//写入明细
			foreach($goods_list AS $key => $val)
			{
				$sql = "INSERT INTO `warehouse_bill_goods`(`bill_id`, `bill_no`, `bill_type`, `goods_id`, `goods_sn`, `goods_name`, `num`, `warehouse_id`, `caizhi`, `jinzhong`, `yanse`, `zuanshidaxiao`, `yuanshichengben`, `sale_price`, `shijia`, `in_warehouse_type`, `account`, `addtime`, `pandian_status`, `guiwei`) VALUES (" . $id . ",'" . $bill_no . "','D'," . $val['goods_id'] . ",'" . $val['goods_sn'] . "','" . $val['goods_name'] . "',1," . $bill_info['to_warehouse_id'] . ",'" . $val['caizhi'] . "'," . $val['jinzhong'] . ",'" . $val['zhushiyanse'] . "'," . $val['zuanshidaxiao'] . "," . $val['yuanshichengbenjia'] . "," . $val['sale_price'] . "," . $val['shijia'] . ",'0' " .",0,'" . $time . "',0,null)";
				$pdo->query($sql);

				//改变货品状态为退货中 -------特殊情况  admin账号 不改变货品位置
				//if($_SESSION['userName'] != 'admin')
				//{

					$sql = "UPDATE `warehouse_goods` SET `is_on_sale`= 11  WHERE is_on_sale=3 and goods_id = " . $val['goods_id'];
					$count=$pdo->exec($sql);
					if($count<>1){
						$pdo->rollback();//事务回滚
						$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
						return array('success' => 0 , 'error' =>'事物操作不成功，导致添加销售退货单失败 可能货品不是已销售状态'.$val['goods_id']);
					}
				//}
			}

			//写入warehouse_bill_status信息
			$update_time = date('Y-m-d H:i:s');
			$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$id}, '{$bill_no}', 1 , '{$time}', '{$user}', '{$ip}') ";
			$pdo->query($sql);
			$pdo->commit();//如果没有异常，就提交事务
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 1 , 'x_id' => $id, 'label'=>$bill_no);
		}
		catch(Exception $e){//捕获异常
			echo $sql;die;
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 0 , 'error' =>'事物操作不成功，导致添加批发销售单失败');
		}
			
	}


		/**
	* 联表查询 获取编辑时的单据明细
	*/
	/*public function GetDetailByBillId($bill_id)
	{
		$sql = "SELECT `a`.`goods_id` , `a`.`goods_sn` , `a`.`goods_name` , `a`.`jinzhong` , `a`.`jingdu` , `a`.`yanse` , `a`.`zhengshuhao` , `a`.`zuanshidaxiao` , `a`.`mingyijia` , `a`.`xiaoshoujia` , `a`.`num` , `b`.`caizhi`, `b`.`zhushi`, `b`.`zhushilishu` , `b`.`fushi`, `b`.`fushizhong` , `b`.`fushilishu`, `b`.`shoucun` , `b`.`changdu` , `b`.`chengbenjia` , `b`.`zongzhong`, `b`.`fushizhong`,b.zuixinlingshoujia,b.mairugongfei FROM  `warehouse_bill_goods` AS `a` INNER JOIN `warehouse_goods` AS `b` ON `a`.`goods_id` = `b`.`goods_id` WHERE  `a`.`bill_id` = {$bill_id} ORDER BY `a`.`id`DESC";
		return $this->db()->getAll($sql);
	}*/
	/**
	* 编辑
	*/
	public function update_shiwu($bill_info , $goods_list)
	{
		$result = array('success' => 0,'error' =>'');
		$yuanshichengben =0;$goods_total=0;$tuihuojia=0;

		//验证销售单
		$sql = "SELECT count(1) FROM warehouse_bill where `order_sn` = '{$bill_info['order_sn']}' AND `bill_status` = 2 AND `bill_type` = 'S'";
		$Is_exit_xiao = $this->db()->getOne($sql);
		if($Is_exit_xiao<1)
		{
			$result['error'] = "订单号".$bill_info['order_sn']."不存在有效的销售单";
			Util::jsonExit($result);
		}
		//获取该订单对应的销售单中的货号
		$sql = "SELECT wbg.goods_id FROM warehouse_bill as wb left join warehouse_bill_goods as wbg on wb.id=wbg.bill_id where  wb.`order_sn` = '{$bill_info['order_sn']}' AND wb.`bill_status` = 2 AND wb.`bill_type` = 'S' ";
		$goods_ids_arr = $this->db()->getAll($sql);
		$goods_ids     = array();
		foreach ($goods_ids_arr as $k=>$v)
		{
			$goods_ids[] = $v['goods_id'];
		}

		foreach ($goods_list as $key=>$val)
		{
			$sql = "select goods_id,goods_sn,goods_name,caizhi,jinzhong,yanse,zuanshidaxiao,yuanshichengbenjia,mingyichengben from warehouse_goods where goods_id = '{$val['goods_id']}'";
			$arr = $this->db()->getRow($sql);
			if(empty($arr))
			{
			   $result['error'] = $val['goods_id'].'货号不存在';
				Util::jsonExit($result);
			}
						//检查订单和货号的销售单是否存在
			if(!in_array($val['goods_id'],$goods_ids))
			{
			    $result['error'] = $val['goods_id'].'货号不存在订单'.$bill_info['order_sn']."对应的销售单中";
				Util::jsonExit($result);
			}
			$goods_list[$key] = array_merge($val, $arr);
			$yuanshichengben += $arr['yuanshichengbenjia'];
			$goods_total += $arr['mingyichengben'];
			$tuihuojia += $val['shijia'];
		}
		$time = date('Y-m-d H:i:s');
		$user = $_SESSION['userName'];
		$billModel = new WarehouseBillModel(22);
		$ip = Util::getClicentIp();
		$type = 'D';

		#//查询旧数据货号
		$billgModel = new WarehouseBillGoodsModel(21);
		$old_goods = $billgModel->get_bill_data($bill_info['id']);
		$old_goods_id = array();
		foreach ($old_goods as $key=>$val)
		{
			$old_goods_id[] = $val['goods_id'];
		}
		$old_goods_ids = join("','",$old_goods_id);
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			$id			= $bill_info['id'];
			$bill_no    = $bill_info['bill_no'];
			#1、还原货品状态-已销售、删除单据明细数据
			#2、修改主表信息
			#3、重新写入明细
			#4、记录单据状态日志
			/********************************1--start************************************************/
			$sql = "DELETE FROM `warehouse_bill_goods` WHERE `bill_id` = {$bill_info['id']}";
			$pdo->query($sql);
			$sql = "UPDATE `warehouse_goods` SET `is_on_sale` = 3 WHERE `goods_id` IN ('{$old_goods_ids}')";
			$num = $pdo->exec($sql);
           /*  if($num != count(explode(',',$old_goods_ids))){
                return array('success' => 0 , 'error' =>'操作失败，原始货品');
            } */
			/********************************1--end************************************************/

			/********************************2--start************************************************/
			$sql = "update  `warehouse_bill` SET `order_sn`='{$bill_info['order_sn']}',`goods_num`='{$bill_info['goods_num']}',`to_warehouse_id`='{$bill_info['to_warehouse_id']}',`to_warehouse_name`='{$bill_info['to_warehouse_name']}',`to_company_id`='{$bill_info['to_company_id']}',`to_company_name`='{$bill_info['to_company_name']}',`bill_note`='{$bill_info['bill_note']}',`yuanshichengben`='{$yuanshichengben}',`goods_total`='{$goods_total}',`shijia`='{$tuihuojia}',`bill_status`=1 where id='{$bill_info['id']}'";
			$pdo->query($sql);
			/********************************2--start************************************************/

			//写入明细
			foreach($goods_list AS $key => $val)
			{
				$sql = "INSERT INTO `warehouse_bill_goods`(`bill_id`, `bill_no`, `bill_type`, `goods_id`, `goods_sn`, `goods_name`, `num`, `warehouse_id`, `caizhi`, `jinzhong`, `yanse`, `zuanshidaxiao`, `yuanshichengben`, `sale_price`, `shijia`, `in_warehouse_type`, `account`, `addtime`, `pandian_status`, `guiwei`) VALUES (" . $id . ",'" . $bill_no . "','D'," . $val['goods_id'] . ",'" . $val['goods_sn'] . "','" . $val['goods_name'] . "',1," . $bill_info['to_warehouse_id'] . ",'" . $val['caizhi'] . "'," . $val['jinzhong'] . ",'" . $val['yanse'] . "'," . $val['zuanshidaxiao'] . "," . $val['yuanshichengbenjia'] . "," . $val['mingyichengben'] . "," . $val['shijia'] . ",'0' " .",0,'" . $time . "',0,null)";
				$pdo->query($sql);

				//改变货品状态为退货中
				$sql = "UPDATE `warehouse_goods` SET `is_on_sale`= 11  WHERE goods_id =  '{$val['goods_id']}' ";
				$pdo->query($sql);
			}

			//写入warehouse_bill_status信息
			$update_time = date('Y-m-d H:i:s');
			$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$id}, '{$bill_no}', 1 , '{$time}', '{$user}', '{$ip}') ";
			$pdo->query($sql);
			
			$pdo->commit();//如果没有异常，就提交事务
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 1 , 'error' =>'修改成功');
		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 0 , 'error' =>'事物操作不成功，导致修改批发销售单失败');
		}
		
	}

	/**
	* 取消单据
	*/
	public function closebill($bill_id , $bill_no)
	{
		$user = $_SESSION['userName'];
		$time = date('Y-m-d H:i:s');
		$type = 'D';
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//业务逻辑开始

			//将货品状态还原为已销售
			$sql = "UPDATE `warehouse_goods` as g,`warehouse_bill_goods` as bg  SET  `is_on_sale`= '3'  WHERE g.goods_id = bg.goods_id and bg.bill_id = {$bill_id} and `is_on_sale`= '11'";
			$num = $pdo->exec($sql);
			if($num==0){
			    $pdo->rollback();//事务回滚
    			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
    			return array('success' => 0 , 'error' =>'操作失败');
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
			
			$pdo->commit();//如果没有异常，就提交事务
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 1 , 'error' =>'取消成功');
		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 0 , 'error' =>'事物操作不成功，导致取消批发销售单失败');
		}
		
	}
	/**
	* 审核
	*/
	public function checkBill($bill_id )
	{
		$user = $_SESSION['userName'];
		if($user == 'admin')
		{
			$user = 'system';
		}
		$time = date('Y-m-d H:i:s');
		$type = 'D';
		$model = new WarehouseBillModel($bill_id,21);
		$bill_no = $model->getValue('bill_no');
		$to_warehouse_id = $model->getValue('to_warehouse_id');
		$to_warehouse_name = $model->getValue('to_warehouse_name');
		$to_company_id = $model->getValue('to_company_id');
		$to_company_name = $model->getValue('to_company_name');
		//file_put_contents("d:/tt.txt","666\r\n",FILE_APPEND);

		$gdModel = new WarehouseBillGoodsModel(21);
		$goods = $gdModel->select2('`goods_id`', $where = "`bill_id` = {$bill_id}" , $is_all = 3);
		$goods_ids = '';
		$goodsCount=count($goods);
		
		//获取入库仓的默认柜位ID
		$boxModel = new WarehouseBoxModel(21);
		$default_box = $boxModel->select2(' `id` ' , $where = " warehouse_id = {$to_warehouse_id} AND `box_sn` = '0-00-0-0' AND `is_deleted` = 1 " , $is_all = 3);
		$default_box_id  = $default_box ? $default_box : 0 ;	//入库仓默认柜位
			
		foreach($goods as $key => $val){
			$goods_ids .= "'".$val['goods_id']."',";
		}
		$goods_ids = rtrim($goods_ids , ',');

		
		
			
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//业务逻辑开始
			
			//销售退货单--入库时间需要显示入当前仓库的最新的审核时间--boss717
			foreach($goods as $key => $val)
			{
			 
				$sql = "select count(*) from goods_warehouse where warehouse_id = '{$to_warehouse_id}' ";
			
				$checkExist = $this->db()->getRow($sql);
				
				if(!$checkExist)
				{
					$sql = "INSERT INTO `goods_warehouse`(`good_id`, `warehouse_id`, `box_id` , `add_time`,`create_user`) VALUES ( '{$val['goods_id']}' , '{$to_warehouse_id}','{$default_box_id}', '{$time}','SYSTEM')";
					$pdo->query($sql);
				}
				else//已经存在就更新入库时间为当前仓库最新的审核时间 boss717
				{
					$sql = "update `goods_warehouse` set `add_time` = '{$time}',`warehouse_id` = {$to_warehouse_id}
					where `good_id` = '{$val['goods_id']}' ";
					$pdo->query($sql);
				}
			}
			
		

			#更改主表状态 warehouse_bill
			$sql = "UPDATE `warehouse_bill` SET `bill_status` = 2, `check_time`='{$time}', `check_user` = '{$user}' WHERE `id`={$bill_id}";
			$pdo->query($sql);

            #重新入库，写入库龄表 warehouse_goods_age；
            //1、货品重新入库库，状态为库存，清空出库时间endtime（货品库龄重新开始，系统重新统计）；
            $sql = "UPDATE `warehouse_goods_age` SET `endtime` = '0000-00-00 00:00:00',`self_age` = 1 WHERE `goods_id` IN (".$goods_ids.")";
            $pdo->query($sql);

			#写入warehouse_bill_status 表
			$ip = Util::getClicentIp();
			$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$bill_id}, '{$bill_no}', 2, '{$time}'
				 , '{$user}', '{$ip}')";
			$pdo->query($sql);

			//if($_SESSION['userName'] != 'admin') //---admin账号 审核不改变货品位置
			//{
				#变更明细们的状态，变成库存状态,解除和订单的关系 -公司和仓储
				$sql = "UPDATE `warehouse_goods` SET `is_on_sale` = 2,`order_goods_id` = 0,`company` = '{$to_company_name}' , `company_id` = {$to_company_id} , `warehouse` = '{$to_warehouse_name}' , `warehouse_id` = {$to_warehouse_id} ,`change_time`='{$time}' WHERE is_on_sale=11 and `goods_id` IN ({$goods_ids})";
				//file_put_contents("d:/tt.txt",$sql."\r\n",FILE_APPEND);
				$count=$pdo->exec($sql);
                if($count<>$goodsCount){
					$pdo->rollback();//事务回滚
					$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
					return array('success' => 0 , 'error' =>'事物操作不成功，导致审核销售退货单失败 可能货品不是退货中状态'.$goods_ids);
                }
			//}
                $pdo->commit();//如果没有异常，就提交事务
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                return array('success' => 1 , 'error' =>'审核成功');
			//业务逻辑结束
		}
		catch(Exception $e)
		{//捕获异常
			// echo $sql;die;
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 0 , 'error' =>'事物操作不成功，导致审核批发销售单失败');
		}
		
	}

	//人工添加销售退货单的明细
	// $data array  退货单号，退货的货号，以及退货金额
	public function addDetailGoods($data){
		$user = $_SESSION['userName'];
		$time = date('Y-m-d H:i:s');
		$type = 'D';

		$sql = "SELECT `id` FROM `warehouse_bill` WHERE `bill_no` = '{$data['bill_no']}' LIMIT 1";
		$bill_id = $this->db()->getOne($sql);

		//获取货品的详细信息
		$sql = "SELECT `goods_sn` , `goods_name`, `warehouse_id`, `caizhi` , `jinzhong` , `yanse` ,`zuanshidaxiao` , `chengbenjia` , `mingyichengben` , `put_in_type` , `account` FROM `warehouse_goods` WHERE `goods_id` = '{$data['goods_id']}'";
		$goods = $this->db()->getRow($sql);
		if(empty($goods)){
			return array('success' => 0 , 'error' =>"找不到这货:{$data['goods_id']}");
		}

		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//跟新单据主表的信息
			$sql = "UPDATE `warehouse_bill` SET `shijia` = `shijia` + {$data['tuihuojia']} , `goods_num` = `goods_num` + 1 WHERE `bill_no` = '{$data['bill_no']}'";
			$pdo->query($sql);
			//添加明细表
			$sql = "INSERT INTO `warehouse_bill_goods`(
				`bill_id`, `bill_no`, `bill_type`, `goods_id`,
				`goods_sn`, `goods_name`, `num`, `warehouse_id`,
				`caizhi`, `jinzhong`, `yanse`, `zuanshidaxiao`,
				`yuanshichengben`, `sale_price`, `shijia`,
				`in_warehouse_type`, `account`, `addtime`) VALUES (
				{$bill_id} , '{$data['bill_no']}' , '{$type}' , '{$data['goods_id']}' ,
				'{$goods['goods_sn']}' , '{$goods['goods_name']}' , 1 , {$goods['warehouse_id']},
				'{$goods['caizhi']}' , '{$goods['jinzhong']}' , '{$goods['yanse']}', {$goods['zuanshidaxiao']},
				'{$goods['chengbenjia']}' , '{$goods['mingyichengben']}' , {$data['tuihuojia']},
				'{$goods['put_in_type']}' , '{$goods['account']}' , '{$time}'
				)";
				$pdo->query($sql);

			//改变货品状态为退货中
			$sql = "UPDATE `warehouse_goods` SET `is_on_sale`= 11  WHERE `goods_id` = {$data['goods_id']} and `is_on_sale`=10";
			$num = $pdo->exec($sql);
			if($num != 1){
			    $pdo->rollback();//事务回滚
			    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			    return array('success' => 0 , 'error' =>'操作失败，可能货品不是已销中状态');
			}
			$pdo->commit();//如果没有异常，就提交事务
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 1);
		}catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 0 , 'error' =>'事物操作不成功，导致操作失败');
		}
		
	}
}
?>