<?php

/**
 *  -------------------------------------------------
 *   @file		: AppShopConfigModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-18 14:56:43
 *   @update	:
 *  -------------------------------------------------
 */
class AppShopConfigModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'app_shop_config';
        $this->_dataObject = array("id" => " ",
            "name" => "名称",
            "code" => "编码",
            "value" => "值");
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url AppShopConfigController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        $sql = "SELECT `id`, `name`, `code`, `value` FROM `" . $this->table() . "`";
        $str = '';
        if (isset($where['name']) && !empty($where['name'])) {
            $str.= "`name` LIKE '%" . $where['name'] . "%' AND ";
        }
        if (isset($where['code']) && !empty($where['code'])) {
            $str.= "`code` LIKE '%" . $where['code'] . "%' AND ";
        }
        if ($str) {
            $str = rtrim($str, "AND "); //这个空格很重要
            $sql .=" WHERE 1 AND " . $str;
        }
        $sql .= " ORDER BY `id` DESC";
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }

    function hasConfig($name,$code){
        $sql = "select count(1) from `".$this->table()."` where name='".$name."' or code='".$code."'";
        return  $this->db()->getOne($sql,array(),false);
                
    }

    function gethuilvValue(){
        $sql = "SELECT `value` FROM `".$this->table()."` WHERE `code` LIKE 'dia_huilv' ";
        return  $this->db()->getOne($sql);
                
    }
}

?>