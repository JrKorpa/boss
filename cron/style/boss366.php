<?php
header("Content-type:text/html;charset=utf-8;");
//$conn=mysqli_connect('192.168.10.23','root','1308b8dac1e577','front') or die("数据库链接失败");
$conn=mysqli_connect('localhost','cuteman','QW@W#RSS33#E#','front') or die("数据库链接失败");
mysqli_query($conn,'set names utf-8');
$sql = "select att_value_id from app_attribute_value where attribute_id=1 ";
$goodsdata  = mysqli_query($conn,$sql);
$goodsarr = combinedata($goodsdata);
$allid = array();
foreach($goodsarr as $v)
{
	array_push($allid,$v[0]);
}
$sql = "select rel_id,attribute_value from rel_style_attribute where attribute_id=1";
$styledata  = mysqli_query($conn,$sql);
$stylearr = combinedata($styledata);
foreach($stylearr as $v)
{
	$relid = $v[0];
	$value = $v[1];
	$tmp = explode(',',$value);
	$tmp = array_filter($tmp);
	$tmp = array_unique($tmp);
	$nowvalue = '';
	foreach($tmp as $tmpv)
	{
		if(in_array($tmpv,$allid))
		{
			$nowvalue .= $tmpv.',';
		}
	}
	//更新上去
	$sql = "update rel_style_attribute set attribute_value = '".$nowvalue."' where rel_id='".$relid."'";
	mysqli_query($conn,$sql);
	echo $sql.'<br/>';
}

/*------------------------------------------------------ */
//-- 把数据源组装为数组 返回
//-- by 刘林燕
/*------------------------------------------------------ */
function combinedata($result)
{
	$goods_ids = array();
	if(!$result)
	{
		return $goods_ids;
	}
	
	while($row = mysqli_fetch_row($result))
	{
		array_push($goods_ids,$row);
	}
	return $goods_ids;
}