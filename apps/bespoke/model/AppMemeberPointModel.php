<?php
/**
 *  -------------------------------------------------
 *   @file		: AppMemeberPointModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 13:33:02
 *   @update	:
 *  -------------------------------------------------
 */
class AppMemeberPointModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_memeber_point';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"序号",
"memeber_id"=>"会员ID",
"change_step"=>"本次调整",
"chane_type"=>"事件类型",
"change_status"=>"调整状态",
"happen_time"=>"发生时间",
"pass_time"=>"通过时间",
"pass_userid"=>"操作人",
"is_deleted"=>"删除标识");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppMemeberPointController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 AND is_deleted = 0";
		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
}

?>