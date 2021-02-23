<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoWModel.php
 *   @link		:  www.kela.cn
 *   @update  	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoWModel extends Model
{
	//定义是否开启盘点异常日志追踪
	private $buglog = true;

	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'warehouse_bill_info_w';
		$this->pk='id';
		$this->_prefix='';
		$this->_dataObject = array("id"=>" ",
		"bill_id"=>"关联warehouse_bill 主键",
		"box_sn"=>"柜位号");
		parent::__construct($id,$strConn);
	}
	function pageList($where,$page,$pageSize=10,$useCache=true , $down = false)
	{
		$sql = "SELECT bill.bill_no,w.bill_id,bill.to_warehouse_name,w.id,left(bill.check_time,11) as opt_date FROM `warehouse_bill_info_w` w JOIN `warehouse_bill` bill ON bill.id=w.bill_id JOIN `warehouse_rel` rel ON rel.warehouse_id=bill.`to_warehouse_id`";
		$str=" bill.check_user!='' AND ";
		if(isset($where['status_in']) && !empty($where['status_in']))
		{
			$str .= " bill.`bill_status` in(".$where['status_in'].") AND ";
		}
		if(isset($where['warehouse_id']) && !empty($where['warehouse_id']))
		{
			$where['warehouse_id']=trim($where['warehouse_id']);
			$str .= " bill.`to_warehouse_id` in('".$where['warehouse_id']."') AND ";
		}
		if(isset($where['to_company_id']) && !empty($where['to_company_id']))
		{
			$str .= " rel.`company_id` in('".$where['to_company_id']."') AND ";
		}
		
		if(!empty($where['start_time'])){
				$str.="  bill.check_time >= '".$where['start_time']." 00:00:00' AND ";
		}
		if(!empty($where['end_time'])){
				$str.=" bill.check_time <= '".$where['end_time']." 23:59:59' AND ";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY w.`id` DESC";
		//echo $sql;exit;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		if($data){
			foreach($data['data'] as $key => &$val){
				$val['real_num'] = 0;  //实盘数量  总共实盘的数量（正常num+  盘盈num+ 盘亏num）
				$val['real_price'] = 0;  //实盘金额 = 总共实盘的金额（正常+  盘盈+ 盘亏）
				//
				$sql="SELECT count(*)  AS num  ,sum(`chengbenjia`) AS `price`,sum(`yuanshichengben`) AS `origin_price`  FROM `warehouse_bill_goods` WHERE `bill_id` = {$val['bill_id']}";
				$row=$this->db()->getRow($sql);
				if($row['price']<=0) $row['price']=$row['origin_price'];
				$val['all_price'] = $row['price'];
				$val['all_num']=$row['num'];
				//正常统计
				$sql = "SELECT count(*) AS num ,sum(`chengbenjia`) AS `price`,sum(`yuanshichengben`) AS `origin_price` FROM `warehouse_bill_goods` WHERE `bill_id` = {$val['bill_id']} AND `pandian_status` = 4";
				$row = $this->db()->getRow($sql);
				if($row['price']<=0) $row['price']=$row['origin_price'];
				$val['real_num'] += $row['num'];
				$val['real_price'] += $row['price'];
				$val['nomal_num'] = $row['num'];
				$val['nomal_price'] = $row['price'];
		
				//盘盈统计
				$sql = "SELECT count(*) AS num ,sum(`chengbenjia`) AS `price`,sum(`yuanshichengben`) AS `origin_price` FROM `warehouse_bill_goods` WHERE `bill_id` = {$val['bill_id']} AND `pandian_status` = 3";
				$row = $this->db()->getRow($sql);
				if($row['price']<=0) $row['price']=$row['origin_price'];
				$val['real_num'] += $row['num'];
				$val['real_price'] += $row['price'];
		
				$all_overage_num = $val['overage_num'] = $row['num'];
				$val['overage_price'] = $row['price'];
		
				//盘亏统计
				$sql = "SELECT count(*) AS num ,sum(`chengbenjia`) AS `price`,sum(`yuanshichengben`) AS `origin_price` FROM `warehouse_bill_goods` WHERE `bill_id` = {$val['bill_id']} AND `pandian_status` = 2";
				$row = $this->db()->getRow($sql);
				if($row['price']<=0) $row['price']=$row['origin_price'];
				$val['real_num'] += $row['num'];
				$val['real_price'] += $row['price'];
		
				$all_loss_num = $val['loss_num'] = $row['num'];
				$val['loss_price'] = $row['price'];
		
				$val['overage_rate']=$val['loss_rate']=$val['accurate_rate']=0;
				
				//计算盘点准确率
				if ($val['nomal_num'] != 0)
				{
					$val['accurate_rate'] = round(($val['nomal_num'])/$val['real_num']*100 , 2);		//盘点准确率=正常数量/实盘数量
				}
				//盘亏占比
				if ($val['loss_num'] != 0)
				{
					$val['loss_rate'] = round(($val['loss_num'])/$val['real_num']*100 , 2);		//盘亏占比=盘亏数量/实盘数量；
				}
				//盘盈占比
				if ($val['overage_num'] != 0)
				{
					//$val['overage_rate'] = round(($val['overage_num'])/$val['real_num']*100 , 2);		//盘盈占比=盘盈数量/实盘数量
                    $val['overage_rate'] = round(100 - $val['loss_rate'] - $val['accurate_rate'],4);
				}
			}
		}
		//exit;
		return $data;
	}
	


}
