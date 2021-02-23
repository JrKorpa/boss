<?php
header("Content-type:text/html;charset=utf8;");
error_reporting(E_ALL ^ E_DEPRECATED);
$mysqli=new mysqli('localhost','cuteman','QW@W#RSS33#E#','front') or die("数据库连接失败！") ; 
//$mysqli=new mysqli('192.168.1.63','develop','123456','front'); 
$sql ="select * from base_salepolicy_info where is_delete=0 ";
$result=$mysqli->query($sql);

$time = date("Y-m-d H:i:s");
if ($result) {
	if($result->num_rows>0){                                               //判断结果集中行的数目是否大于0
		while($row =$result->fetch_array() ){                        //循环输出结果集中的记录
			if($time >= $row['policy_end_time']){
				echo $sql="update base_salepolicy_info set is_delete=1 where policy_id = '".$row['policy_id']."'";
				$mysqli->query($sql);
echo "\r\n";
				echo $sql="update app_salepolicy_goods set is_delete=2 where policy_id = '".$row['policy_id']."'";
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
