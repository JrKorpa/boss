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

$filenames = 'update_sales_pricy_by_sn';
$filename = ROOT_PATH.'/templates/'.$filenames.'.csv';
$file = fopen($filename,'r');
$i=0;
$Model = new OrderClassModel();
$tuo = array('成品'=>1,'空托女戒'=>2,'空托'=>3);
while ($obj = fgetcsv($file))
{
	if($i==0){
		$i++;
		continue;
	}
	$sn= $obj[0];
	if(empty($sn))
	{
		continue;
	}
	$sn = $obj[0];
	$tuotype = trim(iconv('gb2312','utf-8',$obj[3]));
	$tuoid = '';
	if($tuotype !='')
	{
		$tuoid = $tuo[$tuotype];
	}
	$where = array();
	$where['goods_sn'] = $sn;
	$where['tuo_type'] = $tuoid;
	$where['product_type1'] =  trim(iconv('gb2312','utf-8',$obj[4]));
	$where['caizhi'] = trim(iconv('gb2312','utf-8',$obj[6]));
	$where['start'] =  $obj[8];
	$where['end'] = $obj[7];
	$price = trim($obj[9]);
	$Model->updatepricebysn($where,$price);
}
?>