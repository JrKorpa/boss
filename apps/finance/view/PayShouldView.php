<?php
/**
 *  -------------------------------------------------
 *   @file		: PayShouldView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-08 16:51:14
 *   @update	:
 *  -------------------------------------------------
 */
class PayShouldView extends View
{
	protected $_pay_number_id;
	protected $_pay_type;
	protected $_prc_id;
	protected $_prc_name;
	protected $_settle_mode;
	protected $_company;
	protected $_make_time;
	protected $_make_name;
	protected $_check_time;
	protected $_check_name;
	protected $_status;
	protected $_pay_status;
	protected $_total_cope;
	protected $_total_real;
	protected $_pay_should_all_name;


	public function get_pay_number_id(){return $this->_pay_number_id;}
	public function get_pay_type(){return $this->_pay_type;}
	public function get_prc_id(){return $this->_prc_id;}
	public function get_prc_name(){return $this->_prc_name;}
	public function get_settle_mode(){return $this->_settle_mode;}
	public function get_company(){return $this->_company;}
	public function get_make_time(){return $this->_make_time;}
	public function get_make_name(){return $this->_make_name;}
	public function get_check_time(){return $this->_check_time;}
	public function get_check_name(){return $this->_check_name;}
	public function get_status(){return $this->_status;}
	public function get_pay_status(){return $this->_pay_status;}
	public function get_total_cope(){return $this->_total_cope;}
	public function get_total_real(){return $this->_total_real;}
	public function get_pay_should_all_name(){return $this->_pay_should_all_name;}

}
?>