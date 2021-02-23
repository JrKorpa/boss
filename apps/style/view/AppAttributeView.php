<?php
/**
 *  -------------------------------------------------
 *   @file		: AppAttributeView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-08 11:11:09
 *   @update	:
 *  -------------------------------------------------
 */
class AppAttributeView extends View
{
	protected $_attribute_id;
	protected $_attribute_name;

	protected $_attribute_code;
	protected $_show_type;
	protected $_attribute_status;
	protected $_create_time;
	protected $_create_user;
	protected $_attribute_remark;


	public function get_attribute_id(){return $this->_attribute_id;}
	public function get_attribute_name(){return $this->_attribute_name;}
	public function get_attribute_code(){return $this->_attribute_code;}
	public function get_show_type(){return $this->_show_type;}
	public function get_attribute_status(){return $this->_attribute_status;}
	public function get_create_time(){return $this->_create_time;}
	public function get_create_user(){return $this->_create_user;}
	public function get_attribute_remark(){return $this->_attribute_remark;}

}
?>