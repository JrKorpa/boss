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
class WarehouseModel extends SelfModel
{
    protected $db;
	function __construct ($strConn="")
	{
		parent::__construct($strConn);
	}	
	public function getAllWarehouse(){
	    $sql = "SELECT `id`,`name` FROM `warehouse` WHERE `is_delete` = '1'"; //有效仓库
	    return $this->db->getAll($sql);
	}
	//获取库存中的所有主成色
	public function getChuchengseList(){
	    $sql = "SELECT distinct `caizhi` FROM `warehouse_goods` WHERE is_on_sale=2"; //有效主成色
	    return $this->db->getAll($sql);
	     
	}
    /**
     * 通过货号绑定\解绑货品
     * @return json
     */
    public function BindGoodsInfoByGoodsId($goods_id,$order_goods_id,$bind_type=1) {
    	
            $set = '';
            $where = '';
            if (empty($goods_id)||empty($order_goods_id)) {
            	return false;
            }
            if($bind_type==1){
              $set .= " `order_goods_id` = '" . $order_goods_id . "' ";
              $where .= " `goods_id` = '" . $goods_id . "' ";
              $sql = "update `warehouse_goods` set " . $set . "  WHERE " . $where . " and is_on_sale = 2 ";
            }elseif($bind_type==2){
            	$set .= " `order_goods_id` = '' ";
            	$where .= " `goods_id` = '" . $goods_id . "' ";
            	$sql = "update `warehouse_goods` set " . $set . "  WHERE " . $where ;
            }else{
            	return false;
            }

            $data = $this->db->query($sql);

           return $data;

    }

    /*
    *通过货品ID获取货品状态
    */
    public function getIsOnSaleByGoodsId($goods_id){
    	$sql = "select is_on_sale from warehouse_shipping.warehouse_goods where product_type like '钻石' AND goods_id ='".$goods_id."'";
    	$res = $this->db->getOne($sql);
    	return $res;
    }

    /**
     * 修改warehouse_goods表记录
     * @param array $data
     * @param string $where 字符串形式where条件
     */
    public function updateWarehouseGoods($data,$where){
        $sql = $this->updateSql('warehouse_goods',$data, $where);
        return $this->db()->query($sql);
    }
    /**
     * 检查布产绑定货品的记录
     * @param unknown $goods_id
     * @return boolean
     */
    public function getBCGoodsHasBind($order_goods_id){
        $sql ="select order_goods_id,goods_id from warehouse_goods where order_goods_id='{$order_goods_id}'";
        return $this->db()->getRow($sql);
    }


    /*
    *通过货品ID判断货品是不是现货
    *
    */
    public function isExistsByGoodsId($goods_id){
    	$sql ="select id from warehouse_shipping.warehouse_goods where goods_id='".$goods_id."'";
    	return $this->db()->getOne($sql);

    }
    
    public function selectWarehouseGoods($field="*",$where,$type=2){
        return $this->select($field, $where, $type,"warehouse_goods");
    }

    // 打印提货单 查询订单明细对应的货号和柜位
	function getOrderGoodsAndBox($order_id){
        $sql="select g.goods_id,g.warehouse,b.box_sn from warehouse_goods g left join goods_warehouse w on (g.goods_id=w.good_id) left join warehouse_box b on w.box_id=b.id where g.order_goods_id='{$order_id}'";
        return $this->db()->getRow($sql);

    }

    // 根据货号确认货品信息
    public function checkGoodsByGoods_id($goods_sn)
    {
        $sql = "select `fushizhong`,`fushilishu`,`shi2zhong`,`shi2lishu`,`shi3zhong`,`shi3lishu` from `warehouse_shipping`.`warehouse_goods` where `goods_id` = '$goods_sn'";
        return $this->db()->getRow($sql);
    }

    
    public function getWarehouseNumByid($buchan_sn,$is_group = true){
        $url = "select b.bill_no as bill_no,sum(bg.num) as num from warehouse_shipping.warehouse_bill b,warehouse_shipping.warehouse_bill_goods bg,warehouse_shipping.warehouse_goods g where b.id=bg.bill_id and bg.goods_id=g.goods_id and g.buchan_sn='{$buchan_sn}' and b.bill_type='L' and b.bill_status=2";
        if($is_group){
            $url .="  group by b.id ";
        }
        return $this->db()->getAll($url);
    }
}

?>