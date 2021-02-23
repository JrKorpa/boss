<?php
/**
 *  -------------------------------------------------
 *   @file		: ListCancleReasonView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-03 12:27:26
 *   @update	:
 *  -------------------------------------------------
 */
class ListCancleReasonView extends View
{
	protected $_id;
	protected $_style_id;
	protected $_create_user;
	protected $_create_time;
	protected $_remark;
	protected $_type;


	public function get_id(){return $this->_id;}
	public function get_style_id(){return $this->_style_id;}
	public function get_create_user(){return $this->_create_user;}
	public function get_create_time(){return $this->_create_time;}
	public function get_remark(){return $this->_remark;}
	public function get_type(){return $this->_type;}

}
?>