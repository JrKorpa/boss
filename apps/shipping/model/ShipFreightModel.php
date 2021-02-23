<?php
/**
 *  -------------------------------------------------
 *   @file		: ShipFreightModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class ShipFreightModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'ship_freight';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"序号",
			"order_no"=>"订单号",
			"freight_no"=>"快递单号",
			"express_id"=>"快递公司ID",
			"consignee"=>"收件人",
			"cons_address"=>"收货地址",
			"cons_mobile"=>"收件人手机",
			"cons_tel"=>"收件人电话",
        	"channel_id"=>"渠道",
			"note"=>"操作备注",
			"create_id"=>"操作人ID",
			"create_name"=>"操作人",
			"create_time"=>"操作时间",
			"is_deleted"=>"删除标识");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url ShipFreightController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段create_time
		$sql = "SELECT * FROM (SELECT GROUP_CONCAT(DISTINCT consignee) as consignee,GROUP_CONCAT(DISTINCT print_date) as print_date, GROUP_CONCAT(DISTINCT id) as id,GROUP_CONCAT(DISTINCT order_no) as order_no,freight_no,GROUP_CONCAT(DISTINCT sender) as sender,GROUP_CONCAT(DISTINCT department) as department,SUM(order_mount) as order_mount,GROUP_CONCAT(DISTINCT remark) as remark,GROUP_CONCAT(DISTINCT is_print) as is_print,GROUP_CONCAT(DISTINCT express_id) as express_id,GROUP_CONCAT(DISTINCT cons_address) as cons_address,GROUP_CONCAT(DISTINCT channel_id) as channel_id,GROUP_CONCAT(DISTINCT out_order_id) as out_order_id,GROUP_CONCAT(DISTINCT create_time) as create_time,GROUP_CONCAT(DISTINCT create_name) as create_name,GROUP_CONCAT(DISTINCT cons_tel) as cons_tel,id as sort_id FROM `".$this->table()."`";
		$str = '`is_deleted` != 1 ';

        if(isset($where['id']) && $where['id']) {$str .=" AND `id` ='".$where['id']."'";}
		if(isset($where['freight_no']) && $where['freight_no']) {$str .=" AND `freight_no` ='".$where['freight_no']."'";}
		if(isset($where['express_id']) && $where['express_id']) {$str .=" AND `express_id` ='".$where['express_id']."'";}
		if(isset($where['create_name']) && $where['create_name']) {$str .=" AND `create_name`='".$where['create_name']."'";}
		if(isset($where['order_no']) && $where['order_no']) {$str .=" AND `order_no`='".$where['order_no']."'  ";}
		//寄件部门  发货缘由 打印状态
		if(isset($where['department']) && $where['department']) {$str .=" AND `department`='".$where['department']."'  ";}
		if(isset($where['remark']) && $where['remark']) {$str .=" AND `remark`='".$where['remark']."'  ";}
		if(isset($where['is_print']) && $where['is_print']!='') {$str .=" AND `is_print`='".$where['is_print']."'  ";}
		if(isset($where['out_order_sn']) && $where['out_order_sn']){$str .=" AND `out_order_id`='".$where['out_order_sn']."'  ";}
		if(isset($where['channel_id']) && $where['channel_id']){$str .=" AND `channel_id`='".$where['channel_id']."'  ";}
		if(isset($where['is_tsyd']) && $where['is_tsyd']){$str .=" AND `is_tsyd`='".$where['is_tsyd']."'  ";}
		if(isset($where['date_time_s']) && isset($where['date_time_e']))
		{
			$str .= " AND `create_time` >= ".$where['date_time_s'];
			$str .= " AND `create_time` <= ".$where['date_time_e'];
		} else if (isset($where['date_time_s'])) {
			$str .= " AND `create_time` >= ".$where['date_time_s'];
		} else if (isset($where['date_time_e'])) {
			$str .= " AND `create_time` <= ".$where['date_time_e'];
		} else {
			$str .= " AND `create_time` >= ".strtotime(date("Y-m-d",strtotime("-2 month")));
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE {$str} ";
		}
		$sql .= "GROUP BY freight_no ORDER BY `sort_id` DESC) AS tmp";
		//echo $sql;die();
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	public function hasOrderno($order_no){
		$sql = 'SELECT COUNT(*) FROM '.$this->table().' WHERE `order_no` = '.$order_no;
		return $this->db()->getOne($sql);
	}

	public function	getOrderDetailsId($order_sn){
		if(empty($order_sn)){
			return false;
		}
		/*
		$keys=array('order_sn');
		$vals=array($order_sn);
		$ret=ApiModel::sales_api($keys,$vals,'getOrderDetailsId');
		*/

		$where = " 1";
		if(isset($order_sn) && !empty($order_sn))
		{
			$where .=" AND `order_sn` ='".$order_sn."'";
		}

		$sql = "SELECT `id` FROM app_order.`base_order_info`  WHERE ".$where;
		$order_id = $this->db()->getOne($sql);
		if(empty($order_id))
			return null;
        //`oa`.`coupon_price`,`oa`.`shipping_fee`,`oa`.`insure_fee`,`oa`.`pay_fee`,`oa`.`pack_fee`,`oa`.`card_fee`
		//获取订单基本信息
		$sql = "SELECT oi.`id`, oi.`order_sn`, oi.`user_id`, oi.`department_id`,oi.`order_status`, oi.`order_pay_type`, oi.`order_pay_status`,oi.`send_good_status`,oi.`is_delete`,oi.`is_xianhuo`, `oi`.`order_remark`, `oa`.`order_amount`, `oa`.`money_paid`, `oa`.`money_unpaid`, `oa`.`goods_return_price`, `oa`.`real_return_price`,`oa`.`shipping_fee`,`oa`.`coupon_price`,`oa`.`insure_fee`,`oa`.`pay_fee`,`oa`.`pack_fee`,`oa`.`card_fee`,oi.customer_source_id,oi.apply_return,oi.apply_close,s.channel_name,c.source_name,oi.hidden FROM app_order.`base_order_info` as oi left join cuteframe.sales_channels s on oi.department_id=s.id left join cuteframe.customer_sources c on oi.customer_source_id=c.id,app_order.`app_order_account` as `oa`  WHERE `oi`.`id`=`oa`.`order_id` and ".$where;

		$orderInfo = $this->db()->getRow($sql);
		//获取订单地址
		$sql = "SELECT `consignee`,`shop_name`,`distribution_type`,`express_id`,`country_id`,`province_id`,`city_id`,`regional_id`,`address`,`tel`,`email`,`zipcode`,`freight_no`,`shop_type`,`consignee2`,`tel2`,`address2` FROM app_order.`app_order_address` WHERE `order_id` = '".$order_id."'";
		$order_address = $this->db()->getRow($sql);

		//获取订单明细ID
		$sql = "SELECT `id`,`goods_id`,`goods_sn`,`goods_name`,`goods_count` as num, `goods_price`,`cart`,`cut`,`clarity`,`color`,`caizhi`,`jinse`, `jinzhong`,`zhiquan`,`kezi`,`face_work`,`xiangqian`,`favorable_status`,`favorable_price`,`is_return`,`is_zp`,`is_finance` FROM app_order.`app_order_details`  WHERE `order_id` = '".$order_id."'";
		//echo $sql;
		$detail = $this->db()->getAll($sql);

		//获取发票信息
		$sql = "SELECT `is_invoice`,`invoice_title`,`invoice_num`,`invoice_amount`,`create_time`,invoice_type,invoice_status  FROM app_order.`app_order_invoice`  WHERE `order_id` = '".$order_id."'";
		$order_invoice = $this->db()->getRow($sql);

        //外部订单信息
        $sql = "SELECT `out_order_sn` FROM app_order.`rel_out_order` WHERE `order_id` = '{$order_id}' limit 1";
        $order_out = $this->db()->getOne($sql);

		$data[0] = $orderInfo;
		$data[1] = $order_address;
		$data[2] = $detail;
		$data[3] = $order_invoice;
        $data[4] = $order_out;

		return $data;
	}
	
	public function	getOrderinfo_row($order_sn){
		if(empty($order_sn)){
			return false;
		}
		$keys=array('order_sn');
		$vals=array($order_sn);
		$ret=ApiModel::sales_api($keys,$vals,'getOrder_infoByOrder_sn');
		return $ret;
	}
	
	//获取商品货号
	public function getOrderGoodsSNarr($order_sn){
		if(empty($order_sn)){
			return false;
		}
		$keys=array('order_sn');
		$vals=array($order_sn);
		$ret=ApiModel::warehouse_api($keys,$vals,'GetGoodsInfobyOrderSN');
		//var_dump($ret);exit;
		foreach ($ret as $v) {
			$sn_arr[] = trim($v['goods_id']);
		}
		return $sn_arr;
	}
        //get order action log from sales
        public function getOrderActionLogList($order_sn){
            if(empty($order_sn)){
                return false;
            }
            $data = ApiModel::sales_api(array('order_sn'), array($order_sn), "GetOrderInfoBySn");
            $order_id = isset($data['id']) ? $data['id'] : 0;

            $ret = ApiModel::sales_api(array("order_id"), array($order_id), "getOrderActionList");
            return $ret;
        }
        
        
        //替换上面的api
        public function getOrderActionLogLists($order_sn){
        	if(empty($order_sn)){
        		return false;
        	}
        	$SalesModel=new SalesModel(27);
        	$orderArr=$SalesModel->GetOrderInfoByOrderSn($order_sn,'id,hidden');
            $order_id=$orderArr['id'];
            $hidden=$orderArr['hidden'];
        	if(empty($order_id)){
        		return false;
        	}
        	$ret=$SalesModel->getOrderActionList($order_id,$hidden);
        	return $ret;
        	
        	
        }
        

        //接口推送订单操作日志
        public function AddOrderLog($order_no, $create_user , $remark){
		if(empty($order_no)){
			return false;
		}
		$keys = array('order_no' , 'create_user' , 'remark');
		$vals = array($order_no , $create_user , $remark);
		$ret = ApiModel::sales_api($keys, $vals , "AddOrderLog");
		return $ret;
        }

	/**
	 * 获取订单商品信息
	 * @param $order_sn
	 * @return array|bool|mixed
	 */
	public function getGoodsByOrderSN($order_sn){
		if(empty($order_sn)){
			return false;
		}
		$keys=array('order_sn');
		$vals=array($order_sn);
		$ret=ApiModel::sales_api($keys,$vals,'GetGoodsInfobyOrderSN');
		return $ret;
	}

	/**
	* 根据订单号，获取订单信息
	*/
	public function GetDeliveryStatus( $order_sn , $fields ){
		if(empty($order_sn)){
			return false;
		}
		$keys=array('order_sn', 'fields');
		$vals=array($order_sn, $fields);
		$ret=ApiModel::sales_api($keys,$vals,'GetDeliveryStatus');
		return $ret;
	}

	/**
	 * @param $detail_id
	 * @return array|bool|mixed
	 */
	public function getSaleGoodsInfo($detail_id){
		if(empty($detail_id)){
			return false;
		}
		$keys=array('detail_arr');
		$vals=array($detail_id);
		$ret=ApiModel::warehouse_api($keys,$vals,'GetGoodsInfobyDetailId');
		return $ret;
	}

	//修改订单发货状态
	public function setOrderGoodsStatus($order_sn,$status,$express_id){
		if(empty($order_sn) ||empty($status) || empty($express_id) ){
			return false;
		}
		$keys=array('order_sn','send_good_status','express_id');
		$vals=array($order_sn,$status,$express_id);

		$ret=ApiModel::sales_api($keys,$vals,'updateOrderSendStatus');
		return $ret;
	}

	//销售政策商品下架
	public function setGoodsSaleOff($sn_arr){
		if(empty($sn_arr) || !is_array($sn_arr)){
			return false;
		}else{
			$is_sale = 0;//下架
		}
		$keys=array('goods_id','is_sale');
		$vals=array($sn_arr,$is_sale);

		$ret=ApiModel::salepolicy_api($keys,$vals,'EditIsSaleStatus');
		return $ret;
	}

	/**
	 * 修改订单和商品发货状态
	 *
	 *
	 */
	public function setOrderGoodsSend($order_sn)
	{
		$ret=ApiModel::sales_api(array('order_sn'),array($order_sn),'setOrderGoodsSend');
		return $ret;
	}


	/**
	 * 通过订单获取商品货号(销售单)
	 * @param $order_no
	 * @return array
	 */
	public function getGoodsSNByOrderSN($order_no)
	{
		#通过订单号获取销售单保存状态的所以货号
		/* $ret=ApiModel::warehouse_api(array('order_sn'),array($order_no),'GetGoodsIdsByOrderSN'); */
	    $ret=$this->GetGoodsIdsByOrderSN($order_no);
		$goods_id_arr = array();
		foreach ($ret as $key=>$val)
		{
			if(isset($val['goods_id']))
			{
				$goods_id_arr[] = $val['goods_id'];
			}
		}
		return $goods_id_arr;
		//print_r($ret);exit;
		/*
		$data = $this->getOrderDetailsId($order_no);
		$detail = $data[2];
		$detail_id = array();
		foreach ($detail as $row) {
			$detail_id[] = $row['id'];
		}
		//通过订单明细ID获取商品
		$goodsInfo = $this->getSaleGoodsInfo($detail_id);
		$sn_srr = array();
		foreach ($goodsInfo as $row) {
			if(!empty($row))
			$sn_srr[] = $row['goods_id'];
		}
		return $sn_srr;
		*/

	}

	/**
	 * 通过订单获取商品货号
	 * @param $order_no
	 * @return array
	 */
	public function getshipfreightById($id){

		$sql = 'SELECT `freight_no`,`express_id`,`sender`,`department`,`create_time` FROM '.$this->table().' WHERE `id` in ('.$id.')';
		$sql .= " ORDER BY `id` DESC";
		return $this->db()->getAll($sql);

	}

	/**
	* 修改快递方式
	* @param $arr1 修改快递方式，order_sn 订单号/express_id 快递ID
	* @param $arr2 修改订单操作日志参数 order_id 订单ID/order_status 订单状态 / send_good_status发货状态 / order_pay_status订单支付状态 /  time 时间 / user 操作人 / remark 操作内容
	*/
	/*
	public function updateShipMethod($arr1 , $arr2){
		//修改快递方式
		$ret=ApiModel::sales_api(array_keys($arr1),array_values($arr1),'updateAddressWay');
		//推送订单操作日志
		$keys2 = array('order_id' , 'order_status' , 'shipping_status' , 'pay_status' , 'create_time' , 'create_user' , 'remark');
		$vals2 = array($arr2['order_id'] , $arr2['order_status'] , $arr2['send_good_status'] , $arr2['order_pay_status'] , $arr2['time'] , $arr2['user'] , $arr2['remark']);
		ApiModel::sales_api( $keys2 , $vals2 , 'addOrderAction');
		return $ret;
	}*/
	public function updateShipMethod($arr1 , $arr2){
		$salesModel = new SalesModel(28);
		$pdo28 = $salesModel->db()->db();
		$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
		$pdo28->beginTransaction(); //开启事务
		try{
			$ret=$salesModel->updateAddressWay($arr1);
			if($ret['error']==1){
				$pdo28->rollback(); //事务回滚
				$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$result['error'] =$ret['msg'];
				Util::jsonExit($result);
			}
			
			$ret1=$salesModel->addOrderAction($arr2);
			if($ret1['error']==1){
				$pdo28->rollback(); //事务回滚
				$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$result['error'] =$ret1['msg'];
				Util::jsonExit($result);
			}
			
			$pdo28->commit(); //事务提交
			$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			return 'success';
			
		}catch (Exception $e){
			$pdo28->rollback(); //事务回滚
			$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			$result['error'] ="系统异常！error code:".$e->getMessage();
			Util::jsonExit($result);
		}
	}
        //修改快递方式
        public function updateShipMethod2($order_id,$new_freight_no,$express_id) {
            $sql = "update `ship_freight` set `freight_no`='{$new_freight_no}',`express_id`='{$express_id}' where `order_no`='{$order_id}'";
            $flag = $this->db()->query($sql);
            //var_dump($flag);exit;
            return $flag;
        }
        
        // 通用表查询
        public function select2($field,$where,$type=1) {
        	$sql = "SELECT {$field} FROM ".$this->table()." where {$where}";
        	$sql .= " ORDER BY `id` DESC";
        	if($type==1){
        		return $this->db()->getAll($sql);
        	}elseif($type==2){
        		return $this->db()->getRow($sql);
        	}elseif($type==3){
        		return $this->db()->getOne($sql);
        	}
        } 
        
        
      //获取订单的赠品信息 
        public function getGifts($order_no){
        	if(empty($order_no)){
        		return false;
        	}
        	$sql="SELECT `g`.`remark`,`g`.`gift_id`,`g`.`gift_num` FROM `app_order`.`rel_gift_order` as g left join `app_order`.`base_order_info` as i on `g`.`order_id`=`i`.`id`  WHERE `i`.`order_sn`='$order_no'";
        	//return $sql;
        	return  $this->db()->getRow($sql);
        }
		
        /**
	 * 赠品未绑定现货不需要验证货号
	 * @param $order_no
	 * @return array
	 */
	public function getzengpinById($order_sn){

		$sql = "select a.id from `app_order`.`app_order_details` as a inner join `app_order`.`base_order_info` as b on 
        a.`order_id`=b.`id` where ((a.goods_id!='' ) or a.is_finance=2)  and  b.`order_sn`='$order_sn'  ";
  
		return $this->db()->getOne($sql);
	}
	
	
	public function existsAllZp($order_sn){
	
		$sql = "select a.id from `app_order`.`app_order_details` as a inner join `app_order`.`base_order_info` as b on
		a.`order_id`=b.`id` where (a.is_zp=0 or a.is_finance=2)  and  b.`order_sn`='$order_sn'  ";
	
		$res=$this->db()->getOne($sql);
		if($res){
			return false;
		}else{
			return true;
		}
			
	}

	public function GetGoodsIdsByOrderSN($order_sn){
            $sql = "select bg.goods_id from warehouse_shipping.`warehouse_bill` as b,warehouse_shipping.`warehouse_bill_goods` as bg  where b.id=bg.bill_id and b.order_sn = '{$order_sn}' and b.bill_type='S' and b.bill_status =1";
            $res = $this->db()->getAll($sql);
            return $res;
	}
	public function GetStyleGallery($style_sn){
        $where = '';
        if(!empty($style_sn))
        {
            $where .= " AND `style_sn` = '".$style_sn."'";
        }
        if(!empty($where)){
            $sql = "SELECT `style_sn`, `img_ori`,`thumb_img`,`middle_img`,`big_img` FROM front.`app_style_gallery` WHERE `image_place` = '1' ".$where;
            $row = $this->db()->getRow($sql);
        }else
            $row=array();		
        return $row;
	}

}

?>