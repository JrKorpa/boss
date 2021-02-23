<?php
/**
 *  -------------------------------------------------
 *   @file		: DBSessionHandler.class.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-05-27
 *   @update	:
 *  -------------------------------------------------
 */
/**
 *	自定义会话处理
 */
class DBSessionHandler
{
	private static $clientIP = null;
	private static $lifetime = null;
	private static $clientBrowner = null;
	private static $time = null;
	private static $handler = null;
	private static $admin_id = null;
	private static $Session_Id = null;
	private static $session_name = 'kela__fds78gui';
	private static $memcache = null;

	private static function init() 
	{
		self::$clientIP = Util::getClicentIp();
		self::$clientBrowner = Util::getBrowser();
		
		$life_time = 14400; //4个小时
		ini_set('session.gc_maxlifetime', $life_time);
		self::$lifetime = ini_get('session.gc_maxlifetime'); //存活时间
		self::$time = time();
		
		self::$memcache = new Memcache();
		self::$memcache->addserver(MEMCACHE_SERVER,MEMCACHE_PORT);
	
		$connected = self::$memcache->connect(MEMCACHE_SERVER,MEMCACHE_PORT);
		if (!$connected) {
			$connected = self::$memcache->connect(MEMCACHE_SERVER,MEMCACHE_PORT);
			if (!$connected) {
				die ("Could not connect");
			}
		}
	}

        /**
	* start,会话开始
	*/
    public static function start()
	{
        self::init();
        session_set_save_handler(
                array(__CLASS__, 'open'),
                array(__CLASS__, 'close'),
                array(__CLASS__, 'read'),
                array(__CLASS__, 'write'),
                array(__CLASS__, 'destroy'),
                array(__CLASS__, 'gc')
        );
		ini_set("session.use_trans_sid", 0);
		ini_set("session.use_cookies", 1);
		ini_set("session.use_only_cookies", 1);
		
        session_start();
		session_regenerate_id(true);
        session_name(self::$session_name);
        ob_start();
    }

    public static function open($savePath, $sessionName)
	{
		return true;
    }

    public static function close()
	{
        return true;
    }

	/**
	* read,读取会话
	*/
    public static function read($sessionid)
	{
        $sessionid = self::getSessionId();
        $result = self::getSessionData($sessionid);
        if (!$result) return '';
        
		// 已经过期的，直接删除
        if (isset($result['s_time']) && (self::$time - $result['s_time']) >= self::$lifetime)
        {
			self::destroy($sessionid);
			return '';
        }
        else
        {
			// 没有过期的，尝试更新session生命周期
            self::tryUpdataSessionLife($sessionid, $result);
        }
        $data = $result['data'];
        $data = self::my_session_encode($data);
        return $data;
    }

    private static function getSessionData($sessionid)
    {
	    $raw_data = self::$memcache->get($sessionid);
	    if (empty($raw_data)){
                       $raw_data = self::$memcache->get($sessionid);
                       if (empty($raw_data)) 
                                 return array();
            }
	   
	    return self::decompress($raw_data);
    }

	/**
	* write,存储会话
	*/
	public static function write($sessionid, $data) {
		$sessionid = self::getSessionId();
		
		$raw_data = self::unserializesession($data);
		$result = self::getSessionData($sessionid);
		if ($result)
		{
			// 内容有变化，直接进行替换
			if ($result['data'] != $raw_data) {
				$result['data'] = $raw_data;
				$result['s_time'] = self::$time;
				
				$raw_str = self::compress($result);
				return self::$memcache->replace($sessionid, $raw_str, false, self::$lifetime);
			} else {
				// 尝试延长session生命周期
				self::tryUpdataSessionLife($sessionid, $result);
				return true;
			}
		}
		else
		{
			$result = array(
				's_time' => self::$time,
				'data' => $raw_data
			);
			
			$raw_str = self::compress($result);
			return self::$memcache->set($sessionid, $raw_str, false, self::$lifetime);
		}
	}

	/**
	* destroy,销毁会话
	*/
	public static function destroy($session_id)
	{
		if ($session_id == self::getSessionId()) {
			return self::$memcache->delete($session_id);
		}
		return true;
	}

	/**
	* gc,超时回收
	*/
	public static function gc($lifetime) {
		return true;
	}

	public static function setAdminId($admin_id) {
		return true;
	}

	/*session—data 数据格式转义*/
	public static function unserializesession($data) {
		if (empty($data)) 
		{
			return array();
		}
		$vars = preg_split('/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff^|]*)\|/', $data, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		for ($i = 0; isset($vars[$i]); $i++) 
		{
			$result[$vars[$i++]] = unserialize($vars[$i]);
		}
		return $result;
	}

	/*session—data 数据格式转义*/
	public static function my_session_encode($array)
	{
		$raw = '';
		if (is_null($array)) 
		{
			return $raw;
		}
		$line = 0;
		$keys = array_keys($array);
		foreach ($keys as $key) 
		{
			$value = $array[$key];
			$line ++;
			$raw .= $key . '|';
			if (is_array($value) && isset($value['huge_recursion_blocker_we_hope'])) 
			{
				$raw .= 'R:' . $value['huge_recursion_blocker_we_hope'] . ';';
			} 
			else 
			{
				$raw .= serialize($value);
			}
			$array[$key] = Array('huge_recursion_blocker_we_hope' => $line);
		}
		return $raw;
	}

	public static function gen_session_key($session_id)
	{
		return sprintf('%08x', $session_id);
	}
	
	/*获取cookie里面的session_id*/
	public static function getSessionId()
	{
		if (!empty($_COOKIE[self::$session_name]) && self::$Session_Id == null) 
		{
			self::$Session_Id = $_COOKIE[self::$session_name];
		}
		if (self::$Session_Id) 
		{
			$tmp_session_id = substr(self::$Session_Id, 0, 32);
			if (self::gen_session_key($tmp_session_id) == substr(self::$Session_Id, 32)) 
			{
				return $tmp_session_id;
			}
			else 
			{
				$id = md5(uniqid(rand(), true));
				self::$Session_Id = $id . self::gen_session_key($id);
				setcookie(self::$session_name, self::$Session_Id, 0, '/',ROOT_DOMAIN, false);
				return $id;
			}
		}
		else
		{
			$id = md5(uniqid(rand(), true));
			self::$Session_Id = $id.self::gen_session_key($id);
			setcookie(self::$session_name,self::$Session_Id, 0,'/',ROOT_DOMAIN,false);
			return $id;
		}
	}

	/* 更新session 存活时间 */
	private static function tryUpdataSessionLife($sessionid, $cache) 
	{
		if (!$cache) return false;
		// 每隔1小时，自动延长session生命周期
		if (!isset($cache['s_time']) || (isset($cache['s_time']) && (self::$time - $cache['s_time']) >= 800)) {
            $cache['s_time'] = time();

			$raw_str = self::compress($cache);
			return self::$memcache->replace($sessionid, $raw_str, false, self::$lifetime);
		}
		return false;
	}
	
	
	private static function compress($raw_data) {
	    $raw_str = serialize($raw_data);
	    if (strlen($raw_str) > 204800) {
	        //超过200K则压缩字符串
	        $raw_str = 'gzd:' . (gzcompress($raw_str, 3));
	    }
	     
	    return $raw_str;
	}
	
	private static function decompress($raw_str) {
	    if (empty($raw_str)) return array();
	    if (substr($raw_str, 0, 4) == 'gzd:') {
	        $raw_str = substr($raw_str, 4);
	        $raw_str = gzuncompress($raw_str);
	    }
	    
	    return unserialize($raw_str);
	}
}
?>
