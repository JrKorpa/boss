<?php 
/**
 *  -------------------------------------------------
 * 文件说明		仓库 数据导入
 * @file		: warehouse.php
 * @date 		: 2015-3-10 16:13:31
 * @author		: yangxt <yangxiaotong@163.com
 *  -------------------------------------------------
*/
header("Content-type:text/html;charset=utf-8");
require_once('00_DB.class.php');
date_default_timezone_set('Asia/Shanghai');
set_time_limit(0);
ini_set('memory_limit','2000M');

$conf = [
	'dsn'=>"mysql:host=192.168.1.93;dbname=warehouse_shipping;charset=utf8",
	'user'=>"cuteman",
	'password'=>"QW@W#RSS33#E#",
];

$db = new DB($conf);

$is_sale=['1'=>'0','2'=>'1','3'=>'0','4'=>'1','5'=>'1','6'=>'0','7'=>'0','8'=>'0','9'=>'0','10'=>'0','11'=>'0','12'=>'0'];

//$sql = "SELECT w.goods_id,w.is_on_sale AS is_sale FROM `warehouse_goods` AS w WHERE EXISTS (SELECT 1 FROM `tmp_goods` AS t WHERE t.goods_id = w.goods_id)";
$sql = 'SELECT `goods_id` FROM `tmp_goods`';
$data = $db->getAll($sql);

foreach ($data as $k=>$row) {
    $sql = 'SELECT `is_on_sale` FROM `warehouse_goods` WHERE `goods_id` = '.$row['goods_id'];
    $res = $db->getOne($sql);
    $data[$k]['is_sale'] = $is_sale[$res];
}

$db->selectDB('front');
foreach ($data as $row) {
    $where = ['goods_id'=>array_shift($row)];
    //print_r($row);print_r($where);exit;
    $res = $db->autoExec($row,'base_salepolicy_goods','UPDATE',$where);
    if($res){
        echo iconv('UTF-8', 'GBK', "SUCCESS UPDATE ".$where['goods_id']." [base_salepolicy_goods]\r\n");
    }else{
        echo iconv('UTF-8', 'GBK', "LOSE UPDATE ".$where['goods_id']." [base_salepolicy_goods]\r\n");
    }
}
//UPDATE END
echo iconv('UTF-8', 'GBK', "---===THE UPDATE MISSION END===---\r\n");
echo iconv('UTF-8', 'GBK', "---===THE UPDATE MISSION END===---\r\n");
echo iconv('UTF-8', 'GBK', "---===THE UPDATE MISSION END===---\r\n");




?>