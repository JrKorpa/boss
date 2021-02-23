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
	 * 通过石包号查询石包是否存在
	 * @return json
	 */
	public function getShibaoInfo() 
	{

		$s_time = microtime();
		$where = '';
		$sql = "SELECT * FROM `dia` WHERE 1"; //暂时用＊号

		if (!empty($this->filter['shibao'])) 
		{
			$sql .= " and `shibao` = '{$this->filter['shibao']}'";
		} 
		else 
		{
				$this->error = 1;
				$this->return_sql = $sql;
				$this->error_msg = "石包不能为空!!";
				$this->return_msg = array();
				$this->display();
		}
		$data = $this->db->getRow($sql);

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if (!$data)
		{
				$this->error = 1;
				$this->return_sql = $sql;
				$this->error_msg = "未查询到石包号";
				$this->return_msg = array();
				$this->display();
		} else 
		{
				$this->error = 0;
				$this->return_sql = $sql;
				$this->return_msg = $data;
				$this->display();
		}
	}
	/**
	 * 添加还石单
	 */
    public function addHsOrder()
	{
		$s_time = microtime();
		$time = date('Y-m-d H:i:s');

		if (!empty($this->filter['info'])) 
		{
			$info = $this->filter['info'];
		} 
		else 
		{
			$this->error = 1;
			$this->return_sql = '';
			$this->error_msg = "单据不能为空!!";
			$this->return_msg = array();
			$this->display();
		}
		if (!empty($this->filter['data'])) 
		{
			$data = $this->filter['data'];
		} 
		else 
		{
			$this->error = 1;
			$this->return_sql = '';
			$this->error_msg = "数据不能为空!!";
			$this->return_msg = array();
			$this->display();
		}
		define("root_path", dirname(dirname(dirname(dirname(__FILE__)))));
		require_once(root_path . '/frame/init.php');
		$pdo = DB::cn(46)->db(); //pdo对象

		try 
		{

			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
			$pdo->beginTransaction(); //开启事务

			#1、添加主表单据信息
			$sql = "INSERT INTO `dia_order` (`type`,`status`,`order_time`,`in_warehouse_type`,`account_type`,`adjust_type`,`send_goods_sn`,`shijia`,`make_order`,`prc_id`,`prc_name`,`addtime`,`info`,`times`) VALUES ('{$info['type']}','{$info['status']}','{$info['order_time']}',{$info['in_warehouse_type']},'{$info['account_type']}','0','{$info['send_goods_sn']}','{$info['shijia']}','{$info['make_order']}','{$info['prc_id']}','{$info['prc_name']}','{$info['addtime']}','{$info['info']}','{$info['times']}') ";
			//file_put_contents('d://uu.txt',$sql."<br>",FILE_APPEND);

			$pdo->query($sql);
			$id = $pdo->lastInsertId();
			#2、买附表明细添加
			//var_dump($data);exit;
			foreach ($data as $value)
			{
				$sql = "insert into `dia_order_goods`(
					`order_id`,`order_type`,`shibao`,`zhengshuhao`,`zhong`,`yanse`,`jingdu`,`qiegong`,`duichen`,`paoguang`,`yingguang`,`num`,`zongzhong`,`caigouchengben`,`xiaoshouchengben`) values (
					{$id},'{$info['type']}','{$value['shibao']}',
					'{$value['zhengshuhao']}','{$value['zhong']}','{$value['yanse']}','{$value['jingdu']}',
					'{$value['qiegong']}','{$value['duichen']}','{$value['paoguang']}','{$value['yingguang']}',
					'{$value['num']}','{$value['zongzhong']}','{$value['caigouchengben']}','{$value['xiaoshouchengben']}')";
				//file_put_contents('d://uu.txt',$sql."<br>",FILE_APPEND);
				$pdo->query($sql);
			}
			#3、计算单据总数量
			$sql = "select sum(`num`) as goods_num,  sum(`zongzhong`) as goods_zhong, sum(`caigouchengben` * `zongzhong`) as goods_total from dia_order_goods where order_id = '$id'";
			//file_put_contents('d://uu.txt',$sql."\n",FILE_APPEND);
			//$ret = $this->db->getRow($sql);
			
			$res=$pdo->query($sql);
            $ret=$res->fetch(PDO::FETCH_ASSOC);   
			#4、计算修改总数量、总重、总金额
			if($ret!=false){
			    $sql = "UPDATE `dia_order` SET `goods_num` = '" . $ret['goods_num'] . "',`goods_zhong` = '" . $ret['goods_zhong'] . "' ,`goods_total` = '" . $ret['goods_total'] . "' WHERE `order_id` ='" . $id . "' ";
			    $pdo->query($sql);
			} 
			//业务逻辑结束
			//file_put_contents('d://uu.txt',$sql,FILE_APPEND);
		}
		catch (Exception $e) 
		{
			$pdo->rollback(); //事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); //开启sql语句自动提交
			$this->error = 1;
			$this->return_sql = $sql;
			$this->error_msg = "创建还石单失败";
			$this->return_msg = 0;
			$this->display();                       
		}

		$pdo->commit(); //如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); //开启sql语句自动提交
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		$this->error = 0;
		$this->return_sql = $sql;
		$this->return_msg = '生成还石单成功<br><font color="red">单号HS'.$id.'</font>';
		$this->display();
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
	/* ------------------------------------------------------ */
	//-- 记录日志信息
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
