<?php
/**
 *  -------------------------------------------------
 *   @file		: StoneBagEnterModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-03-23 14:24:36
 *   @update	:
 *  -------------------------------------------------
 */
class StoneBagEnterModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'stone';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"ID",
"dia_package"=>"石包",
"purchase_price"=>"每卡采购价格(元)",
"status"=>"状态",
"sup_id"=>" ",
"sup_name"=>"供应商",
"specification"=>"规格",
"color"=>"颜色",
"neatness"=>"净度",
"cut"=>"切工",
"symmetry"=>"对称",
"polishing"=>"抛光",
"fluorescence"=>"荧光",
"lose_efficacy_time"=>"失效时间",
"lose_efficacy_cause"=>"失效原因",
"lose_efficacy_user"=>"失效操作人");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url StoneController/search
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