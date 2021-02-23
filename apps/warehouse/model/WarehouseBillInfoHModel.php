<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoHModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-21 21:31:17
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoHModel extends Model
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
	 *	@url WarehouseBillInfoHController/search
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
	 function: add_info_o
	description:维修退货单添加
	para:data 二维数组
	return: true
	*********************************/

	public function add_info_h ($data,$info)
	{
		//echo "<pre>";print_r($data);exit;
		$billModel  = new WarehouseBillModel(21);

		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

			//添加批发退货单
			$sql = "INSERT INTO `warehouse_bill` (`bill_no`,`goods_num`,`bill_note`,`create_user`,`create_time`,`bill_type`,`to_warehouse_id`,`to_warehouse_name`,`goods_total`,`shijia`,`pifajia`,`to_customer_id`,`to_company_id`,`to_company_name`)
			VALUES ('','{$info['goods_num']}','{$info['bill_note']}','{$info['create_user']}','{$info['create_time']}','{$info['bill_type']}','{$info['to_warehouse_id']}','{$info['to_warehouse_name']}','{$info['goods_total']}','{$info['shijia']}','{$info['pifajia']}','{$info['to_customer_id']}','{$info['to_company_id']}','{$info['to_company_name']}')";

			$pdo->query($sql);
			$id = $pdo->lastInsertId();
			$info['bill_no'] =  $billModel->create_bill_no('H',$id);
			$sql = "UPDATE `warehouse_bill` SET `bill_no`='{$info['bill_no']}' WHERE `id`={$id}";
			$pdo->query($sql);

			//批发退货单明细添加
			$str_goods_id = "";
			//var_dump($data);exit;
			$addtime=date("Y-m-d");
			foreach ($data as $key=>$value)
			{
			$sql = "INSERT INTO `warehouse_bill_goods`(
			`bill_id`,`bill_no`,`bill_type`,`addtime`,
			`goods_id`,`goods_sn`,`jinzhong`,`zuanshidaxiao`,`zhengshuhao`,`sale_price`,`shijia`,`pifajia`,
			`jingdu`,`yanse`,`goods_name`,`num`) VALUES (
			{$id},'{$info['bill_no']}','H','{$addtime}',
			'{$value[0]}','{$value[1]}','{$value[2]}',
			'{$value[3]}','{$value[4]}',
			'{$value[5]}','{$value[6]}','{$value[7]}','{$value[18]}','{$value[19]}','{$value[20]}',1)";
			$pdo->query($sql);
			if ($key)
			{
			$str_goods_id  .= ",'".$value[0]."'";
			}
			else
			{
				$str_goods_id  .= "'".$value[0]."'";
			}
			}
					//货品状态改变，因批发单签收后货品是库存状态，此处增加对库存状态的支持
				$sql = "UPDATE `warehouse_goods` SET `is_on_sale`=11 WHERE `goods_id` in (".$str_goods_id.") and `is_on_sale`in (3,2)";
				//$pdo->query($sql);
				$changed=$pdo->exec($sql);  
			    if($changed<>count(explode(",",$str_goods_id))) {  
						$pdo->rollback();//事务回滚
						$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
						return array('success'=> 0 , 'error'=>'货品状态不是已销售'.$str_goods_id);
			    }

			//
			//重新核算价格
			$sql = "select sum(sale_price) as goods_total,sum(shijia) as shijia,sum(pifajia) as pifajia from warehouse_bill_goods where bill_id ={$id}";
			$price_row = $this->db()->getRow($sql);
            //取货品所在公司;
            $sql = "select `company_id`,`company` from warehouse_goods where goods_id in(".$str_goods_id.")";
            $companyInfo = $this->db()->getRow($sql);
			$sql = "update warehouse_bill set goods_total='{$price_row['goods_total']}',shijia='{$price_row['shijia']}',pifajia='{$price_row['pifajia']}',`from_company_id`='{$companyInfo['company_id']}',`from_company_name`='{$companyInfo['company']}' where id={$id}";
			$pdo->query($sql);


					//写入日志
				$time = date('Y-m-d H:i:s');
				$user = $_SESSION['userName'];
				$ip = Util::getClicentIp();
				$sql = "INSERT INTO `warehouse_bill_status`(`bill_id`,`bill_no`,`status`,`update_time`,`update_user`,`update_ip`) VALUES ({$id},'{$info['bill_no']}',1,'{$time}','{$user}','{$ip}')";
				$pdo->query($sql);

                

                }catch(Exception $e){//捕获异常
				    $pdo->rollback();//事务回滚
					$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
					return array('success' => 0 , 'error' =>'事物操作不成功，导致添加批发销售单失败.'.$sql);
				}
				$pdo->commit();//如果没有异常，就提交事务
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
				return array('success' => 1 , 'x_id' => $id, 'label'=>$info['bill_no']);
		}

		public function add_info_ha ($data,$info)
		{
		    //echo "<pre>";print_r($data);exit;
		    $billModel  = new WarehouseBillModel(21);
		
		    $pdo = $this->db()->db();//pdo对象
		    try{
		        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
		        $pdo->beginTransaction();//开启事务
		
		        //添加批发退货单
		        $sql = "INSERT INTO `warehouse_bill` (`bill_no`,`goods_num`,`bill_note`,`create_user`,`create_time`,`bill_type`,`to_warehouse_id`,`to_warehouse_name`,`goods_total`,`shijia`,`pifajia`,`to_customer_id`,`to_company_id`,`to_company_name`,`order_sn`)
		        VALUES ('','{$info['goods_num']}','{$info['bill_note']}','{$info['create_user']}','{$info['create_time']}','{$info['bill_type']}','{$info['to_warehouse_id']}','{$info['to_warehouse_name']}','{$info['goods_total']}','{$info['shijia']}','{$info['pifajia']}','{$info['to_customer_id']}','{$info['to_company_id']}','{$info['to_company_name']}','WUDINGDAN')";
		
		        $pdo->query($sql);
		        $id = $pdo->lastInsertId();
		            $info['bill_no'] =  $billModel->create_bill_no('HA',$id);
		            $sql = "UPDATE `warehouse_bill` SET `bill_no`='{$info['bill_no']}' WHERE `id`={$id}";
		        $pdo->query($sql);
		
		        //批发退货单明细添加
		        $str_goods_id = "";
		        //var_dump($data);exit;
		        $addtime=date("Y-m-d");
		        foreach ($data as $key=>$value)
		        {
		            if(empty($value[6])){
		                $value[6] = $value[5];
		            }
		            if(empty($value[7])){
		                $value[7] = $value[5];
		            }
		        $sql = "INSERT INTO `warehouse_bill_goods`(
		        `bill_id`,`bill_no`,`bill_type`,`addtime`,
		        `goods_id`,`goods_sn`,`jinzhong`,`zuanshidaxiao`,`zhengshuhao`,`sale_price`,`shijia`,`pifajia`,
		        `jingdu`,`yanse`,`goods_name`,`num`) VALUES (
		        {$id},'{$info['bill_no']}','H','{$addtime}',
		        '{$value[0]}','{$value[1]}','{$value[2]}',
		        '{$value[3]}','{$value[4]}',
		        '{$value[5]}','{$value[6]}','{$value[7]}','{$value[18]}','{$value[19]}','{$value[20]}',1)";
		        $pdo->query($sql);
		        if ($key)
		        {
		        $str_goods_id  .= ",'".$value[0]."'";
		    }
			else
			{
				$str_goods_id  .= "'".$value[0]."'";
			}
			}
		    //货品状态改变，因批发单签收后货品是库存状态，此处增加对库存状态的支持
		    $sql = "UPDATE `warehouse_goods` SET `is_on_sale`=11 WHERE `goods_id` in (".$str_goods_id.") and `is_on_sale`in (3,2)";
		        //$pdo->query($sql);
		        $changed=$pdo->exec($sql);
		        if($changed<>count(explode(",",$str_goods_id))) {
		        $pdo->rollback();//事务回滚
		        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		        return array('success'=> 0 , 'error'=>'货品状态不是已销售'.$str_goods_id);
		    }
		
		    //
		    //重新核算价格
		    $sql = "select sum(sale_price) as goods_total,sum(shijia) as shijia,sum(pifajia) as pifajia from warehouse_bill_goods where bill_id ={$id}";
		        $price_row = $this->db()->getRow($sql);
		            $sql = "update warehouse_bill set goods_total='{$price_row['goods_total']}',shijia='{$price_row['shijia']}',pifajia='{$price_row['pifajia']}' where id={$id}";
		        $pdo->query($sql);
		
		
		        //写入日志
		        $time = date('Y-m-d H:i:s');
		        $user = $_SESSION['userName'];
		        $ip = Util::getClicentIp();
		        $sql = "INSERT INTO `warehouse_bill_status`(`bill_id`,`bill_no`,`status`,`update_time`,`update_user`,`update_ip`) VALUES ({$id},'{$info['bill_no']}',1,'{$time}','{$user}','{$ip}')";
		        $pdo->query($sql);
		}
		            catch(Exception $e){//捕获异常		            
		            $pdo->rollback();//事务回滚
		            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		            return array('success' => 0 , 'error' =>'事物操作不成功，导致添加批发销售单失败'.$sql);
			}
						$pdo->commit();//如果没有异常，就提交事务
						$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
						return array('success' => 1 , 'x_id' => $id, 'label'=>$info['bill_no']);
				}
				
		/***
		 获取批发退货单单明细
		***/
		public function get_data_h($id)
		{
			$sql = "select a.sale_price,a.shijia,a.pifajia,b.* from warehouse_bill_goods a,warehouse_goods b where a.goods_id = b.goods_id AND bill_id = {$id}";
			return $this->db()->getAll($sql);
		}

		/***
		 获取批发销售单  销售价 连3表
		***/
		public function get_pifa_xiaoshoujia($goods_id,$to_customer_id,$is_on_sale)
		{
			//H2015042500003  and o.to_customer_id={$to_customer_id}
			//   and og.goods_id = g.goods_id  and o.bill_status=2 and og.bill_type='H' and o.to_customer_id={$to_customer_id}
			/*$sql = "select g.goods_id, og.shijia as shijia_price, og.shijia as pifa_price,g.*
			 from warehouse_goods as g,warehouse_bill_goods as og,warehouse_bill as o
			 where o.id=og.bill_id
			 and og.goods_id = g.goods_id  and o.bill_status in (2, 4) and og.bill_type='P' and o.to_customer_id={$to_customer_id}
			and g.goods_id = '{$goods_id}' and g.is_on_sale in ( {$is_on_sale}, 2)  order by o.create_time desc limit 0, 1";*/
			// 批发销售单加了签收功能，签收后货品会变成库存状态，单据变为已签收状态
			if (SYS_SCOPE == 'boss') {
                $sql = "select g.goods_id, p.sale_price as caigoujia,p.shijia,p.from_company_id,g.* from warehouse_goods as g left join (
		        SELECT og.goods_id as huo,o.to_customer_id,o.from_company_id,og.* from warehouse_bill_goods as og
		        inner join warehouse_bill as o on o.id = og.bill_id and o.bill_status in (2,4)
		        where og.goods_id = '{$goods_id}' and og.bill_type ='P' order by o.create_time desc limit 0, 1
		        ) p on p.huo = g.goods_id
		        where g.goods_id = '{$goods_id}' and p.to_customer_id = '{$to_customer_id}' and g.is_on_sale in ({$is_on_sale}, 2)";
			} else {
			    $sql = "select g.goods_id, p.sale_price as caigoujia,p.shijia,p.from_company_id,g.* from warehouse_goods as g inner join (
			     SELECT og.goods_id as huo,o.to_customer_id,o.from_company_id,og.* from warehouse_bill_goods as og
			     inner join warehouse_bill as o on o.id = og.bill_id and o.bill_status in (2,4)
			     where og.goods_id = '{$goods_id}' and og.bill_type ='P' and o.to_customer_id = '{$to_customer_id}'
			    ) p on p.huo = g.goods_id
			    inner join jxc_wholesale j on j.wholesale_id = p.to_customer_id and (j.sign_company = g.company_id or j.sign_required = 0)
			    where g.goods_id = '{$goods_id}' and g.is_on_sale in ({$is_on_sale}, 2) order by p.bill_id desc limit 1;";
			}
			//echo $sql;die;
			return $this->db()->getRow($sql);
		}
         
		public function get_pifa_xiaoshoujia_ha($goods_id)
		{
		    //H2015042500003  and o.to_customer_id={$to_customer_id}
		    //   and og.goods_id = g.goods_id  and o.bill_status=2 and og.bill_type='H' and o.to_customer_id={$to_customer_id}
		    /*$sql = "select g.goods_id, og.shijia as shijia_price, og.shijia as pifa_price,g.*
		     from warehouse_goods as g,warehouse_bill_goods as og,warehouse_bill as o
		     where o.id=og.bill_id
		     and og.goods_id = g.goods_id  and o.bill_status in (2, 4) and og.bill_type='P' and o.to_customer_id={$to_customer_id}
		     and g.goods_id = '{$goods_id}' and g.is_on_sale in ( {$is_on_sale}, 2)  order by o.create_time desc limit 0, 1";*/
		    // 批发销售单加了签收功能，签收后货品会变成库存状态，单据变为已签收状态
		    $sql = "select * from warehouse_goods  where goods_id = '{$goods_id}' and is_on_sale=3";
		    return $this->db()->getRow($sql);
		}

		public function update_info_h ($data,$info,$del_goods_id)
		{
			$time = date('Y-m-d H:i:s');
			//echo "<pre>";print_r($data);exit;
			$pdo = $this->db()->db();//pdo对象
			try{
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
				$pdo->beginTransaction();//开启事务

				//删除旧数据
				$sql = "DELETE FROM `warehouse_bill_goods` WHERE `bill_id` = {$info['id']}";
				$pdo->query($sql);
				//还原以前货品状态为已销售
				$sql ="update warehouse_goods set is_on_sale = '3' where goods_id in (".$del_goods_id.") ";
				$pdo->query($sql);
				//修改批发退货信息主表
				$sql = "UPDATE `warehouse_bill` SET `goods_num`={$info['goods_num']},`goods_total`='{$info['goods_total']}',`pifajia`='{$info['pifajia']}',`shijia`='{$info['shijia']}',`bill_note`='{$info['bill_note']}' WHERE `id`={$info['id']}";

				$pdo->query($sql);

				$str_goods_id = "";
				foreach ($data as $key=>$value)
			{
			$sql = "INSERT INTO `warehouse_bill_goods`(
			`bill_id`,`bill_no`,`bill_type`,
			`goods_id`,`goods_sn`,`jinzhong`,`zuanshidaxiao`,`zhengshuhao`,`sale_price`,`pifajia`,`shijia`,
			`jingdu`,`yanse`,`goods_name`,`addtime`) VALUES (
			{$info['id']},'{$info['bill_no']}','H',
			'{$value[0]}','{$value[1]}','{$value[2]}',
			'{$value[3]}','{$value[4]}',
			'{$value[5]}','{$value[7]}','{$value[6]}','{$value[18]}','{$value[19]}','{$value[20]}','{$time}')";
				$pdo->query($sql);
				if ($key)
				{
				$str_goods_id  .= ",'".$value[0]."'";
				}
				else
				{
					$str_goods_id  .= "'".$value[0]."'";
				}
				}
						//货品状态改变
				$sql = "UPDATE `warehouse_goods` SET `is_on_sale`=11 WHERE `goods_id` in (".$str_goods_id.") and `is_on_sale`=3";
				//$pdo->query($sql);
			    $changed=$pdo->exec($sql);  
			    if($changed<>count(explode(",",$str_goods_id))) {  
						$pdo->rollback();//事务回滚
						$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
						//return array('success'=> 0 , 'error'=>'货品状态不是已销售'.$str_goods_id);
			            return false;
			    }

			//重新核算价格
			$sql = "select sum(sale_price) as goods_total,sum(shijia) as shijia,sum(pifajia) as pifajia from warehouse_bill_goods where bill_id ={$info['id']}";
			$price_row = $this->db()->getRow($sql);
            //取货品所在公司;
            $sql = "select `company_id`,`company` from warehouse_goods where goods_id in(".$str_goods_id.")";
            $companyInfo = $this->db()->getRow($sql);
            $sql = "update warehouse_bill set goods_total='{$price_row['goods_total']}',shijia='{$price_row['shijia']}',pifajia='{$price_row['pifajia']}',`from_company_id`='{$companyInfo['company_id']}',`from_company_name`='{$companyInfo['company']}' where id={$info['id']}";
			$pdo->query($sql);

						//写入日志
					$time = date('Y-m-d H:i:s');
					$user = $_SESSION['userName'];
					$ip = Util::getClicentIp();
					$sql = "INSERT INTO `warehouse_bill_status`(`bill_id`,`bill_no`,`status`,`update_time`,`update_user`,`update_ip`) VALUES ({$info['id']},'{$info['bill_no']}',1,'{$time}','{$user}','{$ip}')";
					$pdo->query($sql);

		}
		catch(Exception $e){//捕获异常
			//echo $sql;exit;
		//print_r($e);exit;
		$pdo->rollback();//事务回滚
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;
		}





		/** 根据货号，检测是否是当前单据的明细 **/
		public function checkDetail($goods_id , $bill_id){
			$sql = "SELECT `id` FROM `warehouse_bill_goods` WHERE `goods_id` = '{$goods_id}' AND `bill_id` = {$bill_id}";
			return $this->db()->getOne($sql);
		}


		/**
		 * 编辑发货单时，获取相关的明细列表数据
		 * $bill_id Int 单据ID
		 * return array 明细
		 */
		public function getBillGoogsList($bill_id){
			$sql = "SELECT `b`.`goods_id`, `b`.`goods_sn`,  `b`.`goods_name`, `b`.`jinzhong`, `b`.`caizhi`, `b`.`yanse`, `b`.`jingdu`, `b`.`jinhao`, `b`.`sale_price`, `b`.`zhengshuhao` FROM `warehouse_bill` AS `a` LEFT JOIN `warehouse_bill_goods` AS `b` ON `a`.`id` = `b`.`bill_id`  WHERE `a`.`id`={$bill_id} ";
			return $this->db()->getAll($sql);
		}


		/** 取消单据 **/
		public function closeBillInfoH($bill_id,$bill_no,$goods_id_str){
		    
		    //$to_customer_id = $this->db()->getOne("select to_customer_id from `warehouse_bill` where id={$bill_id}");
		    //$sign_req = $this->db()->getOne("select ifnull(sign_required,0) as sign_required from jxc_wholesale where wholesale_id = '{$to_customer_id}' ");
		    $company_from = $this->db()->getOne("select company_from from `warehouse_bill` where id={$bill_id}");
			$pdo = $this->db()->db();//pdo对象
			try{
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
				$pdo->beginTransaction();//开启事务
				//TODO: 根据客户是否需要签收，来决定将货品状态还原为什么状态
				if(SYS_SCOPE=='boss'){
					$sql = "UPDATE `warehouse_goods`  SET  `is_on_sale`= 3  WHERE `goods_id` IN (".$goods_id_str.") and `is_on_sale`= 11";
				}else{				
					if ($company_from=='ishop') {
					    $sql = "UPDATE `warehouse_goods`  SET  `is_on_sale`= 3  WHERE `goods_id` IN (".$goods_id_str.") and `is_on_sale`= 11";
					} else {
					    $sql = "UPDATE `warehouse_goods`  SET  `is_on_sale`= 2  WHERE `goods_id` IN (".$goods_id_str.") and `is_on_sale`= 11";
					}
				}	

				//$pdo->query($sql);
				$changed=$pdo->exec($sql);  
			    if($changed<>count(explode(",",$goods_id_str))) {  
						$pdo->rollback();//事务回滚
						$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
						//return array('success'=> 0 , 'error'=>'货品状态不是退货中'.$goods_id_str);
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


		/** 审核单据 **/
		public function checkBillInfoH($bill_id,$bill_no,$info,$goods_id_str,$to_warehouse_id){
			$pdo = $this->db()->db();//pdo对象
			$time = date('Y-m-d H:i:s');
			
			//$to_warehouse_id = $info['to_warehouse_id'];
			//获取入库仓的默认柜位ID
			$boxModel = new WarehouseBoxModel(21);
			$default_box = $boxModel->select2(' `id` ' , $where = " warehouse_id = {$to_warehouse_id} AND `box_sn` = '0-00-0-0' AND `is_deleted` = 1 " , $is_all = 3);
			$default_box_id  = $default_box ? $default_box : 0 ;	//入库仓默认柜位
				
			
			try{
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
				$pdo->beginTransaction();//开启事务
				
				//批发退货单--入库时间需要显示入当前仓库的最新的审核时间--boss717
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
				//$sql = "UPDATE `warehouse_goods`  SET  `is_on_sale`= 2,`company_id`={$info['to_company_id']},`company`='{$info['to_company_name']}',`warehouse_id`={$info['to_warehouse_id']},`warehouse`='{$info['to_warehouse_name']}',`change_time`='{$time}' WHERE `goods_id` IN (".$goods_id_str.") and `is_on_sale`= 11";
                $sql = "UPDATE `warehouse_goods`  SET  `is_on_sale`= 2,`company_id`={$info['to_company_id']},`company`='{$info['to_company_name']}',`warehouse_id`='{$to_warehouse_id}',`warehouse`='{$info['to_warehouse_name']}',`change_time`='{$time}' WHERE `goods_id` IN (".$goods_id_str.") and `is_on_sale`= 11";
				//$pdo->query($sql);
				$changed=$pdo->exec($sql); 
				$idn = count(explode(",",$goods_id_str));
				// print_r($changed.$sql.$idn);
				// die;
			    if($changed<>$idn) {  
						$pdo->rollback();//事务回滚
						$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
						//return array('success'=> 0 , 'error'=>'货品状态不是退货中'.$goods_id_str);
						return false;
			    }	

				//var_dump($sql);exit;
				#更改主表状态 warehouse_bill
				
				$user = $_SESSION['userName'];
				$sql = "UPDATE `warehouse_bill` SET `bill_status` =2, `check_time`='{$time}', `check_user` = '{$user}',`to_warehouse_id` = '{$to_warehouse_id}', `to_warehouse_name` = '{$info['to_warehouse_name']}' WHERE `id`={$bill_id}";
				$pdo->query($sql);
                #重新入库，写入库龄表 warehouse_goods_age；
                //1、货品重新入库库，状态为库存，清空出库时间endtime（货品库龄重新开始，系统重新统计）
                $sql = "UPDATE `warehouse_goods_age` SET `endtime` = '0000-00-00 00:00:00',`self_age` = 1 WHERE `goods_id` IN (".$goods_id_str.")";
                $pdo->query($sql);
				#写入warehouse_bill_status 表
				$ip = Util::getClicentIp();
				$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$bill_id}, '{$bill_no}', 2, '{$time}'
				, '{$user}', '{$ip}')";
				$pdo->query($sql);

			}catch(Exception $e){//捕获异常
				//echo $sql;exit;
				$pdo->rollback();//事务回滚
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
				echo $sql;
				return false;
		    }
			$pdo->commit();//如果没有异常，就提交事务
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return true;
		}



		/** 浩鹏审核单据 **/
		public function ZTcheckBillInfoH($bill_id,$bill_no,$info,$goods_id_str){
			$pdo = $this->db()->db();//pdo对象
			$time = date('Y-m-d H:i:s');
			
			try{
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
				$pdo->beginTransaction();//开启事务

                $sql = "UPDATE `warehouse_goods`  SET  `is_on_sale`= 11,`change_time`='{$time}' WHERE `goods_id` IN (".$goods_id_str.") and `is_on_sale` in (3,11)";
                ///$sql = "UPDATE `warehouse_goods`  SET  `is_on_sale`= 2,`company_id`={$info['to_company_id']},`company`='{$info['to_company_name']}',`warehouse_id`='{$to_warehouse_id}',`warehouse`='{$info['to_warehouse_name']}',`change_time`='{$time}' WHERE `goods_id` IN (".$goods_id_str.") and `is_on_sale` in (3,11)";
				
				//$pdo->query($sql);
				$changed=$pdo->exec($sql); 
				$idn = count(explode(",",$goods_id_str));
				// print_r($changed.$sql.$idn);
				// die;
				/*
			    if($changed<>$idn) {  
						$pdo->rollback();//事务回滚
						$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
						//return array('success'=> 0 , 'error'=>'货品状态不是退货中'.$goods_id_str);
						return false;
			    }*/	

				//var_dump($sql);exit;
				#更改主表状态 warehouse_bill
				
				$user = $_SESSION['userName'];

				$sql = "UPDATE `warehouse_bill` SET `bill_status` =2, `check_time`='{$time}', `check_user` = '{$user}' WHERE `id`={$bill_id}";
				//echo $sql;
				$pdo->query($sql);
                
				#写入warehouse_bill_status 表
				$ip = Util::getClicentIp();
				$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$bill_id}, '{$bill_no}', 2, '{$time}'
				, '{$user}', '{$ip}')";
				$pdo->query($sql);
                
			}
			catch(Exception $e){//捕获异常
                $user = $_SESSION['userName'];
                if($user == 'admin') echo json_encode($e);
				//echo $sql;exit;
				$pdo->rollback();//事务回滚
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
				//echo $sql;
				return false;
		    }
			$pdo->commit();//如果没有异常，就提交事务
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return true;
		}

		/** 浩鹏签收单据 **/
		public function signBillInfoH($bill_id,$bill_no,$info,$goods_id_str,$to_warehouse_id,$remark){
			$pdo = $this->db()->db();//pdo对象
			$time = date('Y-m-d H:i:s');

            $billModel  = new WarehouseBillModel(21);       
			
			//$to_warehouse_id = $info['to_warehouse_id'];
			//获取入库仓的默认柜位ID
			$boxModel = new WarehouseBoxModel(21);
			$default_box = $boxModel->select2(' `id` ' , $where = " warehouse_id = {$to_warehouse_id} AND `box_sn` = '0-00-0-0' AND `is_deleted` = 1 " , $is_all = 3);
			$default_box_id  = $default_box ? $default_box : 0 ;	//入库仓默认柜位
				
			
			try{
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
				$pdo->beginTransaction();//开启事务
				
				//批发退货单--入库时间需要显示入当前仓库的最新的审核时间--boss717
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
				//$sql = "UPDATE `warehouse_goods`  SET  `is_on_sale`= 2,`company_id`={$info['to_company_id']},`company`='{$info['to_company_name']}',`warehouse_id`={$info['to_warehouse_id']},`warehouse`='{$info['to_warehouse_name']}',`change_time`='{$time}' WHERE `goods_id` IN (".$goods_id_str.") and `is_on_sale`= 11";
                $sql = "UPDATE `warehouse_goods`  SET  `is_on_sale`= 2,`company_id`={$info['to_company_id']},`company`='{$info['to_company_name']}',`warehouse_id`='{$to_warehouse_id}',`warehouse`='{$info['to_warehouse_name']}',`change_time`='{$time}' WHERE `goods_id` IN (".$goods_id_str.") and `is_on_sale`= 11";
				//$pdo->query($sql);
				$changed=$pdo->exec($sql); 
				$idn = count(explode(",",$goods_id_str));
				// print_r($changed.$sql.$idn);
				// die;
				/*
			    if($changed<>$idn) {  
						$pdo->rollback();//事务回滚
						$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
						//return array('success'=> 0 , 'error'=>'货品状态不是退货中'.$goods_id_str);
						return false;
			    }*/	

				//var_dump($sql);exit;
				#更改主表状态 warehouse_bill

                $remark = addslashes($remark);
				$user = $_SESSION['userName'];
				$sql = "UPDATE `warehouse_bill` SET `bill_status` =4, `sign_time`='{$time}', `sign_user` = '{$user}',`to_warehouse_id` = '{$to_warehouse_id}', `to_warehouse_name` = '{$info['to_warehouse_name']}' ,bill_note =concat(bill_note,' {$remark}') WHERE `id`={$bill_id}";
				$pdo->query($sql);
                #重新入库，写入库龄表 warehouse_goods_age；
                //1、货品重新入库库，状态为库存，清空出库时间endtime（货品库龄重新开始，系统重新统计）
                $sql = "UPDATE `warehouse_goods_age` SET `endtime` = '0000-00-00 00:00:00',`self_age` = 1 WHERE `goods_id` IN (".$goods_id_str.")";
                $pdo->query($sql);
				#写入warehouse_bill_status 表
				$ip = Util::getClicentIp();
				$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$bill_id}, '{$bill_no}', 4, '{$time}'
				, '{$user}', '{$ip}')";
				$pdo->query($sql);


                //#################################
                //浩鹏批发退货生成入库单
                
                $sql = "select * from warehouse_shipping.warehouse_bill_goods where bill_id = {$bill_id}";
                //获取明细信息
                $data = $this->db()->getAll($sql);
                //AsyncDelegate::dispatch('warehouse', array('event'=>'bill_H_create', 'bill_id' => $bill_id, 'goods_id_str'=>$goods_id_str, 'bill_info'=>$info, 'bill_goods_info'=> $data));

                $create_time = date("Y-m-d H:i:s");
                $check_time = date("Y-m-d H:i:s",time()+2000);

                //隐藏货号
                $sql = "select goods_id from warehouse_shipping.warehouse_goods where goods_id in(".$goods_id_str.") and hidden = 1";
                $l_goods_id = $this->db()->getAll($sql);
                $l_goods_id_str = '';
                if(!empty($l_goods_id)){
                    $l_goods_id = array_column($l_goods_id, 'goods_id');

                    $l_goods_id_str = implode(',', $l_goods_id);
                }
                if($l_goods_id_str){
                    //隐藏有隐藏货号的H单
                    $sql = "update warehouse_bill set hidden=1 where id={$bill_id}";
                    $pdo->query($sql);

                    //非隐藏货号
                    $sql = "select goods_id from warehouse_shipping.warehouse_goods where goods_id in(".$goods_id_str.") and hidden = 0";
                    $h_goods_id = $this->db()->getAll($sql);
                    $h_goods_id_str = '';
                    if(!empty($h_goods_id)){
                        $h_goods_id = array_column($h_goods_id, 'goods_id');

                        $h_goods_id_str = implode(',', $h_goods_id);
                    }
                    //删除H单中的非隐藏货号，重新计算H单数量金额
                    //$h_bill_no = "";
                    if($h_goods_id_str){
                        $sql = "delete from warehouse_shipping.warehouse_bill_goods where bill_id = {$bill_id} and goods_id in(".$h_goods_id_str.")";
                        $pdo->query($sql);

                        //重新核算H单数量价格
                        $sql = "select sum(sale_price) as goods_total,sum(shijia) as shijia,sum(yuanshichengben) as yuanshichengben, sum(pifajia) as pifajia, sum(num) as goods_num from warehouse_bill_goods where bill_id ={$bill_id} and goods_id not in(".$h_goods_id_str.")";
                        $price_row = $this->db()->getRow($sql);

                        $sql = "update warehouse_bill set goods_total='{$price_row['goods_total']}',yuanshichengben='{$price_row['yuanshichengben']}', pifajia='{$price_row['pifajia']}',shijia='{$price_row['shijia']}', goods_num = '{$price_row['goods_num']}' where id={$bill_id}";
                        $pdo->query($sql);

                        //添加非隐藏货号批发退货单
                        $sql = "INSERT INTO `warehouse_bill` (`bill_no`,`goods_num`,`bill_note`,`create_user`,`create_time`,`bill_type`,`bill_status`,`to_warehouse_id`,`to_warehouse_name`,`goods_total`,`shijia`,`pifajia`,`to_customer_id`,`to_company_id`,`to_company_name`)
                        VALUES ('','{$info['goods_num']}','{$info['bill_note']}','{$info['create_user']}','{$info['create_time']}','{$info['bill_type']}', '4','{$to_warehouse_id}','{$info['to_warehouse_name']}','{$info['goods_total']}','{$info['shijia']}','{$info['pifajia']}','{$info['to_customer_id']}','{$info['to_company_id']}','{$info['to_company_name']}')";

                        $pdo->query($sql);
                        $id = $pdo->lastInsertId();
                        $info['bill_no'] =  $billModel->create_bill_no('H',$id);
                        //$h_bill_no = ltrim($info['bill_no'], 'H');
                        $sql = "UPDATE `warehouse_bill` SET `bill_no`='{$info['bill_no']}' WHERE `id`={$id}";
                        $pdo->query($sql);

                        //批发退货单明细添加
                        //$fh_str_goods_id = "";

                        $addtime=date("Y-m-d");
                        foreach ($data as $key=>$value)
                        {
                            if(in_array($value['goods_id'], $h_goods_id)){
                                $sql = "INSERT INTO `warehouse_bill_goods`(
                                `bill_id`,`bill_no`,`bill_type`,`addtime`,
                                `goods_id`,`goods_sn`,`jinzhong`,`zuanshidaxiao`,`zhengshuhao`,`sale_price`,`shijia`,`pifajia`,
                                `jingdu`,`yanse`,`goods_name`,`num`) VALUES (
                                {$id},'{$info['bill_no']}','H','{$addtime}',
                                '{$value['goods_id']}','{$value['goods_sn']}','{$value['jinzhong']}',
                                '{$value['zuanshidaxiao']}','{$value['zhengshuhao']}',
                                '{$value['sale_price']}','{$value['shijia']}','{$value['pifajia']}','{$value['jingdu']}','{$value['yanse']}','{$value['goods_name']}',1)";
                                $pdo->query($sql);
                            }
                        }

                        //重新核算价格
                        $sql = "select sum(sale_price) as goods_total,sum(shijia) as shijia,sum(pifajia) as pifajia,sum(num) as goods_num from warehouse_bill_goods where bill_id ={$id}";
                        $price_row = $this->db()->getRow($sql);
                        //取货品所在公司;
                        $sql = "select `company_id`,`company` from warehouse_goods where goods_id in(".$goods_id_str.")";
                        $companyInfo = $this->db()->getRow($sql);
                        $sql = "update warehouse_bill set goods_total='{$price_row['goods_total']}',shijia='{$price_row['shijia']}',pifajia='{$price_row['pifajia']}',goods_num='{$price_row['goods_num']}',`from_company_id`='{$companyInfo['company_id']}',`from_company_name`='{$companyInfo['company']}' where id={$id}";
                        $pdo->query($sql);
                    }

                    //隐藏货号生成L单
                    $sql="select g.company_id,c.company_name,prc_id,s.name,(select wholesale_id from warehouse_shipping.jxc_wholesale j where j.sign_company=g.company_id limit 1) as to_customer_id,count(g.goods_id) as goods_num,sum(g.yuanshichengbenjia) as yuanshichengben,sum(g.biaoqianjia) as label_price,sum(g.jingxiaoshangchengbenjia-g.management_fee) as pifajia,sum(g.mingyichengben) as mingyichengbenjia 
                    from warehouse_goods g left join kela_supplier.app_processor_info s on g.prc_id=s.id left join cuteframe.company c on g.company_id=c.id
                    where g.goods_id in(".$l_goods_id_str.") group by prc_id order by prc_id";
                    
                    $h_bill_info = $this->db()->getAll($sql);

                    if(!empty($h_bill_info)){
                        foreach ($h_bill_info as $key => $val) {
                            //1、添加Bill主表
                            $sql = "INSERT INTO warehouse_shipping.`warehouse_bill` (
                                `id`, `bill_no`, `bill_type`, `bill_status`,
                                `goods_num`, `to_warehouse_id`, `to_warehouse_name`,
                                `to_company_id`,`to_company_name`, 
                                `from_company_id`, `from_company_name`,
                                `order_sn`,`bill_note`,
                                `check_user`, `check_time`, `create_user`, `create_time`,
                                `put_in_type`,`send_goods_sn`,
                                 `pro_id`, `pro_name`,`jiejia`,
                                 `goods_total`, `yuanshichengben`, `shijia`,
                                 `production_manager_name`,`label_price_total`,`company_id_from`
                                ) VALUES (
                                NULL, '0', 'L', 2,
                                {$val['goods_num']}, '1873', '浩鹏后库', 
                                '58','总公司',
                                0, '', 
                                '','{$info['to_customer_id']}', 
                                '李丽珍','{$check_time}','王永红', '{$create_time}',
                                '1', '',
                                '{$val['prc_id']}', '{$val['name']}', '1',
                                '{$val['yuanshichengben']}', '{$val['yuanshichengben']}', '0',
                                '','{$val['label_price']}',1)";
                            $pdo->query($sql);
                            $l_id = $pdo->lastInsertId();

                            $bill_no= $billModel->create_bill_no('L',$l_id);
                            $sql = "UPDATE `warehouse_bill` SET `bill_no`='{$bill_no}' WHERE `id`={$l_id}";
                            $pdo->query($sql);

                            //2.添加单据明细
                            $sql = "insert into warehouse_shipping.warehouse_bill_goods (id,bill_id,bill_no,bill_type,goods_id,goods_sn,goods_name,num,warehouse_id,caizhi,jinzhong,jingdu,yanse,zhengshuhao,zuanshidaxiao,in_warehouse_type,account,addtime,pandian_guiwei,sale_price,yuanshichengben,label_price)
                             select 0,'{$l_id}','{$bill_no}','L',g.goods_id,g.goods_sn,g.goods_name,1,1873,g.caizhi,g.jinzhong,g.jingdu,g.yanse,g.zhengshuhao,g.zuanshidaxiao,1,1,'2019-01-01 00:00:00','0-00-0-0',g.yuanshichengbenjia,g.yuanshichengbenjia,g.biaoqianjia from warehouse_goods g where company_id='{$val['company_id']}' and prc_id='{$val['prc_id']}' and g.goods_id in(".$l_goods_id_str.")";
                            $pdo->query($sql);

                            //3.添加结算商信息
                            $sql = "insert into warehouse_shipping.warehouse_bill_pay values (0,'{$l_id}','{$val['prc_id']}','{$val['name']}','3','2','2','{$val['yuanshichengben']}')";
                            $pdo->query($sql); 
                        }

                        $sql = "update warehouse_shipping.warehouse_goods set hidden='0',addtime='{$create_time}' where goods_id in(".$l_goods_id_str.")";
                        $pdo->query($sql);
                    }
                }
                //#################################

			}catch(Exception $e){//捕获异常
				//echo $sql;exit;
				$pdo->rollback();//事务回滚
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
				//echo $sql;
				return false;
		    }
			$pdo->commit();//如果没有异常，就提交事务
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return true;
		}


		/** 浩鹏财务结算审核 **/
		public function finCheckBillInfoH($bill_id,$bill_no){
			$pdo = $this->db()->db();//pdo对象
			$time = date('Y-m-d H:i:s');		
			
			try{
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
				$pdo->beginTransaction();//开启事务
	
				$user = $_SESSION['userName'];
				//$remark = addslashes($remark);
				$sql = "UPDATE `warehouse_bill` SET `jiejia` =1, `fin_check_time`='{$time}', `fin_check_user` = '{$user}' WHERE `id`={$bill_id}";
				//echo $sql;
				$pdo->query($sql);
               
			}
			catch(Exception $e){//捕获异常
				//echo $sql;exit;
                $user = $_SESSION['userName'];
                if($user == 'admin') echo json_encode($e);
				$pdo->rollback();//事务回滚
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
				//echo $sql;
				return false;
		    }
			$pdo->commit();//如果没有异常，就提交事务
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return true;
		}

}

?>