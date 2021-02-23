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


class CompanyRecycleController extends CommonController
{
	protected $smartyDebugEnabled = true;


	/**
	 *	recycle，回收站搜索框
	 */
	public function index ($params)
	{

		$this->render('company_recycle_search_form.html',array('bar'=>Auth::getBar
		()));
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
			'company_name'=>_Request::get("company_name"),
			'contact'=>_Request::get("contact"),
			'is_deleted'=>1
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where['company_name']=$args['company_name'];
		$where['contact']=$args['contact'];
		$where['is_deleted']=$args['is_deleted'];
		$model = new CompanyModel(1);
		$data = $model->pageList($where,$page,2,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'company_search_page';
		$this->render('company_search_list.html',array(
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
		$model = new CompanyModel($id,2);
		$do = $model->getDataObject();
		if(!$do['is_deleted'])
		{
			$result['error'] = "当前公司没有被删除";
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

