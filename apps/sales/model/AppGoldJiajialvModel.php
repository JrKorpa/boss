<?php
/**
 *  -------------------------------------------------
 *   @file		: AppGoldJiajialvModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-08-17 13:57:53
 *   @update	:
 *  -------------------------------------------------
 */
class AppGoldJiajialvModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_gold_jiajialv';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"ID",
"gold_price"=>"黄金价格",
"jiajialv"=>"加价率",
"create_time"=>"创建时间",
"create_user"=>"创建用户",
"is_usable"=>"是否可用");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppGoldJiajialvController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
		if($where['create_user'] != "")
		{
			$str .= "`create_user` like \"".addslashes($where['create_user'])."%\" AND ";
		}
		if($where['is_usable'] !== '')
		{
			$str .= "`is_usable`='".$where['is_usable']."' AND ";
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
}

?>