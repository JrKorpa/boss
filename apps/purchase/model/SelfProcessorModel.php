<?php
/**
 * 销售模块的数据模型（代替Processor/Api/api.php）
 *  -------------------------------------------------
 *   @file		: ProcessorModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class SelfProcessorModel extends SelfModel
{
    protected $db;
	function __construct ($strConn="")
	{
		parent::__construct($strConn);
	}		
   	/**
   	 * product_info表快速搜索
   	 * @param $field
   	 * @param $where
   	 * @param $type
   	 */
	function selectProductInfo($field,$where,$type=2){
	    return $this->select($field, $where,$type,"product_info");
	}
	
	/**
	 * 检验供应商$supplier_id1是否属于供应商$supplier_id2及关联供应商范围内
	 * @param int $supplier_id1
	 * @param int $supplier_id2
	 */
	function checkSupplierConnected($supplier_id1,$supplier_id2){
	    $sql = "SELECT count(*) from app_processor_group where group_id in (SELECT  group_id from app_processor_group where supplier_id={$supplier_id2}) and supplier_id={$supplier_id1}";
	    return $this->db()->getOne($sql);
	}
	
	
	
	public function getFactoryArr($supplier_id){
	    $sql="select 473 as supplier_id union select supplier_id from app_processor_group where group_id=(select group_id from app_processor_group where supplier_id={$supplier_id})";
		$rows=$this->db()->getAll($sql);
		$row=array();
		if(count($rows)==1){
			$row[0]=473;
			$row[1]=$supplier_id;
		}else{
			foreach ($rows as $v){
				$row[]=$v['supplier_id'];
			}
		}
		
		return $row;
		
	}
	
	public function getFactoryName($id){
		$sql="select name from app_processor_info where id=$id";
		return  $this->db()->getOne($sql);
	}
	
	

	//查询默认工厂
	public function getFactoryIdByStyle($style_sn){
	    $sql = "SELECT factory_id FROM front.rel_style_factory WHERE style_sn='{$style_sn}' AND is_factory=1";
	    return  $this->db()->getOne($sql);
	
	}
	

}

?>