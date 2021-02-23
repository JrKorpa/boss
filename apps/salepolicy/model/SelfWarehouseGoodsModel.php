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
   //获取批发客户
    public function getWholesaleArr($wholesale_id){
    	$sql ="select wholesale_name from warehouse_shipping.jxc_wholesale where wholesale_id='".$wholesale_id."'";
    	return $this->db()->getOne($sql);
    }
    
    
    //获取所有批发客户
    public function getWholesaleAll(){
    	$sql ="select * from warehouse_shipping.jxc_wholesale ";
    	return $this->db()->getAll($sql);
    }
    
    //根据货号获取批发销售单(P单)里的管理费
    public function getManagementFee($good_ids){
    	$return=array('error'=>'0','msg'=>'');
    	if(!is_array($good_ids) || empty($good_ids)){
    		$return['error']=0;
    		$return['msg']='参数必须是有效数组';
    		return $return;
    	}
    	$rows=array();
    	foreach ($good_ids as $good_id){
    		$sql="select management_fee from warehouse_shipping.warehouse_bill_goods as wg left join warehouse_shipping.warehouse_bill wb on wb.id=wg.bill_id  where wg.goods_id={$good_id} and wg.bill_type='P' and wb.bill_status=2 order by wg.id desc limit 1";
    		$management_fee=$this->db()->getOne($sql);
    		if(!$management_fee) $management_fee=0;
    		$rows["{$good_id}"]=$management_fee;
    	}
    	
    	return $rows;
    }
    
    

}

?>