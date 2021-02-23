<?php
/*
	@author: liulinyan
	@date: 2016-12-27
	@filename:qh_goods_auto_link_add.php
	@used:可销售商品自动同步到销售商品表）
*/
header('Content-type: text/html; charset=utf-8');
date_default_timezone_set('Asia/Shanghai');
define('ROOT_PATH',str_replace('\\','/',realpath(dirname(__FILE__))));//定义目录
define('API_ROOT',ROOT_PATH.'/lib/');
include_once(API_ROOT.'PdoModel.php');
include(API_ROOT.'SgoodsClassModel.php');
include(API_ROOT.'OrderClassModel.php');
$Model = new SgoodsClassModel();
$OrModel = new OrderClassModel();

//拿出所有的产品线
$products = $OrModel->getallproduct();
//拿出所有的款式分类
$cates = $OrModel->getallcattype();

//is_default 1默认 2不是默认政策
//is_delete 0有效  1无效
//huopin_type 0期货  2全部
//bsi_status 记录状态3位已审核


//失效掉过期的销售政策和绑定了过去的销售政策的商品
$Model->autodelete();
//is_default 1默认 2不是默认政策
//is_delete 0有效  1无效
//huopin_type 1现货  2全部
//bsi_status 记录状态3位已审核
$data = $Model->getallokinfo();
if(empty($data))
{
	die('请先配置销售政策');
}
$result_arr = array();
//循环销售政策,只要满足条件的,并且不存在的 我们就添加 否则就修改
foreach($data as $obj)
{
	$policyid = $obj['policy_id'];
	$jiajia = $obj['jiajia'];
	$sta_value = $obj['sta_value'];
	//销售政策id
	$gdata = $Model->getallqihuogoods($obj);
	if(empty($gdata))
	{
		echo '没有找到满足销售政策id'.$policyid.'的可销售商品';
		continue;
	}
	foreach($gdata as $goodsinfo)
	{
		//整理好数据之后直接入库
		$goodsinfo['policy_id'] = $policyid;
		$goodsinfo['jiajia'] = $jiajia;
		$goodsinfo['sta_value'] = $sta_value;
		if(empty($Model->checkgoods($goodsinfo['goods_id'],$policyid)))
		{
			$res = $Model->insertappgoods($goodsinfo);
		}
	}
}
?>