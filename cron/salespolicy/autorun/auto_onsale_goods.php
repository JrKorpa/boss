<?php
/*
	@author: liulinyan
	@date: 2015-08-30
	@filename:auto_onsale_goods.php
	@used:商品自动上下架   新的产品线和新的款式分类
*/
header("Content-type:text/html;charset=utf-8;");
define('ROOT_LOG_PATH',str_replace('auto_onsale_goods.php', '', str_replace('\\', '/', __FILE__))); 
//$conn=mysqli_connect('192.168.0.95','cuteman','QW@W#RSS33#E#','warehouse_shipping') or die("数据库链接失败");
$conn=mysqli_connect('192.168.1.59','cuteman','QW@W#RSS33#E#','warehouse_shipping') or die("数据库链接失败");
mysqli_query($conn,'set names utf-8');
//a. 定时任务执行时，默认选择《商品列表》中的所有库存状态商品，自动根据一键生成可销售商品按钮的功能， 对符合条件的商品（在"默认上架"仓位、货品状态为"库存"、且是否绑定为"未绑定"三个条件时），将自动添加到可销售商品清单并更新为上架状态，可正常接受下单销售；
/*
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
$sql = 
"select
	b.id
from
	warehouse_shipping.warehouse_goods as a
	inner join front.base_salepolicy_goods as b on a.goods_id=b.goods_id
	inner join warehouse_shipping.warehouse as c on a.warehouse_id=c.id
where
	a.is_on_sale=2 and 
	a.order_goods_id < 1 and
	b.is_sale=0 and
	c.is_default=1
	and (a.product_type1 != '彩钻' or (a.product_type1 !='钻石' and a.cat_type1 != '裸石')) 
";
$goodsdata  = mysqli_query($conn,$sql);
$goodsarr = combinedata($goodsdata);
if(!empty($goodsarr))
{
	//执行sql  商品自动上架
	$ids = implode(',',$goodsarr);
	$sql = "update front.base_salepolicy_goods set is_sale=1,is_valid=1 where id in($ids)";
	mysqli_query($conn,$sql);
	
	$str = '商品 '.$ids.'自动上架';
	
	recordLog($api='auto_onsale_goods', $str);
}

/*
自动下架的
b. 当这三个条件中的任一条件不满足时，货品将自动从可销售商品下架，不允许下单销售；
*/
//(2)找出所有不满足条件的，目前是上架的,让他下架
$sql = 
"select
	b.id
from
	warehouse_shipping.warehouse_goods as a
	inner join front.base_salepolicy_goods as b on a.goods_id=b.goods_id
	inner join warehouse_shipping.warehouse as c on a.warehouse_id=c.id
where
	b.is_sale=1 and
	(a.is_on_sale !=2 or 
	a.order_goods_id >0 or
	c.is_default !=1) and
	(a.product_type1 != '彩钻' or (a.product_type1 !='钻石' and a.cat_type1 != '裸石')) 
";
$goodsdata  = mysqli_query($conn,$sql);
$goodsarr = combinedata($goodsdata);
if(!empty($goodsarr))
{
	//执行sql  商品自动下架
	$ids = implode(',',$goodsarr);
	$sql = "update front.base_salepolicy_goods set is_sale=0 where id in($ids)";
	mysqli_query($conn,$sql);
	$str = '商品 '.$ids.'自动下架';
	recordLog($api='auto_onsale_goods', $str);
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
		array_push($goods_ids,$row[0]);
	}
	return $goods_ids;
}

/*------------------------------------------------------ */
//-- 记录日志信息
//-- by 刘林燕
/*------------------------------------------------------ */
function recordLog($api, $str)
{
	if (!file_exists(ROOT_LOG_PATH . 'logs/auto_onsale'))
	{
		mkdir(ROOT_LOG_PATH . 'logs/auto_onsale', 0777);
		chmod(ROOT_LOG_PATH . 'logs/auto_onsale', 0777);
	}
	$content = $api."||".date("Y-m-d H:i:s")."||\r\n".$str."||"."\r\n";
	$file_path =  ROOT_LOG_PATH . 'logs/auto_onsale/'.date('Y')."_".date('m')."_".date('d')."$api.txt";
	file_put_contents($file_path, $content, FILE_APPEND );
}

//end
/*******************  liulinyan  ********************/