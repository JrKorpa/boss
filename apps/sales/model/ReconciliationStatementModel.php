<?php
/**
 *  -------------------------------------------------
 *   @file		: ReconciliationStatementModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: zhangruiying
 *   @date		: 2015/4/24
 *   @update	:
 *  -------------------------------------------------
 */
class ReconciliationStatementModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'base_order_info';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
			"order_sn"=>"订单编号",
            "user_id"=>"会员id",
            "consignee"=>"名字");
		parent::__construct($id,$strConn);
	}
    /**	pageList，分页列表
	 *
	 *	@url ApplicationController/search
	 */
	function getSql($where)
	{
		$sql  = "SELECT a.order_sn,a.consignee,b.order_amount,a.department_id,a.id,rel.out_order_sn,det.goods_name,address.express_id,address.freight_no FROM rel_out_order as rel left join `".$this->table()."` as a on rel.order_id=a.id LEFT JOIN `app_order_account` as b ON `a`.`id`=`b`.`order_id`  left join app_order_details as det on det.id=rel.id left join app_order_address as address on rel.order_id=address.order_id where (a.order_status<3 or a.order_status>4)";

		if(isset($where['order_sn']) and $where['order_sn'] != "")
		{
			$sql .= " and `rel`.`out_order_sn`='{$where['order_sn']}'";
		}
		if(isset($where['ids'])  and $where['ids'] != "")
		{
			$sql .= " and `rel`.`out_order_sn` in({$where['ids']})";
		}
		if(isset($where['channel_id']) and $where['channel_id']!="")
		{
			$sql .= " and `a`.`department_id`={$where['channel_id']}";
		}
        //zt隐藏
        if(SYS_SCOPE == 'zhanting'){
            $sql .= " and `a`.`hidden` <> 1";
        }

		$sql .= " ORDER BY `rel`.`id` DESC";
		return $sql;
	}
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{

		$sql=$this->getsql($where);
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;

	}
	function getDownload($where)
	{
		$sql=$this->getsql($where);
		return $data = $this->db()->getAll($sql);
	}

}

?>
