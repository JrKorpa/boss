<?php

/**
 *  -------------------------------------------------
 *   @file		: AppCouponTypeModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-11 16:52:55
 *   @update	:
 *  -------------------------------------------------
 */
class AppCouponTypeModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'app_coupon_type';
        $this->pk = 'id';
        $this->_prefix = '';
        $this->_dataObject = array("id" => "自增id",
            "type_name" => "优惠类型名称");
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
		if($where['type_name'] != "")
		{
			$str .= "`type_name` like \"%".addslashes($where['type_name'])."%\" AND ";
		}
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
    
    
    function getCouponTypeList($id=0) {
        $sql = "SELECT `id`,`type_name` FROM `{$this->table()}`";
        if($id > 1){
            $sql .= " WHERE `id`=$id";
        }
        return $this->db()->getAll($sql);
    }
    
    //获取优惠券名称  验证是否已经存在
    function getTypeName($type_name) {
    	$sql = "SELECT `id`,`type_name` FROM `{$this->table()}`";
    	if($type_name!=''){
    		$sql .= " WHERE `type_name`='{$type_name}'";
    	}
    	return $this->db()->getRow($sql);
    }
    

}

?>