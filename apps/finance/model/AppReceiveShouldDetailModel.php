<?php

/**
 *  -------------------------------------------------
 *   @file		: AppReceiveShouldDetailModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-02 14:49:51
 *   @update	:
 *  -------------------------------------------------
 */
class AppReceiveShouldDetailModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'app_receive_should_detail';
        $this->pk = 'detail_id';
        $this->_prefix = '';
        $this->_dataObject = array("detail_id" => " ",
            "should_id" => "应收单对应ID",
            "apply_number" => "应收申请单单号",
            "total_cope" => "应收金额");
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url AppReceiveShouldDetailController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        //不要用*,修改为具体字段
        $sql = "SELECT * FROM `" . $this->table() . "`";
        $str = '';
        if ($str) {
            $str = rtrim($str, "AND "); //这个空格很重要
            $sql .=" WHERE " . $str;
        }
        $sql .= " ORDER BY `detail_id` DESC";
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }

    /**
     * 根据应收单ID(should_id) 获取对应的应收申请单信息
     * @param $should_id     INT     应收单ID
     * @param $col     String     查询的字段
     * @param $type     String     获取结果集中apply_number 一位数组
     * @return Array         应收申请单集合信息
     */
    public function getDetailArr($should_id, $col = '*', $type = false) {
        $sql = 'SELECT ' . $col . ' FROM `' . $this->table() . '` WHERE `should_id` = ' . $should_id;
        $data = $this->db()->getAll($sql);
        $arr = array();
        if (!$type) {
            foreach ($data as $key => $value) {
                $arr[] = $value['apply_number'];
            }
        } else {
            $arr = $data;
        }
        return $arr;
    }

	//根据apply_id 查询BDD订单详细信息所有去除重复的
	function getDataOfapply_Id($apply_id,$flag=0)
	{
		$sql = "select * from ".$this->table()."  where apply_id='$apply_id' order by detail_id asc";
		$res = $this->db()->getAll($sql);
		if ($flag)
		{
			return $res;
		}
		$result = array();
		if ($res)
		{
			foreach ($res as $value)
			{
				$result[] = $value['kela_sn'];
			}
			return array_unique ($result);
		}
	}


}

?>