<?php
class GoodsClassModel extends PdoModel
{
	public function __construct()
	{
		
		parent::__construct();
	}
	public function __distuct()
	{
		//
	}

	//检查base_salepolicy_goods表中是否有该货号
	public function checkgoods($data)
	{
		if(empty($data))
		{
			return false;
		}
		$sql  = "select * from front.base_salepolicy_goods where ";
		if(isset($data['goods_id']) && $data['goods_id'] !='')
		{
			$sql .= "goods_id='".$data['goods_id']."' and ";
		}
		if(isset($data['goods_sn']) && $data['goods_sn'] !='')
		{
			$sql .= "goods_sn='".$data['goods_sn']."' and ";
		}
		$sql .=" 1 ";
		$datainfo = array();
		$result = $this->mysqli->query($sql);
		if($result)
		{
			$datainfo = $result->fetch_assoc();
		}
		return $datainfo;
	}
	
	//检查app_salepolicy_goods表中是否有该货号
	public function checkapp($data)
	{
		if(empty($data))
		{
			return false;
		}
		$sql  = "select * from front.app_salepolicy_goods where ";
		if(isset($data['goods_id']) && $data['goods_id'] !='' )
		{
			$sql .= " goods_id='".$data['goods_id']."' and ";
		}
		if(isset($data['policy_id']) && $data['policy_id'] !='' )
		{
			$sql .=" policy_id='".$data['policy_id']."' and ";
		}
		$sql .=" 1 ";
		$datainfo = array();
		$result = $this->mysqli->query($sql);
		if($result)
		{
			$datainfo = $result->fetch_assoc();
		}
		return $datainfo;
	}
	
	
	//根据款号获取产品线id和款式分类id
	public function getproandcat($goodssn)
	{
		$datainfo = array();
		if(empty($goodssn))
		{
			return $datainfo;
		}
		$sql = "select product_type,style_type,style_sn from front.base_style_info where style_sn='".$goodssn."' ";
		$result = $this->mysqli->query($sql);
		if($result)
		{
			$datainfo = $result->fetch_assoc();
		}
		return $datainfo; 	
	}
	
	//修改base_policy_goods表中goodsid的价格和上下架
	public function updategoods($goodsid,$price)
	{
		if(empty($goodsid))
		{
			return 0;
		}
		$sql = "update front.base_salepolicy_goods set chengbenjia='".$price."',is_policy=2,is_sale=1 where ";
		$sql .=" goods_id='".$goodsid."'";
		return $this->mysqli->query($sql);
		
	}
	//修改app_policy_goods表中的销售价格
	public function updateapp($goodsid,$policy_id,$price,$saleprice)
	{
		if(empty($goodsid))
		{
			return 0;
		}
		$sql = "update front.app_salepolicy_goods set sale_price='".$saleprice."',chengben='".$price."' ";
		$sql .=" where goods_id='".$goodsid."' and ";
		$sql .=" policy_id='".$policy_id."' ";
		return $sql;
		return $this->mysqli->query($sql);
	}
	
	//自动录入信息
	public function autoinsert($tablename,$data)
	{
		$newdata = array();
		foreach($data as $k=>$v)
		{
			$m = addslashes($v);
			$newdata[$k] = $m;
		}
		$key = implode(',',array_keys($newdata));
		$val = implode("','",array_values($newdata));
		$sql = "insert into $tablename($key) values('$val')";
		//return $sql;
		$this->mysqli->query($sql);
		return $this->mysqli->insert_id;
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
		$sql .=" 1 group by res order by res desc";
		$result = $this->mysqli->query($sql);
		if($result && $result->num_rows>0)
		{
			while($obj=$result->fetch_assoc())
			{
				if($obj['res']==1)
				{
					$numarr['ok']= $obj['count'];
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
			where a.res=0 ";
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
		$sql = "select out_order_sn,reason from s11_order_info where res=0 ";
		if(isset($where['btime']) && !empty($where['btime']))
		{
			$sql .=" and add_time >='".$where['btime']." 00:00:00' ";
		}
		if(isset($where['etime']) && !empty($where['etime']))
		{
			$sql .=" and add_time <='".$where['etime']." 23:59:59' ";
		}
		if(isset($where['reason']) && !empty($where['reason']))
		{
			$sql .= " and reason like '%".$where['reason']."%' ";
		}
		if(isset($where['isyushou']))
		{
			$isyushou = (int)$where['isyushou'];
			$sql .=" and ispreorder = '".$isyushou."'";
		}
		$result = $this->mysqli->query($sql);
		return $result;
	}
	
	//创建BDD订单编号
	public function createordersn()
	{
	     switch (SYS_SCOPE){
            case 'boss':
                return date('Ymd').mt_rand(3,9).str_pad(mt_rand(1, 99999),5,'1',STR_PAD_LEFT);
            case "zhanting":
                return date('ymd').mt_rand(3,9).str_pad(mt_rand(1, 99999),5,'1',STR_PAD_LEFT);
            default:
                die();
        }
	}
	
	//创建s11的记录日志
	public function createlog()
	{
		
	}
	
	
	/**********  点款的操作  **********/
	
	
	//修改订单金额表
	public function updatemoney($orderid,$moneydata)
	{
		if(empty($orderid) || empty($moneydata) )
		{
			return false;
		}
		$moneypaid = $moneydata['money_paid'];
		$moneyunpaid = $moneydata['money_unpaid'];
		$sql ="update app_order_account set money_paid = '".$moneypaid."' , ";
		$sql .=" money_unpaid='".$moneyunpaid."' where order_id='".$orderid."'";
		echo $sql.'<br/>';
		return $this->mysqli->query($sql);
	}
	
	//修改订单的发货状态和付款状态
	public function updateorder($orderid,$ordersarr)
	{
		if(empty($orderid) || empty($ordersarr) )
		{
			return false;
		}
		$sql =" update base_order_info set ";
		if(isset($ordersarr['order_pay_status']) && $ordersarr['order_pay_status'] > 0)
		{
			$sql .=" order_pay_status = '".$ordersarr['order_pay_status']."' ,";
		}
		if(isset($ordersarr['delivery_status']) && $ordersarr['delivery_status'] >0)
		{
			$sql .= " delivery_status = '".$ordersarr['delivery_status']."' ,";
		}
		if(isset($ordersarr['pay_date']) && !empty($ordersarr['pay_date']))
		{
			$sql .= " pay_date='".$ordersarr['pay_date']."' ";
		}
		$sql .=" where id='".$orderid."'";
		echo $sql.'<br/>';
		return $this->mysqli->query($sql);
	}
	
	public function createcode()
	{
		//生成一个支付凭据
		$date = date("Ymd");
		$header='DK-KLSZFGS-'.$date;
		$receipt_id = rand(0,999);
		$nes = str_pad($receipt_id,4,'0',STR_PAD_LEFT);
		$bonus_code=$header.$nes;
		return $bonus_code;
	}
	
	//查看是否有过支付,避免重复付款
	public function getPaySnExt($attach_sn)
	{
		global $mysqli;
		$sql = "select a.attach_sn,a.order_id from app_order.app_order_pay_action
				inner join app_order.base_order_info as b on a.order_id=b.id 
				where (attach_sn='".$attach_sn."' or pay_sn='".$attach_sn."') and b.order_status not in(3,4) limit 1";
		$result = $this->mysqli->query($sql);
		$res = array();
		if($result)
		{
			if($result->num_rows>0)
			{
				$res = $result->fetch_assoc();
			}
		}
		return $res;
	}
	
	
	public function getorderinfo($taobaoid)
	{
		$sql = " select b.order_sn,b.send_good_status,a.* from app_order_details as a 
		inner join base_order_info as b on a.order_id=b.id 
		inner join rel_out_order as c on a.order_id=c.order_id 
		where c.out_order_sn='".$taobaoid."' and b.is_xianhuo=0 and a.is_zp=0 ";
		$result = $this->mysqli->query($sql);
		return $result;
	}
	
}
?>