<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseOrderInfoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2019-06-21 17:54:25
 *   @update	:
 *  -------------------------------------------------
 */
class BaseOrderInfoModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'base_order_info';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"order_sn"=>"订单编号",
"old_order_id"=>"老订单号",
"bespoke_id"=>"预约号",
"old_bespoke_id"=>"老预约号",
"user_id"=>"会员id",
"consignee"=>"名字",
"mobile"=>"手机号",
"order_status"=>"订单审核状态: 1（默认待审核）2已审核 3取消 4关闭5审核未通过",
"order_pay_status"=>"支付状态:1未付款2部分付款3已付款4财务备案",
"order_pay_type"=>"支付类型;0:默认，1:展厅订购,2:货到付款",
"delivery_status"=>"[参考数字字典：配送状态(sales.delivery_status)]",
"send_good_status"=>"1未发货2已发货3收货确认4允许发货5已到店",
"buchan_status"=>"布产状态:1未操作,2已布产,3生产中,4已出厂,5不需布产",
"customer_source_id"=>"客户来源",
"department_id"=>"订单部门",
"create_time"=>"制单时间",
"create_user"=>"制单人",
"check_time"=>"审核时间",
"check_user"=>"审核人",
"genzong"=>"跟单人",
"recommended"=>"推荐人",
"recommender_sn"=>"推荐人会员编号",
"modify_time"=>"修改时间",
"order_remark"=>"备注信息",
"referer"=>"录单来源",
"is_delete"=>"订单状态0有效1删除",
"apply_close"=>"申请关闭:0=未申请，1=申请关闭",
"is_xianhuo"=>"是否是现货：1现货 0定制",
"is_print_tihuo"=>"是否打印提货单（数字字典confirm）",
"effect_date"=>"订单生效时间(确定布产)",
"is_zp"=>"订单是否是赠品订单 1是0不是",
"pay_date"=>"第一次点款时间",
"apply_return"=>"1未操作2正在退款",
"weixiu_status"=>"维修状态",
"update_time"=>"更新时间",
"shipfreight_time"=>" ",
"is_real_invoice"=>"是否需要开发票",
"out_company"=>" ",
"discount_point"=>"折扣积分",
"reward_point"=>"奖励积分",
"jifenma_point"=>"积分码积分",
"zhuandan_cash"=>"转单金额",
"hidden"=>"是否隐藏栏位",
"birthday"=>"会员生日",
"profile_id"=>"crm会员信息id");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url BaseOrderInfoController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT o.*,(select s.freight_no from shipping.ship_freight s where  s.order_no=o.order_sn limit 1) as express_no,(select GROUP_CONCAT(DISTINCT rod.out_order_sn) from rel_out_order as rod where rod.order_id=o.id group by rod.order_id) as out_order_sn FROM `".$this->table()."` as o ";
		$str = 'o.department_id=13 AND ';

		if(!empty($where['order_sn']))
			$str .=" o.order_sn='{$where['order_sn']}' AND ";
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE  ".$str;
		}
		$sql .= " ORDER BY o.`id` DESC limit 30";
		//echo $sql;
		$data = $this->db()->getAll($sql);
		//print_r($data);
		return $data;
	}

	/**
	 *	获取订单商品总数量
	 *
	 *	@url BaseOrderInfoController/search
	 */
	function getGoodsNum ($order_sn)
	{
		$sql = "select count(1) as goodsNum from app_order_details d,base_order_info o where d.order_id=o.id and o.order_sn='{$order_sn}'";
		//echo $sql;
		return $this->db()->getOne($sql);
	}

}

?>