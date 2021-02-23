<?php
/**
 *  -------------------------------------------------
 *   @file		: SystemAccessLogController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-11 11:50:03
 *   @update	:
 *  -------------------------------------------------
 */
class SystemAccessLogController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('system_access_log_search_form.html',array('bar'=>Auth::getBar()));
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
			'start_date'=>_Request::get('start_date'),
			'end_date'=>_Request::get('end_date')
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where['start_date'] = $args['start_date'];
		$where['end_date'] = $args['end_date'];

		$model = SystemAccessLog::getInstance(null,1);
		$data = SystemAccessLog::getLogs($where,$page,10,1);

		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'system_access_log_search_page';
		$this->render('system_access_log_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}
}

?>