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
  
   

    public function SearchGoodsZp($where){
    	if(empty($where)){
    		return false;
    	}else{
    		$sql = "SELECT ao.`id`,ao.`order_id`,ao.`goods_id`,ao.`goods_sn`,ao.`goods_name`,ao.`goods_price`,ao.`favorable_price`,ao.`goods_count`,ao.`create_time`,ao.`modify_time`,ao.`create_user`,ao.`details_status`,ao.`send_good_status`,ao.`buchan_status`,ao.`is_stock_goods`,ao.`is_return`,ao.`details_remark`,ao.`cart`,ao.`cut`,ao.`clarity`,ao.`color`,ao.`zhengshuhao`,ao.`caizhi`,ao.`jinse`,ao.`jinzhong`,ao.`zhiquan`,ao.`kezi`,ao.`face_work`,ao.`xiangqian`,ao.`goods_type`,ao.`cat_type`,ao.`product_type`,ao.`favorable_status`,ao.`kuan_sn`,bo.`id`,bo.`order_sn`,bo.`user_id`,bo.`consignee`,bo.`mobile`,bo.`order_status`,bo.`order_pay_status`,bo.`order_pay_type`,bo.`delivery_status`,bo.`send_good_status`,bo.`buchan_status`,bo.`customer_source_id`,bo.`department_id`,bo.`create_time`,bo.`create_user`,bo.`check_time`,bo.`check_user`,bo.`genzong`,bo.`modify_time`,bo.`order_remark`,bo.`referer`,bo.`is_delete`,bo.`apply_close`,bo.`is_xianhuo`,bo.`is_print_tihuo`,bo.`is_zp`,bo.`effect_date`,bo.`bespoke_id`,bo.`recommended`,sum(ao.goods_count) as xuqiu  FROM `app_order_details` as `ao` LEFT JOIN `base_order_info` as `bo` on ao.`order_id`=bo.id ";
    		$str = "";
    
    		if($where['start_time'] != "")
    		{
    			$str .= "bo.`create_time` >= '".$where['start_time']."' AND ";
    		}
    		if($where['end_time'] != "")
    		{
    			$str .= "bo.`create_time` <= '".$where['end_time']."' AND ";
    		}
    		if($where['channel_id'] != "")
    		{
    			$str .= "bo.`department_id` = ".$where['channel_id']." AND ";
    		}
    
    		if($where['goods_sn'] != "")
    
    		{
    			$str .= "ao.`goods_sn` LIKE '%".$where['goods_sn']."%' AND ";
    		}
    
    		$str .= "ao.`goods_type` = 'zp' AND bo.order_status=2 AND ";
    		if($str)
    		{
    			$str = rtrim($str,"AND ");
    			$sql .=" WHERE ".$str;
    		}
    		$sql .= " GROUP BY  ao.`goods_sn`,ao.`zhiquan`,ao.`kezi` order by ao.`goods_sn`,ao.`zhiquan`,ao.`kezi`;";
    		$data = $this->db->getAll($sql);
    	}
    	if(empty($data)){
    		return false;
    	}else{
    		return $data;
    	}
    }
    
    public function SearchOrderdownLoad($where){
    	if(empty($where)){
    		return false;
    	}else{
    		$sql = "SELECT ao.`id`,ao.`order_id`,ao.`goods_id`,ao.`goods_sn`,ao.`goods_name`,ao.`goods_price`,ao.`favorable_price`,ao.`goods_count`,ao.`create_time`,ao.`modify_time`,ao.`create_user`,ao.`details_status`,ao.`send_good_status`,ao.`buchan_status`,ao.`is_stock_goods`,ao.`is_return`,ao.`details_remark`,ao.`cart`,ao.`cut`,ao.`clarity`,ao.`color`,ao.`zhengshuhao`,ao.`caizhi`,ao.`jinse`,ao.`jinzhong`,ao.`zhiquan`,ao.`kezi`,ao.`face_work`,ao.`xiangqian`,ao.`goods_type`,ao.`cat_type`,ao.`product_type`,ao.`favorable_status`,ao.`kuan_sn`,bo.`id`,bo.`order_sn`,bo.`user_id`,bo.`consignee`,bo.`mobile`,bo.`order_status`,bo.`order_pay_status`,bo.`order_pay_type`,bo.`delivery_status`,bo.`send_good_status`,bo.`buchan_status`,bo.`customer_source_id`,bo.`department_id`,bo.`create_time`,bo.`create_user`,bo.`check_time`,bo.`check_user`,bo.`genzong`,bo.`modify_time`,bo.`order_remark`,bo.`referer`,bo.`is_delete`,bo.`apply_close`,bo.`is_xianhuo`,bo.`is_print_tihuo`,bo.`is_zp`,bo.`effect_date`,bo.`bespoke_id`,bo.`recommended`,bo.`apply_return` FROM `app_order_details` as `ao` LEFT JOIN `base_order_info` as `bo` on ao.`order_id`=bo.id ";
    		$str = "";

    		if($where['start_time'] != "")
    		{
    			$str .= "bo.`create_time` >= '".$where['start_time']."' AND ";
    		}
    		if($where['end_time'] != "")
    		{
    			$str .= "bo.`create_time` <= '".$where['end_time']."' AND ";
    		}
    		if($where['channel_id'] != "")
    		{
    			$str .= "bo.`department_id` = ".$where['channel_id']." AND ";
    		}
    
    		if($where['goods_sn'] != "")
    
    		{
    			$str .= "ao.`goods_sn` LIKE '%".$where['goods_sn']."%' AND ";
    		}
    
    		$str .= "ao.`goods_type` = 'zp' AND bo.order_status=2 AND ";
    		if($str)
    		{
    			$str = rtrim($str,"AND ");
    			$sql .=" WHERE ".$str;
    		}
    		$data = $this->db->getAll($sql);
    	}
    	if(empty($data)){
    		return  false;
    	}else{
    		return $data;
    	}
    }
     
   

     
}

?>