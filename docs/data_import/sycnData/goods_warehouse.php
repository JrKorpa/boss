<?php 
/**
 *  -------------------------------------------------
 * 文件说明		商品柜位数据导入
 * @file		: warehouse_box.php
 * @date 		: 2015-03-07 17:29:11
 * @author		: yangxt <yangxiaotong@163.com
 *  -------------------------------------------------
*/
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
set_time_limit(0);
ini_set('memory_limit','2000M');
//新项目数据库
$new_conf = [
	'dsn'=>"mysql:host=192.168.1.93;dbname=warehouse_shipping",
	'user'=>"cuteman",
	'password'=>"QW@W#RSS33#E#",
];

$new_db = new PDO($new_conf['dsn'], $new_conf['user'], $new_conf['password'],array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));

//商品导入完毕后;只执行一次
/*--------------------------------*/
// $sql = "UPDATE `warehouse_goods` SET `old_set_w` = '0'";
// $res = $new_db->query($sql);
// if(!$res){echo "设置初始值失败";exit;}else{echo "设置初始值成功";exit;}

/*--------------------------------*/

$len = 50000;

 $sql = "SELECT `g`.`id`,`g`.`goods_id`,`g`.`warehouse_id`,`b`.`id` AS `box_id`
 FROM `warehouse_goods` AS `g`,`warehouse_box` AS `b` WHERE `g`.`company_id` = '58' AND `g`.`box_sn`=`b`.`box_sn` and `g`.`warehouse_id`=`b`.`warehouse_id` AND `g`.`old_set_w`='0' AND `g`.`is_on_sale` = '2'";

//$sql = "SELECT `g`.`id`,`g`.`goods_id`,`g`.`warehouse_id`,`b`.`id` AS `box_id` FROM `warehouse_goods` AS `g`,`warehouse_box` AS `b` WHERE `g`.`box_sn`=`b`.`box_sn` and `g`.`warehouse_id`=`b`.`warehouse_id` AND `g`.`old_set_w`='0' AND `g`.`is_on_sale` = '2'";
//$sql .= "AND `g`.`warehouse_id` IN (SELECT warehouse_id FROM warehouse_rel WHERE company_id = '58')";
if($len){
	$sql .= "LIMIT 0,$len";
}


// print_r($sql);exit;
$obj = $new_db->query($sql);
$obj->setFetchMode(PDO::FETCH_ASSOC);
$data = $obj->fetchAll();

// print_r($data[0]);exit;

if(empty($data)){
	echo "NOT DATA TO INSERT !!!\r\n";exit;
}

//设置默认值
foreach ($data as $key => $row) {
	foreach ($row as $k => $v) {
		$data[$key]['create_user'] = 'system';
		$data[$key]['add_time'] = date('Y-m-d H:i:s');
		$data[$key]['create_time'] = date('Y-m-d H:i:s');
	}
}

//预处理
$label = " (";$value = " (";
foreach ($data[0] as $k1 => $v1) {
	if($k1 == 'id'){continue;}
	if($k1 == 'goods_id'){$k1 = 'good_id';}
	$label .= "`".$k1."`,";
	$value .= ":".$k1.",";
}
$label = substr($label,0,-1);$label .= ") ";
$value = substr($value, 0,-1);$value .= ")";
$sql = "INSERT INTO `goods_warehouse`".$label."VALUES ".$value."";
$sql_1 = "INSERT INTO `goods_warehouse`".$label."VALUES";

// print_r($sql);exit;
$stmt = $new_db->prepare($sql);
//绑定参数
foreach ($data[0] as $key1 => $value11) {
	if($key1 == 'id'){continue;} 
	if($key1 == 'goods_id'){$key1 = 'good_id';}
    $stmt->bindParam(':'.$key1.'', $$key1,PDO :: PARAM_STR);
}

//绑定值
$sql_2 = " (";
$s = 0;$l = 0;
foreach ($data as $row) {

	foreach ($row as $k2 => $v2) {	
		if($k2 == 'id'){continue;}
		if($k2 == 'goods_id'){$k2 = 'good_id';}
	    $$k2 = "".$v2;
	    $sql_2 .= "'".$v2."',"; 
    }
	//插入数据
	$res = $stmt->execute();
	// /////////////////////////////
$sql_2 = substr($sql_2, 0,-1);$sql_2 .= ")";
$sql_test = $sql_1."".$sql_2;
 //print_r($sql_test);exit;

	/*---回写----------------*/
	if($res){
		$s++;
		$sql = "UPDATE `warehouse_goods` SET `old_set_w` = '1' WHERE `id` = ".$row['id'];
		$res = $new_db->query($sql);
		if(!$res){
			echo iconv('UTF-8', 'GBK', "LOSE UPDATE warehouse_goods ".$row['id']."\r\n");
			file_put_contents(__DIR__."/log/update/g_".date('Ymd_')."_sql.log",$sql."\r\n",FILE_APPEND);
		}else{
			echo iconv('UTF-8', 'GBK', "SUCCESS UPDATE warehouse_goods ".$row['id']."\r\n");
		}
		echo iconv('UTF-8', 'GBK', "SUCCESS INSERT ".$row['goods_id']." TO goods_warehouse ".$s." \r\n");$sql_2=" (";
	}else{
		$l++;
		$sql = "SELECT COUNT(*) FROM `goods_warehouse` WHERE `good_id` = '".$row['goods_id']."'";
		$obj = $new_db->query($sql);
		$obj->setFetchMode(PDO::FETCH_NUM);
		$has = $obj->fetch();
		if($has[0] > 0){
			$sql = "UPDATE `warehouse_goods` SET `old_set_w` = '1' WHERE `id` = ".$row['id'];
			$res = $new_db->query($sql);
		}else{
			echo iconv('UTF-8', 'GBK', "LOSE INSERT ".$row['goods_id']." TO goods_warehouse".$l."\r\n");$sql_2=" (";
			file_put_contents(__DIR__."/log/insert/g_LOSE_INSERT".date('Ymd_')."_sql.log",$sql_test."\r\n",FILE_APPEND);
		}
	}

}

echo iconv('UTF-8', 'GBK', "---===THE MISSION END===---\r\n");
echo iconv('UTF-8', 'GBK', "SUCCESS INSERT ".$s." LINE \r\n");
echo iconv('UTF-8', 'GBK', "LOSE INSERT ".$l." LINE \r\n");

file_put_contents(__DIR__."/log/insert/warehouse_goods.log",date('Y-m-d H:i:s')."\r\n",FILE_APPEND);





?>