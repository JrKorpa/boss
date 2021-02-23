<?php
/**
 *  -------------------------------------------------
 *   @file		: ExtraPushCoefficientModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-09-29 11:04:34
 *   @update	:
 *  -------------------------------------------------
 */
class ExtraPushCoefficientModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'extra_push_coefficient';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"dep_id"=>"体验店ID",
"dep_name"=>"体验店名称",
"station"=>"岗位",
"bonus_gears"=>"差额奖金档位",
"add_performance_standard"=>"新增业绩标准（万）",
"excess_price"=>"超额奖金（元）",
"push_money_coefficient"=>"档位提成系数");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url ExtraPushCoefficientController/search
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
		if(!empty($where['dep_id']))
		{
			$str .= "`dep_id`='".$where['dep_id']."' AND ";
		}
        if(!empty($where['station']))
        {
            $str .= "`station`='".$where['station']."' AND ";
        }
        if(!empty($where['bonus_gears']))
        {
            $str .= "`bonus_gears`='".$where['bonus_gears']."' AND ";
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
}

?>