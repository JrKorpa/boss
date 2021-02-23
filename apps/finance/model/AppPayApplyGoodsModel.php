<?php
/**
 *  -------------------------------------------------
 *   @file		: AppPayApplyGoodsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-21 22:08:51
 *   @update	:
 *  -------------------------------------------------
 */
class AppPayApplyGoodsModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_pay_apply_goods';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"apply_id"=>"应付申请ID",
"serial_number"=>"货流水号",
"goods_id"=>"货号/单号",
"total"=>"系统金额",
"total_cope"=>"应付金额",
"total_dev"=>"偏差金额",
"dev_direction"=>"偏差说明",
"overrule_reason"=>"驳回原因");
		parent::__construct($id,$strConn);
	}



	/**
	 *	pageList，分页列表
	 *
	 *	@url AppPayApplyGoodsController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT `id`,`apply_id`,`serial_number`,`goods_id`,`total`,`total_cope`,`total_dev`,`dev_direction`,`overrule_reason` FROM `".$this->table()."` WHERE 1 ";
		$sql .= " ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

    function getDataOfApplyId($applyid)
    {
        $sql = "SELECT `id`,`apply_id`,`serial_number`,`goods_id`,`total`,`total_cope`,`total_dev`,`dev_direction`,`overrule_reason` FROM `".$this->table()."` WHERE apply_id = '$applyid' ";
		return $this->db()->getAll($sql);    
    }
}

?>