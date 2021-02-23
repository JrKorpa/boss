<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseStyleLogController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-02 16:58:21
 *   @update	:
 *  -------------------------------------------------
 */
class BaseStyleLogController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('base_style_log_search_form.html',array('bar'=>Auth::getBar()));
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
            'style_id'=>  _Request::getInt('_id'),
		);
		$page = _Request::getInt("page",1);
		$where = array(
             'style_id'=>  _Request::getInt('_id'),
        );

		$model = new BaseStyleLogModel(11);
		$data = $model->pageList($where,$page,25,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'base_style_log_search_page';
		$this->render('base_style_log_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}
	    /**
     * 	add，渲染添加页面
     */
    public function add() {
		$style_id = _Request::getInt('_id');
		$view = new BaseStyleLogView( new BaseStyleLogModel(11));
        $result['content'] = $this->fetch('base_style_log_info.html', array(
            'style_id' => $style_id,
            '_id' => _Post::getInt('_id'),
			'view'=>$view
        ));
        $result['title'] = '添加';
        Util::jsonExit($result);
    }
	/**
	*insert信息入库
	*/
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
        $remark = _Request::getString('style_remark');
		$style_id = _Post::getInt('_id');
		if($remark=='')
		{
			$result['error'] ="备注不能为空！";
			Util::jsonExit($result);
		}
		$olddo = array();
		$newdo=array(
			'style_id'=>$style_id,
			'create_user'=>$_SESSION['userName'],
            'create_time'=>date("Y-m-d H:i:s"),
			'remark'=>$remark
		);
		$newmodel =  new BaseStyleLogModel(12);
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '添加失败';
		}
		Util::jsonExit($result);
	}
}

?>