<?php
header("Content-type:text/html;charset=utf8;");
error_reporting(E_ALL ^ E_DEPRECATED);
$mysqli=new mysqli('192.168.1.93','cuteman','QW@W#RSS33#E#','app_order') or die("数据库连接失败！") ; 
//$mysqli=new mysqli('192.168.0.131','root','123456','app_order'); 
$sql ="update base_order_info o,app_order_account oa set o.order_pay_status=2 where o.id=oa.order_id and o.order_pay_status=1 and money_paid>0";
$mysqli->query($sql);

$sql ="update base_order_info o,app_order_account oa set o.order_pay_status=3 where o.id=oa.order_id and o.order_pay_status <=2 and money_paid >= order_amount and order_amount > 0 and referer='天生一对加盟商'";
$mysqli->query($sql);


$mysqli->close();
