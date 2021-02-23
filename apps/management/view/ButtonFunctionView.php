<?php
/**
 *  -------------------------------------------------
 *   @file		: ButtonFunctionView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-11 10:07:51
 *   @update	:
 *  -------------------------------------------------
 */
class ButtonFunctionView extends View
{
	protected $_id;
	protected $_name;
	protected $_label;
	protected $_tips;
	protected $_is_system;
	protected $_is_deleted;
	protected $_type;


	public function get_id(){return $this->_id;}
	public function get_name(){return $this->_name;}
	public function get_label(){return $this->_label;}
	public function get_tips(){return $this->_tips;}
	public function get_is_system(){return $this->_is_system;}
	public function get_is_deleted(){return $this->_is_deleted;}
	public function get_type(){return $this->_type;}

}
?>