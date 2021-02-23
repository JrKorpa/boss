<?php
/**
 *  -------------------------------------------------
 *   @file		: LargeAreaModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-29 11:50:43
 *   @update	:
 *  -------------------------------------------------
 */
class LargeAreaModel extends Model
{

	protected $pagedata=array();
	protected $newdata=array();

	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'large_area';
        $this->_dataObject = array('id'=>'主键',
'name'=>'名称',
'parent_id'=>'PID',
'create_user'=>'创建人',
'create_time'=>'创建时间',
"tree_path"=>"全路径",
"pids"=>"上级分类",
"childrens"=>"下级分类数",
'is_enable'=>'是否有效 0有效1无效');
		parent::__construct($id,$strConn);
	}

	//事务提交遇到修改和添加的时候向所有的上级通知childrens +1 和 -1的规则（这里的save方法只有修改的时候会触发父级减一的情况）
	public function saveDatas($newdo,$olddo){
		$save = false;//新增和修改的标记  修改为true
		$sqls = array();//为db的提交函数提供数组sql
		if(!empty($newdo[$this->getPk()])){
			$save = true;
		}
		$data = $this->dealData($newdo,$olddo);
		if($save){
			//如果是修改  那就所有的上级都有增减的可能这里$olddo['pids']一旦这条数据有设置了pids 则所有的上级全部减一
			if($olddo['pids']){
				$sqls[] = "UPDATE `".$this->table()."` SET `childrens`=`childrens`-1 WHERE `id` in (".$olddo['pids']
					.")";
			}
			//如果老数据没有pids的值则直接修改其他数据 不需要通知上级

			$sqls[] = $this->updateSql($data);
		}else{
			//如果你是新增如果有pids则通知所有的上级chiderns全部+1
			$sqls[]=$this->insertSql($data);
		}

		if($newdo['pids']){
				$sqls[]="UPDATE `".$this->table()."` SET  `childrens`=`childrens` +1 WHERE `id` in (".$newdo['pids']
					.")";
		}
/*		var_dump($sqls);
		exit;*/


		return $this->db()->commit($sqls);

	}

	public function getAreaTree()
	{
		$sql = "SELECT * FROM large_area";

		return $this->db()->getAll($sql);

	}

	public function getList ()
	{
		$sql = "SELECT m.*,ifnull(p.name,'这是顶级') AS parent_name FROM `".$this->table()."` AS m LEFT JOIN `".$this->table()."` AS p ON m.parent_id=p.id ORDER BY id ASC";
		$res = $this->db()->getAll($sql);
		//把所有数组的$res id生成新的数组
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
			$val['tree_name'] = str_repeat('&nbsp;',2*($val['level']-1)).$val['name'];
			$return[] = $val;
			if(isset($val['son']))
			{
				$this->flatArray($val['son'],$return);
			}
		}
		return $return;
	}
}

?>