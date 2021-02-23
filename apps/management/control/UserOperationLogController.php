<?php
/**
 *  -------------------------------------------------
 *   @file		: UserOperationLogController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-24 11:41:04
 *   @update	:
 *  -------------------------------------------------
 */
class UserOperationLogController extends CommonController
{
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{   
	    $c_view = new ControlView(new ControlModel(1));
	    
		$this->render('user_operation_log_form.html',array(
		        'c_view'=>$c_view,
		        'bar'=>Auth::getBar())
		);
	}
	
	public function search($params){
	    $args = array(
	        'mod'	=> _Request::get("mod"),
	        'con'	=> substr(__CLASS__, 0, -10),
	        'act'	=> __FUNCTION__,
	        'start_date'=>_Request::get('start_date'),
	        'end_date'=>_Request::get('end_date')
	    );
	    $where = array(
	        'module'=>_Request::get("module"),
	        'controller'=>_Request::get("controller"),
	        'action'=>_Request::get("action"),
	        'remark'=>_Request::get("remark"),
	        'start_date'=>_Request::get("start_date"),
	        'end_date'=>_Request::get("end_date"),
	        'create_user'=>_Request::get('create_user')
	    );
	    
	    $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
  
	    $model = new UserOperationLogModel(1);
	    $data = $model->pageList($where,$page,10,false);
	    
	    $pageData = $data;
	    $pageData['filter'] = $args;
	    $pageData['jsFuncs'] = 'user_operation_log_search_page';
	    $this->render('user_operation_log_search_list.html',array(
	        'pa'=>Util::page($pageData),
			'page_list'=>$data
	    ));
	}
}

?>