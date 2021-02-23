<?php
/**
 *  -------------------------------------------------
 *   @file		: DiamondInfoLogController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-17 18:23:12
 *   @update	:
 *  -------------------------------------------------
 */
class AppDiamondLogController extends CommonController
{
	protected $smartyDebugEnabled = false;

    public function __construct() {
        parent::__construct();
        
        $diamondview = new AppDiamondColorView(new AppDiamondColorModel(19));
        $jiajialvview = new AppDiamondJiajialvView(new AppDiamondJiajialvModel(19));	//获得DiamondJiajialvView和DiamondJiajialvModel对象的属性和方法，在模板页可以调用
        
        $this->assign('jiajialvview', $jiajialvview);
        $this->assign('diamondview', $diamondview);
    }
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $this->render('diamond_info_log_search_form.html',array('bar'=>Auth::getBar()));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		
// 		echo "<pre>";
// 		print_r($params);exit;
		//$params = array('mod'=>diamond,'con'=>diamondInfoLog,'act'=>search);
         $jiajialvview = new AppDiamondJiajialvView(new AppDiamondJiajialvModel(19));
        
        $this->assign('jiajialvview', $jiajialvview);
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
            'operation_type'=> _Request::getInt('operation_type'),
            'create_user'=> _Request::getString('create_user'),
            'start_time'=> _Request::getString('start_time'),
            'end_time'=> _Request::getString('end_time'),
            'from_ad'=> _Request::getInt('from_ad'),

		);
		$page = _Request::getInt("page",1);
		$where = array(
            'operation_type'=> _Request::getInt('operation_type'),
            'create_user'=> _Request::getString('create_user'),
            'start_time'=> _Request::getString('start_time'),
            'end_time'=> _Request::getString('end_time'),
            'from_ad'=> _Request::getInt('from_ad'),
        );
		
		$model = new AppDiamondLogModel(19);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'diamond_info_log_search_page';
		$this->render('diamond_info_log_search_list.html',array(
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
		$result['content'] = $this->fetch('diamond_info_log_info.html',array(
			'view'=>new DiamondInfoLogView(new DiamondInfoLogModel(19))
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
		$tab_id = intval($params["tab_id"]);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('diamond_info_log_info.html',array(
			'view'=>new DiamondInfoLogView(new DiamondInfoLogModel($id,19)),
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
		$this->render('diamond_info_log_show.html',array(
			'view'=>new DiamondInfoLogView(new DiamondInfoLogModel($id,19)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$olddo = array();
		$newdo=array(
                    'from_ad'=>$params['from_ad'],
                    'operation_type'=>$params['operation_type'],
                    'operation_content'=>$params['operation_content'],
                    'create_time'=>date("Y-m-d H:i:s"),
                    'create_user'=>$_SESSION['userName'],
                );

		$newmodel =  new DiamondInfoLogModel(20);
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

		$newmodel =  new DiamondInfoLogModel($id,20);

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
		$model = new DiamondInfoLogModel($id,20);
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
}

?>