<?php
/**
 *  -------------------------------------------------
 *   @file		: AppStyleFeeModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-12 17:02:54
 *   @update	:
 *  -------------------------------------------------
 */
class AppStyleFeeModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_style_fee';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"style_id"=>"款id",
"fee_type"=>"费用类型：1材质费，2超石工费，3表面工艺",
"price"=>"费用",
"status"=>"状态：1启用2停用",
"check_user"=>"创建人",
"check_time"=>"创建时间",
"cancel_time"=>"停用时间");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppStyleFeeController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
//		if($where['xxx'] != "")
//		{
//			$str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
//		}
		if(!empty($where['style_id']))
		{
			$str .= "`style_id`='".$where['style_id']."' AND ";
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
	
	//查询款式的3种工费   表面工艺费   超时费  基础工费 
	function  getStyleFee($style_id){
		$sql = "SELECT * FROM `".$this->table()."` WHERE  `style_id` ={$style_id}";
		return $res = $this->db()->getAll($sql);
	}
    function getStylesFees($style_ids){
        $sql = "SELECT * FROM `".$this->table()."` WHERE  `style_id` in ('{$style_ids}')";
        return $res = $this->db()->getAll($sql);
    }
    
    
    /**
     * 检查同款同费用类型的记录是否重复
     * @param type $style_id
     * @param type $fee_type
     * @return boolean
     */
    function existFeeType($style_id,$fee_type) {
        if($style_id < 1 || $fee_type < 1){
            return 3;
        }
        $sql = "SELECT COUNT(*) FROM `".$this->table()."` WHERE `style_id`=$style_id AND `fee_type`=$fee_type";
        return $this->db()->getOne($sql);
    }
}

?>