<?php
// 老款的款属性
error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");
//此文件是where条件
include 'style_where.php';

$conOld = mysqli_connect('192.168.1.55','style_zyy','KELAzhangyuanyuan123','kela_style');
$conNew =  mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');
$old_table = 'style_style';	
$new_table = "app_style_fee";
$date_time = date("Y-m-d H:i:s");
$field = "`style_id`, `style_sn`, `fee_type`, `price`, `status`, `check_user`, `check_time`";
//旧的数据
$sql = "SELECT count(*) FROM " . $old_table . "  WHERE `is_new` =0  and `style_sn`='KLSW028165' ";
$sql = "SELECT count(*) FROM " . $old_table . "  WHERE `is_new` =0  ".$where;
$t = mysqli_query($conOld,$sql);
$cnt = mysqli_fetch_row($t);

$len = $cnt[0];
$forsize = ceil($len / 1000);
for($ii = 1; $ii <= $forsize; $ii ++){
    echo $ii."----------------------------------------------------------"."\n";
    $offset = ($ii - 1) * 1000;
   // $sql = "SELECT *  FROM " . $old_style . "   WHERE `is_new`=0 and style_sn='KLRW028164' limit $offset,1000";
    $sql = "SELECT *  FROM " . $old_table . "   WHERE `is_new`=0 and `style_sn`='KLSW028165'  limit $offset,1000";
    $sql = "SELECT *  FROM " . $old_table . "   WHERE `is_new`=0  ".$where." limit $offset,1000";
    $res = mysqli_query($conOld,$sql);
    $old_style_data = array ();
    while ( $row = mysqli_fetch_array($res) ){
		$style_id = $row['style_id'];
		$style_sn = $row['style_sn'];
        
        //材质信息
        $metail_info = unserialize($row["metal_info"]);
        if(!empty($metail_info)){
            $select_18k = getCaiZhi($metail_info,2);//18k
            $select_pt950 = getCaiZhi($metail_info,4);//pt
           
            if(!empty($select_18k)){
                //材质属性
                $sql = "INSERT INTO `".$new_table."` (" . $field . ")  VALUES (" . $style_id . ", '" . $style_sn . "', 1,'0','1','admin' ,'".$date_time."')";
              //  echo $sql."\n";
               if(!mysqli_query($conNew,$sql)){
                    echo "\n\n\n**".$sql."\n\n\n";exit;
                }else {
                    echo mysqli_insert_id($conNew)."\n";
                }
            }
            
            if(!empty($select_pt950)){
                //材质属性
                $sql = "INSERT INTO `".$new_table."` (" . $field . ")  VALUES (" . $style_id . ", '" . $style_sn . "', 4,'0','1','admin' ,'".$date_time."')";
               // echo "----".$sql."\n";
               if(!mysqli_query($conNew,$sql)){
                    echo "\n\n\n".$sql."\n\n\n";exit;
                }else {
                    echo mysqli_insert_id($conNew)."\n";
                }
            }
        }
       // var_dump($metail_info);
    }
}
echo "\n\n\n\n完成ok！";
die();



function getCaiZhi( $data,$type){
    $_style_gold_type = array(
//        "1" => array("gold_name" => "9K", "price"=>"140", "gold_color"=>",1,2,3,", "loss"=>"0.15", "middle" => "10"),
//        "8" => array("gold_name" => "14K", "price"=>"217", "gold_color"=>",1,2,3,", "loss"=>"0.15", "middle" => "13"),
        "2" => array("gold_name" => "18K", "price"=>"280", "gold_color"=>",1,2,3,", "loss"=>"0.15", "middle" => "13"),
//        "3" => array("gold_name" => "PT900", "price"=>"460", "gold_color"=>",1,", "loss"=>"0.15", "middle" => "20"),
        "4" => array("gold_name" => "PT950", "price"=>"465", "gold_color"=>",1,", "loss"=>"0.15", "middle" => "20"),
//        "10" => array("gold_name" => "PT999", "price"=>"338.5", "gold_color"=>",1,", "loss"=>"0.15", "middle" => "20"),
//        "5" => array("gold_name" => "S925", "price"=>"", "gold_color"=>",1,"),
//        "6" => array("gold_name" => "S990", "price"=>"", "gold_color"=>",1,"),
//        "7" => array("gold_name" => "千足金", "price"=>"375", "gold_color"=>",2,"),//这个不属于素金
//        "9" => array("gold_name" => "千足银", "price"=>"", "gold_color"=>",1,")
    );
    
    $select_caizhi = array();
    foreach ($data as $key=>$val){
        if($val['selected'] ==1){
            if($key == $type){
                $select_caizhi[] = $_style_gold_type[$key]['gold_name'];
            }
        }
    }
    return $select_caizhi;
}

