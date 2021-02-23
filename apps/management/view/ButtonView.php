<?php
/**
 *  -------------------------------------------------
 *   @file		: ButtonView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-24 11:43:16
 *   @update	:
 *  -------------------------------------------------
 */
class ButtonView extends View
{
	protected $_id;
	protected $_label;
	protected $_class_id;
	protected $_function_id;
	protected $_icon_id;
	protected $_data_url;
	protected $_tips;
	protected $_type;
	protected $_is_system;
	protected $_is_deleted;
	protected $_data_title;
	protected $_cust_function;
	protected $_a_id;
	protected $_c_id;
	protected $_o_id;
	protected $_display_order;


	public function get_id(){return $this->_id;}
	public function get_label(){return $this->_label;}
	public function get_class_id(){return $this->_class_id;}
	public function get_function_id(){return $this->_function_id;}
	public function get_icon_id(){return $this->_icon_id;}
	public function get_data_url(){return $this->_data_url;}
	public function get_tips(){return $this->_tips;}
	public function get_type(){return $this->_type;}
	public function get_is_system(){return $this->_is_system;}
	public function get_is_deleted(){return $this->_is_deleted;}
	public function get_data_title(){return $this->_data_title;}
	public function get_cust_function(){return $this->_cust_function;}
	public function get_a_id(){return $this->_a_id;}
	public function get_c_id(){return $this->_c_id;}
	public function get_o_id(){return $this->_o_id;}
	public function get_display_order(){return $this->_display_order;}

	public function getIconList () 
	{
		$m = new ButtonIconModel(1);
		return $m->getIconList();
	}

	public function getClassList () 
	{
		$m = new ButtonClassModel(1);
		return $m->getClassList();
	}

	public function getAppList () 
	{
		$model = new ApplicationModel(1);
		return $model->getAppList();
	}

	public function getCtlList () 
	{
		$m = new ControlModel(1);
		return $m->getCtlList();
	}
}
?>