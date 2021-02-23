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
class MenuRecycleController extends CommonController
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
		$this->render('menu_recycle_search_form.html',array('view'=>new MenuView(new MenuModel(1)),'bar'=>Auth::getBar()));
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
			'label'=>_Request::get('label'),
			'group_id'=>_Request::getInt('group_id'),
			'is_deleted'=>1
		);
		$page = _Request::getInt("page",1);
		$where = array();
		$where['label'] = $args['label'];
		$where['group_id'] = $args['group_id'];
		$where['is_deleted'] = $args['is_deleted'];

		$model = new MenuModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'menu_recycle_search_page';
		$this->render('menu_recycle_search_list.html',array(
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
		$model = new MenuModel($id,2);
		$do = $model->getDataObject();
		if(!$do['is_deleted'])
		{
			$result['error'] = "当前菜单没有被删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',0);
		$res = $model->save(true);
		if($res !== false){
			$model->recoverPermission(array('id'=>$id,'type'=>'MENU'));
			$result['success'] = 1;
		}else{
			$result['error'] = "恢复失败";
		}
		Util::jsonExit($result);
	}
}

?>