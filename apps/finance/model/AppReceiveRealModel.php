<?php

/**
 *  -------------------------------------------------
 *   @file		: AppReceiveRealModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-31 12:17:00
 *   @update	:
 *  -------------------------------------------------
 */
class AppReceiveRealModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'app_receive_real';
        $this->pk = 'real_id';
        $this->_prefix = '';
        $this->_dataObject = array("real_id" => "实收单ID",
            "real_number" => "实收单单号",
            "from_ad" => "订单来源",
            "should_number" => "应收单单号",
            "bank_name" => "银行名称",
            "bank_serial_number" => "银行交易流水号",
            "total" => "实收金额",
            "pay_tiime" => "财务收款时间",
            "maketime" => "制单时间",
            "makename" => "制单人");
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url AppReceiveRealController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        //不要用*,修改为具体字段
        $sql = "SELECT `pr`.`real_id`,`pr`.`real_number`,`pr`.`should_number`,`pr`.`from_ad`,`pr`.`bank_serial_number`,`pr`.`pay_tiime`,`pr`.`total`,`pr`.`maketime`,`pr`.`makename`,`pr`.`from_ad` FROM `" . $this->table() . "` AS `pr`";
        $sql .= "where 1 ";
        if (!empty($where['from_ad'])) {
            $sql .= " AND `pr`.`from_ad` = '{$where['from_ad']}'";
        }

        if (!empty($where['real_number'])) {
            $sql .= " AND `pr`.`real_number` = '{$where['real_number']}'";
        }

        if (!empty($where['should_number'])) {
            $sql .= " AND `pr`.`should_number` = '{$where['should_number']}'";
        }

        if (!empty($where['pay_tiime_start'])) {
            $sql .= " AND `pr`.`pay_tiime` >= '" . $where['pay_tiime_start'] . " 00:00:00'";
        }
        if (!empty($where['pay_tiime_end'])) {
            $sql .= " AND `pr`.`pay_tiime` <= '" . $where['pay_tiime_end'] . " 23:59:59'";
        }

        $sql .= " ORDER BY `pr`.`real_id` DESC";
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }


    public function getInfoList($where) {
        $sql = "SELECT `pr`.`real_id`,`pr`.`real_number`,`pr`.`should_number`,`pr`.`from_ad`,`pr`.`bank_serial_number`,`pr`.`pay_tiime`,`pr`.`total`,`pr`.`maketime`,`pr`.`makename`,`pr`.`from_ad` FROM `" . $this->table() . "` AS `pr`";
        $sql .= "where 1 ";
        if (!empty($where['from_ad'])) {
            $sql .= " AND `pr`.`from_ad` = '{$where['from_ad']}'";
        }

        if (!empty($where['real_number'])) {
            $sql .= " AND `pr`.`real_number` = '{$where['real_number']}'";
        }

        if (!empty($where['should_number'])) {
            $sql .= " AND `pr`.`should_number` = '{$where['should_number']}'";
        }

        if (!empty($where['pay_tiime_start'])) {
            $sql .= " AND `pr`.`pay_tiime` >= '" . $where['pay_tiime_start'] . " 00:00:00'";
        }
        if (!empty($where['pay_tiime_end'])) {
            $sql .= " AND `pr`.`pay_tiime` <= '" . $where['pay_tiime_end'] . " 23:59:59'";
        }

        $data = $this->db()->getAll($sql);
        return $data;
    }

    /**
	* 确认收款，生成实收单记录
	* @param $data Array 提交的数据
	*/
	public function addReal($data)
	{
		$rdata = $this->saveData($data,array(),true);
        $real_number = 'CWSS'.$rdata['real_id'];
		$sql = 'UPDATE `'.$this->table().'` SET `real_number` = \''.$real_number.'\' WHERE `real_id` = '.$rdata['real_id'];
		return $result = $this->db()->query($sql) ?  $rdata : false;
	}

}

?>