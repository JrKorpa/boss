<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseSalepolicyInfoView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-11 10:54:58
 *   @update	:
 *  -------------------------------------------------
 */
class AppSalepolicyChannelView extends View
{
	protected $_id;
	protected $_policy_id;
	protected $_channel;
	protected $_channel_level;
	protected $_create_time;
	protected $_create_user;
	protected $_check_time;
	protected $_check_user;
	protected $_status;
	protected $_is_delete;


	public function get_id(){return $this->_id;}
	public function get_policy_id(){return $this->_policy_id;}
	public function get_channel(){return $this->_channel;}
	public function get_channel_level(){return $this->_channel_level;}
	public function get_create_time(){return $this->_create_time;}
	public function get_create_user(){return $this->_create_user;}
	public function get_check_time(){return $this->_check_time;}
	public function get_check_user(){return $this->_check_user;}
	public function get_status(){return $this->_status;}
	public function get_is_delete(){return $this->_is_delete;}

}
?>