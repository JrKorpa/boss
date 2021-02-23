<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseOrderInfoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-07-12 14:44:01
 *   @update	:
 *  -------------------------------------------------
 */
class PurchaseOrderInfoModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'purchase_order_info';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"ID",
"purchase_id"=>"采购单明细ID",
"detail_id"=>"订单明细ID",
"order_sn"=>"订单号",
"dep_name"=>"销售渠道");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url PurchaseOrderInfoController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT `oi`.`id`,`poi`.`order_sn`,`ro`.`out_order_sn`,oi.`create_user`,`oi`.`create_time`,
        oi.order_status,(case `oi`.`buchan_status` when 1 then '初始化' when 2 then '待分配' when 3 then '已分配' when 4 then '生产中' when 5 then '质检中' when 6 then '质检完成' when 7 then '部分出厂' when 8 then '作废' when 9 then '已出厂' when 10 then '已取消' when 11 then '不需布产' else '其他' end) as buchan_status,oi.delivery_status,poi.dep_name,pi.p_sn
         FROM app_order.`".$this->table()."` poi 
        inner join purchase.purchase_goods pg on pg.id = poi.purchase_id 
        inner join purchase.purchase_info pi on pi.id = pg.pinfo_id
        inner join app_order.base_order_info oi on oi.order_sn = poi.order_sn
        inner join app_order.rel_out_order ro on ro.order_id = oi.id and ro.goods_detail_id = poi.detail_id";
		$str = '';
//		if($where['xxx'] != "")
//		{
//			$str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
//		}
//        'order_sn'   =>$args["order_sn"],
		if(!empty($where['order_sn']))
		{
			$str .= "`poi`.`order_sn`='".$where['order_sn']."' AND ";
		}
        if(!empty($where['out_order_sn']))
        {
            $str .= "`ro`.`out_order_sn`='".$where['out_order_sn']."' AND ";
        }
        if(!empty($where['create_user']))
        {
            $str .= "`oi`.`create_user`='".$where['create_user']."' AND ";
        }
        if(!empty($where['order_status']))
        {
            $str .= "`oi`.`order_status`='".$where['order_status']."' AND ";
        }
        if(!empty($where['buchan_status']))
        {
            $str .= "`oi`.`buchan_status`='".$where['buchan_status']."' AND ";
        }
        if(!empty($where['delivery_status']))
        {
            $str .= "`oi`.`delivery_status`='".$where['delivery_status']."' AND ";
        }
        if(!empty($where['order_department']))
        {
            $str .= "`oi`.`department_id`='".$where['order_department']."' AND ";
        }
        if(!empty($where['start_time'])){
            $str.="`oi`.`create_time` >= '".$where['start_time']." 00:00:00' AND ";
        }
        if(!empty($where['end_time'])){
            $str.="`oi`.`create_time` <= '".$where['end_time']." 23:59:59' AND ";
        }
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `poi`.`id` DESC";
        //echo $sql;
		$data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
}

?>