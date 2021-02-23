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
	 * 根据订单号查询(替换Sales/Api/api->GetOrderInfoBySn方法)
	 */
	public function getOrderInfoBySn($order_sn) 
	{   
	    if(!empty($order_sn)){
	        $select = "`oi`.*, `oa`.`order_amount`, `oa`.`money_paid`, `oa`.`money_unpaid`, `oa`.`goods_return_price`, `oa`.`real_return_price`, `oa`.`shipping_fee`, `oa`.`goods_amount`,`oi`.`department_id`,`oi`.`apply_close`,`oi`.`order_status`,`oi`.`order_pay_status`,oi.apply_return ";
	        $sql = "SELECT {$select} ,(select GROUP_CONCAT(DISTINCT rod.out_order_sn) from rel_out_order as rod where rod.order_id=`oi`.id group by rod.order_id) as out_order_sn FROM `base_order_info` as `oi` LEFT JOIN `app_order_account` as `oa` ON `oi`.`id`=`oa`.`order_id` WHERE `oi`.`order_sn`='{$order_sn}'";
	        $res = $this->db()->getRow($sql);
	        return $res;
	    }else{
	        return false;
	    }    
	}
	
	public function getGoodsInfoByZhengshuhao($zhengshuhao,$order_id=''){
	    //取出非本订单的证书号关联的商品（主要是祼钻的戒托的关系）
	    if($order_id){
	        $w .= " AND d.order_id !=".$order_id." ";
			$w .= " AND d.is_return =0 ";
			$w .= " AND d.is_peishi =0 ";
	    }
	    
	    $sql = "SELECT b.*,d.zhengshuhao,d.id as detail_id,d.goods_type,d.goods_sn,d.goods_name FROM `app_order_details` AS d,`base_order_info` AS b WHERE b.`id` = d.`order_id` AND b.order_status=2 AND d.goods_type != 'lz' AND d.`zhengshuhao` = '".$zhengshuhao."' $w";
	    return $this->db->getRow($sql);
	}
	
	/**
	 * 根据订单号查询订单明细
	 */
	public function getOrderDetailsBySn($order_sn)
	{
	    if(!empty($order_sn)){
	        $select = "`og`.* ";
	        $sql = "SELECT {$select} FROM app_order_details as og left join `base_order_info` as `oi`  ON `oi`.`id`=`og`.`order_id` WHERE `oi`.`order_sn`='{$order_sn}'";
	        $res = $this->db()->getAll($sql);
	        return $res;
	    }else{
	        return false;
	    }
	}
	/**
	 * 订单发货时验证是否有库管审核的退款单(替换Sales/Api/api->isHaveGoodsCheck方法)
	 * @param type $order_sn
	 * $is_true = 1  可以配货，0 不能配货
	 */
	public function checkOrderTuikuan($order_sn)
	{
	    if(empty($order_sn)){
	        return false;
	    }	    
	    $sql = "SELECT count(1) as num,`rg`.`check_status`,`rg`.`return_id` FROM `app_return_goods` as `rg` WHERE `rg`.`order_goods_id` !=0 AND `order_sn` = '{$order_sn}' order by `rg`.`return_id` desc limit 1";
	    $row = $this->db()->getRow($sql);
	    $is_true = 0;
	    if($row['num']==0){
	        $is_true = 1;
	    }elseif ($row['check_status'] == 0) {
	        $_sql = "SELECT `leader_status` FROM `app_return_check` WHERE `return_id`={$row['return_id']}";
	        $leader_status = $this->db()->getOne($_sql);
	        if($leader_status == 2){
	            $is_true = 1;
	        }
	    }elseif ($isHave['check_status'] == 5) {
	        $is_true = 1;
	    }
	    return $is_true;
	}
	/**
	 * 根据订单编号获取订单记录
	 */
	public function getBaseOrderInfoBySn($order_sn){
	    if(empty($order_sn)){
	        return false;
	    }
	    $sql = "select o.*,s.channel_class from base_order_info o left join cuteframe.sales_channels s on o.department_id=s.id WHERE o.`order_sn`='{$order_sn}'";
	    $result = $this->db()->getRow($sql);
	    return $result;
	}
	
	public function getAddressByOrderSn($order_sn){
	    $sql = "select a.*,o.order_sn,o.send_good_status,(select region_name from cuteframe.region r where r.region_id=a.province_id) as province ,(select region_name from cuteframe.region r where r.region_id=a.city_id) as city,(select region_name from cuteframe.region r where r.region_id=a.regional_id) as district,o.customer_source_id,o.order_pay_type from app_order_address a left join base_order_info o on a.order_id=o.id where order_sn='{$order_sn}' limit 1";
	    return $this->db()->getRow($sql);
	}
	
	/**
	 * 更改订单商品发货状态,回写快递单号(By:order_sn)
	 */
	public function setOrderGoodsSend($data){
	    
	    if(empty($data['order_sn'])){
	        return false;
	    }else{
	        $order_sn = $data['order_sn'];
	    }
	    
	    if(!empty($data['freight_no']))
	    {
	        $freight_no = $data['freight_no'];
	    }
	    else
	    {
	        $freight_no = '';
	    }
	    try{
    	    $sql = "update base_order_info as a left join app_order_details as b on a.id=b.order_id set a.send_good_status='2',a.shipfreight_time='".date("Y-m-d H:i:s",time())."',b.send_good_status=2 where a.order_sn='{$order_sn}' ";
    	    $res1= $this->db->query($sql);
    	    //回写快递号
    	    $sql = "UPDATE `app_order_address` SET freight_no='{$freight_no}' WHERE `order_id` = (SELECT `id` FROM `base_order_info` WHERE `order_sn` = '".$order_sn."')";
    	    $res2 = $this->db->query($sql);
    	    if($res1 !==false && $res2 !==false){
    	        return true;
    	    }else{
    	        return false;
    	    }
	    }catch (Exception $e){
	        return false;
	    } 
	    
	}
	/**
	 * 订单日志
	 * @param unknown $data
	 * @return unknown
	 */
	public function addOrderLog($data){
	    //$sql="insert into app_order_action(`order_id`,`order_status`,`shipping_status`,`pay_status`,`create_user`,`create_time`,`remark`) values ('{$row['id']}','{$row['order_status']}','{$row['send_good_status']}','{$row['order_pay_status']}','{$create_user}','".date('Y-m-d H:i:s')."','{$remark}')";
        $sql = $this->insertSql($data,"app_order_action");
	    $res = $this->db()->query($sql);
	    return $res;
	}
	
	/**
	 * 更新订单数据
	 */
	public function updateBaseOrderInfo($data,$where) {
	    $sql = $this->updateSql("base_order_info",$data, $where);
	    $result = $this->db()->query($sql);
	    //$res = $this->db->autoExecute('base_order_info',$arr['update_fileds'],'UPDATE',$where);
	    return $result;
	    
	}
	
	/**
	 * 获取订单明细ID
	 */
	public function getRelOrderByOrderid($order_id){
	    if(empty($order_id)){
	        return false;
	    }
	    $sql = "SELECT distinct `out_order_sn` FROM `rel_out_order` WHERE order_id='{$order_id}'";
	    return $this->db->getAll($sql);	
	}
	
	
	/*
     * 根据订单id获取订单数据
     */
    public function getOrderInfoById($id) {
        if(empty($id)){
            return false;
        }
        $sql = "SELECT * FROM `base_order_info` WHERE `id`=".$id." limit 1;";
        return $this->db()->getRow($sql);
    }


    
    
    /**
     * 根据订单号查询
     */
    public function GetOrderInfoByOrderSn($order_sn,$select="*") {
    	    
    		$sql = "SELECT {$select} FROM `base_order_info` WHERE `order_sn`='{$order_sn}'";
    		return $res = $this->db->getRow($sql);
    	
    
    }
    
    
    
    /*
     * 添加订单操作日志
     */
    public function getOrderActionList($order_id, $hidden=0){
    	
    	
    	$where = " order_id = $order_id ";
        if($hidden){
            $where.=" ORDER BY `action_id` asc limit 0";
        }else{
            $where.=" ORDER BY `action_id` DESC";
        }
    
    	$action_field = " `order_id`, `order_status`, `shipping_status`, `pay_status`,`create_time`,`create_user`,`remark` ";
    	$sql = "SELECT $action_field FROM `app_order_action` WHERE $where ";
    
    	$res = $this->db->getAll($sql);
    
    	return $res;
    
    	
    }
    

	/**
	 * 获取顾客订单的顾客来源id
	 */
	public function getOrderSourceId($order_sn){
	    if(empty($order_sn)){
	        return false;
	    }
		$sql  = "SELECT customer_source_id FROM `base_order_info`
		where `order_sn` = '$order_sn' ";

	    return $this->db()->getRow($sql);
	}
	
	/**
	 * 获取顾客订单的顾客来源的姓名
	 */
	public function getOrderSourceName($order_sn){
	    if(empty($order_sn)){
	        return false;
	    }
		$sql  = "SELECT customer_source_id FROM `base_order_info`
		where `order_sn` = '$order_sn' ";
		$ret = $this->db()->getRow($sql);
		
		$csn = "";
		if(!empty($ret['customer_source_id']))
		{
		$csi = $ret['customer_source_id'];
		
		$customerSourcesModel = new CustomerSourcesModel(1);
		$csinf = $customerSourcesModel->getCustomerSourceById($csi);
        $csn = empty($csinf['source_name'])?"":$csinf['source_name'];
		}
	    return $csn;
	}
	
	/**
	 * 订单赠品详情
	 */
	public function getOrderGiftInfo($order_id) {
		$sql="SELECT `gift_id`,`remark`,`gift_num` FROM `rel_gift_order` AS `rg` LEFT JOIN `base_order_info` AS `b` ON `rg`.`order_id`=`b`.`id`  WHERE `rg`.`order_id`=$order_id AND `b`.`create_time` < '2015-10-23 00:00:00' ";
		return $this->db()->getRow($sql);
	}
	
    /**
     * 根据订单号取出订单地址信息
     */
    public function getDissInfoOrder_sn($order_sn)
    {
        # code...
        $sql = "select * from `app_order`.`base_order_info` `boi` inner join `app_order`.`app_order_address` `aoa` on `boi`.`id` = `aoa`.`order_id` where `boi`.`order_sn` = '{$order_sn}'";

        return $this->db()->getRow($sql);
    }

    /**
     * 根据订单号修改订单明细商品的发货状态；
     */
    public function updateOrderSendStatus($id_str)
    {
        # code...
        $sql = "update `app_order`.`app_order_details` `s` set `s`.`send_good_status` = 2 where `s`.`id` in ($id_str)";
        //echo $sql;die;
        return $this->db()->query($sql);
    }
    
    
    function updateBaseOrderSendStatus($order_sn){
    	$sql="select aod.delivery_status,aod.send_good_status from app_order_details as aod left join base_order_info as b on b.id=aod.order_id where b.order_sn='{$order_sn}'";
    	$deliveryArr=$this->db()->getAll($sql);
    	$row1=array_column($deliveryArr,'delivery_status');
    	$row2=array_column($deliveryArr,'send_good_status');
    	$delivery_status=min($row1);
    	if(in_array(1, $row2)){
    		$send_good_status=1;
    	}elseif(in_array(4, $row2)){
    		$send_good_status=4;
    	}else{
    		$send_good_status=2;
    	}
        $sql="update base_order_info set delivery_status={$delivery_status},send_good_status={$send_good_status} where order_sn='{$order_sn}' ";
    	return $r=$this->db()->query($sql);    	
    	
    }
    //base_order_info 表单表查询
    function selectBaseOrderInfo($filed="*",$where,$type=1){
        return $this->select($field,$where,$type,"base_order_info");
    }
	
	
	function getAddressByWholesaleId($wholesale_id){
		$sql="select tel,address from app_order_address where wholesale_id={$wholesale_id} limit 1";
		return $this->db()->getRow($sql);
	}
	
	function updateOrderInfos($row,$express_id,$freight_no){
		if(!isset($row['order_sn'])){
			return false;
		}
		$ExpView = new ExpressView(new ExpressModel(1));
		$expArr=$ExpView->getAllexp();
		$expressArr=array();
		foreach ($expArr as $v){
			$expressArr[$v['id']]=$v['exp_name'];
		}
		$express_name=$expressArr[$express_id];
		$orderInfo=$this->GetOrderInfoByOrderSn($row['order_sn'],'id');
		$order_id=$orderInfo['id'];
	    $sql="update app_order_address set express_id={$express_id} where order_id={$order_id}";
		$r=$this->db()->query($sql);
		if(!$r){
			return false;
		}
		$create_time=date("Y-m-d H:i:s",time());
		$create_user=$_SESSION['userName'];
		$remark="经销商发货：货号：".$row['goods_id'].",名称：".$row['goods_name'].",快递公司:".$express_name.",快递号:".$freight_no;
	     $sql="insert into app_order_action (order_id,create_time,create_user,remark) values({$order_id},'{$create_time}','{$create_user}','{$remark}')";
		return $r=$this->db()->query($sql);
		
	}
	
		
	
	/**
	 * 更新订单的快递方式
	 */
	public function updateAddressWay($arr) {
		$result=array('error'=>0,'msg'=>'');
		if(!isset($arr['order_sn']) || empty($arr['order_sn'])){
			$result['error']=1;
			$result['msg']='缺少参数订单号';
			return $result;
		}
		if(!isset($arr['express_id']) || empty($arr['express_id'])){
			$result['error']=1;
			$result['msg']='缺少参数快递方式';
			return $result;
		}
		if ($arr['express_id'] != 10) {//上门取货
			if(isset($arr['freight_no']) && empty($arr['freight_no'])){
				$result['error']=1;
			    $result['msg']='缺少参数快递单号';
			    return $result;
			}
		}
	
		$_sql = "select count(*) as num from `base_order_info` where `order_sn`='{$arr['order_sn']}' and `send_good_status` in (3,5)";
		$count = $this->db->getRow($_sql);
		if(0==$count['num']){
			$setValue = "`oa`.`express_id` = '{$arr['express_id']}'";
			if(isset($arr['freight_no']) && !empty($arr['freight_no'])){
				$setValue .= ",`oa`.`freight_no`='{$arr['freight_no']}'";
			}
			$sql = "UPDATE `app_order_address` as `oa`,`base_order_info` as `oi` SET $setValue WHERE `oa`.`order_id`=`oi`.`id` and `oi`.`order_sn`='{$arr['order_sn']}'";
		}else{
			    $result['error']=1;
			    $result['msg']='发货状态下的订单不允许更改快递方式';
			    return $result;
		}
		$res = $this->db->query($sql);
		if(!$res)
		{
			$result['error']=1;
			$result['msg']='更新快递方式失败';
			return $result;
		}
		else
		{
			$result['error']=0;
			return $result;
		}
	}
	
	
	/*
	 * 添加订单操作日志
	 */
	public function addOrderAction($arr){
		$result=array('error'=>0,'msg'=>'');
		if(!isset($arr['order_id'])){
			$result['error']=1;
			$result['msg']='缺少参数订单号';
			return $result;
		}
		$order_id=intval(trim($arr['order_id']));
	
		$action_field = " `order_id`, `order_status`, `shipping_status`, `pay_status`,`create_time`,`create_user`,`remark`";
		$action_value = "".$order_id." ,".$arr['order_status']." ,".$arr['send_good_status']." ,".$arr['order_pay_status'].", '".$arr['time']."' , '".$arr['user']."', '".$arr['remark']."' ";
		$sql = "INSERT INTO `app_order_action` (" . $action_field . ") VALUES (". $action_value .")";
		$res = $this->db->query($sql);
	
			
		//返回信息
		if(!$res){
			$result['error']=1;
			$result['msg']='生成订单日志失败';
			return $result;
		}else{
			$result['error']=0;
			return $result;
		}
	}
	
	/**
	 * 更改订单商品发货状态,回写快递单号(By:order_sn)
	 */
	public function updatOrderGoodsSend($order_sn){
			
		if(empty($order_sn)){
			return false;
		}
		$sql = "update base_order_info as a left join app_order_details as b on a.id=b.order_id set a.send_good_status='2',a.shipfreight_time='".date("Y-m-d H:i:s",time())."',b.send_good_status=2 where a.order_sn='{$order_sn}' ";
		$res= $this->db->query($sql);
			
		if($res !==false){
			return true;
		}else{
			return false;
		}
	
	}
	//根据订单编号查询 历史发票记录
	public function getOrderInvoice($order_sn){
	    $sql = "SELECT a.order_sn,b.order_amount,c.* FROM base_order_info a left join app_order_account b on a.id=b.order_id left join app_order_invoice c on a.id=c.order_id where a.order_sn='{$order_sn}'";
	    return $this->db()->getAll($sql);
	}


	public function getOrderInfoForInvoice($order_sn){
		$sql="select o.order_sn,r.tel as mobile,o.consignee,now() as invoicedate,o.order_pay_type,a.order_amount,(select sum(if(d.favorable_status=3,d.goods_price-d.favorable_price,d.goods_price)) from app_order_details d where d.order_id=o.id ) as dgoods_price,v.* from base_order_info o,app_order_account a, app_order_address r,app_order_invoice v where o.id=a.order_id and o.id=r.order_id 
and o.id=v.order_id and o.order_sn='{$order_sn}'";
        $order=$this->db()->getRow($sql);

        if(!empty($order)){
            //$sql="select if(g.cat_type1 is not null,case when g.cat_type1 in ('摆件','金条','套装','手镯','情侣戒','男戒','脚链','女戒','吊坠','项链','耳饰','手镯','手链') then g.cat_type1 when g.cat_type1='裸石' then '成品钻' when g.cat_type1='彩钻' then '成品钻件' else  '饰品' end,'饰品')  as goods_name,ROUND(if(d.favorable_status=3,d.goods_price-d.favorable_price,d.goods_price)/ac.order_amount * v.invoice_amount,5) as goods_price, if(g.cat_type1 is null,1,if(g.cat_type1='金条',g.jinzhong,if(g.cat_type1 in ('裸石','裸钻','彩钻'),g.zuanshidaxiao,1))) as goods_count,if(g.cat_type1 is null,'件',if(g.cat_type1='金条','克',if(g.cat_type1 in ('裸石','裸钻','彩钻'),'克拉','件'))) as unit,                       g.goods_sn as spec  from app_order.app_order_details d left join warehouse_shipping.warehouse_goods g on d.goods_id=g.goods_id LEFT JOIN app_order.app_order_invoice v ON d.order_id = v.order_id
            //LEFT JOIN app_order.app_order_account ac ON ac.order_id=d.order_id where if(d.favorable_status=3,d.goods_price-d.favorable_price,d.goods_price)>0 and d.order_id='{$order['order_id']}' and d.id not in (select d2.id from app_order_details d2,app_return_goods r2 where d2.id=r2.order_goods_id and r2.return_by=1 and r2.check_status>=4 and d2.order_id='{$order['order_id']}')";

          
        	if(bccomp($order['order_amount'],$order['invoice_amount'],2)==0 && bccomp($order['dgoods_price'],$order['invoice_amount'],2)==0 ){
        	    $sql="select                        if(g.cat_type1 is not null,	case when g.cat_type1 in ('摆件','金条','套装','手镯','情侣戒','男戒','脚链','女戒','吊坠','项链','耳饰','手镯','手链') then g.cat_type1 when g.cat_type1='裸石' then '成品钻' when g.cat_type1='彩钻' then '成品钻件' else  '饰品' end,'饰品')  as goods_name,if(d.favorable_status=3,d.goods_price-d.favorable_price,d.goods_price) as goods_price, if(g.cat_type1 is null,1,if(g.cat_type1='金条',g.jinzhong,if(g.cat_type1 in ('裸石','裸钻','彩钻'),g.zuanshidaxiao,1))) as goods_count,if(g.cat_type1 is null,'件',if(g.cat_type1='金条','克',if(g.cat_type1 in ('裸石','裸钻','彩钻'),'克拉','件'))) as unit,                       g.goods_sn as spec  from app_order.app_order_details d left join warehouse_shipping.warehouse_goods g on d.goods_id=g.goods_id where if(d.favorable_status=3,d.goods_price-d.favorable_price,d.goods_price)>0 and d.order_id='{$order['order_id']}' and d.id not in (select d2.id from app_order_details d2,app_return_goods r2 where d2.id=r2.order_goods_id and r2.return_by=1 and r2.check_status>=4 and d2.order_id='{$order['order_id']}')";
        	}else{
                $sql="select group_concat( distinct if(g.cat_type1 is not null,	case when g.cat_type1 in ('摆件','金条','套装','手镯','情侣戒','男戒','脚链','女戒','吊坠','项链','耳饰','手镯','手链') then g.cat_type1 when g.cat_type1='裸石' then '成品钻' when g.cat_type1='彩钻' then '成品钻件' else  '饰品' end,'饰品')) as goods_name,                                                      v.invoice_amount as goods_price,                                                                                  count(d.goods_count) as goods_count,                                                                                       '件' as unit,group_concat(distinct g.goods_sn) as spec  from app_order.app_order_details d left join warehouse_shipping.warehouse_goods g on d.goods_id=g.goods_id,app_order.app_order_invoice v where d.order_id=v.order_id and if(d.favorable_status=3,d.goods_price-d.favorable_price,d.goods_price)>0 and d.order_id='{$order['order_id']}' and d.id not in (select d2.id from app_order_details d2,app_return_goods r2 where d2.id=r2.order_goods_id and r2.return_by=1 and r2.check_status>=4 and d2.order_id='{$order['order_id']}') group by d.order_id";
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

	/**
	 * 更新订单发票表回写发票编号
	 * @param unknown $data
	 * @return unknown
	 */
	public function update_app_order_invoice($data){
	    //$sql="insert into app_order_action(`order_id`,`order_status`,`shipping_status`,`pay_status`,`create_user`,`create_time`,`remark`) values ('{$row['id']}','{$row['order_status']}','{$row['send_good_status']}','{$row['order_pay_status']}','{$create_user}','".date('Y-m-d H:i:s')."','{$remark}')";
        $res = false;
        if(!empty($data['invoice_num']) && !empty($data['order_id'])){ 
            $sql="update app_order.app_order_invoice set invoice_num='{$data['invoice_num']}',invoice_status='2',invoice_type='{$data['invoice_type']}' where order_id='{$data['order_id']}'";            
            $res = $this->db()->query($sql);
        }	   
	    return $res;
	}	

    /*****
     *获取发货的货号信息
    */
    public function getGoods($goods_id){
    	$res = $this->db()->getRow("select * from warehouse_shipping.warehouse_goods where goods_id='{$goods_id}'");
    	return $res;
    }

    public function getAppOrderDetails($order_sn){
    	$res =$this->db()->getAll("select d.* from app_order_details d,base_order_info o where  d.order_id=o.id and o.order_sn='{$order_sn}'");
    	return $res;
    }
  

    /******
     ** 唯品会发货审核相关调拨单
    */
    public function checkBillM($order_sn,$goods_list){
        if(empty($order_sn) || empty($goods_list) || !is_array($goods_list)){
        	return false;
        }   
        //$goods_list = explode("_",$goods_id_list);
        $billM = array();
        foreach ($goods_list as $key => $goods_id) {
        	$sql = "select b.bill_no,bg.goods_id from warehouse_shipping.warehouse_bill b,warehouse_shipping.warehouse_bill_goods bg where b.id=bg.bill_id and bg.goods_id='{$goods_id}' and b.bill_type='M' and b.bill_status='1' and b.order_sn='{$order_sn}' order by bg.id desc limit 1";
        	$res = $this->db()->getRow($sql);
        	if($res){
        		$billM[] = $res['bill_no'];
        	}
        }
        if(count($billM)==1){
        	$sql = "select bg.goods_id from warehouse_shipping.warehouse_bill_goods bg where bg.bill_no='{$billM[0]}'";
        	//echo $sql;
            $res = $this->db()->getAll($sql);
            if(count($goods_list)==count($res)){
            	$this->db()->query("
                    update warehouse_shipping.warehouse_bill b,warehouse_shipping.warehouse_bill_goods bg,
                    warehouse_shipping.warehouse_goods g
            		    set b.bill_status=2,g.is_on_sale=2,g.warehouse_id=b.to_warehouse_id,g.warehouse=b.to_warehouse_name   where b.id=bg.bill_id and bg.goods_id=g.goods_id and b.bill_type='M' and b.bill_status='1' and g.is_on_sale=5 and b.bill_no='{$billM[0]}' and b.order_sn ='{$order_sn}' ");

            }
        }
        
    }

}

?>