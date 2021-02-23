<?php
/**
 *  -------------------------------------------------
 *   @file		: OrderFqcModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-28 15:59:57
 *   @update	:
 *  -------------------------------------------------
 */
class OrderFqcModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'order_fqc';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"admin"=>"",
"order_sn"=>"订单号",
"problem_type"=>"问题类型（工厂，订单，仓库）",
"problem"=>"问题（刻字、）",
"datatime"=>" ",
"remark"=>"备注",
"is_pass"=>"是否质检通过");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url OrderFqcController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	
	/**
	 * 根据订单号获取订单信息
	 * is_return 必须是数字类型的，不可以传字符串类型的。
	 * @return json
	 */
	public function getOrderGoodsInfoByOrdersn($order_sn) {
	    $sql = "SELECT a.*,b.*,s.shoucun,s.company_id,s.company,s.warehouse,s.warehouse_id FROM app_order.base_order_info AS `a` LEFT JOIN app_order.app_order_details AS `b` ON `a`.`id` = `b`.`order_id` left join`warehouse_shipping`.`warehouse_goods` s ON b.goods_id=s.goods_id WHERE 1"; //暂时用＊号
	    $sql .= " and `order_sn` = '{$order_sn}'";
	    return $this->db()->getAll($sql);
	}
	
	public function getOrderInfoByOrderSn($order_sn){
	    //查询商品详情
	    $sql = "SELECT `a`.* , b.`order_amount`,b.`money_paid`,b.`money_unpaid`,ar.`consignee` as shouhuoren,ar.`distribution_type`,b.`coupon_price`,(select group_concat(distinct r.out_order_sn) from app_order.rel_out_order r where r.order_id = a.id group by r.order_id) as out_order_sn  FROM app_order.base_order_info AS `a` LEFT JOIN app_order.app_order_account AS `b` ON `a`.`id`= `b`.`order_id` LEFT JOIN app_order.app_order_address as ar ON `a`.`id`=`ar`.`order_id`  WHERE `a`.`order_sn` = '{$order_sn}' ;";
	    return $this->db()->getRow($sql);
	}
	
	public function GetStyleGallery($style_sn) {
	    if(is_array($style_sn)){
	        $style_sn = implode("','",$style_sn);
	    }
	    $sql = "SELECT `style_sn`, `img_ori`,`thumb_img`,`middle_img`,`big_img` FROM front.app_style_gallery WHERE `image_place` = 1 AND `style_sn` in ('{$style_sn}') GROUP BY style_sn";
	    return $this->db()->getAll($sql);
	}
	/**
	 * 订单赠品详情
	 */
	public function getOrderGiftInfo($order_id) {
	    $sql="SELECT `gift_id`,`remark`,`gift_num` FROM app_order.rel_gift_order WHERE `order_id`={$order_id}";
	    return $this->db()->getRow($sql);
	}
	
	public function getOrderActionList($order_id, $hidden=true) {
	    $where = " order_id = $order_id ";
        if($hidden){
            $where.=" ORDER BY `action_id` DESC";
        }else{
            $where.=" ORDER BY `action_id` asc limit 1";
        }
	    $action_field = " `order_id`, `order_status`, `shipping_status`, `pay_status`,`create_time`,`create_user`,`remark` ";
	    $sql = "SELECT $action_field FROM app_order.app_order_action WHERE $where";
	    return $this->db()->getAll($sql);
	}
}

?>