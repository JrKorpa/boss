<?php
/**
 *  -------------------------------------------------
 *   @file		:AppUserBespokeLogModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-07 19:31:15
 *   @update	:
 *  -------------------------------------------------
 */
class AppUserBespokeLogModel extends Model
{
    public $_prefix = 'log';
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_user_bespoke_log';
		$this->_prefix = 'log';
        $this->_dataObject = array("log_id"=>" ",
"bespoke_id"=>"预约ID",
"mem_id"=>"用户ID",
"create_user"=>"操作人",
"create_time"=>"操作时间",
"IP"=>"操作IP",
"remark"=>"备注信息");
		parent::__construct($id,$strConn);
	}
	/**
	 *	pageList，分页列表
	 *
	 *	@url MessageController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";
        if(isset($where['bespoke_id'])&&!empty($where['bespoke_id'])){
            $sql.=" AND bespoke_id like '".addslashes($where['bespoke_id'])."%'";
        }
        if(isset($where['mem_id'])&&!empty($where['mem_id'])){
            $sql.=" AND mem_id =".$where['mem_id'];
        }
		$sql .= " ORDER BY log_id DESC";
		$data = $this->db(17)->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

    public function getMemByMobile ($mobile)
    {
        $sql="SELECT * FROM `".$this->table()."` WHERE member_mobile='{$mobile}' ";
		$data = $this->db(17)->getRow($sql);
		return $data;
    }
}
?>