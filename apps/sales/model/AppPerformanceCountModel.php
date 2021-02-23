<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseOrderInfoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-13 21:02:11
 *   @update	:
 *  -------------------------------------------------
 */
class AppPerformanceCountModel extends Model
{
		function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'base_order_info';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
			"order_sn"=>"订单编号",
            "user_id"=>"会员id",
            "consignee"=>"名字",
            "mobile"=>"手机号",
            "order_status"=>"订单审核状态1无效2已审核3取消4关闭",
            "order_pay_status"=>"支付状态:1未付款2部分付款3已付款",
            "order_pay_type"=>"支付类型",
            "delivery_status"=>"[参考数字字典：配送状态(sales.delivery_status)]",
            "send_good_status"=>"1未发货2已发货3收货确认4允许发货5已到店",
            "buchan_status"=>"布产状态:0未操作, 1 已布产,2 已出厂,8待审核",
            "customer_source_id"=>"客户来源",
            "department_id"=>"订单部门",
            "create_time"=>"制单时间",
            "create_user"=>"制单人",
            "recommended"=>"推荐人",
            "check_time"=>"审核时间",
            "check_user"=>"审核人",
            "modify_time"=>"修改时间",
            "order_remark"=>"备注信息",
            "referer"=>"录入来源",
            "is_delete"=>"订单状态0有效1删除",
            "apply_close"=>"申请关闭:0=未申请，1=申请关闭",
            "is_xianhuo"=>"是否是现货：1现货 0定制 2未添加商品",
            "is_print_tihuo"=>"是否打印提货单（数字字典confirm）",
            "is_zp"=>"是否为赠品单1为不是2为是",
            "effect_date"=>"订单生效时间(确定布产)");
		parent::__construct($id,$strConn);
	}
    /**	pageList，分页列表
	 *
	 *	@url AppPerformanceCountController/search
	 */
	function pageList ($where)
	{

		$sql  = "SELECT `b`.`order_amount`,`b`.`money_paid`,`b`.`money_unpaid`,`b`.`goods_return_price`,`b`.`real_return_price`,`b`.`shipping_fee`,`b`.`goods_amount`,`b`.`coupon_price`,`b`.`favorable_price`,`b`.`card_fee`,`b`.`pack_fee`,`b`.`pay_fee`,`b`.`insure_fee`,`a`.* FROM `".$this->table()."` as a,`app_order_account` as b";

		$sql .= " where `a`.`id`=`b`.`order_id`";
		if(!empty($where['deparment']))
		{
			$sql .= " AND `a`.`department_id` in ('".$where['deparment']."')";
		}
		if(!empty($where['salse']))
		{
			$sql .= " AND `a`.`create_user` = '".$where['salse']."'";
		}
		if(!empty($where['from_ad']))
		{
			$sql .= " AND `a`.`customer_source_id` in ('".$where['from_ad']."')";
		}
		if($where['is_zp']=='1' ||$where['is_zp']=='0')
		{
			$sql .= " AND `a`.`is_zp` in ('".$where['is_zp']."')";
		}
		if(!empty($where['start_time']))
		{
			$sql.=" AND `a`.`create_time` >= '".$where['start_time']." 00:00:00'";
		}
        if(!empty($where['end_time']))
            {
            $sql.=" AND `a`.`create_time` <= '".$where['end_time']." 23:59:59'";
        }
		if(!empty($where['referer']))
		{
			$sql .= " AND `a`.`referer` in ('".addslashes($where['referer'])."')";
		}
		$sql .= " ORDER BY `a`.`id` DESC";

		$data = $this->db()->getAll($sql);
		return array('data'=>$data);
	}
    /**	pageLists
	 *
	 *	@url AppPerformanceCountController/search
	 */
	function pageAllList($where)
	{
		$sql  = "SELECT `a`.`id`,`a`.`order_sn`,`a`.`old_order_id`,`a`.`bespoke_id`,`a`.`old_bespoke_id`,`a`.`user_id`,`a`.`consignee`,`a`.`mobile`,`a`.`order_status`,`a`.`order_pay_status`,`a`.`order_pay_type`,`a`.`delivery_status`,`a`.`send_good_status`,`a`.`buchan_status`,`a`.`customer_source_id`,`a`.`department_id`,`a`.`create_time`,`a`.`create_user`,`a`.`check_time`,`a`.`check_user`,`a`.`genzong`,`a`.`recommended`,`a`.`modify_time`,`a`.`order_remark`,`a`.`referer`,`a`.`is_delete`,`a`.`apply_close`,`a`.`is_xianhuo`,`a`.`is_print_tihuo`,`a`.`effect_date`,`a`.`is_zp`,`a`.`pay_date`,`a`.`apply_return`,`b`.`order_amount`,`b`.`money_paid`,`b`.`money_unpaid`,`b`.`goods_return_price`,`b`.`real_return_price`,`b`.`shipping_fee`,`b`.`goods_amount`,`b`.`coupon_price`,`b`.`favorable_price`,`b`.`card_fee`,`b`.`pack_fee`,`b`.`pay_fee`,`b`.`insure_fee` ";
        $sql .=" FROM `".$this->table()."` as a inner join `app_order_account` as b on `a`.`id`=`b`.`order_id`";
		$sql .= " WHERE `a`.`order_status`=2 AND `a`.`order_pay_status` IN ('2','3','4') AND `a`.`is_delete`=0 ";
        
		if(!empty($where['salse']))
		{
            if(is_array($where['salse'])){
                $sales_str = '';
                foreach ($where['salse'] as $v) {
                    # code...
                    $sales_str .= "'".$v."',";
                }
                $sales_str = rtrim($sales_str,",");
                $sql .= " AND `a`.`create_user` in (".$sales_str.")";
            }else{
                $sql .= " AND `a`.`create_user` = '".$where['salse']."'";
            }
		}
		if(!empty($where['from_ad']))
		{
			$sql .= " AND `a`.`customer_source_id` = ".$where['from_ad']." ";
		}
		if($where['is_zp']=='1' || $where['is_zp']=='0')
		{
			$sql .= " AND `a`.`is_zp` = ".$where['is_zp']." ";
		}
		if(!empty($where['start_time']))
		{
			$sql.=" AND `a`.`pay_date` >= '".$where['start_time']." 00:00:00'";
		}
        if(!empty($where['end_time']))
            {
            $sql.=" AND `a`.`pay_date` <= '".$where['end_time']." 23:59:59'";
        }
        if(!empty($where['department']))
		{
			if(is_array($where['department'])){
				$sql .= " AND `a`.`department_id` in (".implode(",",$where['department']).")";
			}else{
				$sql .= " AND `a`.`department_id` in (".$where['department'].")";
			}
		}
        if(!empty($where['referer']))
		{
			if($where['referer'] == '1'){
				$sql .= " AND `a`.`referer` = '婚博会' ";
			}else{
				$sql .= " AND `a`.`referer` != '婚博会' ";
			}
		}
		$sql .= " ORDER BY `a`.`id` DESC";
		return $this->db()->getAll($sql);
	}

    
	
    /**
     * 退货申请金额
     * @param unknown $where
     * @return unknown
     */
    public function getReturnGoods($where)
    {
        $sql  = "SELECT 
            SUM(`rg`.`real_return_amount`)
        FROM 
            `base_order_info` as a
            inner join `app_return_goods` rg on rg.order_sn = a.order_sn
            inner join `app_return_check` rc on rc.return_id = rg.return_id
        WHERE 
            `rg`.`order_goods_id`>0 ";
		if(!empty($where['department']))
		{
			if(is_array($where['department'])){
				$sql .= " AND `a`.`department_id` in (".implode(",",$where['department']).")";
			}else{
				$sql .= " AND `a`.`department_id` in (".$where['department'].")";
			}
		}
		if(!empty($where['salse']))
		{
            if(is_array($where['salse'])){
                $sales_str = '';
                foreach ($where['salse'] as $v) {
                    # code...
                    $sales_str .= "'".$v."',";
                }
                $sales_str = rtrim($sales_str,",");
                $sql .= " AND `a`.`create_user` in (".$sales_str.")";
            }else{
                $sql .= " AND `a`.`create_user` = '".$where['salse']."'";
            }
		}
		if(!empty($where['from_ad']))
		{
			$sql .= " AND `a`.`customer_source_id` = ".$where['from_ad']." ";
		}
		if($where['is_zp'] =='1' || $where['is_zp'] =='0')
		{
			$sql .= " AND `a`.`is_zp` = ".$where['is_zp']." ";
		}
		if(!empty($where['start_time']))
		{
			$sql.=" AND `rc`.`deparment_finance_time` >= '".$where['start_time']." 00:00:00'";
		}
        if(!empty($where['end_time']))
            {
            $sql.=" AND `rc`.`deparment_finance_time` <= '".$where['end_time']." 23:59:59'";
        }
		if(!empty($where['referer']))
		{
			if($where['referer'] == '1'){
				$sql .= " AND `a`.`referer` = '婚博会' ";
			}else{
				$sql .= " AND `a`.`referer` != '婚博会' ";
			}
		}
        $ret = $this->db()->getOne($sql);
		return $ret;
    }
	
	
	
    
	
	

    /**	pageLists
	 *
	 *	@url AppPerformanceCountController/search
	 */
	function pageSaleList($where)
	{
		$sql  = "SELECT SUM( b.`shijia` ) 
FROM  `warehouse_shipping`.`warehouse_bill` AS  `b` ,  `app_order`.`base_order_info` AS  `a` 
WHERE  `a`.`order_sn` =  `b`.`order_sn` 
AND  `b`.bill_status =2
AND  `b`.bill_type =  'S' ";

		if(!empty($where['department']))
		{
			if(is_array($where['department'])){
				$sql .= " AND `a`.`department_id` in (".implode(",",$where['department']).")";
			}else{
				$sql .= " AND `a`.`department_id` in (".$where['department'].")";
			}
		}
		if(!empty($where['salse']))
		{
            if(is_array($where['salse'])){
                $sales_str = '';
                foreach ($where['salse'] as $v) {
                    # code...
                    $sales_str .= "'".$v."',";
                }
                $sales_str = rtrim($sales_str,",");
                $sql .= " AND `a`.`create_user` in (".$sales_str.")";
            }else{
                $sql .= " AND `a`.`create_user` = '".$where['salse']."'";
            }
		}
		if(!empty($where['from_ad']))
		{
			$sql .= " AND `a`.`customer_source_id` = ".$where['from_ad']." ";
		}
		if($where['is_zp']=='1' || $where['is_zp']=='0')
		{
			$sql .= " AND `a`.`is_zp` = ".$where['is_zp']." ";
		}
		if(!empty($where['start_time']))
		{
			$sql.=" AND `b`.`check_time` >= '".$where['start_time']." 00:00:00'";
		}
        if(!empty($where['end_time']))
            {
            $sql.=" AND `b`.`check_time` <= '".$where['end_time']." 23:59:59'";
        }
		if(!empty($where['referer']))
		{
			if($where['referer'] == '1'){
				$sql .= " AND `a`.`referer` = '婚博会' ";
			}else{
				$sql .= " AND `a`.`referer` != '婚博会' ";
			}
		}
		$sql .= " ORDER BY `a`.`id` DESC";
        //echo $sql;die;
		return $this->db()->getOne($sql);
	}

    /**	pageLists
	 *
	 *	@url AppPerformanceCountController/search
	 */
	function pageSaleReturnList($where)
	{
		$sql  = "SELECT SUM( b.`shijia` ) 
FROM  `warehouse_shipping`.`warehouse_bill` AS  `b` ,  `app_order`.`base_order_info` AS  `a` 
WHERE  `a`.`order_sn` =  `b`.`order_sn` 
AND  `b`.bill_status =2
AND  `b`.bill_type =  'D' ";

		if(!empty($where['department']))
		{
			if(is_array($where['department'])){
				$sql .= " AND `a`.`department_id` in (".implode(",",$where['department']).")";
			}else{
				$sql .= " AND `a`.`department_id` in (".$where['department'].")";
			}
		}
		if(!empty($where['salse']))
		{
            if(is_array($where['salse'])){
                $sales_str = '';
                foreach ($where['salse'] as $v) {
                    # code...
                    $sales_str .= "'".$v."',";
                }
                $sales_str = rtrim($sales_str,",");
                $sql .= " AND `a`.`create_user` in (".$sales_str.")";
            }else{
                $sql .= " AND `a`.`create_user` = '".$where['salse']."'";
            }
		}
		if(!empty($where['from_ad']))
		{
			$sql .= " AND `a`.`customer_source_id` = ".$where['from_ad']." ";
		}
		if($where['is_zp']=='1' || $where['is_zp']=='0')
		{
			$sql .= " AND `a`.`is_zp` = ".$where['is_zp']." ";
		}
		if(!empty($where['start_time']))
		{
			$sql.=" AND `b`.`check_time` >= '".$where['start_time']." 00:00:00'";
		}
        if(!empty($where['end_time']))
            {
            $sql.=" AND `b`.`check_time` <= '".$where['end_time']." 23:59:59'";
        }
		if(!empty($where['referer']))
		{
			if($where['referer'] == '1'){
				$sql .= " AND `a`.`referer` = '婚博会' ";
			}else{
				$sql .= " AND `a`.`referer` != '婚博会' ";
			}
		}
		$sql .= " ORDER BY `a`.`id` DESC";
        //echo $sql;die;
		return $this->db()->getOne($sql);
	}
    
    public function getAllRelativeList($where)
    {
		$sql  = "SELECT COUNT(customer_mobile) FROM  `front`.`app_bespoke_info` `a` ";
		$sql .= " WHERE `a`.`real_inshop_time` !=  '0000-00-00 00:00:00' ";
		if(!empty($where['department']))
		{
			if(is_array($where['department'])){
				$sql .= " AND `a`.`department_id` in (".implode(",",$where['department']).")";
			}else{
				$sql .= " AND `a`.`department_id` in (".$where['department'].")";
			}
		}
		if(!empty($where['salse']))
		{
			if(is_array($where['salse'])){
                $sales_str = '';
                foreach ($where['salse'] as $v) {
                    # code...
                    $sales_str .= "'".$v."',";
                }
                $sales_str = rtrim($sales_str,",");
                $sql .= " AND `a`.`accecipt_man` in (".$sales_str.")";
            }else{
                $sql .= " AND `a`.`accecipt_man` = '".$where['salse']."' ";
            }
		}
		if(!empty($where['from_ad']))
		{
			$sql .= " AND `a`.`customer_source_id` = ".$where['from_ad']." ";
		}
		if(!empty($where['start_time']))
		{
			$sql.=" AND `a`.`create_time` >= '".$where['start_time']." 00:00:00'";
		}
        if(!empty($where['end_time']))
            {
            $sql.=" AND `a`.`create_time` <= '".$where['end_time']." 23:59:59'";
        }
        //echo $sql;
		return $this->db()->getOne($sql);
	}

    //取用户
    public function getMember_Info_userId($user_id){
        if(!empty($user_id)){
            $keys[] ='member_id';
            $vals[] =$user_id;
        }else{
            return false;
        }

        $ret = ApiModel::sale_member_api($keys, $vals, 'GetMemberByMember_id');
        return $ret;
    }
	//add end
	
	
	
	
	
	
	
	/**
	 * 改版改造
	 * 获取满足条件的新增订单
	 *	@url AppPerformanceCountController/search
	**/
	function GetOrderList($where)
	{
		$sql  = "SELECT `a`.`id`,`a`.`order_sn`,`a`.`old_order_id`,`a`.`bespoke_id`,`a`.`old_bespoke_id`,`a`.`user_id`,`a`.`consignee`,`a`.`mobile`,`a`.`order_status`,`a`.`order_pay_status`,`a`.`order_pay_type`,`a`.`delivery_status`,`a`.`send_good_status`,`a`.`buchan_status`,`a`.`customer_source_id`,`a`.`department_id`,`a`.`create_time`,`a`.`create_user`,`a`.`check_time`,`a`.`check_user`,`a`.`genzong`,`a`.`recommended`,`a`.`modify_time`,`a`.`order_remark`,`a`.`referer`,`a`.`is_delete`,`a`.`apply_close`,`a`.`is_xianhuo`,`a`.`is_print_tihuo`,`a`.`effect_date`,`a`.`is_zp`,`a`.`pay_date`,`a`.`apply_return`,`b`.`order_amount`,`b`.`money_paid`,`b`.`money_unpaid`,`b`.`goods_return_price`,`b`.`real_return_price`,`b`.`shipping_fee`,`b`.`goods_amount`,`b`.`coupon_price`,`b`.`favorable_price`,`b`.`card_fee`,`b`.`pack_fee`,`b`.`pay_fee`,`b`.`insure_fee`,`c`.`source_name`,`c`.`fenlei` ";
        $sql .=" FROM `".$this->table()."` as a inner join `app_order_account` as b on `a`.`id`=`b`.`order_id`";
		$sql .=" left join cuteframe.`customer_sources` as c on `a`.`customer_source_id`=`c`.`id`";
		$sql .= " WHERE `a`.`order_status`=2 AND `a`.`order_pay_status` IN ('2','3','4') AND `a`.`is_delete`=0 ";
        
		if(!empty($where['salse']))
		{
            if(is_array($where['salse'])){
                $sql .= " AND `a`.`create_user` in ('".implode("','",$where['salse'])."')";
            }else{
                $sql .= " AND `a`.`create_user` = '".$where['salse']."'";
            }
		}
		if(!empty($where['from_ad']))
		{
			$sql .= " AND `a`.`customer_source_id` in( ".$where['from_ad'].") ";
		}
		if($where['is_zp']=='1' || $where['is_zp']=='0')
		{
			$sql .= " AND `a`.`is_zp` = ".$where['is_zp']." ";
		}
		if(!empty($where['start_time']))
		{
			$sql.=" AND `a`.`pay_date` >= '".$where['start_time']." 00:00:00'";
		}
        if(!empty($where['end_time']))
            {
            $sql.=" AND `a`.`pay_date` <= '".$where['end_time']." 23:59:59'";
        }
        if(!empty($where['department']))
		{
			if(is_array($where['department'])){
				$sql .= " AND `a`.`department_id` in (".implode(",",$where['department']).")";
			}else{
				$sql .= " AND `a`.`department_id` in (".$where['department'].")";
			}
		}
        if(!empty($where['referer']))
		{
			if($where['referer'] == '1'){
				$sql .= " AND `a`.`referer` = '婚博会' ";
			}else{
				$sql .= " AND `a`.`referer` != '婚博会' ";
			}
		}
		if(isset($where['order_pay_type']) && $where['order_pay_type']>0 )
		{
			$sql .= " AND `a`.`order_pay_type` != '".$where['order_pay_type']."' ";
		}
		$sql .= " ORDER BY `a`.`id` DESC";	
        echo $sql;die;	
		return $this->db()->getAll($sql);
	}

    /**
     * 改版改造
     * 获取满足条件的新增订单 不含换购
     *  @url AppPerformanceCountController/search
    **/
    function GetOrderListHuanGou($where)
    {
        $sql  = "select `a`.`mobile`,`a`.`pay_date`,a.is_zp ";
        $sql .=" FROM `".$this->table()."` as a ";
        $sql .=" inner join `app_order_details` as d on `a`.`id`=`d`.`order_id`";
        $sql .= " WHERE `a`.`order_status`=2 AND `a`.`order_pay_status` IN ('2','3','4') AND `a`.`is_delete`=0 AND IF (
                    d.favorable_status = 3,
                    d.goods_price - d.favorable_price,
                    d.goods_price
            ) >= 300 ";
        
        if(!empty($where['salse']))
        {
            if(is_array($where['salse'])){
                $sql .= " AND `a`.`create_user` in ('".implode("','",$where['salse'])."')";
            }else{
                $sql .= " AND `a`.`create_user` = '".$where['salse']."'";
            }
        }
        if(!empty($where['from_ad']))
        {
            $sql .= " AND `a`.`customer_source_id` in( ".$where['from_ad'].") ";
        }
        if($where['is_zp']=='1' || $where['is_zp']=='0')
        {
            $sql .= " AND `a`.`is_zp` = ".$where['is_zp']." ";
        }
        if(!empty($where['start_time']))
        {
            $sql.=" AND `a`.`pay_date` >= '".$where['start_time']." 00:00:00'";
        }
        if(!empty($where['end_time']))
            {
            $sql.=" AND `a`.`pay_date` <= '".$where['end_time']." 23:59:59'";
        }
        if(!empty($where['department']))
        {
            if(is_array($where['department'])){
                $sql .= " AND `a`.`department_id` in (".implode(",",$where['department']).")";
            }else{
                $sql .= " AND `a`.`department_id` in (".$where['department'].")";
            }
        }
        if(!empty($where['referer']))
        {
            if($where['referer'] == '1'){
                $sql .= " AND `a`.`referer` = '婚博会' ";
            }else{
                $sql .= " AND `a`.`referer` != '婚博会' ";
            }
        }
        if(isset($where['order_pay_type']) && $where['order_pay_type']>0 )
        {
            $sql .= " AND `a`.`order_pay_type` != '".$where['order_pay_type']."' ";
        }
        $sql .= " ORDER BY `a`.`id` DESC";
        return $this->db()->getAll($sql);
    }
	
	
	
	/**
	 * 退货商品金额
	 * 获取满足条件的 退货商品金额
	 * @param unknown $where
	 * @return number
	**/
    public function getRetrunGoodsAmount($where){
        $sql  = "
		SELECT
            d.favorable_status,d.goods_price,d.goods_count,d.favorable_price
        FROM
            `base_order_info` as a
            inner join `app_return_goods` rg on rg.order_sn = a.order_sn
            inner join `app_return_check` rc on rc.return_id = rg.return_id
            inner join `app_order_details` d on d.id = rg.order_goods_id 
        WHERE
            `rg`.`return_by`=1 and rc.deparment_finance_status= 1 ";
        if(!empty($where['department']))
        {
            if(is_array($where['department'])){
                $sql .= " AND `a`.`department_id` in (".implode(",",$where['department']).")";
            }else{
                $sql .= " AND `a`.`department_id` in (".$where['department'].")";
            }
        }
		/*根据退款申请的申请人统计[替换掉原来的按照订单的制单人统计]*/
		if(!empty($where['salseids']))
		{
            if(is_array($where['salseids'])){
                //$sql .= " AND `a`.`create_user` in ('".implode("','",$where['salse'])."')";
				$sql .= " AND `rg`.`apply_user_id` in ('".implode("','",$where['salseids'])."')";
            }else{
                //$sql .= " AND `a`.`create_user` = '".$where['salse']."'";
				$sql .= " AND `rg`.`apply_user_id` = '".$where['salseids']."'";
            }
		}
		if(!empty($where['from_ad']))
		{
			$sql .= " AND `a`.`customer_source_id` in( ".$where['from_ad'].") ";
		}
        if($where['is_zp']=='1' || $where['is_zp'] =='0')
        {
            $sql .= " AND `a`.`is_zp` = ".$where['is_zp']." ";
        }
        if(!empty($where['start_time']))
        {
            $sql.=" AND `rc`.`deparment_finance_time` >= '".$where['start_time']." 00:00:00'";
        }
        if(!empty($where['end_time']))
        {
            $sql.=" AND `rc`.`deparment_finance_time` <= '".$where['end_time']." 23:59:59'";
        }
		if(!empty($where['referer']))
		{
			if($where['referer'] == '1'){
				$sql .= " AND `a`.`referer` = '婚博会' ";
			}else{
				$sql .= " AND `a`.`referer` != '婚博会' ";
			}
		}
		if(isset($where['order_pay_type']) && $where['order_pay_type']>0 )
		{
			$sql .= " AND `a`.`order_pay_type` != '".$where['order_pay_type']."' ";
		}
		//echo $sql;//die();
        $data = $this->db()->getAll($sql);
        $returngoodsamount = 0;
        foreach ($data as $vo){
            //判断优惠是否通过
            if($vo['favorable_status'] == 3)
            {
                //优惠通过(价格等于商品价格减去优惠价)
                $money = $vo['goods_count']*($vo['goods_price']-$vo['favorable_price']);
            }else{
                $money = $vo['goods_count']*$vo['goods_price'];
            }
            $returngoodsamount += $money;
        }
        unset($data);
        return $returngoodsamount;
    }
	
	
	
	
	
	
	
	
	/**
	 * 退款不退货金额(order_goods_id = 0 意味着不退货)
	 * 跟进筛选条件获取 实际退款的金额总和
	 * @param unknown $where
	 * @param oids   退了商品的订单明细自增id
	 * @return number
	**/
	public function getReturnPrice($where,$oids=array())
    {
        $sql  = "
		SELECT 
            SUM(`rg`.`real_return_amount`)
        FROM 
            `base_order_info` as a
            inner join `app_return_goods` rg on rg.order_sn = a.order_sn
            inner join `app_return_check` rc on rc.return_id = rg.return_id
        WHERE 
            `rg`.`return_by`= 2 and rc.deparment_finance_status=1 ";
		if(!empty($where['department']))
		{
			if(is_array($where['department'])){
				$sql .= " AND `a`.`department_id` in (".implode(",",$where['department']).")";
			}else{
				$sql .= " AND `a`.`department_id` in (".$where['department'].")";
			}
		}
		
		/*根据退款申请的申请人统计[替换掉原来的按照订单的制单人统计]*/
		if(!empty($where['salseids']))
		{
            if(is_array($where['salseids'])){
                //$sql .= " AND `a`.`create_user` in ('".implode("','",$where['salse'])."')";
				$sql .= " AND `rg`.`apply_user_id` in ('".implode("','",$where['salseids'])."')";
            }else{
                //$sql .= " AND `a`.`create_user` = '".$where['salse']."'";
				$sql .= " AND `rg`.`apply_user_id` = '".$where['salseids']."'";
            }
		}
		if(!empty($where['from_ad']))
		{
			$sql .= " AND `a`.`customer_source_id` in( ".$where['from_ad'].") ";
		}
		if($where['is_zp']=='1' || $where['is_zp']=='0')
        {
            $sql .= " AND `a`.`is_zp` = ".$where['is_zp']." ";
        }
		if(!empty($where['start_time']))
		{
			$sql.=" AND `rc`.`deparment_finance_time` >= '".$where['start_time']." 00:00:00'";
		}
        if(!empty($where['end_time']))
            {
            $sql.=" AND `rc`.`deparment_finance_time` <= '".$where['end_time']." 23:59:59'";
        }
		if(!empty($where['referer']))
		{
			if($where['referer'] == '1'){
				$sql .= " AND `a`.`referer` = '婚博会' ";
			}else{
				$sql .= " AND `a`.`referer` != '婚博会' ";
			}
		}
		if(isset($where['order_pay_type']) && $where['order_pay_type']>0 )
		{
			$sql .= " AND `a`.`order_pay_type` != '".$where['order_pay_type']."' ";
		}
		if(!empty($oids)){
			$sql .= " AND `rg`.`order_goods_id` in('".implode("','",$oids)."') ";
		}
		//echo $sql;
        $ret = $this->db()->getOne($sql);
		$ReturnPrice = $ret>0 ? $ret : 0 ;
		return $ReturnPrice;
    }
	
	/**
	 * 订单商品统计
	 * 跟进筛选条件获取 订单商品明细
	 * 如果订单明细里面的证书类型为空,则再根据货号去仓库里面查询该货品的证书类型
	 * @param unknown $where
	 * @return number
	**/
	function pageAllGoodsList($where,$otherw=array())
	{
		$tt = 0;
		$sql  = "SELECT `a`.`id`,b.goods_sn,`b`.`id` as detailsid,`b`.`favorable_price`,
		`b`.`favorable_status`,`b`.`goods_price`,`b`.`goods_type`,`b`.`goods_count` ,
		`b`.`is_return`,`b`.`cart`,`b`.`xiangqian`,g.product_type1,`p`.`product_type_name`,
		if(LENGTH(b.cert) >0,b.cert,g.zhengshuleibie) as cert,`b`.`zhengshuhao`,a.mobile,
        a.pay_date,a.`is_zp` ";
		$sql .=" FROM `".$this->table()."` as a left join `app_order_details` as b on ";
		$sql .= " `a`.`id`=`b`.`order_id` ";
		$sql .=" left join `warehouse_shipping`.`warehouse_goods` as g on `b`.`goods_id`=`g`.`goods_id`
        left join front.base_style_info bi on bi.style_sn = b.goods_sn 
        left join front.`app_product_type` `p` on `p`.`product_type_id` = `bi`.`product_type` where `a`.`order_status`=2 
		AND `a`.`order_pay_status` IN ('2','3','4') AND `a`.`is_delete`=0 ";
		if(!empty($where['department']))
		{
			if(is_array($where['department'])){
				$sql .= " AND `a`.`department_id` in (".implode(",",$where['department']).")";
			}else{
				$sql .= " AND `a`.`department_id` in (".$where['department'].")";
			}
		}
		if(!empty($where['salse']))
		{
            if(is_array($where['salse'])){
                $sql .= " AND `a`.`create_user` in ('".implode("','",$where['salse'])."')";
            }else{
                $sql .= " AND `a`.`create_user` = '".$where['salse']."'";
            }
		}
		if(!empty($where['from_ad']))
		{
			$sql .= " AND `a`.`customer_source_id` in( ".$where['from_ad'].") ";
		}
		if($where['is_zp']=='1' || $where['is_zp'] =='0')
        {
            $sql .= " AND `a`.`is_zp` = ".$where['is_zp']." ";
        }
		if(!empty($where['start_time']))
		{
			$sql.=" AND `a`.`pay_date` >= '".$where['start_time']." 00:00:00'";
		}
        if(!empty($where['end_time']))
            {
            $sql.=" AND `a`.`pay_date` <= '".$where['end_time']." 23:59:59'";
        }
		if(!empty($where['referer']))
		{
			if($where['referer'] == '1'){
				$sql .= " AND `a`.`referer` = '婚博会' ";
			}else{
				$sql .= " AND `a`.`referer` != '婚博会' ";
			}
		}
		if(isset($where['order_pay_type']) && $where['order_pay_type']>0 )
		{
			$sql .= " AND `a`.`order_pay_type` != '".$where['order_pay_type']."' ";
		}
		
		
		if(!empty($otherw))
		{
			if(isset($otherw['xiangqian']) && !empty($otherw['xiangqian']))
			{
				$sql .= " AND `b`.`xiangqian` in('".implode("','",$otherw['xiangqian'])."') ";
			}
			if(isset($otherw['cert']) && !empty($otherw['cert']))
			{
				$sql .= " AND `b`.`cert` = '".$otherw['cert']."'";
			}
			if(isset($otherw['zhengshuhao']) && !empty($otherw['zhengshuhao'] ))
			{
				$sql .= " AND `b`.`zhengshuhao` = '".$otherw['zhengshuhao']."'";
			}
			if(isset($otherw['id']) && !empty($otherw['id'] ))
			{
				$sql .= " AND `b`.`id` != '".$otherw['id']."'";
			}
			if(isset($otherw['detailid']) && !empty($otherw['detailid']))
			{
				$tt = 1;
				$sql .= " AND `b`.`id` not in ('".implode("','",$otherw['detailid'])."') ";
			}
		}
		if($tt > 0 )
		{
			//
		}
		$sql .=" order by b.order_id,b.goods_type ";//echo $sql;
        $ret = $this->db()->getAll($sql);
		return $ret;
	}
	
	/**
	 * 当期发货商品总金额
	 * @param unknown $where
	 * @return number
	**/
	//获取当期订单发货商品总金额
	public function getSendgoodsPrice($where)
	{
		$sql  = "SELECT `a`.`id`,`b`.`favorable_price`,`b`.`favorable_status`,`b`.`goods_price`,`b`.`goods_type`,`b`.`goods_count` ,`b`.`is_return`,g.product_type1,`p`.`product_type_name`";
		$sql .=" FROM `".$this->table()."` as a inner join `app_order_details` as b on ";
		$sql .= " `a`.`id`=`b`.`order_id` LEFT JOIN `warehouse_shipping`.`warehouse_goods` AS g ON `b`.`goods_id` = `g`.`goods_id` left join front.base_style_info bi on bi.style_sn = b.goods_sn
LEFT join front.`app_product_type` `p` on `p`.`product_type_id` = `bi`.`product_type`  where `a`.`order_status`=2 AND `a`.`order_pay_status` IN ('2','3','4') AND `a`.`is_delete`=0 AND b.is_return=0 ";
		if(!empty($where['department']))
		{
			if(is_array($where['department'])){
				$sql .= " AND `a`.`department_id` in (".implode(",",$where['department']).")";
			}else{
				$sql .= " AND `a`.`department_id` in (".$where['department'].")";
			}
		}
		if(!empty($where['salse']))
		{
            if(is_array($where['salse'])){
                $sales_str = '';
                foreach ($where['salse'] as $v) {
                    # code...
                    $sales_str .= "'".$v."',";
                }
                $sales_str = rtrim($sales_str,",");
                $sql .= " AND `a`.`create_user` in (".$sales_str.")";
            }else{
                $sql .= " AND `a`.`create_user` = '".$where['salse']."'";
            }
		}
		if(!empty($where['from_ad']))
		{
			$sql .= " AND `a`.`customer_source_id` in( ".$where['from_ad'].") ";
		}
		if($where['is_zp']=='1' || $where['is_zp']=='0')
		{
			$sql .= " AND `a`.`is_zp` = ".$where['is_zp']." ";
		}
		if(!empty($where['start_time']))
		{
			$sql.=" AND `a`.`shipfreight_time` >= '".$where['start_time']." 00:00:00'";
		}
        if(!empty($where['end_time']))
            {
            $sql.=" AND `a`.`shipfreight_time` <= '".$where['end_time']." 23:59:59'";
        }
		if(!empty($where['referer']))
		{
			if($where['referer'] == '1'){
				$sql .= " AND `a`.`referer` = '婚博会' ";
			}else{
				$sql .= " AND `a`.`referer` != '婚博会' ";
			}
		}
        $ret = $this->db()->getAll($sql);
		return $ret;
	}
	
	//为了统计获取所有天生一对的款
	public function getalltsydgoodssn()
	{
		$sql = "select DISTINCT style_sn from front.base_style_info where instr(xilie,',8,')>0";
		$ret = $this->db()->getAll($sql);
		return $ret;
	}
	
	//根据订单明细id获取订单明细的详细信息 
	public function getDetailsbyid($oids=array(),$ng=1)
	{
		if(empty($oids))
		{
			return array();
		}
		$sql = "select a.xiangqian,
		if(LENGTH(a.cert) >0,a.cert,g.zhengshuleibie) as cert,
		a.goods_count,a.goods_type,a.cart,a.zhengshuhao,a.goods_sn,a.is_return,a.favorable_status,
		a.goods_price,a.favorable_price,g.product_type1,`p`.`product_type_name`
		from app_order_details as a 
		left join warehouse_shipping.warehouse_goods as g on a.goods_id=g.goods_id 
        left join front.base_style_info bi on bi.style_sn = a.goods_sn
        LEFT join front.`app_product_type` `p` on `p`.`product_type_id` = `bi`.`product_type` 
		where a.id in ('".implode("','",$oids)."') order by goods_type";
		if($ng==2)
		{
			$sql = " select a.xiangqian,
			if(LENGTH(a.cert) >0,a.cert,g.zhengshuleibie) as cert,
			1 as goods_count,a.goods_type,a.cart,a.zhengshuhao,a.goods_sn,
			0 as is_return,1 as favorable_status,b.real_return_amount as goods_price,0 as favorable_price,g.product_type1,`p`.`product_type_name`  
			from app_order_details as a  
			inner join app_return_goods as b on a.id=b.order_goods_id  
			left join warehouse_shipping.warehouse_goods as g on a.goods_id=g.goods_id 
            left join front.base_style_info bi on bi.style_sn = a.goods_sn
            LEFT join front.`app_product_type` `p` on `p`.`product_type_id` = `bi`.`product_type` 
			where  b.return_by=2 and b.check_status in(4,5) and  
			a.id in ('".implode("','",$oids)."') order  by a.goods_type";
			//echo $sql;die();
		}
		$ret = $this->db()->getAll($sql);
		return $ret;
	}
	
	
	/**
	 * 退款不退货金额(return_by =2 退款方式 1退商品，2不退商品)
	 * 根据筛选条件,获取出满足条件的订单明细自增id
	 * @param unknown $where
	 * @param oids   退了商品的订单明细自增id
	 * @return number
	**/
	public function getNogoodsReturoids($where,$oids)
    {
        $sql  = "
		SELECT 
            rg.order_goods_id 
        FROM 
            `app_return_goods` as rg
            inner join `base_order_info` a on rg.order_sn = a.order_sn
            inner join `app_return_check` rc on rc.return_id = rg.return_id
        WHERE 
            `rg`.`return_by`= 2 ";
		if(!empty($where['department']))
		{
			if(is_array($where['department'])){
				$sql .= " AND `a`.`department_id` in (".implode(",",$where['department']).")";
			}else{
				$sql .= " AND `a`.`department_id` in (".$where['department'].")";
			}
		}
        /*根据退款申请的申请人统计[替换掉原来的按照订单的制单人统计]*/
        if(!empty($where['salseids']))
        {
            if(is_array($where['salseids'])){
                //$sql .= " AND `a`.`create_user` in ('".implode("','",$where['salse'])."')";
                $sql .= " AND `rg`.`apply_user_id` in ('".implode("','",$where['salseids'])."')";
            }else{
                //$sql .= " AND `a`.`create_user` = '".$where['salse']."'";
                $sql .= " AND `rg`.`apply_user_id` = '".$where['salseids']."'";
            }
        }
		/*if(!empty($where['salse']))
		{
            if(is_array($where['salse'])){
                $sql .= " AND `a`.`create_user` in ('".implode("','",$where['salse'])."')";
            }else{
                $sql .= " AND `a`.`create_user` = '".$where['salse']."'";
            }
		}*/
		if(!empty($where['from_ad']))
		{
			$sql .= " AND `a`.`customer_source_id` in( ".$where['from_ad'].") ";
		}
		if($where['is_zp']=='1' || $where['is_zp']=='0')
        {
            $sql .= " AND `a`.`is_zp` = ".$where['is_zp']." ";
        }
		if(!empty($where['start_time']))
		{
			$sql.=" AND `rc`.`deparment_finance_time` >= '".$where['start_time']." 00:00:00'";
		}
        if(!empty($where['end_time']))
            {
            $sql.=" AND `rc`.`deparment_finance_time` <= '".$where['end_time']." 23:59:59'";
        }
		if(!empty($where['referer']))
		{
			if($where['referer'] == '1'){
				$sql .= " AND `a`.`referer` = '婚博会' ";
			}else{
				$sql .= " AND `a`.`referer` != '婚博会' ";
			}
		}
		if(isset($where['order_pay_type']) && $where['order_pay_type']>0 )
		{
			$sql .= " AND `a`.`order_pay_type` != '".$where['order_pay_type']."' ";
		}
		if(!empty($oids)){
			$sql .= " AND `rg`.`order_goods_id` in('".implode("','",$oids)."') ";
		}
		//echo $sql;
        $ret = $this->db()->getAll($sql);
		return $ret;
    }
	
	/**
	 * 退货金额(return_by =1 退款方式 1退商品，2不退商品)
	 * 根据筛选条件,获取出满足条件的订单明细自增id
	 * @param unknown $where
	 * @param oids   退了商品的订单明细自增id
	 * @return array
	**/
    public function getRetrunGoodsOrderid($where,$return_by=1){
        $sql  = "
		SELECT
             rg.order_goods_id  
        FROM
            `base_order_info` as a
            left join `app_return_goods` rg on rg.order_sn = a.order_sn
            left join `app_return_check` rc on rc.return_id = rg.return_id
            left join `app_order_details` d on d.id = rg.order_goods_id 
        WHERE
            `rg`.`return_by` = $return_by ";
        if(!empty($where['department']))
        {
            if(is_array($where['department'])){
                $sql .= " AND `a`.`department_id` in (".implode(",",$where['department']).")";
            }else{
                $sql .= " AND `a`.`department_id` in (".$where['department'].")";
            }
        }
        /*根据退款申请的申请人统计[替换掉原来的按照订单的制单人统计]*/
        if(!empty($where['salseids']))
        {
            if(is_array($where['salseids'])){
                //$sql .= " AND `a`.`create_user` in ('".implode("','",$where['salse'])."')";
                $sql .= " AND `rg`.`apply_user_id` in ('".implode("','",$where['salseids'])."')";
            }else{
                //$sql .= " AND `a`.`create_user` = '".$where['salse']."'";
                $sql .= " AND `rg`.`apply_user_id` = '".$where['salseids']."'";
            }
        }
		/*if(!empty($where['salse']))
		{
            if(is_array($where['salse'])){
                $sql .= " AND `a`.`create_user` in ('".implode("','",$where['salse'])."')";
            }else{
                $sql .= " AND `a`.`create_user` = '".$where['salse']."'";
            }
		}*/
		if(!empty($where['from_ad']))
		{
			$sql .= " AND `a`.`customer_source_id` in( ".$where['from_ad'].") ";
		}
        if($where['is_zp']=='1' || $where['is_zp'] =='0')
        {
            $sql .= " AND `a`.`is_zp` = ".$where['is_zp']." ";
        }
        if(!empty($where['start_time']))
        {
            $sql.=" AND `rc`.`deparment_finance_time` >= '".$where['start_time']." 00:00:00'";
        }
        if(!empty($where['end_time']))
        {
            $sql.=" AND `rc`.`deparment_finance_time` <= '".$where['end_time']." 23:59:59'";
        }
		if(!empty($where['referer']))
		{
			if($where['referer'] == '1'){
				$sql .= " AND `a`.`referer` = '婚博会' ";
			}else{
				$sql .= " AND `a`.`referer` != '婚博会' ";
			}
		}
		if(isset($where['order_pay_type']) && $where['order_pay_type']>0 )
		{
			$sql .= " AND `a`.`order_pay_type` != '".$where['order_pay_type']."' ";
		}
		//echo $sql;
        $data = $this->db()->getAll($sql);
        return $data;
    }
	

    //验证款号是否天生一对特殊款
	public function getTsydSpecial($style_sn)
    {
        if(empty($style_sn)) return false;
        $sql = "select * from front.app_tsyd_special where style_sn = '".$style_sn."' and status = 1 order by id desc limit 1";
        $res = $this->db()->getRow($sql);
        if($res){
            return true;
        }
        return false;
    }

    //获取天生一对价格
    public function getTsydDiaPrice($zhengshuhao)
    {
        $sql = "select (`od`.`goods_price` - if(`od`.`favorable_price`,`od`.`favorable_price`,0.00)) as price from app_order.base_order_info oi inner join app_order.app_order_details od on oi.id = od.order_id where od.zhengshuhao = '".$zhengshuhao."' and cert <> 'HRD-D' and goods_type = 'lz' and oi.order_status = 2 and `oi`.`is_delete` <> 1 order by od.id desc limit 1";
        return $this->db()->getOne($sql);
    }
}


?>
