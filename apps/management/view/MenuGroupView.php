<?php
/**
 *  -------------------------------------------------
 *   @file		: MenuGroupView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-19 16:10:48
 *   @update	:
 *  -------------------------------------------------
 */
class MenuGroupView extends View
{
	protected $_id;
	protected $_label;
	protected $_application_id;
	protected $_icon;
	protected $_display_order;
	protected $_is_enabled;
	protected $_is_deleted;
	protected $_is_system;


	public function get_id(){return $this->_id;}
	public function get_label(){return $this->_label;}
	public function get_application_id(){return $this->_application_id;}
	public function get_icon(){return $this->_icon;}
	public function get_display_order(){return $this->_display_order;}
	public function get_is_enabled(){return $this->_is_enabled===0 ? 0 : 1;}
	public function get_is_deleted(){return $this->_is_deleted;}
	public function get_is_system(){return $this->_is_system;}

	public function getAppList () 
	{
		$model = new ApplicationModel(1);
		return $model->getAppList();
	}

	public function getIconList () 
	{
		//todo
		$sql = "SELECT * FROM `button_icon`";
		$data = DB::cn(1)->getAll($sql);
		return $data;
	}

	public function get_icon_by_id ($id) 
	{
		//todo
		$sql = "SELECT name FROM `button_icon` WHERE `id`='{$id}' ";
		return DB::cn(1)->getone($sql);
	}

	public function get_app_by_id ($id) 
	{
		$v = new ApplicationView(new ApplicationModel($id,1));
		return $v->get_label();
	}
}
?>