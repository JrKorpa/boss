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
class AppDownloadZhekouModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'diamond_info_log';
        $this->pk = 'id';
        $this->_prefix = '';
        $this->_dataObject = array("id" => " ",
            "from_ad" => "1=51钻，2=BDD",
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
            $sql.= "`operation_type`=" . $where['operation_type']." AND ";
        }
        if (isset($where['create_user']) && !empty($where['create_user'])) {
            $sql.= "`create_user` like'" . $where['create_user'] . "%' AND ";
        }
        if (isset($where['start_time']) && !empty($where['start_time'])) {
            $sql.= "`create_time`>='" . $where['start_time'] . "' AND ";
        }
        if (isset($where['end_time']) && !empty($where['end_time'])) {
            $sql.= "`create_time`<='" . $where['end_time'] . "' AND ";
        }
        if (isset($where['from_ad']) && !empty($where['from_ad'])) {
            $sql.= "`from_ad`=" . $where['from_ad']." AND ";
        }
        if ($str) {
            $str = rtrim($str, "AND "); //这个空格很重要
            $sql .=" WHERE " . $str;
        }       
        $sql .= " ORDER BY `id` DESC";
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }

    /**
     * 取裸钻
     * @return type
     */
    function getDiamond_all($start,$limit) {
        $sql="SELECT `goods_sn`,`cert`,`cert_id`,`goods_number`,`chengben_jia` as `chengbenjia`,`carat`,`color`,`clarity`,`cut`,`symmetry`,`polish`,`shape` as `shape`,`fluorescence`,`depth_lv` as `depth`,`table_lv` as `table`,`from_ad` as `source`,`source_discount`,`warehouse`,`gemx_zhengshu`,`kuan_sn`,
		`is_active`,`guojibaojia`,`us_price_source` FROM `diamond_info` WHERE `cert` = 'GIA' and `status` = 1 AND `shop_price`>0 limit $start, $limit ";
        return $this->db()->getAll($sql);
    }
	//获取总数
	function getdiacount()
	{
		$sql = "select count(1) as count 
		from `diamond_info` WHERE `cert` = 'GIA' and `status` = 1 AND `shop_price`>0 ";
		$datainfo = $this->db()->getRow($sql);
		return $datainfo['count'];
	}

}

?>