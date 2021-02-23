<?php
require_once('MysqlDB.class.php');
require_once('Utils.class.php');

class Worker {

	private $worker;
	private $db_config;
	private $client;
	
	public function __construct($job_server_list, $db_conf, $use_as_client = false) {
		$this->db_config = $db_conf;		
		$this->worker = new GearmanWorker();
		if ($use_as_client === true) $this->client = new GearmanClient();
		foreach ($job_server_list as $serv) {
			try {
				$this->worker->addServer($serv['host'], $serv['port']);
				if ($use_as_client === true) $this->client->addServer($serv['host'], $serv['port']);
			} catch (Exception $e) {
				file_put_contents(__DIR__.'/gearman.log', "exception when adding server ".$serv['host'].':'.$serv['port'].', and error is:'.$e->getMessage().PHP_EOL, FILE_APPEND);
			}
		}
	}

	public function registry_queue($queue) {
		
		$this->worker->addFunction($queue, function($job) use($queue) {
			$message = $job->workload();
			echo 'got a message:'.$message.PHP_EOL;
	
			$data = json_decode($message, true);
			$db = null;
			try {
				if (empty($data) || !isset($data['event']) || empty($data['event'])) return false;
							
				// 备份消息实体
				$data_path = __DIR__ .'/data/'.date('Ymd').'/data.log';
				$data_path_dir = dirname($data_path);
				if (!is_dir($data_path_dir)) {
					@mkdir($data_path_dir, 0777, true);
				}
				file_put_contents($data_path, $message.PHP_EOL, FILE_APPEND);			

				$event = trim($data['event']);
				$sys_scope= isset($data['sys_scope']) ? $data['sys_scope'] : 'boss';
				
				// 是否在支持的系统范围内
				if (!in_array($sys_scope, array_keys($this->db_config))) {
					echo $sys_scope .' is not supported.';
					return;
				}				
				
				// 加载消息处理函数
				$file = __DIR__ .'/'.$queue.'/'.$event.'.php';
				include_once $file;		

				if (function_exists($sys_scope.'_on_'.$event)) {
					$mt = $sys_scope.'_on_'.$event;
				} else if (function_exists('on_'.$event)) {
					$mt = 'on_'.$event;
				} else {
					$mt = '';
					echo 'no handler exists for this message.';
				}
				
				if ($mt) {
					$db = new MysqlDB($this->db_config[$sys_scope]);
					$mt($data, $db);
				}
			} catch (Exception $ex) {
				echo $ex->getMessage();
				file_put_contents(__DIR__ . '/'.date('Ymd').'_error.log',  $data['msgId'].PHP_EOL, FILE_APPEND);
				mail("quanxiaoyun@kela.cn", "任务执行异常", json_encode(array('error' => $ex->getMessage(), 'msg' => $data), JSON_UNESCAPED_UNICODE));
			} finally {
				if ($db != null) $db->dispose();
			}
		});
	}
	
	public function start() {
		while(true){
			try {
				$this->worker->work();
			} catch(Exception $ex) {
				echo $ex->getMessage();
				sleep(5);
			}
		}
	}
	
	public function dispatch($queue, $sys_scope, $payload) {
        if ($this->client) {
            $payload['sys_scope'] = $sys_scope;
            $payload['timestamp'] = date('Y-m-d H:i:s'); //time();
            $payload['msgId'] = $this->getGUID();
            
            $i = 0;
			do {
				$handle = $this->client->doBackground($queue, json_encode($payload, JSON_UNESCAPED_UNICODE));
				if ($this->client->returnCode() == GEARMAN_SUCCESS) return true;

			} while(++$i < 3);
        }        
        
		echo 'fail to dispatch msg'.PHP_EOL;
		file_put_contents(__DIR__ . '/'.$queue.'_'.date('Ymd').'.dispatch_fail.log', json_encode($payload, JSON_UNESCAPED_UNICODE).PHP_EOL, FILE_APPEND);	
        return false;
    }
    
    private function getGUID() {
        if (function_exists('com_create_guid')) {
            return substr(com_create_guid(), 1, 36);
        } else {

			if (function_exists('openssl_random_pseudo_bytes') === true) {
				$data = openssl_random_pseudo_bytes(16);
				$data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
				$data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
				return vsprintf('%s%s%s%s%s%s%s%s', str_split(bin2hex($data), 4));
			}
			
            mt_srand((double) microtime() * 10000);
            $charid = strtoupper(md5(uniqid(rand(), true)));
                 
            return substr($charid, 0, 8).substr($charid, 8, 4).substr($charid, 12, 4).substr($charid, 16, 4).substr($charid, 20, 12);
        }
    }
	
}
?>
