<?php
/**
 *  -------------------------------------------------
 *   @file		: SalesChannelsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 15:25:16
 *   @update	:
 *  -------------------------------------------------
 */
class SalesChannelsModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'sales_channels';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"序号",
"channel_name"=>"渠道名称",
"channel_code"=>"渠道编码",
"channel_class"=>"1线上，2线下",
"channel_type"=>"1部门，2体验店，3公司",
"channel_own_id"=>"所属ID",
"channel_own"=>"渠道归属",
"addby_id"=>"创建人",
"addby_time"=>"创建时间",
"updateby_id"=>"更新人",
"update_time"=>"修改时间",
"channel_man"=>"渠道联系人",
"channel_email"=>"联系人邮箱",
"channel_phone"=>"联系人手机",
"is_deleted"=>"删除标识");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url SalesChannelsController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE is_deleted = ".$where['is_deleted'];
		if($where['channel_name'] != "")
		{
			$sql .= " AND channel_name like \"%".addslashes($where['channel_name'])."%\"";
		}
		if($where['channel_code'] != "")
		{
			$sql .= " AND channel_code like \"%".addslashes($where['channel_code'])."%\"";
		}
		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	/******
	取部门，体验店，公司渠道
	*******/
	public function getChannelByChannel_Name($channel_name_arr=array())
	{
		$channel_names='';
		foreach($channel_name_arr as $val){
			$channel_names.="'{$val}',";
		}
		$channel_names=trim($channel_names,',');
		$sql = "select GROUP_CONCAT(id)  from `".$this->table()."` where  `is_deleted`=0 and `channel_name` in (".$channel_names.")";
		return $this->db()->getOne($sql);
	}

	/******
	取部门，体验店，公司渠道
	*******/
	public function getChannelById($id)
	{
		$sql = "select * from `".$this->table()."` where `channel_type`=2 and `is_deleted`=0 and `id` not in($id)";
		return $this->db()->getAll($sql);
	}

	/******
	getNameByid
	*******/
	public function getNameByid($id)
	{
		$sql = "select channel_name from ".$this->table()." where id ={$id}";
		return $this->db()->getOne($sql);
	}
	public function getShopCid(){
		$sql = "select id,dp_leader_name,dp_people_name from sales_channels_person";
		return $this->db()->getAll($sql);
	}
    // 取渠道用户
    public function getShopPersonById($channel_id){
        $sql = "select * from sales_channels_person where id={$channel_id}";
        return $this->db()->getRow($sql);
    }
	/*
	 * 取渠道 channel_type
	*/
	public function getSalesChannelsInfo($select="*",$where){
		$sql = "SELECT $select FROM `sales_channels` WHERE 1";
		if(!empty($where['id'])){
			$sql.=" AND `id`=".$where['id'];
		}
		if(!empty($where['channel_class'])){
			$sql.=" AND `channel_class`=".$where['channel_class'];
		}
		if(isset($where['is_deleted'])){
			$sql.=" AND `is_deleted`=".$where['is_deleted'];
		}
		if(!empty($where['channel_type'])){
			$sql.=" AND `channel_type`=".$where['channel_type'];
		}
		return $this->db()->getAll($sql);
	}
}


?>