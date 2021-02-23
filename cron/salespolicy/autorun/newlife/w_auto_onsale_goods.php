<?php
/*
	@author: liulinyan
	@date: 2015-12-27
	@filename:auto_onsale_goods.php
	@used:商品自动上下架   (用新的产品线和新的款式分类）
*/
header('Content-type: text/html; charset=utf-8');
date_default_timezone_set('Asia/Shanghai');
define('ROOT_PATH',str_replace('\\','/',realpath(dirname(__FILE__))));//定义目录
define('API_ROOT',ROOT_PATH.'/lib/');
include_once(API_ROOT.'PdoModel.php');
include(API_ROOT.'OrderClassModel.php');
$Model = new OrderClassModel();

/*同时满足三个条件(
1：商品列表中 货品状态为库存
2：货品所在仓库为  "默认上架" 仓位
3：商品列表中 货品是否绑定为"未绑定"
)
注释：
	b.id 销售商品表base_salepolicy_goods中的自增id
	a.goods_id = b.goods_id 仓库商品表中的货号
	warehouse 仓库表  warehouse_id仓库id
	
	a.is_on_sale = 2       货品状态为库存
	a.order_goods_id= 0    为绑定订单
	b.is_sale = 0          状态为下架的
	c.is_default=1         是否默认为是
*/
//(1)找出所有满足条件的,目前是下架的，让他重新上架
$goodsarr  = $Model->getautosalegoods();
if(!empty($goodsarr))
{
	//执行sql  商品自动上架
	$Model->autoonsale($goodsarr);
}
/*
自动下架的
b. 当这三个条件中的任一条件不满足时，货品将自动从可销售商品下架，不允许下单销售；
*/
//(2)找出所有不满足条件的，目前是上架的,让他下架
$goodsarr  = $Model->getautosalegoods(0);
if(!empty($goodsarr))
{
	//执行sql  商品自动下架
	$Model->autoonsale($goodsarr,0);
}