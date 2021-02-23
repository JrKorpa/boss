<?php
/**
 *  -------------------------------------------------
 *   @file		: DictView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-05 11:01:28
 *   @update	:
 *  -------------------------------------------------
 */
class DictView extends View
{
	protected $_id;
	protected $_name;
	protected $_label;
	protected $_is_system;
	protected $_is_deleted;


	public function get_id(){return $this->_id;}
	public function get_name(){return $this->_name;}
	public function get_label(){return $this->_label;}
	public function get_is_system(){return $this->_is_system;}
	public function get_is_deleted(){return $this->_is_deleted;}

	public function getEnumArray ($name) 
	{
		if(!$name)
		{
			return array();	
		}
		return $this->getModel()->getEnumArray($name);
	}

	public function getEnum ($name,$key) 
	{
		if(!$name || !isset($key))
		{
			return '';	
		}
		return $this->getModel()->getEnum($name,$key);
	}
	public function getEnumByNote ($name,$note)
	{
	    if(!$name || !isset($note))
	    {
	        return '';
	    }
	    return $this->getModel()->getEnumByNote($name,$note);
	}
	
	
	
	
	
	
	
	
}

class DictItemView extends View
{
	protected $_id;
	protected $_dict_id;
	protected $_name;
	protected $_label;
	protected $_note;
	protected $_display_order;
	protected $_is_system;
	protected $_is_deleted;


	public function get_id(){return $this->_id;}
	public function get_dict_id(){return $this->_dict_id;}
	public function get_name(){return $this->_name;}
	public function get_label(){return $this->_label;}
	public function get_note(){return $this->_note;}
	public function get_display_order(){return $this->_display_order;}
	public function get_is_system(){return $this->_is_system;}
	public function get_is_deleted(){return $this->_is_deleted;}

	public function set_dict_id ($dict_id) 
	{
		$this->_dict_id = $dict_id;
	}

}
?>