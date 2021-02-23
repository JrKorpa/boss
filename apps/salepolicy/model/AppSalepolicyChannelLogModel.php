<?php
/**
 *  -------------------------------------------------
 *   @file		: AppSalepolicyChannelLogModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-15 10:27:48
 *   @update	:
 *  -------------------------------------------------
 */
class AppSalepolicyChannelLogModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_salepolicy_channel_log';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"自增ID",
"policy_id"=>"销售策略id",
"create_user"=>"操作人  ",
"create_time"=>"操作时间",
"IP"=>"操作IP",
"status"=>"记录状态 1保存,2申请,3审核通过,4未通过,5取消",
"remark"=>"备注",
"is_delete"=>"删除状态 1已删除,2未删除");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppSalepolicyChannelLogController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";
        if(isset($where['policy_id'])&&!empty($where['policy_id'])){
            $sql.=" AND policy_id = '".$where['policy_id']."'";
        }
		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
}

?>