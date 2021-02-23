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

	/*添加订单日志*/
	public function AddOrderLog($order_sn='',$create_user='',$remark='')
	{
		
		$res=false;
		if($order_sn != ''){
			$sql="select id,order_status,send_good_status,order_pay_status from base_order_info where order_sn='{$order_sn}'";
			$row=$this->db->getRow($sql);
	        if(empty($row)) return false;
			$sql="insert into app_order_action(`order_id`,`order_status`,`shipping_status`,`pay_status`,`create_user`,`create_time`,`remark`) values ('{$row['id']}','{$row['order_status']}','{$row['send_good_status']}','{$row['order_pay_status']}','{$create_user}','".date('Y-m-d H:i:s')."','{$remark}')";
		    $res = $this->db->query($sql);
		}
		return $res;

		
	}
	
	
	
	
	/**
	 * 布产修改商品属性 yxt
	 */
	public function EditOrderGoodsInfo($bc_id='',$weixiu_status=1){
		
		
			$sql="UPDATE  `app_order_details`  SET weixiu_status = {$weixiu_status} WHERE bc_id={$bc_id}";
		    return $res=$this->db->query($sql);
		
		
		//return $res= $this->db->autoExecute('app_order_details',$apply_info,'UPDATE','`id`='.$detail_id);

	}
	
	
	/**
	 * 获取订单商品信息
	 */
	public function getOrderDetailByBCId($bc_id='',$select=" * ") {
		$res=false;
		if($bc_id != ''){
			$sql = "SELECT $select FROM `app_order_details` WHERE `bc_id`={$bc_id}";
			$res = $this->db->getRow($sql);
		}
	    return $res;
		
	}
	
	
	
	public function getAppOrderDetailsById($id){
		$sql="SELECT aod.id as id,aod.weixiu_status as weixiu_status,aod.order_id,aod.goods_id,boi.order_sn,boi.consignee,boi.department_id FROM app_order_details AS aod LEFT JOIN base_order_info as boi ON aod.order_id=boi.id  WHERE aod.id={$id}";
		return $res=$this->db->getRow($sql);
	}
	
	/**
	 * 布产修改商品属性 yxt
	 */
	public function EditOrderGoodsStatusById($id='',$weixiu_status=1){
		$res=false;
		if($id != ''){
			$sql="UPDATE app_order_details SET weixiu_status=$weixiu_status WHERE id=$id";
			$res=$this->db->query($sql);
		}
		return $res;
		//return $res= $this->db->autoExecute('app_order_details',$apply_info,'UPDATE','`id`='.$detail_id);
	
	}
	
	public function EditAppOrderDetailsStatus($order_sn,$goods_id,$weixiu_status){
	
		$sql="SELECT aod.id as id FROM app_order_details AS aod LEFT JOIN base_order_info as boi ON aod.order_id=boi.id  WHERE (aod.goods_id='$goods_id' or aod.ext_goods_sn='$goods_id') AND boi.order_sn='$order_sn'";
		$arr=$this->db->getRow($sql);
		if(!empty($arr)){
			$sql="UPDATE app_order_details SET weixiu_status=$weixiu_status WHERE id=".$arr['id'];
			return $res=$this->db->query($sql);
		}else{
		   	return false;
		}
		
		
	}
	
	public function getChannelClass($department_id){
		$sql="SELECT channel_class FROM `cuteframe`.`sales_channels` WHERE `id` = {$department_id}";
	    return $channel_class=$this->db->getOne($sql);
	}
	
	public function getOrderDetailId($order_sn,$goods_id){
	
		$sql="SELECT aod.id as id FROM app_order_details AS aod LEFT JOIN base_order_info as boi ON aod.order_id=boi.id  WHERE (aod.goods_id='$goods_id' or aod.ext_goods_sn='$goods_id') AND boi.order_sn='$order_sn'";
		$arr=$this->db->getRow($sql);
		if(!empty($arr)){
			return $arr['id'];
		}else{
			return false;
		}
	
	
	}
}

?>