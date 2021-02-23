<?php

/**
 *  -------------------------------------------------
 *   @file		: PurchaseIqcOpraModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: 
 *   @date		: 2015年9月16日 11:16:34
 *   @update	:
 *  -------------------------------------------------
 */
class PerformanceReportModel extends Model
{

    function __construct($id = NULL, $strConn = "")
    {
        $this->_objName = 'base_order_info';
        $this->pk = 'id';
        $this->_prefix = '';
        $this->_dataObject = array(
            "id" => " ",
            "order_sn" => "订单编号",
            "user_id" => "会员id",
            "consignee" => "名字",
            "mobile" => "手机号",
            "order_status" => "订单审核状态1无效2已审核3取消4关闭",
            "order_pay_status" => "支付状态:1未付款2部分付款3已付款",
            "order_pay_type" => "支付类型",
            "delivery_status" => "[参考数字字典：配送状态(sales.delivery_status)]",
            "send_good_status" => "1未发货2已发货3收货确认4允许发货5已到店",
            "buchan_status" => "布产状态:0未操作, 1 已布产,2 已出厂,8待审核",
            "customer_source_id" => "客户来源",
            "department_id" => "订单部门",
            "create_time" => "制单时间",
            "create_user" => "制单人",
            "recommended" => "推荐人",
            "check_time" => "审核时间",
            "check_user" => "审核人",
            "modify_time" => "修改时间",
            "order_remark" => "备注信息",
            "referer" => "录入来源",
            "is_delete" => "订单状态0有效1删除",
            "apply_close" => "申请关闭:0=未申请，1=申请关闭",
            "is_xianhuo" => "是否是现货：1现货 0定制 2未添加商品",
            "is_print_tihuo" => "是否打印提货单（数字字典confirm）",
            "is_zp" => "是否为赠品单1为不是2为是",
            "effect_date" => "订单生效时间(确定布产)"
        );
        parent::__construct($id, $strConn);
    }

    public function pageList($where)
    {
        $conditionSql = "";
        if ($where['is_zp'] != '') {
            $conditionSql .= " AND `a`.`is_zp` = " . $where['is_zp'] . " ";
        }
        if (! empty($where['start_time'])) {
            $conditionSql .= " AND `a`.`create_time` >= '" . $where['start_time'] . " 00:00:00'";
        }
        if (! empty($where['end_time'])) {
            $conditionSql .= " AND `a`.`create_time` <= '" . $where['end_time'] . " 23:59:59'";
        }
        if (! empty($where['referer'])) {
            if ($where['referer'] == '1') {
                $conditionSql .= " AND `a`.`referer` = '婚博会' ";
            } else {
                $conditionSql .= " AND `a`.`referer` != '婚博会' ";
            }
        }
        if (! empty($where['department_id']) && is_array($where['department_id'])) {
            $departmentSql = implode("','", $where['department_id']);
            $conditionSql .= "AND `a`.`department_id` IN ('{$departmentSql}')";
        }
        if (! empty($where['customer_source_id']) && is_array($where['customer_source_id'])) {
            $sourceSql = implode("','", $where['customer_source_id']);
            $conditionSql .= "AND `a`.`customer_source_id` IN ('{$sourceSql}')";
        }
        
        $sql = "
         SELECT *,
         concat(round(cp_count/goods_count*100,2),'%') AS cp_count_ratio,
         concat(round(lz_count/goods_count*100,2),'%') AS lz_count_ratio,
         concat(round(lz_amount/total_amount*100,2),'%') AS lz_amount_ratio,
         concat(round(cp_amount/total_amount*100,2),'%') AS cp_amount_ratio
         FROM 
        (
            SELECT 
            a.department_id ,
            c.channel_name,
            SUM(`b`.`goods_count`) AS goods_count,
            SUM(IF(b.goods_type = 'lz',0,`b`.`goods_count`)) AS cp_count,
            SUM(IF(b.goods_type = 'lz',`b`.`goods_count`,0)) AS lz_count,
            SUM(IF(b.favorable_status = 3,`b`.`goods_count`*(`b`.`goods_price`- `b`.`favorable_price`), `b`.`goods_count` * `b`.`goods_price`)) AS total_amount ,
            SUM(IF(b.goods_type = 'lz',IF(b.favorable_status  = 3,`b`.`goods_count`*(`b`.`goods_price`- `b`.`favorable_price`), `b`.`goods_count` * `b`.`goods_price`) ,0)) AS lz_amount,
            SUM(IF(b.goods_type = 'lz',0,IF(b.favorable_status  = 3,`b`.`goods_count`*(`b`.`goods_price`- `b`.`favorable_price`), `b`.`goods_count` * `b`.`goods_price`))) AS cp_amount
            FROM
            base_order_info AS a INNER JOIN `app_order_details` AS b ON`a`.`id`=`b`.`order_id` 
            LEFT JOIN cuteframe.sales_channels AS c ON c.id = a.department_id
            WHERE `a`.`order_status`=2 AND `a`.`order_pay_status` IN ('2','3','4') AND `a`.`is_delete`=0 AND `b`.`is_return`=0
            {$conditionSql}
            GROUP BY a.department_id 
        ) AS order_count_info
        LEFT JOIN 
        (
            SELECT 
            a.department_id,
            COUNT(DISTINCT(a.id)) AS order_num,
            SUM(b.order_amount) AS order_amount,
            SUM(b.money_paid) AS money_paid,
            SUM(b.money_unpaid) AS money_unpaid,
            SUM(b.real_return_price) AS real_return_price,
            SUM(IF(a.apply_return = 1 ,0,(SELECT g.apply_return_amount FROM app_return_goods g WHERE  g.order_id = a.id ORDER BY g.return_id desc limit 1))) AS on_return_price 
            FROM 
            `base_order_info` AS a INNER JOIN `app_order_account` AS b ON a.id = b.order_id
            LEFT JOIN cuteframe.sales_channels AS c ON a.department_id = c.id
            where `a`.`id`=`b`.`order_id` AND `a`.`order_status`=2 AND `a`.`order_pay_status` IN ('2','3','4') AND `a`.`is_delete`=0 
            {$conditionSql}
            GROUP BY a.department_id
        ) AS order_amount_info
        ON order_count_info.department_id = order_amount_info.department_id
         ";
        return $this->db()->getAll($sql);
    }
    /**
     * 退货商品金额
     * @param unknown $where
     * @return number
     */
    public function getRetrunGoodsAmount($where){
        $sql  = "SELECT
            d.favorable_status,d.goods_price,d.goods_count,d.favorable_price
        FROM
            `base_order_info` as a
            inner join `app_return_goods` rg on rg.order_sn = a.order_sn
            inner join `app_return_check` rc on rc.return_id = rg.return_id
            inner join `app_order_details` d on d.id = rg.order_goods_id
        WHERE
            `rg`.`order_goods_id`>0 ";
        if(!empty($where['department_id']))
        {
            if(is_array($where['department_id'])){
                $sql .= " AND `a`.`department_id` in (".implode(",",$where['department_id']).")";
            }else{
                $sql .= " AND `a`.`department_id` in (".$where['department_id'].")";
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
        if(!empty($where['from_ad']) || $where['from_ad'] === 0)
        {
            $sql .= " AND `a`.`customer_source_id` = ".$where['from_ad']." ";
        }
        if($where['is_zp']!='')
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
        //echo $sql;die;
        $data = $this->db()->getAll($sql);
        // print_r($data);
        $return = 0;
        foreach ($data as $vo){
            //判断优惠是否通过
            if($vo['favorable_status'] == 3)
            {
                //优惠通过(价格等于商品价格减去优惠价)
                $money = $vo['goods_count']*($vo['goods_price']-$vo['favorable_price']);
            }else{
                $money = $vo['goods_count']*$vo['goods_price'];
            }
            $return += $money;
        }
        unset($data);
        return $return;
    }

    /**
     * 退货商品金额
     * 获取满足条件的 退货商品金额
     * @param unknown $where
     * @return number
    **/
    public function getRetrunGoodsAmountA($where){
        $sql  = "
        SELECT
            d.favorable_status,d.goods_price,d.goods_count,d.favorable_price,a.customer_source_id
        FROM
            `base_order_info` as a
            inner join `app_return_goods` rg on rg.order_sn = a.order_sn
            inner join `app_return_check` rc on rc.return_id = rg.return_id
            inner join `app_order_details` d on d.id = rg.order_goods_id 
            left join `cuteframe`.`user` u on rg.apply_user_id = u.id
            left join `cuteframe`.`customer_sources` cs on cs.id = a.`customer_source_id`
        WHERE
            `rg`.`return_by`=1 and rc.deparment_finance_status= 1 ";
        if(!empty($where['department_id']))
        {
            if(is_array($where['department_id'])){
                $sql .= " AND `a`.`department_id` in (".implode(",",$where['department_id']).")";
            }else{
                $sql .= " AND `a`.`department_id` in (".$where['department_id'].")";
            }
        }
        /*根据退款申请的申请人统计[替换掉原来的按照订单的制单人统计]*/
        if(!empty($where['salseids']))
        {
            if(is_array($where['salseids'])){
                //$sql .= " AND `a`.`create_user` in ('".implode("','",$where['salse'])."')";
                $sql .= " AND `u`.`account` in ('".implode("','",$where['salseids'])."')";
            }else{
                //$sql .= " AND `a`.`create_user` = '".$where['salse']."'";
                $sql .= " AND `u`.`account` = '".$where['salseids']."'";
            }
        }
        if(isset($where['from_ad'])){
            if(!empty($where['from_ad']) || $where['from_ad'] === 0)
            {
                $sql .= " AND `a`.`customer_source_id` in( ".$where['from_ad'].") ";
            }
        }
        
        if(!empty($where['fenlei']))
        {
            $sql .= " AND `cs`.`fenlei` = '".$where['fenlei']."'";
        }
        if(isset($where['is_zp'])){
          if($where['is_zp']=='1' || $where['is_zp'] =='0')
            {
                $sql .= " AND `a`.`is_zp` = ".$where['is_zp']." ";
            }  
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
        $data = $this->db()->getAll($sql);
        $returngoodsamount = 0;
        foreach ($data as $vo){
            
            //判断优惠是否通过
            if($vo['favorable_status'] == 3)
            {
                if(isset($where['is_tree']) && $where['is_tree'] == 1 && ($vo['goods_price']-$vo['favorable_price']) <300){
                    continue;
                }
                //优惠通过(价格等于商品价格减去优惠价)
                $money = $vo['goods_count']*($vo['goods_price']-$vo['favorable_price']);
            }else{
                if(isset($where['is_tree']) && $where['is_tree'] == 1 && $vo['goods_price'] <300){
                    continue;
                }
                $money = $vo['goods_count']*$vo['goods_price'];
            }
            $returngoodsamount += $money;
        }
        unset($data);
        return $returngoodsamount;
    }

    public function getRetrunGoodsAmountGs($where){
        $sql  = "
        SELECT
            a.customer_source_id
        FROM
            `base_order_info` as a
            inner join `app_return_goods` rg on rg.order_sn = a.order_sn
            inner join `app_return_check` rc on rc.return_id = rg.return_id
            inner join `app_order_details` d on d.id = rg.order_goods_id 
            left join `cuteframe`.`user` u on rg.apply_user_id = u.id
            left join `cuteframe`.`customer_sources` cs on cs.id = a.`customer_source_id`
        WHERE
            `rg`.`return_by`=1 and rc.deparment_finance_status= 1 ";
        if(!empty($where['department_id']))
        {
            if(is_array($where['department_id'])){
                $sql .= " AND `a`.`department_id` in (".implode(",",$where['department_id']).")";
            }else{
                $sql .= " AND `a`.`department_id` in (".$where['department_id'].")";
            }
        }
        /*根据退款申请的申请人统计[替换掉原来的按照订单的制单人统计]*/
        if(!empty($where['salseids']))
        {
            if(is_array($where['salseids'])){
                //$sql .= " AND `a`.`create_user` in ('".implode("','",$where['salse'])."')";
                $sql .= " AND `u`.`account` in ('".implode("','",$where['salseids'])."')";
            }else{
                //$sql .= " AND `a`.`create_user` = '".$where['salse']."'";
                $sql .= " AND `u`.`account` = '".$where['salseids']."'";
            }
        }
        if(!empty($where['from_ad']) || $where['from_ad'] === 0)
        {
            $sql .= " AND `a`.`customer_source_id` in( ".$where['from_ad'].") ";
        }
        if(!empty($where['fenlei']))
        {
            $sql .= " AND `cs`.`fenlei` = '".$where['fenlei']."'";
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
        $data = $this->db()->getAll($sql);
        return $data;
    }

    /**
     * 退货商品金额 去除单件商品在300元以下的商品
     * 获取满足条件的 退货商品金额
     * @param unknown $where
     * @return number
    **/
    public function getRetrunGoodsAmountHG($where){
        $sql  = "
        SELECT
            d.favorable_status,d.goods_price,d.goods_count,d.favorable_price
        FROM
            `base_order_info` as a
            inner join `app_return_goods` rg on rg.order_sn = a.order_sn
            inner join `app_return_check` rc on rc.return_id = rg.return_id
            inner join `app_order_details` d on d.id = rg.order_goods_id 
            left join `cuteframe`.`user` u on rg.apply_user_id = u.id
        WHERE
            `rg`.`return_by`=1 and rc.deparment_finance_status= 1 ";
        if(!empty($where['department_id']))
        {
            if(is_array($where['department_id'])){
                $sql .= " AND `a`.`department_id` in (".implode(",",$where['department_id']).")";
            }else{
                $sql .= " AND `a`.`department_id` in (".$where['department_id'].")";
            }
        }
        /*根据退款申请的申请人统计[替换掉原来的按照订单的制单人统计]*/
        if(!empty($where['salseids']))
        {
            if(is_array($where['salseids'])){
                //$sql .= " AND `a`.`create_user` in ('".implode("','",$where['salse'])."')";
                $sql .= " AND `u`.`account` in ('".implode("','",$where['salseids'])."')";
            }else{
                //$sql .= " AND `a`.`create_user` = '".$where['salse']."'";
                $sql .= " AND `u`.`account` = '".$where['salseids']."'";
            }
        }
        if(!empty($where['from_ad']) || $where['from_ad'] === 0)
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
        $data = $this->db()->getAll($sql);
        $returngoodsamount = 0;
        foreach ($data as $vo){
            
            //判断优惠是否通过
            if($vo['favorable_status'] == 3)
            {
                if($where['is_tree'] == 1 && ($vo['goods_price']-$vo['favorable_price']) <300){
                    continue;
                }
                //优惠通过(价格等于商品价格减去优惠价)
                $money = $vo['goods_count']*($vo['goods_price']-$vo['favorable_price']);
            }else{
                if($where['is_tree'] == 1 && $vo['goods_price'] <300){
                    continue;
                }
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
    public function getReturnPriceA($where,$oids=array())
    {
        $sql  = "
        SELECT 
            SUM(`rg`.`real_return_amount`)
        FROM 
            `base_order_info` as a
            inner join `app_return_goods` rg on rg.order_sn = a.order_sn
            inner join `app_return_check` rc on rc.return_id = rg.return_id
            left join `cuteframe`.`customer_sources` cs on cs.id = a.`customer_source_id`
        WHERE 
            `rg`.`return_by`= 2 and rc.deparment_finance_status=1 ";
        if(!empty($where['department_id']))
        {
            if(is_array($where['department_id'])){
                $sql .= " AND `a`.`department_id` in (".implode(",",$where['department_id']).")";
            }else{
                $sql .= " AND `a`.`department_id` in (".$where['department_id'].")";
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
        if(isset($where['from_ad'])){
           if(!empty($where['from_ad']) || $where['from_ad'] === 0)
            {
                $sql .= " AND `a`.`customer_source_id` in( ".$where['from_ad'].") ";
            } 
        }
        
        if(!empty($where['fenlei']))
        {
            $sql .= " AND `cs`.`fenlei` = '".$where['fenlei']."'";
        }
        if(isset($where['is_zp'])){
            if($where['is_zp']=='1' || $where['is_zp']=='0')
            {
                $sql .= " AND `a`.`is_zp` = ".$where['is_zp']." ";
            }
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
        //echo $sql;die;
        $ret = $this->db()->getOne($sql);
        $ReturnPrice = $ret>0 ? $ret : 0 ;
        return $ReturnPrice;
    }

    public function getReturnPriceGs($where,$oids=array())
    {
        $sql  = "
        SELECT 
            a.customer_source_id
        FROM 
            `base_order_info` as a
            inner join `app_return_goods` rg on rg.order_sn = a.order_sn
            inner join `app_return_check` rc on rc.return_id = rg.return_id
            left join `cuteframe`.`customer_sources` cs on cs.id = a.`customer_source_id`
        WHERE 
            `rg`.`return_by`= 2 and rc.deparment_finance_status=1 ";
        if(!empty($where['department_id']))
        {
            if(is_array($where['department_id'])){
                $sql .= " AND `a`.`department_id` in (".implode(",",$where['department_id']).")";
            }else{
                $sql .= " AND `a`.`department_id` in (".$where['department_id'].")";
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
        if(!empty($where['from_ad']) || $where['from_ad'] === 0)
        {
            $sql .= " AND `a`.`customer_source_id` in( ".$where['from_ad'].") ";
        }
        if(!empty($where['fenlei']))
        {
            $sql .= " AND `cs`.`fenlei` = '".$where['fenlei']."'";
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
     * 退款不退货金额(order_goods_id = 0 意味着不退货)
     * 跟进筛选条件获取 实际退款的金额总和
     * @param unknown $where
     * @param oids   退了商品的订单明细自增id
     * @return number
    **/
    public function getReturnPriceHG($where,$oids=array())
    {
        $sql  = "
        SELECT 
            SUM(`rg`.`real_return_amount`)
        FROM 
            `base_order_info` as a
            inner join `app_return_goods` rg on rg.order_sn = a.order_sn
            inner join `app_return_check` rc on rc.return_id = rg.return_id
            left join `app_order_details` d on d.id = rg.order_goods_id 
            left join `cuteframe`.`user` u on rg.apply_user_id = u.id
            left join `cuteframe`.`customer_sources` cs on cs.id = a.`customer_source_id`
        WHERE 
            `rg`.`return_by`= 2 and rc.deparment_finance_status=1 AND IF (
                    d.favorable_status = 3,
                    d.goods_price - d.favorable_price,
                    d.goods_price
            ) >= 300 ";
        if(!empty($where['department_id']))
        {
            if(is_array($where['department_id'])){
                $sql .= " AND `a`.`department_id` in (".implode(",",$where['department_id']).")";
            }else{
                $sql .= " AND `a`.`department_id` in (".$where['department_id'].")";
            }
        }
        
        /*根据退款申请的申请人统计[替换掉原来的按照订单的制单人统计]*/
        if(!empty($where['salseids']))
        {
            if(is_array($where['salseids'])){
                //$sql .= " AND `a`.`create_user` in ('".implode("','",$where['salse'])."')";
                $sql .= " AND `u`.`account` in ('".implode("','",$where['salseids'])."')";
            }else{
                //$sql .= " AND `a`.`create_user` = '".$where['salse']."'";
                $sql .= " AND `u`.`account` = '".$where['salseids']."'";
            }
        }
        if(!empty($where['fenlei']))
        {
            $sql .= " AND `cs`.`fenlei` = '".$where['fenlei']."'";
        }
        if(isset($where['from_ad'])){
           if(!empty($where['from_ad']) || $where['from_ad'] === 0)
        {
            $sql .= " AND `a`.`customer_source_id` in( ".$where['from_ad'].") ";
        } 
        }
        if(isset($where['is_zp'])){
            if($where['is_zp']=='1' || $where['is_zp']=='0')
        {
            $sql .= " AND `a`.`is_zp` = ".$where['is_zp']." ";
        }
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
        //echo $sql;die;
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
        $sql  = "
        SELECT
             rg.order_goods_id  
        FROM
            `base_order_info` as a
            left join `app_return_goods` rg on rg.order_sn = a.order_sn
            left join `app_return_check` rc on rc.return_id = rg.return_id
            left join `app_order_details` d on d.id = rg.order_goods_id 
            left join `cuteframe`.`customer_sources` cs on cs.id = a.`customer_source_id`
        WHERE
            `rg`.`return_by` = $return_by ";
        if(!empty($where['department_id']))
        {
            if(is_array($where['department_id'])){
                $sql .= " AND `a`.`department_id` in (".implode(",",$where['department_id']).")";
            }else{
                $sql .= " AND `a`.`department_id` in (".$where['department_id'].")";
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
        if(!empty($where['fenlei']))
        {
            $sql .= " AND `cs`.`fenlei` = '".$where['fenlei']."'";
        }
        if(isset($where['from_ad'])){
            if(!empty($where['from_ad']) || $where['from_ad'] === 0)
                    {
                        $sql .= " AND `a`.`customer_source_id` in( ".$where['from_ad'].") ";
                    }
        }
        if(isset($where['is_zp'])){
            if($where['is_zp']=='1' || $where['is_zp'] =='0')
                    {
                        $sql .= " AND `a`.`is_zp` = ".$where['is_zp']." ";
                    }
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
        //echo $sql;die;
        $data = $this->db()->getAll($sql);
        return $data;
    }

    /**
     * 退货金额(return_by =1 退款方式 1退商品，2不退商品)
     * 根据筛选条件,获取出满足条件的订单明细自增id
     * @param unknown $where
     * @param oids   退了商品的订单明细自增id
     * @return array
    **/
    public function getRetrunGoodsOrderidHG($where,$return_by=1){
        $sql  = "
        SELECT
             rg.order_goods_id  
        FROM
            `base_order_info` as a
            left join `app_return_goods` rg on rg.order_sn = a.order_sn
            left join `app_return_check` rc on rc.return_id = rg.return_id
            left join `app_order_details` d on d.id = rg.order_goods_id 
            left join `cuteframe`.`customer_sources` cs on cs.id = a.`customer_source_id`
        WHERE
            `rg`.`return_by` = $return_by AND IF (
                    d.favorable_status = 3,
                    d.goods_price - d.favorable_price,
                    d.goods_price
            ) >= 300 ";
        if(!empty($where['department_id']))
        {
            if(is_array($where['department_id'])){
                $sql .= " AND `a`.`department_id` in (".implode(",",$where['department_id']).")";
            }else{
                $sql .= " AND `a`.`department_id` in (".$where['department_id'].")";
            }
        }
        if(!empty($where['fenlei']))
        {
            $sql .= " AND `cs`.`fenlei` = '".$where['fenlei']."'";
        }
        if(!empty($where['salse']))
        {
            if(is_array($where['salse'])){
                $sql .= " AND `a`.`create_user` in ('".implode("','",$where['salse'])."')";
            }else{
                $sql .= " AND `a`.`create_user` = '".$where['salse']."'";
            }
        }
        if(isset($where['from_ad'])){
            if(!empty($where['from_ad']) || $where['from_ad'] === 0)
        {
            $sql .= " AND `a`.`customer_source_id` in( ".$where['from_ad'].") ";
        }
        }
        if(isset($where['is_zp'])){
            if($where['is_zp']=='1' || $where['is_zp'] =='0')
        {
            $sql .= " AND `a`.`is_zp` = ".$where['is_zp']." ";
        }
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
    /**	pageLists
	 *
	 *	@url AppPerformanceCountController/search
	 */
	function pageAllList($where)
	{
		$sql  = "SELECT 
                department_id,
                COUNT(`a`.`id`) ordersum,
                SUM(`b`.`order_amount`) orderamount,
                SUM(`b`.`goods_amount` - `b`.`favorable_price`) ordergoodsamount ,
                SUM(`b`.`money_paid`) money_paid,
                SUM(`b`.`money_unpaid`) money_unpaid,
                SUM(`b`.`real_return_price`) real_return_price   
                ";
        
        $sql .=" FROM `".$this->table()."` as a inner join `app_order_account` as b on `a`.`id`=`b`.`order_id`";
		$sql .= " WHERE `a`.`order_status`=2 AND `a`.`order_pay_status` IN ('2','3','4') AND `a`.`is_delete`=0 ";
		if(!empty($where['department_id']))
		{
			if(is_array($where['department_id'])){
				$sql .= " AND `a`.`department_id` in (".implode(",",$where['department_id']).")";
			}else{
				$sql .= " AND `a`.`department_id` in (".$where['department_id'].")";
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
		if(!empty($where['from_ad']) || $where['from_ad'] === 0)
		{
			//$sql .= " AND `a`.`customer_source_id` = ".$where['from_ad']." ";
			if(is_array($where['from_ad'])){
				$sql .= " AND `a`.`customer_source_id` in ('".implode("','",$where['from_ad'])."')";
			}else{
				$sql .= " AND `a`.`customer_source_id` in ('".$where['from_ad']."')";
			}
		}
		if($where['is_zp']!='')
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
		$sql .= " GROUP BY `a`.`department_id` ";
		return $this->db()->getAll($sql);
	}


    /** pageLists
     *
     *  @url AppPerformanceCountController/search
     */
    function getOrderInfoBySn($order_sn)
    {
        $sql  = "SELECT 
                COUNT(`a`.`id`) ordersum,
                SUM(`b`.`order_amount`) orderamount,
                SUM(`b`.`goods_amount` - `b`.`favorable_price`) ordergoodsamount ,
                SUM(`b`.`money_paid`) money_paid,
                SUM(`b`.`money_unpaid`) money_unpaid,
                SUM(`b`.`real_return_price`) real_return_price   
                ";
        
        $sql .=" FROM `".$this->table()."` as a inner join `app_order_account` as b on `a`.`id`=`b`.`order_id`";
        $sql .= " WHERE `a`.`order_status`=2 AND `a`.`order_pay_status` IN ('2','3','4') AND `a`.`is_delete`=0 ";
        $sql .= " AND order_sn in('".implode("','", $order_sn)."')";
        return $this->db()->getRow($sql);
    }

    /**	pageLists
	 *
	 *	@url AppPerformanceCountController/search
	 *  @统计 商品
	 */
	function pageAllGoodsList($where)
	{
		$sql  = "SELECT 
            department_id,
            SUM(IF(`b`.`goods_type`='lz',1,0))         lz_count,
            SUM(IF(`b`.`goods_type`!='lz',1,0))        cp_count,
            SUM(IF(`b`.`goods_type`='lz',IF(`b`.`favorable_status`=3,`b`.`goods_price`-`b`.`favorable_price`,`b`.`goods_price`),0))         lz_sum_price,
            SUM(IF(`b`.`goods_type`!='lz',IF(`b`.`favorable_status`=3,`b`.`goods_price`-`b`.`favorable_price`,`b`.`goods_price`),0))         cp_sum_price
            
            ";
		$sql .=" FROM `".$this->table()."` as a inner join `app_order_details` as b on `a`.`id`=`b`.`order_id` ";
		$sql .= " where `a`.`order_status`=2 
        AND `a`.`order_pay_status` IN ('2','3','4') 
        AND `a`.`is_delete` = 0 
        ";
		if(!empty($where['department_id']))
		{
			if(is_array($where['department_id'])){
				$sql .= " AND `a`.`department_id` in (".implode(",",$where['department_id']).")";
			}else{
				$sql .= " AND `a`.`department_id` in (".$where['department_id'].")";
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
		if(!empty($where['from_ad']) || $where['from_ad'] === 0)
		{
			//$sql .= " AND `a`.`customer_source_id` = ".$where['from_ad']." ";
			if(is_array($where['from_ad'])){
				$sql .= " AND `a`.`customer_source_id` in ('".implode("','",$where['from_ad'])."')";
			}else{
				$sql .= " AND `a`.`customer_source_id` in ('".$where['from_ad']."')";
			}
		}
		if($where['is_zp']!='')
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
        
		$sql .= " GROUP BY `a`.`department_id` ";
        $ret = $this->db()->getAll($sql);
		return $ret;
	}

    function getReturnGoods($where)
    {
        $sql  = "SELECT 
            a.department_id,
            SUM(`rg`.`real_return_amount`) rg
        FROM 
            `base_order_info` as a
            inner join `app_return_goods` rg on rg.order_sn = a.order_sn
            inner join `app_return_check` rc on rc.return_id = rg.return_id
        WHERE 
            `rg`.`order_goods_id`>0
            ";
		if(!empty($where['department_id']))
		{
			if(is_array($where['department_id'])){
				$sql .= " AND `a`.`department_id` in (".implode(",",$where['department_id']).")";
			}else{
				$sql .= " AND `a`.`department_id` in (".$where['department_id'].")";
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
		if(!empty($where['from_ad']) || $where['from_ad'] === 0)
		{
			//$sql .= " AND `a`.`customer_source_id` = ".$where['from_ad']." ";
			if(is_array($where['from_ad'])){
				$sql .= " AND `a`.`customer_source_id` in ('".implode("','",$where['from_ad'])."')";
			}else{
				$sql .= " AND `a`.`customer_source_id` in ('".$where['from_ad']."')";
			}
		}
		if($where['is_zp']!='')
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
        $sql .= " group by a.department_id ";
        //echo $sql;die;
        $ret = $this->db()->getAll($sql);
		return $ret;
    }
	
    function getReturnPrice($where)
    {
        $sql  = "SELECT 
            a.department_id,
            SUM(`rg`.`real_return_amount`) rp
        FROM 
            `base_order_info` as a
            inner join `app_return_goods` rg on rg.order_sn = a.order_sn
            inner join `app_return_check` rc on rc.return_id = rg.return_id
        WHERE
            `rg`.`order_goods_id`=0
            ";
		if(!empty($where['department_id']))
		{
			if(is_array($where['department_id'])){
				$sql .= " AND `a`.`department_id` in (".implode(",",$where['department_id']).")";
			}else{
				$sql .= " AND `a`.`department_id` in (".$where['department_id'].")";
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
		if(!empty($where['from_ad']) || $where['from_ad'] === 0)
		{
			//$sql .= " AND `a`.`customer_source_id` = ".$where['from_ad']." ";
			if(is_array($where['from_ad'])){
				$sql .= " AND `a`.`customer_source_id` in ('".implode("','",$where['from_ad'])."')";
			}else{
				$sql .= " AND `a`.`customer_source_id` in ('".$where['from_ad']."')";
			}
		}
		if($where['is_zp']!='')
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
        $sql .= " group by a.department_id ";
        //echo $sql;die;
        $ret = $this->db()->getAll($sql);
		return $ret;
    }
	
	//获取当期订单发货商品总金额
	public function getSendgoodsPrice($where)
	{
		$sql  = "SELECT a.department_id,`a`.`id`,`b`.`favorable_price`,`b`.`favorable_status`,`b`.`goods_price`,`b`.`goods_type`,`b`.`goods_count` ,`b`.`is_return`";
		$sql .=" FROM `".$this->table()."` as a inner join `app_order_details` as b on ";
		$sql .= " `a`.`id`=`b`.`order_id` where `a`.`order_status`=2 AND `a`.`order_pay_status` IN ('2','3','4') AND `a`.`is_delete`=0 AND b.is_return=0 ";
		if(!empty($where['department_id']))
		{
			if(is_array($where['department_id'])){
				$sql .= " AND `a`.`department_id` in (".implode(",",$where['department_id']).")";
			}else{
				$sql .= " AND `a`.`department_id` in (".$where['department_id'].")";
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
		if(!empty($where['from_ad']) || $where['from_ad'] === 0)
		{
			//$sql .= " AND `a`.`customer_source_id` = ".$where['from_ad']." ";
			if(is_array($where['from_ad'])){
				$sql .= " AND `a`.`customer_source_id` in ('".implode("','",$where['from_ad'])."')";
			}else{
				$sql .= " AND `a`.`customer_source_id` in ('".$where['from_ad']."')";
			}
		}
		if($where['is_zp']!='')
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


}

