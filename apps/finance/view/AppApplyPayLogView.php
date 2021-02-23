<?php
/**
 *  -------------------------------------------------
 *   @file		: AppApplyPayLogView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-05 16:56:14
 *   @update	:
 *  -------------------------------------------------
 */
class AppApplyPayLogView extends View
{
	protected $_id;
	protected $_order_type;
	protected $_order_id;
	protected $_order_no;
	protected $_handle_type;
	protected $_content;
	protected $_create_id;
	protected $_create_name;
	protected $_create_time;


	public function get_id(){return $this->_id;}
	public function get_order_type(){return $this->_order_type;}
	public function get_order_id(){return $this->_order_id;}
	public function get_order_no(){return $this->_order_no;}
	public function get_handle_type(){return $this->_handle_type;}
	public function get_content(){return $this->_content;}
	public function get_create_id(){return $this->_create_id;}
	public function get_create_name(){return $this->_create_name;}
	public function get_create_time(){return $this->_create_time;}

	public function OrderType($id = 0){
		$type = [
			['id'=>1,'label'=>'申请单'],
			['id'=>2,'label'=>'调整单'],
			['id'=>3,'label'=>'应付单'],
			['id'=>4,'label'=>'实付单'],
		];
		return ($id)?$type[($id-1)]['label']:$type;
	}

	/**
	 * 1=生成,2调整,3=审批,4=付款
	 */
	public function HandleType($id = 0){
		$type = [
			['id'=>1,'label'=>'生成'],
			['id'=>2,'label'=>'调整'],
			['id'=>3,'label'=>'审批'],
			['id'=>4,'label'=>'付款']
		];
		return ($id)?$type[($id-1)]['label']:$type;
	}


}
?>