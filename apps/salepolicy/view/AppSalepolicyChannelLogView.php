<?php
/**
 *  -------------------------------------------------
 *   @file		: AppSalepolicyChannelLogView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-15 10:27:48
 *   @update	:
 *  -------------------------------------------------
 */
class AppSalepolicyChannelLogView extends View
{
	protected $_id;
	protected $_policy_id;
	protected $_create_user;
	protected $_create_time;
	protected $_IP;
	protected $_status;
	protected $_remark;


	public function get_id(){return $this->_id;}
	public function get_policy_id(){return $this->_policy_id;}
	public function get_create_user(){return $this->_create_user;}
	public function get_create_time(){return $this->_create_time;}
	public function get_IP(){return $this->_IP;}
	public function get_status(){return $this->_status;}
	public function get_remark(){return $this->_remark;}

}
?>