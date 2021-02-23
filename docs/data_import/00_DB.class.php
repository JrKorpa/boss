<?php 
/**
 *  -------------------------------------------------
 * 文件说明		Mysql PDO 数据库类文件
 * @file		: DB.calss.php
 * @author		: yangxt <yangxiaotong@163.com>
 *  -------------------------------------------------
*/
class DB{
	private static $mydb = null;

	final function __construct($conf,$charset = 'uft8'){
		self::$mydb = new PDO($conf['dsn'], $conf['user'], $conf['password']);
	}

	public function db()
	{
		return self::$mydb;
	}

	/*选数据库*/
	public function selectDB($db){
		return $this->db()->Query('use '.$db);
	}

	/*设置字符集*/
	public function setChar($char){
		return $this->db()->exec('set names '.$char);
	}

	public function insertId()
	{
		return $this->db()->lastInsertId();
	}

	public function getAll($sql)
	{
		$obj = $this->db()->query($sql);
        return $obj->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getRow($sql)
	{
		$obj = $this->db()->query($sql,PDO::FETCH_ASSOC);
        return $obj->fetch();
	}

	public function getOne($sql)
	{
		$obj = $this->db()->query($sql,PDO::FETCH_NUM);
        $row = $obj->fetch();
        return $row[0];
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
        $param = array();$_param = array();
    	foreach ($fields as $v) {
            if(array_key_exists($v,$data)){
                $param[$v] = ":".$v;
				$_param[$v] = "'".$data[$v]."'";
            }
        }

        if($act == 'INSERT'){
            $sql = "INSERT INTO `".$table."` (".implode(',',array_keys($param)).") VALUES (".implode(',',$param).")";
            $_sql = "INSERT INTO `".$table."` (".implode(',',array_keys($_param)).") VALUES (".implode(',',$_param).")";
			//print_r($_sql);exit;
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
            // print_r($sql);exit;
            $data = array_merge($data,$where);
        }
        $_res = $this->db()->prepare($sql);
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

	public function setDefault($data,$default){
		foreach ($data as $k=>$row) {
			foreach ($default as $lab=>$v) {
				$data[$k][$lab] = $v;
			}
		}
		return $data;
	}


}


