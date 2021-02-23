<?php
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require_once('MysqlDB.class.php');
set_time_limit(0);
ini_set('memory_limit','2000M');
$doTest = false;

if($doTest){
    $new_conf = [
        'dsn'=>"mysql:host=192.168.0.91;dbname=kucun_bak",
        'user'=>"root",
        'password'=>"123456",
        'charset' => 'utf8'
    ];
}else{
    $new_conf = [
        'dsn'=>"mysql:host=192.168.1.192;dbname=warehouse_shipping",
        'user'=>"cuteman",
        'password'=>"QW@W#RSS33#E#",
        'charset' => 'utf8'
    ];
}
$day = 1 ;
$t = time()-86400*$day;
$date = date("Y-m-d",$t);


$db = new MysqlDB($new_conf);
$sql= "select dotime from kucun_bak.product_info_gongchang where dotime = '{$date}'";
$dotime = $db->getOne($sql);

if($dotime){
    die("已执行成功");
}


$sql="insert into kucun_bak.product_info_gongchang
(
    a_id,dotime,id,bc_sn,p_id,p_sn,style_sn,status,buchan_fac_opra,num,prc_id,prc_name,opra_uname,
    add_time,esmt_time,rece_time,info,from_type,consignee,edit_time,order_time,
    remark,bc_style,goods_name,xiangqian,factory_opra_status,customer_source_id,channel_id,caigou_info,
    create_user,weixiu_status,is_peishi,buchan_times,is_alone,
    to_factory_time,oqc_pass_time,
    last_opra_uname,last_opra_remark,last_opra_time

)
select 
    NULL,'$date',p.id,p.bc_sn,p.p_id,p.p_sn,p.style_sn,p.status,p.buchan_fac_opra,p.num,p.prc_id,p.prc_name,p.opra_uname,
    p.add_time,p.esmt_time,p.rece_time,p.info,p.from_type,p.consignee,p.edit_time,p.order_time,
    p.remark,p.bc_style,p.goods_name,p.xiangqian,p.factory_opra_status,p.customer_source_id,p.channel_id,p.caigou_info,
    p.create_user,p.weixiu_status,p.is_peishi,p.buchan_times,p.is_alone,
    p.to_factory_time,p.oqc_pass_time,
    l.uname last_opra_uname,l.remark opra_remark,l.MaxTime last_opra_time 
from kela_supplier.product_info p
    LEFT JOIN (SELECT tl.uname,tl.time,tl.bc_id,tl.remark,ol.MaxTime FROM kela_supplier.product_opra_log tl inner join (SELECT bc_id, MAX(time) AS MaxTime FROM kela_supplier.product_opra_log GROUP BY bc_id  ) ol on  tl.bc_id =ol.bc_id AND tl.time=ol.MaxTime ) as l ON l.bc_id=p.id
   where 
	add_time like '$date%'
	OR esmt_time = '$date'
	OR oqc_pass_time like '$date%'
	OR order_time like '$date%'";
//die;
$res = $db->query($sql);

if($res){
    die("执行成功");
}else{
    die("执行失败");
}

