<?php
/**
 *  -------------------------------------------------
 *   @file		: RelStyleLoversController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-20 17:37:07
 *   @update	:
 *  -------------------------------------------------
 */
class RelStyleLoversController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $model= new AppXilieModel(11);
        $xilie=$model->getAllXilieName();
         //产品线
        $new_product_data= array();
        $productModel = new AppProductTypeModel(11);
        $product_data = $productModel->getCtlList();
        foreach ($product_data as $val){
            $new_product_data[$val['product_type_id']]=$val['product_type_name'];
        }
        $this->assign('product_data',$new_product_data);//数据字典
		$this->render('rel_style_lovers_search_form.html',array(
		'bar'=>Auth::getBar(),
        'xilie'=>$xilie,
		'view' => new BaseStyleInfoView(new BaseStyleInfoModel(11)),
		'viewproduct'=>new AppProductTypeView(new AppProductTypeModel(11)),
		'viewcat'=>new AppCatTypeView(new AppCatTypeModel(11)),
		'is_check'=>0
		));
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
			'style_sn' => _Request::getString("style_sn"),
            'product_type_id' => _Request::getInt('product_type_id'),
            'cat_type_id' => _Request::getInt('cat_type_id'),
            'check_status' => _Request::getInt('check_status'),
            'dismantle_status' => _Request::getInt('dismantle_status'),
            'style_sex' => _Request::getInt('style_sex'),
            'style_name' => _Request::getString('style_name'),
            'factory_sn' => _Request::getString('factory_sn'),
            'xilie[]' => _Request::getList('xilie'),
            'xiangkou' => _Request::getString('xiangkou'),
            'is_made' => _Request::get('is_made')
		);
		$page = _Request::getInt("page",1);
		//款号 批量搜索
        $style_sn = '';
        $product_type = '';
        if($args['style_sn']){
            //若 款号中间存在空格 汉字逗号 替换为英文模式逗号
            $args['style_sn'] = str_replace(' ',',',$args['style_sn']);
            $args['style_sn'] = str_replace('，',',',$args['style_sn']);
            $tmp = explode(",", $args['style_sn']);
            foreach($tmp as $val){
                $style_sn .= "'$val',";
            }
            $style_sn = rtrim($style_sn,',');
        }
		$app_type = new AppProductTypeModel(11);
		if($args['product_type_id']!=''){
			$res = $app_type->get_Product_type_id(array('product_type_id'=>$args['product_type_id']));
			foreach($res as $k=>$val){	 
				$ret[$k]=$val['parent_id'];
			}
			$parent =implode(',',$ret);
			if($parent==1){
				$res = $app_type->get_Product_type_id(array('parent_id'=>$args['product_type_id']));
				foreach($res as $k=>$val){	 
					$ret[$k]=$val['product_type_id'];
				}
				$product_type = implode(',',$ret).','.$args['product_type_id'];
			}else{
				$product_type = $args['product_type_id'];
			}

		}
        $where['style_sn_in'] = $style_sn;
        $where['product_type_id'] = $product_type;
        $where['check_status'] = $args['check_status'];
        $where['dismantle_status'] = $args['dismantle_status'];
        $where['style_sex'] = $args['style_sex'];
        $where['is_made'] = $args['is_made'];
        $where['style_name'] = $args['style_name'];
        $where['xilie'] = _Request::getList('xilie');
        $where['xiangkou'] = $args['xiangkou'];//
        $where['check_status_zuofei'] = 7;
        
        $baseStyleModel = new BaseStyleInfoModel(11);
                
		$model = new RelStyleLoversModel(11);
        $supplier_model = new RelStyleFactoryModel(11);
        $appley_model = new RelStyleFactoryModel(11);
        $gallery_model = new AppStyleGalleryModel(11);
        $_newModel = new ApiProcessorModel();
        if($args['factory_sn'] != ''){
            $styleinfo = $supplier_model->getStyleInfoByFactorySn($args['factory_sn']);
            if($styleinfo){
                $str = '';
                foreach ($styleinfo as $k => $v) {
                    $str.="'".$v['style_sn']."',";
                }
                $stylesn = trim($str,',');
                $where['style_sn_in'] = $stylesn;
            }
        }
        $data = $model->pageList($where, $page, null, false);

        
        if ($data) {
            foreach ($data['data'] as &$val) {
            	$style_type = $baseStyleModel->getStyleTypeList($val['style_type']);
				if($style_type){
					$val['style_type'] = isset($style_type['cat_type_name']) ?$style_type['cat_type_name']:"";
				}else{
					$val['style_type'] = '';
				}
				$product_type = $baseStyleModel->getProductTypeList($val['product_type']);
				if($product_type){
					$val['product_type'] = isset($product_type['product_type_name'])?$product_type['product_type_name']:'';
				}else{
					$val['product_type'] = '';
				}
            }
         
            $res = array();
            foreach ($data['data'] as $k => $v) {

                $con['style_id'] = $v['style_id'];
                $con['style_sn'] = $v['style_sn'];
                $res = $appley_model->getStyleFactoryInfo($con);
                $img = $gallery_model->getStyleGalleryInfo($con);
                $data['data'][$k]['thumb_img'] = '';
                $data['data'][$k]['big_img'] = '';
                if(!empty($img)){
                    $data['data'][$k]['thumb_img'] = $img['thumb_img'];//45°图片
                    $data['data'][$k]['big_img'] = $img['big_img'];//datu
                }
                $data['data'][$k]['factory_info'] = '';
                if(!empty($res)){
                    foreach ($res as $key => $value) {
                        $factory_name = '';
                        $factory_id = isset($value['factory_id'])?$value['factory_id']:'';
                        if($factory_id != ''){
                            $name = '';
                            $name = $_newModel->GetSupplierListName($value['factory_id']);
                            $factory_name = isset($name['data'])?$name['data']:'';
                        }
                        $factory_sn = isset($value['factory_sn'])?$value['factory_sn']:'';
                        $xiangkou = isset($value['xiangkou'])?$value['xiangkou']:'';
                        $factory_fee = isset($value['factory_fee'])?$value['factory_fee']:'';
                        $data['data'][$k]['factory_info'] .= $factory_name.':'.$factory_sn.':'.$xiangkou.':'.$factory_fee.';<br />';//工厂模号信息
                    }
                }
            }
        }
        
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'rel_style_lovers_search_page';
		$this->render('rel_style_lovers_search_list.html',array(
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
		$result['content'] = $this->fetch('rel_style_lovers_info.html',array(
			'view'=>new RelStyleLoversView(new RelStyleLoversModel(11))
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
		$result['content'] = $this->fetch('rel_style_lovers_info.html',array(
			'view'=>new RelStyleLoversView(new RelStyleLoversModel($id,11)),
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
		$this->render('rel_style_lovers_show.html',array(
			'view'=>new RelStyleLoversView(new RelStyleLoversModel($id,11)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Post::getInt('id');
		$style_sn1 = _Request::getString('style_sn1');
		$style_sn2 = _Request::getString('style_sn2');
        if(empty($style_sn1)){
            $result['error'] = "款号1不能为空";
			Util::jsonExit($result);
        }
        if(empty($style_sn2)){
            $result['error'] = "款号2不能为空";
			Util::jsonExit($result);
        }
        
        //检查2个款号是否存在，情侣戒中
        $relStyleLoverModel = new RelStyleLoversModel(11);
        $love_data1 = $relStyleLoverModel->checkStyleLoverStyleSn($style_sn1);
        if(!empty($love_data1)){
            $result['error'] = "款号1已经添加到侣戒中了";
			Util::jsonExit($result);
        }
        $love_data2 = $relStyleLoverModel->checkStyleLoverStyleSn($style_sn2);
        if(!empty($love_data2)){
            $result['error'] = "款号2已经添加到情侣戒中了";
			Util::jsonExit($result);
        }
        //检查款号是否有效
        $baseStyleModel = new BaseStyleInfoModel(11);
        $style_sn1_data = $baseStyleModel->getStyleStyleByStyle_sn(array('style_sn'=>$style_sn1,'check_status'=>3));
        $style_sn2_data = $baseStyleModel->getStyleStyleByStyle_sn(array('style_sn'=>$style_sn2,'check_status'=>3));
        
        if($style_sn1 == $style_sn2){
            $result['error'] = "款号1和款号2不可以是同一个款号";
			Util::jsonExit($result);
        }
        if(empty($style_sn1_data)){
            $result['error'] = "款号1不存在或不是审核状态";
			Util::jsonExit($result);
        }
        if($style_sn1_data[0]['style_type']!=11){
            $result['error'] = "款号1不是情侣戒";
			Util::jsonExit($result);
        }
        if(empty($style_sn2_data)){
            $result['error'] = "款号2不存在或不是审核状态";
			Util::jsonExit($result);
        }
         if($style_sn2_data[0]['style_type']!=11){
            $result['error'] = "款号2不是情侣戒";
			Util::jsonExit($result);
        }
		$olddo = array();
		$newdo=array(
            'style_id1'=>$style_sn1_data[0]['style_id'],
            'style_id2'=>$style_sn2_data[0]['style_id'],
            'style_sn1'=>$style_sn1,
            'style_sn2'=>$style_sn2,
        );
        
		$newmodel =  new RelStyleLoversModel(12);
		$res = $newmodel->saveData($newdo,$olddo);
		$model = new BaseStyleInfoModel(12);
		$model->addBaseStyleLog(array('style_id'=>$newdo['style_id1'],'remark'=>'添加情侣戒款式'));
		$model->addBaseStyleLog(array('style_id'=>$newdo['style_id2'],'remark'=>'添加情侣戒款式'));
		if($res !== false)
		{
			$result['success'] = 1;
		}else{
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
        $id = _Request::getInt('id');
        $style_sn1 = _Request::getString('style_sn1');
		$style_sn2 = _Request::getString('style_sn2'); 
		$newdo=array(
			'style_id1'=>$style_sn1_data[0]['style_id'],
			'style_id2'=>$style_sn2_data[0]['style_id'],
        );
		if(empty($style_sn1)){
            $result['error'] = "款号1不能为空";
			Util::jsonExit($result);
        }
        if(empty($style_sn2)){
            $result['error'] = "款号2不能为空";
			Util::jsonExit($result);
        }
        
        //检查2个款号是否存在，情侣戒中
        $relStyleLoverModel = new RelStyleLoversModel(11);
        $love_data1 = $relStyleLoverModel->checkStyleLoverStyleSn($style_sn1);
        
        if(!empty($love_data1) && $love_data1[0]['id']!=$id){
            //排除自己本身
            $result['error'] = "款号1已经添加到情侣戒中了";
			Util::jsonExit($result);
        }
        $love_data2 = $relStyleLoverModel->checkStyleLoverStyleSn($style_sn2);
        if(!empty($love_data2)  && $love_data1[0]['id']!=$id){
            $result['error'] = "款号2已经添加到情侣戒中了";
			Util::jsonExit($result);
        }
        //检查款号是否有效
        $baseStyleModel = new BaseStyleInfoModel(11);
        $style_sn1_data = $baseStyleModel->getStyleStyleByStyle_sn(array('style_sn'=>$style_sn1,'check_status'=>3));
        $style_sn2_data = $baseStyleModel->getStyleStyleByStyle_sn(array('style_sn'=>$style_sn2,'check_status'=>3));
        
        if($style_sn1 == $style_sn2){
            $result['error'] = "款号1和款号2不可以是同一个款号";
			Util::jsonExit($result);
        }
        if(empty($style_sn1_data)){
            $result['error'] = "款号1不存在或不是审核状态";
			Util::jsonExit($result);
        }
        if($style_sn1_data[0]['style_type']!=11){
            $result['error'] = "款号1不是情侣戒";
			Util::jsonExit($result);
        }
        if(empty($style_sn2_data)){
            $result['error'] = "款号2不存在或不是审核状态";
			Util::jsonExit($result);
        }
         if($style_sn2_data[0]['style_type']!=11){
            $result['error'] = "款号2不是情侣戒";
			Util::jsonExit($result);
        }
		
		$newdo=array(
            'id'=>$id,
            'style_id1'=>$style_sn1_data[0]['style_id'],
            'style_id2'=>$style_sn2_data[0]['style_id'],
            'style_sn1'=>$style_sn1,
            'style_sn2'=>$style_sn2,
        );

		$newmodel =  new RelStyleLoversModel($id,12);
        $olddo = $newmodel->getDataObject();
		$model = new BaseStyleInfoModel($id, 12);
		$res = $newmodel->saveData($newdo,$olddo);
		$model->addBaseStyleLog(array('style_id'=>$newdo['style_id1'],'remark'=>'修情侣戒款式信息'));
		$model->addBaseStyleLog(array('style_id'=>$newdo['style_id2'],'remark'=>'修情侣戒款式信息'));
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
	public function del ($params)
	{
		$style_sn1 = _Request::getString('style_sn1');
		$style_sn2 = _Request::getString('style_sn2'); 
		$baseStyleModel = new BaseStyleInfoModel(11);
        $style_sn1_data = $baseStyleModel->getStyleStyleByStyle_sn(array('style_sn'=>$style_sn1,'check_status'=>3));
        $style_sn2_data = $baseStyleModel->getStyleStyleByStyle_sn(array('style_sn'=>$style_sn2,'check_status'=>3)); 
		$newdo=array(
            'id'=>$id,
            'style_id1'=>$style_sn1_data[0]['style_id'],
            'style_id2'=>$style_sn2_data[0]['style_id'],
            'style_sn1'=>$style_sn1,
            'style_sn2'=>$style_sn2,
        );
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new RelStyleLoversModel($id,12);
		$newmodel = new BaseStyleInfoModel($id, 12);
		$res = $model->delete();
		$newmodel->addBaseStyleLog(array('style_id'=>$newdo['style_id1'],'remark'=>'删除情侣戒款式'));
		$newmodel->addBaseStyleLog(array('style_id'=>$newdo['style_id2'],'remark'=>'删除情侣戒款式'));
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
}

?>