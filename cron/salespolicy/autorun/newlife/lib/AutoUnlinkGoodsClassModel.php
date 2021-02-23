<?php
class AutoUnlinkGoodsClassModel extends PdoModel
{
	public function __construct()
	{
		
		parent::__construct();
	}
	public function __distuct()
	{
	}
	
	/*
	@parames referer      默认双十一抓单的
	@parames paystatus    默认为未付款的
	@parames orderstatus  默认为未关闭的订单
	@parames isyushou     是否是预售的订单
	获取所有需要解绑的订单的信息
	*/
	public function getlist($isyushou=1)
	{
		$sql ="
		select a.id,a.order_sn,d.goods_id,d.id as detailid 
		from app_order.base_order_info as a 
		inner join s11_order_info as b on a.id=b.order_id 
		left join app_order.app_order_details as d on a.id=d.order_id 
		where b.res=1 and a.order_status<3 and a.is_delete=0 and a.order_pay_status !=3 and 
		a.referer in('双11抓单','双11导单','双11预售','核销单') ";
		if($isyushou > 0)
		{
		 	$sql .= " and b.ispreorder = 1 "; 
		}
		$result = $this->mysqli->query($sql);
		$data = array();
		if($result)
		{
			while($obj = $result->fetch_assoc())
			{
				array_push($data,$obj);
			}
		}
		return $data;
	}
	
	//货品自动解绑   只针对双十一 只绑定订单的情况
	//验证绑定的是否正确所以加上order_goods_id验证
	public function autounbind($goodsid,$detailid)
	{
		$sql = "update warehouse_shipping.warehouse_goods set order_goods_id=0 ";
		$sql .=" where goods_id='".$goodsid."' and order_goods_id='".$detailid."' ";
		return $this->mysqli->query($sql);
	}
	
	//订单自动关闭
	//订单审核状态1无效（默认待审核）2已审核3取消4关闭
	//申请关闭:0=未申请，1=申请关闭
	public function autocloseorder($orderid)
	{
		$sql ="update app_order.base_order_info set order_status=4,apply_close=1,is_delete=1 ";
		$sql .=" where id='".$orderid."' ";
		return $this->mysqli->query($sql);
	}
	
	//记录操作日志
	public function addactionlog($orderid,$action)
	{
		//操作日志
		$ation['order_status'] = 2;
		$ation['order_id'] = $orderid;
		$ation['shipping_status'] = 1;
		$ation['pay_status'] = 1;
		$ation['create_user'] = 'admin';
		$ation['create_time'] = date("Y-m-d H:i:s");
		$ation['remark'] = $action;
		$fields = implode(',',array_keys($ation));
		$values = implode("','",array_values($ation));
		$sql = "insert into app_order.app_order_action($fields) values('$values')";
		return $this->mysqli->query($sql);
	}
}
?>