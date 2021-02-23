<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>BDD淘宝订单基础信息查看</title>
</head>

<body>
<?php
echo bcadd($left=1.0321456, $right=0.0243456, 2);
echo '<br/>';
echo bcdiv($left=6, $right=5, 2);
echo '<br/>';
$array = array(1,2,3,4,5);
foreach($array as $v)
{
	echo $v;
	if($v>1)
	{
		echo '我不走了';
		break;
	}
	
}
echo 'nihao';

?>
<form action="looktaobaoinfo.php" enctype="multipart/form-data" method="post" target="_blank">
<textarea name="orderid"  placeholder='请输入订单号,多个订单请以逗号区分'></textarea>
<br/>
<input type="submit" value="立即查看" />
</form>
</body>
</html>