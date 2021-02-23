<?php
/**
 *  -------------------------------------------------
 *   @file		: FactoryOpraDictModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ruir
 *   @date		: 2015-04-13 11:55:49
 *   @update	:
 *  -------------------------------------------------
 */
class FactoryOpraDictModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'factory_opra_dict';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"主键",
"name"=>"名称",
"create_emp_time"=>"创建时间",
"edit_time"=>"修改时间");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url FactoryOpraDictController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT main.id,main.name,main.create_time,main.edit_time,main.status FROM `".$this->table()."` as main";
		$str = '';
		 if(!empty($where['start_time']) and empty($where['end_time']))
         {
            $str .= " main.`create_time`>='".$where['start_time']."' AND ";
         }
         elseif(empty($where['start_time']) and !empty($where['end_time']))
         {
            $str .= " main.`create_time`<'".$where['end_time']."' AND ";
         }
         elseif(!empty($where['start_time']) and !empty($where['end_time']))
         {
            $str .= " (main.`create_time` BETWEEN '".$where['start_time']."' AND '".$where['end_time']."') AND ";
         }
		 if(!empty($where['edit_start_time']) and empty($where['edit_end_time']))
         {
            $str .= " main.`edit_time`>='".$where['edit_start_time']."' AND ";
         }
         elseif(empty($where['edit_start_time']) and !empty($where['edit_end_time']))
         {
            $str .= " main.`edit_time`<'".$where['edit_end_time']."' AND ";
         }
         elseif(!empty($where['edit_start_time']) and !empty($where['edit_end_time']))
         {
            $str .= " (main.`edit_time` BETWEEN '".$where['edit_start_time']."' AND '".$where['edit_end_time']."') AND ";
         }
		if($where['name'] != "")
		{
			$str .= " main.`name` like \"".addslashes($where['name'])."%\" AND ";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY main.`display_order`asc";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	/** 排序 **/
	public function move ($id,$up=true)
	{
		$do = $this->getDataObject();
		if(!$do)
		{
			return false;
		}
		if($up)
		{
			$sql = "SELECT `id`,`display_order` FROM `".$this->table()."` WHERE `display_order`<".$do['display_order']." ORDER BY `display_order` DESC LIMIT 1";
		}
		else
		{
			$sql = "SELECT `id`,`display_order` FROM `".$this->table()."` WHERE `display_order`>".$do['display_order']." ORDER BY `display_order` ASC LIMIT 1";
		}
		$destdo = $this->db()->getRow($sql);
		if(!$destdo)
		{
			return 3;
		}

		$sql = "UPDATE `".$this->table()."` SET `display_order`=".$do['display_order']." WHERE `id`=".$destdo['id']." ";
		$res = $this->db()->query($sql);
		if(!$res)
		{
			return false;
		}
		$sql = "UPDATE `".$this->table()."` SET `display_order`=".$destdo['display_order']." WHERE `id`=".$id." ";
		$res = $this->db()->query($sql);
		if($res)
		{
			return 1;
		}
		else
		{
			return false;
		}
	}

	/**
	* 新增 新增的同时，往数字字典推送数据
	*/
	public function addInfo($data){
		$dd = new DictModel(1);
		$dditem = new DictItemModel(1);
		$dic = $dd->getEnumArray('buchan_fac_opra');
		$info = $dd->pageList ($where = array('name'=>'buchan_fac_opra' , 'label'=>'') ,1,$pageSize=1,$useCache=false);

		$last = 0;	//获取数字字典值得最大的枚举值
		$dict_id = $info['data'][0]['id'];	//获取数字字典id
		foreach($dic as $val){
			if($val['name'] > $last){
				$last = $val['name'];
			}
			//判断推送的数据是否在数字字典是否存在相同的值
			if($val['label'] === $data['name']){
				return array('success' => 0 , 'error'=>"数字字典‘buchan_fac_opra’ 已经存在相同的值‘{$data['name']}’，不允许新增");
			}
		}

		$newdo = array(
			'dict_id'=>$dict_id,
			'name'=>$last + 1,
			'label'=>$data['name'],
			'display_order'=>time(),
			'note'=>'工厂操作维护推送过来的数据',
		);
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//往数字字典推送数据
			$dict_item_id = $dditem->saveData($newdo , array());
			if($dict_item_id !== true)
			{
				return array('success'=> 0 , 'error' => '推送数据到数字字典失败');
			}

			$sql = "INSERT INTO `factory_opra_dict` (`name` , `create_time` , `edit_time` , `display_order` , `dict_id` , `dict_value`) VALUES ( '{$data['name']}' , '{$data['create_time']}' , '{$data['edit_time']}' , '{$data['display_order']}' , {$dict_item_id} , {$newdo['name']})";
			$pdo->query($sql);
		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success'=> 0 , 'error' => '事物执行不成功，操作失败');
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return array('success'=> 1 , 'error' => '操作成功');
	}

	//启用，禁用（同时推送数据到数字字典）
	public function updatestatus($id , $status){
		$sql = "SELECT `dict_id` FROM `factory_opra_dict` WHERE `id` = {$id}";
		$dict_id = $this->db()->getOne($sql);
		$dditem = new DictItemModel( $dict_id , 1);
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			$sql = "UPDATE `factory_opra_dict` SET `status` = {$status} WHERE `id` = {$id}";
			$pdo->query($sql);

			//推送数据到数字字典
			$status = ($status == 1) ? 0 : 1;
			$dditem->setValue('is_deleted' , $status);
			$res = $dditem->save(true);
			if(!$res){
				$pdo->query('');		//制造错误回滚
			}
		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success'=> 0 , 'error' => '事物执行不成功，操作失败');
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return array('success'=> 1 , 'error' => '操作成功');
	}

	/**
	* 普通查询
	*/
	public function GetInfo($fields = '*' , $where = '1 LIMIT 1', $type = 'one'){
		$sql = "SELECT {$fields} FROM `factory_opra_dict` WHERE {$where} ORDER BY `display_order` DESC";
		if($type == 'one'){
			$res = $this->db()->getOne($sql);
		}else if($type == 'row'){
			$res = $this->db()->getRow($sql);
		}else if($type == 'all'){
			$res = $this->db()->getAll($sql);
		}
		return $res;
	}

	/**
	* 比较两个工厂操作的排序
	* 第一个参数(排序) > 第二个参数(排序) 返回 true , 否则返回false
	* @param $dict_value1 $dict_value2
	*/
	public function checkSort($dict_value1 , $dict_value2){
		$sql = "SELECT `display_order` FROM `factory_opra_dict` WHERE `dict_value` = $dict_value1";
		$data1 = $this->db()->getOne($sql);
		if(!$data1){
			return false;	//如果是等钻/工厂出货，则返回false , 不必填写备注
		}
		$sql = "SELECT `display_order` FROM `factory_opra_dict` WHERE `dict_value` = $dict_value2";
		$data2 = $this->db()->getOne($sql);
		if(!$data2){
			return false;	//如果是第一次
		}
		if($data1 > $data2){
			return true;
		}else{
			return false;
		}
	}

}?>