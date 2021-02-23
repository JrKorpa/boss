<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseTypeView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-07 16:12:40
 *   @update	:
 *  -------------------------------------------------
 */
class PurchaseTypeView extends View
{
	protected $_id;
	protected $_t_name;
	protected $_is_auto;
	protected $_add_name;
	protected $_add_time;
	protected $_is_enabled;
	protected $_is_deleted;
        protected $_is_system;

	public function get_id(){return $this->_id;}
	public function get_t_name(){return $this->_t_name;}
	public function get_is_auto(){return $this->_is_auto;}
	public function get_add_name(){return $this->_add_name;}
	public function get_add_time(){return $this->_add_time;}
	public function get_is_enabled(){return $this->_is_enabled;}
	public function get_is_deleted(){return $this->_is_deleted;}
        public function get_is_system(){return $this->_is_system;}

}
?>