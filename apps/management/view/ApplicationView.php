<?php
/**
 *  -------------------------------------------------
 *   @file		: ApplicationView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-19 13:47:31
 *   @update	:
 *  -------------------------------------------------
 */
class ApplicationView extends View
{
	protected $_id;
	protected $_label;
	protected $_code;
	protected $_icon;
	protected $_display_order;
	protected $_is_enabled;
	protected $_is_deleted;
	protected $_is_system;


	public function get_id(){return $this->_id;}
	public function get_label(){return $this->_label;}
	public function get_code(){return $this->_code;}
	public function get_icon(){return $this->_icon;}
	public function get_display_order(){return $this->_display_order;}
	public function get_is_enabled(){return !empty($this->_is_enabled) ? 1 : 0 ;}
	public function get_is_deleted(){return $this->_is_deleted;}
	public function get_is_system(){return $this->_is_system;}

	public function getIconList ()
	{
		$m = new ButtonIconModel(1);
		return $m->getIconList();
	}

	public function get_icon_by_id ($id)
	{
		$v = new ButtonIconView(new ButtonIconModel($id,1));
		return $v->get_name();
	}

}
?>