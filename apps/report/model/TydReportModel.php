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
class TydReportModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
        $this->zuan = "'lz','cz','caizuan_goods'";
		$this->_objName = 'shop_cfg';
		parent::__construct($id,$strConn);
	}
	
	//获取预约的信息
	public function getbokecount($where)
	{
        $sql = " select count(distinct abi.customer_mobile) as count,abi.customer_source_id ";
		$sql .= " from front.app_bespoke_info as abi ";
		$sql .= " inner join cuteframe.customer_sources as cs on abi.customer_source_id = cs.id ";
		$sql .= " where abi.is_delete=0 and ";
		
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
		
		//实际到店的
		if(isset($where['real_inshop_time_start']) && !empty($where['real_inshop_time_start']))
		{
			$sql .=" abi.real_inshop_time >= '".$where['real_inshop_time_start']." 00:00:00' and ";
		}
		if(isset($where['real_inshop_time_end']) && !empty($where['real_inshop_time_end']))
		{
			$sql .=" abi.real_inshop_time <= '".$where['real_inshop_time_end']." 23:59:59' and ";
		}
		if(isset($where['re_status']) && $where['re_status']>0)
		{
			$sql .= "abi.re_status = '".$where['re_status']."' and ";
		}
        if(isset($where['deal_status']) && $where['deal_status']>0)
        {
            $sql .= "abi.deal_status = '".$where['deal_status']."' and ";
        }
		
		//当期应到的
		if(isset($where['bespoke_inshop_time_start']) && !empty($where['bespoke_inshop_time_start']))
		{
			$sql .=" abi.bespoke_inshop_time >= '".$where['bespoke_inshop_time_start']." 00:00:00' and ";
		}
		if(isset($where['bespoke_inshop_time_end']) && !empty($where['bespoke_inshop_time_end']))
		{
			$sql .=" abi.bespoke_inshop_time <= '".$where['bespoke_inshop_time_end']." 23:59:59' and ";
		}
		
		if(isset($where['department_id']) && !empty($where['department_id']))
		{
			$sql .= "abi.department_id='".$where['department_id']."' and ";
		}
		
		if(isset($where['customer_source_id']) && $where['customer_source_id']>0)
		{
			$sql .= "abi.customer_source_id='".$where['customer_source_id']."' and ";
		}
		
		if(isset($where['fenlei']) && $where['fenlei'] !="")
		{
			$sql .= "cs.fenlei='".$where['fenlei']."' and ";
		}

        //根据销售顾问导出
        if(isset($where['make_order']) && !empty($where['make_order']))
        {
            if(is_array($where['make_order'])){
                $sql .= "abi.accecipt_man in('".implode("','", $where['make_order'])."') and ";
            }else{
                $sql .= "abi.accecipt_man='".$where['make_order']."' and ";
            }
            
        }
		$sql .=" 1 group by abi.customer_source_id ";
        //echo $sql;die;
		return $this->db()->getAll($sql);
	}
	
	//获取订单的统计信息
	public function getordercount($where)
	{
		$sql="select
                oi.customer_source_id,cs.source_name,cs.fenlei,
                SUM(IF(oi.is_zp=0,1,0)) as ordernum,
                sum(oi.is_zp) as zpnum,
                sum(IF(oi.is_zp=0,oc.order_amount,0)) as orderamount,
                SUM(IF(oi.is_zp=0,oc.goods_amount - oc.favorable_price,0)) goodsamount,
                sum(IF(oi.is_zp=0,oc.money_paid,0)) as moneypaid,
                SUM(IF(oi.is_zp=0,oc.real_return_price,0)) realreturnprice,
                sum(IF(oi.is_zp=0,oc.money_unpaid,0)) as moneyunpaid,
                cs.source_own_id,cs.source_own
            from
                app_order.base_order_info as oi
                left join app_order.app_order_account as oc on oi.id=oc.order_id
                left join cuteframe.customer_sources as cs on oi.customer_source_id = cs.id
            where
                oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0";

		if( isset($where['department_id']) && $where['department_id'] > 0 )
		{
			$sql .= " and oi.department_id= ".$where['department_id'];
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

        //根据销售顾问导出

        if(isset($where['create_user']) && !empty($where['create_user']))
        {
            if(is_array($where['create_user'])){
                $sql .= " and oi.create_user in('".implode("','", $where['create_user'])."') ";
            }else{
                $sql .= " and oi.create_user ='".$where['create_user']."' ";
            }
            
        }

        if(isset($where['make_order']) && !empty($where['make_order']))
        {
            if(is_array($where['make_order'])){
                $sql .= " and oi.create_user in('".implode("','", $where['make_order'])."') ";
            }else{
                $sql .= " and oi.create_user ='".$where['make_order']."' ";
            }
            
        }
		
		$sql .= " group by oi.customer_source_id ";
        //echo $sql;die;
		return $this->db()->getAll($sql);
	}

    //获取订单的赠品
    public function getIsZpNum($where)
    {
        $sql="select
                count(distinct oi.mobile) as num
            from
                app_order.base_order_info as oi
                left join app_order.app_order_account as oc on oi.id=oc.order_id
                left join cuteframe.customer_sources as cs on oi.customer_source_id = cs.id
            where
                oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0 and oi.is_zp = 1";

        if( isset($where['department_id']) && $where['department_id'] > 0 )
        {
            $sql .= " and oi.department_id= ".$where['department_id'];
        }
        //if( isset($where['from_ad']) && $where['from_ad'] > 0 )
        //{
            //$sql .= " and oi.customer_source_id= ".$where['from_ad'];
        //}
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
        if(isset($where['from_ad']) && (!empty($where['from_ad']) || $where['from_ad'] === 0))
        {
            $sql .= " and oi.customer_source_id  = '".$where['from_ad']."' ";
        }
        if(isset($where['endtime']) && !empty($where['endtime']))
        {
            $sql .= " and oi.pay_date <= '".$where['endtime']." 23:59:59'"; 
        }
        
        if(isset($where['fenlei']) && $where['fenlei'] !="")
        {
            $sql .= " and cs.fenlei='".$where['fenlei']."' ";
        }

        //根据销售顾问导出

        if(isset($where['create_user']) && !empty($where['create_user']))
        {
            if(is_array($where['create_user'])){
                $sql .= " and oi.create_user in('".implode("','", $where['create_user'])."') ";
            }else{
                $sql .= " and oi.create_user ='".$where['create_user']."' ";
            }
            
        }

        if(isset($where['make_order']) && !empty($where['make_order']))
        {
            if(is_array($where['make_order'])){
                $sql .= " and oi.create_user in('".implode("','", $where['make_order'])."') ";
            }else{
                $sql .= " and oi.create_user ='".$where['make_order']."' ";
            }
            
        }
        //echo $sql;die;
        return $this->db()->getOne($sql);
    }
    //获取订单的统计信息 - 统计预约数 相同预约的订单算一个
    public function getordercountDis($where) {
        $sql="
            select
                oi.customer_source_id,COUNT(distinct oi.bespoke_id) as count
            from
                app_order.base_order_info as oi
                left join app_order.app_order_account as oc on oi.id=oc.order_id
                left join cuteframe.customer_sources as cs on oi.customer_source_id = cs.id
            where
                oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0 AND oi.is_zp=0
            ";
        if( isset($where['department_id']) && $where['department_id'] > 0 )
        {
            $sql .= " and oi.department_id= ".$where['department_id'];
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

        $sql .= " group by oi.customer_source_id ";
        return $this->db()->getAll($sql);
    }

    //获取订单的统计信息
    public function getbespokecount($where)
    {
        $sql="select
                oi.customer_source_id,cs.source_name,cs.fenlei
            from
                front.app_bespoke_info as oi
                left join cuteframe.customer_sources as cs on oi.customer_source_id = cs.id
            where
                oi.is_delete=0 and oi.bespoke_status = 2";

        if( isset($where['department_id']) && $where['department_id'] > 0 )
        {
            $sql .= " and oi.department_id= ".$where['department_id'];
        }

        if(isset($where['begintime']) && !empty($where['begintime']))
        {
            $sql .= " and oi.create_time >= '".$where['begintime']." 00:00:00'";   
        }

        if(isset($where['endtime']) && !empty($where['endtime']))
        {
            $sql .= " and oi.create_time <= '".$where['endtime']." 23:59:59'"; 
        }
        
        if(isset($where['fenlei']) && $where['fenlei'] !="")
        {
            $sql .= " and cs.fenlei='".$where['fenlei']."' ";
        }

        //根据销售顾问导出
        if(isset($where['create_user']) && !empty($where['create_user']))
        {
            if(is_array($where['create_user'])){
                $sql .= " and oi.accecipt_man in('".implode("','", $where['create_user'])."') ";
            }else{
                $sql .= " and oi.accecipt_man ='".$where['create_user']."' ";
            }
            
        }

        if(isset($where['make_order']) && !empty($where['make_order']))
        {
            if(is_array($where['make_order'])){
                $sql .= " and oi.accecipt_man in('".implode("','", $where['make_order'])."') ";
            }else{
                $sql .= " and oi.accecipt_man ='".$where['make_order']."' ";
            }
            
        }
        //echo $sql;die;
        $sql .= " group by oi.customer_source_id ";
        return $this->db()->getAll($sql);
    }

    // add by gengchao, 各店到店成交率1：预约
    public function getShopsBespokeCount($where) {
        $sql = " select abi.department_id,cs.fenlei,count(abi.bespoke_id) as count ";
		$sql .= " from front.app_bespoke_info as abi ";
		$sql .= " inner join cuteframe.customer_sources as cs on abi.customer_source_id = cs.id ";
		$sql .= " where abi.is_delete=0 ";

		//添加时间
		if(isset($where['create_time_start']) && !empty($where['create_time_start'])) {
            $sql .="and abi.create_time >= '".$where['create_time_start']." 00:00:00' ";
        }
		if(isset($where['create_time_end']) && !empty($where['create_time_end'])) {
            $sql .="and abi.create_time <= '".$where['create_time_end']." 23:59:59' ";
        }
        //当期应到的
        if(isset($where['bespoke_inshop_time_start']) && !empty($where['bespoke_inshop_time_start'])) {
            $sql .="and abi.bespoke_inshop_time >= '".$where['bespoke_inshop_time_start']." 00:00:00' ";
        }
        if(isset($where['bespoke_inshop_time_end']) && !empty($where['bespoke_inshop_time_end'])) {
            $sql .="and abi.bespoke_inshop_time <= '".$where['bespoke_inshop_time_end']." 23:59:59' ";
        }
		//实际到店的
		if(isset($where['real_inshop_time_start']) && !empty($where['real_inshop_time_start'])) {
            $sql .="and abi.real_inshop_time >= '".$where['real_inshop_time_start']." 00:00:00' ";
        }
		if(isset($where['real_inshop_time_end']) && !empty($where['real_inshop_time_end'])) {
            $sql .="and abi.real_inshop_time <= '".$where['real_inshop_time_end']." 23:59:59' ";
        }

        if(isset($where['bespoke_status']) && $where['bespoke_status']>0) {
            $sql .= "and abi.bespoke_status = '".$where['bespoke_status']."' ";
        }
		if(isset($where['re_status']) && $where['re_status']>0) {
            $sql .= "and abi.re_status = '".$where['re_status']."' ";
        }
        if(isset($where['deal_status']) && $where['deal_status']>0) {
            $sql .= "and abi.deal_status = '".$where['deal_status']."' ";
        }

		if(isset($where['department_id']) && !empty($where['department_id'])) {
            $dep_ids = implode(',', $where['department_id']);
            $sql .= " and abi.department_id in ($dep_ids)";
        }
		if(isset($where['customer_source_id']) && $where['customer_source_id']>0) {
            $sql .= "and abi.customer_source_id='".$where['customer_source_id']."' ";
        }
		if(isset($where['fenlei']) && $where['fenlei'] !="") {
            $sql .= "and cs.fenlei='".$where['fenlei']."'";
        }
        $sql .=" group by abi.department_id,cs.fenlei ";
        return $this->db()->getAll($sql);
    }

    // add by gengchao, 各店到店成交率2：订单
    public function getShopsOrderCount($where) {
        $sql=" select oi.department_id,cs.fenlei,count(1) as count,sum(IF(oi.is_zp=0,1,0)) as ordernum
            from app_order.base_order_info as oi
            left join app_order.app_order_account as oc on oi.id=oc.order_id
            left join cuteframe.customer_sources as cs on oi.customer_source_id = cs.id
            where oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0";

        if(isset($where['begintime']) && !empty($where['begintime'])) {
            $sql .= " and oi.pay_date >= '".$where['begintime']." 00:00:00'";
        }
        if(isset($where['endtime']) && !empty($where['endtime'])) {
            $sql .= " and oi.pay_date <= '".$where['endtime']." 23:59:59'";
        }

        if( isset($where['department_id']) && $where['department_id'] ) {
            $dep_ids = implode(',', $where['department_id']);
            $sql .= " and oi.department_id in ($dep_ids)";
        }
        if(isset($where['fenlei']) && $where['fenlei'] !="") {
            $sql .= " and cs.fenlei='".$where['fenlei']."' ";
        }
        if(isset($where['orderenter']) && !empty($where['orderenter'])) {
            if($where['orderenter'] == '婚博会')
            {
                $sql .= " and oi.referer ='婚博会' ";
            }else{
                $sql .= " and oi.referer <> '婚博会' ";
            }
        }
        $sql .= " group by oi.department_id,cs.fenlei";
        return $this->db()->getAll($sql);
    }

    // add by gengchao, 网销相关报表1：预约
    public function getNetBespokeCount($where) {
        $sql = " select abi.make_order,count(abi.bespoke_id) as count ";
        $sql .= " from front.app_bespoke_info as abi ";
        $sql .= " where abi.is_delete=0 ";

        //添加时间
        if(!empty($where['create_time_start'])) {
            $sql .="and abi.create_time >= '".$where['create_time_start']." 00:00:00' ";
        }
        if(!empty($where['create_time_end'])) {
            $sql .="and abi.create_time <= '".$where['create_time_end']." 23:59:59' ";
        }
        //实际到店的
        if(!empty($where['real_inshop_time_start'])) {
            $sql .="and abi.real_inshop_time >= '".$where['real_inshop_time_start']." 00:00:00' ";
        }
        if(!empty($where['real_inshop_time_end'])) {
            $sql .="and abi.real_inshop_time <= '".$where['real_inshop_time_end']." 23:59:59' ";
        }

        if(!empty($where['bespoke_status'])) {
            $sql .= "and abi.bespoke_status = '".$where['bespoke_status']."' ";
        }
        if(!empty($where['re_status'])) {
            $sql .= "and abi.re_status = '".$where['re_status']."' ";
        }
        if(!empty($where['deal_status'])) {
            $sql .= "and abi.deal_status = '".$where['deal_status']."' ";
        }

        if(!empty($where['department_id'])) {
            $sql .= " and abi.department_id =".$where['department_id'];
        }
        if (!empty($where['make_order'])) {
            if (is_array($where['make_order'])) {
                $str_names = '';
                foreach ($where['make_order'] as $name) {
                    $str_names .= "'{$name}',";
                }
                $str_names = rtrim($str_names, ',');
                $sql .= " and abi.make_order in (".$str_names.") ";
            } else {
                $sql .= " and abi.make_order='".$where['make_order']."' ";
            }
        }
        $sql .=" group by abi.make_order ";
        return $this->db()->getAll($sql);
    }

    /**
    *    网销相关报表1：预约
    */
    public function getNetBespokeCountDetail($where) {
        $sql = " select 
            abi.bespoke_sn,
            sc.channel_name,
            cs.source_name,
            abi.customer,
            abi.customer_mobile,
            abi.create_time,
            abi.bespoke_inshop_time,
            abi.real_inshop_time,
            abi.accecipt_man,
            abi.deal_status,
            abi.re_status,
            abi.queue_status,
            abi.withuserdo
        ";
        $sql .= " from front.app_bespoke_info as abi ";
        $sql .= " join cuteframe.sales_channels sc on sc.id = abi.department_id ";
        $sql .= " join cuteframe.customer_sources cs on cs.id = abi.customer_source_id ";
        $sql .= " where abi.is_delete=0 ";

        //添加时间
        if(!empty($where['create_time_start'])) {
            $sql .="and abi.create_time >= '".$where['create_time_start']." 00:00:00' ";
        }
        if(!empty($where['create_time_end'])) {
            $sql .="and abi.create_time <= '".$where['create_time_end']." 23:59:59' ";
        }
        //实际到店的
        if(!empty($where['real_inshop_time_start'])) {
            $sql .="and abi.real_inshop_time >= '".$where['real_inshop_time_start']." 00:00:00' ";
        }
        if(!empty($where['real_inshop_time_end'])) {
            $sql .="and abi.real_inshop_time <= '".$where['real_inshop_time_end']." 23:59:59' ";
        }

        if(!empty($where['bespoke_status'])) {
            $sql .= "and abi.bespoke_status = '".$where['bespoke_status']."' ";
        }
        if(!empty($where['re_status'])) {
            $sql .= "and abi.re_status = '".$where['re_status']."' ";
        }
        if(!empty($where['deal_status'])) {
            $sql .= "and abi.deal_status = '".$where['deal_status']."' ";
        }

        if(!empty($where['department_id'])) {
            $sql .= " and abi.department_id =".$where['department_id'];
        }
        if (!empty($where['make_order'])) {
            if (is_array($where['make_order'])) {
                $str_names = '';
                foreach ($where['make_order'] as $name) {
                    $str_names .= "'{$name}',";
                }
                $str_names = rtrim($str_names, ',');
                $sql .= " and abi.make_order in (".$str_names.") ";
            } else {
                $sql .= " and abi.make_order='".$where['make_order']."' ";
            }
        }
        $sql .=" order by abi.create_time desc ";
        return $this->db()->getAll($sql);
    }

    // add by gengchao, 网销相关报表2：订单, 不包含赠品 和优惠
    public function getNetOrderCount($where) {
        //order_amount有误，只能计算得出：goods_amount-coupon_price-favorable_price+card_fee+pack_fee+pay_fee+insure_fee
        $sql=" select bs.make_order,count(1) as count,
            sum(goods_amount-coupon_price-favorable_price+card_fee+pack_fee+pay_fee+insure_fee) as amount
            from app_order.base_order_info as oi
            join app_order.app_order_account as oc on oi.id=oc.order_id
            join front.app_bespoke_info bs on oi.bespoke_id=bs.bespoke_id
            where oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0 ";

        if(!empty($where['begintime'])) {
            $sql .= " and oi.pay_date >= '".$where['begintime']." 00:00:00'";
        }
        if(!empty($where['endtime'])) {
            $sql .= " and oi.pay_date <= '".$where['endtime']." 23:59:59'";
        }
        if($where['department_id']) {
            $sql .= " and oi.department_id =".$where['department_id'];
        }
        if (!empty($where['make_order'])) {
            if (is_array($where['make_order'])) {
                $str_names = '';
                foreach ($where['make_order'] as $name) {
                    $str_names .= "'{$name}',";
                }
                $str_names = rtrim($str_names, ',');
                $sql .= " and bs.make_order in (".$str_names.") ";
            } else {
                $sql .= " and bs.make_order='".$where['make_order']."' ";
            }
        }
        if (isset($where['is_zp'])) {
            $sql .= ' and oi.is_zp='.$where['is_zp'];
        }
        $sql .= " group by bs.make_order";
        return $this->db()->getAll($sql);
    }

    // “相同客户算一个订单”（合并了同一个手机号的订单数）
    public function getNetOrderCountByMobile($where) {
        //order_amount有误，只能计算得出：goods_amount-coupon_price-favorable_price+card_fee+pack_fee+pay_fee+insure_fee
        $sql=" select bs.make_order,count(distinct oi.mobile) as count,
            sum(goods_amount-coupon_price-favorable_price+card_fee+pack_fee+pay_fee+insure_fee) as amount
            from app_order.base_order_info as oi
            join app_order.app_order_account as oc on oi.id=oc.order_id
            join front.app_bespoke_info bs on oi.bespoke_id=bs.bespoke_id
            where oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0 ";

        if(!empty($where['begintime'])) {
            $sql .= " and oi.pay_date >= '".$where['begintime']." 00:00:00'";
        }
        if(!empty($where['endtime'])) {
            $sql .= " and oi.pay_date <= '".$where['endtime']." 23:59:59'";
        }
        if($where['department_id']) {
            $sql .= " and oi.department_id =".$where['department_id'];
        }
        if (!empty($where['make_order'])) {
            if (is_array($where['make_order'])) {
                $str_names = '';
                foreach ($where['make_order'] as $name) {
                    $str_names .= "'{$name}',";
                }
                $str_names = rtrim($str_names, ',');
                $sql .= " and bs.make_order in (".$str_names.") ";
            } else {
                $sql .= " and bs.make_order='".$where['make_order']."' ";
            }
        }
        if (isset($where['is_zp'])) {
            $sql .= ' and oi.is_zp='.$where['is_zp'];
        }
        $sql .= " group by bs.make_order";
        return $this->db()->getAll($sql);
    }

   /**
    *    网销相关报表1：订单, 不包含赠品 和优惠
    */
    public function getNetOrderCountDetail($where) {
        $sql=" select 
                bs.bespoke_sn,
                oi.order_sn,
                oi.pay_date,
                bs.make_order,
                oi.create_user,
                sc.channel_name,
                cs.source_name,
                oi.consignee,
                oi.mobile,
                IF(od.goods_type in ({$this->zuan}),'裸钻','成品') goods_type,
                od.goods_id,
                od.goods_sn,
                od.zhengshuhao,
                if(od.favorable_status=3,od.goods_price-od.favorable_price,od.goods_price) as price
            from app_order.base_order_info as oi
            join app_order.app_order_account as oc on oi.id=oc.order_id
            join front.app_bespoke_info bs on oi.bespoke_id=bs.bespoke_id
            join app_order.app_order_details as od on oi.id=od.order_id
            join cuteframe.sales_channels sc on sc.id = oi.department_id
            join cuteframe.customer_sources cs on cs.id = oi.customer_source_id
            where oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0 ";

        if(!empty($where['begintime'])) {
            $sql .= " and oi.pay_date >= '".$where['begintime']." 00:00:00'";
        }
        if(!empty($where['endtime'])) {
            $sql .= " and oi.pay_date <= '".$where['endtime']." 23:59:59'";
        }
        if($where['department_id']) {
            $sql .= " and oi.department_id =".$where['department_id'];
        }
        if (!empty($where['make_order'])) {
            if (is_array($where['make_order'])) {
                $str_names = '';
                foreach ($where['make_order'] as $name) {
                    $str_names .= "'{$name}',";
                }
                $str_names = rtrim($str_names, ',');
                $sql .= " and bs.make_order in (".$str_names.") ";
            } else {
                $sql .= " and bs.make_order='".$where['make_order']."' ";
            }
        }
        if (isset($where['is_zp'])) {
            $sql .= ' and oi.is_zp='.$where['is_zp'];
        }
        $sql .=" order by oi.pay_date desc ";
        return $this->db()->getAll($sql);
    }

    /**
    *   网销相关报表2：网络订单
    */
    public function getNetOrderCountOnline($where) {
        $sql=" select bs.make_order,count(1) as count
            from app_order.base_order_info as oi
            join app_order.app_order_account as oc on oi.id=oc.order_id
            join front.app_bespoke_info bs on oi.bespoke_id=bs.bespoke_id
            where oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0 
            AND ((bs.make_order = oi.create_user AND oi.create_user = oi.genzong ) OR ( bs.make_order = oi.create_user AND  (oi.genzong = '' or oi.genzong is null)))
            ";

        if(!empty($where['begintime'])) {
            $sql .= " and oi.pay_date >= '".$where['begintime']." 00:00:00'";
        }
        if(!empty($where['endtime'])) {
            $sql .= " and oi.pay_date <= '".$where['endtime']." 23:59:59'";
        }
        if($where['department_id']) {
            $sql .= " and oi.department_id =".$where['department_id'];
        }
        if (!empty($where['make_order'])) {
            if (is_array($where['make_order'])) {
                $str_names = '';
                foreach ($where['make_order'] as $name) {
                    $str_names .= "'{$name}',";
                }
                $str_names = rtrim($str_names, ',');
                $sql .= " and bs.make_order in (".$str_names.") ";
            } else {
                $sql .= " and bs.make_order='".$where['make_order']."' ";
            }
        }
        if (isset($where['is_zp'])) {
            $sql .= ' and oi.is_zp='.$where['is_zp'];
        }
        $sql .= " group by bs.make_order";
        return $this->db()->getAll($sql);
    }

    /**
    *   网销相关报表2：网络订单
    */
    public function getNetOrderCountOnlineDetail($where) {
        $sql="select
                bs.bespoke_sn,
                oi.order_sn,
                oi.pay_date,
                bs.make_order,
                oi.create_user,
                sc.channel_name,
                cs.source_name,
                oi.consignee,
                oi.mobile,
                IF(od.goods_type in ({$this->zuan}),'裸钻','成品') goods_type,
                od.goods_id,
                od.goods_sn,
                od.zhengshuhao,
                if(od.favorable_status=3,od.goods_price-od.favorable_price,od.goods_price) as price
            from app_order.base_order_info as oi
            join app_order.app_order_account as oc on oi.id=oc.order_id
            join front.app_bespoke_info bs on oi.bespoke_id=bs.bespoke_id
            join app_order.app_order_details as od on oi.id=od.order_id
            join cuteframe.sales_channels sc on sc.id = oi.department_id
            join cuteframe.customer_sources cs on cs.id = oi.customer_source_id
            where oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0 
            AND ((bs.make_order = oi.create_user AND oi.create_user = oi.genzong ) OR ( bs.make_order = oi.create_user AND  (oi.genzong = '' or oi.genzong is null)))
            ";

        if(!empty($where['begintime'])) {
            $sql .= " and oi.pay_date >= '".$where['begintime']." 00:00:00'";
        }
        if(!empty($where['endtime'])) {
            $sql .= " and oi.pay_date <= '".$where['endtime']." 23:59:59'";
        }
        if($where['department_id']) {
            $sql .= " and oi.department_id =".$where['department_id'];
        }
        if (!empty($where['make_order'])) {
            if (is_array($where['make_order'])) {
                $str_names = '';
                foreach ($where['make_order'] as $name) {
                    $str_names .= "'{$name}',";
                }
                $str_names = rtrim($str_names, ',');
                $sql .= " and bs.make_order in (".$str_names.") ";
            } else {
                $sql .= " and bs.make_order='".$where['make_order']."' ";
            }
        }
        if (isset($where['is_zp'])) {
            $sql .= ' and oi.is_zp='.$where['is_zp'];
        }
        return $this->db()->getAll($sql);
    }


    // add by gengchao, 网销相关报表3：退款 return_goods_amount, 退款不退货 return_only_amount
    public function getNetOrderReturnCount($where) {
        $sql=" select bs.make_order,
            sum(if(r.return_by=1,r.real_return_amount,0)) as return_goods_amount,
            sum(if(r.return_by=2,r.real_return_amount,0)) as return_money_amount
            from app_order.base_order_info as oi
            join app_order.app_order_account as oc on oi.id=oc.order_id
            join front.app_bespoke_info bs on oi.bespoke_id=bs.bespoke_id
            join app_order.app_return_goods r on oi.id=r.order_id
            join app_order.app_return_check c on r.return_id=c.return_id
            where oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0 and c.deparment_finance_status=1";

        if(!empty($where['begintime'])) {
            $sql .= " and c.deparment_finance_time >= '".$where['begintime']." 00:00:00'";
        }
        if(!empty($where['endtime'])) {
            $sql .= " and c.deparment_finance_time <= '".$where['endtime']." 23:59:59'";
        }
        if(!empty($where['department_id'])) {
            $sql .= " and oi.department_id =".$where['department_id'];
        }
        if (!empty($where['make_order'])) {
            if (is_array($where['make_order'])) {
                $str_names = '';
                foreach ($where['make_order'] as $name) {
                    $str_names .= "'{$name}',";
                }
                $str_names = rtrim($str_names, ',');
                $sql .= " and bs.make_order in (".$str_names.") ";
            } else {
                $sql .= " and bs.make_order='".$where['make_order']."' ";
            }
        }
        $sql .= " group by bs.make_order";
        return $this->db()->getAll($sql);
    }
    /**
    *    网销相关报表1：退订单 real_return_amount
    */
    public function getNetOrderReturnCountDetail($where) {
        $sql=" select 
                bs.bespoke_sn,
                oi.order_sn,
                oi.pay_date,
                bs.make_order,
                oi.create_user,
                sc.channel_name,
                cs.source_name,
                oi.consignee,
                oi.mobile,
                r.real_return_amount price
            from app_order.base_order_info as oi
            join cuteframe.sales_channels sc on sc.id = oi.department_id
            join cuteframe.customer_sources cs on cs.id = oi.customer_source_id
            join app_order.app_order_account as oc on oi.id=oc.order_id
            join front.app_bespoke_info bs on oi.bespoke_id=bs.bespoke_id
            join app_order.app_return_goods r on oi.id=r.order_id
            join app_order.app_return_check c on r.return_id=c.return_id
            where oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0 and c.deparment_finance_status=1";

        if(!empty($where['begintime'])) {
            $sql .= " and c.deparment_finance_time >= '".$where['begintime']." 00:00:00'";
        }
        if(!empty($where['endtime'])) {
            $sql .= " and c.deparment_finance_time <= '".$where['endtime']." 23:59:59'";
        }
        if(!empty($where['department_id'])) {
            $sql .= " and oi.department_id =".$where['department_id'];
        }
        if(!empty($where['return_by'])) {
            $sql .= " and r.return_by =".$where['return_by'];
        }
        if (!empty($where['make_order'])) {
            if (is_array($where['make_order'])) {
                $str_names = '';
                foreach ($where['make_order'] as $name) {
                    $str_names .= "'{$name}',";
                }
                $str_names = rtrim($str_names, ',');
                $sql .= " and bs.make_order in (".$str_names.") ";
            } else {
                $sql .= " and bs.make_order='".$where['make_order']."' ";
            }
        }
        $sql .=" order by c.deparment_finance_time desc ";
        return $this->db()->getAll($sql);
    }
    /**
    *    网销相关报表1：退货 return_only_amount
    */
    public function getNetOrderReturnCountgoodsDetail($where) {
        $sql=" select 
                bs.bespoke_sn,
                oi.order_sn,
                oi.pay_date,
                bs.make_order,
                oi.create_user,
                sc.channel_name,
                cs.source_name,
                oi.consignee,
                oi.mobile,
                IF(od.goods_type in ({$this->zuan}),'裸钻','成品') goods_type,
                od.goods_id,
                od.goods_sn,
                od.zhengshuhao,
                if(od.favorable_status=3,od.goods_price-od.favorable_price,od.goods_price) as price,
                r.real_return_amount price
            from app_order.base_order_info as oi
            join cuteframe.sales_channels sc on sc.id = oi.department_id
            join cuteframe.customer_sources cs on cs.id = oi.customer_source_id
            join app_order.app_order_account as oc on oi.id=oc.order_id
            join front.app_bespoke_info bs on oi.bespoke_id=bs.bespoke_id
            join app_order.app_return_goods r on oi.id=r.order_id
            join app_order.app_return_check c on r.return_id=c.return_id
            join app_order.app_order_details od on od.order_id = oi.id
            where oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0 and c.deparment_finance_status=1 AND od.id = r.order_goods_id ";

        if(!empty($where['begintime'])) {
            $sql .= " and c.deparment_finance_time >= '".$where['begintime']." 00:00:00'";
        }
        if(!empty($where['endtime'])) {
            $sql .= " and c.deparment_finance_time <= '".$where['endtime']." 23:59:59'";
        }
        if(!empty($where['department_id'])) {
            $sql .= " and oi.department_id =".$where['department_id'];
        }
        if(!empty($where['return_by'])) {
            $sql .= " and r.return_by =".$where['return_by'];
        }
        if (!empty($where['make_order'])) {
            if (is_array($where['make_order'])) {
                $str_names = '';
                foreach ($where['make_order'] as $name) {
                    $str_names .= "'{$name}',";
                }
                $str_names = rtrim($str_names, ',');
                $sql .= " and bs.make_order in (".$str_names.") ";
            } else {
                $sql .= " and bs.make_order='".$where['make_order']."' ";
            }
        }
        $sql .=" order by c.deparment_finance_time desc ";
        return $this->db()->getAll($sql);
    }
    
    // add by gengchao, 网销销售报表1：新增裸钻、成品的网销数和网销金额
    public function getNetSaleStat($where) {
        $sql=" select bs.make_order,
            sum(IF(od.goods_type in ({$this->zuan}),1,0)) as lz_count,
            sum(IF(od.goods_type in ({$this->zuan}),if(od.favorable_status=3,od.goods_price-od.favorable_price,od.goods_price),0)) as lz_amount,
            sum(IF(od.goods_type not in ({$this->zuan}),1,0)) as cp_count,
            sum(IF(od.goods_type not in ({$this->zuan}),if(od.favorable_status=3,od.goods_price-od.favorable_price,od.goods_price),0)) as cp_amount
            from app_order.base_order_info as oi
            join app_order.app_order_details as od on oi.id=od.order_id
            join front.app_bespoke_info bs on oi.bespoke_id=bs.bespoke_id
            where oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0 ";

        if(!empty($where['begintime'])) {
            $sql .= " and oi.pay_date >= '".$where['begintime']." 00:00:00'";
        }
        if(!empty($where['endtime'])) {
            $sql .= " and oi.pay_date <= '".$where['endtime']." 23:59:59'";
        }
        if($where['department_id']) {
            $sql .= " and oi.department_id =".$where['department_id'];
        }
        if (!empty($where['make_order'])) {
            if (is_array($where['make_order'])) {
                $str_names = '';
                foreach ($where['make_order'] as $name) {
                    $str_names .= "'{$name}',";
                }
                $str_names = rtrim($str_names, ',');
                $sql .= " and bs.make_order in (".$str_names.") ";
            } else {
                $sql .= " and bs.make_order='".$where['make_order']."' ";
            }
        }
        if (isset($where['is_zp'])) {
            $sql .= ' and oi.is_zp='.$where['is_zp'];
        }
        $sql .= " group by bs.make_order";
        return $this->db()->getAll($sql);
    }

    // add by gengchao, 网销销售报表2：退款退货：裸钻、成品的网销数和网销金额
    public function getNetSaleReturnGoodsStat($where) {
        // 分组子sql：因为同一商品可能退款多次
        $return_sql = "select order_goods_id, sum(real_return_amount) return_goods_amount
            from app_order.app_return_goods rg join app_order.app_return_check rc on rg.return_id=rc.return_id
            where rc.deparment_finance_status=1 and return_by=1";
        if(!empty($where['begintime'])) {
            $return_sql .= " and rc.deparment_finance_time >= '".$where['begintime']." 00:00:00'";
        }
        if(!empty($where['endtime'])) {
            $return_sql .= " and rc.deparment_finance_time <= '".$where['endtime']." 23:59:59'";
        }
        $return_sql .= 'group by order_goods_id';

        $sql=" select bs.make_order,
                sum(IF(od.goods_type in ({$this->zuan}),1,0)) as lz_return_goods_count,
                sum(IF(od.goods_type not in ({$this->zuan}),1,0)) as cp_return_goods_count,
                sum(IF(od.goods_type in ({$this->zuan}),rt.return_goods_amount,0)) as lz_return_goods_amount,
                sum(IF(od.goods_type not in ({$this->zuan}),rt.return_goods_amount,0)) as cp_return_goods_amount
            from app_order.app_order_details as od
            join app_order.base_order_info as oi on oi.id=od.order_id
            join front.app_bespoke_info bs on bs.bespoke_id=oi.bespoke_id
            join ($return_sql) rt on rt.order_goods_id=od.id
            where oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0  and od.is_return=1";

        if(!empty($where['department_id'])) {
            $sql .= " and oi.department_id =".$where['department_id'];
        }
        if (!empty($where['make_order'])) {
            if (is_array($where['make_order'])) {
                $str_names = '';
                foreach ($where['make_order'] as $name) {
                    $str_names .= "'{$name}',";
                }
                $str_names = rtrim($str_names, ',');
                $sql .= " and bs.make_order in (".$str_names.") ";
            } else {
                $sql .= " and bs.make_order='".$where['make_order']."' ";
            }
        }
        if (isset($where['is_zp'])) {
            $sql .= ' and oi.is_zp='.$where['is_zp'];
        }
        $sql .= " group by bs.make_order";
        return $this->db()->getAll($sql);
    }
    // add by gengchao, 网销销售报表3：退款不退货：裸钻、成品的网销数和网销金额
    public function getNetSaleReturnStat($where) {
        $return_sql = "select order_id, sum(real_return_amount) return_amount
            from app_order.app_return_goods rg join app_order.app_return_check rc on rg.return_id=rc.return_id
            where rc.deparment_finance_status=1 and return_by=2";
        if(!empty($where['begintime'])) {
            $return_sql .= " and rc.deparment_finance_time >= '".$where['begintime']." 00:00:00'";
        }
        if(!empty($where['endtime'])) {
            $return_sql .= " and rc.deparment_finance_time <= '".$where['endtime']." 23:59:59'";
        }
        $return_sql .= 'group by order_id';

        $sql=" select bs.make_order,sum(rt.return_amount) return_amount  from app_order.base_order_info as oi
            join front.app_bespoke_info bs on bs.bespoke_id=oi.bespoke_id join ($return_sql) rt on rt.order_id=oi.id
            where oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0 ";

        if(!empty($where['department_id'])) {
            $sql .= " and oi.department_id =".$where['department_id'];
        }
        if (!empty($where['make_order'])) {
            if (is_array($where['make_order'])) {
                $str_names = '';
                foreach ($where['make_order'] as $name) {
                    $str_names .= "'{$name}',";
                }
                $str_names = rtrim($str_names, ',');
                $sql .= " and bs.make_order in (".$str_names.") ";
            } else {
                $sql .= " and bs.make_order='".$where['make_order']."' ";
            }
        }
        if (isset($where['is_zp'])) {
            $sql .= ' and oi.is_zp='.$where['is_zp'];
        }
        $sql .= " group by bs.make_order";
        return $this->db()->getAll($sql);
    }

    // add by gengchao, 网销销售报表4：发货裸钻、成品的网销数和网销金额
    public function getNetSaleShipStat($where) {
        $sql=" select bs.make_order,
            sum(IF(od.goods_type in ({$this->zuan}),1,0)) as lz_count,
            sum(IF(od.goods_type in ({$this->zuan}),if(od.favorable_status=3,od.goods_price-od.favorable_price,od.goods_price),0)) as lz_amount,
            sum(IF(od.goods_type not in ({$this->zuan}),1,0)) as cp_count,
            sum(IF(od.goods_type not in ({$this->zuan}),if(od.favorable_status=3,od.goods_price-od.favorable_price,od.goods_price),0)) as cp_amount
            from app_order.base_order_info as oi
            join app_order.app_order_details as od on oi.id=od.order_id
            join front.app_bespoke_info bs on oi.bespoke_id=bs.bespoke_id
            where oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0 ";

        if(!empty($where['begintime'])) {
            $sql .= " and oi.shipfreight_time >= '".$where['begintime']." 00:00:00'";
        }
        if(!empty($where['endtime'])) {
            $sql .= " and oi.shipfreight_time <= '".$where['endtime']." 23:59:59'";
        }
        if(!empty($where['department_id'])) {
            $sql .= " and oi.department_id =".$where['department_id'];
        }
        if (!empty($where['make_order'])) {
            if (is_array($where['make_order'])) {
                $str_names = '';
                foreach ($where['make_order'] as $name) {
                    $str_names .= "'{$name}',";
                }
                $str_names = rtrim($str_names, ',');
                $sql .= " and bs.make_order in (".$str_names.") ";
            } else {
                $sql .= " and bs.make_order='".$where['make_order']."' ";
            }
        }
        if (isset($where['is_zp'])) {
            $sql .= ' and oi.is_zp='.$where['is_zp'];
        }
        $sql .= " group by bs.make_order";
        return $this->db()->getAll($sql);
    }

    /**
    *    网销销售报表4：网销金额
    */
    public function getNetSaleShipStatDetail($where) {
        $sql=" select 
                bs.bespoke_sn,
                oi.order_sn,
                oi.shipfreight_time,
                bs.make_order,
                oi.create_user,
                sc.channel_name,
                cs.source_name,
                oi.consignee,
                oi.mobile,
                IF(od.goods_type in ({$this->zuan}),'裸钻','成品') goods_type,
                od.goods_id,
                od.goods_sn,
                od.zhengshuhao,
                if(od.favorable_status=3,od.goods_price-od.favorable_price,od.goods_price) as price
            from app_order.base_order_info as oi
            join app_order.app_order_details as od on oi.id=od.order_id
            join front.app_bespoke_info bs on oi.bespoke_id=bs.bespoke_id
            join cuteframe.sales_channels sc on sc.id = oi.department_id
            join cuteframe.customer_sources cs on cs.id = oi.customer_source_id

            where oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0 ";

        if(!empty($where['begintime'])) {
            $sql .= " and oi.shipfreight_time >= '".$where['begintime']." 00:00:00'";
        }
        if(!empty($where['endtime'])) {
            $sql .= " and oi.shipfreight_time <= '".$where['endtime']." 23:59:59'";
        }
        if(!empty($where['department_id'])) {
            $sql .= " and oi.department_id =".$where['department_id'];
        }
        if (!empty($where['make_order'])) {
            if (is_array($where['make_order'])) {
                $str_names = '';
                foreach ($where['make_order'] as $name) {
                    $str_names .= "'{$name}',";
                }
                $str_names = rtrim($str_names, ',');
                $sql .= " and bs.make_order in (".$str_names.") ";
            } else {
                $sql .= " and bs.make_order='".$where['make_order']."' ";
            }
        }
        if (isset($where['is_zp'])) {
            $sql .= ' and oi.is_zp='.$where['is_zp'];
        }
        $sql.=" order by oi.shipfreight_time desc";
        return $this->db()->getAll($sql);
    }

    // add by gengchao, 网销订单接待成单率1：到店预约数
    public function statNetInshopBesCount($where) {
        $sql = "select bs.accecipt_man,bs.make_order,count(bs.bespoke_id) as count from front.app_bespoke_info as bs
                where bs.is_delete=0 and EXISTS (
                  select 1 from cuteframe.sales_channels_person where CONCAT(',', dp_is_netsale, ',') like CONCAT('%,', bs.make_order, ',%') limit 1
                ) ";

        //实际到店的
        if(!empty($where['real_inshop_time_start'])) {
            $sql .="and bs.real_inshop_time >= '".$where['real_inshop_time_start']." 00:00:00' ";
        }
        if(!empty($where['real_inshop_time_end'])) {
            $sql .="and bs.real_inshop_time <= '".$where['real_inshop_time_end']." 23:59:59' ";
        }
        if(!empty($where['bespoke_status'])) {
            $sql .= "and bs.bespoke_status = ".$where['bespoke_status']." ";
        }
        if(!empty($where['re_status'])) {
            $sql .= "and bs.re_status = {$where['re_status']} ";
        }
        if(!empty($where['deal_status'])) {
            $sql .= "and bs.deal_status = {$where['deal_status']} ";
        }
        if(!empty($where['department_id'])) {
            $sql .= " and bs.department_id =".$where['department_id'];
        }
        //if(isset($where['make_order']) && !empty($where['make_order'])) {
            //$sql .= " and bs.make_order in ('".$where['make_order']."')";
        //}
        if(isset($where['create_user']) && !empty($where['create_user']))
        {
            if(is_array($where['create_user'])){
                $sql .= " and bs.make_order in('".implode("','", $where['create_user'])."') ";
            }else{
                $sql .= " and bs.make_order ='".$where['create_user']."' ";
            }
            
        }

        if(isset($where['make_order']) && !empty($where['make_order']))
        {
            if(is_array($where['make_order'])){
                $sql .= " and bs.make_order in('".implode("','", $where['make_order'])."') ";
            }else{
                $sql .= " and bs.make_order ='".$where['make_order']."' ";
            }
            
        }
        $sql .=" group by bs.accecipt_man";
        return $this->db()->getAll($sql);
    }
    // add by gengchao, 网销订单接待成单率1：到店预约数 明细
    public function statNetInshopBesCountDetail($where) {
        $sql = "SELECT bs.bespoke_sn, sc.channel_name, cs.source_name, bs.customer, bs.customer_mobile, bs.create_time,
            bs.bespoke_inshop_time, bs.real_inshop_time, bs.accecipt_man, bs.deal_status, bs.re_status, bs.queue_status, bs.withuserdo
            FROM front.app_bespoke_info AS bs
            join cuteframe.sales_channels sc on sc.id = bs.department_id
            join cuteframe.customer_sources cs on cs.id = bs.customer_source_id
            where bs.is_delete=0 and EXISTS (
              select 1 from cuteframe.sales_channels_person where CONCAT(',', dp_is_netsale, ',') like CONCAT('%,', bs.make_order, ',%') limit 1
            ) ";

        //实际到店的
        if(!empty($where['real_inshop_time_start'])) {
            $sql .="and bs.real_inshop_time >= '".$where['real_inshop_time_start']." 00:00:00' ";
        }
        if(!empty($where['real_inshop_time_end'])) {
            $sql .="and bs.real_inshop_time <= '".$where['real_inshop_time_end']." 23:59:59' ";
        }

        if(!empty($where['bespoke_status'])) {
            $sql .= "and bs.bespoke_status = '".$where['bespoke_status']."' ";
        }
        if(!empty($where['re_status'])) {
            $sql .= "and bs.re_status = ".$where['re_status']." ";
        }
        if(!empty($where['deal_status'])) {
            $sql .= "and bs.deal_status = ".$where['deal_status']." ";
        }
        if(!empty($where['department_id'])) {
            $sql .= " and bs.department_id =".$where['department_id'];
        }
        if (!empty($where['accecipt_man'])) {
            $sql .= " and bs.accecipt_man='".$where['accecipt_man']."' ";
        }
        if(!empty($where['make_order'])) {
            $sql .= " and bs.make_order in('".implode("','", $where['make_order'])."')";
        }
        $sql .=" order by bs.create_time desc ";
        return $this->db()->getAll($sql);
    }

    // add by gengchao, 网销订单接待成单率2：成交订单数
    public function statNetDealOrderCount($where) {
        $sql = " select bs.accecipt_man,count(1) as count
            from app_order.base_order_info as oi
            join front.app_bespoke_info bs on oi.bespoke_id=bs.bespoke_id
            where oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0
            and EXISTS (
              select 1 from cuteframe.sales_channels_person where CONCAT(',', dp_is_netsale, ',') like CONCAT('%,', bs.make_order, ',%') limit 1
            ) ";

        if (!empty($where['begintime'])) {
            $sql .= " and oi.pay_date >= '" . $where['begintime'] . " 00:00:00'";
        }
        if (!empty($where['endtime'])) {
            $sql .= " and oi.pay_date <= '" . $where['endtime'] . " 23:59:59'";
        }
        if (!empty($where['department_id'])) {
            $sql .= " and oi.department_id =" . $where['department_id'];
        }
        if (isset($where['is_zp'])) {
            $sql .= ' and oi.is_zp=' . $where['is_zp'];
        }
        $sql .= " group by bs.accecipt_man";
        return $this->db()->getAll($sql);
    }
    // add by gengchao, 网销订单接待成单率2：成交订单数
    public function statNetDealOrderCountDetail($where) {
        $sql="SELECT bs.bespoke_sn, oi.order_sn, oi.pay_date, bs.make_order, oi.create_user, sc.channel_name, cs.source_name, oi.consignee,
                oi.mobile, IF ( od.goods_type IN ({$this->zuan}), '裸钻', '成品' ) goods_type, od.goods_id, od.goods_sn, od.zhengshuhao,
                IF ( od.favorable_status = 3, od.goods_price - od.favorable_price, od.goods_price ) AS price
            FROM app_order.base_order_info AS oi
            join front.app_bespoke_info bs on oi.bespoke_id=bs.bespoke_id
            join app_order.app_order_details as od on oi.id=od.order_id
            join cuteframe.sales_channels sc on sc.id = oi.department_id
            join cuteframe.customer_sources cs on cs.id = oi.customer_source_id
            where oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0
            and EXISTS (
              select 1 from cuteframe.sales_channels_person where CONCAT(',', dp_is_netsale, ',') like CONCAT('%,', bs.make_order, ',%') limit 1
            ) ";

        if(!empty($where['begintime'])) {
            $sql .= " and oi.pay_date >= '".$where['begintime']." 00:00:00'";
        }
        if(!empty($where['endtime'])) {
            $sql .= " and oi.pay_date <= '".$where['endtime']." 23:59:59'";
        }
        if(!empty($where['department_id'])) {
            $sql .= " and oi.department_id =".$where['department_id'];
        }
        if (!empty($where['accecipt_man'])) {
            $sql .= " and bs.accecipt_man='".$where['accecipt_man']."' ";
        }
        if (isset($where['is_zp'])) {
            $sql .= ' and oi.is_zp='.$where['is_zp'];
        }
        return $this->db()->getAll($sql);
    }
    
    /**
     * 各店某时间内成品占比和转化率报表1：成品销售额占比
     * 成品占比（金额）=成品成交金额/订单总金额（成品+非成品）, 时间为订单第一次付款时间，排除赠品和优惠
     */
    public function getShopsCpSaleRate($where) {
        $sql=" select oi.department_id,
            sum(IF(od.goods_type not in ({$this->zuan}),if(od.favorable_status=3,od.goods_price-od.favorable_price,od.goods_price),0)) as cp_amount,
            sum(if(od.favorable_status=3,od.goods_price-od.favorable_price,od.goods_price)) as total_amount
            from app_order.base_order_info as oi
            join app_order.app_order_details as od on oi.id=od.order_id
            where oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0 and oi.is_zp=0 ";

        if(!empty($where['begintime'])) {
            $sql .= " and oi.pay_date >= '".$where['begintime']."'";
        }
        if(!empty($where['endtime'])) {
            $sql .= " and oi.pay_date < '".$where['endtime']."'";
        }
        $sql .= " group by oi.department_id";
        return $this->db()->getAll($sql);
    }

    /**
     * 各店某时间内成品占比和转化率报表2：预约单到店成交转化率
     * 转化率=预约单成交状态/已到店的预约单, 预约单实际到店时间
     */
    public function getShopsInshopRate($where) {
        $sql="select department_id,sum(if(deal_status=1,1,0)) deal_count, count(1) re_count from front.app_bespoke_info
            where is_delete=0 and bespoke_status<>3 and re_status=1";

        if(!empty($where['begintime'])) {
            $sql .= " and real_inshop_time >= '".$where['begintime']."'";
        }
        if(!empty($where['endtime'])) {
            $sql .= " and real_inshop_time < '".$where['endtime']."'";
        }
        $sql .= " group by department_id";
        return $this->db()->getAll($sql);
    }

    /**
     * 预约成交明细报表
     */
    public function getBespokeOrderDetails($where=array()) {
        $sql="select sc.channel_name,cs.source_name,bs.bespoke_sn,bs.make_order,
            oi.order_sn,oi.is_xianhuo,oi.order_status,oi.order_pay_status,oi.order_pay_type,
            oa.money_paid,oa.order_amount,oi.send_good_status,oi.buchan_status,oi.create_time,
            oi.create_user,oi.order_remark,od.goods_id,od.goods_sn,od.goods_name,od.is_stock_goods
            from front.app_bespoke_info bs join app_order.base_order_info oi on bs.bespoke_id=oi.bespoke_id
            join app_order.app_order_account oa on oa.order_id=oi.id
            join app_order.app_order_details od on od.order_id=oi.id
            join cuteframe.sales_channels sc on sc.id = bs.department_id
            join cuteframe.customer_sources cs on cs.id = bs.customer_source_id
            where bs.is_delete=0 and bs.bespoke_status<>3 and oi.is_delete=0";

        if(!empty($where['begintime'])) {
            $sql .= " and bs.create_time >= '".$where['begintime']." 00:00:00'";
        }
        if(!empty($where['endtime'])) {
            $sql .= " and bs.create_time <= '".$where['endtime']." 23:59:59'";
        }
        if(!empty($where['department_id'])) {
            $sql .= " and bs.department_id = ".$where['department_id'];
        }
        return $this->db()->getAll($sql);
    }

    //取部门销售顾问
    public function getDepUserInfo($dep_id)
    {
        # code...
        $sql = "SELECT * FROM `cuteframe`.`sales_channels_person` WHERE id = {$dep_id}";

        return $this->db()->getRow($sql);
    }

    //获取订单的统计信息
    public function getordercounts($where)
    {
        $sql="select
                oi.customer_source_id,cs.source_name,cs.fenlei,
                SUM(IF(oi.is_zp=0,1,0)) as ordernum,
                sum(oi.is_zp) as zpnum,
                sum(IF(oi.is_zp=0,oc.order_amount,0)) as orderamount,
                SUM(IF(oi.is_zp=0,oc.goods_amount - oc.favorable_price,0)) goodsamount,
                sum(IF(oi.is_zp=0,oc.money_paid,0)) as moneypaid,
                SUM(IF(oi.is_zp=0,oc.real_return_price,0)) realreturnprice,
                sum(IF(oi.is_zp=0,oc.money_unpaid,0)) as moneyunpaid,
                cs.source_own_id,cs.source_own
            from
                app_order.base_order_info as oi
                left join app_order.app_order_account as oc on oi.id=oc.order_id
                left join cuteframe.customer_sources as cs on oi.customer_source_id = cs.id
            where
                oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0";

        if( isset($where['department_id']) && $where['department_id'] > 0 )
        {
            $sql .= " and oi.department_id= ".$where['department_id'];
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

        //根据销售顾问导出

        if(isset($where['create_user']) && !empty($where['create_user']))
        {
            if(is_array($where['create_user'])){
                $sql .= " and oi.create_user in('".implode("','", $where['create_user'])."') ";
            }else{
                $sql .= " and oi.create_user ='".$where['create_user']."' ";
            }
            
        }

        if(isset($where['make_order']) && !empty($where['make_order']))
        {
            if(is_array($where['make_order'])){
                $sql .= " and oi.create_user in('".implode("','", $where['make_order'])."') ";
            }else{
                $sql .= " and oi.create_user ='".$where['make_order']."' ";
            }
            
        }
        
        $sql .= " group by oi.customer_source_id ";
        //echo $sql;die;
        return $this->db()->getAll($sql);
    }

    public function getFenLeiList($id)
    {
        $sql = "select `fenlei` from `cuteframe`.`customer_sources` where `id` = $id";
        return $this->db()->getOne($sql);
    }

    public function getCustomerSourcesList($where)
    {
        $sql = "select `id`,`fenlei` from `cuteframe`.`customer_sources` where `source_own_id` = ".$where['department_id'];

        if(isset($where['fenlei']) && $where['fenlei'] !="")
        {
            $sql .= " and fenlei = ".$where['fenlei']."";
        }
        return $this->db()->getAll($sql);
    }

    //获取预约的信息
    public function getbespokeinfo($where)
    {
        $sql = "select bi.create_time,bi.real_inshop_time,bi.bespoke_inshop_time,bi.department_id,bi.customer_mobile,cs.fenlei";
        $sql.= " from front.app_bespoke_info as bi ";
        $sql.= " LEFT JOIN cuteframe.sales_channels AS sa ON sa.id = bi.department_id";
        $sql.= " LEFT JOIN cuteframe.shop_cfg sc ON sc.id = sa.channel_own_id ";
        $sql.= " LEFT JOIN cuteframe.customer_sources AS cs ON cs.id = bi.customer_source_id";
        $sql.= " where bi.is_delete = 0 and bi.bespoke_status <> 3 and ";

        if(isset($where['re_status']) && $where['re_status']>0)
        {
            $sql .= "bi.re_status = '".$where['re_status']."' and ";
        }

        if(isset($where['deal_status']) && $where['deal_status']>0)
        {
            $sql .= "bi.deal_status = '".$where['deal_status']."' and ";
        }

        //创建时间
        if(isset($where['create_time_start']) && !empty($where['create_time_start']))
        {
            $sql .=" bi.create_time >= '".$where['create_time_start']." 00:00:00' and ";
        }
        if(isset($where['create_time_end']) && !empty($where['create_time_end']))
        {
            $sql .=" bi.create_time <= '".$where['create_time_end']." 23:59:59' and ";
        }
        
        //实际到店的
        if(isset($where['real_inshop_time_start']) && !empty($where['real_inshop_time_start']))
        {
            $sql .=" bi.real_inshop_time >= '".$where['real_inshop_time_start']." 00:00:00' and ";
        }
        if(isset($where['real_inshop_time_end']) && !empty($where['real_inshop_time_end']))
        {
            $sql .=" bi.real_inshop_time <= '".$where['real_inshop_time_end']." 23:59:59' and ";
        }
        
        //预约预计到店
        if(isset($where['bespoke_inshop_time_start']) && !empty($where['bespoke_inshop_time_start']))
        {
            $sql .=" bi.bespoke_inshop_time >= '".$where['bespoke_inshop_time_start']." 00:00:00' and ";
        }
        if(isset($where['bespoke_inshop_time_end']) && !empty($where['bespoke_inshop_time_end']))
        {
            $sql .=" bi.bespoke_inshop_time <= '".$where['bespoke_inshop_time_end']." 23:59:59' and ";
        }

        if(isset($where['shop_type']) && $where['shop_type'] !="")
        {
            $sql .= "sc.shop_type=".$where['shop_type']." and ";
        }
        
        if(isset($where['department_id']) && !empty($where['department_id']))
        {
            if(count($where['department_id'])>1){
                $sql .= "bi.department_id in(".implode(',', $where['department_id']).") and ";
            }else{
                $sql .= "bi.department_id='".$where['department_id'][0]."' and ";
            }
        }
        
        if(isset($where['fenlei']) && $where['fenlei'] !="")
        {
            $sql .= "cs.fenlei='".$where['fenlei']."' and ";
        }

        $sql .=" 1 ";
        //echo $sql;die;
        return $this->db()->getAll($sql);
    }

    //获取订单信息
    public function getorderinfo($where)
    {   
        $sql="select
                oi.department_id,oi.is_zp,oi.mobile,oi.id,oi.pay_date,cs.fenlei,oc.order_amount,(oc.goods_amount - oc.favorable_price) as goodsamount
            from
                app_order.base_order_info as oi
                inner join app_order.app_order_account as oc on oi.id = oc.order_id
                left join cuteframe.sales_channels AS sa ON sa.id = oi.department_id
                left join cuteframe.shop_cfg sc on sc.id = sa.channel_own_id
                left join cuteframe.customer_sources as cs on oi.customer_source_id = cs.id
            where
                oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0";

        if(isset($where['department_id']) && !empty($where['department_id']))
        {
            if(count($where['department_id'])>1){
                $sql .= " and oi.department_id in(".implode(',', $where['department_id']).")";
            }else{
                $sql .= " and oi.department_id='".$where['department_id'][0]."'";
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

        if(isset($where['shop_type']) && $where['shop_type'] !="")
        {
            $sql .= " and sc.shop_type=".$where['shop_type']."";
        }
        
        if(isset($where['fenlei']) && $where['fenlei'] !="")
        {
            $sql .= " and cs.fenlei='".$where['fenlei']."' ";
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
        //echo $sql;die;
        return $this->db()->getAll($sql);
    }
}
?>