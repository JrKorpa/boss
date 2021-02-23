<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseInOrderModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 16:54:53
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseInOrderModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'warehouse_in_order';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"序号",
"order_no"=>"入库单号",
"order_type"=>"单据类型",
"put_in_type"=>"入库方式",
"send_no"=>"送货单号",
"goods_num"=>"货品总数",
"cost_price"=>"总成本",
"pay_price"=>"总支付金额",
"order_status"=>"状态1保存，2取消，3已审核",
"prc_id"=>"加工商ID",
"company_id"=>"所属公司ID",
"addby_id"=>"入库人",
"addby_time"=>"申请时间",
"addby_ip"=>"入库IP",
"check_id"=>"审核人",
"check_time"=>"审核时间",
"check_ip"=>"审核IP",
"order_note"=>"备注",
"is_deleted"=>"删除标识");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url WarehouseInOrderController/search
	 */
	public function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";
		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	public function makTable(){
		$table_id = ['id'=>'#from_table_data'];
		$table_title = ['title'=>['序号','货品名称','货品货号','货品款式','成本价','货品数量','价格小计']];
		$table_columns = ['columns'=>[]];

	}

}

?>