<?php

/**
 * 销售模块的数据模型（代替Sales/Api/api.php）
 *  -------------------------------------------------
 *   @file		: SaleModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class SelfWarehouseModel extends SelfModel {

    protected $db;

    function __construct($strConn = "") {
        $this->db = DB::cn($strConn);
    }

    public function db() {
        return $this->db;
    }

    /*
     * 加工商结算
     */
    
    public function GetProcessorInAccount($w)
    {
    
    	$type = $w['type'];
    	$where = " where 1 ";
    	if (isset($w['company']) && !empty($w['company']))
    	{
    		$where .= " and wb.to_company_id=" . $w['company'];
    	}
    
    	if (isset($w['fin_status']) && !empty($w['fin_status']))
    	{
    		$where .= " and wb.fin_check_status=" . $w['fin_status'];
    	}
    	if (isset($w['bill_status']) && !empty($w['bill_status']))
    	{
    		$where .= " and wb.bill_status=" . $w['bill_status'];
    	}
    
    	if (isset($w['put_in_type']) && !empty($w['put_in_type']))
    	{
    		$where .= " and wb.put_in_type=" . $w['put_in_type'];
    	}
    
    
    	//加工商出库结算定位搜索条件 附表b或c
    	if (isset($w['bill_type_info']) && !empty($w['bill_type_info']))
    	{
    		$where .= " and wb.bill_type='" . $w['bill_type_info']."'";
    	}
    	if (isset($w['pay_channel']) && !empty($w['pay_channel']))
    	{
    		$where .= " and wb.pro_id=" . $w['pay_channel'];
    	}
    	if ($type == "in")
    	{
    		//判断是否结价
    		if (isset($w['account_type']) && in_array($w['account_type'], array('0', '1'),true))
    		{
    			$where .= " and wb.jiejia=" . $w['account_type'];
    		}
    	}
    	if (isset($w['make_time_start']) && !empty($w['make_time_start']))
    	{
    		$where .= " and wb.create_time>='" . $w['make_time_start'] . " 00:00:00'";
    	}
    	if (isset($w['make_time_end']) && !empty($w['make_time_end']))
    	{
    		$where .= " and wb.create_time<='" . $w['make_time_end'] . " 23:59:59' ";
    	}
    	if (isset($w['check_time_start']) && !empty($w['check_time_start']))
    	{
    		$where .= " and wb.check_time>='" . $w['check_time_start'] . " 00:00:00'";
    	}
    	if (isset($w['check_time_end']) && !empty($w['check_time_end']))
    	{
    		$where .= " and wb.check_time<='" . $w['check_time_end'] . " 23:59:59' ";
    	}
    	if (isset($w['fin_check_time_start']) && !empty($w['fin_check_time_start']))
    	{
    		$where .= " and wb.fin_check_time>='" . $w['fin_check_time_start'] . " 00:00:00' ";
    	}
    	if (isset($w['fin_check_time_end']) && !empty($w['fin_check_time_end']))
    	{
    		$where .= " and wb.fin_check_time<='" . $w['fin_check_time_end'] . " 23:59:59' ";
    	}
    
    	$page = isset($w["page"]) ? intval($w["page"]) : 1;
    
    	if ($type == "in")
    	{
    
    		$sql = "select wb.id,wb.put_in_type,wb.fin_check_status,wb.bill_type,wb.bill_note,wb.bill_no,wb.goods_num,wb.goods_total,wb.shijia,wb.check_time,wb.create_time , wb.fin_check_time,wp.pro_name,wp.pay_method,wp.amount,wp.pro_id from warehouse_bill as wb,warehouse_bill_pay as wp
			" . $where . " and wb.id=wp.bill_id and wb.bill_type in('L','T') and wb.bill_status=2 order by wb.create_time DESC";
    
    	}
    	else
    	{
    		$sql = "select wb.id,wb.bill_type,wb.put_in_type,wb.fin_check_status,wb.bill_note,wb.bill_no,wb.goods_num,wb.goods_total,wb.shijia,wb.check_time,wb.create_time , wb.fin_check_time, wb.pro_id, wb.pro_name from warehouse_bill as wb " . $where . " and  wb.bill_type in ('B','C') and wb.bill_status=2  order by wb.create_time DESC";
    	}
    	if (empty($page))
    	{
    		//file_put_contents("D:\u223.txt",$sql."\r\n",FILE_APPEND );
    		$res = $this->db->getAll($sql);
    	}
    	else
    	{
    		$res = $this->db->getPageList($sql, array(), $page, 20);
    	}
    	return $res['data'];
    }
    /**
     * 查询仓储货品信息
     * @return json
     */
    public function getWarehouseGoodsByGoodsid($goods_id) {
    	
    	$where = '';
    	$sql = "SELECT is_on_sale FROM `warehouse_goods` WHERE 1"; //暂时用＊号
    
    	if (!empty($goods_id)) {
    		$sql .= " and `goods_id` = '{$goods_id}'";
    	} else {
    		return false;
    	}
    	
    
       return $row = $this->db->getRow($sql);
    
    	
    }
    
    
    
}

?>