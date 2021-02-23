<?php
/**
 *  -------------------------------------------------
 *   @file		: PaymentView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-19 10:15:58
 *   @update	:
 *  -------------------------------------------------
 */
class PaymentView extends View
{
	protected $_id;
	protected $_pay_code;
	protected $_pay_name;
	protected $_pay_fee;
	protected $_pay_desc;
	protected $_pay_order;
	protected $_pay_config;
	protected $_is_enabled;
	protected $_is_cod;
	protected $_is_display;
	protected $_is_web;
//	protected $_add_time;
	protected $_is_deleted;
	protected $_is_online;
	protected $_is_offline;
    protected $_is_order;
    protected $_is_balance;
    protected $_is_beian;
    protected $_is_pfls;


	public function get_id(){return $this->_id;}
	public function get_pay_code(){return $this->_pay_code;}
	public function get_pay_name(){return $this->_pay_name;}
	public function get_pay_fee(){return ($this->_id)?$this->_pay_fee:0;}
	public function get_pay_desc(){return $this->_pay_desc;}
	public function get_pay_order(){return $this->_pay_order;}
	public function get_pay_config(){return $this->_pay_config;}
	public function get_is_enabled(){return ($this->_id)?$this->_is_enabled:1;}
	public function get_is_cod(){return $this->_is_cod;}
	public function get_is_display(){return ($this->_id)?$this->_is_display:1;}
	public function get_is_web(){return $this->_is_web;}
//	public function get_add_time(){return $this->$_add_time;}
	public function get_is_deleted(){return $this->_is_deleted;}
	public function get_is_online(){return $this->_is_online;}
	public function get_is_offline(){return $this->_is_offline;}
	public function get_is_order(){return $this->_is_order;}
    public function get_is_balance(){return $this->_is_balance;}
    public function get_is_beian(){return $this->_is_beian;}
    public function get_is_pfls(){return $this->_is_pfls;}

	public function getPayNameById($id){
		$sql = "SELECT `pay_name` FROM `payment` WHERE `id` = '".$id."'";
		$model = $this->getModel();
		$name = $model->db()->getOne($sql);
		return $name;
	}


}
?>