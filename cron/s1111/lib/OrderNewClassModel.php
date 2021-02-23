<?php
class OrderNewClassModel extends PdoModel
{
	public function __construct()
	{
		parent::__construct();
	}
	public function __distuct()
	{
		//
	}

	public function gettaobaoids(){
		$sql = " select * from tmp_618_taobao_800 ";
		$result = $this->mysqli->query($sql);
		$data = array();
		while($obj=$result->fetch_assoc())
		{
			array_push($data,$obj);
		}
		return $data;		
	}
	
	
	//(1)检测外部订单是否已经录入过了
	public function check($out_order_sn)
	{
		//为了兼容boss所以去之前的手动录单那里做一次查询
		$boss_sql = "select a.* from rel_out_order as a 
		left join base_order_info as b on a.order_id=b.id 
		where a.out_order_sn='".$out_order_sn."' and b.order_status < 3 limit 1";   //待审核的和已经审核的就不能再次录入了
		$result = $this->mysqli->query($boss_sql);
		$rows = $result->num_rows;
		if($result && $rows > 0)
		{
			return 1;
		}else{
			return 0;
		}
	}
	
	/**
	 * add by lly 
	 * date 2017-06-22
	 * 获取618需要绑定货品的订单
	 */
	public function get_needbdgs_order()
	{
		$sql ="select a.id,b.id as detailid,b.goods_sn,a.order_status from app_order_details as b 
		inner join base_order_info as a on b.order_id=a.id 
		where a.order_status<3 and b.goods_id < 1 and b.goods_sn !='' and a.referer='618抓单' ";
		$result = $this->mysqli->query($sql);
		$data = array();
		while($obj=$result->fetch_assoc())
		{
			array_push($data,$obj);
		}
		return $data;
	}
	public function getbdgoods($gsn)
	{
		$sql = "select goods_id from warehouse_shipping.warehouse_goods where goods_sn='".$gsn."' and order_goods_id <1 and is_on_sale=2 limit 1";
		//$sql = "select goods_id from warehouse_shipping.warehouse_goods where goods_sn='".$gsn."' limit 1";
		$result = $this->mysqli->query($sql);
		return $result->fetch_assoc();
	}
	
	//货品绑定订单
	public function upbdgoods($goodsid,$ordergoodsid)
	{
		$sql = "update warehouse_shipping.warehouse_goods set order_goods_id='".$ordergoodsid."' where goods_id ='".$goodsid."' ";
		echo $sql;
		return $this->mysqli->query($sql);
	}
	//订单绑定货品
	public function uporderdetail($orderid,$detaildid,$goodsid)
	{
		if($orderid< 1 || $detaildid < 1 || $goodsid < 1){
			return ;	
		}
		$sql ="update base_order_info set is_xianhuo=1 where id='".$orderid."'";
		echo $sql;
		$this->mysqli->query($sql);
		//更改订单明细 
		$dtsql = "update app_order_details set is_stock_goods=1,goods_id='".$goodsid."' where id='".$detaildid."'";
		echo $dtsql;
		$this->mysqli->query($dtsql);
	}
	
	
	
	//创建BDD订单编号
	public function createordersn()
	{
		$order_sn = date('Ymd').mt_rand(3,9).str_pad(mt_rand(1, 99999),5,'1',STR_PAD_LEFT);
		return $order_sn;
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
		$recordlog['order_status'] = isset($data['order_status']) ? $data['order_status']:'无';
		$recordlog['ispreorder'] = isset($data['ispreorder']) ? $data['ispreorder']:0;
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
		$newdata = array();
		foreach($data as $k=>$v)
		{
			@$m = addslashes($v);
			$newdata[$k] = $m;
		}
		$key = implode(',',array_keys($newdata));
		$val = implode("','",array_values($newdata));
		$sql = "insert into $tablename($key) values('$val')";
		echo $sql.';<br/>';
		//return 666;
		$this->mysqli->query($sql);
		return $this->mysqli->insert_id;
	}
	
	
	//根据条件匹配商品
	public function getgoodsinfo($where,$num=1)
	{
		$needfushi = 1;  //是否需要匹配副石   如果主石有了就不用了   默认为需要
		$caisearr = array('18K彩,18K彩色','18K彩金','18K彩色','18K彩');
		$groupby = 'a.id';
		$sql = " select
		a.goods_id,
		a.goods_sn,
		a.goods_name,
		a.qiegong as cut,
		a.zuanshidaxiao,
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
	where a.order_goods_id < 1 and a.is_on_sale=2 and a.ziyin = '' and ";
		if(isset($where['goods_sn']) && !empty($where['goods_sn']))
		{
			$sql .= " a.goods_sn = '".$where['goods_sn']."' and ";
		}
		if(isset($where['gold_weight_min']) && $where['gold_weight_min'] > 0 )
		{
			$sql .= " a.jinzhong >= ".$where['gold_weight_min']." and ";
			$groupby = 'a.jinzhong';
		}
		if(isset($where['gold_weight_max']) && $where['gold_weight_max'] > 0 )
		{
			$sql .= " a.jinzhong <= ".$where['gold_weight_max']." and ";
			$groupby = 'a.jinzhong';
		}
		
		if(isset($where['shoucun']) && !empty($where['shoucun']))
		{
			$scmin = ( $where['shoucun'] - 0.5 )>0 ? $where['shoucun'] - 0.5 : 0 ;
			$scmax = $where['shoucun'] + 0.5;
			$sql .=" a.shoucun >= ".$scmin." and shoucun <= ".$scmax." and ";
		}
		/*
		if(isset($where['ziyin']) && !empty($where['ziyin']))
		{
			$sql .=" a.ziyin ='".$where['ziyin']."' and ";
		}*/
		if(isset($where['caizhi']) && !empty($where['caizhi']))
		{
			$caizhi = explode(',',$where['caizhi']);
			$caizhi = array_filter($caizhi);
			$caizhi = array_unique($caizhi);
			//检验是否彩金,彩色
			$tmpcaizhi = strtoupper(implode(",",$caizhi));
			$caizhi = implode("','",$caizhi);
			if( in_array($tmpcaizhi,$caisearr) )
			{
				$sql .= " a.caizhi like '%18K%' and ";
			}else{
				$sql .=" a.caizhi in('$caizhi') and ";
			}
		}
		
		if(isset($where['zuanshidaxiao']) && !empty($where['zuanshidaxiao']))
		{
			 $needfushi = 0 ;  //不需要匹配副石
			 //如果小于8分 允许向上浮动1分
			 //如果大于等于8分小于30分，允许向上浮动2分
			 //如果大于等于30分,允许向上浮动3分
			 $plus = bcsub($where['zuanshidaxiao'],0.3,3);
			 $minplus = bcsub(0.08,$where['zuanshidaxiao'],3);
			 if($plus >=0 )
			 {
				 $max = bcadd($where['zuanshidaxiao'],0.03,3);
			 }elseif($minplus>0)
			 {
				$max = bcadd($where['zuanshidaxiao'],0.01,3); 
			 }else{
				$max = bcadd($where['zuanshidaxiao'],0.02,3);
			 }
			$sql .=" a.zuanshidaxiao >= ".$where['zuanshidaxiao']." and a.zuanshidaxiao <= ".$max." and ";
		}
		
		
		if(isset($where['fushizhong'])&& !empty($where['fushizhong']) && $needfushi>0)
		{
			 //如果小于8分 允许向上浮动1分
			 //如果大于等于8分小于30分，允许向上浮动2分
			 //如果大于等于30分,允许向上浮动3分
			 $maxplus = bcsub($where['fushizhong'],0.3,3);
			 $minplus = bcsub(0.08,$where['fushizhong'],3);
			 if($maxplus >=0 )
			 {
				 $fsmax = bcadd($where['fushizhong'],0.03,3);
			 }elseif($minplus>0)
			 {
				$fsmax = bcadd($where['fushizhong'],0.01,3); 
			 }else{
				$fsmax = bcadd($where['fushizhong'],0.02,3);
			 }
			 $sql .=" a.fushizhong >= ".$where['fushizhong']." and a.fushizhong <=".$fsmax." and ";
		}
		if(isset($where['fushilishu'])&& !empty($where['fushilishu']) && $needfushi>0 )
		{
			$sql .=" a.fushilishu = ".$where['fushilishu']." and ";
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
		//echo $sql.'<br/>';
		return $this->mysqli->query($sql);
	}
	
	
	
	//双11凌晨两点之前成交的订单送的赠品其实就是商品
	public function gettowclockgiftinfo($goodssn)
	{
		$sql = " select
			a.goods_id,
			a.goods_sn,
			a.goods_name,
			a.qiegong as cut,
			a.zuanshidaxiao,
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
		where a.order_goods_id < 1 and a.is_on_sale=2 and a.ziyin = '' and a.warehouse_id = 2";
		
		if(!empty($goodssn) && is_array($goodssn))
		{
			$goodssn = implode("','",$goodssn);
			$sql .=" and a.goods_sn in('$goodssn') ";
		}
		$sql .=" order by a.mingyichengben asc limit 1";
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
		$sql ="select count(*) as count,res from app_order.s11_order_info where ";
		if(isset($where['btime']) && !empty($where['btime']))
		{
			$sql .=" add_time>= '".$where['btime']." 00:00:00' and ";
		}
		if(isset($where['etime']) && !empty($where['etime']))
		{
		    $sql .= " add_time <= '".$where['etime']." 23:59:59' and ";
		}
		if(isset($where['res']))
		{
			$res = (int)$where['res'];
			$sql .= " res = '".$res."' and ";
		}
		if(isset($where['isyushou']))
		{
			$isys = (int)$where['isyushou'];
			$sql .=" ispreorder= '".$isys."' and ";	
		}
		$sql .= " ( reason like '成功' or reason like '%备注%' or reason like '%标签%' or reason like '%标题%' ) and ";
		$sql .=" 1 group by res order by res desc";
		$result = $this->mysqli->query($sql);
		if($result && $result->num_rows>0)
		{
			while($obj=$result->fetch_assoc())
			{
				if($obj['res']==1)
				{
					$numarr['ok'] += $obj['count'];
				}else{
					$numarr['false'] +=$obj['count'];	
				}
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
		$sql .=" inner join app_order.s11_order_info as a on a.order_id=bi.id where a.res=1 and ";
		
		if(isset($where['btime']) && !empty($where['btime']))
		{
			$sql .=" add_time>= '".$where['btime']." 00:00:00' and ";
		}
		if(isset($where['etime']) && !empty($where['etime']))
		{
		    $sql .= " add_time <= '".$where['etime']." 23:59:59' and ";
		}
		if(isset($where['isyushou']))
		{
			$isys = (int)$where['isyushou'];
			$sql .=" ispreorder= '".$isys."' and ";	
		}
		$sql .=" 1 group by bi.is_xianhuo order by bi.is_xianhuo desc";
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
	
	//获取已经抓单的现货和期货
	public function countfh($where)
	{
		$numarr = array(
			'yfh'=>0,
			'wfh'=>0
		);
		$sql = "select bi.is_xianhuo,bi.send_good_status 
		 from app_order.base_order_info as bi ";
		$sql .=" inner join  app_order.s11_order_info as a on a.order_id=bi.id where a.res=1 and ";
		if(isset($where['btime']) && !empty($where['btime']))
		{
			$sql .=" add_time>= '".$where['btime']." 00:00:00' and ";
		}
		if(isset($where['etime']) && !empty($where['etime']))
		{
		    $sql .= " add_time <= '".$where['etime']." 23:59:59' and ";
		}
		if(isset($where['isyushou']))
		{
			$isys = (int)$where['isyushou'];
			$sql .=" ispreorder= '".$isys."' and ";	
		}
		$sql .=" bi.is_xianhuo=1 ";
		$result = $this->mysqli->query($sql);
		if($result)
		{
			while($obj=$result->fetch_assoc())
			{
				if($obj['send_good_status'] ==2)
				{
					$numarr['yfh'] += 1;
				}else{
					$numarr['wfh'] += 1;	
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
		$sql .=" where b.res=1 and ";
		if(isset($where['btime']) && !empty($where['btime']))
		{
			$sql .=" b.add_time>= '".$where['btime']." 00:00:00' and ";
		}
		if(isset($where['etime']) && !empty($where['etime']))
		{
		    $sql .= " b.add_time <= '".$where['etime']." 23:59:59' and ";
		}
		if(isset($where['isyushou']))
		{
			$isys = (int)$where['isyushou'];
			$sql .=" ispreorder= '".$isys."' and ";	
		}
		$sql .=" 1 ";
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
	
	//获取所有满足条件的成功的订单信息
	public function getalloklist($where,$all=0)
	{
		$sql = "select 
			a.out_order_sn,a.order_id,a.order_sn,a.add_time,
			b.order_amount,b.money_paid,b.money_unpaid 
			from s11_order_info as a
			inner join app_order_account as b on a.order_id=b.order_id
			where a.res=1 ";
		if(isset($where['btime']) && !empty($where['btime']))
		{
			$sql .=" and a.add_time >= '".$where['btime']." 00:00:00' ";
		}
		if(isset($where['etime']) && !empty($where['etime']))
		{
			$sql .=" and a.add_time <= '".$where['etime']." 23:59:59' ";
		}
		if(isset($where['isyushou']))
		{
			$isys = (int)$where['isyushou'];
			$sql .=" and a.ispreorder ='".$isys."'";
		}
		if($all < 1)
		{
			$sql .=" limit 20 ";
		}
		return  $this->mysqli->query($sql);
	}
	
	//期货分类统计用 tool/qihuolist.php
	public function getallokqihuolist($where,$all=0)
	{
		$sql = "select 
			a.out_order_sn,a.order_id,a.order_sn,a.add_time 
			from s11_order_info as a
			inner join base_order_info as o on a.order_id=o.id 
			left join app_order_details as b on a.order_id=b.order_id
			where a.res=1 and o.is_xianhuo=0 ";
		if(isset($where['btime']) && !empty($where['btime']))
		{
			$sql .=" and a.add_time >= '".$where['btime']." 00:00:00' ";
		}
		if(isset($where['etime']) && !empty($where['etime']))
		{
			$sql .=" and a.add_time <= '".$where['etime']." 23:59:59' ";
		}
		if(isset($where['isyushou']))
		{
			$isys = (int)$where['isyushou'];
			$sql .=" and a.ispreorder ='".$isys."'";
		}
		if(isset($where['name']) &&  !empty($where['name']))
		{
			//产品线
			$sql .=" and b.goods_name like '%".$where['name']."%'";
		}
		if($all < 1)
		{
			$sql .=" limit 20 ";
		}
		return  $this->mysqli->query($sql);
		
	}
	
	
	//获取所有满足条件的失败的订单信息
	public function getfalselist($where,$all=0)
	{
		$sql = "select 
			distinct(a.out_order_sn) as out_order_sn ,a.reason,a.order_sn,a.order_status,a.ispreorder,a.add_time
			from s11_order_info as a
			left join rel_out_order as b on a.out_order_sn=b.out_order_sn 
			where a.res=0 and b.out_order_sn is null ";
		if(isset($where['btime']) && !empty($where['btime']))
		{
			$sql .=" and a.add_time >= '".$where['btime']." 00:00:00' ";
		}
		if(isset($where['etime']) && !empty($where['etime']))
		{
			$sql .=" and a.add_time <= '".$where['etime']." 23:59:59' ";
		}
		if(isset($where['isyushou']))
		{
			$isys = (int)$where['isyushou'];
			$sql .=" and a.ispreorder ='".$isys."'";
		}
		$sql .= " and ( reason like '%备注%' or reason like '%标签%' or reason like '%标题%' )";
		if($all < 1)
		{
			$sql .=" limit 20 ";
		}
		return  $this->mysqli->query($sql);
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
		$sql = "select id,price,is_xz,sell_sprice from app_order.gift_goods where goods_number='".$goods_sn."'";
		$result = $this->mysqli->query($sql);
		$giftinfo = array();
		if($result)
		{
			$giftinfo = $result->fetch_assoc();
		}
		return $giftinfo;
	}
	
	
	//获取所有失败的信息
	public function getfalsedata($where)
	{
		$sql = "select distinct(a.out_order_sn),a.reason from s11_order_info as a
		left join rel_out_order as b on a.out_order_sn=b.out_order_sn 
		where a.res=0 and b.out_order_sn is null ";
		if(isset($where['btime']) && !empty($where['btime']))
		{
			$sql .=" and a.add_time >='".$where['btime']." 00:00:00' ";
		}
		if(isset($where['etime']) && !empty($where['etime']))
		{
			$sql .=" and a.add_time <='".$where['etime']." 23:59:59' ";
		}
		if(isset($where['reason']) && !empty($where['reason']))
		{
			$sql .= " and a.reason like '%".$where['reason']."%' ";
		}
		if(isset($where['isyushou']))
		{
			$isyushou = (int)$where['isyushou'];
			$sql .=" and a.ispreorder = '".$isyushou."'";
		}
		$sql .=" and ( a.reason like '%备注%' or a.reason like '%标签%' or a.reason like '%标题%' )";
		$result = $this->mysqli->query($sql);
		return $result;
	}
	
	//获取所有失败的信息 仅为双11
	public function getfalsedatafor11()
	{
		$sql = "select distinct(a.out_order_sn),a.reason from s11_order_info as a
		left join rel_out_order as b on a.out_order_sn=b.out_order_sn  
		where a.res=0 and b.out_order_sn is null  
		and a.add_time >='2015-11-11 00:00:00' and a.add_time <='2015-11-12 00:09:15'  
		and ( a.reason like '%备注%' or a.reason like '%标签%' or a.reason like '%标题%' )";
		$result = $this->mysqli->query($sql);
		return $result;
	}
	
	
	//获取所有成功录入的外部订单回写
	public function getreturntaobaoinfo()
	{
		$sql = " select b.id,a.out_order_sn,b.order_sn,b.order_remark from rel_out_order as a 
		left join base_order_info as b on a.order_id=b.id
		left join s11_order_info as c on a.out_order_sn=c.out_order_sn 
		where b.order_status < 3 and c.res=1 and b.referer='双11抓单' and c.isreturn=0";
		$result = $this->mysqli->query($sql);
		$data = array();
		if($result && $result->num_rows>0)
		{
			while($obj=$result->fetch_assoc())
			{
				array_push($data,$obj);
			}
		}
		return $data;
	}
	//修改备注是否回写
	public function updateisreturn($taobaoid,$order_sn)
	{
		$sql ="update s11_order_info set isreturn=1 where out_order_sn='".$taobaoid."' and order_sn='".$order_sn."'";
		echo $sql.'<br/>';
		return $this->mysqli->query($sql);
	}
	
	
	
	
	
	//因为之前把优惠券记录到了订单,现在需要找出所有用了优惠券的订单
	public function getallyhqorder()
	{
		$sql ="
		SELECT a.id AS orderid,a.order_sn,
		d.order_amount,d.coupon_price,d.favorable_price,d.shipping_fee,
		b.out_order_sn,
		od.goods_id,od.goods_sn,od.goods_price,od.favorable_price
		FROM base_order_info AS a
		INNER JOIN s11_order_info AS b ON a.id=b.order_id
		INNER JOIN rel_out_order AS c ON a.id=c.order_id
		INNER JOIN app_order_account AS d ON a.id=d.order_id
		LEFT JOIN app_order_details AS od ON a.id=od.order_id
		WHERE b.res=1 AND d.coupon_price > 0
		";
		$result = $this->mysqli->query($sql);
		$data = array();
		if($result && $result->num_rows>0)
		{
			while($obj=$result->fetch_assoc())
			{
				array_push($data,$obj);
			}
		}
		return $data;
	}
	
	//定义淘宝抓单仓库
	public function getgoodswarehouse()
	{
		//仓库信息
		$warehouse_arr=array(
			2=>'线上低值库',
			79=>'深圳珍珠库',
			96=>'总公司后库',
			184=>'黄金网络库',
			386=>'彩宝库',
			482=>'淘宝黄金',
			484=>'淘宝素金',
			486=>'线上钻饰库',
			516=>'物控库',
			672=>'轻奢库',
			673=>'彩钻库'
		);
		$warehouseid = implode("','",array_keys($warehouse_arr));
		return $warehouseid;
	}
	//定义淘宝快递数组
	public function getshippintlist()
	{
		//快递数组
		$shipping_list = array(
			"4" => "SF",
			"9" => "EMS",
			"12" => "YTO",
			"14" => "STO",
			"19" => "ZTO"
		);
		return $shipping_list;
	}
	
	
	//梁全升 要求统计款的多少
	public function getallgoods_sn($where)
	{
		$sql = "select a.goods_sn,count(a.goods_sn) as count from app_order_details as a
		inner join base_order_info as b on a.order_id=b.id
		where ";
		if(isset($where['isys']) && $where['isys'] >0 )
		{
			$sql .=" b.referer='双11预售'  and ";
		}
		$sql .=" b.order_status = 2 and b.send_good_status = 1 and delivery_status = 1 ";
		$sql .=" group by a.goods_sn order by count desc ";
		$result = $this->mysqli->query($sql);
		$data = array();
		if($result && $result->num_rows>0)
		{
			while($obj=$result->fetch_assoc())
			{
				array_push($data,$obj);
			}
		}
		return $data;	
	}
	
	
	//修改制单人
	public function updateuser($tablename,$orderid,$createname)
	{
		if($tablename =='base_order_info')
		{
			$sql = "update $tablename set create_user='".$createname."' where id='".$orderid."'";
		}else{
			$sql = "update $tablename set create_user='".$createname."' where order_id='".$orderid."'";
		}
		return $this->mysqli->query($sql);
	}
	
	//数组去空
	public function filterarr($data)
	{
		$arr = array();
		if(empty($data))
		{
			return $arr;
		}
		foreach($data as $key=>$v)
		{
			if( $v ===0 || !empty($v))
			{
				$arr[$key]=$v;
			}
		}
		return $arr;
	}
	
	//20160407
	public function getorderinfo($order_sn)
	{
		$sql = "select * from base_order_info where order_sn='".$order_sn."' limit 1";
		$result = $this->mysqli->query($sql);
		$data = array();
		if($result && $result->num_rows>0)
		{
			$data=$result->fetch_assoc();
		}
		return $data;	
	}
	public function getrelorderinfo($order_id)
	{
		$sql1 = "select * from rel_out_order where order_id='".$order_id."' limit 1";
		$sql2 = "select * from app_order_invoice where order_id='".$order_id."' limit 1";
		$sql3 = "select * from app_order_details where order_id='".$order_id."' ";
		$sql4 = "select * from app_order_action where order_id='".$order_id."' ";
		$sql5 = "select * from app_order_account where order_id='".$order_id."' limit 1";
		$sql6 = "select * from app_order_address where order_id='".$order_id."' limit 1";
		$sql7 = "select * from finance.app_order_pay_action where order_id='".$order_id."' ";
		
		$result1 = $this->mysqli->query($sql1);
		$result2 = $this->mysqli->query($sql2);
		$result3 = $this->mysqli->query($sql3);
		$result4 = $this->mysqli->query($sql4);
		$result5 = $this->mysqli->query($sql5);
		$result6 = $this->mysqli->query($sql6);
		$result7 = $this->mysqli->query($sql7);
		$data = array();
		if($result1 && $result1->num_rows>0)
		{
			$data['rel_out_order']=$result1->fetch_assoc();
		}
		if($result2 && $result2->num_rows>0)
		{
			$data['app_order_invoice']=$result2->fetch_assoc();
		}
		if($result5 && $result5->num_rows>0)
		{
			$data['app_order_account']=$result5->fetch_assoc();
		}
		if($result6 && $result6->num_rows>0)
		{
			$data['app_order_address']=$result6->fetch_assoc();
		}
		if($result3 && $result3->num_rows>0)
		{
			$data['app_order_details'] = array();
			while($obj=$result3->fetch_assoc())
			{
				array_push($data['app_order_details'],$obj);
			}
		}
		if($result4 && $result4->num_rows>0)
		{
			$data['app_order_action'] = array();
			while($obj=$result4->fetch_assoc())
			{
				array_push($data['app_order_action'],$obj);
			}
		}
		if($result7 && $result7->num_rows>0)
		{
			$data['app_order_pay_action'] = array();
			while($obj=$result7->fetch_assoc())
			{
				array_push($data['app_order_pay_action'],$obj);
			}
		}
		return $data;
	}

	
}
?>
