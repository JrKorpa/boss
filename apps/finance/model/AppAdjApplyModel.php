<?php
/**
 *  -------------------------------------------------
 *   @file		: AppAdjApplyModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-21 19:03:33
 *   @update	:
 *  -------------------------------------------------
 */
class AppAdjApplyModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_pay_apply';
		$this->pk='apply_id';
		$this->_prefix='';
        $this->_dataObject = array("apply_id"=>"ID",
"pay_apply_number"=>"应付申请单号",
"status"=>"状态(0、新增；1、待审核；2、已驳回；3、已取消；4、待生成应付单；5、已生成应付单)",
"pay_number"=>"财务应付单单号",
"make_time"=>"制单时间",
"make_name"=>"制单人",
"check_time"=>"审核时间",
"check_name"=>"审核人",
"company"=>"所属公司",
"prc_id"=>"供货商ID",
"prc_name"=>"供货商名称",
"pay_type"=>"应付类型",
"amount"=>"总数量",
"total_cope"=>"应付金额",
"total_dev"=>"偏差金额",
"adj_reason"=>"调整原因（调整单的调整原因）",
"record_type"=>"单据类型(1、应付申请单；2、应付调整单)",
"overrule_reason"=>"调整单的驳回原因",
"fapiao"=>"发票");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppAdjApplyController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";
		if(!empty($where['company']))
		{
			$sql .= " AND `company` = {$where['company']}";
		}
		if(!empty($where['status']))
		{
			$sql .= " AND `status` = {$where['status']}";
		}
		if(!empty($where['prc_id']))
		{
			$sql .= " AND `prc_id` = {$where['prc_id']}";
		}
		if(!empty($where['payType']))
		{
			$sql .= " AND `pay_type` = {$where['payType']}";
		}
		if(!empty($where['pay_number']))
		{
			$sql .= " AND `pay_number` = '{$where['pay_number']}'";
		}
		if(!empty($where['pay_apply_number']))
		{
			$sql .= " AND `pay_apply_number` = '{$where['pay_apply_number']}'";
		}
		if(!empty($where['start_make_date']))
		{
			$sql .= " AND `make_time` >= '{$where['start_make_date']} 00:00:00'";
		}
		if(!empty($where['end_make_date']))
		{
			$sql .= " AND `make_time` <= '{$where['end_make_date']} 23:59:59'";
		}
		if(!empty($where['start_check_date']))
		{
			$sql .= " AND `check_time` >= '{$where['start_check_date']} 00:00:00'";
		}
		if(!empty($where['end_check_date']))
		{
			$sql .= " AND `check_time` <= '{$where['end_check_date']} 23:59:59'";
		}
		$sql .= " ORDER BY apply_id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	function getNameList($id=NULL,$status=NULL)
	{
		$where = 'pt_id > 0 ';
		if(!empty($id)){
			$where .= " AND p_id = $id";
		}
		if(!empty($status)){
			$where .= " AND status = $status";
		}
		$sql = "SELECT p_id,p_name FROM `jxc_processors`";
		$sql .= " where $where";
		return $this->db()->getAll($sql,array(),false);
	}
}

?>