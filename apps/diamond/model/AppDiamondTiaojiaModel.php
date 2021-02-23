<?php
/**
 *  -------------------------------------------------
 *   @file		: AppDiamondTiaojiaModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-05-10 17:32:49
 *   @update	:
 *  -------------------------------------------------
 */
class AppDiamondTiaojiaModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_diamond_tiaojia';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"address1"=>"区域1",
"address2"=>"区域2",
"address3"=>"区域3",
"dep_id1"=>"城市1",
"dep_id2"=>"城市2",
"dep_id3"=>"城市3",
"zhekou_31"=>"0.3-0.49",
"zhekou_32"=>"0.3-0.49",
"zhekou_33"=>"0.3-0.49",
"zhekou_51"=>"0.5-0.79",
"zhekou_52"=>"0.5-0.79",
"zhekou_53"=>"0.5-0.79",
"zhekou_81"=>"0.8-0.99",
"zhekou_82"=>"0.8-0.99",
"zhekou_83"=>"0.8-0.99",
"zhekou_01"=>"1-1.49",
"zhekou_02"=>"1-1.49",
"zhekou_03"=>"1-1.49",
"zhekou_11"=>"1.5-1.99",
"zhekou_12"=>"1.5-1.99",
"zhekou_13"=>"1.5-1.99",
"zhekou_21"=>"2以上",
"zhekou_22"=>"2以上",
"zhekou_23"=>"2以上");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppDiamondTiaojiaController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
        $data=array();
		//不要用*,修改为具体字段
		//$sql = "SELECT * FROM `".$this->table()."`";
		//$str = '';
//		if($where['xxx'] != "")
//		{
//			$str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
//		}
//		if(!empty($where['xx']))
//		{
//			$str .= "`xx`='".$where['xx']."' AND ";
//		}
		//if($str)
		//{
		//	$str = rtrim($str,"AND ");//这个空格很重要
		//	$sql .=" WHERE ".$str;
		//}
		//$sql .= " ORDER BY `id` DESC";
		//$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	/**
	 *	getAllList，取所有
	 *
	 *	@url AppDiamondTiaojiaController/getAllList
	 */
	function getAllList ()
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC LIMIT 1";
		$data = $this->db()->getRow($sql);
		return $data;
	}
}

?>