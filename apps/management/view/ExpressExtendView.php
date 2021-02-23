<?php
/**
 *  -------------------------------------------------
 *   @file		: ExpressExtendView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2016-01-27 13:49:35
 *   @update	:
 *  -------------------------------------------------
 */
class ExpressExtendView extends View
{
	protected $_id;
	protected $_express_id;
	protected $_send_time_end;
	protected $_send_time_start;
	protected $_exp_areas_id;
	protected $_exp_areas_name;


	public function get_id(){return $this->_id;}
	public function get_express_id(){return $this->_express_id;}
	public function get_send_time_end(){return $this->_send_time_end;}
	public function get_send_time_start(){return $this->_send_time_start;}
	public function get_exp_areas_id(){return $this->_exp_areas_id;}
	public function get_exp_areas_name(){return $this->_exp_areas_name;}

}
?>