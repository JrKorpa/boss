<?php
/**
 *  -------------------------------------------------
 *   @file		: AppSalepolicyTogetherGoodsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-14 18:55:25
 *   @update	:
 *  -------------------------------------------------
 */
class AppSalepolicyTogetherGoodsModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_salepolicy_together_goods';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"自增id",
"together_name"=>"打包策略名称",
"create_user"=>"创建人",
"create_time"=>"创建时间",
"is_split"=>"是否可以拆分，1：否；2：是",
"status"=>"是否有效，1：有效；2：无效");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppSalepolicyTogetherGoodsController/search
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
//		if(!empty($where['xx']))
//		{
//			$str .= "`xx`='".$where['xx']."' AND ";
//		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
    
    
    
    /**
     * 政策和打包策略关联查询
     * @param type $where
     * @param type $page
     * @param type $pageSize
     * @param type $useCache
     * @return type
     */
    function pageTogetherList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT `stg`.* FROM `".$this->table()."` as `stg`,`app_together_policey_related` as `tpr` WHERE `tpr`.`together_id`=`stg`.`id`";
		if($where['policy_id'] != "")
		{
			$sql .= " AND `tpr`.`policy_id` = {$where['policy_id']}";
		}
		$sql .= " ORDER BY `stg`.`id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
    
    
    /**
     * 下拉列表显示数据
     * @return type
     */
    function getTogetherList() {
        $sql = "SELECT `id`,`together_name` FROM `".$this->table()."` WHERE `status`=1";
        $data = $this->db()->getAll($sql);
        return $data;
    }
    
    /**
     * 重复验证策略名称
     * @return boole
     */
    function getTogetherName($where) {
        $sql = "SELECT count(*) FROM `".$this->table()."` WHERE `together_name` = '{$where}'";
        $data = $this->db()->getOne($sql);
        return $data;
    }
}

?>