<?php
/**
 *  -------------------------------------------------
 *   @file		: DepartmentModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-26 11:52:16
 *   @update	:
 *  -------------------------------------------------
 */
class DepartmentModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'department';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"部门id",
"name"=>"部门名称",
"code"=>"部门编码",
"note"=>"描述",
"parent_id"=>"上级部门id",
"tree_path"=>"全路径",
"pids"=>"祖先分类",
"childrens"=>"下级分类数",
"display_order"=>"显示顺序",
"is_deleted"=>"是否删除",
"is_system"=>"系统内置");
		parent::__construct($id,$strConn);
	}

	/**
	 *	getList，列表
	 *
	 *	@url DepartmentController/search
	 */
	public function getList ()
	{
		$sql = "SELECT `id`,`name`,`code`,`parent_id`,`tree_path` FROM `".$this->table()."` WHERE `is_deleted`='0' ORDER BY `display_order` ASC";
		$res = $this->db()->getAll($sql);
		$keys = array_column($res,'id');
		$res = array_combine($keys,$res);
		$data = array();
		foreach ($res as $val )
		{
			if(isset($res[$val['parent_id']]))
			{
				$res[$val['parent_id']]['son'][] = &$res[$val['id']];
			}
			else
			{
				$data[] = &$res[$val['id']];
			}
		}
		$list = array();
		$this->flatArray($data,$list);
		return $list;
	}

	/*
	*	将多维数组转化为二维数组
	*/
	function flatArray($arr,&$return)
	{
		foreach ($arr as $key => $val )
		{
			$val['level'] = count(explode('-',$val['tree_path']));
			$val['tree_name'] = str_repeat('&nbsp;',2*($val['level']-1)).$val['name'];
			$return[] = $val;
			if(isset($val['son']))
			{
				$this->flatArray($val['son'],$return);
			}
		}
		return $return;
	}

	/*
	* saveDatas,事务提交
	*/
	public function saveDatas ($newdo,$olddo)
	{
		$save = false;
		$sqls=array();
		if(!empty($newdo[$this->getPk()]))
		{
			$save = true;
		}
		$data = $this->dealData($newdo,$olddo);
		if($save)
		{
			if($olddo['pids'])
			{
				$sqls[] = "UPDATE `".$this->table()."` SET `childrens`=`childrens`-1 WHERE `id` IN (".$olddo['pids'].")";//向上汇总
			}
			$sqls[] = $this->updateSql($data);
		}
		else
		{
			$sqls[] = $this->insertSql($data);
		}
		if($newdo['pids'])
		{
			$sqls[] = "UPDATE `".$this->table()."` SET `childrens`=`childrens`+1 WHERE `id` IN (".$newdo['pids'].")";//向上汇总
		}
		return $this->db()->commit($sqls);
	}

	/*
	*  -------------------------------------------------
	*   getDepts
	*   获取部门用作下拉
	*   @return	array	二维数组
	*  -------------------------------------------------
	*  @url DepartmentView/getDeptTree
	*/
	public function getDeptTree ($all=true)
	{
		$sql = "SELECT `id`,`name`,`parent_id`,concat(`tree_path`,'-',`id`) AS `abspath` FROM `".$this->table()."` WHERE `is_deleted`= '0'";
		if(!$all && $this->pk())
		{
			$sql .= " AND `tree_path` NOT LIKE \"".$this->getValue("tree_path")."-".$this->pk()."%\" AND `id`<>'".$this->pk()."'";
		}
		$sql .=" ORDER BY `abspath` ASC,`display_order` DESC";
		return $this->db()->getAll($sql,array(),false);
	}

	public function hasCode ($code)
	{
		$sql = "SELECT COUNT(1) FROM `".$this->table()."` WHERE `code` = '{$code}'";
		if($this->pk())
		{
			$sql .=" AND `id`<>'".$this->pk()."'";
		}
		return  $this->db()->getOne($sql,array(),false);
	}

	public function hasName ($name,$parent_id)
	{
		$sql = "SELECT COUNT(1) FROM `".$this->table()."` WHERE `name` = '{$name}' AND `parent_id`=".$parent_id;
		if($this->pk())
		{
			$sql .=" AND `id`<>'".$this->pk()."'";
		}
		return  $this->db()->getOne($sql,array(),false);
	}

	public function move ($id,$up=true)
	{
		$do = $this->getDataObject();
		if(!$do)
		{
			return false;
		}
		if($up)
		{
			$sql = "SELECT `id`,`display_order` FROM `".$this->table()."` WHERE `parent_id`='".$do['parent_id']."' AND `display_order`<'".$do['display_order']."' ORDER BY `display_order` DESC LIMIT 1";
		}
		else
		{
			$sql = "SELECT `id`,`display_order` FROM `".$this->table()."` WHERE `parent_id`='".$do['parent_id']."' AND `display_order`>'".$do['display_order']."' ORDER BY `display_order` ASC LIMIT 1";
		}
		$destdo = $this->db()->getRow($sql);
		if(!$destdo)
		{
			return 3;
		}

		$sql = "UPDATE `".$this->table()."` SET `display_order`='".$do['display_order']."' WHERE `id`='".$destdo['id']."' ";
		$res = $this->db()->query($sql);
		if(!$res)
		{
			return false;
		}
		$sql = "UPDATE `".$this->table()."` SET `display_order`='".$destdo['display_order']."' WHERE `id`='".$id."' ";
		$res = $this->db()->query($sql);
		if($res)
		{
			return 1;
		}
		else
		{
			return false;
		}

	}


	public function hasRelData ($id)
	{
		$sql = "SELECT COUNT(*) FROM `organization` WHERE `dept_id`='{$id}' ";
		$res = $this->db()->getOne($sql);
		if($res)
		{
			return true;
		}
		return false;
	}

	public function getDepartmentexist($dept_id){
		if(!$dept_id){
			return false;
		}
		$sql = "SELECT COUNT(1) FROM `".$this->table()."` where id=".$dept_id." AND `is_deleted`='0'";
		return $this->db()->getOne($sql);
	}


	public function getDepartmentExists($department_id,$company_id){

		$sql ="SELECT COUNT(1) FROM `company_department` WHERE `dep_id`='".$department_id."'";
		$res = $this->db()->getOne($sql);
		if($res===false){
			$sql ="SELECT count(1) FROM `department` WHERE `id`='".$department_id."'";
			return  $this->db()->getOne($sql);
		}

		$sql = "SELECT COUNT(1) FROM `company_department` WHERE EXISTS (SELECT null FROM `department` as `u` WHERE `u`.`id`='".$department_id."' AND `u`.`is_deleted`='0') AND `company_id`='".$company_id."' AND `dep_id`<>'".$company_id."'";
		return $this->db()->getOne($sql);

	}

    /*
     *
     */
	public function getDepartmentInfo($select="*",$where)
	{
		$sql = "SELECT $select FROM `department`";
		$str = '';
		if(!empty($where['parent_id']))
		{
			$str .= "`parent_id`='".$where['parent_id']."' AND ";
		}
		if(!empty($where['parent_id_no']))
		{
			$str.="`parent_id`!='".$where['parent_id_no']."' AND ";
		}
		if(!empty($where['id']))
		{
			$str.="`id`='".$where['id']."' AND ";
		}

		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}

		return $this->db()->getAll($sql);
	}
	//根据部门id 获取部门名称
	public function getNameById($id)
	{
		$sql = "select `name` from  `".$this->table()."` where id={$id} ";
        return $this->db()->getOne($sql);
	}
	//根据部门名称查询部门是否存在
	public function checkExistsByName($name){
	    $sql = "select count(*) as 'c' from  `".$this->table()."` where `name`='{$name}'";
	    return $this->db()->getOne($sql);
	}
	
	
	function getDepartmentInfoById($id){
		$sql="SELECT tree_path,pids FROM ".$this->table()." WHERE id=$id";
		$row=$this->db()->getRow($sql);
		return $row;
	}
	
	function UpdateDepartmentInfo($do){
		if(!isset($do['id'])&& empty($do['id'])){
			return false;
		}
		if(!isset($do['parent_id'])&& empty($do['parent_id'])){
			return false;
		}
		if(!isset($do['tree_path'])&& empty($do['tree_path'])){
			return false;
		}
		if(!isset($do['pids'])&& empty($do['pids'])){
			return false;
		}
		$id=$do['id'];
		$parent_id=$do['parent_id'];
		$tree_path=$do['tree_path'];
		$pids=$do['pids'];
		$sql="UPDATE ".$this->table()." SET parent_id={$parent_id},tree_path='{$tree_path}',pids='{$pids}'  WHERE id=$id";
		return $this->db()->query($sql);
		
	}
	
	//根据父级Id更新部门tree_path、pids
	function UpdateByParentId($parent_id){
		$sql="SELECT id FROM ".$this->table()." WHERE parent_id=$parent_id";
		$rows=$this->db()->getAll($sql);
		if(empty($rows)){
			$result['success']=0;
			return $return;
		}
				
		$sql="SELECT tree_path,pids FROM ".$this->table()." WHERE id=$parent_id";
		$row=$this->db()->getRow($sql);
		$tree_path=$row['tree_path'];
		$pids=$row['pids'];
		
		if($tree_path==null)
		{
			$tree_path = 0;
		}
		else
		{
			$tree_path .= "-".$parent_id;
		}
		if(count(explode('-', $tree_path)) > 4){
			$result['success']=1;
			$result['error'] ="深度不可以大于5层！";
			return $result;		
		}
		if($pids)
		{
			$pids.=",".$parent_id;
		}
		else
		{
			$pids = $parent_id;
		}
		
		$sql="UPDATE ".$this->table()." SET tree_path='{$tree_path}',pids='{$pids}' WHERE parent_id={$parent_id}";
		$re=$this->db()->query($sql);
		if($re){
			foreach ($rows as $r){
				$this->UpdateByParentId($r['id']);
			}
		}else{
			$result['success']=1;
			$result['error'] ="子类更新路径失败";
			return $result;
		}
		
		
	}
	

}

?>