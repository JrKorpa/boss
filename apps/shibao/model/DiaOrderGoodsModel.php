<?php
/**
 *  -------------------------------------------------
 *   @file		: DiaOrderGoodsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: lyanhong <462166282@qq.com>
 *   @date		: 2015-03-16 17:29:10
 *   @update	:
 *  -------------------------------------------------
 */
class DiaOrderGoodsModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'dia_order_goods';
		$this->pk='og_id';
		$this->_prefix='';
        $this->_dataObject = array(
			"og_id"=>" ",
			"order_id"=>" ",
			"order_type"=>" ",
			"shibao"=>" ",
			"zhengshuhao"=>" ",
			"zhong"=>" ",
			"yanse"=>" ",
			"jingdu"=>" ",
			"qiegong"=>" ",
			"duichen"=>" ",
			"paoguang"=>" ",
			"yingguang"=>" ",
			"num"=>"石包总数",
			"zongzhong"=>"石包总重量",
			"caigouchengben"=>"每卡采购价格",
			"xiaoshouchengben"=>"每卡销售价格",
			"add_time"=>" ");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url DiaOrderGoodsController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT og_id,shibao,num,zongzhong,caigouchengben,xiaoshouchengben,zhengshuhao,zhong,yanse,jingdu,qiegong,duichen,paoguang,yingguang FROM `".$this->table()."` where ";

		if(!empty($where['_id']))
		{
			$sql .="`order_id`='".$where['_id']."'  ";	
		}

		$sql .= " ORDER BY `og_id` ASC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	/***************************************************************************
	fun:getDetailByOrderId
	description:根据单据号获取明细信息
	****************************************************************************/
	public function getDetailByOrderId($id)
	{
 		$sql = "select og_id,shibao,num,zongzhong,caigouchengben,xiaoshouchengben,zhengshuhao,zhong,yanse,jingdu,qiegong,duichen,paoguang,yingguang,order_id from ".$this->table()." where order_id='{$id}' order by og_id asc";
		return $this->db()->getAll($sql);
	}

}

?>