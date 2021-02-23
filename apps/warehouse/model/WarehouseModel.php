<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-07 19:07:53
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'warehouse';
		$this->_dataObject = array("id"=>" ",
		"name"=>"仓库名称",
		"code"=>" ",
		"remark"=>"备注",
		"create_time"=>" ",
		"create_user"=>" ",
		"lock"=>"锁定状态 0未锁定/1锁定",
		"type"=>"仓库类型",
		"is_delete"=>"是否有效；0为无效，1为有效");
		parent::__construct($id,$strConn);
	}
	/**
	 *	pageList，分页列表
	 *
	 *	@url ApplicationController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql  = "SELECT `w`.`is_default`,`w`.`id`, `w`.`name`, `w`.`remark`, `w`.`create_time`, `w`.`create_user`,`w`.`code`,`w`.`is_delete`,`r`.`company_id`,`w`.`type` FROM `{$this->table()}` AS `w`,`warehouse_rel` AS `r` ";
		$sql .= " WHERE `w`.`id` = `r`.`warehouse_id` ";
		if($where['name'] != "")
		{
			$sql .= " AND `w`.`name` LIKE \"%".addslashes($where['name'])."%\"";
		}
		if($where['code'] != "")
		{
			$sql .= " AND `w`.`code` LIKE \"%".addslashes($where['code'])."%\"";
		}
		if($where['company_id'] !=="")
		{
			$sql .= " AND `r`.`company_id` = ".$where['company_id'];
		}
                if($where['type'] !=="")
		{
			$sql .= " AND `w`.`type` = ".$where['type'];
		}
		if($where['is_delete'] !=="")
		{
			$sql .= " AND `w`.`is_delete` = ".$where['is_delete'];
		}
		$sql .= " ORDER BY `w`.`id` DESC";
		// echo $sql;
		
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	/*
	function : check_code

	*/
	function check_code($code)
	{
		$sql = "select count(*) from ".$this->table()." where code ='$code'";
		return $this->db()->getOne($sql);
	}

	//2015/1/9 星期五
	public function select($dataArray='',$fieldArray='')
	{
		$fieldString = '';
		if(is_array($fieldArray) && !empty($fieldArray))
		{
			foreach($fieldArray as $key => $value){
				$fieldString .= ", $value";
				$fieldString = ltrim($fieldString,',');
			}
		}else
		{
			$fieldString = '*';
		}

		$DataString = '';
		if(is_array($dataArray) && !empty($dataArray)){
			foreach($dataArray as $key => $value){
				//$this->trim($value);
				$DataString .= " AND $key ='$value' ";
			}
		}
		$sql = "SELECT ".$fieldString." FROM ".$this->table()." WHERE 1 ";
		$sql .= $DataString;
		$data = $this->db()->getAll($sql);
		// print_r($data);exit;
		return $data;
	}

	/**
	* 根据仓库ID ，禁用仓库时，同时禁用柜位[前提是货位上没有商品]
	*/
	public function all_off($id)
	{
		$sqlArr = array(
			"UPDATE `warehouse_box` SET `is_deleted`= 0 WHERE `warehouse_id` = {$id}",	//禁用柜位
			"UPDATE `warehouse` SET `is_delete`=0 WHERE `id` = '$id'"	//修改仓库
			);
		$res = $this->db()->commit($sqlArr);
		return $res;
	}

	/**
	* 检测仓库下是有已经有商品入库
	* @param $id 仓库ID
	*/
	public function check_warehouse_goods($id)
	{
		$sql = "SELECT `id` FROM `goods_warehouse` WHERE `warehouse_id` = {$id}";
		return $this->db()->getAll($sql);
	}

	/**
	* 根据仓库ID ，删除仓库时，同时删除柜位[前提是货位上没有商品]
	*/
	public function deleteList($id)
	{
		$ids = $this->getWarehouseAll_id($id);
		$sqlArr = array();
		if($ids['rel_id'] != ''){
			$sqlArr[] = 'DELETE FROM `warehouse_rel` WHERE id in('.$ids['rel_id'].')';
		}
		if($ids['box_id'] != ''){
			$sqlArr[] = 'DELETE FROM `warehouse_box` WHERE id in('.$ids['box_id'].')';
		}
		$sqlArr[] = 'DELETE FROM `warehouse` WHERE id = '.$id;

		$res = $this->db()->commit($sqlArr);
		return $res;
	}

	/**
	* 根据仓库 ID 获取关联的 柜位ID 【删除仓库时，同时删除柜位】
	* @param $warehouse_id 仓库ID
	*/
	public function getWarehouseAll_id($warehouse_id){
		$return_arr = array(
			'box_id' => '',
			'rel_id' => '',
			);
		$box = $rel = '';
		/**获取柜位**/
		$sql = "SELECT `a`.`id` FROM `warehouse` AS `o`, `warehouse_box` AS `a` WHERE `o`.`id`='$warehouse_id' AND `o`.`id`= `a`.`warehouse_id` ";
		$area_id = $this->db()->getAll($sql);
		foreach($area_id as $cv){
			$box .= ','.$cv['id'];
		}

		/** 获取 仓库公司关系 **/
		$sql = " SELECT id FROM `warehouse_rel` WHERE warehouse_id = '{$warehouse_id}' ";
		$rel_id = $this->db()->getAll($sql);
		foreach($rel_id as $rv){
			$rel .= ','.$rv['id'];
		}

		$return_arr['box_id'] = ltrim($box , ',');
		$return_arr['rel_id'] = ltrim($rel , ',');
		return $return_arr;
	}

	/** 根据主键 获取仓库名称 **/
	public function getWarehosueNameForId($id){
		$sql ="SELECT `name`FROM `{$this->table()}`WHERE `id`=$id";
		return $this->db()->getOne($sql);
	}

	/** 检测仓库下 是否有柜位 **/
	public function checkBoxByWarehouse($warehouse_id){
		$sql = "SELECT `a`.`id` FROM `warehouse` AS `a` , `warehouse_box` AS `b` WHERE `a`.`id` = `b`.`warehouse_id` LIMIT 1";
		return $this->db()->getOne($sql);
	}

	/** 检测仓库是否锁定（既是否在盘点中） **/
	public function checkLock($warehouse_id){
		$sql = "SELECT `lock` FROM `warehouse` WHERE `id` = {$warehouse_id} LIMIT 1";
		return $this->db()->getOne($sql);
	}

	public function getAllhouse(){
		$sql = "SELECT `id`,`name`,code FROM `".$this->table()."` WHERE `is_delete` = '1'";
		$houses = $this->db()->getAll($sql);
		return $houses;
	}

	//普通查询
	public function select2($fields = '*' , $where = '1 limit 1' , $type = 'one'){
		$sql = "SELECT {$fields} FROM `warehouse` WHERE {$where}";
		if($type == 'one'){
			return $this->db()->getOne($sql);
		}else if($type == 'row'){
			return $this->db()->getRow($sql);
		}else if($type = 'all'){
			return $this->db()->getAll($sql);
		}
	}


	//根据仓库id 获取该仓库下的默认柜位
	public function GetDefaultBox($warehouse_id){
		$sql = "SELECT * FROM `warehouse_box` WHERE `warehouse_id` = $warehouse_id AND `is_deleted` = 1 AND `box_sn` = '0-00-0-0'";
		return $this->db()->getRow($sql);
	}

	/*
	 *通过启用状态，待取类型获取最新的仓库信息
	 */
	public function getLastWarehouse($company_id){
		$sql = "select w.id,w.name,w.create_time from warehouse_shipping.warehouse as w ,`warehouse_rel` AS `r` where is_delete=1 and type=3 and `w`.`id` = `r`.`warehouse_id` and company_id= '".$company_id."'";
		return $this->db()->getAll($sql);

		// $sql = "SELECT `id` , `name` , `code` FROM `warehouse` WHERE  `name` like '%维修%' AND `is_delete` = 1 AND `name` !='跟单维修库'";


	}
	
	
	/*
	* 获取启用、创建最新的维修仓库
	*/

	public function getRepairLastWarehouse($company_id){
		$sql = "select w.id,w.code,w.name,w.create_time from warehouse_shipping.warehouse as w ,`warehouse_rel` AS `r` where is_delete=1 AND `name` like '%维修%' AND `name` !='跟单维修库'
 AND `w`.`id` = `r`.`warehouse_id` and company_id= '".$company_id."' order by create_time desc limit 1";
		return $this->db()->getRow($sql);
	}
	

	/**
	* 获取某公司的仓库
	*/
	public function getMasterWarehouse($company_id)
	{
		$sql = "select w.id,w.name,w.code from warehouse_shipping.warehouse as w ,`warehouse_rel` AS `r` where is_delete=1 
 			AND `w`.`id` = `r`.`warehouse_id` and company_id= '".$company_id."' order by w.create_time desc ";

		return $this->db()->getAll($sql);
	}


	
	//2015/10/15 
	public function select3($dataArray='',$fieldArray='')
	{
		
		//获取用户权限内的仓库,只允许当前登录人盘点 自己拥有权限 的所属仓库的盘点单
		$UserWarehouseModel = new UserWarehouseModel(1);
		$UserWarehouseArr=$UserWarehouseModel->getUserWarehouse();
		$in='(';
		foreach ($UserWarehouseArr as $k=>$row){
			if($k==0){
				$in.=$row['house_id'];
			}else{
				$in.=','.$row['house_id'];
			}
				
		}
		$in.=')';
		
		$fieldString = '';
		if(is_array($fieldArray) && !empty($fieldArray))
		{
			foreach($fieldArray as $key => $value){
				$fieldString .= ", $value";
				$fieldString = ltrim($fieldString,',');
			}
		}else
		{
			$fieldString = '*';
		}
	
		$DataString = '';
		if(is_array($dataArray) && !empty($dataArray)){
			foreach($dataArray as $key => $value){
				//$this->trim($value);
				$DataString .= " AND $key ='$value' ";
			}
		}
		$sql = "SELECT ".$fieldString." FROM ".$this->table()." WHERE id IN {$in} ";
		$sql .= $DataString;
		$data = $this->db()->getAll($sql);
		// print_r($data);exit;
		return $data;
	}
	/**
	 * 获取订单的单据维修发货状态
	 * @param unknown $order_sn
	 * return true 维修发货中，false 维修结束
	 */
	function checkOrderWeixiuStatus($order_sn){
	    //查询是否有 未审核 的维修发货单
	    $sql = "select count(1) as c from warehouse_shipping.warehouse_bill where order_sn='{$order_sn}' and bill_type='R' and bill_status=1";
	    $ret1 = $this->db()->getOne($sql);
	    if($ret1){
	        return true;
	    }else{
	        //查询是否有未取消（ 未审核+已审核）的维修退货或维修调拨单
	        $sql = "select count(1) as c from warehouse_shipping.warehouse_bill where order_sn='{$order_sn}' and bill_type in('O','WF') and bill_status=1";
	        $ret2= $this->db()->getOne($sql);
	        if($ret2) {
	            return true;
	        }
	    }
	    return false;
	}
	
    //获取浩鹏系统非直营店销售渠道
    public function getHaopengSalechannel(){
        $sql="select id,channel_name as name,id as code from cuteframe.sales_channels where company_id in (select id from cuteframe.company where is_deleted=0 and company_type<>1) and channel_name not like '%已闭店%' ";
        return $this->db()->getAll($sql);
    }


}?>
