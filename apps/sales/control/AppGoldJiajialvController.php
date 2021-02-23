<?php
/**
 *  -------------------------------------------------
 *   @file		: AppGoldJiajialvController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-08-17 13:57:53
 *   @update	:
 *  -------------------------------------------------
 */
class AppGoldJiajialvController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('app_gold_jiajialv_search_form.html',array('bar'=>Auth::getBar()));
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
            'create_user'   => _Request::get("create_user"),
            'is_usable'   => _Request::get("is_usable")
			//'参数' = _Request::get("参数");


		);
		$page = _Request::getInt("page",1);
		$where = array(
            'is_usable'=>$args['is_usable'],
            'create_user' => $args['create_user']
            );
		$model = new AppGoldJiajialvModel(27);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_gold_jiajialv_search_page';
		$this->render('app_gold_jiajialv_search_list.html',array(
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
        $create_time = date('Y-m-d H:i:s');
        $create_user = $_SESSION['userName'];
		$result['content'] = $this->fetch('app_gold_jiajialv_info.html',array(
			'view'=>new AppGoldJiajialvView(new AppGoldJiajialvModel(27)),
            'create_time'=>$create_time,
            'create_user'=>$create_user
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
		$result['content'] = $this->fetch('app_gold_jiajialv_info.html',array(
			'view'=>new AppGoldJiajialvView(new AppGoldJiajialvModel($id,27)),
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
		$this->render('app_gold_jiajialv_show.html',array(
			'view'=>new AppGoldJiajialvView(new AppGoldJiajialvModel($id,27)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
        $gold_price = _Post::getFloat('gold_price');
        $jiajialv = _Post::getFloat('jiajialv');
		$olddo = array();
		$newdo=array(
            'gold_price'=>$gold_price,
            'jiajialv' => $jiajialv,
            'create_time'=>date('Y-m-d H:i:s'),
            'create_user'=>$_SESSION['userName'],
            'is_usable'=>1
            );
		$newmodel =  new AppGoldJiajialvModel(28);
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

		$newmodel =  new AppGoldJiajialvModel($id,28);

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
	 *	delete，启用
	 */
	public function enabled ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppGoldJiajialvModel($id,28);
		$do = $model->getDataObject();
		$valid = $do['is_usable'];
		if($valid == 1)
		{
			$result['error'] = "当前记录已启用";
			Util::jsonExit($result);
		}
		$model->setValue('is_usable',1);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "启用失败";
		}
		Util::jsonExit($result);
	}

    /**
     *  delete，警用
     */
    public function disabled ($params)
    {
        $result = array('success' => 0,'error' => '');
        $id = intval($params['id']);
        $model = new AppGoldJiajialvModel($id,28);
        $do = $model->getDataObject();
        $valid = $do['is_usable'];
        if($valid == 0)
        {
            $result['error'] = "当前记录已禁用";
            Util::jsonExit($result);
        }
        $model->setValue('is_usable',0);
        $res = $model->save(true);
        if($res !== false){
            $result['success'] = 1;
        }else{
            $result['error'] = "禁用失败";
        }
        Util::jsonExit($result);
    }
}

?>