<?php
/**
 *  -------------------------------------------------
 *   @file		: AppProcessorProcessModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-19 21:46:16
 *   @update	:
 *  -------------------------------------------------
 */
class AppProcessorProcessModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_processor_process';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"序号",
"process_name"=>"流程名称",
"business_type"=>"经营类型",
"business_scope"=>"经营范围",
"department_id"=>"申请部门id",
"is_enabled"=>"是否启用",
"is_deleted"=>"删除标识",
"create_user_id"=>"创建人id",
"create_user"=>"创建人",
"create_time"=>"创建时间");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppProcessorProcessController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE `is_deleted` = 0";
		if(isset($where['business_type'])){
			$sql .= " AND `business_type` ='".$where['business_type']."'";
		}
		if(isset($where['department_id']) && $where['department_id'] != ""){
			$sql .= " AND `department_id` ='".$where['department_id']."'";
		}
		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	/**
	 * getUsers	获取用户
	 * 审批组还未筛选
	 */
	public function getUsers(){
//		$sql = "SELECT `id`,`account`,`real_name` FROM `user` WHERE `is_deleted` = 0 AND `is_on_work` = '1'";
//		$users = DB::cn(1)->getAll($sql);
//		return $users;
		$u_model = new UserModel(1);
		$checkUsers = $u_model->getGroupCheckUser(1);//供应商审核组
		return $checkUsers;
	}

	/**
	 * 获取审核人员
	 * @param $id
	 * @return mixed
	 */
	public function getApproveUsers($id){
		$sql = 'SELECT `user_order`,`user_id` FROM `app_processor_user` WHERE `process_id` = '.$id;
		$sql .= " ORDER BY user_order";
		//echo $sql;
		$res = $this->db()->getAll($sql);
		//print_r($res);
		foreach ($res as $k => $v) {
			$sql = 'SELECT `real_name` FROM `user` WHERE `is_deleted` = 0 AND `id` = '.$v['user_id'];
			$real_name = DB::cn(1)->getOne($sql);
			$res[$k]['real_name'] = $real_name;
		}
		//print_r($res);exit;
		return $res;
	}

	/**
	 * 经营范围名称
	 * @param string $business_scope
	 * @return string
	 */
	public function mkBusiness($business_scope){
		if(strstr($business_scope,'1') === false){
			$business = '[非成品钻]';
		}elseif(strlen($business_scope) == 1){
			$business = '[成品钻]';
		}else{
			$business = '[成品钻/非成品钻]';
		}
		return $business;
	}


	/**
	 * 保存流程审核人
	 */
	public function saveProUser($users,$process_id){
		if(count($users)==0){
			return false;
		}
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			foreach ($users as $k=>$v) {
				$i = $k+1;
				$sql = "INSERT INTO `app_processor_user` (`process_id`,`user_id`,`user_order`) VALUES ($process_id,$v,$i)";
				$this->db()->query($sql);
			}
		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;

	}





}

?>