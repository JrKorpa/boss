<?php
/**
 *  -------------------------------------------------
 *   @file		: VipPickOrderDetailsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @date		: 2016-06-26
 *   @update	:
 *  -------------------------------------------------
 */
class VipPickOrderDetailsModel extends Model
{

	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'vip_pick_order_details';
      	$this->pk='art_no';
		$this->_prefix='';
	    $this->_dataObject = array(
            'pick_no'=> 'VIP货号' ,
            'barcode'=> 'VIP商品条码' ,
            'order_id'=> 'BOSS订单ID',
            'order_sn'=> 'BOSS订单号' ,
            'goods_id'=>'BOSS货号' ,
            'order_detail_id'=>'BOSS订单商品明细ID' ,
            'delivery_status'=> '发货状态'   
	    );        
		parent::__construct($id,$strConn);
	}	
    
    /**
     * 本地数据库拣货单列表分页
     * @param unknown $where
     * @param unknown $page
     * @param number $pageSize
     * @param string $useCache
     * @return unknown
     */
	function pageList($where,$page,$pageSize=10,$useCache=true){
	    $sql = "SELECT a.*,b.goods_sn as style_sn,b.send_good_status,b.is_zp,b.goods_name,d.out_flag,e.is_print_tihuo,e.order_status,e.apply_close,'' as product_name     
	        FROM ".$this->table()." a left join app_order.app_order_details b on a.order_detail_id=b.id 
	            left join vip_delivery_details c on b.id=c.order_detail_id 
	            left join vip_delivery d on c.storage_no=d.storage_no 
	            left join app_order.base_order_info e on b.order_id=e.id 
	            where 1=1";

		
		if(isset($where['out_flag'])){
		    $sql .=" AND a.delivery_status='{$where['out_flag']}'";
		}
		
		if(!empty($where['st_create_time'])){
		    $sql .=" AND a.create_time>='{$where['st_create_time']}'";
		}
		if(!empty($where['et_create_time'])){
		    $sql .=" AND a.create_time<='{$where['et_create_time']} 23:59:59'";
		}
		if(!empty($where['warehouse'])){
		    $sql .=" AND a.warehouse='{$where['warehouse']}'";
		}		
		if(isset($where['is_print_tihuo']) && $where['is_print_tihuo']!=''){
		    $sql .=" AND e.is_print_tihuo={$where['is_print_tihuo']}";
		}
		if(isset($where['send_good_status']) && $where['send_good_status']!=''){
		    $sql .=" AND e.send_good_status={$where['send_good_status']}";
		}
		if(isset($where['is_stock_goods']) && $where['is_stock_goods']!=''){
		    $sql .=" AND b.is_stock_goods={$where['is_stock_goods']}";
		}
		if(isset($where['order_status']) && $where['order_status']!=''){
		    $sql .=" AND e.order_status={$where['order_status']}";
		}else{
		    $sql .=" AND e.order_status in(1,2)";
		}
		if(!empty($where['order_sn'])){
		    if(is_array($where['order_sn'])){
		        $where['order_sn'] = "'".implode("','",$where['order_sn'])."'";
		        $sql .=" AND a.order_sn in({$where['order_sn']})";
		    }else{
		        $sql .=" AND a.order_sn ='{$where['order_sn']}'";
		    }
		}
		
		if(isset($where['is_create_delivery']) && $where['is_create_delivery']!=''){
		    if($where['is_create_delivery']==1){
		        $sql .=" AND c.storage_no is not null";
		    }else{
		        $sql .=" AND c.storage_no is null";
		    }
		}
		/*
		if(isset($where['out_flag']) && $where['out_flag']!=''){
		    if($where['out_flag']==1){
		       $sql .=" AND d.out_flag=1";
		    }else if($where['out_flag']==0){       
		       $sql .=" AND (c.storage_no is null or (c.storage_no is not null and d.out_flag=0))";
		    }
		}*/
		if(!empty($where['po_no'])){
		    if(is_array($where['po_no'])){
		        $where['po_no'] = "'".implode("','",$where['po_no'])."'";
		        $sql .=" AND a.po_no in({$where['po_no']})";
		    }else{
		        $sql .=" AND a.po_no ='{$where['po_no']}'";
		    }
		}
		if(!empty($where['pick_no'])){
		    if(is_array($where['pick_no'])){		        
		        $where['pick_no'] = "'".implode("','",$where['pick_no'])."'";
		        $sql .=" AND a.pick_no in({$where['pick_no']})";
		    }else{
		        $sql .=" AND a.pick_no ='{$where['pick_no']}'";
		    }
		}
		if(!empty($where['barcode'])){
		    if(is_array($where['barcode'])){
		        $where['barcode'] = "'".implode("','",$where['barcode'])."'";
		        $sql .=" AND a.barcode in({$where['barcode']})";
		    }else{
		        $sql .=" AND a.barcode ='{$where['barcode']}'";
		    }
		}
		$sql .= " order by a.create_time desc";
		//echo $sql;
		$data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
		
		foreach ($data['data'] as &$vo){
		    $sql = "select * from vip_pick_details where barcode='{$vo['barcode']}' and pick_no='{$vo['pick_no']}'";
		    $row = $this->db()->getRow($sql);
		    if(!empty($row)){
		        $vo['product_name'] = $row['product_name'];
		        $vo['art_no'] = $row['art_no'];
		        $vo['size'] = $row['size'];
		    }
		}
		//print_r($data);
		return $data;
	
	}
	
	function getOrderDetailsList($where,$page,$pageSize=10,$useCache=true){
	    $sql = "SELECT a.*,b.goods_sn as style_sn,b.send_good_status,b.is_zp,b.goods_id as bind_goods_id,b.goods_name FROM ".$this->table()." a inner join app_order.app_order_details b on a.order_detail_id=b.id inner join app_order.base_order_info d on b.order_id=d.id inner join shipping.ship_freight c on a.order_sn=c.order_no and c.is_deleted=0 where 1=1";
	    
	    if(!empty($where['order_status'])){
            $sql .=" AND d.order_status in({$where['order_status']})";
	    }
	    if(!empty($where['delivery_no'])){
	        $sql .=" AND c.freight_no='{$where['delivery_no']}'";
	    }
	    if(!empty($where['pick_no'])){
	        $sql .=" AND a.pick_no='{$where['pick_no']}'";
	    }
	    if(!empty($where['barcode'])){
	        $sql .=" AND a.barcode='{$where['barcode']}'";
	    }
	    if(isset($where['delivery_status'])){
	        $sql .=" AND a.delivery_status={$where['delivery_status']}";
	    }
	    return $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
	}
	
}