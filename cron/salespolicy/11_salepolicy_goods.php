<?php
header("Content-type:text/html;charset=utf-8;");

$file = fopen(__DIR__."/12.csv","r"); 
$goods_list = array();
while ($data = fgetcsv($file)) { //每次读取CSV里面的一行内容
	$goods_list[] = $data;
}
unset($goods_list[0]);		
fclose($file);

$conn=mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');
//$conn=new mysqli('localhost','root','','test') or die("数据库连接失败！"); 

//mysqli_query($conn,'set names utf-8');
$conn -> set_charset ( "utf8" );

$insert_fields = "`goods_id`, `goods_sn`, `goods_name`, `isXianhuo`, `chengbenjia`, `category`, `product_type`, `add_time`, `is_sale`, `type`, `is_base_style`, `xiangkou`, `is_valid`, `company`, `warehouse`, `company_id`, `warehouse_id`, `stone`, `finger`, `caizhi`, `yanse`";
if(!empty($goods_list)){
	$time = date("Y-m-d H:i:s");
	foreach($goods_list as $val){
		for($j=0;$j<=15;$j++){
			$val[$j] = trim($val[$j]);
		}
		$goods_id = $val[0];
		$goods_sn = $val[1];
		//$goods_name = iconv('gbk','utf-8',$val[10]);
		$goods_name = $val[10];
		$price = $val[9];
		$category = 1;
		$product_type = 1;
		$xiangkou = $val[5]?$val[5]:0;
		//$company = iconv('gbk','utf-8',$val[14]);
		$company = $val[14];
		//$warehouse = iconv('gbk','utf-8',$val[13]);
		$warehouse = $val[13];
		$company_id = $val[11];
		$warehouse_id = $val[12];
		$stone = $val[5]?$val[5]:0;
		$finger = $val[6]?$val[6]:0;
		//获取材质值-----start----------
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
		//获取材质值-----end----------
		
		$sql = "INSERT INTO `base_salepolicy_goods` (" . $insert_fields . ")  VALUES (".$goods_id.",'".$goods_sn."','".$goods_name."',0,".$price.",".$category.",".$product_type.",'".$time."',1,1,1,".$xiangkou.",1,'".$company."','".$warehouse."',".$company_id.",".$warehouse_id.",".$stone.",".$finger.",".$caizhi.",".$yanse.")";
		
		//file_put_contents("e:\a.sql",$sql,FILE_APPEND);
		
		if(!mysqli_query($conn, $sql)){
            echo $sql."\n\n";
        }else{
            echo mysqli_insert_id($conn)."\n\n";
        }
		
	}
}