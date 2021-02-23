<?php
error_reporting(E_ALL & ~E_NOTICE);

class transferModel {

	function __construct() {

	}

	public function getWarehouse() {
		global $db;
		// add exception after 58 (means master company):
		// 排除：广州天河分公司广晟大厦体验店、广州越秀分公司吉邦大厦体验店、郑州二七路体验店
		// 排除：上海南京东路体验店
		// 上海淮海中路体验店
		// 湛江观海路体验店,西安北大街体验店,哈尔滨中央大街体验店,南京珠江一号体验店,成都科甲巷体验店
		$exception = " AND company_id NOT IN(58, 500, 501, 189, 223,59,220,323,364,414,437) ";
		$sql = "SELECT company, warehouse, company_id, warehouse_id FROM `warehouse_goods`
				WHERE is_on_sale=2 {$exception}
				GROUP BY warehouse_id;";
		return $db->getAll($sql);
	}

	public function getGoodsInWarehouse($warehouse_id) {
		global $db;
		$sql = "SELECT * FROM warehouse_goods
				WHERE warehouse_id = {$warehouse_id} AND is_on_sale=2
				ORDER BY goods_id ASC;";
		return $db->getAll($sql);
	}

	/**
	 * 分公司调回总公司
	 * @param  [type] $from_company      [description]
	 * @param  [type] $from_company_id   [description]
	 * @param  [type] $from_warehouse    [description]
	 * @param  [type] $from_warehouse_id [description]
	 * @param  [type] $goods             [description]
	 * @return [type]                    [description]
	 */
	public function createBillM($from_company, $from_company_id, $from_warehouse, $from_warehouse_id, $goods, $create_time) {
		global $db;
		$pdo = $db->db(); //pdo对象

		$vinfo = $this->getVirtualWarehouse();
		$bill_info = array(
			'bill_type' => 'M',
			'bill_status' => '2', // 已审核
			'order_sn' => "",
			'goods_total' => 0,
			'goods_total_jiajia' => 0,
			'goods_num' => count($goods),
			'create_user' => "system",
			'create_time' => $create_time,
			'check_user' => 'system',
			'check_time' => $create_time,
			'bill_note' => '（系统自动生成）',

			'from_company_id' => $from_company_id, //发货
			'from_company_name' => $from_company,
			'from_warehouse_id' => $from_warehouse_id,
			'from_warehouse_name' => $from_warehouse,
			'to_company_id' => $vinfo['company_id'],
			'to_company_name' => $vinfo['company_name'],
			'to_warehouse_id' => $vinfo['warehouse_id'], //入库
			'to_warehouse_name' => $vinfo['warehouse_name'],
		);

		foreach ($goods as $k => $v) {
			/** 过滤啥也没填的，空的明细信息 **/
			if ($v['goods_id'] == '' && $v['goods_sn'] == '' && $v['goods_name'] == '' && $v['jinzhong'] == '' && $v['caizhi'] == '' && $v['yanse'] == '' && $v['jingdu'] == '' && $v['jinhao'] == '' && $v['yuanshichengbenjia'] == '' && $v['zhengshuhao'] == '' && $v['addtime'] == '') {
				unset($goods[$k]);
				continue;
			}

			$bill_info['goods_total'] += $v['yuanshichengbenjia'];
			// 加价率，初次调回总公司不设加价率
			// 加价成本：
			$bill_info['goods_total_jiajia'] += $v['yuanshichengbenjia'];
		}

		try {
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
			$pdo->beginTransaction(); //开启事务

			// 主表：（直接审核）
			$sql = "INSERT INTO `warehouse_bill` (
			`bill_no`, `to_company_id`,
			`from_company_id`,`goods_num`,`goods_total`,
			`goods_total_jiajia`,
			`bill_note`,
			`to_company_name`,`from_company_name`,
			`create_user`,`create_time`,`bill_type`,`order_sn`
			, `bill_status`, `check_user`, `check_time`
			) VALUES (
			'', {$bill_info['to_company_id']},
			{$bill_info['from_company_id']}, {$bill_info['goods_num']}, {$bill_info['goods_total']},
			{$bill_info['goods_total_jiajia']},
			'{$bill_info['bill_note']}',
			'{$bill_info['to_company_name']}',
			'{$bill_info['from_company_name']}','{$bill_info['create_user']}',
			'{$bill_info['create_time']}', '{$bill_info['bill_type']}','{$bill_info['order_sn']}'
			,{$bill_info['bill_status']}, '{$bill_info['check_user']}', '{$bill_info['check_time']}'
			)";
			
			$pdo->query($sql);
			file_put_contents('e:/mdebug.sql',$sql);
			$id = $pdo->lastInsertId();

			//创建单据编号
			$bill_no = $this->create_bill_no($bill_info['bill_type'], $id);
			$sql = "UPDATE `warehouse_bill` SET `bill_no`='{$bill_no}' WHERE `id`={$id}";
			file_put_contents('e:/mdebug2.sql',$sql);
			$pdo->query($sql);

			//插入明细
			foreach ($goods as $gk => $gv) {
				
				//给金耗为空的赋值 0
				if ($gv['jinhao'] == '') {
					$gv['jinhao'] = 0;
				}

				$params = array($id, $bill_no, $bill_info['bill_type'], $gv['goods_id'],
					$gv['goods_sn'], $gv['goods_name'], 1, $gv['jinzhong'],
					$gv['caizhi'], $gv['yanse'], $gv['jingdu'], (int) $gv['jinhao'],
					$gv['yuanshichengbenjia'], $gv['zhengshuhao'], $gv['addtime']);
				$place_holders = implode(',', array_fill(0, count($params), '?'));
				$sql = "INSERT INTO `warehouse_bill_goods` (
		  			`bill_id`, `bill_no`, `bill_type`, `goods_id`,
		  			`goods_sn`, `goods_name`, `num`, `jinzhong`,
		  			`caizhi`, `yanse`,`jingdu`,`jinhao`,
		  			`sale_price`, `zhengshuhao`, `addtime`
		  			) VALUES ({$place_holders}) ";
				// var_dump($db->showQuery($sql, $params));
				$stmt = $pdo->prepare($sql);
				$isSuccess = $stmt->execute($params);
				if (!isSuccess) {
					print_r($pdo->errorInfo());
				}

				// file_put_contents('e:/m9.sql');
				//$pdo->query($sql);
			}

			//插入订单号+快递单号
			// $sql = "INSERT INTO `warehouse_bill_info_m` (`bill_id`,`ship_number`) VALUES ({$id}, '')";
			// $pdo->query($sql);

			//写入warehouse_bill_status信息
			$update_time = date('Y-m-d H:i:s');
			$ip = '';
			$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$id}, '{$bill_no}', 2, '{$update_time}', '{$bill_info['create_user']}', '{$ip}') ";
			$pdo->query($sql);

			// 结束
		} catch (Exception $e) {
			//捕获异常
			$pdo->rollback(); //事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); //开启sql语句自动提交
			return array('success' => 0, 'error' => "Transaction fail! \n" . $goods_id_str);
		}
		$pdo->commit(); //如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); //开启sql语句自动提交

		// write detail log:
		foreach ($goods as $v) {
			$this->writeDetailLog("{$bill_no},{$v['goods_id']},{$v['goods_name']},{$v['yuanshichengbenjia']},0,{$bill_info['from_company_name']},{$bill_info['to_company_name']},-");
		}

		return array('success' => 1, 'x_id' => $id, 'bill_no' => $bill_no, 'num' => count($goods), 'amount' => $bill_info['goods_total_jiajia']);
	}

	/**
	 * 总公司---> 分公司 （加价）
	 * @param  [type] $from_company      [description]
	 * @param  [type] $from_company_id   [description]
	 * @param  [type] $from_warehouse    [description]
	 * @param  [type] $from_warehouse_id [description]
	 * @param  [type] $goods             [description]
	 * @return [type]                    [description]
	 */
	public function createBillY($from_company, $from_company_id, $from_warehouse, $from_warehouse_id, $goods, $create_time) {
		global $db;
		$pdo = $db->db(); //pdo对象

		$vinfo = $this->getVirtualWarehouse();
		$bill_info = array(
			'bill_type' => 'M',
			'bill_status' => '2', // 已审核
			'order_sn' => "",
			'goods_total' => 0, // 程序后续统计再更新
			'goods_total_jiajia' => 0,
			'goods_num' => count($goods),
			'create_user' => "system",
			'create_time' => $create_time,
			'check_user' => 'system',
			'check_time' => $create_time,
			'bill_note' => '（系统自动生成）',

			'to_company_id' => $from_company_id,
			'to_company_name' => $from_company,
			'to_warehouse_id' => $from_warehouse_id,
			'to_warehouse_name' => $from_warehouse,
			'from_company_id' => $vinfo['company_id'],
			'from_company_name' => $vinfo['company_name'],
			'from_warehouse_id' => $vinfo['warehouse_id'],
			'from_warehouse_name' => $vinfo['warehouse_name'],
		);

		foreach ($goods as $k => $v) {
			/** 过滤啥也没填的，空的明细信息 **/
			if ($v['goods_id'] == '' && $v['goods_sn'] == '' && $v['goods_name'] == '' && $v['jinzhong'] == '' && $v['caizhi'] == '' && $v['yanse'] == '' && $v['jingdu'] == '' && $v['jinhao'] == '' && $v['yuanshichengbenjia'] == '' && $v['zhengshuhao'] == '' && $v['addtime'] == '') {
				unset($goods[$k]);
				continue;
			}

			$bill_info['goods_total'] += $v['yuanshichengbenjia'];
			// 加价率
			$goods[$k]['jiajialv'] = $this->getBillJiajiaInfoHard($v['cat_type1']);
			// 加价成本合计：
			$bill_info['goods_total_jiajia'] += $v['yuanshichengbenjia'] * (1 + $goods[$k]['jiajialv'] * 0.01);
		}

		try {
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
			$pdo->beginTransaction(); //开启事务

			// 主表：（直接审核）
			$sql = "INSERT INTO `warehouse_bill` (
			`bill_no`, `to_company_id`, `to_warehouse_id`,
			`from_company_id`,`goods_num`,`goods_total`,
			`goods_total_jiajia`,
			`bill_note`,`to_warehouse_name`,
			`to_company_name`,`from_company_name`,
			`create_user`,`create_time`,`bill_type`,`order_sn`
			, `bill_status`, `check_user`, `check_time`
			) VALUES (
			'', {$bill_info['to_company_id']}, {$bill_info['to_warehouse_id']},
			{$bill_info['from_company_id']}, {$bill_info['goods_num']}, {$bill_info['goods_total']},
			{$bill_info['goods_total_jiajia']},
			'{$bill_info['bill_note']}','{$bill_info['to_warehouse_name']}',
			'{$bill_info['to_company_name']}',
			'{$bill_info['from_company_name']}','{$bill_info['create_user']}',
			'{$bill_info['create_time']}', '{$bill_info['bill_type']}','{$bill_info['order_sn']}'
			,{$bill_info['bill_status']}, '{$bill_info['check_user']}', '{$bill_info['check_time']}'
			)";
			// file_put_contents('e:/debug.sql',$sql);
			$pdo->query($sql);
			$id = $pdo->lastInsertId();

			//创建单据编号
			$bill_no = $this->create_bill_no($bill_info['bill_type'], $id);
			$sql = "UPDATE `warehouse_bill` SET `bill_no`='{$bill_no}' WHERE `id`={$id}";
			// file_put_contents('e:/debug2.sql',$sql);
			$pdo->query($sql);

			//插入明细
			foreach ($goods as $gk => $gv) {
				//给金耗为空的赋值 0
				if ($gv['jinhao'] == '') {
					$gv['jinhao'] = 0;
				}

				$params = array($id, $bill_no, $bill_info['bill_type'], $gv['goods_id'],
					$gv['goods_sn'], $gv['goods_name'], 1, $gv['jinzhong'],
					$gv['caizhi'], $gv['yanse'], $gv['jingdu'], (int) $gv['jinhao'],
					$gv['yuanshichengbenjia'], $gv['zhengshuhao'], $gv['addtime']
					, $gv['jiajialv']);
				$place_holders = implode(',', array_fill(0, count($params), '?'));
				$sql = "INSERT INTO `warehouse_bill_goods` (
		  			`bill_id`, `bill_no`, `bill_type`, `goods_id`,
		  			`goods_sn`, `goods_name`, `num`, `jinzhong`,
		  			`caizhi`, `yanse`,`jingdu`,`jinhao`,
		  			`sale_price`, `zhengshuhao`, `addtime`
		  			, `jiajialv`
		  			) VALUES ({$place_holders}) ";
				$stmt = $pdo->prepare($sql);
				$isSuccess = $stmt->execute($params);
				if (!isSuccess) {
					print_r($pdo->errorInfo());
				}
			}

			//插入订单号+快递单号
			// $sql = "INSERT INTO `warehouse_bill_info_m` (`bill_id`,`ship_number`) VALUES ({$id}, '')";
			// $pdo->query($sql);

			//写入warehouse_bill_status信息
			$update_time = date('Y-m-d H:i:s');
			$ip = '';
			$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$id}, '{$bill_no}', 2, '{$update_time}', '{$bill_info['create_user']}', '{$ip}') ";
			$pdo->query($sql);

			// 结束
		} catch (Exception $e) {
			//捕获异常
			$pdo->rollback(); //事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); //开启sql语句自动提交
			return array('success' => 0, 'error' => 'Transaction fail!' . $goods_id_str);
		}
		$pdo->commit(); //如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); //开启sql语句自动提交

		// write detail log:
		foreach ($goods as $v) {
			$this->writeDetailLog("{$bill_no},{$v['goods_id']},{$v['goods_name']},{$v['yuanshichengbenjia']},{$v['jiajialv']},{$bill_info['from_company_name']},{$bill_info['to_company_name']},{$bill_info['to_warehouse_name']}");
		}

		return array('success' => 1, 'x_id' => $id, 'bill_no' => $bill_no, 'num' => count($goods), 'amount' => $bill_info['goods_total_jiajia']);
	}

	public function getVirtualWarehouse() {
		$info = array('company_id' => 58,
			'company_name' => '总公司',
			'warehouse_id' => NULL,
			'warehouse_name' => '');
		/*global $db;
					$sql  = "SELECT `id`, `name` FROM `warehouse` WHERE `code`=\"VIRTUAL001\"" ;
					$data = $db->getRow($sql);
					//var_dump($data);exit;
			        if(count($data) > 0) {
						$info['warehouse_id']   = $data['id'];
						$info['warehouse_name'] = $data['name'];
		*/
		return $info;
	}

	public function create_bill_no($type, $bill_id = '1') {
		$bill_id = substr($bill_id, -4);
		$bill_no = $type . date('Ymd', time()) . rand(100, 999) . str_pad($bill_id, 4, "0", STR_PAD_LEFT);
		return $bill_no;
	}

	/**
	 * 从已有的单中找对应货品的加价率，没有就取系统配置的（财务管理-->加价率调整）。
	 * @param  string $value [description]
	 * @return [type]        [description]
	 * // 0 - 加价，1 - 查M单历史纪录加价，2 - 不加价
	 */
	public function getBillJiajiaInfo($style_type, $jiajia_type, $goods_id) {
		global $db;
		if ($jiajia_type == 0) {
			return $this->getRisingRate($style_type);
		} else if ($jiajia_type == 1) {
			$sql = "SELECT bg.`jiajialv` FROM `warehouse_bill_goods` bg join warehouse_bill b on bg.bill_id=b.id
				WHERE bg.`bill_type`='M' AND bg.`goods_id`={$goods_id} and from_company_id=58 ORDER BY `bill_id` DESC limit 1";
			$jiajialv = $db->getOne($sql);
			return number_format($jiajialv, 2);
		}
		return 0;
	}

	// Hard Code:
	public function getBillJiajiaInfoHard($style_type) {
		if ($style_type == '金条') {
			return 5;
		}
		return 10;
	}

	/**
	 * 获取单个加价率
	 * @param  [char] $style_type [款式类型]
	 * @return [float]             [加价率]
	 */
	public function getRisingRate($style_type) {
		if (trim($style_type) == '') {
			$style_type = '其他';
		}

		global $db;
		$rate = 0;
		// status: 状态 1 未审核 2 已审核 3 已作废
		// active_date: 生效日期
		$sql = "SELECT `jiajialv` FROM `warehouse_bill_info_y_jiajialv` "
			. " WHERE `style_type_name` = \"%s\""
			. " AND `status` = 2"
			. " AND `active_date` <= \"%s\""
			. " ORDER BY `active_date` ASC;";

		$sql = sprintf($sql, $style_type, date("Y-m-d"));

		$data = $db->getRow($sql);
		//var_dump($data);exit;
		if (count($data) > 0) {
			$rate = (float) $data['jiajialv'];
		}
		return number_format($rate, 2);
	}

	/**
	 * 写操作日志
	 * @param unknown_type $file
	 * @param unknown_type $line
	 */
	function writeLog($line) {
		$string = "[" . date("Y-m-d H:i:s") . "] ";
		$string .= $line;
		if ($_SERVER['HTTP_USER_AGENT'] != NULL) {
			echo $string . "<br/>\n";
		} else {
			echo $string . "\n";
		}
		$string .= "\n";
		$tp = fopen(ROOT_PATH . "data/work.log", "a");
		fwrite($tp, $string);
		fclose($tp);
	}

	/**
	 * 写明细操作日志
	 * @param unknown_type $file
	 * @param unknown_type $line
	 */
	function writeDetailLog($line) {
		// $string = "[". date("Y-m-d H:i:s")."] ";
		$string = $line;
		$string .= "\n";
		$tp = fopen(ROOT_PATH . "data/work_detail.csv", "a");
		fwrite($tp, $string);
		fclose($tp);
	}

	public function readAlreadyDone() {
		$goods_list = array();
		$mFile = fopen(ROOT_PATH . "data/work_detail.csv", "r");
		while ($data = fgetcsv($mFile)) {
			$goods_no = $data[1];
			if (!in_array($goods_no, $goods_list)) {
				$goods_list[] = $goods_no;
			}

			// print_r($goods_list);
			/* foreach ($goods_list as $arr){
			    if ($arr[0]!=""){
			        echo $arr[0]."<br>";
			    }
			} */
		}

		fclose($mFile);
		return $goods_list;
	}

	

	public function getAllData() 
	{
		global $db;

		$exception = " AND company_id NOT IN(58, 500, 501, 189, 223,59,220,323,364,414,437) ";

		// 唯品会，漏掉的特殊货号：
		$sql1 = "SELECT
					g.*
				FROM warehouse_goods g 
				WHERE
					g.goods_id = '150702635736'
				{$exception}
				limit 1
				";
		$plus = $db->getAll($sql1);
		if(count($plus)>0){
			$plus[0]['bill_no'] = "none";
		}
		
		// -- 总公司到分公司，当时调拨中：（1430）
		$sql1 = "SELECT					
					g.*,
				    b.bill_no
				FROM
					warehouse_bill b
				LEFT JOIN warehouse_bill_goods bg ON bg.bill_id = b.id
				LEFT JOIN warehouse_goods g ON g.goods_id = bg.goods_id
				WHERE
					b.bill_type = 'M'
				AND b.bill_status = 2
				AND b.create_time <= '2015-12-31 23:59:59'
				AND b.check_time >= '2016-1-1 0:0:0'
				AND b.from_company_id = 58
				AND b.to_company_id != 58
				AND (g.is_on_sale=2 OR g.is_on_sale=3)
				{$exception}
				";
		$ary1 = $db->getAll($sql1);

		// -- 漏加价率的：(8)
		$sql2 = "SELECT
				g.*,
			     b.bill_no
			FROM
				warehouse_bill b
			LEFT JOIN warehouse_bill_goods bg ON bg.bill_id = b.id
			LEFT JOIN warehouse_goods g ON g.goods_id = bg.goods_id
			WHERE
				b.bill_type = 'M'
			AND b.bill_status = 2
			AND b.create_time >= '2016-1-1 0:0:0'
			AND b.check_time >= '2016-1-1 0:0:0'
			AND b.from_company_id = 58
			AND b.to_company_id != 58
			AND b.to_company_id !=223
			AND bg.jiajialv=0
			AND (g.is_on_sale=2 OR g.is_on_sale=3)
			{$exception}
			";
		$ary2 = $db->getAll($sql2);

		// -- 分公司到分公司，当时调拨中：（0）
		$sql3 = "SELECT
					g.*,
					b.bill_no
				FROM
					warehouse_bill b
				LEFT JOIN warehouse_bill_goods bg ON bg.bill_id = b.id
				LEFT JOIN warehouse_goods g ON g.goods_id = bg.goods_id
				WHERE
					b.bill_type = 'M'
				AND b.bill_status = 2
				AND b.create_time <= '2015-12-31 23:59:59'
				AND b.check_time >= '2016-1-1 0:0:0'
				AND b.from_company_id != 58
				AND b.to_company_id = b.from_company_id
				AND (g.is_on_sale=2 OR g.is_on_sale=3)
				{$exception}
				";
		$ary3 = $db->getAll($sql3);

		$ary_all = array();
		$ary_all = array_merge($ary_all, $plus);
		$ary_all = array_merge($ary_all, $ary1);
		$ary_all = array_merge($ary_all, $ary2);
		$ary_all = array_merge($ary_all, $ary3);

		$ary_final = array();
		foreach ($ary_all as $k => $v) {
			$billSTime = $this->getLastBillSCreateTime($v['goods_id'], $v['is_on_sale']);
			$v['check_time'] = ($billSTime) ? date("Y-m-d H:i:s", strtotime($billSTime) - 120) : date("Y-m-d H:i:s");
			$ary_final['GROUP_' . $v['company_id'] . '_' . $v['warehouse_id'].'_'.$v['check_time']][] = $v;
		}
		return $ary_final;
	}

	/**
	 * 获取某个货号最后的销售单的建单时间
	 */
	public function getLastBillSCreateTime($goods_id, $is_on_sale) {
		if ($is_on_sale!=3) {
			return "";
		}

		$sql = "SELECT b.create_time, b.bill_no, bg.goods_id
				FROM `warehouse_bill_goods` bg,
				`warehouse_bill` b
				WHERE bg.goods_id = '{$goods_id}' 
				and bg.bill_id = b.id 
				and bg.bill_type='S' 
				ORDER BY b.id asc
				;";
		global $db;
		$data = $db->getRow($sql);
		if (count($data) > 0) {
			$log = "goods:".$goods_id. " Status:".$is_on_sale." S Bill create time:". $data['create_time']. " ".$data['bill_no'];
			$this->writeLog($log);
			return $data['create_time'];
		}
		return "";
	}

}
