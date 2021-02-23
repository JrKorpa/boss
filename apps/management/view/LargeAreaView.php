<?php
/**
 *  -------------------------------------------------
 *   @file		: LargeAreaView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-29 11:50:43
 *   @update	:
 *  -------------------------------------------------
 */
class LargeAreaView extends View
{
	protected $_id;
	protected $_name;
	protected $_parent_id;
	protected $_create_user;
	protected $_create_time;
	protected $_pids;
	protected $_tree_path;
	protected $_childrens;
	protected $_is_enable;
	protected $_newdata=array();


	public function get_id(){return $this->_id;}
	public function get_name(){return $this->_name;}
	public function get_parent_id(){return $this->_parent_id;}
	public function get_create_user(){return $this->_create_user;}
	public function get_create_time(){return $this->_create_time;}
	public function get_pids(){return $this->_pids;}
	public function get_tree_path(){return $this->_tree_path;}
	public function get_childrens(){return $this->_childrens;}
	public function get_is_enable(){return $this->_is_enable ? 1 : 0 ;}

	public function __construct($obj){

		parent::__construct($obj);
		$model = $this->getModel();
		$this->Areadata = $model->getAreaTree();

	}

	public function getAreaTree ($pid=0,$lev=0)
	{
		foreach($this->Areadata as $key=>$val){

			if($val['parent_id']==$pid){
				$val['name'] = str_repeat('&nbsp;&nbsp;', $lev).$val['name'];
				$this->_newdata[]=$val;
				$this->getAreaTree($val['id'],$lev+1);
			}

		}
		return $this->_newdata;

	}




}
?>