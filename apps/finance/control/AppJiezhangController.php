<?php
/**
 *  -------------------------------------------------
 *   @file		: AppJiezhangController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-21 19:04:39
 *   @update	:
 *  -------------------------------------------------
 */
class AppJiezhangController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$model = new AppJiezhangModel(29);
		$year_list = $model->getYear();
		$this->render('app_jiezhang_search_form.html',array(
				'bar'=>Auth::getBar(),
				'year_list' => $year_list
		));
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
            'year' => _Request::getString('year'),
            'start_time' => _Request::getString('start_time'),
            'end_time' => _Request::getString('end_time')

		);
		$page = _Request::getInt("page",1);
		$where = array();
		$where = array(
				'year'=>$args['year'],
				'start_time'=>$args['start_time'],
				'end_time'=>$args['end_time']
			);

		$model = new AppJiezhangModel(29);
		$data = $model->pageList($where,$page,20,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_jiezhang_search_page';
		$this->render('app_jiezhang_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$model = new AppJiezhangModel(29);
		$next = $model->getNext();
		$result['content'] = $this->fetch('app_jiezhang_info.html',array(
			'view'=>new AppJiezhangView($model),
			'next' => $next
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	/*public function edit ($params)
	{
		$id = intval($params["id"]);
		$tab_id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$model = new AppJiezhangModel($id,29);
		$next = $model->getDataObject();
		$result['content'] = $this->fetch('app_jiezhang_info.html',array(
			'view'=>new AppJiezhangView($model),
			'tab_id'=>$tab_id,
			'next' => $next
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}*/

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$olddo = array();
		$newdo=array(
			'qihao' => $params['qihao'],
			'start_time' => $params['start_time'],
			'end_time' => $params['end_time'],
			'year' => $params['year']
		);

		if($newdo['end_time']==''){
			$result['error'] = '请选择结束日期';
			Util::jsonExit($result);
		}

        $newmodel =  new AppJiezhangModel(30);
        $existQihao = $newmodel->existQihao($newdo['qihao'],$newdo['year']);
        if($existQihao){
            $result['error'] = '结账账期管理不可添加相同账期';
			Util::jsonExit($result);
        }
        $res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			$result['error'] = '结账成功';
		}
		else
		{
			$result['error'] = '结账失败';
		}
		Util::jsonExit($result);
	}

	public function delete($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = $params['id'];
		$model = new AppJiezhangModel($id,30);
		$info = $model->getLast();
		if($info['id'] != $id)
		{
			$result['error'] = '只可以取消最后一条账期数据';
			Util::jsonExit($result);
		}
		if($model->delete())
		{
			$result['error'] = '取消结账成功';
			$result['success'] = 1;
		}else{
			$result['error'] = '取消结账失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	update，更新信息
	 */
	/*public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');

		$id = _Post::getInt('id');

		$newmodel =  new AppJiezhangModel($id,30);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
				'id' => $id,
				'qihao' => _Request::getInt('qihao'),
				'start_time' => _Request::getString('start_time'),
				'end_time' => _Request::getString('end_time'),
				'year' => _Request::getString('year')
			);
		if($newdo['qihao']==''){
			$result['error'] = '请选择期号(1-12)';
			Util::jsonExit($result);
		}
		if($newdo['start_time']==''){
			$result['error'] = '请选择开始日期';
			Util::jsonExit($result);
		}
		if($newdo['end_time']==''){
			$result['error'] = '请选择结束日期';
			Util::jsonExit($result);
		}
		if($newdo['year']==''){
			$result['error'] = '请选择会计年度';
			Util::jsonExit($result);
		}

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;
			$result['title'] = '修改此处为想显示在页签上的字段';
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}*/

}

?>