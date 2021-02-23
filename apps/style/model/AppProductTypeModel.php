<?php

/**
 *  -------------------------------------------------
 *   @file		: AppProductTypeModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 12:43:49
 *   @update	:
 *  -------------------------------------------------
 */
class AppProductTypeModel extends Model {

    public $_prefix = 'product_type';

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'app_product_type';
        $this->_dataObject = array(
            "product_type_id" => "产品线ID",
            "product_type_name"=>"产品线名称",
            "product_type_code"=>"产品线编码",
            "note"=>"描述",
            "parent_id"=>"上级部门id",
            "tree_path"=>"全路径",
            "pids"=>"祖先分类",
            "childrens"=>"下级分类数",
            "display_order"=>"显示顺序",
            "product_type_status"=>"是否停用:1启用 0停用",
            "is_system"=>"系统内置",
        );
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url ApplicationController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        $sql = "SELECT * FROM `" . $this->table() . "`  WHERE 1 ";

        if ($where['product_type_name'] != "") {
            $sql .= " AND product_type_name like \"%" . addslashes($where['product_type_name']) . "%\"";
        }
        if ($where['product_type_status'] != "") {
            $sql .= " AND product_type_status =" . addslashes($where['product_type_status']);
        }

        $sql .= " ORDER BY product_type_id DESC";
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }

    /*
     * 获取所有产品线
     */

    public function getCtlList($param=0) {
        $sql = "SELECT product_type_id,product_type_name,product_type_code FROM `" . $this->table() . "` where product_type_status=1";
		if($param){
			$sql .= " and product_type_id=$param";
			return $this->db()->getRow($sql);
		}
        return $this->db()->getAll($sql);
    }

     /**
     * 获取对应编码
     * @param type $id
     * @return type
     */
    public function getProductCode($id) {
        $sql = "SELECT product_type_code FROM `" . $this->table() . "` where product_type_id=$id";
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
	public function getProductTree ($all=true)
	{
		//$sql = "SELECT 	product_type_id as id,product_type_name as name,parent_id,concat(tree_path,'-',product_type_id) AS abspath FROM `".$this->table()."` WHERE product_type_staus= 1";
		$sql = "SELECT 	product_type_id as id,product_type_name as name,parent_id,concat(tree_path,'-',product_type_id) AS abspath FROM `".$this->table()."` WHERE product_type_status= 1";
		if(!$all && $this->pk())
		{
			$sql .= " AND tree_path not like \"".$this->getValue("tree_path")."-".$this->pk()."%\" AND product_type_id<>".$this->pk();
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
		$sql = "SELECT count(1) FROM `".$this->table()."` WHERE product_type_code = '{$code}'";
		if($this->pk())
		{
			$sql .=" AND product_type_id<>".$this->pk();	
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
				$sqls[] = "UPDATE `".$this->table()."` SET `childrens`=`childrens`-1 WHERE `product_type_id` IN (".$olddo['pids'].")";//向上汇总
			}
			$sqls[] = $this->updateSql($data);
		}
		else
		{
			$sqls[] = $this->insertSql($data);
		}
		if($newdo['pids'])
		{
			$sqls[] = "UPDATE `".$this->table()."` SET `childrens`=`childrens`+1 WHERE `product_type_id` IN (".$newdo['pids'].")";//向上汇总
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
		$sql = "SELECT * FROM `".$this->table()."`  ORDER BY display_order ASC";
		$res = $this->db()->getAll($sql);
       
		$keys = array_column($res,'product_type_id');
        
		$res = array_combine($keys,$res);
		$data = array();
		foreach ($res as $val ) 
		{
			if(isset($res[$val['parent_id']]))
			{
				$res[$val['parent_id']]['son'][] = &$res[$val['product_type_id']];	
			}
			else
			{
				$data[] = &$res[$val['product_type_id']];
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
			$val['tree_name'] = str_repeat('&nbsp;',2*($val['level']-1)).$val['product_type_name'];
			$return[] = $val;
			if(isset($val['son']))
			{
				$this->flatArray($val['son'],$return);	
			}
		}
		return $return;
	}

    function updateProductTypeStatus ($tp_str,$status)
    {
        $where = "WHERE 1";
        if($tp_str != ''){
            $where .= " AND `tree_path` = '{$tp_str}'" ;
        }
        $sql = "UPDATE `".$this->table()."` SET `product_type_status` = {$status} {$where}";
        return $this->db()->query($sql);
    }

    function getProductStatus ($parent_id)
    {
        $sql = "SELECT `product_type_status` FROM `".$this->table()."` WHERE `product_type_id` = {$parent_id}";
        return $this->db()->getRow($sql);
    }

    /*
    *   获取上级ID
    */
    function getParentIdById ($id)
    {
        $sql = "SELECT `parent_id` FROM `".$this->table()."` WHERE `product_type_id` = {$id}";
        return $this->db()->getOne($sql);
    }
	//获取产品线id
	function get_Product_type_id ($where){
        $sql = "SELECT * FROM `" . $this->table() . "`  WHERE 1 ";
        if ($where['product_type_id'] != "") {
            $sql .= " AND product_type_id =" . $where['product_type_id'];
        }
		if (isset($where['parent_id']) && $where['parent_id'] != "") {
            $sql .= " AND parent_id =" . $where['parent_id'];
        }
        $data = $this->db()->getAll($sql);
        return $data;
    }

    /*1
     * 获取所有产品线11
     */

    public function getCtlListonPt() {
        $sql = "SELECT product_type_id,product_type_name,product_type_code FROM `" . $this->table() . "` where product_type_status=1 and parent_id not in(1,0)";
        return $this->db()->getAll($sql);
    }
	
}

?>