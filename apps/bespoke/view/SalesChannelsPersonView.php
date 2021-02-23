<?php
/**
 *  -------------------------------------------------
 *   @file		: SalesChannelsPersonView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-05-27 16:57:44
 *   @update	:
 *  -------------------------------------------------
 */
class SalesChannelsPersonView extends View
{
	protected $_id;
	protected $_dp_leader;
	protected $_dp_leader_name;
	protected $_dp_people;
	protected $_dp_people_name;


	public function get_id(){return $this->_id;}
	public function get_dp_leader(){return $this->_dp_leader;}
	public function get_dp_leader_name(){return $this->_dp_leader_name;}
	public function get_dp_people(){return $this->_dp_people;}
	public function get_dp_people_name(){return $this->_dp_people_name;}

}
?>