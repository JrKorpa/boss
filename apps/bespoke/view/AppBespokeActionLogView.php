<?php
/**
 *  -------------------------------------------------
 *   @file		: AppBespokeActionLogView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-12 14:32:07
 *   @update	:
 *  -------------------------------------------------
 */
class AppBespokeActionLogView extends View
{
	protected $_action_id;
	protected $_bespoke_id;
	protected $_create_user;
	protected $_create_time;
	protected $_IP;
	protected $_bespoke_status;
	protected $_remark;


	public function get_action_id(){return $this->_action_id;}
	public function get_bespoke_id(){return $this->_bespoke_id;}
	public function get_create_user(){return $this->_create_user;}
	public function get_create_time(){return $this->_create_time;}
	public function get_IP(){return $this->_IP;}
	public function get_bespoke_status(){return $this->_bespoke_status;}
	public function get_remark(){return $this->_remark;}

}
?>