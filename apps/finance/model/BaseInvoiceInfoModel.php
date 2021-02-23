<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseInvoiceInfoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-28 10:27:45
 *   @update	:
 *  -------------------------------------------------
 */
class BaseInvoiceInfoModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'base_invoice_info';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"自增ID",
"price"=>"价格",
"invoice_num"=>"发票号",
"title"=>"抬头",
"content"=>"内容",
"status"=>"状态:1未使用2已使用3已作废",
"create_user"=>"创建人",
"create_time"=>"创建时间",
"use_time"=>"使用时间",
"cancel_user"=>"作废 人",
"cancel_time"=>"作废时间",
"order_sn"=>"订单号",
"type"=>"类型 1客订单");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url BaseInvoiceInfoController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
        $innerjoin = "";
        if(SYS_SCOPE == 'zhanting'){
            $innerjoin = " inner join app_order.base_order_info oi on oi.order_sn = i.order_sn ";
        }

		//不要用*,修改为具体字段
		$sql = "SELECT i.* FROM `".$this->table()."` i {$innerjoin}";
		$str = '';
		if($where['title'] != "")
		{
			$str .= "i.`title` like \"%".addslashes($where['title'])."%\" AND ";
		}
		if(!empty($where['invoice_num']))
		{
			$str .= "i.`invoice_num`='".$where['invoice_num']."' AND ";
		}
		if(!empty($where['order_sn']))
		{
			$str .= "i.`order_sn`='".$where['order_sn']."' AND ";
		}
		if(!empty($where['status']))
		{
			$str .= "i.`status`=".$where['status']." AND ";
		}
		/*
		if(!empty($where['type']))
		{
			$str .= "i.`type`=".$where['type']." AND ";
		}*/
        if(!empty($where['price_end']))
        {
            $str .= "i.`price` <= ".$where['price_end']." AND ";
        }
        if(!empty($where['price_start']))
        {
            $str .= "i.`price` >= ".$where['price_start']." AND ";
        }
        //zt隐藏
        if(SYS_SCOPE == 'zhanting')
        {
            $str .= "oi.`hidden` <> 1 AND ";
        }
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY i.`id` DESC";
		//echo $sql;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

    public function getOrderjiage($goods_sn){
        $apimodel = new ApiOrderModel();
        $res = $apimodel->getOrderListBySn($goods_sn);

        if(!empty($res)){
            return $res['goods_amount'];
        }
        return  false;

    }

    public function getInvoiceNumEx($invoice_num){
        $sql = "SELECT `status` FROM `base_invoice_info`  WHERE `invoice_num`='$invoice_num'";
        return $this->db()->getOne($sql);
    }

    public function getInvoiceByInvoiceNum($invoice_num){
        $sql = "SELECT `status` FROM `base_invoice_info`  WHERE `invoice_num`='$invoice_num'";
        return $this->db()->getOne($sql);
    }
     //获取电子发票数据
    public function getdownloadElecGoods($where){
    	$sql=" SELECT v.invoice_amount,v.taxpayer_sn,v.invoice_title,o.id,o.order_sn,v.invoice_email,o.order_sn,d.goods_id,d.goods_sn,d.goods_price,d.favorable_price,d.favorable_status, g.cat_type1,g.num,g.cat_type,c.cat_type_name FROM app_order.app_order_invoice v,app_order.base_order_info o,app_order.app_order_details d LEFT JOIN warehouse_shipping.warehouse_goods g ON d.goods_id=g.goods_id LEFT JOIN front.base_style_info s ON d.goods_sn=s.style_sn LEFT JOIN front.app_cat_type c ON s.style_type=c.cat_type_id WHERE v.order_id=o.id AND o.id=d.order_id AND  d.`is_return`<>1 AND IF(d.favorable_status = 3,d.goods_price - d.favorable_price,d.goods_price)>0 ".$where['order_sn'];
    	return $this->db()->getAll($sql);
    }
}

?>