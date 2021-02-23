<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseLogView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-20 16:44:03
 *   @update	:
 *  -------------------------------------------------
 */
class PurchaseLogView extends View
{
	protected $_id;
	protected $_rece_detail_id;
	protected $_status;
	protected $_remark;
	protected $_uid;
	protected $_uname;
	protected $_time;


	public function get_id(){return $this->_id;}
	public function get_rece_detail_id(){return $this->_rece_detail_id;}
	public function get_status(){return $this->_status;}
	public function get_remark(){return $this->_remark;}
	public function get_uid(){return $this->_uid;}
	public function get_uname(){return $this->_uname;}
	public function get_time(){return $this->_time;}

}
?>