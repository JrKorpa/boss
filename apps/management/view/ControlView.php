<?php
/**
 *  -------------------------------------------------
 *   @file		: ControlView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-20 10:25:23
 *   @update	:
 *  -------------------------------------------------
 */
class ControlView extends View
{
	protected $_id;
	protected $_label;
	protected $_code;
	protected $_type;
	protected $_parent_id;
	protected $_application_id;
	protected $_is_deleted;
	protected $_is_system;


	public function get_id(){return $this->_id;}
	public function get_label(){return $this->_label;}
	public function get_code(){return $this->_code;}
	public function get_type(){return $this->_type;}
	public function get_parent_id(){return $this->_parent_id;}
	public function get_application_id(){return $this->_application_id;}
	public function get_is_deleted(){return $this->_is_deleted;}
	public function get_is_system(){return $this->_is_system;}

	public function getAppList () 
	{
		$model = new ApplicationModel(1);
		return $model->getAppList();
	}

	public function get_app_by_id ($id) 
	{
		$v = new ApplicationView(new ApplicationModel($id,1));
		return $v->get_label();
	}

	public function getParentobj(){
		$m = $this->getModel();
		return $m->getParentObj();
	}

}
?>