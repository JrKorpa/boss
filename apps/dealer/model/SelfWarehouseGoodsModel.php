<?php
/**
 * 仓库数据模块的模型（代替WareHouse/Api/api.php）
 *  -------------------------------------------------
 *   @file		: WareHouseModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class SelfWarehouseGoodsModel
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

	
	//获取货品状态
   public function getGoodsArr($goods_id){
  	 $sql="select `is_on_sale`,`goods_sn`,`cat_type1`,`zhengshuhao` from `warehouse_goods` where `goods_id`='$goods_id'";
  	 return $this->db->getRow($sql);
  	 
   }
   //获取入货公司名称ID
   public function getToCompanyId($goods_id){
   	 $sql="select b.to_company_id from `warehouse_bill` as b left join `warehouse_bill_goods` as bg on bg.bill_id = b.id where bg.goods_id='{$goods_id}' and b.bill_status =1 limit 1";
   	 return $this->db->getOne($sql);
   }
   
   
   
   //仓储管理->商品列表里的货号绑定订单号
   public function updateOrderGoodsId($goods_id,$order_goods_id){
   	$sql="UPDATE warehouse_goods SET order_goods_id = '$order_goods_id'  WHERE goods_id = '$goods_id' ";
   	return $res = $this->db->query($sql);
   }
   
   public function getWarehouseGoodsRow($fileds="*",$where){
      $sql = "select {$fileds} from warehouse_goods where {$where}";
      return  $this->db->getRow($sql);  
   }
	
  /*
    *通过货品ID判断货品是不是现货
    *
    */
    public function isExistsByGoodsId($goods_id){
    	$sql ="select id from warehouse_shipping.warehouse_goods where goods_id='".$goods_id."'";
    	return $this->db()->getOne($sql);

    }

    /**
     * 普通查询
     * @param $type one 查询单个字段， row查询一条记录 all 查询多条记录
     */
    public function select($table,$fields = ' * ' , $where = " 1 " , $type = 'one'){
    	$sql = "SELECT {$fields} FROM `".$table."` WHERE {$where}";
    	if($type == 'one'){
    		$res = $this->db()->getOne($sql);
    	}else if($type == 'row'){
    		$res = $this->db()->getRow($sql);
    	}else if($type == 'all'){
    		$res = $this->db()->getAll($sql);
    	}
    	return $res;
    }
    
    function GetBillInfo($args){
    	$where=" where 1 ";
    	if(isset($args['bill_no'])&&$args['bill_no']!=''){
    		$where.=" and bill_no='".$args['bill_no']."'";
    	}
    	if(isset($args['bill_status'])&&$args['bill_status']!=''){
    		$where.=" and bill_status=".$args['bill_status'];
    	}else{
    		$where.=" and bill_status != 3" ;
    	}
    	
    	if(isset($args['is_tsyd'])&&$args['is_tsyd']!=''){
    		$where.=" and is_tsyd=".$args['is_tsyd'];
    	}
    	if(isset($args['confirm_delivery'])&&$args['confirm_delivery']!=''){
    		$where.=" and confirm_delivery=".$args['confirm_delivery'];
    	}
    	if(isset($args['to_customer_id'])&&$args['to_customer_id']!=''){
    		$where.=" and to_customer_id=".$args['to_customer_id'];
    	}
    	if(isset($args['start_time'])&&$args['start_time']!=''){
    		$where.=" and create_time>= '".$args['start_time']." 00:00:00'";
    	}
    	if(isset($args['end_time'])&&$args['end_time']!=''){
    		$where.=" and create_time<= '".$args['end_time']." 23:59:59'";
    	}
    	$where.=" order by id desc ";
    	$page=isset($args['page'])?$args['page']:1;
    	
    	$sql=" select id,bill_status,bill_no,confirm_delivery,create_time,to_customer_id from warehouse_bill ".$where;
    	//echo $sql;
    	if (empty($page))
    	{
    		//file_put_contents("D:\u223.txt",$sql."\r\n",FILE_APPEND );
    		$res = $this->db->getAll($sql);
    	}
    	else
    	{
    		$resArr = $this->db->getAll($sql);
    		
    		//$res_relute = $this->db->getPageList($sql, array(), $page, 10);
    		//$res['count']=count($resArr);
    		$res['data']=$resArr;
    	}
    	return $res;
    }
    
    function getBillGoodsArr($id,$confirm_delivery){
    	$sql="select order_sn from warehouse_bill_goods where bill_id={$id} group by order_sn ";
    	$rows=$this->db()->getAll($sql);
    	$SalesModel=new SalesModel(27);
    	$returnArr=array();
    	$list=array();
    	$receivablesCount=0;
    	$moneyPaidCount=0;
    	$balanceCount=0;
    	foreach ($rows as $k=>$v){
    		if(!empty($v['order_sn'])){
    			$orderInfoArr=$SalesModel->getOrderAccountInfoByOrderSn($v['order_sn']);
    			$money_paid=$orderInfoArr['money_paid'];//已付金额
    			$order_id=$orderInfoArr['id'];
    			//订单已配货商品的批发价
    			
    			$pfj=$this->getGoodsPfj($order_id);
    			//非已配货商品的（【原始零售价】）
    			$retail_price=$SalesModel->getOrderDetailRetailPriceByOrderId($order_id);  
    			$receivables=$pfj+$retail_price*0.3;
    			
    			$v['receivables']=$receivables;
    			$v['money_paid']=$money_paid;
    			$balance=$receivables-$money_paid;
    			if($confirm_delivery==1 && $balance>=0){
    				continue;
    			}
    			$v['balance']=$balance;
    			$list[]=$v;
    			$receivablesCount+=$receivables;
    			$moneyPaidCount+=$money_paid;
    			$balanceCount+=$balance;
    			
    		}else{
    			continue;
    		}
    	}
    	$returnArr['list']=$list;
    	$returnArr['receivablesCount']=$receivablesCount;
    	$returnArr['moneyPaidCount']=$moneyPaidCount;
    	$returnArr['balanceCount']=$balanceCount;
    	return $returnArr;
    }
    
    
    public function getGoodsPfj($order_id){
    	if(!$order_id){
    		return 0;
    	}
    	$sql="select SUM(g.shijia) as pfj from warehouse_shipping.warehouse_bill_goods as g left join app_order.app_order_details as a on a.id=g.detail_id where a.order_id={$order_id} and a.delivery_status=5";
    	$row=$this->db()->getRow($sql);
    	if(empty($row)){
    		return 0;
    	}else{
    		return $row['pfj'];
    	}
    }

}

?>