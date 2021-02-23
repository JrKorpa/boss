<?php
/**
 *  -------------------------------------------------
 *   @file		: AppProcessorBuyerView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-29 11:16:57
 *   @update	:
 *  -------------------------------------------------
 */
class AppProcessorBuyerView extends View
{
	protected $_id;
	protected $_supplier_id;
	protected $_buyer_id;
	protected $_buyer_name;
	protected $_buyer_account;
	protected $_buyer_tel;
	protected $_buyer_papers;
	protected $_create_id;
	protected $_create_time;
	protected $_is_deleted;

	public function get_id(){return $this->_id;}
	public function get_supplier_id(){return $this->_supplier_id;}
	public function get_buyer_id(){return $this->_buyer_id;}
	public function get_buyer_name(){return $this->_buyer_name;}
	public function get_buyer_account(){return $this->_buyer_account;}
	public function get_buyer_tel(){return $this->_buyer_tel;}
	public function get_buyer_papers(){return $this->_buyer_papers;}
	public function get_create_id(){return $this->_create_id;}
	public function get_create_time(){return $this->_create_time;}
	public function get_is_deleted(){return $this->_is_deleted;}

	public function get_kela_user(){
		$user = new UserModel(2);
		return $user->getUserInfo();
	}

	public function set_supplier_id($supplier_id){
		$this->_supplier_id = $supplier_id;
	}


}
?>