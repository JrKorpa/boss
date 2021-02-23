<?php
/**
 * This contains the Retrieval API .
 *
 */
class api {

    private $db = null;
    private $error_msg = '';
    private $return_msg = '';
    private $return_sql = '';
    private $filter = array();
    public function __construct($_filter)
    {
        global $config;
        $this->db= new KELA_API_DB($config);
		$this->filter = $_filter;
    }

    /**
     * 验证转单流水号是否存在
     */
    public function checkReturnGoods(){
        $s_time = microtime();
        if (isset($this->filter['id']) && $this->filter['id']!='') {
            $sql = "SELECT * FROM `app_return_goods` WHERE `return_id`=".$this->filter['id'];
            $res = $this->db->getRow($sql); 
        }else{
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "传递的参数不合法";
            $this -> return_msg = array();
            $this->display();
        }

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        if(!$res){
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "没有查到相应的信息";
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $res;
            $this->display();
        }
    }
    
    
    public function getReturnGoodsInfo(){
        $s_time = microtime();
        if (isset($this->filter['order_sn']) && $this->filter['order_sn']!='') {
            $sql = "SELECT `order_goods_id`,`return_type`,`pay_order_sn` FROM `app_return_goods` WHERE `order_sn`='".$this->filter['order_sn']."'";
            $res = $this->db->getRow($sql);
        }else{
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "没有order_sn";
            $this -> return_msg = array();
            $this->display();
        }

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        if(!$res){
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "没有查到相应的信息";
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $res;
            $this->display();
        }
    }
	
	
	public function deleteReturnGoodsInfo(){
        $s_time = microtime();
        if (isset($this->filter['order_sn']) && $this->filter['order_sn']!='') {
            $sql = "delete `rg`,`rc` FROM `app_return_goods` as `rg`,`app_return_check` as `rc` WHERE `rg`.`return_id`=`rc`.`return_id` and `rg`.`order_sn`='".$this->filter['order_sn']."'";
            $res = $this->db->query($sql);
        }else{
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "没有order_sn";
            $this -> return_msg = array();
            $this->display();
        }

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        if(!$res){
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "没有查到相应的信息";
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $res;
            $this->display();
        }
    }

    /* ------------------------------------------------------ */

    //-- 返回内容
    //-- by col
    /* ------------------------------------------------------ */
    public function display() {
        $res = array("error" => intval($this->error), "error_msg" => $this->error_msg, "return_msg" => $this->return_msg, "return_sql" => $this->return_sql);
        die(json_encode($res));
    }

    /* ------------------------------------------------------ */

    //-- 记录日志信息
    //-- by haibo
    /* ------------------------------------------------------ */
    public function recordLog($api, $response_time, $str) {
        define('ROOT_LOG_PATH', str_replace('api/api.php', '', str_replace('\\', '/', __FILE__)));
        if (!file_exists(ROOT_LOG_PATH . 'logs/api_logs')) {
            mkdir(ROOT_LOG_PATH . 'logs/api_logs', 0777);
            chmod(ROOT_LOG_PATH . 'logs/api_logs', 0777);
        }
        $content = $api . "||" . $response_time . "||" . $str . "||" . date('Y-m-d H:i:s') . "\n";
        $file_path = ROOT_LOG_PATH . 'logs/api_logs/' . date('Y') . "_" . date('m') . "_" . date('d') . "_api_log.txt";
        file_put_contents($file_path, $content, FILE_APPEND);
    }

}

?>
