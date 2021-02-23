<?php
/**
 *  -------------------------------------------------
 *   @file		: RoleView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-17 14:29:54
 *   @update	:
 *  -------------------------------------------------
 */
class RoleView extends View
{
	protected $_id;
	protected $_label;
	protected $_code;
	protected $_note;
	protected $_is_deleted;
	protected $_is_system;


	public function get_id(){return $this->_id;}
	public function get_label(){return $this->_label;}
	public function get_code(){return $this->_code;}
	public function get_note(){return $this->_note;}
	public function get_is_deleted(){return $this->_is_deleted;}
	public function get_is_system(){return $this->_is_system;}

}
?>