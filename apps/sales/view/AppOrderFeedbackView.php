<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderFeedbackView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2016-01-26 10:30:32
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderFeedbackView extends View
{
	protected $_id;
	protected $_ks_option;
	protected $_ks_user;
	protected $_ks_time;
	protected $_ks_status;


	public function get_id(){return $this->_id;}
	public function get_ks_option(){return $this->_ks_option;}
	public function get_ks_user(){return $this->_ks_user;}
	public function get_ks_time(){return $this->_ks_time;}
	public function get_ks_status(){return $this->_ks_status;}

}
?>