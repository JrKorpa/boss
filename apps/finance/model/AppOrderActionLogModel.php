<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderActionModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-31 12:17:57
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderActionLogModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_order_action';
		$this->pk='action_id';
		$this->_prefix='';
        $this->_dataObject = array("action_id"=>"自增ID",
"order_id"=>"订单id",
"order_status"=>"订单审核状态1无效2已审核3取消4关闭",
"shipping_status"=>"发货状态",
"pay_status"=>"支付状态:1未付款2部分付款3已付款",
"create_user"=>"操作人",
"create_time"=>"操作时间",
"remark"=>"操作备注");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppOrderActionController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
        
        $sql .=" WHERE 1";
		if(!empty($where['_id']))
		{
			$sql .=" AND `order_id`=".$where['_id'];	
		}
//		if($where['xxx'] != "")
//		{
//			$str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
//		}
//		if(!empty($where['xx']))
//		{
//			$str .= "`xx`='".$where['xx']."' AND ";
//		}

		$sql .= " ORDER BY `action_id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
}

?>