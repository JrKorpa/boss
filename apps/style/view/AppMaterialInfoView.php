<?php
/**
 *  -------------------------------------------------
 *   @file		: AppMaterialInfoView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-07 17:42:43
 *   @update	:
 *  -------------------------------------------------
 */
class AppMaterialInfoView extends View
{
	protected $_material_id;
	protected $_material_name;
	protected $_create_time;
	protected $_create_user;
	protected $_material_status;
	protected $_material_remark;
	protected $_tax_point;
	protected $_price;


	public function get_material_id(){return $this->_material_id;}
	public function get_material_name(){return $this->_material_name;}
	public function get_create_time(){return $this->_create_time;}
	public function get_create_user(){return $this->_create_user;}
	public function get_material_status(){return $this->_material_status;}
	public function get_material_remark(){return $this->_material_remark;}
	public function get_tax_point(){return $this->_tax_point;}
	public function get_price(){return $this->_price;}

}
?>