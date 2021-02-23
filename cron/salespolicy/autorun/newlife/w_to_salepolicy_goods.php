<?php
/*
	@author: liulinyan
	@date: 2016-12-27
	@filename:w_to_salepolicy_goods.php
	@used:仓库商品自动录入到可销售商品表中(用新的产品线和新的款式分类）
*/
header('Content-type: text/html; charset=utf-8');
date_default_timezone_set('Asia/Shanghai');
define('ROOT_PATH',str_replace('\\','/',realpath(dirname(__FILE__))));//定义目录
define('API_ROOT',ROOT_PATH.'/lib/');
include_once(API_ROOT.'PdoModel.php');
include(API_ROOT.'OrderClassModel.php');
$Model = new OrderClassModel();
//拿出所有的产品线
$products = $Model->getallproduct();
//拿出所有的款式分类
$cates = $Model->getallcattype();

/*同时满足三个条件(
1：商品列表中 货品状态为库存
2：货品所在仓库为  "默认上架" 仓位
3：商品列表中 货品是否绑定为"未绑定"
)*/
$alldata = $Model->getneedtosalegoods();
if(empty($alldata))
{
	die('没有需要跑的数据咯');
}
$i=0;
$j=0;
$all=0;
$nostr = '';
foreach($alldata as $obj)
{
	$goods_id = $obj['goods_id'];
	$goodsdata = array();
	$goodsdata['goods_id'] = $goods_id;
	$goodsdata['mingyichengben'] = $obj['chengbenjia'];
	
	$product_type = $obj['product_type'];
	$category = $obj['category'];
	if($product_type == '彩钻' ){
		return ;	
	}
	$ok = 1;
	//同时满足则走 否则就不走了
	if($product_type!='钻石' &&  $category !="裸石")
	{
		$ok = 1;
	}else{
		//$ok = 0;
		continue;
	}
	if(!in_array($product_type,array_keys($products))  || !in_array($category,array_keys($cates)) )
	{
		continue;
	}
	$obj['product_type'] = $products[$product_type];
	$obj['category'] = $cates[$category];
	//检查是否在 base_salepolicy_goods存在 如果存在 那就更改价格
	if(!empty($Model->checkgoods($goods_id)))
	{
		//修改价格   暂时别动价格了
		$Model->updategoods($goodsdata);
		//deletegoods($goods_id,$conn);
		$i++;
	}else{
		//如果不存在则录入进去
		$obj['isXianhuo'] = '1';
		$obj['type'] = 1;
		$obj['is_sale'] = 1;
		$obj['is_base_style'] = 1;
		$obj['is_valid'] = 1;
		$obj['cate_g'] = 0;
		$obj['is_policy']=1;
		$Model->insertgoods($obj);
		$j++;
	}
	$all++;
}
$tjstr = '需要操作的总共有'.$all.'条\r\n';
$tjstr .= '在可销售商品里面存在的有'.$i.'\r\n';
$tjstr .= '在可销售商品里面不存在有,'.$j.'已经录入进去了'.$j.'条\r\n';
echo $tjstr;
?>