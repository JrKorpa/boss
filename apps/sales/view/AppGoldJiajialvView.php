<?php
/**
 *  -------------------------------------------------
 *   @file		: AppGoldJiajialvView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-08-17 13:57:53
 *   @update	:
 *  -------------------------------------------------
 */
class AppGoldJiajialvView extends View
{
	protected $_id;
	protected $_gold_price;
	protected $_jiajialv;
	protected $_create_time;
	protected $_create_user;
	protected $_is_usable;


	public function get_id(){return $this->_id;}
	public function get_gold_price(){return $this->_gold_price;}
	public function get_jiajialv(){return $this->_jiajialv;}
	public function get_create_time(){return $this->_create_time;}
	public function get_create_user(){return $this->_create_user;}
	public function get_is_usable(){return $this->_is_usable;}

}
?>