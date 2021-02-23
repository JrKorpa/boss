<?php
/**
 *  -------------------------------------------------
 *   @file		: AppDiamondPriceModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 11:19:47
 *   @update	:
 *  -------------------------------------------------
 */
class AppDiamondPriceModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_diamond_price';
        $this->_dataObject = array("id"=>" ",
"guige_a"=>"主石规格起始",
"guige_b"=>"主石规格结束",
"price"=>"价格");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppDiamondPriceController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";
        if(isset($where['guige_a']) && !empty($where['guige_a'])){
            $sql .=" AND guige_a = '{$where['guige_a']}' ";
        }
        if(isset($where['guige_b']) && !empty($where['guige_b'])){
            $sql .=" AND guige_b = '{$where['guige_b']}' ";
        }
        if(isset($where['price']) && !empty($where['price'])){
            $sql .=" AND price = '{$where['price']}' ";
        }
		if(isset($where['guige_status']) && $where['guige_status']!= ""){
            $sql .=" AND guige_status = '{$where['guige_status']}' ";
        }
		$sql .= " ORDER BY id DESC";
		$data = $this->db(11)->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	/**
	 *	获取所有的
	 *	@url AppDiamondPriceController/search
	 */
	function getAllList ()
	{
		$sql = "SELECT `id`,`guige_a`,`guige_b`,`price` FROM `".$this->table()."` WHERE 1 ";
		return $this->db()->getAll($sql);
	}
	
	/**
	 *	根据石头重量获取钻石规格单价
	 *	@url AppDiamondPriceController/search
	 */
	function getDanPrice ($where)
	{
		$sql = "SELECT `price` FROM `".$this->table()."` WHERE 1 ";
		if(isset($where['guige']) && !empty($where['guige'])){
			$sql .=" AND guige_a < {$where['guige']} AND guige_b >= {$where['guige']} ";
		}
		return $this->db()->getRow($sql);
	}
}

?>