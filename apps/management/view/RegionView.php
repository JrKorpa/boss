<?php
/**
 *  -------------------------------------------------
 *   @file		: RegionView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-08 14:47:38
 *   @update	:
 *  -------------------------------------------------
 */
class RegionView extends View
{
	protected $_region_id;
	protected $_parent_id;
	protected $_region_name;
	protected $_region_type;


	public function get_region_id(){return $this->_region_id;}
	public function get_parent_id(){return $this->_parent_id;}
	public function get_region_name(){return $this->_region_name;}
	public function get_region_type(){return $this->_region_type;}

}
?>