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
class StoneProcureListController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('stone_procure_search_form.html',array('bar'=>Auth::getBar()));
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

		);
		$page = _Request::getInt("page",1);
		$where = array();
		if(!empty($args['pro_sn'])){$where['pro_sn'] = $args['pro_sn'];}
		if($args['pro_type'] != 0){$where['pro_type'] = $args['pro_type'];}

		$model = new StoneProcureModel(45);
		$data = $model->passPageList($where,$page,10,false);
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


	public function showLog(){
		$pro_id = _Post::getInt('pro_id');
		$model = new StoneProcureModel(45);
		$data = $model->getLogInfo($pro_id);
		$this->render('stone_procute_log_list.html', ['data' => $data]);
	}

}

?>