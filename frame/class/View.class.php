<?php
/**
 *  -------------------------------------------------
 *   @file		: View.class.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		:
 *   @update	:
 *  -------------------------------------------------
 */

abstract class View
{
    protected $_str;
    protected $_dataset = array();
    protected $_author = null;
	protected $_model = null;
	protected $_inner_data = array();

    public function __toString()
	{
		return $this->_str;
	}

	/**
	 *	__construct，构造函数，根据传入的数据对象构造业务数据对象
	 *
	 *	@param Object/Integer $obj
	 *	如果传递的是id，那么子视图和子模型的名称必须一致  MenuView===>MenuModel
	 *
	 */
    public function __construct($obj)
	{
		if(!$obj) return;
		if(is_int($obj))
		{
			$modelName = substr(get_class($this),0,-4)."Model";
			$obj = new $modelName ($obj,1);
		}
		if(!is_object($obj)) return;
		$this->_model = $obj;
		$data = $obj->getDataObject();

		if(!$data || count($data) <= 0)
		{
			if($obj->pk())
			{
				die('记录不存在');
			}
			return;
		}
		
		$this->_inner_data = $data;
		
		foreach($data as $k=>$v)
		{
			$key = "_".$k;
			if($k == 'id')
			{
				$this->$key = (int)$v;
			}
			else
			{
				$this->$key = $v;
			}
			$func = "get" . $key;
			//if(!function_exists($func)) continue;
			if(!method_exists($this,$func)) continue;
			$this->_str .= $key . ":" . print_r($this->$func(),true) . ", ";
		}
		$this->_str = trim($this->_str, ", ");
    }

	public function getModel ()
	{
		return $this->_model;
	}
	
	public function getField($field) {
		$func = 'get_'.$field;
		if(method_exists($this,$func)) {
			return $func();
		}

		return isset($this->_inner_data[$field]) ? $this->_inner_data[$field] : '';
	}
}
?>