<?php
/**
 *  -------------------------------------------------
 *   @file		: AppApplyRealPayView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-04 19:20:56
 *   @update	:
 *  -------------------------------------------------
 */
class AppApplyRealPayView extends View
{
	protected $_id;
	protected $_real_number;
	protected $_apply_no;
	protected $_bank_name;
	protected $_bank_serial;
	protected $_account_name;
	protected $_bank_account;
	protected $_pay_time;
	protected $_supplier_id;
	protected $_supplier_name;
	protected $_pay_total;
	protected $_create_id;
	protected $_create_name;
	protected $_create_time;


	public function get_id(){return $this->_id;}
	public function get_real_number(){return $this->_real_number;}
	public function get_apply_no(){return $this->_apply_no;}
	public function get_bank_name(){return $this->_bank_name;}
	public function get_bank_serial(){return $this->_bank_serial;}
	public function get_account_name(){return $this->_account_name;}
	public function get_bank_account(){return $this->_bank_account;}
	public function get_pay_time(){return $this->_pay_time;}
	public function get_supplier_id(){return $this->_supplier_id;}
	public function get_supplier_name(){return $this->_supplier_name;}
	public function get_pay_total(){return $this->_pay_total;}
	public function get_create_id(){return $this->_create_id;}
	public function get_create_name(){return $this->_create_name;}
	public function get_create_time(){return $this->_create_time;}

	/**
	 * 获取供应商列表
	 */
	function get_supplier(){
		$ret=ApiModel::supplier_api(array(),array(),'GetSupplierList');
		return $ret;
	}

}
?>