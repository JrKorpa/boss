<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderComplaintModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2016-01-27 10:14:05
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderComplaintModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_order_complaint';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"ID",
"order_id"=>"订单ID",
"cl_feedback_id"=>"客诉选项",
"cl_other"=>"客诉备注",
"cl_user"=>"添加人",
"cl_time"=>"添加时间",
"cl_url"=>"图片地址");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppOrderComplaintController/search
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

    /**
     *  getComplaintInfobyOrder_id，根据订单ID获取客诉信息
     *
     *  @url AppOrderComplaintController/add_complaint
     */
    public function getComplaintInfobyOrder_id($order_id)
    {
        # code...
        $sql = "select * from `".$this->table()."` where `order_id` = {$order_id}";
        $data = $this->db()->getAll($sql);
        return $data;
    }
}

?>