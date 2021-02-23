<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseGoodsAttrModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-26 21:30:35
 *   @update	:
 *  -------------------------------------------------
 */
class PurchaseGoodsAttrModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'purchase_goods_attr';
        $this->_dataObject = array("id"=>"ID",
"g_id"=>"采购商品ID",
"code"=>"属性code",
"name"=>"属性name",
"value"=>"属性value值");
		parent::__construct($id,$strConn);
	}

	function getGoodsAttr($gid)
	{
		$sql = "SELECT `id`,`g_id`,`code`,`name`,`value` FROM ".$this->table()." WHERE g_id = ".$gid;
		return $this->db()->getAll($sql);
	}

	function delGoodsAttr($gid)
	{
		$sql = "DELETE FROM ".$this->table()." WHERE g_id = ".$gid;
		return $this->db()->query($sql);
	}
}

?>