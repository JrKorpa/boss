<?php
/**
 *  -------------------------------------------------
 *   @file		: AppReceiptPayLogModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-21 22:51:50
 *   @update	:
 *  -------------------------------------------------
 */
class AppReceiptPayLogModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_receipt_pay_log';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"receipt_id"=>"收据id",
"receipt_action"=>"操作",
"add_time"=>"添加时间",
"add_user"=>" ");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppReceiptPayLogController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";
		if(isset($where['receipt_id']) && $where['receipt_id'] > 0)
		{
			$sql .=" AND receipt_id=".$where['receipt_id'];	
		}
		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
}

?>