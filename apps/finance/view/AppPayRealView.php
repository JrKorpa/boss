<?php
/**
 *  -------------------------------------------------
 *   @file		: AppPayRealView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-27 12:17:56
 *   @update	:
 *  -------------------------------------------------
 */
class AppPayRealView extends View
{
	protected $_pay_real_number;
	protected $_pay_real_all_name;
	protected $_pay_number;
	protected $_pay_type;
	protected $_prc_id;
	protected $_prc_name;
	protected $_company;
	protected $_bank_name;
	protected $_bank_serial_number;
	protected $_bank_account;
	protected $_pay_time;
	protected $_total;
	protected $_make_time;
	protected $_make_name;


	public function get_pay_real_number(){return $this->_pay_real_number;}
	public function get_pay_real_all_name(){return $this->_pay_real_all_name;}
	public function get_pay_number(){return $this->_pay_number;}
	public function get_pay_type(){return $this->_pay_type;}
	public function get_prc_id(){return $this->_prc_id;}
	public function get_prc_name(){return $this->_prc_name;}
	public function get_company(){return $this->_company;}
	public function get_bank_name(){return $this->_bank_name;}
	public function get_bank_serial_number(){return $this->_bank_serial_number;}
	public function get_bank_account(){return $this->_bank_account;}
	public function get_pay_time(){return $this->_pay_time;}
	public function get_total(){return $this->_total;}
	public function get_make_time(){return $this->_make_time;}
	public function get_make_name(){return $this->_make_name;}

}
?>