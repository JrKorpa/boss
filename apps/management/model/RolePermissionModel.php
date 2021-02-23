<?php
/**
 *  -------------------------------------------------
 *   @file		: RolePermissionModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-23 09:53:07
 *   @update	:
 *  -------------------------------------------------
 */
class RolePermissionModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = '';
        $this->_dataObject = array();
		parent::__construct($id,$strConn);
	}

	/*
	 * getRoleList,获取角色列表
	 *
	 */
	function getRoleList()
	{
		$sql = "SELECT * FROM `role` WHERE `is_deleted`='0'";
		if(Auth::$userType>1)
		{
			$sql .=" AND id>1";	//只有超级管理员可以给授权管理员角色添加用户和添加授权
		}
		$res = $this->db()->getAll($sql);
		return $res;
	}

	public function getMenuData ($role_id,$is_menu=false)
	{
		$sql = "SELECT id FROM `resource_type` WHERE `code`='MENU' ";
		$row = $this->db()->getOne($sql);
		if(!$row)
		{
			return false;
		}

		$sql = "SELECT p.id,p.name AS label,p.code,p.resource_id,ifnull(r.permission_id,0) AS chk FROM `permission` AS p LEFT JOIN (SELECT permission_id FROM `role_menu_permission` WHERE `role_id`='{$role_id}') AS r ON p.id=r.permission_id WHERE p.is_deleted=0 AND p.type='".$row."' order by p.id";
		$sql = "SELECT t.*,m.group_id AS parent_id,m.application_id FROM (".$sql.") AS t INNER JOIN `menu` AS m ON t.`resource_id`=`m`.`id` WHERE `m`.`is_deleted`='0' AND m.`is_enabled`='1' AND `m`.`type`='1' ";
		$data = $this->db()->getAll($sql);
		Util::array_unique_fb($data,'id');

		$sql = "SELECT a.id,a.label,mg.id AS gid,mg.label AS gname FROM `application` AS a LEFT JOIN `menu_group` AS mg ON a.id=mg.application_id WHERE a.`is_deleted`=0 AND a.is_enabled=1 ";
		$data1 = $this->db()->getAll($sql);
		$datas = array();
		$relation = array();//记录新的从属关系
		$i=0;
		foreach ($data1 as $val )
		{
			$i++;
			if(!isset($relation[1][$val['id']]))
			{
				$relation[1][$val['id']] = $i;
				$datas[$i] = array('id'=>$val['id'],'label'=>$val['label'],'parent_id'=>0,'i'=>$i,'type'=>1);
				$i++;
			}
			$relation[2][$val['gid']] = $i;
			$datas[$relation[1][$val['id']]]['son'][] = $i;
			$datas[$i] = array('id'=>$val['gid'],'label'=>$val['gname'],'parent_id'=>$relation[1][$val['id']],'i'=>$i,'type'=>2);
		}

		$last_index = $i;
		foreach ($data as $v )
		{
			if(isset($relation[2][$v['parent_id']]) && isset($datas[$relation[2][$v['parent_id']]]))
			{
				$i++;
				$datas[$relation[2][$v['parent_id']]]['son'][] = $i;
				$datas[$i] = array('id'=>$v['id'],'label'=>$v['label'],'code'=>$v['code'],'parent_id'=>$relation[2][$v['parent_id']],'i'=>$i,'chk'=>$v['chk'],'resource_id'=>$v['resource_id'],'type'=>3);
			}
		}

		foreach ($datas as $key => $val )
		{
			if($key<=$last_index)
			{
				if(!isset($val['son']) || count($val['son'])==0)
				{
					unset($datas[$key]);
					if($val['type']==2)
					{
						unset($datas[$val['parent_id']]['son'][array_search($val['i'],$datas[$val['parent_id']]['son'])]);
					}
				}
				else
				{
					if(!isset($val['son']) && $val['son'][0]<=$last_index)
					{
						unset($datas[$key]);
						$kk = array_search($val['i'],$datas[$val['parent_id']]['son']);
						if($kk!==false)
						{
							unset($datas[$val['parent_id']]['son'][$kk]);
						}
					}
				}
			}
			else
			{
				if(!$is_menu)
				{
					if(!$val['chk'])
					{
						unset($datas[$val['parent_id']]['son'][array_search($val['i'],$datas[$val['parent_id']]['son'])]);
						unset($datas[$key]);
					}
				}
			}
		}
		if(!$is_menu)
		{
			foreach ($datas as $key => $val )
			{
				if($key<=$last_index)
				{
					if(isset($val['son']) && count($val['son'])==0)
					{
						unset($datas[$key]);
						if($val['parent_id'])
						{
							unset($datas[$val['parent_id']]['son'][array_search($val['i'],$datas[$val['parent_id']]['son'])]);
						}
					}
				}
			}

			foreach ($datas as $key => $val )
			{

				if($key<=$last_index)
				{
					if(isset($val['son']) && count($val['son'])==0)
					{
						unset($datas[$key]);
					}
				}
			}
		}
 		return $datas;
	}


	public function menuListDetail ($role_id)
	{
		$sql = "SELECT id FROM `resource_type` WHERE `code`='MENU' ";
		$row = $this->db()->getOne($sql);
		if(!$row)
		{
			return false;
		}

		$sql = "SELECT p.id,p.name AS label,p.code,p.resource_id FROM `permission` AS p INNER JOIN (SELECT permission_id FROM `role_menu_permission` WHERE `role_id`='{$role_id}') AS r ON p.id=r.permission_id WHERE p.is_deleted=0 AND p.type='".$row."' order by p.id";

		$sql = "SELECT t.*,m.group_id AS parent_id,m.application_id FROM (".$sql.") AS t INNER JOIN `menu` AS m ON t.`resource_id`=`m`.`id` INNER JOIN `control` AS c ON c.`id`=m.`c_id` WHERE `m`.`is_deleted`='0' AND m.`is_enabled`='1' AND c.`type`=2 ";

		$data = $this->db()->getAll($sql);

		Util::array_unique_fb($data,'id');

		$sql = "SELECT a.id,a.label,mg.id AS gid,mg.label AS gname FROM `application` AS a LEFT JOIN `menu_group` AS mg ON a.id=mg.application_id WHERE a.`is_deleted`=0 AND a.is_enabled=1 ";
		$data1 = $this->db()->getAll($sql);
		$datas = array();
		$relation = array();//记录新的从属关系
		$i=0;
		foreach ($data1 as $val )
		{
			$i++;
			if(!isset($relation[1][$val['id']]))
			{
				$relation[1][$val['id']] = $i;
				$datas[$i] = array('id'=>$val['id'],'label'=>$val['label'],'parent_id'=>0,'i'=>$i);
				$i++;
			}
			$relation[2][$val['gid']] = $i;
			$datas[$relation[1][$val['id']]]['son'][] = $i;
			$datas[$i] = array('id'=>$val['gid'],'label'=>$val['gname'],'parent_id'=>$relation[1][$val['id']],'i'=>$i);
		}
		$last_index = $i;
		foreach ($data as $v )
		{
			$i++;
			$datas[$relation[2][$v['parent_id']]]['son'][] = $i;
			$datas[$i] = array('id'=>$v['id'],'label'=>$v['label'],'code'=>$v['code'],'parent_id'=>$relation[2][$v['parent_id']],'i'=>$i,'resource_id'=>$v['resource_id']);
		}
		foreach ($datas as $key => $val )
		{
			if($key<=$last_index)
			{

				if(!isset($val['son']))
				{
					unset($datas[$key]);
				}
				else
				{
					if(!isset($datas[$val['son'][0]]['son']) && $val['son'][0]<=$last_index)
					{
						unset($datas[$key]);
					}
				}
			}
		}

		return $datas;
	}

	public function saveMenu ($role_id,$ids)
	{
		$sql = "SELECT id,permission_id FROM `role_menu_permission` WHERE `role_id`='{$role_id}'";
		$data = $this->db()->getAll($sql);
		$oldids = array_column($data,'permission_id');
		$data = array_combine(array_column($data,'id'),$oldids);
		$addIds = array_diff($ids,$oldids);
		$delIds = array_diff($oldids,$ids);
		if($delIds)
		{
			$del = array();
			$tmp = array_flip($data);

			foreach ($delIds as $val )
			{
				if(isset($tmp[$val]))
				{
					$del[] = $tmp[$val];
				}
			}

			if($del)
			{
				try{
					//删明细
					$sql = "DELETE sp.*,op.*,sbp.* FROM `role_subdetail_permission` AS sp LEFT JOIN `role_subdetail_operation_permission` AS op ON sp.permission_id=op.parent_id AND sp.role_id=op.role_id LEFT JOIN `role_subdetail_button_permission` AS sbp ON sp.permission_id=sbp.parent_id AND sp.role_id=sbp.role_id WHERE sp.`parent_id` IN (".implode(',',$delIds).") AND sp.role_id='{$role_id}'";

					$this->db()->query($sql);

					//删主对象
					$sql = "DELETE mp.*,bp.*,vbp.*,op.* FROM `role_menu_permission` AS mp LEFT JOIN `role_button_permission` AS bp ON mp.permission_id=bp.parent_id AND mp.role_id=bp.role_id LEFT JOIN `role_view_button_permission` AS vbp ON mp.permission_id=vbp.parent_id AND mp.role_id=vbp.role_id LEFT JOIN `role_operation_permission` AS op ON mp.permission_id=op.parent_id AND mp.role_id=op.role_id WHERE mp.`id` IN (".implode(',',$del).") AND mp.role_id='{$role_id}' ";
					$this->db()->query($sql);

				}
				catch(Exception $e){
					return false;
				}

			}
		}
		if($addIds)
		{
			$arr=array();
			foreach ($addIds as $id )
			{
				$arr[] = array('role_id'=>$role_id,'permission_id'=>$id);
			}

			if($arr)
			{
				try{
					$this->insertAll($arr,'role_menu_permission');
				}
				catch(Exception $e){
					return false;
				}
			}
		}
		return true;
	}

	public function getButtonData ($role_id,$permission_id,$rid)
	{
		$sql = "SELECT id FROM `resource_type` WHERE `code`='BUTTON' ";
		$row = $this->db()->getOne($sql);
		if(!$row)
		{
			return false;
		}
		//$start = microtime(true);
		//方案一：使用子查询
		//取控制器下所有列表页按钮和刷新重置离开三个按钮
		$sql = "(SELECT b.`label`,b.`tips`,b.`display_order`,`b`.`id` FROM `button` AS b WHERE EXISTS (SELECT id FROM `menu` AS m WHERE m.`id`='".$rid."' AND m.`c_id`=b.`c_id` AND m.`is_enabled`='1' AND m.`is_deleted`='0' ) AND `b`.`type`='1' AND `b`.`is_deleted`='0') UNION (SELECT b.`label`,b.`tips`,b.`display_order`,`b`.`id` FROM `button` AS b WHERE `b`.`id` IN (1,2,3) AND `b`.`is_deleted`='0') ";

		$sql = "SELECT p.id AS pid,p.code,b.`label`,b.`tips`,b.`display_order` FROM (".$sql.") AS b INNER JOIN `permission` AS p ON b.id=p.resource_id WHERE p.`type`='".$row."' AND p.`is_deleted`=0 ";

		$sql = "SELECT main.pid AS id,main.label,main.tips,main.code,ifnull(r.permission_id,0) AS chk FROM (".$sql.") AS main LEFT JOIN (SELECT permission_id FROM `role_button_permission` WHERE `role_id`='".$role_id."' AND `parent_id`='".$permission_id."') AS r ON main.pid=r.permission_id ORDER BY main.display_order DESC ";

		$data = $this->db()->getAll($sql);

//		Util::array_unique_fb($data,'id');
		return $data;
	}

	public function getViewButtonData ($role_id,$permission_id,$rid)
	{
		$sql = "SELECT id FROM `resource_type` WHERE `code`='BUTTON' ";
		$row = $this->db()->getOne($sql);
		if(!$row)
		{
			return false;
		}

		$sql = "(SELECT b.`label`,b.`tips`,b.`display_order`,`b`.`id` FROM `button` AS b WHERE EXISTS (SELECT id FROM `menu` AS m WHERE m.`id`='".$rid."' AND m.`c_id`=b.`c_id` AND m.`is_enabled`='1' AND m.`is_deleted`='0' ) AND `b`.`type`='2' AND `b`.`is_deleted`='0') UNION (SELECT b.`label`,b.`tips`,b.`display_order`,`b`.`id` FROM `button` AS b WHERE `b`.`id` IN (3,4) AND `b`.`is_deleted`='0') ";

		$sql = "SELECT p.id AS pid,p.code,b.`label`,b.`tips`,b.`display_order` FROM (".$sql.") AS b INNER JOIN `permission` AS p ON b.id=p.resource_id WHERE p.`type`='".$row."' AND p.`is_deleted`=0 ";

		$sql = "SELECT main.pid AS id,main.label,main.tips,main.code,ifnull(r.permission_id,0) AS chk FROM (".$sql.") AS main LEFT JOIN (SELECT permission_id FROM `role_view_button_permission` WHERE `role_id`='".$role_id."' AND `parent_id`='".$permission_id."') AS r ON main.pid=r.permission_id ORDER BY main.display_order DESC ";
		$data = $this->db()->getAll($sql);

//		Util::array_unique_fb($data,'id');

		return $data;
	}

	public function saveButton ($role_id,$parent_id,$ids)
	{
		$sql = "SELECT id,permission_id FROM `role_button_permission` WHERE `role_id`='{$role_id}' AND `parent_id`='{$parent_id}'";
		$data = $this->db()->getAll($sql);
		$oldids = array_column($data,'permission_id');
		$data = array_combine(array_column($data,'id'),$oldids);
		$addIds = array_diff($ids,$oldids);
		$delIds = array_diff($oldids,$ids);
		if($delIds)
		{
			$del = array();
			$tmp = array_flip($data);

			foreach ($delIds as $val )
			{
				if(isset($tmp[$val]))
				{
					$del[] = $tmp[$val];
				}
			}

			if($del)
			{
				try{
					$sql = "DELETE FROM `role_button_permission` WHERE `id` IN (".implode(',',$del).") ";
					$this->db()->query($sql);
				}
				catch(Exception $e){
					return false;
				}
			}
		}
		if($addIds)
		{
			$arr=array();
			foreach ($addIds as $id )
			{
				$arr[] = array('role_id'=>$role_id,'permission_id'=>$id,'parent_id'=>$parent_id);
			}

			if($arr)
			{
				try{
					$this->insertAll($arr,'role_button_permission');
				}
				catch(Exception $e){
					return false;
				}
			}
		}
		return true;
	}

	//查看页按钮保存
	public function saveViewButton ($role_id,$parent_id,$ids)
	{
		$sql = "SELECT id,permission_id FROM `role_view_button_permission` WHERE `role_id`='{$role_id}' AND `parent_id`='{$parent_id}'";
		$data = $this->db()->getAll($sql);
		$oldids = array_column($data,'permission_id');
		$data = array_combine(array_column($data,'id'),$oldids);
		$addIds = array_diff($ids,$oldids);
		$delIds = array_diff($oldids,$ids);
		if($delIds)
		{
			$del = array();
			$tmp = array_flip($data);

			foreach ($delIds as $val )
			{
				if(isset($tmp[$val]))
				{
					$del[] = $tmp[$val];
				}
			}

			if($del)
			{
				try{
					$sql = "DELETE FROM `role_view_button_permission` WHERE `id` IN (".implode(',',$del).") ";
					$this->db()->query($sql);
				}
				catch(Exception $e){
					return false;
				}
			}
		}
		if($addIds)
		{
			$arr=array();
			foreach ($addIds as $id )
			{
				$arr[] = array('role_id'=>$role_id,'permission_id'=>$id,'parent_id'=>$parent_id);
			}

			if($arr)
			{
				try{
					$this->insertAll($arr,'role_view_button_permission');
				}
				catch(Exception $e){
					return false;
				}
			}
		}
		return true;
	}


	/**
	 * getOprData,根据角色id和权限id获取对应control的操作方法
	 */
	public function getOprData ($role_id,$permission_id,$rid)
	{
		$sql = "SELECT id FROM `resource_type` WHERE `code`='OPERATION' ";
		$row = $this->db()->getOne($sql);
		if(!$row)
		{
			return false;
		}

		$sql = "SELECT `b`.`label`,`b`.`method_name`,`p`.`id` AS `pid` FROM `operation` AS `b` INNER JOIN `permission` AS `p` ON  `b`.`id`=`p`.`resource_id` AND `b`.`is_deleted`=`p`.`is_deleted` WHERE EXISTS (SELECT `id` FROM `menu` AS `m` WHERE `m`.`id`='".$rid."' AND `m`.`c_id`=`b`.`c_id` AND `m`.`is_deleted`='0' AND `m`.`is_enabled`='1' ) AND `p`.`type`='".$row."' AND `b`.`is_deleted`='0' ";

		$sql = "SELECT `main`.`pid` AS `id`,`main`.`label`,`main`.`method_name`,ifnull(`r`.`permission_id`,0) AS `chk` FROM (".$sql.") AS `main` LEFT JOIN (SELECT `permission_id` FROM `role_operation_permission` WHERE `role_id`='".$role_id."' AND `parent_id`='".$permission_id."') AS `r` ON `main`.`pid`=`r`.`permission_id` ORDER BY `main`.`pid`";
		$data = $this->db()->getAll($sql);

//		Util::array_unique_fb($data,'id');

		return $data;
	}


	public function saveOperation ($role_id,$parent_id,$ids)
	{
		$sql = "SELECT id,permission_id FROM `role_operation_permission` WHERE `role_id`='{$role_id}' AND `parent_id`='{$parent_id}'";
		$data = $this->db()->getAll($sql);
		$oldids = array_column($data,'permission_id');
		$data = array_combine(array_column($data,'id'),$oldids);
		$addIds = array_diff($ids,$oldids);
		$delIds = array_diff($oldids,$ids);
		if($delIds)
		{
			$del = array();
			$tmp = array_flip($data);

			foreach ($delIds as $val )
			{
				if(isset($tmp[$val]))
				{
					$del[] = $tmp[$val];
				}
			}

			if($del)
			{

				try{
					$sql = "DELETE FROM `role_operation_permission` WHERE `id` IN (".implode(',',$del).") ";
					$this->db()->query($sql);
				}
				catch(Exception $e){
					return false;
				}
			}
		}
		if($addIds)
		{
			$arr=array();
			foreach ($addIds as $id )
			{
				$arr[] = array('role_id'=>$role_id,'permission_id'=>$id,'parent_id'=>$parent_id);
			}

			if($arr)
			{
				try{
					$this->insertAll($arr,'role_operation_permission');
				}
				catch(Exception $e){
					return false;
				}
			}
		}
		return true;
	}

	public function getRelData ($role_id,$permission_id,$rid)
	{
		$sql = "SELECT `c`.`id`,`c`.`label`,`c`.`code`,`p`.`id` AS `pid` FROM `control` AS `c` INNER JOIN `permission` AS `p` ON `c`.`id`=`p`.`resource_id` AND `c`.`is_deleted`=`p`.`is_deleted` WHERE EXISTS (SELECT `c_id` FROM `menu` AS `m` WHERE `m`.`id`='".$rid."' AND `m`.`c_id`=`c`.`parent_id` AND `m`.`is_deleted`='0' AND `m`.`is_enabled`='1') AND `c`.`type`='3' AND `p`.`type`='4' AND `c`.`is_deleted`='0' ORDER BY `c`.`id` ";

		$sql = "SELECT `main`.`pid` AS `id`,`main`.`label`,`main`.`code`,ifnull(`r`.`permission_id`,0) AS `chk` FROM (".$sql.") AS `main` LEFT JOIN (SELECT `permission_id` FROM `role_subdetail_permission` WHERE `role_id`='".$role_id."' AND `parent_id`='".$permission_id."') AS `r` ON `main`.`pid`=`r`.`permission_id` ORDER BY `main`.`pid`";
		$data = $this->db()->getAll($sql);
//		Util::array_unique_fb($data,'id');
		return $data;
	}

	public function saveRel ($role_id,$parent_id,$ids)
	{
		$sql = "SELECT id,permission_id FROM `role_subdetail_permission` WHERE `role_id`='{$role_id}' AND `parent_id`='{$parent_id}'";
		$data = $this->db()->getAll($sql);

		$oldids = array_column($data,'permission_id');
		$data = array_combine(array_column($data,'id'),$oldids);
		$addIds = array_diff($ids,$oldids);
		$delIds = array_diff($oldids,$ids);

		if($delIds)
		{
			$del = array();
			$tmp = array_flip($data);

			foreach ($delIds as $val )
			{
				if(isset($tmp[$val]))
				{
					$del[] = $tmp[$val];
				}
			}

			if($del)
			{

				try{
					$sql = "DELETE sp.*,sbp.*,sop.* FROM `role_subdetail_permission` AS sp LEFT JOIN `role_subdetail_button_permission` AS sbp ON sp.permission_id=sbp.parent_id AND sp.role_id=sbp.role_id LEFT JOIN `role_subdetail_operation_permission` AS sop ON sp.permission_id=sop.parent_id AND sp.role_id=sop.role_id WHERE sp.`id` IN (".implode(',',$del).") AND sp.role_id='{$role_id}' ";
					$this->db()->query($sql);
				}
				catch(Exception $e){
					return false;
				}
			}
		}
		if($addIds)
		{
			$arr=array();
			foreach ($addIds as $id )
			{
				$arr[] = array('role_id'=>$role_id,'permission_id'=>$id,'parent_id'=>$parent_id);
			}

			if($arr)
			{
				try{
					$this->insertAll($arr,'role_subdetail_permission');
				}
				catch(Exception $e){
					return false;
				}
			}
		}
		return true;
	}

	public function getMenuDetail ($role_id)
	{
		//主对象
		$sql = "SELECT p.id,p.resource_id,c.label FROM `permission` AS p INNER JOIN `control` AS c ON c.id=p.resource_id AND c.is_deleted=p.is_deleted WHERE EXISTS (SELECT parent_id FROM `role_subdetail_permission` AS r WHERE r.`role_id`='".$role_id."' AND p.id=r.parent_id ) AND p.type=1 AND p.is_deleted='0' ";
		$data = $this->db()->getAll($sql);
//		Util::array_unique_fb($data,'id');
		//明细对象
		$sql = "SELECT p.id,p.resource_id,c.label,r.parent_id FROM `permission` AS p INNER JOIN `role_subdetail_permission` AS r ON p.id=r.permission_id INNER JOIN `control` AS c ON c.id=p.resource_id AND c.is_deleted=p.is_deleted WHERE r.`role_id`='".$role_id."' AND p.type=4 AND p.is_deleted='0'";

		$data1 = $this->db()->getAll($sql);
//		Util::array_unique_fb($data1,'id');
		$datas = array();

		foreach ($data as $val )
		{
			$val['parent_id']=0;
			$datas[] = $val;
		}
		foreach ($data1 as $val1 )
		{
			$datas[] = $val1;
		}

		return $datas;
	}

	public function getRelButtonData ($role_id,$permission_id,$rid)
	{
		$sql = "SELECT id FROM `resource_type` WHERE `code`='BUTTON' ";
		$row = $this->db()->getOne($sql);
		if(!$row)
		{
			return false;
		}
		//$start = microtime(true);
		//取控制器下所有列表页按钮
		$sql = "SELECT b.`label`,b.`tips`,b.`display_order`,`b`.`id` FROM `button` AS b WHERE `b`.`c_id`='".$rid."' AND `b`.`type`='1' AND `b`.`is_deleted`='0' ";

		$sql = "SELECT p.id AS pid,p.code,b.`label`,b.`tips`,b.`display_order` FROM (".$sql.") AS b INNER JOIN `permission` AS p ON b.id=p.resource_id WHERE p.`type`='".$row."' AND p.`is_deleted`=0 ";

		$sql = "SELECT main.pid AS id,main.label,main.tips,main.code,ifnull(r.permission_id,0) AS chk FROM (".$sql.") AS main LEFT JOIN (SELECT permission_id FROM `role_subdetail_button_permission` WHERE `role_id`='".$role_id."' AND `parent_id`='".$permission_id."') AS r ON main.pid=r.permission_id ORDER BY main.display_order DESC ";

		$data = $this->db()->getAll($sql);

//		Util::array_unique_fb($data,'id');

		return $data;
	}

	public function saveRelButton ($role_id,$parent_id,$ids)
	{
		$sql = "SELECT id,permission_id FROM `role_subdetail_button_permission` WHERE `role_id`='{$role_id}' AND `parent_id`='{$parent_id}'";
		$data = $this->db()->getAll($sql);

		$oldids = array_column($data,'permission_id');
		$data = array_combine(array_column($data,'id'),$oldids);
		$addIds = array_diff($ids,$oldids);
		$delIds = array_diff($oldids,$ids);

		if($delIds)
		{
			$del = array();
			$tmp = array_flip($data);

			foreach ($delIds as $val )
			{
				if(isset($tmp[$val]))
				{
					$del[] = $tmp[$val];
				}
			}

			if($del)
			{

				try{
					$sql = "DELETE FROM `role_subdetail_button_permission` WHERE `id` IN (".implode(',',$del).") ";
					$this->db()->query($sql);
				}
				catch(Exception $e){
					return false;
				}
			}
		}
		if($addIds)
		{
			$arr=array();
			foreach ($addIds as $id )
			{
				$arr[] = array('role_id'=>$role_id,'permission_id'=>$id,'parent_id'=>$parent_id);
			}

			if($arr)
			{
				try{
					$this->insertAll($arr,'role_subdetail_button_permission');
				}
				catch(Exception $e){
					return false;
				}
			}
		}
		return true;
	}

	public function getRelOprData ($role_id,$permission_id,$rid)
	{
		$sql = "SELECT id FROM `resource_type` WHERE `code`='OPERATION' ";
		$row = $this->db()->getOne($sql);
		if(!$row)
		{
			return false;
		}

		$sql = "SELECT `b`.`label`,`b`.`method_name`,`p`.`id` AS `pid` FROM `operation` AS `b` INNER JOIN `permission` AS `p` ON  `b`.`id`=`p`.`resource_id` AND `b`.`is_deleted`=`p`.`is_deleted` WHERE `b`.`c_id`='".$rid."' AND `p`.`type`='".$row."' AND `b`.`is_deleted`='0' ";

		$sql = "SELECT `main`.`pid` AS `id`,`main`.`label`,`main`.`method_name`,ifnull(`r`.`permission_id`,0) AS `chk` FROM (".$sql.") AS `main` LEFT JOIN (SELECT `permission_id` FROM `role_subdetail_operation_permission` WHERE `role_id`='".$role_id."' AND `parent_id`='".$permission_id."') AS `r` ON `main`.`pid`=`r`.`permission_id` ORDER BY `main`.`pid`";
		$data = $this->db()->getAll($sql);

//		Util::array_unique_fb($data,'id');

		return $data;
	}

	public function saveRelOpr ($role_id,$parent_id,$ids)
	{
		$sql = "SELECT id,permission_id FROM `role_subdetail_operation_permission` WHERE `role_id`='{$role_id}' AND `parent_id`='{$parent_id}'";
		$data = $this->db()->getAll($sql);

		$oldids = array_column($data,'permission_id');
		$data = array_combine(array_column($data,'id'),$oldids);
		$addIds = array_diff($ids,$oldids);
		$delIds = array_diff($oldids,$ids);

		if($delIds)
		{
			$del = array();
			$tmp = array_flip($data);

			foreach ($delIds as $val )
			{
				if(isset($tmp[$val]))
				{
					$del[] = $tmp[$val];
				}
			}

			if($del)
			{

				try{
					$sql = "DELETE FROM `role_subdetail_operation_permission` WHERE `id` IN (".implode(',',$del).") ";
					$this->db()->query($sql);
				}
				catch(Exception $e){
					return false;
				}
			}
		}
		if($addIds)
		{
			$arr=array();
			foreach ($addIds as $id )
			{
				$arr[] = array('role_id'=>$role_id,'permission_id'=>$id,'parent_id'=>$parent_id);
			}

			if($arr)
			{
				try{
					$this->insertAll($arr,'role_subdetail_operation_permission');
				}
				catch(Exception $e){
					return false;
				}
			}
		}
		return true;
	}
}

?>