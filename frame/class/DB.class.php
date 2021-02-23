<?php

/**
 *  -------------------------------------------------
 *   @file		: Db.class.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-04-28
 *   @update	:
 *  -------------------------------------------------
 */
/*
 *  -------------------------------------------------
 * 	PDO单例连接类
 * 	数据库连接、异常处理、查询多条、分页查询、查询单条、查询单字段、执行、insertiId、序列号（随机一个数字做主键数字）
 *  -------------------------------------------------
 */
final class KDB {

        private $config; //数据库配置
        private $_db; //数据库连接资源句柄
        private static $_instance; //数据库连接实例
        private static $errorMode = PDO::ERRMODE_EXCEPTION;  //ERRMODE_SILENT  ERRMODE_WARNING ERRMODE_EXCEPTION
        private $_res; //PDOStatement
        private static $_CON;

        /**
         * 	__construct，构造函数，连接数据库
         *
         * 	@param String $conn 数据库连接标示
         *
         * 	@return 对象自身
         */
        private function __construct($conn) {
                if (is_null($this->config)) {
                        $this->config = new IniFile();
                        $this->config->Load(KELA_PATH . '/common/web.config');
                }

                $config = $this->config->GetSection('DbConfig' . $conn);
                if (empty($config['db_type'])) {
                        throw new DBException('请配置数据库类型DbConfig' . $conn . '::db_type!');
                }
                if ($config['db_type'] != 'mysql') {
                        throw new DBException('对不起，目前只支持mysql，请变更数据库类型DbConfig' . $conn . '::db_type!');
                }
                if (empty($config['db_host'])) {
                        throw new DBException('请配置数据库地址DbConfig' . $conn . '::db_host!');
                }
                if (empty($config['db_port'])) {
                        $config['db_port'] = 3306;
                }
                if (empty($config['db_user'])) {
                        throw new DBException('请配置数据库用户DbConfig' . $conn . '::db_user!');
                }
                /* if(empty($config['db_pwd']))
                  {
                  throw new DBException('请配置数据库密码DbConfig'.$conn.'::db_pwd!');
                  } */
                if (empty($config['db_name'])) {
                        throw new DBException('请配置数据库名称DbConfig' . $conn . '::db_name!');
                }

                self::$_CON = $conn;
                $this->_db = new PDO($config['db_type'] . ':host=' . $config['db_host'] . ';port=' . $config['db_port'] . ';dbname=' . $config['db_name'], $config['db_user'], $config['db_pwd'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));
                $this->_db->setAttribute(PDO::ATTR_ERRMODE, self::$errorMode);
                return $this;
        }

        /**
         * 	__clone，克隆对象时触发，私有，禁止对象克隆
         *
         */
        private function __clone() {

        }

        /**
         * getInstance，单例模式创建数据库连接
         * 如果不用单例模式，那每次都要new操作，但是每次new都会消耗大量的内存资源和系统资源，而且每次打开和关闭数据库连接都 是对数据库的一种极大考验和浪费。
         *
         * @param String $conn 数据库连接标示
         *
         * @return Object 实例化对象
         *
         */
        public static function getInstance($conn) {
                if (!(self::$_instance instanceof self) || self::$_CON != $conn) {
                        self::$_instance = new self($conn);
                }
                return self::$_instance;
        }

        /**
         * 	db，返回Pdo对象
         *
         * 	@return object Pdo
         *
         */
        public function db() {
                if ($this->_db != '' && is_object($this->_db)) {
                        return $this->_db;
                } else {
                        return null;
                }
        }

        /**
         * 	_formatStmt，格式化数据
         *
         * 	@param String $sql
         * 	@param Array $params
         *
         */
        protected function _formatStmt(&$sql, array &$params) {
                foreach ($params as $key => $val) {
                        if (!is_scalar($val) && !is_int($key)) {
                                $str = '';
                                foreach ($val as $x) {
                                        $str .= $this->db()->quote($x) . ',';
                                }
                                $str = rtrim($str, ',');
                                $regex = '#([^\w]{1})(' . $key . ')([^\w]{1})#';
                                $sql = preg_replace($regex, '${1}' . $str . '${3}', $sql);
                                unset($params[$key]);
                        }
                }
        }

        /**
         * 	query，处理一条SQL语句
         *
         * 	@param String $sql 语句
         * 	@param Array $params SQL动态参数
         *
         */
        public function query($sql, $params = array()) {
                //格式化参数
                // select * from aaa where user_id =? and sss=?  array(0=>array(0=>'ss'))
                // select * from aaa where user_id =:user_id and sss=:sss  array(0=>array(':user_id'=>'ss'))
//                if (preg_match('/\bdelete\b.+(user_)/i', $sql)) {
//                        Util::L(date("[c]") . $_SESSION['realName'].'===>IP:'.Util::getClicentIp(),'frame/u.log');
//                        Util::L($sql,'frame/u.log');
//                        Util::L($params,'frame/u.log');
//                }
                $this->_formatStmt($sql, $params);
                $this->_res = $this->db()->prepare($sql);
                foreach ($params as $k => &$p) {
                        if (is_int($k)) {
                                $this->_res->bindValue($k + 1, $p, is_int($p) ? PDO::PARAM_INT : PDO::PARAM_STR);
                        } else {
                                $this->_res->bindParam($k, $p, is_int($p) ? PDO::PARAM_INT : PDO::PARAM_STR);
                        }
                }
                if ($this->_res->execute() === false) {
                        return false;
                }
                return $this->_res;
        }

        /**
         * 	insertId，最后一条写入数据的主键
         * 	PDO::lastInsertId — Returns the ID of the last inserted row or sequence value
         *
         * 	@return Integer
         *
         */
        public function insertId() {
                return $this->db()->lastInsertId();
        }

        /**
         * 	optimizeTable，优化数据表
         * 	@param String $table 待优化的数据表
         *
         */
        public function optimizeTable($table) {
                $sql = "OPTIMIZE TABLE $table";
                $this->query($sql);
        }

        /**
         * 	queryResult，查询数据库
         * 	@param String $sql SQL语句
         * 	@param Array $params SQL动态参数
         * 	@param Boolean $useCache 是否使用memcache缓存，默认情况下只对小结果集缓存
         */
        private function queryResult($sql, $params = array(), $useCache = null, $fetch_stytle = PDO::FETCH_ASSOC) {
                // $memInstance = localCache::getInstance();
                // $_pattern = $memInstance->sqlPattern($sql, $params);
                // if($useCache !== false && $result = $memInstance->get($_pattern))
                // {
                // 	if ($result !== "__kelawebNULL__" )
                // 	{
                // 		return $result;
                // 	}
                // }
                $result = $this->query($sql, $params)->fetchAll($fetch_stytle);
                // if($useCache !== false && $useCache)
                // {
                // 	localCache::getInstance()->set($_pattern, !empty($result) ? $result : "__kelawebNULL__");
                // }
                return $result;
        }

        /**
         * 	getRow，获取单条记录
         *
         * 	@param String $sql 语句
         * 	@param Array $params SQL动态参数
         * 	@param Boolean $useCache 是否使用缓存
         *
         * 	@return array
         *
         */
        public function getRow($sql, $params = array(), $useCache = false, $fetch_stytle = PDO::FETCH_ASSOC) {
                $arr = $this->queryResult($sql, $params, $useCache, $fetch_stytle);
                return !empty($arr[0]) ? $arr[0] : array();
        }

        /**
         * 	getOne，获取指定列的数据
         *
         * 	@param String $sql 语句
         * 	@param Array $params SQL动态参数
         * 	@param Boolean $useCache 是否使用缓存
         *
         * 	@return array
         *
         */
        public function getOne($sql, $params = array(), $useCache = false) {
                $data = $this->getRow($sql, $params, $useCache, PDO::FETCH_NUM);
                return !empty($data[0]) ? $data[0] : false;
        }

        /**
         * 	getAll，获取多条记录
         *
         * 	@param String $sql 语句
         * 	@param Array $params SQL动态参数
         * 	@param Boolean $useCache 是否使用缓存
         *
         * 	@return array
         *
         */
        public function getAll($sql, $params = array(), $useCache = false) {
                $arr = $this->queryResult($sql, $params, $useCache);
                return $arr ? $arr : array();
        }

        /**
         * 	getTables，取得数据库的表信息
         *
         * 	@return array
         *
         */
        public function getTables() {
                $result = $this->getAll("SHOW TABLES", array(), false);
                $info = array();
                foreach ($result as $key => $val) {
                        $info[$key] = current($val);
                }
                return $info;
        }

        /**
         * 	getFields，获取表字段
         *
         * 	@param String $table 数据表名
         *
         * 	@return array
         */
        public function getFields($table) {
                return $this->getAll("DESCRIBE `$table`", array(), false);
        }

        /**
         * 	commit，事务提交[数据表必须是innode引擎]
         *
         * 	@param Array $sqlArr
         * 	array('insert into xxx (id,...) values (null,...)','update xxx set field=value where ...')
         */
        public function commit($sqlArr = array()) {
                if (empty($sqlArr))
                        return false;
                $this->db()->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);

                try {
                        $this->db()->beginTransaction(); //开启事务处理
                        $len = count($sqlArr);
                        for ($i = 0; $i < $len; $i++) {
                                $sql = $sqlArr[$i];
                                $this->db()->exec($sql);
                        }
                } catch (PDOException $e) {
                        $this->db()->rollback();
                        $this->db()->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
                        return false;
                }
                $this->db()->commit();
                $this->db()->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
                return true;
        }

        /**
         * 	getSeq，序列发生器，用来生成不重复的序列值
         *
         * 	@param $fieldName string 序列的名称
         * 	@param $step int 序列号间隔
         * 	@param $start int 序列号的起始数值
         *
         * 	@return int 新的序列值
         */
        public function getSeq($fieldName, $step = 1, $start = 1) {
                $tables = $this->getTables();
                $table = DB_SEQUENCE_TABLENAME;
                $step = (int) $step;
                $start = (int) $start;
                if (!in_array($table, $tables)) {
                        $sql = "CREATE TABLE $table (seq_name VARCHAR( 20 ) NOT NULL ,seq_num BIGINT( 20 ) DEFAULT 1 NOT NULL ,PRIMARY KEY (seq_name))";
                        $this->query($sql);
                }
                $res = $this->getRow("SELECT seq_num FROM $table WHERE seq_name='$fieldName'", array(), false);
                if (!$res) {
                        $this->query("INSERT INTO $table VALUES('$fieldName', $start)");
                        $seq = $start;
                } else {
                        $this->query("UPDATE $table SET seq_num=seq_num+($step) WHERE seq_name='$fieldName'");
                        $seq = $res["seq_num"] + $step;
                }
                return $seq;
        }

        /**
         * 	getPageList，分页数据查询函数
         * 	@param String $sql 数据查询语句
         * 	@param Array $params SQL动态参数
         * 	@param Integer $page 当前预查询页码
         * 	@param Integer$pageSize 每页显示多少条纪录
         * 	@return array数据查询结果,以及数据的分页信息
         *  数据查询结果,以及数据的分页信息
         *  array('pageSize' => 每页显示的条数
         * 'recordCount' => 总纪录数
         * 'pageCount' => 总页数
         * 'page' => 当前页码
         * 'isFirst' => 是否第一页
         * 'isLast' => 是否最后一页
         * 'start' => 返回结果的第一条纪录的序号
         * 'sql' => 查询的sql语句
         * 'data' => 查询得到的数据结果
         * )
         * 数据查询结果,以及数据的分页信息
         */
        public function getPageList($sql, $params = array(), $page = 1, $pageSize = 20, $useCache = false) {
                try {
                        if($pageSize>200000)
                            $pageSize=200000;

                        if($pageSize>10000){
                            $sql=str_replace('ORDER BY a.total_age DESC', ' ',$sql);
                        }
                        $countSql = preg_replace('/^(SELECT.+?\bFROM\b)/i', 'SELECT COUNT(*) count FROM', $sql, 1);
//			$countSql = "SELECT COUNT(*) count FROM (".$sql.") AS xxxxxx";
                        $data['pageSize'] = (int) $pageSize < 1 ? 20 : (int) $pageSize;
                        $data['recordCount'] = $this->getOne($countSql, $params, $useCache);
                        //$data['recordCount'] = count($this->getAll($sql,$params,$useCache));
                        $data['pageCount'] = ceil($data['recordCount'] / $data['pageSize']);
                        $data['page'] = $data['pageCount'] == 0 ? 0 : ((int) $page < 1 ? 1 : (int) $page);
                        $data['page'] = $data['page'] > $data['pageCount'] ? $data['pageCount'] : $data['page'];
                        $data['isFirst'] = $data['page'] > 1 ? false : true;
                        $data['isLast'] = $data['page'] < $data['pageCount'] ? false : true;
                        $data['start'] = ($data['page'] == 0) ? 1 : ($data['page'] - 1) * $data['pageSize'] + 1;
                        $data['sql'] = $sql . ' LIMIT ' . ($data['start'] - 1) . ',' . $data['pageSize'];
                        $data['data'] = $this->queryResult($data['sql'], $params, $useCache);
                } catch (Exception $e) {
                        throw $e;
                }
                return $data;
        }
		//add by zhangruiying

		public function getPageListNew($sql, $params = array(), $page = 1, $pageSize = 20, $useCache = false) {
                try {
                        //$countSql = preg_replace('/^(SELECT.+?\bFROM\b)/i', 'SELECT COUNT(*) count FROM', $sql, 1);
			$countSql = "SELECT COUNT(1) count FROM (".$sql.") AS xxxxxx";
                        $data['pageSize'] = (int) $pageSize < 1 ? 20 : (int) $pageSize;
                        $data['recordCount'] = $this->getOne($countSql, $params, $useCache);
                        //$data['recordCount'] = count($this->getAll($sql,$params,$useCache));
                        $data['pageCount'] = ceil($data['recordCount'] / $data['pageSize']);
                        $data['page'] = $data['pageCount'] == 0 ? 0 : ((int) $page < 1 ? 1 : (int) $page);
                        $data['page'] = $data['page'] > $data['pageCount'] ? $data['pageCount'] : $data['page'];
                        $data['isFirst'] = $data['page'] > 1 ? false : true;
                        $data['isLast'] = $data['page'] < $data['pageCount'] ? false : true;
                        $data['start'] = ($data['page'] == 0) ? 1 : ($data['page'] - 1) * $data['pageSize'] + 1;
                        $data['sql'] = $sql . ' LIMIT ' . ($data['start'] - 1) . ',' . $data['pageSize'];
                        $data['data'] = $this->queryResult($data['sql'], $params, $useCache);
                } catch (Exception $e) {
                        throw $e;
                }
                return $data;
        }


        public function getPageListOpt($sql, $params = array(), $page = 1, $pageSize = 20, $useCache = false,$from_table='',$order_by='') {
                try {
              
                        if(!empty($from_table)){
                            if(!empty($order_by)){                                
                                $countSql = preg_replace('/^(SELECT.+?'.$from_table.')/is', 'SELECT COUNT(*) count '.$from_table, str_replace($order_by, ' ',$sql), 1);
                            }else    
                                $countSql = preg_replace('/^(SELECT.+?'.$from_table.')/is', 'SELECT COUNT(*) count '.$from_table, $sql, 1);                   
                        }else{    
                            $countSql = preg_replace('/^(SELECT.+?FROM)/is', 'SELECT COUNT(*) count FROM', $sql, 1);
                        }             
                        
                        $data['pageSize'] = (int) $pageSize < 1 ? 20 : (int) $pageSize;
                        $data['recordCount'] = $this->getOne($countSql, $params, $useCache);                        
                        $data['pageCount'] = ceil($data['recordCount'] / $data['pageSize']);
                        $data['page'] = $data['pageCount'] == 0 ? 0 : ((int) $page < 1 ? 1 : (int) $page);
                        $data['page'] = $data['page'] > $data['pageCount'] ? $data['pageCount'] : $data['page'];
                        $data['isFirst'] = $data['page'] > 1 ? false : true;
                        $data['isLast'] = $data['page'] < $data['pageCount'] ? false : true;
                        $data['start'] = ($data['page'] == 0) ? 1 : ($data['page'] - 1) * $data['pageSize'] + 1;
                        $data['sql'] = $sql . ' LIMIT ' . ($data['start'] - 1) . ',' . $data['pageSize'];
                        
                        $data['data'] = $this->queryResult($data['sql'], $params, $useCache);
                } catch (Exception $e) {
                        throw $e;
                }
                return $data;
        }



        //分批导数据分页
        public function getPageListForExport($sql, $params = array(), $page = 1, $pageSize = 20, $useCache = false,$recordCount = 0) {
            try {
                //$countSql = preg_replace('/^(SELECT.+?\bFROM\b)/i', 'SELECT COUNT(*) count FROM', $sql, 1);
                $countSql = "SELECT COUNT(1) count FROM (".$sql.") AS xxxxxx";
                $data['pageSize'] = (int) $pageSize < 1 ? 20 : (int) $pageSize;
                if($recordCount < 1){
                   $data['recordCount'] = $this->getOne($countSql, $params, $useCache);
                }else{
                   $data['recordCount'] = $recordCount;
                }
                //$data['recordCount'] = count($this->getAll($sql,$params,$useCache));
                $data['pageCount'] = ceil($data['recordCount'] / $data['pageSize']);
                $data['page'] = $data['pageCount'] == 0 ? 0 : ((int) $page < 1 ? 1 : (int) $page);
                $data['page'] = $data['page'] > $data['pageCount'] ? $data['pageCount'] : $data['page'];
                $data['isFirst'] = $data['page'] > 1 ? false : true;
                $data['isLast'] = $data['page'] < $data['pageCount'] ? false : true;
                $data['start'] = ($data['page'] == 0) ? 1 : ($data['page'] - 1) * $data['pageSize'] + 1;
                $data['sql'] = $sql . ' LIMIT ' . ($data['start'] - 1) . ',' . $data['pageSize'];
                $data['data'] = $this->queryResult($data['sql'], $params, $useCache);
            } catch (Exception $e) {
                throw $e;
            }
            return $data;
        } 

        /**
         * 	CreateStyleTable，生成款式库属性纵表
         *
         * 	@param $fieldName string 表名
         * 	@param $step int field_arr
         * 	@return boolean
         */
        public function createStyleTable($table_name, $field_arr) {
                $tables = $this->getTables();
                if (!in_array($table_name, $tables)) {
                        //展示方式：1文本框，2单选，3多选，4下拉
                        $sql = "CREATE TABLE " . $table_name . "( id int(10) NOT NULL AUTO_INCREMENT, ";
                        foreach ($field_arr as $val) {
                                $field_name = $val['attribute_code'];
                                $commont = $val['attribute_name'];
                                $show_type = $val['show_type'];
                                if ($show_type == 1) {
                                        $sql.=" $field_name VARCHAR(50) NOT NULL COMMENT '" . $commont . "',";
                                } else if ($show_type == 2) {
                                        $sql.=" $field_name int(10) NOT NULL COMMENT '" . $commont . "',";
                                } else if ($show_type == 3) {
                                        $sql.=" $field_name VARCHAR(200) NOT NULL COMMENT '" . $commont . "',";
                                } else {
                                        $sql.=" $field_name int(10) NOT NULL COMMENT  '" . $commont . "',";
                                }
                        }
                        $sql.=" PRIMARY KEY (id))";
                        return $this->query($sql);
                } else {
                        return false;
                }
        }

}

/**
 *  -------------------------------------------------
 * 	DbFactory，数据库工厂类
 *
 *  -------------------------------------------------
 */
class DB {

        /**
         * 数据库操作实例化列表
         */
        private static $links = array();

        /**
         * 构造函数
         */
        private function __construct() {

        }

        /**
         * 	克隆函数
         *
         */
        private function __clone() {

        }

        /**
         * 	getInstance，返回当前终级类对象的实例
         * 	@param String $conn 连接配置0会话库
         * 	@param Boolean $checkConnection 强制连接
         * 	@return Object
         *
         */
        public static function cn($conn, $checkConnection = false) {
                $p = $conn;
                if (!isset(self::$links[$p]) || !is_object(self::$links[$p])) {
                        self::$links[$p] = KDB::getInstance($p);
                } else if ($checkConnection) {
                        if (self::$links[$p]->db()->getAttribute(PDO::ATTR_CONNECTION_STATUS)) {
                                self::$links[$p] = KDB::getInstance($p);
                        }
                }
                return self::$links[$p];
        }

}

/**
 * 	localCache，memcache操作类
 *
 */
Class localCache {

        public $_memcache = null;
        private $_expire = 86400; //60 * 60 * 24
        private static $_instance;

        /**
         * 	__construct，构造函数，连接Memcache
         * 	@param String $expire 超时时间
         *
         */
        public function __construct($expire = null) {
                $this->_memcache = new Memcache;
                if (APP_DEBUG) {
                        //$this->_memcache->connect("localhost", 11211);
                        $this->_memcache->addserver(MEMCACHE_SERVER,MEMCACHE_PORT);
                        $this->_memcache->connect(MEMCACHE_SERVER,MEMCACHE_PORT);
                } else {
                        //$this->_memcache->pconnect("unix:///tmp/memcached.sock", 0);
                        $this->_memcache->addserver(MEMCACHE_SERVER,MEMCACHE_PORT);
                        $this->_memcache->connect(MEMCACHE_SERVER,MEMCACHE_PORT);
                }

                if ($expire) {
                        $this->_expire = $expire;
                }
                return $this;
        }

        public static function getInstance($expire = null) {
                if (!isset(self::$_instance) || !is_object(self::$_instance)) {
                        self::$_instance = new self($expire = null);
                }
                return self::$_instance;
        }

        /**
         * 	sqlPattern，根据sql字符串及参数生产一个字符串
         * 	@param String $sql SQL语句
         * 	@param Array $params SQL动态参数
         * 	@return 返回字符串
         */
        public function sqlPattern($sql, $params) {
                $pattern = $sql;
                if (!empty($params)) {
                        $pattern .= '_*_' . serialize($params);
                }
                if (strlen($pattern) > 64) {
                        $pattern = $this->genKey($pattern);
                }
                return $pattern;
        }

        public function get($key) {
                return $this->_memcache->get($this->genKey($key));
        }

        public function set($key, $value, $expireTime = NULL) {
                if (!$expireTime) {
                        $expireTime = $this->_expire;
                }
                //if (!is_string ($value)) $value = json_encode ($value);
                // DO NOT use json to encode object, because all non-public fields will be discard
                return $this->_memcache->set($this->genKey($key), $value, MEMCACHE_COMPRESSED, $expireTime);
        }

        public function delete($key) {
                return $this->_memcache->set($this->genKey($key), null);
        }

        public function genKey($seed) {
                return md5($seed);
        }

        public function flush() {
                return $this->_memcache->flush();
        }

}
