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
        $sql = "SELECT `out_order_sn` FROM rel_out_order where order_id='{$order_id}'";
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
    public function GetOrderListPage($args,$page,$page_size=10,$useCache=true)
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
        $referer =$args['referer'];//
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
        $str = "(`a`.`referer` <> 'EGL' or a.order_sn in(select order_sn from egl_display)) AND ";
        if(!empty($order_id))
        {
            $str .= " `a`.`id` IN (".$order_id.")AND ";
        }
        if(!empty($order_sn))
        {
            $str .= " `a`.`order_sn` IN (".$order_sn.") AND ";
        }
        if(!empty($delivery_status))
        {
            $str .= " `a`.`delivery_status`=".$delivery_status." AND ";
        }
        if(!empty($delivery_status_str))
        {
            $str .= " `a`.`delivery_status` IN (".$delivery_status_str.") AND ";
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
    
        if(isset($args["order_status"]))
        {
            $str .= " `a`.`order_status` ='".$args["order_status"]."' AND ";
        }
    
        if(isset($args["referer"]))
        {
            $str .= " `a`.`referer` ='".$referer."' AND ";
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
        if(!empty($args["delivery_address"]) && $args["delivery_address"] !='总部到客户' && $args["delivery_address"] !='总部到店面'){
            $str .= " `ad`.`shop_name` ='".$args["delivery_address"]."' AND sc.company_id in('".$has_company1."')";
            // $str =str_replace($str1, '', $str);
            $where .=" WHERE ".$str;
        }elseif($args["delivery_address"] =='总部到客户'){
             $str .= " `ad`.`distribution_type` ='2'";
             // $str =str_replace($str1, '', $str);
             $where .=" WHERE ".$str;

        }elseif($args["delivery_address"] =='总部到店面'){
             $str .= " `ad`.`distribution_type` ='1'";
             // $str =str_replace($str1, '', $str);
             $where .=" WHERE ".$str;

        }else{
            //打开待配货列表默认
            if($shousuo ==''){
                $str .= " a.department_id <>108 AND ";
                if(in_array('总部到客户', $shops)){
                     array_shift($shops);
                     $shops =implode("','", $shops);
                     $where .=" WHERE ".$str." (`ad`.`distribution_type` ='2' OR (sc.company_id in('".$has_company1."') and `ad`.`shop_name` in('". $shops."')) OR EXISTS(SELECT 1 FROM app_order_address WHERE id=ad.id AND shop_type = 2 AND distribution_type = 1))";

                }else{
                    $shops =implode("','", $shops);
                    $where .=" WHERE ".$str." `ad`.`distribution_type`=1 and sc.company_id in('".$has_company1."')  and `ad`.`shop_name` in('". $shops."')";
                }
                 // $where =str_replace($str1, '', $where);
            }else{
                 if(in_array('总部到客户', $shops)){
                    $where .=" WHERE ".$str." (`ad`.`distribution_type`=2 OR sc.company_id in('".$has_company1."') OR EXISTS(SELECT 1 FROM app_order_address WHERE id=ad.id AND shop_type = 2 AND distribution_type = 1))";

                 }else{

                    $where .=" WHERE ".$str." `ad`.`distribution_type`=1 and sc.company_id in('".$has_company1."')";
                 }

            }
            
        }
        // if($str != ''){
        // $str = rtrim($str,"AND ");//这个空格很重要
        // $where .=" WHERE ".$str;
        // }
        
            //有总公司和分公司的
                //有总公司
                 $sql ="SELECT COUNT(*) FROM `base_order_info` AS `a`  INNER JOIN `app_order_account` AS `b` ON `a`.`id`=`b`.`order_id` left join app_order_address as ad on a.id=ad.order_id left join cuteframe.sales_channels sc on  ad.shop_name=sc.channel_own $where";

                $record_count   =  $this -> db ->getOne($sql);
                $page_count     = $record_count > 0 ? ceil($record_count / $page_size) : 1;


                 $sql ="SELECT `a`.*, `b`.`order_amount`,`ad`.distribution_type,`ad`.shop_name FROM `base_order_info` AS `a`  INNER JOIN `app_order_account` AS `b` ON `a`.`id`=`b`.`order_id` left join app_order_address as ad on a.id=ad.order_id left join cuteframe.sales_channels sc on  ad.shop_name=sc.channel_own  $where  ORDER BY `a`.`id` DESC LIMIT " . ($page - 1) * $page_size . ",$page_size";
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

    
    //优惠审核通过，更新订单金额，优惠价格，未付金额
    public function updateupdateOrderDetailFieldById($id) {
    	if(!empty($id)){
    		$where = " `id` = {$id}";
    	}else{
    		return false;
    	}
  
	        $sql="update app_order_details set favorable_status=3 where id={$id}";
	        $res=$this->db()->query($sql);
	        if(!$res){
	        	return false;
	        }
	    	
    		$details_info = $this->db->getRow("select `order_id`,`favorable_price` from `app_order_details` where `id`={$id}");
    		if($details_info){    			
    			$money_unpaid = $this->db->getRow("select `money_unpaid`,`favorable_price`,`money_paid` from `app_order_account` where `order_id`={$details_info['order_id']}");
                $sql="select goods_price,favorable_price,favorable_status from app_order_details where order_id={$details_info['order_id']} and is_return=0";
    			$details_list=$this->db()->getAll($sql);
    			$order_amount=0.00;
    			$favorable_price=0.00;
    			foreach ($details_list as $v){
    				$order_amount += $v['goods_price'];
    				//如果商品优惠审核通过，减去优惠部分
    				if($v['favorable_status']==3){
    					$order_amount -= $v['favorable_price'];
    					$favorable_price += $v['favorable_price'];
    				}
    			}
    			
    			$money_unpaid['money_unpaid'] = $order_amount-$money_unpaid['money_paid'];
    			
    			$res2=$this->db()->query("update `app_order_account` set `order_amount`={$order_amount},`money_unpaid`={$money_unpaid['money_unpaid']},`favorable_price`=$favorable_price where `order_id`={$details_info['order_id']} limit 1;");
                if(!$res2){
                	return false;
                }
    		
    		}
	    	
       
       
    	return true;
    }
    
    
    /**
     * 获取订单order_id，获取自定义字段
     * @author hlc
     */
    public function GetOrderAccountRow($order_id){
    	if(empty($order_id)){
    		return false;
    	}
    	$sql = "SELECT * FROM `app_order_account` WHERE `order_id`={$order_id}";
    	$res = $this->db()->getRow($sql);
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
     * 根据goods_id获取款号,和其他自定义字段
     */
    public function getGoodsSnByGoodsId($goods_id) {
    	if(empty($goods_id)){
    		return false;
    	}
    	$sql = "SELECT goods_sn,goods_type FROM `app_order_details` WHERE `id`={$goods_id}";
    	$res = $this->db->getRow($sql);
    	if(!$res)
    	{
    		return false;
    	}
    	else
    	{
    		return true;
    	}
    }
	
	
	public function updateInvoice($order_id, $amount) {
		$sql ="update `app_order_invoice` set `invoice_amount`=".$amount." where order_id =".$order_id;
		return $this->db()->query($sql);
    }
     
}

?>