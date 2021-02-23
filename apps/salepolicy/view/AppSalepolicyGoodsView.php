<?php
/**
 *  -------------------------------------------------
 *   @file		: AppSalepolicyGoodsView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-17 18:36:47
 *   @update	:
 *  -------------------------------------------------
 */
class AppSalepolicyGoodsView extends View
{
	protected $_id;
	protected $_policy_id;
	protected $_goods_id;
	protected $_chengben;
	protected $_sale_price;
	protected $_jiajia;
	protected $_sta_value;
	protected $_isXianhuo;
	protected $_create_time;
	protected $_create_user;
	protected $_check_time;
	protected $_check_user;
	protected $_status;
	protected $_is_delete;


	public function get_id(){return $this->_id;}
	public function get_policy_id(){return $this->_policy_id;}
	public function get_goods_id(){return $this->_goods_id;}
	public function get_chengben(){return $this->_chengben;}
	public function get_sale_price(){return $this->_sale_price;}
	public function get_jiajia(){return $this->_jiajia;}
	public function get_sta_value(){return $this->_sta_value;}
	public function get_isXianhuo(){return $this->_isXianhuo;}
	public function get_create_time(){return $this->_create_time;}
	public function get_create_user(){return $this->_create_user;}
	public function get_check_time(){return $this->_check_time;}
	public function get_check_user(){return $this->_check_user;}
	public function get_status(){return $this->_status;}
	public function get_is_delete(){return $this->_is_delete;}

}
?>