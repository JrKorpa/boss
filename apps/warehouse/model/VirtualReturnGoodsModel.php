<?php
/**
 *  -------------------------------------------------
 *   @file		: VirtualReturnGoodsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-09-12 10:08:11
 *   @update	:
 *  -------------------------------------------------
 */
class VirtualReturnGoodsModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'virtual_return_goods';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"ID流水号（虚拟货号）",
"business_type"=>"业务类型（退货/维修）",
"order_sn"=>"订单号",
"return_status"=>"货号修退状态",
"style_sn"=>"款号",
"caizhi"=>"材质",
"ingredient_color"=>"材质颜色",
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
"resale_price"=>"零售价",
"out_goods_id"=>"出库货号",
"place_company_id"=>"货品所在公司ID",
"place_company_name"=>"货品所在公司名称",
"place_warehouse_id"=>"货品所在仓库ID",
"place_warehouse_name"=>"货品所在仓库名称",
"guest_name"=>"顾客姓名",
"guest_contact"=>"顾客联系方式",
"return_remark"=>"退货备注",
"without_apply_time"=>"无账申请时间",
"apply_user"=>"申请人",
"exist_account_gid"=>"有账货号",
"exist_account_user"=>"转有账用户",
"exist_account_time"=>"转有账时间");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url VirtualReturnGoodsController/search
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
//		if(!empty($where['xx']))
//		{
//			$str .= "`xx`='".$where['xx']."' AND ";
//		}
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