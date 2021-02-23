<?php
/**
 *  -------------------------------------------------
 *   @file		: ApplicationModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-19 11:17:09
 *   @update	:
 *  -------------------------------------------------
 */
class ApplicationModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'application';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"主键",
"label"=>"项目名称",
"code"=>"项目文件夹",
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
	 *	@url ApplicationController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT `m`.`id`,`m`.`label`,`m`.`code`,`m`.`is_enabled`,`m`.`is_system`,`bi`.`name` AS `icon_name` FROM `".$this->table()."` AS m LEFT JOIN `button_icon` AS `bi` ON `m`.`icon`=`bi`.`id`";
		$str = '';
		if($where['label'] != "")
		{
			$str .= "`m`.`label` like \"%".addslashes($where['label'])."%\" AND ";
		}
		if($where['code'] != "")
		{
			$str .= "`m`.`code` like \"%".addslashes($where['code'])."%\" AND ";
		}
		if(isset($where['is_deleted']))
		{
			$str .= "`m`.`is_deleted` =".$where['is_deleted']." AND ";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `m`.`display_order` DESC,`m`.`id` DESC";

		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	/*
	*  -------------------------------------------------
	*   getAppList，项目列表
	*  -------------------------------------------------
	*/
	public function getAppList ()
	{
		$sql = "SELECT `id`,`label`,`code` FROM `".$this->table()."` WHERE `is_deleted`=0 AND `is_enabled`=1 ORDER BY `display_order` DESC,`id`";
		return $this->db()->getAll($sql);
	}

	/**
	 *	saveSort，排序
	 *
	 *	@url ApplicationController/saveSort
	 */
	public function saveSort ($datas)
	{
		$len = count($datas);
		try{
			for ($i=0;$i<$len;$i++)
			{
				$disp_order = ($datas[$i]==1) ? 10000 : ($i+1);
				$sql = "UPDATE `".$this->table()."` SET `display_order`=".$disp_order." WHERE `id`=".$datas[$i];
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
		$sql = "SELECT count(*) FROM `menu_group` WHERE `is_deleted`=0 AND `application_id`={$id} ";
		$res = $this->db()->getOne($sql);
		if($res)
		{
			return true;
		}
		$sql = "SELECT count(*) FROM `control` WHERE `is_deleted`=0 AND `application_id`={$id} ";
		return $this->db()->getOne($sql);
	}

	public function hasLabel ($label)
	{
		$sql = "SELECT count(*) FROM `".$this->table()."` WHERE `label`='{$label}'";
		if($this->pk())
		{
			$sql .=" AND `id`<>".$this->pk();
		}
		return $this->db()->getOne($sql);
	}

}

?>