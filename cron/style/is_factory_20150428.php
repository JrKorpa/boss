<?php
// v2,v3 款属性
error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");
// define('ROOT_PATH',str_replace('crate_style_info.php', '', str_replace('\\', '/', __FILE__)));
// require_once(ROOT_PATH . 'config/shell_config.php');

$conOld = mysqli_connect('192.168.1.55','style_zyy','KELAzhangyuanyuan123','kela_style');
$conNew =  mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');


$style_id_sql = "SELECT `style_sn` FROM `base_style_info` WHERE `style_id` NOT IN (SELECT `style_id` FROM `rel_style_factory`)";
$res_style_id = mysqli_query($conNew,$style_id_sql);
$style_id_data = array ();
while ( $row = mysqli_fetch_array($res_style_id) ){
	$style_id_data[] = $row['style_sn'];	
}

$old_style = 'style_style';	
$old_table = 'style_factory';	

$new_table = "rel_style_factory";
$new_table_apply = "app_factory_apply";
$new_style = "base_style_info";

// 老款式库
/* 
$t_sql = "TRUNCATE TABLE `front`.$new_table ";
$t_res = mysqli_query($conNew,$t_sql);
if($t_res){
    echo $old_style . "数据已经清空\n\n\n\n";
}

 */
//取出老款的对应关系
$style_sn_in = implode("','",$style_id_data);

$sql = "SELECT count(a.style_id) FROM " . $old_style . " as a , ".$old_table." as b WHERE  a.`style_id`=b.`style_id` AND a.`style_sn` in ('{$style_sn_in}') ";
$t = mysqli_query($conOld,$sql);
$cnt = mysqli_fetch_row($t);


$date_time = date("Y-m-d H:i:s");
$field = " `style_id`, `style_sn`, `factory_id`, `factory_sn`, `factory_fee`, `xiangkou`, `is_def`, `is_factory` ";
$field_apply = " `style_id`, `style_sn`, `f_id`, `factory_id`, `factory_name`, `factory_sn`, `xiangkou`, `factory_fee`, `type`, `status`, `apply_num`, `make_name`, `crete_time`, `check_name`, `check_time`";

$len = $cnt[0];

$num = 500;
$forsize = ceil($len / $num);
for($ii = 1; $ii <= $forsize; $ii ++){
    echo $ii."----------------------------------------------------------"."\n";
    $offset = ($ii - 1) * $num;
    $sql = "SELECT a.style_id,a.style_sn,b.*  FROM " . $old_style . " as a , ".$old_table." as b WHERE  a.`style_id`=b.`style_id`  AND `style_sn` in ('{$style_sn_in}')  limit $offset,$num";
    $res = mysqli_query($conOld,$sql);
    $old_style_data = array ();
	$val_sql=array();
    while ( $row = mysqli_fetch_array($res) ){
		$style_id = $row['style_id'];
		$style_sn = $row['style_sn'];
		$factroy_id = $row['factory_id'];
		$factory_sn = str_replace("'","",$row['factory_sn']);
		$factory_fee = $row['factory_fee'];
		$xiangkou = $row['xiangkou'];
		$is_def = $row['is_def'];
		$is_factory = $row['is_factory'];

        $sql = "INSERT INTO `".$new_table."` (" . $field . ")  VALUES ( '" . $style_id . "', '" . $style_sn . "','" . $factroy_id . "','" . $factory_sn . "','" . $factory_fee . "' ,'".$xiangkou."',  '" . $is_def . "', '".$is_factory."')";
		
		
		if(!mysqli_query($conNew,$sql)){
		
			echo "\n\n\n".$sql."\n\n\n";exit;
		}else {
			echo mysqli_insert_id($conNew)."\n";

		}
    }
    
}
echo "\n\n\n\n完成ok！";
die();
