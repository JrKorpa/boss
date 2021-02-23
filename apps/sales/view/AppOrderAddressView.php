<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderAddressView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-30 16:37:26
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderAddressView extends View
{
	protected $_id;
	protected $_order_id;
	protected $_consignee;
	protected $_express_id;
	protected $_distribution_type;
	protected $_country_id;
	protected $_province_id;
	protected $_city_id;
	protected $_regional_id;
	protected $_address;
	protected $_tel;
	protected $_email;
	protected $_zipcode;
	protected $_goods_id;


	public function get_id(){return $this->_id;}
	public function get_order_id(){return $this->_order_id;}
	public function get_consignee(){return $this->_consignee;}
	public function get_express_id(){return $this->_express_id;}
	public function get_distribution_type(){return $this->_distribution_type;}
	public function get_country_id(){return $this->_country_id;}
	public function get_province_id(){return $this->_province_id;}
	public function get_city_id(){return $this->_city_id;}
	public function get_regional_id(){return $this->_regional_id;}
	public function get_address(){return $this->_address;}
	public function get_tel(){return $this->_tel;}
	public function get_email(){return $this->_email;}
	public function get_zipcode(){return $this->_zipcode;}
	public function get_goods_id(){return $this->_goods_id;}

}
?>