<?php
/**
 *  -------------------------------------------------
 *   @file		: FactoryOpraDictController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ruir
 *   @date		: 2015-04-13 11:55:49
 *   @update	:
 *  -------------------------------------------------
 */
class FactoryOpraDictController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('factory_opra_dict_search_form.html',array('bar'=>Auth::getBar()));
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
		);
		$page = _Request::getInt("page",1);
		$where = array(
			'name'=>_Request::get("name"),
			'start_time'=>_Request::get("start_time"),
			'end_time'=>_Request::get("end_time"),
			'edit_start_time'=>_Request::get("edit_start_time"),
			'edit_end_time'=>_Request::get("edit_end_time")
			);
		$args=array_merge($args,$where);
		$model = new FactoryOpraDictModel(13);
		$data = $model->pageList($where,$page,20,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'factory_opra_dict_search_page';
		$this->render('factory_opra_dict_search_list.html',array(
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
		$result['content'] = $this->fetch('factory_opra_dict_info.html',array(
			'view'=>new FactoryOpraDictView(new FactoryOpraDictModel(13))
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('factory_opra_dict_info.html',array(
			'view'=>new FactoryOpraDictView(new FactoryOpraDictModel($id,13)),
			'tab_id'=>$tab_id
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$this->render('factory_opra_dict_show.html',array(
			'view'=>new FactoryOpraDictView(new FactoryOpraDictModel($id,13)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$newdo=array(
			'name'=>_Request::get('name'),
			'create_time'=>date('Y-m-d H:i:s'),
			'edit_time'=>date('Y-m-d H:i:s'),
			'display_order'=>time()
			);
		$newmodel =  new FactoryOpraDictModel(14);
		$res = $newmodel->addInfo($newdo);
		if($res['success'] == 1)
		{
			$result['success'] = 1;
			$result['error'] = $res['error'];
		}
		else
		{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
	}
	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');

		$id = _Post::getInt('id');
		$newmodel =  new FactoryOpraDictModel($id,14);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'id'=>$id,
			'name'=>_Request::get('name'),
			'edit_time'=>date('Y-m-d H:i:s')
		);

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
	}
	//检查是否重复更新状态
	function checkUpdate($params,$status)
	{
		$id = intval($params['id']);
		$model = new FactoryOpraDictModel($id,14);
		$do = $model->getDataObject();
		if(empty($do))
		{
			return 1;
		}
		elseif($do['status']==$status)
		{
			return 2;
		}
		return 0;
	}

	/**
	 *	delete，禁用
	 */
	public function delete ($params)
	{
		$result=$this->changeStstus($params,0);
		Util::jsonExit($result);
	}
	/**
	 *	delete，启用
	 */
	public function enabled($params)
	{
		$result=$this->changeStstus($params,1);
		Util::jsonExit($result);
	}
	//更新状态
	function changeStstus($params,$value)
	{
		$result = array('success' => 0,'error' => '');
		$status=$this->checkUpdate($params,$value);
		if($status==1)
		{
			$result['error'] = "非法操作";
		}
		elseif($status==2)
		{
			$result['error'] = "重复操作";
		}
		else
		{
			$model = new FactoryOpraDictModel(14);
			$res = $model->updatestatus($params['id'] , $value);
			if($res['success'] == 1){
				$result['success'] = 1;
				$result['error'] = $res['error'];
			}else{
				$result['error'] = $res['error'];
			}
		}
		return $result;

	}
	/**
	 *	moveup,上移
	 */
	public function moveup ()
	{
		$result = array('success' => 0,'error' => '');
		$id = _Post::getInt('id');
		$model = new FactoryOpraDictModel($id,14);
		$res = $model->move($id,true);
		if($res == 1){
			$result['success'] = 1;
		}
		else if ($res==3)
		{
			$result['error'] = "已经是第一个了";
		}
		else
		{
			$result['error'] = "移动失败";
		}
		Util::jsonExit($result);
	}

	/**
	 *	movedown,下移
	 */
	public function movedown ()
	{
		$result = array('success' => 0,'error' => '');
		$id = _Post::getInt('id');
		$model = new FactoryOpraDictModel($id,14);
		$res = $model->move($id,false);
		if($res == 1){
			$result['success'] = 1;
		}
		else if ($res==3)
		{
			$result['error'] = "已经是最后一个了";
		}
		else
		{
			$result['error'] = "移动失败";
		}
		Util::jsonExit($result);
	}
}

?>