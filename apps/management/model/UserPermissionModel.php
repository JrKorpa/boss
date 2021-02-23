<?php
/**
 *  -------------------------------------------------
 *   @file		: RolePermissionModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-28 12:12:07
 *   @update	:
 *  -------------------------------------------------
 */
class UserPermissionModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = '';
        $this->_dataObject = array();
		parent::__construct($id,$strConn);
	}

	public function searchUser ($q)
	{//只能给类型比自己低的用户授权
		$sql = "SELECT id,account,real_name FROM `user` WHERE `is_deleted`='0' AND `is_on_work`=1 AND `user_type`>'".Auth::$userType."' AND ( `account` LIKE '%{$q}%' OR `real_name` LIKE '%{$q}%' )";
		return $this->db()->getAll($sql);
	}

	public function getMenuData ($user_id,$is_menu=false)
	{
//		$sql = "SELECT id FROM `resource_type` WHERE `code`='MENU' ";
//		$row = $this->db()->getOne($sql);
//		if(!$row)
//		{
//			return false;
//		}
                $row=1;

		$sql = "SELECT p.id,p.name AS label,p.code,p.resource_id,ifnull(r.permission_id,0) AS chk_r,ifnull(ump.permission_id,0) AS chk_u FROM `permission` AS p LEFT JOIN (SELECT distinct permission_id FROM `role_menu_permission` AS rmp WHERE EXISTS (SELECT tt.role_id FROM ((SELECT role_id FROM `group_role` WHERE EXISTS (SELECT id FROM `group_user` WHERE `user_id`='".$user_id."' AND group_role.group_id=group_user.group_id)) UNION (SELECT role_id FROM `role_user` WHERE `user_id`='".$user_id."')) AS tt WHERE tt.role_id=rmp.role_id)) AS r ON p.id=r.permission_id LEFT JOIN (SELECT distinct permission_id FROM `user_menu_permission` WHERE `user_id`=".$user_id.") AS ump ON p.id=ump.permission_id WHERE p.is_deleted=0 AND p.type='".$row."' order by p.id";

		$sql = "SELECT t.*,m.group_id AS parent_id,m.application_id FROM (".$sql.") AS t INNER JOIN `menu` AS m ON t.`resource_id`=`m`.`id` WHERE `m`.`is_deleted`='0' AND m.`is_enabled`='1' AND `m`.`type`='1' ";

		$data = $this->db()->getAll($sql);

		Util::array_unique_fb($data,'id');

		$sql = "SELECT a.id,a.label,mg.id AS gid,mg.label AS gname FROM `application` AS a LEFT JOIN `menu_group` AS mg ON a.id=mg.application_id WHERE a.`is_deleted`=0 AND a.is_enabled=1 ";
		$data1 = $this->db()->getAll($sql);

		$datas = array();
		$relation = array();//记录新的从属关系
                $tmp = array();
                $myPower = $_SESSION["__menu_p"];

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
                        if(Auth::$userType==2 && !isset($myPower[1][0][$v['id']])){
                                continue;
                        }
                        if(isset($tmp[$v['id']]))
                        {
                                continue;
                        }
                        $tmp[$v['id']]=$v['id'];
			if(isset($relation[2][$v['parent_id']]) && isset($datas[$relation[2][$v['parent_id']]]))
			{
				$i++;
				$datas[$relation[2][$v['parent_id']]]['son'][] = $i;
				$datas[$i] = array('id'=>$v['id'],'label'=>$v['label'],'code'=>$v['code'],'parent_id'=>$relation[2][$v['parent_id']],'i'=>$i,'chk_r'=>$v['chk_r'],'chk_u'=>$v['chk_u'],'resource_id'=>$v['resource_id'],'type'=>3);
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
					if(!$val['chk_u'])
					{
						unset($datas[$val['parent_id']]['son'][array_search($val['i'],$datas[$val['parent_id']]['son'])]);
						unset($datas[$key]);
					}
				}
			}
		}
//		if(!$is_menu)
//		{
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
//		}
		return $datas;
	}

	public function saveMenu ($user_id,$ids)
	{
		$sql = "SELECT id,permission_id FROM `user_menu_permission` WHERE `user_id`='{$user_id}'";
		$data = $this->db()->getAll($sql);
		$oldids = array_column($data,'permission_id');//原有权限
		$data = array_combine(array_column($data,'id'),$oldids);//以id为key重组
		$addIds = array_diff($ids,$oldids);//新增的权限
		$delIds = array_diff($oldids,$ids);//去掉的权限ids
                if(Auth::$userType==2){
                        $myPower = $_SESSION["__menu_p"];
                        //delids  要删的（需要去掉交叉授权的）
                        $delIds = array_intersect($delIds,array_values($myPower[1][0]));
                }
                
		if($delIds)
		{
                        //明细权限ids
                        $sql = "select permission_id from user_subdetail_permission where user_id='{$user_id}' and parent_id IN (".implode(',',$delIds).")";
                        $res = $this->db()->getAll($sql);
                        $dtlids = array_column($res,'permission_id');
                        if($dtlids)
                        {
                                //删明细按钮
                                $sql = "delete from user_subdetail_button_permission where user_id='{$user_id}' and parent_id IN (".implode(',',$dtlids).")";
                                $this->db()->query($sql);
                                //删明细操作
                                $sql = "delete from user_subdetail_operation_permission where user_id='{$user_id}' and parent_id IN (".implode(',',$dtlids).")";
                                $this->db()->query($sql);
                                //删除明细
                                $sql = "delete from user_subdetail_permission where user_id='{$user_id}' and parent_id IN (".implode(',',$delIds).")";
                                $this->db()->query($sql);
                        }
                        
                        //删除操作
                        $sql = "delete from user_operation_permission where user_id='{$user_id}' and parent_id IN (".implode(',',$delIds).")";
                        $this->db()->query($sql);
                        //删除按钮
                        $sql = "delete from user_button_permission where user_id='{$user_id}' and parent_id IN (".implode(',',$delIds).")";
                        $this->db()->query($sql);
                        //删除查看按钮
                        $sql = "delete from user_view_button_permission where user_id='{$user_id}' and parent_id IN (".implode(',',$delIds).")";
                        $this->db()->query($sql);
                        //删除主菜单对象
                        $sql = "delete from user_menu_permission where user_id='{$user_id}' AND permission_id IN (".implode(',',$delIds).")";
                        $this->db()->query($sql);
                        
                        //查属性控制
                        $fids = $delIds;
                        if($dtlids)
                        {
                                $fids = array_merge($fids,$dtlids);
                        }
                        //删除对象属性控制
                        $sql = "delete uf.* FROM `permission` AS p,`field_scope` AS f,`permission` AS pp,`user_scope` AS uf WHERE p.resource_id=f.c_id AND f.id=pp.resource_id AND pp.type=5 AND pp.id=uf.permission_id AND uf.user_id='{$user_id}' AND uf.type=1 AND p.id IN (".  implode(',', $fids).") ";
                        $this->db()->query($sql);
		}
		if($addIds)
		{
			$arr=array();
			foreach ($addIds as $id )
			{
				$arr[] = array('user_id'=>$user_id,'permission_id'=>$id);
			}
			if($arr)
			{
				try{
					$this->insertAll($arr,'user_menu_permission');
				}
				catch(Exception $e){
					return false;
				}
			}
		}
		return true;
	}

	public function getButtonData ($user_id,$permission_id,$rid)
	{
		$sql = "SELECT id FROM `resource_type` WHERE `code`='BUTTON' ";
		$row = $this->db()->getOne($sql);
		if(!$row)
		{
			return false;
		}

		$sql = "SELECT `main`.`pid` AS `id`,`main`.`label`,ifnull(uop.permission_id,0) AS chk_u,ifnull(tmp.permission_id,0) AS chk_r FROM ((SELECT `b`.`label`,`p`.`id` AS `pid` FROM `button` AS `b` INNER JOIN `permission` AS `p` ON  `b`.`id`=`p`.`resource_id` AND `b`.`is_deleted`=`p`.`is_deleted` WHERE EXISTS (SELECT c_id FROM `menu` AS m WHERE m.`id`='".$rid."' AND m.`c_id`=`b`.`c_id` AND `m`.`is_deleted`='0' AND `m`.`is_enabled`='1') AND `p`.`type`='".$row."' AND b.type='1' AND `b`.`is_deleted`='0') UNION (SELECT `b`.`label`,`p`.`id` AS `pid` FROM `button` AS b INNER JOIN `permission` AS `p` ON `b`.`id`=`p`.`resource_id` AND `b`.`is_deleted`=`p`.`is_deleted` WHERE b.id IN (1,2,3) AND `p`.`type`='".$row."' AND b.type IN (1,3) AND `b`.`is_deleted`='0')) AS `main` LEFT JOIN (SELECT distinct `permission_id` FROM `user_button_permission` WHERE `user_id`='".$user_id."' AND `parent_id`='".$permission_id."') AS `uop` ON `main`.`pid`=`uop`.`permission_id` LEFT JOIN (SELECT distinct permission_id FROM `role_button_permission` AS rop WHERE EXISTS (SELECT role_id FROM ((SELECT role_id FROM `group_role` WHERE EXISTS (SELECT * FROM `group_user` WHERE `user_id`=".$user_id." AND group_role.group_id=group_user.group_id)) UNION (SELECT role_id FROM `role_user` WHERE `user_id`=".$user_id.")) AS tt WHERE tt.role_id=rop.role_id ) AND rop.parent_id=".$permission_id.") AS tmp ON tmp.permission_id=main.pid  ORDER BY `main`.`pid`";
		$data = $this->db()->getAll($sql);
                if(Auth::$userType==2)
                {
                        $myPower = array_values(Auth::get__buttons());
                        foreach($data AS $key =>$val){
                                if(!in_array($val['id'],$myPower)){
                                        unset($data[$key]);
                                }
                        }              
                }
		//Util::array_unique_fb($data,'id');

		return $data;
	}

	public function saveButton ($user_id,$parent_id,$ids)
	{
		$sql = "SELECT id,permission_id FROM `user_button_permission` WHERE `user_id`='{$user_id}' AND `parent_id`='{$parent_id}'";
		$data = $this->db()->getAll($sql);
		$oldids = array_column($data,'permission_id');
		$data = array_combine(array_column($data,'id'),$oldids);
		$addIds = array_diff($ids,$oldids);
		$delIds = array_diff($oldids,$ids);
                if(Auth::$userType==2){
                        $myPower = array_values(Auth::get__buttons());
                        $delIds = array_intersect($delIds,$myPower); 
                }
		if($delIds)
		{
                        $sql = "DELETE FROM `user_button_permission` WHERE `user_id`='{$user_id}' AND permission_id IN (".  implode(',', $delIds).") ";
                        $this->db()->query($sql);
		}
		if($addIds)
		{
			$arr=array();
			foreach ($addIds as $id )
			{
				$arr[] = array('user_id'=>$user_id,'permission_id'=>$id,'parent_id'=>$parent_id);
			}

			if($arr)
			{
				try{
					$this->insertAll($arr,'user_button_permission');
				}
				catch(Exception $e){
					return false;
				}
			}
		}
		return true;
	}


	public function getViewButtonData ($user_id,$permission_id,$rid)
	{
		$sql = "SELECT id FROM `resource_type` WHERE `code`='BUTTON' ";
		$row = $this->db()->getOne($sql);
		if(!$row)
		{
			return false;
		}

		$sql = "SELECT `main`.`pid` AS `id`,`main`.`label`,ifnull(uop.permission_id,0) AS chk_u,ifnull(tmp.permission_id,0) AS chk_r FROM ((SELECT `b`.`label`,`p`.`id` AS `pid` FROM `button` AS `b` INNER JOIN `permission` AS `p` ON  `b`.`id`=`p`.`resource_id` AND `b`.`is_deleted`=`p`.`is_deleted` WHERE EXISTS (SELECT c_id FROM `menu` AS m WHERE m.`id`='".$rid."' AND m.`c_id`=`b`.`c_id` AND `m`.`is_deleted`='0' AND `m`.`is_enabled`='1') AND `p`.`type`='".$row."' AND b.type='2' AND `b`.`is_deleted`='0') UNION (SELECT `b`.`label`,`p`.`id` AS `pid` FROM `button` AS b INNER JOIN `permission` AS `p` ON `b`.`id`=`p`.`resource_id` AND `b`.`is_deleted`=`p`.`is_deleted` WHERE b.id IN (3,4) AND `p`.`type`='".$row."' AND `b`.`is_deleted`='0')) AS `main` LEFT JOIN (SELECT distinct `permission_id` FROM `user_view_button_permission` WHERE `user_id`='".$user_id."' AND `parent_id`='".$permission_id."') AS `uop` ON `main`.`pid`=`uop`.`permission_id` LEFT JOIN (SELECT distinct permission_id FROM `role_view_button_permission` AS rop WHERE EXISTS (SELECT role_id FROM ((SELECT role_id FROM `group_role` WHERE EXISTS (SELECT * FROM `group_user` WHERE `user_id`=".$user_id." AND group_role.group_id=group_user.group_id)) UNION (SELECT role_id FROM `role_user` WHERE `user_id`=".$user_id.")) AS tt WHERE tt.role_id=rop.role_id ) AND rop.parent_id=".$permission_id.") AS tmp ON tmp.permission_id=main.pid  ORDER BY `main`.`pid`";
		$data = $this->db()->getAll($sql);
		//Util::array_unique_fb($data,'id');
                if(Auth::$userType==2)
                {
                        $myPower = array_values(Auth::get__buttons());
                        foreach($data AS $key =>$val){
                                if(!in_array($val['id'],$myPower)){
                                        unset($data[$key]);
                                }
                        }              
                }
		return $data;
	}

	public function saveViewButton ($user_id,$parent_id,$ids)
	{
		$sql = "SELECT id,permission_id FROM `user_view_button_permission` WHERE `user_id`='{$user_id}' AND `parent_id`='{$parent_id}'";
		$data = $this->db()->getAll($sql);
		$oldids = array_column($data,'permission_id');
		$data = array_combine(array_column($data,'id'),$oldids);
		$addIds = array_diff($ids,$oldids);
		$delIds = array_diff($oldids,$ids);
                if(Auth::$userType==2){
                        $myPower = array_values(Auth::get__buttons());
                        $delIds = array_intersect($delIds,$myPower); 
                }
		if($delIds)
		{
                        $sql = "DELETE FROM `user_view_button_permission` WHERE `user_id`='{$user_id}' AND `permission_id` IN (".implode(',',$delIds).") ";
                        $this->db()->query($sql);
		}
		if($addIds)
		{
			$arr=array();
			foreach ($addIds as $id )
			{
				$arr[] = array('user_id'=>$user_id,'permission_id'=>$id,'parent_id'=>$parent_id);
			}

			if($arr)
			{
				try{
					$this->insertAll($arr,'user_view_button_permission');
				}
				catch(Exception $e){
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * getOprData,根据用户id和权限id获取对应control的操作方法
	 */
	public function getOprData ($user_id,$permission_id,$rid)
	{
		//rid 是菜单id
		$sql = "SELECT id FROM `resource_type` WHERE `code`='OPERATION' ";
		$row = $this->db()->getOne($sql);
		if(!$row)
		{
			return false;
		}

		//$sql = "SELECT c_id FROM `menu` WHERE `id`='".$rid."' ";

		//$sql = "SELECT * FROM `operation` AS o WHERE EXISTS (SELECT c_id FROM `menu` AS m WHERE m.`id`='".$rid."' AND m.`c_id`=o.`c_id` AND `m`.`is_deleted`='0' AND `m`.`is_enabled`='1' ) ";

		//$sql = "SELECT `b`.`label`,`b`.`method_name`,`p`.`id` AS `pid` FROM `operation` AS `b` INNER JOIN `permission` AS `p` ON  `b`.`id`=`p`.`resource_id` AND `b`.`is_deleted`=`p`.`is_deleted` WHERE EXISTS (SELECT c_id FROM `menu` AS m WHERE m.`id`='".$rid."' AND m.`c_id`=`b`.`c_id` AND `m`.`is_deleted`='0' AND `m`.`is_enabled`='1') AND `p`.`type`='".$row."' AND `b`.`is_deleted`='0' ";

		$sql = "SELECT `main`.`pid` AS `id`,`main`.`label`,`main`.`method_name`,ifnull(uop.permission_id,0) AS chk_u,ifnull(tmp.permission_id,0) AS chk_r FROM (SELECT `b`.`label`,`b`.`method_name`,`p`.`id` AS `pid` FROM `operation` AS `b` INNER JOIN `permission` AS `p` ON  `b`.`id`=`p`.`resource_id` AND `b`.`is_deleted`=`p`.`is_deleted` WHERE EXISTS (SELECT c_id FROM `menu` AS m WHERE m.`id`='".$rid."' AND m.`c_id`=`b`.`c_id` AND `m`.`is_deleted`='0' AND `m`.`is_enabled`='1') AND `p`.`type`='".$row."' AND `b`.`is_deleted`='0') AS `main` LEFT JOIN (SELECT distinct `permission_id` FROM `user_operation_permission` WHERE `user_id`='".$user_id."' AND `parent_id`='".$permission_id."') AS `uop` ON `main`.`pid`=`uop`.`permission_id` LEFT JOIN (SELECT distinct permission_id FROM `role_operation_permission` AS rop WHERE EXISTS (SELECT role_id FROM ((SELECT role_id FROM `group_role` WHERE EXISTS (SELECT * FROM `group_user` WHERE `user_id`=".$user_id." AND group_role.group_id=group_user.group_id)) UNION (SELECT role_id FROM `role_user` WHERE `user_id`=".$user_id.")) AS tt WHERE tt.role_id=rop.role_id ) AND rop.parent_id=".$permission_id.") AS tmp ON tmp.permission_id=main.pid  ORDER BY `main`.`pid`";

		$data = $this->db()->getAll($sql);
		//Util::array_unique_fb($data,'id');
                if(Auth::$userType==2)
                {
                        $myPower = array_values(Auth::get__operation_ps());
                        foreach($data AS $key =>$val){
                                if(!in_array($val['id'],$myPower)){
                                        unset($data[$key]);
                                }
                        }              
                }
		return $data;
	}

	public function saveOperation ($user_id,$parent_id,$ids)
	{
		$sql = "SELECT id,permission_id FROM `user_operation_permission` WHERE `user_id`='{$user_id}' AND `parent_id`='{$parent_id}'";
		$data = $this->db()->getAll($sql);
		$oldids = array_column($data,'permission_id');
		$data = array_combine(array_column($data,'id'),$oldids);
		$addIds = array_diff($ids,$oldids);
		$delIds = array_diff($oldids,$ids);
                if(Auth::$userType==2){
                        $myPower = array_values(Auth::get__operation_ps());
                        $delIds = array_intersect($delIds,$myPower); 
                }
		if($delIds)
		{
                        $sql = "DELETE FROM `user_operation_permission` WHERE `user_id`='{$user_id}' AND `permission_id` IN (".implode(',',$delIds).") ";
				$this->db()->query($sql);
			$del = array();
			foreach ($delIds as $val )
			{
				$del[] = array_search($val,$data);
			}

			try{
				$sql = "DELETE FROM `user_operation_permission` WHERE `id` IN (".implode(',',$del).") ";
				$this->db()->query($sql);
			}
			catch(Exception $e){
				return false;
			}
		}
		if($addIds)
		{
			$arr=array();
			foreach ($addIds as $id )
			{
				$arr[] = array('user_id'=>$user_id,'permission_id'=>$id,'parent_id'=>$parent_id);
			}

			try{
				$this->insertAll($arr,'user_operation_permission');
			}
			catch(Exception $e){
				return false;
			}
		}
		return true;
	}



	public function menuListDetail ($user_id)
	{
//		$sql = "SELECT id FROM `resource_type` WHERE `code`='MENU' ";
//		$row = $this->db()->getOne($sql);
//		if(!$row)
//		{
//			return false;
//		}
                $row = 1;

		$sql = "SELECT p.id,p.name AS label,p.code,p.resource_id,ifnull(r.permission_id,0) AS chk_r,ifnull(ump.permission_id,0) AS chk_u FROM `permission` AS p LEFT JOIN (SELECT distinct permission_id FROM `role_menu_permission` AS rmp WHERE EXISTS (SELECT tt.role_id FROM ((SELECT role_id FROM `group_role` WHERE EXISTS (SELECT id FROM `group_user` WHERE `user_id`='".$user_id."' AND group_role.group_id=group_user.group_id)) UNION (SELECT role_id FROM `role_user` WHERE `user_id`='".$user_id."')) AS tt WHERE tt.role_id=rmp.role_id)) AS r ON p.id=r.permission_id INNER JOIN (SELECT distinct permission_id FROM `user_menu_permission` WHERE `user_id`=".$user_id.") AS ump ON p.id=ump.permission_id WHERE p.is_deleted=0 AND p.type='".$row."' order by p.id";

		$sql = "SELECT t.*,m.group_id AS parent_id,m.application_id FROM (".$sql.") AS t INNER JOIN `menu` AS m ON t.`resource_id`=`m`.`id` INNER JOIN `control` AS c ON c.`id`=m.`c_id` WHERE `m`.`is_deleted`='0' AND m.`is_enabled`='1' AND c.`type`='2' ";
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
                $myPower = $_SESSION["__menu_p"];
                
		foreach ($data as $v )
		{
                        if(Auth::$userType==2 && !isset($myPower[1][0][$v['id']])){
                                continue;
                        }
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


	public function getRelData ($user_id,$permission_id,$rid)
	{
		$sql = "SELECT `c`.`id`,`c`.`label`,`c`.`code`,`p`.`id` AS `pid` FROM `control` AS `c` INNER JOIN `permission` AS `p` ON `c`.`id`=`p`.`resource_id` AND `c`.`is_deleted`=`p`.`is_deleted` WHERE EXISTS (SELECT `c_id` FROM `menu` AS `m` WHERE `m`.`id`='".$rid."' AND `m`.`c_id`=`c`.`parent_id` AND `m`.`is_deleted`='0' AND `m`.`is_enabled`='1') AND `c`.`type`='3' AND `p`.`type`='4' AND `c`.`is_deleted`='0' ORDER BY `c`.`id` ";

		$sql = "SELECT `main`.`pid` AS `id`,`main`.`label`,`main`.`code`,ifnull(r.permission_id,0) AS chk_r,ifnull(ump.permission_id,0) AS chk_u FROM (".$sql.") AS `main` LEFT JOIN (SELECT distinct permission_id FROM `role_subdetail_permission` AS rmp WHERE EXISTS (SELECT tt.role_id FROM ((SELECT role_id FROM `group_role` WHERE EXISTS (SELECT id FROM `group_user` WHERE `user_id`='".$user_id."' AND group_role.group_id=group_user.group_id)) UNION (SELECT role_id FROM `role_user` WHERE `user_id`='".$user_id."')) AS tt WHERE tt.role_id=rmp.role_id) AND `parent_id`='".$permission_id."') AS `r` ON `main`.`pid`=`r`.`permission_id` LEFT JOIN (SELECT distinct permission_id FROM `user_subdetail_permission` WHERE `user_id`=".$user_id." AND `parent_id`='".$permission_id."') AS ump ON `main`.`pid`=ump.permission_id  ORDER BY `main`.`pid`";
		$data = $this->db()->getAll($sql);
		Util::array_unique_fb($data,'id');
                
                $myPower = $_SESSION["__menu_p"];
                if(Auth::$userType==2)
                {
                        $myPower = array_values($myPower[1][0]);
                        foreach($data AS $key =>$val){
                                if(!in_array($val['id'],$myPower)){
                                        unset($data[$key]);
                                }
                        }              
                }
		return $data;
	}

	public function saveRel ($user_id,$parent_id,$ids)
	{
		$sql = "SELECT id,permission_id FROM `user_subdetail_permission` WHERE `user_id`='{$user_id}' AND `parent_id`='{$parent_id}'";
		$data = $this->db()->getAll($sql);

		$oldids = array_column($data,'permission_id');
		$data = array_combine(array_column($data,'id'),$oldids);
		$addIds = array_diff($ids,$oldids);
		$delIds = array_diff($oldids,$ids);
                if(Auth::$userType==2){
                        $myPower = $_SESSION["__menu_p"];
                        //delids  要删的（需要去掉交叉授权的）
                        $delIds = array_intersect($delIds,array_values($myPower[1][0]));
                }
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
					//删除属性控制
					$sql = "DELETE us.* FROM `user_subdetail_permission` AS sp,`permission` AS p,`field_scope` AS fs,`permission` AS pp,`user_scope` AS us WHERE us.permission_id=pp.id AND sp.permission_id=p.id AND p.type=4 AND p.resource_id=fs.c_id AND fs.id=pp.resource_id AND pp.type=5 AND us.user_id='{$user_id}' AND us.type=1 AND sp.`id` IN (".implode(',',$del).") ";

					$this->db()->query($sql);

					$sql = "DELETE sp.*,sbp.*,sop.* FROM `user_subdetail_permission` AS sp LEFT JOIN `user_subdetail_button_permission` AS sbp ON sp.permission_id=sbp.parent_id AND sp.user_id=sbp.user_id LEFT JOIN `user_subdetail_operation_permission` AS sop ON sp.permission_id=sop.parent_id AND sp.user_id=sop.user_id WHERE sp.`id` IN (".implode(',',$del).") AND sp.user_id='{$user_id}' ";

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
				$arr[] = array('user_id'=>$user_id,'permission_id'=>$id,'parent_id'=>$parent_id);
			}

			if($arr)
			{
				try{
					$this->insertAll($arr,'user_subdetail_permission');
				}
				catch(Exception $e){
					return false;
				}
			}
		}
		return true;
	}

	public function getMenuDetail ($user_id)
	{
		//主对象
		$sql = "SELECT p.id,p.resource_id,c.label FROM `permission` AS p INNER JOIN `control` AS c ON c.id=p.resource_id WHERE EXISTS (SELECT parent_id FROM `user_subdetail_permission` AS r WHERE r.`user_id`='".$user_id."' AND p.id=r.parent_id ) AND p.type=1 AND p.is_deleted='0' AND c.is_deleted='0' ";
		$data = $this->db()->getAll($sql);

		//明细对象
		$sql = "SELECT p.id,p.resource_id,c.label,r.parent_id FROM `permission` AS p INNER JOIN `user_subdetail_permission` AS r ON p.id=r.permission_id INNER JOIN `control` AS c ON c.id=p.resource_id WHERE r.`user_id`='".$user_id."' AND p.type=4 AND p.is_deleted='0' AND c.is_deleted='0' ";

		$data1 = $this->db()->getAll($sql);
		Util::array_unique_fb($data1,'id');
                $myPower = $_SESSION["__menu_p"];
                if(Auth::$userType==2)
                {
                        $myPower = array_values($myPower[1][0]);
                        foreach($data AS $key =>$val){
                                if(!in_array($val['id'],$myPower)){
                                        unset($data[$key]);
                                }
                        }              
                }
		$datas = array();

		foreach ($data as $val )
		{
                        if(Auth::$userType==2 && !isset($myPower[1][0][$val['id']])){
                                continue;
                        }
			$val['parent_id']=0;
			$datas[] = $val;
		}
		foreach ($data1 as $val1 )
		{
                        if(Auth::$userType==2 && !isset($myPower[1][0][$val1['id']])){
                                continue;
                        }
			$datas[] = $val1;
		}

		return $datas;
	}

	public function getRelButtonData ($user_id,$permission_id,$rid)
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

		$sql = "SELECT main.pid AS id,main.label,main.tips,main.code,ifnull(r.permission_id,0) AS chk_r,ifnull(tmp.permission_id,0) AS chk_u FROM (".$sql.") AS main LEFT JOIN (SELECT distinct permission_id FROM `role_subdetail_button_permission` AS rmp WHERE EXISTS (SELECT tt.role_id FROM ((SELECT role_id FROM `group_role` WHERE EXISTS (SELECT id FROM `group_user` WHERE `user_id`='".$user_id."' AND group_role.group_id=group_user.group_id)) UNION (SELECT role_id FROM `role_user` WHERE `user_id`='".$user_id."')) AS tt WHERE tt.role_id=rmp.role_id) AND `parent_id`='".$permission_id."') AS `r` ON `main`.`pid`=`r`.`permission_id` LEFT JOIN (SELECT distinct permission_id FROM `user_subdetail_button_permission` WHERE user_id='".$user_id."' AND parent_id='".$permission_id."') AS tmp ON tmp.permission_id=main.pid ORDER BY main.display_order DESC ";
		$data = $this->db()->getAll($sql);
		//Util::array_unique_fb($data,'id');
                if(Auth::$userType==2)
                {
                        $myPower = array_values(Auth::get__buttons());
                        foreach($data AS $key =>$val){
                                if(!in_array($val['id'],$myPower)){
                                        unset($data[$key]);
                                }
                        }              
                }
		return $data;
	}

	public function saveRelButton ($user_id,$parent_id,$ids)
	{
		$sql = "SELECT id,permission_id FROM `user_subdetail_button_permission` WHERE `user_id`='{$user_id}' AND `parent_id`='{$parent_id}'";
		$data = $this->db()->getAll($sql);

		$oldids = array_column($data,'permission_id');
		$data = array_combine(array_column($data,'id'),$oldids);
		$addIds = array_diff($ids,$oldids);
		$delIds = array_diff($oldids,$ids);
                if(Auth::$userType==2){
                        $myPower = array_values(Auth::get__buttons());
                        $delIds = array_intersect($delIds,$myPower); 
                }
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
					$sql = "DELETE FROM `user_subdetail_button_permission` WHERE `id` IN (".implode(',',$del).") ";
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
				$arr[] = array('user_id'=>$user_id,'permission_id'=>$id,'parent_id'=>$parent_id);
			}

			if($arr)
			{
				try{
					$this->insertAll($arr,'user_subdetail_button_permission');
				}
				catch(Exception $e){
					return false;
				}
			}
		}
		return true;
	}

	public function getRelOprData ($user_id,$permission_id,$rid)
	{
		$sql = "SELECT id FROM `resource_type` WHERE `code`='OPERATION' ";
		$row = $this->db()->getOne($sql);
		if(!$row)
		{
			return false;
		}

		$sql = "SELECT `b`.`label`,`b`.`method_name`,`p`.`id` AS `pid` FROM `operation` AS `b` INNER JOIN `permission` AS `p` ON  `b`.`id`=`p`.`resource_id` AND `b`.`is_deleted`=`p`.`is_deleted` WHERE `b`.`c_id`='".$rid."' AND `p`.`type`='".$row."' AND `b`.`is_deleted`='0' ";

		$sql = "SELECT main.pid AS id,main.label,main.method_name,ifnull(r.permission_id,0) AS chk_r,ifnull(tmp.permission_id,0) AS chk_u FROM (".$sql.") AS main LEFT JOIN (SELECT distinct permission_id FROM `role_subdetail_operation_permission` AS rmp WHERE EXISTS (SELECT tt.role_id FROM ((SELECT role_id FROM `group_role` WHERE EXISTS (SELECT id FROM `group_user` WHERE `user_id`='".$user_id."' AND group_role.group_id=group_user.group_id)) UNION (SELECT role_id FROM `role_user` WHERE `user_id`='".$user_id."')) AS tt WHERE tt.role_id=rmp.role_id) AND `parent_id`='".$permission_id."') AS `r` ON `main`.`pid`=`r`.`permission_id` LEFT JOIN (SELECT distinct permission_id FROM `user_subdetail_operation_permission` WHERE user_id='".$user_id."' AND parent_id='".$permission_id."') AS tmp ON tmp.permission_id=main.pid ORDER BY main.pid DESC ";
		$data = $this->db()->getAll($sql);
		//Util::array_unique_fb($data,'id');
                if(Auth::$userType==2)
                {
                        $myPower = array_values(Auth::get__operation_ps());
                        foreach($data AS $key =>$val){
                                if(!in_array($val['id'],$myPower)){
                                        unset($data[$key]);
                                }
                        }              
                }  
		return $data;
	}

	public function saveRelOpr ($user_id,$parent_id,$ids)
	{
		$sql = "SELECT id,permission_id FROM `user_subdetail_operation_permission` WHERE `user_id`='{$user_id}' AND `parent_id`='{$parent_id}'";
		$data = $this->db()->getAll($sql);

		$oldids = array_column($data,'permission_id');
		$data = array_combine(array_column($data,'id'),$oldids);
		$addIds = array_diff($ids,$oldids);
		$delIds = array_diff($oldids,$ids);
                if(Auth::$userType==2){
                        $myPower = array_values(Auth::get__operation_ps());
                        $delIds = array_intersect($delIds,$myPower); 
                }
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
					$sql = "DELETE FROM `user_subdetail_operation_permission` WHERE `id` IN (".implode(',',$del).") ";
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
				$arr[] = array('user_id'=>$user_id,'permission_id'=>$id,'parent_id'=>$parent_id);
			}

			if($arr)
			{
				try{
					$this->insertAll($arr,'user_subdetail_operation_permission');
				}
				catch(Exception $e){
					return false;
				}
			}
		}
		return true;
	}


	/**
	 * @param $p_id   	被复制权限的用户的id
	 * @param $u_id		要复制权限的用户id
	 *
	 * return true or false 复制成功或者失败
	 */
	public function saveCopy($u_id,$p_id){

		$button_table = 'user_button_permission';//按钮权限
		$menu_table = 'user_menu_permission';//菜单权限
		$oper_table = 'user_operation_permission';//操作权限
		$view_button = 'user_view_button_permission';//查看按钮
		$subdetail_table ='user_subdetail_permission';//明细
		$subdetail_button = 'user_subdetail_button_permission';//明细按钮
		$subdetail_operation = 'user_subdetail_operation_permission';//明细操作
		$user_scope = 'user_scope';//属性控制
		$user_warehouse = 'user_warehouse';//仓库管控
		$user_channel = 'user_channel';//渠道管控
		$user_extend_menu = 'user_extend_menu';//扩展菜单权限
		$user_extend_list_button = 'user_extend_list_button';//扩展列表按钮
		$user_extend_view_button = 'user_extend_view_button';//扩展查看按钮
		$user_extend_operation = 'user_extend_operation';//扩展操作权限
		$user_extend_subdetail = 'user_extend_subdetail';//扩展明细权限
		$user_extend_subdetail_button = 'user_extend_subdetail_button';//扩展明细按钮
		$user_extend_subdetail_operation = 'user_extend_subdetail_operation';//扩展明细操作

		$table = array(
			$button_table,
			$menu_table,
			$oper_table,
			$view_button,
			$subdetail_table,
			$subdetail_button,
			$subdetail_operation,
			$user_scope,
			$user_warehouse,
			$user_channel,
			$user_extend_menu,
			$user_extend_list_button,
			$user_extend_view_button,
			$user_extend_operation,
			$user_extend_subdetail,
			$user_extend_subdetail_button,
			$user_extend_subdetail_operation
		);

		$b_sql = 'SELECT user_id,parent_id,permission_id FROM '.$button_table.' WHERE user_id = '.$p_id;
		$m_sql = 'SELECT user_id,permission_id FROM '.$menu_table.' WHERE user_id = '.$p_id;
		$o_sql = 'SELECT user_id,parent_id,permission_id FROM '.$oper_table.' WHERE user_id = '.$p_id;
		$vb_sql= 'SELECT user_id,parent_id,permission_id FROM '.$view_button.' WHERE user_id= '.$p_id;
		$st_sql= 'SELECT user_id,parent_id,permission_id FROM '.$subdetail_table.' WHERE user_id= '.$p_id;
		$sb_sql= 'SELECT user_id,parent_id,permission_id FROM '.$subdetail_button.' WHERE user_id= '.$p_id;
		$so_sql= 'SELECT user_id,parent_id,permission_id FROM '.$subdetail_operation.' WHERE user_id= '.$p_id;
		$us_sql = "SELECT user_id,type,source_id,permission_id,scope FROM `".$user_scope."` WHERE `user_id`='{$p_id}'";

		$uw_sql = "SELECT user_id,house_id FROM `".$user_warehouse."` WHERE `user_id`='{$p_id}'";
		$uc_sql = "SELECT user_id,channel_id,power FROM `".$user_channel."` WHERE `user_id`='{$p_id}'";
		$ue_sql = "SELECT user_id,type,source_id,permission_id FROM `".$user_extend_menu."` WHERE `user_id`='{$p_id}'";
		$ueb_sql = "SELECT user_id,type,source_id,permission_id,parent_id FROM `".$user_extend_list_button."` WHERE `user_id`='{$p_id}'";
		$uev_sql = "SELECT user_id,type,source_id,permission_id,parent_id FROM `".$user_extend_view_button."` WHERE `user_id`='{$p_id}'";
		$ueo_sql = "SELECT user_id,type,source_id,permission_id,parent_id FROM `".$user_extend_operation."` WHERE `user_id`='{$p_id}'";
		$ues_sql = "SELECT user_id,type,source_id,permission_id,parent_id FROM `".$user_extend_subdetail."` WHERE `user_id`='{$p_id}'";
		$uesb_sql = "SELECT user_id,type,source_id,permission_id,parent_id FROM `".$user_extend_subdetail_button."` WHERE `user_id`='{$p_id}'";
		$ueso_sql = "SELECT user_id,type,source_id,permission_id,parent_id FROM `".$user_extend_subdetail_operation."` WHERE `user_id`='{$p_id}'";

		$sqlarr=array($b_sql,$m_sql,$o_sql,$vb_sql,$st_sql,$sb_sql,$so_sql,$us_sql,$uw_sql,$uc_sql,$ue_sql,$ueb_sql,$uev_sql,$ueo_sql,$ues_sql,$uesb_sql,$ueso_sql);
//		$sqlarr=array($b_sql,$m_sql,$o_sql,$vb_sql,$st_sql,$sb_sql,$so_sql,$us_sql);

		$resarr = array();
		foreach($sqlarr as $key=>$val){
			$reaarrs= $this->db()->getAll($val);
			if($reaarrs==array()){
				$resarr[] = $reaarrs;
				continue;
			}
			foreach($reaarrs as $k=>$v){
				$reaarrs[$k]['user_id'] =$u_id;
			}
			$resarr[] = $reaarrs;
		}

		$arr = array_combine($table,$resarr);
                //复制库管和渠道
                $sql = "select is_warehouse_keeper, is_channel_keeper from user where id=".$p_id;
                $ress = $this->db()->getRow($sql);

                $sql1 = "update user set is_warehouse_keeper=".$ress['is_warehouse_keeper'].",is_channel_keeper=".$ress['is_channel_keeper']." where id=".$u_id;
                
                $this->db()->query($sql1);
		$res =  $this->cancelPermissions($table,$u_id);

		if($res){
			$rq = true;
			foreach($arr as $key=>$val){
				if($val===array()){
					continue;
				}

				$rq = $this->insertAll($val,$key);

				if(!$rq){
					return false;
				}

			}
			return true;
		}

		return false;


	}

	public function cancelPermissions($tabel,$u_id){
		foreach($tabel as $key=>$val){
//				if($val=='user_scope')
//				{
//					$sqlarr[] = 'DELETE FROM '.$val.' where user_id = '.$u_id.' AND type=1';
//				}
//				else
//				{
					$sqlarr[] = 'DELETE FROM '.$val.' where user_id = '.$u_id;
//				}
		}
                $sqlarr[] = "delete u.* from user_channel AS u,(select id,concat(user_id,'-',channel_id) AS cc from user_channel group by cc having count(cc )>1 ) AS t where u.id=t.id";
                $sqlarr[] = "delete u.* from user_warehouse AS u,(select id,concat(user_id,'-',house_id) AS cc from user_warehouse group by cc having count(cc )>1 ) AS t where u.id=t.id";                
		return  $this->db()->commit($sqlarr);
	}

	public function listScope ($u_id)
	{
/*		//菜单权限
		$sql = "SELECT `u`.`id`,`u`.`permission_id`,`p`.`name`,0 AS `parent_id`,`p`.`resource_id` FROM `user_menu_permission` AS `u`,`permission` AS `p` WHERE `u`.`permission_id`=`p`.`id` AND `u`.`user_id`='{$u_id}' AND `p`.`type`=1";
		echo $sql;echo '<br />';

		//属性权限
		$sql = "SELECT main.id,main.label,main.parent_id,ifnull(us.permission_id,0) AS chk,ifnull(us.scope,0) AS v,1 AS type FROM (SELECT t.id,t.label,t.f_id,pp.id AS parent_id FROM (SELECT p1.id,f.label,f.c_id,f.id AS f_id FROM `permission` AS p1,`field_scope` AS f,`control` AS c WHERE p1.resource_id=f.id AND p1.is_deleted=f.is_deleted AND p1.type=5 AND f.is_enabled=1 AND f.c_id=c.id AND c.parent_id=0) AS t,`permission` AS pp,`menu` AS mm WHERE t.c_id=mm.c_id AND mm.id=pp.resource_id AND EXISTS (SELECT null FROM `user_menu_permission` AS `u`,`permission` AS `p2`,`menu` AS `m` WHERE `u`.`permission_id`=`p2`.`id` AND `u`.`user_id`=".$u_id." AND `p2`.`type`=1 AND m.id=p2.resource_id AND m.c_id=t.c_id) AND pp.type=1) AS main LEFT JOIN `user_scope` AS us ON main.id=us.permission_id AND main.parent_id=us.parent_id AND us.user_id=".$u_id." AND us.type=5";
echo $sql;echo '<br />';

		//菜单权限2
		$sql = "SELECT `u`.`id`,`u`.`permission_id`,`p`.`name`,`u`.`parent_id`,`p`.`resource_id` FROM `user_subdetail_permission` AS `u`,`permission` AS `p` WHERE `u`.`permission_id`=`p`.`id` AND `u`.`user_id`='{$u_id}' AND `p`.`type`=4 AND EXISTS (SELECT null FROM `control` AS `c`,`permission` AS spp WHERE c.id=spp.resource_id AND spp.type=4 AND EXISTS (SELECT m.c_id FROM `user_menu_permission` AS `u1`,`permission` AS `p1`,`menu` AS `m` WHERE `u1`.`permission_id`=`p1`.`id` AND `p1`.`resource_id`=`m`.`id` AND `u1`.`user_id`='4' AND `p1`.`type`=1 AND `c`.`parent_id`=m.c_id) AND `u`.`permission_id`=spp.id)";
echo $sql;echo '<br />';

		//属性权限2
		$sql = "SELECT tmp.id,tmp.label,tmp.parent_id,ifnull(us.permission_id,0) AS chk,ifnull(us.scope,0) AS v FROM (SELECT main.id,main.label,p2.id AS parent_id FROM (SELECT pp.id,f.label,f.c_id FROM `field_scope` AS f,`permission` AS pp WHERE f.id=pp.resource_id AND f.is_deleted=pp.is_deleted AND f.is_enabled=1 AND pp.type=5 AND EXISTS (SELECT null FROM `user_subdetail_permission` AS `u`,`permission` AS `p` WHERE `u`.`permission_id`=`p`.`id` AND `u`.`user_id`='{$u_id}' AND `p`.`type`=4 AND EXISTS (SELECT null FROM `control` AS `c`,`permission` AS spp WHERE c.id=spp.resource_id AND spp.type=4 AND EXISTS (SELECT m.c_id FROM `user_menu_permission` AS `u1`,`permission` AS `p1`,`menu` AS `m` WHERE `u1`.`permission_id`=`p1`.`id` AND `p1`.`resource_id`=`m`.`id` AND `u1`.`user_id`='4' AND `p1`.`type`=1 AND `c`.`parent_id`=m.c_id) AND `u`.`permission_id`=spp.id) AND f.c_id=p.resource_id)) AS main,`permission` AS p2 WHERE p2.resource_id=main.c_id AND p2.type=4 AND p2.is_deleted=0) AS tmp LEFT JOIN `user_scope` AS us ON tmp.id=us.permission_id AND tmp.parent_id=us.parent_id AND us.user_id=".$u_id." AND us.type=5 ";

echo $sql;exit;
*/
//id:权限id  label:属性名 parent_id:所属竹子对象权限id chk:是否选中 v:控制值 type:主子
//		//菜单和明细权限
		$sql = "(SELECT `u`.`id`,`u`.`permission_id`,`p`.`name`,0 AS `parent_id`,1 AS `type` FROM `user_menu_permission` AS `u`,`permission` AS `p` WHERE `u`.`permission_id`=`p`.`id` AND `u`.`user_id`='{$u_id}' AND `p`.`type`=1)";
		//$sql .= " UNION (SELECT `u`.`id`,`u`.`permission_id`,`p`.`name`,`u`.`parent_id`,2 AS `type` FROM `user_subdetail_permission` AS `u`,`permission` AS `p` WHERE `u`.`permission_id`=`p`.`id` AND `u`.`user_id`='{$u_id}' AND `p`.`type`=4 AND EXISTS (SELECT null FROM `control` AS `c`,`permission` AS spp WHERE c.id=spp.resource_id AND spp.type=4 AND EXISTS (SELECT m.c_id FROM `user_menu_permission` AS `u1`,`permission` AS `p1`,`menu` AS `m` WHERE `u1`.`permission_id`=`p1`.`id` AND `p1`.`resource_id`=`m`.`id` AND `u1`.`user_id`='4' AND `p1`.`type`=1 AND `c`.`parent_id`=m.c_id) AND `u`.`permission_id`=spp.id))";
		$sql .= " UNION (SELECT ud.id,ud.permission_id,p.name,ud.parent_id,2 AS `type` FROM `user_subdetail_permission` AS ud INNER JOIN `user_menu_permission` AS um ON ud.parent_id=um.permission_id AND ud.user_id=um.user_id INNER JOIN `permission` AS p ON ud.permission_id=p.id AND p.type=4 AND ud.user_id='{$u_id}')";

		$p1 = $this->db()->getAll($sql);
		Util::array_unique_fb($p1,'id');

		//属性权限
		$sql = "(SELECT main.id,main.label,main.parent_id,ifnull(us.permission_id,0) AS chk,ifnull(us.scope,0) AS v,1 AS type FROM (SELECT t.id,t.label,t.f_id,pp.id AS parent_id FROM (SELECT p1.id,f.label,f.c_id,f.id AS f_id FROM `permission` AS p1,`field_scope` AS f,`control` AS c WHERE p1.resource_id=f.id AND p1.is_deleted=f.is_deleted AND p1.type=5 AND f.is_enabled=1 AND f.c_id=c.id AND c.parent_id=0) AS t,`permission` AS pp,`menu` AS mm WHERE t.c_id=mm.c_id AND mm.id=pp.resource_id AND EXISTS (SELECT 1 FROM `user_menu_permission` AS `u`,`permission` AS `p2`,`menu` AS `m` WHERE `u`.`permission_id`=`p2`.`id` AND `u`.`user_id`=".$u_id." AND `p2`.`type`=1 AND m.id=p2.resource_id AND m.c_id=t.c_id) AND pp.type=1) AS main LEFT JOIN `user_scope` AS us ON main.id=us.permission_id AND us.user_id=".$u_id." AND us.type=1) UNION (SELECT tmp.id,tmp.label,tmp.parent_id,ifnull(us.permission_id,0) AS chk,ifnull(us.scope,0) AS v,2 AS type FROM (SELECT pp.id,f.label,f.c_id,m.permission_id AS parent_id FROM `field_scope` AS f,`permission` AS pp,(SELECT ud.permission_id,c.id FROM `user_subdetail_permission` AS ud INNER JOIN `user_menu_permission` AS um ON ud.parent_id=um.permission_id AND ud.user_id=um.user_id INNER JOIN `permission` AS p ON ud.permission_id=p.id AND p.type=4 INNER JOIN `control` AS c ON p.resource_id=c.id AND p.is_deleted=c.is_deleted WHERE um.user_id=".$u_id.") AS m WHERE f.id=pp.resource_id AND f.is_deleted=pp.is_deleted AND f.is_enabled=1 AND pp.type=5 AND f.c_id=m.id) AS tmp LEFT JOIN `user_scope` AS us ON tmp.id=us.permission_id AND us.user_id=".$u_id." AND us.type=1)";
		$p2 = $this->db()->getAll($sql);
		Util::array_unique_fb($p2,'id');

		$r1 = array();
		$r2 = array();
		$relation = array();
		foreach ($p1 as $key => $val )
		{
			${"r".$val['type']}[$val['permission_id']] = $val;
			($val['type']==2) && $relation[$val['permission_id']] =$val['parent_id'] ;
		}

		foreach ($p2 as $key2 => $val2 )
		{
			${"r".$val2['type']}[$val2['parent_id']]['son'][] = $val2;
		}

		foreach ($r2 as $key3 => $val3 )
		{
			if(isset($val3['son']) && count($val3['son']))
			{
				$r1[$relation[$key3]]['sub'][] = $val3;
			}
		}

		foreach ($r1 as $key => $val )
		{
			if(!!(empty($val['son']) && empty($val['sub'])))
			{
				unset($r1[$key]);
			}
		}

		return array_values($r1);
	}

	public function saveScope ($user_id,$data)
	{//1 通用 2仓库 3渠道
		$sql = "SELECT * FROM `user_scope` WHERE `type`=1 AND `user_id`=".$user_id;
		$oldScope = $this->db()->getAll($sql);
		$oldids = array_column($oldScope,'permission_id');
		$datas = array_combine(array_column($oldScope,'id'),$oldids);
		$scopes = array_combine($oldids,array_column($oldScope,'scope'));
		$newScopeP = array_keys($data);
		$addIds = array_diff($newScopeP,$oldids);
		$delIds = array_diff($oldids,$newScopeP);
		$xids = array_intersect($oldids,$newScopeP);
		if($delIds)
		{
			$del = array();
			$tmp = array_flip($datas);

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
					$sql = "DELETE FROM `user_scope` WHERE `id` IN (".implode(',',$del).") ";
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
				$arr[] = array('user_id'=>$user_id,'permission_id'=>$id,'type'=>1,'scope'=>$data[$id]);
			}
			if($arr)
			{
				try{
					$this->insertAll($arr,'user_scope');
				}
				catch(Exception $e){
					return false;
				}
			}
		}
		if($xids)
		{
			$upArr = array();
			$datas = array_flip($datas);
			foreach ($xids as $val )
			{
				if($scopes[$val]!=$data[$val])
				{
					try{
						$sql = "UPDATE `user_scope` SET `scope`='".$data[$val]."' WHERE `id`=".$datas[$val];
						$this->db()->query($sql);
					}
					catch(Exception $e){
						return false;
					}
				}
			}
		}
		return true;
	}	
}

?>