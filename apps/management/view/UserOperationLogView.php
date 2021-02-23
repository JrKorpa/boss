<?php
/**
 *  -------------------------------------------------
 *   @file		: UserOperationLog.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-30 10:30:05
 *   @update	:
 *  -------------------------------------------------
 */
class UserOperationLogView extends View
{
	protected $_id;
	protected $_module;
	protected $_controller;
	protected $_action;
	protected $_remark;
	protected $_data;
	protected $_request_url;
	protected $_create_user;
	protected $_ip;
	protected $_create_time;

	public function get_id(){return $this->_id;}
	public function get_module(){return $this->_module;}
	public function get_controller(){return $this->_controller;}
	public function get_action(){return $this->_action;}
	public function get_remark(){return $this->_remark;}
	public function get_request_url(){return $this->_request_url;}
	public function get_data(){return $this->_data;}
	public function get_create_user(){return $this->_create_user;}
	public function get_ip(){return $this->_ip;}
	public function get_create_time(){return $this->_create_time;}
    
}
?>