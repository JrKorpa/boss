<?php
/**
 *  -------------------------------------------------
 *   @file		: AppProcessorTakerView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-30 10:11:14
 *   @update	:
 *  -------------------------------------------------
 */
class AppProcessorTakerView extends View
{
	protected $_id;
	protected $_supplier_id;
	protected $_taker_id;
	protected $_taker_account;
	protected $_taker_name;
	protected $_taker_gender;
	protected $_taker_tel;
	protected $_taker_papers;
	protected $_create_id;
	protected $_create_time;
	protected $_is_deleted;


	public function get_id(){return $this->_id;}
	public function get_supplier_id(){return $this->_supplier_id;}
	public function get_taker_id(){return $this->_taker_id;}
	public function get_taker_account(){return $this->_taker_account;}
	public function get_taker_name(){return $this->_taker_name;}
	public function get_taker_gender(){return $this->_taker_gender;}
	public function get_taker_tel(){return $this->_taker_tel;}
	public function get_taker_papers(){return $this->_taker_papers;}
	public function get_create_id(){return $this->_create_id;}
	public function get_create_time(){return $this->_create_time;}
	public function get_is_deleted(){return $this->_is_deleted;}

	public function get_kela_user(){
		$user = new UserModel(1);
		return $user->getUserInfo();
	}

	public function set_supplier_id($supplier_id){
		$this->_supplier_id = $supplier_id;
	}

}
?>