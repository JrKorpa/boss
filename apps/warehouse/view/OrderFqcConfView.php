<?php
/**
 *  -------------------------------------------------
 *   @file		: OrderFqcConfView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-10 17:28:29
 *   @update	:
 *  -------------------------------------------------
 */
class OrderFqcConfView extends View
{
	protected $_id;
	protected $_cat_name;
	protected $_parent_id;
	protected $_is_deleted;


	public function get_id(){return $this->_id;}
	public function get_cat_name(){return $this->_cat_name;}
	public function get_parent_id(){return $this->_parent_id;}
	public function get_is_deleted(){return $this->_is_deleted;}

}
?>