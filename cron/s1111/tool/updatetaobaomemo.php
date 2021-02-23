<?php
define('ROOT_PATH',str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));//定义目录
include(ROOT_PATH.'/taobaoapi.php');
//引入订单操作类文件
include_once(API_ROOT.'PdoModel.php');
include_once(API_ROOT.'OrderClassModel.php');
$orderid = isset($_POST['orderid'])?str_replace(' ',',',$_POST['orderid']):'';
$taobao_memo =  isset($_POST['info'])?$_POST['info']:'';
if(empty($orderid))
{
	exit ('请输入淘宝订单编号');
}
if(empty($taobao_memo))
{
	exit ('请输入订单备注');	
}
/*
$allids = array_filter(explode(',',$allids));
if(empty($allids))
{
	exit('请输入淘宝订单编号');
}
*/
//检查淘宝订单是否存在
$orderModel = new OrderClassModel();
$info = $apiModel->getorderinfo($orderid);
if($info->code)
{
	exit ('没有找到订单id为:'.$orderid.'的信息');
}
$apiModel->update_taobao_memo($orderid,$taobao_memo,1);
echo 1;
?>