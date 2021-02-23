<?php
//v1款  副石：非：异形钻2 珍珠3  其他主石头格式一样 ； 圆钻1 比其他的多2个属性
set_time_limit(0);
include 'style_where.php';
//读数据
$conn = mysqli_connect('192.168.1.55','style_zyy','KELAzhangyuanyuan123','kela_style');
$connNew =  mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');

//and style_sn='KLRW000007'
$count_sql = "select count(*) from `style_style` where `is_new`=0 and sec_stone_cat!=2 and sec_stone_cat!=3 ".$where;
$count_res = mysqli_fetch_row(mysqli_query($conn,$count_sql));
$len = $count_res[0];
$page_size = ceil($len/1000);
$date_time = date("Y-m-d H:i:s");
$field = "`style_id`, `stone_position`, `stone_cat`, `stone_attr`,`add_time`";
for($j = 1; $j <= $page_size; $j ++){
    $start = ($j - 1) * 1000;
    $sql = "select style_id,style_sn,sec_stone_cat,sec_stone_attr,is_confirm from `style_style` where `is_new`=0  and 
	 sec_stone_cat!=2 and sec_stone_cat!=3 ".$where."  limit $start,1000";
    $result = mysqli_query($conn, $sql);
	$arr = array();
	while($row=mysqli_fetch_assoc($result)){
		$arr[] = $row;
	}
	
	foreach($arr as $val){
        $style_id = $val['style_id'];
        $style_sn = $val['style_sn'];
        $stone_type = $val['sec_stone_cat'];
		if($stone_type){
			//副石
            $stone_attr = unserialize($val['sec_stone_attr']);
            $new_arr['weight'] = $stone_attr['1'];
            $new_arr['number'] = $stone_attr['2'];
            if($stone_type == 1){//副石：形状圆形=1
                 $new_arr['clarity_fushi'] = $stone_attr['3'];
                 $new_arr['color_fushi'] = $stone_attr['4'];
            }

			$insert_sql= "update  `rel_style_stone` set  `stone_attr`= '".serialize($new_arr)."' ,stone_cat=".$stone_type." where `stone_position`=2 and `style_id`=$style_id";
			
			if(!mysqli_query($connNew, $insert_sql)){
				echo $insert_sql."\n\n";
				$info = $style_sn."\n".$insert_sql."\n";
                writefile('no_32_fushi_error.txt',$info);
			}else{
				echo $style_id."\n\n";
			}
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
