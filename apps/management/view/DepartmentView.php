<?php
/**
 *  -------------------------------------------------
 *   @file		: DepartmentView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-26 11:52:16
 *   @update	:
 *  -------------------------------------------------
 */
class DepartmentView extends View
{
	protected $_id;
	protected $_name;
	protected $_code;
	protected $_note;
	protected $_parent_id;
	protected $_tree_path;
	protected $_pids;
	protected $_childrens;
	protected $_display_order;
	protected $_is_deleted;
	protected $_is_system;


	public function get_id(){return $this->_id;}
	public function get_name(){return $this->_name;}
	public function get_code(){return $this->_code;}
	public function get_note(){return $this->_note;}
	public function get_parent_id(){return $this->_parent_id;}
	public function get_tree_path(){return $this->_tree_path;}
	public function get_pids(){return $this->_pids;}
	public function get_childrens(){return $this->_childrens;}
	public function get_display_order(){return $this->_display_order;}
	public function get_is_deleted(){return $this->_is_deleted;}
	public function get_is_system(){return $this->_is_system;}

	/**
	 *	部门树
	 */
	public function getDeptTree ($all=true)
	{
		$model = $this->getModel();
		$data = $model->getDeptTree($all);
		$newData = array();
		foreach ($data as $key => $val )
		{
			$level = count(explode('-', $val['abspath']))-1;
			$val['tname'] = str_repeat('&nbsp;&nbsp;', $level-1).$val['name'];
			$newData[] = $val;
		}
		return $newData;
	}

	public function getParentName($id) 
	{
		$v = new self(new DepartmentModel($id,1));
		return $v->get_name();
	}

	public function getName($id)
	{
		$model = new DepartmentModel(1);
		return $model->getNameById($id);
	}
}
?>