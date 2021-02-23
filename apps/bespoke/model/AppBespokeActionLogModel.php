<?php
/**
 *  -------------------------------------------------
 *   @file		: AppBespokeActionLogModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-12 14:32:07
 *   @update	:
 *  -------------------------------------------------
 */
class AppBespokeActionLogModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_bespoke_action_log';
		$this->pk='action_id';
		$this->_prefix='';
        $this->_dataObject = array("action_id"=>"自增ID",
"bespoke_id"=>"预约ID",
"create_user"=>"操作人  ",
"create_time"=>"操作时间",
"IP"=>"操作IP",
"bespoke_status"=>"预约状态",
"remark"=>"备注");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppBespokeActionLogController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";
		$sql .= " ORDER BY action_id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	/**
	 *	getActionLogByBespokeID，按预约ID查找日志
	 *
	 *	@url AppBespokeInfoController/show
	 */
    function getActionLogByBespokeID ($id)
    {
		$sql = "SELECT * FROM `".$this->table()."` WHERE bespoke_id = '{$id}' order by create_time asc ";
		$data = $this->db()->getAll($sql);
		return $data;
    }
}

?>