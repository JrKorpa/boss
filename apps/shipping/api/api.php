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
	 * 通过调拨单号，检测提交过来的调拨单号是否有绑定包裹单
	 * @param bill_no 调拨单号
	 */
    public function CheckExistBing(){
        $s_time = microtime();
        if( isset($this->filter['bill_no']) && !empty($this->filter['bill_no']) )
		{
        	$sql = "SELECT `id` FROM `ship_parcel_detail` WHERE `zhuancang_sn` = '{$this->filter['bill_no']}' LIMIT 1";
        	$row = $this->db->getOne($sql);
        }
		else
		{
            $this ->error = 1;
            $this ->return_sql = '';
            $this ->error_msg = "参数传入错误，调拨单号不能为空";
            $this ->return_msg = array();
            $this->display();
		}


         // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if(!$row){
            $this ->error = 1;
            $this ->return_sql = $sql;
            $this ->error_msg =  0;
            $this ->return_msg = 0;
            $this->display();
        }else{
            $this ->error = 0;
            $this ->return_sql = $sql;
            $this ->return_msg = array($row);
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
	/*------------------------------------------------------ */
    //-- 根据订单编码获取对应的快递方式和快递编码
    //-- add by zhangruiying
    /*------------------------------------------------------ */

	public function getExpressDelivery()
	{
		$order_no=isset($this->filter['order_sn'])?$this->filter['order_sn']:'';
		if(empty($order_no))
		{
			$this ->error = 1;
            $this ->return_sql = '';
            $this ->error_msg = "订单编码为空！";
            $this ->return_msg = array();
		}
		else
		{
			$sql="select freight_no,express_id,order_no from ship_freight where order_no in({$order_no})";
			//file_put_contents('c:/aa.txt',print_r($sql,true),FILE_APPEND);
			$row = $this->db->getAll($sql);
			if(empty($row))
			{
				$this ->error = 1;
				$this ->return_sql =$sql;
				$this ->error_msg = "订单没有对应的快递信息";
				$this ->return_msg = array();
			}
			else
			{
				$freight_no=array();
				$express_id=array();
				foreach($row as $k=>$v)
				{
					$express_id[$v['order_no']]=$v['express_id'];
					$freight_no[$v['order_no']]=$v['freight_no'];

				}
				$arr['freight_no']=$freight_no;
				$arr['express_id']=$express_id;
				$this ->error = 0;
				$this ->return_sql =$sql;
				$this ->error_msg = "";
				$this ->return_msg =$arr;
			}

		}


	}
        //更具调拨单号获取快递单号 added by Renee
        public function GetExpressSnByBillno() {
            if (isset($this->filter['zhuancang_sn']) && !empty($this->filter['zhuancang_sn'])) {
                $zhuancang_sn = $this->filter['zhuancang_sn'];
                $sql = "select `express_sn` from `ship_parcel_detail` as spd,`ship_parcel` as sp where sp.id=spd.`parcel_id` and `zhuancang_sn`='{$zhuancang_sn}'";
                $row = $this->db->getRow($sql);
            }
            else{
                $this ->error = 1;
                $this ->return_sql = '';
                $this ->error_msg = "参数传入错误，调拨单号不能为空";
                $this ->return_msg = array();
                $this->display();
	    }
            // 记录日志
            $reponse_time = microtime() - $s_time;
            $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

            //返回信息
            if(!$row){
                $this ->error = 1;
                $this ->return_sql = $sql;
                $this ->error_msg =  0;
                $this ->return_msg = 0;
                $this->display();
            }else{
                $this ->error = 0;
                $this ->return_sql = $sql;
                $this ->return_msg = array($row);
                $this->display();
            }
        }

}
?>
