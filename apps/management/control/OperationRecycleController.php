<?php
/**
 *  -------------------------------------------------
 *   @file		: OperationController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-24
 *   @update	:
 *  -------------------------------------------------
 */
class OperationRecycleController extends Controller
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{

	}
	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		if(Auth::$userType>2)
		{
			die('操作禁止');
		}
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'_id' => _Request::get("_id"),//主对象的数据id
			'is_deleted'=>1,
		);

		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();

		$where['_id'] =$args['_id'];
		$where['is_deleted']=$args['is_deleted'];

		$model = new OperationModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'operation_recycle_search_page_1';
		$this->render('operation_recycle_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}


	public function recover ()
	{
		$id = _Post::getInt('id');
/*		var_dump($_POST);
		exit;*/
		$model = new OperationModel($id,2);
		$do = $model->getDataObject();
		if(!$do['is_deleted'])
		{
			$result['error'] = "当前操作没有被删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',0);
		$res = $model->save(true);
		if($res !== false){
			$model->recoverPermission(array('id'=>$id,'type'=>'OPERATION'));
			$result['success'] = 1;
		}else{
			$result['error'] = "恢复失败";
		}
		Util::jsonExit($result);
	}



}

?>