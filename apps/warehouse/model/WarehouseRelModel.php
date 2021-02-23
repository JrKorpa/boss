<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-07 19:07:53
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseRelModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'warehouse_rel';
		$this->_dataObject = array("id"=>" ",
		"warehouse_id"=>"",
		"company_id"=>" ",
		"create_time"=>" ",
		"company_name"=> " ",
		);
		parent::__construct($id,$strConn);
	}

	function select($where=array(), $type = true)
	{
		$sql = "select `id`, `warehouse_id`, `company_id`, `create_time` from ".$this->table() ;
		$sql .=" WHERE 1";
		if(!empty($where['warehouse_id']))
		{
			$sql .= " AND `warehouse_id` = {$where['warehouse_id']}";
		}
		if($type){
			$data = $this->db()->getAll($sql);
		}else{
			$data = $this->db()->getRow($sql);
		}
		return $data;
	}

	public function del($id)
	{
		$sql = "DELETE FROM ".$this->table()." WHERE `warehouse_id` = '$id'";
		return $this->db()->query($sql);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url ApplicationController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//需要显示的 仓库 公司  创建时间  添加人
		$sql  = "SELECT `r`.`id`, `w`.`code`, `w`.`name`, `r`.`company_id`, `r`.`create_time`, `w`.`create_user`, `w`.`is_delete`  FROM `{$this->table()}` AS `r`,`warehouse` AS `w` WHERE `r`.`warehouse_id` = `w`.`id`";
		if($where['name'] != "")
		{
			$sql .= " AND `w`.`name` = '".$where['name']."'";
		}
		if($where['company_id'] != "")
		{
			$sql .= " AND `r`.`company_id` = ".$where['company_id'];
		}
		if($where['code'] != "")
		{
			$sql .= " AND `w`.`code` = '".$where['code']."'";
		}

		if($where['is_delete'] !=="")
		{
			$sql .= " AND `w`.`is_delete` = ".$where['is_delete'];
		}
		$sql .= " ORDER BY `r`.`id` DESC";
		//echo $sql;exit;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		//var_dump($data);exit;
		return $data;
	}

	/** 根据仓库ID，获取所属公司ID **/
	public function GetCompanyByWarehouseId($warehouse_id){
		$sql = "SELECT `company_id` FROM `warehouse_rel` WHERE `warehouse_id`={$warehouse_id}";
		return $this->db()->getOne($sql);
	}

	/** 查询公司与关联仓库的信息 **/
	public function GetCompanyByWarehouse($fields = '*' , $where = '1 LIMIT 1' , $type = 'getOne'){
		$sql = "SELECT {$fields} FROM `warehouse_rel` AS `a` LEFT JOIN `warehouse` AS `b` ON `a`.`warehouse_id` = `b`.`id` WHERE {$where}";
		if($type == 'getOne'){
			return $this->db()->getOne($sql);
		}else if($type == 'getRow'){
			return $this->db()->getRow($sql);
		}else if($type = 'getAll'){
			return $this->db()->getAll($sql);
		}
	}

	/**
	* 普通查询
	*/
	public function select2($fields, $where , $type = 'getOne'){
		$sql = "SELECT {$fields} FROM `warehouse_rel` WHERE {$where} ORDER BY `id` DESC";
		if($type == 'getOne'){
			return $this->db()->getOne($sql);
		}else if($type == 'getRow'){
			return $this->db()->getRow($sql);
		}else if($type = 'getAll'){
			return $this->db()->getAll($sql);
		}
	}

    /**
    * 普通查询
    */
    public function select3($fields, $where , $type = 'getOne'){
        $sql = "SELECT {$fields} FROM `warehouse` AS `w`,`warehouse_rel` AS `r` WHERE {$where} ORDER BY w.`id` DESC";
        if($type == 'getOne'){
            return $this->db()->getOne($sql);
        }else if($type == 'getRow'){
            return $this->db()->getRow($sql);
        }else if($type = 'getAll'){
            return $this->db()->getAll($sql);
        }
    }
}?>