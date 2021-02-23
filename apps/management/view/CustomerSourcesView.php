<?php
/**
 *  -------------------------------------------------
 *   @file		: CustomerSourcesView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-05 11:29:16
 *   @update	:
 *  -------------------------------------------------
 */
class CustomerSourcesView extends View
{
	protected $_id;
	protected $_source_name;
	protected $_source_code;
	protected $_source_class;
	protected $_source_type;
	protected $_source_own_id;
	protected $_source_own;
	protected $_add_id;
	protected $_add_time;
	protected $_update_id;
	protected $_update_time;
	protected $_is_deleted;
	protected $_fenlei;
	protected $is_enabled;


	public function get_id(){return $this->_id;}
	public function get_source_name(){return $this->_source_name;}
	public function get_source_code(){return $this->_source_code;}
	public function get_source_class(){return $this->_source_class;}
	public function get_source_type(){return $this->_source_type;}
	public function get_source_own_id(){return $this->_source_own_id;}
	public function get_source_own(){return $this->_source_own;}
	public function get_add_id(){return $this->_add_id;}
	public function get_add_time(){return $this->_add_time;}
	public function get_update_id(){return $this->_update_id;}
	public function get_update_time(){return $this->_update_time;}
	public function get_is_deleted(){return $this->_is_deleted;}
	public function get_fenlei(){return $this->_fenlei;}
	public function get_is_enabled(){return ($this->_id)?$this->_is_enabled:1;}


	public function getSourceName($id){
		$sql = "SELECT `source_name` FROM `customer_sources` WHERE `id` = '".$id."'";
		$model = $this->getModel();
		$name = $model->db()->getOne($sql);
		return $name;
	}

}
?>