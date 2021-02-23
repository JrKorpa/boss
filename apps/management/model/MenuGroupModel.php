<?php
/**
 *  -------------------------------------------------
 *   @file		: MenuGroupModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-19 16:10:48
 *   @update	:
 *  -------------------------------------------------
 */
class MenuGroupModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'menu_group';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"主键",
"label"=>"分组名称",
"application_id"=>"所属模块",
"icon"=>"图标",
"display_order"=>"显示顺序",
"is_enabled"=>"是否启用",
"is_deleted"=>"是否删除",
"is_system"=>"是否系统内置");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url MenuGroupController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT `m`.`id`,`m`.`label`,`m`.`is_enabled`,`m`.`is_system`,`bi`.`name` AS `icon_name` FROM `".$this->table()."` AS m LEFT JOIN `button_icon` AS `bi` ON `m`.`icon`=`bi`.`id` WHERE `m`.`is_deleted`='0'";

		if($where['label'] != "")
		{
			$sql .= " AND `m`.`label` like \"%".addslashes($where['label'])."%\"";
		}
		if(!empty($where['_id']))
		{
			$sql .= " AND `m`.`application_id`='".$where['_id']."'";
		}
		$sql .= " ORDER BY `m`.`id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	/**
	 *	getMenuGroups
	 */
	public function getMenuGroups ($application_id)
	{
		$sql = "SELECT `id`,`label` FROM `".$this->table()."` WHERE `is_deleted`='0' AND `is_enabled`='1' AND `application_id`='".$application_id."' ORDER BY `display_order` DESC ";
		return $this->db()->getAll($sql);
	}

	/**
	 *	saveSort，菜单组排序
	 *
	 *	@url MenuGroupController/saveSort
	 */
	public function saveSort ($datas)
	{
		$len = count($datas);
		try{
			for ($i=0;$i<$len;$i++)
			{
				$sql = "UPDATE `".$this->table()."` SET `display_order`='".($i+1)."' WHERE `id`='".$datas[$i]."'";
				$this->db()->query($sql);
			}
		}
		catch(Exception $e)
		{
			return false;
		}
		return true;
	}

	public function hasRelData ($id)
	{
		$sql = "SELECT count(*) FROM `menu` WHERE `is_deleted`='0' AND group_id='{$id}' ";
		$res = $this->db()->getOne($sql);
		if($res)
		{
			return true;
		}
		return false;
	}

	public function ListMenu ($id)
	{
		$sql = "SELECT `id`,`label`,`code` FROM `menu` WHERE `group_id`='".$id."' AND `is_deleted`='0' AND `is_enabled`='1' ORDER BY `display_order` DESC ";
		return $this->db()->getAll($sql);
	}

	/**
	 *	saveMenuSort，菜单组排序
	 *
	 *	@url MenuGroupController/saveMenuSort
	 */
	public function saveMenuSort ($datas)
	{
		$len = count($datas);
		try{
			for ($i=0;$i<$len;$i++)
			{
				$sql = "UPDATE `menu` SET `display_order`='".($i+1)."' WHERE `id`='".$datas[$i]."'";
				$this->db()->query($sql);
			}
		}
		catch(Exception $e)
		{
			return false;
		}
		return true;
	}

	public function hasLabel ($label)
	{
		$sql = "SELECT count(*) FROM `".$this->table()."` WHERE `label`='{$label}'";
		if($this->pk())
		{
			$sql .=" AND id<>".$this->pk();
		}
		return $this->db()->getOne($sql);
	}
}

?>