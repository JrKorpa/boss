<?php
/**
 *  -------------------------------------------------
 *   @file		: DiyXiangkouConfigModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-03 12:27:26
 *   @update	:
 *  -------------------------------------------------
 */
class DiyXiangkouConfigModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'diy_xiangkou_config';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array(
            "id"=>"自增ID",
            "style_sn"=>"款号",
            "xiangkou"=>"镶口",
            "carat_lower_limit"=>"石重下限",
            "carat_upper_limit"=>"石重上限"            
        );
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1=1";
		if(!empty($where['style_sn'])){
		    $sql .=" AND style_sn='{$where['style_sn']}'";
		}
		if(isset($where['xiangkou_lower_limit']) && $where['xiangkou_lower_limit']!=''){
		    $sql .=" AND xiangkou>={$where['xiangkou_lower_limit']}";
		}
		if(isset($where['xiangkou_upper_limit']) && $where['xiangkou_upper_limit']!=''){
		    $sql .=" AND xiangkou<={$where['xiangkou_upper_limit']}";
		}
		if(isset($where['carat_lower_limit']) && $where['carat_lower_limit']!=''){
		    $sql .=" AND (carat_lower_limit>={$where['xiangkou_upper_limit']})";
		}
		if(isset($where['carat_upper_limit']) && $where['carat_upper_limit']!=''){
		    $sql .=" AND (carat_upper_limit<={$where['carat_upper_limit']})";
		}

		$sql .= " ORDER BY `id` DESC";
		//echo $sql;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}  

	//批量删除
	public function multi_delete($ids){	     
	    if(empty($ids) || !is_array($ids)){
	        return false;
	    }
        $ids = implode(',',$ids);
        $sql = "delete from ".$this->table()." where id in({$ids})";	        
	    return $this->db()->query($sql);
	}
	/**
	 * 单条查询
	 */
	public function getDiyXiangkouConfig($fieds="*",$where=""){
	   
	    $sql = "select {$fieds} from ".$this->table()." where {$where}";
	    return $this->db()->getRow($sql);
	}
    
}

?>