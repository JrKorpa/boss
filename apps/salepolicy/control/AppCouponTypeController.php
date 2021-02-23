<?php
/**
 *  -------------------------------------------------
 *   @file		: AppCouponTypeController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-11 16:52:55
 *   @update	:
 *  -------------------------------------------------
 */
class AppCouponTypeController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('app_coupon_type_search_form.html',array('bar'=>Auth::getBar()));
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
			'type_name' => _Request::get("type_name")
		);

		$page = _Request::getInt("page",1);
		$where = array(
            'type_name' => $args['type_name']
        );
		$model = new AppCouponTypeModel(17);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_coupon_type_search_page';
		$this->render('app_coupon_type_search_list.html',array(
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
		$result['content'] = $this->fetch('app_coupon_type_info.html',array(
			'view'=>new AppCouponTypeView(new AppCouponTypeModel(17))
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
		$result['content'] = $this->fetch('app_coupon_type_info.html',array(
			'view'=>new AppCouponTypeView(new AppCouponTypeModel($id,17)),
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
		$this->render('app_coupon_type_show.html',array(
			'view'=>new AppCouponTypeView(new AppCouponTypeModel($id,17)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$type_name = _Request::getString('type_name');
        if($type_name == ''){
            $result['error'] = '名称不能为空';
            Util::jsonExit($result);
        }
		$olddo = array();
		$newdo=array();
        $newdo['type_name'] = $type_name;

		$newmodel =  new AppCouponTypeModel(18);
		$ret= $newmodel->getTypeName($type_name);
		if($ret&&strtolower($ret['type_name'])==strtolower($type_name)){
			$result['error'] = "优惠券名称已经存在!";
			Util::jsonExit($result);
		}
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
		
		$id = _Post::getInt('id');
		$type_name = _Request::getString('type_name');
        if($type_name == ''){
            $result['error'] = '名称不能为空';
            Util::jsonExit($result);
        }
		$newdo=array();
        $newdo['id'] = $id;
        $newdo['type_name'] = $type_name;

		$newmodel =  new AppCouponTypeModel(18);
		$ret= $newmodel->getTypeName($type_name);
        if(!empty($ret)){
            if($ret['id']!=$id){
                if($ret&&strtolower($ret['type_name'])==strtolower($type_name)){
                    $result['error'] = "优惠券名称已经存在!";
                    Util::jsonExit($result);
                }
            }
        }

		$newmodel =  new AppCouponTypeModel($id,18);
        $olddo = $newmodel->getDataObject();
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
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
		$model = new AppCouponTypeModel($id,18);
		
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