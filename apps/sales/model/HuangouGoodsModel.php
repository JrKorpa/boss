<?php
/**
 *  -------------------------------------------------
 *   @file		: HuangouGoodsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2018-01-05 11:06:19
 *   @update	:
 *  -------------------------------------------------
 */
class HuangouGoodsModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'huangou_goods';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
		"channel_id"=>"渠道ID",
		"style_sn"=>"款号",
		"label_price"=>"标签价",
		"sale_price"=>"换购价",
		"create_user"=>" ",
		"create_time"=>" ",
		"update_user"=>" ",
		"update_time"=>" ");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url HuangouGoodsController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT h.*,s.channel_name FROM `".$this->table()."` h left join cuteframe.sales_channels s on h.channel_id=s.id ";
		$str = '';
		if($where['channel_id'] != "")
		{
			$str .= "h.`channel_id` ='".$where['channel_id']."' AND ";
		}
		if($where['style_sn'] != "")
		{
			$str .= "h.`style_sn` ='".$where['style_sn']."' AND ";
		}
		if($where['status'] != "")
		{
			$str .= "h.`status` =".$where['status']." AND ";
		}		
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY h.`id` DESC";
		 
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	/***
	 * 查询换购商品列表，默认5000行
	 */
	function getHuangouGoodsList ($where,$fields="*")
	{
	    //不要用*,修改为具体字段
		$sql = "SELECT {$fields} FROM `".$this->table()."` h ";
		$str = '';
		if($where['status'] != "")
		{
			$str .= "h.`status` =".$where['status']." AND ";
		}
		if($where['channel_id'] != "")
		{
		    $str .= "h.`channel_id` =".$where['channel_id']." AND ";
		}		
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY h.`id` DESC limit 5000";		 
		$data = $this->db()->getAll($sql);
		return $data;
	}
	function getHuangouGoodsInfo($where){
	    $sql = "select h.*,s.style_name from ".$this->table()." h inner join front.base_style_info s on h.style_sn=s.style_sn where 1=1";
	    if(!empty($where['id'])){
	        $sql .=" AND h.id=".$where['id'];
	    }
	    if(isset($where['status']) && $where['status']!=""){
	        if($where["status"]==1){
	           $sql .=" AND h.status=1 and s.check_status=3";
	        }else{
	           $sql .=" AND h.status=0";
	        }
	    }
	    return $this->db()->getRow($sql);
	    
	}
    function getStyleInfo($style_sn){
        $sql="select * from front.base_style_info where style_sn='{$style_sn}'";
        $row=$this->db()->getRow($sql);
        return $row;
    }

}

?>