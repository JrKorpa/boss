<?php
/**
 *  -------------------------------------------------
 *   @file		: AppApplyListApplyModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-21 19:03:33
 *   @update	:
 *  -------------------------------------------------
 */
class AppApplyListApplyModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_pay_apply';
		$this->pk='apply_id';
		$this->_prefix='';
        $this->_dataObject = array("apply_id"=>"ID",
"pay_apply_number"=>"应付申请单号",
"status"=>"状态(0、新增；1、待审核；2、已驳回；3、已取消；4、待生成应付单；5、已生成应付单)",
"pay_number"=>"财务应付单单号",
"make_time"=>"制单时间",
"make_name"=>"制单人",
"check_time"=>"审核时间",
"check_name"=>"审核人",
"company"=>"所属公司",
"prc_id"=>"供货商ID",
"prc_name"=>"供货商名称",
"pay_type"=>"应付类型",
"amount"=>"总数量",
"total_cope"=>"应付金额",
"total_dev"=>"偏差金额",
"adj_reason"=>"调整原因（调整单的调整原因）",
"record_type"=>"单据类型(1、应付申请单；2、应付调整单)",
"overrule_reason"=>"调整单的驳回原因",
"fapiao"=>"发票");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppApplyListApplyController/search 
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";
		if(!empty($where['company']))
		{
			$sql .= " AND `company` = {$where['company']}";
		}
		if(!empty($where['status']))
		{
			$sql .= " AND `status` = {$where['status']}";
		}
		if(!empty($where['prc_id']))
		{
			$sql .= " AND `prc_id` = {$where['prc_id']}";
		}
		if(!empty($where['payType']))
		{
			$sql .= " AND `pay_type` = {$where['payType']}";
		}
		if(!empty($where['pay_number']))
		{
			$sql .= " AND `pay_number` = '{$where['pay_number']}'";
		}
		if(!empty($where['pay_apply_number']))
		{
			$sql .= " AND `pay_apply_number` = '{$where['pay_apply_number']}'";
		}
		if(!empty($where['start_make_date']))
		{
			$sql .= " AND `make_time` >= '{$where['start_make_date']} 00:00:00'";
		}
		if(!empty($where['end_make_date']))
		{
			$sql .= " AND `make_time` <= '{$where['end_make_date']} 23:59:59'";
		}
		if(!empty($where['start_check_date']))
		{
			$sql .= " AND `check_time` >= '{$where['start_check_date']} 00:00:00'";
		}
		if(!empty($where['end_check_date']))
		{
			$sql .= " AND `check_time` <= '{$where['end_check_date']} 23:59:59'";
		}
		$sql .= " ORDER BY apply_id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	function getNameList($id=NULL,$status=NULL)
	{
		$where = 'pt_id > 0 ';
		if(!empty($id)){
			$where .= " AND p_id = $id";
		}
		if(!empty($status)){
			$where .= " AND status = $status";
		}
		$sql = "SELECT p_id,p_name FROM `jxc_processors`";
		$sql .= " where $where";
		return $this->db()->getAll($sql,array(),false);
	}

/*------------------------------------------------------ */
	//-- 判断传入的ID的某一列的值是不是一样的。
	// true  是一样的。 false 不是一样的。
	//-- by Zlj
	/*------------------------------------------------------ */

	public function checkDistinct($col,$ids)
	{
		$sql = "select count(distinct $col) from ".$this->table()." where apply_id in($ids)";
		$count = $this->db()->getOne($sql);
		if($count == '1'){return true;}return false;
	}

	public function getRow($id,$col = "*")
	{
		$sql = "select $col from ".$this->table()." where apply_id = ".$id;
		return $this->db()->getRow($sql);
	}

	/*------------------------------------------------------ */
	//-- 计算传入的ID的金额总和。
	//-- 返回金额
	//-- by Zlj
	/*------------------------------------------------------ */

	public function getTotalOfIds($ids)
	{
		$sql = "select sum(total_cope) from ".$this->table()." where apply_id in($ids)";
		$total = $this->db()->getOne($sql);
		return $total;
	}

	public function add($ids)
	{
		//计算总金额
			$total = $this->getTotalOfIds($ids);
		
			//通过checkShouldCon方法已经确定所有单据都是同一个类型和供货商，只取ids_arr[0]的数据即可
			$ids_arr = explode(',',$ids);
			$applyarr = $this->getRow($ids_arr[0]);

            $sql = "select * from `jxc_processors` where `p_id` =".$applyarr['prc_id'];
            $pro = $this->db()->getRow($sql);
			$payment = $pro['payment'];
			$pay_day = $pro['pay_day'];
			$settle_mode = '';
			if($payment == '3')
			{
				$settle_mode = '货到付款';
			}elseif($payment == '1')
			{
				$settle_mode = '自然日结算 '.$pay_day.'天';
			}elseif($payment == '2')
			{
				$settle_mode = '月结 每月'.$pay_day.'日';
			}
			$data = array(
			'pay_type'		=> $applyarr['pay_type'],
			'prc_id'			=> $applyarr['prc_id'],
			'prc_name'		=> $applyarr['prc_name'],
			'settle_mode'=> $settle_mode,
			'company'		=> '58', 
			'check_name'		=> '222', 
			'make_time'	=> date('Y-m-d H:i:s',time()),
			'make_name'	=> $_SESSION['userName'],
			'check_time'	=> '0000-00-00 00:00:00',
			'total_cope'	=> $total
			);
            $newmodel =  new AppPayShouldModel(30);
            $resul = $newmodel->saveData($data,$olddo=array());
            $result['pay_number_id']=$resul;
            $sql = "select * from `app_pay_should` where `pay_number_id` =".$result['pay_number_id'];
            $resu = $this->db()->getRow($sql);	
            $result['pay_should_all_name']=$resu['pay_should_all_name'];
			$pay_number_id = $result['pay_number_id'];
			$pay_should_all_name = $result['pay_should_all_name'].$pay_number_id;
			$sql = " update `app_pay_should` set pay_should_all_name = '".$pay_should_all_name."'   where pay_number_id = ".$pay_number_id;
			$this->db()->query($sql,array());
			
			//pay_should_detail表的数据
			$detailData = array();
			foreach($ids_arr as $k => $v)
			{
				$ar = $this->getRow($v,'pay_apply_number,total_cope');
				$dataArr['pay_number']				= $pay_number_id;
				$dataArr['pay_apply_number']	= $ar['pay_apply_number'];
				$dataArr['total_cope']					= $ar['total_cope'];
				$detailData[] = $dataArr;
				//修改申请单对应的应付单单号
				$this->update(array('pay_number'=>$pay_should_all_name,'status'=>'5'),array('apply_id'=>$v));
			}
            foreach($detailData as $k=>$v){
                $r=$this->db()->query("INSERT INTO `app_pay_should_detail`(`pay_number`, `pay_apply_number`, `total_cope`) VALUES ('{$v['pay_number']}','{$v['pay_apply_number']}','{$v['total_cope']}')");
            }
			$resultarr['error'] = false;
			
			if($r)
			{
				$resultarr['error'] = true;
				$resultarr['id'] = $pay_number_id;
			}
			return $resultarr;
	}

	public function update($valueArr,$whereArr)
	{
		$field = '';
		$where = ' 1';
		foreach($valueArr as $k => $v)
		{
			$field .= "$k = '$v',";
		}
		foreach($whereArr as $k => $v)
		{
			$where .= " AND $k = '$v'";
		}
		$field = substr($field,0,-1);
		$sql = "UPDATE ".$this->table()." SET ".$field;
        $sql .= " WHERE ".$where;
		return $this->db()->query($sql,array());
	}

	function getDataOfApplyId($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "select * from `app_pay_apply_goods`";
		$sql .= " where `apply_id` =".$where['apply_id'];
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
        return $data;
	}

	public function updategoods($valueArr,$whereArr)
	{
		$field = '';
		$where = ' 1';
		foreach($valueArr as $k => $v)
		{
			$field .= "$k = '$v',";
		}
		foreach($whereArr as $k => $v)
		{
			$where .= " AND $k = '$v'";
		}
		$field = substr($field,0,-1);
		$sql = "UPDATE `app_pay_apply_goods` SET ".$field;
        $sql .= " WHERE ".$where;
		return $this->db()->query($sql,array());
	}

	function getDataOfApplyapply_id($apply_id)
	{
		$sql = "select * from `app_pay_apply_goods`";
		$sql .= " where apply_id = '$apply_id'";

		return $this->db()->getAll($sql);
	}

	public function update_goods($valueArr,$whereArr)
	{
		$field = '';
		$where = ' 1';
		foreach($valueArr as $k => $v)
		{
			$field .= "$k = '$v',";
		}
		foreach($whereArr as $k => $v)
		{
			$where .= " AND $k = '$v'";
		}
		$field = substr($field,0,-1);
		$sql = "UPDATE `goods` SET ".$field;
        $sql .= " WHERE ".$where;
		return $this->db()->query($sql,array());
	}
}

?>