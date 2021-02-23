<?php
include('../taobaoapi.php');
//获取自己所有淘宝出售中商品
$allids = isset($_REQUEST['goodsid'])?str_replace(' ',',',$_REQUEST['goodsid']):'';
if(empty($allids))
{
	exit('请输入商品信息');
}
$allids = array_filter(explode(',',$allids));
if(empty($allids))
{
	exit('请输入商品信息');
}
foreach($allids as $taobaoid)
{
	//$goodsinfo = $apiModel->getgoodsinfo();
	//print_r($goodsinfo);
}
?>