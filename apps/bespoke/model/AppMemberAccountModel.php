<?php
/**
 *  -------------------------------------------------
 *   @file		: AppMemberAccountModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 12:23:01
 *   @update	:
 *  -------------------------------------------------
 */
class AppMemberAccountModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_member_account';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"序号",
"memeber_id"=>"会员ID",
"current_money"=>"当前余额",
"total_money"=>"总消费金额",
"total_point"=>"总积分",
"is_deleted"=>"删除标识");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppMemberAccountController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 AND is_deleted = 0";
		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
        function getDataInfoByMemeberId($memeber_id) {
                $sql = "select count(1) from `".$this->table()."` where 1 and memeber_id='".$memeber_id."'";
                $res = $this->db()->getOne($sql);
                return $res;
        }
}

?>