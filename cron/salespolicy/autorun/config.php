<?php
header("Content-type:text/html;charset=utf-8;");
date_default_timezone_set('Asia/Shanghai');
//$conn=mysqli_connect('192.168.0.91','root','123456','front') or die("数据库链接失败");
$conn=mysqli_connect('192.168.1.59','cuteman','QW@W#RSS33#E#','front') or die("数据库链接失败");
$conn -> set_charset ("utf8" );
?>