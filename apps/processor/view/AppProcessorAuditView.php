<?php
/**
 *  -------------------------------------------------
 *   @file		: AppProcessorAuditView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-22 10:07:27
 *   @update	:
 *  -------------------------------------------------
 */
class AppProcessorAuditView extends View
{
	protected $_id;
	protected $_record_id;
	protected $_process_id;
	protected $_user_id;
	protected $_audit_status;
	protected $_audit_time;
	protected $_audit_plan;


	public function get_id(){return $this->_id;}
	public function get_record_id(){return $this->_record_id;}
	public function get_process_id(){return $this->_process_id;}
	public function get_user_id(){return $this->_user_id;}
	public function get_audit_status(){return $this->_audit_status;}
	public function get_audit_time(){return $this->_audit_time;}
	public function get_audit_plan(){return $this->_audit_plan;}

}
?>