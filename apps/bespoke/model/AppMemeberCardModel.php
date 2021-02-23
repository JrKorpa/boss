<?php
/**
 *  -------------------------------------------------
 *   @file		: AppMemeberCardModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-08 13:58:21
 *   @update	:
 *  -------------------------------------------------
 */
class AppMemeberCardModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_memeber_card';
        $this->_dataObject = array("id"=>"序号",
"mem_card_sn"=>"会员卡号",
"mem_card_level"=>"会员卡等级",
"mem_card_uptime"=>"会员卡升级时间",
"men_card_type"=>"会员卡类型",
"mem_card_status"=>"1/有效,0/无效",
"is_deleted"=>"删除标识",
"addby_id"=>"创建人",
"add_time"=>"创建时间");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url MessageController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 and is_deleted = 0";
		if($where['mem_card_sn'] != "")
		{
			$sql .= " AND mem_card_sn like \"%".addslashes($where['mem_card_sn'])."%\"";
		}
		if($where['mem_card_type'] != "")
		{
			$sql .= " AND mem_card_type like \"%".addslashes($where['mem_card_type'])."%\"";
		}
		if($where['mem_card_status'] != "")
		{
			$sql .= " AND mem_card_status like \"%".addslashes($where['mem_card_status'])."%\"";
		}

		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
        
        function hasCard($card_id){
            $sql = "SELECT COUNT(*) FROM `".$this->table()."` WHERE `mem_card_sn`='{$card_id}' ";
		$res = $this->db()->getOne($sql);
		if($res)
		{
			return true;	
		}
		return false;
        }


}

?>