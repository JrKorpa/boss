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
class TydcountReportModel extends Model
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
		
		//当期应到的
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
		$sql .=" 1 group by abi.department_id ";
		return $this->db()->getAll($sql);
	}
	
	//获取订单的统计信息
	public function getordercount($where)
	{
		$sql="
select 
	count(oi.id) as ordernum,sum(oi.is_zp) as zpnum,oi.department_id,
	sum(oc.order_amount) as orderamount,
    SUM(oc.goods_amount - oc.favorable_price) goodsamount,
    sum(oc.money_paid) as moneypaid,
    SUM(aoa.real_return_price) realreturnprice,
    sum(oc.money_unpaid) as moneyunpaid
from 
	app_order.base_order_info as oi
	inner join app_order.app_order_account as oc on oi.id=oc.order_id 
where
	oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0
";
		if( isset($where['department_id']) && $where['department_id'] > 0 )
		{
			$sql .= " and oi.department_id in(".$where['department_id'].")";
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
		$sql .= " group by oi.department_id ";
		return $this->db()->getAll($sql);
	}
	//获取预约的信息
	public function getBespokeInfoByMan($where)
	{
		$sql = " select abi.accecipt_man,count(distinct abi.customer_mobile) count ";
		$sql .=" from front.app_bespoke_info as abi ";
		$sql .=" where  ";
		//追加条件
		if(isset($where['create_time_start']) && !empty($where['create_time_start']))
		{
			$sql .=" abi.create_time >= '".$where['create_time_start']." 00:00:00' and ";
		}
		if(isset($where['create_time_end']) && !empty($where['create_time_end']))
		{
			$sql .=" abi.create_time <= '".$where['create_time_end']." 23:59:59' and ";
		}
		if(isset($where['bespoke_status']) && $where['bespoke_status']>0)
		{
			$sql .= "abi.bespoke_status = '".$where['bespoke_status']."' and ";
		}
		if(isset($where['re_status']) && $where['re_status']>0)
		{
			$sql .= "abi.re_status = '".$where['re_status']."' and ";
		}
		//实际到店的
		if(isset($where['real_inshop_time_start']) && !empty($where['real_inshop_time_start']))
		{
			$sql .=" abi.real_inshop_time >= '".$where['real_inshop_time_start']." 00:00:00' and ";
		}
		if(isset($where['real_inshop_time_end']) && !empty($where['real_inshop_time_end']))
		{
			$sql .=" abi.real_inshop_time <= '".$where['real_inshop_time_end']." 23:59:59' and ";
		}
		//当期应到的
		if(isset($where['bespoke_inshop_time_start']) && !empty($where['bespoke_inshop_time_start']))
		{
			$sql .=" abi.bespoke_inshop_time >= '".$where['bespoke_inshop_time_start']."' and ";
		}
		if(isset($where['bespoke_inshop_time_end']) && !empty($where['bespoke_inshop_time_end']))
		{
			$sql .=" abi.bespoke_inshop_time <= '".$where['bespoke_inshop_time_end']."' and ";
		}
		
		if(isset($where['department_id']) && !empty($where['department_id']))
		{
			$sql .= "abi.department_id in(".$where['department_id'].") and ";
		}

        if(isset($where['make_order']) && !empty($where['make_order']))
        {
            if(is_array($where['make_order'])){
                $sql .= " and abi.accecipt_man in('".implode("','", $where['make_order'])."') ";
            }else{
                $sql .= " and abi.accecipt_man ='".$where['make_order']."' ";
            }
            
        }
		$sql .=" 1 group by abi.accecipt_man ";
		return $this->db()->getAll($sql);
	}

	//获取预约的信息
	public function getBespokeInfo($where)
	{
        if(isset($where['dis_bespoke_id']) && !empty($where['dis_bespoke_id'])){
    		$sql = " select abi.department_id,count(distinct abi.customer_mobile) count ";
        }else{
    		$sql = " select abi.department_id,count(abi.customer_mobile) count ";
        }
		$sql .=" from front.app_bespoke_info as abi ";
		$sql .=" where abi.is_delete=0 and ";
		//追加条件
		if(isset($where['create_time_start']) && !empty($where['create_time_start']))
		{
			$sql .=" abi.create_time >= '".$where['create_time_start']." 00:00:00' and ";
		}
		if(isset($where['create_time_end']) && !empty($where['create_time_end']))
		{
			$sql .=" abi.create_time <= '".$where['create_time_end']." 23:59:59' and ";
		}
		if(isset($where['bespoke_status']) && $where['bespoke_status']>0)
		{
			$sql .= "abi.bespoke_status = '".$where['bespoke_status']."' and ";
		}
		if(isset($where['re_status']) && $where['re_status']>0)
		{
			$sql .= "abi.re_status = '".$where['re_status']."' and ";
		}
		//实际到店的
		if(isset($where['real_inshop_time_start']) && !empty($where['real_inshop_time_start']))
		{
			$sql .=" abi.real_inshop_time >= '".$where['real_inshop_time_start']." 00:00:00' and ";
		}
		if(isset($where['real_inshop_time_end']) && !empty($where['real_inshop_time_end']))
		{
			$sql .=" abi.real_inshop_time <= '".$where['real_inshop_time_end']." 23:59:59' and ";
		}
		//当期应到的
		if(isset($where['bespoke_inshop_time_start']) && !empty($where['bespoke_inshop_time_start']))
		{
			$sql .=" abi.bespoke_inshop_time >= '".$where['bespoke_inshop_time_start']."' and ";
		}
		if(isset($where['bespoke_inshop_time_end']) && !empty($where['bespoke_inshop_time_end']))
		{
			$sql .=" abi.bespoke_inshop_time <= '".$where['bespoke_inshop_time_end']."' and ";
		}
		
		if(isset($where['department_id']) && !empty($where['department_id']))
		{
			$sql .= "abi.department_id in(".$where['department_id'].") and ";
		}
        if(isset($where['from_ad']) && !empty($where['from_ad']))
        {
            $sql .= "abi.customer_source_id in(".$where['from_ad'].") and ";
        }

        if(isset($where['make_order']) && !empty($where['make_order']))
        {
            if(is_array($where['make_order'])){
                $sql .= "abi.make_order in('".implode("','", $where['make_order'])."') and ";
            }else{
                $sql .= "abi.make_order ='".$where['make_order']."' and ";
            }
            
        }
		$sql .=" 1 group by abi.department_id ";
		return $this->db()->getAll($sql);
	}
    public function getOrderInfoDis($where)
    {
		$sql="select 
                oi.department_id,COUNT(distinct oi.mobile) as dis_ordernum
            from 
                app_order.base_order_info as oi
                inner join app_order.app_order_account as oc on oi.id=oc.order_id 
                left join cuteframe.customer_sources as cs on oi.customer_source_id = cs.id
            where
                oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0 AND oi.is_zp=0
            ";
		if( isset($where['department_id']) && $where['department_id'] > 0 )
		{
			$sql .= " and oi.department_id in (".$where['department_id'].")";
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
        if(isset($where['fenlei']) && $where['fenlei'] !="")
        {
            $sql .= " and cs.fenlei='".$where['fenlei']."' ";
        }
        if(isset($where['make_order']) && !empty($where['make_order']))
        {
            if(is_array($where['make_order'])){
                $sql .= " and oi.create_user in('".implode("','", $where['make_order'])."')";
            }else{
                $sql .= " and oi.create_user ='".$where['make_order']."'";
            }
            
        }
		$sql .= " group by oi.department_id ";
		return $this->db()->getAll($sql);
    }

    public function getOrderInfoDiscs($where)
    {
        $sql="select 
                oi.customer_source_id,COUNT(distinct oi.mobile) as dis_ordernum
            from 
                app_order.base_order_info as oi
                inner join app_order.app_order_account as oc on oi.id=oc.order_id 
                left join cuteframe.customer_sources as cs on oi.customer_source_id = cs.id
            where
                oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0 AND oi.is_zp=0
            ";
        if( isset($where['department_id']) && $where['department_id'] > 0 )
        {
            $sql .= " and oi.department_id in (".$where['department_id'].")";
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
        if(isset($where['fenlei']) && $where['fenlei'] !="")
        {
            $sql .= " and cs.fenlei='".$where['fenlei']."' ";
        }
        if(isset($where['make_order']) && !empty($where['make_order']))
        {
            if(is_array($where['make_order'])){
                $sql .= " and oi.create_user in('".implode("','", $where['make_order'])."')";
            }else{
                $sql .= " and oi.create_user ='".$where['make_order']."'";
            }
            
        }
        $sql .= " group by oi.customer_source_id ";
        return $this->db()->getAll($sql);
    }

    public function getOrderInfoDisTo($where)
    {
        $sql="select 
                oi.department_id,COUNT(distinct oi.mobile) as dis_ordernum
            from 
                app_order.base_order_info as oi
                inner JOIN app_order.app_order_details AS d ON oi.id = d.order_id
                 left join cuteframe.customer_sources as cs on oi.customer_source_id = cs.id
            where
                oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0 AND oi.is_zp=0 AND IF (
                    d.favorable_status = 3,
                    d.goods_price - d.favorable_price,
                    d.goods_price
            ) >= 300 ";
        if( isset($where['department_id']) && $where['department_id'] > 0 )
        {
            $sql .= " and oi.department_id in (".$where['department_id'].")";
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
        if(isset($where['fenlei']) && $where['fenlei'] !="")
        {
            $sql .= " and cs.fenlei='".$where['fenlei']."' ";
        }
        if(isset($where['begintime']) && !empty($where['begintime']))
        {
            $sql .= " and oi.pay_date >= '".$where['begintime']." 00:00:00'";   
        }
        if(isset($where['endtime']) && !empty($where['endtime']))
        {
            $sql .= " and oi.pay_date <= '".$where['endtime']." 23:59:59'"; 
        }
        if(isset($where['make_order']) && !empty($where['make_order']))
        {
            if(is_array($where['make_order'])){
                $sql .= " and oi.create_user in('".implode("','", $where['make_order'])."')";
            }else{
                $sql .= " and oi.create_user ='".$where['make_order']."'";
            }
            
        }
        $sql .= " group by oi.department_id ";
        return $this->db()->getAll($sql);
    }

    public function getOrderInfoDisToNum($where)
    {
        $sql="select 
                oi.department_id,COUNT(distinct oi.mobile) as dis_ordernum
            from 
                app_order.base_order_info as oi
                inner JOIN app_order.app_order_details AS d ON oi.id = d.order_id
                 left join cuteframe.customer_sources as cs on oi.customer_source_id = cs.id
            where
                oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0 AND oi.is_zp=0 AND IF (
                    d.favorable_status = 3,
                    d.goods_price - d.favorable_price,
                    d.goods_price
            ) < 300 ";
        if( isset($where['department_id']) && $where['department_id'] > 0 )
        {
            $sql .= " and oi.department_id in (".$where['department_id'].")";
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
        if(isset($where['fenlei']) && $where['fenlei'] !="")
        {
            $sql .= " and cs.fenlei='".$where['fenlei']."' ";
        }
        if(isset($where['begintime']) && !empty($where['begintime']))
        {
            $sql .= " and oi.pay_date >= '".$where['begintime']." 00:00:00'";   
        }
        if(isset($where['endtime']) && !empty($where['endtime']))
        {
            $sql .= " and oi.pay_date <= '".$where['endtime']." 23:59:59'"; 
        }
        if(isset($where['make_order']) && !empty($where['make_order']))
        {
            if(is_array($where['make_order'])){
                $sql .= " and oi.create_user in('".implode("','", $where['make_order'])."')";
            }else{
                $sql .= " and oi.create_user ='".$where['make_order']."'";
            }
            
        }
        $sql .= " group by oi.department_id ";
        return $this->db()->getAll($sql);
    }

    public function getOrderInfoDisTocs($where)
    {
        $sql="select 
                oi.customer_source_id,COUNT(distinct oi.mobile) as dis_ordernum
            from 
                app_order.base_order_info as oi
                inner JOIN app_order.app_order_details AS d ON oi.id = d.order_id
                 left join cuteframe.customer_sources as cs on oi.customer_source_id = cs.id
            where
                oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0 AND oi.is_zp=0 AND IF (
                    d.favorable_status = 3,
                    d.goods_price - d.favorable_price,
                    d.goods_price
            ) >= 300 ";
        if( isset($where['department_id']) && $where['department_id'] > 0 )
        {
            $sql .= " and oi.department_id in (".$where['department_id'].")";
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
        if(isset($where['fenlei']) && $where['fenlei'] !="")
        {
            $sql .= " and cs.fenlei='".$where['fenlei']."' ";
        }
        if(isset($where['begintime']) && !empty($where['begintime']))
        {
            $sql .= " and oi.pay_date >= '".$where['begintime']." 00:00:00'";   
        }
        if(isset($where['endtime']) && !empty($where['endtime']))
        {
            $sql .= " and oi.pay_date <= '".$where['endtime']." 23:59:59'"; 
        }
        if(isset($where['make_order']) && !empty($where['make_order']))
        {
            if(is_array($where['make_order'])){
                $sql .= " and oi.create_user in('".implode("','", $where['make_order'])."')";
            }else{
                $sql .= " and oi.create_user ='".$where['make_order']."'";
            }
            
        }
        $sql .= " group by oi.customer_source_id ";
        return $this->db()->getAll($sql);
    }

    public function getOrderInfoDisTocsNum($where)
    {
        $sql="select 
                oi.customer_source_id,COUNT(distinct oi.mobile) as dis_ordernum
            from 
                app_order.base_order_info as oi
                inner JOIN app_order.app_order_details AS d ON oi.id = d.order_id
                 left join cuteframe.customer_sources as cs on oi.customer_source_id = cs.id
            where
                oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0 AND oi.is_zp=0 AND IF (
                    d.favorable_status = 3,
                    d.goods_price - d.favorable_price,
                    d.goods_price
            ) < 300 ";
        if( isset($where['department_id']) && $where['department_id'] > 0 )
        {
            $sql .= " and oi.department_id in (".$where['department_id'].")";
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
        if(isset($where['fenlei']) && $where['fenlei'] !="")
        {
            $sql .= " and cs.fenlei='".$where['fenlei']."' ";
        }
        if(isset($where['begintime']) && !empty($where['begintime']))
        {
            $sql .= " and oi.pay_date >= '".$where['begintime']." 00:00:00'";   
        }
        if(isset($where['endtime']) && !empty($where['endtime']))
        {
            $sql .= " and oi.pay_date <= '".$where['endtime']." 23:59:59'"; 
        }
        if(isset($where['make_order']) && !empty($where['make_order']))
        {
            if(is_array($where['make_order'])){
                $sql .= " and oi.create_user in('".implode("','", $where['make_order'])."')";
            }else{
                $sql .= " and oi.create_user ='".$where['make_order']."'";
            }
            
        }
        $sql .= " group by oi.customer_source_id ";
        //echo $sql;die;
        return $this->db()->getAll($sql);
    }

    public function getOrderInfo($where)
    {
		$sql="select 
                SUM(IF(oi.is_zp=1,0,1)) as ordernum,
                SUM(IF(oi.is_zp=1,1,0)) as zpnum,
                oi.department_id,
                sum(IF(oi.is_zp=0,oc.order_amount,0)) as orderamount,
                SUM(IF(oi.is_zp=0,oc.goods_amount - oc.favorable_price,0)) goodsamount,
                sum(IF(oi.is_zp=0,oc.money_paid,0)) as moneypaid,
                SUM(IF(oi.is_zp=0,oc.real_return_price,0)) realreturnprice,
                sum(IF(oi.is_zp=0,oc.money_unpaid,0)) as moneyunpaid,
                SUM(IF((oc.goods_amount - oc.favorable_price)>=300,oc.goods_amount - oc.favorable_price,0)) goodsamounts
            from 
                app_order.base_order_info as oi
                inner join app_order.app_order_account as oc on oi.id=oc.order_id 
            where
                oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0
            ";
		if( isset($where['department_id']) && $where['department_id'] > 0 )
		{
			$sql .= " and oi.department_id in (".$where['department_id'].")";
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
		if(isset($where['make_order']) && !empty($where['make_order']))
        {
            if(is_array($where['make_order'])){
                $sql .= " and oi.create_user in('".implode("','", $where['make_order'])."')";
            }else{
                $sql .= " and oi.create_user ='".$where['make_order']."'";
            }
            
        }
		$sql .= " group by oi.department_id ";
		return $this->db()->getAll($sql);
    }

    public function getZpnum($where)
    {
        $sql="select count(distinct oi.mobile) as num
            from 
                app_order.base_order_info as oi
                inner join app_order.app_order_account as oc on oi.id=oc.order_id 
            where
                oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0 and oi.is_zp = 1
            ";
        if( isset($where['department_id']) && $where['department_id'] > 0 )
        {
            $sql .= " and oi.department_id in (".$where['department_id'].")";
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
        if(isset($where['make_order']) && !empty($where['make_order']))
        {
            if(is_array($where['make_order'])){
                $sql .= " and oi.create_user in('".implode("','", $where['make_order'])."')";
            }else{
                $sql .= " and oi.create_user ='".$where['make_order']."'";
            }
            
        }
        return $this->db()->getOne($sql);
    }
    public function getOrderInfoForManDis($where)
    {
		$sql="select 
                oi.create_user,COUNT(distinct oi.mobile) as dis_ordernum
            from 
                app_order.base_order_info as oi
                inner join app_order.app_order_account as oc on oi.id=oc.order_id and oi.is_delete = 0
            where
                oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0 AND oi.is_zp=0
            ";
		if( isset($where['department_id']) && $where['department_id'] > 0 )
		{
			$sql .= " and oi.department_id in (".$where['department_id'].")";
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
		if(isset($where['make_order']) && !empty($where['make_order']))
        {
            if(is_array($where['make_order'])){
                $sql .= " and oi.create_user in('".implode("','", $where['make_order'])."')";
            }else{
                $sql .= " and oi.create_user ='".$where['make_order']."'";
            }
            
        }
		$sql .= " group by oi.create_user ";
		return $this->db()->getAll($sql);
    }

    public function getOrderInfoForManDisTo($where)
    {
        $sql="select 
                oi.create_user,COUNT(distinct oi.mobile) as dis_ordernum
            from 
                app_order.base_order_info as oi
                inner JOIN app_order.app_order_details AS d ON oi.id = d.order_id
            where
                oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0 AND oi.is_zp=0 AND IF (
                    d.favorable_status = 3,
                    d.goods_price - d.favorable_price,
                    d.goods_price
            ) >= 300 ";
        if( isset($where['department_id']) && $where['department_id'] > 0 )
        {
            $sql .= " and oi.department_id in (".$where['department_id'].")";
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
        if(isset($where['make_order']) && !empty($where['make_order']))
        {
            if(is_array($where['make_order'])){
                $sql .= " and oi.create_user in('".implode("','", $where['make_order'])."')";
            }else{
                $sql .= " and oi.create_user ='".$where['make_order']."'";
            }
            
        }
        $sql .= " group by oi.create_user ";
        return $this->db()->getAll($sql);
    }

    public function getOrderInfoForManDisToNum($where)
    {
        $sql="select 
                oi.create_user,COUNT(distinct oi.mobile) as dis_ordernum
            from 
                app_order.base_order_info as oi
                inner JOIN app_order.app_order_details AS d ON oi.id = d.order_id
            where
                oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0 AND oi.is_zp=0 AND IF (
                    d.favorable_status = 3,
                    d.goods_price - d.favorable_price,
                    d.goods_price
            ) < 300 ";
        if( isset($where['department_id']) && $where['department_id'] > 0 )
        {
            $sql .= " and oi.department_id in (".$where['department_id'].")";
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
        if(isset($where['make_order']) && !empty($where['make_order']))
        {
            if(is_array($where['make_order'])){
                $sql .= " and oi.create_user in('".implode("','", $where['make_order'])."')";
            }else{
                $sql .= " and oi.create_user ='".$where['make_order']."'";
            }
            
        }
        $sql .= " group by oi.create_user ";
        return $this->db()->getAll($sql);
    }

    public function getOrderInfoForMan($where)
    {
		$sql="select 
                SUM(IF(oi.is_zp=1,0,1)) as ordernum,
                SUM(IF(oi.is_zp=1,1,0)) as zpnum,
                oi.create_user,
                sum(IF(oi.is_zp=0,oc.order_amount,0)) as orderamount,
                SUM(IF(oi.is_zp=0,oc.goods_amount - oc.favorable_price,0)) goodsamount,
                sum(IF(oi.is_zp=0,oc.money_paid,0)) as moneypaid,
                SUM(IF(oi.is_zp=0,oc.real_return_price,0)) realreturnprice,
                sum(IF(oi.is_zp=0,oc.money_unpaid,0)) as moneyunpaid,
                SUM(IF((oc.goods_amount - oc.favorable_price)>=300,oc.goods_amount - oc.favorable_price,0)) goodsamounts
            from 
                app_order.base_order_info as oi
                inner join app_order.app_order_account as oc on oi.id=oc.order_id and oi.is_delete = 0
            where
                oi.order_status=2 and oi.order_pay_status in (2,3,4)
            ";
		if( isset($where['department_id']) && $where['department_id'] > 0 )
		{
			$sql .= " and oi.department_id in (".$where['department_id'].")";
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
		if(isset($where['make_order']) && !empty($where['make_order']))
        {
            if(is_array($where['make_order'])){
                $sql .= " and oi.create_user in('".implode("','", $where['make_order'])."')";
            }else{
                $sql .= " and oi.create_user ='".$where['make_order']."'";
            }
            
        }
		$sql .= " group by oi.create_user ";
		return $this->db()->getAll($sql);
    }

    public function getOrderInfoHG($where)
    {
        $sql="select 
                sum(IF (
                    od.favorable_status = 3,
                    od.goods_price - od.favorable_price,
                    od.goods_price
                )) as price
            from 
                app_order.base_order_info as oi
                INNER JOIN app_order.app_order_details AS od ON oi.id = od.order_id
            where
                oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0
            ";
        if( isset($where['department_id']) && $where['department_id'] > 0 )
        {
            $sql .= " and oi.department_id in (".$where['department_id'].")";
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
        if(isset($where['make_order']) && !empty($where['make_order']))
        {
            if(is_array($where['make_order'])){
                $sql .= " and oi.create_user in('".implode("','", $where['make_order'])."')";
            }else{
                $sql .= " and oi.create_user ='".$where['make_order']."'";
            }
            
        }
        if(isset($where['from_ad']) && (!empty($where['from_ad']) || $where['from_ad'] === 0))
        {
            $sql .= " and oi.customer_source_id  = '".$where['from_ad']."' ";
        }
        $sql .= " AND IF (
                    od.favorable_status = 3,
                    od.goods_price - od.favorable_price,
                    od.goods_price
                ) >=300";
        return $this->db()->getOne($sql);
    }
}

