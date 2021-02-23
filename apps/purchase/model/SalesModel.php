<?php
/**
 * 销售模块的数据模型（代替Sales/Api/api.php）
 *  -------------------------------------------------
 *   @file		: SaleModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class SalesModel extends SelfModel
{
    protected $db;
	function __construct ($strConn="")
	{
		parent::__construct($strConn);
	}

    /**
     *跟进布产ID获取订单里面的商品属性
     */
    public function getOrderAttrInfoByBc_sn($where)
    {
        $sql = "select * from `app_order_details` where `bc_id` = ".$where['id']." and `goods_sn` = '".$where['style_sn']."'";
        return $this->db->getRow($sql);
    }
    

}

?>