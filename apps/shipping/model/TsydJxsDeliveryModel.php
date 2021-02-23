<?php
/**
 *  -------------------------------------------------
 *   @file		: TsydJxsDeliveryModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2016-01-28 17:56:56
 *   @update	:
 *  -------------------------------------------------
 */
class TsydJxsDeliveryModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'tsyd_jxs_delivery';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"bill_no"=>" ",
"bill_type"=>" ",
"bill_status"=>" ");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url TsydJxsDeliveryController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
//		if($where['xxx'] != "")
//		{
//			$str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
//		}
//		if(!empty($where['xx']))
//		{
//			$str .= "`xx`='".$where['xx']."' AND ";
//		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
}

?>