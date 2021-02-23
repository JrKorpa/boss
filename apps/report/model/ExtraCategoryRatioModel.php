<?php
/**
 *  -------------------------------------------------
 *   @file		: ExtraCategoryRatioModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-09-29 11:26:14
 *   @update	:
 *  -------------------------------------------------
 */
class ExtraCategoryRatioModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'extra_category_ratio';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"ID",
"dep_id"=>"体验店ID",
"dep_name"=>"体验店名称",
"goods_type"=>"商品类型",
"discount"=>"折扣",
"pull_ratio_a"=>"提成比例A（裸钻小于0.5克拉）",
"pull_ratio_b"=>"提成比例B（裸钻0.5克拉（含0.5克拉）-1克拉）",
"pull_ratio_c"=>"提成比例C（裸钻1克拉（含1克拉）-1.5克拉）",
"pull_ratio_d"=>"提成比例D（裸钻1.5克拉及以上）");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url ExtraCategoryRatioController/search
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
        if(!empty($where['goods_type']))
        {
            $str .= "`goods_type`='".$where['goods_type']."' AND ";
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
        //echo $sql;die;
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