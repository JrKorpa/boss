<?php
/**
 *  -------------------------------------------------
 *   @file		: AppApplyBillsView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-03 15:28:39
 *   @update	:
 *  -------------------------------------------------
 */
class AppApplyBillsView extends View
{
	protected $_id;
	protected $_apply_no;
	protected $_pay_number;
	protected $_detail_array;
	protected $_pay_type;
	protected $_bills_type;
	protected $_supplier_id;
	protected $_pay_total;
	protected $_adjust_total;
	protected $_apply_total;
	protected $_invoice_no;
	protected $_create_id;
	protected $_create_name;
	protected $_create_time;
	protected $_check_id;
	protected $_check_name;
	protected $_check_time;


	public function get_id(){return $this->_id;}
	public function get_apply_no(){return $this->_apply_no;}
	public function get_pay_number(){return $this->_pay_number;}
	public function get_detail_array(){return $this->_detail_array;}
	public function get_pay_type(){return $this->_pay_type;}
	public function get_bills_type(){return $this->_bills_type;}
	public function get_supplier_id(){return $this->_supplier_id;}
	public function get_pay_total(){return $this->_pay_total;}
	public function get_adjust_total(){return $this->_adjust_total;}
	public function get_apply_total(){return $this->_apply_total;}
	public function get_invoice_no(){return $this->_invoice_no;}
	public function get_create_id(){return $this->_create_id;}
	public function get_create_name(){return $this->_create_name;}
	public function get_create_time(){return $this->_create_time;}
	public function get_check_id(){return $this->_check_id;}
	public function get_check_name(){return $this->_check_name;}
	public function get_check_time(){return $this->_check_time;}

	/**
	 * 获取供应商名称
	 */
	function get_supplier_name($id){
		if(empty($id)){
			return false;
		}
		$keys=array("id");
		$vals=array($id);

		$ret=ApiModel::supplier_api($keys,$vals,'GetSupplierName');
		return $ret;
	}

	/**
	 * 获取供应商列表
	 */
	function get_supplier(){
		$ret=ApiModel::supplier_api(array(),array(),'GetSupplierList');
		return $ret;
	}

	public function getBillsType($id = 0){
		$type = [
			["id"=>1,'label'=>"新增"],
			["id"=>2,'label'=>"待审核"],
			["id"=>3,'label'=>"已驳回"],
			["id"=>4,'label'=>"已取消"],
			["id"=>5,'label'=>"待生成应付单"],
			["id"=>6,'label'=>"已生产应付单"],
		];
		if($id == 0){
			return $type;
		}else{
			return $type[($id-1)]['label'];
		}
	}

	public function getPayType($id=0){
		$type = [
			["id"=>1,'label'=>"代销借贷"],
			["id"=>2,'label'=>"成品采购"],
			["id"=>3,'label'=>"石包采购"]
		];
		if($id == 0){
			return $type;
		}else{
			return $type[($id-1)]['label'];
		}

	}



}
?>