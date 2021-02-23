<?php
include('../taobaoapi.php');
include('../include/giftconfig.php');
$s_data = '2015-09-29 00:00:00';
$e_data = '2015-09-29 23:59:59';
$allids = $apiModel->getTaobaoOrderList($s_data,$e_data,1,10);
$orderModel = new OrderClassModel();
foreach($allids as $key=>$id)
{
	$info = $apiModel->getorderinfo($id);
	//print_r($info);
	if($info->code)
	{
		echo '没有找到订单id的信息';
		echo '<hr/>';
		continue;
	}
	$order = $info->trade->orders->order;
	foreach($order as $v)
	{
		
		//根据商品的标题和款号录入赠品
		$title = $v->title;
		$goods_sn = $v->outer_sku_id == "" ? $v->outter_iid : $v->outer_sku_id;
		if($goods_sn =="")
		{
			$goods_sn = 'other';	
		}
		//获取赠品
		$goods_gift = getgift($title,$goods_sn);
		if(!empty($goods_gift))
		{
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
				$goods_gift['gift_id'] = $giftlist['id'];
			}
			//赠品入库订单商品表
			
			$order_gift=array(
				'order_id'=>1,
				'goods_id'=>0,
				'goods_sn'=>$goods_gift['goods_sn'],
				'ext_goods_sn'=>0,
				'goods_name'=>$goods_gift['name'],
				'goods_price'=>0,
				'favorable_price'=>0,
				'goods_count'=>1,
				'create_time'=>date("Y-m-d H:i:s"),
				'modify_time'=>date("Y-m-d H:i:s"),
				'create_user'=>'新娟',
				'details_status'=>0,
				'send_good_status'=>4,
				'buchan_status'=>9,
				'is_stock_goods'=>1,//赠品默认为现货
				'is_return'=>0,
				'details_remark'=>$goods_gift['desc'],
				'xiangqian'=>0,
				'favorable_status'=>3,
				'cat_type'=>$goods_gift['cat_type'],
				'product_type'=>$goods_gift['product_type'],
				'is_zp'=>1,
				'is_finance'=>1
			);
			$orderModel->autoinsert('app_order_details',$order_gift);
			//入库rel_gift_order
			//__CLASS__
			$order_id = 1;
			$relgift['order_id'] =$order_id;
			$relgift['gift_id'] = $goods_gift['gift_id'];
			$relgift['gift_num'] = 1;  //默认为一个 以赠品备注为主
			$relgift['remark'] = $goods_gift['desc'];
			$orderModel->autoinsert('rel_gift_order',$relgift);
			
		}
		
	}
	
}



function getgift($title,$goods_sn)
{
	global $gift;
	$newtitle = str_replace(' ','',trim($title));
	$name1 = strtolower(substr($newtitle,-1));
	$name2 = strtolower(substr($newtitle,-2));
	//定义单字母数组
	$array1 = array('h','b','k','d','q','p');
	//定义双字母数组
	$array2 = array('hz','hx','hd','bz','bx','bd','be','kz','kx','kd','ke','dz','dd','de','qz','qd','qe');
	$goods_gift = array();
	if(in_array($name2,$array2))
	{
		if(isset($gift[$name2]) && !empty($gift[$name2]))
		{
			$goods_sn_arr = array_keys($gift[$name2]);
			if(in_array($goods_sn,$goods_sn_arr))
			{
				$goods_gift = $gift[$name2][$goods_sn];
			}else{
				$goods_gift = $gift[$name2]['other'];	
			}
		}
	}
	return $goods_gift;
}

	
?>