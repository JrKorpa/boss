<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseLzDiscountConfigModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-19 15:37:36
 *   @update	:
 *  -------------------------------------------------
 */
class BaseLzDiscountConfigModel extends Model
{
    
    function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'base_lz_discount_config';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"自增ID",
"user_id"=>"管理员ID[user表id]",
"type"=>"类型范围 0未设定 1.小于50分 2.小于1克拉 3.大于1克拉",
"zhekou"=>"折扣",
"enabled"=>"是否可用 1可用 0停用");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url BaseLzDiscountConfigController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT `id`,`user_id`,`type`,`zhekou`,`enabled` FROM `".$this->table()."`";
		$str = '';
        if(!empty($where['user_id'])){
            //$str .= "`user_id` =".$where['user_id']." AND ";
            $str .= "`user_id` in(".implode(',', $where['user_id']).") AND ";
        }
        if(!empty($where['type'])){
            $str .= "`type` = ".$where['type']." AND ";
        }
        if($where['enabled']!=''){
            $str .= "`enabled` = ".$where['enabled']." AND ";
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
    
    //用户密码授权的时的分页
    	/**
	 *	pageList，分页列表
	 *
	 *	@url AppLzDiscountGrantController/search
	 */
	function GrantpageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//读的裸钻折扣管理的用户数据
		$sql = "SELECT `user_id`,`zhekou`  FROM `".$this->table()."`";
		$str = '';

		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		
        $sql .= " GROUP BY `user_id`";
        $sql .= " ORDER BY `id` DESC";
        //echo $
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
    
    //查看此用户裸钻数据是否存在
    function getDiscountByWhere($where) {
        $sql = "SELECT `id`,`user_id`,`zhekou`,`type` FROM `".$this->table()."`";
		$str = '';
        if(!empty($where['user_id'])){
            $str .= "`user_id` =".$where['user_id']." AND ";
        }
        
        if(!empty($where['type'])){
            $str .= "`type` = ".$where['type']." AND ";
        }
        
        if(!empty($where['id'])){
            $str .= " `id` = ".$where['id']." AND ";
        }
        if(isset($where['enabled']) && $where['enabled']!=''){
            $str .= " `enabled` = ".$where['enabled']." AND ";
        }
        if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
        return $this->db()->getAll($sql);
    }
}

?>