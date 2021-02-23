<?php
/**
 *  -------------------------------------------------
 *   @file		: AppSalepolicyTogetherGoodsView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-14 18:55:25
 *   @update	:
 *  -------------------------------------------------
 */
class AppSalepolicyTogetherGoodsView extends View
{
	protected $_id;
	protected $_together_name;
	protected $_together_goods_id;
	protected $_create_user;
	protected $_create_time;
	protected $_is_split;
	protected $_status;


	public function get_id(){return $this->_id;}
	public function get_together_name(){return $this->_together_name;}
	public function get_together_goods_id(){return $this->_together_goods_id;}
	public function get_create_user(){return $this->_create_user;}
	public function get_create_time(){return $this->_create_time;}
	public function get_is_split(){return $this->_is_split?$this->_is_split:1;}
	public function get_status(){return $this->_status;}

}
?>