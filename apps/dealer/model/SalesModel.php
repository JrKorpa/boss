<?php
/**
 * 销售模块的数据模型（代替Sales/Api/api.php）
 *  -------------------------------------------------
 *   @file      : SaleModel.php
 *   @link      :  www.kela.cn
 *   @copyright : 2014-2024 kela Inc
 *   @author    : Laipiyang <462166282@qq.com>
 *   @date      : 2015-02-10 15:34:30
 *   @update    :
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
  
    /*
    *替换之前的sales/api/api.php中checkOrderSn方法
    *
    */

    public function CheckOrderSn($order_sn){
         $sql = "SELECT a . * , b.`order_amount` , b.`money_paid` , b.`money_unpaid` , r.out_order_sn FROM `base_order_info` AS a LEFT JOIN rel_out_order AS r ON a.`id` = `r`.`order_id` , `app_order_account` AS b WHERE a.`id` = b.`order_id` and order_sn='" .$order_sn."' ;";
        $res = $this->db->getRow($sql);
         return $res;
        //写日志??


    }


    /*
    *$param $order_id  订单ID
    *return 订单配送方式 
    */
    public function getDistributionByOrderId($order_id){
       $sql = "SELECT distribution_type,shop_name FROM app_order_address where order_id='".$order_id."'";
       return $this->db()->getRow($sql);

    }

   /*
    *$param $order_sn  订单号
    *return 顾客姓名 
    */
    public function getConsigneeByOrderSn($order_sn){
       $sql = "SELECT consignee FROM app_order.base_order_info where order_sn='".$order_sn."'";
       return $this->db()->getOne($sql);

    }
    /**
     * 根据订单编号查询订单信息(base_order_info单表)
     * @param unknown $order_sn
     */
    public function getBaseOrderInfoByOrderSn($order_sn,$select="*"){
        $sql = "SELECT {$select} FROM app_order.base_order_info where order_sn='".$order_sn."'";
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
    
    
    
    /**
     * 根据订单编号查询订单商品明细(app_order_detailsb单表)
     * @param unknown $order_sn
     */
    public function getAppOrderDetailsPeihuoByOrderId($order_id){
    	$sql = "select * from app_order.app_order_details where order_id='{$order_id}' and delivery_status=2";
    	return $this->db()->getAll($sql);
    }
    /**
     * 根据id查询订单商品明细(app_order_details单表)
     * @param unknown $order_sn
     */
    public function getAppOrderDetailsById($detail_id){
        $sql = "select * from app_order.app_order_details where id='{$detail_id}'";
        return $this->db()->getRow($sql);
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
    //更改订单商品信息（app_order_details表）
    public function updateAppOrderDetail($data,$where){
        $sql = $this->updateSql('app_order_details',$data,$where);
        return $this->db()->query($sql);
    }
    //查询订单基本信息（根据订单商品明细的主键id）
    public function getOrderInfoByDetailId($detail_id){
        $sql = "select o.* from app_order.app_order_details d left join app_order.base_order_info o on d.order_id=o.id where d.id={$detail_id}";
        return $this->db()->getRow($sql); 
    }  
    
    /**
     * 获取可自动绑定货号的订单商品信息
     * @param array $bc_ids
     */
    public function getAllowBindGoodsByBcIds($bc_ids){
       if(!empty($bc_ids)){    
           $sql = "SELECT od.id as order_detail_id,od.goods_id,od.goods_sn,od.zhengshuhao,od.bc_id,od.order_id,o.order_status,o.send_good_status,o.order_pay_status,r.return_by from app_order.app_order_details od LEFT JOIN app_order.base_order_info o on od.order_id=o.id left join app_order.app_return_goods r on r.order_goods_id=od.id where o.delivery_status<5 and o.send_good_status=1 and o.order_status=2 and (r.return_by<>1 or r.return_by is null) and od.bc_id in (".implode(',',$bc_ids).")";
           return $this->db()->getAll($sql); 
       }
       return array();
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

    public function GetPrintBillsInfo($order_sn)
    {
       
        $sql = "SELECT  `a`.`id`,`a`.`order_sn`, `a`.`user_id`, `a`.`delivery_status`,`a`.`department_id`,`a`.`customer_source_id`,`a`.`send_good_status`, `a`.`order_remark`, `a`.`order_status`, `a`.`order_pay_status`,`a`.`order_pay_type`,`a`.`consignee` order_consignee,`a`.`create_time`,`b`.`order_amount`, `b`.`money_paid`, `b`.`money_unpaid`,`b`.`goods_amount`,`b`.`favorable_price`,`b`.`coupon_price`,c.`consignee`, `c`.`distribution_type`, `c`.`express_id`, `c`.`address`,`c`.`shop_type`,`c`.`shop_name`, `d`.`invoice_title`, `d`.`invoice_amount`, `d`.`invoice_address`, `d`.`invoice_num`, `d`.`is_invoice`,`go`.gift_id,`go`.remark,`go`.gift_num,(SELECT r.`out_order_sn` FROM rel_out_order r where r.order_id=a.id limit 1) as `out_order_sn`  FROM  `app_order_account` AS `b`, `app_order_address` AS `c`,`base_order_info` AS `a` LEFT JOIN `app_order_invoice` AS d ON `a`.`id` = `d`.`order_id` LEFT JOIN `rel_gift_order` AS `go` ON `go`.`order_id`=`a`.`id`  WHERE `a`.`order_sn` = '{$order_sn}' AND `a`.`id` = `b`.`order_id` AND `a`.`id` = `c`.`order_id`";
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
        $sql = "SELECT a.*,b.*,s.shoucun,s.company_id,s.company,s.warehouse,s.warehouse_id FROM `base_order_info` AS `a` LEFT JOIN `app_order_details` AS `b` ON `a`.`id` = `b`.`order_id` left join`warehouse_shipping`.`warehouse_goods` s ON b.goods_id=s.goods_id WHERE a.order_sn='{$order_sn}' and b.is_return=0";
        return $this->db()->getAll($sql); 
    }     
    
     /**
     * 查询订单列表分页信息
     * @param order_sn 订单号
     * @param delivery_status 配送状态
     * @return json
     */
    public function GetOrderListPage($args,$page,$page_size=10,$useCache = true)
    {
        //$this -> filter["page"] = 3;  //当前页
        $page = intval($page) <= 0 ? 1 : intval($page);
        $order_id = isset($args['order_id']) ? intval(trim($args['order_id'])) : 0 ;//订单id
        $order_sn = isset($args['order_sn']) ? trim($args['order_sn']) : '';//订单号
        $delivery_status = isset($args['delivery_status']) ? intval(trim($args['delivery_status'])) : 0;//订单配送状态
        $consignee = isset($args['consignee']) ? trim($args['consignee']) : '';//客户姓名
        $order_pay_status = intval(trim($args['order_pay_status']));//订单状态
        $order_status = intval(trim($args['order_status']));//订单状态
        $mobile = isset($args['mobile']) ? trim($args['mobile']) : '';//手机号
        $start_time =trim($args['start_time']);//
        $end_time =trim($args['end_time']);//
        $department_id =trim($args['order_department']);//
        $referer = isset($args['referer']) ? trim($args['referer']) : '';//录单来源
        $ids = isset($args['ids']) ? trim($args['ids']) : '';//订单号
        
        
        $where = " where 1 AND `oi`.`id`=`oa`.`order_id`";
		if(!empty($order_id))
		{
			$where .= " and `oi`.`id` = " . $order_id;
        }
		if(!empty($order_sn))
		{
			$where .= " and `oi`.`order_sn`='".$order_sn."'";
		}
		if(!empty($consignee))
		{
			$where .= " and `oi`.`consignee` LIKE '".$consignee."%'";
		}
		if(!empty($order_pay_status))
		{
			$where .= " and `oi`.`order_pay_status`=".$order_pay_status;
		}
		if(!empty($order_status))
		{
			$where .= " and `oi`.`order_status`=".$order_status;
		}
        if(!empty($mobile))
        {
            $where .= " and `oi`.`mobile`='".$mobile."'";
        }
        if(!empty($start_time))
        {
            $where .= " and `oi`.`create_time` >= '".$start_time." 00:00:00'";
        }
        if(!empty($end_time))
        {
            $where .= " and `oi`.`create_time` <= '".$end_time." 23:59:59'";
        }
		if(!empty($department_id))
		{
			$where .= " and `oi`.`department_id` in (".$department_id.")";
		}
		if(!empty($ids))
		{
			$where .= " and `oi`.`order_sn` in($ids)";
		}
       if(!empty($referer))
        {
            $where .= " and `oi`.`referer` ='".$referer."'";
        }

       
        $sql   = "SELECT COUNT(*) FROM `base_order_info` as `oi`,`app_order_account` as `oa` ".$where;
		
        $record_count   =  $this -> db ->getOne($sql);
		$page_count     = $record_count > 0 ? ceil($record_count / $page_size) : 1;

	    $sql = "select * from `base_order_info` as oi,`app_order_account` as oa ".$where." ORDER BY `oa`.`id` desc LIMIT " . ($page - 1) * $page_size . ",$page_size";
		$res = $this -> db -> getAll($sql);
	    $content = array("page" => $page, "pageSize" => $page_size, "recordCount" => $record_count, "data" => $res, "sql" => $sql,'pageCount'=>$page_count);
        return $content;
       
       

    }  
    
    
    public function GetOrderList($args)
    {
    	//$this -> filter["page"] = 3;  //当前页
    	$page = intval($page) <= 0 ? 1 : intval($page);
    	$order_id = isset($args['order_id']) ? intval(trim($args['order_id'])) : 0 ;//订单id
    	$order_sn = isset($args['order_sn']) ? trim($args['order_sn']) : '';//订单号
    	$delivery_status = isset($args['delivery_status']) ? intval(trim($args['delivery_status'])) : 0;//订单配送状态
    	$consignee = isset($args['consignee']) ? trim($args['consignee']) : '';//客户姓名
    	$order_pay_status = intval(trim($args['order_pay_status']));//订单状态
    	$order_status = intval(trim($args['order_status']));//订单状态
    	$mobile = isset($args['mobile']) ? trim($args['mobile']) : '';//手机号
    	$start_time =trim($args['start_time']);//
    	$end_time =trim($args['end_time']);//
    	$department_id =trim($args['order_department']);//
    	$referer = isset($args['referer']) ? trim($args['referer']) : '';//录单来源
    	$ids = isset($args['ids']) ? trim($args['ids']) : '';//订单号
    
    
    	$where = " where 1 AND `oi`.`id`=`oa`.`order_id`";
    	if(!empty($order_id))
    	{
    		$where .= " and `oi`.`id` = " . $order_id;
    	}
    	if(!empty($order_sn))
    	{
    		$where .= " and `oi`.`order_sn`='".$order_sn."'";
    	}
    	if(!empty($consignee))
    	{
    		$where .= " and `oi`.`consignee` LIKE '".$consignee."%'";
    	}
    	if(!empty($order_pay_status))
    	{
    		$where .= " and `oi`.`order_pay_status`=".$order_pay_status;
    	}
    	if(!empty($order_status))
    	{
    		$where .= " and `oi`.`order_status`=".$order_status;
    	}
    	if(!empty($mobile))
    	{
    		$where .= " and `oi`.`mobile`='".$mobile."'";
    	}
    	if(!empty($start_time))
    	{
    		$where .= " and `oi`.`create_time` >= '".$start_time." 00:00:00'";
    	}
    	if(!empty($end_time))
    	{
    		$where .= " and `oi`.`create_time` <= '".$end_time." 23:59:59'";
    	}
    	if(!empty($department_id))
    	{
    		$where .= " and `oi`.`department_id` in (".$department_id.")";
    	}
    	if(!empty($ids))
    	{
    		$where .= " and `oi`.`order_sn` in($ids)";
    	}
    	if(!empty($referer))
    	{
    		$where .= " and `oi`.`referer` ='".$referer."'";
    	}
    
    	$sql = "select oi.id,oi.order_sn,oi.consignee,oi.create_user,oi.create_time,oi.order_pay_status,oi.order_status,oa.order_amount,oa.money_paid,oa.real_return_price from `base_order_info` as oi,`app_order_account` as oa ".$where." ORDER BY `oa`.`id` desc ";
    	return $res = $this -> db -> getAll($sql);
    	
    
    }
    
    
    
    /**
     * 查询订单列表分页信息
     * @param order_sn 订单号
     * @param delivery_status 配送状态
     * @return json
     */
    public function GetOrderTsydListPage($args,$page,$page_size=10,$useCache=true)
    {
    	//$this -> filter["page"] = 3;  //当前页
    	$page = intval($page) <= 0 ? 1 : intval($page);
    	$order_id = isset($args['order_id']) ? intval(trim($args['order_id'])) : 0 ;//订单id
    	$order_sn = isset($args['order_sn']) ? trim($args['order_sn']) : '';//订单号
    	$delivery_status = isset($args['delivery_status']) ? intval(trim($args['delivery_status'])) : 0;//订单配送状态
    	$delivery_status_str = trim($args['delivery_status_str']);//多个订单配送状态
    	$order_status = intval(trim($args['order_status']));//订单状态
    
    	$customer_source_id =trim($args['customer_source_id']);//
    	$create_user =trim($args['create_user']);//
    	$is_print_tihuo =trim($args['is_print_tihuo']);//
    	$sales_channels_id =trim($args['sales_channels_id']);//
    	$create_time_start =trim($args['create_time_start']);//
    	$create_time_end =trim($args['create_time_end']);//
    	$delivery_address =$args['delivery_address'];//
    	
    	$has_company =$args['has_company'];//shousuo
    	$shops =$args['shops'];//shousuo
    	$shousuo =$args['shousuo'];//
    	//根据款号  批量查询配货订单
    	$style_sn=trim($args['style_sn']);//
    
    	if(!empty($style_sn)){
    		$sql="select order_id from app_order_details where goods_sn in ({$style_sn})  group by order_id";
    		$rec = $this -> db -> getAll($sql);
    		if($rec){
    			foreach($rec as $val){
    				$order_id .= $val['order_id'].',';
    			}
    			$order_id = rtrim($order_id,',');
    		}else{
    			//无效订单号
    			$order_id=0;
    		}
    
    	}
    
    	$where = '';
    	
    	if(!empty($order_id))
    	{
    		$str .= " `a`.`id` IN (".$order_id.") AND ";
    	}
    	if(!empty($order_sn))
    	{
    		$str .= " `a`.`order_sn` IN (".$order_sn.") AND ";
    	}
    	
    
    	if(!empty($create_user))
    	{
    		$str .= " `a`.`create_user`='".$create_user."' AND ";
    	}
    	if($is_print_tihuo!='')
    	{
    		$str .= " `a`.`is_print_tihuo`=".$is_print_tihuo." AND ";
    	}
    	if(!empty($sales_channels_id))
    	{
    		$str .= " `a`.`department_id`IN (".$sales_channels_id.") AND ";
    	}
    
    	if(!empty($customer_source_id))
    	{
    		$str .= " `a`.`customer_source_id`=".$customer_source_id." AND ";
    	}
    	if(!empty($create_time_start))
    	{
    		$str .= " `a`.`create_time`>'".$create_time_start." 00:00:00' AND ";
    	}
    	if(!empty($create_time_end))
    	{
    		$str .= " `a`.`create_time`<'".$create_time_end." 23:59:59' AND ";
    	}
    	if(isset($args["apply_close"]))
    	{
    		$str .= " `a`.`apply_close` ='".$args["apply_close"]."' AND ";
    	}
    
    	
    
    	
    
    	//默认不显示
    	if(!empty($args["no_view"]))
    	{
    		$str .= " a.department_id <>108 AND ";
    	}
    	//待配货订单里 发货状态是已到店的是否可以配货??
    	/*        if(isset($args["send_good_status"]))
    	{
    	$str .= " `a`.`send_good_status` ='".$args["send_good_status"]."' AND ";
    	}*/
    
    	$has_company1 =implode("','", $has_company);
    	// $str =str_replace($str1, '', $str);
    	// $results = array_intersect(array(58,445), $has_company);
    	if(!empty($args["delivery_address"]) ){
    	  $str .= " `ad`.`shop_name` ='".$args["delivery_address"]."' AND ";
            // $str =str_replace($str1, '', $str);
                
    	}
    	$where .=" WHERE ".$str;
        // if($str != ''){
            // $str = rtrim($str,"AND ");//这个空格很重要
                						// $where .=" WHERE ".$str;
                						// }
    
                						//有总公司和分公司的
                						//有总公司
                						$sql ="SELECT COUNT(*) FROM `base_order_info` AS `a`  INNER JOIN `app_order_account` AS `b` ON `a`.`id`=`b`.`order_id` left join app_order_address as ad on a.id=ad.order_id left join cuteframe.sales_channels sc on  ad.shop_name=sc.channel_own LEFT JOIN app_order_details AS aod ON aod.order_id=a.id $where aod.delivery_status=2 AND a.referer ='天生一对加盟商'";
   
                						$record_count   =  $this -> db ->getOne($sql);
                						$page_count     = $record_count > 0 ? ceil($record_count / $page_size) : 1;
    
    
                						$sql ="SELECT `a`.*, `b`.`order_amount`,`ad`.distribution_type,`ad`.shop_name FROM `base_order_info` AS `a`  INNER JOIN `app_order_account` AS `b` ON `a`.`id`=`b`.`order_id` left join app_order_address as ad on a.id=ad.order_id left join cuteframe.sales_channels sc on  ad.shop_name=sc.channel_own LEFT JOIN app_order_details AS aod ON aod.order_id=a.id $where aod.delivery_status=2 AND referer ='天生一对加盟商' group by a.id ORDER BY `a`.`id` DESC LIMIT " . ($page - 1) * $page_size . ",$page_size";
                						$res = $this -> db -> getAll($sql);
    
    
                						foreach($res as $key => $val){
                						$order_id = $val['id'];
                						$action_sql="select create_time from app_order_action where order_id = {$order_id} order by action_id desc;";
                						$last_time = $this->db->getOne($action_sql);
                						if(empty($last_time)){
                						$last_time = '0000-00-00 00:00:00';
                						}
                								$res[$key]['last_time'] = $last_time;
                						}
    
                						 
                						$content = array("page" => $page, "pageSize" => $page_size, "recordCount" => $record_count, "data" => $res, "sql" => $sql,'pageCount'=>$page_count);
            return $content;
    
        }
    
    
    
    /**
     * 订单赠品详情
     */
    public function getOrderGiftInfo($order_id) {
    	 $sql="SELECT `gift_id`,`remark`,`gift_num` FROM `rel_gift_order` AS `rg` LEFT JOIN `base_order_info` AS `b` ON `rg`.`order_id`=`b`.`id`  WHERE `rg`.`order_id`=$order_id AND `b`.`create_time` < '2015-10-23 00:00:00' ";
    	return $this->db()->getRow($sql);
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
     * 
     * @param $order_sn 订单号
     * @param $where
     * @return
     * LRJ
     */
    public function getSalesChannelByOrderSn($order_sn){
        $sql = "SELECT s.channel_name from app_order.base_order_info b LEFT JOIN cuteframe.sales_channels s on b.department_id=s.id where b.order_sn='".$order_sn."'";
        return $this->db()->getOne($sql);
    }



     
    /*
    * 获取所有的录单类型
    *
    */
    public function getAllReferers(){
        $sql = "SELECT DISTINCT referer from app_order.base_order_info where referer <>'EGL'";   
        return $this->db()->getAll($sql);
    }


    /*
    * 获取订单的维修状态
    *
    */
    public function getWeixiustatusById($order_id){
        $sql = "SELECT weixiu_status from app_order.app_order_details where order_id=".$order_id;
        return $this->db()->getAll($sql);
    }
    
    
    //根据货号查询订单号
    public function getOrderIdByGoodsId($goods_id){
    	$sql="SELECT b.order_sn FROM app_order_details AS aod,base_order_info AS b WHERE aod.order_id=b.id AND aod.goods_id='{$goods_id}'";
    	 $orderArr=$this->db()->getROW($sql);
    	 if(empty($orderArr)){
    	 	return '';
    	 }else{
    	 	return $orderArr['order_sn'];
    	 }
    }

    
    /**
     * 根据订单号获取订单信息
     * is_return 必须是数字类型的，不可以传字符串类型的。
     * @return json
     */
    public function GetOrderInfoArrByOrdersn($oder_sn) {
    	
    	$sql = "SELECT a.*,b.*,s.shoucun,s.company_id,s.company,s.warehouse,s.warehouse_id FROM `base_order_info` AS `a` LEFT JOIN `app_order_details` AS `b` ON `a`.`id` = `b`.`order_id` left join`warehouse_shipping`.`warehouse_goods` s ON b.goods_id=s.goods_id WHERE a.order_sn='{$oder_sn}' "; //暂时用＊号
    	
    	$rows= $this->db()->getAll($sql);
    	return $rows;
    	
    	
    }
    
    function getWholesaleId($order_id){
    	
    	$wholesale_id=0;
    	 $sql="select wholesale_id from app_order_address where order_id={$order_id}";
    	 $wholesale_id= $this->db()->getOne($sql);
    	
    	if($wholesale_id == ''){
	    	$sql="select department_id from base_order_info where id=$order_id";
	    	$OrderArr=$this->db()->getRow($sql);
	    	$SalesChannelsModel = new SalesChannelsModel(1);
	    	$ChannelsArr=$SalesChannelsModel->getChannelIdById($OrderArr['department_id']);
	    	if(!empty($ChannelsArr) && $ChannelsArr['wholesale_id'] != ''){
	    		$wholesale_id=$ChannelsArr['wholesale_id'];
	    		
	    	}
    	}
    	return $wholesale_id;
    }
    function getWholesaleName($order_id){
    	$sql="select wholesale_name from app_order_address where order_id={$order_id}";
    	$wholesale_name= $this->db()->getOne($sql);
    	 
    	if(empty($wholesale_name)){
    		$wholesale_name='';
    		$sql="select department_id from base_order_info where id=$order_id";
    		$OrderArr=$this->db()->getRow($sql);
    		$SalesChannelsModel = new SalesChannelsModel(1);
    		$ChannelsArr=$SalesChannelsModel->getChannelIdById($OrderArr['department_id']);
    		if(!empty($ChannelsArr)){
    			$wholesale_id=$ChannelsArr['wholesale_id'];
    			if($wholesale_id !='' && $wholesale_id != 0){
    				$WarehouseGoodsModel =new WarehouseGoodsModel(21);
    				$wholesale_name=$WarehouseGoodsModel->getWholesaleArr($wholesale_id);
    			}
    		}
    
    	}
    	return $wholesale_name;
    }
    
    
    public function getAppOrderInfoById($detail_id){
    	$sql = "select a.*,b.order_pay_status from app_order.app_order_details as a left join base_order_info as b on b.id=a.order_id where a.id='{$detail_id}'";
    	return $this->db()->getRow($sql);
    }
     
    
    //根据订单明细id查询商品实际价格
    function getOrderDetailPrice($id_str_in){
    	$sql="select sum(goods_price) from app_order_details where id in ($id_str_in)";
    	return $this->db()->getOne($sql);
    }
    
    
    
    //查询订单列表
    public function getOrderAccountInfoByOrderSn($order_sn) {
    	$select = "`oi`.*, `oa`.`order_amount`, `oa`.`money_paid`, `oa`.`money_unpaid`, `oa`.`goods_return_price`, `oa`.`real_return_price`, `oa`.`shipping_fee`, `oa`.`goods_amount`,`oi`.`department_id`,`oi`.`apply_close`,`oi`.`order_status`,`oi`.`order_pay_status` ";
    	$sql = "SELECT {$select} FROM `base_order_info` as `oi` LEFT JOIN `app_order_account` as `oa` ON `oi`.`id`=`oa`.`order_id` WHERE `oi`.`order_sn`='{$order_sn}'";
    	return $this->db()->getRow($sql);
    }
    
    /**
     * 根据订单号查询订单明细
     */
    public function getOrderDetailsIdBySn($order_sn,$goods_id)
    {
    	if(!empty($order_sn)){    		
    		$sql = "SELECT og.id FROM app_order_details as og left join `base_order_info` as `oi`  ON `oi`.`id`=`og`.`order_id` WHERE `oi`.`order_sn`='{$order_sn}' and og.goods_id={$goods_id}";
    		$res = $this->db()->getOne($sql);
    		return $res;
    	}else{
    		return false;
    	}
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
}

?>