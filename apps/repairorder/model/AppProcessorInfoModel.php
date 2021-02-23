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
class AppProcessorInfoModel
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
	 * 获取供应商ID,NAME数组 采购和仓储都在调用，莫要删除和减少条件
	 */
	public function getProList($arr=array()){
		
		$s_time = microtime();
		$sql = "SELECT  `id`,`code`,`name` FROM `app_processor_info` WHERE 1";
		
		if (!empty($arr['status'])) {
			$sql .= " and `status` = '{$arr['status']}'";
		}
		
		 $sql .= " order by id desc";
		
		return $data = $this->db->getAll($sql);
		
	}
	
	
	
	/*
	 * 添加布产日志
	 * add by linian
	 */
	public function AddOrderLog($args = array())
	{
	  
	    if(!isset($args['rec_id']) && $args['rec_id']==''){
	    	return false;
	    }
		$bc_id=$args['rec_id'];
		$create_user=$args['create_user'];
		$create_user_id=$args['create_user_id'];
		$remark=$args['remark'];
		$sql="select id,status from product_info where bc_sn='{$bc_id}'";
		$row=$this->db->getRow($sql);
		if(!empty($row)) return false;
		$sql="insert into product_opra_log(`bc_id`,`status`,`uid`,`uname`,`time`,`remark`) values ('{$row['id']}','{$row['status']}','{$create_user_id}','{$create_user}','".date('Y-m-d H:i:s')."','{$remark}')";
	
		$res = $this->db->query($sql);
		
	
		return $sql;
	}
	
	
	
	
	public function editWeixiuStatus($id,$weixiu_status){
		$sql="UPDATE product_info SET weixiu_status=$weixiu_status WHERE id=$id";
		
		return $this->db()->query($sql);
	}
	

}

?>