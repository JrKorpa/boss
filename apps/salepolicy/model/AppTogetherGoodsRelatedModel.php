<?php
/**
 *  -------------------------------------------------
 *   @file		: AppTogetherGoodsRelatedModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-16 09:40:44
 *   @update	:
 *  -------------------------------------------------
 */
class AppTogetherGoodsRelatedModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_together_goods_related';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"自增id",
"together_id"=>"打包策略",
"goods_id"=>"商品货号",
"chengben"=>"成本价",
"sale_price"=>"销售价",
"jiajia"=>"加价率",
"sta_value"=>"固定值",
"chengben_compare"=>"当可销售商品表中的成本发生变化 向本字段写入改变的陈本价格",
"isXianhuo"=>"现货状态0是期货1是现货",
"create_time"=>"创建时间",
"create_user"=>"创建人",
"check_time"=>"审核时间",
"check_user"=>"审核人",
"status"=>"状态:1保存2申请3审核通过4未通过5取消",
"is_delete"=>"删除 1未删除 2已删除");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppTogetherGoodsRelatedController/search
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
}

?>