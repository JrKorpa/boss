<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseHunbohuiInfoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-05-10 11:06:56
 *   @update	:
 *  -------------------------------------------------
 */
class BaseHunbohuiInfoModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'base_hunbohui_info';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"department"=>"店ID",
"name"=>"婚博会名字",
"from_ad"=>"来源",
"warehouse"=>"库房",
"start_time"=>"允许录单开始时间",
"end_time"=>"允许录单结束时间",
"active_start_time"=>"活动举行开始时间",
"active_end_time"=>"活动举行结束时间",
"user_name"=>"操作人",
"manager"=>"负责人",
"is_delete"=>"删除");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url BaseHunbohuiInfoController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT `bhi`.* FROM `".$this->table()."` AS `bhi`,`customer_sources` AS `cs` WHERE `bhi`.`from_ad`=`cs`.`source_code`";
		if($where['name'] != "")
		{
			$sql .= " AND `bhi`.`name` like \"%".$where['name']."%\"";
		}
		if($where['department'] != "")
		{
			$sql .= " AND `bhi`.`department` = ".$where['department']."";
		}
		if(isset($where['from_ad']) && $where['from_ad'] != "")
		{
			$sql .= " AND `bhi`.`from_ad` = '".$where['from_ad']."'";
		}
		if(!empty($where['start_time'])){
            $sql.=" AND `bhi`.`start_time` >= '".$where['start_time']." 00:00:00'";
        }
        if(!empty($where['end_time'])){
            $sql.=" AND `bhi`.`end_time` <= '".$where['end_time']." 23:59:59'";
        }
		if(!empty($where['active_start_time'])){
            $sql.=" AND `bhi`.`active_start_time` >= '".$where['active_start_time']." 00:00:00'";
        }
        if(!empty($where['active_end_time'])){
            $sql.=" AND `bhi`.`active_end_time` <= '".$where['active_end_time']." 23:59:59'";
        }
		$sql .= " ORDER BY `bhi`.`id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
    
    /**
     * 获取婚博会的销售渠道
     * @param type $department
     * @return type
     */
    function getDepartmentInfo($department) {
        $where = '';
        if($department){
            $where = " AND `hi`.`department` IN ($department)";
        }
        $sql = "SELECT `sc`.`id`,`sc`.`channel_name` FROM `{$this->table()}` AS `hi`,`sales_channels` AS `sc` WHERE `hi`.`department`=`sc`.`id`";
        return $this->db()->getAll($sql);
    }
    
    /**
     * 获取婚博会的销售渠道
     * @param type $department
     * @return type
     */
    function getDepartmentInfoqc($department) {
        $where = '';
        if($department){
            $where = " AND `hi`.`department` IN ($department)";
        }
        $sql = "SELECT DISTINCT(`sc`.`id`),`sc`.`channel_name` FROM `{$this->table()}` AS `hi`,`sales_channels` AS `sc` WHERE `hi`.`department`=`sc`.`id`";
        return $this->db()->getAll($sql);
    }
    
    /**
     * 获取某个销售渠道的婚博会信息
     * @param type $department
     * @return array
     */
    function getHbhInfoList($department) {
        $data = array();
        if(empty($department)){
            return $data;
        }
        $sql = "SELECT * FROM `{$this->table()}` WHERE `department`=$department";
        return $this->db()->getAll($sql);
    }
    
    
    function getHbhInfo($id) {
        $data = array();
        if(empty($id)){
            return $data;
        }
        $sql = "SELECT * FROM `{$this->table()}` WHERE `id`=$id";
        return $this->db()->getRow($sql);
    }
    
}

?>