<?php
if (!defined('IN_API'))
{
    die('Hacking attempt');
}

/*
*  -------------------------------------------------
*	PDO单例连接类
*	数据库连接、异常处理、查询多条、分页查询、查询单条、查询单字段、执行、insertiId、序列号（随机一个数字做主键数字）
*  -------------------------------------------------
*/
final class KELA_API_DB
{
	private $config;//数据库配置
	private $_db;//数据库连接资源句柄
	private static $_instance;//数据库连接实例
	private static $errorMode = PDO::ERRMODE_EXCEPTION;  //ERRMODE_SILENT  ERRMODE_WARNING ERRMODE_EXCEPTION
	private $_res;//PDOStatement
	/**
	 *	__construct，构造函数，连接数据库
	 *
	 *	@param String $conn 数据库连接标示
	 *
	 *	@return 对象自身
	 */
	public function __construct ($config)
	{
		if($config['db_type']!='mysql')
		{
			throw new DBException('对不起，目前只支持mysql!');
		}
		if(empty($config['db_host']))
		{
			throw new DBException('请配置数据库地址db_host!');
		}
		if(empty($config['db_port']))
		{
			$config['db_port']=3306;
		}
		if(empty($config['db_user']))
		{
			throw new DBException('请配置数据库用户db_user!');
		}
		if(empty($config['db_name']))
		{
			throw new DBException('请配置数据库名称db_name!');
		}

		$this->_db = new PDO($config['db_type'].':host='.$config['db_host'].';port='.$config['db_port'].';dbname='.$config['db_name'], $config['db_user'], $config['db_pwd'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));
		$this->_db->setAttribute(PDO::ATTR_ERRMODE, self::$errorMode);
		return $this;
	}

	/**
	 *	__clone，克隆对象时触发，私有，禁止对象克隆
	 *
	 */
	private function __clone()
	{

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
	public static function getInstance()
	{
        if(!(self::$_instance instanceof self))
		{
			self::$_instance = new self();
        }
        return self::$_instance;
    }

	/**
	 *	db，返回Pdo对象
	 *
	 *	@return object Pdo
	 *
	 */
	public function db ()
	{
		if ($this->_db != '' && is_object($this->_db))
		{
            return $this->_db;
        }
		else
		{
			return null;
		}
	}

	/**
	 *	_formatStmt，格式化数据
	 *
	 *	@param String $sql
	 *	@param Array $params
	 *
	 */
    protected function _formatStmt(&$sql, array &$params)
    {
        foreach ($params as $key => $val)
		{
            if (!is_scalar($val) && !is_int($key))
			{
                $str = '';
                foreach ($val as $x)
				{
                    $str .= $this->db()->quote($x).',';
                }
                $str = rtrim($str, ',');
                $regex = '#([^\w]{1})('.$key.')([^\w]{1})#';
                $sql = preg_replace($regex, '${1}'.$str.'${3}', $sql);
                unset($params[$key]);
            }
        }
    }
	/**
	 *	query，处理一条SQL语句
	 *
	 *	@param String $sql 语句
	 *	@param Array $params SQL动态参数
	 *
	 */
    public function query($sql,$params = array())
    {
		//格式化参数
    	// select * from aaa where user_id =? and sss=?  array(0=>array(0=>'ss'))
    	// select * from aaa where user_id =:user_id and sss=:sss  array(0=>array(':user_id'=>'ss'))
        $this->_formatStmt($sql, $params);
		$this->_res = $this->db()->prepare($sql);
		foreach ($params as $k => &$p)
		{
			if (is_int($k))
			{
				$this->_res->bindValue($k+1,$p,is_int($p) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
			else
			{
				$this->_res->bindParam($k,$p,is_int($p) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
		}
		if ($this->_res->execute() === false)
		{
            return false;
        }
		return $this->_res;
    }

	/**
	 *	insertId，最后一条写入数据的主键
	 *	PDO::lastInsertId — Returns the ID of the last inserted row or sequence value
	 *
	 *	@return Integer
	 *
	 */
    public function insertId()
    {
		return $this->db()->lastInsertId();
    }

	/**
	 *	optimizeTable，优化数据表
	 *	@param String $table 待优化的数据表
	 *
	 */
	public function optimizeTable($table)
	{
		$sql = "OPTIMIZE TABLE $table";
		$this->query($sql);
	}

	/**
	 *	queryResult，查询数据库
	 *	@param String $sql SQL语句
	 *	@param Array $params SQL动态参数
	 *	@param Boolean $useCache 是否使用memcache缓存，默认情况下只对小结果集缓存
	 */
	private function queryResult($sql, $params = array(), $useCache = null,$fetch_stytle=PDO::FETCH_ASSOC) {
		// $memInstance = localCache::getInstance();
		// $_pattern = $memInstance->sqlPattern($sql, $params);
		// if($useCache !== false && $result = $memInstance->get($_pattern))
		// {
		// 	if ($result !== "__kelawebNULL__" )
		// 	{
		// 		return $result;
		// 	}
		// }
		$result = $this->query($sql,$params)->fetchAll($fetch_stytle);
		// if($useCache !== false && $useCache)
		// {
		// 	localCache::getInstance()->set($_pattern, !empty($result) ? $result : "__kelawebNULL__");
		// }
		return $result;
	}

	/**
	 *	getRow，获取单条记录
	 *
	 *	@param String $sql 语句
	 *	@param Array $params SQL动态参数
	 *	@param Boolean $useCache 是否使用缓存
	 *
	 *	@return array
	 *
	 */
	public function getRow($sql,$params=array(),$useCache=false,$fetch_stytle=PDO::FETCH_ASSOC)
	{
		$arr = $this->queryResult($sql,$params,$useCache,$fetch_stytle);
		return !empty($arr[0]) ? $arr[0] : array() ;
	}

	/**
	 *	getOne，获取指定列的数据
	 *
	 *	@param String $sql 语句
	 *	@param Array $params SQL动态参数
	 *	@param Boolean $useCache 是否使用缓存
	 *
	 *	@return array
	 *
	 */
	public function getOne($sql,$params=array(),$useCache=false)
	{
		$data = $this->getRow($sql,$params,$useCache,PDO::FETCH_NUM);
		return !empty($data[0]) ? $data[0] : false;
	}

	/**
	 *	getAll，获取多条记录
	 *
	 *	@param String $sql 语句
	 *	@param Array $params SQL动态参数
	 *	@param Boolean $useCache 是否使用缓存
	 *
	 *	@return array
	 *
	 */
	public function getAll($sql,$params=array(),$useCache=false)
	{
		$arr = $this->queryResult($sql,$params,$useCache);
		return $arr ? $arr : array() ;
	}

	/**
	 *	getTables，取得数据库的表信息
	 *
	 *	@return array
	 *
	 */
	public function getTables()
	{
		$result = $this->getAll("SHOW TABLES",array(),false);
		$info = array();
		foreach ($result as $key => $val)
		{
			$info[$key] = current($val);
		}
		return $info;
	}

	/**
	 *	getFields，获取表字段
	 *
	 *	@param String $table 数据表名
	 *
	 *	@return array
	 */
	public function getFields($table)
	{
		return $this->getAll ("DESCRIBE `$table`",array(),false);
	}

	/**
	 *	commit，事务提交[数据表必须是innode引擎]
	 *
	 *	@param Array $sqlArr
	 *	array('insert into xxx (id,...) values (null,...)','update xxx set field=value where ...')
	 */
	public function commit ($sqlArr=array())
	{
		if(empty($sqlArr)) return false;
		$this->db()->setAttribute(PDO::ATTR_AUTOCOMMIT,0);

		try{
			$this->db()->beginTransaction();//开启事务处理
			$len = count($sqlArr);
			for ($i=0;$i<$len;$i++)
			{
				$sql = $sqlArr[$i];
				$this->db()->exec($sql);
			}
		}catch(PDOException $e){
			$this->db()->rollback();
			$this->db()->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
			return false;
		}
		$this->db()->commit();
		$this->db()->setAttribute(PDO::ATTR_AUTOCOMMIT,1); 
		return true;
	}
    public function autoExecute($table, $field_values, $mode = 'INSERT', $where = '', $querymode = '')
    {
        $_field_names = $this->getFields($table);
        foreach($_field_names as $v){
            $field_names[]=$v['Field'];
        }

        $sql = '';
        if ($mode == 'INSERT')
        {
            $fields = $values = array();
            foreach ($field_names AS $value)
            {
                if (array_key_exists($value, $field_values) == true)
                {
                    $fields[] = $value;
                    $values[] = "'" . $field_values[$value] . "'";
                }
            }

            if (!empty($fields))
            {
                $sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
            }
        }
        else
        {
            $sets = array();
            foreach ($field_names AS $value)
            {
                if (array_key_exists($value, $field_values) == true)
                {
                    $sets[] = $value . " = '" . $field_values[$value] . "'";
                }
            }

            if (!empty($sets))
            {
                $sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $sets) . ' WHERE ' . $where;
            }
        }
        if ($sql)
        {
            return $this->query($sql, $field_values);
        }
        else
        {
            return false;
        }
    }
    
    public function getPageList ($sql,$params = array(), $page=1, $pageSize=20,$useCache=false,$recordCount=false)
    {
        try
        {
            $countSql = preg_replace('/^(SELECT.+?\bFROM\b)/i','SELECT COUNT(*) count FROM', $sql,1);
            if(preg_match("/group by/is", $countSql)){
                $countSql = "select count(*) count from ({$countSql})t";
            }
            $data['pageSize'] = (int)$pageSize<1? 20: (int)$pageSize;
            if($recordCount===false){
               $data['recordCount'] = $this->getOne($countSql,$params,$useCache);
               $data['countSql'] = $countSql;
            }else{
               $data['recordCount'] = $recordCount;   
               $data['countSql'] = "NONE";
            }
            $data['pageCount'] = ceil($data['recordCount']/$data['pageSize']);
            $data['page'] = $data['pageCount']==0? 0: ((int)$page<1? 1: (int)$page);
            $data['page'] = $data['page']>$data['pageCount']? $data['pageCount']:$data['page'];
            $data['isFirst'] = $data['page']>1? false: true;
            $data['isLast'] = $data['page']<$data['pageCount']? false: true;
            $data['start'] = ($data['page']==0)? 1: ($data['page']-1)*$data['pageSize']+1;
            $data['sql'] = $sql.' LIMIT '.($data['start']-1).','.$data['pageSize'];            
            $data['data'] = $this->queryResult($data['sql'],$params,$useCache);
        }
        catch(Exception $e)
        {
            return false;
        }
        return $data;
    }
    
    
    
}
