<?php
/**
 *  -------------------------------------------------
 *   @file		: AppProcessorProcessController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-19 21:46:16
 *   @update	:
 *  -------------------------------------------------
 */
class AppProcessorProcessController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('app_processor_process_search_form.html',array(
			'bar'=>Auth::getBar(),'depart'=>new DepartmentView(new DepartmentModel(1)),
		));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			//'参数' = _Request::get("参数");
			'business_type'=>_Request::getString('business_type'),
			'department_id'=>_Request::getInt('department_id')

		);
		$page = _Request::getInt("page",1);
		$where = array();
		if($args['business_type'] != ""){$where['business_type'] = $args['business_type'];};
		if($args['department_id'] != ""){$where['department_id'] = $args['department_id'];};

		$model = new AppProcessorProcessModel(13);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_processor_process_search_page';
		$this->render('app_processor_process_search_list.html',array(
			'view'=>new AppProcessorProcessView(new AppProcessorProcessModel(13)),
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{

		$model = new AppProcessorProcessModel(13);
		$users = $model->getUsers();
		$recordV = new AppProcessorRecordView(new AppProcessorRecordModel(13));
		$scope = $recordV->getScopeList();//经营范围

		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_processor_process_info.html',array(
			'view'=>new AppProcessorProcessView($model),
			'depart'=>new DepartmentView(new DepartmentModel(1)),
			'users'=>$users,'scope'=>$scope
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);

		$model = new AppProcessorProcessModel($id,13);
		$users = $model->getUsers();
		$recordV = new AppProcessorRecordView(new AppProcessorRecordModel(13));
		$scope = $recordV->getScopeList();//经营范围

		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_processor_process_info.html',array(
			'view'=>new AppProcessorProcessView($model),
			'depart'=>new DepartmentView(new DepartmentModel(1)),
			'users'=>$users,'scope'=>$scope
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');

		$scope = _Post::getList('scope');//经营范围array
		$depart_id = _Post::getInt('department_id');//部门ID
		$user = _Post::getList('user');//审核人array
		if(empty($scope)){
			$result['error'] = '请选择经营范围!!!';
			Util::jsonExit($result);
		}
		if(empty($depart_id)){
			$result['error'] = '请选择申请部门!!!';
			Util::jsonExit($result);
		}
		if(count($user)==0){
			$result['error'] = '请选择审批人!!!';
			Util::jsonExit($result);
		}
		$newmodel =  new AppProcessorProcessModel(14);
		$view = new AppProcessorProcessView($newmodel);
		$depart_name = $view->get_department_name($depart_id);

		$process_name = $depart_name.'=>';
		$business_scope = implode(',',$scope);
		$business_type = $newmodel->mkBusiness($business_scope);
		$process_name = $process_name.$business_type.'=>['.date('Y-m-d',time()).']';//流程名称

		$olddo = array();
		$newdo=array(
			'process_name'=>$process_name,		//流程名称
			'business_type'=>$business_type,	//经营范围[中文]
			'business_scope'=>$business_scope,	//经营范围[数字]
			'department_id'=>$depart_id,		//部门ID
			'is_enabled'=>1,					//新增默认启用
			'create_user_id'=>$_SESSION['userId'],
			'create_user'=>$_SESSION['userName'],
			'create_time'=>date('Y-m-d',time())
		);

		$sql = "SELECT count(*) FROM `app_processor_process` WHERE `process_name` ='".$newdo['process_name']."'";
		$res = $newmodel->db()->getOne($sql);
		if($res !== false){
			$result['error'] = '流程名称重复,不允许添加!!!';
			Util::jsonExit($result);
		}

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			//保存审核人
			$res = $newmodel->saveProUser($user,$res);
			if($res !== false){
				$result['success'] = 1;
			}else{
				$result['error'] = '添加失败';
			}
		}
		else
		{
			$result['error'] = '添加失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');

		$id = _Post::getInt('id');
		$scope = _Post::getList('scope');//经营范围array
		$depart_id = _Post::getInt('department_id');//部门ID
		$user = _Post::getList('user');//审核人array

		$sql = 'SELECT count(*) FROM `app_processor_audit` WHERE `process_id` = '.$id;
		$res = DB::cn(14)->getOne($sql);
		if($res){
			$result['error'] = '该流程已被使用,不可修改,请新建流程!!!';
			Util::jsonExit($result);
		}
		if(empty($scope)){
			$result['error'] = '请选择经营范围!!!';
			Util::jsonExit($result);
		}
		if(empty($depart_id)){
			$result['error'] = '请选择申请部门!!!';
			Util::jsonExit($result);
		}
		if(count($user)==0){
			$result['error'] = '请选择审批人!!!';
			Util::jsonExit($result);
		}
		$newmodel =  new AppProcessorProcessModel($id,14);
		$view = new AppProcessorProcessView($newmodel);
		$depart_name = $view->get_department_name($depart_id);

		$process_name = $depart_name.'=>';
		$business_scope = implode(',',$scope);
		$business_type = $newmodel->mkBusiness($business_scope);
		$process_name = $process_name.$business_type.'=>['.date('Y-m-d',time()).']';//流程名称

		$newdo=array(
			'id'=>$id,
			'process_name'=>$process_name,			//流程名称
			'business_type'=>$business_type,		//经营范围[中文]
			'business_scope'=>$business_scope,		//经营范围[数字]
			'department_id'=>$depart_id,			//部门ID
			'create_user_id'=>$_SESSION['userId'],
			'create_user'=>$_SESSION['userName'],
			'create_time'=>date('Y-m-d H:i:s',time())
		);

		$sql = "SELECT count(*) FROM `app_processor_process` WHERE `process_name` ='".$newdo['process_name']."' AND `id` <> ".$id;
		$res = $newmodel->db()->getOne($sql);
		if($res !== false){
			$result['error'] = '流程名称已被使用,不允许添加!!!';
			Util::jsonExit($result);
		}

		$olddo = $newmodel->getDataObject();

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			//删除原审核人
			if(!empty($user)){
				$sql = "DELETE FROM `app_processor_user` WHERE `process_id` = ".$id;
				DB::cn(14)->query($sql);
			}
			//保存审核人
			$res = $newmodel->saveProUser($user,$id);
			if($res !== false){
				$result['success'] = 1;
			}else{
				$result['error'] = '修改失败';
			}
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppProcessorProcessModel($id,14);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		$v = new AppProcessorProcessView($model);
		$Enabled = $v->get_is_enabled();
		if($Enabled != 0){
			$result['error'] = "流程正在使用中,请先禁用,再删除!";
		}else{
			$model->setValue('is_deleted',1);
			$res = $model->save(true);
			if($res !== false){
				$result['success'] = 1;
			}else{
				$result['error'] = "删除失败";
			}
		}

		Util::jsonExit($result);
	}

	/*启用*/
	public function toEnabled($params){
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppProcessorProcessModel($id,14);
		$model->setValue('is_enabled',1);
		$res = $model->save(true);

		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);

	}

	/*禁用*/
	public function noEnabled($params){
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppProcessorProcessModel($id,14);
		$model->setValue('is_enabled',0);
		$res = $model->save(true);

		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);

	}

//	public function mkTable(){
//		$user_id = _Post::getInt('user_id');
//		$real_name = _Post::getString('real_name');
//
//		$this->render('app_processor_process_table.html',
//			['user_id'=>$user_id,'real_name'=>$real_name]
//		);
//	}

	public function getUserTable(){
		$id = _Post::getInt('id');
		$model = new AppProcessorProcessModel($id,13);
		$users = $model->getApproveUsers($id);

		$this->render('app_processor_process_table1.html',['users'=>$users]);


	}



}

?>