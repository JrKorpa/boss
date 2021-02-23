<?php
/**
 *  -------------------------------------------------
 *   @file		: AppProcessorFeeModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 21:23:45
 *   @update	:
 *  -------------------------------------------------
 */
class AppProcessorFeeModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_processor_fee';
        $this->_dataObject = array("id"=>" ",
"processor_id"=>"供应商id",
"fee_type"=>"费用类型：1材质费，2超石头费，3表面工艺",
"price"=>"费用",
"status"=>"状态：1启用2停用",
"check_user"=>"创建人",
"check_time"=>"创建时间",
"cancel_time"=>"停用时间"                );
		parent::__construct($id,$strConn);
	}

        /**
	 *	pageList，分页列表
	 *
	 *	@url ControlController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";

		if(isset($where['processor_id']) && $where['processor_id'] > 0)
		{
			$sql .= " AND processor_id  = {$where['processor_id']}";
		}
		
		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
}

?>