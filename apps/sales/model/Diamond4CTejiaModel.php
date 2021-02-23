<?php
/**
 * 裸钻4C特价MODEL
 *  -------------------------------------------------
 *   @file		: Diamond4CTejiaModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: gaopeng
 *   @date		: 2015-09-15 21:17:07
 *   @update	:
 *  -------------------------------------------------
 */
class Diamond4CTejiaModel extends Model
{
	
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'diamond_4c_tejia';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array(
        );
		parent::__construct($id,$strConn);
	}
	/**
	 * 裸钻4C特价列表搜索(不含分页)
	 * @param unknown $where
	 */
	public function getList($where){
	   $today = date("Y-m-d H:i:s");
	   $sql = "select * from ".$this->table()." where (end_time is null or end_time>'{$today}')"; 
	   
	   if(!empty($where['city'])){
	       if(is_array($where['city'])){
	           $city = implode("','",$where['city']);
	           $sql .=" AND city in ('{$city}')";
	       }else{
	           $city = trim($where['city']);
	           $sql .=" AND city = '{$city}'";
	       }	       
	   }
	   $result = $this->db()->getALL($sql);
	   return $result; 
	}
	
	public function getCityList(){
	    $sql = "select DISTINCT city from ".$this->table();
	    $result = $this->db()->getALL($sql);
	    return $result;
	}
	

    
    
}	

?>