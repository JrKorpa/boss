<?php
header("Content-type:text/html;charset=utf-8;");

$conn=mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');
//$conn=new mysqli('localhost','root','','test') or die("数据库连接失败！"); 

//mysqli_query($conn,'set names utf-8');
$conn -> set_charset ( "utf8" );

//线下彩宝类默认销售政策
$policy_id = 17;

$info_sql = "select `jiajia`,`sta_value` from `base_salepolicy_info` where `policy_id`=".$policy_id;
$tmp = mysqli_query($conn,$info_sql);
$arr = mysqli_fetch_row($tmp);
$jiajia = $arr[0];
$sta_value = $arr[1];

$company_in = "'苏州广济南路体验店','广州越秀分公司吉邦大厦体验店','郑州二七路体验店','上海南京东路体验店','重庆分公司解放碑体验店','长沙分公司黄兴中路体验店','青岛分公司香港中路体验店','乌鲁木齐新华北路体验店','广州天河分公司广晟大厦体验店','天津滨江道体验店','成都分公司中环广场体验店','武汉分公司佳丽广场体验店','北京西单体验店','合肥分公司长江中路银泰体验店','深圳罗湖分公司地王大厦体验店','南京分公司新街口体验店','杭州分公司环球中心体验店'";
$warehouse_in = "'总公司后库','总公司店面配货库','婚博会备货库','代销库','展会活动库'";

$sql = "SELECT count(*) FROM `base_salepolicy_goods` WHERE `isXianhuo`=1 and (`company` in ($company_in) or `warehouse` in ($warehouse_in)) and `product_type` in (16,17)";
$t = mysqli_query($conn,$sql);
$cnt = mysqli_fetch_row($t);

$len = $cnt[0];
$forsize = ceil($len / 1000);

$insert_fields = "`policy_id`, `goods_id`, `chengben`, `sale_price`, `jiajia`, `sta_value`, `isXianhuo`, `create_time`, `create_user`, `check_time`, `check_user`, `status`, `is_delete`";

for($ii = 1; $ii <= $forsize; $ii ++){
	$offset = ($ii - 1) * 1000;
    $sql = "SELECT * FROM `base_salepolicy_goods` WHERE `isXianhuo`=1 and (`company` in ($company_in) or `warehouse` in ($warehouse_in)) and `product_type` in (16,17) limit $offset,1000";
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