<?php
/**
 *  -------------------------------------------------
 *   @file		: DefectiveProductModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-18 23:02:26
 *   @update	:
 *
 *	IQC质检
 *  -------------------------------------------------
 */
class DefectiveProductModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'defective_product';
        $this->_dataObject = array("id"=>"ID",
"status"=>"状态",
"prc_id"=>"供应商ID",
"prc_name"=>"供应商名称",
"ship_num"=>"出货单号",
"num"=>"总数量",
"total"=>"总金额",
"info"=>"备注",
"make_name"=>"制单人",
"make_time"=>"制单时间",
"check_name"=>"审核人",
"check_time"=>"审核时间");
		parent::__construct($id,$strConn);
	}

	function pageList ($where,$page,$pageSize=10,$useCache=true,$is_groupbyId=true)
	{
		$sql = "SELECT a.*,b.xuhao as xuhao,b.factory_sn as factory_sn,b.bc_sn as bc_sn,b.customer_name as customer_name,b.cat_type as cat_type,b.total as details_total,b.info as info FROM `".$this->table()."` as a left join defective_product_detail as b on a.id = b.info_id where 1";

		if(isset($where['hidden']) && $where['hidden'] != ''){
            $sql .= " and `a`.hidden = ".$where['hidden'];
        }
		if ($where['id'] != "")
		{
				$sql .= " AND a.id = ".$where['id'];
		}
		if ($where['status'] != "")
		{
				$sql .= " AND a.status = ".$where['status'];
		}
		if ($where['ship_num'] != "")
		{
				$sql .= " AND a.ship_num like \"%".addslashes($where['ship_num'])."%\"";
		}
		if ($where['prc_id'] != "")
		{
				$sql .= " AND a.prc_id = ".$where['prc_id'];
		}
		if ($where['make_name'] != "")
		{
				$sql .= " AND a.make_name like \"%".addslashes($where['make_name'])."%\"";
		}
		if ($where['check_name'] != "")
		{
				$sql .= " AND a.check_name like \"%".addslashes($where['check_name'])."%\"";
		}
		if($where['bc_sn'] != ""){
			$sql .= " AND b.bc_sn = '{$where['bc_sn']}'";
		}
		if($where['make_time_min'] != ""){
			$sql .= " AND a.make_time >= '{$where['make_time_min']}'";
		}
		if($where['make_time_max'] != ""){
			$sql .= " AND a.make_time <= '{$where['make_time_max']}'";
		}
		if($where['check_time_min'] != ""){
			$sql .= " AND a.check_time >= '{$where['check_time_min']}'";
		}
		if($where['check_time_max'] != ""){
			$sql .= " AND a.check_time <= '{$where['check_time_max']}'";
		}
		if($is_groupbyId){
			$sql .= " group by a.id ORDER BY a.id DESC";
		} else{
			$sql .= " ORDER BY a.id DESC";
		}
		//echo $sql; exit;
		$data = $this->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	public function getPageList ($sql,$params = array(), $page=1, $pageSize=20,$useCache=false)
    {
        try
        {
			$countSql = "SELECT COUNT(*) as count FROM (" . $sql . ") AS b";
            //$countSql = preg_replace('/^(SELECT.+?\bFROM\b)/i','SELECT COUNT(*) count FROM', $sql,1);
			$data['pageSize'] = (int)$pageSize<1? 20: (int)$pageSize;
            $data['recordCount'] = $this->db()->getOne($countSql,$params,$useCache);
            $data['pageCount'] = ceil($data['recordCount']/$data['pageSize']);
            $data['page'] = $data['pageCount']==0? 0: ((int)$page<1? 1: (int)$page);
            $data['page'] = $data['page']>$data['pageCount']? $data['pageCount']:$data['page'];
            $data['isFirst'] = $data['page']>1? false: true;
            $data['isLast'] = $data['page']<$data['pageCount']? false: true;
            $data['start'] = ($data['page']==0)? 1: ($data['page']-1)*$data['pageSize']+1;
            $data['sql'] = $sql.' LIMIT '.($data['start']-1).','.$data['pageSize'];
			// $data['data'] = $this->db()->queryResult($data['sql'],$params,$useCache);
			//$data['data'] = $this->db()->query($data['sql'], $params, $useCache);
			$data['data'] = $this->db()->getAll($data['sql']);
        }
        catch(Exception $e)
        {
            return false;
        }
        return $data;
    }

	/*
	* 改变不良品返厂单下的货品在质检列表下的状态
	* id:不良品返厂单单据ID
	* status: 要改变的状态ID
	*/
	function editDeceiptStatus($id,$status)
	{
		$sql = "SELECT rece_detail_id FROM defective_product_detail WHERE info_id = ".$id;
		$arr = $this->db()->getAll($sql);
		if(!count($arr))
		{
			return false;
		}

		$logModel = new PurchaseLogModel(24);
		if($status  == 7)
		{
			$remark = "审核不良品返厂单，单号：".$id."，已返厂。";
		}
		elseif($status == 5)
		{
			$remark = "取消不良品返厂单，单号：".$id."，恢复为IQC未过。";
		}

		foreach($arr as $key => $val)
		{
			#如果是取消则需要判断货品质检状态
			if ($status == 5)
			{
				//1、根据货品id查询IQC质检状态（IQC未过3---对应货品状态5或是报废2--对应货品状态2）
				$sql = "SELECT 	`opra_code` FROM `purchase_iqc_opra` where `rece_detail_id`={$val['rece_detail_id']} order by id desc limit 1";
				$status_ioc = $this->db()->getOne($sql);
				$remark_new = '';
				if ($status_ioc == 2)
				{
					$status_new = 2;
					$remark_new = "取消不良品返厂单，单号：".$id."，恢复为报废状态。";
				}
				else if ($status_ioc == 3)
				{
					$status_new = 5;
					$remark_new = "取消不良品返厂单，单号：".$id."，恢复为IQC未过。";
				}
				$sql = "UPDATE purchase_receipt_detail set status = $status_new  WHERE id = ".$val['rece_detail_id'];
				$this->db()->query($sql);
				//记录log
				$logModel->addlog($val['rece_detail_id'],$status_new,$remark_new);
			}
			else if ($status == 7)
			{
				$sql = "UPDATE purchase_receipt_detail set status = $status WHERE id = ".$val['rece_detail_id'];
				$this->db()->query($sql);
				//记录log
				$logModel->addlog($val['rece_detail_id'],$status,$remark);
			}
		}
		return true;
	}

	/**
	 * 未通过质检的采购货品  首先添加到不良返厂表  后把详细质检信息添加到不良返厂详细表 BY linian
	 * */
	public function insert_shiwu($info,$ids)
	{

		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

			//写入 defective_product表
			$sql = "INSERT INTO `defective_product`(`status`, `prc_id`, `prc_name`,`ship_num`, `num`, `total`,`make_name`, `make_time`, `check_name`,`note`) VALUES (
			'{$info['status']}', '{$info['prc_id']}', '{$info['prc_name']}','{$info['ship_num']}', '{$info['num']}', '{$info['total']}','{$info['make_name']}', '{$info['make_time']}', '{$info['check_name']}','{$info['note']}')";
			$pdo->query($sql);
			$id = $pdo->lastInsertId();
			$total = 0;
			foreach($ids as $val){
				//根据插入数据得到的ID  去采购工厂出库表 取出所有数据
				$sql = "SELECT * FROM purchase_iqc_opra WHERE rece_detail_id = ".$val." AND (opra_code = 3 or opra_code = 2 ) order by id desc limit 1";
				//echo $sql;exit;
				$arr = $this->db()->getRow($sql);

				//获取PurchaseReceiptDetail表所有数据
				$model = new PurchaseReceiptDetailModel($val,23);
				$receipt_detail_info = $model->getDataObject();
				$xiaoji=$receipt_detail_info['chengbenjia'] + $receipt_detail_info['tax_fee'];
				//成本价+ 税费 = 含税成本价
				$total +=$xiaoji;

				$sql = "INSERT INTO `defective_product_detail`(`info_id`, `rece_detail_id`, `xuhao`,`factory_sn`, `bc_sn`, `customer_name`,`cat_type`, `total`, `info`) VALUES ({$id}, {$receipt_detail_info['id']},'{$receipt_detail_info['xuhao']}', '{$receipt_detail_info['factory_sn']}', '{$receipt_detail_info['bc_sn']}',
				'{$receipt_detail_info['customer_name']}', '{$receipt_detail_info['cat_type']}',{$xiaoji}, '{$arr['opra_info']}')";
				//var_dump($sql); exit;
				$pdo->query($sql);
				//echo $sql;exit;
				//echo "<br>";
				//更新purchase_receipt_detail表中status状态为6 6:待返厂
				$sql ="update `purchase_receipt_detail` set `status`=6 where id = {$val}";
				//echo $sql;
				$pdo->query($sql);
				$times = date('Y-m-d H:i:s');
				//记录log
				$sql = "INSERT INTO `purchase_log`(`rece_detail_id`, `status`, `remark`,`uid`, `uname`, `time`) VALUES ({$receipt_detail_info['id']}, {$receipt_detail_info['status']},'生成不良品返厂单，单号：{$val}，等待返厂。', {$_SESSION['userId']}, '{$_SESSION['userName']}','{$times}')";
				$pdo->query($sql);

			}
			$sql = "update `defective_product` set total='{$total}' where id={$id}";
			$pdo->query($sql);
		}
		catch(Exception $e){//捕获异常
			echo $sql;exit;
			print_r($e);exit;
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;
	}
	
        public function getDetailId($infoid){
            $sql = "select `rece_detail_id` from `defective_product_detail` where `info_id`=$infoid";
            $detail_id = $this->db()->getOne($sql);
            return $detail_id;
        }
	
	
	public function batinsert_shiwu($info,$ids)
	{
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
	
			//写入 defective_product表
			$sql = "INSERT INTO `defective_product`(`status`, `prc_id`, `prc_name`,`ship_num`, `num`, `total`,`make_name`, `make_time`, `check_name`,`note`) VALUES (
			'{$info['status']}', '{$info['prc_id']}', '{$info['prc_name']}','{$info['ship_num']}', '{$info['num']}', '{$info['total']}','{$info['make_name']}', '{$info['make_time']}', '{$info['check_name']}','{$info['note']}')";
			//echo $sql;exit;
			$pdo->query($sql);
			$id = $pdo->lastInsertId();
			$total = 0;
			foreach($ids as $val){
			//根据插入数据得到的ID  去采购工厂出库表 取出所有数据
				
				$sql = "INSERT INTO `defective_product_detail`(`info_id`,`xuhao`,`factory_sn`,`total`,  `bc_sn`,`customer_name`,`cat_type`,`rece_detail_id`,`info`) 
				VALUES ({$id},'{$val[0]}','{$val[1]}','{$val[2]}','{$val[3]}','{$val[4]}','{$val[5]}',0,'{$val[6]}')";
				$pdo->query($sql);
				
				//记录log
				/* $sql = "INSERT INTO `purchase_log`(`rece_detail_id`, `status`, `remark`,`uid`, `uname`, `time`) VALUES ({$receipt_detail_info['id']}, {$receipt_detail_info['status']},'生成不良品返厂单，单号：{$val}，等待返厂。', {$_SESSION['userId']}, '{$_SESSION['userName']}','{$times}')";
				$pdo->query($sql); */
	
			}
			
	}
	catch(Exception $e){//捕获异常
	//echo $sql;exit;
	print_r($e);exit;
	$pdo->rollback();//事务回滚
	$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
	return false;
	}
	$pdo->commit();//如果没有异常，就提交事务
	$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
	return true;
	}

}

?>