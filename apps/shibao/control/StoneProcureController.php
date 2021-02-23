<?php
/**
 *  -------------------------------------------------
 *   @file		: StoneProcureController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-28 15:47:27
 *   @update	:
 *  -------------------------------------------------
 */
class StoneProcureController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('stone_procure_search_form.html', [
				'bar'=>Auth::getBar(),'view'=>new StoneProcureView(new StoneProcureModel(45))
			]);
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
			'pro_sn'=>_Request::getString('pro_sn'),
			'pro_type'=>_Request::getInt('pro_type'),
			'check_status'=>_Request::getString('check_status'),
		);
		$page = _Request::getInt("page",1);
		$where = array();
		if(!empty($args['pro_sn'])){$where['pro_sn'] = $args['pro_sn'];}
		if($args['pro_type'] != 0){$where['pro_type'] = $args['pro_type'];}
		if($args['check_status']!=''){$where['check_status'] = $args['check_status'];}

		$model = new StoneProcureModel(45);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'stone_procure_search_page';
		$this->render('stone_procure_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	public function detailSearch(){
		$pro_id = _Get::getInt('pro_id');
		$where['pro_id'] = $pro_id;
		$page = _Request::getInt("page",1);
		$model = new StoneProcureModel(45);
		$data = $model->getDetailList($where,$page,10,false);
		$pageData = $data;
		$this->render('stone_procure_detail_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));

	}
	
	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$this->render('stone_procure_info.html',array(
			'view'=>new StoneProcureView(new StoneProcureModel(45))
		));
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$this->render('stone_procure_info.html',array(
			'view'=>new StoneProcureView(new StoneProcureModel($id,45)),
		));
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$this->render('stone_procure_show.html',array(
			'view'=>new StoneProcureView(new StoneProcureModel($id,45)),
			'bar'=>Auth::getViewBar()
		));
	}

	public function mkJson(){
		$id = _Post::getInt('id');
		$dict = new DictModel(1);
		$model = new StoneProcureModel(45);
		$arr = $model->mkJsonTable();
		if(!$id){
			$arr['data'] = array();
		}else{
			$select = $model->mkJsonTable(true);
			$data = $model->getDetail($id,$select);
			foreach ($data as $k=>$row) {
				$data[$k]=[
					"{$row['keep_ct']}",
					"{$row['keep_num']}",
					"{$row['pro_ct']}",
					"{$dict->getEnum('stone.ct_norms',$row['ct_norms'])}",
					"{$dict->getEnum('stone.color_norms',$row['color_norms'])}",
					"{$dict->getEnum('stone.clarity_norms',$row['clarity_norms'])}",
					"{$row['pro_budget']}",
				];
			}
			$arr['data'] = $data;
		}
		$json = json_encode($arr);
		echo $json;
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$newmodel =  new StoneProcureModel(46);

		$newdo = $newmodel->mkNewdo();
		$detail = _Post::getList('data');
		//去除空行
		if(empty(end($detail)[0])){array_pop($detail);}
		$detail = $newmodel->getAssocData($detail);
		if($detail === false){
			$result['error'] = '明细参数有误';
			Util::jsonExit($result);
		}
		$newdo['pro_sn'] = $newmodel->createProSn();
		$newdo['create_id'] = $_SESSION['userId'];
		$newdo['create_user'] = $_SESSION['realName'];
		$newdo['create_time'] = date('Y-m-d H:i:s');
		$newdo['pro_total'] = 0;//总金额
		$newdo['pro_ct'] = 0;//总重量

		foreach ($detail as $v) {
			$newdo['pro_total'] += $v['pro_budget'];
			$newdo['pro_ct'] += $v['pro_ct'];
		}
		$res = $newmodel->insert2($newdo,$detail,'stone_procure_details','pro_id');
		if($res !== false)
		{
			$do['pro_id'] = $res;
			$do['check_id'] = $newdo['create_id'];
			$do['check_user'] = $newdo['create_user'];
			$do['check_time'] = date('Y-m-d H:i:s');
			$do['check_info'] = '创建单据';
			$res = $newmodel->mkLog($do);
			if($res){
				$result['success'] = 1;
			}else{
				$result['error'] = '写入日志失败';
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
	public function update ()
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Post::getInt('id');
		$newmodel = new StoneProcureModel($id,46);
		$view = new StoneProcureView($newmodel);
		$check_status = $view->get_check_status();
		if($check_status == '1'){
			$result['error'] = '该信息审核中,不可修改';
			Util::jsonExit($result);
		}
		$olddo = $newmodel->getDataObject();
		$newdo = $newmodel->mkNewdo();

		$detail = _Post::getList('data');
		//去除空行
		if(empty(end($detail)[0])){array_pop($detail);}
		$detail = $newmodel->getAssocData($detail);
		if($detail === false){
			$result['error'] = '明细参数有误';
			Util::jsonExit($result);
		}
		$newdo['id'] = $id;
		$newdo['create_id'] = $_SESSION['userId'];
		$newdo['create_user'] = $_SESSION['realName'];
		$newdo['create_time'] = date('Y-m-d H:i:s');
		$newdo['pro_total'] = 0;//总金额
		$newdo['pro_ct'] = 0;//总重量
		foreach ($detail as $v) {
			$newdo['pro_total'] += $v['pro_budget'];
			$newdo['pro_ct'] += $v['pro_ct'];
		}
		$res = $newmodel->update2($newdo,$olddo,$detail,'stone_procure_details','pro_id');
		if($res !== false)
		{
			$do['pro_id'] = $id;
			$do['check_id'] = $newdo['create_id'];
			$do['check_user'] = $newdo['create_user'];
			$do['check_time'] = date('Y-m-d H:i:s');
			$do['check_info'] = '修改单据';
			$res = $newmodel->mkLog($do);
			if($res){
				$result['success'] = 1;
			}else{
				$result['error'] = '写入日志失败';
			}
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	/**
	 * 提交申请
	 */
	public function submitAudit($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new StoneProcureModel($id,46);
		$model->setValue('check_status',4);//待审核
		$res = $model->save(true);
		if($res !== false){
			$model->clearCheck($id);
			$result['success'] = 1;
		}else{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);

	}

	public function checkPass($params){
		$id = intval($params['id']);
		$model = new StoneProcureModel($id,46);
		$view = new StoneProcureView($model);
		//审核状态
		$check_status = $view->get_check_status();
		if(!in_array($check_status,['4','1'])){
			$result['error'] = '该信息不是待审核状态';
			Util::jsonExit($result);
		}
		if($check_status == '2'){
			$result['error'] = '对不起,该信息已驳回';
			Util::jsonExit($result);
		}
		//获取审核组用户
		$u_model = new UserModel(1);
		$checkUsers = $u_model->getGroupCheckUser(2);//石包采购组
		$checkUsers = array_column($checkUsers,'user_id','id');
		if(!in_array($_SESSION['userId'],$checkUsers)){
			$result['error'] = '对不起,您没有审核权限';
			Util::jsonExit($result);
		}
		$res = $model->checkPass($_SESSION['userId'],$id);
		if($res !== true ){
			$result['error'] = ($res === false)?"操作失败":"您已审核过该信息";
			Util::jsonExit($result);
		}
		$check_plan = $view->get_check_plan();
		$check_plan += 1;
		$check_s = count($checkUsers);
		if($check_plan >= $check_s){
			$check_plan = $check_s;
			$model->setValue('check_status',3);//审核通过
		}else{
			$model->setValue('check_status',1);//审核中
		}
		$model->setValue('check_plan',$check_plan);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);

	}

	public function checkOut($params){
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new StoneProcureModel($id,46);
		$view = new StoneProcureView($model);
		//审核状态
		$check_status = $view->get_check_status();
		if(!in_array($check_status,['4','1'])){
			$result['error'] = '该信息不是待审核状态';
			Util::jsonExit($result);
		}
		if($check_status == '2'){
			$result['error'] = "对不起,该信息已驳回";
			Util::jsonExit($result);
		}
		//获取审核组用户
		$u_model = new UserModel(1);
		$checkUsers = $u_model->getGroupCheckUser(2);//石包采购组
		$checkUsers = array_column($checkUsers,'user_id','id');
		if(!in_array($_SESSION['userId'],$checkUsers)){
			$result['error'] = "对不起,您没有审核权限";
			Util::jsonExit($result);
		}
		$refuse_cause = _Post::getString('refuse_cause');
		if(empty($refuse_cause)){
			$result['error'] = "请填写驳回原因";
			Util::jsonExit($result);
		}
		//进行审核
		$res = $model->checkOut($_SESSION['userId'],$id,$refuse_cause);
		if($res !== true ){
			$result['error'] = ($res === false)?"操作失败":"您已审核过该信息";
			Util::jsonExit($result);
		}
		$model->setValue('refuse_cause',$refuse_cause);
		$model->setValue('check_status',2);//审核驳回
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);
	}


	public function showLog(){
		$pro_id = _Post::getInt('pro_id');
		$model = new StoneProcureModel(45);
		$data = $model->getLogInfo($pro_id);
		$this->render('stone_procute_log_list.html', ['data' => $data]);
	}

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new StoneProcureModel($id,46);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		//联合删除？
		//$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
}

?>