<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="author" content="杨福友" />
<meta name="keywords" content="杨福友" />
<meta name="description" content="Just do!" />
<meta http-equiv="refresh" content="<?php echo $msg['time'];?>; URL=<?php echo $msg['url'];?>">
<title>提示信息</title>
<style type="text/css">
	#msg {border:1px solid #ccc;width:400px;min-height:120px;margin:100px auto;font-size:13px;padding:5px;}
	h2 {height:25px;text-align:center;border-bottom:1px solid #ccc;}
</style>
</head>
<body>
<div id="msg" >
	<h2><?php echo $msg['title'];?></h2>
	<p><?php echo $msg['info'];?></p>
	<br />
	正在跳转中...
	<br />
	<p>系统将在 <span style="color:blue;font-weight:bold"><?php echo $msg['time'];?></span> 秒后自动跳转,如果不想等待,直接点击 <a href="<?php echo $msg['url'];?>">这里</a> 跳转</p>
</div>
</body>
</html>