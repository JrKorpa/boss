<?php
/**
 *  -------------------------------------------------
 *   @file		: OperationView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-24 09:51:54
 *   @update	:
 *  -------------------------------------------------
 */
class OperationView extends View
{
	protected $_id;
	protected $_method_name;
	protected $_label;
	protected $_c_id;
	protected $_is_system;
	protected $_is_deleted;


	public function get_id(){return $this->_id;}
	public function get_method_name(){return $this->_method_name;}
	public function get_label(){return $this->_label;}
	public function get_is_system(){return $this->_is_system;}
	public function get_is_deleted(){return $this->_is_deleted;}
	public function get_c_id(){return !empty($this->_c_id) ? $this->_c_id : 0;}

	public function getCtlList () 
	{
		$m = new ControlModel(1);
		return $m->getCtlList();
	}

	public function get_ctl_by_id ($id) 
	{
		$v=new ControlView(new ControlModel($id,1));
		return $v->get_label()."(".$v->get_code().")";
	}

}
?>