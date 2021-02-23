<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseHunbohuiInfoView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-05-10 11:06:56
 *   @update	:
 *  -------------------------------------------------
 */
class BaseHunbohuiInfoView extends View
{
	protected $_id;
	protected $_department;
	protected $_name;
	protected $_from_ad;
	protected $_warehouse;
	protected $_start_time;
	protected $_end_time;
	protected $_active_start_time;
	protected $_active_end_time;
	protected $_user_name;
	protected $_manager;
	protected $_is_delete;


	public function get_id(){return $this->_id;}
	public function get_department(){return $this->_department;}
	public function get_name(){return $this->_name;}
	public function get_from_ad(){return $this->_from_ad;}
	public function get_warehouse(){return $this->_warehouse;}
	public function get_start_time(){return $this->_start_time;}
	public function get_end_time(){return $this->_end_time;}
	public function get_active_start_time(){return $this->_active_start_time;}
	public function get_active_end_time(){return $this->_active_end_time;}
	public function get_user_name(){return $this->_user_name;}
	public function get_manager(){return $this->_manager;}
	public function get_is_delete(){return $this->_is_delete;}

}
?>