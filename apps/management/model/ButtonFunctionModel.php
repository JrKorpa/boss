<?php
/**
 *  -------------------------------------------------
 *   @file		: ButtonFunctionModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-11 10:13:26
 *   @update	:
 *  -------------------------------------------------
 */
class ButtonFunctionModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'button_function';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"js方法id",
"name"=>"js组件名",
"label"=>"中文显示",
"tips"=>"使用提示",
"is_system"=>"系统内置",
"is_deleted"=>"是否删除",
"type"=>"事件类型：1为列表页，2为查看页，3为列表和查看通用");
		parent::__construct($id,$strConn);
	}

	public function listAll ($where)
	{

		$sql = "SELECT `id`,`name`,`label`,`tips`,`is_system`,`type` FROM `".$this->table()."`";
		$str = '';
		if(isset($where['is_deleted']))
		{
			$str .="`is_deleted`='".$where['is_deleted']."' AND ";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .=" ORDER BY `id` DESC ";
		return $this->db()->getAll($sql);
	}



	public function hasName ($name)
	{
		$sql = "SELECT count(*) FROM `".$this->table()."` WHERE `name`='{$name}'";
		if($this->pk())
		{
			$sql .=" AND `id`<>'".$this->pk()."'";
		}
		return $this->db()->getOne($sql);
	}

	public function hasRelData ($id)
	{
		$sql = "SELECT count(*) FROM `button` WHERE `function_id`='".$id."' AND `is_deleted`='0' ";
		return $this->db()->getOne($sql);
	}
}

?>