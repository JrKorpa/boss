<?php
/**
 *  -------------------------------------------------
 *   @file		: DealerCustomerManageView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-12-15 11:51:21
 *   @update	:
 *  -------------------------------------------------
 */
class DealerCustomerManageView extends View
{
	protected $_id;
	protected $_customer_name;
	protected $_status;
	protected $_source;
	protected $_source_channel;
	protected $_tel;
	protected $_email;
	protected $_province;
	protected $_city;
	protected $_district;
	protected $_shop_nums;
	protected $_investment_amount;
	protected $_info;
	protected $_follow_upper_id;
    protected $_spread_id;
	protected $_created_time;
	protected $_modified_time;
    protected $_text_item;


	public function get_id(){return $this->_id;}
	public function get_customer_name(){return $this->_customer_name;}
	public function get_status(){return $this->_status;}
	public function get_source(){return $this->_source;}
	public function get_source_channel(){return $this->_source_channel;}
	public function get_tel(){return $this->_tel;}
	public function get_email(){return $this->_email;}
	public function get_province(){return $this->_province;}
	public function get_city(){return $this->_city;}
	public function get_district(){return $this->_district;}
	public function get_shop_nums(){return $this->_shop_nums;}
	public function get_investment_amount(){return $this->_investment_amount;}
	public function get_info(){return $this->_info;}
	public function get_follow_upper_id(){return $this->_follow_upper_id;}
    public function get_spread_id(){return $this->_spread_id;}
	public function get_created_time(){return $this->_created_time;}
	public function get_modified_time(){return $this->_modified_time;}
    public function get_text_item(){return $this->_text_item;}

}
?>