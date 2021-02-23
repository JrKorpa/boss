<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
//header('Content-type: text/html; charset=utf-8');
/*
author:刘林燕
date:2015-08-31
filename:view.php
used:导出excel
*/
$conn=mysqli_connect('192.168.1.59','cuteman','QW@W#RSS33#E#','front') or die("数据库链接失败");
$conn -> set_charset ("utf8" );
//拼接sql
$sql = " select * from front.auto_run_goods where ";
if(isset($_POST['goods_id']) && $_POST['goods_id'] !='')
{
	$sql .= " goods_id = '".$_POST['goods_id']."' and ";
}
if(isset($_POST['product_type']) && $_POST['product_type'] !='')
{
	$sql .= " product_type = '".$_POST['product_type']."' and ";
}
if(isset($_POST['action_name']) && $_POST['action_name'] !='')
{
	$sql .= " action_name = '".$_POST['action_name']."' and ";
}
if(isset($_POST['begintime']) && $_POST['begintime'] !='')
{
	$sql .= "action_time >= '".$_POST['begintime']."' and ";
}
if(isset($_POST['endtime']) && $_POST['endtime'] !='')
{
	$sql .= "action_time <= '".$_POST['endtime']."' and ";
}
$sql = $sql." 1 order by id asc ";

$result  = mysqli_query($conn,$sql);
$rows = $result->num_rows;



//循环所有的
if($rows<1)
{
	exit('亲,没有满足条件的数据啊');
}
$time = date('Y-m-d');
?>
<?php
header('content-type:charset=utf-8');
header("Content-Type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename={$time}.xls");    
header("Pragma:no-cache");
header("Expires:0");
//导出excel
//header("Content-type:application/vnd.ms-excel");
//header("Content-Disposition:filename={$tydname}.xls");
?>
<style type="text/css">
.page{width:1200px; margin:40px auto;}
/*表格展示样式*/
.tj{background-color:#FC0; color:red;}
.line{background-color:yellow;}
.tydmc{background-color:#09F; color:#fff; font-size:16px;}
.flmc{background-color:#6FF; color:#009; font-size:16px;}
table { 
	border-collapse:collapse; /* 关键属性：合并表格内外边框(其实表格边框有2px，外面1px，里面还有1px哦) */ 
	border:solid #000; /* 设置边框属性；样式(solid=实线)、颜色(#999=灰) */ 
	border-width:1px 1px 1px 1px; /* 设置边框状粗细：上 右 下 左 = 对应：1px 0 0 1px */
	text-align:center;
}
table tr th{border:1px solid #000; padding:10px; background-color:#099; height:30px; line-height:30px; font-size:16px; font-weight:bold; color:#fff;}
table th,table td {border:1px solid #000;border-width:1px 1px 1px 1px; padding:10px; text-align:center;height:30px; line-height:30px; }
.line{background-color:yellow;} 
</style>
<div class="page">
<table border="1px">
<tr>
    <th>货号</th>
    <th>产品线</th>
    <th>款式分类</th>
    <th>状态</th>
    <th>所在仓库</th>
    <th>款号</th>
    <th>名称</th>
    <th>名义价</th>
    <th>金托类型</th>
    <th>主石</th>
    <th>主石粒数</th>
    <th>主石大小</th>
    <th>证书号</th>
    <th>是否绑定</th>
    <th>柜位</th>
    <th>单据类型</th>
    <th>单据编号</th>
    <th>发生动作</th>
    <th>发生时间</th>
</tr>
<?php
while($obj = mysqli_fetch_assoc($result))
{
	echo <<< HTML
	<tr>
		<td>{$obj['goods_id']}</td>
		<td>{$obj['product_type']}</td>
		<td>{$obj['cat_type']}</td>
		<td>{$obj['is_on_sale']}</td>
		<td>{$obj['warehouse']}</td>
		<td>{$obj['goods_sn']}</td>
		<td>{$obj['goods_name']}</td>
		<td>{$obj['mingyichengben']}</td>
		<td>{$obj['tuo_type']}</td>
		<td>{$obj['zhushi']}</td>
		<td>{$obj['zhushilishu']}</td>
		<td>{$obj['zuanshidaxiao']}</td>
		<td>{$obj['zhengshuhao']}</td>
		<td>{$obj['order_goods_id']}</td>
		<td>{$obj['box_sn']}</td>
		<td>{$obj['bill_type']}</td>
		<td>{$obj['bill_no']}</td>
		<td>{$obj['action_name']}</td>
		<td>{$obj['action_time']}</td>
	</tr>
HTML;
}
?>
</table>
</div>