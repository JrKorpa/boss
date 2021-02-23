<?php
define('ROOT_PATH',str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));//定义目录
include(ROOT_PATH.'/taobaoapi.php');
//引入订单操作类文件
include_once(API_ROOT.'PdoModel.php');
include_once(API_ROOT.'OrderClassModel.php');
$allids = isset($_REQUEST['orderid'])?str_replace(' ',',',$_REQUEST['orderid']):'';
if(empty($allids))
{
	exit('请输入订单信息');
}
$allids = array_filter(explode(',',$allids));
if(empty($allids))
{
	exit('请输入订单信息');
}
$orderModel = new OrderClassModel();
foreach($allids as $key=>$id)
{
	echo '订单id:&nbsp;'.$id.'<br/>';
	$info = $apiModel->getorderinfo($id);
	//print_r($info);
	if($info->code)
	{
		echo '没有找到订单id的信息';
		echo '<hr/>';
		continue;
	}
	//把订单抓取的结果显示出来
	$result = $orderModel->getresult($id);
	if(!empty($result))
	{
		echo '订单id:&nbsp;'.$id.'已经抓取过,结果是:&nbsp;'.$result['reason'].'<br/>';
	}

	$order = $info->trade->orders->order;
	foreach($order as $v)
	{
		$title = empty($v->title) ? '没有标题':$v->title;
		$smark = empty($v->sku_properties_name) ?'没有标签': $v->sku_properties_name;
		echo '&nbsp;&nbsp;&nbsp;标题是：'.$title.'<br/>';
		echo '&nbsp;&nbsp;&nbsp;标签是：'.$smark.'<br/>';
	}
	$bz = $info->trade->seller_memo;
	$bz = empty($bz) ? '没有备注':$bz;
	echo '&nbsp;&nbsp;&nbsp;备注是：'.$bz.'<br/>';
	print_r($info);
}
?>