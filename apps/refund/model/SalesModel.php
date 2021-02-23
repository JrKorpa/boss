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
class SalesModel
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
	                else if($val==null ||strtoupper($val)=='NULL')
	                {
	                    $fields[] = self::add_special_char($key) . '=NULL';
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
	    return "INSERT INTO ".$tableName." (`" . $field . "`) VALUES (\"". $value ."\")";
	}
	
	//   //替换api的getOrderInfoBySn函数
    public function GetExistOrderSn($order_sn,$select=""){
        $res = 0;
        $sql = '';
        if(!empty($order_sn)){
             if(empty($select)){
                 $select = "`oi`.*, `oa`.`order_amount`, `oa`.`money_paid`, `oa`.`money_unpaid`, `oa`.`goods_return_price`, `oa`.`real_return_price`, `oa`.`shipping_fee`, `oa`.`goods_amount`,`oi`.`department_id`,`oi`.`apply_close`,`oi`.`order_status`,`oi`.`order_pay_status`,(select GROUP_CONCAT(DISTINCT rod.out_order_sn) from rel_out_order as rod where rod.order_id=oi.id group by rod.order_id) as out_order_sn,oi.apply_return ";
            }
            $sql = "SELECT {$select} FROM `base_order_info` as `oi` LEFT JOIN `app_order_account` as `oa` ON `oi`.`id`=`oa`.`order_id` WHERE `oi`.`order_sn`='$order_sn'";
            $res = $this->db()->getRow($sql);
            return $res;
        }else{
            return false;
        }
    }
    //更改订单商品信息（app_order_details表）
    public function updateAppOrderDetail($data,$where){
        $sql = $this->updateSql('app_order_details',$data,$where);
        return $this->db()->query($sql);
    }
 //    /**
 //     * 根据goods_id获取款号,和其他自定义字段,替换api中的getGoodsSnByGoodsId函数
 //     */
    public function getGoodsSnByGoodsId($goods_id,$fields='') {
        if(!empty($goods_id)){
        	if(empty($fields)){
	           $fields = "`goods_sn`";
	        }
	        $sql = "SELECT $fields FROM `app_order_details` WHERE `id`=$goods_id";
	        $res = $this->db()->getRow($sql);
	    	return $res;
        }else{
        	return false;
        }	   
   	}
   	/**
   	 * 根据订单明细主键ID获取订单明细记录
   	 * @param unknown $detail_id
   	 * @param string $fields
   	 */
   	public function getAppOrderDetailByDetailId($detail_id,$fields="*"){
   	    $sql = "SELECT {$fields} FROM app_order.`app_order_details` WHERE `id`={$detail_id}";
   	    return $res = $this->db()->getRow($sql);
   	}
   	/**
   	 * 根据订单编号查询订单基本信息
   	 * @param $order_sn
   	 * @param $fields
   	 */
    public function getBaseOrderInfoByOrderSn($order_sn,$fields="*"){
        $sql = "SELECT {$fields} FROM app_order.`base_order_info` WHERE `order_sn`='{$order_sn}'";
        return $this->db()->getRow($sql);
    }
   	/*
   	*通过订单号获取订单的发货状态
   	*
   	*/
   	public function getSendGoodStatusByOrderSn($order_sn){
   		$sql ="select send_good_status from app_order.base_order_info where order_sn='".$order_sn."'";
		return 	$this->db()->getOne($sql); 
   	}

    /**
     * 添加订单日志记录
     * @param unknown $data
     */
   	public function addOrderAction($data){
   	    $sql = $this->insertSql($data,'app_order_action');
   	    return $this->db()->query($sql);
   	}
   	/**
   	 * 更新订单基本信息
   	 * @param unknown $data
   	 */
   	public function updateBaseOrderInfo($data,$where){
   	    $sql = $this->updateSql('base_order_info',$data, $where);
   	    return $this->db()->query($sql);
   	}
   	/**
   	 * 修改app_return_check表记录
   	 * @param array $data
   	 * @param string $where
   	 */
   	public function udpateAppReturnCheck($data,$where){
   	     $sql = $this->updateSql('app_return_check',$data, $where);
   	     return $this->db()->query($sql);
   	}
   	
   	
   	
   	
   	
   	//改期货状态为现货,且更新货号到表中
   	public function updateXianhuo($id,$goods_id){
   		$sql="UPDATE app_order_details SET is_stock_goods = 1 , goods_id = '$goods_id' WHERE id = '$id' ";
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
   	
   		$sql = "SELECT id,goods_id,goods_count,is_stock_goods,buchan_status,is_return,delivery_status,retail_price FROM `app_order_details` WHERE `order_id`='$order_id' ";
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
   		return $row=$this->db->query($sql);
   			
   	
   		
   	}
   	
   	
   	/**
   	 * 更新订单明细表中的状态
   	 * @param array  此商品的对的所以状态，指针对货品
   	 * app_order_details
   	 */
   	
   	public function UpdateOrderDetailStatus($order_gs_id) {
   		$sql="update app_order_details set buchan_status=11 where id=".$order_gs_id;
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
	
	public function updateOrderIsZp($orderid)
	{
		//只要有一个是非赠品 就ok
		$sql ="select * from app_order.app_order_details where order_id='".$orderid."' and is_zp=0 and is_return=0";
		$row = $this->db()->getRow($sql);
		if(empty($row))
		{
			$sql ="select * from app_order_details where order_id='".$orderid."' and is_zp=1 and is_return=0";
         //还得有一个赠品才是赠品单
         $row = $this->db()->getRow($sql);
         if(!empty($row))
         {
            //是赠品单
            $sql = "update base_order_info set is_zp = 1 where id='".$orderid."'";
         }else{
            $sql = "update base_order_info set is_zp = 0 where id='".$orderid."'";
         }
		}else{
			//如果不为空则不是赠品单
			$sql = "update app_order.base_order_info set is_zp = 0 where id='".$orderid."'";
		}
		return $this->db()->query($sql);
	}
   	
   	
   	
   	

   	/**
   	 * 获取订单所属部门
   	 * @param unknown $data
   	 */
   	public function getDepartment_id($id){
   		$sql = "SELECT department_id FROM base_order_info WHERE id= {$id}";
   		return $this->db()->getOne($sql);
   	} 	
   	
   	//改期货状态为现货,且更新货号到表中
   	public function udpateAppOrderDetailsReturn($id,$is_return=0){
   		$sql="UPDATE app_order_details SET is_return = {$is_return}  WHERE id = '$id' ";
   		return $res = $this->db->query($sql);
   	}
   	//录单来源：B2C订单，制单人是：system_api 的订单
   public function orderIdInStr(){
   	 $sql="SELECT id FROM base_order_info WHERE referer = 'B2C订单' AND create_user = 'system_api' AND apply_return = 2";
   	 $rows=$this->db()->getAll($sql);
   	 $str='';
   	 foreach ($rows as $r){
   	 	$str.=$r['id'].',';
   	 }
   	 $str=rtrim($str,",");
   	 return $str;
   }
   	
   	
   
   //查询订单列表
   public function getOrderAccountInfoByOrderId($order_id) {
   	$select = "`oa`.`order_amount`, `oa`.`money_paid`, `oa`.`money_unpaid`, `oa`.`goods_return_price`, `oa`.`real_return_price`, `oa`.`shipping_fee`, `oa`.`goods_amount` ";
   	$sql = "SELECT {$select} FROM `app_order_account` as `oa` WHERE `oa`.`order_id`={$order_id}";
   	return $this->db()->getRow($sql);
   }
   
   public function getOrderDetailRetailPriceByOrderId($order_id) {
   	$res = 0;
   	if(empty($order_id))
   	{
   		return 0;
   			
   	}
   
   	$sql = "SELECT SUM(retail_price) FROM `app_order_details` WHERE `order_id`='$order_id' and delivery_status != 5";
   	$res = $this->db->getOne($sql);
   
   
   	return $res;
   
   }
   
   /*
    * 根据订单id获取订单数据
    */
   public function getOrderInfoById($id) {
   	if(empty($id)){
   		return false;
   	}
   	$sql = "SELECT order_sn,referer,delivery_status,send_good_status,is_zp FROM `base_order_info` WHERE `id`=".$id." limit 1;";
   	return $this->db()->getRow($sql);
   }
   
   /**
    * 根据订单编号查询订单商品明细(app_order_detailsb单表)
    * @param unknown $order_sn
    */
   public function getAppOrderDetailsByOrderId($order_id){
       $sql = "select * from app_order.app_order_details where order_id='{$order_id}'";
       return $this->db()->getAll($sql);
   }

   //现场财务审核更新订单退款金额 如果有退货则更新订单金额
   public function updateOrderAccountRealReturnPrice($return_id) {
   
       $s_time = microtime();
       
       if(empty($return_id)){
           $result['error'] = "订单号order_sn不能为空或更新的数据不能为空";
           Util::jsonExit($result);
       }

       $sql="select * from app_order.app_return_goods where return_id='".$return_id."'";
       $param=$this->db()->getRow($sql);
       if(empty($param)){
           $result['error'] = "退款流水不存在";
           Util::jsonExit($result);         
       }
       $model = new AppReturnCheckModel(32);   
       $order_account = $model->get_order_account($param['order_id']);   
       if(empty($order_account)){
           $result['error'] = "没有该订单";
           Util::jsonExit($result);
       }
       if(bccomp($order_account['money_paid']-$order_account['real_return_price'],$param['apply_return_amount'],5)<0){
           $result['error'] = "退款金额不能大于订单已付款金额".($order_account['money_paid']-$order_account['real_return_price']);
           Util::jsonExit($result);
       }
        
      
       $return_all = $model->get_order_detail($param['order_goods_id']);
   
       $price = 0;
       if(!empty($return_all)){
           foreach ($return_all as $k => $v){
               if ($v['favorable_status'] == 3){
                   $p = $v['goods_price'] - $v['favorable_price'];
                   $price  +=  $p;
               }
               else {
                   $p = $v['goods_price'];
                   $price  +=  $p;
               }
           }
       }

   
       //$money_unpaid = $order_amount - $order_account['money_paid'] + $real_return_price; //
       $money_unpaid=bcadd($order_account['money_unpaid'],$param['apply_return_amount'],3);
       if($money_unpaid < 0)
          $money_unpaid = 0;
   
       $real_return_price= bcadd($order_account['real_return_price'],$param['apply_return_amount'],3);
   
       if($param['return_by']==1){
           $order_amount = bcsub($order_account['order_amount'], $price,3); //订单总金额
           $money_unpaid = bcsub($money_unpaid, $price,3);
           if($money_unpaid < 0)
              $money_unpaid = 0;
           $set = "`real_return_price`=$real_return_price,`money_unpaid`=$money_unpaid,`order_amount`=$order_amount";
       }else{
           $set = "`real_return_price`=$real_return_price,`money_unpaid`=$money_unpaid ";
       }
        
       $sql = "UPDATE `app_order_account` SET $set WHERE `order_id`={$param['order_id']}";
       $res =$this->db()->query($sql);

       //退款后金额，尾款金额大于0，且支付状态是已付款，则改成付定金
       if($res && $money_unpaid > 0){
           $sql = "select order_pay_status,send_good_status from base_order_info WHERE id = {$param['order_id']}";
           $row = $this->db()->getRow($sql);
           $order_pay_status = $row['order_pay_status'];
           $send_good_status = $row['send_good_status'];
           if(in_array($order_pay_status,array(3,4)) && in_array($send_good_status,array(1,4))){
               $sql = "update base_order_info set order_pay_status = 2 WHERE id = {$param['order_id']}";
               $this->db()->query($sql);
           }

       }






       return $res;  

   }   
  
    //获取订单开发票所需信息  
    public function getOrderInfoForInvoice($order_sn){
      $sql="select o.order_sn,r.tel as mobile,o.consignee,now() as invoicedate,o.order_pay_type,a.order_amount,v.* from base_order_info o,app_order_account a, app_order_address r,app_order_invoice v where o.id=a.order_id and o.id=r.order_id 
  and o.id=v.order_id and o.order_sn='{$order_sn}'";
          $order=$this->db()->getRow($sql);

          if(!empty($order)){
              if(bccomp($order['order_amount'],$order['invoice_amount'],2)==0){
                  $sql="select                        if(g.cat_type1 is not null, case when g.cat_type1 in ('摆件','金条','套装','手镯','情侣戒','男戒','脚链','女戒','吊坠','项链','耳饰','手镯','手链') then g.cat_type1 when g.cat_type1='裸石' then '成品钻' when g.cat_type1='彩钻' then '成品钻件' else  '饰品' end,'饰品')  as goods_name,if(d.favorable_status=3,d.goods_price-d.favorable_price,d.goods_price) as goods_price, if(g.cat_type1 is null,1,if(g.cat_type1='金条',g.jinzhong,if(g.cat_type1 in ('裸石','裸钻','彩钻'),g.zuanshidaxiao,1))) as goods_count,if(g.cat_type1 is null,'件',if(g.cat_type1='金条','克',if(g.cat_type1 in ('裸石','裸钻','彩钻'),'克拉','件'))) as unit,                       g.goods_sn as spec  from app_order.app_order_details d left join warehouse_shipping.warehouse_goods g on d.goods_id=g.goods_id where d.is_zp=0 and d.order_id='{$order['order_id']}' and d.id not in (select d2.id from app_order_details d2,app_return_goods r2 where d2.id=r2.order_goods_id and r2.return_by=1 and r2.check_status>=4 and d2.order_id='{$order['order_id']}')";
              }else{
                  $sql="select group_concat( distinct if(g.cat_type1 is not null, case when g.cat_type1 in ('摆件','金条','套装','手镯','情侣戒','男戒','脚链','女戒','吊坠','项链','耳饰','手镯','手链') then g.cat_type1 when g.cat_type1='裸石' then '成品钻' when g.cat_type1='彩钻' then '成品钻件' else  '饰品' end,'饰品')) as goods_name,                                                      v.invoice_amount as goods_price,                                                                                  count(d.goods_count) as goods_count,                                                                                       '件' as unit,group_concat(distinct g.goods_sn) as spec  from app_order.app_order_details d left join warehouse_shipping.warehouse_goods g on d.goods_id=g.goods_id,app_order.app_order_invoice v where d.order_id=v.order_id and d.is_zp=0 and d.order_id='{$order['order_id']}' and d.id not in (select d2.id from app_order_details d2,app_return_goods r2 where d2.id=r2.order_goods_id and r2.return_by=1 and r2.check_status>=4 and d2.order_id='{$order['order_id']}') group by d.order_id";
              }
              //echo $sql;
              $order['detail']=$this->db()->getAll($sql);
          }
          return $order;
    }

    /**
     * 回写财务模块订单发票记录
     * @param unknown $data
     * @return unknown
     */
    public function add_base_invoice_info($data){
        //$sql="insert into app_order_action(`order_id`,`order_status`,`shipping_status`,`pay_status`,`create_user`,`create_time`,`remark`) values ('{$row['id']}','{$row['order_status']}','{$row['send_good_status']}','{$row['order_pay_status']}','{$create_user}','".date('Y-m-d H:i:s')."','{$remark}')";
        $sql = $this->insertSql($data,"finance.base_invoice_info");        
        $res = $this->db()->query($sql);
        return $res;
    }

   

   public function getUnReturnGoods($order_sn){
      $sql="SELECT
              a.id
            FROM
              app_order.app_order_details AS a
            LEFT JOIN app_order.app_return_goods AS b ON a.id = b.order_goods_id
            left join app_order.base_order_info  o on a.order_id=o.id
            WHERE
              a.is_return = 1
            AND b.check_status < 4 and o.order_sn='{$order_sn}'
            union  all 
            select a.id from app_order.app_order_details a,app_order.base_order_info o where a.order_id=o.id and a.is_return=0 and a.is_zp<>'1' and  o.order_sn='{$order_sn}' " ;
      return $this->db()->getAll($sql);
   } 

   public function updateUnDeliveryStatus($order_sn){
      $sql="update app_order.base_order_info set delivery_status=1 where order_sn='{$order_sn}' " ;
      $this->db()->query($sql);
      $sql="delete from  warehouse_shipping.order_distrib_todo where order_sn='{$order_sn}'";
      $this->db()->query($sql);
   }   

    //库管审核：订单未发货前发生商品退货 如果使用了积分码 则修改对应的积分码为未使用
    public function update_jifenma_status($jifenma_code){
        $sql = "update cuteframe.point_code set status='0',order_sn=null,use_people_name=null where point_code='{$jifenma_code}'";
        //echo $sql;
        return $this->db()->query($sql);
    }

    //库管审核： 订单未发货前发生商品退货 清除商品积分 积分码 代金券
    public function clear_order_goods_point($order_detail_id){
        $sql = "update app_order.app_order_details set discount_point=0,reward_point=0,jifenma_point=0,jifenma_code=null,daijinquan_code=null where id='{$order_detail_id}'";
        //echo $sql;
        return $this->db()->query($sql);
    }
    //库管审核：
    public function remove_order_details_jifenma($order_detail_id){
      $sql = "update app_order.app_order_details set daijinquan_code=null where id='{$order_detail_id}'";
      return $this->db()->query($sql);
    }

    //财务审核：订单发货后发生退金额 生成退积分记录 同步到CRM积分系统
    public function app_return_point_add($return_id){
        $sql = "select o.order_sn, r.return_by from app_order.app_return_goods r,app_order.app_order_details d,app_order.base_order_info o where r.order_goods_id=d.id and d.order_id=o.id and r.return_id='{$return_id}' and ifnull(d.discount_point,0)+ifnull(d.reward_point,0)+ ifnull(d.jifenma_point,0) >0 ";
        //echo $sql;
        $row = $this->db()->getRow($sql);
        if($row){
            if($row['return_by'] == 1) {
                $sql = "insert into app_order.app_return_point select 0,o.order_sn,d.id,r.return_id,r.return_by,r.return_type,d.goods_id,d.goods_sn,r.apply_return_amount,d.discount_point,d.reward_point,d.jifenma_point,'". $_SESSION['userName'] ."',now() from app_order.app_return_goods r,app_order.app_order_details d,app_order.base_order_info o where r.order_goods_id=d.id and d.order_id=o.id and r.return_id='{$return_id}'";
            }
            else {
                $sql = "insert into app_order.app_return_point select 0,o.order_sn,d.id,r.return_id,r.return_by,r.return_type,d.goods_id,d.goods_sn,r.apply_return_amount,round(r.apply_return_amount/if(d.favorable_status=3,d.goods_price-d.favorable_price,d.goods_price)*d.discount_point,0),round(r.apply_return_amount/if(d.favorable_status=3,d.goods_price-d.favorable_price,d.goods_price)*d.reward_point,0),round(r.apply_return_amount/if(d.favorable_status=3,d.goods_price-d.favorable_price,d.goods_price)*d.jifenma_point,0),'". $_SESSION['userName'] ."',now() from app_order.app_return_goods r,app_order.app_order_details d,app_order.base_order_info o where r.order_goods_id=d.id and d.order_id=o.id and r.return_id='{$return_id}'";
            }
            //echo $sql;
            return $this->db()->query($sql);
        }
        
    }

}

?>