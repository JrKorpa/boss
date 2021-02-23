<?php
/**
 *  -------------------------------------------------
 *   @file		: RegionModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-19 00:32:52
 *   @update	:
 *  -------------------------------------------------
 */
class RegionModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'region';
		$this->_prefix ='region';
        $this->_dataObject = array("region_id"=>"主键",
"parent_id"=>"父级地区",
"region_name"=>"地区名称",
"region_type"=>"属于第几层从0层开始");
		parent::__construct($id,$strConn);
	}

	public function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT r.*,region.region_name as parent_name FROM (SELECT * FROM ".$this->table()." WHERE  1 ";

		if($where['region_name'] != "")
		{
			$sql .= " AND region_name like \"%".addslashes($where['region_name'])."%\"";
		}

		if($where['region_type'] != "")
		{
			$sql .= " AND region_type = ".addslashes($where['region_type']);
		}

		$sql .= ") as r LEFT JOIN region ON r.parent_id=region.region_id ORDER BY r.region_id ASC";
		//echo $sql;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	public function getRegion($region_id){
		$sql = "SELECT * FROM `".$this->table()."` WHERE `parent_id`='{$region_id}'";
		return $this->db()->getAll($sql);
	}

	public function getRegionType($region_type){
		$sql = "SELECT * FROM `".$this->table()."` WHERE `region_type`='{$region_type}'";
		return $this->db()->getAll($sql);
	}
	public function getReginName($reginon_id){
		   $sql = "SELECT region_name FROM `".$this->table()."` WHERE `region_id`={$reginon_id}";
		  return $this->db()->getOne($sql);
	}

	public function getRegionList($region_ids){
		   $sql = "SELECT `region_id`,`region_name` FROM `".$this->table()."` WHERE `region_id` in (".$region_ids.")";
		  return $this->db()->getAll($sql);
	}

	/**
	 * 获取省市区地址：中文
	 * @param $str
	 * @return string
	 */
	public function getAddreszhCN($str){
		$arr = explode(',',$str);
		$addres ='';
		foreach ($arr as $v) {
			$addres .= $this->getRegionName($v);
		}
		return $addres;
	}

	public function getRegionName($id){
		$sql = 'SELECT `region_name` FROM '.$this->table().' WHERE `region_id` = '.$id;
		return $this->db()->getOne($sql);
	}


    public function GetReginIdByName($in){
        if($in==''){
            return false;
        }
        $sql = "SELECT region_id,region_type FROM ".$this->table()." WHERE  region_name in($in)";
        return array_column($this->db()->getAll($sql),'region_id','region_type');
    }

  public function getRegionByName($name,$type){
        $sql = "SELECT `region_id` FROM ".$this->table()." WHERE `region_name` like '".$name."%' and `region_type`=$type";
        return $this->db()->getOne($sql); 
  }

}

?>