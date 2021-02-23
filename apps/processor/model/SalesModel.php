<?php
/**
 * 销售模块的数据模型（代替Sales/Api/api.php）
 *  -------------------------------------------------
 *   @file		: SaleModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class SalesModel extends SelfModel
{
    protected $db;
	function __construct ($strConn="")
	{
		parent::__construct($strConn);
	}		
	/**
	 * 查询订单信息 (关联支付 app_order_account)
	 * @param $order_id
	 * @return json
	 */
	public function GetOrderInfo($order_sn)
	{
		//var_dump(__FUNCTION__);	
		
		if(empty($order_sn))
		{
			return null;
			
		}
		
		//查询商品详情
		//$sql = "select a.*,b.`order_amount`,b.`money_paid`,b.`money_unpaid`,r.out_order_sn from `base_order_info` AS a,  `app_order_account` AS b , rel_out_order AS r WHERE a.`id`=b.`order_id` AND a.`id`=`r`.`order_id`" .$where." ;";
		$sql = "SELECT id,order_pay_status,delivery_status,referer FROM `base_order_info`  WHERE `order_sn` ='$order_sn' ;";
		$row = $this->db->getRow($sql);
		
		//返回信息
		
		return $row;
	}
	
	
	
	
	
	/**
	 * 获取订单商品信息
	 */
	public function getOrderDetailByOrderId($order_id) {
		$res = 0;
	    if(empty($order_id))
		{
			return null;
			
		}

		$sql = "SELECT id,goods_id,goods_count,is_stock_goods,buchan_status,is_return FROM `app_order_details` WHERE `order_id`='$order_id' ";
		$res = $this->db->getAll($sql);

		return $res;
	}
	
	
	
	
	/*------------------------------------------------------ */
	//-- 更改订单状态:更改订单的配货状态为已配货,更改订单的布产状态为已出厂
	//-- by linian
	/*------------------------------------------------------ */
	public function EditOrderdeliveryStatus($order_sn)
	{
	
		//若 接收到的数据不为空 则拼接where条件
		if(empty($order_sn))
		{
			return false;
		}
               
		$sql="select min(d.buchan_status) as buchan_status from base_order_info o,app_order_details d where o.id=d.order_id and d.is_stock_goods=0 and d.is_return=0 and o.order_sn='{$order_sn}'";
		$res=$this->db->query($sql);
		$rowbc=$res->fetch(PDO::FETCH_ASSOC);
		$buchan_status=5;
		if($rowbc!=false){
			$min_buchan_status=$rowbc['buchan_status'];
			if($min_buchan_status==10 || $min_buchan_status==11)
				$buchan_status=5;
			if($min_buchan_status==9)
				$buchan_status=4;
			if(in_array($min_buchan_status,array(4,5,6,7,8)))
					$buchan_status=3;
			if($min_buchan_status==3 || $min_buchan_status==2)
				$buchan_status=2;
			if($min_buchan_status==1)
				$buchan_status=1;
		}
			$sql = "UPDATE `base_order_info` SET `delivery_status`='2',`buchan_status`='$buchan_status' WHERE `order_sn`='{$order_sn}' and `delivery_status` = 1";
			$row=$this->db->query($sql);	

		//返回信息
		if(!$row){
			return false;
		}else{
			return true;
		}
	}
	
	
	
	
	/**
	 * 更新订单明细表中的状态
	 * @param array  此商品的对的所以状态，指针对货品
	 * app_order_details
	 */
	
	public function UpdateOrderDetailStatus($order_gs_id,$status=11) {
		$sql="update app_order_details set buchan_status=$status where id=".$order_gs_id;
		return $res=$this->db()->query($sql);
	}
	/*
	public function UpdateOrderDetailStatus($update_data) {
		
		$arrids = array();
		//布产状态为出厂状态则把这个order_id记录上    然后进行判断改订单状态
		if (isset($update_data)) {
			$data = $update_data;
			if(count($data) > 0){
				
				foreach ($data as $val){
					$all_ids[] = $val['id'];
					if($val['buchan_status']==9 || $val['buchan_status']==11){
						$arrids[]=$val['id'];
					}
					$where = " `id` = {$val['id']}";
					unset($val['id']);
					$sql = $this ->  updateSql('app_order_details',$val, $where);
					$res=$this->db()->query($sql);
					
				}
				
				//通过商品id取出order_id 并通过orderid 取不是都出厂的商品  判断有无  如果没有则把已付款订单改为待配货
				if(count($arrids) > 0){
					$arrids=implode(',',$arrids);
					$sql1 = "SELECT `order_id` FROM `app_order_details` WHERE `id` IN (".$arrids.")";
					$res1 = $this->db->getAll($sql1);
					$order_id_arr = array();
					foreach($res1 as $k=>$v){
						$sql2 ="SELECT COUNT(id) FROM `app_order_details` WHERE `order_id`=".$v['order_id']."  AND (`buchan_status`!=9 and `buchan_status`!=11) AND `is_stock_goods`=0";
						$res2 =$this->db->getOne($sql2);
						if(!$res2){
							$sql3 ="UPDATE `base_order_info` SET `delivery_status`=2 WHERE `id`=".$v['order_id']." AND (`order_pay_status`=3 or `order_pay_status`=4)";
							$res3 =  $this->db->query($sql3);
						}
					}
				}
	
				//更改订单状态：当商品的布产状态都为：生产中，订单状态变成生产中；都已出厂，则订单状态为已出厂；当都为不需要配货，都为不需要配货，其他状态未操作
				if(count($all_ids) > 0){
					$allids=implode(',',$all_ids);
					$sql1 = "SELECT `order_id` FROM `app_order_details` WHERE `id` IN (".$allids.")";
					$res1 = $this->db->getAll($sql1);
					$order_id_arr = array();
					foreach($res1 as $k=>$v){
						$order_id_arr[$v['order_id']] = $v['order_id'];
					}
	
					foreach($order_id_arr as $v){
						$order_id = $v;
						$sql3 ="SELECT id,buchan_status FROM `app_order_details` WHERE `order_id`=".$order_id."  AND `is_stock_goods`=0";
						$data_detail =$this->db->getAll($sql3);
						$all_detail_num = count($data_detail);
	
						$new_data_detail = array();
						//商品的布产状态都为生产中：4 或者不需要布产；11
						foreach ($data_detail as $dd_val){
							$new_data_detail[$dd_val['buchan_status']][]= 1;
						}
						//获取订单的状态
						$sql3= "SELECT `buchan_status` FROM `base_order_info` WHERE `id` = ".$order_id;
						$data_order =$this->db->getRow($sql3);
						$order_buchan_status = $data_order['buchan_status'];
						switch ($order_buchan_status){
							//订单的状态:允许布产2
							case 2:
								//判断订单中期货都为生产中：4，则订单的状态也改为:生产中：3
								if(isset($new_data_detail[4])){
									if($all_detail_num == count($new_data_detail[4])){
										$sql5 ="UPDATE `base_order_info` SET `buchan_status`=3 WHERE `id`=".$order_id;
										$res5 =  $this->db->query($sql5);
									}
								}
								//判断订单中期货都为:不需要布产：11，则订单的状态也改为:不需要布产：5
								if(isset($new_data_detail[11])){
									if($all_detail_num == count($new_data_detail[11])){
										$sql5 ="UPDATE `base_order_info` SET `buchan_status`=5  WHERE `id`=".$order_id;
										$res5 =  $this->db->query($sql5);
									}
								}
	
								break;
								//订单状态：生产中：3
							case 3;
							//判断订单中期货都为:已出厂 9，则订单的状态也改为:不需要布产：4
							if(isset($new_data_detail[9])){
								if($all_detail_num == count($new_data_detail[9])){
									$sql5 ="UPDATE `base_order_info` SET `buchan_status`=4 WHERE `id`=".$order_id;
									$res5 =  $this->db->query($sql5);
								}
							}
							break;
	
							case 4:
								break;
							case 5:
								break;
						}
					}
				}
			}else{
				return false;
			}
		}else{
			return false;
		}	
	
		//返回信息
		if(!$res)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
*/
		
	public function AddOrderLog($order_sn,$remark)
	{
		//根据布产号查布产状态和布产类型	
		$create_user=$_SESSION['userName']?$_SESSION['userName']:'第三方';
		
		$sql="select id,order_status,send_good_status,order_pay_status from base_order_info where order_sn='{$order_sn}'";
		$row=$this->db->getRow($sql);
		
		$sql="insert into app_order_action(`order_id`,`order_status`,`shipping_status`,`pay_status`,`create_user`,`create_time`,`remark`) values ('{$row['id']}','{$row['order_status']}','{$row['send_good_status']}','{$row['order_pay_status']}','{$create_user}','".date('Y-m-d H:i:s')."','{$remark}')";
		$res = $this->db->query($sql);
		
		return $res;
	}
	/**
	 * 根据订单编号获取订单基本信息
	 * @param $order_sn
	 * @return 
	 * gaopeng
	 */
	public function getBaseOrderInfoByOrderSn($fields="*",$order_sn){
	    $sql = "SELECT {$fields} FROM `base_order_info` WHERE `order_sn`='{$order_sn}'";
	    return $res = $this->db->getRow($sql);
	}
	/**
	 * 更改base_order_info表
	 * @param $data
	 * @param $where
	 * @return 
	 * gaopeng
	 */
	public function updateBaseOrderInfo($data,$where){
	    $sql = $this->updateSql("base_order_info",$data, $where);
	    return $this->db()->query($sql);
	}
	/**
	 * 修改订单明细记录
	 * @param  $data
	 * @param  $where
	 * @return 
	 * gaopeng
	 */
	public function updateOrderDetail($data,$where){
	    $sql = $this->updateSql("app_order_details",$data, $where);
	    return $this->db()->query($sql);
	}
	
	/**
	 * 添加订单日志记录
	 * @param $data
	 * return
	 * gaopeng
	 */
	public function insertOrderAction($data){
	   $sql = $this->insertSql($data,"app_order_action");
	   return $this->db()->query($sql);
	}
	/**
	 * 根据订单编号查询订单明细列表
	 * @param $order_sn
	 * @return 
	 * gaopeng
	 */
	public function getOrderDetailsByOrderId($fields="*",$order_id){
	    $sql = "select {$fields} from app_order.app_order_details where order_id='{$order_id}'";
	    return $this->db()->getAll($sql);
	}
	/**
	 * 根据订单明细主键ID获取订单明细记录
	 * @param unknown $detail_id
	 * @param string $fields
	 */
	public function getAppOrderDetailByDetailId($fields="*",$detail_id){
	    $sql = "SELECT {$fields} FROM app_order.`app_order_details` WHERE `id`={$detail_id}";
	    return $this->db()->getRow($sql);	    
	}
	//获取根据证书号查询对应空托列表(4c专用方法)
	public function getOrderDetailsFor4C($cert_id,$is_kongtuo=true){   
	    if($is_kongtuo==true){
	        $goods_type_str = "and a.goods_type <>'lz'";
	    }else{
	        $goods_type_str = "and a.goods_type ='lz'";
	    }
	    $sql = "select a.*,b.order_sn,b.consignee from app_order.app_order_details a left join app_order.base_order_info b on a.order_id=b.id where a.zhengshuhao='{$cert_id}' {$goods_type_str} and a.buchan_status<=2";
		//echo $sql;
	    return $this->db()->getAll($sql);
	}
	/**
	 * 更新订单主表布产状态
	 * @param unknown $order_sn
	 * @return boolean
	 */
	public function updateOrderBCStatus($order_sn)
	{
		//AsyncDelegate::dispatch("order", array('event' => 'refresh_order', 'order_sn' => $order_sn));
		return true;
		
		//若 接收到的数据不为空 则拼接where条件
		if(empty($order_sn))
		{
			return false;
		}
               
		$sql="select min(d.buchan_status) as buchan_status from base_order_info o,app_order_details d where o.id=d.order_id and d.is_stock_goods=0 and d.is_return=0 and o.order_sn='{$order_sn}'";
		$res=$this->db->query($sql);
		$rowbc=$res->fetch(PDO::FETCH_ASSOC);
		$buchan_status=5;
		if($rowbc!=false){
			$min_buchan_status=$rowbc['buchan_status'];
			if($min_buchan_status==10 || $min_buchan_status==11)
				$buchan_status=5;
			if($min_buchan_status==9)
				$buchan_status=4;
			if(in_array($min_buchan_status,array(4,5,6,7,8)))
					$buchan_status=3;
			if($min_buchan_status==3 || $min_buchan_status==2)
				$buchan_status=2;
			if($min_buchan_status==1)
				$buchan_status=1;
		}
		$sql = "UPDATE `base_order_info` SET  `buchan_status`={$buchan_status} WHERE `order_sn`='{$order_sn}'";
		$row=$this->db->query($sql);	

		//返回信息
		if(!$row){
			return false;
		}else{
			return true;
		}
	}
	
	/*
	*通过订单获取钻石货号类型(现货或期货)
	* @author lrj
	*/
 	public function getStockGoodsByOrderSn($order_sn,$style_sn){
 		$sql = "select d.is_stock_goods,d.goods_id,d.zhengshuhao from app_order.base_order_info b left join app_order.app_order_details d on b.id=d.order_id where b.order_sn='".$order_sn."' AND d.goods_sn='".$style_sn."'";
 		$res = $this->db->getRow($sql);
 		return $res;
 	}

    public function addOrderLog2($data){
        //$sql="insert into app_order_action(`order_id`,`order_status`,`shipping_status`,`pay_status`,`create_user`,`create_time`,`remark`) values ('{$row['id']}','{$row['order_status']}','{$row['send_good_status']}','{$row['order_pay_status']}','{$create_user}','".date('Y-m-d H:i:s')."','{$remark}')";
        $sql = $this->insertSql($data,"app_order_action");
        $res = $this->db()->query($sql);
        return $res;
    }

    public function GetPrintBillsInfo($order_sn)
    {
       
        $sql = "SELECT  `a`.`id`,`a`.`order_sn`, `a`.`user_id`, `a`.`delivery_status`,`a`.`department_id`,`a`.`customer_source_id`,`a`.`send_good_status`, `a`.`order_remark`, `a`.`order_status`, `a`.`order_pay_status`,`a`.`order_pay_type`,`a`.`consignee` order_consignee,`a`.`create_time`,`a`.`referer`,`b`.`order_amount`, `b`.`money_paid`, `b`.`money_unpaid`,`b`.`goods_amount`,`b`.`favorable_price`,`b`.`coupon_price`,c.`consignee`, `c`.`distribution_type`, `c`.`express_id`, `c`.`address`,`c`.`shop_type`,`c`.`shop_name`, `d`.`invoice_title`, `d`.`invoice_amount`, `d`.`invoice_address`, `d`.`invoice_num`, `d`.`is_invoice`,`d`.`invoice_email`,d.invoice_type,`go`.gift_id,`go`.remark,`go`.gift_num,(SELECT r.`out_order_sn` FROM rel_out_order r where r.order_id=a.id limit 1) as `out_order_sn`  FROM  `app_order_account` AS `b`, `app_order_address` AS `c`,`base_order_info` AS `a` LEFT JOIN `app_order_invoice` AS d ON `a`.`id` = `d`.`order_id` LEFT JOIN `rel_gift_order` AS `go` ON `go`.`order_id`=`a`.`id`  WHERE `a`.`order_sn` = '{$order_sn}' AND `a`.`id` = `b`.`order_id` AND `a`.`id` = `c`.`order_id`";
        return $row = $this->db()->getRow($sql);
    }
    
    public function GetOutOrderSn($order_id) {          
        $sql = "SELECT distinct `out_order_sn` FROM rel_out_order where order_id='{$order_id}'";
        return   $row = $this->db()->getRow($sql);
    } 


     /**
     * 根据订单号获取订单信息 打印提货单    
     */
    public function GetOrderInfoByOrdersn($order_sn) {
        $sql = "SELECT a.*,b.*,s.shoucun,s.company_id,s.company,s.warehouse,s.warehouse_id,b.id as order_detail_id FROM `base_order_info` AS `a` LEFT JOIN `app_order_details` AS `b` ON `a`.`id` = `b`.`order_id` left join`warehouse_shipping`.`warehouse_goods` s ON b.goods_id=s.goods_id WHERE a.order_sn='{$order_sn}' and b.is_return=0";
        return $this->db()->getAll($sql); 
    }   	
    
    //查询是否是申请关闭或者已关闭的订单
    public function getOrderCloseArr($order_sn){
    	//查询是否是申请关闭
     	$sql="SELECT id FROM base_order_info WHERE apply_close=1  AND order_sn='{$order_sn}'";
    	$r= $this->db()->getRow($sql);
    	if(!empty($r)){
    		return 1;
    	}
    	//查询是否是已关闭
    	$sql="SELECT id FROM base_order_info WHERE is_delete = 1  AND order_sn='{$order_sn}'";
    	$r= $this->db()->getRow($sql);
    	if(!empty($r)){
    		return 2;
    	}
    	
    	return 0;
    	
    }
    
    //查询是否有退货申请
    public function getOrderReturnArr($order_sn,$order_goods_id){
    	$sql="SELECT return_id FROM app_return_goods WHERE return_by = 1 AND order_goods_id = {$order_goods_id} AND order_sn = '{$order_sn}'";
    	$r= $this->db()->getAll($sql);
    	if(empty($r)){return 0;}
    	$return_id='';
    	foreach ($r as $v){
    		$return_id=$v['return_id'].',';
    	}
    	$return_id=rtrim($return_id,',');
    	//查询是否主管审核
    	$sql1="SELECT id FROM app_return_check WHERE  return_id in ($return_id)";
    	$r1= $this->db()->getAll($sql1);
    	if(!empty($r1)){
    		//查询是否有不是主管驳回的
    		$sql2="SELECT id FROM app_return_check WHERE leader_status != 2 AND return_id in ($return_id)";
    		$r2= $this->db()->getAll($sql2);
    		if(empty($r2)){
    			return 0;
    		}
    		
    		
    		$sql3="SELECT id FROM app_return_check WHERE deparment_finance_status =1 AND return_id in ($return_id)";
    		$r3= $this->db()->getAll($sql2);
    		if(!empty($r3)){
    			return 4;
    		}
    		
    	}
    	
    		return 3;
    	
    	
    	
    }
    
    function isTsydOrder($order_sn){
    	$sql="select id from base_order_info where order_sn='{$order_sn}' and referer='天生一对加盟商'";
    	$row=$this->db()->getRow($sql);
    	if(!empty($row)){
    		return true;
    	}else {
    		return false;
    	}
    }
    
    
    /**
     * 取现有订单中所有录单类型
     */
    public function getReferers() {
    	$sql = 'select referer from base_order_info group by referer';
    	$list =  $this->db()->getAll($sql);
    	return array_column($list, 'referer');
    }
    
    
    public function updateOrderInfoStatusByOrderSn($order_sn)
    {
    
    	//若 接收到的数据不为空 则拼接where条件
    	if(empty($order_sn))
    	{
    		return false;
    	}
    	$buchan_status=1;	
    	$sql="select min(d.buchan_status) as buchan_status,min(d.delivery_status) as delivery_status,o.referer,o.order_pay_status,o.delivery_status as delivery_status1 from base_order_info o,app_order_details d where o.id=d.order_id and d.is_stock_goods=0 and d.is_return=0 and o.order_sn='{$order_sn}'";
		$rowbc=$this->db()->getRow($sql);

		if(!empty($rowbc)){
			$min_buchan_status=$rowbc['buchan_status'];
			if($min_buchan_status==10 || $min_buchan_status==11)
				$buchan_status=5;
			if($min_buchan_status==9)
				$buchan_status=4;
			if(in_array($min_buchan_status,array(4,5,6,7,8)))
				$buchan_status=3;
			if($min_buchan_status==3 || $min_buchan_status==2)
				$buchan_status=2;
			if($min_buchan_status==1)
				$buchan_status=1;
			
			if($rowbc['referer']=='天生一对加盟商'){
				$delivery_status=$rowbc['delivery_status'];
			}else{
				if(in_array($rowbc['order_pay_status'], array(3,4)) && ($min_buchan_status==9 || $min_buchan_status==11) && $rowbc['delivery_status1']==1){
					$delivery_status=2;
				}else{
					$delivery_status=$rowbc['delivery_status1'];
				}
			}
		}
		
		$sql = "UPDATE `base_order_info` SET  `buchan_status`='$buchan_status',`delivery_status`={$delivery_status} WHERE `order_sn`='{$order_sn}'";
		$row=$this->db->query($sql);
    
    	//返回信息
    	if(!$row){
    		return false;
    	}else{
    		return true;
    	}
    }

    /**
     *跟进布产ID获取订单里面的商品属性
     */
    public function getOrderAttrInfoByBc_sn($where)
    {
        $sql = "select * from `app_order_details` where `bc_id` = ".$where['id']." and `goods_sn` = '".$where['style_sn']."'";
        return $this->db->getRow($sql);
    }
    
    //根据订单号取渠道分类
    public function getOrderClass($order_sn){
        $sql = "SELECT `sc`.`channel_class` FROM `app_order`.`base_order_info` `oi` inner join `cuteframe`.`sales_channels` `sc` on `oi`.`department_id` = `sc`.`id` WHERE `oi`.`order_sn`= '{$order_sn}'";
        return $res = $this->db->getOne($sql);
    }
}

?>