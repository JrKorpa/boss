<?php
/**
 *  -------------------------------------------------
 * 文件说明		商品状态复查
 * @file		: checkGoodsStatus.php
 * @date 		: 2015-04-26 16:13:31
 * @author		: yangxt <yangxiaotong@163.com>
 *  -------------------------------------------------
 */
header("Content-type:text/html;charset=utf-8");
require_once('00_DB.class.php');
date_default_timezone_set('Asia/Shanghai');
set_time_limit(0);
// ini_set('memory_limit','2000M');

$conf = [
    'dsn'=>"mysql:host=192.168.1.93;dbname=warehouse_shipping;charset=utf8",
    'user'=>"cuteman",
    'password'=>"QW@W#RSS33#E#",
];

$conf = [
    'dsn'=>"mysql:host=localhost;dbname=warehouse_shipping;charset=utf8",
    'user'=>"root",
    'password'=>"yangxt",
];

$db = new DB($conf);

$bill_type = 'L';
//创建临时表
// $sql = "SELECT g.goods_id,g.warehouse_id,b.bill_status FROM warehouse_bill_goods AS g,warehouse_bill AS b WHERE g.`bill_type` = '".$bill_type."' AND g.bill_id = b.id AND b.bill_status <> '3'";
// $sql = "CREATE TABLE `tmp_c1` AS ".$sql;
// $res = $db->query($sql);
$dict = [
	'is_on_sale'=>['1'=>'10','2'=>'3']
];

$sql = "SELECT t.`goods_id`,t.bill_status AS `is_on_sale`,t.`warehouse_id`,w.`name` AS warehouse,r.company_id,r.company_name AS company FROM `tmp_c1` AS t,`warehouse_rel` AS r,`warehouse` AS w
WHERE r.warehouse_id = t.warehouse_id AND t.warehouse_id = w.id";

$data = $db->getAll($sql);
//替换状态
$data = $db->replaceDict($data,$dict);
print_r($data[0]);exit;
foreach ($data as $k => $row) {
	$where = ['goods_id'=>array_shift($row)];

	$res = $db->autoExec($row,'warehouse_goods','UPDATE',$where);
    if($res){
        echo iconv('UTF-8', 'GBK', "SUCCESS UPDATE ".$where['goods_id']." [warehouse_goods]\r\n");
    }else{
        echo iconv('UTF-8', 'GBK', "LOSE UPDATE ".$where['goods_id']." [warehouse_goods]\r\n");
    }
}




?>