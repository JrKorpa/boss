<?php
/**
 *  -------------------------------------------------
 *   @file		: GroupUserModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-22 11:36:28
 *   @update	:
 *  -------------------------------------------------
 */
class GroupUserModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'group_user';
        $this->_dataObject = array("id"=>"主键",
"user_id"=>"用户",
"group_id"=>"组别");
		parent::__construct($id,$strConn);
	}

	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT m.*,u.account,u.real_name,g.name FROM `".$this->table()."` AS m LEFT JOIN `user` AS u ON m.user_id=u.id left JOIN `group` as g on m.group_id=g.id WHERE u.is_enabled=1 AND u.is_deleted=0";
		if($where['group_id'])
		{
/*			$subsql = "SELECT id FROM `group` WHERE tree_path LIKE (SELECT concat(tree_path,'-',id,'-%') FROM `group` WHERE id=".$where['group_id'].")";
			$subsql .= " UNION SELECT id FROM `group` WHERE parent_id=".$where['group_id'];
			$subsql .= " UNION SELECT id FROM `group` WHERE id=".$where['group_id'];*/

			$sql .=" AND m.group_id =".$where['group_id'];
		}

		$sql .= " ORDER BY m.display_order, m.id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	public function getUserTree ($group_id=0,$id=0)
	{
		if(!$id)
		{
			$id=0;
		}
		//var_dump($group_id);exit;

		$sql = "SELECT u.id,u.account,u.real_name FROM `user` AS u WHERE u.`is_deleted`=0 AND u.`is_enabled`=1 AND NOT EXISTS (SELECT o.user_id FROM `group_user` AS o WHERE u.id=o.user_id AND o.group_id=".$group_id." AND o.id<>".$id.") ";

		return $this->db()->getAll($sql);
	}

	public function getGroupexist($group_id){
		if(!$group_id){
			return false;
		}
		$sql = "SELECT * FROM `group` where id=".$group_id." AND is_deleted=0";
		return $this->db()->getOne($sql);
	}

	public function getUserexist($user_id,$group_id){
		$sql ="SELECT * FROM group_user WHERE group_id=".$group_id;
		$res = $this->db()->getOne($sql);
		if($res===false){
			$sql ="SELECT * FROM `user` WHERE id=".$user_id;
			return  $this->db()->getOne($sql);
		}

		$sql = "SELECT * FROM group_user WHERE exists(SELECT null  from user as u WHERE u.id=".$user_id." AND is_deleted=0) AND group_id=".$group_id." AND user_id<>".$user_id;
		return $this->db()->getOne($sql);
	}

	/**
	 * 获取组用户
	 */
	public function getGroupUser($group_id){
		$sql = "SELECT m.id,m.user_id,m.group_id,u.account,u.real_name,m.display_order FROM `".$this->table()."` AS m LEFT JOIN `user` AS u ON m.user_id=u.id left JOIN `group` as g on m.group_id=g.id WHERE u.is_enabled=1 AND u.is_deleted=0";
		$sql .=" AND m.group_id =".$group_id;
		$res = $this->db()->getAll($sql);
		return $res;
	}

    /**
     * @param $group_id
     * @param $user_id
     * @return mixed
     * 判断用户是否真指定的组里
     */
	public function checkGroupUser($group_id,$user_id){
	    $result = false;
        $sql = "SELECT count(id) as count FROM `".$this->table()."` AS m WHERE 1";
        $sql .=" AND m.group_id =".$group_id;
        $sql .=" AND m.user_id =".$user_id;
        $res = $this->db()->getRow($sql);
        if($res['count'] > 0){
            $result = true;
        }
        return $result;
    }

	/**
	 * 保存组排序
	 */
	public function saveSort($sort){
		$len = count($sort);
		try{
			for ($i=0;$i<$len;$i++)
			{
				$sql = "UPDATE `".$this->table()."` SET `display_order`='".($i+1)."' WHERE `id`=".$sort[$i];
				$this->db()->query($sql);
			}
		}
		catch(Exception $e)
		{
			return false;
		}
		return true;
	}


}

?>