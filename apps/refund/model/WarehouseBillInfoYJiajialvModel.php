<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoYJiajialvModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-11-26 12:03:08
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoYJiajialvModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'warehouse_bill_info_y_jiajialv';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
			"sytle_type_id"=>"款式分类:app_cat_type",
			"sytle_type_name"=>"款式分类名称",
			"jiajialv"=>"加价率",
			"create_time"=>"创建时间",
			"check_time"=>"审核时间",
			"active_date"=>"生效日期",
			"remark"=>"备注",
			"creator"=>"制单人",
			"checker"=>"审核人",
			"status"=>"状态");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url WarehouseBillInfoYJiajialvController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1";
		
		if(!empty($where['id']))
		{
			$sql .=" AND `id`=".$where['id'];	
		}
		
		if(!empty($where['style_type_id']))
		{
			$sql .=" AND `style_type_id`=".$where['style_type_id'];	
		}
		
		if(!empty($where['remark']))
		{
			$sql .=" AND `remark`=".$where['remark'];	
		}
		
		if(!empty($where['status']))
		{
			$sql .=" AND `status`=".$where['status'];	
		}
		
		if(!empty($where['create_time_s']))
		{
			$sql .=" AND `create_time` >= '".$where['create_time_s'] . "'";	
		}
		
		if(!empty($where['create_time_e']))
		{
			$sql .=" AND `create_time` <= '".$where['create_time_e'] . "'";	
		}
		
		
		if(!empty($where['check_time_s']))
		{
			$sql .=" AND `check_time` >= '".$where['check_time_s'] . "'";	
		}
		
		if(!empty($where['check_time_e']))
		{
			$sql .=" AND `check_time` <= '".$where['check_time_e'] . "'";	
		}
		
//		if($where['xxx'] != "")
//		{
//			$str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
//		}
//		if(!empty($where['xx']))
//		{
//			$str .= "`xx`='".$where['xx']."' AND ";
//		}
		
		$sql .= " ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		/*
		if (!empty($data['data'])){
			foreach ($data['data'] as $key => $val){
				$data['data'][$key]['active_jiajialv'] = $this->getJiajialvByStyleTypeId($val['style_type_id']);
			}
		}
		*/
		return $data;
	}
	
	function  getJiajialvByStyleTypeId($typeId){
		$this->updateJiajiaLvStatus();
		$sql = "SELECT `jiajialv` FROM `warehouse_bill_info_y_jiajialv` WHERE `style_type_id` = '{$typeId}' AND `status` = '2' AND active_date <= '".date("Y-m-d",time())."' ORDER BY active_date ASC";
		return number_format($this->db()->getOne($sql),2);
	}
	
	function  getJiajialvByStyleTypeName($typeName){
		$this->updateJiajiaLvStatus();
		$sql = "SELECT `jiajialv` FROM `warehouse_bill_info_y_jiajialv` WHERE `style_type_name` = '{$typeName}' AND `status` = '2' AND active_date <= '".date("Y-m-d",time())."' ORDER BY active_date ASC";
		$jiajialv = $this->db()->getOne($sql);
		if (!$jiajialv){
			$sql = "SELECT `jiajialv` FROM `warehouse_bill_info_y_jiajialv` WHERE `style_type_name` = '其他' AND `status` = '2' AND active_date <= '".date("Y-m-d",time())."' ORDER BY active_date ASC";
			$jiajialv = $this->db()->getOne($sql);
		}
		return number_format($jiajialv,2);
	}
	
	function  getJiajialvList(){
		$this->updateJiajiaLvStatus();
		$sql = "select * from `warehouse_bill_info_y_jiajialv` where id in (select max(id) from `warehouse_bill_info_y_jiajialv` group by style_type_id)";
		$data = $this->db()->getAll($sql);
		foreach ($data as $key => $val){
			$data[$key]['active_jiajialv'] = $this->getJiajialvByStyleTypeId($val['style_type_id']);
		}
		return $data;
	}
	
	function  updateJiajiaLvStatus(){
		$sql = "select id from warehouse_bill_info_y_jiajialv where `status` = 2 and active_date <= CURDATE() group by style_type_id HAVING count(*) > 1";
		$data = $this->db()->getAll($sql);
		foreach ($data as $key => $val){
			$sql = "UPDATE `warehouse_bill_info_y_jiajialv` SET status = 3 WHERE id = '" . $val['id'] . "';";
			$this->db()->query($sql);
		}
		return true;
	}
	
	function  check($id){
		$sql = "SELECT * FROM `warehouse_bill_info_y_jiajialv` WHERE id = '" . $id . "'";
		$data = $this->db()->getRow($sql);
		
		$sql = "UPDATE `warehouse_bill_info_y_jiajialv` SET status = 2 ,`checker`= '" . $_SESSION['userName'] ."', `check_time`= '". date("Y-m-d H:i:s",time()) ."' WHERE id = '" . $id . "';";
		return $this->db()->query($sql);
	}
}

?>