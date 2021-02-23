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
class SelfSalesChannelsModel
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
	
	/**
	 *给予渠道id返回对应的实体信息
	 * @param $channel_id
	 * return array
	 */
	public function getChannelByOwnId($channel_id){
		$sql = "SELECT `channel_own_id`,`channel_type` FROM `sales_channels` WHERE `id`=".$channel_id;
		$res = $this->db()->getRow($sql);
	
		$ret = array();
		if($res['channel_type'] == 2){
			$table = 'shop_cfg';//体验店表
			$name = '`short_name`,`shop_address`,`country_id`,`province_id`,`city_id`,`regional_id`,`id`,`shop_name`,`shop_type`,`shop_phone`';
	
			$sql1 = "SELECT ".$name." FROM `".$table."` WHERE `id`=".$res['channel_own_id'];
			$ret = $this->db()->getRow($sql1);
			if(!$ret){
				$list = array();
				$list['short_name']='';
				$list['shop_address']='';
				$list['country_id']=0;
				$list['province_id']=0;
				$list['city_id']=0;
				$list['regional_id']=0;
				$list['id']='';
				$list['shop_name']='';
				$list['shop_type']='';
				$list['shop_phone']='';
				return $list;
			}else{
				return $ret;
			}
		}
		else{
			$list = array();
			$list['short_name']='';
			$list['shop_address']='';
			$list['country_id']=0;
			$list['province_id']=0;
			$list['city_id']=0;
			$list['regional_id']=0;
			$list['id']='';
			$list['shop_name']='';
			$list['shop_type']='';
			$list['shop_phone']='';
			return $list;
		}
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

	
	public function getShopCid(){
		$sql = "select id,dp_leader_name,dp_people_name from sales_channels_person";
		return $this->db()->getAll($sql);
	
	}
	
	//获取渠道所属公司ID
   public function getCompanyId($id){
  	 $sql="select `company_id` from `sales_channels` where `id`='$id'";
  	 return $this->db->getOne($sql);
  	 
   }

	
   /*
    * 取渠道 channel_type
    */
   public function getSalesChannelsInfo($select="*",$where){
   	$sql = "SELECT $select FROM `sales_channels` WHERE 1";
   	if(!empty($where['id'])){
   		$sql.=" AND `id`=".$where['id'];
   	}
   	if(!empty($where['channel_class'])){
   		$sql.=" AND `channel_class`=".$where['channel_class'];
   	}
   	if(isset($where['is_deleted'])){
   		$sql.=" AND `is_deleted`=".$where['is_deleted'];
   	}
   	if(!empty($where['channel_type'])){
   		$sql.=" AND `channel_type`=".$where['channel_type'];
   	}
   	return $this->db()->getAll($sql);
   }
  
   
   
  public function getWholesaleByCompany($channel_own_id){
    $sql="select wholesale_id from sales_channels where channel_type=2 and channel_own_id=$channel_own_id";
   	$row= $this->db()->getRow($sql);
   	if(empty($row['wholesale_id'])){
   		return 0;
   	}else{
   		return $row['wholesale_id'];
   	}
   }
}

?>