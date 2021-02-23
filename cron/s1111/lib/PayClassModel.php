<?php
/*
auto: liulinyan
date: 2015-09-21
file: PayClassModel.php
used: 支付类
*/
class PayClassModel extends PdoModel
{
	function __construct()
	{
		parent::__construct();	
	}
	function __distruct()
	{
		//
	}
	//获取所有成功的外部订单信息
	public function getalloutids($isyushou=0,$taobaoid=0)
	{
		//只针对现货点款
		$sql = "select a.out_order_sn as outid,a.order_id,a.order_sn from s11_order_info as a 
		inner join base_order_info as b on a.order_id=b.id 
		where a.res=1 and b.order_status not in(3,4,5)  
		and b.order_pay_status in(1,2) and b.referer in('双11抓单') ";		
		if($isyushou>0)
		{
			$sql .= " and a.order_status='WAIT_BUYER_PAY' ";
		}else{
			$sql .= " and a.order_status='WAIT_SELLER_SEND_GOODS' "; 
		}
		if($taobaoid>0)
			$sql .= " and a.out_order_sn='".$taobaoid."' ";				
		$result = $this->mysqli->query($sql);
		$res = array();
		if($result && $result->num_rows>0)
		{
			while($obj = $result->fetch_assoc())
			{
				array_push($res,$obj);
			}
		}
		return $res;
	}
	
	public function updatestatus($outid,$ordersn)
	{
		$sql = "update s11_order_info set order_status='WAIT_SELLER_SEND_GOODS' where ";
		$sql .=" out_order_sn='".$outid."' and order_sn='".$ordersn."' and res=1 ";
		echo $sql."<br>".PHP_EOL;
		return $this->mysqli->query($sql);
	}
	
	//获取订单的总金额
	public function getordermoney($orderid)
	{
		$sql ="select order_amount,money_paid,money_unpaid,favorable_price,coupon_price from app_order_account where ";
		$sql .=" order_id='".$orderid."' limit 1";
		$result = $this->mysqli->query($sql);
		$rows = $result->num_rows;
		$res = array();
		if($rows > 0)
		{
			$res = $result->fetch_assoc();
		}
		return $res;
	}
	
	//修改订的的金额
	public function updateordermoney($orderid,$data)
	{
		$sql = "update app_order_account set order_amount='".$data['order_amount']."', ";
		$sql .=" money_unpaid='".$data['money_unpaid']."', ";
		$sql .=" favorable_price='".$data['favorable_price']."' ";
		$sql .= " where order_id='".$orderid."'";
		echo $sql."<br>".PHP_EOL;
		return $this->mysqli->query($sql);
	}

	//修改订的发票金额
	public function update_invoice_amount($orderid,$invoice_amount)
	{
		$sql = "update app_order_invoice set invoice_amount='".$invoice_amount."' where order_id='".$orderid."'";
		echo $sql."<br>".PHP_EOL;
		return $this->mysqli->query($sql);
	}
	
	
	
	
	//检测外部订单是否已经录入过了
	public function check($out_order_sn)
	{
		$cksql = "select * from s11_order_info where out_order_sn = '".$out_order_sn."' order by id desc limit 1";
		$result = $this->mysqli->query($cksql);
		$rows = $result->num_rows;
		$res = array();
		if($rows > 0)
		{
			$res = $result->fetch_assoc();
		}
		return $res;
	}
	
	//检查外部单号是否存在
	public function checkTaobaoOrder($taobaoid)
	{
		$sql = "select a.id,a.order_sn from app_order.base_order_info as a
	inner join app_order.rel_out_order as b on a.id=b.order_id 
	where b.out_order_sn ='".$taobaoid."' and a.order_status in (1,2) order by a.id desc limit 1";
		return $this->mysqli->query($sql);
	}
	
	//获取BDD订单的信息
	function getklorderinfo($ordersn)
	{
		if(empty($ordersn)){
			return false;
		}
		$sql = " select boi.*,aoa.*,ro.out_order_sn from app_order.app_order_account as aoa 
				inner JOIN app_order.base_order_info as boi ON boi.id=aoa.order_id
				inner join app_order.rel_out_order as ro on aoa.order_id=ro.order_id  
				WHERE boi.order_sn='".$ordersn."' limit 1 ";
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
	
	//公用的插入数据方法
	function autoinsert($tablname,$data)
	{
		if(empty($data) || empty($tablname))
		{
			return false;
		}
		//$data = array_filter($data);
		$key = implode(',',array_keys($data));
		$value = implode("','",array_values($data));
		$sql = "insert into $tablname($key) value('$value')";
		$this->mysqli->query($sql);
		return $this->mysqli->insert_id;
	}
	
	//更改订单
	function updateOutOrder($real_payment,$order_id)
	{
		if(empty($order_id)){
			return false;
		}
		$sql = "update app_order.app_order_account set money_paid=money_paid+$real_payment,";
		$sql .="money_unpaid = money_unpaid-$real_payment where order_id = '".$order_id."'";
		$this->mysqli->query($sql);
	}
	
	//如果已付全款就把订单状态改成已付款付了一部分款就变成部分付款
	function changgestu($order_id)
	{
		$sql = "select order_amount,money_paid from app_order.app_order_account where order_id='".$order_id."' ";
		$sql .=" limit 1";
		//echo $sql.'<br/>';
		$res = $this->mysqli->query($sql);
		//print_r($res);
		if($res && $res->num_rows>0)
		{
			$orderinfo = $res->fetch_assoc();
			if($orderinfo['order_amount']<=$orderinfo['money_paid'])
			{
				$dsql = "select * from app_order.app_order_details where order_id='".$order_id."'";
				$dsql .=" AND `buchan_status`<>9 AND is_stock_goods=0";
				//如果有期货 的话 是不能改变发货状态的
				$res2 = $this->mysqli->query($dsql);
				//echo $dsql.'<br/>';
				//print_r($res2);
				if($res2 && $res2->num_rows>0)
				{
					$sql = "update app_order.base_order_info set order_pay_status=3,pay_date='".date("Y-m-d H:i:s")."' where id='".$order_id."'";
				}else{
					//如果没查到则可以吧order_pay_status=3,
				$sql = "update app_order.base_order_info set order_pay_status=3,delivery_status=2,";
				$sql .=" pay_date='".date("Y-m-d H:i:s")."' where id='".$order_id."'";
				}
				//echo $sql.'<br/>';
				$this->mysqli->query($sql);
				return 3;
			}
			if(($orderinfo['order_amount']>$orderinfo['money_paid']) && ($orderinfo['money_paid']>0))
			{
				$sql = "update app_order.base_order_info set order_pay_status=2,pay_date='".date("Y-m-d H:i:s")."'";
				$sql .=" where id='".$order_id."'";
				//echo $sql.'<br/>';
				$this->mysqli->query($sql);
				return 2;
			}
		}
		return true;
	}

	//如果已付全款就把订单状态改成已付款付了一部分款就变成部分付款
	function changgestu_yushou($order_id)
	{
		$sql = "select order_amount,money_paid from app_order.app_order_account where order_id='".$order_id."' ";
		$sql .=" limit 1";
		//echo $sql.'<br/>';
		$res = $this->mysqli->query($sql);
		//print_r($res);
		if($res && $res->num_rows>0)
		{
			$orderinfo = $res->fetch_assoc();
			if($orderinfo['order_amount']<=$orderinfo['money_paid'])
			{
				$dsql = "select * from app_order.app_order_details where order_id='".$order_id."'";
				$dsql .=" AND `buchan_status`<>9 AND is_stock_goods=0";
				//如果有期货 的话 是不能改变发货状态的
				$res2 = $this->mysqli->query($dsql);
				//echo $dsql.'<br/>';
				//print_r($res2);
				if($res2 && $res2->num_rows>0)
				{
					$sql = "update app_order.base_order_info set order_pay_status=3 where id='".$order_id."'";
				}else{
					//如果没查到则可以吧order_pay_status=3,
					$sql = "update app_order.base_order_info set order_pay_status=3,delivery_status=2 ";
					$sql .=" where id='".$order_id."'";
				}
				//echo $sql.'<br/>';
				$this->mysqli->query($sql);
				return 3;
			}
			if(($orderinfo['order_amount']>$orderinfo['money_paid']) && ($orderinfo['money_paid']>0))
			{
				$sql = "update app_order.base_order_info set order_pay_status=2 ";
				$sql .=" where id='".$order_id."'";
				//echo $sql.'<br/>';
				$this->mysqli->query($sql);
				return 2;
			}
		}
		return true;
	}

	
}
?>
