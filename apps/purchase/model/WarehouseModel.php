<?php
/**
 * 仓库数据模块的模型（代替WareHouse/Api/api.php）
 *  -------------------------------------------------
 *   @file		: WareHouseModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseModel extends SelfModel
{
    protected $db;
	function __construct ($strConn="")
	{
		parent::__construct($strConn);
	}
    
    // 根据货号确认货品信息
    public function checkGoodsByGoods_id($goods_sn)
    {
        $sql = "select `fushizhong`,`fushilishu`,`shi2zhong`,`shi2lishu` from `warehouse_shipping`.`warehouse_goods` where `goods_id` = '$goods_sn'";
        return $this->db()->getRow($sql);
    }
}

?>