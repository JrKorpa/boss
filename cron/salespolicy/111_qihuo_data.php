<?php
header("Content-type:text/html;charset=utf-8;");

$conn=mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');
//$conn=new mysqli('localhost','root','','test') or die("数据库连接失败！"); 

//mysqli_query($conn,'set names utf-8');
$conn -> set_charset ( "utf8" );

//线上线下期货默认销售政策
$policy_id = 29;

$info_sql = "select `jiajia`,`sta_value` from `base_salepolicy_info` where `policy_id`=".$policy_id;
$tmp = mysqli_query($conn,$info_sql);
$arr = mysqli_fetch_row($tmp);
$jiajia = $arr[0];
$sta_value = $arr[1];


$sql = "SELECT count(*) FROM `base_salepolicy_goods` WHERE `isXianhuo`=0";
//echo $sql;die;
$t = mysqli_query($conn,$sql);
$cnt = mysqli_fetch_row($t);

$len = $cnt[0];
$num = 500;
$forsize = ceil($len / $num);

$insert_fields = "`policy_id`, `goods_id`, `chengben`, `sale_price`, `jiajia`, `sta_value`, `isXianhuo`, `create_time`, `create_user`, `check_time`, `check_user`, `status`, `is_delete`";

$dtime = date("Y-m-d H:i:s");
for($ii = 1; $ii <= $forsize; $ii ++){
	$offset = ($ii - 1) * $num;
    $sql = "SELECT * FROM `base_salepolicy_goods` WHERE `isXianhuo`=0 limit $offset,$num";
    $res = mysqli_query($conn,$sql);
	$val_sql=array(); 
	while ( $data = mysqli_fetch_array($res) ){
		
		$goods_id = $data['goods_id'];
		$chengben = $data['chengbenjia'];
		$sale_price = $chengben*$jiajia+$sta_value;
		$row = array();
        $row['policy_id']       = $policy_id;
		$row['goods_id']        = $goods_id;
		$row['chengben']        = $chengben;
		$row['sale_price']      = $sale_price;
		$row['jiajia']          = $jiajia;
		$row['sta_value']       = $sta_value;
		$row['isXianhuo']       = 0;
		$row['create_time']     = $dtime;
		$row['create_user']     = 'admin';
		$row['check_time']      = $dtime;
		$row['check_user']      = 'admin';
		$row['status']          = 3;
		$row['is_delete']       = 1;
        $val_sql[] = "('".implode("','",$row)."')";
	}
    $sql = "INSERT INTO `app_salepolicy_goods` (" . $insert_fields . ")  VALUES " . implode(',',$val_sql).";";
	//echo $sql;die;	
    if(!mysqli_query($conn, $sql)){
        echo $sql."\n\n";
    }else{
        echo mysqli_insert_id($conn)."\n\n";
    }
}