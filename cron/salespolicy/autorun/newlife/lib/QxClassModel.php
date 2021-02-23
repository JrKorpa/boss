<?php
class QxClassModel extends PdoModel
{
	public function __construct()
	{
		
		parent::__construct();
	}
	public function __distuct()
	{
		//
	}
	
	public function getlist()
	{
		$sql ="
		select a.id,a.order_sn,c.id as detailid,c.goods_sn from base_order_info as a 
		inner join s11_order_info as b on a.id=b.order_id
		left join app_order_details as c on a.id = c.order_id 
		where a.is_xianhuo = 0 and b.res=1 and a.referer='双11抓单' and a.order_status < 3 and a.is_delete =0 
		and c.goods_sn in('KLPW029315','KLPW029314')";
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
	
	public function getgoodsinfo($goods_sn)
	{
		$data = array();
		if(empty($goods_sn))
		{
			return $data;
		}
		
		$sql = "select goods_id,caizhi from warehouse_shipping.warehouse_goods where goods_sn='".$goods_sn."' and ";
		$sql .=" is_on_sale=2 and order_goods_id < 1 limit 1";
		$result = $this->mysqli->query($sql);
		if($result){
			$data = $result->fetch_assoc();
		}
		return $data;
		
	}
	//修改订单详情里面的信息
	public function updateorderdetail($detailid,$goodsid,$caizhi)
	{
		if($detailid < 1)
		{
			return false;
		}
		$sql ="update app_order_details set goods_id ='".$goodsid."',buchan_status=9,";
		$sql .=" ext_goods_sn = '".$goodsid."',is_stock_goods=1,xiangqian='成品',";
		$sql.=" caizhi='".$caizhi."' where id='".$detailid."'";
		echo $sql.'<br/>';
		return $this->mysqli->query($sql);
	}
	//修改订单为期货单
	public function updateXianhuo($isxianhuo=1,$id)
	{
		$upsql = "update base_order_info set is_xianhuo=$isxianhuo where id='".$id."'";
		echo $upsql.'<br/>';
		return $this->mysqli->query($upsql);
	}
	public function updategoodsorderid($goodsid,$detailid)
	{
		$upsql = "update warehouse_shipping.warehouse_goods set order_goods_id='".$detailid."' ";
		$upsql .=" where goods_id='".$goodsid."'";
		echo $upsql.'<br/>';
		return $this->mysqli->query($upsql);
	}
}
?>