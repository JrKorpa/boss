<?php
/**
 *  -------------------------------------------------
 *   @file		: FieldScopeView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-06 09:27:11
 *   @update	:
 *  -------------------------------------------------
 */
class FieldScopeView extends View
{
	protected $_id;
	protected $_label;
	protected $_code;
	protected $_c_id;
	protected $_is_enabled;
	protected $_is_deleted;


	public function get_id(){return $this->_id;}
	public function get_label(){return $this->_label;}
	public function get_code(){return $this->_code;}
	public function get_c_id(){return $this->_c_id;}
	public function get_is_enabled(){return $this->_is_enabled;}
	public function get_is_deleted(){return $this->_is_deleted;}

	public function getCtlList ()
	{
		$m = new ControlModel(1);
		return $m->getCtlList();
	}
}
?>