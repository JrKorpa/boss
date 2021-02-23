<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductFqcConfModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-03 17:24:09
 *   @update	:
 *  -------------------------------------------------
 */
class ProductFqcConfModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'product_fqc_conf';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"主键id",
"cat_name"=>"分类名字",
"parent_id"=>"分类父id",
"tree_path"=>"全路径",
"childrens"=>"下级分类",
"pids"     =>"祖先分类",
"display_order"=>"显示顺序",
"is_deleted"=>"删除标识");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url ProductFqcConfController/search
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
		$sql .= " ORDER BY `parent_id` ASC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
        //获取一级分类
	public function get_top_menu ($all=true)
	{
		$sql = "SELECT `id`,`cat_name`,`parent_id` FROM `".$this->table()."` WHERE is_deleted=0 and `parent_id`= '0'";
		return $this->db()->getAll($sql,array(),false);
	}

	//获取对应的二级分类
	public function get_second_menu ($where)
	{

		$sql = "SELECT `id`,`cat_name`,`parent_id` FROM `".$this->table()."` WHERE is_deleted=0 and `parent_id`= {$where}";
		return $this->db()->getAll($sql,array(),false);
	}
        //获取
        public function get_order_tree ($all=true)
	{
		$sql = "SELECT `id`,`cat_name`,`parent_id`,concat(`tree_path`,'-',`id`) AS `abspath` FROM `".$this->table()."` WHERE `is_deleted`= '0'";
		if(!$all && $this->pk())
		{
			$sql .= " AND `tree_path` NOT LIKE \"".$this->getValue("tree_path")."-".$this->pk()."%\" AND `id`<>'".$this->pk()."'";
		}
		$sql .=" ORDER BY `abspath` ASC,`display_order` DESC";
		return $this->db()->getAll($sql,array(),false);
	}
        //获取并处理树形列表
        public function get_list ()
	{
		$sql = "SELECT `id`,`cat_name`,`parent_id`,`tree_path` FROM `".$this->table()."` WHERE `is_deleted`='0' ORDER BY `display_order` ASC";
		$res = $this->db()->getAll($sql);
		$keys = array_column($res,'id');
		$res = array_combine($keys,$res);
		$data = array();
		foreach ($res as $val )
		{
			if(isset($res[$val['parent_id']]))
			{
				$res[$val['parent_id']]['son'][] = &$res[$val['id']];
			}
			else
			{
				$data[] = &$res[$val['id']];
			}
		}
		$list = array();
		$this->flatArray($data,$list);
		return $list;
	}

        /*
	*	将多维数组转化为二维数组
	*/
	function flatArray($arr,&$return)
	{
		foreach ($arr as $key => $val )
		{
			$val['level'] = count(explode('-',$val['tree_path']));
			$val['tree_name'] = str_repeat('&nbsp;',2*($val['level']-1)).$val['cat_name'];
			$return[] = $val;
			if(isset($val['son']))
			{
				$this->flatArray($val['son'],$return);
			}
		}
		return $return;
	}

        public function saveDatas ($newdo,$olddo)
	{
		$save = false;
		$sqls=array();
		if(!empty($newdo[$this->getPk()]))
		{
			$save = true;
		}
		$data = $this->dealData($newdo,$olddo);
		if($save)
		{
			if($olddo['pids'])
			{
				$sqls[] = "UPDATE `".$this->table()."` SET `childrens`=`childrens`-1 WHERE `id` IN (".$olddo['pids'].")";//向上汇总
			}
			$sqls[] = $this->updateSql($data);
		}
		else
		{
			$sqls[] = $this->insertSql($data);
		}
		if($newdo['pids'])
		{
			$sqls[] = "UPDATE `".$this->table()."` SET `childrens`=`childrens`+1 WHERE `id` IN (".$newdo['pids'].")";//向上汇总
		}
		return $this->db()->commit($sqls);
	}
        //同级不能重复添加分类
        function if_has_catname($parent_id,$cat_name,$id=''){
            $sql = "SELECT COUNT(1) FROM `".$this->table()."` WHERE is_deleted=0 and `parent_id` = '{$parent_id}' and cat_name='".$cat_name."' and id!='{$id}'";

		return  $this->db()->getOne($sql,array(),false);
        }
        //是否有子类
        function if_has_chidrens($id,$pid){
            if (empty($pid)) {
                $sql = "select count(1) from `".$this->table()."` where is_deleted=0 and pids='".$id."'";
            }else {
                $sql = "select count(1) from `".$this->table()."` where is_deleted=0 and pids like '%$id%'";
            }
            return  $this->db()->getOne($sql,array(),false);
        }
}

?>