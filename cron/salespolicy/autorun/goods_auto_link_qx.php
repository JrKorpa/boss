<?php
/*
auto :刘林燕
file :a5.php
used :处理app_salepolicy_goods表中的关联了销售政策的   
	  如果政策不是默认 匹配一条最新的 重新录进来
	  如果政策是默认的 那再看看,是不是最新的如果是什么都不处理，如果不是的话，删除，再重新录进来,
*/
include('config.php');
//引入计算保险费
include('include/countprice.php');
//日志路径
define('ROOT_LOG_PATH',str_replace('goods_auto_link_qx.php', '', str_replace('\\', '/', __FILE__)));

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
	a.is_default=1 and
	a.is_delete = 0 and
	a.bsi_status = 3
	order by a.policy_id desc;
";
$result = mysqli_query($conn,$sql);
if(!$result)
{
	return;
}
$result_arr = array();


$policyidsarr = getundefaultzcid($conn);
//有效的所有ids
$yxpolicyidsarr = getdefaultzcid($conn);
$allgoodsid = array();
$i = 0;
while($obj = mysqli_fetch_assoc($result))
{
	//传递条件拿取所有满足条件的值
	//if($obj['policy_id'] == 68)
	//{
		$resultgoodsid = getgoods($conn,$obj,$policyidsarr,$allgoodsid,$yxpolicyidsarr);
		//continue;
		//把已经出来好的goodsid 再次放到$allgoodsid里面去
		if(empty($resultgoodsid))
		{
			continue;
		}
		foreach($resultgoodsid as $goodsid)
		{
			if(!in_array($goodsid,$allgoodsid))
			{
				array_push($allgoodsid,$goodsid);
			}
		}
		//print_r($resultinfo);
	//}
	//返回的是操作成功的商品id-政策id 以及失败的商品id
	//$pilicyid = $obj['policy_id'];
	//$result_arr[$pilicyid] = $resultinfo;	
	$i++;
}
//print_r($allgoodsid);
echo '总共执行了政策'.$i.'次';



//查找所有:有效的非默认政策
function getundefaultzcid($conn)
{
	$sql = "
select 
	a.policy_id
from
	base_salepolicy_info as a
where 
	a.is_default=2 
";
/*
where 
	a.is_default=2 and
	a.is_delete=0 and
	a.bsi_status = 3*/
	$result = mysqli_query($conn,$sql);
	$allids = array();
	if(!$result)
	{
		return $allids;	
	}
	while($obj = mysqli_fetch_assoc($result))
	{
		array_push($allids,$obj['policy_id']);
	}
	return $allids;
}


//查找所有:有效的默认政策
function getdefaultzcid($conn)
{
	$sql = "
select 
	a.policy_id
from
	base_salepolicy_info as a
where 
	a.is_default=1 
";
	$result = mysqli_query($conn,$sql);
	$allids = array();
	if(!$result)
	{
		return $allids;	
	}
	while($obj = mysqli_fetch_assoc($result))
	{
		array_push($allids,$obj['policy_id']);
	}
	return $allids;
}



/*
@parames where 条件
@policyidsarr  非默认销售政策ids
@goodsarr      是否已经处理过的商品
@yxpolicyidsarr  默认的销售政策ids
*/
function getgoods($conn,$where,$policyidsarr,$goodsarr,$yxpolicyidsarr)
{

//销售政策id
$policyid = $where['policy_id'];
$jiajia = $where['jiajia'];
$sta_value = $where['sta_value'];

//获取关联了非默认销售政策的商品信息
$undefault_ids = implode("','",$policyidsarr);
$undefaultids = "'$undefault_ids'";
//is_sale  1上架  0下架  不用考虑上下 都要执行
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
	b.tuo_type,
	c.policy_id,
	c.id as appid
from 
	base_salepolicy_goods as a
	inner join warehouse_shipping.warehouse_goods as b on a.goods_id=b.goods_id
	inner join app_salepolicy_goods as c on a.goods_id=c.goods_id
where a.is_policy=2 and a.is_sale=1 and 
";
	if(isset($where['product_type']) && !empty($where['product_type']))
	{
		
		$sql .= " b.product_type1='".$where['product_type']."' and "; 
	}
	if(isset($where['huopin_type']) && $where['huopin_type'] !="")
	{
		$sql .= " a.isXianhuo = '".$where['huopin_type']."' and " ; //货品类型
	}
	//空着就是要一一匹配 只有全部的时候不用匹配
	if(isset($where['cat_type']) && $where['cat_type']!="全部" )
	{
		$sql .= " b.cat_type1 = '".$where['cat_type']."' and " ; //新款式分类
	}
	if(isset($where['tuo_type']) && $where['tuo_type']!='0')
	{
		$sql .= " b.tuo_type= '".$where['tuo_type']."' and ";   //空拖类型
	}
	if(isset($where['zhushi_begin']) && isset($where['zhushi_end']) && $where['zhushi_begin']!="" && $where['zhushi_end']!="" && $where['zhushi_end']!=0 )
	{
	$sql .=" b.zuanshidaxiao >= '".$where['zhushi_begin']."' and b.zuanshidaxiao <='".$where['zhushi_end']."' and ";	
	}
	$sql .= " c.policy_id not in ($undefaultids) and 1 ";
	/*
	$sql .=" and a.goods_id in('150922095292',
'150922095289',
'150922095288',
'150922095285',
'150922095279',
'150922095278',
'150617597755',
'150411533825',
'150411533824',
'150411533823',
'150411533822',
'150411533821',	
'150922080695',
'150922080694',
'150922080693',
'150702681082',
'150701609109',
'150702676117',
'150702676115',
'150702671048',
'150702650784',
'150702648040',	
'150922093357', 
'150922093356',
'150922093355', 
'150922093354',
'150922093353', 
'150922091157',
'150922086588',
'150922086587',
'150922086586',
'150922082077',
'150922093313',
'150922091560',
'150922091559',
'150922091558',
'150922082073',
'150922080727',
'150922080718',
'150922080714',
'150922080713',
'150922080277',
'150702679140',
'150702677658'
)";*/
	$result = mysqli_query($conn,$sql);
	if($result)
	{
		$num = $result->num_rows;
		if($num<1)
		{
			return $goodsarr;
		}else{
			while($obj = mysqli_fetch_assoc($result))
			{
				
				//目前的销售政策id
				$now_policyid = $obj['policy_id'];
				$goodsid = $obj['goods_id'];
				
				//如果商品已经处理过了直接就跳出此次循环
				if(in_array($goodsid,$goodsarr))
				{
					continue;
				}
				
				//删除关联了默认销售政策的商品信息
				$policy_ids = implode(',',$yxpolicyidsarr);
				$delsql = "delete from app_salepolicy_goods where goods_id='".$goodsid."'";
				$delsql .= " and policy_id in($policy_ids)";
				mysqli_query($conn,$delsql);
				//记录日志
				recordLog('action',$delsql);
				
				
				//去掉appid
				unset($obj['appid']);
				//更改政策为最新的政策id
				$obj['policy_id'] = $policyid;
				//整理好数据之后直接入库
				$obj['jiajia'] = $jiajia;
				$obj['sta_value'] = $sta_value;
				
				//入库 返回处理的商品id
				$res = insertappgoods($obj,$conn);
				array_push($goodsarr,$res);
			}
			return $goodsarr;
		}	
	}else{
		return $goodsarr;	
	}
}





//插入数据到表app_salepolicy_goods
function insertappgoods($goodsinfo,$conn)
{
	$str = '';
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
	if($result)
	{
		//如果成功
		$str = "商品id ".$goodsinfo['goods_id'].'添加最新的默认销售政策'.$goodsinfo['policy_id'].'成功<br/>';
		//更改 是否有政策的值
		$upsql = "update base_salepolicy_goods set is_policy=2 where goods_id='".$goodsinfo['goods_id']."'";
		mysqli_query($conn,$upsql);
		$str .= "商品id ".$goodsinfo['goods_id'].'状态更新为已经绑定销售政策 is_policy设置为2<br/>';
		recordLog('action_ok', $str);
	}else{
		//如果失败
		$str = "商品id ".$goodsinfo['goods_id'].'添加最新的默认销售政策'.$goodsinfo['policy_id'].'失败<br/>';
		recordLog('action_err', $str);
	}
	$returninfo = $goodsinfo['goods_id'];
	return $returninfo;
	/*
	//记录日志
	recordLog('action', $str);
	//$returninfo = $goodsinfo['goods_id'].'-'.$goodsinfo['policy_id'];
	*/
}


/*------------------------------------------------------ */
//-- 记录日志信息
//-- by 刘林燕
/*------------------------------------------------------ */
function recordLog($api, $str)
{
	if (!file_exists(ROOT_LOG_PATH . 'logs/goods_auto_link_qx'))
	{
		mkdir(ROOT_LOG_PATH . 'logs/goods_auto_link_qx', 0777);
		chmod(ROOT_LOG_PATH . 'logs/goods_auto_link_qx', 0777);
	}
	$content = date("Y-m-d H:i:s")."||\r\n".$str."\r\n";
	$file_path =  ROOT_LOG_PATH . 'logs/goods_auto_link_qx/'.date('Y')."_".date('m')."_".date('d')."$api.txt";
	file_put_contents($file_path, $content, FILE_APPEND );
}


?>