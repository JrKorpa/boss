<?php
header("Content-type:text/html;charset=utf8;");
error_reporting(E_ALL ^ E_DEPRECATED);
$mysqli=new mysqli('localhost','cuteman','QW@W#RSS33#E#','front') or die("数据库连接失败！") ; 
//$mysqli=new mysqli('192.168.1.63','develop','123456','front'); 
$sql ="select * from app_coupon_policy where policy_status in (1,2,4) ";
$result=$mysqli->query($sql);

$time = date("Y-m-d H:i:s");
if ($result) {
	if($result->num_rows>0){                                               //判断结果集中行的数目是否大于0
		while($row =$result->fetch_array() ){                        //循环输出结果集中的记录
			if($time >= $row['valid_time_end']){
				echo $sql="update app_coupon_policy set policy_status=6 where id = '".$row['id']."'";
				$mysqli->query($sql);
echo "\r\n";
				echo $sql="update base_coupon set coupon_status=4 where coupon_policy= '".$row['id']."' and coupon_status=1";
				$mysqli->query($sql);
echo "\r\n";
			}
		}
	}
	echo "操作结束";
}else {
	echo "查询失败";
}
$result->free();
$mysqli->close();
