<?php
/**
 *  -------------------------------------------------
 *   @file		: AppPayShouldModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-27 10:41:29
 *   @update	:
 *  -------------------------------------------------
 */
class AppPayShouldModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_pay_should';
		$this->pk='pay_number_id';
		$this->_prefix='';
        $this->_dataObject = array("pay_number_id"=>"财务应付ID",
"pay_type"=>"应付类型",
"prc_id"=>"供货商ID",
"prc_name"=>"供货商",
"settle_mode"=>"结算方式",
"company"=>"所属公司",
"make_time"=>"制单时间",
"make_name"=>"制单人",
"check_time"=>"审核时间",
"check_name"=>"审核人",
"status"=>"单据状态(1、待审核；2、已审核；3、已取消",
"pay_status"=>"付款状态：1、未付款；2、部分付款；3、已付款)",
"total_cope"=>"应付金额",
"total_real"=>"财务实付金额",
"pay_should_all_name"=>"财务应付单号");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppPayShouldController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT `pay_number_id`,`pay_type`,`prc_id`,`prc_name`,`settle_mode`,`company`,`make_time`,`make_name`,`check_time`,`check_name`,`status`,`pay_status`,`total_cope`,`total_real`,`pay_should_all_name` FROM `".$this->table()."`";
		$sql.= ' WHERE 1';
		if($where['company']!='')
		{
			$sql .=" AND `company`=".$where['company'];
		}
		if($where['status']!='')
		{
			$sql .=" AND `status`=".$where['status'];
		}
		if($where['pay_status']!='')
		{
			$sql .=" AND `pay_status`=".$where['pay_status'];
		}
		if($where['prc_id']!='')
		{
			$sql .=" AND `prc_id`=".$where['prc_id'];
		}
		if($where['pay_type']!='')
		{
			$sql .=" AND `pay_type`=".$where['pay_type'];
		}
		if($where['pay_should_all_name']!='')
		{
			$sql .=" AND `pay_should_all_name`='".$where['pay_should_all_name']."'";
		}
		if($where['make_time_s']!='')
		{
			$sql .=" AND `make_time`>='".$where['make_time_s']."'";
		}
		if($where['make_time_e']!='')
		{
			$sql .=" AND `make_time`<='".$where['make_time_e']."'";
		}
		if($where['check_time_s']!='')
		{
			$sql .=" AND `check_time`>='".$where['check_time_s']."'";
		}
		if($where['check_time_e']!='')
		{
			$sql .=" AND `check_time`<='".$where['check_time_e']."'";
		}
		$sql .= " ORDER BY `pay_number_id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	public function getShouldDetail($Id)
	{
		$sql = "select `ps`.`pay_number`,`ps`.`pay_apply_number`,`ps`.`total_cope`,`pa`.`record_type` from `app_pay_should_detail` as `ps`,`app_pay_apply` as `pa` where `ps`.`pay_apply_number` = `pa`.`pay_apply_number` and `ps`.`pay_number` = $Id";
		return $this->db()->getAll($sql,array(),false);
	}

	/**
	 *	getShouldDetailpage，分页列表 
	 *
	 *	@url AppPayShouldController/getShouldDetailpage
	 */
	function getShouldDetailpage ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "select `ps`.`pay_number`,`ps`.`pay_apply_number`,`ps`.`total_cope`,`pa`.`record_type`,`pa`.`apply_id` from `app_pay_should_detail` as `ps`,`app_pay_apply` as `pa` where `ps`.`pay_apply_number` = `pa`.`pay_apply_number` and `ps`.`pay_number` = ".$where['pay_number'];
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	public function getShouldInfo($id)
	{
		$sql = "select *,a.total_cope as t_cope,b.total_cope as b_price from `".$this->table()."` as a,`app_pay_should_detail` as b ";
		$sql .= " where a.pay_number_id = b.pay_number and a.pay_number_id = ".$id;
		return $this->db()->getAll($sql,array(),false);
	}
}

?>