<?php

/**
 *  -------------------------------------------------
 *   @file		: AppReceiptPayModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-21 22:49:36
 *   @update	:
 *  -------------------------------------------------
 */
class AppReceiptPayModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'app_receipt_pay';
        $this->pk = 'id';
        $this->_prefix = '';
        $this->_dataObject = array("id" => " ",
            "order_sn" => "订单号",
            "receipt_sn" => "收据号",
            "customer" => "客户名称",
            "department" => "部门id",
            "pay_fee" => "支付金额",
            "pay_type" => "支付类型",
            "card_no" => "卡号",
            "card_voucher" => "刷卡凭证",
            "pay_time" => "收款时间",
            "status" => "状态：1有效，2作废",
            "print_num" => "打印次数",
            "pay_user" => "收款人",
            "remark" => "备注",
            "add_time" => " ",
            "add_user" => "操作人",
            "zuofei_time" => " ");
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url AppReceiptPayController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {

        $innerjoin = "";
        if(SYS_SCOPE == 'zhanting'){
            $innerjoin = " inner join app_order.base_order_info oi on oi.order_sn = rp.order_sn ";
        }

        $sql = "SELECT rp.`id`, rp.`order_sn`, rp.`receipt_sn`, rp.`customer`, rp.`department`, rp.`pay_fee`, rp.`pay_type`, rp.`card_no`, rp.`card_voucher`, rp.`pay_time`, rp.`status`, rp.`print_num`, rp.`pay_user`, rp.`remark`, rp.`add_time`, rp.`add_user`, rp.`zuofei_time` FROM `" . $this->table() . "` rp {$innerjoin} WHERE 1 ";
        if(isset($where['order_sn']) && $where['order_sn'] != ''){
            $sql .= " and rp.`order_sn` = '{$where['order_sn']}'";
        }
        if(isset($where['receipt_sn']) && $where['receipt_sn'] != ''){
            $sql .= " and rp.`receipt_sn` = '{$where['receipt_sn']}'";
        }
        if(isset($where['status']) && $where['status'] > 0){
            $sql .= " and rp.`status` = {$where['status']}";
        }
        if(isset($where['pay_department']) && $where['pay_department'] > 0){
            $sql .= " and rp.`department` in ({$where['pay_department']})";
        }
        if(isset($where['pay_start_time']) && isset($where['pay_end_time'])){
            $sql .= " and rp.`pay_time` >= '{$where['pay_start_time']}'";
        }
        if(isset($where['pay_end_time']) && $where['pay_end_time']){
            $sql .= " and rp.`pay_time` <= '{$where['pay_end_time']}'";
        }
        if(isset($where['add_start_time']) && isset($where['add_start_time'])){
            $sql .= " and rp.`add_time` >= '{$where['add_start_time']}'";
        }
        if(isset($where['add_end_time']) && $where['add_end_time']){
            $sql .= " and rp.`add_time` <= '{$where['add_end_time']}'";
        }
        //zt隐藏
        if(SYS_SCOPE == 'zhanting')
        {
            $sql .= " and oi.`hidden` <> 1";
        }

//        if($where['channerids']!==false){
//            $sql .= " and rp.`department` in ({$where['channerids']})";
//        }

        $sql .= " ORDER BY rp.`id` DESC";
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }
    
    
    
    public function getRowList($param) {
        $sql = "SELECT * FROM `" . $this->table() . "` WHERE receipt_sn = '{$param}' ";
        return $this->db()->getRow($sql);
    }
    
    public function getStatusList($param=0) {
        $data = array(1=>'有效',2=>'作废');
        if($param){
            return $data[$param];
        }
        return $data;
    }
    
    /**
     * 获取该订单号的点款数据
     * @param type $order_sn
     * @return type
     */
    public function getInfoByOrderSn($order_sn='') {
        $sql = "SELECT * FROM `" . $this->table() . "` WHERE `order_sn` = '{$order_sn}' ";
        return $this->db()->getAll($sql);
    }
    
    /**
     * 更新点款记录为作废
     * @param type $id
     * @return type
     */
    public function updatePayInfo($id=0) {
        $sql = "update `" . $this->table() . "` set `status`=2,`zuofei_time`='".date("Y-m-d H:i:s")."' where `id`={$id}";
        return $this->db()->query($sql);
    }

    /**
     * 删除该订单点款记录
     * @param type $order_sn
     * @return type
     */
    public function deletePayInfo($order_sn='') {
        $sql = "delete from `" . $this->table() . "` where `order_sn`='{$order_sn}'";
        return $this->db()->query($sql);
    }
    
    /**
     * 获取订单支付金额
     * @param type $order_sn
     * @return type
     */
    public function getSumMoneyByOrderSn($order_sn='') {
        $sql = "select sum(`pay_fee`) from `" . $this->table() . "` where `order_sn`='{$order_sn}'";
        return $this->db()->getOne($sql);
    }
}

?>