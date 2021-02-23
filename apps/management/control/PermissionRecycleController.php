<?php
/**
 *  -------------------------------------------------
 *   @file		: PermissionRecycleController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-19 11:51:55
 *   @update	:
 *  -------------------------------------------------
 */
class PermissionRecycleController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		if(Auth::$userType>2)
		{
			die('操作禁止');
		}
		$model = new PermissionModel(1);
		if(Auth::$userType<3)
		{
			$bar = Auth::getBar();
		}
		else
		{
			$bar = '';
		}
		$this->render('permission_recycle_search_form.html',array('bar'=>$bar,'view'=>new PermissionView($model)));
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
			'name'  =>_Request::get('name'),
			'type'  =>_Request::getInt('type'),
			'is_deleted'=>1
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array(
			'name'=>$args['name'],'type'=>$args['type'],'is_deleted'=>$args['is_deleted']
		);

		$model = new PermissionModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'permission_recycle_search_page';
		$this->render('permission_recycle_search_list.html',array(
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
		$model = new PermissionModel($id,2);
		$do = $model->getDataObject();
		if(!$do['is_deleted'])
		{
			$result['error'] = "当前记录没有被删除";
			Util::jsonExit($result);
		}
		//检查元素是否删除，元素已删除不允许恢复
		$valid = $model->checkElement($do['resource_id'],$do['type']);
		if(!$valid)
		{
			$result['error'] = "实体元素已删除，权限恢复失败";
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