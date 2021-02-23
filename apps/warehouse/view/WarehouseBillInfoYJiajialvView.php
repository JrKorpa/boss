<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoYJiajialvView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-11-26 12:03:08
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoYJiajialvView extends View
{
	protected $_id;
	protected $_sytle_type;
	protected $_jiajialv;
	protected $_create_time;
	protected $_check_time;
	protected $_remark;
	protected $_creator;
	protected $_checker;
	protected $_status;


	public function get_id(){return $this->_id;}
	public function get_sytle_type(){return $this->_sytle_type;}
	public function get_jiajialv(){return $this->_jiajialv;}
	public function get_create_time(){return $this->_create_time;}
	public function get_check_time(){return $this->_check_time;}
	public function get_remark(){return $this->_remark;}
	public function get_creator(){return $this->_creator;}
	public function get_checker(){return $this->_checker;}
	public function get_status(){return $this->_status;}

}
?>