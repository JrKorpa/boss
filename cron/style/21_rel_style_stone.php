<?php
//v1款
set_time_limit(0);

//读数据
$conn = mysqli_connect('192.168.1.55','style_zyy','KELAzhangyuanyuan123','kela_style');
$connNew =  mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');

$count_sql = "select count(*) from `style_style` where `is_new`=0 and style_sn='KLRW028033'";
$count_res = mysqli_fetch_row(mysqli_query($conn,$count_sql));
$len = $count_res[0];
$page_size = ceil($len/1000);
for($j = 1; $j <= $page_size; $j ++){
    $start = ($j - 1) * 1000;
    $sql = "select style_id,style_sn,main_stone_cat,main_stone_attr,sec_stone_cat,sec_stone_attr from `style_style` where `is_new`=0  and style_sn='KLRW028033' limit $start,1000";
    $result = mysqli_query($conn, $sql);
	$arr = array();
	while($row=mysqli_fetch_assoc($result)){
		/*$_sql = "select `si`.`style_id` from `style_style` as `ss`,`base_style_info` as `si` where `ss`.`style_sn`=`si`.`style_sn` and `ss`.`style_id`=".$row['style_id'];
		$_style_id_arr = mysqli_fetch_row(mysqli_query($conn,$_sql));
		if($_style_id_arr[0]){
			$row['_style_id'] = $_style_id_arr[0];
			$arr[] = $row;
		}*/
		$arr[] = $row;
	}
	$field = "`style_id`, `stone_position`, `stone_cat`, `stone_attr`";
	foreach($arr as $val){
		if($val['main_stone_cat']){
			//主石
			$insert_sql= "INSERT INTO `rel_style_stone` (".$field.")  VALUES ( '".$val['style_id']."','1','".$val['main_stone_cat']."', '".$val['main_stone_attr']."')";
			echo $insert_sql."\n\n";
			if(!mysqli_query($connNew, $insert_sql)){
				echo $insert_sql."\n\n";
			}else{
				echo mysqli_insert_id($connNew)."\n\n";
			}
		}
		if($val['sec_stone_cat']){
			//副石
			$insert_sql= "INSERT INTO `rel_style_stone` (".$field.")  VALUES ( '".$val['style_id']."','2','".$val['sec_stone_cat']."', '".$val['sec_stone_attr']."')";
			echo $insert_sql."\n\n";
			if(!mysqli_query($connNew, $insert_sql)){
				echo $insert_sql."\n\n";
			}else{
				echo mysqli_insert_id($connNew)."\n\n";
			}
		}
	}
}
echo "OK";
?>
