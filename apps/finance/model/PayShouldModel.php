<?php
/**
 *  -------------------------------------------------
 *   @file		: PayShouldModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-08 16:51:14
 *   @update	:
 *  -------------------------------------------------
 */
class PayShouldModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'pay_should';
        $this->_dataObject = array();
		parent::__construct($id,$strConn);
	}

	public function getShouldListByWhere($where = array(),$page,$pageSize,$useCache=true)
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
		if(!empty($where['pay_status'])){
			$sql .= " AND pay_status = ".$where['pay_status'];
		}
		if(!empty($where['pay_should_all_name'])){
			$sql .= " AND pay_should_all_name = '".$where['pay_should_all_name']."'";
		}
		if(!empty($where['status'])){
			$sql .= " AND status = '".$where['status']."' ";
		}
		if(!empty($where['make_name'])){
			$sql .= " AND make_name = '".$where['make_name']."' ";
		}
		if(!empty($where['make_time_s'])){
			$sql .= " AND make_time >= '".$where['make_time_s']." 00:00:00' ";
		}
		if(!empty($where['make_time_e'])){
			$sql .= " AND make_time <= '".$where['make_time_e']." 23:59:59' ";
		}
		if(!empty($where['check_time_s'])){
			$sql .= " AND check_time >= '".$where['check_time_s']." 00:00:00' ";
		}
		if(!empty($where['check_time_e'])){
			$sql .= " AND check_time <= '".$where['check_time_e']." 23:59:59' ";
		}
        if(isset($where['hidden']) && $where['hidden'] != ''){
            $sql .= " and hidden = ".$where['hidden'];
        }
		$sql .= " order by `pay_number_id` desc";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	public function getShouldallprice($where = array())
	{
		$sql = "select SUM(total_cope) as all_should_price,SUM(total_real) as all_real_price from `".$this->table()."`";
		$sql .= " where 1 ";
		if(!empty($where['company'])){
			$sql .= " AND company = ".$where['company'];
		}
		if(!empty($where['prc_id'])){
			$sql .= " AND prc_id = ".$where['prc_id'];
		}
		if(!empty($where['pay_status'])){
			$sql .= " AND pay_status = ".$where['pay_status'];
		}
		if(!empty($where['pay_type'])){
			$sql .= " AND pay_type = ".$where['pay_type'];
		}
		if(!empty($where['pay_should_all_name'])){
			$sql .= " AND pay_should_all_name = '".$where['pay_should_all_name']."'";
		}
		if(!empty($where['status'])){
			$sql .= " AND status = '".$where['status']."' ";
		}
		if(!empty($where['make_name'])){
			$sql .= " AND make_name = '".$where['make_name']."' ";
		}
		if(!empty($where['make_time_s'])){
			$sql .= " AND make_time >= '".$where['make_time_s']." 00:00:00' ";
		}
		if(!empty($where['make_time_e'])){
			$sql .= " AND make_time <= '".$where['make_time_e']." 23:59:59' ";
		}
		if(!empty($where['check_time_s'])){
			$sql .= " AND check_time >= '".$where['check_time_s']." 00:00:00' ";
		}
		if(!empty($where['check_time_e'])){
			$sql .= " AND check_time <= '".$where['check_time_e']." 23:59:59' ";
		}
		return $this->db()->getRow($sql,array(),false);
	}
	public function getShouldInfo($id)
	{
		$sql = "select *,a.total_cope as t_cope,b.total_cope as b_price from `".$this->table()."` as a,pay_should_detail as b ";
		$sql .= " where a.pay_number_id = b.pay_number and a.pay_number_id = ".$id;
		return $this->db()->getAll($sql,array(),false);
	}
	public function getShouldDetail($Id)
	{
		$sql = "select ps.*,pa.record_type from pay_should_detail as ps,pay_apply as pa where ps.pay_apply_number = pa.pay_apply_number and ps.pay_number = $Id";
		return $this->db()->getAll($sql,array(),false);
	}

	public function updatePayshouldStatus($id,$status)
	{
		$sql = "UPDATE `".$this->table()."` SET `status` = '$status' WHERE pay_number_id = '$id'  LIMIT 1 ";
		return $this->db()->query($sql,array(),false);
	}
	public function updateFinPayshouldStatus($id,$status)
	{
		$sql = "UPDATE `".$this->table()."` SET `pay_status` = '$status' WHERE pay_number_id = '$id'  LIMIT 1 ";
		return $this->db()->query($sql,array(),false);
	}
	public function updatePayshouldTotal_real($id,$price)
	{
		$sql = "UPDATE `".$this->table()."` SET `total_real` = '$price' WHERE pay_number_id = '$id'  LIMIT 1 ";
		return $this->db()->query($sql,array(),false);
	}

	public function add($ids)
	{
		//计算总金额
			$applyModel = new PayApplyModel(29);
			$total = $applyModel->getTotalOfIds($ids);

			//通过checkShouldCon方法已经确定所有单据都是同一个类型和供货商，只取ids_arr[0]的数据即可
			$ids_arr = explode(',',$ids);
			$applyarr = $applyModel->getRow($ids_arr[0]);

			$apiProcess = new ApiProcessorModel();
			$prc_info = $apiProcess->GetSupplierPay(array('id'),array($applyarr['prc_id']));

			if(!count($prc_info))
			{
				$settle_mode = '';
				$settle_mode = 0;
			}else{
				$settle_mode = $prc_info['data']['balance_type'];
			}

			$data = array(
			'pay_type'		=> $applyarr['pay_type'],
			'prc_id'		=> $applyarr['prc_id'],
			'prc_name'		=> $applyarr['prc_name'],
			'settle_mode'	=> $settle_mode,
			'company'		=> '58',
			'make_time'		=> date('Y-m-d H:i:s'),
			'make_name'		=> $_SESSION['userName'],
			'check_time'	=> '0000-00-00 00:00:00',
			'total_cope'	=> $total,
			'pay_should_all_name' => 'CWYF'
			);
			$pay_number_id = $this->saveData($data,array());
			$pay_should_all_name = $data['pay_should_all_name'].$pay_number_id;
			$sql = " update ".$this->table()." set pay_should_all_name = '".$pay_should_all_name."'   where pay_number_id = ".$pay_number_id;
			$this->db()->query($sql,array());

			//pay_should_detail表的数据
			$detailData = array();
			foreach($ids_arr as $k => $v)
			{
				$ar = $applyModel->getRow($v,'pay_apply_number,total_cope');
				$dataArr['pay_number']			= $pay_number_id;
				$dataArr['pay_apply_number']	= $ar['pay_apply_number'];
				$dataArr['total_cope']			= $ar['total_cope'];
				$detailData[] = $dataArr;
				//修改申请单对应的应付单单号
				$applyModel->update(array('pay_number'=>$pay_should_all_name,'status'=>'6'),array('apply_id'=>$v));
			}
			$detailModel = new PayShouldDetailModel(29);
			$resultarr['error'] = false;

			if($detailModel->insertAll($detailData))
			{
				$resultarr['error'] = true;
				$resultarr['id'] = $pay_number_id;
			}
			return $resultarr;
	}

	public function getRow($id,$col = "*")
	{
		$sql = "select $col from ".$this->table()." where pay_number_id = ".$id;
		return $this->db()->getRow($sql);
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
}

?>