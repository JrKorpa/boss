<?php
/**
 *  -------------------------------------------------
 *   @file		: AppReceiveRealView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-31 12:17:00
 *   @update	:
 *  -------------------------------------------------
 */
class AppReceiveRealView extends View
{
	protected $_real_id;
	protected $_real_number;
	protected $_from_ad;
	protected $_should_number;
	protected $_bank_name;
	protected $_bank_serial_number;
	protected $_total;
	protected $_pay_tiime;
	protected $_maketime;
	protected $_makename;


	public function get_real_id(){return $this->_real_id;}
	public function get_real_number(){return $this->_real_number;}
	public function get_from_ad(){return $this->_from_ad;}
	public function get_should_number(){return $this->_should_number;}
	public function get_bank_name(){return $this->_bank_name;}
	public function get_bank_serial_number(){return $this->_bank_serial_number;}
	public function get_total(){return $this->_total;}
	public function get_pay_tiime(){return $this->_pay_tiime;}
	public function get_maketime(){return $this->_maketime;}
	public function get_makename(){return $this->_makename;}


}
?>