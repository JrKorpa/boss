<?php
/**
 * used:   按照模板,批量修改货品在销售渠道的价格
 * author: lly
 * date:   20161223
**/
header('Content-type: text/html; charset=utf-8');
date_default_timezone_set('Asia/Shanghai');
define('ROOT_PATH',str_replace('\\','/',realpath(dirname(__FILE__))));//定义目录
define('API_ROOT',ROOT_PATH.'/lib/');
include_once(API_ROOT.'PdoModel.php');
include(API_ROOT.'OrderClassModel.php');

$filenames = 'update_sale_price';
$filename = ROOT_PATH.'/templates/'.$filenames.'.csv';
$file = fopen($filename,'r');
$i=0;
$Model = new OrderClassModel();
$salechannels = $Model->getids();
while ($obj = fgetcsv($file))
{
	if($i==0){
		$i++;
		continue;
	}
	$goods_id = trim($obj[0]);
	$shopname = trim(iconv('gb2312','utf-8',$obj[1]));
	if($shopname !='')
	{
		$channelid = $salechannels[$shopname];
	}
	$price = trim($obj[2]);
	$Model->updateprice($goods_id,$channelid,$price);	
}
?>