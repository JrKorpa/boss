<?php
/**
 *  -------------------------------------------------
 *   @file		: AppStyleForModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-19 10:41:52
 *   @update	:
 *  -------------------------------------------------
 */
class AppStyleForModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_style_for';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"style_id"=>"款式id",
"style_for_who"=>"适合对象(按年龄)",
"style_for_use"=>"适合场景(按用途)",
"style_for_when"=>"适合节庆",
"style_for_designer"=>"设计师");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppStyleForController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";
        if(isset($where['style_id']) && !empty($where['style_id'])){
            $sql .=" AND style_id = {$where['style_id']} ";
        }
		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	/**
	 *	pageList，分页列表
	 *
	 *
	 */
	function get_style_for_by_id ($where)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";
        if(isset($where['id']) && !empty($where['id'])){
            $sql .=" AND id = {$where['id']} ";
        }
		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getRow($sql);
		return $data;
	}
}

?>