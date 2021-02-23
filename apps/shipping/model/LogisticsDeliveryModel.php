<?php
/**
 *  -------------------------------------------------
 *   @file		: LogisticsDeliveryModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-06 11:57:16
 *   @update	:
 *  -------------------------------------------------
 */
class LogisticsDeliveryModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'logistics_delivery';
        $this->_dataObject = array("id"=>"id",
			"order_sn"=>"订单号",
			"delivery_sn"=>"快递号",
			"user_name"=>"操作人",
			"date_time"=>"操作时间",
			"ip"=>"操作ip",
			"user_id"=>" ",
			"jijian_person"=>"寄件人",
			"dep_id"=>"寄件部门",
			"address"=>"收货地址",
			"ship_company"=>"快递公司",
			"reason"=>"寄件缘由",
			"is_delete"=>""
			);
		parent::__construct($id,$strConn);
	}


				/**
	pageList ,分页
	**/
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{

		$sql  = "SELECT m.*  FROM `".$this->table()."` AS m where `m`.`is_delete` = '0'  ";
		$str = '';
		if($where['delivery_sn'])
		{
			$str .=" AND `m`.`delivery_sn` ='".$where['delivery_sn']."'  ";
		}
		if($where['user_name'])
		{
			$str .=" AND `m`.`user_name`='".$where['user_name']."' ";
		}
		if($where['order_sn'])
		{
			$str .=" AND `m`.`order_sn`='".$where['order_sn']."'  ";
		}
		if($where['date_time_s'] != '')
		{
			$str .= " AND `m`.`date_time` >= '".$where['date_time_s']." 00:00:00' ";
		}
		if($where['date_time_e'] != '')
		{
			$str .= " AND `m`.`date_time` <= '".$where['date_time_e']." 23:59:59' ";
		}

		$sql .=  $str;

		$sql .= " ORDER BY m.id DESC";
		//var_dump($sql);exit;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
}

?>