<?php
/**
 *  -------------------------------------------------
 *   @file		: AppApplyPayLogModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-05 16:56:14
 *   @update	:
 *  -------------------------------------------------
 */
class AppApplyPayLogModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_apply_pay_log';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"序号",
"order_type"=>"单据类型",
"order_id"=>"单据ID",
"order_no"=>"单据号码",
"handle_type"=>"1=调整,2审批,3=付款",
"content"=>"操作内容",
"create_id"=>"操作人ID",
"create_name"=>"操作人",
"create_time"=>"操作时间");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppApplyPayLogController/search
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
		if(!empty($where['order_no'])) {$str .= "`order_no`='".$where['order_no']."' AND ";}
		if(!empty($where['create_name'])) {$str .= "`create_name`='".$where['create_name']."' AND ";}
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