<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductOpraLogModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-08 11:58:03
 *   @update	:
 *  -------------------------------------------------
 */
class ClothProductionTrackingLogModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'product_opra_log';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"bc_id"=>"布产号ID",
"status"=>"当前状态",
"remark"=>"备注",
"uid"=>"操作人ID",
"uname"=>"操作人姓名",
"time"=>"操作时间");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url ProductOpraLogController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";

		$str = '';
		if(!empty($where['_id']))
		{
			$str .="`bc_id`='".$where['_id']."' AND ";	
		}
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