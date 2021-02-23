<?php
/**
 *  -------------------------------------------------
 *   @file		: ShipParcelView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-06 10:15:23
 *   @update	:
 *  -------------------------------------------------
 */
class ShipParcelView extends View
{
	protected $_id;
	protected $_express_id;
	protected $_express_sn;
	protected $_amount;
	protected $_num;
	protected $_shouhuoren;
	protected $_company_id;
	protected $_sales_channels;
	protected $_create_time;
	protected $_send_status;
	protected $_send_time;
	protected $_is_print;
	protected $_create_user;


	public function get_id(){return $this->_id;}
	public function get_express_sn(){return $this->_express_sn;}
	public function get_amount(){return $this->_amount;}
	public function get_num(){return $this->_num;}
	public function get_shouhuoren(){return $this->_shouhuoren;}
	public function get_company_id(){return $this->_company_id;}
	public function get_sales_channels(){return $this->_sales_channels;}
	public function get_create_time(){return $this->_create_time;}
	public function get_send_status(){return $this->_send_status;}
	public function get_send_time(){return $this->_send_time;}
	public function get_is_print(){return $this->_is_print;}
	public function get_create_user(){return $this->_create_user;}
	public function get_express_id(){
		return ($this->_id)?$this->_express_id:4;
	}

}
?>