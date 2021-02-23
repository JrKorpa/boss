<?php
/*
author:刘林燕
date:2015-08-31
filename:product_type.php
used:根据体验店统计
*/
define('ROOT_LOG_PATH',str_replace('goods_to_salepolicy_goods.php', '', str_replace('\\', '/', __FILE__))); 
$conn=mysqli_connect('192.168.1.59','cuteman','QW@W#RSS33#E#','front') or die("数据库链接失败");
$conn -> set_charset ("utf8" );
//第一步,查找所有的产品线的名称
$sql = "select distinct product_type from front.auto_run_goods where 1";
$data = mysqli_query($conn,$sql);
$row = $data->num_rows;
//定义一个变量$alltype来存储所有的产品线类型
$alltype = '';
if($row < 1 )
{
	$alltype['null'] = '暂时没有产品线'; 
}else{
	while($obj = mysqli_fetch_assoc($data))
	{
		$type = $obj['product_type'];
		$alltype[$type] = $type;
	}
}
?>