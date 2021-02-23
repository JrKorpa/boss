<?php
/**
 *  -------------------------------------------------
 *   @file		: MenuView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-08 15:17:33
 *   @update	:
 *  -------------------------------------------------
 */
class MenuView extends View
{
	protected $_id;
	protected $_label;
	protected $_code;
	protected $_url;
	protected $_icon;
	protected $_is_enabled;
	protected $_display_order;
	protected $_is_system;
	protected $_is_deleted;
	protected $_group_id;
	protected $_application_id;
	protected $_c_id;
	protected $_o_id;
	protected $_type;
	protected $_is_out;


	public function get_id(){return $this->_id;}
	public function get_label(){return $this->_label;}
	public function get_code(){return $this->_code;}
	public function get_url(){return $this->_url;}
	public function get_icon(){return $this->_icon;}
	public function get_is_enabled(){return empty($this->_is_enabled) ? 0 : 1 ;}
	public function get_display_order(){return $this->_display_order;}
	public function get_is_system(){return $this->_is_system;}
	public function get_is_deleted(){return $this->_is_deleted;}
	public function get_group_id(){return $this->_group_id;}
	public function get_application_id(){return $this->_application_id;}
	public function get_c_id(){return $this->_c_id;}
	public function get_o_id(){return $this->_o_id;}
	public function get_type(){return $this->_type;}
	public function get_is_out(){return $this->_is_out;}

	public function getMenuOptions ()
	{
		$model = $this->getModel();
		return $model->getMenuOptions();
	}

	public function getIconList ()
	{
		$m = new ButtonIconModel(1);
		return $m->getIconList();
	}

	public function getAppList ()
	{
		$model = new ApplicationModel(1);
		return $model->getAppList();
	}

}
?>