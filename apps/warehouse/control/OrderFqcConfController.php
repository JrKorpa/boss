<?php
/**
 *  -------------------------------------------------
 *   @file		: OrderFqcConfController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-02 12:12:53
 *   @update	:
 *  -------------------------------------------------
 */
class OrderFqcConfController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('order_fqc_conf_search_form.html',array('bar'=>Auth::getBar()));
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

		$model = new OrderFqcConfModel(21);
		$data = $model->pageList($where,$page,20,false);
		//var_dump($data,77);exit;
		
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'order_fqc_conf_search_page';
		$this->render('order_fqc_conf_search_list.html',array(
			'page_list'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{		
		$result = array('success' => 0,'error' => '');
		$arr['arr'][] = array('goods_id' => 888);
		$arr['arr'][] = array('goods_id' => 999);
		/** 获取顶级导航列表**/
		$model = new OrderFqcConfModel(21);
		$data = $model->get_top_menu();
		//var_dump($data);exit;
		$result['content'] = $this->fetch('order_fqc_conf_info.html',array(
			'view'=>new OrderFqcConfView(new OrderFqcConfModel(21)),'menu_list'=>$data,
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
		$model = new OrderFqcConfModel(21);
		$data = $model->get_top_menu();
		$result['content'] = $this->fetch('order_fqc_conf_info.html',array(
			'view'=>new OrderFqcConfView(new OrderFqcConfModel($id,21)),
			'tab_id'=>$tab_id,'menu_list'=>$data,
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
		$this->render('order_fqc_conf_show.html',array(
			'view'=>new OrderFqcConfView(new OrderFqcConfModel($id,21)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$cat_name = _Post::get('cat_name');
		$parent_id = _Post::get('parent_id');
		$id = _Post::get('id');
		$olddo = array();
		$newdo=array(
				"cat_name"=>$cat_name,
				"parent_id"=>$parent_id
		);

		$newmodel =  new OrderFqcConfModel(22);
		$has = $newmodel->if_has_childrens($id,$parent_id,$cat_name);
		if (!empty($has)){
			$result['error'] = "已经存在该分类名字";
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
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');

		$id = _Post::getInt('id');
		
		$cat_name = _Post::get('cat_name');
		$parent_id = _Post::get('parent_id');
		//echo '<pre>';
		//print_r ($_POST);
		//echo '</pre>';
		//exit;

		$newmodel =  new OrderFqcConfModel($id,22);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
				"id"=>$id,
				"cat_name"=>$cat_name,
				"parent_id"=>$parent_id
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
                $parent_id = _Post::getInt("parent_id");
		//echo $id;exit;
		$model = new OrderFqcConfModel($id,22);
		$do = $model->getDataObject();
                //如果下面有子类则不能删除
                if($parent_id == 0) {
                    $has = $model->if_has_childrens($id, $parent_id);
                    if ($has){
                        $result['error'] = "该分类下面有子类，不能删除！";
                        Util::jsonExit($result);
                    }
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