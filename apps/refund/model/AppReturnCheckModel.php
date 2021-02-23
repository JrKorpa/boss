<?php

/**
 *  -------------------------------------------------
 *   @file		: AppReturnCheckModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-13 11:04:54
 *   @update	:
 *  -------------------------------------------------
 */
class AppReturnCheckModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'app_return_check';
        $this->pk = 'id';
        $this->_prefix = '';
        $this->_dataObject = array("id" => "",
            "return_id" => "退款单id",
            "leader_id" => "部门主管",
            "leader_res" => "业务负责人意见",
            "leader_status" => "主管审核状态",
            "leader_time" => "主管审核时间",
            "goods_comfirm_id" => "库管ID",
            "goods_res" => "库管部门意见",
            "goods_status" => "产品状态,0,未确认,1,留库存,2未出库",
            "goods_time" => "产品状态操作时间",
            "cto_id" => "CTOID",
            "cto_res" => "CTO意见",
            "cto_status" => "CTO状态：0为操作，1批准",
            "cto_time" => "CTO操作时间",
            "deparment_finance_id" => "部门财务id",
            "deparment_finance_status" => "部门财务审核状态0,未操作,1已审核",
            "deparment_finance_res" => "部门财务备注",
            "deparment_finance_time" => "部门财务操作时间",
            "finance_id" => "财务操作人ID",
            "bak_fee" => "支付手续费",
            "finance_res" => "财务意见",
            "finance_status" => "财务状态,0未操作,1,已确认",
            "finance_time" => "财务操作时间",            
        );
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url AppReturnCheckController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        //不要用*,修改为具体字段
        $sql = "SELECT * FROM `" . $this->table() . "`";
        $str = '';
//		if($where['xxx'] != "")
//		{
//			$str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
//		}
//		if(!empty($where['xx']))
//		{
//			$str .= "`xx`='".$where['xx']."' AND ";
//		}
        if ($str) {
            $str = rtrim($str, "AND "); //这个空格很重要
            $sql .=" WHERE " . $str;
        }
        $sql .= " ORDER BY `id` DESC";
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }
    
    
    /**
     * 更新审核表对应的审核阶段的字段
     * @param type $return_id
     * @param type $fileds 更新的数据
     * @return boolean
     */
    function modfiyCheckStatus($return_id,$fileds){
        if(intval($return_id) < 1 || empty($fileds)){
            return false;
        }
        $param = '';
        foreach ($fileds as $key=>$val){
            $param .= "`{$key}`='{$val}',";
        }
        if(empty($param)){
            return false;
        }
        $param = rtrim($param,',');
        $sql = "UPDATE `{$this->table()}` SET $param WHERE `return_id` = $return_id";
        return $this->db()->query($sql);
    }

    /**
     *  getCheckId,获取checkID
     *
     *  @url AppReturnCheckController/get
     */
    function getCheckId($id){

        $sql = "SELECT `id` FROM `".$this->table()."` WHERE `return_id` = ".$id;
        $id = $this->db()->getOne($sql);
		
        return $id;
    }
    function getCheckIdForLeader($id){
    
        $sql = "SELECT `id` FROM `".$this->table()."` WHERE leader_status>0 and `return_id` = ".$id;
        $id = $this->db()->getOne($sql);
    
        return $id;
    }
    function getOrderPayStatus($order_id){
        $sql = "SELECT `order_pay_status` FROM `base_order_info` WHERE `id`=$order_id";
        return $this->db()->getOne($sql);
    }

    /**
     * 获取订单的明细金额、订单支付状态
     * @param type $order_goods_id
     * @return type
     */
    function getOrderPriceSatausGoods($order_goods_id){
        $sql = "select `oi`.`order_pay_status`, oi.send_good_status,oi.delivery_status,
(`od`.`goods_price` - IF(`od`.`favorable_price`,`od`.`favorable_price`,0.00)) as `goods_price` 
FROM `base_order_info` `oi` 
inner join `app_order_details` `od` on `oi`.`id` = `od`.`order_id`
where `od`.`id` = $order_goods_id";
        return $this->db()->getRow($sql);
    }
    
    /**
     * 获取订单的订购方式
     * @param type $order_id
     * @return type
     */
    function getOrderPayType($order_id) {
        $sql = "SELECT `order_pay_type` FROM `base_order_info` WHERE `id`={$order_id}";
        return $this->db()->getOne($sql);
    }

	
	 public function get_return($return_id){
          $sql = "select * from app_return_goods where return_id = " . $return_id; 
          $res = $this->db()->getRow($sql);
          return $res;
    }
    
	  function get_order_all($order_id){
        $sql = "SELECT a.*,b.* FROM `app_order`.`app_order_details` AS a  LEFT JOIN app_return_goods as b ON  a.id = b.order_goods_id WHERE a.is_return = 0 AND b.check_status < 4 AND  a.`order_id`=".$order_id;
        $detail = $this->db()->getAll($sql);
        return $detail;
    }

	
	  function get_app_retun_good($order_id){
    
        $sql = "SELECT * FROM app_order_details WHERE id IN (SELECT order_goods_id FROM app_return_goods WHERE `order_id` = {$order_id} AND check_status >=4)";
        
       $detail = $this->db()->getAll($sql);
      
        return $detail; 
    }
    
    public function getGoodsPrice($order_id){
          $sql = "SELECT * FROM app_order_details WHERE id IN (SELECT order_goods_id FROM app_return_goods WHERE `order_id` = {$order_id} AND check_status >=4)";
     
       $detail = $this->db()->getAll($sql);
      
        return $detail;  
    }
    
     public function getReturnGoodsfavor($order_id){
         $sql = "SELECT SUM(favorable_price) AS t_favorable_price FROM app_order_details WHERE id IN (SELECT order_goods_id FROM app_return_goods WHERE `order_id` = {$order_id} AND check_status >=4) AND favorable_status =3";
         return $this->db()->getOne($sql);
    }
    
    function get_real_retun_good($order_id){
         $sql = "SELECT SUM(real_return_amount) FROM app_return_goods WHERE `order_id` = {$order_id} AND check_status >=4";
       
         $detail = $this->db()->getOne($sql);
     
        return $detail; 
    }
            function get_order_detail($id){
          $sql = "SELECT *  FROM `app_order`.`app_order_details` WHERE `id`={$id}"; 
          $detail = $this->db()->getAll($sql);
          return $detail;
    }
	
	 function get_order_account($order_id){
        $_sql = "SELECT `real_return_price`,`money_unpaid`,`goods_amount`,`money_paid`,`coupon_price`,favorable_price,goods_amount-favorable_price AS n_goods_amount,`order_amount` FROM `app_order_account` WHERE `order_id`={$order_id}";
        $order_account =  $this->db()->getRow($_sql);
        return $order_account;
    }
    
    function getAppReturnCheckByReturnId($return_id,$fields="*"){
        $sql = "select {$fields} from ".$this->table().' where return_id='.$return_id.' order by id desc';
        return $this->db()->getRow($sql);
    }
    //检查订单是否已经开发票
    function checkOrderHasInvoice($order_sn){
         $sql = "select count(*) from app_order.base_order_info a inner join app_order.app_order_invoice b on a.id=b.order_id where a.order_sn='{$order_sn}' and b.is_invoice=1 and b.invoice_status=2";
         return $this->db()->getOne($sql);
    }
}

?>