<?php
/**
 *  -------------------------------------------------
 *   @file		: RelStyleAttributeView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-11 19:34:35
 *   @update	:
 *  -------------------------------------------------
 */
class RelStyleAttributeView extends View
{
	protected $_rel_id;
	protected $_style_id;
	protected $_style_sn;
	protected $_cat_type_id;
	protected $_product_type_id;
	protected $_attribute_id;
	protected $_attribute_value;
	protected $_show_type;
	protected $_create_time;
	protected $_create_user;
	protected $_info;



	public function get_rel_id(){return $this->_rel_id;}
	public function get_style_id(){return $this->_style_id;}
	public function get_style_sn(){return $this->_style_sn;}
	public function get_cat_type_id(){return $this->_cat_type_id;}
	public function get_product_type_id(){return $this->_product_type_id;}
	public function get_attribute_id(){return $this->_attribute_id;}
	public function get_attribute_value(){return $this->_attribute_value;}
	public function get_show_type(){return $this->_show_type;}
	public function get_create_time(){return $this->_create_time;}
	public function get_create_user(){return $this->_create_user;}
	public function get_info(){return $this->_info;}

}
?>