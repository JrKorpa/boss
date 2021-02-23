<?php
/**
 *  -------------------------------------------------
 *   @file		: AppMemeberPointView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 13:33:02
 *   @update	:
 *  -------------------------------------------------
 */
class AppMemeberPointView extends View
{
	protected $_id;
	protected $_memeber_id;
	protected $_change_step;
	protected $_chane_type;
	protected $_change_status;
	protected $_happen_time;
	protected $_pass_time;
	protected $_pass_userid;
	protected $_is_deleted;


	public function get_id(){return $this->_id;}
	public function get_memeber_id(){return $this->_memeber_id;}
	public function get_change_step(){return $this->_change_step;}
	public function get_chane_type(){return $this->_chane_type;}
	public function get_change_status(){return $this->_change_status;}
	public function get_happen_time(){return $this->_happen_time;}
	public function get_pass_time(){return !empty($this->_pass_time)?$this->_pass_time:0;}
	public function get_pass_userid(){return $this->_pass_userid;}
	public function get_is_deleted(){return $this->_is_deleted;}

}
?>