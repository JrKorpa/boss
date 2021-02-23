<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseOutVoucherModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 21:07:31
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseOutVoucherModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'warehouse_out_voucher';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"序号",
"voucher_outno"=>"出库单号",
"kela_order_sn"=>"BDD订单号",
"voucher_type"=>"1外部，2内部",
"voucher_stauts"=>"单据状态",
"warehouse_id"=>"转出仓ID",
"company_id"=>"转出公司ID",
"goods_num"=>"总数",
"cost_price"=>"总成本",
"sales_price"=>"总销售价",
"addby_id"=>"制单人",
"add_time"=>"制单时间",
"addby_ip"=>"制单IP",
"check_id"=>"审核人",
"check_time"=>"审核时间",
"check_ip"=>"审核IP",
"note"=>"备注",
"is_deleted"=>"删除标识");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url WarehouseOutVoucherController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";
		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
}

?>