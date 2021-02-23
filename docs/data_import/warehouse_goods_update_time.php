<?php
	/**
	* 获取货品的最后单据和新老状态---JUAN
 	*/
 	# 初始化数据
	header("Content-type:text/html;charset=utf-8");
	date_default_timezone_set('Asia/Shanghai');
	require_once('MysqlDB.class.php');
	set_time_limit(0);
	ini_set('memory_limit','2000M');

 	# 连接数据库

//新项目数据库
$new_conf = [
	'dsn'=>"mysql:host=192.168.1.93;dbname=warehouse_shipping",
	'user'=>"cuteman",
	'password'=>"QW@W#RSS33#E#",
		'charset' => 'utf8'
];

// 旧项目数据库
$old_conf = [
	'dsn'=>"mysql:host=192.168.1.79;dbname=jxc",
	'user'=>"root",
	'password'=>"zUN5IDtRF5R@",
		'charset' => 'utf8'
];

$newStatus = array(
	'' => '无',
	'100' => '锁定',
	'1'	=> '收货中',
	'2'	=> '库存',
	'3' => '已销售',
	'4' => '盘点中',
	'5' => '调拨中',
	'6' => '损益中',
	'7' => '已报损',
	'8' => '返厂中',
	'9' => '已返厂',
	'10' => '销售中',
	'11' => '退货中',
	'12' => '作废'
);

$oldStatus = array(
	'' => '无',
	'100' => '锁定',
	'0'	=> '收货中',
	'1'	=> '库存',
	'2'	=> '已销售',
	'3' => '转仓中',
	'4' => '盘点中',
	'5' => '销售中',
	'7' => '已返厂',
	'8' => '退货中',
	'9' => '返厂中',
	'10' => '作废',
	'11' => '损益中',
	'12' => '已报损'
);
 	$newdb = new MysqlDB($new_conf);
 	$olddb = new MysqlDB($old_conf);

	$sql = "select goods_id from warehouse_goods limit 53153,20000";
	$arr = $newdb->getAll($sql);
	unlink(__DIR__."/goods_log/____goods.csv");
	foreach($arr as $key => $val)
	{

		$con = "";
		//根据收货单、其他收货单的create_time洗数据---新系统
		$sql = "SELECT goods_id,wb.bill_no,wb.check_time,wb.create_time	FROM warehouse_bill AS wb, warehouse_bill_goods AS wbg	WHERE wbg.bill_id = wb.id AND wb.bill_type in ('T','L') and wbg.goods_id='{$val['goods_id']}' order by wb.id asc limit 1 ";
		$new_arr_a = $newdb->getRow($sql);

		//根据单据(L/T/ M/ D/H)已审核状态，取最新的单据的审核时间的洗到change_time中--新系统
		$sql = "SELECT goods_id,wb.bill_no,wb.check_time,wb.create_time	FROM warehouse_bill AS wb, warehouse_bill_goods AS wbg	WHERE wbg.bill_id = wb.id AND wb.bill_type in ('L','T','M', 'D','H') and wbg.goods_id='{$val['goods_id']}' and wb.bill_status=2 order by wb.check_time desc limit 1 ";
		$new_arr_c = $newdb->getRow($sql);

		// 老系统 L/T/F/B/H
		$sql = "select goods_id,concat(o.type,o.order_id) as bill_no,o.addtime,o.checktime from jxc_order as o,jxc_order_goods as og where o.order_id = og.order_id and o.type in ('T','L') and og.goods_id = '{$val['goods_id']}' order by o.order_id asc limit 1";
		//echo $sql;exit;
		$old_arr_a = $olddb->getRow($sql);

		$sql = "select goods_id,concat(o.type,o.order_id) as bill_no,o.addtime,o.checktime from jxc_order as o,jxc_order_goods as og where o.order_id = og.order_id and o.type in ('L','T','F','B','H') and og.goods_id = '{$val['goods_id']}' and o.status=2 order by o.checktime desc limit 1";
		$old_arr_c = $olddb->getRow($sql);echo '4';

		//如果新系统有数据则按照新系统、如果新系统没数据则按照老系统数据  --addtime
		$addtime = '0000-00-00 00:00:00';
		if (!empty($new_arr_a))
		{
			$addtime = $new_arr_a['create_time'];
		}
		elseif(!empty($old_arr_a))
		{
			$addtime = $new_arr_a['addtime'];
		}
		else
		{
			$con = $val['goods_id'].'找不到单据\n';
			file_put_contents(__DIR__."/goods_log/____goods.csv",$con,FILE_APPEND);
		}

		$change_time_o  = '0000-00-00 00:00:00';
		$change_time_n  = '0000-00-00 00:00:00';
		if (!empty($new_arr_c))
		{
			$change_time_o = $new_arr_c['check_time'];
		}
		if(!empty($old_arr_c))
		{
			$change_time_n = $old_arr_c['checktime'];
		}

		$change_time = $change_time_o >= $change_time_n ? $change_time_o:$change_time_n;
		if($addtime == '0000-00-00 00:00:00' || empty($addtime))
		{
			$sql = "update warehouse_goods set change_time='{$change_time}' where goods_id = {$val['goods_id']}";
		}else{
			$sql = "update warehouse_goods set addtime='{$addtime}',change_time='{$change_time}' where goods_id = {$val['goods_id']}";
		}
		echo $sql."\n";
		$newdb->query($sql);
		$con = '货号'.$val['goods_id'].'，修改--总库龄时间:'.$addtime.',本库库龄:'.$change_time;
		file_put_contents(__DIR__."/goods_log/____goods.csv",$con,FILE_APPEND);

		echo $key.'-------------'.$con."\n";

	}
echo '------------over';exit;

 ?>