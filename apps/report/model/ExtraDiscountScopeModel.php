<?php
/**
 *  -------------------------------------------------
 *   @file		: ExtraDiscountScopeModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-09-29 11:22:42
 *   @update	:
 *  -------------------------------------------------
 */
class ExtraDiscountScopeModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'extra_discount_scope';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"ID",
"dep_id"=>"体验店id",
"dep_name"=>"体验店名称",
"style_sn_id"=>"款式渠道来源id",
"style_channel"=>"款式渠道来源名称",
"goods_type"=>"商品类型",
"discount_upper"=>"折扣上限",
"discount_floor"=>"折扣下限",
"push_money"=>"提成",
"priority"=>"优先级");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url ExtraDiscountScopeController/search
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
        if(!empty($where['style_channel']))
        {
            $str .= "`style_channel_id`='".$where['style_channel']."' AND ";
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