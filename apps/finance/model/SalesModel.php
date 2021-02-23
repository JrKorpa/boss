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
class SalesModel extends SelfModel {

    protected $db;

    function __construct($strConn = "") {
        $this->db = DB::cn($strConn);
    }

    public function db() {
        return $this->db;
    }

    //   //替换api的getOrderList函数
    public function getOrderList($order_id) {
        $s_time = microtime();
        $where = "";

        //$order_id = 60;
        if (isset($order_id) && !empty($order_id)) {
            $order_id = intval(trim($order_id));
            $where .= " AND a.`id` = " . $order_id;
        }
        if (isset($order_sn) && !empty($order_sn)) {
            $where .=" AND a.`order_sn` ='" . $order_sn . "'";
        }


        //查询商品详情
        //$sql = "select a.*,b.`order_amount`,b.`money_paid`,b.`money_unpaid`,r.out_order_sn from `base_order_info` AS a,  `app_order_account` AS b , rel_out_order AS r WHERE a.`id`=b.`order_id` AND a.`id`=`r`.`order_id`" .$where." ;";
        $sql = "SELECT a . * , b.`order_amount` , b.`money_paid` , b.`money_unpaid` ,(select GROUP_CONCAT(DISTINCT rod.out_order_sn) from rel_out_order as rod where rod.order_id=a.id group by rod.order_id) as out_order_sn  FROM `base_order_info` AS a , `app_order_account` AS b WHERE a.`id` = b.`order_id` " . $where . " ;";
         $row = $this->db->getRow($sql);

        // 记录日志
        //$reponse_time = microtime() - $s_time;
        //$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
        //返回信息
        if ($row) {
            return $row;
        } else {
            return false;
        }
    }
    
    
    
    /**
     * 验证转单流水号是否存在
     */
    public function checkReturnGoods($val){
        $s_time = microtime();
        if (isset($val) && $val!='') {
            $sql = "SELECT * FROM `app_return_goods` WHERE `return_id`='".$val."'";
            $res = $this->db->getRow($sql); 
        }else{
          return false;
        }

        // 记录日志
        $reponse_time = microtime() - $s_time;
    //    $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        if(!$res){
            return "没有查到相应的信息";
        }else{
         return $res;
        }
    }

    
    
     /**
     * 更新订单表中的部分数据
     */
    public function updateOrderInfoByOrderId($order_id,$amount_deposit,$pay_date) {
        if(isset($order_id) && !empty($order_id)){
            $where = " WHERE `order_id` = {$order_id}";
        }
        if(empty($amount_deposit) || !isset($where)){
           return false;
        }
        try{
            $order_info = $this->db->getRow("SELECT `oa`.`order_amount`,`oa`.`money_paid`,`oa`.`money_unpaid`,`oi`.`order_pay_status`,`oi`.referer,`oi`.`order_sn` FROM `app_order_account` as `oa`,`base_order_info` as `oi` where `oa`.`order_id`=`oi`.`id` and `oa`.`order_id`={$order_id}");
            $order_amount = $order_info['order_amount'];
            $money_paid = $order_info['money_paid'] + $amount_deposit;
            $money_unpaid = $order_info['money_unpaid'] - $amount_deposit;
            $referer = $order_info['referer'];
            $order_sn = $order_info['order_sn'];
            if($money_unpaid<=0){
                $new_order_pay_status = 3;
            }else{
                $new_order_pay_status = 2;
            } 
            //天生一对加盟商
            if($referer == "天生一对加盟商"){
                //查询是否全部已配货
                $sql = "select count(*) from app_order_details where order_id={$order_id} and delivery_status<>5";
                $delivery_flag = $this->db->getOne($sql);
                if($delivery_flag){
                    //未全部配货,商品配货状态不全是已配货，当已付金额小于订单金额，订单更新为支付定金状态；
                    if($money_paid < $order_amount) {
                        $new_order_pay_status = 2;
                    }else {
                        $new_order_pay_status = 3;
                    }                        
                }else{
                    //已经全部配货,指定A=订单已付金额-所有商品的批发价，当A≥0，更新为已付款状态，当A＜0，更新为支付定金状态
                    $sql = "select sum(shijia) from warehouse_shipping.warehouse_bill_goods where order_sn='{$order_sn}'";
                    $shijia_amount = $this->db->getOne($sql);
                    if($money_paid>=$shijia_amount){
                         $new_order_pay_status = 3;
                    }else{
                         $new_order_pay_status = 2;
                    }
                }
            
            }
			$sql = "UPDATE `base_order_info` SET `order_pay_status`={$new_order_pay_status} WHERE `id`={$order_id}";
            //file_put_contents("1.txt",$sql);
			$this->db->query($sql);
            //第一次点款时间
            if($pay_date != '0000-00-00 00:00:00'){
                $sql = "UPDATE `base_order_info` SET `pay_date`='{$pay_date}' WHERE `id`={$order_id} and (pay_date is null or pay_date ='0000-00-00 00:00:00') limit 1;";
                $this->db->query($sql);
            }
            
            $sql = "UPDATE `app_order_account` SET `money_paid`={$money_paid},`money_unpaid`={$money_unpaid} $where limit 1;";
            $res = $this->db->query($sql);
            
            
            return true;
        }catch (Exception $e){
            return false;
        }        
    }
    
    
    
    
    
    /**
    * 查询订单信息 (关联支付 app_order_account)
    * @param $order_id
    * @return json
    */
	public function GetOrderInfo($order_id)
	{
        //var_dump(__FUNCTION__);
		$s_time = microtime();
		$where = "";

		//$order_id = 60;
		if(isset($order_id) && !empty($order_id))
		{
            $order_id=intval(trim($order_id));
			$where .= " AND a.`id` = " . $order_id;
        }
		if(isset($order_sn) && !empty($order_sn))
		{
			$where .=" AND a.`order_sn` ='".$order_sn."'";
		}


        //查询商品详情
       //$sql = "select a.*,b.`order_amount`,b.`money_paid`,b.`money_unpaid`,r.out_order_sn from `base_order_info` AS a,  `app_order_account` AS b , rel_out_order AS r WHERE a.`id`=b.`order_id` AND a.`id`=`r`.`order_id`" .$where." ;";
       $sql = "SELECT a . * , b.`order_amount` , b.`money_paid` , b.`money_unpaid` ,(select GROUP_CONCAT(DISTINCT rod.out_order_sn) from rel_out_order as rod where rod.order_id=a.id group by rod.order_id) as out_order_sn FROM `base_order_info` AS a , `app_order_account` AS b WHERE a.`id` = b.`order_id` " .$where." ;";
       $row = $this->db->getRow($sql);

		// 记录日志
		$reponse_time = microtime() - $s_time;
		//$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			return false;
		}else{
			return $row;
		}
	}
	public function GetOrderInfoByOrderSn($order_sn)
	{
		//var_dump(__FUNCTION__);
	
		if(empty($order_sn))
		{
			return null;
	
		}
	
		//查询商品详情
		//$sql = "select a.*,b.`order_amount`,b.`money_paid`,b.`money_unpaid`,r.out_order_sn from `base_order_info` AS a,  `app_order_account` AS b , rel_out_order AS r WHERE a.`id`=b.`order_id` AND a.`id`=`r`.`order_id`" .$where." ;";
		 $sql = "SELECT id,order_status,send_good_status,order_pay_status,delivery_status FROM `base_order_info`  WHERE `order_sn` ='$order_sn' LIMIT 1";
		$row = $this->db->getRow($sql);
	
		//返回信息
	
		return $row;
	
	}


    public function getOrderInfoByBespokeId($bespoke_id) {
        $sql = "SELECT * FROM `base_order_info` where bespoke_id=".$bespoke_id;
        return $this->db->getAll($sql);
    }
     /**
     * 获取订单商品信息
     */
    public function getOrderDetailByOrderId($data) {
        $res = 0;
        if(isset($data['order_id']) && !empty($data['order_id'])){
            //$select = "`id`, `order_id`, `goods_id`, `goods_sn`, `goods_name`, `goods_price`, `goods_count`, `create_time`, `modify_time`, `create_user`, `details_status`, `send_good_status`, `is_stock_goods`, `is_return`, `details_remark`,`goods_type`";
            $select = "*";
            if(isset($data['select']) && !empty($data['select'])){
                $select = $data['select'];
            }

            $sql = "SELECT {$select} FROM `app_order_details` WHERE `order_id`={$data['order_id']} ";
            if(isset($data['goods_id']) && !empty($data['goods_id'])){
                $sql.= " AND `goods_id`='".$data['goods_id']."'";
            }
            if (isset($data['is_return']) && $data['is_return'] !='') {
                $sql .= " and `is_return` = {$data['is_return']}";
            }
               
            $res = $this->db->getAll($sql);
        }

        if(!$res)
		{
              return false;
		}
		else
		{
			return $res;
		}
    }
    
    
    
    
	/**
	 * 订单日志接口
	 */
	public function mkOrderLog($data)
	{
		$s_time = microtime();
		if(!isset($data['remark']) || !isset($data['create_user'])){
			$result ['error'] = '参数错误!';
			Util::jsonExit($result);
		}

		if(!isset($data['order_id']) || isset($data['order_sn'])){
			$sql = "SELECT `id` FROM `base_order_info` WHERE `order_sn` = '".$data['order_sn']."'";
			$newdo['order_id'] = $this->db->getOne($sql);
		}

		if(isset($data['order_id'])){
			$newdo['order_id'] = $data['order_id'];
		}

		$newdo['remark'] = $data['remark'];
		$newdo['create_user'] = $data['create_user'];
		$newdo['create_time'] = date('Y-m-d H:i:s');

		$newdo['order_status'] = (isset($data['order_status']))?$data['order_status']:'0';
		$newdo['shipping_status'] = (isset($data['shipping_status']))?$data['shipping_status']:'0';
		$newdo['pay_status'] = (isset($data['pay_status']))?$data['pay_status']:'0';

		$sql = "INSERT INTO `app_order_action` (";
		$label = '';$val = '';
		foreach ($newdo as $k=>$v) {
			$label .= "`".$k."`,";
			$val .= "'".$v."',";
		}
		$label = rtrim($label,',');$val = rtrim($val,',');
		$sql .= $label.") VALUES (".$val.")";
		$res = $this->db->query($sql);

		// 记录日志
		$reponse_time = microtime() - $s_time;
		//$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

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
	
	
	public function updateBaseOrderInfoPayStatus($id){
		$sql = "UPDATE `base_order_info` SET `order_pay_status`=3 WHERE id = {$id}";
		return $res = $this->db->query($sql);
	}
    public function updateBaseOrderInfoBespokeId($id, $bespoke_id){
        $sql = "UPDATE `base_order_info` SET `bespoke_id`={$bespoke_id} WHERE id = {$id}";
        return $this->db->query($sql);
    }
    // 根据转单流水号，取订单信息
	public function getOrderInfoByZhuandanNo($zhuandan_no) {
        $sql = "select * from `base_order_info` a join app_return_goods b on a.id=b.order_id WHERE b.return_id = {$zhuandan_no}";
        return $this->db->getRow($sql);
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
	
	
		
		$sql = "UPDATE `base_order_info` SET `delivery_status`='2'  WHERE `order_sn`='{$order_sn}' and `delivery_status` = 1";
		$row=$this->db->query($sql);
			
	
		//返回信息
		if(!$row){
			return false;
		}else{
			return true;
		}
	}
	//查询订单列表
	public function getOrderAccountInfoByOrderSn($order_sn) {	    
        $select = "`oi`.*, `oa`.`order_amount`, `oa`.`money_paid`, `oa`.`money_unpaid`, `oa`.`goods_return_price`, `oa`.`real_return_price`, `oa`.`shipping_fee`, `oa`.`goods_amount`,`oi`.`department_id`,`oi`.`apply_close`,`oi`.`order_status`,`oi`.`order_pay_status` ";
        $sql = "SELECT {$select} FROM `base_order_info` as `oi` LEFT JOIN `app_order_account` as `oa` ON `oi`.`id`=`oa`.`order_id` WHERE `oi`.`order_sn`='{$order_sn}'";
        return $this->db()->getRow($sql);
	}
    /**
     * 更新单条订单点款金额
     * @param  $params 
     * array('order_info'=>"订单信息",'pay_money'=>"点款金额",'pay_type'=>'点款类型')
     * @return array 
     * array('error'=>"状态码，0成功，1失败",'msg'=>'消息提示')
     * gaopeng
     */
	public function  updataOrderForDiankuan($params){
	    $result = array('error'=>0,'msg'=>'');
	    
	    if(empty($params['pay_type'])){
	        return array('error'=>1,'msg'=>'pay_type参数为空');
	    }else{
	        $pay_type=$params['pay_type'];
	    }
	    if(empty($params['pay_money'])){
	        return array('error'=>1,'msg'=>'支付金额不能小于等于0');
	    }else{
	        $pay_money = $params['pay_money'];
	    }
	    if(empty($params['order_info'])){
	        return array('error'=>1,'msg'=>'order_info参数为空');
	    }
	    $orderInfo = $params['order_info'];
	    $order_id = $orderInfo['id'];
	    $order_sn = $orderInfo['order_sn'];
	    $order_pay_status = $orderInfo['order_pay_status'];
	    $referer = $orderInfo['referer'];
	    $order_status = $orderInfo['order_status'];
	    $order_amount = $orderInfo['order_amount'];
	    $money_paid   = $orderInfo['money_paid'];
	    $money_unpaid = $orderInfo['money_unpaid'];
	    $is_xianhuo   = $orderInfo['is_xianhuo'];
	    $delivery_status = $orderInfo['delivery_status'];
	    $shipping_status = $orderInfo['send_good_status'];
	    $time = date('Y-m-d H:i:s');
	    
	    if($pay_money > $money_unpaid){
	        return array('error'=>1,'msg'=>"支付金额不能大于未付款金额：".$money_unpaid."元");
	    }
	    $new_money_paid = $money_paid+$pay_money;//新已支付金额=原始已付金额+新点款金额
	    $new_money_unpaid = $money_unpaid-$pay_money;//新未付金额=原始未金额-新点款金额
	    $new_delivery_status = $delivery_status;
	    $new_shipping_status = $shipping_status;
	    if($order_status !=2){
	        return array('error'=>1,'msg'=>'订单不是已审核状态，不能点款');
	    }
	    if($order_pay_status==3){
	        return array('error'=>1,'msg'=>'订单已经付款，不能再点款了');
	    }else if($order_pay_status==4){
	        return array('error'=>1,'msg'=>'财务备案订单不能点款');
	    }else if($referer != '婚博会' && $referer != "天生一对加盟商"){
	        //付款不能小于总金额的一半
            $perlimit=0.5;
            $perlimit_str='50';
            if(time()>strtotime("2017-11-15")){
                $perlimit=1.0;
                $perlimit_str='100'; 
            }	        
	        $tmp_amount = $order_amount*$perlimit-$money_paid;
	        if($pay_money < $tmp_amount){
	            return array('error'=>1,'msg'=>"支付金额不能小于：".$tmp_amount."元 (订单总金额的".$perlimit_str."%-已付金额)");
	        }
	    }
	    
	    if($new_money_paid<$new_money_unpaid){
	        $new_order_pay_status = 2;//支付定金
	    }else{
	        $new_order_pay_status = 3;//付全款
	    }	

	    //天生一对加盟商
	    if($referer == "天生一对加盟商"){
	        //天生一对加盟商的订单，第一次点款金额必须大于等于零售价的30%  BOSS-1133
	        $tsyd_percent = $order_amount>0?$pay_money/$order_amount:1;
	        if($order_pay_status==1 && $tsyd_percent<0.3){
	            return array('error'=>1,'msg'=>"天生一对加盟商的订单，第一次点款金额不能小于总金额的30%");
	        }
	        //查询是否全部已配货
	        $sql = "select count(*) from app_order_details where order_id={$order_id} and delivery_status<>5";
	        $delivery_flag = $this->db->getOne($sql);
	        if($delivery_flag){
	            //未全部配货,商品配货状态不全是已配货，当已付金额小于订单金额，订单更新为支付定金状态；
	            if($new_money_paid < $order_amount) {
	                //file_put_contents('1.txt',$new_money_paid.'-'.$order_amount);
	                $new_order_pay_status = 2;
	            }else {
	                $new_order_pay_status = 3;
	            }
	        }else{
	            //已经全部配货,指定A=订单已付金额-所有商品的批发价，当A≥0，更新为已付款状态，当A＜0，更新为支付定金状态
	            $sql = "select sum(shijia) from warehouse_shipping.warehouse_bill_goods where order_sn='{$order_sn}'";
	            $shijia_amount = $this->db->getOne($sql);
	            if($new_money_paid>=$shijia_amount){
	                $new_order_pay_status = 3;
	            }else{
	                $new_order_pay_status = 2;
	            }
	        }
	    
	    }
        try{
            if ($is_xianhuo==1) {
                $new_delivery_status = 2;
                $doTip = "修改支付状态和配货状态";                
                $sql = "UPDATE base_order_info SET order_pay_status={$new_order_pay_status},delivery_status={$new_delivery_status} WHERE id={$order_id}";
                $this->db->query($sql);
                $doTip = "修改付款金额";
                $sql = "UPDATE app_order_account SET money_paid={$new_money_paid},money_unpaid={$new_money_unpaid} WHERE order_id={$order_id}"; 
                $this->db->query($sql);            
            }else{      
                $doTip = "修改付款金额";
                $sql = "UPDATE app_order_account SET money_paid={$new_money_paid},money_unpaid={$new_money_unpaid} WHERE order_id={$order_id}";
                $this->db->query($sql);
                $doTip = "查询期货订单布产单是否已经出厂";
                $sql="select count(d.id) from  base_order_info o ,app_order_details d where o.id=d.order_id and o.id='".$order_id."' and d.id is not null and d.is_stock_goods=0 and d.buchan_status<>9 and d.buchan_status<>11 and d.is_return<>1";
                $rescount=$this->db->getOne($sql);
                if($rescount==0){
                    $new_delivery_status = 2;
                }
                $doTip = "修改支付状态和配货状态";
                $sql = "UPDATE base_order_info SET order_pay_status={$new_order_pay_status},delivery_status={$new_delivery_status} WHERE id={$order_id}";
                $this->db->query($sql);                
            } 
            $doTip = "更改订单首次付款时间";
            $sql = "Update base_order_info set pay_date='".$time."' where id='".$order_id."' AND (pay_date is null or pay_date ='0000-00-00 00:00:00')";
            $this->db->query($sql);
            //订单日志的生成            
            $user = isset($_SESSION['userName'])?$_SESSION['userName']:'未知';
            $remark = "通过批量点款 支付金额 {$pay_money}元";
            $doTip = "添加订单日志";
            $sql = "insert into app_order_action (`order_id`,`order_status`,`shipping_status`,`pay_status`,`create_user`,`create_time`,`remark`) VALUES ({$order_id},{$order_status},{$new_shipping_status},{$new_order_pay_status},'{$user}','{$time}','{$remark}')";
            $this->db->query($sql);            
            return array('error'=>0,'msg'=>'批量修改成功');
        }catch (Exception $e){
            return array('error'=>1,'msg'=>"{$action}失败,系统运行以下SQL时发生异常:".$sql);
        }	
	   
	}	
	//查询app_order_details表数据
	public function selectAppOrderDetails($field,$where,$type=2){
	    return $this->select($field,$where,$type,"app_order_details");
	}
    //更改diamond_info表数据
	public function updateDiamondInfo($data,$where){
	   $model = new SelfDiamondModel(20);
	   return $model->updateDiamondInfo('diamond_info',$data, $where);
	}
	
	public function addBuchan(){
	    
	}
	
	
	
	
	public function UpdateOrderInfoModiy($order_sn,$order_pay_status=4){
		if(empty($order_sn)){
			$result['error']=0;
			$result['msg'] ="订单号为空";
			return $result;
		}
		//第一次点款时间
		if($order_pay_status==4){
			$pay_date=",pay_date='".date("Y-m-d H:i:s",time())."'";
		}else{
			$pay_date='';
		}
		$sql = "SELECT id,order_pay_status,delivery_status, order_status  FROM `base_order_info`  WHERE `order_sn` ='$order_sn' ;";
		$orderInfoArr = $this->db->getRow($sql);
		if(empty($orderInfoArr)){
			$result['error']=0;
			$result['msg'] ="订单号 ".$order_sn." 不存在";
			return $result;			
		}
		$pdo=$this->db()->db();
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
			$pdo->beginTransaction(); //开启事务
			$sql="UPDATE `base_order_info` SET order_pay_status={$order_pay_status} {$pay_date} WHERE id='{$orderInfoArr['id']}' limit 1;";
			$res=$pdo->query($sql);
			if(!$res){
				$pdo->rollback(); //事务回滚
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$result['error']=0;
			    $result['msg'] ="未付款转换财务备案失败！";
			    return $result;	
			}
			$sql="SELECT * FROM app_order_details WHERE order_id={$orderInfoArr['id']} AND is_return != 1 limit 1";
			$order_detail_data=$this->db()->getRow($sql);
			if(!empty($order_detail_data && $orderInfoArr['order_status']==2)){
				$is_peihuo = true;
			}else{
				$is_peihuo = false;
			}
				
			$sql="SELECT * FROM app_order_details WHERE order_id={$orderInfoArr['id']} AND is_stock_goods = 0 AND buchan_status != 9 AND buchan_status != 11 AND is_return != 1  limit 1";
			$orderDetailsArr=$this->db()->getRow($sql);
			if(!empty($orderDetailsArr)){
				$is_peihuo = false;
			}
				
				
			if($is_peihuo){
				
				$sql = "UPDATE `base_order_info` SET `delivery_status`='2'  WHERE `order_sn`='{$order_sn}' and `delivery_status` = 1";
		        $res1=$pdo->query($sql);
		        if(!$res1){
		        	$pdo->rollback(); //事务回滚
		        	$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		        	$result['error']=0;
		        	$result['msg'] ="修改配货状态失败！";
		        	return $result;
		        }
				
			}
				
				
		}catch (Exception $e){
			$pdo->rollback(); //事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			$result['error']=0;
			$result['msg'] ="系统异常！error code:".$e;
			return $result;
		}
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
		$pdo->commit();
		$result['error']=1;
		$result['msg'] ="";
		return $result;
	}
	
	
	
	/*
	 * 添加订单操作日志
	 */
	public function addOrderAction($actionArr){
		
		if(empty($actionArr['order_id'])){
			$result['error'] = 0;
			$result['msg'] = "订单id不能为空";
			return $result;
		}
		if(empty($actionArr['order_status'])){
			$result['error'] = 0;
			$result['msg'] = "缺省参数order_status";
			return $result;
		}
		if(empty($actionArr['shipping_status'])){
			$result['error'] = 0;
			$result['msg'] = "缺省参数shipping_status";
			return $result;
		}
		if(empty($actionArr['pay_status'])){
			$result['error'] = 0;
			$result['msg'] = "缺省参数pay_status";
			return $result;
		}
		

		$order_id=intval(trim($actionArr['order_id']));
	
		$action_field = " `order_id`, `order_status`, `shipping_status`, `pay_status`,`create_time`,`create_user`,`remark`";
		$action_value = "".$order_id." ,".$actionArr['order_status']." ,".$actionArr['shipping_status']." ,".$actionArr['pay_status'].", '".date("Y-m-d H:i:s")."' , '".$_SESSION['userName']."', '".$actionArr['remark']."' ";
		$sql = "INSERT INTO `app_order_action` (" . $action_field . ") VALUES (". $action_value .")";
		$row = $this->db()->query($sql);
	
		//返回信息
		if(!$row){
			$result['error'] = 0;
			$result['msg'] = "添加订单操作日志失败";
			return $result;
		}else{
			$result['error'] = 1;
			$result['msg'] = "";
			return $result;
		}
	}
	//天生一对，取消点款，订单明细配货状态修改
	function UpdateOrderDetailDeliveryStatus($order_sn){
	    $sql="SELECT aod.id,aod.delivery_status FROM app_order_details AS aod LEFT JOIN base_order_info AS b ON b.id=aod.order_id WHERE b.order_sn='{$order_sn}'";
		$orderDetailsArr=$this->db()->getAll($sql);
		$id_str='';
		foreach ($orderDetailsArr as $k=>$v){
			if($v['delivery_status']==2){
				$id_str.=$v['id'].',';
			}
		}
		$id_str=rtrim($id_str,',');
		if($id_str!=''){
		  $sql="UPDATE app_order_details SET delivery_status=1 WHERE id in ({$id_str})";
		  $this->db()->query($sql);
		}
		//return $sql;
		
	}	/**
	 * 更改订单明细表
	 * @param unknown $data
	 * @param unknown $where
	 */
	public function updateAppOrderDetail($data,$where){
	    $sql = $this->updateSql("app_order_details", $data, $where);
	    return $this->db()->query($sql);
	}
	/**
	 * 更改订单主表信息
	 * @param unknown $data
	 * @param unknown $where
	 */
	public function updateBaseOrderInfo($data,$where){
	    $sql = $this->updateSql("base_order_info", $data, $where);
	    return $this->db()->query($sql);
	}
	/**
	 * 添加订单操作日志
	 * @param unknown $order_sn
	 * @param unknown $remark
	 * @return unknown
	 */
	public function AddOrderLog($order_sn,$remark)
	{
	    //根据布产号查布产状态和布产类型
	    $create_user=$_SESSION['userName']?$_SESSION['userName']:'第三方';
	
	    $sql="select id,order_status,send_good_status,order_pay_status from base_order_info where order_sn='{$order_sn}'";
	    $row=$this->db->getRow($sql);
	
	    $sql="insert into app_order_action(`order_id`,`order_status`,`shipping_status`,`pay_status`,`create_user`,`create_time`,`remark`) values ('{$row['id']}','{$row['order_status']}','{$row['send_good_status']}','{$row['order_pay_status']}','{$create_user}','".date('Y-m-d H:i:s')."','{$remark}')";
	    //echo $sql;
	    $res = $this->db->query($sql);
	    return $res;
	}


    /**
     * @param $zhuandan_no
     * @param string $filed
     * @return mixed
     * 通过流水号获取订单的持有积分、新老订单的积分差
     */
    public function getOrderAccountByZhuandanNo($zhuandan_no,$filed="current_point,old_point"){
        $sql = "SELECT {$filed} FROM app_order_account AS aa INNER JOIN base_order_info AS bi ON aa.order_id = bi.id INNER JOIN app_return_goods AS ag  ON bi.id = ag.order_id where ag.return_id = {$zhuandan_no}";
        return $this->db->getRow($sql);
    }

    /**
     * @param $old_current_point
     * @param $old_old_point
     * 进行订单积分变更计算
     */
    public function udpateOrderAccountPoint($new_order_id,$old_current_point,$old_old_point){
        $account_sql = "SELECT * FROM app_order_account order_id = {$new_order_id}";
        $order_account_info = $this->db->getRow($account_sql);
        if(!$order_account_info){
            return false;
        }
        $old_point = $order_account_info['old_point'] + ($old_current_point-$old_current_point);
        $update_account_sql = "UPDATE app_order_account SET old_point={$old_point}";
        return $this->db->query($update_account_sql);
    }
}

?>