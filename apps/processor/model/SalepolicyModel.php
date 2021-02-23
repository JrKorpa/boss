<?php
/**
 * 销售策略模块的数据模型（代替Salespolicy/Api/api.php）
 *  -------------------------------------------------
 *   @file		: SaleModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class SalepolicyModel
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

	
	/**
	 *可销售状态批量更改接口
	 * @param goods_id Array 批量操作的货号
	 * @param del_goods Array 编辑仓储单据时，被删除的明细，既要自动上架的货品 （改参数在接口里可选，不传也没事）
	 * @param  is_sale  Int 上架状态，1上架，0下架
	 * @param is_valid Int 商品是否有效 数字字典font.sale_goods_valid
	 * @author caocao
	 * @update 2015-04-10
	 */
	public function EditIsSaleStatus($goods_id,$is_sale='0',$is_valid='2')
	{
		return true;	
	    if(empty($goods_id)){
	    	return false;
	    }
		
	
		try{
			
				$_sql = "select `id`,`is_sale`,`is_valid` from `base_salepolicy_goods` where `goods_id` = '{$goods_id}'";
				$info = $this->db->getRow($_sql);
				
				//如果有更新它的数据
				if($info){
					//如果已经下架并且此货已经销售是不更改此货的状态的
					if($info['is_sale'] ==0 && $info['is_valid']==2){
						return -1;
					}
				}
				//return $info['is_sale'].'---'.$info['is_valid'];
				//批量更新订单配送状态
				$sql = "UPDATE `base_salepolicy_goods` SET `is_sale`= {$is_sale} ,`is_valid`={$is_valid} WHERE `goods_id`='{$goods_id}'";
				// file_put_contents("D:\u22.txt",$sql."\r\n",FILE_APPEND );
				//return $sql;
				return $this->db()->query($sql);
			
	
		}catch(Exception $e){//捕获异常
			//print_r($e);exit;
			
			return false;
		}
		
	
		
	
		return true;
	}
	
	


}

?>