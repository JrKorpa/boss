<?php
/**
 *  -------------------------------------------------
 *   @file		: ListStyleGoodsController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-26 12:26:50
 *   @update	:
 *  -------------------------------------------------
 */
class ListStyleGoodsCreatController extends CommonController
{
	protected $smartyDebugEnabled = false;
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
                $cat_data = $appCatModel->getCtlList();
                foreach ($cat_data as $val){
                    $new_cat_data[$val['cat_type_id']]=$val['cat_type_name'];
                }
                //获取属性
                $new_attribute_data=array();
                $appAttributeModel = new AppAttributeModel(11);
                $attribute_data = $appAttributeModel->getCtlList();
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
		$this->render('list_style_goods_creat_search_form.html',array('bar'=>Auth::getBar()));
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

		$model = new ListStyleGoodsModel(11);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'list_style_goods_creat_search_page';
		$this->render('list_style_goods_creat_search_list.html',array(
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
		$result['content'] = $this->fetch('list_style_goods_creat_info.html',array(
			'view'=>new ListStyleGoodsView(new ListStyleGoodsModel(11))
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
		$result['content'] = $this->fetch('list_style_goods_creat_info.html',array(
			'view'=>new ListStyleGoodsView(new ListStyleGoodsModel($id,11)),
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
		$this->render('list_style_goods_creat_show.html',array(
			'view'=>new ListStyleGoodsView(new ListStyleGoodsModel($id,11)),
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

		$newmodel =  new ListStyleGoodsModel(12);
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

		$newmodel =  new ListStyleGoodsModel($id,12);

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
		$model = new ListStyleGoodsModel($id,12);
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
    
     /*
         * 生成表
         * luna
         */
		public function createStyleTable(){
			$result = array('success' => 0,'error' => '');
			$product_type_id = _Post::getInt('product_type_id');
			$cat_type_id = _Post::getInt('cat_type_id');
			if(empty($product_type_id)){
				$result['error'] = "请选择产品线";
				Util::jsonExit($result);
			}
			
			if(empty($cat_type_id)){
				$result['error'] = "请选择分类";
				Util::jsonExit($result);
			}
			/*$product_type_id = 5;
			$cat_type_id = 1;
			$style_id = 1;*/
			//获取所有属性对应的编码
			$catAttributeModel = new RelCatAttributeModel(12);
			$all_attr_data = $catAttributeModel->getCatAttrInfo(array('product_type_id'=>$product_type_id,'cat_type_id'=>$cat_type_id));
			
			$new_attr_data = array();
			foreach ($all_attr_data as $val){
				$new_attr_data[$val['attribute_id']]['attribute_code'] = $val['attribute_code'];
				$new_attr_data[$val['attribute_id']]['attribute_name'] = $val['attribute_name'];
			}
			 //var_dump($new_attr_data);
			//获取款的所有属性的id
			$styleAttributeModel= new RelStyleAttributeModel(12);
			$style_attr_data = $styleAttributeModel->getList(array('product_type_id'=>$product_type_id,'cat_type_id'=>$cat_type_id));
		   
			$new_style_attr_data = array();
			foreach ($style_attr_data as $val){
				$attr_id = $val['attribute_id'];
				$show_type = $val['show_type']; 
				if(array_key_exists($attr_id, $new_attr_data)){
					$tmp['attribute_code'] = $new_attr_data[$attr_id]['attribute_code'];
					$tmp['attribute_name'] = $new_attr_data[$attr_id]['attribute_name'];
					$tmp['show_type'] = $show_type;
					$new_style_attr_data[$attr_id] = $tmp;
				}
			}
		   $common_attr = array(
			   array('attribute_code'=>'style_id','attribute_name'=>'款式id','show_type'=>4),
			   array('attribute_code'=>'cat_type_id','attribute_name'=>'款式id','show_type'=>4),
			   array('attribute_code'=>'product_type_id','attribute_name'=>'款式id','show_type'=>4),
			   );
		   $all_attr = array_merge($common_attr,$new_style_attr_data);
		   //获取分类编码
		  $appCatTypeModel = new AppCatTypeModel(11);
		  $catCode = $appCatTypeModel->getCatCode($cat_type_id);
		  //获取产品线编码
		  $appProductModel = new AppProductTypeModel(11);
		  $productCode = $appProductModel->getProductCode($product_type_id);
		  $table_name = 'list_'.$catCode.'_'.$productCode;
		  $res = $catAttributeModel->createTable ($table_name, $all_attr);
		  
		  if($res==TRUE){
				$result['error'] = "操作成功";
				Util::jsonExit($result);
		  }else{
			   $result['error'] = "表已经存在";
			   Util::jsonExit($result);
		  }
		}
        
        /*
         * 写入款对属性的表数据
         * luna
         */
		public function writeStyleTable(){
			$result = array('success' => 0,'error' => '');
			$product_type_id = _Post::getInt('product_type_id');
			$cat_type_id = _Post::getInt('cat_type_id');
			
			if(empty($product_type_id)){
				$result['error'] = "请选择产品线";
				Util::jsonExit($result);
			}
			
			if(empty($cat_type_id)){
				$result['error'] = "请选择分类";
				Util::jsonExit($result);
			}
			/*$product_type_id = 5;
			$cat_type_id = 1;
			$style_id = 1;*/
			//获取所有属性对应的编码
			$catAttributeModel = new RelCatAttributeModel(12);
			$all_attr_data = $catAttributeModel->getCatAttrInfo(array('product_type_id'=>$product_type_id,'cat_type_id'=>$cat_type_id));
			
			$new_attr_data = array();
			foreach ($all_attr_data as $val){
				$new_attr_data[$val['attribute_id']]['attribute_code'] = $val['attribute_code'];
				$new_attr_data[$val['attribute_id']]['attribute_name'] = $val['attribute_name'];
			}
			 //var_dump($new_attr_data);die;
			//获取款的所有属性的id
			$styleAttributeModel= new RelStyleAttributeModel(12);
			$style_attr_data = $styleAttributeModel->getList(array('product_type_id'=>$product_type_id,'cat_type_id'=>$cat_type_id));
			if(empty($style_attr_data)){
				$result['error'] = "此产品线，此分类下还没有数据，请添加数据在写入数据！";
				Util::jsonExit($result);
			}
			
		   $new_style_attr_data = array();
			//新加
		  foreach ($style_attr_data as $key=>$val){
				$style_id = $val['style_id'];
				$attr_id = $val['attribute_id'];
				$cat_id = $val['cat_type_id'];
				$product_id = $val['product_type_id'];
				$scp_id = $style_id.'_'.$cat_id.'_'.$product_id;
				if(array_key_exists($attr_id, $new_attr_data)){
					$new_style_attr_data[$scp_id][$key]['attribute_id'] = $new_attr_data[$attr_id]['attribute_code'];
					$code = $new_attr_data[$attr_id]['attribute_code'];
					$attr_value = $style_attr_data[$key]['attribute_value'];
					
					$new_style_attr_data[$scp_id][$key] = $val;
					$new_style_attr_data[$scp_id][$key][$code] = $attr_value;
					$attribute_code_arr[] = $new_attr_data[$attr_id]['attribute_code'];
				}
		  }
			//$new_style_attr_data
		  //  var_dump($new_style_attr_data);die;
		  //获取分类编码
		  $appCatTypeModel = new AppCatTypeModel(11);
		  $catCode = $appCatTypeModel->getCatCode($cat_type_id);
		  //获取产品线编码
		  $appProductModel = new AppProductTypeModel(11);
		  $productCode = $appProductModel->getProductCode($product_type_id);
		  $table_name = 'list_'.$catCode.'_'.$productCode;
			//获取所有的字段
		  $res = $catAttributeModel->inseartStyleList($table_name,$attribute_code_arr ,$new_style_attr_data) ;
		  if($res){
			   $result['error'] = "操作成功";
				 Util::jsonExit($result);
		  }else{
			   $result['error'] = "操作失败";
				Util::jsonExit($result);
		  }
		}
}

?>