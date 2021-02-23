<?php
// v2,v3 款属性
error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");
//此文件是where条件
include 'style_where.php';

$conOld = mysqli_connect('192.168.1.55','style_zyy','KELAzhangyuanyuan123','kela_style');
$conNew =  mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');

$old_table = 'style_factory_apply';	
$new_table_apply = "app_factory_apply";

// 老款式库
/*$t_sql = "TRUNCATE TABLE `front`.$new_table_apply ";
$t_res = mysqli_query($conNew,$t_sql);
if($t_res){
    echo $new_table_apply . "数据已经清空\n\n\n\n";
}*/

//取出老款的对应关系

$sql = "SELECT count(*) FROM " .$old_table." where 1 ".$where;
$t = mysqli_query($conOld,$sql);
$cnt = mysqli_fetch_row($t);


$date_time = date("Y-m-d H:i:s");
//$field = " `style_id`, `style_sn`, `factory_id`, `factory_name`, `factory_sn`, `factory_fee`, `xiangkou`, `is_def`, `is_factory` ";
$field_apply = " `style_id`, `style_sn`, `f_id`, `factory_id`, `factory_name`, `factory_sn`, `xiangkou`, `factory_fee`, `type`, `status`, `apply_num`, `make_name`, `crete_time`, `check_name`, `check_time`, `info`";

$page_size = 1000;
$len = $cnt[0];
$forsize = ceil($len / $page_size);
for($ii = 1; $ii <= $forsize; $ii ++){
    echo $ii."----------------------------------------------------------"."\n";
    $offset = ($ii - 1) * 1000;
    $sql = "SELECT * FROM " .$old_table." where 1 ".$where." limit $offset,$page_size";
    $res = mysqli_query($conOld,$sql);
    $old_style_data = array ();
    while ( $row = mysqli_fetch_array($res) ){
        $old_style_data[] = $row; 
    }
     $val_sql = array();
    foreach ($old_style_data as $val){
        //如果已经存在跳出
        $sql ="select style_id from  $new_table_apply where style_id =".$val['style_id']." and factory_id=".$val['factory_id']." and xiangkou='".$val['xiangkou']."'";
        $res = mysqli_query($conNew, $sql);
        $tmp_data = mysqli_fetch_row($res);
        if($tmp_data){
            continue;
        }
        $rows = array();
        $rows[] = $val['style_id'];
		$rows[]  = $val['style_sn'];
		$rows[]  = $val['f_id'];
		$rows[]  = $val['factory_id'];
		$rows[]  = $val['factory_name'];
		$rows[]  = $val['factory_sn'];
		$rows[]  = $val['xiangkou'];
		$rows[]  = $val['factory_fee'];
		$rows[]  = $val['type'];
		$rows[]  = $val['status'];
		$rows[]  = $val['apply_num'];
		$rows[]  = $val['make_name'];
		$rows[]  = $val['make_time'];
        $rows[]  = $val['check_name'];
		$rows[]  = $val['check_time'];
		$rows[]  = $val['info'];
   
        $val_sql[] = "('".implode("','",$rows)."')";
    }
    
    if($val_sql){
        $sql = "INSERT INTO `".$new_table_apply."` (" . $field_apply . ")  VALUES ". implode(',',$val_sql)." ;";	
 
        if(!mysqli_query($conNew,$sql)){
            echo "\n\n\n".$sql."\n\n\n";exit;
        }else {
            echo mysqli_insert_id($conNew)."\n";
        }
    }
	
}
echo "\n--11--完成ok！\n";
die();
