<?php
/**
 *  -------------------------------------------------
 *   @file		: GroupView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-16 15:38:44
 *   @update	:
 *  -------------------------------------------------
 */
class GroupView extends View
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
	 *	工作组树
	 */
	public function getGroupTree ($all=true)
	{
		$model = $this->getModel();
		$data = $model->getGroupTree($all);
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
		$v = new self(new GroupModel($id,1));
		return $v->get_name();
	}
}
?>