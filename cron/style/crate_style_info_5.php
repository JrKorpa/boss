<?php
//v3款式信息
error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");
// define('ROOT_PATH',str_replace('crate_style_info.php', '', str_replace('\\', '/', __FILE__)));
include('Pinyin.class.php');
//require_once(ROOT_PATH . 'config/shell_config.php');
$pinyin_obj = new Pinyin();

$conOld = mysqli_connect('192.168.1.55','style_zyy','KELAzhangyuanyuan123','kela_style');
$conNew =  mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#');


//旧款式的属性字段对应新的属性字段
$old_attr_new_attr = array(
    'style_sn'=>'style_sn',
    'style_name'=>'style_name',
    'pro_line'=>'product_type',
    'style_cat'=>'style_type',//v3的数据都是戒指
    'create_time'=>'create_time',
    'last_update'=>'modify_time',
    'zuhe_time'=>'cancel_time',
    'is_confirm'=>'check_status',
    'is_chaihuo'=>'dismantle_status',
);

$table = 'base_style_info';
$t_sql = "TRUNCATE TABLE front.$table ";
$t_res = mysqli_query($conNew, $t_sql);

//获取原来款式基本信息
$old_table = 'style_style';
$sql = "SELECT count(*) FROM kela_style." . $old_table . "  ";
$t = mysqli_query($conOld,$sql);
$cnt = mysqli_fetch_row($t);

$len = $cnt[0];
$forsize = ceil($len / 1000);
for($ii = 1; $ii <= $forsize; $ii ++){
    $offset = ($ii - 1) * 1000;
    $sql = "SELECT * FROM kela_style." . $old_table . "   limit $offset,1000";
    $res = mysqli_query($conOld, $sql);
    $old_data = array();
    while ($row = mysqli_fetch_array($res)){
        $old_data[]= $row;
    }
    
    $style_cat = array(	"戒指"=>2,	"吊坠",	"项链",	"耳钉",	"耳环",	"耳坠",	"手镯",	"手链",	"其他");
    $field = " `style_sn`, `style_name`, `product_type`, `style_type`, `create_time`, `modify_time`, `cancel_time`, `check_status`, `is_sales`, `is_made`, `dismantle_status`, `style_status`, `style_remark`";
    
    foreach ($old_data as $key=>$val){
    
        $style_name = $val['style_name'];
        $editor1 =$val['editor1'];
        $is_new = $val['is_new'];
    
        $product_type= 6;//钻石
        $cat_type =  2;//戒指
    
        if($is_new ==0){
            $product_type= $val['pro_line'];
            $cat_type =  $val['style_cat'];
            $editor1 =$val['simple_desc'];
        }
        $sql= "INSERT INTO front.`".$table."` (".$field.")  VALUES ( '".$val['style_sn']."',  '".$style_name."', '".$product_type."',"
                . " '".$cat_type."', '".$val['create_time']."', '".$val['last_update']."', '".$val['zuofei_time']."', '".$val['is_confirm']."',1,1, '".$val['is_chaihuo']."',1,'".$editor1."')" ;
        if(!mysqli_query($conNew, $sql)){
            echo $sql."\n\n";
        }else{
            echo mysqli_insert_id($conNew)."\n\n";
        }
        
    }
}
//款式基本信息

echo '基础数据已经ok';
die;

