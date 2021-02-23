<?php
/**
 *  -------------------------------------------------
 *   @file		: PayYfRealModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-08 17:47:22
 *   @update	:
 *  -------------------------------------------------
 */
class PayYfRealModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'pay_yf_real';
        $this->_dataObject = array();
		parent::__construct($id,$strConn);
	}


	public function getRealListByWhere($where = array(),$page,$pageSize,$useCache=true)
	{
		$sql = "select * from `".$this->table()."`";
		$sql .= " where 1 ";
		if(!empty($where['company'])){
			$sql .= " AND company = ".$where['company'];
		}
		if(!empty($where['prc_id'])){
			$sql .= " AND prc_id = ".$where['prc_id'];
		}
		if(!empty($where['pay_type'])){
			$sql .= " AND pay_type = ".$where['pay_type'];
		}
		if(!empty($where['pay_real_number'])){
			$sql .= " AND pay_real_all_name = '".$where['pay_real_number']."'";
		}
		if(!empty($where['pay_number'])){
			$sql .= " AND pay_number = '".$where['pay_number']."' ";
		}
		if(!empty($where['make_name'])){
			$sql .= " AND make_name = '".$where['make_name']."' ";
		}
		if(!empty($where['pay_time_s'])){
			$sql .= " AND pay_time >= '".$where['pay_time_s']." 00:00:00' ";
		}
		if(!empty($where['pay_time_e'])){
			$sql .= " AND pay_time <= '".$where['pay_time_e']." 23:59:59' ";
		}
        if(isset($where['hidden']) && $where['hidden'] != ''){
            $sql .= " and hidden = ".$where['hidden'];
        }
		$sql .= " order by `pay_real_number` desc";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	public function getRealallprice($where = array())
	{
		$sql = "select SUM(total) as all_price from `".$this->table()."`";
		$sql .= " where 1 ";
		if(!empty($where['company'])){
			$sql .= " AND company = ".$where['company'];
		}
		if(!empty($where['prc_id'])){
			$sql .= " AND prc_id = ".$where['prc_id'];
		}
		if(!empty($where['pay_type'])){
			$sql .= " AND pay_type = ".$where['pay_type'];
		}
		if(!empty($where['pay_real_number'])){
			$sql .= " AND pay_real_all_name = '".$where['pay_real_number']."'";
		}
		if(!empty($where['pay_number'])){
			$sql .= " AND pay_number = '".$where['pay_number']."' ";
		}
		if(!empty($where['make_name'])){
			$sql .= " AND make_name = '".$where['make_name']."' ";
		}
		if(!empty($where['pay_time_s'])){
			$sql .= " AND pay_time >= '".$where['pay_time_s']." 00:00:00' ";
		}
		if(!empty($where['pay_time_e'])){
			$sql .= " AND pay_time <= '".$where['pay_time_e']." 23:59:59' ";
		}
		return $this->db()->getOne($sql,array(),false);
	}

	public function getRealinfoByWhere($where = array())
	{
		$sql = "select * from `".$this->table()."`";
		$sql .= " where 1 ";
		if(!empty($where['company'])){
			$sql .= " AND company = ".$where['company'];
		}
		if(!empty($where['prc_id'])){
			$sql .= " AND prc_id = ".$where['prc_id'];
		}
		if(!empty($where['pay_type'])){
			$sql .= " AND pay_type = ".$where['pay_type'];
		}
		if(!empty($where['pay_real_number'])){
			$sql .= " AND pay_real_all_name = '".$where['pay_real_number']."'";
		}
		if(!empty($where['pay_number'])){
			$sql .= " AND pay_number = '".$where['pay_number']."' ";
		}
		if(!empty($where['make_name'])){
			$sql .= " AND make_name = '".$where['make_name']."' ";
		}
		if(!empty($where['pay_time_s'])){
			$sql .= " AND pay_time >= '".$where['pay_time_s']." 00:00:00' ";
		}
		if(!empty($where['pay_time_e'])){
			$sql .= " AND pay_time <= '".$where['pay_time_e']." 23:59:59' ";
		}
		$sql .= " order by `pay_real_number` desc";
		return $this->db()->getAll($sql,array(),false);
	}

	public function getJiezhangList()
	{
		$sql = "select year from app_jiezhang group by year order by year desc ";
		return $this->db()->getAll($sql,array(),false);
	}
	public function getJiezhangLists()
	{
		$sql = "select year from app_jiezhang  order by year desc";
		return $this->db()->getAll($sql,array(),false);
	}
	public function getJiezhangInfoList($data)
	{
		$sql = "select qihao from app_jiezhang where start_time!='0000-00-00' and end_time!='0000-00-00' and year='".$data."' order by id asc";
		return $this->db()->getAll($sql,array(),false);
	}
	public function getJiezhangCheck($data)
	{
		$sql = "select qihao from app_jiezhang where start_time!='0000-00-00' and end_time!='0000-00-00' and year='".$data."' order by id asc";
		return $this->db()->getAll($sql,array(),false);
	}

	public function getJiezhangtimes($where = array())
	{
		$sql = "select start_time from app_jiezhang";
		$sql .= " where 1 ";
		if(!empty($where['start_year'])){
			$sql .= " AND year = ".$where['start_year'] ;
		}
		if(!empty($where['start_qihao'])){
			$sql .= " AND qihao = '".$where['start_qihao']." ' ";
		}
		return $this->db()->getOne($sql,array(),false);
	}
	public function getJiezhangtimee($where = array())
	{
		$sql = "select end_time from app_jiezhang";
		$sql .= " where 1 ";
		if(!empty($where['end_year'])){
			$sql .= " AND year = ".$where['end_year'] ;
		}
		if(!empty($where['end_qihao'])){
			$sql .= " AND qihao = '".$where['end_qihao']." ' ";
		}
		return $this->db()->getOne($sql,array(),false);
	}
	function add($data)
	{
		return $this->saveData($data,array(),false);
	}
	public function updatePayrealallname($id,$data)
	{
		$sql = "UPDATE `".$this->table()."` SET `pay_real_all_name` = '$data' WHERE pay_real_number = '$id'  LIMIT 1 ";
		return $this->db()->query($sql,array(),false);
	}
}

?>