<?php
/**
 *  -------------------------------------------------
 * 文件说明		Mysql PDO 数据库操作类
 * @file		: MysqlDB.calss.php
 * @author		: yangxt <yangxiaotong@163.com>
 *  -------------------------------------------------
*/
class MysqlDB {

    protected static $_pdo = null;
    public  $statement = null;

    final function __construct($conf) {
        try {
            self::$_pdo = new PDO($conf['dsn'], $conf['user'], $conf['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES '.$conf['charset']));
            self::$_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function db()
    {
    	return self::$_pdo;
    }

	public function selectDB($db)
	{
		return $this->db()->query('use '.$db);
	}

	public function setChar($char){
		return $this->db()->exec('set names '.$char);
	}

	public function insertId()
	{
		return $this->db()->lastInsertId();
	}

	public function prepare($sql){
        if(empty($sql)){return false;}
        $this->statement = $this->db()->prepare($sql);
        return $this->statement;
    }
    //返回影响的行数 [insert/update/delete]
    public function exec($sql)
    {
        if(empty($sql)){return false;}
        try{
            return $this->db()->exec($sql);
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    //返回PDOStatement,[select]链式操作
    public function query($sql)
    {
        if(empty($sql)){return false;}
        $this->statement = $this->db()->query($sql);
        return $this->statement;
    }

    public function insert($table,$row)
    {
    	if(empty($row) || !is_array($row)){
    		return false;
    	}
    	return $this->autoExec($row,$table);
    }

    public function update($tabel,$set,$where)
    {
    	if(empty($row) || !is_array($row) || empty($where) || !is_array($row)){
    		return false;
    	}
    	return $this->autoExec($set,$table,'UPDATE',$where);
    }

    public function getAll($sql,$fetch_type = PDO::FETCH_ASSOC)
    {
    	if(empty($sql)){return false;}
    	$data = $this->query($sql)->fetchAll($fetch_type);
    	return $data;
    }

    public function getRow($sql,$fetch_type = PDO::FETCH_ASSOC)
    {
    	if(empty($sql)){return false;}
		$data = $this->query($sql)->fetch($fetch_type);
    	return $data;
    }

    public function getOne($sql,$fetch_type = PDO::FETCH_NUM)
    {
    	if(empty($sql)){return false;}
		$data = $this->query($sql)->fetch($fetch_type);
    	return $data[0];
    }

    public function getFields($tabel)
	{
		$sql = "DESCRIBE ".$tabel;
		$res = $this->getAll($sql);
		foreach ($res as $val) {
			$fields[] = $val['Field'];
		}
		return $fields;
	}

	/**
	 * 自动 插入/更新
	 * @param array 	$data
	 * @param string	$table
	 * @param string 	$act
	 * @param array 	$where
	 * @return bool
	 */
    public function autoExec($data,$table,$act='INSERT',$where=array()){
    	$fields = $this->getFields($table);
        $param = array();
    	foreach ($fields as $v) {
            if(array_key_exists($v,$data)){
                $param[$v] = ":".$v;
            }
        }
        if($act == 'INSERT'){
        	$sql = sprintf("INSERT INTO `%s` (%s) VALUES (%s)",$table,implode(',',array_keys($param)),implode(',',$param));
            //$sql = "INSERT INTO `".$table."` (".implode(',',array_keys($param)).") VALUES (".implode(',',$param).")";
        }
        if(($act == 'UPDATE') && !empty($where)){
            $set = '';
            foreach ($param as $k => $v) {
                $set .= "`".$k."` = ".$v.",";
            }
            $set = rtrim($set,',');
            $_where = '';
            foreach ($where as $k=>$v) {
                $_where .= "`".$k."` = :".$k." AND ";
            }
            $_where = rtrim($_where,' AND ');
            $sql = "UPDATE `".$table."` SET ".$set." WHERE ".$_where;
            $data = array_merge($data,$where);
        }
        $_res = $this->prepare($sql);
        try{
            $this->db()->beginTransaction();//开启事务处理
                $_res->execute($data);
                //$_res->debugDumpParams();exit;
        }catch(PDOException $e){
            $this->db()->rollback();
            $this->db()->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
            return false;
        }
        $this->db()->commit();
        $this->db()->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
        return true;
    }

    /**
	 * 替换字典
	 * @param array $data
	 * @param array $dict
	 * @return mixed
	 */
    public function replaceDict($data,$dict)
    {
    	foreach ($data as $k=>$row) {
    		foreach ($dict as $lab=>$val) {
    			if(array_key_exists($lab,$row)){
				    $data[$k][$lab] = $val[$data[$k][$lab]];
    			}
    		}
    	}
    	return $data;
    }

    /**
     * 设置默认值
     */
    public function setDefault($data,$default){
		foreach ($data as $k=>$row) {
			foreach ($default as $lab=>$v) {
				$data[$k][$lab] = $v;
			}
		}
		return $data;
	}

	/**
	 * 替换字段名称
	 * @param array	$filter
	 * @return bool|string
	 */
	public function replaceFields($filter){
		if(empty($filter)){return false;}
        $r_sel = '';
        foreach ($filter as $k => $v) {
            $r_sel .= "`".$k."` AS `".$v."`,";
        }
        return rtrim($r_sel,',');
    }

    /**
	 *	批量更新/插入
	 * @param $data
	 * @param $table
	 * @param string $act
	 * @param array $where 现只判断等于情况
	 * @return bool
	 * @note	: 注[data与where 字段不能重复]
	 */
	public function autoExecALL($data,$table,$act='INSERT',$where = array()){
		$_fields = $this->getFields($table);

		if(count($data) != count($where)){
			return false;
		}
		foreach ($_fields as $v) {
			if(array_key_exists($v,$data[0])){
				$param[$v] = ":".$v;
			}
		}
		if($act == 'INSERT'){
			$sql = "INSERT INTO `".$table."` (".implode(',',array_keys($param)).") VALUES (".implode(',',$param).")";
		}
		if(($act == 'UPDATE') && !empty($where)){
			$set = '';
			foreach ($param as $k => $v) {
				$set .= "`".$k."` = ".$v.",";
			}
			$set = rtrim($set,',');
			$_where = '';
			foreach ($where[0] as $k=>$v) {
				$_where .= "`".$k."` = ':".$k."' AND ";
			}
			$_where = rtrim($_where,' AND ');
			$sql = "UPDATE `".$table."` SET ".$set." WHERE ".$_where;

			$data = array_map([$this,'arr_merge'],$data,$where);
		}
		$this->_res = $this->db()->prepare($sql);

		try{
			$this->db()->beginTransaction();//开启事务处理
			foreach ($data as $row) {
				$this->_res->execute($row);
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

	/**
	 * 合并三维数组
	 */
	public function arr_merge($n,$m){
		$x = count($n);
		$y = count($m);
		if($x == $y){
			foreach ($n as $k=>$v) {
				$n[$k] = array_merge($n[$k],$m[$k]);
			}
			return $n;
		}else{
			return false;
		}
	}

    /**
     * 如果货品没有goods_warehouse 信息，写入柜位信息。规则：计算出货品所在的仓库的默认柜位，写入到goods_warehouse表中
     */
    public function CreateDefaultBox($goods_id , $warehouse_id){
        if($warehouse_id == 0 || $warehouse_id == ''){
            echo $error = "{$goods_id} 的warehouse_id = {$warehouse_id},数据异常\r\n";
            file_put_contents(__DIR__.'/log/error_warehouse_id.log',$error, FILE_APPEND);
            return false;
        }
        $time = date('Y-m-d H:i:s');
        #获取仓库的默认柜位ID
        $sql = "SELECT `id` FROM `warehouse_box` WHERE `warehouse_id` = {$warehouse_id} AND `box_sn` = '0-00-0-0' LIMIT 1";
        $box_id = $this->getOne($sql);
        try{
            $sql = "INSERT INTO `goods_warehouse` (`good_id`,`warehouse_id`,`box_id`,`add_time`,`create_time`,`create_user`) VALUES ('{$goods_id}',{$warehouse_id},{$box_id},'{$time}','{$time}','system')";
            $this->query($sql);
        }catch(PDOException $e){
            echo $error = "{$sql} 生成柜位失败\r\n";
            file_put_contents(__DIR__.'/log/no_goods_warehouse.log',$error, FILE_APPEND);
            return false;
        }
            echo $error = "{$goods_id} 生成柜位成功\r\n";
            file_put_contents(__DIR__.'/log/no_goods_warehouse.log',$error, FILE_APPEND);

    }

}
?>
