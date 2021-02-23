<?php
set_time_limit(0);
//读数据
include 'style_where.php';
$conn = mysqli_connect('192.168.1.55','style_zyy','KELAzhangyuanyuan123','kela_style');
$connNew =  mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');

$count_sql = "select count(*) from `style_style` as a where 1 ".$where_40;
$count_res = mysqli_fetch_row(mysqli_query($conn,$count_sql));
$len = $count_res[0];
$page_size = ceil($len/1000);
for($j = 1; $j <= $page_size; $j ++){
    $start = ($j - 1) * 1000;
    $sql = "select * from `style_style` as a where 1 ".$where_40." limit $start,1000";
    $result = mysqli_query($conn, $sql);
	$arr = array();
	while($row=mysqli_fetch_assoc($result)){
		$arr[] = $row;
	}
	$field = "`style_id`, `style_sn`, `image_place`, `img_sort`, `img_ori`, `thumb_img`, `middle_img`, `big_img`";
	foreach($arr as $val){
		if($val['style_img']){
			//45度主图
			$val['img_ori'] = "http://style.kela.cn/".$val['style_img'];
			$val['thumb_img'] = "http://style.kela.cn/".$val['thumb_img'];
			$val['middle_img'] = "http://style.kela.cn/".$val['middle_img'];
			$val['big_img'] = "http://style.kela.cn/".$val['big_img'];
			
			$count_45_sql = "select count(*) from `app_style_gallery` where `image_place`=1 and `style_id`=".$val['style_id'];
			$count_45_res = mysqli_fetch_row(mysqli_query($conn,$count_45_sql));
			if($count_45_res[0]){
				$insert_sql = "UPDATE `app_style_gallery` SET `img_ori`='".$val['img_ori']."',`thumb_img`='".$val['thumb_img']."',`middle_img`='".$val['middle_img']."',`big_img`='".$val['big_img']."' where `style_id`=".$val['style_id']." and `image_place`=1";
			}else{
				$insert_sql= "INSERT INTO `app_style_gallery` (".$field.")  VALUES ( '".$val['style_id']."',  '".$val['style_sn']."', '1',"
					. " '0', '".$val['img_ori']."', '".$val['thumb_img']."', '".$val['middle_img']."', '".$val['big_img']."')";
			}
			if(!mysqli_query($connNew, $insert_sql)){
				echo $insert_sql."\n\n";
			}else{
				echo mysqli_insert_id($connNew)."\n\n";
			}
		}
		if($val['style_img_n']){
			//180度主图
			$val['img_ori'] = "http://style.kela.cn/".$val['style_img_n'];
			$val['thumb_img'] = "http://style.kela.cn/".$val['thumb_img_n'];
			$val['middle_img'] = "http://style.kela.cn/".$val['middle_img_n'];
			$val['big_img'] = "http://style.kela.cn/".$val['big_img_n'];
			
			$count_180_sql = "select count(*) from `app_style_gallery` where `image_place`=2 and `style_id`=".$val['style_id'];
			$count_180_res = mysqli_fetch_row(mysqli_query($conn,$count_180_sql));
			if($count_180_res[0]){
				$insert_sql = "UPDATE `app_style_gallery` SET `img_ori`='".$val['img_ori']."',`thumb_img`='".$val['thumb_img']."',`middle_img`='".$val['middle_img']."',`big_img`='".$val['big_img']."' where `style_id`=".$val['style_id']." and `image_place`=2";
			}else{
				$insert_sql= "INSERT INTO `app_style_gallery` (".$field.")  VALUES ( '".$val['style_id']."',  '".$val['style_sn']."', '2',"
					. " '0', '".$val['img_ori']."', '".$val['thumb_img']."', '".$val['middle_img']."', '".$val['big_img']."')";
			}
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
