<?php
/**
 *  -------------------------------------------------
 *   @file		: AppReceiveOperatLogModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-27 15:05:14
 *   @update	:
 *  -------------------------------------------------
 */
class AppReceiveOperatLogModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_receive_operat_log';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"related_id"=>"关联ID",
"type"=>"分类( 1、应付申请单)",
"operat_name"=>"操作人",
"operat_time"=>"操作时间",
"operat_content"=>"操作内容");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppReceiveOperatLogController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
        if(!empty($where["related_id"]))
		{
			$str .= " related_id = '".$where["related_id"]."' AND";
		}
		if(!empty($where["type"]))
		{
			$str .= " type = '".$where["type"]."' AND ";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		//$data['data'] = $this->db()->getAll($sql);
		return $data;
	}
}

?>