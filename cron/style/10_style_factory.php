<?php
// v2,v3 工厂
error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");
//此文件是where条件
include 'style_where.php';

$conOld = mysqli_connect('192.168.1.55','style_zyy','KELAzhangyuanyuan123','kela_style');
$conNew =  mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');


$old_style = 'style_style';	
$old_table = 'style_factory';	

$new_table = "rel_style_factory";
$new_table_apply = "app_factory_apply";
$new_style = "base_style_info";

// 老款式库

/*$t_sql = "TRUNCATE TABLE `front`.$new_table ";
$t_res = mysqli_query($conNew,$t_sql);
if($t_res){
    echo $old_style . "数据已经清空\n\n\n\n";
}*/


//取出老款的对应关系

$sql = "SELECT count(a.style_id) FROM " . $old_style . " as a , ".$old_table." as b WHERE  a.`style_id`=b.`style_id`   ".$where_40;
$t = mysqli_query($conOld,$sql);
$cnt = mysqli_fetch_row($t);


$date_time = date("Y-m-d H:i:s");
$field = " `style_id`, `style_sn`, `factory_id`, `factory_sn`, `factory_fee`, `xiangkou`, `is_def`, `is_factory` ";

$len = $cnt[0];
$forsize = ceil($len / 2);
for($ii = 1; $ii <= $forsize; $ii ++){
    echo $ii."----------------------------------------------------------"."\n";
    $offset = ($ii - 1) * 1000;
    $sql = "SELECT a.style_id,a.style_sn,b.*  FROM " . $old_style . " as a , ".$old_table." as b WHERE  a.`style_id`=b.`style_id` ".$where_40." limit $offset,1000";
    $res = mysqli_query($conOld,$sql);
    $old_style_data = array ();
    while ( $row = mysqli_fetch_array($res) ){
		
        $old_style_data[] = $row;
        
    }
    $val_sql = array();
    foreach ($old_style_data as $val){
        $sql ="select style_id from  $new_table where style_id =".$val['style_id']." and factory_id =".$val['factory_id']." and xiangkou='".$val['xiangkou']."'";
        $res = mysqli_query($conNew, $sql);
        $tmp_data = mysqli_fetch_row($res);
        if($tmp_data){
            continue;
        }
        $rows = array();
        $rows[] = $val['style_id'];
		$rows[] = $val['style_sn'];
		$rows[] = $val['factory_id'];
		$rows[] = str_replace("'","",$val['factory_sn']);
		$rows[] = $val['factory_fee'];
		$rows[] = $val['xiangkou'];
		$rows[] = $val['is_def'];
		$rows[] = $val['is_factory'];
        $val_sql[] = "('".implode("','",$rows)."')";
    }
    if($val_sql){
        $sql = "INSERT INTO `".$new_table."` (" . $field . ")  VALUES ". implode(',',$val_sql)." ;";
		
		if(!mysqli_query($conNew,$sql)){
			echo "\n\n\n".$sql."\n\n\n";exit;
		}else {
			echo mysqli_insert_id($conNew)."\n";
		}
    }
         
}
echo "\n-10-完成ok！\n";
die();
