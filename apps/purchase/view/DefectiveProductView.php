<?php
/**
 *  -------------------------------------------------
 *   @file		: DefectiveProductView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-18 23:02:26
 *   @update	:
 *  -------------------------------------------------
 */
class DefectiveProductView extends View
{
	protected $_id;
	protected $_status;
	protected $_prc_id;
	protected $_prc_name;
	protected $_ship_num;
	protected $_num;
	protected $_total;
	protected $_info;
	protected $_make_name;
	protected $_make_time;
	protected $_check_name;
	protected $_check_time;


	public function get_id(){return $this->_id;}
	public function get_status(){return $this->_status;}
	public function get_prc_id(){return $this->_prc_id;}
	public function get_prc_name(){return $this->_prc_name;}
	public function get_ship_num(){return $this->_ship_num;}
	public function get_num(){return $this->_num;}
	public function get_total(){return $this->_total;}
	public function get_info(){return $this->_info;}
	public function get_make_name(){return $this->_make_name;}
	public function get_make_time(){return $this->_make_time;}
	public function get_check_name(){return $this->_check_name;}
	public function get_check_time(){return $this->_check_time;}

}
?>