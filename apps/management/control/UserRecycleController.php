<?php
/**
 *  -------------------------------------------------
 *   @file		: UserRecycleController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-11 12:12:13
 *   @update	:
 *  -------------------------------------------------
 */
class UserRecycleController extends CommonController
{
	protected $smartyDebugEnabled = true;


	/**
	 *	recycle，回收站搜索框
	 */
	public function index ($params)
	{
		if(Auth::$userType>2)
		{
			die('操作禁止');
		}
		$this->render('user_recycle_search_form.html',array('view'=>new UserView(new UserModel(1)),'bar'=>Auth::getBar()));
	}


	/**
	 *  recycleList，回收列表
	 */
	public function search ($params)
	{
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'account'	=> _Request::get("account"),
			'real_name'	=> _Request::get("real_name"),
			'code'	=> _Request::get("code"),
			'is_on_work'	=> _Request::getInt("is_on_work"),
			'user_type'	=> _Request::getInt("user_type"),
			'is_deleted'=>1
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where['account'] = $args['account'];
		$where['real_name'] = $args['real_name'];
		$where['code'] = $args['code'];
		$where['user_type'] = $args['user_type'];
		$where['is_on_work'] = $args['is_on_work'];
		$where['is_deleted'] = $args['is_deleted'];
		$model = new UserModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'user_recycle_search_page';
		$this->render('user_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	recover，恢复
	 */
	public function recover ()
	{
		$id = _Post::getInt('id');
		$model = new UserModel($id,2);
		$do = $model->getDataObject();
		if(!$do['is_deleted'])
		{
			$result['error'] = "当前用户没有被删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',0);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "恢复失败";
		}
		Util::jsonExit($result);
	}
}

?>