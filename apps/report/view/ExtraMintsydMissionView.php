<?php
/**
 *  -------------------------------------------------
 *   @file		: ExtraMintsydMissionView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-09-29 11:14:39
 *   @update	:
 *  -------------------------------------------------
 */
class ExtraMintsydMissionView extends View
{
	protected $_id;
	protected $_dep_id;
	protected $_dep_name;
	protected $_sale_name;
	protected $_minimum_price;
	protected $_tsyd_mission;
	protected $_task_date;


	public function get_id(){return $this->_id;}
	public function get_dep_id(){return $this->_dep_id;}
	public function get_dep_name(){return $this->_dep_name;}
	public function get_sale_name(){return $this->_sale_name;}
	public function get_minimum_price(){return $this->_minimum_price;}
	public function get_tsyd_mission(){return $this->_tsyd_mission;}
    public function get_task_date(){return $this->_task_date;}

}
?>