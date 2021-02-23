<?php

/**
 * 裸钻模块API数据模型（代替Diamond/Api/api.php）
 *  -------------------------------------------------
 *   @file		: SaleModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class SelfDiamondModel extends SelfModel {

    protected $db;

    function __construct($strConn = "") {
        $this->db = DB::cn($strConn);
    }

    public function db() {
        return $this->db;
    }
    //更改diamond_info表数据
	public function updateDiamondInfo($data,$where){
	    $sql = $this->updateSql('diamond_info',$data, $where);
	    return $this->db()->query($sql);
	}
}

?>