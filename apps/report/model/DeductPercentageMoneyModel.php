<?php
/**
 *  -------------------------------------------------
 *   @file		: DeductPercentageMoneyModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-09-29 11:50:09
 *   @update	:
 *  -------------------------------------------------
 */
class DeductPercentageMoneyModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'deduct_percentage_money';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"search_date"=>"日期",
"department_id"=>"渠道ID",
"department_name"=>"渠道名称",
"sales_name"=>"销售顾问",
"should_ticheng_price"=>"应发提成",
"baodi_price"=>"保底任务",
"real_add_price"=>"实际总新增",
"hbh_add_price"=>"婚博会新增金额   ",
"undiscount_add_price"=>"低于折扣下限新增核算金额",
"cp_add_price"=>"成品新增核算金额（非婚博会&高于折扣下限）",
"lzxy_add_price"=>"裸钻星耀新增核算金额（非婚博会&高于折扣下限）",
"lzfxy_add_price"=>"裸钻非星耀新增核算金额（非婚博会&高于折扣下限）",
"tejia_add_price"=>"特价商品新增金额",
"total_add_price"=>"总新增核算金额",
"real_return_price"=>"实际总转退",
"hbh_return_price"=>"婚博会转退金额",
"undiscount_return_price"=>"低于折扣下限转退核算金额",
"cp_return_price"=>"成品转退核算金额（非婚博会&高于折扣下限）",
"lzxy_return_price"=>"裸钻星耀转退核算金额（非婚博会&高于折扣下限）",
"lzfxy_return_price"=>"裸钻非星耀转退核算金额（非婚博会&高于折扣下限）",
"tejia_return_price"=>"特价商品转退金额",
"total_return_price"=>"总转退核算金额",
"real_deduct_price"=>"实际总新增扣除转退",
"hbh_deduct_price"=>"婚博会新增扣除转退金额",
"undiscount_deduct_price"=>"低于折扣下限核算新增扣除转退金额（非婚博会）",
"cp_deduct_price"=>"成品核算新增扣除转退金额（非婚博会&高于折扣下限）",
"lzxy_deduct_price"=>"裸钻星耀核算新增扣除转退金额（非婚博会&高于折扣下限）",
"lzfxy_deduct_price"=>"裸钻非星耀核算新增扣除转退金额（非婚博会&高于折扣下限）",
"tejia_deduct_price"=>"特价商品新增扣除转退金额",
"total_deduct_price"=>"总新增扣除转退核算金额  ",
"is_dabiao"=>"是否完成新增保底任务",
"bonus_gears"=>"新增完成业绩所属档级",
"dabiao_price"=>"达标新增奖",
"cp_shipments_price"=>"成品发货总金额",
"lzxy_shipments_price"=>"裸钻星耀发货总金额",
"lzfxy_shipments_price"=>"裸钻非星耀发货总金额",
"tejia_shipments_price"=>"特价商品发货总金额",
"shipments_total_price"=>"发货总金额",
"cp_jiti_price"=>"成品发货计提总金额",
"lzxy_jiti_price"=>"裸钻星耀发货计提总金额",
"lzfxy_jiti_price"=>"裸钻非星耀发货计提总金额",
"tejia_jiti_price"=>"特价商品发货计提总金额",
"jiti_total_price"=>"发货计提总金额",
"ticheng_factor"=>"档位提成系数",
"ticheng_price"=>"提成",
"tejia_ticheng_price"=>"特价商品提成",
"tsyd_award_price"=>"天生一对奖励",
"tsyd_punish_price"=>"天生一对惩罚",
"real_should_price"=>"实际应发",
"xy_award_price"=>"星耀奖励");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表  抓取条件内的所有订单
	 *
	 *	@url DeductPercentageMoneyController/search
	 */
	public function pageList ($where)
	{
		$sql = $this->getSql($where);
        //echo $sql;die;
		$data = $this->db()->getAll($sql);
		return $data;
	}

    /**
     * 普通查询
     * @param $type one 查询单个字段， row查询一条记录 all 查询多条记录
     */
    public function select2($fields = ' * ' , $where = " 1 " , $type = 'one'){
        $sql = "SELECT {$fields} FROM `".$this->table()."` WHERE {$where}";
        if($type == 'one'){
            $res = $this->db()->getOne($sql);
        }else if($type == 'row'){
            $res = $this->db()->getRow($sql);
        }else if($type == 'all'){
            $res = $this->db()->getAll($sql);
        }
        return $res;
    }

    //拼接sql
    public function getSql($where)
    {
        //不要用*,修改为具体字段
        $sql = "SELECT `oi`.`create_user`, `oi`.`referer`,`oi`.`department_id`,`sc`.`company_id`,od.favorable_price,od.favorable_status,od.xiangqian,od.goods_count,od.goods_type,od.cart,od.cert,od.zhengshuhao,od.goods_sn,od.is_return,od.goods_price,od.cpdzcode FROM `".$this->table()."` `oi` inner join app_order_details `od` on oi.id = od.order_id left join cuteframe.sales_channels sc on sc.id = oi.department_id";
        $str = '';
          //if($where['xxx'] != "")
          //{
              //$str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
          //}
        //if(!empty($where['create_user']))
        //{
        //    $str .= "`oi`.`create_user`='".$where['create_user']."' AND ";
        //}
        if(!empty($where['create_user']))
        {
            if(is_array($where['create_user'])){
                $sales_str = '';
                foreach ($where['create_user'] as $v) {
                    $sales_str .= "'".$v."',";
                }
                $sales_str = rtrim($sales_str,",");
                $str .= "`oi`.`create_user` in (".$sales_str.")  AND ";
            }else{
                $str .= "`oi`.`create_user` = '".$where['create_user']."' AND ";
            }
            //$sql .= " AND `oi`.`create_user` = '".$where['create_user']."'";
        }
        if(!empty($where['department_id']))
        {
            $str .= "`oi`.`department_id`='".$where['department_id']."' AND ";
        }
        if(!empty($where['pay_date_start']))
        {
            $str.="`oi`.`pay_date` >= '".$where['pay_date_start']." 00:00:00' AND ";
        }
        if(!empty($where['pay_date_end']))
        {
            $str.="`oi`.`pay_date` <= '".$where['pay_date_end']." 23:59:59' AND ";
        }
        if($str)
        {
            $str = rtrim($str,"AND ");//这个空格很重要
            $sql .=" WHERE ".$str;
        }
        //echo $sql;die;
        $sql .= " ORDER BY oi.`id` DESC";

        return $sql;
    }

    //为了统计获取所有天生一对的款
    public function getalltsydgoodssn()
    {
        $sql = "select DISTINCT style_sn from front.base_style_info where instr(xilie,',8,')>0";
        $ret = $this->db()->getAll($sql);
        return $ret;
    }

    /**
     * 退货商品金额
     * 获取满足条件的 退货商品金额
     * @param unknown $where
     * @return number
    **/
    public function getRetrunGoodsAmount($where){
        $sql  = "SELECT
            d.favorable_status,d.goods_price,d.goods_count,d.favorable_price
        FROM
            `base_order_info` as a
            inner join `app_return_goods` rg on rg.order_sn = a.order_sn
            inner join `app_return_check` rc on rc.return_id = rg.return_id
            inner join `app_order_details` d on d.id = rg.order_goods_id 
        WHERE
            `rg`.`return_by`=1 and rc.deparment_finance_status= 1 ";
        if(!empty($where['department_id']))
        {
            //if(is_array($where['department'])){
                //$sql .= " AND `a`.`department_id` in (".implode(",",$where['department']).")";
            //}else{
                //$sql .= " AND `a`.`department_id` in (".$where['department'].")";
            //}
            $sql .= " AND `a`.`department_id` = ".$where['department_id']."";
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
            //$sql .= " AND `rg`.`apply_user_id` = '".$where['salseids']."'";
        }
        /*if(!empty($where['from_ad']))
        {
            $sql .= " AND `a`.`customer_source_id` in( ".$where['from_ad'].") ";
        }
        if($where['is_zp']=='1' || $where['is_zp'] =='0')
        {
            $sql .= " AND `a`.`is_zp` = ".$where['is_zp']." ";
        }*/
        if(!empty($where['pay_date_start']))
        {
            $sql.=" AND `rc`.`deparment_finance_time` >= '".$where['pay_date_start']." 00:00:00'";
        }
        if(!empty($where['pay_date_end']))
        {
            $sql.=" AND `rc`.`deparment_finance_time` <= '".$where['pay_date_end']." 23:59:59'";
        }
        if(!empty($where['referer']))
        {
            if($where['referer'] == '1'){
                $sql .= " AND `a`.`referer` = '婚博会' ";
            }else{
                $sql .= " AND `a`.`referer` != '婚博会' ";
            }
        }
        /*if(isset($where['order_pay_type']) && $where['order_pay_type']>0 )
        {
            $sql .= " AND `a`.`order_pay_type` != '".$where['order_pay_type']."' ";
        }*/
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
        if(!empty($where['department_id']))
        {
            //if(is_array($where['department'])){
            //    $sql .= " AND `a`.`department_id` in (".implode(",",$where['department']).")";
            //}else{
            //    $sql .= " AND `a`.`department_id` in (".$where['department'].")";
            //}
            $sql .= " AND `a`.`department_id` = ".$where['department_id']."";
        }
        
        /*根据退款申请的申请人统计[替换掉原来的按照订单的制单人统计]*/
        if(!empty($where['salseids']))
        {
            if(is_array($where['salseids'])){
            //    //$sql .= " AND `a`.`create_user` in ('".implode("','",$where['salse'])."')";
                $sql .= " AND `rg`.`apply_user_id` in ('".implode("','",$where['salseids'])."')";
            }else{
            //    //$sql .= " AND `a`.`create_user` = '".$where['salse']."'";
                $sql .= " AND `rg`.`apply_user_id` = '".$where['salseids']."'";
            }
            //$sql .= " AND `rg`.`apply_user_id` = '".$where['salseids']."'";
        }
        //if(!empty($where['from_ad']))
        //{
            //$sql .= " AND `a`.`customer_source_id` in( ".$where['from_ad'].") ";
        //}
        //if($where['is_zp']=='1' || $where['is_zp']=='0')
        //{
            //$sql .= " AND `a`.`is_zp` = ".$where['is_zp']." ";
        //}
        if(!empty($where['pay_date_start']))
        {
            $sql.=" AND `rc`.`deparment_finance_time` >= '".$where['pay_date_start']." 00:00:00'";
        }
        if(!empty($where['pay_date_end']))
            {
            $sql.=" AND `rc`.`deparment_finance_time` <= '".$where['pay_date_end']." 23:59:59'";
        }
        //if(!empty($where['referer']))
        //{
            //if($where['referer'] == '1'){
            //    $sql .= " AND `a`.`referer` = '婚博会' ";
            //}else{
            //    $sql .= " AND `a`.`referer` != '婚博会' ";
            //}
        //}
        //if(isset($where['order_pay_type']) && $where['order_pay_type']>0 )
        //{
            //$sql .= " AND `a`.`order_pay_type` != '".$where['order_pay_type']."' ";
        //}
        if(!empty($oids)){
            $sql .= " AND `rg`.`order_goods_id` in('".implode("','",$oids)."') ";
        }
        //echo $sql;
        $ret = $this->db()->getOne($sql);
        $ReturnPrice = $ret>0 ? $ret : 0 ;
        return $ReturnPrice;
    }

    /**
     * 退货金额(return_by =1 退款方式 1退商品，2不退商品)
     * 根据筛选条件,获取出满足条件的订单明细自增id
     * @param unknown $where
     * @param oids   退了商品的订单明细自增id
     * @return array
    **/
    public function getRetrunGoodsOrderid($where,$return_by=1){
        $sql  = "SELECT
             rg.order_goods_id  
        FROM
            `base_order_info` as a
            left join `app_return_goods` rg on rg.order_sn = a.order_sn
            left join `app_return_check` rc on rc.return_id = rg.return_id
            left join `app_order_details` d on d.id = rg.order_goods_id 
        WHERE
            `rg`.`return_by` = $return_by ";
        if(!empty($where['department_id']))
        {
            //if(is_array($where['department'])){
            //    $sql .= " AND `a`.`department_id` in (".implode(",",$where['department']).")";
            //}else{
            //    $sql .= " AND `a`.`department_id` in (".$where['department'].")";
            //}
            $sql .= " AND `a`.`department_id` =".$where['department_id']."";
        }
        /*根据退款申请的申请人统计[替换掉原来的按照订单的制单人统计]*/
        if(!empty($where['salseids']))
        {
            if(is_array($where['salseids'])){
            //    //$sql .= " AND `a`.`create_user` in ('".implode("','",$where['salse'])."')";
                $sql .= " AND `rg`.`apply_user_id` in ('".implode("','",$where['salseids'])."')";
            }else{
            //    //$sql .= " AND `a`.`create_user` = '".$where['salse']."'";
                $sql .= " AND `rg`.`apply_user_id` = '".$where['salseids']."'";
            }
            //$sql .= " AND `rg`.`apply_user_id` = '".$where['salseids']."'";
        }
        /*if(!empty($where['salse']))
        {
            if(is_array($where['salse'])){
                $sql .= " AND `a`.`create_user` in ('".implode("','",$where['salse'])."')";
            }else{
                $sql .= " AND `a`.`create_user` = '".$where['salse']."'";
            }
        }*/
        //if(!empty($where['from_ad']))
        //{
        //    $sql .= " AND `a`.`customer_source_id` in( ".$where['from_ad'].") ";
        //}
        //if($where['is_zp']=='1' || $where['is_zp'] =='0')
        //{
        //    $sql .= " AND `a`.`is_zp` = ".$where['is_zp']." ";
        //}
        if(!empty($where['pay_date_start']))
        {
            $sql.=" AND `rc`.`deparment_finance_time` >= '".$where['pay_date_start']." 00:00:00'";
        }
        if(!empty($where['pay_date_end']))
        {
            $sql.=" AND `rc`.`deparment_finance_time` <= '".$where['pay_date_end']." 23:59:59'";
        }
        //if(!empty($where['referer']))
        //{
        //    if($where['referer'] == '1'){
        //        $sql .= " AND `a`.`referer` = '婚博会' ";
        //    }else{
        //        $sql .= " AND `a`.`referer` != '婚博会' ";
        //    }
        //}
        //if(isset($where['order_pay_type']) && $where['order_pay_type']>0 )
        //{
        //    $sql .= " AND `a`.`order_pay_type` != '".$where['order_pay_type']."' ";
        //}
        //echo $sql;
        $data = $this->db()->getAll($sql);
        return $data;
    }

    //根据订单明细id获取订单明细的详细信息 
    public function getDetailsbyid($oids=array(),$ng=1)
    {
        if(empty($oids))
        {
            return array();
        }
        $sql = "select us.account as create_user,a.xiangqian,`sc`.`company_id`,
        if(LENGTH(a.cert) >0,a.cert,g.zhengshuleibie) as cert,
        a.goods_count,a.goods_type,a.cart,a.zhengshuhao,a.goods_sn,a.is_return,a.favorable_status,a.cpdzcode,
        a.goods_price,a.favorable_price 
        from app_order_details as a 
        inner join base_order_info oi on oi.id = a.order_id
        inner join app_return_goods as b on a.id=b.order_goods_id  
        left join cuteframe.sales_channels sc on sc.id = oi.department_id
        left join cuteframe.user us on us.id = b.apply_user_id
        left join warehouse_shipping.warehouse_goods as g on a.goods_id=g.goods_id 
        where a.id in ('".implode("','",$oids)."') order by goods_type";
        if($ng==2)
        {
            $sql = " select us.account as create_user,a.xiangqian,`sc`.`company_id`,a.cpdzcode,
            if(LENGTH(a.cert) >0,a.cert,g.zhengshuleibie) as cert,
            1 as goods_count,a.goods_type,a.cart,a.zhengshuhao,a.goods_sn,
            0 as is_return,1 as favorable_status,b.real_return_amount as goods_price,0 as favorable_price 
            from app_order_details as a  
            inner join app_return_goods as b on a.id=b.order_goods_id  
            left join cuteframe.sales_channels sc on sc.id = b.department
            left join cuteframe.user us on us.id = b.apply_user_id
            left join warehouse_shipping.warehouse_goods as g on a.goods_id=g.goods_id 
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
        if(!empty($where['department_id']))
        {
            //if(is_array($where['department'])){
            //    $sql .= " AND `a`.`department_id` in (".implode(",",$where['department']).")";
            //}else{
            //    $sql .= " AND `a`.`department_id` in (".$where['department'].")";
            //}
            $sql .= " AND `a`.`department_id` =".$where['department_id']."";
        }
        /*根据退款申请的申请人统计[替换掉原来的按照订单的制单人统计]*/
        if(!empty($where['salseids']))
        {
            if(is_array($where['salseids'])){
            //    //$sql .= " AND `a`.`create_user` in ('".implode("','",$where['salse'])."')";
                $sql .= " AND `rg`.`apply_user_id` in ('".implode("','",$where['salseids'])."')";
            }else{
            //    //$sql .= " AND `a`.`create_user` = '".$where['salse']."'";
                $sql .= " AND `rg`.`apply_user_id` = '".$where['salseids']."'";
            }
            //$sql .= " AND `rg`.`apply_user_id` = '".$where['salseids']."'";
        }
        /*if(!empty($where['salse']))
        {
            if(is_array($where['salse'])){
                $sql .= " AND `a`.`create_user` in ('".implode("','",$where['salse'])."')";
            }else{
                $sql .= " AND `a`.`create_user` = '".$where['salse']."'";
            }
        }*/
        //if(!empty($where['from_ad']))
        //{
        //    $sql .= " AND `a`.`customer_source_id` in( ".$where['from_ad'].") ";
        //}
        //if($where['is_zp']=='1' || $where['is_zp']=='0')
        //{
        //    $sql .= " AND `a`.`is_zp` = ".$where['is_zp']." ";
        //}
        if(!empty($where['pay_date_start']))
        {
            $sql.=" AND `rc`.`deparment_finance_time` >= '".$where['pay_date_start']." 00:00:00'";
        }
        if(!empty($where['pay_date_end']))
            {
            $sql.=" AND `rc`.`deparment_finance_time` <= '".$where['pay_date_end']." 23:59:59'";
        }
        //if(!empty($where['referer']))
        //{
        //    if($where['referer'] == '1'){
        //        $sql .= " AND `a`.`referer` = '婚博会' ";
        //    }else{
        //        $sql .= " AND `a`.`referer` != '婚博会' ";
        //    }
        //}
        //if(isset($where['order_pay_type']) && $where['order_pay_type']>0 )
        //{
        //    $sql .= " AND `a`.`order_pay_type` != '".$where['order_pay_type']."' ";
        //}
        if(!empty($oids)){
            $sql .= " AND `rg`.`order_goods_id` in('".implode("','",$oids)."') ";
        }
        //echo $sql;
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
        //var_dump($where);die;
        $sql  = "SELECT `a`.`id`,`a`.`create_user`,`sc`.`company_id`,`b`.`favorable_price`,`b`.`favorable_status`,`b`.`goods_price`, `b`.`goods_price` as 'market_price', IF(`b`.`favorable_status` = 3,`b`.`goods_price`-`b`.`favorable_price`,`b`.`goods_price`) as 'discount_price',`b`.`goods_type`,`b`.`goods_count` ,`b`.`is_return`,`b`.`xiangqian`,`b`.`cart`,`b`.`cert`,`b`.`goods_sn`,`b`.`is_return`,`b`.`zhengshuhao`,b.is_cpdz,b.cpdzcode";
        $sql .=" FROM `".$this->table()."` as a inner join `app_order_details` as b on ";
        $sql .= " `a`.`id`=`b`.`order_id` left join cuteframe.sales_channels sc on sc.id = a.department_id where `a`.`order_status`=2 AND `a`.`order_pay_status` IN ('2','3','4') AND `a`.`is_delete`=0 AND b.is_return=0 ";
        if(!empty($where['department_id']))
        {
            //if(is_array($where['department_id'])){
            //    $sql .= " AND `a`.`department_id` in (".implode(",",$where['department_id']).")";
            //}else{
            //    $sql .= " AND `a`.`department_id` in (".$where['department_id'].")";
            //}
            $sql .= " AND `a`.`department_id` = ".$where['department_id']."";
        }
        if(!empty($where['create_user']))
        {
            if(is_array($where['create_user'])){
                $sales_str = '';
                foreach ($where['create_user'] as $v) {
                    # code...
                    $sales_str .= "'".$v."',";
                }
                $sales_str = rtrim($sales_str,",");
                $sql .= " AND `a`.`create_user` in (".$sales_str.")";
            }else{
                $sql .= " AND `a`.`create_user` = '".$where['create_user']."'";
            }
            //$sql .= " AND `a`.`create_user` = '".$where['create_user']."'";
        }
        //if(!empty($where['from_ad']))
        //{
        //    $sql .= " AND `a`.`customer_source_id` in( ".$where['from_ad'].") ";
        //}
        //if($where['is_zp']=='1' || $where['is_zp']=='0')
        //{
        //    $sql .= " AND `a`.`is_zp` = ".$where['is_zp']." ";
        //}
        if(!empty($where['pay_date_start']))
        {
            $sql.=" AND `a`.`shipfreight_time` >= '".$where['pay_date_start']." 00:00:00'";
        }
        if(!empty($where['pay_date_end']))
            {
            $sql.=" AND `a`.`shipfreight_time` <= '".$where['pay_date_end']." 23:59:59'";
        }
        //if(!empty($where['referer']))
        //{
        //    if($where['referer'] == '1'){
        //        $sql .= " AND `a`.`referer` = '婚博会' ";
        //    }else{
        //        $sql .= " AND `a`.`referer` != '婚博会' ";
        //    }
        //}
        //echo $sql;die;
        $ret = $this->db()->getAll($sql);
        return $ret;
    }

    //根据成品定制码获取款式销售渠道
    public function getChannelByCpdzcode($cpdzcode='')
    {
        $sql = "select style_channel_id from front.base_cpdz_code where code = '".$cpdzcode."'";
        return $this->db()->getOne($sql);
    }
    
}

?>