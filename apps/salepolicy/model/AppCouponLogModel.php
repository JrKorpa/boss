<?php

/**
 *  -------------------------------------------------
 *   @file		: AppCouponLogModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-11 16:52:55
 *   @update	:
 *  -------------------------------------------------
 */
class AppCouponLogModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'app_coupon_log';
        $this->pk = 'exchange_id';
        $this->_prefix = '';
        $this->_dataObject = array("exchange_id" => "自增id",
            "exchange_coupon" => "兑换码",
            "exchange_status" => "优惠状态",
            "exchange_time" => "兑换时间",
            "exchange_name" => "兑换人",
            "exchange_remark" => "备注");
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url AppCouponTypeController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        //不要用*,修改为具体字段
        $sql = "SELECT * FROM `" . $this->table() . "`";
        $str = '';
		if($where['exchange_name'] != "")
		{
			$str .= "`exchange_name` like \"%".addslashes($where['exchange_name'])."%\" AND ";
		}
        if(!empty($where['time_start']))
        {
            $str.="`exchange_time` >= '".$where['time_start']." 00:00:00' AND ";
        }
        if(!empty($where['time_end']))
        {
            $str.="`exchange_time` <= '".$where['time_end']." 23:59:59' AND ";
        }
//		if(!empty($where['xx']))
//		{
//			$str .= "`xx`='".$where['xx']."' AND ";
//		}
        if ($str) {
            $str = rtrim($str, "AND "); //这个空格很重要
            $sql .=" WHERE " . $str;
        }
        $sql .= " ORDER BY `exchange_id` DESC";
        //echo $sql;die;
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }

}

?>