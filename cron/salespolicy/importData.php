<?php
header("Content-type:text/html;charset=utf-8;");

$conn_read=mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','warehouse_shipping');
$conn_write=mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');
//$conn=new mysqli('localhost','root','','test') or die("数据库连接失败！"); 

//mysqli_query($conn,'set names utf-8');
//$conn -> set_charset ( "utf8" );


//武汉黄金活动
$policy_id = 31;

$info_sql = "select `jiajia`,`sta_value` from `base_salepolicy_info` where `policy_id`=".$policy_id;
$tmp = mysqli_query($conn_write,$info_sql);
$arr = mysqli_fetch_row($tmp);
$jiajia = $arr[0];
$sta_value = $arr[1];


$sql = "SELECT count(`bg`.`goods_id`) FROM `warehouse_bill_goods` as `bg` LEFT JOIN `warehouse_goods` as `g` ON `bg`.`goods_id` = `g`.`goods_id` WHERE `bill_id` in (277,289,323,313,321)";

//echo $sql;die;
$t = mysqli_query($conn_read,$sql);
$cnt = mysqli_fetch_row($t);

$len = $cnt[0];
$num = 500;
$forsize = ceil($len / $num);

$insert_fields = "`policy_id`, `goods_id`, `chengben`, `sale_price`, `jiajia`, `sta_value`, `isXianhuo`, `create_time`, `create_user`, `check_time`, `check_user`, `status`, `is_delete`";

$dtime = date("Y-m-d H:i:s");
for($ii = 1; $ii <= $forsize; $ii ++){
	$offset = ($ii - 1) * $num;
    $sql = "SELECT `bg`.`goods_id` FROM `warehouse_bill_goods` as `bg` LEFT JOIN `warehouse_goods` as `g` ON `bg`.`goods_id` = `g`.`goods_id` WHERE `bill_id` in (277,289,323,313,321) limit $offset,$num";
    $res = mysqli_query($conn_read,$sql);
	$goods_ids=array(); 
	while ( $data = mysqli_fetch_array($res) ){
		$goods_ids[] = $data['goods_id'];
	}
	$goods_id_in = implode(",",$goods_ids);
	$read_sql = "select * from `base_salepolicy_goods` where `goods_id` in ($goods_id_in)";
	$res_data = mysqli_query($conn_write,$read_sql);
	$val_sql=array(); 
	while ( $data = mysqli_fetch_array($res_data) ){
		
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
		$row['isXianhuo']       = $data['isXianhuo'];
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
    if(!mysqli_query($conn_write, $sql)){
        echo $sql."\n\n";
    }else{
        echo mysqli_insert_id($conn_write)."\n\n";
    }
	die('ok');
}