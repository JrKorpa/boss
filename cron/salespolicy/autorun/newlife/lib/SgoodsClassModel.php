<?php
class SgoodsClassModel extends PdoModel
{
	public function __construct()
	{
		
		parent::__construct();
	}
	public function __distuct()
	{
		//
	}
	
	//结束时间已到的销售政策自动失效
	public function autodelete()
	{
		$timenow = date('Y-m-d H:i:s');
		//先让政策失效
		$sql = "update front.base_salepolicy_info set is_delete=1 where policy_end_time <= '".$timenow."'";
		$this->mysqli->query($sql);
		
		//再让绑定了销售政策的商品失效
		$sql = " update front.app_salepolicy_goods as a 
		inner join front.base_salepolicy_info as b on a.policy_id = b.policy_id 
		set a.is_delete=2 where b.is_delete=1";
		return $this->mysqli->query($sql);
	}
	
	//找到正常的销售政策
	public function getallokinfo($isqh=0)
	{
		$data = array();
		$huo_type = array(1,2);
		if($isqh==1)
		{
			$huo_type = array(0,2);
		}
		$sql = " select 
		policy_id,jiajia,sta_value,product_type,huopin_type, 
		cat_type,tuo_type,zhushi_begin,zhushi_end,range_begin,range_end   
		from front.base_salepolicy_info   
		where is_kuanprice = 0 and is_default=1 and is_delete = 0 and bsi_status = 3 
		and huopin_type in(".implode(',',$huo_type).") order by policy_id desc ";
		$result = $this->mysqli->query($sql);
		if($result)
		{
			while($obj = $result->fetch_assoc())
			{
				$data[] = $obj;
			}
		}
		return $data;
	}
	
	//获取可销售商品列表中所有满足条件的现货
	public function getallxianhuogoods($where)
	{
		//销售政策id
		$where = array_filter($where);
		$sql = " select 
		a.isXianhuo,
		a.chengbenjia,
		b.goods_id,b.product_type1,b.cat_type1,
		b.zuanshidaxiao,b.jietuoxiangkou,b.tuo_type 
	from 
		front.base_salepolicy_goods as a
		inner join warehouse_shipping.warehouse_goods as b on a.goods_id=b.goods_id
	where 
		a.is_sale=1 and a.isXianhuo=1 and ";
		
		if(isset($where['product_type']) && $where['product_type'] !='全部')
		{
			$sql .= " b.product_type1 = '".$where['product_type']."' and " ; //新产品线
		}
		//只有全部的时候不用匹配
		if(isset($where['cat_type']) && $where['cat_type']!="全部" )
		{
			$sql .= " b.cat_type1 = '".$where['cat_type']."' and " ; //新产品线
		}
		if(isset($where['tuo_type']))
		{
			$sql .= " b.tuo_type= '".$where['tuo_type']."' and ";
		}
		if(isset($where['range_begin']))
		{
			$sql .=" b.jietuoxiangkou >= '".$where['range_begin']."' and ";	
		}
		if(isset($where['range_end']))
		{
			$sql .=" b.jietuoxiangkou <= '".$where['range_end']."' and ";	
		}
		if(isset($where['zhushi_begin']))
		{
			$sql .=" b.zuanshidaxiao >= '".$where['zhushi_begin']."' and ";	
		}
		if(isset($where['zhushi_end']))
		{
			$sql .=" b.zuanshidaxiao <= '".$where['zhushi_end']."' and ";	
		}
		$sql .= " 1 " ;
		$result = $this->mysqli->query($sql);
		$data = array();
		if($result){
			while($obj = $result->fetch_assoc())
			{
				$data[] = $obj;
			}
		}
		return $data;
	}
	
	//检查同一个货号,同一个销售政策是否存在
	public function checkgoods($goodsid,$policy_id)
	{
		$sql = "select id  from front.app_salepolicy_goods where goods_id = '".$goodsid."' and policy_id='".$policy_id."'";
		$result = $this->mysqli->query($sql);
		$data = array();
		if($result)
		{
			while($obj=$result->fetch_assoc())
			{
				$data[] = $obj;
			}
		}
		return $data;
	}
	
	
	public function GetBaoxianFei($xiangkou)
	{
		//拿取保险费
		$xiangkou = $xiangkou * 10000;
		$i = 0;
		$j = 0;
		$k = 0;
		$sql = 'SELECT `id`,`min`,`max`,`price`,`status` FROM front.`app_style_baoxianfee` WHERE 1';
		$baoxianfei = $this->mysqli->query($sql);
		while($v = $baoxianfei->fetch_assoc())
		{
			$max[$i] = $v['max'] * 10000;
			$min[$j] = $v['min'] * 10000;
			$fee[$k] = $v['price'];
			$i++;$j++;$k++; 
		}
		$count = count($max);
		for($i = 0; $i <$count; $i ++) 
		{
			if ($xiangkou >= $min[$i] && $xiangkou <= $max[$i])
			{
				return $fee[$i];
			}
		}
	}


	//获取保险费的值 金托类型和主石大小镶口值
	public function getbxfinfo($data)
	{
		//保险费
		$baoxianfei = '';
		if(!empty($data))
		{
			if($data['tuo_type']>1)
			{
				//托类型
				//获取镶口
				$xiankou = $data['jietuoxiangkou'];
				if(!empty($xiankou) && $xiankou > 0)
				{
					$getbxf_data = $xiankou;
				}else{
					$getbxf_data = $data['zuanshidaxiao'];
				}
				$baoxianfei = $this->GetBaoxianFei($getbxf_data);
			}else{
				$baoxianfei = 0;	
			}
		}
		return $baoxianfei;
	}
	
	
	//插入数据到表app_salepolicy_goods
	public function insertappgoods($goodsinfo)
	{
		//定义所有需要加保险费用的产品线
		$allproducttype = array('钻石','珍珠','珍珠饰品','翡翠','翡翠饰品','宝石','宝石饰品','钻石饰品','宝石饰品','宝石');
		//定义保险费默认值
		$baoxianfei = 0;
		//判断是否需要拿取保险费 (镶嵌类的现货,拖类型)
		if(in_array($goodsinfo['product_type1'],$allproducttype) && $goodsinfo['isXianhuo']==1 && $goodsinfo['tuo_type']>1)
		{
			//拿取保险费的值
			$baoxianfei = $this->getbxfinfo($goodsinfo);
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
		$sql = "insert into front.app_salepolicy_goods";
		$sql .= " ($allkeys) ";
		$sql .= "values('$allvalue')";
		$result = $this->mysqli->query($sql);
		$returninfo ='';
		if($result)
		{
			$upsql = "update front.base_salepolicy_goods set is_policy=2 where goods_id='".$goodsinfo['goods_id']."'";
			$this->mysqli->query($upsql);
		}else{
			//如果失败就只返回goods_id自身
			$str = "商品id ".$goodsinfo['goods_id'].'添加最新的默认销售政策'.$goodsinfo['policy_id'].'失败<br/>';
			echo $str;
		}
	}
	
	
	
	//获取期货信息
	//获取可销售商品列表中所有满足条件的现货
	public function getallqihuogoods($where,$products,$cates)
	{
		//销售政策id
		$where = array_filter($where);
	$sql = "
		select
			isXianhuo,chengbenjia,
			goods_id,product_type,
			category,stone,xiangkou 
		from front.base_salepolicy_goods
		where is_sale=1 and isXianhuo=0 and ";
		if(isset($where['product_type']) && $where['product_type'] !='全部')
		{
			$product_type = $products[$where['product_type']];
			$sql .= " product_type = $product_type and " ; //新产品线
		}
		//只有全部的时候不用匹配
		if(isset($where['cat_type']) && $where['cat_type']!="全部" )
		{
			$category = $cates[$where['category']];
			$sql .= " category = $category and " ; //新产品线
		}
		if(isset($where['range_begin']))
		{
			$sql .=" xiangkou >= '".$where['range_begin']."' and ";	
		}
		if(isset($where['range_end']))
		{
			$sql .=" xiangkou <= '".$where['range_end']."' and ";	
		}
		if(isset($where['zhushi_begin']))
		{
			$sql .=" stone >= '".$where['zhushi_begin']."' and ";	
		}
		if(isset($where['zhushi_end']))
		{
			$sql .=" stone <= '".$where['zhushi_end']."' and ";	
		}
		$sql .= " 1 " ;
		$result = $this->mysqli->query($sql);
		$data = array();
		if($result){
			while($obj = $result->fetch_assoc())
			{
				$data[] = $obj;
			}
		}
		return $data;
	}
}
?>