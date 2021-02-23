<?php
/**
 *  -------------------------------------------------
 *   @file		: AppSalepolicyTogetherGoodsController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-14 18:55:25
 *   @update	:
 *  -------------------------------------------------
 */
class AppSalepolicyTogetherGoodsController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('app_salepolicy_together_goods_search_form.html',array('bar'=>Auth::getBar()));
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
			'policy_id'    => _Request::getInt("id")


		);
		$page = _Request::getInt("page",1);
		$where = array('policy_id'=>$args['policy_id']);

		$model = new AppSalepolicyTogetherGoodsModel(17);
		if($where['policy_id'] != ''){
            $data = $model->pageTogetherList($where,$page,10,false);
        }else{
            $data = $model->pageList($where,$page,10,false);
        }
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_salepolicy_together_goods_search_page';
		$this->render('app_salepolicy_together_goods_search_list.html',array(
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
		$result['content'] = $this->fetch('app_salepolicy_together_goods_info.html',array(
			'view'=>new AppSalepolicyTogetherGoodsView(new AppSalepolicyTogetherGoodsModel(17))
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
		$result['content'] = $this->fetch('app_salepolicy_together_goods_info.html',array(
			'view'=>new AppSalepolicyTogetherGoodsView(new AppSalepolicyTogetherGoodsModel($id,17)),
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
		$this->render('app_salepolicy_together_goods_show.html',array(
			'view'=>new AppSalepolicyTogetherGoodsView(new AppSalepolicyTogetherGoodsModel($id,17)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
        $name = _Post::getString('name');
        $is_split = _Post::getInt('is_split');
        if(empty($name)){
            $result['error'] = '策略名称不能为空';
            Util::jsonExit($result);
        }
        if(strlen($name)>30){
            $result['error'] = '策略名称长度过长';
            Util::jsonExit($result);
        }
        $model = new AppSalepolicyTogetherGoodsModel(17);
        $oldname = $model->getTogetherName($name);
        if($oldname > 0) {
            $result['error'] = '策略名称不可重复添加！';
            Util::jsonExit($result);
        }
		$olddo = array();
		$newdo=array(
            'together_name'=>$name,
            'is_split'=>$is_split,
            'create_user'=>$_SESSION['userName'],
            'create_time'=>date("Y-m-d H:i:s"),
        );

		$newmodel =  new AppSalepolicyTogetherGoodsModel(18);
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
		$name = _Post::getString('name');
        $is_split = _Post::getInt('is_split');
        if(empty($name)){
            $result['error'] = '策略名称不能为空';
            Util::jsonExit($result);
        }
        if(strlen($name)>30){
            $result['error'] = '策略名称长度过长';
            Util::jsonExit($result);
        }
		$newmodel =  new AppSalepolicyTogetherGoodsModel($id,18);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
            'id'=>$id,
            'together_name'=>$name,
            'is_split'=>$is_split,
        );

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
     * 
     * @param type $param
     */
    public function bindSale($param) {
        $result = array('success' => 0, 'error' => '');
        $ids = _Post::getList('_ids');
       
        $model = new BaseSalepolicyInfoModel(17);
        $policyList = $model->getPolicyList();
        $result['content'] = $this->fetch('base_salepolicy_batch_goods.html', array(
            'ids' => implode(',', $ids),
            'policy_list' => $policyList
        ));
        $result['title'] = '批量添加销售政策商品';
        Util::jsonExit($result);
    }
    

	/**
	 *	enable，启用
	 */
	public function enable ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppSalepolicyTogetherGoodsModel($id,18);
		$do = $model->getDataObject();
		if($do['status']==1)
		{
			$result['error'] = "已是启用状态";
			Util::jsonExit($result);
		}
		$model->setValue('status',1);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "启用失败";
		}
		Util::jsonExit($result);
	}
    
    
	/**
	 *	cancel，启用
	 */
	public function cancel ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppSalepolicyTogetherGoodsModel($id,18);
		$do = $model->getDataObject();
		if($do['status']==2)
		{
			$result['error'] = "已是停用状态";
			Util::jsonExit($result);
		}
		$model->setValue('status',2);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "停用失败";
		}
		Util::jsonExit($result);
	}
}

?>