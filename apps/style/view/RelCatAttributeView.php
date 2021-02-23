<?php
/**
 *  -------------------------------------------------
 *   @file		: RelCatAttributeView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 21:06:40
 *   @update	:
 *  -------------------------------------------------
 */
class RelCatAttributeView extends View
{
	protected $_rel_id;
	protected $_cat_type_id;
	protected $_product_type_id;
	protected $_attribute_id;
	protected $_is_show=1;
	protected $_is_default=1;
	protected $_is_require=1;
	protected $_status;
	protected $_attr_type;
	protected $_create_time;
	protected $_create_user;
	protected $_info;
	protected $_default_va;


	public function get_rel_id(){return $this->_rel_id;}
	public function get_cat_type_id(){return $this->_cat_type_id;}
	public function get_product_type_id(){return $this->_product_type_id;}
	public function get_attribute_id(){return $this->_attribute_id;}
	public function get_is_show(){return $this->_is_show;}
	public function get_is_default(){return $this->_is_default;}
	public function get_is_require(){return $this->_is_require;}
	public function get_status(){return $this->_status;}
	public function get_attr_type(){return $this->_attr_type;}
	public function get_create_time(){return $this->_create_time;}
	public function get_create_user(){return $this->_create_user;}
	public function get_info(){return $this->_info;}
	public function get_default_val(){return $this->_default_va;}

}
?>