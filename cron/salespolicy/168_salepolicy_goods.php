<?php
header("Content-type:text/html;charset=utf-8;");

$conn=mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');
//$conn=new mysqli('localhost','root','','test') or die("数据库连接失败！"); 

//mysqli_query($conn,'set names utf-8');
$conn -> set_charset ( "utf8" );

//线上空托类默认销售政策
$policy_id = 13;

$info_sql = "select `jiajia`,`sta_value` from `base_salepolicy_info` where `policy_id`=".$policy_id;
$tmp = mysqli_query($conn,$info_sql);
$arr = mysqli_fetch_row($tmp);
$jiajia = $arr[0];
$sta_value = $arr[1];

$where_in = "'线上低值库','总公司低值库','彩宝库','线上钻饰库','深圳珍珠库','线上混合库','线上唯品会货品库','京东素金','黄金网络库','淘宝素金','淘宝黄金','京东黄金','淘宝裸钻库','投资金条库'";

$sql = "SELECT count(*) FROM `base_salepolicy_goods` WHERE `isXianhuo`=1 and `product_type` in (5,10,11,12,23,25,27) and `warehouse` in ($where_in)";
//echo $sql;die;
$t = mysqli_query($conn,$sql);
$cnt = mysqli_fetch_row($t);

$len = $cnt[0];
$forsize = ceil($len / 1000);

$insert_fields = "`policy_id`, `goods_id`, `chengben`, `sale_price`, `jiajia`, `sta_value`, `isXianhuo`, `create_time`, `create_user`, `check_time`, `check_user`, `status`, `is_delete`";

for($ii = 1; $ii <= $forsize; $ii ++){
	$offset = ($ii - 1) * 1000;
    $sql = "SELECT * FROM `base_salepolicy_goods` WHERE `isXianhuo`=1 and `product_type` in (5,10,11,12,23,25,27) and `warehouse` in ($where_in) limit $offset,1000";
    $res = mysqli_query($conn,$sql);
	while ( $row = mysqli_fetch_array($res) ){
		
		$goods_id = $row['goods_id'];
		$chengben = $row['chengbenjia'];
		$sale_price = $chengben*$jiajia+$sta_value;
		$sql = "INSERT INTO `app_salepolicy_goods_copy` (" . $insert_fields . ")  VALUES (".$policy_id.",'".$goods_id."',".$chengben.",".$sale_price.",".$jiajia.",".$sta_value.",1,'".date("Y-m-d H:i:s")."','admin','".date("Y-m-d H:i:s")."','admin',3,1)";
		
		if(!mysqli_query($conn, $sql)){
            echo $sql."\n\n";
        }else{
            echo mysqli_insert_id($conn)."\n\n";
        }
	}
}