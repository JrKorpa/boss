<?php
/*
	@author: liulinyan
	@date: 2015-08-30
	@filename:goods_to_salepolicy_goods.php
	@used:仓库商品自动录入到可销售商品表中  新的产品线和新的款式分类
*/
header("Content-type:text/html;charset=utf-8;");
define('ROOT_LOG_PATH',str_replace('goods_to_salepolicy_goods.php', '', str_replace('\\', '/', __FILE__))); 
//$conn=mysqli_connect('192.168.0.95','cuteman','QW@W#RSS33#E#','warehouse_shipping') or die("数据库链接失败");
$conn=mysqli_connect('192.168.1.59','cuteman','QW@W#RSS33#E#','warehouse_shipping') or die("数据库链接失败");
$conn -> set_charset ("utf8" );
//a. 定时任务执行时，默认选择《商品列表》中的所有库存状态商品，自动根据一键生成可销售商品按钮的功能， 对符合条件的商品（在"默认上架"仓位、货品状态为"库存"、且是否绑定为"未绑定"三个条件时），将自动添加到可销售商品清单并更新为上架状态，可正常接受下单销售；
//(1)
//(1)找出所有满足条件的商品货号
/*
case 'a.product_type'
		  when '' then '其他'
		  else a.product_type
		  end as a.product_type,
	case 'a.cat_type'
		 when '' then '其他'
		 else a.cat_type
		 end as a.cat_type,
*/

$sql = 
"select
	a.goods_id,a.goods_sn,a.goods_name,a.is_on_sale,
	a.mingyichengben as chengbenjia,act.cat_type_id as category,
	pt.product_type_id as product_type,
	a.jietuoxiangkou as xiangkou,
	a.company,a.warehouse,
	a.company_id,a.warehouse_id,
	a.shoucun as finger
from
	warehouse_goods as a
	inner join warehouse as c on a.warehouse_id=c.id
	inner join front.app_product_type as pt on a.product_type1 = pt.product_type_name
	inner join front.app_cat_type as act on a.cat_type1 = act.cat_type_name
where
	a.order_goods_id < 1 and
	c.is_default=1
	and (a.product_type1 != '彩钻' or (a.product_type1 !='钻石' and a.cat_type1 != '裸石')) ";
/*select
	a.goods_id,a.goods_sn,a.goods_name,
	a.mingyichengben as chengbenjia,act.cat_type_id as category,
	pt.product_type_id as product_type,
	a.jietuoxiangkou as xiangkou,
	a.company,a.warehouse,
	a.company_id,a.warehouse_id,
	a.shoucun as finger
from
	warehouse_goods as a
	inner join warehouse as c on a.warehouse_id=c.id
	
	inner join front.base_style_info as s on a.goods_sn=s.style_sn
	inner join front.app_cat_type as act on s.style_type=act.cat_type_id
	inner join front.app_product_type as pt on s.product_type=pt.product_type_id
	
where
	a.is_on_sale=2 and 
	a.order_goods_id < 1 and
	c.is_default=1
	and (pt.product_type_name != '彩钻' or (pt.product_type_name !='钻石' and act.cat_type_name != '裸石')) 
	and a.goods_id in('150922095292','150922094754','150922094755','150922094756','1408241251');
";*/

$result = mysqli_query($conn,$sql);
$rows = $result->num_rows;
if($rows < 1)
{
	return ;
}
$i=0;
$j=0;
$all=0;
$nostr = '';
while($obj = mysqli_fetch_assoc($result))
{
	//
	$goods_id = $obj['goods_id'];
	
	$goodsdata = array();
	$goodsdata['goods_id'] = $goods_id;
	$goodsdata['mingyichengben'] = $obj['chengbenjia'];
	
	$isonsale = $obj['is_on_sale'];   //因为数据查询加上这个就很慢 所以移到这里来
	if($isonsale !=2)
	{
		continue;
	}
	//检查是否在 base_salepolicy_goods存在 如果存在 那就更改价格
	if(checkgoods($goods_id,$conn) > 0 )
	{
		//修改价格   暂时别动价格了
		updategoods($goodsdata,$conn);
		//deletegoods($goods_id,$conn);
		$i++;
	}else{
		//如果不存在则录入进去
		unset($obj['is_on_sale']);
		insertgoods($obj,$conn);
		$j++;
	}
	$all++;
}
$tjstr = '需要操作的总共有'.$all.'条\r\n';
$tjstr .= '在可销售商品里面存在的有'.$i.'\r\n';
$tjstr .= '在可销售商品里面不存在有,'.$j.'已经录入进去了'.$j.'条\r\n';
echo $tjstr;
recordLog('tongjinew', $tjstr);
function checkgoods($goods_id,$conn)
{
	$sql = "select id  from front.base_salepolicy_goods where goods_id = '".$goods_id."'";
	$result = mysqli_query($conn,$sql);
	//return mysqli_fetch_assoc($result);
	if($result)
	{
		return $result->num_rows;
	}else{
		return 0;		
	}
}

function deletegoods($goods_id,$conn)
{
	$api = 'delete';
	$sql = "delete from front.base_salepolicy_goods where goods_id = '".$goods_id."'";
	mysqli_query($conn,$sql);
	$str = '从可销售商品删除了货号为 '.$goods_id.'的商品';
	recordLog($api,$str);
}

function updategoods($goodsdata,$conn)
{
	$api = 'update_chengbenjia';
	$goods_id = isset($goodsdata['goods_id']) ? $goodsdata['goods_id'] : '';
	if(empty($goods_id))
	{
		return ;
	}
	$chengbenjia = isset($goodsdata['mingyichengben']) ? $goodsdata['mingyichengben'] : 0;
	$sql = "update front.base_salepolicy_goods set chengbenjia='".$chengbenjia."' where goods_id = '".$goods_id."'";
	//echo $sql;
	mysqli_query($conn,$sql);
	recordLog($api,$sql);
}
function insertgoods($goodsdata,$conn)
{
	$api = 'auto_addgoods';
	$errapi = 'auto_addgood_error';
	if(empty($goodsdata))
	{
		return ;
	}
	$goodsid = $goodsdata['goods_id'];
	$allkeys = implode(',',array_keys($goodsdata));
	$allvalue = implode("','",array_values($goodsdata));
	$add_time = date("Y-m-d H:i:s");
	
	$sql = "insert into front.base_salepolicy_goods";
	$sql .= " ($allkeys,is_sale,add_time) ";
	$sql .= "values('$allvalue',1,'".$add_time."')";
	//echo $sql;
	$result = mysqli_query($conn,$sql);
	$num = mysqli_insert_id($conn);
	$str = '插入了货号为 '.$goodsid.' 的商品.<br/>';
	echo $str.'\r\r';
	if($num > 0)
	{
		recordLog($api,$str.$sql);
	}else{
		//记录错误日志
		recordLog($errapi,$sql);
		//recordError($conn,$goodsid,$actionname='仓库商品到可销售商品失败');
		return false;
	}
}

/*------------------------------------------------------ */
//-- 记录日志信息
//-- by 刘林燕
/*------------------------------------------------------ */
function recordLog($api, $str)
{
	if (!file_exists(ROOT_LOG_PATH . 'logs/auto_base_salepolicy_goods'))
	{
		mkdir(ROOT_LOG_PATH . 'logs/auto_base_salepolicy_goods', 0777);
		chmod(ROOT_LOG_PATH . 'logs/auto_base_salepolicy_goods', 0777);
	}
	$content = $api."||".date("Y-m-d H:i:s")."||\r\n".$str."||"."\r\n";
	$file_path =  ROOT_LOG_PATH . 'logs/auto_base_salepolicy_goods/'.date('Y')."_".date('m')."_".date('d')."$api.txt";
	file_put_contents($file_path, $content, FILE_APPEND );
}

//end
/*******************  liulinyan  ********************/



/*
//-- 记录错误的信息到表里面去
//-- by 刘林燕
*/

function recordError($conn,$goodsid,$actionname)
{
	$sql = "
select 
	a.goods_id,a.product_type,a.cat_type,
	a.is_on_sale,a.warehouse,a.goods_sn,
	a.goods_name,a.mingyichengben,a.tuo_type,
	a.zhushi,a.zhushilishu,a.zuanshidaxiao,
	a.zhengshuhao,a.order_goods_id,a.box_sn,
	bg.bill_type,bi.bill_no
from
	warehouse_shipping.warehouse_goods as a 
	inner join warehouse_shipping.warehouse_bill_goods as bg on a.goods_id=bg.goods_id 
	inner join warehouse_shipping.warehouse_bill as bi on bg.bill_type=bi.bill_type 
where
	a.goods_id= '".$goodsid."'
	order by bg.id desc limit 1
";
	$result = mysqli_query($conn,$sql);
	$rows = $result->num_rows;
	if($rows < 1)
	{
		return;
	}
	$obj = mysqli_fetch_assoc($result);
	if(!empty($obj))
	{
		//入库咯
		$obj['id'] = '';
		$obj['action_name'] = $actionname;
		$obj['action_time'] = date('Y-m-d H:i:s'); 
		$fields = implode(',',array_keys($obj));
		$values = implode("','",array_values($obj));
		$sql = "insert into front.auto_run_goods($fields) values('$values')";
		$result = mysqli_query($conn,$sql);
		return mysqli_insert_id($conn);
	}
}
