<?php
/**
 *  -------------------------------------------------
 *   @file		: SalesChannelsRecycleController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 
 *   @update	:
 *  -------------------------------------------------
 */
class SalesChannelsRecycleController extends CommonController
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
		$this->render('sales_channels_recycle_search_form.html',array('bar'=>Auth::getBar()));
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
			'channel_name'=>_Request::get("channel_name"),
			'channel_code'=>_Request::get("channel_code")


		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where['channel_name'] = $args['channel_name'];
		$where['channel_code'] = $args['channel_code'];
		$where['is_deleted'] = 1;

		$model = new SalesChannelsModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'sales_channels_recycle_search_page';
		$this->render('sales_channels_recycle_search_list.html',array(
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
		$model = new SalesChannelsModel($id,2);
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


