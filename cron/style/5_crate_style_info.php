<?php
//v3款式信息
error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");
//此文件是where条件
include 'style_where.php'; 

$conOld = mysqli_connect('192.168.1.55','style_zyy','KELAzhangyuanyuan123','kela_style');
$conNew =  mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');

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

$new_pro_line = array (
    0 => 25, // 其他(原名)
    1 => 14,
    2 => 4,
    3 => 7, // 黄金饰品(原名)
    4 => 6, // 结婚钻石饰品
    5 => 6,  // 5 => array(item_name => 钻石饰品),
    6 => 15,
    7 => 17, // 彩宝及翡翠饰品(原名)
    8 => 6,
    9 => 16,
    10 => 10,
    11 => 12 
);
$new_cat_type_arr=array(1=>"戒指",2=>3,3=>4,4=>5,5=>5,6=>5,7=>6,8=>7,9=>9,13=>8,10=>14,11=>14,12=>14);//新项目中款式分类的id
$cat_jiezhi_arr = array("W"=>2,"M"=>10,"X"=>11);//新项目中款式分类的id：2=>"女戒",10=>"男戒",11=>"情侣戒"

$table = 'base_style_info';
//$t_sql = "TRUNCATE TABLE front.$table ";
//$t_res = mysqli_query($conNew, $t_sql);

//获取原来款式基本信息
$old_table = 'style_style';
$sql = "SELECT count(*) FROM kela_style." . $old_table . " where  style_sn= 'KLPW000170'  ";

$sql = "SELECT count(*) FROM kela_style." . $old_table . " where 1  ".$where ;
$t = mysqli_query($conOld,$sql);
$cnt = mysqli_fetch_row($t);

$len = $cnt[0];
$forsize = ceil($len / 1000);
for($ii = 1; $ii <= $forsize; $ii ++){
    $offset = ($ii - 1) * 1000;
$sql = "SELECT * FROM kela_style." . $old_table . "  where 1  ".$where  ." limit $offset,1000";
    $res = mysqli_query($conOld, $sql);
    $old_data = array();
    while ($row = mysqli_fetch_array($res)){
        $old_data[]= $row;
    }
    
    $field = " `style_id`,`is_new`,`style_sn`, `style_name`, `product_type`, `style_type`, `create_time`, `modify_time`, `cancel_time`, `check_status`, `is_sales`, `is_made`, `dismantle_status`, `style_status`, `style_remark`";
    
    foreach ($old_data as $key=>$val){
        $style_id = $val['style_id'];
        $style_name = $val['style_name'];
        $style_sn = $val['style_sn'];
        $editor1 =$val['editor1'];
        $is_new = $val['is_new'];
        
        if($is_new ==0){
            $old_product_type= $val['pro_line'];
            $old_cat_type =  $val['style_cat'];
            $style_sex =  $val['style_sex'];//2女 1男 0中性
//            var_dump($old_product_type,$old_cat_type);
//            var_dump($new_cat_type_arr);
//            var_dump($new_cat_type_arr);
            
             //如果是戒指，需要区分一下男戒:M，女戒:W，情侣戒:X
            if($old_cat_type == 1){
               $sex =  strtoupper(substr($style_sn,3,1));//KLPW000001
               $cat_type = $cat_jiezhi_arr[$sex];
            }else{
               $cat_type = $new_cat_type_arr[$old_cat_type];
            }

            //如果产品线是素金=>2 ,需要根据金托信息来对产品线
            if($old_product_type == 2){
                 $metail_info = unserialize($val["metal_info"]);
                 $product_type = get_sujin_cat_type($metail_info);
                 if(empty($product_type)){
                     $product_type = 4;//新项目素金 4
                 }
            }else{
                $product_type = $new_pro_line[$old_product_type];
            }
            $editor1 =$val['simple_desc'];
            
        }else if($is_new ==  1){//新款
             //style_for_cat ：1男戒;2女戒；3情侣戒
            $sex = $val['style_for_cat'];
            //新项目 10男戒;2女戒；11情侣戒
            $cat_type_arr = array(1=>10,2=>2,3=>11); 
            $cat_type = $cat_type_arr[$sex]; 
            $product_type= 6;
            
        }else if($is_new ==2){
            $product_type = 6;
            $cat_type = 2;
        }
        if(empty($product_type)){
            $product_type =20;
        }
        if(empty($cat_type)){
            $cat_type= 20;
        }
        
        $sql = "select style_id from  `".$table."` where `style_id`='".$style_id."'";
        $res = mysqli_query($conNew, $sql);
        $row = mysqli_fetch_row($res);
        if($row){
            echo $val['style_sn']."\n\n";
            $sql = " update `".$table."` set style_sn='".$style_sn."',style_name='".$style_name."' ,product_type =$product_type,style_type=$cat_type where style_id=".$style_id;
        }else{
            // $sql= "INSERT INTO `".$table."` (".$field.")  VALUES ( $style_id,'".$is_new."','".$val['style_sn']."',  '".$style_name."', '".$product_type."',"
           //         . " '".$cat_type."', '".$val['create_time']."', '".$val['last_update']."', '".$val['zuofei_time']."', '".$val['is_confirm']."',1,1, '".$val['is_chaihuo']."',1,'".$editor1."')" ;
            $sql= "INSERT INTO `".$table."` (".$field.")  VALUES ( $style_id,'".$is_new."','".$style_sn."',  '".$style_name."', '".$product_type."',"
                . " '".$cat_type."', '".$val['create_time']."', '".$val['last_update']."', '".$val['zuofei_time']."', 3,1,1,1,1,'".$editor1."')" ;
        }
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

// 根据金托信息判断此素金属于哪种类型：8  K金 ,9  PT; 19  银
//旧的数据：k金：1,8,2; pt:3,4,10; 银:5,6,9
function get_sujin_cat_type($data){
    if($data[1]['selected'] ==1 || $data[8]['selected'] ==1 || $data[2]['selected'] ==1){
        return 8;
    }
    if($data[3]['selected'] ==1 || $data[4]['selected'] ==1 || $data[10]['selected'] ==1){
       return 9; 
    }
    if($data[5]['selected'] ==1 || $data[6]['selected'] ==1 || $data[9]['selected'] ==1){
        return 19;
    }
}


//新的：8  K金 ,9  PT; 19  银
$_style_gold_type = array(
	"1" => array("gold_name" => "9K金", "price"=>"140", "gold_color"=>",1,2,3,", "loss"=>"0.15", "middle" => "10"),
	"8" => array("gold_name" => "14K金", "price"=>"217", "gold_color"=>",1,2,3,", "loss"=>"0.15", "middle" => "13"),
	"2" => array("gold_name" => "18K金", "price"=>"280", "gold_color"=>",1,2,3,", "loss"=>"0.15", "middle" => "13"),
	"3" => array("gold_name" => "PT900", "price"=>"460", "gold_color"=>",1,", "loss"=>"0.15", "middle" => "20"),
	"4" => array("gold_name" => "PT950", "price"=>"465", "gold_color"=>",1,", "loss"=>"0.15", "middle" => "20"),
	"10" => array("gold_name" => "PT999", "price"=>"338.5", "gold_color"=>",1,", "loss"=>"0.15", "middle" => "20"),
	"5" => array("gold_name" => "S925", "price"=>"", "gold_color"=>",1,"),
	"6" => array("gold_name" => "S990", "price"=>"", "gold_color"=>",1,"),
	"7" => array("gold_name" => "千足金", "price"=>"375", "gold_color"=>",2,"),//这个不属于素金
	"9" => array("gold_name" => "千足银", "price"=>"", "gold_color"=>",1,")
);
