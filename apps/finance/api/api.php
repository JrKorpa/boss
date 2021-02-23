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
     * 应付插入数据接口 JUAN
     * @param array $insert_data 推送过来的数据
     */
    public function AddAppPayDetail() {
        $s_time = microtime();
        $_id = 0;
        if (isset($this->filter['insert_data'])) {
            $data = $this->filter['insert_data'];

        if(count($data) > 0){
		//事务添加数据，如果处理错误则返回false
		define("root_path",dirname(dirname(dirname(dirname(__FILE__)))));
		require_once(root_path.'/frame/init.php');
		$pdo = DB::cn(29)->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

			foreach ($data as $val){
                if($val['type'] == 1){ //如果是货品需要检测重复
                    $sql = "SELECT count(1) FROM goods WHERE item_id = '".$val['item_id']."'";
                    if($this->db->getOne($sql))
                    {
                       continue;
                    }
                }
                $sql = "INSERT INTO goods (item_id, order_id, zhengshuhao, goods_status, item_type, company, prc_id, prc_name, prc_num, type, pay_content, storage_mode, make_time, check_time, total, pay_apply_status, pay_apply_number, add_time) VALUES ('".$val['item_id']."', '".$val['order_id']."', '".$val['zhengshuhao']."', '".$val['goods_status']."', '".$val['item_type']."', '".$val['company']."', '".$val['prc_id']."', '".$val['prc_name']."', '".$val['prc_num']."', '".$val['type']."', '".$val['pay_content']."', '".$val['storage_mode']."', '".$val['make_time']."', '".$val['check_time']."', '".$val['total']."', '1', '', '".date("Y-m-d H:i:s")."')";
                $pdo->query($sql);
			}
		}
		catch(Exception $e){//捕获异常
			//print_r($e);exit;
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			$this->error = 1;
			$this->return_sql = '';
			$this->error_msg = "数据异常，推送失败。";
			$this->return_msg = 0;
			$this->display();
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交

            }else{
                $this->error = 1;
		$this->return_sql = '';
		$this->error_msg = "insert_data是个空数组";
		$this->return_msg = 0;
		$this->display();
            }
        }else{
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "缺少参数insert_data";
            $this->return_msg = 0;
            $this->display();
        }
        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		$this -> error = 0;
		$this -> return_sql = '';
		$this -> return_msg = '推送成功';
		$this->display();
    }


    /**
     * 应收插入数据接口
     * @param array $insert_data 推送过来的数据
     */
    public function AddPayOrderInfo() {
        $s_time = microtime();
        $_id = 0;
        if (isset($this->filter['insert_data'])) {
            $data = $this->filter['insert_data'];
            if(count($data) > 0){
                foreach ($data as $val){
                    $val['hexiao_number'] = '';
                    $val['hexiaotime'] = '0000-00-00 00:00:00';
                    $val['returntime'] = '0000-00-00 00:00:00';
                    $this -> db -> autoExecute('pay_jxc_order',$val,'INSERT','', "SILENT");
                }
                $_id = $this->db->insert_id();
            }else{
                $this->error = 1;
				$this->return_sql = '';
				$this->error_msg = "insert_data是个空数组";
				$this->return_msg = 0;
				$this->display();
            }
        }else{
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "缺少参数insert_data";
            $this->return_msg = 0;
            $this->display();
        }
        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
		if(!$_id)
		{
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "失败";
			$this -> return_msg = array();
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = '';
			$this -> return_msg = $_id;
			$this->display();
		}
    }

    public function GetInvoiceInfo(){
        $s_time = microtime();
        if (isset($this->filter['order_sn'])) {
            $sql = "SELECT `id`,`invoice_num`,`price`,`status`,`title`,`type`,`create_time` FROM `base_invoice_info` WHERE `order_sn`='".$this->filter['order_sn']."'";
            $res = $this->db->getAll($sql);
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
            $this -> error_msg = "没有查到相应的发票信息";
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $res;
            $this->display();
        }
    }

    public function updateInvoiceInfoStatusByIds(){
        $s_time = microtime();
        if (true) {
			$ids=implode(',',$this->filter['ids']);
			$status = intval($this->filter['status']);
            $sql = "update `base_invoice_info` set status=$status WHERE `id` in (".$ids.");";
            $res = $this->db->query($sql);
        }else{
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "没有ids";
            $this -> return_msg = array();
            $this->display();
        }

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        if(!$res){
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> error_msg = "";
            $this -> return_msg = false;
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = true;
            $this->display();
        }
    }

	public function createInvoiceInfo(){
        $s_time = microtime();
        if (isset($this->filter['insertdata'])) {
			$res = $this->db->autoExecute('base_invoice_info',$this->filter['insertdata']);
			if($res){
				$id = $this->db->insertId();
			}else{
				$id = 0;
			}
        }else{
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "没有参数";
            $this -> return_msg = array();
            $this->display();
        }

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        if(!$res){
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = false;
            $this -> return_msg = $id;
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = "";
            $this -> return_msg = $id;
            $this->display();
        }
	}

    public function getInvoiceInfoByInvoiceNum(){
        $s_time = microtime();
        if (isset($this->filter['invoice_num'])) {
            $sql = "SELECT `id`,`invoice_num`,`price`,`title`,`content`,`status`,`create_user`,`create_time`,`use_user`,`use_time`,`cancel_user`,`cancel_time`,`order_sn`,`type` FROM `base_invoice_info` WHERE `invoice_num`='".$this->filter['invoice_num']."' limit 1;";
            $res = $this->db->getRow($sql);
        }else{
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "没有invoice_num";
            $this -> return_msg = array();
            $this->display();
        }

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        if(!$res){
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> error_msg = "没有查到相应的发票信息";
            $this -> return_msg = $res;
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $res;
            $this->display();
        }

    }

    public function updateInvoiceInfoByInvoiceNum(){
        $s_time = microtime();
        if (isset($this->filter['invoice_num'])) {
			$res = $this->db->autoExecute('base_invoice_info',$this->filter['updatedata'],'UPDATE',"`invoice_num`='".$this->filter['invoice_num']."'");
        }else{
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "没有invoice_num";
            $this -> return_msg = array();
            $this->display();
        }

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        if(!$res){
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = false;
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = '';
            $this -> return_msg = true;
            $this->display();
        }
    }

	public function deleteInvoiceInfoByInvoiceNum(){
        $s_time = microtime();
        if (isset($this->filter['invoice_num'])) {
			$this->db->query("DELETE FROM `base_invoice_info` WHERE `invoice_num`='".$this->filter['invoice_num']."' limit 1;");
            $res = $this->db->getAll($sql);
        }else{
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "没有invoice_num";
            $this -> return_msg = false;
            $this->display();
        }

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        if(!$res){
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = false;
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = '';
            $this -> return_msg = true;
            $this->display();
        }
	}

    public function getPaySnExt(){
        $s_time = microtime();
        if (isset($this->filter['attach_sn'])) {
           $attach_sn= $this->filter['attach_sn'];
            $sql = "SELECT `attach_sn`,`order_id` FROM `app_order_pay_action` WHERE `attach_sn`='".$attach_sn."' OR `pay_sn`='".$attach_sn."'";
            $res = $this->db->getAll($sql);
        }else{
            $this -> error = 3;
            $this -> return_sql = '';
            $this -> error_msg = "没有attach_sn";
            $this -> return_msg =$this->filter['attach_sn'];
            $this->display();
        }

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        if(!empty($res)){
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> error_msg = "查到了attach_sn的信息";
            $this -> return_msg =$res;
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = 0;
            $this->display();
        }

    }
    //通过接口向app_order_pay_action推送数据
    public function cerateOrderPayAction(){
        $s_time = microtime();
        if (isset($this->filter['insertdata'])) {
            $res = $this->db->autoExecute('app_order_pay_action',$this->filter['insertdata']['Payaction'],'INSERT','', "SILENT");
            $rea = $this->db->autoExecute('app_receipt_pay',$this->filter['insertdata']['AppReceiptPay'],'INSERT','', "SILENT");
            $sql="SELECT MAX(id) FROM app_receipt_pay";
            $rea = $this->db->getOne($sql);
            $AppReceiptPayLog=$this->filter['insertdata']['AppReceiptPayLog'];
            $AppReceiptPayLog['receipt_id']=$rea;
            $ret = $this->db->autoExecute('app_receipt_pay_log',$AppReceiptPayLog,'INSERT','', "SILENT");
        }else{
            $this -> error = 1;
            $this -> return_sql ='';
            $this -> error_msg = "insertdata不能为空";
            $this -> return_msg = array();
            $this->display();
        }

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
        if(!$res){
            $this -> error = 1;
            $this -> return_sql = $res;
            $this -> error_msg = "保存失败";
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $res;
            $this -> return_msg = '保存成功';
            $this->display();
        }

    }
    
    //退款时生成一条退款记录
    public function createPayActionInfo($param) {
        $s_time = microtime();
        if (isset($this->filter['insertdata'])) {
            $res = $this->db->autoExecute('app_order_pay_action',$this->filter['insertdata'],'INSERT','', "SILENT");
        }else{
            $this -> error = 1;
            $this -> return_sql ='';
            $this -> error_msg = "insertdata不能为空";
            $this -> return_msg = array();
            $this->display();
        }

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
        if(!$res){
            $this -> error = 1;
            $this -> return_sql = $res;
            $this -> error_msg = "保存失败";
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $res;
            $this -> return_msg = '保存成功';
            $this->display();
        }
    }
    
    public function getJxslist() {
        
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
