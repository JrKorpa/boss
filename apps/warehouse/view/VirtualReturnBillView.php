<?php
/**
 *  -------------------------------------------------
 *   @file		: VirtualReturnBillView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-09-08 15:45:07
 *   @update	:
 *  -------------------------------------------------
 */
class VirtualReturnBillView extends View
{
	protected $_id;
	protected $_g_id;
	protected $_bill_status;
	protected $_bill_type;
	protected $_create_user;
	protected $_create_time;
	protected $_from_company_id;
	protected $_from_company_name;
	protected $_from_warehouse_id;
	protected $_from_warehouse_name;
	protected $_out_company_id;
	protected $_out_company_name;
	protected $_out_warehouse_id;
	protected $_out_warehouse_name;
	protected $_check_time;
	protected $_check_user;
	protected $_express_sn;
	protected $_remark;
    protected $_to_customer_id;
    protected $_exist_account_user;
    protected $_exist_account_time;


	public function get_id(){return $this->_id;}
	public function get_g_id(){return $this->_g_id;}
	public function get_bill_status(){return $this->_bill_status;}
	public function get_bill_type(){return $this->_bill_type;}
	public function get_create_user(){return $this->_create_user;}
	public function get_create_time(){return $this->_create_time;}
	public function get_from_company_id(){return $this->_from_company_id;}
	public function get_from_company_name(){return $this->_from_company_name;}
	public function get_from_warehouse_id(){return $this->_from_warehouse_id;}
	public function get_from_warehouse_name(){return $this->_from_warehouse_name;}
	public function get_out_company_id(){return $this->_out_company_id;}
	public function get_out_company_name(){return $this->_out_company_name;}
	public function get_out_warehouse_id(){return $this->_out_warehouse_id;}
	public function get_out_warehouse_name(){return $this->_out_warehouse_name;}
	public function get_check_time(){return $this->_check_time;}
	public function get_check_user(){return $this->_check_user;}
	public function get_express_sn(){return $this->_express_sn;}
	public function get_remark(){return $this->_remark;}
    public function get_to_customer_id(){return $this->_to_customer_id;}
    public function get_exist_account_user(){return $this->_exist_account_user;}
    public function get_exist_account_time(){return $this->_exist_account_time;}
    
    public function get_company_list(){
        $model = new CompanyModel(1);
        $list = $model->getCompanyTree();
        return $list;
    }
}
?>