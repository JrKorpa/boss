<?php

/**
 *  -------------------------------------------------
 *   @file		: AppReceiptDepositModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-19 14:29:54
 *   @update	:
 *  -------------------------------------------------
 */
class AppReceiptDepositModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'app_receipt_deposit';
        $this->pk = 'id';
        $this->_prefix = '';
        $this->_dataObject = array("id" => " ",
            "order_sn" => "订单号",
            "receipt_sn" => "定金收据号",
            "customer" => "客户名称",
            "department" => "部门id",
            "pay_fee" => "支付金额",
            "pay_type" => "支付类型",
            "card_no" => "卡号",
            "card_voucher" => "刷卡凭证",
            "pay_time" => "收款时间",
            "status" => "状态：1有效，2点款，3作废",
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
     * 	@url AppReceiptDepositController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        $sql = "SELECT rd.`id`, rd.`order_sn`, rd.`receipt_sn`, rd.`customer`, rd.`department`, rd.`pay_fee`, rd.`pay_type`, rd.`card_no`, rd.`card_voucher`, rd.`pay_time`, rd.`status`, rd.`print_num`, rd.`pay_user`, rd.`remark`, rd.`add_time`, rd.`add_user`, rd.`zuofei_time` FROM `" . $this->table() . "` rd left join app_order.base_order_info oi on oi.order_sn = rd.order_sn WHERE 1 ";//inner join app_order.base_order_info oi on oi.order_sn = rd.order_sn
        if(isset($where['order_sn']) && $where['order_sn'] != ''){
            $sql .= " and rd.`order_sn` = '{$where['order_sn']}'";
        }
        if(isset($where['receipt_sn']) && $where['receipt_sn'] != ''){
            $sql .= " and rd.`receipt_sn` = '{$where['receipt_sn']}'";
        }
        if(isset($where['status']) && $where['status'] > 0){
            $sql .= " and rd.`status` = {$where['status']}";
        }
        if(isset($where['pay_department']) && $where['pay_department'] > 0){
            $sql .= " and rd.`department` in ({$where['pay_department']})";
        }
        if(isset($where['pay_start_time']) && isset($where['pay_end_time'])){
            $sql .= " and rd.`pay_time` >= '{$where['pay_start_time']}'";
        }
        if(isset($where['pay_end_time']) && $where['pay_end_time']){
            $sql .= " and rd.`pay_time` <= '{$where['pay_end_time']}'";
        }
        if(isset($where['add_start_time']) && isset($where['add_start_time'])){
            $sql .= " and rd.`add_time` >= '{$where['add_start_time']}'";
        }
        if(isset($where['add_end_time']) && $where['add_end_time']){
            $sql .= " and rd.`add_time` <= '{$where['add_end_time']}'";
        }
        //zt隐藏
        if(SYS_SCOPE == 'zhanting')
        {
            $sql .= " and oi.`hidden` <> 1";
        }

//        if($where['channerids']!==false){
//            $sql .= " and rd.`department` in ({$where['channerids']})";
//        }

        $sql .= " ORDER BY rd.`id` DESC";
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }
    
    /**
     * 获取id
     * @param string $param
     * @return type
     */
    public function getIdBySn($param) {
        $sql = "SELECT `id` FROM `" . $this->table() . "` WHERE receipt_sn = '{$param}' ";
        return $this->db()->getOne($sql);
    }
    
    public function getRowList($param) {
        $sql = "SELECT * FROM `" . $this->table() . "` WHERE receipt_sn = '{$param}' ";
        return $this->db()->getRow($sql);
    }
    
    public function getStatusList($param=0) {
        $data = array(1=>'有效',2=>'点款',3=>'作废');
        if($param){
            return $data[$param];
        }
        return $data;
    }
    
    
    /**
     * 生成定金收据编号
     * @param type $department
     * @return string
     */
	public function create_receipt($shop_short="DYC",$str='DJ'){
        //$shop_short=$_SESSION['warehouse'];
//        $shop_short='DYC';
		$date = date("Ymd");
        $header=$str.'-'.$shop_short.'-'.$date;

        $receipt_id = rand(1000,9999);
        $nes = str_pad($receipt_id,4,'0',STR_PAD_LEFT);
        $bonus_code=$header.$nes;
        return $bonus_code;	
	}
    

    //取用户 
    public function get_user_name($user_id){
        if(!empty($user_id)){
            $keys[] ='member_id';
            $vals[] =$user_id;
        }else{
            return false;
        }

        $ret = ApiModel::sale_member_api($keys, $vals, 'GetMemberByMember_id');
        return $ret;
    }

    /**
     * 获取定金点款订单数据
     * @param type $order_sn
     * @return type
     */
    public function getInfoByOrderSn($order_sn=''){
        $sql = "select * from  `" . $this->table() . "` WHERE `order_sn`='{$order_sn}'";
        $arr = $this->db()->getAll($sql);
        return $arr;
    }
    
    /**
     * 更新定金收据为初始
     * @param type $id
     * @return type
     */
    public function updateDepositInfo($id=0) {
        $sql = "update `" . $this->table() . "` set `status`=1,`order_sn`='' where `id`={$id}";
        return $this->db()->query($sql);
    }

}

?>