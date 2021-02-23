<?php

/**
 *  -------------------------------------------------
 *   @file		: AppOrderAccountModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-12 12:23:42
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderAccountModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'app_order_account';
        $this->pk = 'id';
        $this->_prefix = '';
        $this->_dataObject = array("id" => "自增ID",
            "order_id" => "订单id",
            "order_amount" => "订单总金额",
            "money_paid" => "已付",
            "money_unpaid" => "未付",
            "goods_return_price" => "商品实际退款",
            "real_return_price" => "实退金额",
            "shipping_fee" => "快递费",
            "goods_amount" => "商品总额",
            "favorable_price" => "优惠价格",
            "coupon_price" => "订单优惠券金额");
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url AppOrderAccountController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        //不要用*,修改为具体字段
        $sql = "SELECT * FROM `" . $this->table() . "`";
        $str = '';
//		if($where['xxx'] != "")
//		{
//			$str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
//		}
//		if(!empty($where['xx']))
//		{
//			$str .= "`xx`='".$where['xx']."' AND ";
//		}
        if ($str) {
            $str = rtrim($str, "AND "); //这个空格很重要
            $sql .=" WHERE " . $str;
        }
        $sql .= " ORDER BY `id` DESC";
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }

}

?>