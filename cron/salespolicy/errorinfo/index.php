<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<script type="text/javascript" src="js/jquery-2.1.1.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script type="text/javascript" src="My97DatePicker/WdatePicker.js"></script>
<link rel="stylesheet" type="text/css" href="css/base.css" />
</head>

<body>
<div class="page">
<?php
include('product_type.php');
?>
<table class="showinfo">
<form action="#" method="post" id="searchform">
<tr>
	<td>货号：<input type="text" name="goods_id" /></td>
	<td>产品线:
    <input type="hidden" value="1" name="page" id="pagenow" />
    <select name="product_type">
    <option value="">全部</option>
    <?php
        foreach($alltype as $k=>$v)
        {
            echo <<<HTML
        <option value="{$k}">{$v}</option>
HTML;
        }
    ?>
    </select>
    </td>
    <td>发生时间:
  <input type="text" class="Wdate" name="begintime" onFocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd HH:mm:ss'})" />
  --
  <input type="text" class="Wdate" name="endtime" onFocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd HH:mm:ss'})" />		
  	</td>
    <td>发生动作:
    <select name="source_name" id="action_name">
    <option value="">全部</option>
    <?php
	$arr=array(0=>"自动上架失败",1=>"自动下架失败",2=>"仓库商品到可销售商品失败",3=>"可销售商品关联销售策略失败",4=>"可销售商品到销售策略商品失败");
	foreach($arr as $k=>$v)
	{
		echo <<< HTML
		<option value="{$v}">$v</option>
HTML;
	}
	?>
    </select>
    </td>
    <td>
    	<input type="button" value="搜索" class="submit" onclick="tosearch();" />
        <!--<input type="submit" class="submit" value="搜索" />-->
    </td>
</tr>
</form>
</table>
<div class="clear"></div>
<div id="show"></div>
<table id="showinfo">
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
        <th>单据<br />编号</th>
        <th>发生动作</th>
        <th>发生时间</th>
    </tr>
</table>
</div>
</body>
</html>
