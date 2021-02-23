<?php
/**
 *  -------------------------------------------------
 *   @file		: AppDiamondTiaojiaController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-05-10 17:32:48
 *   @update	:
 *  -------------------------------------------------
 */
class AppDiamondTiaojiaController extends CommonController
{
	protected $smartyDebugEnabled = false;

    public function __construct() {
        parent::__construct();

		$model = new AppDiamondTiaojiaModel(19);
		$tiaojia = $model->getAllList();

		$this->assign("tiaojia",$tiaojia);
    }

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('app_diamond_tiaojia_search_form.html',array('bar'=>Auth::getBar()));
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


		);
		$page = _Request::getInt("page",1);
		$where = array();

		$model = new AppDiamondTiaojiaModel(19);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_diamond_tiaojia_search_page';
		$this->render('app_diamond_tiaojia_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
            'bar'=>Auth::getBar()
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_diamond_tiaojia_info.html',array(
			'view'=>new AppDiamondTiaojiaView(new AppDiamondTiaojiaModel(19))
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
		$result['content'] = $this->fetch('app_diamond_tiaojia_info.html',array(
			'view'=>new AppDiamondTiaojiaView(new AppDiamondTiaojiaModel($id,19)),
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
		$this->render('app_diamond_tiaojia_show.html',array(
			'view'=>new AppDiamondTiaojiaView(new AppDiamondTiaojiaModel($id,19)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;
		$olddo = array();
		$newdo=array();

		$newmodel =  new AppDiamondTiaojiaModel(20);
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

		$newmodel =  new AppDiamondTiaojiaModel($id,20);

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
		$model = new AppDiamondTiaojiaModel($id,20);
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
	 *	up，更新信息
	 */
	public function up ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');
		$newmodel =  new AppDiamondTiaojiaModel(1,20);

		$olddo = $newmodel->getDataObject();

        $tiao = array();
        $tiao['id'] = 1;
        $tiao['address1'] = _Request::getString('address1');
        $tiao['address2'] = _Request::getString('address2');
        $tiao['address3'] = _Request::getString('address3');
        $tiao['zhekou_31'] =  _Request::getString('zhekou_31');
        $tiao['zhekou_32'] = _Request::getString('zhekou_32');
        $tiao['zhekou_33'] = _Request::getString('zhekou_33');
        $tiao['zhekou_51'] = _Request::getString('zhekou_51');
        $tiao['zhekou_52'] = _Request::getString('zhekou_52');
        $tiao['zhekou_53'] = _Request::getString('zhekou_53');
        $tiao['zhekou_81'] = _Request::getString('zhekou_81');
        $tiao['zhekou_82'] = _Request::getString('zhekou_82');
        $tiao['zhekou_83'] = _Request::getString('zhekou_83');
        $tiao['zhekou_01'] = _Request::getString('zhekou_01');
        $tiao['zhekou_02'] = _Request::getString('zhekou_02');
        $tiao['zhekou_03'] = _Request::getString('zhekou_03');
        $tiao['zhekou_11'] = _Request::getString('zhekou_11');
        $tiao['zhekou_12'] = _Request::getString('zhekou_12');
        $tiao['zhekou_13'] = _Request::getString('zhekou_13');
        $tiao['zhekou_21'] = _Request::getString('zhekou_21');
        $tiao['zhekou_22'] = _Request::getString('zhekou_22');
        $tiao['zhekou_23'] = _Request::getString('zhekou_23');

        $ChannelsModel=new SalesChannelsModel(1);
        $add1 = explode(',',$tiao['address1']);
        foreach ($add1 as $key => $val ) {
            $dep1 =$ChannelsModel->getChannelByChannel_Name($val);
            foreach ($dep1 as $k => $v ) {
                $ids1[] = $v['id'];
            }
        }
        
        $tiao['dep_id1'] = implode(',',$ids1);

        $add2 = explode(',',$tiao['address2']);
        foreach ($add2 as $key => $val ) {
            $dep2 =$ChannelsModel->getChannelByChannel_Name($val);
            foreach ($dep2 as $k => $v ) {
                $ids2[] = $v['id'];
            }
        }
        $tiao['dep_id2'] = implode(',',$ids2);

        $add3 = explode(',',$tiao['address3']);
        foreach ($add3 as $key => $val ) {
            if ($val=='其他') {
                $ads = $tiao['dep_id1'].",".$tiao['dep_id2'];
                $dep3 =$ChannelsModel->getChannelById($ads);
            }else {
                $dep3 =$ChannelsModel->getChannelByChannel_Name($val);
            }
            
            foreach ($dep3 as $k => $v ) {
                $ids3[] = $v['id'];
            }

        }
        $tiao['dep_id3'] = implode(',',$ids3);

		$res = $newmodel->saveData($tiao,$olddo);
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
}

?>