<?php

/**
 *  -------------------------------------------------
 *   @file		: BaseCouponModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-11 10:39:22
 *   @update	:
 *  -------------------------------------------------
 */
class BaseCouponModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'base_coupon';
        $this->pk = 'id';
        $this->_prefix = '';
        $this->_dataObject = array("id" => "优惠券自增id",
            "coupon_code" => "优惠券码",
            "coupon_price" => "优惠券等价金额",
            "coupon_type" => "优惠券类型",
            "coupon_policy" => "优惠券所属政策",
            "coupon_status" => "优惠券状态；1，有效；2，已使用；3，作废",
            "create_time" => "创建时间",
            "create_user" => "创建人");
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url BaseCouponController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        //不要用*,修改为具体字段
        $sql = "SELECT * FROM `" . $this->table() . "`";
        $str = '';
		if($where['coupon_status'] != "")
		{
			$str .= "`coupon_status` = {$where['coupon_status']} AND ";
		}
		if(!empty($where['coupon_code']))
		{
			$str .= "`coupon_code`='".$where['coupon_code']."' AND ";
		}
		if(!empty($where['price_start']))
		{
			$str .= "`coupon_price`>=".$where['price_start']." AND ";
		}
		if(!empty($where['price_end']))
		{
			$str .= "`coupon_price`<=".$where['price_end']." AND ";
		}
		if(!empty($where['coupon_policy']))
		{
			$str .= "`coupon_policy`=".$where['coupon_policy']." AND ";
		}
        if ($str) {
            $str = rtrim($str, "AND "); //这个空格很重要
            $sql .=" WHERE " . $str;
        }
        $sql .= " ORDER BY `id` DESC";
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }
    
    
    function batchModfiyStatus($ids) {
        if(count($ids) < 1){
            return false;
        }
        $ids = implode(",", $ids);
        $sql = "UPDATE `{$this->table()}` SET `coupon_status` = 3 WHERE `id` in ({$ids})";
        return $this->db()->query($sql);
    }
    
    
    function getBatchCoupon($ids){
        $ids = implode(",", $ids);
        $sql = "select `coupon_status`,`id` from `{$this->table()}` WHERE `id` in ({$ids})";
        return $this->db()->getAll($sql);
    }

    function checkCodeStatus($id){
        if($id == ''){
            return false;
        }
        $sql = "select `coupon_status`,`coupon_policy` from `{$this->table()}` WHERE `id` = {$id}";
        return $this->db()->getRow($sql);
    }

    function checkCode($code) {
        $sql = "select `id` from `{$this->table()}` where `coupon_code`='{$code}'";
        return $this->db()->getOne($sql);
    }

    function updateCouponStatus($data) {
        $id = $data['id'];
        $use_time = $data['use_time'];
        $exchange_user = $data['exchange_user'];
        if($id == ''){
            return false;
        }
        $sql = "UPDATE `{$this->table()}` SET `coupon_status` = 2,`use_time` = '{$use_time}',`exchange_user` = '{$exchange_user}' WHERE `id` = {$id}";
        return $this->db()->query($sql);
    }

}

?>