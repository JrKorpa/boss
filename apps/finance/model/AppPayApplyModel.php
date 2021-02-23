<?php
/**
 *  -------------------------------------------------
 *   @file		: AppPayApplyModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-21 19:03:33
 *   @update	:
 *  -------------------------------------------------
 */
class AppPayApplyModel extends Model
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
	 *	@url AppPayApplyController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";
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