<?php
// v2,v3 的款属性
error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");
//此文件是where条件
include 'style_where.php';

$conOld = mysqli_connect('192.168.1.55','style_zyy','KELAzhangyuanyuan123','kela_style');
$conNew =  mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');

$old_style = 'style_style';	
$new_table = "app_style_fee";

$field = "`style_id`, `style_sn`, `fee_type`, `price`, `status`, `check_user`, `check_time`";
//旧的数据
$old_table = 'style_style';
//$sql = "SELECT count(*) FROM " . $old_table . "  WHERE `is_new` =0 and style_sn='KLRW028164' ";
$sql = "SELECT count(*) FROM " . $old_table . "  WHERE `is_new` !=0  ".$where;
$t = mysqli_query($conOld,$sql);
$cnt = mysqli_fetch_row($t);
$date_time = date("Y-m-d H:i:s");
$len = $cnt[0];
$forsize = ceil($len / 1000);
for($ii = 1; $ii <= $forsize; $ii ++){
    echo $ii."----------------------------------------------------------"."\n";
    $offset = ($ii - 1) * 1000;
   // $sql = "SELECT *  FROM " . $old_style . "   WHERE `is_new`=0 and style_sn='KLRW028164' limit $offset,1000";
    $sql = "SELECT *  FROM " . $old_style . "   WHERE `is_new`!=0  ".$where." limit $offset,1000";
    $res = mysqli_query($conOld,$sql);
    $old_style_data = array ();
    while ( $row = mysqli_fetch_array($res) ){
		$style_id = $row['style_id'];
		$style_sn = $row['style_sn'];
		$n1 = $row['18ky_gongfei'];
        $n4= $row['ptw_gongfei'];
        $n3 =  $row['bmgy_gongfei'];
        $n2 =  $row['csflj_gongfei'];
        
       for ($i=1;$i<5;$i++){
           $fee_name = "n".$i;
           $fee = $$fee_name;
           $sql = "INSERT INTO `".$new_table."` (" . $field . ")  VALUES (" . $style_id . ", '" . $style_sn . "', " .$i. ",'".$fee."','1','admin' ,'".$date_time."')";
			
           if(!mysqli_query($conNew,$sql)){
				echo "\n\n\n".$sql."\n\n\n";exit;
			}else {
				echo mysqli_insert_id($conNew)."\n";
			}
       }
    }
}
echo "\n\n\n\n完成ok！";
die();



