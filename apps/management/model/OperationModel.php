<?php
/**
 *  -------------------------------------------------
 *   @file		: OperationModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-24 09:51:54
 *   @update	:
 *  -------------------------------------------------
 */
class OperationModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'operation';
        $this->_dataObject = array("id"=>"主键id",
"method_name"=>"方法名称",
"label"=>"显示标识",
"is_system"=>"系统内置",
"is_deleted"=>"是否删除",
"c_id"=>"所属控制器");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url ControlController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT `m`.`id`,`m`.`label`,`m`.`method_name` FROM `".$this->table()."` AS `m` WHERE `m`.`is_deleted`='".$where['is_deleted']."' AND `m`.`c_id`='".$where['_id']."' ORDER BY `m`.`id` DESC";

		return $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
	}

	/**
	 *	查重
	 */
	public function has ($newdo)
	{
		$sql = "SELECT count(*) FROM `".$this->table()."` WHERE `method_name`='".$newdo['method_name']."' AND `c_id`='".$newdo['c_id']."' ";
		if($this->pk())
		{
			$sql .=" AND id<>".$this->pk();	
		}
		return $this->db()->getOne($sql);
	}

	public function hasRelData ($id) 
	{
		$sql = "SELECT count(*) FROM `menu` WHERE `o_id`='{$id}' AND `is_deleted`=0 ";
		if($this->db()->getOne($sql))
		{
			return true;	
		}
		$sql = "SELECT count(*) FROM `button` WHERE `o_id`='{$id}' AND `is_deleted`='0' ";
		return $this->db()->getOne($sql);
	}
}

?>