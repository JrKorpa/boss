<?php
/**
 *  -------------------------------------------------
 *   @file		: ControlModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-21 17:46:57
 *   @update	:
 *  -------------------------------------------------
 */
class ControlModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'control';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"主键",
"label"=>"显示名称",
"code"=>"控制器名",
"type"=>"1为独立对象2为主对象3为明细对象",
"parent_id"=>"明细对象和主对象的关联字段0为不是明细对象",
"application_id"=>"所属项目",
"is_deleted"=>"是否删除",
"is_system"=>"系统内置");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url ControlController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT `m`.`id`,`m`.`label`,`m`.`code`,`m`.`type`,`m`.`parent_id`,`a`.`label` AS `app_name` FROM `".$this->table()."` AS `m` LEFT JOIN `application` AS `a` ON `m`.`application_id`=`a`.`id` WHERE `m`.`is_deleted`='".$where['is_deleted']."'";

		if($where['label'] != "")
		{
			$sql .= " AND `m`.`label` like \"%".addslashes($where['label'])."%\"";
		}
		if($where['code'] != "")
		{
			$sql .= " AND `m`.`code` like \"%".addslashes($where['code'])."%\"";
		}
		if($where['application_id'] != "")
		{
			$sql .= " AND `m`.`application_id`='".$where['application_id']."'";
		}
		//这里是增加了对control类型的检索
		if($where['type']!=""){
			$sql .= " AND `m`.`type`='".$where['type']."'";
		}

		$sql .= " ORDER BY `m`.`id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	/*
	*	查重
	*/
	public function hasCode ($code)
	{
		$sql = "SELECT count(*) FROM `control` WHERE `code`='{$code}'";
		if($this->pk())
		{
			$sql .=" AND id<>".$this->pk();
		}
		return $this->db()->getOne($sql);
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

	public function getCtlList ()
	{
		$sql = "SELECT `id`,`label`,`code` FROM `".$this->table()."` WHERE `is_deleted`='0'";
		return $this->db()->getAll($sql);
	}

	public function hasRelData ($id)
	{
		$sql = "SELECT count(*) FROM `menu` WHERE `c_id`='{$id}' AND `is_deleted`='0' ";
		if($this->db()->getOne($sql))
		{
			return true;
		}
		$sql = "SELECT count(*) FROM `operation` WHERE `c_id`='{$id}' AND `is_deleted`='0' ";
		if($this->db()->getOne($sql))
		{
			return true;
		}
		$sql = "SELECT count(*) FROM `button` WHERE `c_id`='{$id}' AND `is_deleted`='0' ";
		if($this->db()->getOne($sql))
		{
			return true;
		}
		return false;
	}

	public function getParentObj()
	{
		$sql="SELECT `id`,`label`,`code` FROM `control` WHERE `type`='2' AND `is_deleted`='0'";
		return $this->db()->getAll($sql);
	}
	//这里负责获取关联的明细对象
	public function getLinkObj($type,$con_id)
	{
		if($type==3)
		{
			$sql = "SELECT `label`,`id`,`code` FROM `control` WHERE `id`='".$con_id."' AND `is_deleted`='0'";
			return $this->db()->getAll($sql);
		}
		else
		{
			$sql = "SELECT `label`,`id`,`code` FROM `control` WHERE `parent_id`='".$con_id."' AND `is_deleted`='0'";
			return $this->db()->getAll($sql);
		}
	}

	//获取这个文件的列表按钮
	public function getButtons($c_id)
	{
		$sql ="SELECT `b`.`label` as `label`,`b`.`display_order`, `bi`.`name` as `icon_name`,`b`.`id` as `id`,`b`.`tips` as `tips`  FROM  `button` as `b` LEFT JOIN  `control` as `c` ON `b`.`c_id` = `c`.`id` LEFT JOIN `button_icon` as `bi` ON `b`.`icon_id`=`bi`.`id` WHERE c.`id`='".$c_id."' AND `b`.`type`='1' AND `b`.`is_deleted`='0' ORDER BY `b`.`display_order` DESC";

		return $this->db()->getAll($sql);
	}

	//查看页按钮排序
	public function getButtonss($c_id)
	{
		$sql ="SELECT `b`.`label` as `label`,`b`.`display_order`, `bi`.`name` as `icon_name`,`b`.`id` as `id`,`b`.`tips` as `tips`  FROM  `button` as `b` LEFT JOIN  `control` as `c` ON `b`.`c_id` = `c`.`id` LEFT JOIN `button_icon` as `bi` ON `b`.`icon_id`=`bi`.`id` WHERE c.`id`='".$c_id."' AND `b`.`type`='2' AND `b`.`is_deleted`='0' ORDER BY `b`.`display_order` DESC";
		return $this->db()->getAll($sql);
	}

	/**
	 *	sortButton，按钮保存排序
	 *
	 *	@url MenuController/saveSort
	 */
	public function sortButton ($btns)
	{
		$len = count($btns);

		try{
			for ($i=0;$i<$len;$i++)
			{
				if($btns[$i]<21)
				{
					continue;
				}
				$sql = "UPDATE `button` SET `display_order`='".($i+21)."' WHERE `id`='".$btns[$i]."'";
				$this->db()->query($sql);
			}
		}
		catch(Exception $e)
		{
			return false;
		}
		return true;
	}



	public function getSonObj($id)
	{
		$sql ="SELECT count(id) FROM `control` WHERE `is_deleted`='0' AND `parent_id`='".$id."'";
		return $this->db()->getOne($sql);
	}


    //根据控制器关联删除操作 按钮 和控制器本身
    public function reldelete($c_id){

        if(isset($c_id)){
            //关联删除
          /*  $sql = "delete from control as c LEFT JOIN operation as o ON c.id=o.c_id LEFT JOIN button as b on c.id=b.c_id WHERE c.id=$c_id";*/
        $sqlc = "delete from control where id=".$c_id;
        $resc = $this->db()->query($sqlc);
        $sqlb = "delete from button WHERE c_id=".$c_id;
        $this->db()->query($sqlb);
        $sqlo = "delete from operation WHERE c_id=".$c_id;
         return $this->db()->query($sqlo);
        }else{
            return false;
        }




    }



}

?>