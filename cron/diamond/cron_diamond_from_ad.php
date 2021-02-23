<?php
header("Content-type:text/html;charset=utf8;");
error_reporting(E_ALL ^ E_DEPRECATED);
$mysqli=new mysqli('192.168.1.192','cuteman','QW@W#RSS33#E#','app_order') or die("数据库连接失败！") ;
$sql ="SELECT * FROM `front`.`diamond_from_ad` where enabled=1";
$result = $mysqli->query($sql); 
$mysqli->close();
$num = $result->num_rows;

$csvfile = DIRNAME(__FILE__)."/diamond_from_ad.log";

$fh = fopen($csvfile,'a+') or die("Can't open file1.csv");
$info = array($num);
fputcsv($fh, $info);
fclose($fh);


