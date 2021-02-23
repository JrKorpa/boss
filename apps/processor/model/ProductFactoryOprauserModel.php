<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductFactoryOprauserModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-21 11:25:32
 *   @update	:
 *  -------------------------------------------------
 */
class ProductFactoryOprauserModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'product_factory_oprauser';
		$this->pk='id';
		$this->_prefix='';
		$this->_dataObject = array("id"=>" ",
		"prc_id"=>"工厂id",
		"opra_user_id"=>"跟单人ID",
		"opra_uname"=>"跟单人姓名",
		"add_user"=>"添加人",
		"add_time"=>"添加时间");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url ProductFactoryOprauserController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
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
	*/
	public function select2($fields = '*' , $where = ' 1 LIMIT 1 ' , $type = 'one'){
		$sql = "SELECT {$fields} FROM `product_factory_oprauser` WHERE {$where}";
		if($type == 'one'){
			return $this->db()->getOne($sql);
		}else if($type == 'row'){
			return $this->db()->getRow($sql);
		}else if($type == 'all'){
			return $this->db()->getAll($sql);
		}
	}

	/** 绑定跟单人到工厂上 **/
	public function BingGendanMan($pro_id , $user_id,$production_manager_id){
		$time = date('Y-m-d H:i:s');
		$user = $_SESSION['userName'];
		$userModel = new UserModel(2);
		$bing_user_name = $userModel->getAccount($user_id);
		$production_manager_name = $userModel->getAccount($production_manager_id);
		//判断当前工厂是否已经绑定了跟单人，已经存在则修改，不存在则新增
		$exists = $this->select2('`id`' , "`prc_id`={$pro_id}" , $type = 'one');
		if($exists !== false){
			$sql = "UPDATE `product_factory_oprauser` SET `opra_user_id` = {$user_id} , `opra_uname`= '{$bing_user_name}',`production_manager_id`=$production_manager_id,`production_manager_name`='{$production_manager_name}' WHERE `prc_id` = {$pro_id}";
		}else{
			//写入主表
			$sql = "INSERT INTO `product_factory_oprauser` (`prc_id`,`opra_user_id`,`opra_uname`,`production_manager_id`,`production_manager_name`,`add_user`,`add_time`) VALUES ({$pro_id} , {$user_id} , '{$bing_user_name}',{$production_manager_id} , '{$production_manager_name}' , '{$user}' , '{$time}')";
		}
		$res = $this->db()->query($sql);
		return $res;
	}
}?>