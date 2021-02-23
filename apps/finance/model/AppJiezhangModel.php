<?php
/**
 *  -------------------------------------------------
 *   @file		: AppJiezhangModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-21 19:04:39
 *   @update	:
 *  -------------------------------------------------
 */
class AppJiezhangModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_jiezhang';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"qihao"=>"期号（1-12）",
"start_time"=>"开始日期",
"end_time"=>"结束日期",
"year"=>"会计年度");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppJiezhangController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";
		if(!empty($where['start_time']))
		{
			$sql .=" AND `start_time`>='".$where['start_time']."'";
		}
		if(!empty($where['end_time']))
		{
			$sql .=" AND `end_time`<='".$where['end_time']."'";
		}
		if(!empty($where['year']))
		{
			$sql .=" AND `year`='".$where['year']."'";
		}
		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}


    /**
     * 添加一条期号记录，验证该期号是否存在
     * @param type $qihao
     * @return boolean
     */
    function existQihao($qihao,$year) {
        $sql = "SELECT COUNT(1) FROM `".$this->table()."` WHERE `qihao`=$qihao AND `year`='$year'";
        return $this->db()->getOne($sql);
    }

	function getYear()
	{
		$sql = "select DISTINCT `year` from ".$this->table()." order by year DESC";
		return $this->db()->getAll($sql);
	}

	//获取下一个需要添加的年和期号以及时间
	function getNext()
	{
		$sql = "select year,qihao,end_time from ".$this->table()." order by id DESC limit 0,1";
		$row = $this->db()->getRow($sql);
		if($row['qihao'] == 12)//如果期号已经到12月，年份加1，期号到1月
		{
			$row['qihao'] = 1;
			$row['year'] += 1;
		}else{ //如果期号不是12，则只给期号加1，年份不变
			$row['qihao'] += 1;
		}
		$row['start_time'] = date("Y-m-d",strtotime("+1 day",strtotime($row['end_time'])));
		return $row;
	}

	function getQihao($year)
	{
		$sql = "select qihao from app_jiezhang where start_time!='0000-00-00' and end_time!='0000-00-00' and year='".$year."' order by id asc";
		return $this->db()->getAll($sql,array(),false);
	}

	//取最后一条数据
	function getLast()
	{
		$sql = "SELECT id,year,qihao,start_time,end_time from ".$this->table()." order by id DESC limit 0,1";
		return $this->db()->getRow($sql);
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

}

?>