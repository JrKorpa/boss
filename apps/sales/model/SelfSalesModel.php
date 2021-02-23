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
class SelfSalesModel
{
    protected $db;
	function __construct ($strConn="")
	{
		$this->db = DB::cn($strConn);
	}
	public function db(){
	    return $this->db;
	}
	final public static function add_special_char($value)
	{
	    if ('*' == $value || false !== strpos($value, '(') || false !== strpos($value, '.') || false !== strpos($value, '`'))
	    {
	        //不处理包含* 或者 使用了sql方法。
	    }
	    else
	    {
	        $value = '`' . trim($value) . '`';
	    }
	    if (preg_match('/\b(select|insert|update|delete)\b/i', $value))
	    {
	        $value = preg_replace('/\b(select|insert|update|delete)\b/i', '', $value);
	    }
	    return $value;
	}
	/*
	 * updateSql,生成更新语句
	 */
	protected function updateSql ($table,$do,$where)
	{
	    $field = '';
	    $fields = array();
	    foreach ($do as $key=>$val)
	    {
	        switch (substr($val, 0, 2))
	        {
	            case '+=':
	                $val = substr($val,2);
	                if (is_numeric($val)) {
	                    $fields[] = self::add_special_char($key) . '=' . self::add_special_char($key) . '+' . $val;
	                }
	                else
	                {
	                    continue;
	                }
	                break;
	            case '-=':
	                $val = substr($val, 2);
	                if (is_numeric($val))
	                {
	                    $fields[] = self::add_special_char($val) . '=' . self::add_special_char($key) . '-' . $val;
	                }
	                else
	                {
	                    continue;
	                }
	                break;
	            default:
	                if(is_numeric($val))
	                {
	                    $fields[] = self::add_special_char($key) . '=' . $val;
	                }
	                else
	                {
	                    $fields[] = self::add_special_char($key) . '="' . $val.'"';
	                }
	        }
	    }
	    $field = implode(',', $fields);
	    $sql = "UPDATE `".$table."` SET ".$field;
	    $sql .= " WHERE {$where}";
	    return $sql;
	}
	protected function insertSql ($do,$tableName = "")
	{  
	    $fields = array_keys($do);
	    $valuedata = array_values($do);
	    array_walk($fields, array($this, 'add_special_char'));
	    $field = implode('`,`', $fields);
	    $value = implode('","',$valuedata);
	    
	    return "INSERT INTO `".$tableName."` (`" . $field . "`) VALUES (\"". $value ."\")";
	}
	
	
	//改期货状态为现货,且更新货号到表中
	public function updateXianhuo($id,$goods_id){
		$sql="UPDATE app_order_details SET is_stock_goods = 1 , goods_id = '$goods_id' , dia_type = 1 WHERE id = '$id' ";
		return $res = $this->db->query($sql);
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
		$sql = "SELECT id,order_pay_status,delivery_status FROM `base_order_info`  WHERE `order_sn` ='$order_sn' ;";
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
	//-- 更改订单状态:更改订单的配货状态为允许配货,更改订单的布产状态为已出厂
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
		 
		$sql = "UPDATE `base_order_info` SET `delivery_status`='2',`buchan_status`='{$buchan_status}' WHERE `order_sn`='{$order_sn}' and `delivery_status` = 1";
		$row=$this->db->query($sql);
			

		//返回信息
		if(!$row){
			return false;
		}else{
			return true;
		}
	}
	
	
	//更改订单类型
	public function EditOrderdexianhuoStatus($order_sn,$xianhuo)
	{
	
		//若 接收到的数据不为空 则拼接where条件
		if(empty($order_sn))
		{
			return false;
		}
	
	
		$sql = "UPDATE `base_order_info` SET `is_xianhuo` = {$xianhuo} WHERE `order_sn`='{$order_sn}'";
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
	
	public function UpdateOrderDetailStatus($order_gs_id,$buchan_status=11) {
		$sql="update app_order_details set buchan_status={$buchan_status} where id=".$order_gs_id;
		return $res=$this->db()->query($sql);
	}

	

	
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
	public function EditOrderStatus($order_sn)
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
		$sql = "UPDATE `base_order_info` SET  `buchan_status`='$buchan_status' WHERE `order_sn`='{$order_sn}'";
		$row=$this->db->query($sql);
	
		//返回信息
		if(!$row){
			return false;
		}else{
			return true;
		}
	}


	function updateOrderDetialDelivery($id,$delivery_status){
	    $sql="UPDATE `app_order_details` SET `delivery_status` = {$delivery_status} WHERE `id` = {$id}";
		return $this->db()->query($sql);
	}
	
}

?>