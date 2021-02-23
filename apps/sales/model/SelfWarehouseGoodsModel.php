<?php
/**
 * 仓库数据模块的模型（代替WareHouse/Api/api.php）
 *  -------------------------------------------------
 *   @file		: WareHouseModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class SelfWarehouseGoodsModel extends  SelfModel
{
    protected $db;
	function __construct ($strConn="")
	{
		$this->db = DB::cn($strConn);
	}
	public function db(){
	    return $this->db;
	}
	
	//获取货品状态
   public function getGoodsArr($goods_id){
  	 $sql="select `is_on_sale`,`goods_sn`,`cat_type1`,`zhengshuhao` from `warehouse_goods` where `goods_id`='$goods_id'";
  	 return $this->db->getRow($sql);
  	 
   }

   //获取货品状态
   public function getGoodsArrOnLine($goods_id){
     $sql="select `is_on_sale`,`goods_sn`,`cat_type1`,`zhengshuhao` from `warehouse_goods` where `goods_id`='$goods_id' and company_id = 58 and warehouse_id in(698,323,699)";
     return $this->db->getRow($sql);//可用仓库库存有：淘宝B店、淘宝裸钻库、京东SOP
     
   }

   //根据证书号获取证书类型
   public function getCertByZhengshuhao($zhengshuhao){
     $sql="select `zhengshuleibie` from `warehouse_goods` where `zhengshuhao`='$zhengshuhao'";
     return $this->db->getRow($sql);
     
   }

   //获取入货公司名称ID
   public function getToCompanyId($goods_id){
   	 $sql="select b.to_company_id from `warehouse_bill` as b left join `warehouse_bill_goods` as bg on bg.bill_id = b.id where bg.goods_id='{$goods_id}' and b.bill_status =1 limit 1";
   	 return $this->db->getOne($sql);
   }
   
   
   
   //仓储管理->商品列表里的货号绑定订单号
   public function updateOrderGoodsId($goods_id,$order_goods_id){
   	$sql="UPDATE warehouse_goods SET order_goods_id = '$order_goods_id'  WHERE goods_id = '$goods_id' ";
   	return $res = $this->db->query($sql);
   }
   
   public function getWarehouseGoodsRow($fileds="*",$where){
      $sql = "select {$fileds} from warehouse_goods where {$where}";
      return  $this->db->getRow($sql);  
   }
	
  /*
    *通过货品ID判断货品是不是现货
    *
    */
    public function isExistsByGoodsId($goods_id){
    	$sql ="select id from warehouse_shipping.warehouse_goods where goods_id='".$goods_id."'";
    	return $this->db()->getOne($sql);

    }
   //获取批发客户
    public function getWholesaleArr($wholesale_id){
    	$sql ="select wholesale_name from warehouse_shipping.jxc_wholesale where wholesale_id='".$wholesale_id."'";
    	return $this->db()->getOne($sql);
    }
    
    
    //获取所有批发客户
    public function getWholesaleAll(){
    	$sql ="select * from warehouse_shipping.jxc_wholesale ";
    	return $this->db()->getAll($sql);
    }
    
    public function getVipPickOrderDetail($where){
        $sql = "select * from vip_pick_order_details where 1=1";
        if(!empty($where['order_id'])){
            $sql .=" AND order_id={$where['order_id']}";
        }
        return $this->db()->getRow($sql);
    }
    
    public function updateVipPickOrderDetail($data,$where){
        $sql = $this->updateSql("vip_pick_order_details", $data, $where);
        return $this->db()->query($sql);
    }

}

?>