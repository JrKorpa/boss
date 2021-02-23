<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductFqcConfView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-03 17:24:09
 *   @update	:
 *  -------------------------------------------------
 */
class ProductFqcConfView extends View
{
	protected $_id;
	protected $_cat_name;
	protected $_parent_id;
	protected $_is_deleted;
        protected $_tree_path;
	protected $_pids;
	protected $_childrens;
	protected $_display_order;

	public function get_id(){return $this->_id;}
	public function get_cat_name(){return $this->_cat_name;}
	public function get_parent_id(){return $this->_parent_id;}
	public function get_is_deleted(){return $this->_is_deleted;}
        public function get_tree_path(){return $this->_tree_path;}
        public function get_pids(){return $this->_pids;}
	public function get_childrens(){return $this->_childrens;}
	public function get_display_order(){return $this->_display_order;}
	
        /*获取订单问题层级树*/
       public function getOrderTree($all=true){
            $model = $this->getModel();
            $data  = $model->get_order_tree($all);
            $newData = array();
            foreach ($data as $key => $val )
            {
                    $level = count(explode('-', $val['abspath']))-1;
                    $val['tname'] = str_repeat('&nbsp;&nbsp;', $level-1).$val['cat_name'];
                    $newData[] = $val;
            }
            return $newData;
       }
       

}
?>