<?php
/**
 *  -------------------------------------------------
 *   @file		: AppMemberAccountController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 12:23:01
 *   @update	:
 *  -------------------------------------------------
 */
class AppMemberAccountController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('app_member_account_search_form.html',array('bar'=>Auth::getBar()));
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


		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();

		$model = new AppMemberAccountModel(17);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_member_account_search_page';
		$this->render('app_member_account_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');

		$result['content'] = $this->fetch('app_member_account_info.html',array(
			'view'=>new AppMemberAccountView(new AppMemberAccountModel(17))
		));
		$result['title'] = '添加-会员账户';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_member_account_info.html',array(
			'view'=>new AppMemberAccountView(new AppMemberAccountModel($id,17))
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		die('开发中');
		$id = intval($params["id"]);
		$this->render('app_member_account_show.html',array(
			'view'=>new AppMemberAccountView(new AppMemberAccountModel($id,17))
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		//rules验证
		$vd = new Validator();
		$vd->set_rules('memeber_id', '会员卡号',  'require');
		$vd->set_rules('memeber_id', '会员卡号',  'isNumer');
		$vd->set_rules('current_money', '当前余额',  'require');
		$vd->set_rules('total_money', '总消费金额',  'require');
		$vd->set_rules('total_point', '总积分',  'require');

		if (!$vd->is_valid($_POST))
		{
			$result['error'] = $vd->get_errors();
			Util::jsonExit($result);
		}

		//接收数据
		foreach ($_POST as $k => $v) {
			$$k = _Post::get($k);
		}

		$olddo = [];
		$newdo = [
			'memeber_id'=>$memeber_id,
			'current_money'=>$current_money,
			'total_money'=>$total_money,
			'total_point'=>$total_point,
		];

		$newmodel =  new AppMemberAccountModel(18);
		$memeber_id_info = $newmodel-> getDataInfoByMemeberId($memeber_id);
		if($memeber_id_info){

			$result['error'] = '此会员卡号已存在';
			Util::jsonExit($result);
			exit;
		}
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
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
		//rules验证
		$vd = new Validator();
		$vd->set_rules('memeber_id', '会员卡号',  'require');
		$vd->set_rules('current_money', '当前余额',  'require');
		$vd->set_rules('total_money', '总消费金额',  'require');
		$vd->set_rules('total_point', '总积分',  'require');

		if (!$vd->is_valid($_POST))
		{
			$result['error'] = $vd->get_errors();
			Util::jsonExit($result);
		}

		//接收数据
		foreach ($_POST as $k => $v) {
			$$k = _Post::get($k);
		}

		$newmodel =  new AppMemberAccountModel($id,18);

		$olddo = $newmodel->getDataObject();
		$newdo = [
			'id'=>$id,
			'memeber_id'=>$memeber_id,
			'current_money'=>$current_money,
			'total_money'=>$total_money,
			'total_point'=>$total_point,
		];

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
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
		$model = new AppMemberAccountModel($id,18);
//		$do = $model->getDataObject();
//		$valid = $do['is_system'];
//		if($valid)
//		{
//			$result['error'] = "当前记录为系统内置，禁止删除";
//			Util::jsonExit($result);
//		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
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