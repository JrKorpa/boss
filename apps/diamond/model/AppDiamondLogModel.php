<?php

/**
 *  -------------------------------------------------
 *   @file		: DiamondInfoLogModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-17 18:23:12
 *   @update	:
 *  -------------------------------------------------
 */
class AppDiamondLogModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'app_diamond_log';
        $this->pk = 'id';
        $this->_prefix = '';
        $this->_dataObject = array("id" => " ",
            "from_ad" => "1=kela，2=leibish",
            "operation_type" => "1添加，2修改，3上架，4下架",
            "operation_content" => "操作内容",
            "create_time" => "操作时间",
            "create_user" => "操作人");
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url DiamondInfoLogController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        $sql = "SELECT `id`, `from_ad`, `operation_type`, `operation_content`, `create_time`, `create_user` FROM `" . $this->table() . "`";
        $str = '';
        if (isset($where['operation_type']) && !empty($where['operation_type'])) {
            $str.= "`operation_type`=" . $where['operation_type']." AND ";
        }
        if (isset($where['create_user']) && !empty($where['create_user'])) {
            $str.= "`create_user` like'" . $where['create_user'] . "%' AND ";
        }
        if (isset($where['start_time']) && !empty($where['start_time'])) {
            $str.= "`create_time`>='" . $where['start_time'] . " 00:00:00' AND ";
        }
        if (isset($where['end_time']) && !empty($where['end_time'])) {
            $str.= "`create_time`<='" . $where['end_time'] . " 23:59:59' AND ";
        }
        if (isset($where['from_ad']) && !empty($where['from_ad'])) {
            $str.= "`from_ad`=" . $where['from_ad']." AND ";
        }
        if ($str) {
            $str = rtrim($str, "AND "); //这个空格很重要
            $sql .=" WHERE 1 AND " . $str;
        }       
        $sql .= " ORDER BY `id` DESC";//echo $sql;exit;
//         echo $sql;exit;
        
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }

    

}

?>