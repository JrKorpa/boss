<?php
/**
 *  -------------------------------------------------
 *   @file		: ShopCfgModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Liulinyan <939942478@qq.com>
 *   @date		: 2015-09-01 18:51:16
 *   @update	:
 *  -------------------------------------------------
 */
class TydgwcountReportModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'shop_cfg';
		parent::__construct($id,$strConn);
	}
	
	//获取预约的信息
	public function getbokecount($where)
	{
		$sql = " select count(abi.bespoke_id) as count,abi.department_id,abi.make_order";
		$sql .=" from front.app_bespoke_info as abi ";
		$sql .=" where abi.is_delete=0 and ";
		//追加条件
		if(isset($where['create_time_start']) && !empty($where['create_time_start']))
		{
			$sql .=" abi.create_time >= '".$where['create_time_start']." 00:00:00 ' and ";
		}
		if(isset($where['create_time_end']) && !empty($where['create_time_end']))
		{
			$sql .=" abi.create_time <= '".$where['create_time_end']." 23:59:59' and ";
		}
		if(isset($where['bespoke_status']) && $where['bespoke_status']>0)
		{
			$sql .= "abi.bespoke_status = '".$where['bespoke_status']."' and ";
		}
		//实际到店的
		if(isset($where['real_inshop_time_start']) && !empty($where['real_inshop_time_start']))
		{
			$sql .=" abi.real_inshop_time >= '".$where['real_inshop_time_start']." 00:00:00' and ";
		}
		if(isset($where['real_inshop_time_end']) && !empty($where['real_inshop_time_end']))
		{
			$sql .=" abi.real_inshop_time <= '".$where['real_inshop_time_end']." 23:59:59 ' and ";
		}
		if(isset($where['re_status']) && $where['re_status']>0)
		{
			$sql .= "abi.re_status = '".$where['re_status']."' and ";
		}
		
		//当前应到的
		if(isset($where['bespoke_inshop_time_start']) && !empty($where['bespoke_inshop_time_start']))
		{
			$sql .=" abi.bespoke_inshop_time >= '".$where['bespoke_inshop_time_start']." 00:00:00' and ";
		}
		if(isset($where['bespoke_inshop_time_end']) && !empty($where['bespoke_inshop_time_end']))
		{
			$sql .=" abi.bespoke_inshop_time <= '".$where['bespoke_inshop_time_end']." 23:59:59 ' and ";
		}

		if(isset($where['department_id']) && !empty($where['department_id']))
		{
			$sql .= "abi.department_id in(".$where['department_id'].") and ";
		}
		if(isset($where['make_order']) && !empty($where['make_order']))
		{
			$sql .= "abi.make_order ='".$where['make_order']."' and ";
		}
		
		$sql .=" 1 group by abi.make_order ";
		return $this->db()->getAll($sql);
	}
	
	//获取订单的统计信息
	public function getordercount($where)
	{
		$sql="
select 
	count(oi.id) as ordernum,sum(oi.is_zp) as zpnum,oi.department_id,oi.create_user,
	sum(oc.order_amount) as orderamount,sum(oc.money_paid) as moneypaid,sum(oc.money_unpaid) as moneyunpaid
from 
	app_order.base_order_info as oi
	inner join app_order.app_order_account as oc on oi.id=oc.order_id 
where
	oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0
";
		if( isset($where['department_id']) && $where['department_id'] > 0 )
		{
			$sql .= " and oi.department_id = '".$where['department_id']."'";
		}
		if(isset($where['orderenter']) && !empty($where['orderenter']))
		{
			if($where['orderenter'] == '婚博会')
			{
				$sql .= " and oi.referer ='婚博会' ";
			}else{
				$sql .= " and oi.referer <> '婚博会' ";
			}
		}
		if(isset($where['begintime']) && !empty($where['begintime']))
		{
			$sql .= " and oi.pay_date >= '".$where['begintime']." 00:00:00'";
		}
		if(isset($where['endtime']) && !empty($where['endtime']))
		{
			$sql .= " and oi.pay_date <= '".$where['endtime']." 23:59:59'";	
		}
		
		if(isset($where['create_user']) && !empty($where['create_user']))
		{
			$sql .= " and oi.create_user = '".$where['create_user']."'";
		}
			
		$sql .= " group by oi.create_user ";
		return $this->db()->getAll($sql);
	}
}

?>