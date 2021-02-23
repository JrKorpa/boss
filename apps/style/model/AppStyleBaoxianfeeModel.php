<?php
/**
 *  -------------------------------------------------
 *   @file		: AppStyleBaoxianfeeModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 11:19:47
 *   @update	:
 *  -------------------------------------------------
 */
class AppStyleBaoxianfeeModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_style_baoxianfee';
        $this->_dataObject = array("id"=>" ",
"min"=>"最小值",
"max"=>"最大值",
"price"=>"价格",
"status"=>"状态");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppStyleBaoxianfeeController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";
        if(isset($where['min']) && $where['min']!=''){
            $sql .=" AND `min` = {$where['min']} ";
        }
        if(isset($where['max']) && $where['max']!=''){
            $sql .=" AND `max` = {$where['max']} ";
        }
        if(isset($where['price_min']) && $where['price_min']!=''){
            $sql .=" AND `price` >= '{$where['price_min']}' ";
        }
        if(isset($where['price_max']) && $where['price_max']!=''){
            $sql .=" AND `price` <= '{$where['price_max']}' ";
        }
		if(isset($where['status']) && $where['status']!= ""){
            $sql .=" AND `status` = '{$where['status']}' ";
        }
		$data = $this->db(11)->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	/**
	 *	获取所有的
	 *	@url AppStyleBaoxianfeeController/search
	 */
	function getAllList ()
	{
		$sql = "SELECT `id`,`min`,`max`,`price`,`status` FROM `".$this->table()."` WHERE 1 ";
		return $this->db()->getAll($sql);
	}

    /**
     *  镶口获取保险费｛只针对于产品线：镶嵌类｝
     *  @url AppStyleBaoxianfeeController/search
     */
    function getPriceByXiangkou ($xiangkou)
    {
        $sql = "SELECT `price` FROM `".$this->table()."` WHERE {$xiangkou} >= min and {$xiangkou} <= max and `status` = 1";
        return $this->db()->getOne($sql);
    }

    /**
     *  检查是否有相同区间存在
     */
    function getQujianByMax ($where)
    {
        $sql = "SELECT `id`,`min`,`max`,`price`,`status` FROM `".$this->table()."` WHERE status=1 and ( `min` >= '{$where['min']}' OR `max` = '{$where['max']}' )";
        return $this->db()->getRow($sql);
    }
}

?>