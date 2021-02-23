<?php
/**
 *  -------------------------------------------------
 *   @file		: MenuModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-08 15:17:33
 *   @update	:
 *  -------------------------------------------------
 */
class MenuModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'menu';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"菜单id",
"c_id"=>"所属文件",
"o_id"=>"请求操作",
"label"=>"菜单名称",
"code"=>"编码",
"url"=>"地址",
"icon"=>"图标",
"group_id"=>"所属菜单组",
"application_id"=>"所属项目",
"display_order"=>"显示顺序",
"is_enabled"=>"是否启用",
"is_system"=>"是否系统内置",
"type"=>"菜单类型：1、通用，2、库管可见，3、渠道操作员可见",
"is_deleted"=>"是否删除",
"is_out"=>'是否链接外部系统'                
                );
		parent::__construct($id,$strConn);
	}

	public function getMenuOptions ()
	{
		$sql = "SELECT `app`.`id` AS `app_id`,`app`.`label` AS `app_label`,`app`.`code` AS `code`,`g`.`id` AS `group_id`,`g`.`label` AS `group_label`,`g`.`application_id` FROM `application` AS `app` LEFT JOIN (SELECT * FROM `menu_group` WHERE `is_deleted`='0' AND `is_enabled`='1' ORDER BY `display_order` DESC ) AS `g` ON `app`.`id`=`g`.`application_id` WHERE `app`.`is_deleted`='0' AND `app`.`is_enabled`='1' ORDER BY `app`.`display_order` DESC ";
		$res = $this->db()->getAll($sql);
		$data = array();
		foreach ($res as $val )
		{
			if(!isset($data[$val['app_id']]))
			{
				$data[$val['app_id']]=$val;
			}
			$data[$val['app_id']]['son'][] = $val;
		}
		return $data;
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url MenuController/search
	 */
	public function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT `m`.`id`,`m`.`label`,`m`.`code`,`m`.`url`,`m`.`type`,`m`.`is_system`,`m`.`is_enabled`,`m`.`group_id`,`g`.`label` AS `group_name`,`a`.`label` AS `app_label` FROM `".$this->table()."` AS `m` INNER JOIN `menu_group` AS `g` ON `m`.`group_id`=`g`.`id` INNER JOIN `application` AS `a` ON `m`.`application_id`=`a`.`id` ";

		$str = '';
		if($where['group_id'])
		{
			$str .= "`m`.`group_id`='".$where['group_id']."' AND ";
		}
		if(!empty($where['type']))
		{
			$str .= "`m`.`type`='".$where['type']."' AND ";
		}
		if(isset($where['is_deleted']))
		{
			$str .= "`m`.`is_deleted`='".$where['is_deleted']."' AND ";
		}
		if($where['label'] != "")
		{
			$str .= "`m`.`label` LIKE \"%".addslashes($where['label'])."%\" AND ";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}

		$sql .= " ORDER BY `m`.`id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	public function hasLabel ($name)
	{
		$sql = "SELECT count(*) FROM `".$this->table()."` WHERE `label`='{$name}'";
		if($this->pk())
		{
			$sql .=" AND id<>".$this->pk();
		}
		return $this->db()->getOne($sql);
	}

	public function hasUrl ($url)
	{
		$sql = "SELECT count(*) FROM `".$this->table()."` WHERE `url`='{$url}'";
		if($this->pk())
		{
			$sql .=" AND id<>".$this->pk();
		}
		return $this->db()->getOne($sql);
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


	public function getControls ($app_id)
	{
		$sql = "SELECT `id`,`label`,`code` FROM `control` WHERE `application_id`='".$app_id."' AND `type`<3 AND `is_deleted`='0'";
		return $this->db()->getAll($sql);
	}

	public function getOperations ($c_id)
	{
		$sql = "SELECT `id`,`label`,`method_name` FROM `operation` WHERE `c_id`='".$c_id."' AND `is_deleted`='0'";
		return $this->db()->getAll($sql);
	}
    //获取跳转id和标签的的方法
    public function getMenuId($url){
        if(empty($url)){
            return false;
        }
        $sql = "SELECT `id`,`label`,`url` FROM `".$this->table()."` WHERE `url`='{$url}'";
        return $this->db()->getRow($sql);
    }


}

?>