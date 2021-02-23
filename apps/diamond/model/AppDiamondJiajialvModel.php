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
class AppDiamondJiajialvModel extends Model {

    public static $optertion_list = array(1 => '添加', 2 => '修改', 3 => '停用', 4 => '启用');

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'app_diamond_jiajialv';
        $this->pk = 'id';
        $this->_prefix = '';
        $this->_dataObject = array("id" => " ",
            "good_type" => "货品类型1现货2期货",
            "from_ad" => "来源:2=>BDD,1=>51钻",
            "cert" => "证书类型",
            "cost_min" => "最低成本价",
            "cost_max" => "最高成本价",
            "jiajialv" => "加价率");
        parent::__construct($id, $strConn);
    }

    public function get_cloumn_name() {
        $cloumn_arr = array(
            "good_type" => "货品类型",
            "from_ad" => "来源",
            "cert" => "证书类型",
            "cost_min" => "最低成本价",
            "cost_max" => "最高成本价",
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
        if (isset($where['cost_min']) && $where['cost_min'] != '') {
            $str.= "`cost_min` >= " . $where['cost_min']." AND ";
        }
        if (isset($where['cost_max']) && $where['cost_max'] != '') {
            $str.= "`cost_max` <= " . $where['cost_max']." AND ";
        }
        if (isset($where['from_ad']) && !empty($where['from_ad'])) {
            $str.= "`from_ad` = '" . $where['from_ad']."' AND ";
        }
        if (isset($where['good_type']) && !empty($where['good_type'])) {
            $str.= "`good_type` = " . $where['good_type']." AND ";
        }
        if (isset($where['status']) && $where['status']!='') {
            $str.= "`status` = " . $where['status']." AND ";
        }
        if (isset($where['cert']) && $where['cert'] != '') {
            $str.= "`cert` = '" . $where['cert'] . "' AND ";
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
        if (isset($where['cert']) && !empty($where['cert'])) {
            $where_sql .= "`cert` = '" . $where['cert'] . "' AND ";
        }
        if (isset($where['from_ad']) && !empty($where['from_ad'])) {
            $where_sql .= "`from_ad` = '" . $where['from_ad'] . "' AND ";
        }
        if (isset($where['good_type']) && !empty($where['good_type'])) {
            $where_sql .= "`good_type` = '" . $where['good_type'] . "' AND ";
        }
        if ($where_sql) {
            $str = rtrim($where_sql, "AND "); //这个空格很重要
            $sql .=" WHERE " . $str;
        }
        $sql = " SELECT `id`,`good_type`,`from_ad`,`cert`,`cost_min`,`cost_max`,`jiajialv`,`status` FROM `" . $this->table() . "`" . $sql;

//         echo $sql;exit;
        
        return $this->db()->getAll($sql);
    }

    function getJiajialv($jiajialvList, $cert, $carat, $from_ad, $status,$good_type) {
    	
		if(empty($jiajialvList)){
			return false;
		}
        foreach ($jiajialvList as $v) {
            if ($cert == $v['cert'] && $from_ad == $v['from_ad'] && $v['status'] == 1 && $good_type == $v['good_type']) {
                if ($v['cost_min'] <= $carat && $carat < $v['cost_max']) {
                    return $v['jiajialv'];
                }
            }
        }
        return false;
    }

    //对比新旧数据
    public function array_difficult($newdo, $olddo) {
        $r = array();
        if (!($olddo and $newdo))
            throw new Exception('array_diffx的参数必须是两个非空数组');
        $diamondView = new AppDiamondColorView($newdo['id']); //不能直接new View，需要传一个主键id或传一个model对象
        $good_type = $diamondView->getGoodTypeList();
        $from_ad = $diamondView->getFromAdList();
        //获取字段的名称
        $cloumn_name = $this->get_cloumn_name();
        foreach ($newdo as $i => $l) {
            if (!isset($olddo[$i]) || $olddo[$i] != $l) {
                if (in_array($i, array('good_type', 'from_ad'))) {
                    $a = $olddo[$i];
                    $b = $l;
                    if ($i == 'good_type') {
                        $r[$i]['old'] = $good_type[$a];
                        $r[$i]['new'] = $good_type[$b];
                    } else {
                        $r[$i]['old'] = $from_ad[$a];
                        $r[$i]['new'] = $from_ad[$b];
                    }
                } else {
                    $r[$i]['old'] = $olddo[$i];
                    $r[$i]['new'] = $l;
                }

                $r[$i]['name'] = $cloumn_name[$i];
            }
        }
        return $r;
    }

}

?>