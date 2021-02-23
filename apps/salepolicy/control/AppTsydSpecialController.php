<?php
/**
 *  -------------------------------------------------
 *   @file		: AppTsydSpecialController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-08-25 17:30:09
 *   @update	:
 *  -------------------------------------------------
 */
class AppTsydSpecialController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('app_tsyd_special_search_form.html',array('bar'=>Auth::getBar()));
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
            'style_sn'   => _Request::getString("style_sn"),
			//'参数' = _Request::get("参数");


		);
		$page = _Request::getInt("page",1);
		$where = array(
            'style_sn'=>$args['style_sn']
            );

		$model = new AppTsydSpecialModel(17);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_tsyd_special_search_page';
		$this->render('app_tsyd_special_search_list.html',array(
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
		$result['content'] = $this->fetch('app_tsyd_special_info.html',array(
			'view'=>new AppTsydSpecialView(new AppTsydSpecialModel(17))
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
		$result['content'] = $this->fetch('app_tsyd_special_info.html',array(
			'view'=>new AppTsydSpecialView(new AppTsydSpecialModel($id,17)),
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
		$this->render('app_tsyd_special_show.html',array(
			'view'=>new AppTsydSpecialView(new AppTsydSpecialModel($id,17)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
        $style_sn = _Post::getString('style_sn');
        $newmodel =  new AppTsydSpecialModel(18);
        if(empty($style_sn)){
            $result['error'] = '亲~ 请输入款号！';
            Util::jsonExit($result);
        }
        $checkIn = $newmodel->checkStyle_sn($style_sn);
        if(empty($checkIn)){
            $result['error'] = '亲~ 输入的款号在款式库不存在或未审核！';
            Util::jsonExit($result);
        }
        $checkInTodo = $newmodel->checkStyle_snTodo($style_sn);
        if(!empty($checkInTodo)){
            $result['error'] = '亲~ 输入的款号已存在！';
            Util::jsonExit($result);
        }
		$olddo = array();
		$newdo=array(
            'style_sn'=>$style_sn,
            'status'=>1,
            'style_name'=>$checkIn['style_name'],
            'add_user'=>$_SESSION['userName'],
            'add_time'=>date('Y-m-d H:i:s')
            );
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

	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');

		$id = _Post::getInt('id');
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;

		$newmodel =  new AppTsydSpecialModel($id,18);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
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

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppTsydSpecialModel($id,18);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		//联合删除？
		//$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

    /**
     *  open，启用
     */
    public function open ($params)
    {
        $result = array('success' => 0,'error' => '');
        $id = intval($params['id']);
        $model = new AppTsydSpecialModel($id,18);
        $do = $model->getDataObject();
        $valid = $do['status'];
        if($valid == 1)
        {
            $result['error'] = "当前记录已启用";
            Util::jsonExit($result);
        }
        $model->setValue('status',1);
        $res = $model->save(true);
        //联合删除？
        //$res = $model->delete();
        if($res !== false){
            $result['success'] = 1;
        }else{
            $result['error'] = "启用失败";
        }
        Util::jsonExit($result);
    }

    /**
     *  close，禁用
     */
    public function close ($params)
    {
        $result = array('success' => 0,'error' => '');
        $id = intval($params['id']);
        $model = new AppTsydSpecialModel($id,18);
        $do = $model->getDataObject();
        $valid = $do['status'];
        if($valid == 2)
        {
            $result['error'] = "当前记录已禁用";
            Util::jsonExit($result);
        }
        $model->setValue('status',2);
        $model->setValue('ban_user',Auth::$userName);
        $model->setValue('ban_time',date('Y-m-d H:i:s'));
        $res = $model->save(true);
        //联合删除？
        //$res = $model->delete();
        if($res !== false){
            $result['success'] = 1;
        }else{
            $result['error'] = "禁用失败";
        }
        Util::jsonExit($result);
    }
}

?>