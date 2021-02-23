<?php
/**
 *  -------------------------------------------------
 *   @file		: DiaOrderModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: liyanhong <462166282@qq.com>
 *   @date		: 2015-03-16 11:12:23
 *   @update	:
 *  -------------------------------------------------
 */
class DiaOrderModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'dia_order';
		$this->pk='order_id';
		$this->_prefix='';
        $this->_dataObject = array("order_id"=>" ",
			"type"=>"'MS'=>'买石单','SS'=>'送石单','HS'=>'还石单','TS'=>'退石单','YS'=>'遗失单','SY'=>'损益单','RK'=>'其他入库单','CK'=>'其他出库单'",
			"status"=>"0=新增，1=已保存，2=已审核，3=已取消",
			"fin_status"=>"0=未审核，1=已审核",
			"order_time"=>" ",
			"in_warehouse_type"=>"0=购买，1=借",
			"account_type"=>"0=已付款，1=借",
			"adjust_type"=>"调整类型0-扣减,1-增加",
			"send_goods_sn"=>"送货单号",
			"prc_id"=>" ",
			"prc_name"=>" ",
			"goods_num"=>"石包总数",
			"goods_zhong"=>"石包总重量",
			"goods_total"=>"石包总价值",
			"shijia"=>"采购支付金额",
			"make_order"=>" ",
			"addtime"=>" ",
			"check_order"=>" ",
			"checktime"=>" ",
			"fin_check"=>" ",
			"fin_check_time"=>" ",
			"info"=>" ",
			"times"=>"时间戳");
		parent::__construct($id,$strConn);
	}

	/**************************************************************************************************
	 *	pageList，分页列表
	 *
	 *	@url DiaOrderController/search
	 **************************************************************************************************/
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT a.order_id,a.prc_name, a.type, a.status, a.make_order, a.check_order, a.send_goods_sn, a.addtime, a.checktime,a.goods_zhong,a.goods_num,a.goods_total,'' as shibao FROM `".$this->table()."` as a ";
		$str = '';
		if(!empty($where['order_id']))
		{
			$order_id_end = substr($where['order_id'], 2);
			$str .= "  (a.order_id = '$order_id_end' or a.order_id = '{$where['order_id']}') AND ";
		}
		if(!empty($where['type']))
		{
			$str .= "  a.type='{$where['type']}' AND ";
		}
		if(!empty($where['status']))
		{
			$str .= "  a.status='{$where['status']}' AND ";
		}
 		if(!empty($where['send_goods_sn']))
		{
			$str .= "  a.send_goods_sn='{$where['send_goods_sn']}' AND ";
		}
 		if(!empty($where['make_order']))
		{
			$str .= "  a.make_order='{$where['make_order']}' AND ";
		}
 		if(!empty($where['prc_id']))
		{
			$str .= "  a.prc_id='{$where['prc_id']}' AND ";
		}
 		if(!empty($where['in_warehouse_type']))
		{
			$str .= "  a.in_warehouse_type='{$where['in_warehouse_type']}' AND ";
		}
 		if(!empty($where['account_type']))
		{
			$str .= "  a.account_type='{$where['account_type']}' AND ";
		}
		if($where['add_time_start'] != '')
		{
			$str .= "`a`.`addtime` >= '".$where['add_time_start']." 00:00:00' AND ";
		}
		if($where['add_time_end'] != '')
		{
			$str .= "`a`.`addtime` <= '".$where['add_time_end']." 23:59:59' AND ";
		}
		if($where['check_time_start'] != '')
		{
			$str .= "`a`.`checktime` >= '".$where['check_time_start']." 00:00:00' AND ";
		}
		if($where['check_time_end'] != '')
		{
			$str .= "`a`.`checktime` <= '".$where['check_time_end']." 23:59:59' AND ";
		}
	 	if(!empty($where['info']))
		{
			$str .= "  a.info like '%{$where['info']}%' AND ";
		}
	 	if(!empty($where['shibao']))
		{
			$str .= "  og.shibao='{$where['shibao']}' AND ";
		}
	 	if(!empty($where['zhengshuhao']))
		{
			$str .= "  og.zhengshuhao='{$where['zhengshuhao']}' AND ";
		}
		if ($where['shibao'] != '' || $where['zhengshuhao'] != '')
		{
			$str = "select a.order_id,a.prc_name, a.type, a.status, og.shibao, og.num as goods_num, og.zongzhong as goods_zhong,'' as goods_total, a.make_order, a.check_order, a.send_goods_sn, a.addtime, a.checktime from dia_order as a , dia_order_goods as og  where  a.order_id = og.order_id and ".$str;
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql = $str." order by a.addtime desc";
		}
		else
		{
			if($str)
			{
				$str = rtrim($str,"AND ");//这个空格很重要
				$sql .=" WHERE ".$str." order by a.addtime desc";
			}
			else
			{
				$sql .= " order by a.addtime desc";
			}
		}
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	/***************************************************************************************************
	fun:add_info
	description:添加单据信息

	***************************************************************************************************/
	public function add_info ($info,$data)
	{
		$pdo = $this->db()->db();//pdo对象
		try
		{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			if($info['shijia' ] == ''){$info['shijia' ] = 0;}
			#1、添加主表单据信息
			if(isset($info['pro_sn'])){
				$sql = "INSERT INTO `dia_order` (`type`,`status`,`order_time`,`in_warehouse_type`,`account_type`,`adjust_type`,`send_goods_sn`,`shijia`,`make_order`,`prc_id`,`prc_name`,`addtime`,`info`,`times`,`pro_sn`) VALUES ('{$info['type']}','{$info['status']}','{$info['order_time']}',{$info['in_warehouse_type']},'{$info['account_type']}','{$info['adjust_type']}','{$info['send_goods_sn']}','{$info['shijia']}','{$info['make_order']}','{$info['prc_id']}','{$info['prc_name']}','{$info['addtime']}','{$info['info']}','{$info['times']}','{$info['pro_sn']}') ";
			}else{
				$sql = "INSERT INTO `dia_order` (`type`,`status`,`order_time`,`in_warehouse_type`,`account_type`,`adjust_type`,`send_goods_sn`,`shijia`,`make_order`,`prc_id`,`prc_name`,`addtime`,`info`,`times`) VALUES ('{$info['type']}','{$info['status']}','{$info['order_time']}',{$info['in_warehouse_type']},'{$info['account_type']}','{$info['adjust_type']}','{$info['send_goods_sn']}','{$info['shijia']}','{$info['make_order']}','{$info['prc_id']}','{$info['prc_name']}','{$info['addtime']}','{$info['info']}','{$info['times']}') ";
			}
			$pdo->query($sql);
			$id = $pdo->lastInsertId();
			#2、买附表明细添加
			//var_dump($data);exit;
			foreach ($data as $value)
			{
				$sql = "insert into `dia_order_goods`(
					`order_id`,`order_type`,`shibao`,`zhengshuhao`,`zhong`,`yanse`,`jingdu`,`qiegong`,`duichen`,`paoguang`,`yingguang`,`num`,`zongzhong`,`caigouchengben`,`xiaoshouchengben`) values (
					{$id},'{$info['type']}','{$value['shibao']}',
					'{$value['zhengshuhao']}','{$value['zhong']}','{$value['yanse']}','{$value['jingdu']}',
					'{$value['qiegong']}','{$value['duichen']}','{$value['paoguang']}','{$value['yingguang']}',
					'{$value['num']}','{$value['zongzhong']}','{$value['caigouchengben']}','{$value['xiaoshouchengben']}')";
				$pdo->query($sql);
			}
			#3、计算单据总数量
			$sql = "select sum(`num`) as goods_num,  sum(`zongzhong`) as goods_zhong, sum(`caigouchengben` * `zongzhong`) as goods_total from dia_order_goods where order_id = '$id'";
			$ret = $this->db()->getRow($sql);
			#4、计算修改总数量、总重、总金额
			if($info['type']=='CK'){//如果是出库单则支付总价为价格总价
				$sql = "UPDATE `dia_order` SET `shijia`='" . $ret['goods_total'] . "' , `goods_num` = '" . $ret['goods_num'] . "',`goods_zhong` = '" . $ret['goods_zhong'] . "' ,`goods_total` = '" . $ret['goods_total'] . "' WHERE `order_id` ='" . $id . "' ";
			}
			else 
				$sql = "UPDATE `dia_order` SET  `goods_num` = '" . $ret['goods_num'] . "',`goods_zhong` = '" . $ret['goods_zhong'] . "' ,`goods_total` = '" . $ret['goods_total'] . "' WHERE `order_id` ='" . $id . "' ";
			$pdo->query($sql);
			//业务逻辑结束
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

	/************************************************************************************************
	fun:update_info
	description:编辑信息
	*************************************************************************************************/
	public function update_info ($info,$data)
	{
		$pdo = $this->db()->db();//pdo对象
		try
		{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

			#1、删除买石单已有的明细
			$sql = "delete from dia_order_goods where order_id = '{$info['order_id']}' ";
			$pdo->query($sql);
			#2、添加买石单明细信息
			foreach ($data as $value)
			{
				$sql = "insert into `dia_order_goods`(
					`order_id`,`order_type`,`shibao`,`zhengshuhao`,`zhong`,`yanse`,`jingdu`,`qiegong`,`duichen`,`paoguang`,`yingguang`,`num`,`zongzhong`,`caigouchengben`,`xiaoshouchengben`) values (
					'{$info['order_id']}','{$info['type']}','{$value['shibao']}',
					'{$value['zhengshuhao']}','{$value['zhong']}','{$value['yanse']}','{$value['jingdu']}',
					'{$value['qiegong']}','{$value['duichen']}','{$value['paoguang']}','{$value['yingguang']}',
					'{$value['num']}','{$value['zongzhong']}','{$value['caigouchengben']}','{$value['xiaoshouchengben']}')";
				$pdo->query($sql);
			}
			#3、修改买石单主表信息
			$sql = "select sum(`num`) as goods_num,  sum(`zongzhong`) as goods_zhong, sum(`caigouchengben` * `zongzhong`) as goods_total from dia_order_goods where order_id = '{$info['order_id']}'";
			$ret = $this->db()->getRow($sql);
			//var_dump($ret);exit;
			$sql = "UPDATE `dia_order` SET `goods_num` = '{$ret['goods_num']}',`goods_zhong` = '{$ret['goods_zhong']}',`goods_total` = '{$ret['goods_total']}',`in_warehouse_type`='{$info['in_warehouse_type']}',`account_type`='{$info['account_type']}',`send_goods_sn`='{$info['send_goods_sn']}',`shijia`='{$info['shijia']}',`prc_id`='{$info['prc_id']}',`prc_name`='{$info['prc_name']}',`order_time`='{$info['order_time']}',`info`='{$info['info']}' WHERE `order_id` ='" . $info['order_id'] . "' ";
			$pdo->query($sql);
		}
		catch(Exception $e)
		{
			//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;
	}
	/*************************************************************************************************
	fun:check_info_ms_rk
	description:审核买石单、其他入库单
	**************************************************************************************************/
	public function check_info_ms_rk($id,$data,$type,$order_data)
	{
		$pdo = $this->db()->db();//pdo对象
		try
		{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

			#1、如果石包存在则--根据石包号更新每卡采购价、最新销售价
			#2、如果石包存在则--更新库存数量；
			#3、如果石包不存在则新增
			#4、修改单据状态
			foreach ($data as $goods)
			{
				$shibao = $goods['shibao'];
				$num = $goods['num'];
				$zongzhong = $goods['zongzhong'];
				$caigouchengben = $goods['caigouchengben'];
				$xiaoshouchengben = $goods['xiaoshouchengben'];
				$sql1 = "select count(1) from dia where shibao='{$goods['shibao']}'";
				//石包存在
				if ($this->db()->getOne($sql1)>0)
				{
					$sql2 = " select kucun_zhong,caigouchengben,xiaoshouchengben from dia where shibao='".$goods['shibao']."' and kucun_cnt > 0 ";
					$row = $this->db()->getRow($sql2);
					$kc_total = 0;
					$kc_xiaoshou = $kc_weight= 0;
					if($row)
					{
						$kc_total = $row['kucun_zhong'] * $row['caigouchengben'];
						$kc_weight= $row['kucun_zhong'];
		 //BOSS-754 石包管理-其他入库单的每克拉销售成本算法修复 20151113 by lyy
						$kc_xiaoshou = $row['kucun_zhong'] * $row['xiaoshouchengben'];
					}
					/* 买石单入库成本总价 */
					$total_price = $goods['zongzhong'] * $goods['caigouchengben'] + $kc_total;
					$total_weight= $goods['zongzhong'] + $kc_weight;
					
					// add by lyy 20111113
					$sales_price_total =  $goods['zongzhong'] * $goods['xiaoshouchengben']+$kc_xiaoshou;

					/* 评价成本 */
					$av_price = round($total_price/$total_weight, 2);
					
					// add by lyy 20111113
					$new_sales_price = round($sales_price_total/$total_weight, 2);

					/* 更新库存现有石包采购成本  如果该石包号已经被删除则恢复正常状态 */
					$sql3 = " update dia set status='1',caigouchengben='".$av_price."',xiaoshouchengben='".$new_sales_price."' where shibao='".$goods['shibao']."' limit 1 ";
					$pdo->query($sql3);
					//更新库存
					$opt_cnt = $type . '_cnt';
					$opt_zhong = $type . '_zhong';

					$pdo->query("update dia set `$opt_cnt` = `$opt_cnt` + $num , `$opt_zhong` = `$opt_zhong` + $zongzhong where shibao = '{$goods['shibao']}'");

					$pdo->query("update dia set `kucun_cnt` =  `MS_cnt` + `fenbaoru_cnt` - `SS_cnt` - `fenbaochu_cnt` + `TS_cnt` - `YS_cnt` - `SY_cnt` - `TH_cnt` + `RK_cnt` - `CK_cnt`, `kucun_zhong` =  `MS_zhong` + `fenbaoru_zhong` - `SS_zhong` - `fenbaochu_zhong` + `TS_zhong` - `YS_zhong` - `SY_zhong` - `TH_zhong`+ `RK_zhong` - `CK_zhong` where shibao = '{$goods['shibao']}'");
				}
				//石包不存在
				else
				{
					$addtime = date("Y-m-d H:i:s");
					if ($type=="MS")
					{
						$pdo->query("INSERT INTO `dia` (`shibao`, `addtime`,`kucun_cnt`, `MS_cnt`,`kucun_zhong`, `MS_zhong`, `caigouchengben`, `xiaoshouchengben`,`yuanshicaigouchengben` ) VALUES ('{$goods['shibao']}', '{$addtime}', '{$goods['num']}','{$goods['num']}', '{$goods['zongzhong']}', '{$goods['zongzhong']}', '{$goods['caigouchengben']}','{$goods['xiaoshouchengben']}','{$goods['caigouchengben']}')");
					}
					else if($type == "RK")
					{
						$pdo->query("INSERT INTO `dia` (`shibao`, `addtime`,`kucun_cnt`, `RK_cnt`,`kucun_zhong`, `RK_zhong`, `caigouchengben`, `xiaoshouchengben`,`yuanshicaigouchengben`  )VALUES ('{$goods['shibao']}', '{$addtime}', '{$goods['num']}','{$goods['num']}', '{$goods['zongzhong']}', '{$goods['zongzhong']}', '{$goods['caigouchengben']}','{$goods['xiaoshouchengben']}','{$goods['caigouchengben']}')");
					}
				}
			}
			$sql4 = "UPDATE `dia_order` SET `status` = '2', `check_order` = '" . $_SESSION['userName'] . "', `checktime` = '" . date("Y-m-d H:i:s") . "', fin_status = '1',fin_check_time = '".date("Y-m-d H:i:s")."',fin_check = 'SYSTEM系统自动' WHERE `order_id` = '$id' LIMIT 1";
			$pdo->query($sql4);
			#5、向财务应付推送数据
			if ($type == 'MS')
			{
				$fin_data = array(
					'item_id'	=> $type.$id,
					'order_id'	=> 0,
					'zhengshuhao' => '',
					'goods_status' => 1,
					'item_type'	=> '1',//1买石单
					'company'=> 58,
					'prc_id'	=> $order_data['prc_id'],
					'prc_name'	=> $order_data['prc_name'],
					'prc_num'	=> $order_data['send_goods_sn'],
					'type'=>3,//石包
					'pay_content' => '',//空
					'storage_mode'=> $order_data['in_warehouse_type'],//购买方式
					'make_time'	=> $order_data['addtime'],
					'check_time'=> date("Y-m-d H:i:s"), //
					'total'		=> $order_data['shijia'] //'支付'
					);
				$ret=ApiModel::fin_api('AddAppPayDetail',array('insert_data' => array($fin_data)));
				if($ret['error'])
				{
					//捕获异常
					$pdo->rollback();//事务回滚
					$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
					return false;
				}
			}
		}
		catch(Exception $e)
		{
			//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;
	}

	/**************************************************************************************************
	fun:check_info_other
	description:审核单据(除去买石单、其他入库单以外的所有单据)
	***************************************************************************************************/
	public function check_info_other($id,$data,$type,$order_data)
	{
		$pdo = $this->db()->db();//pdo对象
		try
		{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

			foreach ($data as $goods)
			{
				$shibao = $goods['shibao'];
				$num = $goods['num'];
				$zongzhong = $goods['zongzhong'];
				$caigouchengben = $goods['caigouchengben'];
				$xiaoshouchengben = $goods['xiaoshouchengben'];
				if($type == 'AS')
				{
					#1、调整单----（还石数量-调整数量）
					$sql = " update `dia` set HS_cnt=HS_cnt-".$num.", HS_zhong=HS_zhong-".$zongzhong." where shibao='$shibao' limit 1 ";
					$pdo->query($sql);
				}
				else
				{
					if($type == "TH" || $type == "CK")
					{
						$sql2 = " select kucun_zhong,caigouchengben from dia where shibao='".$goods['shibao']."' and kucun_cnt > 0 ";
						$row = $this->db()->getRow($sql2);
						$kc_total = 0;
						$kc_weight= 0;
						if($row)
						{
							$kc_total = $row['kucun_zhong'] * $row['caigouchengben'];
							$kc_weight= $row['kucun_zhong'];
						}

						/* 买石单入库成本总价 */
						$total_price = $kc_total - $goods['zongzhong'] * $goods['caigouchengben'];
						$total_weight= $kc_weight - $goods['zongzhong'];

						/* 评价成本 */
						$av_price = round($total_price/$total_weight, 2);
						/* 更新库存现有石包采购成本 */
						if($av_price > 0)
						{
							$sql3 = " update dia set caigouchengben='".$av_price."' where shibao='".$goods['shibao']."' limit 1 ";
							$pdo->query($sql3);
						}
					}
					//opt_shibao($shibao,$type,$num,$zongzhong);
					#除买石单、其他入库、调整单以外；修改对应单据的数量
					$opt_cnt = $type . '_cnt';
					$opt_zhong = $type . '_zhong';
					$pdo->query("update dia set `$opt_cnt` = `$opt_cnt` + $num , `$opt_zhong` = `$opt_zhong` + $zongzhong where shibao = '$shibao'");
					$pdo->query("update dia set `kucun_cnt` =  `MS_cnt` + `fenbaoru_cnt` - `SS_cnt` - `fenbaochu_cnt` + `TS_cnt` - `YS_cnt` - `SY_cnt` - `TH_cnt` + `RK_cnt` - `CK_cnt`, `kucun_zhong` =  `MS_zhong` + `fenbaoru_zhong` - `SS_zhong` - `fenbaochu_zhong` + `TS_zhong` - `YS_zhong` - `SY_zhong` - `TH_zhong`+ `RK_zhong` - `CK_zhong` where shibao = '$shibao'");
				}
			}
			$sql4 = "UPDATE `dia_order` SET `status` = '2', `check_order` = '" . $_SESSION['userName'] . "', `checktime` = '" . date("Y-m-d H:i:s") . "', fin_status = '1',fin_check_time = '".date("Y-m-d H:i:s")."',fin_check = 'SYSTEM系统自动' WHERE `order_id` = '$id' LIMIT 1";
			$pdo->query($sql4);
			#5、向财务应付推送数据
			if ($type == 'TH')
			{
				$fin_data = array(
					'item_id'	=> $type.$id,
					'order_id'	=> 0,
					'zhengshuhao' => '',
					'goods_status' => 1,
					'item_type'	=> '2',//1退货单
					'company'=> 58,
					'prc_id'	=> $order_data['prc_id'],
					'prc_name'	=> $order_data['prc_name'],
					'prc_num'	=> $order_data['send_goods_sn'],
					'type'=>3,//石包
					'pay_content' => '',//空
					'storage_mode'=> $order_data['in_warehouse_type'],//购买方式
					'make_time'	=> $order_data['addtime'],
					'check_time'=> date("Y-m-d H:i:s"), //
					'total'		=> $order_data['goods_total'] //'支付'
					);
				$ret=ApiModel::fin_api('AddAppPayDetail',array('insert_data' => array($fin_data)));
				if($ret['error'])
				{
					//捕获异常
					$pdo->rollback();//事务回滚
					$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
					return false;
				}
			}
		}
		catch(Exception $e)
		{
			//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;
	}
	/**************************************************************************************************
	fun:cancle_info
	description:取消单据
	***************************************************************************************************/
	public function cancle_info($id)
	{
		$sql = "UPDATE `dia_order` SET `status` = '3', `check_order` = '" . $_SESSION['userName'] . "', `checktime` = '" . date("Y-m-d H:i:s") . "' WHERE `order_id` = '$id' LIMIT 1";
		return  $this->db()->query($sql);
	}
	/**********************************************************************************************************
	fun:getInfoByOrderId
	description:根据id获取信息
    *************************************************************************************************************/
	public function getInfoByOrderId($order_id)
	{
		$sql = "select * from dia_order where order_id={$order_id}";
		return $this->db()->getRow($sql);
	}

	/*************************************************************************************************************
	fun:checkShibaoInfo
	description:重新核算石包信息 2015/3/20 星期五
	**************************************************************************************************************/
	public function  checkShibaoInfo($shibao,$danju)
	{
		$sql = "select do.type,sum(num) as cnt, sum(zongzhong) as zhong from dia_order_goods as dog, dia_order as do where do.order_id = dog.order_id and dog.shibao = '$shibao' and do.status = 2 group by do.type";
		$ret = $this->db()->getAll($sql);
		unset($danju['fenbaoru']);
		unset($danju['fenbaochu']);
		unset($danju['AS']);

		$update_where = '';
		foreach ($danju as $dj => $value)
		{
			$update_where .= $dj . '_cnt = 0 ,';
			$update_where .= $dj . '_zhong = 0 ,';
		}
		//var_dump($update_where);exit;
		$sql = "update dia set $update_where kucun_cnt = 0 where shibao = '$shibao' limit 1";
		$this->db()->query($sql);
		// 调整信息
		$as_info = array("cnt"=>0, "zhong"=>0);

		foreach($ret as $r)
		{
			if($r['type'] == 'AS')
			{
				$as_info['cnt']	= $r['cnt'];
				$as_info['zhong']=$r['zhong'];
				continue;
			}
			$cnt_word = $r['type'] . '_cnt';
			$zhong_word = $r['type'] . '_zhong';
			$cnt = $r['cnt'];
			$zhong = $r['zhong'];
			$s = "update dia set `$cnt_word` = '$cnt', `$zhong_word` = '$zhong' where shibao = '$shibao' limit 1";
			$this->db()->query($s);
		}
		$this->db()->query("update dia set `kucun_cnt` =  `MS_cnt` + `fenbaoru_cnt` - `SS_cnt` - `fenbaochu_cnt` + `TS_cnt` - `YS_cnt` - `SY_cnt` - `TH_cnt`+ `RK_cnt` - `CK_cnt`, `kucun_zhong` =  `MS_zhong` + `fenbaoru_zhong` - `SS_zhong` - `fenbaochu_zhong` + `TS_zhong` - `YS_zhong` - `SY_zhong` - `TH_zhong`+ `RK_zhong` - `CK_zhong`   where shibao = '$shibao' limit 1");

		// 更新调整石包信息
		$this->db()->query(" update dia set HS_cnt=HS_cnt-".$as_info['cnt'].", HS_zhong=HS_zhong-".$as_info['zhong']." where shibao='$shibao' limit 1 ");
		return true;
	}

	/**
	 * 校验买石包采购单
	 */
	public function checkMSpro($pro_sn){
		$select = ['pro_sn','pro_type','pro_ct','pro_total','is_batch','check_status',];
		$sql = "SELECT ".implode(',',$select)." FROM `stone_procure` WHERE `pro_sn` = '".$pro_sn."'";
		$data = $this->db()->getRow($sql);
		return $data;
	}

	public function hasProSN($pro_sn){
		$sql = "SELECT count(*) FROM `dia_order` WHERE `pro_sn` = '".$pro_sn."'";
		return $this->db()->getOne($sql);
	}
	//获取已采购的总重量、总金额
	public function getSumByProSN($pro_sn){
		$sql = "SELECT sum(goods_zhong) AS weight,sum(goods_total) AS all_total FROM `dia_order` WHERE `pro_sn` = '".$pro_sn."'";
		return $this->db()->getRow($sql);
	}
	//获取采购单总重、总金额
	public function getAltProinfo($pro_sn){
		$sql = "SELECT `pro_ct` AS weight,`pro_total` AS all_total FROM `stone_procure` WHERE `pro_sn` = '".$pro_sn."'";
		return $this->db()->getRow($sql);
	}

	public function verify($n,$min,$max){
		return ($n>$min && $n <$max)?true:false;
	}

	public function checkProRules($now_w,$now_t,$all_w,$all_t){
		//总量误差
		if($all_w <= 30){
			$poor = $all_w * 0.00015;
		}
		if($all_w >30 &&  $all_w <=100){
			$poor = $all_w * 0.0002;
		}
		if($all_w >100){
			$poor = $all_w * 0.00025;
		}
		$w = $this->verify($now_w,($all_w-$poor),($all_w+$poor));
		if(!$w){return 'w_error';}
		$t = $this->verify($now_t,($all_t-10),($all_t+10));
		if(!$t){return 't_error';}
		return true;
	}

	public function checkProRules2($now_w,$now_t,$all_w,$all_t){
		if($all_w <= 30){
			$poor = $all_w * 1.00015;
		}
		if($all_w >30 &&  $all_w <=100){
			$poor = $all_w * 1.0002;
		}
		if($all_w >100){
			$poor = $all_w * 1.00025;
		}
		if($now_w>$poor){return 'w_error';}
		if($now_t>($all_t+10)){return 't_error';}
		return true;
	}

	public function setBatch($is_batch,$pro_sn){
		$sql = "UPDATE `stone_procure` SET `is_batch`='".$is_batch."' WHERE `pro_sn` = '".$pro_sn."'";
		return $this->db()->query($sql);
	}


}

?>