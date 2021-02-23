<?php

/**
 *  -------------------------------------------------
 *   @file		: AppProcessorRecordView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-15 17:11:58
 *   @update	:
 *  -------------------------------------------------
 */
class AppProcessorRecordView extends View {

    protected $_id;
    protected $_info_id;
    protected $_code;
    protected $_name;
    protected $_business_scope;
    protected $_is_open;
    protected $_password;
    protected $_business_license;
    protected $_tax_registry_no;
    protected $_business_license_region;
    protected $_business_license_address;
    protected $_pro_region;
    protected $_pro_address;
    protected $_cycle;
    protected $_pay_type;
    protected $_tax_invoice;
    protected $_tax_point;
    protected $_balance_type;
    protected $_balance_day;
    protected $_purchase_amount;
    protected $_pro_contact;
    protected $_pro_phone;
    protected $_pro_qq;
    protected $_contact;
    protected $_kela_phone;
    protected $_kela_qq;
    protected $_bank_name;
    protected $_account_name;
    protected $_account;
    protected $_is_invoice;
    protected $_pro_email;
    protected $_pid;
    protected $_company;
    protected $_status;
    protected $_create_id;
    protected $_create_time;
    protected $_create_user;
    protected $_info;
    protected $_check_status;
    protected $_department_id;
    protected $_audit_plan;
    protected $_business_zhCN;

    protected $_pact_doc;
    protected $_license_jpg;
    protected $_tax_jpg;


    public function get_id() {return $this->_id;}
    public function get_info_id() {return $this->_info_id;}
    public function get_audit_plan() {return $this->_audit_plan;}
    public function get_code() {return $this->_code;}
    public function get_name() {return $this->_name;}
    public function get_business_scope() {return $this->_business_scope;}
    public function get_is_open() {return ($this->_id)?$this->_is_open:0;}
    public function get_password() {return $this->_password;}
    public function get_business_license() {return $this->_business_license;}
    public function get_tax_registry_no() {return $this->_tax_registry_no;}
    public function get_business_license_region() {return $this->_business_license_region;}
    public function get_business_license_address() {return $this->_business_license_address;}
    public function get_pro_region() {return $this->_pro_region;}
    public function get_pro_address() {return $this->_pro_address;}
    public function get_cycle() {return $this->_cycle;}
    public function get_pay_type() {
		if($this->_pay_type)
		{
			return explode(',',$this->_pay_type);
		}
		return array();
	}
	public function get_pay_type_old() {
		return $this->_pay_type;
	}
    public function get_tax_invoice() {return $this->_tax_invoice;}
    public function get_tax_point() {return $this->_tax_point;}
    public function get_balance_type() {return $this->_balance_type;}
    public function get_balance_day() {return $this->_balance_day;}
    public function get_purchase_amount() {return $this->_purchase_amount;}
    public function get_pro_contact() {return $this->_pro_contact;}
    public function get_pro_phone() {return $this->_pro_phone;}
    public function get_pro_qq() {return $this->_pro_qq;}
    public function get_contact() {return $this->_contact;}
    public function get_kela_phone() {return $this->_kela_phone;}
    public function get_kela_qq() {return $this->_kela_qq;}
    public function get_bank_name() {return $this->_bank_name;}
    public function get_account_name() {return $this->_account_name;}
    public function get_account() {return $this->_account;}
    public function get_is_invoice() {return $this->_is_invoice;}
    public function get_pro_email() {return $this->_pro_email;}
    public function get_status() {return ($this->_id)? $this->_status:0;}
    public function get_create_time() {return $this->_create_time;}
    public function get_create_id() {return $this->_create_id;}
    public function get_create_user() {return $this->_create_user;}
    public function get_info() {return $this->_info;}
    public function get_check_status() {return $this->_check_status;}
    public function get_department_id() {return $this->_department_id;}

    public function get_pact_doc() {return $this->_pact_doc;}
    public function get_license_jpg() {return $this->_license_jpg;}
    public function get_tax_jpg() {return $this->_tax_jpg;}
    public function getStatusList() {
        $model = new AppProcessorRecordModel(13);
        return $model->getStatusList();
    }

    public function getCheckStatusList() {
        $model = new AppProcessorRecordModel(13);
        return $model->getCheckStatusList();
    }

    public function get_business_region_zhCN($str){
        $region = new RegionModel(1);
        $address = $region->getAddreszhCN($str);
        return $address;
    }

    public function get_show_point(){
        $tax_point = $this->_tax_point;
        if(!empty($tax_point) && !is_numeric($tax_point)){
            $point = explode(',',$tax_point);
            $res = '';
            foreach ($point as $v) {
                $p = explode('|',$v);
                $res .= $p[1].":".$p[2].",";
            }
            $res = substr($res,0,-1);
            return $res;
        }else{
          return $this->_tax_point;
        }
    }

    /**
     * 经营范围[中文]
     * @param $business_scope
     * @return string
     */
    public function get_business_zhCN($business_scope){

        $data = explode(',',$business_scope);
        $all = $this->getScopeList();
        $str ='';
        foreach ($data as $k) {
            foreach ($all as $v) {
                if($k == $v['id']){
                    $str .=$v['name'].',';
                }
            }
        }
        $business_zhCN = substr($str,0,-1);
        return $business_zhCN;
    }


    /**
     * 经营范围
     * @return array
     */
    public function getScopeList() {
        $data = [
            ['id'=>'1','name' => '成品钻'],
            ['id'=>'2','name' => '黄金'],
            ['id'=>'3','name' => 'K金素金'],
            ['id'=>'4','name' => 'PT素金'],
            ['id'=>'5','name' => 'K金钻石镶嵌品'],
            ['id'=>'6','name' => 'PT钻石镶嵌品'],
            ['id'=>'7','name' => '彩宝饰品'],
            ['id'=>'8','name' => '银饰品'],
            ['id'=>'9','name' => '其他']
        ];
        return $data;
    }

    /**
     * 结算方式
     * @return array
     */
    public function getPayTypeList($ids=array()) {
        $data = [
            ['id'=>1,'name' => '现金'],
            ['id'=>2,'name' => '转账'],
            ['id'=>3,'name' => '支票']
        ];
        if(!empty($ids)){
			//edit by zhangruiying修改查看时多个支付方式只显示一个BUG
			$str='';
			foreach($ids as $id)
			{
				 $str.=$data[($id-1)]['name'].',';
			}
			return rtrim($str,',');
			//add end;
			//return $data[($id-1)]['name']

        }
        return $data;
    }

    /**
     * 增值税发票
     * @return array
     */
    public function getTaxInvoiceList() {
        $data = [
            ['id'=>0.17,'name' => '17%'],
            ['id'=>0.11,'name' => '11%'],
            ['id'=>0.06,'name' => '6%'],
            ['id'=>0.03,'name' => '3%'],
            ['id'=>0.00,'name' => '没有']
        ];
        return $data;
    }

    /**
     * 获取省市区联动数据
     * @return type
     */
    public function getProvinceOption() {
        $model = new RegionModel(1);
        return $model->getRegion(1);
    }

    public function get_area($id) {
        $is_have = $this->_business_license_region;
        if($is_have){
            $data = explode(',', $is_have);
            return $data[$id];
        }
        return '';
    }

    public function get_pro_area($id) {
        $is_have = $this->_pro_region;
        if($is_have){
            $data = explode(',', $is_have);
            return $data[$id];
        }
        return '';
    }

    /**
     * getProcess 获取流程ID
     * @author   yangxiaotong
     * @return int|bool
     */
    public function getProcess(){

        $business_scope = $this->get_business_scope();

        if(strstr($business_scope,'1') === false){
            $business = '[非成品钻]';//非成品钻
        }elseif(strlen($business_scope) == 1){
            $business = '[成品钻]';//成品钻
        }else{
            $business = '[成品钻/非成品钻]';//
        }
        $department_id = $this->get_department_id();

        //获取审批流程ID（array）;
        $sql = "SELECT `id` FROM `app_processor_process` WHERE `business_type` = '".$business."' AND `department_id` = '".$department_id."' ORDER BY `id`";
        $res = DB::cn(13)->getALL($sql);
        if(!empty($res) && is_array($res)){
            foreach ($res as $v) {
                $sql = 'SELECT `process_id` FROM `app_processor_audit` WHERE `record_id` = '.$this->_id.' AND `process_id` = '.$v['id'];
                $process = DB::cn(13)->getOne($sql);
            }
            $res = ($process)?$process:array_pop($res)['id'];
            return $res;
        }else{
            return false;
        }
    }

    /**
     * 获取审核人员
     * @return array|bool
     */
    public function getCheckUser(){
        $process_id = $this->getProcess();
        if($process_id){
            $sql = 'SELECT `user_id`,`user_order` FROM `app_processor_user` WHERE `process_id` = '.$process_id.' ORDER BY `user_order`';
            $users = DB::cn(13)->getAll($sql);
            foreach ($users as $k => $v) {
                $sql = 'SELECT `real_name` FROM `user` WHERE `id` = '.$v['user_id'];
                $users[$k]['real_name'] = DB::cn(1)->getOne($sql);
            }
            return $users;
        }else{
          return false;
        }

    }

    /**
     * 获取申请部门
     * @return mixed
     */
    public function getDepartName($id =0){
        if($id==0){
            $department_id = $this->get_department_id();
        }else{
            $department_id = $id;
        }
        $sql = 'SELECT `name` FROM `department` WHERE `id`='.$department_id;
        $name = DB::cn(1)->getOne($sql);
        return $name;
    }

    /**
     * 获取审批状态
     */
    public function getAuditStatus($user,$record_id){
        foreach ($user as $k => $v) {
            $sql = 'SELECT `audit_status` FROM `app_processor_audit` WHERE `record_id` ='.$record_id.' AND `user_id` ='.$v['user_id']." order by id desc limit 0,1";
            $user[$k]['audit_status'] = DB::cn(13)->getOne($sql);
        }
        return $user;
    }

    /**
     * 获取当前用户所属部门
     */
    public function getUserDepart(){
        $userModel = new UserModel(1);
        $dept_arr = $userModel->getUserDept($_SESSION['userId']);
        foreach ($dept_arr as $k => $v) {
            $sql = 'SELECT `id`,`name` AS `label` FROM `department` WHERE `id` = '.$v;
            $data[] = DB::cn(1)->getRow($sql);
        }
        return $data;
    }


    /**
     * 获取当前审批人
     * @param $p_id 审批流程
     * @param $order 审批顺序
     */
    public function getCurrentCheckUser($p_id,$order){
        $sql = "SELECT `user_id` FROM `app_processor_user` WHERE `process_id` = '".$p_id."' AND `user_order` = '".$order."'";
        $res = DB::cn(13)->getOne($sql);
        return $res;
    }

	
	public function getProcessorList() {
        $sql = "SELECT `id`,`name`,`status` FROM `app_processor_info` WHERE `status`=1";
        $model = $this->getModel();
        $arr = $model->db()->getAll($sql);
        return $arr;
    }
    
	//没有条件限制，全部供应商读出来选择
    public function getProcessorAll() {
        $sql = "SELECT `id`,`name`,`status` FROM `app_processor_record` where is_A_company = 'N' order by id desc ";
        $model = $this->getModel();
        $arr = $model->db()->getAll($sql);
        return $arr;
    }

}

?>