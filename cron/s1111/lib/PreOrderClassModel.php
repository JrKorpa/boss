<?php
class PreOrderClassModel extends PdoModel
{
	public function __construct()
	{
		parent::__construct();
	}
	//(1)检测外部订单是否已经录入过了
	public function check($out_order_sn)
	{
		//为了兼容boss所以去之前的手动录单那里做一次查询
		$boss_sql = "select * from rel_out_order where out_order_sn='".$out_order_sn."'";
		$result = $this->mysqli->query($boss_sql);
		$rows = $result->num_rows;
		if($result && $rows > 0)
		{
			return 1;
		}else{
			$cksql = "select * from s11_order_info where out_order_sn = '".$out_order_sn."'";
			$ckresult = $this->mysqli->query($cksql);
			$ckrows = $ckresult->num_rows;
			if($ckrows > 0)
			{
				return 1;
			}else{
				return 0;
			}
		}
	}
	
	//获取订单的信息
	public function getresult($out_order_sn)
	{
		$data = array();
		$cksql = "select * from s11_order_info where out_order_sn = '".$out_order_sn."'";
		$result = $this->mysqli->query($cksql);
		if($result)
		{
			$data = $result->fetch_assoc();
		}
		return $data;
	}
	
	//外部导单成功与否记录日志
	public function recordInsert($data)
	{
		if(empty($data))
		{
			return false;
		}
		$recordlog['out_order_sn']= isset($data['out_order_sn'])?$data['out_order_sn']:0;
		$orderdlog['order_id'] = isset($data['order_id'])?$data['order_id']:0;
		$recordlog['order_sn']= isset($data['order_sn'])?$data['order_sn']:0;
		$recordlog['res'] = isset($data['res'])?$data['res']:0;
		$recordlog['reason'] = isset($data['reason'])?$data['reason']:0;
		$recordlog['ispreorder'] = isset($data['ispreorder']) ? $data['ispreorder'] : 1;   
		//预售订单 2是等待买家付款的非预售单出错的状态
		$recordlog['order_status'] = isset($data['order_status']) ? $data['order_status']:'无';
		$recordlog['add_time'] = date('Y-m-d H:i:s');
		$key = implode(',',array_keys($recordlog));
		$val = implode("','",array_values($recordlog));
		$sql = "insert into s11_order_info($key) values('$val')";
		$this->mysqli->query($sql);
		return $this->mysqli->insert_id;
	}
	
	
	//自动录入信息
	public function autoinsert($tablename,$data)
	{
		$key = implode(',',array_keys($data));
		$val = implode("','",array_values($data));
		$sql = "insert into $tablename($key) values('$val')";
		echo $sql.'<br/>';
		$this->mysqli->query($sql);
		return $this->mysqli->insert_id;
	}
	
	
	//根据条件匹配商品
	public function getgoodsinfo($where,$num=1)
	{
		$groupby = 'a.id';
		$sql = " select
		a.goods_id,
		a.goods_sn,
		a.goods_name,
		a.qiegong as cut,
		a.jingdu as clarity,
		a.yanse as color,
		a.zhengshuleibie as cert,
		a.zhengshuhao as zhengshuhao,
		a.caizhi,
		a.zhushiyanse as jinse,
		a.jinzhong,
		a.shoucun as zhiquan,
		a.ziyin as kezi,
		a.product_type1,
		a.cat_type1,
		a.jietuoxiangkou as xiangkou,
		a.mingyichengben as chengbenjia
	from
		warehouse_shipping.warehouse_goods as a 
	where a.order_goods_id < 1 and a.is_on_sale=2 and ";
		if(isset($where['goods_sn']) && !empty($where['goods_sn']))
		{
			$sql .= " a.goods_sn = '".$where['goods_sn']."' and ";
		}
		if(isset($where['gold_weight_min']) && $where['gold_weight_min'] > 0 )
		{
			$sql .= " a.jinzhong >= '".$where['gold_weight_min']."' and ";
			$groupby = 'a.jinzhong';
		}
		if(isset($where['gold_weight_max']) && $where['gold_weight_max'] > 0 )
		{
			$sql .= " a.jinzhong <= '".$where['gold_weight_max']."' and ";
			$groupby = 'a.jinzhong';
		}
		
		if(isset($where['shoucun']) && !empty($where['shoucun']))
		{
			$scmin = ( $where['shoucun'] - 0.5 )>0 ? $where['shoucun'] - 0.5 : 0 ;
			$scmax = $where['shoucun'] + 0.5;
			$sql .=" a.shoucun >= '".$scmin."' and shoucun <= '".$scmax."' and ";
		}
		if(isset($where['ziyin']) && !empty($where['ziyin']))
		{
			$sql .=" a.ziyin ='".$where['ziyin']."' and ";
		}
		if(isset($where['caizhi']) && !empty($where['caizhi']))
		{
			$caizhi = explode(',',$where['caizhi']);
			$caizhi = array_filter($caizhi);
			$caizhi = array_unique($caizhi);
			$caizhi = implode("','",$caizhi);
			$sql .=" a.caizhi in('$caizhi') and ";
		}
		if(isset($where['zuanshidaxiao']) && !empty($where['zuanshidaxiao']))
		{
			echo $where['zuanshidaxiao'];
			$plus = bcsub($where['zuanshidaxiao'],0.1,3);
			if($plus>0)
			{
				$max = bcadd($where['zuanshidaxiao'],0.03,3);   //规则主砖10分以上允许在0.03的浮动范围内
			}else{
				$max = bcadd($where['zuanshidaxiao'],0.01,3);   //规则主砖10分以上允许在0.01的浮动范围内
			}
			$sql .=" a.zuanshidaxiao >= '".$where['zuanshidaxiao']."' and zuanshidaxiao <= '".$max."' and ";
		}
		
		
		if(isset($where['fushizhong'])&& !empty($where['fushizhong']))
		{
			$sql .=" a.fushizhong = '".$where['fushizhong']."' and ";
		}
		if(isset($where['fushilishu'])&& !empty($where['fushilishu']))
		{
			$sql .=" a.fushilishu = '".$where['fushilishu']."' and ";
		}
		
		
		if(isset($where['zhushiyanse']) && !empty($where['zhushiyanse']))
		{
			$sql .=" a.zhushiyanse in (".$where['zhushiyanse'].") and ";
		}
		if(isset($where['zhushijingdu']) && !empty($where['zhushijingdu']))
		{
			$sql .=" a.zhushijingdu in(".$where['zhushijingdu'].") and ";
		}
		if(isset($where['warehouseid']) &&!empty($where['warehouseid']))
		{
			$sql .=" a.warehouse_id in('".$where['warehouseid']."') and ";
		}
		$sql .=" 1 order by a.zuanshidaxiao asc,a.jinzhong asc,$groupby asc limit $num";
		echo $sql.'<br/>';
		return $this->mysqli->query($sql);
	}
	
	//修改订单为期货单
	public function updateXianhuo($isxianhuo=1,$id)
	{
		$upsql = "update base_order_info set is_xianhuo=$isxianhuo where id='".$id."'";
		return $this->mysqli->query($upsql);
	}
	
	
	//修改商品绑定订单
	public function updateorderid($orderid,$goodsid)
	{
		$sqlup = "update warehouse_shipping.warehouse_goods set order_goods_id= '".$orderid."' where ";
		$sqlup .=" goods_id='".$goodsid."' ";
		return $this->mysqli->query($sqlup);
	}
	
	
	//把所有的款式分类和产品线拿出来，加快匹配商品的速度
	public function getallproducttype()
	{
		$sql = "select product_type_id,product_type_name from front.app_product_type order by product_type_id asc";
		$result = $this->mysqli->query($sql);
		$allproducttype = array();
		while($obj=$result->fetch_assoc())
		{
			$name = $obj['product_type_name'];
			$allproducttype[$name] = $obj['product_type_id'];
		}
		return $allproducttype;
	}
	
	public function getallcattype()
	{
		$sql = "select cat_type_id,cat_type_name from front.app_cat_type order by cat_type_id asc";
		$result = $this->mysqli->query($sql);
		$allcattype = array();
		while($obj=$result->fetch_assoc())
		{
			$name = $obj['cat_type_name'];
			$allcattype[$name] = $obj['cat_type_id'];
		}
		return $allcattype;
	}
	
	//获取省，城市和区的id
	public function getcityid($cityname,$parentid=null)
	{
		$sql = "select * from cuteframe.region where region_name='".$cityname."'";
		if( $parentid > 0 )
		{
			$sql .=" and parent_id='".$parentid."' ";
		}
		$sql .=" limit 1 ";
		$result = $this->mysqli->query($sql);
		$regioninfo = array();
		if($result)
		{
			if($result->num_rows>0)
			{
				$regioninfo = $result->fetch_assoc();
			}
		}
		return $regioninfo;
	}
	//根据传过来的值组装id
	public function getcityids($province,$city,$regional)
	{
		$allcityinfo = array(
			'province_id'=>'',
			'city_id'=>'',
			'regional_id'=>''
		);
		$province_arr = $this->getcityid($province);
		if(!empty($province_arr))
		{
			$allcityinfo['province_id'] = $province_arr['region_id'];
		}
		$city_arr = $this->getcityid($city,$allcityinfo['province_id']);
		if(!empty($city_arr))
		{
			$allcityinfo['city_id']= $city_arr['region_id'];
		}
		$regional_arr = $this->getcityid($regional,$allcityinfo['city_id']);
		if($regional_arr)
		{
			//echo $regional.'的id是'.$regional_arr['region_id'];
			$allcityinfo['regional_id']= $regional_arr['region_id'];
		}
		return $allcityinfo;
	}
	
	
	
	//统计方法
	//默认统计成功的
	public function countorder($where)
	{
		$numarr = array(
			'ok'=>0,
			'false'=>0
		);
		$sql ="select count(*) as count,res from app_order.s11_order_info ";
		$sql .=" where add_time>= '".$where['btime']."' and add_time <= '".$where['etime']."' ";
		$sql .=" group by res order by res desc";
		$result = $this->mysqli->query($sql);
		while($obj=$result->fetch_assoc())
		{
			if($obj['res']==1)
			{
				$numarr['ok']= $obj['count'];
			}else{
				$numarr['false'] +=$obj['count'];	
			}
		}
		return $numarr;
	}
	
	//获取已经抓单的现货和期货
	public function countxianhuo($where)
	{
		$numarr = array(
			'xianhuo'=>0,
			'qihuo'=>0
		);
		$sql = "select count(bi.id) as count,bi.is_xianhuo from app_order.base_order_info as bi ";
		$sql .=" inner join  app_order.s11_order_info as a on a.order_id=bi.id ";
		$sql .=" where a.res=1 ";
		$sql .= " and add_time>= '".$where['btime']."' and add_time <= '".$where['etime']."'";
		$sql .=" group by bi.is_xianhuo order by bi.is_xianhuo desc";
		$result = $this->mysqli->query($sql);
		if($result)
		{
			while($obj=$result->fetch_assoc())
			{
				if($obj['is_xianhuo'] ==1)
				{
					$numarr['xianhuo'] = $obj['count'];
				}else{
					$numarr['qihuo'] = $obj['count'];	
				}
			}
		}
		return $numarr;
	}
	//付款金额
	public function countmoney($where)
	{
		$ordermoney = array('total'=>0,'paid'=>0,'unpaid'=>0);
		$sql ="select sum(a.order_amount) as total,sum(a.money_paid) as paid,sum(a.money_unpaid) as unpaid ";
		$sql .=" from app_order.app_order_account as a ";
		$sql .=" inner join app_order.s11_order_info as b on a.order_id=b.order_id ";
		$sql .=" where b.res=1 and add_time>= '".$where['btime']."' and add_time <= '".$where['etime']."'";
		$result = $this->mysqli->query($sql);
		if($result)
		{
			$ordermoney = $result->fetch_assoc();
		}
		return $ordermoney;
	}
	
	//获取所有的订单信息
	public function getorderlist()
	{
		$sql = 'select * from s11_order_info where 1';
		$result = $this->mysqli->query($sql);
		return $result;
	}
	
	//根据款号去拿取材质信息等
	//根据款去拿取材质
	public function getstyleinfo($goods_sn)
	{
		$sql = "select product_type,style_type,is_zp,is_xz from front.base_style_info where style_sn='".$goods_sn."'";
		$result = $this->mysqli->query($sql);
		$styleinfo = array();
		if($result)
		{
			$styleinfo = $result->fetch_assoc();	
		}
		return $styleinfo;
	}
	
	//根据款号去拿取赠品列表中的赠品信息
	public function getgiftinfo($goods_sn)
	{
		$sql = "select id,price,sell_sprice from app_order.gift_goods where goods_number='".$goods_sn."'";
		$result = $this->mysqli->query($sql);
		$giftinfo = array();
		if($result)
		{
			$giftinfo = $result->fetch_assoc();
		}
		return $giftinfo;
	}
	
	
	//获取所有失败的信息
	public function getfalsedata($where,$sendgoods=1,$time)
	{
		$sql = "select out_order_sn,reason from s11_order_info where res=0 ";
		if($sendgoods>0)
		{
			$sql .= " and order_status='WAIT_SELLER_SEND_GOODS' ";
		}
		if(isset($time['btime']) && !empty($time['btime']))
		{
			$sql .=" and add_time >='".$time['btime']." 00:00:00' ";
		}
		if(isset($time['etime']) && !empty($time['etime']))
		{
			$sql .=" and add_time <='".$time['etime']." 23:59:59' ";
		}
		$sql .= " and $where ";
		$result = $this->mysqli->query($sql);
		return $result;
	}
	
}
?>