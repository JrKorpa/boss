<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderComplaintView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2016-02-27 14:29:07
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderComplaintView extends View
{
	protected $_id;
	protected $_order_id;
	protected $_cl_feedback_id;
	protected $_cl_other;
	protected $_cl_user;
	protected $_cl_time;
	protected $_cl_url;


	public function get_id(){return $this->_id;}
	public function get_order_id(){return $this->_order_id;}
	public function get_cl_feedback_id(){return $this->_cl_feedback_id;}
	public function get_cl_other(){return $this->_cl_other;}
	public function get_cl_user(){return $this->_cl_user;}
	public function get_cl_time(){return $this->_cl_time;}
	public function get_cl_url(){return $this->_cl_url;}

}
?>