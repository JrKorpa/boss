<?php

/**
 *  -------------------------------------------------
 *   @file		: AppCatTypeModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 11:58:19
 *   @update	:
 *  -------------------------------------------------
 */
class AppCatTypeModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'app_cat_type';
		$this->pk='cat_type_id';
		$this->_prefix='cat_type';
        $this->_dataObject = array(
			"cat_type_id"=>"部门id",
			"cat_type_name"=>"部门名称",
			"cat_type_code"=>"部门编码",
			"note"=>"描述",
			"parent_id"=>"上级部门id",
			"tree_path"=>"全路径",
			"pids"=>"祖先分类",
			"childrens"=>"下级分类数",
			"display_order"=>"显示顺序",
			"cat_type_status"=>"是否删除",
			"is_system"=>"系统内置");
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url ApplicationController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        $sql = "SELECT * FROM `" . $this->table() . "`  WHERE 1 ";

        if ($where['cat_type_name'] != "") {
            $sql .= " AND cat_type_name like \"%" . addslashes($where['cat_type_name']) . "%\"";
        }
        if ($where['cat_type_status'] != "") {
            $sql .= " AND cat_type_status =" . addslashes($where['cat_type_status']);
        }

        $sql .= " ORDER BY cat_type_id DESC";
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }

    /*
     * 获取所有属性值
     */

    public function getCtlList($param=0) {
        $sql = "SELECT cat_type_id,cat_type_name,cat_type_code FROM `" . $this->table() . "` where 1";
		if($param){
			$sql .= " and cat_type_id=$param";
			return $this->db()->getRow($sql);
		}
        return $this->db()->getAll($sql);
    }
    
    /*
     * 获取启用的属性值
    */
    
    public function getCtlListon() {
    	$sql = "SELECT cat_type_id,cat_type_name,cat_type_code FROM `" . $this->table() . "`";
    	return $this->db()->getAll($sql);
    }
    
    public function getTreeIdInfo($id) {
    	$sql = "SELECT cat_type_id FROM `" . $this->table() . "` where tree_path like '%$id-%'";
    	return $this->db()->getAll($sql);
    }
    //批量停用
    public function updateStatus($ids) {
    	$sql = "UPDATE `" . $this->table() . "` SET cat_type_status = 0 where cat_type_id in(".$ids.")";
		return $this->db()->query($sql);
    }
    //批量启用
    public function updateStatuson($ids) {
    	$sql = "UPDATE `" . $this->table() . "` SET cat_type_status = 1 where cat_type_id in(".$ids.")";
    	return $this->db()->query($sql);
    }
    /**
     * 获取对应编码
     * @param type $id
     * @return type
     */
    public function getCatCode($id) {
        $sql = "SELECT cat_type_code FROM `" . $this->table() . "` where cat_type_id=$id";
        return $this->db()->getOne($sql);
    }
    
    /*
	*  -------------------------------------------------
	*   getCatTree
	*   获取部门用作下拉
	*   @return	array	二维数组
	*  -------------------------------------------------
	*  @url AppCatTypeView/getCatTree
	*/
	public function getCatTree ($all=true)
	{
		//$sql = "SELECT 	cat_type_id as id,cat_type_name as name,parent_id,concat(tree_path,'-',cat_type_id) AS abspath FROM `".$this->table()."` WHERE cat_type_staus= 1";
		$sql = "SELECT 	cat_type_id as id,cat_type_name as name,parent_id,concat(tree_path,'-',cat_type_id) AS abspath FROM `".$this->table()."` WHERE 1 and cat_type_status=1";
		if(!$all && $this->pk())
		{
			$sql .= " AND tree_path not like \"".$this->getValue("tree_path")."-".$this->pk()."%\" AND cat_type_id<>".$this->pk();
		}
		$sql .=" ORDER BY abspath ASC,display_order DESC";
       // echo $sql;die;
		return $this->db()->getAll($sql,array(),false);
	}
    
    /*
     * 判断编码是否已经存在
     */
    public function hasCode ($code) 
	{
		$sql = "SELECT count(1) FROM `".$this->table()."` WHERE cat_type_code = '{$code}'";
		if($this->pk())
		{
			$sql .=" AND cat_type_id<>".$this->pk();	
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
				$sqls[] = "UPDATE `".$this->table()."` SET `childrens`=`childrens`-1 WHERE `cat_type_id` IN (".$olddo['pids'].")";//向上汇总
			}
			$sqls[] = $this->updateSql($data);
		}
		else
		{
			$sqls[] = $this->insertSql($data);
		}
		if($newdo['pids'])
		{
			$sqls[] = "UPDATE `".$this->table()."` SET `childrens`=`childrens`+1 WHERE `cat_type_id` IN (".$newdo['pids'].")";//向上汇总
		}
       
		return $this->db()->commit($sqls);
	}
    
    /**
	 *	getList，列表
	 *
	 *	@url DepartmentController/search
	 */
	public function getList () 
	{
		$sql = "SELECT * FROM `".$this->table()."`   ORDER BY display_order ASC";
		$res = $this->db()->getAll($sql);
       
		$keys = array_column($res,'cat_type_id');
        
		$res = array_combine($keys,$res);
		$data = array();
		foreach ($res as $val ) 
		{
			if(isset($res[$val['parent_id']]))
			{
				$res[$val['parent_id']]['son'][] = &$res[$val['cat_type_id']];	
			}
			else
			{
				$data[] = &$res[$val['cat_type_id']];
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
			$val['tree_name'] = str_repeat('&nbsp;',2*($val['level']-1)).$val['cat_type_name'];
			$return[] = $val;
			if(isset($val['son']))
			{
				$this->flatArray($val['son'],$return);	
			}
		}
		return $return;
	}

}

?>