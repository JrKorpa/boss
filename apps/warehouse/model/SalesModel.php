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
        $sql = "SELECT a . * , b.`order_amount` , b.`money_paid` , b.`money_unpaid` ,(select GROUP_CONCAT(DISTINCT r.out_order_sn) from rel_out_order r where r.order_id=a.id group by r.order_id) as out_order_sn FROM `base_order_info` AS a , `app_order_account` AS b WHERE a.`id` = b.`order_id` and order_sn='" .$order_sn."' ;";
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
    public function getBaseOrderInfoByOrderSn($order_sn){
        $sql = "SELECT * FROM app_order.base_order_info where order_sn='".$order_sn."'";
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

    //更改备货订单关联表订单商品信息（purchase_order_info表）
    public function updatePurchaseOrderInfo($data,$where){
        $sql = $this->updateSql('purchase_order_info',$data,$where);
        return $this->db()->query($sql);
    }
    
    public function getSqlForUpdateAppOrderDetail($data,$where){
    	$sql = $this->updateSql('app_order_details',$data,$where);
    	return rtrim(rtrim($sql),';');
    }
    
    
    //查询订单基本信息（根据订单商品明细的主键id）
    public function getOrderInfoByDetailId($detail_id){
        $sql = "select o.* from app_order.app_order_details d left join app_order.base_order_info o on d.order_id=o.id where d.id={$detail_id}";
        return $this->db()->getRow($sql); 
    }  
    //查询订单基本信息（根据订单商品明细的主键id）
    public function getOrderAccountByDetailId($detail_id){
    	$sql = "select o.*,b.order_sn,d.goods_id,d.goods_price,d.favorable_price from app_order.app_order_details d left join app_order.app_order_account o on d.order_id=o.order_id left join app_order.base_order_info as b on b.id=d.order_id where d.id={$detail_id}";
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
       
        $sql = "SELECT  `a`.`id`,`a`.`order_sn`, `a`.`user_id`, `a`.`delivery_status`,`a`.`department_id`,`a`.`customer_source_id`,`a`.`send_good_status`, `a`.`order_remark`, `a`.`order_status`, `a`.`order_pay_status`,`a`.`order_pay_type`,`a`.`consignee` order_consignee,`a`.`create_time`,`b`.`order_amount`, `b`.`money_paid`, `b`.`money_unpaid`,`b`.`goods_amount`,`b`.`favorable_price`,`b`.`coupon_price`,c.`consignee`, `c`.`distribution_type`, `c`.`express_id`, `c`.`address`,`c`.`shop_type`,`c`.`shop_name`, `d`.`invoice_title`, `d`.`invoice_amount`, `d`.`invoice_address`, `d`.`invoice_num`, `d`.`is_invoice`,`d`.`invoice_email`,`d`.`taxpayer_sn`,d.invoice_type,`go`.gift_id,`go`.remark,`go`.gift_num,(SELECT r.`out_order_sn` FROM rel_out_order r where r.order_id=a.id limit 1) as `out_order_sn`  FROM  `app_order_account` AS `b`, `app_order_address` AS `c`,`base_order_info` AS `a` LEFT JOIN `app_order_invoice` AS d ON `a`.`id` = `d`.`order_id` LEFT JOIN `rel_gift_order` AS `go` ON `go`.`order_id`=`a`.`id`  WHERE `a`.`order_sn` = '{$order_sn}' AND `a`.`id` = `b`.`order_id` AND `a`.`id` = `c`.`order_id`";
        return $row = $this->db()->getRow($sql);
    }
    
    public function GetOutOrderSn($order_id) {          
        $sql = "SELECT `out_order_sn` FROM rel_out_order where order_id='{$order_id}'";
        return   $row = $this->db()->getRow($sql);
    } 


     /**
     * 根据订单号获取订单信息 打印提货单    
     */
    public function GetOrderInfoByOrdersn($order_sn) {
        $sql = "SELECT a.*,b.*,s.shoucun,s.company_id,s.company,s.warehouse,s.warehouse_id,b.id as order_detail_id FROM `base_order_info` AS `a` LEFT JOIN `app_order_details` AS `b` ON `a`.`id` = `b`.`order_id` left join`warehouse_shipping`.`warehouse_goods` s ON b.goods_id=s.goods_id WHERE a.order_sn='{$order_sn}' and b.is_return=0";
        return $this->db()->getAll($sql); 
    }     
    
     /**
     * 查询订单列表分页信息
     * @param order_sn 订单号
     * @param delivery_status 配送状态
     * @return json
     */
    public function GetOrderListPage($args,$page,$page_size=10,$useCache=true)
    {
        //$this -> filter["page"] = 3;  //当前页
        $page = intval($page) <= 0 ? 1 : intval($page);
        $order_id = isset($args['order_id']) ? intval(trim($args['order_id'])) : 0 ;//订单id
        $order_sn = isset($args['order_sn']) ? trim($args['order_sn']) : '';//订单号
        $delivery_status = isset($args['delivery_status']) ? intval(trim($args['delivery_status'])) : 0;//订单配送状态
        $delivery_status_str = isset($args['delivery_status_str'])?$args['delivery_status_str']:'';//多个订单配送状态
        $order_status = isset($args['order_status'])?$args['order_status']:'';//订单状态

        $customer_source_id =isset($args['customer_source_id'])?$args['customer_source_id']:'';//
        $create_user =isset($args['create_user'])?$args['create_user']:'';//
        $is_print_tihuo =isset($args['is_print_tihuo'])?$args['is_print_tihuo']:'';//
        $sales_channels_id =isset($args['sales_channels_id'])?$args['sales_channels_id']:'';//
        $create_time_start =isset($args['create_time_start'])?$args['create_time_start']:'';//
        $create_time_end =isset($args['create_time_end'])?$args['create_time_end']:'';//
        $delivery_address = isset($args['delivery_address'])?$args['delivery_address']:'';//
        $referer = isset($args['referer'])?$args['referer']:'';//
        $has_company = isset($args['has_company'])?$args['has_company']:'';//shousuo
        $shops = isset($args['shops'])?$args['shops']:'';//shousuo
        $shousuo = isset($args['shousuo'])?$args['shousuo']:'';//
        //根据款号  批量查询配货订单
        $style_sn= isset($args['style_sn'])?$args['style_sn']:'';//

        if(!empty($style_sn)){
            $sql="select order_id from app_order_details where goods_sn in ({$style_sn})  group by order_id";
            echo $sql;
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
    
        $where = " where 1 = 1 ";
        //$str = "(t.`referer` <> 'EGL' or a.order_sn in(select order_sn from egl_display)) AND ";
        if(!empty($order_id))
        {
        	$where .= " and t.`order_id` IN (".$order_id.") ";
        }
        if(!empty($order_sn))
        {
        	$where.= " and t.`order_sn` IN (".$order_sn.") ";
        }
        if(!empty($delivery_status))
        {
        	$where .= " and t.`delivery_status`=".$delivery_status." ";
        }
        if(!empty($delivery_status_str))
        {
        	$where.= " and t.`delivery_status` IN (".$delivery_status_str.") ";
        }
    
        if(!empty($create_user))
        {
        	$where.= " and t.`create_user`='".$create_user."' ";
        }
        if($is_print_tihuo!='')
        {
        	$where.= " and t.`is_print_tihuo`=".$is_print_tihuo." ";
        }
        if(!empty($sales_channels_id))
        {
        	$where.= " and t.`department_id`IN (".$sales_channels_id.") ";
        }

        if(!empty($customer_source_id))
        {
        	$where.= " and t.`customer_source_id`=".$customer_source_id." ";
        }
        if(!empty($create_time_start))
        {
        	$where.= " and t.`create_time`>'".$create_time_start." 00:00:00' ";
        }
        if(!empty($create_time_end))
        {
        	$where.= " and t.`create_time`<'".$create_time_end." 23:59:59' ";
        }
        if(!empty($args["referer"]))
        {
        	$where.= " and t.`referer` ='".$referer."' ";
        }

        //默认不显示
        if(!empty($args["no_view"]))
        {
        	$where.= " and t.department_id <>108 ";
        }
        
        if(!empty($args["normal_view"]))
        {
        	$where.= " and NOT EXISTS (SELECT id FROM app_order_details AS d WHERE d.weixiu_status IN (1,2,3,4,5,6,7) AND d.order_id=t.order_id ) and ";
        	$where.= " t.is_xianhuo=1 AND ";//现货
        	$where.= " t.apply_close=0 AND ";//未申请关闭或申请关闭取消
        	$where.= " t.apply_return !=2 ";
        }

        if (empty($order_sn) && empty($order_id)) {
	        $has_company1 =implode("','", $has_company);
	                
	        $is_top_user = Auth::user_is_from_base_company();
	        if (empty($delivery_address)) {
	        	if (!$is_top_user) {
	        		$where .= " and t.addr_company_id in ('".$has_company1."') ";
	        	} else {
	        		$where .= " and (`t`.`distribution_type` ='2' or t.addr_company_id in ('".$has_company1."')) ";
	        	}
	        } elseif ($delivery_address =='总部到客户'){
	        	// 总部到（当前用户所属公司的）个人	
	        	if ($is_top_user) {
	        		$where .= " and `t`.`distribution_type` ='2' ";
	        	} else {
	        		$where .= " and `t`.`distribution_type` ='2' and t.addr_company_id in ('".$has_company1."') ";
	        	}
	        } elseif ($delivery_address =='总部到店面'){
	        	// 总部到（当前用户所属公司的）店面 
	        	if ($is_top_user) {
	        		$where .= " and `t`.`distribution_type` ='1' and t.department_id in (SELECT id from cuteframe.sales_channels where channel_class = 1 and is_deleted = 0) ";
	        	} else {
	        		$where .= " and `t`.`distribution_type` ='1' and t.addr_company_id in ('".$has_company1."') ";
	        	}
	        } else {
	        	// 具体的（当前用户所属公司的）某个店面
	        	if ($is_top_user) {
	        		$where .= " and t.shop_name = '{$delivery_address}'";
	        	} else {
	        		$where .= " and t.shop_name = '{$delivery_address}' and t.addr_company_id in ('".$has_company1."') ";
	        	}
	        }
        }

        $where1 = ' 1=1 ';
        if(isset($args['hidden']) && $args['hidden'] != ''){
            $where1 .= " and a.hidden = ".$args['hidden']." ";
        }
        
        $sql ="SELECT t.*,`b`.`order_amount`, a.* FROM (select DISTINCT t.distribution_type, t.shop_name, t.order_id, t.last_time from warehouse_shipping.order_distrib_todo t $where ) as t inner join app_order.base_order_info a on a.id = t.order_id inner join app_order.app_order_account b on b.order_id = t.order_id where {$where1} ORDER BY `t`.`order_id` DESC";



        //die( $sql);
        $datalist = $this->db()->getPageListNew($sql,array(),$page,$page_size,$useCache);   
        if ($datalist['recordCount']) {
        	return $datalist;
        } else {
        	if (!empty($order_id)) {
        		//AsyncDelegate::dispatch("order", array('event' => 'refresh_order', 'order_id' => $order_id));
        	} else if (!empty($order_sn)) {
        	    $sn_list = Util::eexplode(',', $order_sn);
        	    foreach ($sn_list as $sn_item) {
        	        //AsyncDelegate::dispatch("order", array('event' => 'refresh_order', 'order_sn' => $sn_item));
        	    }
        	}
        	
        	return $datalist;
        }
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
    	$delivery_address = isset($args['delivery_address'])?$args['delivery_address']:'';//
    	
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
    	$sql="select sum(retail_price) from app_order_details where id in ($id_str_in)";
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
    
    //更改信息（表）
    public function updateTable($table,$data,$where){
    	$sql = $this->updateSql($table,$data,$where);
    	return $this->db()->query($sql);
    }
    

    //更新打印提货单信息
    public function updatePrintTihuo($order_sn_str){
        if(is_array($order_sn_str)){
            $order_sn_arr = explode(',' , trim($order_sn_str));
            foreach($order_sn_arr as $order_sn){
                $sql = "UPDATE `base_order_info` SET `is_print_tihuo` = 1 WHERE `order_sn` = '{$order_sn}'";
                return $this->db->query($sql);  
            }  
        }else{
            if($order_sn_str!=''){
                $sql = "UPDATE `base_order_info` SET `is_print_tihuo` = 1 WHERE `order_sn` = '{$order_sn_str}'";
                return $this->db->query($sql);                
            } 
           
        }        
    }

    //查询订单号是否存在
    public function check_select_order_sn($order_sn='')
    {

        if(!$order_sn){
            return false;
        }
        # code...
        $sql = "select * from base_order_info where order_sn = '{$order_sn}'";
        return $this->db()->getRow($sql);
    }

    //修改配货状态
    public function up_order_dvtype($order_sns, $delivery_status)
    {
        # code...
        $sql = "update base_order_info set delivery_status = '{$delivery_status}' where order_sn in($order_sns)";
        return $this->db->query($sql);
    }

    public function showBcBillToCaigou($bc_info)
    {
        $rest = array();
        $sql = "select p_id,from_type,bc_sn from kela_supplier.product_info where bc_sn in('".implode("','", $bc_info)."')";
        $bcinfo = $this->db()->getAll($sql);
        if(!empty($bcinfo)){
            foreach ($bcinfo as $k => $val) {
                if($val['from_type'] == 1 && !empty($val['p_id'])){
                    $sql = "select `order_sn` from `app_order`.`purchase_order_info` where (bd_goods_id is null or bd_goods_id = '') and purchase_id = '".$val['p_id']."'";
                    $bind_order = $this->db()->getAll($sql);
                    if(!empty($bind_order)){
                        foreach ($bind_order as $key => $value) {
                            $rest[$val['bc_sn']][] = $value['order_sn'];
                        }
                    }
                }
            }
        }
        return $rest;
    }

    public function getBcSnOrderId($purchase_id)
    {
        $sql = "select poi.id as poi_id,poi.`detail_id`,oi.`id`,oi.order_sn,oi.`order_status`,oi.`send_good_status`,oi.`order_pay_status`,`oi`.`delivery_status` from `app_order`.`purchase_order_info` poi inner join app_order.app_order_details od on od.id = poi.detail_id inner join app_order.base_order_info oi on oi.id = od.order_id where poi.purchase_id = '".$purchase_id."' and (poi.bd_goods_id = '' or bd_goods_id is null)";
        return $this->db()->getAll($sql);
    }

    //查询是否有期货
    public function getStockGoods($order_sn='')
    {
        $sql = "select count(od.id) from app_order.base_order_info oi inner join app_order.app_order_details od on oi.id = od.order_id where oi.order_sn = '{$order_sn}' and od.is_stock_goods <> 1";
        return $this->db()->getOne($sql);
    }

    //货号查询绑定订单
    public function getOrderSnByGoodsId($goods_id='')
    {
        $sql = "select order_sn from app_order.purchase_order_info where bd_goods_id = '{$goods_id}'";
        return $this->db()->getOne($sql);
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

            $sql = "SELECT id,goods_id,goods_count,is_stock_goods,buchan_status,is_return FROM `app_order`.`app_order_details` WHERE `order_id`='$order_id' ";
            $res = $this->db->getAll($sql);

    
            return $res;
        
    }
}

?>