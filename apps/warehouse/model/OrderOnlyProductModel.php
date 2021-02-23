<?php
/**
 *  -------------------------------------------------
 *   @file		: OrderFqcConfModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-02 12:12:53
 *   @update	:
 *  -------------------------------------------------
 */
class OrderOnlyProductModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'warehouse_bill';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"",
        		"goods_id"=>"货号",
        		"goods_sn"=>"款号",
        		"is_on_sale"=>"货品状态（见数据字典，货品状态）",
        		"num"=>"",
        		"warehouse"=>"所在仓库",
        		"company"=>"所在公司",
        		"company_id"=>"所在公司ID",
        		"storage_mode"=>"入库方式",
        		"product_type"=>"产品线",
        		"cat_type"=>"款式分类",
        		"caizhi"=>"主成色(材质)",
        		"jinzhong"=>"主成色重（金重）",
        		"shoucun" => "手寸",
        		"jinhao"=>"金耗",
        );
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url OrderFqcConfController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "select id from  `".$this->table()."`";
		$str = '';
		if($where['order_sn']!='')
		{
			$str .= "order_sn in (".$where['order_sn'].") AND ";
		}
		if($where['to_company_id'] !== "")
		{
			$str .= "to_company_id = '".$where['to_company_id']."' AND ";
		} 
		if($where['to_warehouse_id'] !== "")
		{
			$str .= "to_warehouse_id = '".$where['to_warehouse_id']."' AND ";
		}
		if($where['bill_status']!='')
		{
			$str .= "bill_status in (".$where['bill_status'].") AND ";
		}
		
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}	
		$sql .= " ORDER BY `id` DESC";
		//echo $sql;exit;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		//print_r($data);exit;
		foreach ($data['data'] as $key=>$v){
			$data['data'][$key]=$this->getOnlyProduct($v['id']);
		}
		
		return $data;
	}
	
	
	function getOnlyProduct($id){
		$sql = "SELECT b.order_sn ,
                        g.goods_id , 
                        g.goods_sn  ,
                        b.bill_no,
                        wg.`warehouse` ,
                       case `wg`.`is_on_sale`
				       when 1 then '收货中' 
                       when 2 then '库存'
                       when 3 then '已销售'
                       when 4 then '盘点中'
                       when 5 then '调拨中'
                       when 6 then '损益中'
                       when 7 then '已报损'
                       when 8 then '返厂中'
                       when 9 then '已返厂'
                       when 10 then '销售中'
                       when 11 then '退货中'
                       when 12 then '作废'
					   end as is_on_sale,					   
					   case `i`.`order_pay_status`
					   when 1 then '未付款'
					   when 2 then '部分付款'
					   when 3 then '已付款'
					   when 4 then '财务备案'
					   end as pay_status,i.hidden
					   FROM `warehouse_bill_goods` as `g`  
                       left join `warehouse_bill` as `b` on `g`.`bill_id`=`b`.`id` 
                       left join `warehouse_goods` as `wg` on `wg`.`goods_id` = `g`.`goods_id`
				       left join `app_order`.`base_order_info` as `i` on `i`.`order_sn`=`b`.`order_sn` 
		               where g.bill_id =$id";
		              $data = $this->db()->getAll($sql);
		              return $data;
	}
	
}

?>