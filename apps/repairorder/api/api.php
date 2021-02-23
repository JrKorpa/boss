<?php
/**
 * This contains the Retrieval API .
 *
 */
class api
{
    private  $db = null;
    private  $error_msg = '';
    private  $return_msg = '';
    private  $return_sql = '';
    private  $filter = array();
    public function __construct($_filter)
    {
        global $config;
        $this->db= new KELA_API_DB($config);
		$this->filter = $_filter;
    }

    /**
     * 查询订单列表分页信息
     * @param order_sn order_pay_status
     * @return json
     */
	public function GetOrderList()
	{
		$s_time = microtime();
		//$this -> filter["page"] = 3;  //当前页
		$page = intval($this -> filter["page"]) <= 0 ? 1 : intval($this -> filter["page"]);
		$page_size = intval($this -> filter["page_size"]) > 0 ? intval($this -> filter["page_size"]) : 15;

        $order_id			=intval(trim($this->filter['order_id']));//订单id
		$order_sn			=trim($this->filter['order_sn']);//订单号
		$order_pay_status	=intval(trim($this->filter['order_pay_status']));//支付状态

		$where = " where  1 ";
		if(!empty($order_id))
		{
			$where .= " and `id` = " . $order_id;
        }
		if(!empty($order_sn))
		{
			$where .= " and `order_sn`='".$order_sn."'";
		}
		if(!empty($order_pay_status))
		{
			$where .= " and `order_pay_status`=".$order_pay_status;
		}
		$sql   = "SELECT COUNT(*) FROM `base_order_info` ".$where;
		$record_count   =  $this -> db ->getOne($sql);
		$page_count     = $record_count > 0 ? ceil($record_count / $page_size) : 1;

		$sql = "select * from `base_order_info` ".$where." ORDER BY id desc LIMIT " . ($page - 1) * $page_size . ",$page_size";
		$res = $this -> db -> getAll($sql);
		$content = array("page" => $page, "page_size" => $page_size, "record_count" => $record_count, "data" => $res, "sql" => $sql);
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
		//var_dump($res);
		if(!$res)
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此订单";
			$this -> return_msg = array();
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $content;
			$this->display();
		}
	}
	/**
    * 查询订单信息
    * @param $order_id
    * @return json
    */
	public function GetOrderInfo()
	{
		$s_time = microtime();
		$where = "";
        $order_id=intval(trim($this->filter['order_id']));
		//$order_id = 60;
		if(!empty($order_id))
		{
			$where .= " AND `id` = " . $order_id;
        }
		if(!empty($this -> filter["order_sn"]))
		{
			$where .=" AND order_sn ='".$this -> filter["order_sn"]."'";
		}
        if(!empty($where)){
            //查询商品详情
            $sql = "select * from `base_order_info` " .
                   "where 1 ".$where." ;";
            $row = $this->db->getRow($sql);
        }else{
            $row=false;
        }

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此订单";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $row;
			$this->display();
		}
	}
	/*------------------------------------------------------ */
	//-- 返回内容
	//-- by col
	/*------------------------------------------------------ */
	public function display()
	{
		$res = array("error" => intval($this -> error), "error_msg" => $this -> error_msg, "return_msg" => $this -> return_msg, "return_sql" => $this -> return_sql);
		die (json_encode($res));
	}

	/*------------------------------------------------------ */
	//-- 记录日志信息
	//-- by haibo
	/*------------------------------------------------------ */
	public function recordLog($api, $response_time, $str)
	{
        define('ROOT_LOG_PATH',str_replace('api/api.php', '', str_replace('\\', '/', __FILE__)));
		if (!file_exists(ROOT_LOG_PATH . 'logs/api_logs'))
		{
			mkdir(ROOT_LOG_PATH . 'logs/api_logs', 0777);
			chmod(ROOT_LOG_PATH . 'logs/api_logs', 0777);
		}
		$content = $api."||".$response_time."||".$str."||".date('Y-m-d H:i:s')."\n";
		$file_path =  ROOT_LOG_PATH . 'logs/api_logs/'.date('Y')."_".date('m')."_".date('d')."_api_log.txt";
		file_put_contents($file_path, $content, FILE_APPEND );
	}
}
?>
