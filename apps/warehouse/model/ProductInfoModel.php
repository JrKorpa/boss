<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductInfoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: rong
 *   @date		: 2015-10-28 14:58:58
 *   @update	:
 *  -------------------------------------------------
 */
class ProductInfoModel{
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
  
	 /**
     * 取一条布产详情---JUAN
     */
    public function GetProductInfoByOrderID($orderDetailID) {
        
        $sql = "SELECT `id`, `bc_sn`, `p_id`, `p_sn`, `style_sn`, `status`, `num`, `prc_id`, `prc_name`, `opra_uname`, `add_time`, `esmt_time`, `rece_time`, `info`,`from_type`,`consignee`,`is_alone` FROM `product_info` WHERE p_id='{$orderDetailID}'";
      
        $data = $this->db->getRow($sql);
        return $data;
    }
    
    /**
     * 根据订单号，客户姓名
     */
    public function getConsigneeOrder_sn($order_sn=''){
    	if($order_sn==''){
    		return false;
    	}
    	
    	 $sql = "SELECT `consignee` FROM `product_info` WHERE `p_sn` = '".$order_sn."'";
    	 $res = $this->db->getOne($sql);
    	 if(!$res){
    	 	$SalesModel=new SalesModel(27);
    	 	$res=$SalesModel->getConsigneeByOrderSn($order_sn);
    	 }
    	 return $res;
    
    	
    }

   
	
}?>