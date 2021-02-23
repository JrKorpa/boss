<?php

error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");
// define('ROOT_PATH',str_replace('crate_style_info.php', '', str_replace('\\', '/', __FILE__)));
// require_once(ROOT_PATH . 'config/shell_config.php');
$conOld = mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');
$conNew =  mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');

$old_style = 'list_style_goods';	
$new_table = "base_salepolicy_goods";

$field = ['goods_id','goods_sn','goods_name','isXianhuo','chengbenjia','category','product_type','add_time','is_sale','type','is_base_style','xiangkou','is_valid','company','warehouse','company_id','warehouse_id','stone','finger','caizhi','yanse'];

$field_sql = implode('`,`',$field);

//旧的数据
$old_table = 'list_style_goods';
$sql = "SELECT count(*) FROM " . $old_table . "  WHERE 1 ";
$t = mysqli_query($conOld,$sql);
$cnt = mysqli_fetch_row($t);
$date_time = date("Y-m-d H:i:s");
$len = $cnt[0];

$num = 500;

$forsize = ceil($len / $num);
$dtime = date("Y-m-d H:i:s");
for($ii = 1; $ii <= $forsize; $ii ++){
    echo $ii."----------------------------------------------------------"."\n";
    $offset = ($ii - 1) * $num;
    $sql = "SELECT * FROM " . $old_style . " limit $offset,$num";
    $res = mysqli_query($conOld,$sql);
    $old_style_data = array();
	$val_sql=array();    
    while ( $data = mysqli_fetch_assoc($res) ){
		$row = array();
		$row['goods_id']     = $data['goods_sn'];
		$row['goods_sn']     = $data['style_sn'];
		$row['goods_name']   = $data['style_name'];
		$row['isXianhuo']    = 0;
		$row['chengbenjia']  = $data['dingzhichengben'];
		$row['category']     = $data['cat_type_id'];
		$row['product_type'] = $data['product_type_id'];
		$row['add_time']     = $dtime;
		$row['is_sale']      = 1;
		$row['type']         = 1;
		$row['is_base_style']= 1;
		$row['xiangkou']     = $data['xiangkou'];
		$row['is_valid']     = 1;
		$row['company']      = '';
		$row['warehouse']    = '';
		$row['company_id']   = 0;
		$row['warehouse_id'] = 0;
		$row['stone']        = $data['zhushizhong'];
		$row['finger']       = $data['shoucun'];
		$row['caizhi']       = $data['caizhi'];
		$row['yanse']        = $data['yanse'];
		$val_sql[] = "('".implode("','",$row)."')";
    }

	$sql = "INSERT INTO `".$new_table."` (`" . $field_sql . "`)  VALUES " . implode(',',$val_sql)." ;";
	//echo $sql;die;
	if(!mysqli_query($conNew,$sql)){
		echo "\n\n\n".$sql."\n\n\n";exit;
	}
}

echo "\n\n\n\n完成ok！";
die();





