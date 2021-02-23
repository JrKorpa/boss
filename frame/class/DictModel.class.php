<?php
/**
 *  -------------------------------------------------
 *   @file		: DictModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-05 11:01:28
 *   @update	:
 *  -------------------------------------------------
 */
class DictModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'dict';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"主键",
"name"=>"属性",
"label"=>"标识",
"is_system"=>"是否内置",
"is_deleted"=>"是否删除");
		parent::__construct($id,$strConn);
	}

	public function hasName($name){
		$sql = "SELECT COUNT(`id`) FROM `dict` WHERE `name`='$name'";
		if($this->pk())
		{
			$sql .=" AND id<>".$this->pk();
		}
		return $this->db()->getOne($sql);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url DictController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{

		$sql = "SELECT * FROM `".$this->table()."` ";

		$str = '';
		if($where['label']!=='')
		{
			$str.="`label` like \"%".addslashes($where['label'])."%\" AND ";
		}

		if($where['name']!==''){
			$str.="`name` like \"%".addslashes($where['name'])."%\" AND ";
		}

		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}

		$sql .=" ORDER BY `id` DESC";

		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	/**
	*	getDict,缓存字典表
	*/
	private function getDict ($refresh=false)
	{
		$memcached_power = defined('DD_USE_MEMCACHE') && DD_USE_MEMCACHE;
		if($memcached_power)
		{
			$memcached = localCache::getInstance();
			if($refresh)
			{
				$memcached->delete("dict");
			}
			$list = unserialize($memcached->get("dict"));
		}
		else
		{
			$list = false;
		}
		if(!$list)
		{
			$sql = "SELECT `m`.`name`,`m`.`label`,`m`.`note`,`d`.`name` AS dict_name FROM `dict_item` AS m LEFT JOIN `dict` AS `d` ON `m`.`dict_id`=`d`.`id` WHERE `m`.`is_deleted`='0' AND `d`.`is_deleted`='0'";
			$data = $this->db()->getAll($sql);
			$list = array();
			foreach ($data as $key => $val )
			{
				$list[$val['dict_name']][] = $val;
			}
			if($memcached_power)
			{
				$memcached->set("dict",serialize($list),86400);
			}
		}

		return $list;
	}

	/**
	* getEnumArray，获取所有枚举值组成数组
	*/
	public function getEnumArray ($dict_name)
	{
		$list = $this->getDict();
		if(empty($list[$dict_name]))
		{
			$list = $this->getDict(true);
		}
		return $list[$dict_name];
	}

	/**
	* getEnum，获取枚举值
	*/
	public function getEnum ($dict_name,$enum_key)
	{
		$list = $this->getEnumArray($dict_name);
		$return = '';
		foreach ($list as $val )
		{
			if($val['name']==$enum_key)
			{
				$return = $val['label'];
				break;
			}
		}
		return $return;
	}
	
	public function getEnumByNote ($dict_name,$enum_note)
	{
	    $list = $this->getEnumArray($dict_name);
	    $return = '';
	    foreach ($list as $val )
	    {
	        if($val['note']==$enum_note)
	        {
	            $return = $val['label'];
	            break;
	        }
	    }
	    return $return;
	}

	/**
	 * 获取所有枚举值,js_table使用
	 * @author：yxt
	 */
	public function getEnums($dict_name){
		$list = $this->getEnumArray($dict_name);
		$data = array();
		foreach ($list as $k=>$v) {
			$data[$k+1] = $v['label'];
		}
		return $data;
	}
}

class DictItemModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'dict_item';
        $this->_dataObject = array("id"=>"明细主键",
"dict_id"=>"字典id",
"name"=>"枚举key",
"label"=>"枚举值",
"note"=>"描述",
"display_order"=>"顺序号",
"is_system"=>"系统内置",
"is_deleted"=>"删除标记");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url DictController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT `id`,`name`,`label`,`note`,`is_system`,`is_deleted` FROM `".$this->table()."` WHERE 1 ";
		if(!empty($where['_id']))
		{
			$sql .=" AND `dict_id`='".$where['_id']."'";
		}
		$sql .= " ORDER BY `display_order` DESC,`id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	public function getName ($id)
	{
		$sql = "SELECT IFNULL(max(name)+1,1) FROM `".$this->table()."` WHERE `dict_id`='{$id}'";

		return $this->db()->getOne($sql);
	}

	public function has($label,$dict_id){
		$sql = "SELECT COUNT(`id`) FROM `dict_item` WHERE `dict_id`=".$dict_id." AND `label`='$label'";
		if($this->pk())
		{
			$sql .=" AND id<>".$this->pk();
		}
		return $this->db()->getOne($sql);
	}
}

?>