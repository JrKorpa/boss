<?php

/**
 *  -------------------------------------------------
 *   @file		: AppProcessorOperationModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-15 17:12:26
 *   @update	:
 *  -------------------------------------------------
 */
class AppProcessorOperationModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'app_processor_operation';
        $this->pk = 'id';
        $this->_prefix = '';
        $this->_dataObject = array("id" => " ",
            "processor_id" => "供应商ID",
            "name" => "供应商名称",
            "operation_type" => "操作类型：1保存，2提交,3审批，4拒绝,5修改，6删除，",
            "operation_content" => "操作内容",
            "create_time" => "操作时间",
            "create_user_id" => "操作人ID",
            "create_user" => "操作人");
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url AppProcessorOperationController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        $sql = "SELECT * FROM `" . $this->table() . "` WHERE 1 ";
        if(isset($where['name']) && $where['name'] != ''){
            $sql .= " and name like '%{$where['name']}%'";
        }
        if(isset($where['type']) && $where['type'] != ''){
            $sql .= " and operation_type = {$where['type']}";
        }
        if(isset($where['create_user']) && $where['create_user'] != ''){
            $sql .= " and create_user like '%{$where['create_user']}%'";
        }
        if(isset($where['start_time']) && ($where['start_time'] != '')){
            $sql .= " and create_time >= '{$where['start_time']}'";
        }
        if(isset($where['end_time']) && ($where['end_time'] != '')){
            $sql .= " and create_time <= '{$where['end_time']} 23:59:59'";
        }
        if(isset($where['processor_id']) && ($where['processor_id'] != '')){
            $sql .= " and processor_id = {$where['processor_id']}";
        }

        $sql .= " ORDER BY id DESC";
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }
    
    /**
     * 返回类型状态
     * @param type $param
     * @return string
     */
    public function getTypeList($param = '') {
        $data = array('1' => '保存', '2' => '提交', '3' => '审批', '4' => '拒绝');
        if ($param) {
            return $data[$param];
        }
        return $data;
    }
    
    public function getStatusList($param='') {
        $data = array(
            '1'=>'启用',
            '2'=>'停用'
        );
        if($param){
            return $data[$param];
        }
        return $data;
    }

}

?>