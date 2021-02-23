<?php
/**
 *  -------------------------------------------------
 *   @file		: AppProductTypeView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 12:43:49
 *   @update	:
 *  -------------------------------------------------
 */
class AppProductTypeView extends View
{
	protected $_product_type_id;
	protected $_product_type_name;
	protected $_product_type_code;
	protected $_note;
	protected $_parent_id;
	protected $_tree_path;
	protected $_pids;
	protected $_childrens;
	protected $_display_order;
	protected $_product_type_status;
	protected $_is_system;


	public function get_product_type_id(){return $this->_product_type_id;}
	public function get_product_type_name(){return $this->_product_type_name;}
	public function get_product_type_code(){return $this->_product_type_code;}
	public function get_note(){return $this->_note;}
	public function get_parent_id(){return $this->_parent_id;}
	public function get_tree_path(){return $this->_tree_path;}
	public function get_pids(){return $this->_pids;}
	public function get_childrens(){return $this->_childrens;}
	public function get_display_order(){return $this->_display_order;}
	public function get_product_type_status(){return $this->_product_type_status;}
	public function get_is_system(){return $this->_is_system;}

    /*
        * 获取分类树
        */
    public function getProductTree ($all=true)
    {
        $model = $this->getModel();
        $data = $model->getProductTree($all);
        $newData = array();
        foreach ($data as $key => $val )
        {
            $level = count(explode('-', $val['abspath']))-1;
            $val['tname'] = str_repeat('&nbsp;&nbsp;', $level-1).$val['name'];
            $newData[] = $val;
        }
        return $newData;
    }
}
?>