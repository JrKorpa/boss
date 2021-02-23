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

    /*
     * 获取赠品信息
     */
    public function GetGiftManList()
    {
        $s_time = microtime();

        $sell_type = $this->filter['sell_type'];
        $status = $this->filter['status'];
        $goods_number = $this->filter['goods_number'];
        $sale_way = $this->filter['sale_way'];
      

        $where = " WHERE 1 ";
        if($sell_type != ''){
            $where .= " and `sell_type` = {$sell_type}";
        }
        if($sale_way != ''){
            $where .= " and (`sale_way` = {$sale_way} or `sale_way`='12') ";
        }
       
        if($status != ''){
            $where .= " and `status` = {$status}";
        }
        if($goods_number != ''){
            $where .= " and `goods_number` = '{$goods_number}'";
        }
        $sql = "SELECT `name`,`goods_number`,`sell_sprice` FROM `gift_goods` {$where}  order by id desc;";

	    $row = $this->db->getAll($sql);
        // 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到数据";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $row;
			$this->display();
		}
    }
    /*
     * 获取赠品信息
     */
    public function GetGiftByUsefullSn()
    {
        $s_time = microtime();

        $goods_number = $this->filter['goods_number'];
		if(!$goods_number){
			$this -> error = 1;
			$this -> error_msg = "款号出错";
			$this -> return_msg = array();
			$this->display();
		}
        $where = " WHERE 1 ";
        if($goods_number != ''){
            $where .= " and `goods_number` = '{$goods_number}' and status=1";
        }
        $sql = "SELECT `name`,`goods_number`,`sell_sprice`,`status` FROM `gift_goods` {$where};";
	    $row = $this->db->getRow($sql);
        // 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到数据";
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
    
	/*
	 * 款式库增加赠品
	 */
	public function addZpFromStyle(){
		
		$fields = array_keys($this->filter);
		$values = array_values($this->filter);
		
		$sql = "insert into gift_goods (".implode(',',$fields).") values('".implode("','",$values)."')";
		$res = $this->db->query($sql);
		if($res == false){
			$res = array("error" => '1', "error_msg" => '赠品添加失败', "return_msg" => '', "return_sql" => $this -> return_sql);
		}else{
			$res = array("error" => '0', "error_msg" => '11', "return_msg" => '添加成功', "return_sql" => $this -> return_sql);
		}
		die (json_encode($res));
		
	}
	
	/*
	 * 通过款号获取赠品状态
	 */
	
	public function getZpStatusByStyle_sn(){
		$sql ="select status from gift_goods where goods_number = '".$this->filter['style_sn']."'";
// 		file_put_contents('e:/8.sql',serialize($sql));
		$data = $this->db->getRow($sql);
		$res =array("error" => '0', "return_msg" => $data);
		die(json_encode($res));
	}
	
	/*
	 * 通过款号更改赠品状态
	 */
	public function updateZpStatusByStyle_sn(){
		$lists =array();
		foreach($this->filter as $k=>$v){
			if($k !== 'style_sn'){
				$fields_value[$k] = $v;
			}
		}
// 		file_put_contents('e:/8.sql',serialize($lists));
		$data = $this->db->autoExecute('gift_goods',$fields_value,'update',"goods_number='".$this->filter["style_sn"]."'");
// 		file_put_contents('e:/8.sql',serialize($data));
		if(!empty($data)){
			$res =array("error" => '0', "return_msg" =>json_encode($data));
		}else{
			$res =array("error" => '1', "error_msg" =>'error');
		}
		die(json_encode($res));
	}
	
	/*
	 * 通过款号判断是否可以销账
	 */
	public function getXzInfo(){
		
		$this->filter['style_sn'];	
		//需要两个字段   order_status 订单状态,send_good_status  --货品状态
		$sql = "select o.order_status,o.send_good_status from base_order_info o left join app_order_details s on s.order_id = o.id where s.goods_sn='".$this->filter['style_sn']."' and o.order_status in (1,2,3) and o.send_good_status in (1)";
		$row = $this->db->getAll($sql);
		//返回信息
		if(!$row){
			$this -> error = 1;
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_msg = $row;
			$this->display();
		}
		
	}
}
?>
