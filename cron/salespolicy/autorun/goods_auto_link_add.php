<?php
include('config.php');
//引入计算保险费
include('include/countprice.php');
//日志路径
define('ROOT_LOG_PATH',str_replace('goods_auto_link_add.php', '', str_replace('\\', '/', __FILE__)));

//is_default 1默认 2不是默认政策
//is_delete 0有效  1无效
//bsi_status 记录状态3位已审核
$sql = "
select
	a.policy_id,
	a.jiajia,
	a.sta_value,
	a.product_type,
	a.huopin_type,
	a.cat_type,
	a.tuo_type,
	a.zhushi_begin,
	a.zhushi_end
from
	base_salepolicy_info as a
where 
    is_kuanprice = 0 and 
	is_default=1 and
	is_delete = 0 and
	bsi_status = 3
	order by policy_id desc;
";

$result = mysqli_query($conn,$sql);
if(!$result)
{
	return;
}
$result_arr = array();
while($obj = mysqli_fetch_assoc($result))
{
	//传递条件拿取所有满足条件的值
	//$resultinfo = getgoods($conn,$obj);
	getgoods($conn,$obj);
	//返回的是操作成功的商品id-政策id 以及失败的商品id
	//$pilicyid = $obj['policy_id'];
	//$result_arr[$pilicyid] = $resultinfo;
}



function getgoods($conn,$where)
{

//销售政策id
$policyid = $where['policy_id'];
$jiajia = $where['jiajia'];
$sta_value = $where['sta_value'];


//is_policy 1没有 2有政策
//base_salepolicy_goods表中没有销售政策的
$sql = "
select
	a.isXianhuo,
	a.chengbenjia,
	b.goods_id,
	b.product_type1,
	b.cat_type1,
	b.zuanshidaxiao,
	b.jietuoxiangkou,
	b.tuo_type 
from 
	base_salepolicy_goods as a
	inner join warehouse_shipping.warehouse_goods as b on a.goods_id=b.goods_id
where 
	a.is_policy=1 and a.is_sale=1 and 
";
	if(isset($where['product_type']) && !empty($where['product_type']))
	{
		$sql .= " b.product_type1 = '".$where['product_type']."' and " ; //新产品线
	}
	if(isset($where['huopin_type']) && $where['huopin_type'] !="")
	{
		$sql .= " a.isXianhuo = '".$where['huopin_type']."' and " ; //新产品线
	}
	//空着就是要一一匹配 只有全部的时候不用匹配
	if(isset($where['cat_type']) && $where['cat_type']!="全部" )
	{
		$sql .= " b.cat_type1 = '".$where['cat_type']."' and " ; //新产品线
	}
	if(isset($where['tuo_type']) && $where['tuo_type']!='0')
	{
		$sql .= " b.tuo_type= '".$where['tuo_type']."' and ";
	}
	if(isset($where['zhushi_begin']) && isset($where['zhushi_end']) && $where['zhushi_begin']!="" && $where['zhushi_end']!="" && $where['zhushi_end'] != 0)
	{
		$sql .=" b.zuanshidaxiao >= '".$where['zhushi_begin']."' and b.zuanshidaxiao <='".$where['zhushi_end']."' and ";	
	}
	$sql .= " 1 " ;
	
	$goodsid = array(); //默认给个空数组  用来装已经处理了的
	//echo $sql.'<br/>';
	$result = mysqli_query($conn,$sql);
	if(!$result)
	{
		$returninfo = '没有找到满足销售政策id'.$policyid.'的可销售商品';
		//recordLog('action_no', $returninfo);
		return $goodsid;
	}
	while($obj = mysqli_fetch_assoc($result))
	{
		//整理好数据之后直接入库
		$obj['policy_id'] = $policyid;
		$obj['jiajia'] = $jiajia;
		$obj['sta_value'] = $sta_value;
		$res = insertappgoods($obj,$conn);
		array_push($goodsid,$res);
	}
	//return $goodsid;
}









//插入数据到表app_salepolicy_goods
function insertappgoods($goodsinfo,$conn)
{
	//定义所有需要加保险费用的产品线
	$allproducttype = array('钻石','珍珠','珍珠饰品','翡翠','翡翠饰品','宝石','宝石饰品','钻石饰品','宝石饰品','宝石');
	//定义保险费默认值
	$baoxianfei = 0;
	//判断是否需要拿取保险费 (镶嵌类的现货,拖类型)
	if(in_array($goodsinfo['product_type1'],$allproducttype) && $goodsinfo['isXianhuo']==1 && $goodsinfo['tuo_type']>1)
	{
		//拿取保险费的值
		$baoxianfei = getbxfinfo($goodsinfo,$conn);
	}
	//计算价格
	$sale_price = round(($goodsinfo['chengbenjia']+$baoxianfei) * $goodsinfo['jiajia'] + $goodsinfo['sta_value'] );
	
	//开始拼装入库数据
	$sqlarr=array(
		'policy_id'=>$goodsinfo['policy_id'],
		'goods_id'=>$goodsinfo['goods_id'],
		'isXianhuo'=>$goodsinfo['isXianhuo'],
		'sta_value'=>$goodsinfo['sta_value'],
		'chengben'=>$goodsinfo['chengbenjia'],
		'jiajia'=>$goodsinfo['jiajia'],
		'sale_price'=>$sale_price,
		'create_time'=>date("Y-m-d H:i:s"),
		'create_user'=>'adminauto'
	);
	
	$allkeys = implode(',',array_keys($sqlarr));
	$allvalue = implode("','",array_values($sqlarr));
	
	$sql = "insert into app_salepolicy_goods";
	$sql .= " ($allkeys) ";
	$sql .= "values('$allvalue')";
	$result = mysqli_query($conn,$sql);
	$returninfo ='';
	if($result)
	{
		//如果成功
		$str = "商品id ".$goodsinfo['goods_id'].'添加最新的默认销售政策'.$goodsinfo['policy_id'].'成功<br/>'; 
		//更改 是否有政策的值
		$upsql = "update base_salepolicy_goods set is_policy=2 where goods_id='".$goodsinfo['goods_id']."'";
		mysqli_query($conn,$upsql);
		//再把商品的id和政策的id都记录下来
		$str .= "商品id ".$goodsinfo['goods_id'].'状态更新为已经绑定销售政策 is_policy设置为2<br/>';
		recordLog('actionadd', $str);
		$returninfo = $goodsinfo['goods_id'].'-'.$goodsinfo['policy_id'].'<br/>';
	}else{
		//如果失败就只返回goods_id自身
		$str = "商品id ".$goodsinfo['goods_id'].'添加最新的默认销售政策'.$goodsinfo['policy_id'].'失败<br/>'; 
		recordLog('actionadd_err', $str);
		$returninfo = $goodsinfo['goods_id'].'<br/>';
	}
	return $returninfo;
	
	/*
	$str = "商品id ".$goodsinfo['goods_id'].'添加最新的默认销售政策'.$goodsinfo['policy_id'].'成功<br/>';
	$str .= "商品id ".$goodsinfo['goods_id'].'状态更新为已经绑定销售政策 is_policy设置为2<br/>';
	recordLog('actionadd', $str);
	$returninfo = $goodsinfo['goods_id'].'-'.$goodsinfo['policy_id'].'<br/>';
	return $returninfo;
	*/
}

/*------------------------------------------------------ */
//-- 记录日志信息
//-- by 刘林燕
/*------------------------------------------------------ */
function recordLog($api, $str)
{
	if (!file_exists(ROOT_LOG_PATH . 'logs/goods_auto_link_add'))
	{
		mkdir(ROOT_LOG_PATH . 'logs/goods_auto_link_add', 0777);
		chmod(ROOT_LOG_PATH . 'logs/goods_auto_link_add', 0777);
	}
	$content = date("Y-m-d H:i:s")."||\r\n".$str."\r\n";
	$file_path =  ROOT_LOG_PATH . 'logs/goods_auto_link_add/'.date('Y')."_".date('m')."_".date('d')."$api.txt";
	file_put_contents($file_path, $content, FILE_APPEND );
}

?>