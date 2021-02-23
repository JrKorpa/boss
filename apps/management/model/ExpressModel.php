<?php
/**
 *  -------------------------------------------------
 *   @file		: ExpressModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-07 17:16:48
 *   @update	:
 *  -------------------------------------------------
 */
class ExpressModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'express';
        $this->_dataObject = array("id"=>"序号",
"exp_name"=>"快递名称",
"exp_code"=>"快递编码",
"exp_areas"=>"配送区域",
"exp_tel"=>"联系电话",
"exp_note"=>"备注说明",
"freight_rule"=>"快递单规则",
"is_deleted"=>"删除标识",
"addby_id"=>"创建人",
"add_time"=>"创建时间");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url PaymentController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 and is_deleted = 0";
		if($where['exp_name'] != "")
		{
			$sql .= " AND exp_name like \"%".addslashes($where['exp_name'])."%\"";
		}
		if($where['exp_code'] != "")
		{
			$sql .= " AND exp_code like \"%".addslashes($where['exp_code'])."%\"";
		}
		if($where['exp_tel'] != "")
		{
			$sql .= " AND exp_tel like \"%".addslashes($where['exp_tel'])."%\"";
		}

		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	/**
	 * getAreas		获取地区信息
	 */
	public function getAreas(){
		$sql = 'select * from region';
		$data = $this->db()->getAll($sql);
		return $data;
	}
	
	/**
	* getAllExpress 获取所有快递信息
	*/
	public function getAllExpress()
	{
		$sql =  "SELECT id,exp_name FROM `".$this->table()."` where is_deleted = 0";
		return $this->db()->getAll($sql);
	}

	/**
	*getNameById
	**/
	public function getNameById($id)
	{
		$sql = "select `exp_name` from `".$this->table()."` where `id` = {$id}";
		return $this->db()->getOne($sql);
	}
	
	// 通用表查询
	public function select2($field,$where,$type=1) {
	    $sql = "SELECT {$field} FROM ".$this->table()." where {$where}";
	    $sql .= " ORDER BY `id` DESC";
	    if($type==1){
	        return $this->db()->getAll($sql);
	    }elseif($type==2){
	        return $this->db()->getRow($sql);
	    }elseif($type==3){
	        return $this->db()->getOne($sql);
	    }
	}

    /**
     * getRegionAreas     获取省级地区信息
     */
    public function getRegionAreas(){
        $sql = 'select `region_id`,`region_name` from region where `parent_id` = 1';
        $data = $this->db()->getAll($sql);
        return $data;
    }

    /**
     * getRegionAreasNamebyid     获取地区名称
     */
    public function getRegionAreasNamebyid($ids)
    {
        $sql = "select `region_name` from region where `parent_id` = 1 and `region_id` in($ids)";
        $data = $this->db()->getAll($sql);
        return $data;
    }
}

?>