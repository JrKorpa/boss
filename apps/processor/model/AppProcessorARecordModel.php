<?php

/**
 *  -------------------------------------------------
 *   @file		: AppProcessorRecordModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-15 17:11:58
 *   @update	:
 *  -------------------------------------------------
 */
class AppProcessorARecordModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'app_processor_record';
        $this->pk = 'id';
        $this->_prefix = '';
        $this->_dataObject = array("id" => " ",
            "info_id" => "供应商ID",
            "code" => "供应商编码",
            "name" => "供应商名称",
            "business_scope" => "经营范围：1:黄金,2:K金素金,3:PT素金,4:K金钻石镶嵌品,5:PT钻石镶嵌品,6:成品钻,7:彩宝饰品,8:银饰品,9:其他",
            "is_open" => "是否开通系统:1是0否",
            "password" => "密码",
            "business_license" => "营业执照号码",
            "tax_registry_no" => "税务登记证号",
            "business_license_region" => "营业执照地址:省，市，区",
            "business_license_address" => "营业执照地址",
            "pro_region" => "取货地址:省，市，区",
            "pro_address" => "取货地址",
            "cycle" => "出货周期",
            "pay_type" => "结算方式：1现金,2转账,3支票",
            "tax_invoice" => "增值税发票",
            "tax_point" => "税点",
            "balance_type" => "付款周期：",
            "balance_day" => "周期付款(天数)：",
            "purchase_amount" => "采购额度",
            "pro_contact" => "公司联系人",
            "pro_phone" => "公司联系电话",
            "pro_qq" => "公司联系qq",
            "contact" => "BDD紧急联系人",
            "kela_phone" => "BDD紧急联系电话",
            "kela_qq" => "BDD紧急联系qq",
            "bank_name" => "开户银行",
            "account_name" => "户名",
            "account" => "银行账户",
            "is_invoice" => "此供应商是否有能力开发票:1开，0不开",
            "pro_email" => "供货商邮箱",
            "balance_day" => "结算日期",
            "pid" => "供应商分类类型",
            "company" => "加工商所属公司",
            "status" => "状态：1启用0停用",
            "create_time" => "创建时间",
            "create_id" => "创建人ID",
            "create_user" => "创建人",
            "check_status" => "审批状态",
            "department_id" => "部门id",
            "pact_doc" => "合同附件",
            "license_jpg" => "营业执照附件",
            "tax_jpg" => "税务登记证附件",
            "info" => "备注");
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url AppProcessorRecordController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true,$search=1) {
        $sql = "SELECT * FROM `" . $this->table() . "` WHERE 1 and is_A_company = 'Y'";
        if(isset($where['name']) && $where['name'] != ''){
            $sql .= " and `name` like '%{$where['name']}%'";
        }
        if(isset($where['code']) && $where['code'] != ''){
            $sql .= " and `code` like '%{$where['code']}%'";
        }
        if(isset($where['pro_contact']) && $where['pro_contact'] != ''){
            $sql .= " and `pro_contact` like '%{$where['pro_contact']}%'";
        }
        if(isset($where['check_status']) && $where['check_status'] != ''){
            $sql .= " and check_status = {$where['check_status']}";
        }
        if(isset($where['status']) && $where['status'] != ''){
            $sql .= " and status = {$where['status']}";
        }
        if(isset($where['business_scope']) && $where['business_scope'] != ''){
            $sql .= " and business_scope LIKE '%{$where['business_scope']}%'";
        }
        $str = '';
		/*
        //创建人
        if(in_array($_SESSION['userId'],$this->getAllcreateID()) && $_SESSION['userType']!=1){
            $str .= "`create_id` = ".$_SESSION['userId']." OR ";
        }
        //审核人
        if(in_array($_SESSION['userId'],$this->getAllCheckUser()) && $_SESSION['userType']!=1){
            $str .= "`check_user` = ".$_SESSION['userId']." OR ";
        }
		*/
        if($search ==2){
            $sql .= " AND ((check_status in(1,2) AND create_id=".$_SESSION['userId'].") OR check_user=".$_SESSION['userId'].")";
        }

        if($str)
        {
            $str = rtrim($str,"OR ");//这个空格很重要
            $sql .= " AND ".$str;
        }
        $sql .= " ORDER BY `id` DESC";
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }

    function getCheckStatusList($param='') {
        $data = array(
            '1' => '保存',
            '2' => '提交',
            '3' => '审批中',
            '4' => '驳回',
            '7'=>'通过');
        if ($param) {
            return $data[$param];
        }
        return $data;
    }

    function getStatusList($param='') {
        $data = array('1' => '启用', '2' => '停用');
        if ($param) {
            return $data[$param];
        }
        return $data;
    }

    /**
     * 写入主表
     * @return bool|last|null
     * @throws ObjectException
     */
    public function toInfo(){
        $newdo = $this->getDataObject();
        $newdo['status'] = 1;//审核通过启用
        $newdo['create_id'] = ($newdo['update_id'])?$newdo['update_id']:$newdo['create_id'];
        $newdo['create_user'] = ($newdo['update_user'])?$newdo['update_user']:$newdo['create_user'];
        $newdo['create_time'] = date('Y-m-d H:i:s',time());

        $recorView = new AppProcessorRecordView($this);
        $newdo['business_scope'] = $recorView->get_business_zhCN($newdo['business_scope']);
        if($newdo['is_open'] == 0){unset($newdo['password']);}
        unset($newdo['id']);
        unset($newdo['check_status']);  //审核状态
        unset($newdo['department_id']); //申请部门
        unset($newdo['audit_plan']);    //审核进度
        unset($newdo['update_id']);     //审核进度
        unset($newdo['update_user']);   //审核进度
        unset($newdo['update_time']);   //审核进度
        unset($newdo['check_user']);    //审核人
        unset($newdo['oldsys_id']);

        if($newdo['info_id']){         //回写ID
            $infoModel = new AppProcessorInfoModel($newdo['info_id'], 14);
            $newdo['id'] = $newdo['info_id'];
            unset($newdo['info_id']);
            $olddo = $infoModel->getDataObject();
            if(empty($olddo)){$olddo = array();}
            $res = $infoModel->saveData($newdo,$olddo);
        }else{
            unset($newdo['info_id']);
            $olddo = array();
            $infoModel = new AppProcessorInfoModel(14);
            $res = $infoModel->saveData($newdo,$olddo);
            $this->setValue('info_id',$res);
        }
        if($res){
            $this->setValue('check_status',7);
        }
        return $res;
    }

    public function checkSupplierName($name){
        $sql = "SELECT count(*) FROM ".$this->table()." WHERE `name` ='".$name."'";
        $res = $this->db()->getOne($sql);
        return $res;
    }
    public function checkSupplierCode($code){
        $sql = "SELECT count(*) FROM ".$this->table()." WHERE `name` ='".$code."'";
        $res = $this->db()->getOne($sql);
        return $res;
    }

    /**
     * 获取所有创建人
     */
    public function getAllcreateID(){
        $sql = "SELECT `create_id`,`create_user` FROM ".$this->table();
        $res = $this->db()->getAll($sql);
        $res = array_column($res,'create_id','create_user');
        return $res;
    }

    /**
     * 获取所有审批人
     */
    public function getAllCheckUser(){
        $sql = "SELECT `user_id` FROM `app_processor_user` GROUP BY `user_id`";
        $res = $this->db()->getAll($sql);
        $res = array_column($res,'user_id');
        return $res;
    }

    public function getHasCheck(){
        $sql = "SELECT `record_id` FROM `app_processor_audit` WHERE `user_id` = '".$_SESSION['userId']."'";
        $res = $this->db()->getAll($sql);
        $res = array_column($res,'record_id');
        return $res;
    }

    /*
    * 取消申请
    *
    */

    public function delProcessorRecordById($id){
        $sql = "delete from kela_supplier.app_processor_record where id=".$id;
        return $this->db()->query($sql);

    }




}

?>