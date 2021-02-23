<?php
/**
 *  -------------------------------------------------
 *   @file		: ResourceTypeView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-20 20:37:42
 *   @update	:
 *  -------------------------------------------------
 */
class ResourceTypeView extends View
{
	protected $_id;
	protected $_label;
	protected $_code;
	protected $_main_table;
	protected $_user_table;
	protected $_fields;
	protected $_foreigh_key;
	protected $_is_system;
	protected $_is_enabled;
	protected $_is_deleted;
	protected $_note;


	public function get_id(){return $this->_id;}
	public function get_label(){return $this->_label;}
	public function get_code(){return $this->_code;}
	public function get_main_table(){return $this->_main_table;}
	public function get_user_table(){return $this->_user_table;}
	public function get_fields(){return $this->_fields;}
	public function get_foreigh_key(){return $this->_foreigh_key;}
	public function get_is_system(){return $this->_is_system=='' ? 1 : $this->_is_system ;}
	public function get_is_enabled(){return $this->_is_enabled=='' ? 1 : $this->_is_enabled ;}
	public function get_is_deleted(){return $this->_is_deleted;}
	public function get_note(){return $this->_note;}

}
?>