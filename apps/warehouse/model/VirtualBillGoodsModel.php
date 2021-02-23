<?php
/**
 *  -------------------------------------------------
 *   @file		: VirtualBillGoodsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-11-04 16:43:23
 *   @update	:
 *  -------------------------------------------------
 */
class VirtualBillGoodsModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'virtual_bill_goods';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"ID流水号（虚拟货号）",
"bill_id"=>"单据ID",
"virtual_id"=>"无账修退流水号",
"business_type"=>"业务类型（退货/维修）",
"order_sn"=>"订单号",
"goods_id"=>"货号",
"style_sn"=>"款号",
"ingredient_color"=>"主成色",
"gold_weight"=>"金重",
"torr_type"=>"金托类型",
"product_line"=>"产品线",
"style_type"=>"款式分类",
"finger_circle"=>"指圈",
"credential_num"=>"证书号",
"main_stone_weight"=>"主石重",
"main_stone_num"=>"主石粒数",
"deputy_stone_weight"=>"副石重",
"deputy_stone_num"=>"副石粒数",
"resale_price"=>"零售价");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url VirtualBillGoodsController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
//		if($where['xxx'] != "")
//		{
//			$str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
//		}
		if(!empty($where['bill_id']))
		{
			$str .= "`bill_id`='".$where['bill_id']."' AND ";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
}

?>