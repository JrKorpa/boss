<?php
/**
 *  -------------------------------------------------
 *   @file		: AppMaterialInfoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-07 17:42:43
 *   @update	:
 *  -------------------------------------------------
 */
class AppMaterialInfoModel extends Model
{
        public $_prefix = 'material';
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_material_info';
        $this->_dataObject = array("material_id"=>"材质ID",
"material_name"=>"材质名称",
"create_time"=>"创建时间",
"create_user"=>"创建人",
"price"=>"价格",
"material_status"=>"状态:1启用;0停用",
"material_remark"=>"记录备注");
		parent::__construct($id,$strConn);
	}
        
        /**
	 *	pageList，分页列表
	 *
	 *	@url ApplicationController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."`  WHERE 1 ";

		if($where['material_name'] != "")
		{
			$sql .= " AND material_name like \"%".addslashes($where['material_name'])."%\"";
		}
		if($where['material_status'] != "")
		{
			$sql .= " AND material_status =".addslashes($where['material_status']);
		}
		
		$sql .= " ORDER BY material_id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	
	//验证表中是否已存在 材质名称  BY linian
	function getMaterialName($material_name){
		$sql = "SELECT * FROM `".$this->table()."`  WHERE 1 ";
		
		if($material_name != "")
		{
			$sql .= " AND material_name ='{$material_name}'";
		}		
		return $res = $this->db()->getRow($sql);
	
	}
}

?>