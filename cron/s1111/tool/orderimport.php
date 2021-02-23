<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>抓取淘宝订单</title>
<style type="text/css">
	.page{margin:0 auto; width:800px; height:auto; border:1px solid #CCC;}
	.line{
		width:800px;
		height:30px;
		float:left;
		line-height:20px;
		font-size:16px;
		text-align:center;
		display:block;
		background-color:#6CF;
	}
	.box{width:300px; height:200px;line-height:40px; margin:20px 30px 10px 30px; padding:20px; }
	.green{background-color:#39F; float:left;color:#fff;font-weight:bold; }
	.red{background-color:#F00; float:right;}
	.gray{background-color:#CCC; float:left;}
	.yellow{background-color:#FF0; float:right;}
	a{color:#FFF; float:right;}
	#message{padding:20px; width:758px; border:1px solid #F00; float:left;height:auto;
	word-wrap:break-word;word-break:break-all; line-height:30px;}
</style>
<script type="text/javascript" src="../js/jquery-1.10.2.js"></script>
<script type="text/javascript" src="My97DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="../js/orderimport.js"></script>
</head>

<body>
<div class="page">
 
	<div class="line">《双十一抓单程序》
    </div>
    <div class="line">
    	<form action="#" method="post" enctype="multipart/form-data">
        	<input type="text" name="out_order_sn" placeholder="请输入淘宝订单" id="out_order_sn"
           onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"  />
           <input type="button" name="import" value="开始录入" onclick="importorder()" />
        </form>
    </div>
    <div id="message">
    </div>
</div>
</body>
</html>