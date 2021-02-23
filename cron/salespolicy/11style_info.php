<?php
header("Content-type:text/html;charset=utf-8;");
set_time_limit(0);
$file = fopen(__DIR__."/13.csv","r"); 

$goods_list = array();

while ($data = fgetcsv($file)) { //每次读取CSV里面的一行内容
	$data[1] = strtoupper($data[1]);
	$goods_list[$data[1]] = $data;
}
unset($goods_list[0]);		
fclose($file);

$conn=mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');
//$conn=new mysqli('localhost','root','','test') or die("数据库连接失败！"); 

//mysqli_query($conn,'set names utf-8');
$conn -> set_charset ( "utf8" );

$insert_fields = "`style_id`, `stone_position`, `stone_cat`, `stone_attr`";


if(!empty($goods_list)){
	$time = date("Y-m-d H:i:s");
	foreach($goods_list as $val){
		for($j=0;$j<=15;$j++){
			$val[$j] = trim($val[$j]);
		}
		$goods_sn = $val[1];
		$xiangkou = $val[5]?$val[5]:0;
		
		/* //获取材质值-----start----------
		$attr_val_sql = "select `att_value_id` from `app_attribute_value` where `att_value_name`='".$val[7]."'";
		$att_value_arr = mysqli_query($conn,$attr_val_sql);
		$att_value_id = mysqli_fetch_row($att_value_arr);
		$caizhi = 0;
		if($att_value_id != NUll){
			$caizhi = $att_value_id[0];
		}
		//获取材质值-----end----------
		//获取材质值-----start----------
		$attr_val_sql = "select `att_value_id` from `app_attribute_value` where `att_value_name`='".$val[15]."'";
		$att_value_arr = mysqli_query($conn,$attr_val_sql);
		$att_value_id = mysqli_fetch_row($att_value_arr);
		$yanse = 267;
		if($att_value_id != NUll){
			$yanse = $att_value_id[0];
		}
		//获取材质值-----end---------- */
		
		$style_info_sql = "select `style_id` from `base_style_info` where `style_sn`='".$goods_sn."'";
		$style_info_arr = mysqli_query($conn,$style_info_sql);
		$style_id = mysqli_fetch_row($style_info_arr);
		if($style_id != NUll){
			$cat = 3;
			if($val[9]=='钻石'){
				$cat = 1;
			}
			//主石
			if($xiangkou){
				$sql1 = "select `style_id` from `rel_style_stone` where `style_id`=".$style_id[0]." and `stone_position`=1";
				$att_value_arr = mysqli_query($conn,$sql1);
				$att_value_id = mysqli_fetch_row($att_value_arr);
				if($att_value_id == NUll){
					$attr = array('weight'=>$xiangkou,'number'=>$val[10],'xiangkou_start'=>$xiangkou,'xiangkou_end'=>$xiangkou);
					$_attr = serialize($attr);
					$sql = "INSERT INTO `rel_style_stone` (" . $insert_fields . ")  VALUES ('".$style_id[0]."','1',".$cat.", '".$_attr."')";
							
					if(!mysqli_query($conn, $sql)){
						echo $sql."\n\n";
					}else{
						echo mysqli_insert_id($conn)."\n\n";
					}
				}
			}
			//副石
			if($val[14]){
				$sql1 = "select `style_id` from `rel_style_stone` where `style_id`=".$style_id[0]." and `stone_position`=1";
				$att_value_arr = mysqli_query($conn,$sql1);
				$att_value_id = mysqli_fetch_row($att_value_arr);
				if($att_value_id == NUll){
					$attr = array('weight'=>$val[15],'number'=>$val[14]);
					$_attr = serialize($attr);
					$sql = "INSERT INTO `rel_style_stone` (" . $insert_fields . ")  VALUES ('".$style_id[0]."','2','".$cat."', '".$_attr."')";
							
					if(!mysqli_query($conn, $sql)){
						echo $sql."\n\n";
					}else{
						echo mysqli_insert_id($conn)."\n\n";
					}
				}
			}
		}
	}
}