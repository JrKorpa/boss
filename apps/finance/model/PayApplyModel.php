<?php
/**
 *  -------------------------------------------------
 *   @file		: PayApplyModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-08 16:14:50
 *   @update	:
 *  -------------------------------------------------
 */
class PayApplyModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'pay_apply';
        $this->_dataObject = array(
			'pay_apply_number'	=>'应付申请单号',
			'status'			=>'申请状态',
			'pay_number'		=>'财务应付单单号',
			'make_time'			=>'制单时间',
			'make_name'			=>'制单人',
			'check_time'		=>'审核时间',
			'check_name'		=>'审核人',
			'company'			=>'所属公司',
			'prc_id'			=>'供货商ID',
			'prc_name'			=>'供货商名称',
			'pay_type'			=>'应付类型',
			'amount'			=>'总数量',
			'total_cope'		=>'应付金额',
			'total_dev'			=>'偏差金额',
			'adj_reason'		=>'调整原因',
			'record_type'		=>'单据类型',
			'fapiao'			=>'应付单发票号'
		);
		parent::__construct($id,$strConn);
	}


	function pageList($filter, $page=1, $pageSize=15)
	{
		//$sql = "select * from `".$this->table()."`";
		$sql = "SELECT pay.*,cat.cat_type_name FROM `".$this->table()."` AS pay LEFT JOIN front.app_cat_type AS cat ON pay.style_type = cat.cat_type_id";
		//$sql = "SELECT pay.*,coalesce(cat.cat_type_name,'未找到') AS cat_type_name FROM `".$this->table()."` AS pay LEFT JOIN front.app_cat_type AS cat ON pay.style_type = cat.cat_type_id";
		$where = " where 1 ";
		$filter["order"]  = 'pay.'.empty($filter["order"]) ? "apply_id" :$filter["order"];
		$filter["sort"]  = 'pay.'.empty($filter["sort"]) ? "desc" :$filter["order"];

		if(!empty($filter["company"]))
		{
			$where .= " AND pay.company = '".$filter["company"]."'";
		}
		if(isset($filter['status']) && $filter["status"] != '')
		{
			$where .= " AND pay.status = '".$filter["status"]."'";
		}
		if(!empty($filter["prc_id"]))
		{
			$where .= " AND pay.prc_id = '".$filter["prc_id"]."'";
		}
		if(!empty($filter["pay_apply_number"]))
		{
			$where .= " AND pay.pay_apply_number = '".addslashes($filter["pay_apply_number"])."'";
		}
		if(!empty($filter["pay_type"]))
		{
			$where .= " AND pay.pay_type = '".$filter["pay_type"]."'";
		}
		if(!empty($filter["pay_number"]))
		{
			$where .= " AND pay.pay_number = '".$filter["pay_number"]."'";
		}
		if(!empty($filter["start_make_time"]))
		{
			$where .= " AND pay.make_time >= '".$filter["start_make_time"]." 00:00:00'";
		}
		if(!empty($filter["end_make_time"]))
		{
			$where .= " AND pay.make_time <= '".$filter["end_make_time"]." 23:59:59'";
		}
		if(!empty($filter["start_check_time"]))
		{
			$where .= " AND pay.check_time >= '".$filter["start_check_time"]." 00:00:00'";
		}
		if(!empty($filter["end_check_time"]))
		{
			$where .= " AND pay.check_time <= '".$filter["end_check_time"]." 23:59:59'";
		}
		if(!empty($filter["make_name"]))
		{
			$where .= " AND pay.make_name = '".$filter["make_name"]."'";
		}
		if(!empty($filter["record_type"]))
		{
			$where .= " AND pay.record_type = '".$filter["record_type"]."'";
		}
		if(!empty($filter["fapiao"]))
		{
			$where .= " AND pay.fapiao = '".$filter["fapiao"]."'";
		}
		if(!empty($filter["cat_type"]))
		{
			$where .= " AND pay.style_type = '".$filter["cat_type"]."'";
		}
        if(isset($filter['hidden']) && $filter['hidden'] != ''){
            $where .= " and `pay`.hidden = ".$filter['hidden'];
        }
// 		if(!empty($filter['style_type'])){
// 		    $where .= " AND pay.style_type = '".$filter["style_type"]."'";
// 		}
		$sql = $sql. $where. " order by ".$filter["order"]." ".$filter["sort"];
		//die ($sql);
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize);
		return $data;
	}


	public function saveDatas($applydata,$data)
	{
		$applyGoodsModel = new PayApplyGoodsModel(29);
		if(empty($applydata['apply_id']))//添加数据
		{
			$apply_id = $this->saveData($applydata,array());
			$pay_apply_number = $applydata['pay_apply_number'].$apply_id;//应付申请单号
			$sql = " update ".$this->table()." set pay_apply_number = '".$pay_apply_number."'   where apply_id = {$apply_id}";
			$this->db()->query($sql,array());
		}else{//修改数据
			$pay_apply_number = $applydata['pay_apply_number'];
			$apply_id = $applydata['apply_id'];

			$editData = array(
			'prc_id'		=> $applydata['prc_id'],
			'prc_name'		=> $applydata['prc_name'],
			'pay_type'		=> $applydata['pay_type'],
			'amount'		=> $applydata['amount'],
			'total_cope'	=> $applydata['total_cope'],
			'total_dev'		=> $applydata['total_dev'],
			'fapiao'		=> $applydata['fapiao']
			);

			$this->update($editData,array('apply_id'=>$apply_id));//修改单据内容
			$applyGoodsModel->deleteOfApplyId($apply_id);//删除单据中商品
			$goodsModel = new GoodsModel(29);
			$goodsModel->update(array('pay_apply_status'=>'1','pay_apply_number'=>''),array('pay_apply_number'=>$pay_apply_number));
		}

		if($applyGoodsModel->addData($apply_id,$data))//添加pay_apply_goods表（修改是都删除后重新添加）
		{
			$GoodsModel = new GoodsModel(29);
			//添加数据成功，改变goods表数据申请状态和单号
			foreach($data as $k => $v)
			{
				$value = array('pay_apply_status'=>'2','pay_apply_number'=>$pay_apply_number);
				$where = array('serial_number'=>$v['serial_number']);
				$GoodsModel ->update($value,$where);
			}
			$reg['result'] = true;
			$reg['apply_id'] = $apply_id;
		}else{
			$this->delete(array('apply'=>$apply_id));
			$reg['result'] = false;
		}
		return $reg;
	}

	public function savaAdjData($applydata)
	{
		$reg['result'] = false;
		if(empty($applydata['apply_id']))//添加数据
		{
			$apply_id = $this->saveData($applydata,array());
			$pay_apply_number = $applydata['pay_apply_number'].$apply_id;//应付申请单号
			$sql = " update ".$this->table()." set pay_apply_number = '".$pay_apply_number."'   where apply_id = {$apply_id}";
			if($this->db()->query($sql,array()))
			{
				$reg['result'] = true;
			}
		}else{//修改数据
			$apply_id = $applydata['apply_id'];
			unset($applydata['apply_id']);
			if($this->update($applydata,array('apply_id'=>$apply_id)))
			{
				$reg['result'] = true;
			}
		}
		$reg['apply_id'] = $apply_id;
		return $reg;
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

	public function getRow($id,$col = "*")
	{
		$sql = "select $col from ".$this->table()." where apply_id = ".$id;
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
    /**
     * 获取所有款式类型
     */	
	public function getCatTypeList() {
	    $sql = "SELECT cat_type_id ,cat_type_name,cat_type_code FROM front.app_cat_type WHERE cat_type_code != 'all' and cat_type_status =1";
	    return $this->db()->getAll($sql);
	}
}

?>