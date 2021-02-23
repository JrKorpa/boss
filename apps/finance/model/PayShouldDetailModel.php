<?php
/**
 *  -------------------------------------------------
 *   @file		: PayShouldDetailModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-08-07 12:14:53
 *   @update	:
 *  -------------------------------------------------
 */
class PayShouldDetailModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'pay_should_detail';
        $this->_dataObject = array();
        $this->_prefix= "pay_should_detail";
		parent::__construct($id,$strConn);
	}

	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";

		if($where['pay_number'] !== "")
		{
			$sql .= " AND pay_number = ".$where['pay_number'];
		}
		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
}

?>