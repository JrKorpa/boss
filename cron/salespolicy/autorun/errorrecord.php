<?php
header("Content-type:text/html;charset=utf-8;");
$conn=mysqli_connect('192.168.10.23','root','123456','front') or die("数据库链接失败");
//$conn=mysqli_connect('localhost','cuteman','QW@W#RSS33#E#','front') or die("数据库链接失败");
$conn -> set_charset ("utf8" );

$starttime = explode(' ',microtime());
echo microtime()."<br/>";

//c.bill_no,c.bill_type
//inner join warehouse_shipping.warehouse_bill_goods as c on a.goods_id = c.goods_id

$sql = "
select 
	a.goods_id,a.goods_sn,a.product_type,a.category as cat_type,
	b.is_on_sale,b.warehouse,b.goods_name,
	b.mingyichengben,b.tuo_type,b.zhushi,b.zhushilishu,
	b.zuanshidaxiao,b.zhengshuhao,b.order_goods_id,b.box_sn
	
from
	base_salepolicy_goods as a
	inner join warehouse_shipping.warehouse_goods as b on a.goods_id=b.goods_id
		
where
	a.is_policy=1
	and b.is_on_sale=2
";
$result = mysqli_query($conn,$sql);


$allcpx = getallcpx(); //产品线
$allcat = getallcat(); //款式分类
$alldj = getalldj();   //单据类型


if($result)
{
	while($obj = mysqli_fetch_assoc($result))
	{
		
		if(!empty($obj))
		{
			//入库咯
			$obj['id'] = '';
			
			//产品线名称获取
			$product_type = $obj['product_type'];
			$obj['product_type'] = isset($allcpx[$product_type]) ? $allcpx[$product_type] : '';
			
			//款式分类名称获取
			$cat_type = $obj['cat_type'];
			$obj['cat_type'] = isset($allcat[$cat_type]) ? $allcat[$cat_type] : '';
			unset($obj['castegory']);
			
			$obj['action_name'] = '可销售商品关联销售策略失败';
			$obj['action_time'] = date('Y-m-d H:i:s'); 
			$fields = implode(',',array_keys($obj));
			$values = implode("','",array_values($obj));
			$sql = "insert into front.auto_run_goods($fields) values('$values')";
			mysqli_query($conn,$sql);
			//echo mysqli_insert_id($conn);
			echo '商品货号：',$obj['goods_id'].'没有找到策略,录入到异常信息表<br/>';
		}
	}
}
$endtime = explode(' ',microtime());
$thistime = $endtime[0]+$endtime[1]-($starttime[0]+$starttime[1]);
$thistime = round($thistime,3);
echo "time：".$thistime." sec。".time();


//获取所有的产品线
function getallcpx()
{
	global $conn;
	$sql = "select product_type_id,product_type_name from app_product_type where 1";
	$result = mysqli_query($conn,$sql);
	$alltype = array();
	while($obj = mysqli_fetch_assoc($result))
	{
		$id = $obj['product_type_id'];
		$alltype[$id] = $obj['product_type_name'];
	}
	return $alltype;
}

//获取所有的款式分类
function getallcat()
{
	global $conn;
	$sql = "select cat_type_id,cat_type_name from app_cat_type where 1";
	$result = mysqli_query($conn,$sql);
	$allcate = array();
	while($obj = mysqli_fetch_assoc($result))
	{
		$id = $obj['cat_type_id'];
		$allcate[$id] = $obj['cat_type_name'];
	}
	return $allcate;
}

//获取所有的单据类型
function getalldj()
{
	global $conn;
	$sql = "select type_name,type_SN from warehouse_shipping.warehouse_bill_type";
	$result = mysqli_query($conn,$sql);
	$alldj = array();
	while($obj = mysqli_fetch_assoc($result))
	{
		$id = $obj['type_SN'];
		$alldj[$id] = $obj['type_name'];
	}
	return $alldj;
}


?>