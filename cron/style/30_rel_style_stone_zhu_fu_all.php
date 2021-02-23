<?php
//插入所有款的石头信息,默认都没有赋值
set_time_limit(0);
//此文件是where条件
include 'style_where.php';
//读数据
$conn = mysqli_connect('192.168.1.55','style_zyy','KELAzhangyuanyuan123','kela_style');
$connNew =  mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');

//and style_sn='KLRW028033'
$count_sql = "select count(*) from `style_style` where 1 ".$where;
$count_res = mysqli_fetch_row(mysqli_query($conn,$count_sql));
$len = $count_res[0];
$page_size = ceil($len/1000);
$date_time = date("Y-m-d H:i:s");
//$field = "`style_id`, `stone_position`, `stone_cat`, `stone_attr`,`add_time`";
$field = "`style_id`, `stone_position`, `stone_cat`, `add_time`";
for($j = 1; $j <= $page_size; $j ++){
    $start = ($j - 1) * 1000;
    $sql = "select style_id,style_sn,main_stone_cat,main_stone_attr,sec_stone_cat,sec_stone_attr,is_confirm from `style_style`  where 1 ".$where."  limit $start,1000";
    $result = mysqli_query($conn, $sql);
	$arr = array();
	while($row=mysqli_fetch_assoc($result)){
		$arr[] = $row;
        $style_id = $row['style_id'];
        $style_sn = $row['style_sn'];

		//如果已经存在跳出
		/*$sql1 = "select `style_id` from `rel_style_stone` where style_id = ".$style_id;
		//echo $sql1;exit;

		$res1 = mysqli_query($connNew, $sql1);
		$row1 = mysqli_fetch_row($res1);
		if($row1){
			continue;
		}
*/
        //主石
        $insert_sql= "INSERT INTO `rel_style_stone` (".$field.")  VALUES ( '".$style_id."','1','".$row['main_stone_cat']."','".$date_time."')";

        if(!mysqli_query($connNew, $insert_sql)){
            echo $insert_sql."\n\n";
            $info = $style_sn."\n".$insert_sql."\n";
            writefile('no_30_zhushi_error.txt',$info);
        }else{
            echo mysqli_insert_id($connNew)."\n\n";
        }
        
        //副石
        $insert_sql= "INSERT INTO `rel_style_stone` (".$field.")  VALUES ( '".$style_id."','2','".$row['sec_stone_cat']."','".$date_time."')";
        if(!mysqli_query($connNew, $insert_sql)){
            echo $insert_sql."\n\n";
            $info = $style_sn."\n".$insert_sql."\n";
            writefile('no_30_fushi_error.txt',$info);
        }else{
            echo mysqli_insert_id($connNew)."\n\n";
        }
	}
}
echo "OK";

function writefile($file,$info) {
    $fh = fopen($file, "a");
    echo fwrite($fh, $info);    // 输出：6
    fclose($fh);
}

?>
