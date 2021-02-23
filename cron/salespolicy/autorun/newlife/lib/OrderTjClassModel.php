<?php
class OrderTjClassModel extends PdoModel
{
	public function __construct()
	{
		
		parent::__construct();
	}
	public function __distuct()
	{
		//
	}

	
	
	//统计方法
	//默认统计成功的
	public function countorder($where)
	{
		$resultarr = array();
		$numarr = array(
			'ordercount'=>0,
			'xianhuo'=>0,
			'qihuo'=>0,
			'ordermoney'=>0,
			'moneypaid'=>0,
			'unpaid'=>0,
			'yfh'=>0,
			'wfh'=>0
		);
		
		//天猫的
		$tmarr = array(
			'ordercount'=>0,
			'xianhuo'=>0,
			'qihuo'=>0,
			'ordermoney'=>0,
			'moneypaid'=>0,
			'unpaid'=>0,
			'yfh'=>0,
			'wfh'=>0
		);
		//其他的
		$otherarr = array(
			'ordercount'=>0,
			'xianhuo'=>0,
			'qihuo'=>0,
			'ordermoney'=>0,
			'moneypaid'=>0,
			'unpaid'=>0,
			'yfh'=>0,
			'wfh'=>0
		);
		
		$sql  = "select a.order_sn,a.id,a.is_xianhuo,a.send_good_status,a.department_id,
		b.order_amount,b.money_paid,b.money_unpaid from base_order_info as a 
		inner join app_order_account as b on a.id=b.order_id 
		inner join cuteframe.`sales_channels` AS s ON a.department_id=s.id 
		where s.channel_class=1 and ";
		if(isset($where['btime']) && !empty($where['btime']))
		{
			$sql .=" a.create_time>= '".$where['btime']." 00:00:00' and ";
		}
		if(isset($where['etime']) && !empty($where['etime']))
		{
		    $sql .= " a.create_time <= '".$where['etime']." 23:59:59' and ";
		}
		$sql .= "a.order_status in(1,2) and a.is_delete =0 ";
		$result = $this->mysqli->query($sql);
		if($result && $result->num_rows>0)
		{
			while($obj=$result->fetch_assoc())
			{
				if($obj['is_xianhuo'] == 1)
				{
					$numarr['xianhuo'] += 1;
					if($obj['department_id'] == 2)
					{
						$tmarr['xianhuo'] +=1;
					}else{
						$otherarr['xianhuo'] +=1;
					}
					//
					if($obj['send_good_status'] == 2)
					{
						$numarr['yfh'] += 1;
						//天猫的
						if($obj['department_id'] == 2)
						{
							$tmarr['yfh'] +=1;
						}else{
							$otherarr['yfh'] +=1;
						}
					}else{
						//天猫的
						if($obj['department_id'] == 2)
						{
							$tmarr['wfh'] +=1;
						}else{
							$otherarr['wfh'] +=1;
						}
						$numarr['wfh'] += 1;
					}
				}else{
					//天猫的
					if($obj['department_id'] == 2)
					{
						$tmarr['qihuo'] +=1;
					}else{
						$otherarr['qihuo'] +=1;
					}
					$numarr['qihuo'] += 1;
				}
				
				if($obj['department_id'] == 2)
				{
					$tmarr['ordercount'] +=1;
					$tmarr['ordermoney'] = bcadd($tmarr['ordermoney'],$obj['order_amount'],2);
					$tmarr['moneypaid'] = bcadd($tmarr['moneypaid'],$obj['money_paid'],2);
					$tmarr['unpaid'] =  bcadd($tmarr['unpaid'],$obj['money_unpaid'],2);
				}else{
					$otherarr['ordercount'] +=1;
					$otherarr['ordermoney'] = bcadd($otherarr['ordermoney'],$obj['order_amount'],2);
					$otherarr['moneypaid'] = bcadd($otherarr['moneypaid'],$obj['money_paid'],2);
					$otherarr['unpaid'] =  bcadd($otherarr['unpaid'],$obj['money_unpaid'],2);	
				}
				$numarr['ordercount'] +=1;
				$numarr['ordermoney'] = bcadd($numarr['ordermoney'],$obj['order_amount'],2);
				$numarr['moneypaid'] = bcadd($numarr['moneypaid'],$obj['money_paid'],2);
				$numarr['unpaid'] =  bcadd($numarr['unpaid'],$obj['money_unpaid'],2);
			}
		}
		
		array_push($resultarr,$numarr);
		array_push($resultarr,$tmarr);
		array_push($resultarr,$otherarr);
		return $resultarr;
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
			a.out_order_sn,a.reason,a.order_status,a.ispreorder,a.add_time
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
}
?>