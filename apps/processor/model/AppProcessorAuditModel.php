<?php
/**
 *  -------------------------------------------------
 *   @file		: AppProcessorAuditModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-22 10:07:27
 *   @update	:
 *  -------------------------------------------------
 */
class AppProcessorAuditModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_processor_audit';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"序号",
"record_id"=>"申请ID",
"process_id"=>"流程ID",
"user_id"=>"审批人ID",
"audit_status"=>"审批状态:1待审批,2审批中,3通过,4驳回",
"audit_time"=>"审批时间",
"audit_plan"=>"审批进度");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppProcessorAuditController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";
		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	/*获取用户最后一次审核时间*/
	public function getcheckLastTime($user_id,$record_id){
		$sql = "SELECT `audit_time` FROM `".$this->table()."` WHERE `user_id` = ".$user_id." AND `record_id` =".$record_id ;
		$res = $this->db()->getOne($sql);
		return $res;
	}



}

?>