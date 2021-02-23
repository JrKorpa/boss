<?php
/**
 *  -------------------------------------------------
 *   @file		: GroupModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-16 15:34:06
 *   @update	:
 *  -------------------------------------------------
 */
class GroupModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'group';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"组id",
"name"=>"组名称",
"code"=>"组编码",
"note"=>"描述",
"parent_id"=>"上级组id",
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
	 *	@url GroupController/search
	 */
	public function getList ()
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE `is_deleted`='0' ORDER BY `display_order` ASC";
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
	*  -------------------------------------------------
	*   getGroupTree
	*   获取工作组用作下拉
	*   @return	array	二维数组
	*  -------------------------------------------------
	*  @url GroupView/getGroupTree
	*/
	public function getGroupTree ($all=true)
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
		$sql = "SELECT count(1) FROM `".$this->table()."` WHERE `code` = '{$code}'";
		if($this->pk())
		{
			$sql .=" AND `id`<>'".$this->pk()."'";	
		}
		return  $this->db()->getOne($sql,array(),false);
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
			$sqls[] = $this->updateSqlNew($data,"`group`");
		}
		else
		{
			$sqls[] = $this->insertSqlNew($data,"`group`");
		}
		if($newdo['pids'])
		{
			$sqls[] = "UPDATE `".$this->table()."` SET `childrens`=`childrens`+1 WHERE `id` IN (".$newdo['pids'].")";//向上汇总
		}
		return $this->db()->commit($sqls);
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
}

?>