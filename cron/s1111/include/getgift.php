<?php
//根据订单价格 和 订单支付时间 来送赠品
//订单id
//制单人
//价格
function getgiftbyprice($order_id,$createname,$price,$order_pay_time='',$yushou=false)
{

	global $tianmao_gift;
	if(empty($order_pay_time))
		return null;
	$goods_gift = array();
	if($yushou===true){
		if($order_pay_time>='2018-11-11 00:00:00' && $order_pay_time<='2018-11-11 02:00:00'){
			if($price>=3999)
			{
				$goods_gift[] = $tianmao_gift['999'];
				$goods_gift[] = $tianmao_gift['1499'];
				$goods_gift[] = $tianmao_gift['3999'];
			}elseif($price>=1499)
			{
				$goods_gift[] = $tianmao_gift['999'];
				$goods_gift[] = $tianmao_gift['1499'];
			}else{
				$goods_gift[] = $tianmao_gift['999'];
			}
	    }
		if($order_pay_time>'2018-11-11 02:00:00' && $order_pay_time<'2018-11-12 00:00:00'){
			if($price>=3999)
			{	
			    $goods_gift[] = $tianmao_gift['999'];		
				$goods_gift[] = $tianmao_gift['3999'];
			}else{
				$goods_gift[] = $tianmao_gift['999'];
			}
	    } 
	}else{
		if($order_pay_time>='2018-11-11 00:00:00' && $order_pay_time<='2018-11-11 02:00:00'){
			if($price>=3999)
			{
				$goods_gift[] = $tianmao_gift['1499'];
				$goods_gift[] = $tianmao_gift['3999'];
			}elseif($price>=1699)
			{
				$goods_gift[] = $tianmao_gift['999'];
				$goods_gift[] = $tianmao_gift['1499'];
			}elseif($price>=999){
				$goods_gift[] = $tianmao_gift['999'];
			}
	    }
		if($order_pay_time>'2018-11-11 02:00:00' && $order_pay_time<'2018-11-12 00:00:00'){
			if($price>=3999)
			{			
				$goods_gift[] = $tianmao_gift['3999'];
			}elseif($price>=999){
				$goods_gift[] = $tianmao_gift['999'];
			}
	    } 		
	}   

    $result = array();
    if(empty($goods_gift))
        return array();  
    //print_r($goods_gift);
    foreach ($goods_gift as $key => $g) {
    	$res = array();
    	if(empty($g['goods_sn']))
    		$res['details_remark'] = '赠送无款号赠品:'.$g['desc'] . $g['name'];
    	else
    		$res = combinedata($order_id,$createname,$g);

    	if(!empty($res))
    	    $result[] = $res;	
    }
	
	return $result;
}
//根据商品的标题和款号录入赠品
function getgiftinfo($order_id,$title,$createname,$goods_sn,$giftcaizhi=0)
{
	//if(date('Y-m-d H:i:s')<'2018-11-11 00:00:00')
	//	return null;
	$goods_gift = array();
	if(empty($title))
	{
		return $goods_gift;
	}
	if(empty($goods_sn))
	{
		$goods_sn = 'default';	
	}
	
	//根据赠品配置获取赠品
	$goods_gift = getgift($title,$goods_sn,$giftcaizhi);
	if(!empty($goods_gift))
	    $goods_gift = combinedata($order_id,$createname,$goods_gift);
    
	return $goods_gift;
}

function combinedata($order_id,$createname,$goods_gift)
{
	global $orderModel;
	if(empty($goods_gift) || !is_array($goods_gift))
	{
		$goods_gift = array();
		return $goods_gift;
	}
	//给 即将有的附一个默认值
	$goods_gift['product_type']='';
	$goods_gift['style_type'] = '';
	$goods_gift['gift_id'] = '';
	//拿取赠品的产品线和款式分类信息
	$giftinfo = $orderModel->getstyleinfo($goods_gift['goods_sn']);
	if(!empty($giftinfo))
	{
		$goods_gift['product_type'] = $giftinfo['product_type'];
		$goods_gift['cat_type'] = $giftinfo['style_type'];
	}
	//拿取赠品列表中的赠品id
	$giftlist = $orderModel->getgiftinfo($goods_gift['goods_sn']);

	if(!empty($giftlist))
	{
		$goods_gift['name'] = $giftlist['name'];
		$goods_gift['gift_id'] = $giftlist['id'];
		$goods_gift['is_finance'] = $giftlist['is_xz'];
	}else{
		return array();
	}
	//赠品入库订单商品表	
	$order_gift=array(
		'order_id'=>$order_id,
		'goods_id'=>0,
		'goods_sn'=>$goods_gift['goods_sn'],
		'ext_goods_sn'=>0,
		'goods_name'=>$goods_gift['name'],
		'goods_price'=>0,
		'favorable_price'=>0,
		'goods_type'=>'zp',
		'tuo_type'=>'成品',
		'goods_count'=>1,
		'create_time'=>date("Y-m-d H:i:s"),
		'modify_time'=>date("Y-m-d H:i:s"),
		'create_user'=> empty($createname) ? '':$createname,
		'details_status'=>1,
		'send_good_status'=>1,
		'buchan_status'=>9,
		'is_stock_goods'=>1,//赠品默认为现货
		'is_return'=>0,
		'details_remark'=>$goods_gift['desc'],
		'xiangqian'=>'不需工厂镶嵌',
		'favorable_status'=>3,
		'cat_type'=>$goods_gift['cat_type'],
		'product_type'=>$goods_gift['product_type'],
		'is_zp'=>1,
		'is_finance'=>$goods_gift['is_finance']
	);
	
	$goods_gift['order_gift']=$order_gift;
	//$orderModel->autoinsert('app_order_details',$order_gift);
	//入库rel_gift_order
	//__CLASS__
	$relgift['order_id'] =$order_id;
	$relgift['gift_id'] = $goods_gift['gift_id'];
	$relgift['gift_num'] = 1;  //默认为一个 以赠品备注为主
	//$relgift['remark'] = '';
	//$orderModel->autoinsert('rel_gift_order',$relgift);
	$goods_gift['rel_gift_order'] = $relgift;
	
	return $goods_gift;
}


function getgift($title,$goods_sn,$giftcaizhi=0)
{
	global $gift;
	$goods_sn = trim($goods_sn);
	$newtitle = str_replace(' ','',trim($title));
	$name1 = strtolower(substr($newtitle,-1));
	$name2 = strtolower(substr($newtitle,-2));
	
	//定义单字母数组
	$array1 = array('h','b','k','d','q','p');
	//定义双字母数组
	$array2 = array('hd','bd','dd');
	$goods_gift = array();
	if(in_array($name2,$array2))
	{
		if(isset($gift[$name2]) && !empty($gift[$name2]))
		{
			$goods_sn_arr = array_keys($gift[$name2]);
			if(!empty($gift[$name2][$goods_sn]))
			{
				$goods_gift = $gift[$name2][$goods_sn];
			}else{
				$goods_gift = $gift[$name2]['default'];	
			}
		}
	}

	
	return $goods_gift;
}	
?>