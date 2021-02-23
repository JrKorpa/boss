<?php
/**
 *  -------------------------------------------------
 *   @file		: RelCatAttributeController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 19:54:21
 *   @update	:
 *  -------------------------------------------------
 */
class RelCatAttributeController extends CommonController
{
	protected $smartyDebugEnabled = true;
        
        public function __construct() {
               parent::__construct();

               //产品线
               $new_product_data= array();
               $productModel = new AppProductTypeModel(11);
               $product_data = $productModel->getCtlList();
               foreach ($product_data as $val){
                   $new_product_data[$val['product_type_id']]=$val['product_type_name'];
               }
               //var_dump($new_product_data);die;
               //获取分类名称
                $new_cat_data= array();
                $appCatModel = new AppCatTypeModel(11);
                $cat_data = $appCatModel->getCtlListon();
                foreach ($cat_data as $val){
                    $new_cat_data[$val['cat_type_id']]=$val['cat_type_name'];
                }
                //获取属性
                $new_attribute_data=array();
                $appAttributeModel = new AppAttributeModel(11);
                $attribute_data = $appAttributeModel->getCtlListon();
                foreach ($attribute_data as $val){
                    $new_attribute_data[$val['attribute_id']] = $val['attribute_name'];
                }
                $this->assign('cat_data',$new_cat_data);//数据字典
                $this->assign('attribute_data',$new_attribute_data);//数据字典
                $this->assign('product_data',$new_product_data);//数据字典
        }

        /**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('rel_cat_attribute','front',11);	//生成模型后请注释该行
		//Util::V('rel_cat_attribute',11);	//生成视图后请注释该行
               
                //获取属性
		$this->render('rel_cat_attribute_search_form.html',array('bar'=>Auth::getBar(),'view' => new BaseStyleInfoView(new BaseStyleInfoModel(11)),
            'viewproduct'=>new AppProductTypeView(new AppProductTypeModel(11)),'is_check'=>0));
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
                        'cat_type_id'=> _Request::getInt('cat_type_id'),
                        'product_type_id'=> _Request::getInt('product_type_id'),
                        'attribute_id'=> _Request::getInt('attribute_id'),
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array(
                    'cat_type_id'=> _Request::getInt('cat_type_id'),
                    'product_type_id'=> _Request::getInt('product_type_id')==1?'': _Request::getInt('product_type_id'),
                    'attribute_id'=> _Request::getInt('attribute_id'), 
                );

		$model = new RelCatAttributeModel(11);
		
		$data = $model->pageList($where,$page,50,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'rel_cat_attribute_search_page';
		$this->render('rel_cat_attribute_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('rel_cat_attribute_info.html',array(
			'view'=>new RelCatAttributeView(new RelCatAttributeModel(11)),
			'viewproduct'=>new AppProductTypeView(new AppProductTypeModel(11)),
			'viewcat'=>new AppCatTypeView(new AppCatTypeModel(11))
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
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('rel_cat_attribute_info.html',array(
			'view'=>new RelCatAttributeView(new RelCatAttributeModel($id,11)),
            'viewproduct'=>new AppProductTypeView(new AppProductTypeModel(11)),
			'viewcat'=>new AppCatTypeView(new AppCatTypeModel(11)),
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		die('开发中');
		$id = intval($params["id"]);
		$this->render('rel_cat_attribute_show.html',array(
			'view'=>new RelCatAttributeView(new RelCatAttributeModel($id,11))
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
                $cat_type_id = _Post::getInt('cat_type_id');
                $attribute_id = _Post::getInt('attribute_id');
                $product_type_id = _Post::getInt('product_type_id');
                $is_show = _Post::getInt('is_show');
                $is_default = _Post::getInt('is_default');
                $is_require = _Post::getInt('is_require');
                $info = _Post::getString('info');
                $attr_type = _Post::getInt('attr_type')?_Post::getInt('attr_type'):1;
		
                $newmodel =  new RelCatAttributeModel(12);
                $row_data = $newmodel->pageList(array('cat_type_id'=>$cat_type_id,'attribute_id'=>$attribute_id,'product_type_id'=>$product_type_id),1);
                if($row_data['data']){
                    $result['error'] = '此产品线，此分类，不能添加相同的属性！';
                    Util::jsonExit($result);
                }
                
		$olddo = array();
		$newdo=array(
                    'cat_type_id'=>$cat_type_id,
                    'attribute_id'=>$attribute_id,
                    'product_type_id'=>$product_type_id,
                    'is_show'=>$is_show,
                    'is_default'=>$is_default,
                    'is_require'=>$is_require,
                    'status'=>1,
                    'info'=>$info,
                    'attr_type'=>$attr_type,
                    'create_user'=>$_SESSION['userName'],
                    'create_time'=>date("Y-m-d H:i:s"),
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
		$id = _Post::getInt('id');
        $cat_type_id = _Post::getInt('cat_type_id');
        $attribute_id = _Post::getInt('attribute_id');
        $product_type_id = _Post::getInt('product_type_id');
        $is_show = _Post::getInt('is_show');
        $is_default = _Post::getInt('is_default');
        $is_require = _Post::getInt('is_require');
        $status = _Post::getInt('status');
        $attr_type = _Post::getInt('attr_type');
        $info = _Post::getString('info');

        $newmodel =  new RelCatAttributeModel(12);
        $row_data = $newmodel->pageList(array('cat_type_id'=>$cat_type_id,'attribute_id'=>$attribute_id,'product_type_id'=>$product_type_id),1);
        
        if($row_data['data']){
            if($row_data['data'][0]['rel_id']!=$id){
                $result['error'] = '此产品线，此分类，不能添加相同的属性！';
                Util::jsonExit($result);
            }
        }
		$newmodel =  new RelCatAttributeModel($id,12);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
                    'rel_id'=>$id,
                    'cat_type_id'=>$cat_type_id,
                    'attribute_id'=>$attribute_id,
                    'product_type_id'=>$product_type_id,
                    'is_show'=>$is_show,
                    'is_default'=>$is_default,
                    'is_require'=>$is_require,
                    'status'=>$status,
                    'info'=>$info,
                    'attr_type'=>$attr_type,
                    'create_user'=>$_SESSION['userName'],
                    'create_time'=>date("Y-m-d H:i:s"),
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
	 *	delete，停用
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new RelCatAttributeModel($id,12);
		$do = $model->getDataObject();
		$status = $do['status'];
        if($status == 0){
            $result['error'] = "此条数据已经停用";
            Util::jsonExit($result);
        }
		$model->setValue('status',0);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
    
	/**
	 *	delete，启用
	 */
	public function enable ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new RelCatAttributeModel($id,12);
		$do = $model->getDataObject();
		$status = $do['status'];
        if($status == 1){
            $result['error'] = "此条数据已经启用";
            Util::jsonExit($result);
        }
		$model->setValue('status',1);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);
	}
}

?>