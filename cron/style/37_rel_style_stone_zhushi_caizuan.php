<?php
//v1款  主石： 彩钻20 
//异形钻2 珍珠3 其他主石头格式一样
//此文件是where条件
include 'style_where.php';
set_time_limit(0);
//读数据
$conn = mysqli_connect('192.168.1.55','style_zyy','KELAzhangyuanyuan123','kela_style');
$connNew =  mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');

//and style_sn='KLPW014165'
$count_sql = "select count(*) from `style_style` where `is_new`=0 and main_stone_cat=20 ".$where;
$count_res = mysqli_fetch_row(mysqli_query($conn,$count_sql));
$len = $count_res[0];
$page_size = ceil($len/1000);
$date_time = date("Y-m-d H:i:s");
$field = "`style_id`, `stone_position`, `stone_cat`, `stone_attr`,`add_time`";
for($j = 1; $j <= $page_size; $j ++){
    $start = ($j - 1) * 1000;
    $sql = "select style_id,style_sn,main_stone_cat,main_stone_attr,sec_stone_cat,sec_stone_attr,is_confirm from `style_style` where `is_new`=0  and 
	 main_stone_cat=20 ".$where." limit $start,1000";
    $result = mysqli_query($conn, $sql);
	$arr = array();
	while($row=mysqli_fetch_assoc($result)){
		$arr[] = $row;
	}
	
	foreach($arr as $val){
		if($val['main_stone_cat']){
			//主石:少 6
            /* old    "1": "1.6",     "2": "1",     "3": {        "min": "1.6",         "max": "1.6"    }, 
                      "4": "8",     "5": "1",     "6": "1"*/
            /*new     "weight": "1.6",     "number": "1",     "xiangkou_start": "1.6",     "xiangkou_end": "1.6", 
                      "clarity_zhushi": "8",     "caizuan_color": "1"   "shape_zhushi": "1"*/
            $style_id = $val['style_id'];
            $style_sn = $val['style_sn'];
            $stone_attr = unserialize($val['main_stone_attr']);
            $new_arr['weight'] = $stone_attr['1'];
            $new_arr['number'] = $stone_attr['2'];
            $new_arr['xiangkou_start'] = $stone_attr['3']['min'];
            $new_arr['xiangkou_end'] = $stone_attr['3']['max'];
            $new_arr['clarity_zhushi'] = $stone_attr['4'];
            $new_arr['caizuan_color'] = $stone_attr['5'];
            $new_arr['shape_zhushi'] = $stone_attr['6'];
          
			$insert_sql= "update  `rel_style_stone` set  `stone_attr`= '".serialize($new_arr)."' ,main_stone_cat=20 where `stone_position`=1 and `style_id`=$style_id";
		
			if(!mysqli_query($connNew, $insert_sql)){
				echo $insert_sql."\n\n";
				$info = $style_sn."\n".$insert_sql."\n";
                writefile('no_35_zhushi_error.txt',$info);
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
