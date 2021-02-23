<?php
/**
 *  -------------------------------------------------
 *   @file		: SalesChannelsPersonModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-05-27 16:57:44
 *   @update	:
 *  -------------------------------------------------
 */
class SalesChannelsPersonModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'sales_channels_person';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"序号",
"dp_leader"=>"店长ID",
"dp_leader_name"=>"店长名称",
"dp_people"=>"销售顾问ID",
"dp_people_name"=>"销售顾问名称");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url SalesChannelsPersonController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
//		if($where['xxx'] != "")
//		{
//			$str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
//		}
//		if(!empty($where['xx']))
//		{
//			$str .= "`xx`='".$where['xx']."' AND ";
//		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
    
    /**
     * 获取所属渠道的销售人员
     * @param type $where
     * @return type
     */
    function getInfoList($where) {
        $sql = "SELECT * FROM `".$this->table()."`";
        $str = '';
		if($where['order_department'])
		{
			$str .= "`id`={$where['order_department']} AND ";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
        return $this->db()->getRow($sql);
    }
    
    /**
     * 获取所有id
     * @return type
     */
    function getAllInfo(){
        $sql = "SELECT `id` FROM `{$this->table()}`";
        return $this->db()->getAll($sql);
    }
    
    /**
     * 获取第一条id
     * @return type
     */
    function getOneInfo(){
        $sql = "SELECT `id` FROM `{$this->table()}` LIMIT 1";
        return $this->db()->getOne($sql);
    }

	function addChannelPerson($data) {
		if (empty($data)) return false;

		$fields = array_keys($data);
		$valuedata = array_values($data);
		$field = implode('`,`', $fields);
		$value = str_repeat('?,',count($fields)-1).'?';

		$sql = "INSERT INTO `".$this->table()."` (`" . $field . "`) VALUES (". $value .")";
		print_r($valuedata);
		$this->db()->query($sql, $valuedata);
		return $this->db()->insertId();
	}
    
}

?>