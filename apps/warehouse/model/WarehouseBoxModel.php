<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBoxModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 17:34:45
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBoxModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'warehouse_box';
		$this->pk='id';
		$this->_prefix='';
		$this->_dataObject = array("id"=>" ",
		"warehouse_id"=>"柜位所属仓库",
		"box_sn"=>"柜位号",
		"create_time"=>"新增时间",
		"create_name"=>"新增人",
		"info"=>"备注",
		"is_deleted"=>"是否禁用 0否 1是");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url WarehouseBoxController/search
	 */
	public function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT `id`, `warehouse_id`, `box_sn`, `create_time`, `create_name`, `is_deleted` FROM `".$this->table()."` ";
		$str = '';
		if($where['warehouse_id'] != "")
		{
			$str .= "`warehouse_id` like ".addslashes($where['warehouse_id'])." AND ";
		}
		if(!empty($where['box_sn']))
		{
			$str .= "`box_sn`='".$where['box_sn']."' AND ";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	/**
	* 普通查询
	* @param $fields String 查询的字段
	* @param $where String 查询的条件
	* @param $is_all Boll 查询单条或多条 1:单条 2:多条 3:单个字段
	*/
	public function select2($fields , $where , $is_all = 1){
		$sql = "SELECT {$fields} FROM `warehouse_box` WHERE $where ORDER BY `id` DESC";
		// die($sql);
		if($is_all == 1){
			return $this->db()->getRow($sql);
		}else if($is_all == 2){
			return $this->db()->getAll($sql);
		}else if($is_all == 3){
			return $this->db()->getOne($sql);
		}
	}

	/** 计算柜位下的货品数量 **/
	public function sumGoodsByBox($box_id){
		$sql = "SELECT `id` FROM `goods_warehouse` WHERE `box_id`={$box_id}";
		$res = $this->db()->getAll($sql);
		return count($res);
	}

	/** 检测柜位号是否重复 **/
	public function checkRepeatBoxSn($box_sn,$warehouse_id){
		$sql = "SELECT `id` FROM `warehouse_box` WHERE `box_sn`='{$box_sn}' and `warehouse_id`='{$warehouse_id}'";

		return $this->db()->getOne($sql);
	}

	/**根据柜位号检测柜位是否存在**/
	public function checkExistBySn($guiwei_sn){
		$sql = "SELECT `id` FROM `warehouse_box` WHERE `box_sn` = '{$guiwei_sn}'";
		return $this->db()->getOne($sql);
	}

	/** 检测柜位是否存在，并且没有被禁用 **/
	public function checkBoxRigth($box_sn){
		$sql = "SELECT `id` FROM `warehouse_box` WHERE `box_sn` = '{$box_sn}' AND `is_deleted` = 1";
		return $this->db()->getOne($sql);
	}

	/** 根据仓库 / 柜位 检测柜位是否在仓库下 **/
	public function checkBoxInWarehouse($warehouse_id, $box_sn){
		$sql = "SELECT `warehouse_id` FROM 	`warehouse_box` WHERE `box_sn` = '{$box_sn}' AND `warehouse_id` = {$warehouse_id}";
		$w_id = $this->db()->getOne($sql);
		if(!$w_id){
			return false;
		}else{
			return true;
		}
	}

	/**根据仓库ID，获取该仓库下所有的柜位列表**/
	public function getBoxListByWarehouseID($warehouse_id){
		$sql = "SELECT `id`, `box_sn`FROM `warehouse_box` WHERE `warehouse_id`={$warehouse_id} and is_deleted = 1";
		return $this->db()->getAll($sql);
	}

	/** 检测柜位下是否有货品 **/
	public function checkGoodsInBox($box_id){
		$sql = "SELECT `id` FROM `goods_warehouse` WHERE `box_id` = {$box_id}";
		return $this->db()->getOne($sql);
	}

	//根据柜位信息，获取所在仓库
	public function getWarehouseInfo($fields = ' * ', $where = 1 , $type = 'one'){
		$sql = "SELECT {$fields} FROM `warehouse_box` AS `a` LEFT JOIN `warehouse` AS `b` ON `a`.`warehouse_id` = `b`.`id` WHERE {$where} LIMIT 1";
		if($type == 'one'){
			return $this->db()->getOne($sql);
		}else if($type == 'row'){
			return $this->db()->getRow($sql);
		}else if($type == 'all'){
			return $this->db()->getAll($sql);
		}
	}
	
	
	//根据柜位信息，获取所在仓库
	public function getWarehouseInfoBybox_goods($goods_id,$box_sn){
		$sql = "SELECT `w`.`name` FROM `warehouse_box` AS `b` ,`warehouse` AS `w`,`goods_warehouse` AS `g` WHERE `b`.`warehouse_id` = `w`.`id` AND `g`.`box_id`=`b`.`id` AND `g`.`good_id`='$goods_id' AND `b`.`box_sn`='$box_sn'  LIMIT 1";
		return $this->db()->getOne($sql);
	}
	
}

?>