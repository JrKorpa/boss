<?php
/**
 *  -------------------------------------------------
 *   @file		: CompanyController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-31 17:52:34
 *   @update	:
 *  -------------------------------------------------
 */


class ControlRecycleController extends CommonController

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
		$this->render('control_recycle_search_form.html',array('bar'=>Auth::getBar()));
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
			'label'	=> _Request::get("label"),
			'code'	=> _Request::get("code"),
			'application_id'	=> _Request::getInt("application_id"),
			'type'=>_Request::getInt("type"),
			'is_deleted'=>1,
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where['label'] = $args['label'];
		$where['code'] = $args['code'];
		$where['application_id'] = $args['application_id'];
		$where['type'] = $args['type'];
		$where['is_deleted'] = $args['is_deleted'];

		$model = new ControlModel(1);
		$data = $model->pageList($where,$page,10,false);
		foreach($data['data'] as $key=>$val){
			if($val['type']==1){
				$data['data'][$key]['typename']="独立对象";
			}elseif($val['type']==2){
				$data['data'][$key]['typename']="主对象";
			}elseif($val['type']==3){
				$data['data'][$key]['typename']="明细对象";
			}
		}
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'control_search_page';
		$this->render('control_search_list.html',array(
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
		$model = new ControlModel($id,2);
		$do = $model->getDataObject();

		if(!$do['is_deleted'])
		{
			$result['error'] = "当前文件没有被删除";
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

