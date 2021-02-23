<?php
/**
 *  -------------------------------------------------
 *   @file		: FieldScopeRecycleController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-06 09:27:11
 *   @update	:
 *  -------------------------------------------------
 */
class FieldScopeRecycleController extends CommonController
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
		$this->render('field_scope_recycle_search_form.html',array('bar'=>Auth::getBar(),'view'=>new FieldScopeView(new FieldScopeModel(1))));
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
			'is_deleted'=>1,
			'c_id'=>_Request::getInt('c_id'),
			'label'=>_Request::get('label'),
			'code'=>_Request::get('code')
		);
		$page = _Request::getInt("page",1);
		$where = array();
		$where['is_deleted']=$args['is_deleted'];
		$where['c_id']=$args['c_id'];
		$where['label']=$args['label'];
		$where['code']=$args['code'];

		$model = new FieldScopeModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'field_scope_recycle_search_page';
		$this->render('field_scope_recycle_search_list.html',array(
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
		$model = new FieldScopeModel($id,2);
		$do = $model->getDataObject();
		if(!$do['is_deleted'])
		{
			$result['error'] = "当前记录没有被删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',0);
		$res = $model->save(true);
		if($res !== false){
			$model->recoverPermission(array('id'=>$id,'type'=>'SCOPE'));
			$result['success'] = 1;
		}else{
			$result['error'] = "恢复失败";
		}
		Util::jsonExit($result);
	}
}

?>