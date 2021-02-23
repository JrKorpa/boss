<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoHModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-21 21:31:17
 *   @update	:
 *  -------------------------------------------------
 */
class ExpressCheckModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'express_check';
		$this->pk='id';
		$this->_prefix='';
		$this->_dataObject = array("id"=>"序号","name"=>"文件名","oldname"=>"文件原名","path"=>"路径","option"=>"操作");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url WarehouseBillInfoHController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql=$this->getSql($where);//edit by zhangruiying
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	//add by zhangruiying根据查询条件生成SQL
	function getSql()
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
		if(isset($where['ids']) and $where['ids'] != "")
		{
			$str .= "`freight_no` in({$where['ids']}) AND ";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
		return $sql;

	}
	//add by zhangruiying 获取满足条件所有记录
	function getObjectList($where)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `ship_freight`";
		$str = '';
		if(isset($where['ids']) and $where['ids'] != "")
		{
			$where['ids']="'".str_replace(',',"','",$where['ids'])."'";

			$str .= "`freight_no` in({$where['ids']}) AND ";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
		$data = $this->db()->getAll($sql);
		return $data;
	}
	//获取快递单信息
	function check_freight_no($freight_no){
		$sql = 'SELECT * FROM `ship_freight` WHERE `freight_no` ='.$freight_no;
		return $this->db()->getRow($sql);
	}
}

?>