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
    
    public function getJxslist() {
        $sql= 'select id, shop_name, short_name from shop_cfg where shop_type = 2 and is_delete = 0';
        $ids = $this->filter['ids'];
        if (!empty($ids)) {
            $ids_str = implode(',', $ids);
            $sql .= " and id in ({$ids_str})";
        }
        
        $res = $this->db->getAll($sql);
        
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

    //获取数据字典
    public function get_dict()
    {
        $dict_key = isset($this->filter['dict_key'])?$this->filter['dict_key']:array();
        if(empty($dict_key) || !is_array($dict_key)){
            $this -> error = 1;
            $this -> return_sql = "";
            $this -> error_msg = "参数异常！";
            $this -> return_msg = array();
            $this->display();
        }
        $dd = new DictView(new DictModel(1));
        $res = array();
        foreach ($dict_key as $k => $key) 
        {
            $arr = $dd->getEnumArray($key);
            if(!empty($arr)){
                $res[$key] = array_column($arr,'label','name');
            }
        }
        if(empty($res)){
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
    
    public function GetSalesChannelsByIds() {
        if (isset($this->filter['dept_ids'])) {
            $ids = $this->filter['dept_ids'];
            $arr = preg_split('/,/', $ids, -1, PREG_SPLIT_NO_EMPTY);
            if (!empty($arr)) {
                $ids = implode(',', $arr);
                $sql = "select id, channel_name from sales_channels where is_deleted = 0 and id in ({$ids})";
                $res = $this->db->getAll($sql);
                
                if($res){
                    $this -> error = 0;
                    $this -> return_sql = $sql;
                    $this -> return_msg = $res;
                    $this->display();
                } else {
                    $this -> error_msg = "没有查到相应的信息";
                }
            } else {
                $this -> error_msg = "参数异常";
            }
        } else {
            $this -> error_msg = "参数异常";
        }
        
        $this -> error = 1;
        $this -> return_sql = '';
        $this -> return_msg = array();
        $this->display();
    }
    
    public function GetCustomerSourcesByIds() {
        $filter = $this->filter;
        $sql = "select id, source_code,source_name from customer_sources where is_deleted = 0 and is_enabled= 1";
        if(!empty($filter['ids'])){
            $ids = $filter['ids'];
            if(is_array($ids)){
               $ids = implode(",",$ids);
            }else{
               $ids = preg_split('/,/', $ids, -1, PREG_SPLIT_NO_EMPTY);
            }
            $ids = implode(",",$ids);
            $sql .=" AND id in ({$ids})";
        
        }
        if(!empty($filter['source_codes'])){
            $source_codes = $filter['source_codes'];
            if(is_array($source_codes)){
                $source_codes = implode(",",$source_codes);
            }else{
                $source_codes = preg_split('/,/', $source_codes, -1, PREG_SPLIT_NO_EMPTY);
            }
            $source_codes = implode("','",$source_codes);
            $sql .=" AND source_code in ('{$source_codes}')";
        
        }
        if (!empty($filter['dept_ids'])) {
            $dept_ids = $filter['dept_ids'];
            if(!is_array($dept_ids)){
               $dept_ids = preg_split('/,/', $dept_ids, -1, PREG_SPLIT_NO_EMPTY);
            }
            $dept_ids = implode(",",$dept_ids);
            $sql .=" AND source_own_id in ({$dept_ids})";
        }

        if (!empty($filter['company_ids'])) {
            $company_ids = $filter['company_ids'];
            if(!is_array($company_ids)){
               $company_ids = preg_split('/,/', $dept_ids, -1, PREG_SPLIT_NO_EMPTY);
            }
            $company_ids = implode(",",$company_ids);
            $sqlt = "select id from sales_channels where company_id in ({$company_ids})";
            $rest = $this->db->getAll($sqlt);
            $defarr = array(17);
            if($rest){
                $dept_id = array_column($rest, 'id');
                $defarr = array_merge($defarr,$dept_id);
            }
            $dept_ids = implode(",",$defarr);
            $sql .=" AND source_own_id in ({$dept_ids})";
        }

        if (!empty($filter['fenlei'])) {
            $sql .=" AND fenlei = ".$filter['fenlei'];
        }
        
        $res = $this->db->getAll($sql);                
        if($res){
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $res;
            $this->display();
        } else {
            $this -> error_msg = "没有查到相应的信息";
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> return_msg = array();
            $this->display();
        }
        
    }
    

     /*
    *通过渠道ID获得门店信息
    */

     public function GetShopInfoByShopName() {
        if (isset($this->filter['shop_name'])) {
            $shop_name = $this->filter['shop_name'];
            $sql = "select id from cuteframe.shop_cfg where is_delete=0 and shop_name in('".$shop_name."')";
            $shop_id = $this->db->getOne($sql);

            if (!empty($shop_id)) {
                $ids = implode(',', $arr);
                $sql = "select s.id,s.company_id,c.company_name from sales_channels s left join company c on c.id = s.company_id where s.is_deleted = 0 and channel_own_id ='$shop_id' limit 1";
                $res = $this->db->getAll($sql);
                if($res){
                    $this -> error = 0;
                    $this -> return_sql = $sql;
                    $this -> return_msg = $res;
                    $this->display();
                } else {
                    $this -> error_msg = "没有查到相应的信息";
                }
            } else {
                $this -> error_msg = "参数异常";
            }
        } else {
            $this -> error_msg = "参数异常";
        }
        
        $this -> error = 1;
        $this -> return_sql = '';
        $this -> return_msg = array();
        $this->display();
    }

    
     /*
    *通过渠道ID获得公司信息
    */

     public function getSalesChannelByOrderId() {
        if (isset($this->filter['dept_ids'])) {
            $ids = $this->filter['dept_ids'];
            $arr = preg_split('/,/', $ids, -1, PREG_SPLIT_NO_EMPTY);
            if (!empty($arr)) {
                $ids = implode(',', $arr);
                $sql = "select s.id,s.company_id,c.company_name from sales_channels s left join company c on c.id = s.company_id where s.is_deleted = 0 and s.id in ({$ids})";
                $res = $this->db->getAll($sql);
                if($res){
                    $this -> error = 0;
                    $this -> return_sql = $sql;
                    $this -> return_msg = $res;
                    $this->display();
                } else {
                    $this -> error_msg = "没有查到相应的信息";
                }
            } else {
                $this -> error_msg = "参数异常";
            }
        } else {
            $this -> error_msg = "参数异常";
        }
        
        $this -> error = 1;
        $this -> return_sql = '';
        $this -> return_msg = array();
        $this->display();
    }

	     /*
    *通过渠道ID获得线上线下信息
    */

     public function GetChannelClassByIds() {
        if (isset($this->filter['ids'])) {
            $ids = $this->filter['ids'];
            if (is_array($this->filter['ids'])) {
                $ids = implode(',', $this->filter['ids']);
                $sql = "SELECT DISTINCT channel_class FROM sales_channels WHERE id IN ({$ids}) AND NOT ISNULL(channel_class)";
               
                
            } else {
                $sql = "SELECT DISTINCT channel_class FROM sales_channels WHERE id ={$ids} AND NOT ISNULL(channel_class)";
            }
			$res = $this->db->getAll($sql);
			if($res){
                    $this -> error = 0;
                    $this -> return_sql = $sql;
                    $this -> return_msg = $res;
                    $this->display();
                } else {
                    $this -> error_msg = "没有查到相应的信息";
                }
        } else {
            $this -> error_msg = "参数异常";
        }
        
        $this -> error = 1;
        $this -> return_sql = '';
        $this -> return_msg = array();
        $this->display();
    }
    
    /**
     * 获取公司列表，支持批量按ids去查，如果没有指明，则查所有, 返回id及company_name.
     */
    public function getCompanyList() {
        if (isset($this->filter['ids']) && !empty($ids)) {
            $sql = "SELECT id, company_name FROM company WHERE id IN ({$ids})";        
        } else {
            $sql = "SELECT id, company_name FROM company WHERE is_deleted = 0 ";
        }
        $res = $this->db->getAll($sql);
        if($res){
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $res;
            $this->display();
        } else {
            $this -> error_msg = "没有查到相应的信息";
        }
        
        $this -> error = 1;
        $this -> return_sql = '';
        $this -> return_msg = array();
        $this->display();
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
