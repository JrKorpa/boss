<?php
set_time_limit(0);
include 'style_where.php';
$conn = mysqli_connect('192.168.1.55','style_zyy','KELAzhangyuanyuan123','kela_style');
$connNew =  mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');

$count_sql = "select count(*) from style_style as a,`style_gallery` as b where a.style_id = b.style_id ".$where_40;
$count_res = mysqli_fetch_row(mysqli_query($conn,$count_sql));
$len = $count_res[0];
$page_size = ceil($len/1000);
for($j = 1; $j <= $page_size; $j ++){
    $start = ($j - 1) * 1000;
    $sql = "select `g`.*,`a`.`style_sn` from `style_gallery` as `g`,`style_style` as `a` where `a`.`style_id`=`g`.`style_id`  ".$where_40." limit $start,1000";
    $result = mysqli_query($conn, $sql);
	$arr = array();
	while($row=mysqli_fetch_assoc($result)){
		$arr[] = $row;
	}
	$field = "`style_id`, `style_sn`, `image_place`, `img_sort`, `img_ori`, `thumb_img`, `middle_img`, `big_img`";
	foreach($arr as $val){
		$val['img_ori'] = "http://style.kela.cn/".$val['img_ori'];
		$val['thumb_img'] = "http://style.kela.cn/".$val['thumb_img'];
		$val['middle_img'] = "http://style.kela.cn/".$val['middle_img'];
		$val['big_img'] = "http://style.kela.cn/".$val['big_img'];
		$insert_sql= "INSERT INTO `app_style_gallery` (".$field.")  VALUES ( '".$val['style_id']."',  '".$val['style_sn']."', '".$val['image_place']."',"
                . " '".$val['img_sort']."', '".$val['img_ori']."', '".$val['thumb_img']."', '".$val['middle_img']."', '".$val['big_img']."')";
        if(!mysqli_query($connNew, $insert_sql)){
            echo $insert_sql."\n\n";
        }else{
            echo mysqli_insert_id($connNew)."\n\n";
        }
	}
}
echo "OK";
?>
