<?php
set_time_limit ( 0 );
error_reporting(E_ALL);//错误报告设置
ini_set('display_errors',1);//是否显示PHP错误信息，1显，0不显;
date_default_timezone_set ( "PRC" );
$mysqli = new mysqli ( '192.168.1.59', 'cuteman', 'QW@W#RSS33#E#', 'warehouse_shipping' ) or die ( "数据库连接失败！" );
//$mysqli = new mysqli ( '192.168.0.91', 'root', '123456', 'warehouse_shipping' ) or die ( "数据库连接失败！" );
$mysqli->query("set names utf8");
$now_date = date("Y-m-d");
$up_shelf_warehouse=array(
		'线上低值库',
		'线上钻饰库',
		'线上混合库',
		'黄金网络库',
		'主站库',
		'淘宝黄金',
		'淘宝素金',
		'京东黄金',
		'京东素金',
		'彩宝库',
		'深圳珍珠库',
		'银行库',
		'B2C库',
		'婚博会备货库',
		'总公司后库',
		'总公司店面配货库',
		'黄金店面库',
);
$warehouse_list = '';
//$now_date = "2015-8-4";
$now_time=strtotime($now_date);
$three_day_ago=date('Y-m-d H:i:s',$now_time-3600*24*3);
foreach ( $up_shelf_warehouse as $val ) {
	$warehouse_list .= "'{$val}',";
}
//判断当天是否已执行过任务
$sql="select 1 from warehouse_statistic where dt='{$now_date}'";
$has_result=mysqli_getall($sql);
$has_result=count($has_result);
if($has_result) exit;//已经执行过，则退出
$warehouse_list = trim ( $warehouse_list, ',' );
$sql = "select w.warehouse ,w.warehouse_id, COUNT(*) AS cnt,SUM(w.chengbenjia) AS all_price from warehouse_goods w where  warehouse in ({$warehouse_list}) and is_on_sale=2 group by warehouse";
$kucun =  mysqli_getall($sql);
if($kucun){
	foreach ( $kucun as $k ) {
		$kucun_warehouse = $k ['warehouse_id'];
		$kucun_cnt = $k ['cnt'];
		$kucuns [$kucun_warehouse] = $kucun_cnt;
		$all_prices [$kucun_warehouse] = $k ['all_price'];
	}
}
// 上柜的产品
//$sql = "select w.warehouse ,w.warehouse_id, COUNT(*) AS cnt,SUM( w.chengbenjia) as price from goods_warehouse g inner join warehouse_goods w on g.good_id=w.goods_id   where  w.warehouse in ({$warehouse_list}) and w.is_on_sale=2 group by w.warehouse";
$sql = "select w.warehouse ,w.warehouse_id, COUNT(*) AS cnt,SUM( w.chengbenjia) as price from warehouse_goods w  where  w.warehouse in ({$warehouse_list}) and w.is_on_sale=2 and w.box_sn<> '0-00-0-0' group by w.warehouse";
$kucun_no_cab = mysqli_getall($sql);
if($kucun_no_cab){
	foreach ( $kucun_no_cab as $val ) {
		$wh_name = $val ['warehouse'];
		$wh_id = $val ['warehouse_id'];
		$kucun_cnt = $kucuns [$wh_id];
		$kucun_no_cab_cnt = $val ['cnt'];
		$all_price = $all_prices [$wh_id];
		$diff_price = $all_price - $val ['price'];
		$kucun_cab_cnt = $kucun_cnt - $kucun_no_cab_cnt;
		if ($kucun_cab_cnt) {
			// 有差价，则判断是否有超三天未上架
			$sql = "select w.warehouse ,w.warehouse_id, COUNT(*) AS cnt,SUM( w.chengbenjia) as price from warehouse_goods w  where  w.warehouse_id='{$wh_id}' and w.addtime<'{$three_day_ago}' and w.is_on_sale=2 and w.box_sn='0-00-0-0' group by w.warehouse";
			$kucun_no_cab = mysqli_getall($sql);
			$kucun_no_cab=current($kucun_no_cab);
			$threeday_cab_num = $kucun_no_cab['cnt'];
			$threeday_diff_price = $kucun_no_cab['price'];
		} else {
			$threeday_cab_num = 0;
			$threeday_diff_price = 0;
		}
		$sql = "INSERT INTO warehouse_statistic (`dt`, `wh_name`, `wh_id`, `total_num`, `cab_num`,`all_price`,`diff_price`, `threeday_cab_num`,`threeday_diff_price`) VALUES ('$now_date', '$wh_name', '$wh_id', '$kucun_cnt', '$kucun_cab_cnt','" . $all_price . "','$diff_price','$threeday_cab_num','$threeday_diff_price')";
		$mysqli->query ( $sql );
	}
}
function mysqli_getall($sql){
	global $mysqli;
	$result = $mysqli->query($sql);
	$return=array();
	if($result){
		while($row =$result->fetch_assoc() )
		{  //循环输出结果集中的记录
			$return[] = $row;
		}
	}
	return $return;
}
?>