<?php
/**
 *  -------------------------------------------------
 *   @file		: ControlController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-20 13:46:02
 *   @update	:
 *  -------------------------------------------------
 */
class ControlController extends CommonController
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
                $application_id = _Request::getInt("application_id");
		$this->render('control_search_form.html',array('view'=>new ControlView(new ControlModel(1)),'bar'=>Auth::getBar(),'application_id'=>$application_id));
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
			'label'	=> _Request::get("label"),
			'code'	=> _Request::get("code"),
			'application_id'	=> _Request::getInt("application_id"),
			'type'=>_Request::getInt("type"),
			'is_deleted'=>0
		);
		$page = _Request::getInt("page",1);
		$where = array();
		$where['label'] = $args['label'];
		$where['code'] = $args['code'];
		$where['application_id'] = $args['application_id'];
		$where['type'] = $args['type'];
		$where['is_deleted']=$args['is_deleted'];

		$model = new ControlModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'control_search_page';
		$this->render('control_search_list.html',array(
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
		$result['content'] = $this->fetch('control_info.html',array(
			'view'=>new ControlView(new ControlModel(1))
		));
		$result['title'] = '控制器-添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('control_info.html',array(
			'view'=>new ControlView(new ControlModel($id,1))
		));
		$result['title'] = '控制器-编辑';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$model = new ControlModel($id,1);
//		$do = $model->getDataObject();
//		$m = Util::getMethod($do['code'].'Controller');
//		echo '<pre>';
//		print_r ($m);
//		echo '</pre>';
		$this->render('control_show.html',array(
			'view'=>new ControlView($model),
			'bar'=>Auth::getViewBar(),
			'bar1'=>Auth::getDetailBar('operation'),
			'bar2'=>Auth::getDetailBar('operation_recycle')
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');

		$code = _Post::get('code');
		$label = _Post::get('label');
		$type = _Post::getInt('type');
		$parent_id = _Post::getInt('parent_id');

		if($type!=3){
			$parent_id =0;
		}
		$application_id = _Post::getInt('application_id');

		if($label=='')
		{
			$result['error'] ="控制器显示名称不能为空！";
			Util::jsonExit($result);
		}

		if(!Util::isChinese($label))
		{
			$result['error'] ="控制器显示名称只能是汉字！";
			Util::jsonExit($result);
		}
		if(mb_strlen($label)>20)
		{
			$result['error'] ="控制器显示名称最多20个汉字！";
			Util::jsonExit($result);
		}
		if($code=='')
		{
			$result['error'] ="控制器名不能为空！";
			Util::jsonExit($result);
		}
		if(!Util::isEnglish($code))
		{
			$result['error'] ="控制器名只能是字母！";
			Util::jsonExit($result);
		}
		if(mb_strlen($code)>40)
		{
			$result['error'] ="控制器名最多40个字符！";
			Util::jsonExit($result);
		}
		if(!$application_id)
		{
			$result['error'] ="所属项目不能为空！";
			Util::jsonExit($result);
		}

		if(!$type){
			$result['error'] ="对象类型不能为空！";
			Util::jsonExit($result);
		}
		if($type==3){
			if(!$parent_id){
				$result['error'] ="明细对象必须有一个主对象！";
				Util::jsonExit($result);
			}

		}


		$olddo = array();
		$newdo=array(
			'code'=>ucfirst($code),
			'label'=>$label,
			'application_id'=>$application_id,
			'type'=>$type,
			'parent_id'=>$parent_id
		);

		$newmodel =  new ControlModel(2);
		if($newmodel->hasLabel($label))
		{
			$result['error'] ="控制器显示名称重复！";
			Util::jsonExit($result);
		}
		if($newmodel->hasCode(ucfirst($code)))
		{
			$result['error'] ="重复添加！";
			Util::jsonExit($result);
		}
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$newmodel->makePermission(array('id'=>$res,'name'=>$label.'-数据权限','type'=>'DATA','code'=>'OBJ_'.strtoupper(Util::parseStr($code))));
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '添加失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Post::getInt('id');
		$code = _Post::get('code');
		$label = _Post::get('label');
		$type = _Post::getInt('type');
		$parent_id =_Post::getInt('parent_id');
		$application_id = _Post::getInt('application_id');

		if($label=='')
		{
			$result['error'] ="控制器显示名称不能为空！";
			Util::jsonExit($result);
		}

		if(!Util::isChinese($label))
		{
			$result['error'] ="控制器显示名称只能是汉字！";
			Util::jsonExit($result);
		}
		if(mb_strlen($label)>20)
		{
			$result['error'] ="控制器显示名称最多20个汉字！";
			Util::jsonExit($result);
		}
		if($code=='')
		{
			$result['error'] ="控制器名不能为空！";
			Util::jsonExit($result);
		}
		if(!Util::isEnglish($code))
		{
			$result['error'] ="控制器名只能是字母！";
			Util::jsonExit($result);
		}
		if(mb_strlen($code)>40)
		{
			$result['error'] ="控制器名最多40个字符！";
			Util::jsonExit($result);
		}
		if(!$application_id)
		{
			$result['error'] ="所属项目不能为空！";
			Util::jsonExit($result);
		}

		$newmodel =  new ControlModel($id,2);
		if($newmodel->hasCode(ucfirst($code)))
		{
			$result['error'] ="重复添加！";
			Util::jsonExit($result);
		}
		$olddo = $newmodel->getDataObject();

		if($olddo['type']!=$type){
			$res = $newmodel->getSonObj($id);
			if($res!=0){
				$result['error'] ="该对象有明细对象不能修改对象类型！";
				Util::jsonExit($result);
			}

		}
		if($type==3){
			if(!$parent_id){
				$result['error'] ="明细对象必须有一个主对象！";
				Util::jsonExit($result);
			}

		}

		$newdo=array(
			'code'=>ucfirst($code),
			'id'=>$id,
			'label'=>$label,
			'application_id'=>$application_id,
			'type'=>$type,
			'parent_id'=>$parent_id,
		);

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$newmodel->makePermission(array('id'=>$id,'name'=>$label.'-数据权限','type'=>'DATA','code'=>'OBJ_'.strtoupper(Util::parseStr($code))));
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		//删除主对象的时候要删除明细对象
		$model = new ControlModel($id,2);
		$res = $model->getSonObj($id);
		if($res!=0){
			$result['error'] = "有详细对象,禁止删除";
			Util::jsonExit($result);
		}
		$do = $model->getDataObject();
		if($do['is_system'])
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		if($model->hasRelData($id))
		{
			$result['error'] = "存在关联数据，禁止删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		if($res !== false){
			$model->deletePermission(array('id'=>$id,'type'=>'DATA'));
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
	//根据type去决定使用哪种sql语句去查询自己的关联的文件
	public function linkObj(){
		$result = array('success' => 0,'error' => '');
		$con_id = _Get::getInt('id');
		$type = _Get::getInt('type');

		$model = new ControlModel(1);
		$data = $model->getLinkObj($type,$con_id);
		$result['content'] = $this->fetch('control_link_obj.html',array('link'=>$data));
		$result['title'] = '文件-明细对象';
		Util::jsonExit($result);
	}

	/**
	 *	listButton,列表按钮排序页面
	 */
	public function listButton ()
	{
		$result = array('success' => 0,'error' => '');
		$c_id = _Post::getInt('id');
		$model = new ControlModel($c_id,1);
		$buttons = $model->getButtons($c_id);

		$result['content'] = $this->fetch('control_button_sort.html',array('buttons'=>$buttons));
		$result['title'] = '控制器列表-按钮排序';
		Util::jsonExit($result);
	}
	/**
	 *	listButton,查看按钮排序页面
	 */
	public function listButtons ()
	{
		$result = array('success' => 0,'error' => '');
		$c_id = _Post::getInt('id');
		$model = new ControlModel($c_id,1);
		$buttons = $model->getButtonss($c_id);

		$result['content'] = $this->fetch('control_button_sort.html',array('buttons'=>$buttons));
		$result['title'] = '控制器明细-按钮排序';
		Util::jsonExit($result);
	}




	public function saveSort ()
	{
		$result = array('success' => 0,'error' => '');
		$buttons = _Post::getList('buttonsArray');

		krsort($buttons);

		$buttons = array_values($buttons);

		$model = new ControlModel(1);
		$res = $model->sortButton($buttons);

		if($res)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);
	}

    //熊出没！无责任连带删除：控制器，操作，按钮关联

    public function truedelete(){

        $result = array('success' => 0,'error' => '');

        $c_id = _Request::getInt('id');

        $model = new ControlModel(1);

        $res= $model->reldelete($c_id);

        if(!$res){
            $result['error'] = "操作失败";
        }else{
            $result['success'] = 1;
        }

        Util::jsonExit($result);
    }


}

?>