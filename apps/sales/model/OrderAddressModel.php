<?php
/**
 *  -------------------------------------------------
 *   @file		: OrderAddressModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-12 12:33:46
 *   @update	:
 *  -------------------------------------------------
 */
class OrderAddressModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'order_address';
        $this->_dataObject = array("id"=>"id",
		"order_id"=>"订单id",
		"consignee"=>"收货人",
		"distribution_type"=>"配送方式",
		"country_id"=>"国家id",
		"province_id"=>"省份id",
		"city_id"=>"城市id",
		"regional_id"=>"区域id",
		"address"=>"详细地址",
		"tel"=>"电话",
		"email"=>"email",
		"zipcode"=>"邮编",
		"goods_id"=>"商品id");
		parent::__construct($id,$strConn);
	}
	/***根据订单id查询地址信息***/
	public function select($id)
	{
		$sql = "select * from ".$this->table()." where order_id = ".$id;
		//var_dump($sql);exit;
		return $this->db()->getRow($sql);
	}
}

?>