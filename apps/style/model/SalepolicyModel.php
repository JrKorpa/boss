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

    public function UpdateSalepolicyChengben($goods_sn,$chengbenjia)
    {
    	return true;
        //合并成数组
        $data =  array_combine($goods_sn,$chengbenjia);
        foreach($data as $key=>$val){
            $newtime  = date('Y-m-d');
            $sql = "update base_salepolicy_goods set chengbenjia=$val  WHERE goods_id='$key'";
            $rea = $this->db->query($sql);

            $sql1 = "SELECT asg.id,asg.policy_id as policy_ids,asg.jiajia,asg.sta_value FROM app_salepolicy_goods AS asg LEFT JOIN base_salepolicy_info AS bsi ON asg.policy_id=bsi.policy_id WHERE asg.goods_id='$key' AND bsi.bsi_status=1 AND  policy_start_time<='$newtime' AND policy_end_time>='$newtime' AND bsi.is_delete=1";
            //file_put_contents("sql.txt",$sql."\r\n",FILE_APPEND );
            $res = $this->db->getAll($sql1);

            if(!empty($res)){
                foreach($res as $k=>$v){
                    //计算销售价
                    $xiaoshoujia = $v['jiajia']*$val+$v['sta_value'];
                    $sql2 = "update app_salepolicy_goods set sale_price =$xiaoshoujia,chengben=$val WHERE id= '$v[id]'";

                    // file_put_contents("sql1.txt",$sql1."\r\n",FILE_APPEND );
                    $xiugai = $this->db->query($sql2);
                }
            }
        }
        return $rea;
    }
}

?>