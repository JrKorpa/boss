<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseMemberInfoView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-07 17:49:23
 *   @update	:
 *  -------------------------------------------------
 */
class AppUserBespokeLogView extends View
{
	protected $_log_id;
	protected $_bespoke_id;
	protected $_mem_id;
	protected $_create_user;
	protected $_create_time;
	protected $_IP;
	protected $_remark;

	public function get_log_id(){return $this->_log_id;}
	public function get_bespoke_id(){return $this->_bespoke_id;}
	public function get_mem_id(){return $this->_mem_id;}
	public function get_create_user(){return $this->_create_user;}
	public function get_create_time(){return $this->_create_time;}
	public function get_IP(){return $this->_IP;}
	public function get_remark(){return $this->_remark;}
}
?>