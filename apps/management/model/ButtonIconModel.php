<?php
/**
 *  -------------------------------------------------
 *   @file		: ButtonIconModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-25 17:11:28
 *   @update	:
 *  -------------------------------------------------
 */
class ButtonIconModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'button_icon';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"图标id",
"name"=>"图标名");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url ButtonIconController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	public function hasName ($name) 
	{
		$sql = "SELECT count(1) FROM `".$this->table()."` WHERE `name`='{$name}'";

		if($this->pk())
		{
			$sql .=" AND `id`<>'".$this->pk()."'";	
		}
		return $this->db()->getOne($sql);
	}

	public function getIconList () 
	{
		$sql = "SELECT * FROM `".$this->table()."` ORDER BY `id`";
		return $this->db()->getAll($sql);
	}
}

?>