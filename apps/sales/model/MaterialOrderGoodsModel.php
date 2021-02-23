<?php
/**
 *  -------------------------------------------------
 *   @file		: MaterialBillGoodsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2018-01-18 14:00:47
 *   @update	:
 *  -------------------------------------------------
 */
class MaterialOrderGoodsModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'material_order_goods';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"ID",
            "bill_id"=>"单据ID",
            "goods_sn"=>"货品编码",          
            "goods_num"=>"数量",
            "goods_price"=>"成本价",
                   
        );
		parent::__construct($id,$strConn);
	}    
	/**
	 *	pageList，分页列表
	 *
	 *	@url MaterialBillController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true){
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
		if(!empty($where['bill_id'])){   
		    if(is_array($where['bill_id'])){
		        $where['bill_id'] = implode(",",$where['bill_id']);
		        $str .= "`bill_id` in ({$where['bill_id']}) AND ";
		    }else{
			    $str .= "`bill_id` = {$where['bill_id']} AND ";
		    }
		}
		
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
		//echo $sql;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
    /**
     * 根据指定条件查询总数
     * @param unknown $where
     */
	function getCount($where){
	     $sql = "select count(*) from material_order_goods where 1=1";
	     if(!empty($where['bill_id'])){
	         $sql .=" AND bill_id = {$where['bill_id']}";
	     }
	     if(!empty($where['goods_sn'])){
	         $sql .=" AND goods_sn = '{$where['goods_sn']}'";
	     }
	     return $this->db()->getOne($sql);	     
	}
	
}

?>