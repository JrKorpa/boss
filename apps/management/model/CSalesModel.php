<?php
/**
 * 销售 跨模块Model
 * C开头的Model 为 跨模块Model 可被不同模块下的 SelfSalesModel 继承
 *  -------------------------------------------------
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: gaopeng
 *   @date		: 2017-05-12 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class CSalesModel extends SelfModel
{
    protected $db;
	function __construct ($strConn="")
	{
		parent::__construct($strConn);
	}
	/**
	 * 更改 app_order_details 表
	 * @param unknown $newdo
	 * @param unknown $where
	 */
	public function updateAppOrderDetails($newdo,$where){
	    $sql = $this->updateSql('app_order_details',$newdo, $where);
	    return $this->db()->query($sql);
	    
	}
	/**
	 * 更改 base_order_info 表
	 * @param unknown $newdo
	 * @param unknown $where
	 */
	public function updateBaseOrderInfo($newdo,$where){
	    $sql = $this->updateSql('base_order_info',$newdo, $where);
	    return $this->db()->query($sql);
	     
	}
	
	public static function createOrderSn(){
	    switch (SYS_SCOPE){
	        case 'boss':
	            return date('Ymd').mt_rand(3,9).str_pad(mt_rand(1, 99999),5,'1',STR_PAD_LEFT);
	        case "zhanting":
	            return '9'.date('Ymd').mt_rand(3,9).str_pad(mt_rand(1, 99999),5,'1',STR_PAD_LEFT);
	        default:
	            die();
	    }
	}	
	public function addBaseOrderInfo($newdo){
	    $sql = $this->insertSql($newdo,"base_order_info");
	    return $this->db()->query($sql);
	}
	/**
	 * 创建订单
	 * @param array $order 订单主表 
	 * @param array(array) $goodslist 订单商品列表
	 * @param array $money  订单金额表
	 * @param array $address 订单地址表 (非必创建项)
	 * @param array $address 订单发票表
	 * @param array $transMode 自动事物
	 */
	public function createOrder($order,$goodslist,$money,$invoice,$address=array(),$out_order_sn=null,$order_log='',$transMode = true){
	    $result = array('success'=>0,'error'=>'','returnData'=>'');
	    if(empty($order)){
	        $result['error'] = "参数order不能为空！";
	        return $result;
	    }
	    if(empty($goodslist)){
	        $result['error'] = "参数goodslist不能为空！";
	        return $result;
	    }
	    if(empty($money)){
	        $result['error'] = "参数money不能为空！";
	        return $result;
	    }
	    if(empty($invoice)){
	        $result['error'] = "参数invoice不能为空！";
	        return $result;
	    }
	    
	    $pdo = $this->db()->db();//pdo对象
	    try{
	        if($transMode==true){
	            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
	            $pdo->beginTransaction();//开启事务
	        }
	        $time=date('Y-m-d H:i:s');
	        //订单主表创建
	        $tip = "创建订单主表";
	        $sql = $this->insertSql($order,'base_order_info');
	        $pdo->query($sql);	        
            $order_id = $pdo->lastInsertId();
            $order['id'] = $order_id;
            $returnData['order'] = $order;
            
            $tip = "创建订单商品信息";
            foreach ($goodslist as $goods){

                $goods['order_id'] = $order_id;
                $sql = $this->insertSql($goods,'app_order_details');
                $pdo->query($sql);
                $goods['id'] = $pdo->lastInsertId();
                $goods['order_sn'] = $order['order_sn']; 
                if(!empty($goods['goods_id'])){
                    $tip = "绑定货号";
                    $sql = "update warehouse_shipping.warehouse_goods set order_goods_id='{$goods['id']}' where goods_id='{$goods['goods_id']}'";
                    $pdo->query($sql);
                }
                                
                $returnData['goods'][] = $goods;
            }
            
            $tip = "创建订单金额信息";
            $money['order_id'] = $order_id;
            $sql = $this->insertSql($money,'app_order_account');
            $pdo->query($sql);

            //创建地址，非必创建项
            if(!empty($address)){
                $tip = "创建订单收货地址信息";
                $address['order_id'] = $order_id;
                $sql = $this->insertSql($address,'app_order_address');
                $pdo->query($sql);
            }
            $tip = "创建订单发票信息";
            $invoice['order_id'] = $order_id;
            $sql = $this->insertSql($invoice,'app_order_invoice');
            $pdo->query($sql);
            
            $tip = "创建订单日志";            
            $this->addOrderLog($order['order_sn'],$order_log);
            
            
	        if($transMode==true){
	            $pdo->commit();//如果没有异常，就提交事务
	            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
	        }
	        $result['success'] = 1;
	        $result['returnData'] = $returnData;
	    }catch(Exception $e) {//捕获异常
            if($transMode==true){
                $pdo->rollback();//事务回滚
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            }   
            $result['error'] = $tip."失败,".$e->getMessage();            
        }
        
        return $result;
	}
	
	public function addOrderLog($order_sn,$remark)
	{
	    //根据布产号查布产状态和布产类型
	    $create_user=$_SESSION['userName']?$_SESSION['userName']:'第三方';	
	    $sql="select id,order_status,send_good_status,order_pay_status from base_order_info where order_sn='{$order_sn}'";
	    $row=$this->db()->getRow($sql);	
	    $sql="insert into app_order_action(`order_id`,`order_status`,`shipping_status`,`pay_status`,`create_user`,`create_time`,`remark`) values ('{$row['id']}','{$row['order_status']}','{$row['send_good_status']}','{$row['order_pay_status']}','{$create_user}','".date('Y-m-d H:i:s')."','{$remark}')";
	    $res = $this->db()->query($sql);	
	    return $res;
	}
    
}

?>