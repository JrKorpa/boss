<?php
/**
 *  -------------------------------------------------
 *   @file		: ExtraDiscountScopeController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-09-29 11:22:42
 *   @update	:
 *  -------------------------------------------------
 */
class ExtraDiscountScopeController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $model = new CompanyModel(1);
        $companys = $model->getCompanyTree();
        $view=new ExtraDiscountScopeView(new ExtraDiscountScopeModel(27));
		$this->render('extra_discount_scope_search_form.html',array('bar'=>Auth::getBar(),'companys'=>$companys,'view'=>$view));
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
            'style_channel'   => _Request::get("style_channel"),
            'goods_type'   => _Request::get("goods_type")
			//'参数' = _Request::get("参数");


		);
		$page = _Request::getInt("page",1);
		$where = array(
            'dep_id'=>$args['dep_id'],
            'style_channel'=>$args['style_channel'],
            'goods_type'=>$args['goods_type']
            );

		$model = new ExtraDiscountScopeModel(27);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'extra_discount_scope_search_page';
		$this->render('extra_discount_scope_search_list.html',array(
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
		$result['content'] = $this->fetch('extra_discount_scope_info.html',array(
			'view'=>new ExtraDiscountScopeView(new ExtraDiscountScopeModel(27)),
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
		$id = intval($params["id"]);
        $model = new CompanyModel(1);
        $companys = $model->getCompanyTree();
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('extra_discount_scope_info.html',array(
			'view'=>new ExtraDiscountScopeView(new ExtraDiscountScopeModel($id,27)),
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
		$this->render('extra_discount_scope_show.html',array(
			'view'=>new ExtraDiscountScopeView(new ExtraDiscountScopeModel($id,27)),
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
        $style_channel = _Post::get('style_channel');
        $goods_type = _Post::get('goods_type');
        $discount_floor = _Post::get('discount_floor');
        $discount_upper = _Post::get('discount_upper');
        $push_money = _Post::get('push_money');
        $priority = _Post::get('priority');

        $dep = explode('|', $dep_id);
        $channel = explode('|', $style_channel);
        $dep_id = $dep[0];
        $dep_name = $dep[1];
        $style_channel_id = $channel[0];
        $style_channel_name = $channel[1];

		$olddo = array();
		$newdo=array(
            'dep_id'=>$dep_id,
            'dep_name'=>$dep_name,
            'style_channel_id'=>$style_channel_id,
            'style_channel_name'=>$style_channel_name,
            'goods_type'=>$goods_type,
            'discount_floor'=>$discount_floor,
            'discount_upper'=>$discount_upper,
            'push_money'=>$push_money,
            'priority'=>$priority
            );
		$newmodel =  new ExtraDiscountScopeModel(28);
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
        $style_channel = _Post::get('style_channel');
        $goods_type = _Post::get('goods_type');
        $discount_floor = _Post::get('discount_floor');
        $discount_upper = _Post::get('discount_upper');
        $push_money = _Post::get('push_money');
        $priority = _Post::get('priority');

        $dep = explode('|', $dep_id);
        $channel = explode('|', $style_channel);
        $dep_id = $dep[0];
        $dep_name = $dep[1];
        $style_channel_id = $channel[0];
        $style_channel_name = $channel[1];

		$id = _Post::getInt('id');

		$newmodel =  new ExtraDiscountScopeModel($id,28);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
            'id'=>$id,
            'dep_id'=>$dep_id,
            'dep_name'=>$dep_name,
            'style_channel_id'=>$style_channel_id,
            'style_channel_name'=>$style_channel_name,
            'goods_type'=>$goods_type,
            'discount_floor'=>$discount_floor,
            'discount_upper'=>$discount_upper,
            'push_money'=>$push_money,
            'priority'=>$priority,
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
		$model = new ExtraDiscountScopeModel($id,28);
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