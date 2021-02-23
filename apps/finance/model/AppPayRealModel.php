<?php
/**
 *  -------------------------------------------------
 *   @file		: AppPayRealModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-27 12:17:56
 *   @update	:
 *  -------------------------------------------------
 */
class AppPayRealModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_pay_real';
		$this->pk='pay_real_number';
		$this->_prefix='';
        $this->_dataObject = array("pay_real_number"=>"财务实付单号",
"pay_real_all_name"=>"全名",
"pay_number"=>"财务应付单号",
"pay_type"=>"实付类型;1为代销借货；2为成品采购；3石包采购；",
"prc_id"=>"供货商ID",
"prc_name"=>"供货商",
"company"=>"所属公司",
"bank_name"=>"银行名称",
"bank_serial_number"=>"银行交易流水",
"bank_account"=>"收款方帐号",
"pay_time"=>"财务付款时间",
"total"=>"实付金额",
"make_time"=>"操作时间",
"make_name"=>"制单人");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppPayRealController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$sql .= ' WHERE 1 ';
		if(!empty($where['company'])){
			$sql .= " AND `company` = ".$where['company'];
		}
		if(!empty($where['prc_id'])){
			$sql .= " AND `prc_id` = ".$where['prc_id'];
		}
		if(!empty($where['pay_type'])){
			$sql .= " AND `pay_type` = ".$where['pay_type']; 
		}
		if(!empty($where['pay_real_number'])){
			$sql .= " AND `pay_real_number` = '".$where['pay_real_number']."'";
		}
		if(!empty($where['pay_number'])){
			$sql .= " AND `pay_number` = '".$where['pay_number']."'";
		}
		if(!empty($where['make_name'])){
			$sql .= " AND `make_name` = '".$where['make_name']."'";
		}
		if(!empty($where['pay_time_s'])){ 
			$sql .= " AND `pay_time` >= '".$where['pay_time_s']."'";
		}
		if(!empty($where['pay_time_e'])){
			$sql .= " AND `pay_time` <= '".$where['pay_time_e']."'";
		}
		$sql .= " ORDER BY `pay_real_number` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	
	//  结算商/供货商
	public function getJiesuanshangList()
	{
		$sql = "select p_id,p_name from `jxc_processors` ";
		$sql .= " where pt_id >0 ";
		$sql .=" and p_id not in(257,282,283,284,285,286,287,297,298,301,308,310,319,321,322,323,324,340,369,382,386,388 )";
		$sql .= " order by `p_id` asc";
		return $this->db()->getAll($sql);
	}
	
	//账期
	public function getJiezhangList()
	{
		$sql = "select year from `app_jiezhang` group by year order by year desc ";
		return $this->db()->getAll($sql);
	}

	public function getJiezhangtimes($where = array())
	{
		$sql = "select start_time from `app_jiezhang`";
		$sql .= " where 1 ";
		if(!empty($where['start_year'])){
			$sql .= " AND year = ".$where['start_year'] ;
		}
		if(!empty($where['start_qihao'])){
			$sql .= " AND qihao = '".$where['start_qihao']." ' ";
		}
		return $this->db()->getOne($sql,array());
	}

	public function getJiezhangtimee($where = array())
	{
		$sql = "select end_time from `app_jiezhang`";
		$sql .= " where 1 ";
		if(!empty($where['end_year'])){
			$sql .= " AND year = ".$where['end_year'] ;
		}
		if(!empty($where['end_qihao'])){
			$sql .= " AND qihao = '".$where['end_qihao']." ' ";
		}
		return $this->db()->getOne($sql);
	}

	public function getJiezhangLists()
	{
		$sql = "select year from `app_jiezhang`  order by year desc";
		return $this->db()->getAll($sql);
	}

	public function getJiezhangInfoList($data)
	{
		$sql = "select qihao from `app_jiezhang` where start_time!='0000-00-00' and end_time!='0000-00-00' and year='".$data."' order by id asc";
		return $this->db()->getAll($sql,array(),false);
	}
}

?>