<?php
/**
 *  -------------------------------------------------
 *   @file		: FieldScopeModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-06 09:27:11
 *   @update	:
 *  -------------------------------------------------
 */
class FieldScopeModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'field_scope';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"主键id",
"label"=>"标识",
"code"=>"属性",
"c_id"=>"所属控制器",
"is_enabled"=>"是否启用",
"is_deleted"=>"是否删除");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url FieldScopeController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
		if(!empty($where['c_id']))
		{
			$str .= "`c_id`='".$where['c_id']."' AND ";
		}
		if(isset($where['is_deleted']))
		{
			$str .= "`is_deleted`='".$where['is_deleted']."' AND ";
		}
		if($where['label'] != "")
		{
			$str .= "`label` like \"%".addslashes($where['label'])."%\" AND ";
		}
		if($where['code'] != "")
		{
			$str .= "`code` like \"%".addslashes($where['code'])."%\" AND ";
		}

		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	public function hasCode ($code)
	{
		$sql = "SELECT count(*) FROM `".$this->table()."` WHERE `code`='{$code}'";
		if($this->pk())
		{
			$sql .=" AND id<>".$this->pk();
		}
		return $this->db()->getOne($sql);
	}
}

?>