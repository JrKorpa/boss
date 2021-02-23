<?php
/**
 *  -------------------------------------------------
 *   @file		: AppStyleTsydPriceModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-05-10 00:02:24
 *   @update	:
 *  -------------------------------------------------
 */
class AppStyleTsydPriceModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_style_tsyd_price';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"自增",
"style_sn"=>"款号",
"style_name"=>"款式名称",
"work"=>"工艺",
"carat"=>"钻石重量",
"xiangkou_min"=>"镶口最小",
"xiangkou_max"=>"镶口最大",
"k_weight"=>"约18K金重",
"pt_weight"=>"约Pt950金重",
"k_price"=>"18K一对托定制价",
"pt_price"=>"PT950一对定制托价",
"jumpto"=>"跳转地址",
"pic"=>"图片地址",
"group_sn"=>"成组");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppStyleTsydPriceController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
		if($where['style_sn'] != "")
		{
			$str .= " `style_sn` = '".$where['style_sn']."' AND ";
		}
		if(!empty($where['style_name']))
		{
			$str .= " `style_name`='".addslashes($where['style_name'])."' AND ";
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