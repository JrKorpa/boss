<?php
/**
 *  -------------------------------------------------
 *   @file		: ButtonClassModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-25 15:27:55
 *   @update	:
 *  -------------------------------------------------
 */
class ButtonClassModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'button_class';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"样式id",
"classname"=>"样式名");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url ButtonClassController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	public function hasClassName ($classname) 
	{
		$sql = "SELECT count(*) FROM `".$this->table()."` WHERE `classname`='".$classname."'";
		if($this->pk())
		{
			$sql .=" AND `id`<>".$this->pk();	
		}
		return $this->db()->getOne($sql);
	}
	public function getClassList () 
	{
		$sql = "SELECT * FROM `".$this->table()."` ORDER BY `id`";
		return $this->db()->getAll($sql);
	}
}

?>