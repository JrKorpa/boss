<?php
/**
 *  -------------------------------------------------
 *   @file		: AppStyleForView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-19 10:41:52
 *   @update	:
 *  -------------------------------------------------
 */
class AppStyleForView extends View
{
	protected $_id;
	protected $_style_id;
	protected $_style_for_who;
	protected $_style_for_use;
	protected $_style_for_when;
	protected $_style_for_cat;
	protected $_style_for_designer;


	public function get_id(){return $this->_id;}
	public function get_style_id(){return $this->_style_id;}
	public function get_style_for_who(){return $this->_style_for_who;}
	public function get_style_for_use(){return $this->_style_for_use;}
	public function get_style_for_when(){return $this->_style_for_when;}
	public function get_style_for_cat(){return $this->_style_for_cat;}
	public function get_style_for_designer(){return $this->_style_for_designer;}

}
?>