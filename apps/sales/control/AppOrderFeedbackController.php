<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderFeedbackController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2016-01-26 10:30:32
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderFeedbackController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('app_order_feedback_search_form.html',array('bar'=>Auth::getBar()));
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
			'ks_user' => _Request::getString("ks_user")

		);
		$page = _Request::getInt("page",1);
		$where = array(

            'ks_user'=>$args['ks_user']
            );

		$model = new AppOrderFeedbackModel(27);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_order_feedback_search_page';
		$this->render('app_order_feedback_search_list.html',array(
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
		$result['content'] = $this->fetch('app_order_feedback_info.html',array(
			'view'=>new AppOrderFeedbackView(new AppOrderFeedbackModel(27))
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
		$result['content'] = $this->fetch('app_order_feedback_info.html',array(
			'view'=>new AppOrderFeedbackView(new AppOrderFeedbackModel($id,27)),
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
		$this->render('app_order_feedback_show.html',array(
			'view'=>new AppOrderFeedbackView(new AppOrderFeedbackModel($id,27)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');

        $ks_option = _Post::getString('ks_option');

        if($ks_option == '')
        {
            $result['error'] = '<span style="color:red">客诉原因不能为空！</span>';
            Util::jsonExit($result);
        }

		$olddo = array();
		$newdo=array(
            'ks_option'=>$ks_option,
            'ks_user'=>$_SESSION['userName'],
            'ks_time'=>date('Y-m-d H:i:s'),
            'ks_status'=>1
            );

		$newmodel =  new AppOrderFeedbackModel(28);
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
        $ks_option = _Post::getString('ks_option');

        if($ks_option == '')
        {
            $result['error'] = '<span style="color:red">客诉原因不能为空！</span>';
            Util::jsonExit($result);
        }

		$newmodel =  new AppOrderFeedbackModel($id,28);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
            'id'=>$id,
            'ks_option'=>$ks_option,
            'ks_user'=>$_SESSION['userName'],
            'ks_time'=>date('Y-m-d H:i:s'),
            'ks_status'=>1
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
		$model = new AppOrderFeedbackModel($id,28);
		//$do = $model->getDataObject();
		/*$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}*/
		//$model->setValue('is_deleted',1);
		//$res = $model->save(true);
		//联合删除？
		$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

    /**
     *  start_using，启用
     */
    public function start_using ($params)
    {
        $result = array('success' => 0,'error' => '');
        $id = intval($params['id']);
        $model = new AppOrderFeedbackModel($id,28);
        $do = $model->getDataObject();
        $ks_status = $do['ks_status'];
        if($ks_status == 1)
        {
            $result['error'] = "亲，已经启用了！";
            Util::jsonExit($result);
        }
        $model->setValue('ks_status',1);
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
     *  forbidden，禁用
     */
    public function forbidden ($params)
    {
        $result = array('success' => 0,'error' => '');
        $id = intval($params['id']);
        $model = new AppOrderFeedbackModel($id,28);
        $do = $model->getDataObject();
        $ks_status = $do['ks_status'];
        if($ks_status == 2)
        {
            $result['error'] = "亲，已经禁用了！";
            Util::jsonExit($result);
        }
        $model->setValue('ks_status',2);
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
}

?>