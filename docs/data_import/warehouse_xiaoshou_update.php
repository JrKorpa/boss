<?php
/**
 *  -------------------------------------------------
 * 文件说明		订单总金额更新销售单价格
 * @file		:
 * @date 		: 2015/5/21
 * @author		: lyh
 *  -------------------------------------------------
*/
/*header("Content-type:text/html;charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require_once('MysqlDB.class.php');
set_time_limit(0);
ini_set('memory_limit','2000M');
*/

header("Content-type:text/html;charset=utf-8");
require_once('MysqlDB.class.php');



//仓储数据库
/*$conf_warehouse = [
	'dsn'     => "mysql:host=192.168.1.63;dbname=warehouse_shipping;",
	'user'    => "yangfuyou",
	'password'=> "yangfuyou1q2w3e",
	'charset' => 'utf8'
];
*/

$conf_warehouse = [
	'dsn'=>"mysql:host=192.168.1.93;dbname=warehouse_shipping",
	'user'=>"cuteman",
	'password'=>"QW@W#RSS33#E#",
	'charset' => 'utf8'
];
$db2 = new MysqlDB($conf_warehouse);

//1、查询订单号和金额  测试用
//$db1 = new MysqlDB($conf_order);
//$sql = "select o.bill_no,c.order_amount from base_order_info as o,app_order_account as c where o.id=c.order_id  and bill_no ='20150507116223' limit 0,60";
//$data = $db1->getAll($sql);//数组中替换为存放订单号和订单金额

/*
data  = array(
     '0'=>array('bill_no'=>'订单号','order_amount'=>'订单总金额'),
	  .............
	)
*/
$data = array(
array('bill_no'=>'S201506235525194', 'order_amount'=>'1539'),
array('bill_no'=>'S201506237075192', 'order_amount'=>'2379'),
array('bill_no'=>'S201506231894773', 'order_amount'=>'1929'),
array('bill_no'=>'S201506225144150', 'order_amount'=>'1529.04'),
array('bill_no'=>'S201506212423364', 'order_amount'=>'5628'),
array('bill_no'=>'S201506212303312', 'order_amount'=>'1599'),
array('bill_no'=>'S201506203302686', 'order_amount'=>'24000'),
array('bill_no'=>'S201506198541807', 'order_amount'=>'499'),
array('bill_no'=>'S201506199091804', 'order_amount'=>'499'),
array('bill_no'=>'S201506197931796', 'order_amount'=>'2775'),
array('bill_no'=>'S201506193301499', 'order_amount'=>'1788'),
array('bill_no'=>'S201506195441278', 'order_amount'=>'499'),
array('bill_no'=>'S201506194691002', 'order_amount'=>'499'),
array('bill_no'=>'S201506193060842', 'order_amount'=>'4330'),
array('bill_no'=>'S201506183570442', 'order_amount'=>'739'),
array('bill_no'=>'S201506182730415', 'order_amount'=>'320'),
array('bill_no'=>'S201506183059994', 'order_amount'=>'2839'),
array('bill_no'=>'S201506172279220', 'order_amount'=>'2099'),
array('bill_no'=>'S201506175778505', 'order_amount'=>'2395'),
array('bill_no'=>'S201506167198057', 'order_amount'=>'7239'),
array('bill_no'=>'S201506162707761', 'order_amount'=>'58.13'),
array('bill_no'=>'S201506162477629', 'order_amount'=>'2448'),
array('bill_no'=>'S201506167857545', 'order_amount'=>'22.4'),
array('bill_no'=>'S201506163817470', 'order_amount'=>'1856'),
array('bill_no'=>'S201506165697142', 'order_amount'=>'299'),
array('bill_no'=>'S201506132404950', 'order_amount'=>'1500'),
array('bill_no'=>'S201506122463806', 'order_amount'=>'2429'),
array('bill_no'=>'S201506111692837', 'order_amount'=>'85'),
array('bill_no'=>'S201506107520892', 'order_amount'=>'2699'),
array('bill_no'=>'S201506091810245', 'order_amount'=>'1525'),
array('bill_no'=>'S201506081389143', 'order_amount'=>'988'),
array('bill_no'=>'SS201506082249070', 'order_amount'=>'2198'),
array('bill_no'=>'S201506088268974', 'order_amount'=>'718'),
array('bill_no'=>'S201506085557786', 'order_amount'=>'2599'),
array('bill_no'=>'S201506084907776', 'order_amount'=>'1799'),
array('bill_no'=>'S201506065446750', 'order_amount'=>'2298'),
array('bill_no'=>'S201506052434912', 'order_amount'=>'16500')

	/*在此处写订单信息*/
	);

//2、根据订单号查询销售单，以订单总金额计算销售单总金额和货品销售价
foreach ($data as $key=>$val)
{
	$bill_no     = $val['bill_no'];
	$order_money = $val['order_amount'];
	$sql = "select wbg.id,wg.chengbenjia,wg.goods_id,wb.bill_no from warehouse_bill as wb,warehouse_bill_goods as wbg,warehouse_goods as wg where  wg.goods_id=wbg.goods_id and wbg.bill_id=wb.id and wb.bill_type ='S' and wb.bill_status =2 and wb.bill_no = '{$bill_no}'";
	$res = $db2->getAll($sql);
	$sql = "select sum(wg.chengbenjia) as all_price from warehouse_bill as wb,warehouse_bill_goods as wbg,warehouse_goods as wg where  wg.goods_id=wbg.goods_id and wbg.bill_id=wb.id and wb.bill_type ='S' and wb.bill_status =2 and wb.bill_no = '{$bill_no}'";
	//echo $sql."<br>";
	$all_price_goods = $db2->getOne($sql);

	if($res)
	{
		$price_sum_last = 0;//用于存除最后一个商品的所以商品成本价格总和
		for ($i=0;$i<count($res);$i++)
		{
			 if($i==count($res)-1)
			 {
				 $res[$i]['xiaoshoujia'] = $order_money-$price_sum_last;
			 }
			 else
			 {
				 $res[$i]['xiaoshoujia'] =  round(( $res[$i]['chengbenjia']*$order_money)/$all_price_goods,2);
				 $price_sum_last += $res[$i]['xiaoshoujia'];
			 }
			 $res[$i]['order_money'] = $order_money;
		}
		//修改销售单总金额和销售明细销售价格
		foreach ($res as $val)
		{
			$sql =  "update warehouse_bill_goods set xiaoshoujia='{$val['xiaoshoujia']}' where id={$val['id']}";
			file_put_contents("/data/www/cuteframe_boss/docs/data_import/lyh".date("Y-m-d-H-i-s").".txt",$sql."\r\n",FILE_APPEND);
			$ok = $db2->query($sql);
			if(!$ok)
			{
				echo $bill_no."对应销售单销售金额修改失败----货号".$val['goods_id']."\r\n";
			}
		}
		$sql = "update  warehouse_bill set xiaoshoujia='{$order_money}' where bill_no='{$bill_no}' and bill_status=2 ";
		file_put_contents("/data/www/cuteframe_boss/docs/data_import/lyh".date("Y-m-d-H-i-s").".txt",$sql."\r\n",FILE_APPEND);
		$ok = $db2->query($sql);
		if(!$ok)
		{
			echo $bill_no."对应销售单销售总金额修改失败\r\n";
		}else{
			echo $bill_no."\r\n";
		}
	}
	else
	{
		echo $bill_no."没有对应的销售单或者是货号不存在"."\r\n";
		file_put_contents("d:/lyh.txt",$bill_no."没有对应的销售单或者是货号不存在"."\r\n",FILE_APPEND);
	}
}
echo 'ok....';

?>