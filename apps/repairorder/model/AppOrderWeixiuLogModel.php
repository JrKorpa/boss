<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderWeixiuLogModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-28 19:43:30
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderWeixiuLogModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_order_weixiu_log';
        $this->_dataObject = array("id"=>"id",
"do_id"=>"操作id",
"date_time"=>"操作时间",
"user_name"=>"操作人",
"do_type"=>"操作类型",
"content"=>"操作内容");
		parent::__construct($id,$strConn);
	}

	/**
		pageList ,分页
	**/
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{

		$sql  = "SELECT date_time,user_name,content FROM ".$this->table();
		$sql .= ' where do_id =  '.$where['do_id'];
		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
}

?>