<?php
/**
 * 供应商数据模块的模型（代替Processor/Api/api.php）
 *  -------------------------------------------------
 *   @file		: WareHouseModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class ProcessorModel
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
	 * 和供应商解除关系
	 * @param unknown $arr
	 */
	public function relieveProduct($arr){
	    $status = true;
	    $msg = '';
	    try {
	        if(!empty($arr) && count($arr) > 0){
	            foreach ($arr as $key=>$value){
	                $sql = "SELECT  pg.`bc_id`, pg.`goods_id`,p.`status` FROM kela_supplier.product_goods_rel as pg left join kela_supplier.product_info as p on pg.`bc_id`=p.`id` WHERE 1";
	                if (!empty($value)) {
	                    $sql .= " and pg.`goods_id` = '{$value}'";
	                }
	                $res = $this->db()->getRow($sql);
	                if($res){
	                    //判断布产状态 若 已经生产
	                    if($res['status']<=3){
	                        //更新布产表中 生产状态为停止生产
	                        $sql = "UPDATE kela_supplier.product_info SET `status` = 10  WHERE `id` =".$res['bc_id'];
	                        $this->db()->query($sql);
	                    }
	                    //更新关系表中状态为无效
	                    $sql = "UPDATE kela_supplier.product_goods_rel SET `status` = 1 WHERE `bc_id` =".$res['bc_id'];
	                    $this->db()->query($sql);
	                }
	            }
	        }
	    } catch (Exception $e) {
	        $status = false;
	        $msg = $e->getMessage();
	    }
	    return array('status'=>$status,'msg'=>$msg);
	}

}

?>