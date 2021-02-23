<?php
/**
 *  -------------------------------------------------
 *   @file		: LogisticsDeliveryView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-06 11:57:16
 *   @update	:
 *  -------------------------------------------------
 */
class LogisticsDeliveryView extends View
{
	protected $_id;
	protected $_order_sn;
	protected $_delivery_sn;
	protected $_user_name;
	protected $_date_time;
	protected $_ip;
	protected $_user_id;
	protected $_jijian_person;
	protected $_dep_id;
	protected $_address;
	protected $_ship_company;
	protected $_reason;
	protected $_is_delete;


	public function get_id(){return $this->_id;}
	public function get_order_sn(){return $this->_order_sn;}
	public function get_delivery_sn(){return $this->_delivery_sn;}
	public function get_user_name(){return $this->_user_name;}
	public function get_date_time(){return $this->_date_time;}
	public function get_ip(){return $this->_ip;}
	public function get_user_id(){return $this->_user_id;}
	public function get_jijian_person(){return $this->_jijian_person;}
	public function get_dep_id(){return $this->_dep_id;}
	public function get_address(){return $this->_address;}
	public function get_ship_company(){return $this->_ship_company;}
	public function get_reason(){return $this->_reason;}
	public function get_is_delete(){return $this->_is_delete;}

}
?>