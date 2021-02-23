<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseStyleLogModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-02 16:58:21
 *   @update	:
 *  -------------------------------------------------
 */
class BaseStyleLogModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'base_style_log';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"自增ID",
"style_id"=>"款id",
"create_user"=>"操作人",
"create_time"=>"操作时间",
"remark"=>"操作备注");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url BaseStyleLogController/search
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
		if(!empty($where['style_id']))
		{
			$str .= "`style_id`='".$where['style_id']."' AND ";
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