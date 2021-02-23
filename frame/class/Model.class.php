<?php
/*
*  -------------------------------------------------
*   @file		: Model.class.php
*   @link		:  www.kela.cn
*   @copyright	: 2014-2024 kela Inc
*   @author		: Laipiyang <462166282@qq.com>
*   @date		:
*   @update		:
*  -------------------------------------------------
*/
abstract class Model
{
	protected $_db;
	protected $_ID = null;
    protected $_objName; // same as table name in MySQL
    protected $_dataObject;
    protected $_fields;
    protected $_baseAttrs;
    protected $_prefix="";
    private $_dataCached = false;
    private $_strCN = "";
	protected $noPk = false;
	protected $pk = '';
	public $_fieldsDefine;//表字段定义
	/**
	 *	__construct，构造函数
	 *
	 *	@param Integer $id 记录号
	 *	@param String $strConn 数据库连接配置
	 *
	 */
    public function __construct($id = null,$strConn='')
	{
		if($strConn=='')
		{
			$strConn=$id;
			$id=null;
		}
        $this->_ID = $id === null ? null : intval($id);
        $this->_strCN = $strConn;
        $this->_baseAttrs = null;
        $this->_fieldsDefine = $this->_dataObject;
		$this->parseTable();
    }

	/**
	 *	db，连接数据库
	 *
	 */
	public function db ()
	{
		if(empty($this->_db))
		{
			$this->_db = DB::cn($this->_strCN);
		}
		return $this->_db;
	}

	public function hasPk ()
	{
		return !$this->noPk;
	}

	/**
	 *	pk，获取主键值
	 */
	public function pk ()
	{
		if(!$this->hasPk()) return false;
		return $this->_ID;
	}

	/**
	 *	getPk，获取主键
	 */
	public function getPk ()
	{
		if(!$this->hasPk()) return false;

		if($this->pk)
		{
			return $this->pk;
		}
		if($this->_prefix)
		{
			return $this->_prefix."_id";
		}
		return 'id';
	}

	/**
	 *	table，获取表名
	 */
	public function table ()
	{
		return $this->_objName;
	}
	/**
	 *	getMaxID，获取记录表的最大记录号+1
	 *
	 */
	public function getMaxID()
	{
		if(!$this->hasPk()) return false;
        $sql = "select max(".$this->getPk().")+1 AS id from `".$this->table()."` where ".$this->getPk()." < 10000000";
        return $this->db()->getOne($sql,array(),false);
    }

	//分析表结构
	public function parseTable()
	{
		if($this->table())
		{
			$info = $this->db()->getFields($this->table());
			foreach ($info as $v) {
				$this->_fields[] = $v['Field'];
			}
		}
	}

	//获取表字段
	public function getField(){
		return $this->_fields;
	}
	/**
	 * 获取数据库定义字段
	 */
	public function getFieldsDefine(){
	    return $this->_fieldsDefine;
	}
	/**
	 * 接收新数据
	 * @return array
	 */
	public function mkNewdo(){
		$newdo = [];
		foreach ($_REQUEST as $k => $v) {
			if(in_array($k,$this->_fields)){
				$newdo[$k] = _Post::get($k);
			}
			if(isset($newdo[$this->getpk()]) && $newdo[$this->getpk()] == ''){
				unset($newdo[$this->getpk()]);
			}
		}
		return $newdo;
	}

	/**
	 * getDataObject，获取记录
	 *
	 * @param Boolean $useCache 是否使用缓存
	 * @return null
	 */
    public function getDataObject($useCache=false)
	{
		if(!$this->hasPk()) return null;
        if(!$this->_ID) return null;
        if($this->_dataCached) return $this->_dataObject;
        $sql = "SELECT * FROM `".$this->table()."` WHERE ".$this->getPk()."={$this->pk()}";
		$row = $this->db()->getRow($sql,array(),$useCache);
        if(!$row) return null;
        $this->_dataCached = true;
        $this->_dataObject = $row;
        return $this->_dataObject;
    }

	/**
	 *	getValue，获取属性值
	 *
	 *	@param String $key 属性名
	 *	@param Boolean $realTime 实时查询
	 *
	 *	@return 属性值|null
	 *
	 */
    public function getValue($key, $realTime = false)
	{
        if(!$this->pk() || !array_key_exists($key, $this->_dataObject))
		{
            return null;
        }
        if(!$realTime && is_array($this->_baseAttrs) &&  array_key_exists($key, $this->_baseAttrs) && $this->_baseAttrs[$key])
		{
            return $this->_baseAttrs[$key];
        }
        else
		{
            $obj = $this->getDataObject($realTime);
            return isset($obj) ? $obj[$key] : null;
        }
    }

	/**
	 *	setValue，设置属性值
	 *
	 *	@param String $key 属性名
	 *	@param String/Integer $value 属性值
	 *  @throws
	 */
    public function setValue($key, $value)
	{
        if((array_key_exists($key, $this->_dataObject) || $this->fldExisted($key)) && strcasecmp($key, $this->getPk())!=0)
		{
            $this->_baseAttrs[$key] = $value;
        }
		else
		{
			throw new ObjectException($key);
		}
    }

	/**
	 *	处理字段
	 *
	 *	@param String $value
	 *
	 *	@return String
	 *
	 */
	final public static function add_special_char($value)
    {
        if ('*' == $value || false !== strpos($value, '(') || false !== strpos($value, '.') || false !== strpos($value, '`'))
		{
            //不处理包含* 或者 使用了sql方法。
        }
		else
		{
            $value = '`' . trim($value) . '`';
        }
        if (preg_match('/\b(select|insert|update|delete)\b/i', $value))
		{
            $value = preg_replace('/\b(select|insert|update|delete)\b/i', '', $value);
        }
        return $value;
    }

	/*
	* insertSql,生成插入语句
	*/
	public function insertSql ($do,$tableName = "")
	{   
	    unset($do[$this->getPk()]);//干掉主键
	    if(empty($tableName)){    		
   			$tableName = $this->table();
	    }
		$fields = array_keys($do);
        $valuedata = array_values($do);
		array_walk($fields, array($this, 'add_special_char'));
		foreach ($valuedata as $k=>$v){
		    $valuedata[$k] = $this->db()->db()->quote($v);
		}
		$field = implode('`,`', $fields);
        $value = implode(",",$valuedata);
        return "INSERT INTO ".$tableName."  (`" . $field . "`) VALUES (".$value.")";
	}
	public function insertSqlNew ($do,$tableName)
	{
	    if(empty($tableName)){
	        return false;
	    }
	    $fields = array_keys($do);
	    $valuedata = array_values($do);
	    array_walk($fields, array($this, 'add_special_char'));
	    foreach ($valuedata as $k=>$v){
	        $valuedata[$k] = $this->db()->db()->quote($v);
	    }
	    $field = implode('`,`', $fields);
	    $value = implode(",",$valuedata);
	    return "INSERT INTO ".$tableName." (`" . $field . "`) VALUES (".$value.")";
	}
	/*
	* updateSql,生成更新语句
	*/
	public function updateSql ($do)
	{
		$field = '';
		$fields = array();
		foreach ($do as $key=>$val)
		{
			switch (substr($val, 0, 2))
			{
				case '+=':
					$val = substr($val,2);
					if (is_numeric($val)) {
						$fields[] = self::add_special_char($key) . '=' . self::add_special_char($key) . '+' . $val;
					}
					else
					{
						continue;
					}
					break;
				case '-=':
					$val = substr($val, 2);
					if (is_numeric($val))
					{
						$fields[] = self::add_special_char($val) . '=' . self::add_special_char($key) . '-' . $val;
					}
					else
					{
						continue;
					}
					break;
				default:
					if(is_numeric($val))
					{
						$fields[] = self::add_special_char($key) . '=' . $val;
					}
					else if($val===null ||strtoupper($val)=='NULL')
	                {
	                    $fields[] = self::add_special_char($key) . '=NULL';
	                }
					else
					{
					    $val = $this->db()->db()->quote($val);
						$fields[] = self::add_special_char($key) . '='.$val;
					}
			}
        }
		$field = implode(',', $fields);

		$sql = "UPDATE `".$this->table()."` SET ".$field;
        $sql .= " WHERE ".$this->getPk()."={$this->pk()}";
		return $sql;
	}
	
	public function updateSqlNew ($do,$tableName="",$where="")
	{
	    $field = '';
	    $fields = array();
	    foreach ($do as $key=>$val)
	    {
	        switch (substr($val, 0, 2))
	        {
	            case '+=':
	                $val = substr($val,2);
	                if (is_numeric($val)) {
	                    $fields[] = self::add_special_char($key) . '=' . self::add_special_char($key) . '+' . $val;
	                }
	                else
	                {
	                    continue;
	                }
	                break;
	            case '-=':
	                $val = substr($val, 2);
	                if (is_numeric($val))
	                {
	                    $fields[] = self::add_special_char($val) . '=' . self::add_special_char($key) . '-' . $val;
	                }
	                else
	                {
	                    continue;
	                }
	                break;
	            default:
	                if(is_numeric($val))
	                {
	                    $fields[] = self::add_special_char($key) . '=' . $val;
	                }
	                else if($val===null ||strtoupper($val)=='NULL')
	                {
	                    $fields[] = self::add_special_char($key) . '=NULL';
	                }
	                else
	                {
	                    $val = $this->db()->db()->quote($val);
	                    $fields[] = self::add_special_char($key) . '='.$val;
	                }
	        }
	    }
	    $field = implode(',', $fields);
	
	    if(empty($tableName) ){
	        $tableName = $this->table();
	        $sql = "UPDATE `".$tableName."` SET ".$field;
	        $sql .= " WHERE ".$this->getPk()."={$this->pk()}";
	    }else{
	        if(empty($where)){
	            $where = "1=0";
	        }
	        $sql = "UPDATE ".$tableName." SET ".$field;
	        $sql .= " WHERE {$where}";
	    }	    
	    return $sql;
	}
	
	/**
	 *	create，信息入库
	 *
	 *	@param Boolean $return_data
	 *
	 *	@return last inserted id
	 *
	 */
	public function create($return_data=false)
	{
		if(!$this->hasPk()) return null;
        if(!$this->_baseAttrs) return null;
        unset($this->_baseAttrs[$this->getPk()]);
		$fields = array_keys($this->_baseAttrs);
        $valuedata = array_values($this->_baseAttrs);
		array_walk($fields, array($this, 'add_special_char'));
		$field = implode('`,`', $fields);
        $value = str_repeat('?,',count($fields)-1).'?';
        $sql = "INSERT INTO `".$this->table()."` (`" . $field . "`) VALUES (". $value .")";
        $this->db()->query($sql,$valuedata);
		$lastid = $this->db()->insertId();
		if($return_data)
		{
			$sql = "select * from `".$this->table()."` where `".$this->getPk()."`=".$lastid;
			return $this->db()->query($sql)->fetch(PDO::FETCH_ASSOC);
		}
 		return $lastid;
   }

	/**
	 *	save，信息更新
	 *
	 *	@param Boolean $return_data
	 *
	 *
	 *
	 */
	public function save($return_data=false)
	{
		if(!$this->hasPk()) return false;
        if(!$this->pk()) return false;
		$params = array();
		$field = '';
		$fields = array();
		if(!$this->_baseAttrs)
		{
			die('请设置更新的内容');
		}
        foreach ($this->_baseAttrs as $key=>$val)
		{
			switch (substr($val, 0, 2))
			{
				case '+=':
					$val = substr($val,2);
					if (is_numeric($val)) {
						$fields[] = self::add_special_char($key) . '=' . self::add_special_char($key) . '+' . $val;
					}
					else
					{
						continue;
					}
					break;
				case '-=':
					$val = substr($val, 2);
					if (is_numeric($val))
					{
						$fields[] = self::add_special_char($val) . '=' . self::add_special_char($key) . '-' . $val;
					}
					else
					{
						continue;
					}
					break;
				default:
					$fields[] = self::add_special_char($key) . '=:' . $key;
					$k = ':' . $key;
					//$params[$k] = $this->db()->db()->quote($val);
					$params[$k] = ($val!==null)?$val:'';
			}
        }
		$field = implode(',', $fields);
		$sql = "UPDATE `".$this->table()."` SET ".$field;
        $sql .= " WHERE ".$this->getPk()."={$this->pk()}";
        $res = $this->db()->query($sql,$params);
		$id = $this->_ID;
		if($return_data)
		{
			$sql = "select * from `".$this->table()."` where `".$this->getPk()."`=".$id;
			return $this->db()->query($sql)->fetch(PDO::FETCH_ASSOC);
		}
 		return $res->rowCount();
    }

	/**
	 *	delete，删除
	 *
	 *	@param Array $data 删除条件
	 *
	 */
    public function delete($data = array())
	{
        if(!$this->pk() && !($data)) return false;
		$params = array();
		$fields = array();
        $sql = "DELETE from `".$this->table()."` WHERE ";
		if($this->hasPk())
		{
			$sql .=$this->getPk()."={$this->pk()}";
		}
		else
		{
			$sql .="1=1";
		}
		if($data)
		{
			foreach ($data as $col => $val )
			{
				$fields[] = self::add_special_char($col) . '=:'.$col;
				$params[':'.$col] = $val;
			}
			$field = implode(' AND ', $fields);
			$sql .=" AND ".$field;
		}
        return $this->db()->query($sql,$params);
    }

	/**
	 *	计算两个数组的差集
	 *
	 *	@param Array $newdo 新数据
	 *	@param Array $olddo 旧数据
	 *
	 *	@return Array
	 *
	 */
	protected function array_diffx ($newdo,$olddo)
	{
		$r = array();
		if(!($olddo and $newdo)) throw new Exception('array_diffx的参数必须是两个非空数组');
		foreach($newdo as $i=>$l)
		{
			if(!isset($olddo[$i]) || $olddo[$i] != $l)
			{
				$r[$i]=$l;
			}
        }
        return $r;
	}

	protected function dealData ($newdo,$olddo)
	{
		$data = $newdo;
		if(!empty($newdo[$this->getPk()]))
		{
			$data = $this->array_diffx($newdo,$olddo);
		}
		return $data;
	}

	/**
	 *	保存数据
	 *
	 *	@param Array $newdo 新数据
	 *	@param Array $olddo 旧数据
	 *	@param Boolean $return_data 是否返回数据
	 *
	 */
	public function saveData ($newdo,$olddo,$return_data=false)
	{
		if(!$this->hasPk()) return false;
		if(empty($newdo)) return false;
		$save = false;
		if(!empty($newdo[$this->getPk()]))
		{
			$save = true;
		}
		$data = $this->dealData($newdo,$olddo);

		if($data)
		{
			foreach ($data as $key => $value)
			{
				$this->setValue($key,$value);
			}

			return  $save ? $this->save($return_data) : $this->create($return_data);
		}
		else
		{
			return $return_data ? $this->getDataObject() : $newdo[$this->getPk()];
		}
	}

	/**
	 *	fldExisted，字段是否存在
	 *
	 *	@param String $fldName 字段名
	 *	@param String $table 表名
	 *
	 */
	public function fldExisted ($fldName,$table='')
	{
		if(empty($fldName) || !is_string($fldName))
		{
			return false;
		}
		if($table=='')
		{
			$table=$this->table();
		}
		$sql = "SHOW COLUMNS FROM `".$table."` where Field='".$fldName."'";
		$row = $this->db()->getRow($sql);
		return $row ? true : false;
	}

	/**
	 *	insertAll，批量插入
	 *
	 *	@param Array $datas 数据
	 *	$datas = array(
			array('id'=>1,'username'=>'张三'),
			array('id'=>2,'username'=>'张四')
		)
	 *
	 *
	 */
	public function insertAll ($datas,$table='',$returnsql=false)
	{
		if(!is_array($datas[0]) || !$datas[0]) return false;
		$values  =  array();
		foreach ($datas as $data)
		{
			$value   =  array();
			foreach ($data as $key =>$val)
			{
				if(is_scalar($val))
				{
					//$value[]= is_string($val) ? $this->db()->db()->quote($val) : $val ; // 过滤非标量数据
					$value[]= $this->db()->db()->quote($val); // 过滤非标量数据
				}
				else if(gettype($val)==null)
				{
					$value[]='';
				}
				else
				{
					$value[]=0;
				}
			}
			if($value)
			{
				//$values[] = "('".implode("','",$value)."')";
				$values[] = "(".implode(",",$value).")";
			}
		}
		if($table=='')
		{
			$table = $this->table();
		}
        $sql = "INSERT INTO `".$table."` (`".implode('`,`',array_keys($datas[0]))."`) VALUES ".implode(',',$values);
         
                if($returnsql)
                {
                    return $sql;
                }
		return $this->db()->query($sql);
	}

	/**
	* makeBar,生成动态工具栏
	*/
	public function makeBar ($code,$start=0,$size=0,$hasPublic=true)
	{
		$code = strtoupper($code);
		$subsql = "SELECT id FROM `menu` WHERE `code`='{$code}' ";
		$subsql1 = "SELECT button_id FROM `rel_menu_button` WHERE `menu_id`=(".$subsql.") ";
		$sql = "SELECT b.* FROM (".$subsql1.") AS t LEFT JOIN `button` AS b ON t.button_id=b.id ORDER BY b.display_order DESC ";
		$mainsql = "SELECT main.*,bi.name AS icon_name,bc.classname AS class_name,bf.name AS function_name FROM (".$sql.") AS main LEFT JOIN `button_icon` AS bi ON main.icon_id=bi.id LEFT JOIN `button_class` AS bc ON main.class_id=bc.id LEFT JOIN `button_function` AS bf ON bf.id=main.function_id";
		$res =  $this->db()->getAll($mainsql);
		$toolBar = '';
		$len = count($res);
		foreach ($res as $key=>$val )
		{
			if($start)
			{
				if($key<=$start-1)
				{
					continue;
				}
			}
			if($size)
			{
				if($key>$start-1+$size)
				{
					if($hasPublic && $key<=$len-3)
					{

					}
					else
					{
						break;
					}
				}
			}
			if(!$hasPublic)
			{
				if($key>$len-3)
				{
					break;
				}
			}
			$toolBar .='<div class="btn-group">'.PHP_EOL;
			$toolBar .='<button class="btn btn-sm '.$val['class_name'].'" onclick="util.'.$val['function_name'].'(this'.($val['cust_function'] ? ','.$val['cust_function'] : '').');" data-url="'.$val['data_url'].'" name="'.$val['label'].'" title="'.$val['tips'].'" data-title="'.$val['data_title'].'">'.PHP_EOL;
			$toolBar .="\t".$val['label'];
			$toolBar .=' <i class="fa '.$val['icon_name'].'"></i>'.PHP_EOL;
			$toolBar .='</button>'.PHP_EOL.'</div>'.PHP_EOL;
		}
		return $toolBar;
	}

	/**
	*	makePermission,生成权限
	*/
	public function makePermission ($arr)
	{
		$sql = "SELECT id FROM `resource_type` WHERE `code`='".$arr['type']."' AND `is_deleted`=0 ";
		$type_id = $this->db()->getOne($sql);
		if(!$type_id)
		{
			return true;
		}
		$sql = "SELECT * FROM `permission` WHERE `type`='".$type_id."' AND `resource_id`=".$arr['id'];
		$do = $this->db()->getRow($sql);
		if($do)
		{
			$sql = "UPDATE `permission` SET `name`='".$arr['name']."'";
			if(!empty($arr['code']))
			{
				$sql .=",`code`='".$arr['code']."'";
			}
			$sql .=" WHERE `id`=".$do['id'];
		}
		else
		{
			if(empty($arr['code']))
			{
				$arr['code'] = $arr['type'].$arr['id'];
			}
			$sql = "INSERT INTO `permission` (`type`,`resource_id`,`name`,`code`) VALUES (".$type_id.",'".$arr['id']."','".$arr['name']."','".$arr['code']."') ";
		}
		try{
			$this->db()->query($sql);
		}
		catch(Exception $e){
			return false;
		}
		return true;
	}

	/**
	*	deletePermission,删除权限
	*/
	public function deletePermission ($arr)
	{
		$sql = "UPDATE `permission` SET `is_deleted`=1 WHERE `resource_id`=".$arr['id']." AND `type`=(SELECT id FROM `resource_type` WHERE `code`='".$arr['type']."' AND `is_deleted`=0)";
		$this->db()->query($sql);
	}

	/*
	*	recoverPermission,恢复权限
	*/
	public function recoverPermission ($arr)
	{
		$sql = "UPDATE `permission` SET `is_deleted`=0 WHERE `resource_id`=".$arr['id']." AND `type`=(SELECT `id` FROM `resource_type` WHERE `code`='".$arr['type']."' AND `is_deleted`=0)";
		$this->db()->query($sql);
	}

	/*
	*	checkElement,检查元素
	*/
	public function checkElement ($id,$type)
	{
		$sql = "SELECT `main_table` FROM `resource_type` WHERE `id`='{$type}'";
		$tbl = $this->db()->getOne($sql);
		$sql = "SELECT count(1) FROM `".$tbl."` WHERE `id`='{$id}' AND `is_deleted`=0";
		$res = $this->db()->getOne($sql);
		return $res;
	}
	/**
	 * 通过自定义where条件修改当前Model表数据任意字段
	 * @param unknown $data 字段值
	 * @param unknown $where where条件（拼接字符串）
	 * 使用案例：
	 * $data = array("field1"=>'XXXX',"field2"=>'xxxx',....);
	 * $where = "pk1='XXXXX' and pk2='XXXXX' and ...."
	 * $model->update($data,$where);
	 */
	public function update($data,$where){
	    //过滤主键id值
	    if($this->pk() && isset($data[$this->pk()])){
	        unset($data[$this->pk()]);
	    }
	    //通过系统底层函数拼接sql，然后替换掉死板的where条件
	    $sql = $this->updateSql($data);
	    if(preg_match('/ WHERE /is',$sql)){
	        $sql = preg_replace('/ WHERE .*/is',' WHERE '.$where, $sql);
	        return $this->db()->query($sql);
	    }else{
	        return false;
	    }
	}

}
?>