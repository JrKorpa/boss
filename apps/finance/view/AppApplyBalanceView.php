<?php
/**
 *  -------------------------------------------------
 *   @file		: AppApplyBalanceView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-04 12:54:28
 *   @update	:
 *  -------------------------------------------------
 */
class AppApplyBalanceView extends View
{
	protected $_id;
	protected $_balance_no;
	protected $_apply_array;
	protected $_supplier_id;
	protected $_supplier_name;
	protected $_total_sys;
	protected $_total_dev;
	protected $_total_real;
	protected $_pay_total;
	protected $_pay_type;
	protected $_pay_status;
	protected $_balance_status;
	protected $_create_id;
	protected $_create_name;
	protected $_create_time;
	protected $_check_id;
	protected $_check_name;
	protected $_check_time;


	public function get_id(){return $this->_id;}
	public function get_balance_no(){return $this->_balance_no;}
	public function get_apply_array(){return $this->_apply_array;}
	public function get_supplier_id(){return $this->_supplier_id;}
	public function get_supplier_name(){return $this->_supplier_name;}
	public function get_total_sys(){return $this->_total_sys;}
	public function get_total_dev(){return $this->_total_dev;}
	public function get_total_real(){return $this->_total_real;}
	public function get_pay_total(){return $this->_pay_total;}
	public function get_pay_type(){return $this->_pay_type;}
	public function get_pay_status(){return $this->_pay_status;}
	public function get_balance_status(){return $this->_balance_status;}
	public function get_create_id(){return $this->_create_id;}
	public function get_create_name(){return $this->_create_name;}
	public function get_create_time(){return $this->_create_time;}
	public function get_check_id(){return $this->_check_id;}
	public function get_check_name(){return $this->_check_name;}
	public function get_check_time(){return $this->_check_time;}

	/**
	 * 获取付款状态
	 */
	public function getPayStatus($s = 0){
		$status = [
			['id'=>1,'label'=>'未付款'],
			['id'=>2,'label'=>'付部分'],
			['id'=>3,'label'=>'已付款'],
		];
		if($s != 0){
			return $status[($s-1)]['label'];
		}else{
			return $status;
		}

	}

	/**
	 * 获取供应商列表
	 */
	function get_supplier(){
		$ret=ApiModel::supplier_api(array(),array(),'GetSupplierList');
		return $ret;
	}

	/**
	 * 单据状态:1=待审核,2=已审核,3=已取消
	 */
	public function getBalanceStatus($s=0){
		$status = [
			['id'=>1,'label'=>'待审核'],
			['id'=>2,'label'=>'已审核'],
			['id'=>3,'label'=>'已取消'],
		];
		if($s !== false){
			foreach ($status as $v) {
				if($v['id'] == $s){
					return	$v['label'];
				}
			}
		}
		return $status;
	}

	/**
	 * 获取供应商付款周期
	 */
	public function getSupplierPayType($id){
		$model = new AppApplyBalanceModel($id,29);
		$payinfo = $model->getSupplierPay($id);
		return $payinfo['balance_type'];
	}

}
?>