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
        'dsn'=>"mysql:host=192.168.1.59;dbname=warehouse_shipping",
        'user'=>"cuteman",
        'password'=>"QW@W#RSS33#E#",
        'charset' => 'utf8'
    ];
}

$day = 1 ;
$t = time()-86400*$day;
$date = date("Y-m-d",$t);

$db = new MysqlDB($new_conf);


$sql= "select dotime from kucun_bak.product_info_wait_diamond where dotime = '{$date}'";
$dotime = $db->getOne($sql);

if($dotime){
    die("已执行成功");
}

$sql="insert into kucun_bak.product_info_wait_diamond
(
    a_id,dotime,id,bc_sn,p_id,p_sn,style_sn,status,buchan_fac_opra,num,prc_id,prc_name,opra_uname,
    add_time,esmt_time,rece_time,info,from_type,consignee,edit_time,order_time,
    remark,bc_style,goods_name,xiangqian,factory_opra_status,customer_source_id,channel_id,caigou_info,
    create_user,weixiu_status,is_peishi,buchan_times,is_alone,dengzuan_jiejiari,wd_day,
    wait_dia_starttime,wait_dia_endtime,wait_dia_finishtime,diamond_type,qiban_type
)
select 
    NULL,'$date',p.id,p.bc_sn,p.p_id,p.p_sn,p.style_sn,p.status,p.buchan_fac_opra,p.num,p.prc_id,p.prc_name,p.opra_uname,
    p.add_time,p.esmt_time,p.rece_time,p.info,p.from_type,p.consignee,p.edit_time,p.order_time,
    p.remark,p.bc_style,p.goods_name,p.xiangqian,p.factory_opra_status,p.customer_source_id,p.channel_id,p.caigou_info,
    p.create_user,p.weixiu_status,p.is_peishi,p.buchan_times,p.is_alone,0,0,
    wait_dia_starttime,wait_dia_endtime,wait_dia_finishtime,IF(diamond_type is null,0,diamond_type),qiban_type
from kela_supplier.product_info p
   where 
	p.wait_dia_starttime !='0000-00-00 00:00:00'
    ";
//die;
$res = $db->query($sql);

//from_type 1:采购单 2:客订单
//order_type : 1:客订单 2:采购单
$sql = "
   SELECT a_id,prc_id,wait_dia_starttime,wait_dia_endtime,from_type
   FROM kucun_bak.product_info_wait_diamond
   WHERE 
        dotime = '{$date}'
        AND wait_dia_endtime like '{$date}%'
        AND wait_dia_endtime not like '0000-00-00%'
   ;
";
$bcList = $db->getAll($sql);

$sql="SELECT 
    p.id,pwt.order_type,holiday_time 
FROM 
    kela_supplier.`app_processor_info` p
    inner join kela_supplier.`app_processor_worktime` pwt on p.id = pwt.processor_id
where 
    pwt.order_type in (1,2)";
$fangList = $db->getAll($sql);
foreach($fangList as $key => $val){
    $from_type = $val['order_type']==1?2:1;
    $fangjiaList[$val['id']][$from_type]['holiday'] = explode(';',$val['holiday_time']);
}

foreach($bcList as $key => $val){
    $i = 0 ;
    $a_id = $val['a_id'];
    $prc_id = $val['prc_id'];
    $from_type = $val['from_type'];//from_type 1:采购单 2:客订单
    $wait_dia_starttime = $val['wait_dia_starttime'];
    $wait_dia_endtime = $val['wait_dia_endtime'];
    $add_timestamp = strtotime($val['wait_dia_starttime']);
    $rece_timestamp = strtotime($val['wait_dia_endtime']);



    if(isset($fangjiaList[$prc_id]) && isset($fangjiaList[$prc_id][$from_type]) && isset($fangjiaList[$prc_id][$from_type]['holiday'])){
        $fj_prc = $fangjiaList[$prc_id][$from_type]['holiday'];

        $start_date = substr($val['wait_dia_starttime'],0,10);
        $end_date = substr($val['wait_dia_endtime'],0,10);

        $list = get_data_arr($start_date,$end_date);
        array_pop($list);
        array_shift($list);
        if(!empty($list)){
            foreach($list as $date => $val){
                if(in_array($val,$fj_prc)){
                    $i++;
                }
            }
        }
    }
    //echo "<hr>";
    //var_dump($rece_timestamp,$add_timestamp);
    //var_dump($wait_dia_finishtime,$add_time);
    $li = 0;
    $day = 0;
    if($rece_timestamp && $add_timestamp ){
         $li = $rece_timestamp - $add_timestamp;
         $li -= $i*68400;
         $day = round($li/86400,2);
    }
    $sql = "UPDATE kucun_bak.product_info_wait_diamond SET dengzuan_jiejiari = $i,wd_day = $day where a_id = $a_id;";
    //echo $sql;die;
    $db->query($sql);
}

//var_dump($bcList);


if($res){
    die("执行成功");
}else{
    die("执行失败");
}

//ALTER TABLE `product_info_wait_diamond` ADD `dengzuan_jiejiari` INT(10) NOT NULL COMMENT '等钻中的节假日' ;   
//ALTER TABLE `product_info_wait_diamond` ADD `wd_day` DOUBLE(6,4) UNSIGNED NOT NULL COMMENT '等钻时长' ;

function get_data_arr($start_time,$end_time){
    $start_time_str=explode("-",$start_time);
    $end_time_str=explode("-",$end_time);

    $data_arr=array();
    while(true){
        if($start_time_str[0].$start_time_str[1].$start_time_str[2]>$end_time_str[0].$end_time_str[1].$end_time_str[2]) break;
        $data_arr[$start_time_str[0]."-".$start_time_str[1]."-".$start_time_str[2]]=$start_time_str[0]."-".$start_time_str[1]."-".$start_time_str[2];
        $start_time_str[2]++;
        $start_time_str=explode("-",date("Y-m-d",mktime(0,0,0,$start_time_str[1],$start_time_str[2],$start_time_str[0])));
    }
    krsort($data_arr);
    return $data_arr;
}
