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
$old_factory = 'style_factory';	
$new_table = "app_style_fee";
$date_time = date("Y-m-d H:i:s");
$field = "`style_id`, `style_sn`, `fee_type`, `price`, `status`, `check_user`, `check_time`";
//旧的数据
//$sql = "SELECT count(*) FROM " . $old_table . "  WHERE `is_new` =0 and `style_sn`='KLSW028165' ";
$sql = "SELECT count(*) FROM " . $old_table . "  WHERE `is_new` =0 ".$where;
$t = mysqli_query($conOld,$sql);
$cnt = mysqli_fetch_row($t);

$len = $cnt[0];
$forsize = ceil($len / 1000);
for($ii = 1; $ii <= $forsize; $ii ++){
    echo $ii."----------------------------------------------------------"."\n";
    $offset = ($ii - 1) * 1000;
    //$sql = "SELECT *  FROM " . $old_table . "   WHERE `is_new`=0 and `style_sn`='KLSW028165' limit $offset,1000";
    $sql = "SELECT *  FROM " . $old_table . "   WHERE `is_new`=0 ".$where." limit $offset,1000";
    $res = mysqli_query($conOld,$sql);
    $old_style_data = array ();
    while ( $row = mysqli_fetch_array($res) ){
		$style_id = $row['style_id'];
		$style_sn = $row['style_sn'];
        //取出工厂费用
        $sql_1 = "select factory_fee from ".$old_factory."  where style_id=$style_id and is_def =1 ";
        $res_1 = mysqli_query($conOld,$sql_1);
        $tmp_row = mysqli_fetch_row($res_1);
        if(empty($tmp_row)){
            $error = $style_sn."\n";
            writefile("factory_null.txt",$error);
            continue;
        }
        $fee = $tmp_row[0];
        if(empty($fee)){
            $error = $style_sn."\n";
            writefile("factory_0.txt",$error);
            continue;
        }
        $sql_2 = "update ".$new_table." set `price`=$fee where `style_id`=".$style_id;
        mysqli_query($conNew,$sql_2);
    }
}
echo "\n\n\n\n完成ok！";
die();


function writefile($file,$info) {
        $fh = fopen($file, "a");
        echo fwrite($fh, $info);    // 输出：6
    fclose($fh);
}

