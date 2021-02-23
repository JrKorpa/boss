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

        private static function init() 
        {
                self::$clientIP = Util::getClicentIp();
                self::$clientBrowner = Util::getBrowser();
//                $life_time = 86400;
//                ini_set('session.gc_maxlifetime', $life_time);
                self::$lifetime = ini_get('session.gc_maxlifetime'); //存活时间
                self::$time = time();
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

                if (self::$clientIP != $result['ip']) 
                {
                        self::destroy($sessionid);
                        return '';
                }

                if ((self::$time - $result['expiry']) >= self::$lifetime)
		{
                        self::destroy($sessionid);
                        return '';
                }
                else
                {
                        self::UpdataSessionTime($sessionid);
                }
                $data = unserialize($result['data']);
                $data = self::my_session_encode($data);
                return $data;
        }

        private static function getSessionData($sessionid)
	{
                $sql = "SELECT sesskey,ip,data,expiry FROM sessions WHERE sesskey = ? ";
                $result = DB::cn(0)->getRow($sql, array($sessionid));
                return $result;
        }

        /**
	* write,存储会话
	*/
        public static function write($sessionid, $data) {
                $sessionid = self::getSessionId();
                $data = self::unserializesession($data);
                $data = serialize($data);
                $result = self::getSessionData($sessionid);
                if ($result)
		{
                        // || self::$time > ($result['update_time'] + 300
                        if ($result['data'] != $data || self::$time > ($result['expiry']))
                        {
                                $sql = 'UPDATE sessions SET data = ?,expiry = ? WHERE sesskey = ?';
                                DB::cn(0)->query($sql,array($data,self::$time,$sessionid));
                                return true;
                        }
                }
                else
                {
                        if (!empty($data))
                        {
                                $sql = "INSERT INTO sessions (sesskey,ip,expiry,data) values(?,?,?,?)";
                                DB::cn(0)->query($sql,array($sessionid,self::$clientIP,self::$time,$data));
                        }
                }
                return true;
        }

	/**
	* destroy,销毁会话
	*/
        public static function destroy($sesson_id)
	{
                $sql = "DELETE FROM sessions WHERE sesskey = ? ";
                DB::cn(0)->query($sql, array($sesson_id));
                return true;
        }

        /**
	* gc,超时回收
	*/
        public static function gc($lifetime) {
                $sql = "DELETE FROM sessions WHERE expiry < ? ";
                //DB::cn("WWW_SE")->query($sql,array(time() - $lifetime));
                DB::cn(0)->query($sql, array(time() - self::$lifetime));
                return true;
        }

        public static function setAdminId($admin_id) {
                $sessionid = self::getSessionId();
                $sql = 'UPDATE sessions SET adminid=? WHERE sesskey = ?';
                DB::cn(0)->query($sql, array($admin_id, $sessionid));
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

        private static function UpdataSessionTime($sessionid) 
        {
                $sql = 'UPDATE sessions SET expiry = ? WHERE sesskey = ?';
                DB::cn(0)->query($sql, array(time(), $sessionid));
                return true;
        }

}
?>