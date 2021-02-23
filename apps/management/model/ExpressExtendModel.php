<?php
/**
 *  -------------------------------------------------
 *   @file		: ExpressExtendModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2016-01-27 13:49:35
 *   @update	:
 *  -------------------------------------------------
 */
class ExpressExtendModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'express_extend';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"ID",
"express_id"=>"快递公司ID",
"send_time_end"=>"暂停时间",
"send_time_start"=>"开始时间",
"exp_areas_id"=>"地区ID",
"exp_areas_name"=>"地区名称");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url ExpressExtendController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
//		if($where['xxx'] != "")
//		{
//			$str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
//		}
		if(!empty($where['express_id']))
		{
			$str .= "`express_id`='".$where['express_id']."' AND ";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
}

?>