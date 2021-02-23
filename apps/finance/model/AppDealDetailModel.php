<?php
/**
 *  -------------------------------------------------
 *   @file		: AppDealDetailModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-26 16:49:30
 *   @update	:
 *  -------------------------------------------------
 */
class AppDealDetailModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_deal_detail';
		$this->pk='id';
		$this->_prefix='';
		$this->_dataObject = array("id"=>"序号",
			"detail_type"=>"明细类型",
			"serial_number"=>"流水号",
			"goods_no"=>"货号/单据编号",
			"certficate_no"=>"证书号",
			"goods_type"=>"货品分类/单据分类",
			"make_time"=>"入库制单时间",
			"check_time"=>"入库审核时间",
			"put_in_type"=>"入库方式",
			"goods_status"=>"货品状态",
			"supplier_id"=>"供货商ID",
			"supplier_name"=>"供货商名称",
			"supplier_order"=>"供货商单号/纸质单据",
			"amount_total"=>"单据金额",
			"company_id"=>"所属公司",
			"pay_cont"=>"支付内容",
			"apply_id"=>"应付申请单ID",
			"apply_number"=>"应付申请单号",
			"apply_status"=>"应付申请状态");
		parent::__construct($id,$strConn);
	}


	/**
	 *	pageList，分页列表
	 *
	 *	@url AppDealDetailController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{

		$sql = "SELECT * FROM `".$this->table()."`";

		$str = "WHERE `detail_type`=".$where['detail_type'];//明细类型

		if(!empty($where['id'])) {$str .= " AND `id` = '{$where['id']}'";}
		if(!empty($where['serial_number'])) {$str .= " AND `serial_number` = '{$where['serial_number']}'";}
		if(!empty($where['goods_no'])) {$str .= " AND `goods_no` = '{$where['goods_no']}'";}
		if(!empty($where['certficate_no'])) {$str .= " AND `certficate_no` = '{$where['certficate_no']}'";}
		if(!empty($where['goods_type'])) {$str .= " AND `goods_type` = '{$where['goods_type']}'";}
		if(!empty($where['goods_status'])) {$str .= " AND `goods_status` = '{$where['goods_status']}'";}
		if(!empty($where['processor_id'])) {$str .= " AND `processor_id` in ({$where['processor_id']})";}
		if(!empty($where['apply_status'])) {$str .= " AND `apply_status` = '{$where['apply_status']}'";}
		if(!empty($where['apply_number'])) {$str .= " AND `apply_number` = '{$where['apply_number']}'";}
		if(!empty($where['putin_type'])) {$str .= " AND `put_in_type` = '{$where['putin_type']}'";}
		if(!empty($where['supplier_order'])) {$str .= " AND `supplier_order` = '{$where['supplier_order']}'";}
		if(!empty($where['supplier_id'])) {$str .= " AND `supplier_id` = '{$where['supplier_id']}'";}
		if(!empty($where['supplier_name'])) {$str .= " AND `supplier_name` = '{$where['supplier_name']}'";}
		if(!empty($where['company_id'])) {$str .= " AND `company_id` = '{$where['company_id']}'";}
		if(!empty($where['mk_from'])) {$str .= " AND `make_time` >= '".$where['mk_from']." 00:00:00'";}
		if(!empty($where['mk_to'])) {$str .= " AND `make_time` <= '".$where['mk_to']." 23:59:59'";}
		if(!empty($where['check_from'])) {$str .= " AND `check_time` >= '".$where['check_from']." 00:00:00'";}
		if(!empty($where['check_to'])) {$str .= " AND `check_time` <= '".$where['check_to']." 23:59:59'";}

		$sql .= $str;
		$sql .= " ORDER BY `id` DESC";
//		print_r($sql);exit;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	public function getExportList($where){
		$sql = 'SELECT `serial_number`,`detail_type`,`supplier_name`,`amount_total` FROM `app_deal_detail`';
		$sql .= 'WHERE `detail_type` = '.$where['detail_type'];

		if(!empty($where['apply_status'])){$sql .= ' AND `apply_status` ='.$where['apply_status'];};
		if(!empty($where['serial_number'])) {$sql .= " AND `serial_number` = '{$where['serial_number']}'";};
		if(!empty($where['apply_number'])) {$sql .= " AND `apply_number` = '{$where['apply_number']}'";};
		if(!empty($where['supplier_order'])){$sql .= " AND `supplier_order` ='{$where['supplier_order']}'";};
		if(!empty($where['certficate_no'])){$sql .= " AND `certficate_no` ='{$where['certficate_no']}'";};
		if(!empty($where['putin_type'])){$sql .= ' AND `putin_type` ='.$where['putin_type'];};
		if(!empty($where['supplier_id'])){$sql .= ' AND `supplier_id` ='.$where['supplier_id'];};
		if(!empty($where['company_id'])){$sql .= ' AND `company_id` ='.$where['company_id'];};
		if(!empty($where['mk_from'])) {$sql .= " AND `make_time` >= '".$where['mk_from']." 00:00:00'";}
		if(!empty($where['mk_to'])) {$sql .= " AND `make_time` <= '".$where['mk_from']." 23:59:59'";}
		if(!empty($where['check_from'])) {$sql .= " AND `check_time` >= '".$where['check_from']." 00:00:00'";}
		if(!empty($where['check_to'])) {$sql .= " AND `check_time` <= '".$where['check_to']." 23:59:59'";}

		$data = $this->db()->getAll($sql);
		return $data;
	}

	/**
	 * 生成流水号
	 * @param $type
	 * @return bool|string
	 */
	public function mkSerialNO($type){
		switch ($type) {
			case '1':
				$number = 'DX';
				break;
			case '2':
				$number = 'CP';
				break;
			case '3':
				$number = 'SB';
				break;
			default:
				return false;
				break;
		}
		$sql = 'SELECT `serial_number` FROM `app_deal_detail` WHERE id = (SELECT max(id) from `app_deal_detail`)';
		$str = $this->db()->getOne($sql);
		$no = (substr($str,2,8) != date('Ymd',time()))?1:intval(substr($str,11))+1;
		$number .= date('Ymd',time()).str_pad($no,6,"0",STR_PAD_LEFT);
		return  $number;
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function getRow($id)
	{
		$sql = "select * from ".$this->table();
		$sql .= " WHERE `serial_number` = '$id'";
		return $this->db()->getRow($sql);
	}

	public function getDetailName($id = 0){
		$type = [
			['id'=>1,'label'=>'代销借贷'],
			['id'=>2,'label'=>'成品采购'],
			['id'=>3,'label'=>'石包采购'],
		];
		return ($id)?$type[($id-1)]['label']:$type;
	}



}

?>