<?php
/**
 *  -------------------------------------------------
 *   @file		: AppJinsunView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 16:42:07
 *   @update	:
 *  -------------------------------------------------
 */
class AppJinsunView extends View
{
	protected $_s_id;
	protected $_price_type;
	protected $_material_id;
	protected $_lv;


	public function get_s_id(){return $this->_s_id;}
	public function get_price_type(){return $this->_price_type;}
	public function get_material_id(){return $this->_material_id;}
	public function get_lv(){return $this->_lv;}

}
?>