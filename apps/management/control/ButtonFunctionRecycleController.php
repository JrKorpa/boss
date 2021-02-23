<?php
/**
 *  -------------------------------------------------
 *   @file		: ButtonFunctionController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-11 10:07:47
 *   @update	:
 *  -------------------------------------------------
 */
class ButtonFunctionRecycleController extends CommonController
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
		$this->render('button_function_recycle_search_form.html',array('bar'=>Auth::getBar()));
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
			'type'  =>_Request::getInt("type"),
			'is_deleted'=>1
		);

		$where = array();
		$where['is_deleted']=$args['is_deleted'];
		$where['type']=$args['type'];
		$model = new ButtonFunctionModel(1);
		$data = $model->listAll($where);
		$this->render('button_function_search_list.html',array(
			'page_list'=>$data
		));
	}

	//回复按钮函数
	public function recover ()
	{
		$id = _Post::getInt('id');
		$model = new ButtonFunctionModel($id,2);
		$do = $model->getDataObject();
		if(!$do['is_deleted'])
		{
			$result['error'] = "当前按钮函数没有被删除";
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