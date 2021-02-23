<?php
/**
 *  -------------------------------------------------
 *   @file		: JxsOrderDetailModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-07-16 23:51:55
 *   @update	:
 *  -------------------------------------------------
 */
class JxsOrderDetailModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'jxs_order_detail';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"主键",
"order_id"=>"订单号",
"goods_id"=>"货号",
"trading_price"=>"商品最终成交价格",
"cost_price"=>"商品成本价格",
"cart"=>"石重",
"cut"=>"切工",
"clarity"=>"净度",
"color"=>"颜色",
"jinzhong"=>"金重",
"goods_type"=>"商品类型lz:裸钻",
"cat_type"=>"款式分类",
"product_type"=>"产品线",
"xiangkou"=>"镶口",
"caizhi" => "材质",
"jinse"=>"金色",
"profit_type"=>"利润类型",
"calc_profit"=>"利润额"        
        );
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url JxsOrderDetailController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";

		$str = '';
		if(!empty($where['_id']))
		{
			$str .="`order_id`='".$where['_id']."' AND ";	
		}
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

    function findByOrderID($order_ids){
        $order_id_str = implode(',',$order_ids);
        $sql = "select * from {$this->table()} where order_id IN ({$order_id_str}) ORDER BY order_id DESC,id DESC";
        return  $this->db()->getAll($sql);
    }
}

?>