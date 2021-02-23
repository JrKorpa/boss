<?php
/**
 *  -------------------------------------------------
 *   @file		: ExtraCategoryRatioController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-09-29 11:26:14
 *   @update	:
 *  -------------------------------------------------
 */
class ExtraCategoryRatioController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $model = new CompanyModel(1);
        $companys = $model->getCompanyTree();
		$this->render('extra_category_ratio_search_form.html',array('bar'=>Auth::getBar(),'companys'=>$companys));
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
            'dep_id'   => _Request::get("dep_id"),
            'goods_type'   => _Request::get("goods_type")
			//'参数' = _Request::get("参数");


		);
		$page = _Request::getInt("page",1);
		$where = array(
            'dep_id'=>$args['dep_id'],
            'goods_type'=>$args['goods_type']
            );

		$model = new ExtraCategoryRatioModel(27);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'extra_category_ratio_search_page';
		$this->render('extra_category_ratio_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
        $model = new CompanyModel(1);
        $companys = $model->getCompanyTree();
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('extra_category_ratio_info.html',array(
			'view'=>new ExtraCategoryRatioView(new ExtraCategoryRatioModel(27)),
            'companys'=>$companys
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
        $model = new CompanyModel(1);
        $companys = $model->getCompanyTree();
		$id = intval($params["id"]);
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('extra_category_ratio_info.html',array(
			'view'=>new ExtraCategoryRatioView(new ExtraCategoryRatioModel($id,27)),
			'tab_id'=>$tab_id,
            'companys'=>$companys
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
		$this->render('extra_category_ratio_show.html',array(
			'view'=>new ExtraCategoryRatioView(new ExtraCategoryRatioModel($id,27)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
        $dep_id = _Post::get('dep_id');
        $dep = explode("|", $dep_id);
        $depid = $dep[0];
        $dep_name = $dep[1];
        $goods_type = _Post::get('goods_type');
        $discount = _Post::get('discount');
        $pull_ratio_a = _Post::get('pull_ratio_a');
        $pull_ratio_b = _Post::get('pull_ratio_b');
        $pull_ratio_c = _Post::get('pull_ratio_c');
        $pull_ratio_d = _Post::get('pull_ratio_d');
		$olddo = array();
		$newdo=array(
            'dep_id'=>$depid,
            'dep_name'=>$dep_name,
            'goods_type'=>$goods_type,
            'discount'=>$discount,
            'pull_ratio_a'=>$pull_ratio_a,
            'pull_ratio_b'=>$pull_ratio_b,
            'pull_ratio_c'=>$pull_ratio_c,
            'pull_ratio_d'=>$pull_ratio_d
            );
		$newmodel =  new ExtraCategoryRatioModel(28);
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

        $dep_id = _Post::get('dep_id');
        $dep = explode("|", $dep_id);
        $depid = $dep[0];
        $dep_name = $dep[1];
        $goods_type = _Post::get('goods_type');
        $discount = _Post::get('discount');
        $pull_ratio_a = _Post::get('pull_ratio_a');
        $pull_ratio_b = _Post::get('pull_ratio_b');
        $pull_ratio_c = _Post::get('pull_ratio_c');
        $pull_ratio_d = _Post::get('pull_ratio_d');

		$id = _Post::getInt('id');
		$newmodel =  new ExtraCategoryRatioModel($id,28);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
            'id'=>$id,
            'dep_id'=>$depid,
            'dep_name'=>$dep_name,
            'goods_type'=>$goods_type,
            'discount'=>$discount,
            'pull_ratio_a'=>$pull_ratio_a,
            'pull_ratio_b'=>$pull_ratio_b,
            'pull_ratio_c'=>$pull_ratio_c,
            'pull_ratio_d'=>$pull_ratio_d
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
		$model = new ExtraCategoryRatioModel($id,28);
		//$do = $model->getDataObject();
		//$valid = $do['is_system'];
		//if($valid)
		//{
			//$result['error'] = "当前记录为系统内置，禁止删除";
			//Util::jsonExit($result);
		//}
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
}

?>