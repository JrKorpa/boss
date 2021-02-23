<?php

/**
 *  -------------------------------------------------
 *   @file		: DiamondJiajialvModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-15 15:56:34
 *   @update	:
 *  -------------------------------------------------
 */
class DiaChannelJiajialvModel extends Model {

    public static $optertion_list = array(1 => '添加', 2 => '修改', 3 => '停用', 4 => '启用');

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'diamond_channel_jiajialv';
        $this->pk = 'id';
        $this->_prefix = '';
        $this->_dataObject = array(
        	"id" => " ",
            "channel_id" => "",
        	"good_type" => "货品类型1现货2期货",
            "carat_min" => "最小钻重",
            "carat_max" => "最大钻重",
            "jiajialv" => "加价率");
        parent::__construct($id, $strConn);
    }

    public function get_cloumn_name() {
        $cloumn_arr = array(
        	"channel_id" => '渠道',
            "good_type" => "货品类型",
            "carat_min" => "最小钻重",
            "carat_max" => "最大钻重",
            "jiajialv" => "加价率",
        );
        return $cloumn_arr;
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url DiamondJiajialvController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true, $select = "*") {
        $sql = "SELECT $select FROM `" . $this->table() . "`";
        $str = '';
        if (isset($where['carat_min']) && $where['carat_min'] != '') {
            $str.= "`carat_min` >= " . $where['carat_min']." AND ";
        }
        if (isset($where['carat_max']) && $where['carat_max'] != '') {
            $str.= "`carat_max` <= " . $where['carat_max']." AND ";
        }
      
        if (isset($where['good_type']) && !empty($where['good_type'])) {
            $str.= "`good_type` = " . $where['good_type']." AND ";
        }
        if (isset($where['status']) && $where['status']!='') {
            $str.= "`status` = " . $where['status']." AND ";
        }
        
        if (isset($where['channel_id']) && !empty($where['channel_id'])) {
        	$str.= "`channel_id` in (" . $where['channel_id'].") AND ";
        }

        if ($str) {
            $str = rtrim($str, "AND "); //这个空格很重要
            $sql .=" WHERE " . $str;
        }
        $sql .= " ORDER BY `id` DESC";
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }

    /*
     * 获取所有数据
     */

    function getAllList($select = "*", $where = array()) {
        $where_sql = "";
        $sql='';
       
        if (isset($where['good_type']) && !empty($where['good_type'])) {
            $where_sql .= "`good_type` = '" . $where['good_type'] . "' AND ";
        }
        
        if (isset($where['channel_id']) && !empty($where['channel_id'])) {
        	$where_sql.= "`channel_id` in (" . $where['channel_id'].") AND ";
        }
		
		if (isset($where['cert']) && !empty($where['cert'])) {
        	$where_sql.= "`cert` ='".$where['cert']."' AND ";
        }
        
        if ($where_sql) {
            $str = rtrim($where_sql, "AND "); //这个空格很重要
            $sql .=" WHERE " . $str;
        }
        $sql = " SELECT * FROM `" . $this->table() . "`" . $sql;

        return $this->db()->getAll($sql);
    }

    function getJiajialv($jiajialvList, $cert, $carat, $from_ad, $status,$good_type) {
		if(empty($jiajialvList)){
			return false;
		}
        foreach ($jiajialvList as $v) {
            if ($v['status'] == 1 && $good_type == $v['good_type']) {
                if ($v['carat_min'] <= $carat && $carat < $v['carat_max']) {
                    return $v['jiajialv'];
                }
            }
        }
        return false;
    }
}

?>