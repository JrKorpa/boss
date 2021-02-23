<?php
/**
 * 销售模块的数据模型（代替Sales/Api/api.php）
 *  -------------------------------------------------
 *   @file		: SaleModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class PurchaseModel
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
	
		
	/*
	*通过款号查询起版列表是否存在
	*
	*/
	public function getQiBanInfosByStyle_Sn($style_sn,$order_sn){
		$sql = "select id from purchase.purchase_qiban_goods where kuanhao='".$style_sn."' and order_sn='".$order_sn."'";
		return $this->db()->getOne($sql);
	}


	/*
	*通过采购单号在采购列表确定有款无款
	*
	*/
	public function getStyleInfoByCgd($p_sn){
		$sql = "select is_style from purchase.purchase_info where p_sn='".$p_sn."'";
		return $this->db()->getOne($sql);
	}
	
	//获取有款采购并且去工厂生产的采购单列表add by zhangruiying
	public function GetProductInfoPurchaseList($params)
	{
		$page=isset($params['page'])?intval($params['page']):1;
		$pageSize=isset($params['pageSize'])?intval($params['pageSize']):10;
		$start=($page - 1) * $pageSize;
		$user_arr=array('华士伦','祥瑞麟','凯沙琪珠宝','缘星行','荣光行','鹤麟','金嘉福','亿恒','安润盈泰','维爱塔','米莱','大凡珠宝','梵爵珠宝','新宝珠宝','六瑞祥和','钻爱一生','凯特丽');
		if(in_array($_SESSION['userName'], $user_arr)){
			$user_where=" and pr.opra_uname='".$_SESSION['userName']."'";
		}else{
			$user_where="";
		}

		$filter ="";
		if(SYS_SCOPE=='zhanting'){
            $filter = ' and pr.hidden<>1 ';
		}
		$count_sql="select pu.id as num from `purchase_info` pu left join kela_supplier.product_info pr on pr.p_sn=pu.p_sn  WHERE <%where%>";
		$sql = "SELECT pu.`id`,pu.`p_sn`,pu.`t_id`,pu.`p_sum`,pu.`purchase_fee`,pu.`put_in_type`,pu.`make_uname`,pu.`make_time`,pu.`check_uname`,pu.`check_time`,pu.`p_status`,pu.`p_info` FROM `purchase_info` pu left join kela_supplier.product_info pr on pr.p_sn=pu.p_sn WHERE <%where%>";
		$where='pu.is_style=1 and pu.is_tofactory=1 and pu.p_status=3 '.$user_where . $filter;
	
		if(isset($params['p_sn']) and !empty($params['p_sn']))
		{
	
			$where .= " and pu.p_sn in({$params['p_sn']})";
		}
		$count_sql=str_replace('<%where%>',$where,$count_sql);
	    $count_sql.=" group by pu.id ";
		$recordCount=count($this->db->getAll($count_sql));
		//file_put_contents('./ruir.txt',$count_sql."\n",FILE_APPEND);
		$where .= " group by pu.`id`  ORDER BY pu.`id` DESC LIMIT {$start},{$pageSize}";
		$sql=str_replace('<%where%>',$where,$sql);
		//file_put_contents('./ruir.txt',$sql."\n",FILE_APPEND);
		$list=$this->db->getAll($sql);
		$data=array(
				'pageSize'=>$pageSize,
				'recordCount'=>$recordCount,//总记录
				'pageCount'=>ceil($recordCount/$pageSize),//共多少页
				'page'=>$page,
				'isFirst'=>$page>1?false:true,
				'isLast'=>$page==$recordCount?true:false,
				'start'=>$start+1,
				'data'=>$list
		);
	
		if (!$data['data'])
		{
			return false;
		}
		else
		{
			return $data;
		}
	
	}

	

}

?>