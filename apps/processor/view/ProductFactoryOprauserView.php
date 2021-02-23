<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductFactoryOprauserView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-21 11:25:32
 *   @update	:
 *  -------------------------------------------------
 */
class ProductFactoryOprauserView extends View
{
	protected $_id;
	protected $_prc_id;
	protected $_opra_user_id;
	protected $_opra_uname;
	protected $_add_user;
	protected $_add_time;


	public function get_id(){return $this->_id;}
	public function get_prc_id(){return $this->_prc_id;}
	public function get_opra_user_id(){return $this->_opra_user_id;}
	public function get_opra_uname(){return $this->_opra_uname;}
	public function get_add_user(){return $this->_add_user;}
	public function get_add_time(){return $this->_add_time;}

}
?>