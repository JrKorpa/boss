<?php

/**
 *  -------------------------------------------------
 *   @file		: AppProcessorInfoView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-07 17:43:19
 *   @update	:
 *  -------------------------------------------------
 */
class AppProcessorAInfoView extends View {

    protected $_id;
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
    protected $_status;
    protected $_create_id;
    protected $_create_time;
    protected $_create_user;
    protected $_info;

    protected $_pact_doc;
    protected $_license_jpg;
    protected $_tax_jpg;

    public function get_id() {
        return $this->_id;
    }

    public function get_code() {
        return $this->_code;
    }

    public function get_name() {
        return $this->_name;
    }

    public function get_business_scope() {
        return $this->_business_scope;
    }

    public function get_is_open() {
        return $this->_is_open;
    }

    public function get_password() {
        return $this->_password;
    }

    public function get_business_license() {
        return $this->_business_license;
    }

    public function get_tax_registry_no() {
        return $this->_tax_registry_no;
    }

    public function get_business_license_region() {
        return $this->_business_license_region;
    }

    public function get_business_license_address() {
        return $this->_business_license_address;
    }

    public function get_pro_region() {
        return $this->_pro_region;
    }

    public function get_pro_address() {
        return $this->_pro_address;
    }

    public function get_cycle() {
        return $this->_cycle;
    }

    public function get_pay_type() {
        $type = explode(',',$this->_pay_type);
        $pay = '';
        foreach ($type as $v) {
            $pay .= $this->getPayTypeList($v);
        }
        return $pay;
    }

    public function get_tax_invoice() {
        return $this->_tax_invoice;
    }

    public function get_tax_point() {
        return $this->_tax_point;
    }

    public function get_purchase_amount() {return $this->_purchase_amount;}
    public function get_balance_type() {return $this->_balance_type;}

    public function get_pro_contact() {
        return $this->_pro_contact;
    }

    public function get_pro_phone() {
        return $this->_pro_phone;
    }

    public function get_pro_qq() {
        return $this->_pro_qq;
    }

    public function get_contact() {
        return $this->_contact;
    }

    public function get_kela_phone() {
        return $this->_kela_phone;
    }

    public function get_kela_qq() {
        return $this->_kela_qq;
    }

    public function get_bank_name() {
        return $this->_bank_name;
    }

    public function get_account_name() {
        return $this->_account_name;
    }

    public function get_account() {
        return $this->_account;
    }

    public function get_is_invoice() {
        return $this->_is_invoice;
    }

    public function get_pro_email() {
        return $this->_pro_email;
    }

    public function get_balance_day() {
        return $this->_balance_day;
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

    public function get_status() {return $this->_status;}
    public function get_create_id(){return $this->_create_id;}
    public function get_create_time(){return $this->_create_time;}
    public function get_create_user() {return $this->_create_user;}
    public function get_info() {return $this->_info;}

    public function get_pact_doc() {return $this->_pact_doc;}
    public function get_license_jpg() {return $this->_license_jpg;}
    public function get_tax_jpg() {return $this->_tax_jpg;}
    
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

    public function getProcessorTypeList() {
        $model = new AppProcessorTypeModel(13);
        return $model->getProcessorTypeList();
    }

    public function getScopeList() {
        $data = array(
            array('id'=>'1','name' => '成品钻'),
            array('id'=>'2','name' => '黄金'),
            array('id'=>'3','name' => 'K金素金'),
            array('id'=>'4','name' => 'PT素金'),
            array('id'=>'5','name' => 'K金钻石镶嵌品'),
            array('id'=>'6','name' => 'PT钻石镶嵌品'),
            array('id'=>'7','name' => '彩宝饰品'),
            array('id'=>'8','name' => '银饰品'),
            array('id'=>'9','name' => '其他')
        );
        return $data;
    }
    
    public function getStatusList() {
        $model = new AppProcessorInfoModel(13);
        $data = $model->getStatusList();
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
    
    /**
     * 结算方式
     * @return array
     */
    public function getPayTypeList($id = false) {
        $data = array(
            array('id'=>1,'name' => '现金'),
            array('id'=>2,'name' => '转账'),
            array('id'=>3,'name' => '支票')
        );
        return ($id)?$data[($id-1)]['name']:$data;
    }
    
    /**
     * 增值税发票
     * @return array
     */
    public function getTaxInvoiceList() {
        $data = array(
            array('name' => '17%'),
            array('name' => '11%'),
            array('name' => '6%'),
            array('name' => '3%'),
            array('name' => '没有')
        );
        return $data;
    }

    /**
     * 付款周期
     * @return array
     */
    public function getBalanceTypeList() {
        $data = array(
            array('id'=>1,'name' => '日结'),
            array('id'=>2,'name' => '月结'),
            array('id'=>3,'name' => '货到付款')
        );
        return $data;
    }
    
    /**
     * 获取所有供应商
     * @return mixed
     */
    public function get_other($id){
        $sql = 'SELECT `id`,`name`,`status` FROM `app_processor_info` as `i` WHERE NOT EXISTS (SELECT `supplier_id` FROM `app_processor_group` AS `g` WHERE `g`.`supplier_id` = i.`id`) AND `id` <> '.$id;
        return DB::cn(14)->getAll($sql);
    }

    /**
     * 获取关联供应商信息
     * @用于渲染页面
     */
    public function get_group($group_id){
        $sql = 'select 473 as supplier_id union SELECT `supplier_id` FROM `app_processor_group` WHERE `group_id` ='.$group_id;
        $group = DB::cn(14)->getAll($sql);

        $suppliers = array();
        if(!empty($group)){
            foreach ($group as $v) {
//                $sql = 'SELECT `name` FROM `app_processor_info` WHERE `id` ='.$v['supplier_id'];
//                $suppliers[$v['supplier_id']]['name'] = DB::cn(14)->getOne($sql);
//                $sql = 'SELECT `status` FROM `app_processor_info` WHERE `id` ='.$v['supplier_id'];
//                $suppliers[$v['supplier_id']]['status'] = DB::cn(14)->getOne($sql);
                $sql = 'SELECT `name`,`status` FROM `app_processor_info` WHERE `id` ='.$v['supplier_id'];
                $tmp = DB::cn(14)->getRow($sql);
                $suppliers[$v['supplier_id']]['name'] = $tmp['name'];
                $suppliers[$v['supplier_id']]['status'] = $tmp['status'];
            }
            return $suppliers;
        }else{
            return false;
        }
    }


    public function getOpraUname(){
        $sql = "SELECT `opra_uname` FROM `product_factory_oprauser` GROUP BY `opra_uname`";
        $model = $this->getModel();
        $res = $model->db()->getAll($sql);
        $res = array_column($res,'opra_uname');
        return array_filter($res);
    }
    
    
    public function getProcessorList() {
        $sql = "SELECT `id`,`name`,`status` FROM `app_processor_info` WHERE `status`=1";
        $model = $this->getModel();
        $arr = $model->db()->getAll($sql);
        return $arr;
    }
    

}

?>