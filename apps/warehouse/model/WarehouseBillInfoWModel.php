<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoWModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-18 18:49:32
 *   @update  	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoWModel extends Model
{
	//定义是否开启盘点异常日志追踪
	private $buglog = true;

	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'warehouse_bill_info_w';
		$this->pk='id';
		$this->_prefix='';
		$this->_dataObject = array("id"=>" ",
		"bill_id"=>"关联warehouse_bill 主键",
		"box_sn"=>"柜位号");
		parent::__construct($id,$strConn);
	}

	/**
	* 生成盘点单
	*/
	public function createPandian($warehouse_id, $bill_note){
		$result = array('success' => 0 , 'error' => '');
		$type = 'W';
		$user = $_SESSION['userName'];
		$time = date('Y-m-d H:i:s');

		$billModel = new WarehouseBillModel(21);
		$warehouseModel = new WarehouseModel($warehouse_id ,21);
		$warehouseName = $warehouseModel->getValue('name');
		
		$warehouseRelModel = new WarehouseRelModel(21);
		$companyRow = $warehouseRelModel->GetCompanyByWarehouse("company_id,company_name","warehouse_id={$warehouse_id}","getRow");
		//获取所属公司信息
		$to_company_id=0;
		$to_company_name="";
		if(!empty($companyRow['company_id'])){
		    $to_company_id = $companyRow['company_id'];
		    $to_company_name = $companyRow['company_name'];
		}else{
		    $result = array('success' => 0 , 'error'=> '仓库【'.$warehouseName.'】没有对应的所属公司！');
		    return $result;
		}
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//生成主表信息
			$sql = "INSERT INTO `warehouse_bill` (`bill_no` , `bill_type` , `bill_status` , `goods_num` , `to_warehouse_id` , `to_warehouse_name` , `bill_note` , `goods_total`, `create_user` , `create_time`,`to_company_id`,`to_company_name`) VALUES ('' , '{$type}' , 1 , 0 , {$warehouse_id} , '{$warehouseName}' , '{$bill_note}' , 0 , '{$user}', '{$time}',{$to_company_id},'{$to_company_name}')"; 	//初始化状态盘点中
			$pdo->query($sql);
			$id = $pdo->lastInsertId();
			$bill_no = $billModel->create_bill_no($type,$id);
			$sql = "UPDATE `warehouse_bill` SET `bill_no` = '{$bill_no}' WHERE `id` = {$id}";
			$pdo->query($sql);

			//锁定仓库（为了限制一个仓库只生成一个盘点单）
			$sql = "UPDATE `warehouse` SET `lock` = 1 WHERE `id` = $warehouse_id";
			$pdo->query($sql);

			//生成附表信息
			$sql = "INSERT INTO `warehouse_bill_info_w` (`bill_id` , `box_sn`) VALUES ({$id}, '')";
			$pdo->query($sql);

		}
		catch(Exception $e){//捕获异常
			// echo '<pre>';print_r($e);echo '</pre>';die;
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			$result = array('success' => 0 , 'error'=> '生成盘点单程序事物失败');
			return $result;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		$result = array('success' => 1 , 'error'=> "生成盘点单成功 <span style='color:red'>{$bill_no}</span>", 'bill_id'=>$id);
		return $result;
	}

	/** 锁定柜位 **/
	//如果柜位已经盘点过了，再盘的时候，那么不把该柜位的货品写入明细了 ， 不更新主表信息
    /**
     * @param $box_sn
     * @param $bill_id
     * @param $warehouse_id
     * @return array
     */
    public function LockBox( $box_sn , $bill_id , $warehouse_id){
		$res = array('success' => 0 , 'error'=> '');

		//检测单据是否 点击盘点完成
		$pandianoff = $this->GetBillEnd($bill_id);
		if($pandianoff == 1){
			$res['error'] = '单据已经操作了盘点完成，不能继续盘点';
			return $res;
		}

		//检测单据状态
		$status = $this->GetBillStatus($bill_id);
		if($status == 2){
			$res['error'] = '单据已经审核，不能进行操作';
			return $res;
		}
		if($status == 3){
			$res['error'] = '单据已经取消，不能进行操作';
			return $res;
		}

		//获取单据单号
		$model = new WarehouseBillModel($bill_id , 21);
		$bill_no = $model->getValue('bill_no');

		//检测柜位是否盘点过，已经被标注为盘点过的柜位，再次盘点时避免单据主表信息的货品总量 价格总计累加
		$sql = "SELECT `is_pan` FROM `warehouse_box` WHERE `box_sn` = '{$box_sn}' AND `warehouse_id` = {$warehouse_id}";
		$is_pan = $this->db()->getOne($sql);

		if($is_pan == 1){
			/*$res['error'] = "柜位号：{$box_sn} 已经盘点过，不能再次盘点";
			return $res;*/
		}

		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

			//跟新单据附表信息（记录当前盘点的柜位）
			$sql = "UPDATE `warehouse_bill_info_w` SET `box_sn` = '{$box_sn}' WHERE `bill_id` = {$bill_id}";
			$pdo->query($sql);

			//统计柜位中的货品数量，货品金额总计，跟新到单据主表信息中 (库存状态的) ,锁定柜位中的货品(库存状态的)
			// $sql = "SELECT `a`.`goods_id`, `a`.`goods_sn`, `a`.`goods_name` , `a`.`num` , `a`.`warehouse_id`, `a`.`num`, `a`.`chengbenjia`, `a`.`caizhi`, `a`.`jinzhong`, `a`.`jinhao`, `a`.`yanse`, `a`.`zhengshuhao` FROM `warehouse_goods` AS `a` INNER JOIN `goods_warehouse` AS `b` ON `a`.`goods_id` = `b`.`good_id` LEFT JOIN `warehouse_box` AS `c` ON `b`.`box_id` = `c`.`id` WHERE `c`.`box_sn` = '{$box_sn}' AND `a`.`is_on_sale` = 2 AND `a`.`warehouse_id` = {$warehouse_id}";

			$sql = "SELECT `a`.`goods_id`, `a`.`goods_sn`, `a`.`goods_name` , `a`.`num` , `a`.`warehouse_id`, `a`.`num`, `a`.`yuanshichengbenjia`,`a`.`mingyichengben`, `a`.`caizhi`, `a`.`jinzhong`, `a`.`jinhao`, `a`.`yanse`, `a`.`zhengshuhao`,`a`.`jingxiaoshangchengbenjia`,`a`.`management_fee`,`a`.`company_id` FROM `warehouse_goods` AS `a` , `goods_warehouse` AS `b` , `warehouse_box` AS `c` WHERE  `c`.`box_sn` = '{$box_sn}' AND `a`.`goods_id` = `b`.`good_id` AND `b`.`box_id` = `c`.`id` AND `a`.`is_on_sale` = 2 AND `a`.`warehouse_id` = {$warehouse_id}";
			$goods_arr = $this->db()->getAll($sql);

			$box_goods = array_column($goods_arr, 'goods_id');      //获取当前盘点柜位的货号
			$goods_arr = array_combine($box_goods,$goods_arr);      //使用货号作为数组的键值

			//获取当前盘点单的明细货号列表
			$sql = "SELECT `goods_id` FROM `warehouse_bill_goods` WHERE `bill_id` = {$bill_id}";
			$detail = $this->db()->getAll($sql);
			$det_goods = array_column($detail,'goods_id');
			if(!empty($det_goods)){
				$exs = array_intersect($det_goods,$box_goods);      //获取盘点货柜中的货，筛选出已经写入盘点明细的货号
				$no_insert_goods = array_diff($box_goods, $exs);    //获取货柜中没有写入盘点明细的货号
			}
			$no_insert_goods = isset($no_insert_goods) ? $no_insert_goods : $box_goods;

			$goods_list = '';
			$goods=array();
			$count = array('num' => 0 , 'chengbenjia' => 0);
			/**
			* 优化 ，预处理一下
			*/
			$sql = "INSERT INTO `warehouse_bill_goods` (`bill_id`, `bill_no` , `bill_type` , `goods_id` , `goods_sn` , `goods_name` , `num` , `warehouse_id` , `addtime` , `pandian_status`, `guiwei`, `yuanshichengben`,`mingyijia`, `caizhi`, `jinzhong`, `jinhao` , `yanse` , `zhengshuhao` , `pandian_guiwei`) VALUES ( ? , ? , 'W', ?, ? , ? , ? , ? , ?, 2 , ?, ?, ?, ? , ? , ? , ? , ? ,  ?)";
			if(!empty($no_insert_goods)){
				$stmt = $pdo->prepare($sql);
				foreach($no_insert_goods as $val){
				    //boss_1419 价格总计：直营店和BDD总部（boss系统）为名义成本，经销商店（展厅系统）为批发价+管理费，浩鹏总部（展厅系统）为名义成本
					#将柜位中的货品写入明细 , 如果柜位已经盘点过，那么再次盘点，该柜位下的货，不写入明细中#
				    $company_id = $goods_arr[$val]['company_id'];
				    if(SYS_SCOPE=="zhanting" && !in_array($company_id,array(58,515))){
				        //jingxiaoshangchengbenjia 已包含管理费
				        $goods_arr[$val]['mingyichengben'] = (float)$goods_arr[$val]['jingxiaoshangchengbenjia'];
				    }
					if(!$is_pan){
                        $goods_arr[$val]['jinhao'] = ($goods_arr[$val]['jinhao'] != '') ? $goods_arr[$val]['jinhao'] : 0;
						//预处理
						$do = array(
							$bill_id , $bill_no , $goods_arr[$val]['goods_id'],
							$goods_arr[$val]['goods_sn'] , $goods_arr[$val]['goods_name'] , $goods_arr[$val]['num'],
							$goods_arr[$val]['warehouse_id'] , '0000-00-00 00:00:00', $box_sn,
							$goods_arr[$val]['yuanshichengbenjia'],
                            $goods_arr[$val]['mingyichengben'], 
                            $goods_arr[$val]['caizhi'],
							$goods_arr[$val]['jinzhong'] , $goods_arr[$val]['jinhao'] , $goods_arr[$val]['yanse'],
							$goods_arr[$val]['zhengshuhao'], ''
						);

						$res = $stmt->execute($do);
						// $stmt->debugDumpParams();
					}

					$goods_list .= ',\''.$goods_arr[$val]['goods_id'].'\'';
					$goods[]=$goods_arr[$val]['goods_id'];
					$count['num'] += $goods_arr[$val]['num'];
				    $count['chengbenjia'] += $goods_arr[$val]['mingyichengben'];
				}
				//锁定货品为 盘点中状态
				$goods_list = ltrim($goods_list, ',');
				
				foreach ($goods as $key => $goods_id) {				
					$sql = "UPDATE `warehouse_goods` SET `is_on_sale` = 4 WHERE `is_on_sale`=2 and `goods_id`='{$goods_id}'";
					//$pdo->query($sql);
					$changed=$pdo->exec($sql);  
				    if($changed<>1) {  
							$pdo->rollback();//事务回滚
							$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
							return array('success'=> 0 , 'error'=>'货品状态不是库存中'.$goods_id);
				    }  			
                }
				//锁定柜位
				$sql = "UPDATE `warehouse_box`SET `is_pan` = 1 WHERE `box_sn` = '{$box_sn}' AND `warehouse_id` = {$warehouse_id}";
				$pdo->query($sql);

				//跟新主表信息 ,如果柜位已经盘点过，那么再次盘点，数量 总价 不累加到主表信息
				if(!$is_pan){
					$sql = "UPDATE `warehouse_bill` SET `goods_num` = `goods_num` + {$count['num']}, `goods_total` = `goods_total` + {$count['chengbenjia']} WHERE `id` = {$bill_id}";
					$pdo->query($sql);
				}

				//销售政策使销售政策商品状态变为下架
				$model = new ApiSalepolicyModel();
				$goods_list = str_replace("'","",$goods_list);
				$goods_list = explode(",",$goods_list);
				if(!$model->EditIsSaleStatus($goods_list,0,7))
				{
					//修改可销售商品为下架 失败制造错误回滚。
					$pdo->query('');
				}
			}else{
				//锁定柜位
				$sql = "UPDATE `warehouse_box`SET `is_pan` = 1 WHERE `box_sn` = '{$box_sn}' AND `warehouse_id` = {$warehouse_id}";
				$pdo->query($sql);
			}
		}
		catch(Exception $e){//捕获异常
			// echo '<pre>';print_r($e);echo '</pre>';die;
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			$res['error'] = '切换事物操作失败，导致切换柜位不成功';
			return $res;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		$res = array('success' => 1 , 'error'=>"正在盘点柜位：{$box_sn}");
		return $res;
	}

	/**
	* 接收货号来盘点
	* 1/判断货号是否是合法存在的货号，并且是盘点中的状态
	* 2/判断货号是否是单据明细中的货 ：
	* 	2-1 是-> 判断货品是否盘点过，
	*		2-1-1 盘点过则提示已经盘点过，判断上一次盘点状态是否是盘亏，如果是盘亏，将盘点的信息打印出来，否则修改warehouse_bill_goods 明细中的（盘点状态以及盘点时间，盘点人）
	*		2-1-2 没盘点过，则：
	* 			2-1-2-1/判断货品所在的柜位与当前柜位是否相同， 相同则变更明细的状态为 正常
	*			2-1-2-2/货品柜位与正在盘点的柜位不同，则盘盈，记录货品该有的柜位
	* 	2-2 否->如果不是明细中的货，则将该商品信息写入明细，状态为盘盈且记录货品原来的柜位，锁定货品，变更主表信息的货品数和货架总计
	*/
	public function GetGoodsPandian($goods_id , $bill_id , $now_box_sn, $affirm=0){
		$billModel = new WarehouseBillModel($bill_id, 22);
		$bill_no = $billModel->getValue('bill_no');
		$res = array('success'=> 0 , 'error' => '', 'affirm' => 0);
		$time = date('Y-m-d H:i:s');
		//检测单据是否 点击盘点完成
		$pandianoff = $this->GetBillEnd($bill_id);
		if($pandianoff == 1){
			$res['error'] = '单据已经操作了盘点完成，不能继续盘点';
			return $res;
		}

		//检测单据状态
		$status = $this->GetBillStatus($bill_id);
		if($status == 2){
			$res['error'] = '单据已经审核，不能进行操作';
			return $res;
		}
		if($status == 3){
			$res['error'] = '单据已经取消，不能进行操作';
			return $res;
		}

		$pdo = $this->db()->db();//pdo对象
	#1 盘点货号是否存在，是否是盘点中的状态
		$sql = "SELECT `id`, `is_on_sale`,`warehouse_id` , `warehouse` FROM `warehouse_goods` WHERE `goods_id` = '{$goods_id}'";
		$goods = $this->db()->getRow($sql);
		if(empty($goods)){
			$res['error'] = "不存在货号：<span style='color:red'>{$goods_id}</span>";
			return $res;
		}
        //var_dump($goods['is_on_sale'], $affirm);die;
        if(!in_array($goods['is_on_sale'],array(2,4)) && $affirm != 1){
            $res['affirm'] = 1;
            $res['error'] = "是否记入盘点明细，否则重新扫描货号（默认否）";
            return $res;
        }
		
		// else if($goods['is_on_sale'] != 4 && $goods['is_on_sale'] != 2)
		// {
			// $res['error'] = '货品不是“盘点中” 或 “库存” 状态，不能进行盘点';
			// return $res;
		// }
		// 2015-10-28修改
		else if(in_array($goods['is_on_sale'],array("1","5","6","8","10","11","12")))
		{
			$res['error'] = '收货中、损益中、返厂中、销售中、退货中、调拨中、作废七种状态不允许盘点';
			return $res;
		}
		$dd =  new DictView(new DictModel(1));
		$goods_status = $dd->getEnum('warehouse.goods_status',$goods['is_on_sale']);
// 1\盘点单,应盘总数规则不变，目前只有库存和盘点中才允许盘点,要改成:盘点时允许输入货品状态是库存、盘点中、已销售、已报损、已返厂五种状态的货品，当货品是盘盈状态时,下方的提示语中需要再增加一栏[货品状态],盘亏和正常不需要增加这一栏；收货中、损益中、返厂中、销售中、退货中、调拨中\作废七种状态不允许盘点,
		// 1467 	作废 	12 		
		// 1328 	退货中 	11 		
		// 1316 	销售中 	10 		
		// 1214 	已返厂 	9 		
		// 1213 	返厂中 	8 		
		// 1212 	已报损 	7 		
		// 1211 	损益中 	6 		
		// 1163 	调拨中 	5 		
		// 1162 	盘点中 	4 		
		// 1124 	已销售 	3 		
		// 1123 	库存 	2 		
		// 1122 	收货中 	1 
		#获取货品所在的柜位
		$sql = "SELECT `c`.`box_sn` FROM `warehouse_goods` AS `a` , `goods_warehouse` AS `b` , `warehouse_box` AS `c` WHERE `a`.`goods_id` = '{$goods_id}' AND `a`.`goods_id` = `b`.`good_id` AND `b`.`box_id` = `c`.`id`";
		$goods_box_sn = $this->db()->getOne($sql);

		#获取正在盘点的仓库
		$now_pan_warehouse = array();
		$now_pan_warehouse['warehouse_id'] = $billModel->getValue('to_warehouse_id');
		$now_pan_warehouse['warehouse'] = $billModel->getValue('to_warehouse_name');

	#2 判断货号是否在明细中
		$sql = "SELECT `id` FROM `warehouse_bill_goods` WHERE `goods_id` = '{$goods_id}' AND `bill_id` = {$bill_id}";
		$exsisDetail = $this->db()->getOne($sql);
		# 2-1 货号在当前单据的明细中#
		if($exsisDetail)
		{
			# 2-1 判断是否盘点过该货#
			$sql = "SELECT `pandian_status` FROM `warehouse_bill_goods` WHERE `goods_id` = '{$goods_id}' AND `bill_id` = {$bill_id}";
			$status = $this->db()->getOne($sql);
			# 2-1-1 已经盘过 提示盘货的信息#
			if($status > 2){
				$sql = "SELECT `pandian_status`,`goods_name`,`goods_sn`,`pandian_user`,`pandian_guiwei` FROM `warehouse_bill_goods` WHERE `goods_id` = '{$goods_id}' AND `bill_id` = {$bill_id}";
				$exists = $this->db()->getRow($sql);
				$res['error'] = "
				货号：<span style='color:red;'>{$goods_id}</span>
				款号：<span style='color:red;'>{$exists['goods_sn']}</span>
				名称：{$exists['goods_name']}
				<br/>已经被 {$exists['pandian_user']} 在 <span style='color:red;'>{$exists['pandian_guiwei']}</span> 盘点过";
				return $res;
			}
			# 2-1-2 没有盘过该货号#
				#2-1-2-1/判断货品所在的柜位与当前柜位是否相同， 相同则变更明细的状态为 正常#

				if(trim($goods_box_sn) == trim($now_box_sn))
				{
					$sql = "UPDATE `warehouse_bill_goods` SET `pandian_status` = 4 ,`pandian_guiwei` = '".trim($now_box_sn)."' , `pandian_user` = '{$_SESSION['userName']}', `addtime` = '{$time}' WHERE `goods_id` = '{$goods_id}' AND `bill_id`= {$bill_id}";
					$zc = $this->db()->query($sql);
					if($zc)
					{
						$res['success'] = 1;
						$res['error'] = "货号： $goods_id 盘点正常";
						return $res;
					}else{
						$res['error'] = '盘点失败,重新盘点';
						return $res;
					}
				}else{
				#2-1-2-2/货品柜位与正在盘点的柜位不同，则盘盈，记录货品该有的柜位#
					$sql = "UPDATE `warehouse_bill_goods` SET `pandian_status` = 3, `guiwei` = '{$goods_box_sn}' ,`pandian_guiwei` = '{$now_box_sn}', `pandian_user` = '{$_SESSION['userName']}', `addtime` = '{$time}' WHERE `goods_id` = '{$goods_id}' AND `bill_id` = {$bill_id}";
					$py = $this->db()->query($sql);
					if($py)
					{
						//如果货品所在的仓库和正在盘点的仓库是同一个仓库，只是柜位不相同，不用显示盘盈，改为【正常】，显示结果如下“货号：150550358476 盘点正常 柜位错误 实际仓库：### 实际柜位：###”，且导出结果的盘点明细中，【盘点情况】改为【正常】，【柜位情况】改为【错误】，盘点明细中增加一列【盘点仓库】
						// 当货品是盘盈状态时,下方的提示语中需要再增加一栏[货品状态],2015-10-28增加
						if($goods['warehouse_id'] == $now_pan_warehouse['warehouse_id']){
							$res['success'] = 1;
							$res['error'] = "货号： {$goods_id}  盘点<span style='color:blue'>正常</span> 柜位<span style='color:red'>错误</span> 实际仓库：<span style='text-decoration:underline'>{$goods['warehouse']}</span> 实际柜位：<span style='text-decoration:underline'>{$goods_box_sn}</span><span style='color:#00ff00;'>   原因：货品仓库相同，但货品柜位与正在盘点的柜位不同/当前盘点柜位:{$now_box_sn}</span>";
							if($this->buglog){
								file_put_contents(__DIR__.'/../logs/'.$bill_no.'_error.log', "{$goods_id} 盘点正常，柜位错误（实际仓库：{$goods['warehouse']} / 实际柜位{$goods_box_sn} . 原因：货品仓库相同，但货品柜位与正在盘点的柜位{$now_box_sn}不同）。\r\n" , FILE_APPEND );
							}
						}else{
							$res['success'] = 1;
							$res['error'] = "<span style='color:red;'>货号： {$goods_id}  盘盈</span> 实际仓库：<span style='text-decoration:underline'>{$goods['warehouse']}</span> 实际柜位：<span style='text-decoration:underline'>{$goods_box_sn}</span><span style='color:#0000ff;'>   货品状态： {$goods_status}</span>";
						}

						return $res;
					}else{
						$res['error'] = '盘点 [盘盈状态] 失败';
						return $res;
					}
				}
		}
		# 2-2 货号不在当前单据的明细中#
		else
		{
		
			#货品商品信息
			$sql = "SELECT `num`, `chengbenjia` , `goods_sn` , `goods_name`, `warehouse_id`, `caizhi` , `jinzhong` , `jinhao` , `yanse` , `zhengshuhao` FROM `warehouse_goods` WHERE `goods_id` = '{$goods_id}'";
			$row = $this->db()->getRow($sql);
			$row['jinhao'] = ($row['jinhao'] != false) ? $row['jinhao'] : 0;
			#将该商品信息写入明细，状态为盘盈且记录货品原来的柜位，锁定货品，变更主表信息的货品数和货架总计
			try{
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
				$pdo->beginTransaction();//开启事务
				//锁定货品
				/// 盘盈的货品可以入单据 单不能改变货品状态 update by rong 
				/*
				$sql = "UPDATE `warehouse_goods` SET `is_on_sale`= 4 WHERE `goods_id` = '{$goods_id}' and `is_on_sale`  in (2,3,4,7,9)";//
				 // and `is_on_sale`  in (2,3,4,7,9) 此条件是多余的，上面控制了就根本下不来。2015-10-28
				$pdo->query($sql);
                 */

				//跟新主表信息（盘点单数量的问题 这边已经和亚涛 马总确认了，数量不会被增加）
				/*$sql = "UPDATE `warehouse_bill` SET `goods_sum` = `goods_sum` + {$row['num']} , `chengbenjia` = `chengbenjia` + {$row['chengbenjia']} WHERE `id` = {$bill_id}";
				$pdo->query($sql);*/

				//写入明细
				$sql = "INSERT INTO `warehouse_bill_goods` (`bill_id`, `bill_no` , `bill_type` , `goods_id` , `goods_sn` , `goods_name` , `num` , `warehouse_id` , `addtime` , `pandian_status` , `guiwei` , `caizhi` , `jinzhong` , `jinhao` , `yanse` , `zhengshuhao`, `pandian_guiwei` , `pandian_user`) VALUES ( {$bill_id}, '{$bill_no}' , 'W' , '{$goods_id}' , '{$row['goods_sn']}' , '{$row['goods_name']}' , {$row['num']} , {$row['warehouse_id']} , '{$time}' , 3 , '{$goods_box_sn}' , '{$row['caizhi']}', {$row['jinzhong']} , '{$row['jinhao']}' , '{$row['yanse']}' , '{$row['zhengshuhao']}' , '{$now_box_sn}' , '{$_SESSION['userName']}')";
				$pdo->query($sql);
			}
			catch(Exception $e){//捕获异常
				echo $sql;
				$pdo->rollback();//事务回滚
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
				$res['error'] = '写入单据明细异常，盘点失败';
				return $res;
			}
			$pdo->commit();//如果没有异常，就提交事务
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交


			//如果货品所在的仓库和正在盘点的仓库是同一个仓库，只是柜位不相同，不用显示盘盈，改为【正常】，显示结果如下“货号：150550358476 盘点正常 柜位错误 实际仓库：### 实际柜位：###”，且导出结果的盘点明细中，【盘点情况】改为【正常】，【柜位情况】改为【错误】，盘点明细中增加一列【盘点仓库】
			if($goods['warehouse_id'] == $now_pan_warehouse['warehouse_id']){
				$res['success'] = 1;
				$res['error'] = "货号： {$goods_id}  盘点<span style='color:blue'>正常</span> 柜位<span style='color:red'>错误</span> 实际仓库：<span style='text-decoration:underline'>{$goods['warehouse']}</span> 实际柜位：<span style='text-decoration:underline'>{$goods_box_sn}</span><span style='color:#00ff00;'>原因：锁定柜位时有没写入明细</span>";
				if($this->buglog){
					file_put_contents(__DIR__.'/../logs/'.$bill_no.'_error.log', "{$goods_id} 盘点正常， 柜位错误。 实际仓库：{$goods['warehouse']}，实际柜位：{$goods_box_sn}。原因：锁定柜位时有没写入明细。 \r\n" , FILE_APPEND);
				}
			}
			else
			{
				$res = array('success' => 1 , 'error'=>"<span style='color:red;'>货号： {$goods_id}  盘盈</span> 实际仓库：<span style='text-decoration:underline'>{$goods['warehouse']}</span> 实际柜位：<span style='text-decoration:underline'>{$goods_box_sn}</span><span style='color:#0000ff;'>   货品状态： {$goods_status}</span>");
			}
			return $res;
		}
	}

	/**
	* 根据仓库ID，获取最新一个未审核的盘点单 ID
	*/
	public function GetPandianByWarehouse($warehouse_id){
		$sql = "SELECT `id` FROM `warehouse_bill` WHERE `to_warehouse_id` = {$warehouse_id} AND `bill_status` = 1 AND `bill_type` = 'W' ORDER BY `id` LIMIT 1";
		// echo $sql;die;
		return $this->db()->getOne($sql);
	}

	/**
	* 根据单据ID，获取主表+ 附表信息
	*/
	public function GetBillWinfo($bill_id){
		$sql = "SELECT `a`.`id`, `a`.`bill_no`, `a`.`bill_status`, `a`.`goods_num`, `a`.`to_company_id`,`a`.`to_company_name`,`a`.`to_warehouse_name`, `a`.`to_warehouse_id`, `a`.`bill_note`, `a`.`goods_total`, `a`.`check_user` , `a`.`check_time`, `a`.`create_user`, `a`.`create_time`, `b`.`box_sn`, `b`.`status` FROM `warehouse_bill` AS `a` INNER JOIN `warehouse_bill_info_w` AS `b` ON `a`.`id` = `b`.`bill_id` WHERE `a`.`id` = {$bill_id}";
		return $this->db()->getRow($sql);
	}

	/**
	* 检测输入的柜位号是否是盘点仓下的柜位
	**/
	public function GetBoxId($box_sn, $bill_id){
		$sql = "SELECT `b`.`id` FROM `warehouse_bill` AS `a` , `warehouse_box` AS `b` WHERE `a`.`id` = {$bill_id} AND `b`.`warehouse_id` = `a`.`to_warehouse_id` AND `b`.`box_sn` = '{$box_sn}'";
		return $this->db()->getOne($sql);
	}

	/**
	* 如果没有默认柜位，自动生成默认柜位
	*/
	public function CreateBox($warehouse_id){
		$time = date('Y-m-d H:i:s');
		$sql = "INSERT INTO `warehouse_box` (`warehouse_id` , `box_sn` , `create_name` , `create_time` , `info`) VALUES ({$warehouse_id} , '0-00-0-0' , '{$_SESSION['userName']}' , '{$time}' , '盘点自动生成的默认柜位')";
		return $this->db()->query($sql);
	}

	/**
	 * 获取最新一个 保存状态的盘点单
	 * @param int|当前显示的盘点单id $now 当前显示的盘点单id
	 * @param pre上一个|string $type pre上一个 next下一个
	 * @return
	 */
	public function GetLastPandian($now = 0, $type = ''){
		//获取用户权限内的仓库,只允许当前登录人盘点 自己拥有权限 的所属仓库的盘点单
		$UserWarehouseModel = new UserWarehouseModel(1);
		$UserWarehouseArr=$UserWarehouseModel->getUserWarehouse();
		$in='(';
		foreach ($UserWarehouseArr as $k=>$row){
			if($k==0){
				$in.=$row['house_id'];
			}else{
				$in.=','.$row['house_id'];
			}
			
		}
		$in.=')';
		
		
		if(!$now){
			$sql = "SELECT `a`.*, `b`.`status` FROM `warehouse_bill` AS `a` INNER JOIN `warehouse_bill_info_w` AS `b` ON `a`.`id` = `b`.`bill_id` WHERE `a`.`bill_type` = 'W' AND `a`.`bill_status` = 1 AND a.to_warehouse_id IN {$in} ORDER BY `a`.`id` DESC LIMIT 1";
			return $this->db()->getRow($sql);
		}

		if($type == 'pre'){
			$sql = "SELECT `a`.*, `b`.`status` FROM `warehouse_bill` AS `a` INNER JOIN `warehouse_bill_info_w` AS `b` ON `a`.`id` = `b`.`bill_id` WHERE `a`.`bill_type` = 'W' AND `a`.`bill_status` = 1 AND a.to_warehouse_id IN {$in} AND `a`.`id` < {$now} ORDER BY `a`.`id` DESC LIMIT 1";
			return $this->db()->getRow($sql);
		}
		if($type == 'next'){
			$sql = "SELECT `a`.*, `b`.`status` FROM `warehouse_bill` AS `a` INNER JOIN `warehouse_bill_info_w` AS `b` ON `a`.`id` = `b`.`bill_id` WHERE `a`.`bill_type` = 'W' AND `a`.`bill_status` = 1 AND a.to_warehouse_id IN {$in} AND `a`.`id` > {$now} ORDER BY `a`.`id` DESC LIMIT 1";
			return $this->db()->getRow($sql);
		}

	}

	/**
	* 获取盘点单正在盘点的柜位
	*/
	public function GetNowBox($bill_id){
		$sql = "SELECT `box_sn` FROM `warehouse_bill_info_w` WHERE `bill_id`={$bill_id} LIMIT 1";
		return $this->db()->getOne($sql);
	}

	/**
	* 检测单据是否点击了盘点完成
	*/
	public function GetBillEnd($bill_id){
		$sql = "SELECT `status` FROM `warehouse_bill_info_w` WHERE `bill_id` = {$bill_id}";
		return $this->db()->getOne($sql);
	}

	/**
	* 获取单据的状态
	*/
	public function GetBillStatus($bill_id){
		$sql = "SELECT `bill_status` FROM `warehouse_bill`WHERE `id` = {$bill_id}";
		return $this->db()->getOne($sql);
	}

	/**
	* 切换柜位
	*/
	public function qieBox($bill_id){
		$res = array('success' => 0,'error' =>'');
		$warehouseGoodsModel = new WarehouseGoodsModel(21);
		//检测单据状态
		$status = $this->GetBillStatus($bill_id);
		if($status == 2){
			$res['error'] = '单据已经审核，不能进行操作';
			return $res;
		}
		if($status == 3){
			$res['error'] = '单据已经取消，不能进行操作';
			return $res;
		}

		//盘点是否已经点击过盘点完成
		$status = $this->GetBillEnd($bill_id);
		if($status == 1){
			$res['error'] = '当前单据已盘点完成,不能在进行盘点';
			return $res;
		}
		//检测是否是等待输入柜位号状态
		$sql = "SELECT `box_sn` FROM `warehouse_bill_info_w` WHERE `bill_id` = {$bill_id} ";
		$exists = $this->db()->getOne($sql);
		if(!$exists){
			$res['error'] = '柜位已切换成功，请输入下一个要盘点的柜位';
			return $res;
		}

		//获取盘点的仓库ID
		$sql = "SELECT `to_warehouse_id` FROM `warehouse_bill` WHERE `id` = {$bill_id}";
		$warehouse_id = $this->db()->getOne($sql);

		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//标识上一个柜位已经盘点过
			$sql = "UPDATE `warehouse_box` SET `is_pan` = 1 WHERE `box_sn` = (SELECT `box_sn` FROM `warehouse_bill_info_w` WHERE `bill_id` = {$bill_id}) AND `warehouse_id` = {$warehouse_id}";
			$pdo->query($sql);

			//对上一个柜位锁定的货品解锁
			$sql = "SELECT a.`goods_id` FROM `warehouse_goods` AS `a` INNER JOIN `goods_warehouse` AS `b` ON `a`.`goods_id` = `b`.`good_id` , `warehouse_bill` AS `c` INNER JOIN `warehouse_bill_info_w` AS `d` ON `c`.`id` = `d`.`bill_id` WHERE `c`.`id` = {$bill_id} AND `a`.`is_on_sale` = 4";
			$arr = $this->db()->getAll($sql);
			if(!empty($arr)){
				$lock_goods = '';
				foreach ($arr as $key => $val) {
					$lock_goods .= ',\''. $val['goods_id'].'\'';
				}
				$lock_goods = ltrim($lock_goods, ',');
				$sql = "UPDATE `warehouse_goods` SET `is_on_sale` = 2 WHERE  `goods_id` IN ({$lock_goods}) and `is_on_sale` = 4";
				$changed=$pdo->exec($sql);
			    if($changed<>count(explode(",",$lock_goods))) {  
						$pdo->rollback();//事务回滚
						$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
						return array('success'=> 0 , 'error'=>'货品状态不是盘点中');
			    } 
			}

			//清空柜位号，待用户再次提交新的柜位，写入该字段。
			$sql = "UPDATE `warehouse_bill_info_w` SET `box_sn` = '' WHERE `bill_id` = {$bill_id}";
			$pdo->query($sql);

			if(isset($lock_goods) && !empty($lock_goods)){
				//销售政策使销售政策商品状态变为下架
				$model = new ApiSalepolicyModel();
				$lock_goods = str_replace("'","",$lock_goods);
				$lock_goods = explode(",",$lock_goods);
				//已绑定的商品不可推送上架
				foreach($lock_goods AS $key => $val){
					$bing = $warehouseGoodsModel->select2($fields = ' `order_goods_id` ' , $where = " `goods_id` = '{$val}' " , $is_all = 1);
					if($bing != 0){
						unset($lock_goods[$key]);
					}
				}
				if(!$model->EditIsSaleStatus($lock_goods,1,1))
				{
					//修改可销售商品为上架 失败制造错误回滚。
					$pdo->query('');
				}
			}

		}
		catch(Exception $e){//捕获异常
			// echo '<pre>';print_r($e);echo '</pre>';
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			$res['error'] = '切换事物操作失败，导致切换柜位不成功';
			return $res;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		$res['success'] = 1;
		return $res;
	}
	/**
	* 盘点完成操作
	* 1/获取没有盘点的柜位的货品总价/货品总量写入主表
	* 2/没有盘点的柜位下的货全当作明细写入单据明细，状态盘亏
	* 3/跟新 warehouse_bill_info_w status 字段 == 1 （代表此单据盘点完成）
	*/
	public function OffPandian($bill_id , $bill_note)
	{
		$warehouseGoodsModel = new WarehouseGoodsModel(21);
		$res = array('success'=> 0 , 'error' => '');
		//检测该仓库下的所有柜位是否都全部盘点过（是：往下走 / 否：停止，并提示）
		$sql = "SELECT `c`.`id`,`c`.`box_sn` FROM `warehouse_bill` AS `b` LEFT JOIN `warehouse_box` AS `c` ON `b`.`to_warehouse_id` = `c`.`warehouse_id` WHERE `b`.`id` = {$bill_id} AND `c`.`is_pan`=0 AND `is_deleted` = 1";
		$arr = $this->db()->getAll($sql);
		$boxs ='';
		$boxsns = '';
		foreach($arr as $val){
			$boxs .= ','.$val['id'];
			$boxsns .= '    '.$val['box_sn'];
		}
		$boxs = ltrim($boxs, ',');
		$boxsns = ltrim($boxsns, '    ');
		if(strlen($boxs) > 0){
			$res['error'] = "该仓库下还有未盘点的柜位，不能做“盘点完成”操作<br/>未盘柜位：{$boxsns}";
			return $res;
		}

		//检测单据状态
		$status = $this->GetBillStatus($bill_id);
		if($status == 2){
			$res['error'] = '单据已经审核，不能进行操作';
			return $res;
		}
		if($status == 3){
			$res['error'] = '单据已经取消，不能进行操作';
			return $res;
		}
		//盘点是否已经点击过盘点完成
		$status = $this->GetBillEnd($bill_id);
		if($status == 1){
			$res['success'] = 1;
			$res['error'] = '当前单据已盘点完成,不能再做改动';
			return $res;
		}
		$time = date('Y-m-d H:i:s');
		$user = $_SESSION['userName'];
		$type = 'W';
		$model = new WarehouseBillModel($bill_id , 21);
		$bill_no = $model->getValue('bill_no');
		$to_warehouse_id = $model->getValue('to_warehouse_id');

		//将明细中的货品 的仓储状态变更为“库存”。 (既盘点过的货品变为库存)
		$sql = "SELECT `a`.`goods_id` FROM `warehouse_bill_goods` AS `a` RIGHT JOIN `warehouse_bill_info_w` AS `b` ON `a`.`bill_id` = `b`.`bill_id` WHERE `a`.`bill_id` = {$bill_id}";
		$DetailGoodsID = $this->db()->getAll($sql);
		$goods_ids = '';
		foreach ($DetailGoodsID as $key => $val) {
			$goods_ids .= ",'{$val['goods_id']}'";
		}
		$goods_ids = ltrim($goods_ids , ',');	//已经盘点过的货品

		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

			/** 因为要确保所有柜位盘点完成了，才允许盘点完成，所以这里之前是处理未盘过的货柜的功能，注释掉 **/
			/*$sql = "SELECT `c`.`id`,`c`.`box_sn` FROM `warehouse_bill` AS `b` LEFT JOIN `warehouse_box` AS `c` ON `b`.`to_warehouse_id` = `c`.`warehouse_id` WHERE `b`.`id` = {$bill_id} AND `c`.`is_pan`=0";
			$arr = $this->db()->getAll($sql);
			$boxs ='';
			foreach($arr as $val){
				$boxs .= ','.$val['id'];
			}
			$boxs = ltrim($boxs, ',');
			if(strlen($boxs) > 0)
			{
				//统计未盘柜位的货品总计，价格总计(库存状态的)
				$sql = "SELECT SUM(`a`.`chengbenjia`) AS `chengbenjia` , count(`a`.`id`) AS `num` FROM `warehouse_goods` AS `a` , `goods_warehouse` AS `b` WHERE `a`.`goods_id` = `b`.`good_id` AND `a`.`is_on_sale` = 2 AND `a`.`warehouse_id` = {$to_warehouse_id} AND `b`.`box_id` IN ($boxs)";
				$row = $this->db()->getRow($sql);
				$row['chengbenjia'] = $row['chengbenjia'] ? $row['chengbenjia'] : 0;
				$row['num'] = $row['num'] ? $row['num'] : 0;

				//跟新主表信息
				$sql = "UPDATE `warehouse_bill` SET `goods_num` = `goods_num` + {$row['num']} , `chengbenjia` = `chengbenjia` + {$row['chengbenjia']} , `bill_note` = '{$bill_note}' WHERE `id` = {$bill_id}";
				$pdo->query($sql);
				//写入明细主表
				$sql_val = "SELECT {$bill_id} , '{$bill_no}' , '{$type}' , `a`.`goods_id`, `a`.`goods_sn`, `a`.`goods_name`, `a`.`num`, `a`.`warehouse_id`, '{$time}', 2, `c`.`box_sn` , `c`.`box_sn` FROM `warehouse_goods` AS `a` , `goods_warehouse` AS `b` , `warehouse_box` AS `c` WHERE `a`.`goods_id` = `b`.`good_id` AND `a`.`is_on_sale` = 2 AND `c`.`id` = `b`.`box_id` AND `a`.`warehouse_id` = {$to_warehouse_id} AND `b`.`box_id` IN ({$boxs})";

				$sql = "INSERT INTO `warehouse_bill_goods` (`bill_id`, `bill_no` , `bill_type` , `goods_id` , `goods_sn` , `goods_name` , `num` , `warehouse_id` , `addtime` , `pandian_status`, `guiwei` , `pandian_guiwei`) ".$sql_val;
				$pdo->query($sql);

				//标识柜位已经盘点
				$sql = "UPDATE `warehouse_box` SET `is_pan` = 1 WHERE `id` IN ({$boxs})";
				$pdo->query($sql);

				//获取所有未盘点的柜位下的货号集合
				$sql = "SELECT `a`.`goods_id` FROM `warehouse_goods` AS `a` , `goods_warehouse` AS `b` WHERE `a`.`goods_id` = `b`.`good_id` AND `a`.`is_on_sale` = 2 AND `b`.`box_id` IN ({$boxs})";
				$arr = $this->db()->getAll($sql);
				$goods_id_arr = array();
				if(!empty($arr))
				{
					$goods_ids = '';
					foreach ($arr as $key => $val)
					{
						$goods_id_arr[] = $val['goods_id'];
					}
				}
			}*/

			#变更已经盘点完成状态
			$sql = "UPDATE `warehouse_bill_info_w` SET `status` = 1 WHERE `bill_id` = {$bill_id}";
			$pdo->query($sql);

			#将明细中的货品 的仓储状态变更为“库存”。 (既盘点过的货品变为库存)
			if(!empty($goods_ids)){
				$sql = "UPDATE `warehouse_goods` SET `is_on_sale` = 2 WHERE `goods_id` IN ($goods_ids) and `is_on_sale`=4";
				$pdo->query($sql);
			}

			//盘点完成了，所有的柜位都有盘完，取出单据的明细，做销售政策上架
			$sql = "SELECT `goods_id` FROM `warehouse_bill_goods` WHERE `bill_id` = {$bill_id}";
			$arr = $this->db()->getAll($sql);
			$goods_id_arr = array();
			$goods_id_arr = array_column($arr, 'goods_id');

			if(isset($goods_id_arr) && !empty($goods_id_arr)){
				//销售政策使销售政策商品状态变为下架
				$model = new ApiSalepolicyModel();
				//已绑定的商品不可推送上架
				foreach($goods_id_arr AS $key => $val){
					$bing = $warehouseGoodsModel->select2($fields = ' `order_goods_id` ' , $where = " `goods_id` = '{$val}' " , $is_all = 1);
					if($bing != 0){
						unset($goods_id_arr[$key]);
					}
				}
				if(!$model->EditIsSaleStatus($goods_id_arr,1,1))
				{
					//修改可销售商品为下架 失败制造错误回滚。
					$pdo->query('');
				}
			}
		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			$res = array('success'=> 0 , 'error' => '程序事物执行失败,导致完成盘点不成功');
			return $res;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		$res = array('success'=> 1 , 'error' => '盘点完成操作成功');
		return $res;
	}

	/**
	* 审核单据
	* 只有在warehouse_bill_info_w 的字段 status= 1时才能审核
	* 审核过的不能审核
	* 自己不能审核自己的单据
	* 1/ 仓库解锁
	* 2/柜位解锁
	* 3/单据表信息更新
	*/
	public function checkBill($bill_id){
		$res = array('success' => 0 , 'error' => '');
		$user = $_SESSION['userName'];
		$time = date('Y-m-d H:i:s');
		$model = new WarehouseBillModel($bill_id , 21);
		$create_user = $model->getValue('create_user');
		if($model->getValue('bill_status') == 2){
			$res['error'] = '单据已经审核，不能重复审核';
			return $res;
		}
		if($user === $create_user){
			/*$res['error'] = '自己不能审核自己的单据';
			return $res;*/
		}
		$sql = "SELECT `status`FROM `warehouse_bill_info_w` WHERE `bill_id` = {$bill_id}";
		$status = $this->db()->getOne($sql);
		if($status != 1){
			$res['error'] = '单据没有做“盘点完成”操作，不能审核';
			return $res;
		}
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			#1/ 仓库解锁
			$sql = "UPDATE `warehouse` SET `lock` = 0 WHERE `id` = (SELECT `to_warehouse_id` FROM `warehouse_bill` WHERE `id` = {$bill_id})";
			$pdo->query($sql);

			$sql = "SELECT `a`.`id` FROM `warehouse_box`AS `a` , `warehouse_bill` AS `b` WHERE `b`.`id`={$bill_id} AND `a`.`warehouse_id` = `b`.`to_warehouse_id`";
			$arr = $this->db()->getAll($sql);
			$boxs = '';
			foreach ($arr as $key => $val) {
				$boxs .= ',\''.$val['id'].'\'';
			}
			$boxs = ltrim($boxs , ',');
			#2/柜位解锁
			$sql = "UPDATE `warehouse_box` SET `is_pan` = 0 WHERE `id` IN ({$boxs})";
			$pdo->query($sql);

			#3/单据表信息更新
			$sql = "UPDATE `warehouse_bill` SET `check_user` = '{$user}' , `check_time` = '{$time}' , `bill_status` = 2 WHERE `id` = {$bill_id}";
			$pdo->query($sql);

			/**
			审核不做可销售货品上下架操作。已经在点击盘点完成按钮做了这一操作。没有点击盘点完成，不能审核
			*/
		}
		catch(Exception $e){//捕获异常
			die($sql);
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			$res = array('success'=> 0 , 'error' => '程序事物执行失败,导致审核盘点单不成功');
			return $res;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		$res = array('success'=> 1 , 'error' => '盘点审核成功');
		return $res;
	}


	/**
	* 取消单据
	* 1/仓库解锁
	* 2/盘点过的柜位解锁
	* 3/盘点单主表信息状态变更
	* 4/盘点过的货变更为库存状态
	*/
	public function closePandian($bill_id){
		$res = array('success' => 0,'error' =>'');
		//检测单据状态
		$status = $this->GetBillStatus($bill_id);
		if($status == 2){
			$res['error'] = '单据已经审核，不能进行操作';
			return $res;
		}
		if($status == 3){
			$res['error'] = '单据已经取消，不能进行操作';
			return $res;
		}
		//获取单据仓库
		$model = new WarehouseBillModel($bill_id , 21);
		$warehouse_id = $model->getValue('to_warehouse_id');
		//获取仓库下所有的盘点过的柜位
		$sql = "SELECT `id` FROM `warehouse_box` WHERE `warehouse_id` = {$warehouse_id} AND `is_pan` = 1";
		$box_arr = $this->db()->getAll($sql);
		$box_ids = '';
		foreach ($box_arr as $key => $val){
			$box_ids .= ",'{$val['id']}'";
		}
		$box_ids = ltrim($box_ids, ',');

		//获取单据下所有明细货品
		$sql = "SELECT `goods_id` FROM `warehouse_bill_goods` WHERE `bill_id` = {$bill_id}";
		$arr = $this->db()->getAll($sql);
		$goods_ids = '';
		foreach ($arr as $key => $val){
			$goods_ids .= ",'{$val['goods_id']}'";
		}
		$goods_ids = ltrim($goods_ids, ',');

		$warehouseGoodsModel = new WarehouseGoodsModel(21);
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			# 仓库解锁
			$sql = "UPDATE `warehouse` SET `lock` = 0 WHERE `id` = {$warehouse_id}";
			$pdo->query($sql);
			#柜位解锁
			if(strlen($box_ids) > 0){
				$sql = "UPDATE `warehouse_box` SET `is_pan` = 0 WHERE `id` IN ($box_ids)";
				$pdo->query($sql);
			}
			#变更主表信息
			$sql = "UPDATE `warehouse_bill` SET `bill_status` = 3 WHERE `id` = {$bill_id}";
			$pdo->query($sql);
			#跟新附表信息
			$sql = "UPDATE `warehouse_bill_info_w` SET `box_sn` = '' WHERE `bill_id` = {$bill_id}";
			$pdo->query($sql);
			#货品解锁
			if(strlen($goods_ids) > 0){
				$sql = "UPDATE `warehouse_goods` SET `is_on_sale` = 2 WHERE `goods_id` IN ($goods_ids) and  `is_on_sale` = 4";
				$pdo->query($sql);

				//销售政策使销售政策商品状态变为下架
				$model = new ApiSalepolicyModel();
				$goods_ids = str_replace("'","",$goods_ids);
				$goods_ids = explode(",",$goods_ids);
				//已绑定的商品不可推送上架
				foreach($goods_ids AS $key => $val){
					$bing = $warehouseGoodsModel->select2($fields = ' `order_goods_id` ' , $where = " `goods_id` = '{$val}' " , $is_all = 1);
					if($bing != 0){
						unset($goods_ids[$key]);
					}
				}
				if(!$model->EditIsSaleStatus($goods_ids,1,1))
				{
					//修改可销售商品为上架 失败制造错误回滚。
					$pdo->query('');
				}
				#写入状态表信息（warehouse_bill_status）
				$time = date('Y-m-d H:i:s');
				$user = $_SESSION['userName'];
				$billModel = new WarehouseBillModel($bill_id, 21);
				$bill_no = $billModel->getValue('bill_no');
				$ip = Util::getClicentIp();
				$sql = "INSERT INTO `warehouse_bill_status`(`bill_id`,`bill_no`,`status`,`update_time`,`update_user`,`update_ip`) VALUES ({$bill_id},'{$bill_no}',3,'{$time}','{$user}','{$ip}')";
				$pdo->query($sql);
			}

		}catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			$res = array('success'=> 0 , 'error' => '程序事物执行失败,导致取消失败'.$sql);
			return $res;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		$res = array('success'=> 1 , 'error' => '盘点取消成功');
		return $res;

	}

	/**
	* 根据盘点单的id，获取单据的明细
	*/
	public function GetDetailRow($bill_id){
		$sql = "SELECT `b`.`goods_id` FROM `warehouse_bill` AS `a` LEFT JOIN `warehouse_bill_goods` AS `b` ON `a`.`id` = `b`.`bill_id` WHERE  `a`.`id` = {$bill_id}";
		return $this->db()->getAll($sql);
	}

	/**
	* 获取盘点单已经盘点的货品（只要不是盘亏，都是盘点过的）
	*/
	public function GetPaningNum($bill_id){
		$sql = "SELECT `b`.`goods_id` FROM `warehouse_bill` AS `a` LEFT JOIN `warehouse_bill_goods` AS `b` ON `a`.`id` = `b`.`bill_id` WHERE `a`.`id` = {$bill_id} AND `b`.`pandian_status` > 2";
		return $this->db()->getAll($sql);
	}

	/**
	 * 获取盘点单被当前登录人已经盘点的货品（只要不是盘亏，都是盘点过的）
	 */
	public function GetPaningNumByUser($bill_id,$username){
		$sql = "SELECT `b`.`goods_id` FROM `warehouse_bill` AS `a` LEFT JOIN `warehouse_bill_goods` AS `b` ON `a`.`id` = `b`.`bill_id` WHERE `a`.`id` = {$bill_id} AND `b`.`pandian_status` > 2 AND b.pandian_user = '".$username."'";
		return $this->db()->getAll($sql);
	}
	
	
	/**
	* 打印盘点结果集
	*/
	public function PrintInfo($bill_id){
		#这些是盘点到的柜位
		$sql = "SELECT `a`.`goods_id` , `a`.`goods_sn`  , `a`.`addtime` , `a`.`mingyijia` as `mingyichengben` , `a`.`caizhi` , `a`.`jinzhong` , `a`.`jinhao` , `c`.`zuanshidaxiao` , `c`.`jingdu` , `a`.`yanse` , `a`.`zhengshuhao` , `a`.`guiwei` , `a`.`pandian_status` , `a`.`warehouse_id` , `a`.`pandian_guiwei` , `a`.`pandian_user`, `b`.`name` AS `warehouse_name`, `c`.`goods_name`,`c`.`is_on_sale` FROM `warehouse_bill_goods` AS `a` INNER JOIN `warehouse` AS `b` ON `a`.`warehouse_id` = `b`.`id` LEFT JOIN  `warehouse_goods` AS `c` ON `a`.`goods_id` = `c`.`goods_id` WHERE `a`.`bill_id` = {$bill_id}";

		$data = $this->db()->getAll($sql);

		//只要没有盘点完成，就去动态读取当前盘点仓库下库存状态的货，如果点击了盘点完成，则只读单据明细作为导出的结果
		$sql = "SELECT `status` FROM `warehouse_bill_info_w` WHERE `bill_id` = {$bill_id}";
		$pan_off = $this->db()->getOne($sql);
		if(!$pan_off){
			//获取当前盘点的仓库ID
			$model = new WarehouseBillModel($bill_id ,21);
			$warehouse_id = $model->getValue('to_warehouse_id');
			#获取没有盘点的柜位
			$sql = "SELECT `box_sn` FROM `warehouse_box` WHERE `warehouse_id` = {$warehouse_id} AND `is_pan` = 0";
			$no_pan_box = $this->db()->getAll($sql);
			$no_pan_box = implode("','", array_column($no_pan_box , 'box_sn'));

			#未盘的柜位(获取当前盘点柜位的库存货，然后跟上一步的货号比对，取差集)
			//$sql = "SELECT `a`.`goods_id`,`a`.`goods_sn`,`a`.`goods_name`,'' AS `pandian_user`,'' AS `addtime`,`a`.`chengbenjia`,`a`.`caizhi`,`a`.`jinzhong`,`a`.`jinhao`,`a`.`yanse`,`a`.`zhengshuhao`, '' AS `pandian_guiwei`,2 AS `pandian_status`,`a`.`box_sn` AS `guiwei`, `a`.`warehouse` AS `warehouse_name`, `a`.`warehouse_id` FROM `warehouse_goods` AS `a` , `warehouse_box` AS `b` WHERE `a`.`is_on_sale` = 2 AND `a`.`warehouse_id` = {$warehouse_id} AND `a`.`warehouse_id` = `b`.`warehouse_id` AND `b`.`is_pan` = 0 AND `b`.`box_sn` IN ('{$no_pan_box}')";
			$sql = "SELECT `a`.`goods_id`,`a`.`goods_sn`,`a`.`goods_name`,'' AS `pandian_user` ,'' AS `addtime`,`a`.`chengbenjia`  AS `mingyichengben`,`a`.`caizhi`,`a`.`jinzhong`,`a`.`jinhao`,`a`.`zuanshidaxiao`,`a`.`jingdu`,`a`.`yanse`,`a`.`zhengshuhao`, '' AS `pandian_guiwei`,2 AS `pandian_status`,`b`.`box_sn` AS `guiwei`, `a`.`warehouse` AS `warehouse_name`, `a`.`warehouse_id`,`a`.`is_on_sale` FROM `warehouse_goods` AS `a`,`goods_warehouse` as c,`warehouse_box` AS `b` where a.goods_id = c.good_id and c.box_id = b.id and `a`.`is_on_sale` = 2 AND `a`.`warehouse_id` = {$warehouse_id}  AND `b`.`is_pan` = 0 AND `b`.`box_sn` IN ('{$no_pan_box}')";
			if(SYS_SCOPE=='zhanting')
				$sql = "SELECT `a`.`goods_id`,`a`.`goods_sn`,`a`.`goods_name`,'' AS `pandian_user` ,'' AS `addtime`,if(a.company_id=58,`a`.`chengbenjia`,a.jingxiaoshangchengbenjia)  AS `mingyichengben`,`a`.`caizhi`,`a`.`jinzhong`,`a`.`jinhao`,`a`.`zuanshidaxiao`,`a`.`jingdu`,`a`.`yanse`,`a`.`zhengshuhao`, '' AS `pandian_guiwei`,2 AS `pandian_status`,`b`.`box_sn` AS `guiwei`, `a`.`warehouse` AS `warehouse_name`, `a`.`warehouse_id`,`a`.`is_on_sale` FROM `warehouse_goods` AS `a`,`goods_warehouse` as c,`warehouse_box` AS `b` where a.goods_id = c.good_id and c.box_id = b.id and `a`.`is_on_sale` = 2 AND `a`.`warehouse_id` = {$warehouse_id}  AND `b`.`is_pan` = 0 AND `b`.`box_sn` IN ('{$no_pan_box}')";

			$no_pan = $this->db()->getAll($sql);
			//比较差集
			$diff = array_diff(array_column($no_pan,'goods_id'), array_column($data,'goods_id'));
			foreach($no_pan AS $val){
				if(in_array($val['goods_id'], $diff)){
					$data[] = $val;
				}
			}
		}
		return $data;
	}
	/**
	* 打印盘点结果集
	*/
/*	public function PrintInfo($bill_id){
		$sql = "SELECT `a`.`goods_id` , `a`.`goods_sn` , `a`.`goods_name` , `a`.`addtime` , `a`.`chengbenjia` , `a`.`caizhi` , `a`.`jinzhong` , `a`.`jinhao` , `a`.`yanse` , `a`.`zhengshuhao` , `a`.`guiwei` , `a`.`pandian_status` , `a`.`warehouse_id` , `a`.`pandian_guiwei` , `a`.`pandian_user`, `b`.`name` AS `warehouse_name` FROM `warehouse_bill_goods` AS `a` INNER JOIN `warehouse` AS `b` ON `a`.`warehouse_id` = `b`.`id` WHERE `a`.`bill_id` = {$bill_id}";
		return $this->db()->getAll($sql);
	}*/

	/**
	* 获取打印柜位盘点情况
	*/
	public function PrintInfoToGuiwei($bill_id){
		$return  = array('guiwei'=> '' , 'pandian_guiwei' => '');
		$sql = "SELECT `guiwei` , count(*)  AS `cnt` FROM `warehouse_bill_goods` WHERE `bill_id` = {$bill_id} GROUP BY `guiwei`";
		$return['guiwei'] = $this->db()->getAll($sql);
		$sql = "SELECT `pandian_guiwei` , count(*) AS `cnt` FROM `warehouse_bill_goods` WHERE `bill_id` = {$bill_id} GROUP BY `pandian_guiwei`";
		$return['pandian_guiwei'] = $this->db()->getAll($sql);
		return $return;

	}

	//转换编码格式，导出csv数据
	public function detail_csv($name,$title,$content)
	{
		$ymd = date("Ymd_His", time()+8*60*60);
		/*
		header("Content-Disposition: attachment;filename=".iconv('utf-8','gbk',$name).$ymd.".csv");
		$fp = fopen('php://output', 'w');
		$title = eval('return '.iconv('utf-8','gbk',var_export($title,true).';')) ;
		fputcsv($fp, $title);
		foreach($content as $k=>$v)
		{
			fputcsv($fp, $v);
		}
		fclose($fp);
		*/
            header("Content-type:text/csv;charset=gbk");
            header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . ".csv");
            header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
            header('Expires:0');
            header('Pragma:public');
            //echo iconv("utf-8", "gbk//IGNORE", $xls_content);
            $title = eval('return '.iconv('utf-8','gbk',var_export($title,true).';')) ;
            echo  implode(",",$title)."\n";
                foreach($content as $k=>$v)
                {
                       echo  implode(",",$v)."\n";
                }

            exit;
		
	}


}
