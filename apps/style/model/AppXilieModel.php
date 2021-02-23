<?php
/**
 *  -------------------------------------------------
 *   @file		: AppJinsunModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 16:42:07
 *   @update	:
 *  -------------------------------------------------
 */
class AppXilieModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_style_xilie';
	
        
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url MessageController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `front`.`app_style_xilie` WHERE 1 ";
      
        if(isset($where['xilie_id']) && !empty($where['xilie_id'])){
            $sql .=" AND id = '{$where['xilie_id']}' ";
        }
		if(isset($where['xilie_status']) && $where['xilie_status']!=''){
            $sql .=" AND xilie_status = '{$where['xilie_status']}' ";
        }
		$sql .= " ORDER BY id ASC";
		$data = $this->db(11)->getPageList($sql,array(),$page, $pageSize,$useCache);
		//var_dump($data,66);exit;
		return $data;
	}

	//判断是否存在有值
	function getXilieName ($where)
	{
        if(!empty($where['name']) ){
			$sql = "SELECT id FROM `front`.`app_style_xilie` WHERE name ='{$where['name']}' ";
           
		    $data = $this->db()->getOne($sql);
        }else{
			$data='';
		}
		return $data;
	}
    function getAllXilieName ()
	{

			$sql = "SELECT id,name FROM `front`.`app_style_xilie`   ";
           
		    $data = $this->db()->getAll($sql);
  
	
		return $data;
	}
    function getXilieNameBystatus ()
	{

			$sql = "SELECT id,name FROM `front`.`app_style_xilie` where status=1 ";
           
		    $data = $this->db()->getAll($sql);
  
	
		return $data;
	}
}

?>