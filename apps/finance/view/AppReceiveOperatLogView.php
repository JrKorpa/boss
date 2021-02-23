<?php
/**
 *  -------------------------------------------------
 *   @file		: AppReceiveOperatLogView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-27 15:05:14
 *   @update	:
 *  -------------------------------------------------
 */
class AppReceiveOperatLogView extends View
{
	protected $_id;
	protected $_related_id;
	protected $_type;
	protected $_operat_name;
	protected $_operat_time;
	protected $_operat_content;


	public function get_id(){return $this->_id;}
	public function get_related_id(){return $this->_related_id;}
	public function get_type(){return $this->_type;}
	public function get_operat_name(){return $this->_operat_name;}
	public function get_operat_time(){return $this->_operat_time;}
	public function get_operat_content(){return $this->_operat_content;}

}
?>