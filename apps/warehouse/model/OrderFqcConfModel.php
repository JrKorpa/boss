<?php
/**
 *  -------------------------------------------------
 *   @file		: OrderFqcConfModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-02 12:12:53
 *   @update	:
 *  -------------------------------------------------
 */
class OrderFqcConfModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'order_fqc_conf';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"主键",
"cat_name"=>"分类名",
"parent_id"=>"父id");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url OrderFqcConfController/search
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
			$sql .=" WHERE `is_deleted`='0' AND".$str;
		}
		$sql .= "WHERE `is_deleted`='0' ORDER BY `id` ASC";
		$res = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		
		$sql_a = "SELECT * FROM `".$this->table()."` WHERE `is_deleted`=0 and `parent_id`='0'";
		$res_a = $this->db()->getAll($sql_a);
		//重组数组  把一类的放在一起
		$data=array();
		foreach ($res_a  as  $key=>$val )
		{
			if($val['id']){
				$data[]=$val;
				foreach($res['data'] as $v){
					if($v['parent_id']==$val['id']){
						$data[]=$v;
					}					
				}				   	
			}
		
			
		}	
		return $data;
	}
	//获取一级分类
	public function get_top_menu ($all=true)
	{
		$sql = "SELECT `id`,`cat_name`,`parent_id` FROM `".$this->table()."` WHERE `parent_id`= '0' and `is_deleted`=0";
		return $this->db()->getAll($sql,array(),false);
	}
	
	//获取对应的二级分类
	public function get_second_menu ($where)
	{
		$sql = "SELECT `id`,`cat_name`,`parent_id` FROM `".$this->table()."` WHERE `parent_id`= {$where}";
		return $this->db()->getAll($sql,array(),false);
	}
    //是否有子类
    public function if_has_childrens($id,$pid,$cat_name=''){
    	if ($id != "" ){
    		$sql = "select count(1) from `".$this->table()."` where  is_deleted=0 and parent_id='".$id."'";
    	}else {
    		$sql = "select count(1) from `".$this->table()."` where cat_name='".$cat_name."' and is_deleted=0 and parent_id='".$pid."'";
    	}
    	
        return $this->db()->getOne($sql);
    }
    /**
	* 普通查询
	* @param $fields string 要查询的字段
	* @param $where string 要查询的条件
	* @param $is_all Int 1取单个值 2/取一条记录 3/多条记录
	**/
	public function select2($fields, $where , $is_all = 'one'){
		$sql = "SELECT {$fields} FROM `{$this->table()}` WHERE {$where} ORDER BY `id` DESC";
		if($is_all == 'one'){
			return $this->db()->getOne($sql);
		}else if($is_all == 'row'){
			return $this->db()->getRow($sql);
		}else if($is_all == 'all'){
			return $this->db()->getAll($sql);
		}
	}
}

?>