<?php

/**
 *  -------------------------------------------------
 *   @file		: AppCouponPolicyModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-11 15:46:48
 *   @update	:
 *  -------------------------------------------------
 */
class AppCouponPolicyModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'app_coupon_policy';
        $this->pk = 'id';
        $this->_prefix = '';
        $this->_dataObject = array("id" => "自增id",
            "policy_name" => "优惠券政策名称",
            "policy_desc" => "优惠券政策描述",
            "policy_price" => "优惠金额",
            "policy_status" => "优惠政策状态；1，保存；2，提交申请；3，作废；4，审核通过；5，审核驳回；6，过期",
            "policy_type" => "政策类型",
            "valid_time_start" => "有效开始时间",
            "valid_time_end" => "有效结束时间",
            "create_time" => "创建时间",
            "create_user" => "创建人",
            "check_time" => "审核时间",
            "check_user" => "审核人");
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url AppCouponPolicyController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        //不要用*,修改为具体字段
        $sql = "SELECT * FROM `" . $this->table() . "`";
        $str = '';
		if($where['policy_status'] != "")
		{
			$str .= "`policy_status` = {$where['policy_status']} AND ";
		}
		if(!empty($where['valid_time_start']))
		{
			$str .= "`valid_time_start`>='".$where['valid_time_start']."' AND ";
		}
		if(!empty($where['valid_time_end']))
		{
			$str .= "`valid_time_end`<='".$where['valid_time_end']."' AND ";
		}
        if ($str) {
            $str = rtrim($str, "AND "); //这个空格很重要
            $sql .=" WHERE " . $str;
        }
        $sql .= " ORDER BY `id` DESC";
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }
	
	function getPolicyStatusList($status=0){
		//优惠政策状态；1，保存；2，提交申请；3，作废；4，审核通过；5，审核驳回；6，过期
		$data = array('1'=>'保存','2'=>'提交申请','3'=>'作废','4'=>'审核通过','5'=>'审核驳回','6'=>'过期');
		if($status > 0){
			return $data[$status];	
		}
		return $data;
	}
    
    
    function getCouponPolicy($policy=0){
        $sql = "SELECT `id`,`policy_name`,`policy_status` FROM `{$this->table()}`";
        if($policy > 0){
            $sql .= "WHERE `id` = $policy";
        }
        return $this->db()->getAll($sql);
    }

    function getCouponPolicyRow($id){
        if($id == ''){
            return false;
        }
        $sql = "SELECT `policy_status` FROM `{$this->table()}` WHERE `id` = {$id}";
        return $this->db()->getRow($sql);
    }
    
    
    function getCouponPolicyList() {
        //$date = date("Y-m-d 00:00:00");
        $sql = "SELECT `id`,`policy_name`,`policy_status` FROM `{$this->table()}` where `policy_status`=4";
        // where `valid_time_start` <= '{$date}' and `valid_time_end` >='{$date}' and `policy_status`=4
        return $this->db()->getAll($sql);
    }

}

?>