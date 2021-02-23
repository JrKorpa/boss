<?php
/**
 *  -------------------------------------------------
 *   @file		: AppJinsunModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 16:42:07
 *   @update	:
 *  -------------------------------------------------
 */
class AppJinsunModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_jinsun';
		$this->_prefix ='s';
        $this->_dataObject = array("s_id"=>" ",
"price_type"=>"1为男戒;2为女戒;3为情侣男戒;4为情侣女戒",
"material_id"=>"材质",
"lv"=>"价格");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url MessageController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";
        if(isset($where['price_type']) && !empty($where['price_type'])){
            $sql .=" AND price_type = '{$where['price_type']}' ";
        }
        if(isset($where['material_id']) && !empty($where['material_id'])){
            $sql .=" AND material_id = '{$where['material_id']}' ";
        }
		if(isset($where['jinsun_status']) && $where['jinsun_status']!=''){
            $sql .=" AND jinsun_status = '{$where['jinsun_status']}' ";
        }
		$sql .= " ORDER BY s_id DESC";
		$data = $this->db(11)->getPageList($sql,array(),$page, $pageSize,$useCache);
		//var_dump($data,66);exit;
		return $data;
	}

	//判断是否存在有值
	function getNumList ($where)
	{
        if(!empty($where['price_type']) && !empty($where['material_id'])){
			$sql = "SELECT count(*) FROM `".$this->table()."` WHERE 1 ";
            //$sql .=" AND price_type = '{$where['price_type']}' AND material_id = '{$where['material_id']}'";
            $sql .=" AND material_id = '{$where['material_id']}'";
			$sql .= " ORDER BY s_id DESC";
		    $data = $this->db()->getOne($sql);
        }else{
			$data='';
		}
		return $data;
	}
}

?>