<?php 

class AsyncDelegate {

    private static $client = null;
    private static $inited = false;
    
    private static function init() {
        if (self::$client == null && defined('JOB_SERVER')) {
            $job_servers = Util::get_defined_array_var('JOB_SERVER');
            if (!empty($job_servers)) {
                self::$client = new GearmanClient();
                foreach ($job_servers as $serv) {
                    try {
                    	$resp = self::$client->addServer($serv['host'], $serv['port']);
                    	if ($resp === true) self::$inited = true;
                    	else {
                    		file_put_contents(KELA_ROOT.'/async.log', "can not add server ".$serv['host'].':'.$serv['port'].PHP_EOL, FILE_APPEND);
                    	}
                    } catch (Exception $e) {
                    	file_put_contents(KELA_ROOT.'/async.log', "exception when adding server ".$serv['host'].':'.$serv['port'].', and error is:'.$e->getMessage().PHP_EOL, FILE_APPEND);
                    }
                }
                //fault tolerance 
                return self::$inited;
            }
        }
        
        return self::$inited;
    }
    
    public static function dispatch($queue, $payload) {
    	if (self::init()) {
    		$payload['sys_scope'] = SYS_SCOPE;
    		$payload['timestamp'] = date('Y-m-d H:i:s'); //time();
    		$payload['msgId'] = self::getGUID();
    		$payload['who'] = isset( $_SESSION['userName'] ) ? $_SESSION['userName'] : 'sys';
    		
    		$num = 0;
    		do {
                self::$client->doBackground($queue, json_encode($payload, JSON_UNESCAPED_UNICODE));
    			if (self::$client->returnCode() == GEARMAN_SUCCESS) {
    				return true;
    			}
    			
    			$num++;
    		} while($num < 3);
    		
    		file_put_contents(KELA_ROOT.'/async.log', json_encode($payload).PHP_EOL, FILE_APPEND);
    	}
    	return false;
    }
    
    private static function getGUID() {
        if (function_exists('com_create_guid')) {
            return substr(com_create_guid(), 1, 36);
        } else {
            mt_srand((double) microtime() * 10000);
            $charid = strtoupper(md5(uniqid(rand(), true)));
             
            $guid = substr($charid,  0, 8) . '-' .
                substr($charid,  8, 4) . '-' .
                substr($charid, 12, 4) . '-' .
                substr($charid, 16, 4) . '-' .
                substr($charid, 20, 12);
                 
            return $guid;
        }
    }
}



?>