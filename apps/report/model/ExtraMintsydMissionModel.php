<?php
/**
 *  -------------------------------------------------
 *   @file		: ExtraMintsydMissionModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-09-29 11:14:39
 *   @update	:
 *  -------------------------------------------------
 */
class ExtraMintsydMissionModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'extra_mintsyd_mission';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"ID",
"dep_id"=>"销售渠道ID",
"dep_name"=>"体验店渠道名称",
"sale_name"=>"销售顾问",
"minimum_price"=>"保底金额（单位：万元）",
"tsyd_mission"=>"天生一对任务（单位：个）");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url ExtraMintsydMissionController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
		if($where['sale_name'] != "")
		{
			$str .= "`sale_name` like \"%".addslashes($where['sale_name'])."%\" AND ";
		}
		if(!empty($where['dep_id']))
		{
			$str .= "`dep_id`='".$where['dep_id']."' AND ";
		}
        if(!empty($where['search_date']))
        {
            $str .= "`task_date`='".$where['search_date']."' AND ";
        }
        if(SYS_SCOPE == 'zhanting'){
            $str .= " `task_date` >= '2019-01-01 00:00:00' AND ";
        }
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
     * 普通查询
     * @param $type one 查询单个字段， row查询一条记录 all 查询多条记录
     */
    public function select2($fields = ' * ' , $where = " 1 " , $type = 'one'){
        $sql = "SELECT {$fields} FROM `".$this->table()."` WHERE {$where}";
        if($type == 'one'){
            $res = $this->db()->getOne($sql);
        }else if($type == 'row'){
            $res = $this->db()->getRow($sql);
        }else if($type == 'all'){
            $res = $this->db()->getAll($sql);
        }
        return $res;
    }

    public function getmintsydMissionList($where)
    {
        if(empty($where)) return array();
        
        $sql ="SELECT
    dep_id,
dep_name,
tsyd_mission,
    sale_name,
    max(task_date) as task_date,
    (
        SELECT
            t2.id
        FROM
            `extra_mintsyd_mission` t2
        WHERE
            t2.sale_name = t1.sale_name
            and t2.dep_id = t1.dep_id
        AND t2.task_date = max(t1.task_date)
    ) AS id,
    (
        SELECT
            t2.minimum_price
        FROM
            `extra_mintsyd_mission` t2
        WHERE
            t2.sale_name = t1.sale_name
            and t2.dep_id = t1.dep_id
        AND t2.task_date = max(t1.task_date)
    ) AS minimum_price
FROM
    `extra_mintsyd_mission` t1
WHERE
    `dep_id` = '".$where['company_id']."'
AND `task_date` <= '".$where['task_date']."'
GROUP BY
    sale_name";
    $data = $this->db()->getAll($sql);
    return $data;
    }
}

?>