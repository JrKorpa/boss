<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoCModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-18 21:54:49
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoCModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = '';
        $this->_dataObject = array("id"=>"id",
			"bill_id"=>"单据id",
			"pro_id"=>"加工商id",
			"chuku_type"=>"出库类型"
		);
		parent::__construct($id,$strConn);
	}
	/*********************************
	function: add_info_c
	description:其他出库单添加
	para:data 二维数组 e_info
	return: true
	*********************************/

	public function add_info_c ($data,$info)
	{
		$gwModel = new GoodsWarehouseModel(21);

		$billModel  = new WarehouseBillModel(21);
		//记录货品下架出入库记录
		$boxGoodsLogModel = new BoxGoodsLogModel(22);
		$boxGoodsLogModel->addLog(array_column($data,0));
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

			//添加其他出库单

			$sql = "INSERT INTO `warehouse_bill` (`bill_no`,`from_company_id`,`goods_num`,`goods_total`,`shijia`,`bill_note`,`create_user`,`create_time`,`bill_type`,`to_warehouse_name`,`to_company_name`,`from_company_name`,`order_sn`,`pro_id`,`pro_name`,`tuihuoyuanyin`) VALUES ('','{$info['from_company_id']}','{$info['goods_num']}',{$info['goods_total']},'{$info['shijia']}','{$info['bill_note']}','{$info['create_user']}','{$info['create_time']}','{$info['bill_type']}','','','{$info['from_company_name']}','{$info['order_sn']}','{$info['pro_id']}','{$info['pro_name']}','{$info['chuku_type']}') ";

			$pdo->query($sql);
		//	echo $sql;exit;
			$id = $pdo->lastInsertId();

			$bill_no = $billModel->create_bill_no($info['bill_type'],$id);
			$sql = "UPDATE `warehouse_bill` SET `bill_no`='{$bill_no}' WHERE `id`={$id}";
			//echo $sql;echo "<br>";
			$pdo->query($sql);

			/*  删除附表

			//添加其他出库单其他字段
			$sql ="INSERT INTO `warehouse_bill_info_c` (`bill_id`,`pro_id`,`pro_name`,`chuku_type`) VALUES ({$id},'{$info['pro_id']}','{$info['pro_name']}','{$info['chuku_type']}')";
		//	echo $sql;echo "<br>";exit;
			$pdo->query($sql);
			//echo 888;exit;
			*/
			//其他出库单明细添加
			$str_goods_id = "";
			foreach ($data as $key=>$value)
			{
				 $yuanshichengbenjia=0;
				 if(SYS_SCOPE=='boss'){
				 	$yuanshichengbenjia=$value[9];
                    $shijia=$value[10];
                    $jinzhong=$value[11];
                 }   
				 if(SYS_SCOPE=='zhanting'){
				 	$goods_row=$this->getGoodsC($value[0]);
                    $yuanshichengbenjia= !empty($goods_row) ? $goods_row['yuanshichengbenjia'] : 0;
                    $shijia=$value[9];
                    $jinzhong=$value[10];
                 }
				 $sql = "INSERT INTO `warehouse_bill_goods`(
				`bill_id`,`bill_no`,`bill_type`,
				`goods_id`,`goods_name`,`goods_sn`,`in_warehouse_type`,`jingdu`,`zhengshuhao`,
				`yanse`,`zuanshidaxiao`,`sale_price`,`shijia`,`jinzhong`,
				`addtime`) VALUES (
				{$id},'{$bill_no}','C',
				'{$value[0]}','{$value[1]}','{$value[2]}', '{$value[3]}','{$value[4]}','{$value[5]}',
				'{$value[7]}','{$value[8]}','{$yuanshichengbenjia}','{$shijia}','{$jinzhong}','{$info['create_time']}')";
                                        //exit($sql);
                                       // echo $sql;echo "<br>";exit;
				$pdo->query($sql);
				if ($key)
				{
					$str_goods_id  .= ",'".$value[0]."'";
				}
				else
				{
					$str_goods_id  .= "'".$value[0]."'";
				}
				//货品下架
				$default_box = $gwModel->getDefaultBoxIdBygoods($value[0]);	//获取货品所在仓库的默认柜位ID
				if(!$default_box){
					// return array('success' => 0 , 'error'=> "货号：{$value[0]} 不存在相应柜位信息,下架失败！导致制单失败");
					//如果不存在默认柜位，自动创建一条货品的默认柜位信息
					$res = $billModel->CheckAndCreateBox($value[0]);
					if($res['success'] != 1){
						return array('success' => 0 , 'error' => $res['error']);
						$pdo->query('');
					}
					$default_box = $res['box_id'];
				}
				$sql = "UPDATE `goods_warehouse` SET `box_id` = {$default_box} WHERE `good_id` = '{$value[0]}'";
				$pdo->query($sql);
			}
			//货品状态改变
			$sql = "UPDATE `warehouse_goods` SET `is_on_sale`=8,`box_sn` = '0-00-0-0' WHERE `goods_id` in (".$str_goods_id.") and `is_on_sale`=2";
			$num = $pdo->exec($sql);
            if($num != count(explode(',',$str_goods_id))){
                $pdo->rollback();//事务回滚
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                return array('success' => 0 , 'error' =>'操作失败，可能货品不是库存状态');
            }
			//修改可销售商品为下架
			$model = new ApiSalepolicyModel();
			$str_goods_id = str_replace("'","",$str_goods_id);
			$str_goods_id = explode(",",$str_goods_id);
			if(!$model->EditIsSaleStatus($str_goods_id,0,5))
			{
				//修改可销售商品为下架 失败制造错误回滚。
				$pdo->query('');
			}

			//写入日志
			$time = date('Y-m-d H:i:s');
			$user = $_SESSION['userName'];
			$ip = Util::getClicentIp();
			$sql = "INSERT INTO `warehouse_bill_status`(`bill_id`,`bill_no`,`status`,`update_time`,`update_user`,`update_ip`) VALUES ({$id},'{$bill_no}',1,'{$time}','{$user}','{$ip}')";
            $pdo->query($sql);
			//业务逻辑结束
            $pdo->commit();//如果没有异常，就提交事务
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            return array('success' => 1 , 'x_id' => $id, 'label'=>$bill_no);
		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 0 , 'error' =>'事物操作不成功，导致添加其他出库单失败');
		}
		
	}
	/*********************************
	function: update_info_c
	description:其他出库单添加
	para:data 二维数组 e_info
	return: true
	*********************************/

	public function update_info_c ($data,$info,$del_goods_id)
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
			//将删除的数据货品状态还原库存状态
			$sql = "UPDATE `warehouse_goods` SET `is_on_sale`=2 WHERE `goods_id` in (".$del_goods_id.")";
			$pdo->query($sql);

			//获取旧明细数据的货品货号，然后修改其货品仓储状态
			$sql = "SELECT `goods_id` FROM `warehouse_bill_goods` WHERE `bill_id` = {$info['id']}";
			$warehousegoods = $this->db()->getAll($sql);
			$old_goods_id = '';
			foreach ($warehousegoods as $key => $value) {
				$old_goods_id .= ",'{$value['goods_id']}'";
			}
			$old_goods_id = ltrim($old_goods_id, ',');
			$sql = "UPDATE `warehouse_goods` SET `is_on_sale` =2 WHERE `goods_id` IN ($old_goods_id)";
			$pdo->query($sql);

			//删除旧数据
			$sql = "DELETE FROM `warehouse_bill_goods` WHERE `bill_id` = {$info['id']}";
			$pdo->query($sql);

			//修改其他出库单信息主表
            $info['bill_note']= addslashes($info['bill_note']) ;
			$sql = "UPDATE `warehouse_bill` SET `from_company_id` = {$info['from_company_id']},`from_company_name`='{$info['from_company_name']}',`goods_num`={$info['goods_num']},`goods_total`={$info['goods_total']},`shijia`={$info['shijia']},`order_sn`='{$info['order_sn']}',`bill_note`='{$info['bill_note']}',`pro_id` = {$info['pro_id']},`pro_name` = '{$info['pro_name']}',`tuihuoyuanyin`={$info['chuku_type']} WHERE `id`={$info['id']}";

			$pdo->query($sql);
			/* 删掉附表
			//修改其他出库单附表
			$sql ="UPDATE `warehouse_bill_info_c` SET `pro_id` = {$info['pro_id']},`pro_name` = '{$info['pro_name']}',`chuku_type`={$info['chuku_type']} WHERE `bill_id`={$info['id']}";
			$pdo->query($sql);
			*/
			$str_goods_id = "";
			foreach ($data as $key=>$value)
			{
				 $yuanshichengbenjia=0;
				 if(SYS_SCOPE=='boss'){
				 	$yuanshichengbenjia=$value[9];
                    $shijia=$value[10];
                    $jinzhong=$value[11];
                 }   
				 if(SYS_SCOPE=='zhanting'){
				 	$goods_row=$this->getGoodsC($value[0]);
                    $yuanshichengbenjia= !empty($goods_row) ? $goods_row['yuanshichengbenjia'] : 0;
                    $shijia=$value[9];
                    $jinzhong=$value[10];
                 }

                    $sql = "INSERT INTO `warehouse_bill_goods`(
					`bill_id`,`bill_no`,`bill_type`,
					`goods_id`,`goods_name`,`goods_sn`,`in_warehouse_type`,`jingdu`,`zhengshuhao`,
                                        `yanse`,`zuanshidaxiao`,`sale_price`,`shijia`,`jinzhong`,
					`addtime`) VALUES (
					{$info['id']},'{$info['bill_no']}','C',
					'{$value[0]}','{$value[1]}','{$value[2]}',
					'{$value[3]}','{$value[4]}','{$value[5]}',
					'{$value[7]}','{$value[8]}','{$yuanshichengbenjia}','{$shijia}','{$jinzhong}','{$info['create_time']}')";
					$pdo->query($sql);
				if ($key)
				{
					$str_goods_id  .= ",'".$value[0]."'";
				}
				else
				{
					$str_goods_id  .= "'".$value[0]."'";
				}
				//货品下架
				$default_box = $gwModel->getDefaultBoxIdBygoods($value[0]);	//获取货品所在仓库的默认柜位ID
				if(!$default_box){
					// return array('success' => 0 , 'error'=> "货号：{$value[0]} 不存在相应柜位信息,下架失败！导致制单失败");
					//如果不存在默认柜位，自动创建一条货品的默认柜位信息
					$res = $billModel->CheckAndCreateBox($value[0]);
					if($res['success'] != 1){
						return array('success' => 0 , 'error' => $res['error']);
						$pdo->query('');
					}
					$default_box = $res['box_id'];
				}
				$sql = "UPDATE `goods_warehouse` SET `box_id` = {$default_box} WHERE `good_id` = '{$value[0]}'";
				$pdo->query($sql);
			}
			//货品状态改变
			$sql = "UPDATE `warehouse_goods` SET `is_on_sale`=8, `box_sn` = '0-00-0-0' WHERE `goods_id` in (".$str_goods_id.") and `is_on_sale`=2";
			$num = $pdo->exec($sql);
            if($num != count(explode(",",$str_goods_id))){
                $pdo->rollback();//事务回滚
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                return array('success' => 0 , 'error' =>'操作失败，可能货品不是库存状态');
            }
			//写入日志
			$time = date('Y-m-d H:i:s');
			$user = $_SESSION['userName'];
			$ip = Util::getClicentIp();
			$sql = "INSERT INTO `warehouse_bill_status`(`bill_id`,`bill_no`,`status`,`update_time`,`update_user`,`update_ip`) VALUES ({$info['id']},'{$info['bill_no']}',1,'{$time}','{$user}','{$ip}')";
            $pdo->query($sql);
			//业务逻辑结束
            $pdo->commit();//如果没有异常，就提交事务
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            return array('success' => 1 , 'error' =>'制单成功');
		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 0 , 'error' =>'事物操作不成功，导致编辑其它出库单失败');
		}
		
	}

	
	/***
	function:select
	description:查询数据根据单号
	***/
	/**  删除附表
	public function select($id)
	{
		$sql = "select pro_id,chuku_type from warehouse_bill_info_c where bill_id = {$id}";
		return $this->db()->getRow($sql);
	}
	***/
	/***
	查询其他出库单明细
	***/
	public function get_data_c($id)
	{
         	$sql = "SELECT  `a`.*, `b`.`zhushijingdu`,`b`.`zhushilishu`,`b`.`fushizhong`,`b`.`fushilishu` FROM `warehouse_bill_goods` AS `a` LEFT JOIN `warehouse_goods` AS `b` ON `a`.`goods_id` = `b`.`goods_id` WHERE `a`.`bill_id` = {$id}";
		return $this->db()->getAll($sql);
	}
		/***
	fun：check_info_e
	审核其它出库单
	***/
	public function check_info_c($id,$str_id)
	{
		$time = date('Y-m-d H:i:s');
		$user = $_SESSION['userName'];
		$billModel = new WarehouseBillModel($id, 21);
		$bill_no = $billModel->getValue('bill_no');
		$pdo = $this->db()->db();//pdo对象
		/*
		$sql = "SELECT `bill_no`,`bill_type`,`pro_id`,`pro_name`,`bill_status`,`bill_note`,`to_warehouse_id`,`chuku_type`,`create_time`,`check_time`,`chengbenjia`,`order_sn`  FROM `warehouse_bill` AS b  WHERE `b`.`id`='".$id."'";
		$bill_info = $this->db()->getRow($sql);
		//var_dump($bill_info);exit;
		*/
		try
		{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			// 1、货品改变状态 (9:已返厂)      2、单据审核
			$sql = "UPDATE `warehouse_bill` SET `bill_status`=2, `check_user`='{$user}',`check_time`='{$time}' WHERE `id` ={$id}";
			$pdo->query($sql);
			$sql = "UPDATE `warehouse_goods` SET `is_on_sale`=9 WHERE `goods_id` IN (".$str_id.") and `is_on_sale`=8";
			$num = $pdo->exec($sql);
			if($num != count(explode(",",$str_id))){
			    return false;
			}
            #记录出库时间，写入库龄表 warehouse_goods_age；
            //1、货品已出库，状态为已返厂，记录出库时间endtime（货品库龄已结束，之后不再统计）；
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
			//写入日志
			$ip = Util::getClicentIp();
			$sql = "INSERT INTO `warehouse_bill_status`(`bill_id`,`bill_no`,`status`,`update_time`,`update_user`,`update_ip`) VALUES ({$id},'{$bill_no}',2,'{$time}','{$user}','{$ip}')";
			$pdo->query($sql);
			$pdo->commit();//如果没有异常，就提交事务
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		

		/**
		* 获取单据下的商品明细 *
		*/
		/*
	   $sql = "SELECT bg.goods_name,bg.`goods_id`,g.product_type,g.cat_type,bg.chengbenjia,bg.goods_sn FROM `warehouse_bill_goods` AS `bg` LEFT JOIN `warehouse_goods` AS `g` ON `bg`.`goods_id` = `g`.`goods_id` WHERE `bill_id`={$id}";
	   $bill_goods = $this->db()->getAll($sql);
	   */

		//成品采购明细的数据推送
		$data = array();
		if($billModel->getValue('tuihuoyuanyin') == 2){ //只有出库类型说委托加工的推送
			$arr = array(
				'item_id'	=> $billModel->getValue('bill_no'),
				'order_id'	=> 0,
				'zhengshuhao' => '',
				'goods_status' => 2,
				'item_type'	=> 3,
				'company'   => 58,
				'prc_id'	=> $billModel->getValue('pro_id'),
				'prc_name'	=> $billModel->getValue('pro_name'),
				'prc_num'	=> $billModel->getValue('order_sn'),
				'type'      => 2,//成品
				'pay_content' => '',
				'storage_mode'=> $billModel->getValue('tuihuoyuanyin'),
				'make_time'	=> $billModel->getValue('create_time'),
				'check_time'=> $time,
				'total'	=> $billModel->getValue('goods_total')
			);
			$data[] = $arr;
		}
		//var_dump($data);exit;
		$apimodel = new ApiFinanceModel();
		$putres=$apimodel->AddAppPayDetail($data);
		if($putres)
		{
			$filename = date('Y_m_d').'_error_log.txt';
			Util::rmkdir(APP_ROOT.'warehouse/logs/api_logs/');
			file_put_contents(APP_ROOT.'warehouse/logs/api_logs/'.$filename,'单号为'.$bill_no.'失败接口为fin_api时间为'.$time=date('H:i:s').PHP_EOL,FILE_APPEND);
			file_put_contents('d:/aa.txt','单号为'.$bill_no.'失败接口为fin_api时间为'.$time=date('H:i:s').PHP_EOL,FILE_APPEND);
		}
		return true;
	}
	/***
	fun：cancel_info_c
	取消单据
	***/
	public function cancel_info_c($id,$str_id)
	{
		$time = date('Y-m-d H:i:s');
		$user = $_SESSION['userName'];
		$billModel = new WarehouseBillModel($id, 21);
		$bill_no = $billModel->getValue('bill_no');
		$pdo = $this->db()->db();//pdo对象
		try
		{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			// )     1、单据取消 2、货品改变状态 (库存状态
			$sql = "UPDATE `warehouse_bill` SET `bill_status`=3 WHERE `id` ={$id}";
			$pdo->query($sql);
			$sql = "UPDATE `warehouse_goods` SET `is_on_sale`=2 WHERE `goods_id` IN (".$str_id.") and `is_on_sale`=8";
			$pdo->query($sql);
			//写入日志
			$ip = Util::getClicentIp();
			$sql = "INSERT INTO `warehouse_bill_status`(`bill_id`,`bill_no`,`status`,`update_time`,`update_user`,`update_ip`) VALUES ({$id},'{$bill_no}',3,'{$time}','{$user}','{$ip}')";
			$pdo->query($sql);
			//修改可销售商品为上架
			$model = new ApiSalepolicyModel();
			$str_id = str_replace("'","",$str_id);
			$str_id = explode(",",$str_id);
			if(!$model->EditIsSaleStatus($str_id,1,1))
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

	/** 根据bill_id 获取加工商ID (warehouse_bill_info_c) **/
	public function getInfoByBillId($bill_id){
		$sql ="SELECT `pro_id`,`pro_name`,`chuku_type` FROM `warehouse_bill_info_c` WHERE `bill_id`={$bill_id}";
		return $this->db()->getRow($sql);
	}

	/** 根据货号 获取货品信息 **/
	public function getGoodsInfoByGoodsID($goods_id){
		$sql = "SELECT `goods_id`, `goods_sn`,`company_id` , `goods_name`, `num`, `caizhi`, `jinzhong`, `yanse`, `zuanshidaxiao`, `fushizhong`,`chengbenjia`, `is_on_sale`,`zhushilishu`,`fushilishu`,`zhushijingdu`,`put_in_type`,`zhengshuhao`, `order_goods_id` FROM `warehouse_goods` WHERE `goods_id` = '{$goods_id}' and company_id in ('58', '375', '376', '377')";
		return $this->db()->getRow($sql);
	}

	/** 根据货号，检测是否是当前单据的明细 **/
	public function checkDetail($goods_id , $bill_id){
		$sql = "SELECT `id` FROM `warehouse_bill_goods` WHERE `goods_id` = '{$goods_id}' AND `bill_id` = {$bill_id}";
		return $this->db()->getOne($sql);
	}

	/**打印详情  根据货号  $bill_id，获取货品详细属性 **/
	public function getDetail($bill_id){
		$sql = "SELECT a.`goods_id`,a.`goods_sn`,a.`jinzhong`,b.`zhushilishu`,a.`zuanshidaxiao`,a.`chengbenjia`,a.`yanse`,a.`jingdu`,a.`zhengshuhao`,a.`goods_name`,b.`fushilishu`,b.`fushizhong`,b.`shi2lishu`,b.`shi2zhong`,b.`shoucun`,b.`xianzaixiaoshou`,b.`mo_sn`,b.`buchan_sn`,a.`xiaoshoujia`,a.`account`,a.`in_warehouse_type`,b.warehouse_id,b.warehouse FROM `warehouse_bill_goods` as a ,warehouse_goods as b WHERE a.`bill_id` = '{$bill_id}' AND a.`goods_id` = b.`goods_id`";
		//echo $sql;exit;
		return $this->db()->getAll($sql);
	}

	/**打印汇总  根据货号  $bill_id，获取单据和详情表信息 **/
	public function getBillinfo($bill_id){
		$sql = "select  sum(g.`jinzhong`) as jinzhong , g.caizhi from warehouse_bill_goods as og ,warehouse_goods as g where og.goods_id = g.goods_id and og.bill_id = '$bill_id' group by g.caizhi";
		$zhuchengsedata = $this->db()->getAll($sql);
		$sql = "select sum(g.`zhushilishu`) as zhushilishu , sum(g.`zuanshidaxiao`) as zuanshidaxiao , g.zhushi from warehouse_bill_goods as og , warehouse_goods as g where og.goods_id = g.goods_id and og.bill_id = '$bill_id' group by g.zhushi";
		$zhushidata = $this->db()->getAll($sql);
		$sql = "select sum(g.`fushilishu`) as fushilishu , sum(g.`fushizhong`) as fushizhong , g.fushi from warehouse_bill_goods as og ,warehouse_goods as g where og.goods_id = g.goods_id and og.bill_id = '$bill_id' group by g.fushi";
		$fushidata = $this->db()->getAll($sql);
		return array('zhuchengsedata' => $zhuchengsedata,'zhushidata' => $zhushidata,'fushidata' => $fushidata);

	}
	/** 根据货号，获取商品信息 **/
	public function getGoodsC($goods_id){
		$sql = "SELECT goods_id,is_on_sale,order_goods_id,company_id,company,warehouse_id,warehouse,yuanshichengbenjia FROM `warehouse_goods` WHERE `goods_id`='{$goods_id}' LIMIT 1";
		return $this->db()->getRow($sql);
	}	

}?>