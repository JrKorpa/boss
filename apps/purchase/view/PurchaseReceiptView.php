<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseReceiptView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-15 17:35:13
 *   @update	:
 *  -------------------------------------------------
 */
class PurchaseReceiptView extends View
{
	protected $_id;
	protected $_status;
	protected $_prc_id;
	protected $_prc_name;
	protected $_ship_num;
	protected $_chengbenjia;
	protected $_remark;
	protected $_num;
	protected $_all_amount;
	protected $_user_id;
	protected $_user_name;
	protected $_create_time;


	public function get_id(){return $this->_id;}
	public function get_status(){return $this->_status;}
	public function get_prc_id(){return $this->_prc_id;}
	public function get_prc_name(){return $this->_prc_name;}
	public function get_ship_num(){return $this->_ship_num;}
	public function get_chengbenjia(){return $this->_chengbenjia;}
	public function get_remark(){return $this->_remark;}
	public function get_num(){return $this->_num;}
	public function get_all_amount(){return $this->_all_amount;}
	public function get_user_id(){return $this->_user_id;}
	public function get_user_name(){return $this->_user_name;}
	public function get_create_time(){return $this->_create_time;}

}
?>