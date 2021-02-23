<?php
/**
 *  -------------------------------------------------
 *   @file		: PriceByStyleController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *  -------------------------------------------------
 */
class PriceByStyleController extends CommonController {

    protected $smartyDebugEnabled = true;

    public function __construct() {
           parent::__construct();
    }

    /**
     * 	index，搜索框
     */
    public function index($params) {
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
        $this->assign('cat_data',$new_cat_data);//数据字典
        $this->assign('product_data',$new_product_data);//数据字典

        $model= new AppXilieModel(11);
        $xilie=$model->getAllXilieName();
        $this->render('price_by_style_search_form.html', array(
			'bar' => Auth::getBar(),
            'xilie'=>$xilie,
			'view' => new BaseStyleInfoView(new BaseStyleInfoModel(11)),
        	'viewproduct'=>new AppProductTypeView(new AppProductTypeModel(11)),
        	'viewcat'=>new AppCatTypeView(new AppCatTypeModel(11)),
            'is_check'=>0));
    }

    /**
     * 	search，列表
     */
    public function search($params) {
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
        $this->assign('cat_data',$new_cat_data);//数据字典
        $this->assign('product_data',$new_product_data);//数据字典

        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'style_sn' => _Request::getString('style_sn'),
            'product_type_id' => _Request::getInt('product_type_id'),
            'cat_type_id' => _Request::getInt('cat_type_id'),
            'check_status' => _Request::getString('check_status'),
            'dismantle_status' => _Request::getInt('dismantle_status'),
            'style_sex' => _Request::getInt('style_sex'),
            'style_name' => _Request::getString('style_name'),
            'factory_sn' => _Request::getString('factory_sn'),
            'xilie[]' => _Request::getList('xilie'),
            'is_xiaozhang' => _Request::get('is_xiaozhang'),
            'page_size' => _Request::get('PageSize', 25),
            'is_kuanprice' => _Request::get('is_kuanprice', 1)
        );
        $args['is_made'] = '';
		//var_dump($args);die;
        $product_type = '';
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
        if(isset($_REQUEST['is_made'])){
            $args['is_made'] = _Request::getString('is_made');
        }
        $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;
        $where = array();
        //款号 批量搜索
        $style_sn = '';
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
        //模号 批量搜索
        $factory_sn = '';
        if($args['factory_sn']){
            //若 模号中间存在空格 汉字逗号 替换为英文模式逗号
            $args['factory_sn'] = str_replace(' ',',',$args['factory_sn']);
            $args['factory_sn'] = str_replace('，',',',$args['factory_sn']);
            $tmp = explode(",", $args['factory_sn']);
            foreach($tmp as $val){
                $factory_sn .= "'$val',";
            }
            $factory_sn = rtrim($factory_sn,',');
        }
        
        $where['style_sn_in'] = $style_sn;
        $where['product_type_id'] = $product_type;
        $where['cat_type_id'] = $args['cat_type_id'];
        $where['check_status'] = $args['check_status'];
        $where['dismantle_status'] = $args['dismantle_status'];
        $where['style_sex'] = $args['style_sex'];
        $where['is_made'] = $args['is_made'];
        $where['xilie'] = _Request::getList('xilie');
        $where['style_name'] = $args['style_name'];
        $where['factory_sn_in'] = $factory_sn;
        $where['check_status_zuofei'] = 7;
        $where['is_xiaozhang'] = $args['is_xiaozhang'];
        $where['is_kuanprice'] = $args['is_kuanprice'];

        $model = new BaseStyleInfoModel(11);
        $appley_model = new RelStyleFactoryModel(11);
        $gallery_model = new AppStyleGalleryModel(11);
        $_newModel = new ApiProcessorModel();
        if(empty($where['factory_sn_in'])){
            $data = $model->pageList_other($where, $page, $args['page_size'], false);
        }else{
            $data = $model->pageList($where, $page, $page['page_size'], false);
        }
    
        $appPriceByStyleModel = new AppPriceByStyleModel(11);
        
		//var_dump($data);die;
        if ($data) {
            foreach ($data['data'] as &$val) {
                $val['style_type'] = isset($new_cat_data[$val['style_type']]) ?$new_cat_data[$val['style_type']]:"";
                $val['product_type'] = isset($new_product_data[$val['product_type']])?$new_product_data[$val['product_type']]:'';

                $priceList = $appPriceByStyleModel->getListByStyleid($val['style_id']);
                if($priceList){
                    $val['price_by_style'] = implode('<br/>',array_column($priceList,'price'));
                }else{
                    $val['price_by_style'] = '';
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
                    $data['data'][$k]['big_img'] = $img['big_img'];
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
        $pageData['jsFuncs'] = 'price_by_style_search_page';
        $this->render('price_by_style_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data
        ));
    }

    /**
     * 	add，渲染添加页面
     */
    public function add() {
        $result = array('success' => 0, 'error' => '');
        $style_id = _Request::getInt('style_id');
        $styleInfo = new BaseStyleInfoView(new BaseStyleInfoModel($style_id, 11));
        $check_status = $styleInfo->get_check_status();
        if($check_status != 3){
			die('未审核的款式不能操作款式定价');
        }

        $tuoType = array(array('id'=>1,'name'=>'成品'),array('id'=>3,'name'=>'空托'));
        $certType = array();
        $certType[] = array('name'=>'全部');
        $certType[] = array('name'=>'NGDTC');
        $certType[] = array('name'=>'GIA');
        $certType[] = array('name'=>'IGI');
        $certType[] = array('name'=>'NGTC');
        $certType[] = array('name'=>'HRD');
        $certType[] = array('name'=>'AGL');
        $certType[] = array('name'=>'EGL');
        $certType[] = array('name'=>'NGGC');
        $certType[] = array('name'=>'NGSTC');
        $certType[] = array('name'=>'空值');


        $shapes = array();
        $relStyleStoneModel = new RelStyleStoneModel(11);
        $ss = $relStyleStoneModel->getShapeList();
        foreach($ss as $key => $val){
            $shapes[] = array('name'=>$val['item_name']);
        }

        $this->assign('tuoType',$tuoType);
        $this->assign('certType',$certType);
        $this->assign('shapes',$shapes);
        
        $this->assign('stonePosition',array(1=>'主石',2=>'无'));
        $this->assign('stoneCat',array(1=>'无',2=>'圆钻',3=>'异形钻'));

        $appPriceByStyleModel = new AppPriceByStyleModel(17);
        $zuan_yanse = $appPriceByStyleModel->getYanseAll();
        $this->assign('zuanYanse',$zuan_yanse);
        $zuan_jingdu = $appPriceByStyleModel->getJingduAll();
        $this->assign('zuanJindu',$zuan_jingdu);

        
        $result['content'] = $this->fetch('price_by_style_info.html', array(
            'view' => new AppPriceByStyleView(new AppPriceByStyleModel(11)),
            'style_id'=>$style_id
        ));
        $result['title'] = '添加';
        Util::jsonExit($result);
    }

    /**
     * 	edit，渲染修改页面
     */
    public function edit($params) {
        $id = intval($params["id"]);
        $view = new AppPriceByStyleView(new AppPriceByStyleModel($id,11));
        $style_id = $view->get_style_id();

        if($view->get_is_delete() == 0){
            die('只有作废的款才能进行编辑');
        }

        $styleInfo = new BaseStyleInfoView(new BaseStyleInfoModel($style_id, 11));
        $check_status = $styleInfo->get_check_status();
        if($check_status != 3){
			die('未审核的款式不能操作款式定价');
        }

        
        $result = array('success' => 0, 'error' => '');
        $tuoType = array(array('id'=>1,'name'=>'成品'),array('id'=>3,'name'=>'空托'));
        $certType = array();
        $certType[] = array('name'=>'全部');
        $certType[] = array('name'=>'NGDTC');
        $certType[] = array('name'=>'GIA');
        $certType[] = array('name'=>'IGI');
        $certType[] = array('name'=>'NGTC');
        $certType[] = array('name'=>'HRD');
        $certType[] = array('name'=>'AGL');
        $certType[] = array('name'=>'EGL');
        $certType[] = array('name'=>'NGGC');
        $certType[] = array('name'=>'NGSTC');
        $certType[] = array('name'=>'空值');
        
        $shapes = array();
        $relStyleStoneModel = new RelStyleStoneModel(11);
        $ss = $relStyleStoneModel->getShapeList();
        foreach($ss as $key => $val){
            $shapes[] = array('name'=>$val['item_name']);
        }
        $this->assign('tuoType',$tuoType);
        $this->assign('certType',$certType);
        $this->assign('shapes',$shapes);

        $this->assign('stonePosition',array(1=>'主石',2=>'无'));
        $this->assign('stoneCat',array(1=>'无',2=>'圆钻',3=>'异形钻'));

        $appPriceByStyleModel = new AppPriceByStyleModel(17);
        $zuan_yanse = $appPriceByStyleModel->getYanseAll();
        $this->assign('zuanYanse',$zuan_yanse);
        $zuan_jingdu = $appPriceByStyleModel->getJingduAll();
        $this->assign('zuanJindu',$zuan_jingdu);

        $result['content'] = $this->fetch('price_by_style_info.html', array(
            'view' => $view,
            'style_id' => $style_id
        ));
        $result['title'] = '编辑';
        Util::jsonExit($result);
    }

    /**
     * 	show，渲染查看页面
     */
    public function show($params) {
        $id = intval($params["id"]);
        $all_data = new BaseStyleInfoView(new BaseStyleInfoModel($id, 11));
        $model = new BaseStyleInfoModel(11);

        $style_sn = $all_data->get_style_sn();
        $product_type_id = $all_data->get_product_type();
        $product_type = $model->getProductTypeList($product_type_id);
        
        $cat_type_id = $all_data->get_style_type();
        $style_type = $model->getStyleTypeList($cat_type_id);
        $xilie=$model->getXilieById($id);
        $xilie_name=$model->getXilieNameByid($xilie);
        //-------------------------------------------------------------------------------------------------
        //基础信息
            //产品线
            $new_product_data= array();
            $productModel = new AppProductTypeModel(11);
            $product_data = $productModel->getCtlList();
            foreach ($product_data as $val){
                $new_product_data[$val['product_type_id']]=$val['product_type_name'];
            }

            //获取分类名称
            $new_cat_data= array();
            $appCatModel = new AppCatTypeModel(11);
            $cat_data = $appCatModel->getCtlList();
            foreach ($cat_data as $val){
                $new_cat_data[$val['cat_type_id']]=$val['cat_type_name'];
            }

            //获取所有的属性
            $new_attr_data = array();
            $attributeModel = new AppAttributeModel(11);
            $attribute_arr = $attributeModel->getCtlList();
            foreach ($attribute_arr as $val){
                $new_attr_data[$val['attribute_id']] = $val['attribute_name'];
            }

            $this->assign('cat_data',$new_cat_data);//数据字典
            $this->assign('attr_data',$new_attr_data);//数据字典
            $this->assign('product_data',$new_product_data);//数据字典
            $this->assign('showType',array(1=>'文本框',2=>'单选',3=>'多选',4=>'下拉'));//数据字典
            $this->assign('feeTypes',array('1'=>'18K工费','2'=>'超石费用','3'=>'表面工艺','4'=>'PT950工费'));//数据字典
            $this->assign('tuoType',array(1=>'成品',3=>'空托'));
            $this->assign('stonePosition',array(1=>'主石',2=>'无'));
            $this->assign('stoneCat',array(1=>'无',2=>'圆钻',3=>'异形钻'));

        //-------------------------------------------------------------------------------------------------
        //获取所有属性对应的属性值
            $where = array();
            $where = array(
                'cat_type_id' => $cat_type_id,
                'product_type_id' => $product_type_id,
                'style_sn' => $style_sn,
                'style_id' => $id,
            );
        
            
            $model = new RelStyleAttributeModel(11);
            $data = $model->pageList($where,1,1000,false);

            $relCatAttributeModel = new RelCatAttributeModel(11);
            $all_attribute = $relCatAttributeModel->getList($where);
            $attribute_data = array();
            foreach ($all_attribute as $val){
                $attribute_id = $val['attribute_id'];
                $attribute_name = $val['attribute_name'];
                //文本框没有对应属性值
                $attribute_data['info'][$attribute_id] = $attribute_name;
                if($val['show_type'] !=1){
                    $attr = $relCatAttributeModel->getAttr($where,$attribute_id);  
                    foreach($attr as $k => $v){
                        $attribute_data[$v['att_value_id']]=$v['att_value_name'];
                    }
                }
            }

            //把属性和属性值拼到查出的数据中
            foreach ($data['data'] as $key=>$val){
                $att_id = $val['attribute_id'];
                $value_id = $val['attribute_value'];
                
                if(!array_key_exists($att_id, $attribute_data['info'])){
                    continue;
                }
                $data['data'][$key]['attribute_name'] = $attribute_data['info'][$att_id];
                //show_type:1文本框，2单选，3多选，4下拉
                if($val['show_type'] == 1){
                    //unset($data['data'][$key]);
                    //continue;
                    $data['data'][$key]['att_value_name'] = $val['attribute_value']; 
                }elseif($val['show_type'] == 2 || $val['show_type'] == 4){
                    if(!array_key_exists($value_id, $attribute_data)){
                        continue;//9
                    }
                    $data['data'][$key]['att_value_name'] = $attribute_data[$value_id];
                }elseif($val['show_type'] == 3  ){
                    //把属性值解析出来
                    $att_value_arr = array_filter(explode(",", $value_id));
                    $num = count($att_value_arr);
                    if($num == 0){
                        $data['data'][$key]['att_value_name'] = '';
                    }else{
                        $arr_attr_value = array();
                        foreach ($att_value_arr as $v_val){
                            if(!array_key_exists($v_val, $attribute_data)){
                                continue;
                            }
                            $arr_attr_value[]= $attribute_data[$v_val];
                        }
                        $data['data'][$key]['att_value_name'] = implode(',',$arr_attr_value);
                    }
                }
            }
            $style_attrs = $data;

        //-------------------------------------------------------------------------------------------------
        //工厂信息

            $args = array(
                'style_sn' => $style_sn,
                'style_id' => $id
            );
            $model = new RelStyleFactoryModel(11);
            $data = $model->pageList($args, 1, 100, false);
            if(!empty($data['data'])){
                $arrData = array('否', '是');
                 $_newModel = new ApiProcessorModel();
                foreach ($data['data'] as $key=>$val){
                    $factory_name = $_newModel->GetSupplierListName($val['factory_id']);
                    $data['data'][$key]['factory_name']=isset($factory_name['data'])?$factory_name['data']:'';
                    $data['data'][$key]['is_def']=isset($arrData[$val['is_def']])?$arrData[$val['is_def']]:'';
                    $data['data'][$key]['is_factory']=isset($arrData[$val['is_factory']])?$arrData[$val['is_factory']]:'';
                }
            }
            $style_factorys = $data;


        //-------------------------------------------------------------------------------------------------
        //相册信息
            $where = array();
            $where['style_id'] = $id;
            $model = new AppStyleGalleryModel(11);
            $style_gallery = $model->pageList($where, 1, 200, false, true);
            $imagePlace = $model->getImagePlaceList();
            $this->assign('imagePlace', $imagePlace);


        //-------------------------------------------------------------------------------------------------
        //工费信息
            $where = array(
                'style_id'=>$id,
            );
            $model = new AppStyleFeeModel(11);
            $style_fee = $model->pageList($where,1,100,false);

        //-------------------------------------------------------------------------------------------------
        //石头信息
            $args = array(
                'style_id' => $id
            );
            $where = array();
            $where['style_id'] = $args['style_id'];

            $model = new RelStyleStoneModel(11);
            $data = $model->pageList($where, 1, 50, false);
            if ($data) {
                $arr = $model->getStoneCatList();
                $stone_position = array('1' => '主石', '2' => '副石');
                foreach ($data['data'] as $key => &$value) {
                    $value['stone_position'] = $stone_position[$value['stone_position']];
                    $value['stone_cat'] = $arr[$value['stone_cat']]['stone_name'];
                }
            }
            $style_stone = $data;

        //-------------------------------------------------------------------------------------------------
        //日志信息
            $where = array(
                 'style_id'=>$id,
            );

            $model = new BaseStyleLogModel(11);
            $style_log = $model->pageList($where,1,500,false);

        //-------------------------------------------------------------------------------------------------
        //按款定价
            $appPriceByStyleModel = new AppPriceByStyleModel(11);
            $style_price = $appPriceByStyleModel->getListByStyleid($id);

        
        $this->render('price_by_style_show.html', array(
            'view' => $all_data,
            'xilie_name'=>$xilie_name,
            'bar' => Auth::getViewBar(),
            'product_type' => $product_type,
            'style_type' => $style_type,
            'style_attrs'=>$style_attrs,
            'style_factorys'=>$style_factorys,
            'style_stone'=>$style_stone,
            'style_gallery'=>$style_gallery,
            'style_fee'=>$style_fee,
            'style_log'=>$style_log,
            'style_price'=>$style_price
        ));
    }

   
    /**
     * 	insert，信息入库
     */
    public function insert($params) {
        $result = array('success' => 0, 'error' => '');
        $style_id = _Post::getString('style_id');
        $caizhi = _Post::getString('caizhi');
        $stone_position = _Post::getString('stone_position');
        $stone_cat = _Post::getString('stone_cat');
        $tuo_type = _Post::getString('tuo_type');
        $zuan_min = $_REQUEST['zuan_min'];
        $zuan_max = $_REQUEST['zuan_max'];
        $zuan_yanse_min = _Post::getString('zuan_yanse_min');
        $zuan_yanse_max = _Post::getString('zuan_yanse_max');
        $zuan_jindu_min = _Post::getString('zuan_jindu_min');
        $zuan_jindu_max = _Post::getString('zuan_jindu_max');
        $cert = _Post::getString('cert');
        $zuan_shape = _Post::getString('zuan_shape');
        $price = _Post::getString('price');
        
        if($stone_cat == 2 && $zuan_shape != '圆形'){
			$result['error'] = '圆形钻形状使用圆形';
			Util::jsonExit($result);
        }elseif($stone_cat == 3 && $zuan_shape == '圆形'){
			$result['error'] = '异形钻形状使用非圆形';
			Util::jsonExit($result);
        }
        if($caizhi == ''){
			$result['error'] = '材质不能为空';
			Util::jsonExit($result);
        }
		if($tuo_type == ''){
			$result['error'] = '金托类型不能为空';
			Util::jsonExit($result);
        }
		/*update by liulinyan 20151230 fro 胥国凯亲自督察
        if($stone_position == ''){
			$result['error'] = '石头位置类型不能为空';
			Util::jsonExit($result);
        }*/
		if($stone_position==1)
		{
			if($stone_cat == ''){
				$result['error'] = '石头类型不能为空';
				Util::jsonExit($result);
			}
			if($zuan_min === '' || $zuan_max === ''){
				$result['error'] = '主石范围不能为空';
				Util::jsonExit($result);
			}
		}
        $appPriceByStyleModel = new AppPriceByStyleModel(17);
        $zuan_yanse = $appPriceByStyleModel->getYanseAll();
        $zuan_jingdu = $appPriceByStyleModel->getJingduAll();
		
		
        if($zuan_yanse_min == '' || $zuan_yanse_max == ''){
			if($stone_position==1)
			{
				$result['error'] = '主石颜色范围不能为空';
				Util::jsonExit($result);
			}
			//$result['error'] = '主石颜色范围不能为空';
			//Util::jsonExit($result);
        }else{
            $zuanYanse = $zuan_yanse;
            $this->assign('zuanYanse',$zuanYanse);


            if($zuan_yanse_min > $zuan_yanse_max){
                $result['error'] = '主石颜色范围出错';
                Util::jsonExit($result);
            }
            $zuan_yanse_min = $zuanYanse[$zuan_yanse_min];
            $zuan_yanse_max = $zuanYanse[$zuan_yanse_max];
        }

        if($zuan_jindu_min == '' || $zuan_jindu_max == ''){
			if($stone_position==1)
			{
				$result['error'] = '主石净度范围不能为空';
				Util::jsonExit($result);
			}
			//$result['error'] = '主石净度范围不能为空';
			//Util::jsonExit($result);
        }else{
            $zuanJindu = $zuan_jingdu;
            $this->assign('zuanJindu',$zuanJindu);


            if($zuan_jindu_min > $zuan_jindu_max){
                $result['error'] = '主石净度范围出错';
                Util::jsonExit($result);
            }
            $zuan_jindu_min = $zuanJindu[$zuan_jindu_min];
            $zuan_jindu_max = $zuanJindu[$zuan_jindu_max];
        }

        if($cert == '' && $stone_position==1){
			$result['error'] = '证书类型不能为空';
			Util::jsonExit($result);
        }
        if($zuan_shape == '' && $stone_position==1){
			$result['error'] = '主石形状不能为空';
			Util::jsonExit($result);
        }
        if(empty($price)){
			$result['error'] = '价格不能为空';
			Util::jsonExit($result);
        }else{
            if($price > 9999999.99){
                $result['error'] = '价格不能大于 9999999.99 ';
                Util::jsonExit($result);
            }
        }

        $appPriceByStyleModel = new AppPriceByStyleModel(11);
        $style_price = $appPriceByStyleModel->getListByStyleid($style_id);

        if($style_price){
            foreach($style_price as $key => $val){
                if($val['stone_position'] == $stone_position && $val['caizhi'] == $caizhi){
                    if($val['tuo_type'] == $tuo_type){
                        $cat_status = true;
                        if(($val['stone_cat'] != 1  || $stone_cat != 1) && $val['stone_cat'] != $stone_cat){
                            $cat_status = false;                        
                        }
                        if($cat_status){
                            if($val['zuan_shape'] == $zuan_shape){
                                $certIn = $this->CertIn($val['cert'],$cert);
                                $daxiaoIn = $this->DaxiaoIn(sprintf("%.3f",$val['zuan_min']),sprintf("%.3f",$val['zuan_max']),sprintf("%.3f",$zuan_min),sprintf("%.3f",$zuan_max));
                                $yanseIn = $this->YanseIn($val['zuan_yanse_min'],$val['zuan_yanse_max'],$zuan_yanse_min,$zuan_yanse_max);
                                $jinduIn = $this->JinduIn($val['zuan_jindu_min'],$val['zuan_jindu_max'],$zuan_jindu_min,$zuan_jindu_max);
                                if($yanseIn && $jinduIn && $daxiaoIn && $certIn){
                                    $msg = $daxiaoIn?'大小 '.$val['zuan_min'].'-'.$val['zuan_max']:($yanseIn?'颜色 '.$val['zuan_yanse_min'].'-'.$val['zuan_yanse_max']:($certIn?'证书类型出错':'净度 '.$val['zuan_jindu_min'].'-'.$val['zuan_jindu_max']));
                                    $result['error'] = '范围交叉重复，请重新选择 '.$msg;
                                    Util::jsonExit($result);
                                }
                            }
                        }
                    }
                }
            }    
        }

        $olddo = array();
        $newdo = array();
        $newdo['style_id'] = $style_id;
        $newdo['caizhi'] = $caizhi;
        $newdo['stone_position'] = $stone_position;
        $newdo['stone_cat'] = $stone_cat;
        $newdo['tuo_type'] = $tuo_type;
        $newdo['zuan_min'] = $zuan_min;
        $newdo['zuan_max'] = $zuan_max;
        $newdo['zuan_yanse_min'] = $zuan_yanse_min;
        $newdo['zuan_yanse_max'] = $zuan_yanse_max;
        $newdo['zuan_jindu_min'] = $zuan_jindu_min;
        $newdo['zuan_jindu_max'] = $zuan_jindu_max;
        $newdo['cert'] = $cert;
        $newdo['zuan_shape'] = $zuan_shape;
        $newdo['price'] = $price;
        $newdo['is_delete'] = 1;
        $newdo = array_filter($newdo);
        $newmodel = new AppPriceByStyleModel(12);
        $res = $newmodel->saveData($newdo, $olddo);
        
        if ($res !== false ) {
            $baseStyleInfoModel = new BaseStyleInfoModel(12);
            $baseStyleInfoModel->addBaseStyleLog(array('style_id'=>$newdo['style_id'],'remark'=>'添加款式规则，序号'.$res.";"));
            $result['success'] = 1;
        } else {
            $result['error'] = '添加失败';
        }
        Util::jsonExit($result);
    }

    /**
     * 	update，更新信息
     */
    public function update($params) {
        $result = array('success' => 0, 'error' => '');

        $id = _Post::getString('id');
        $style_id = _Post::getString('style_id');
        $caizhi = _Post::getString('caizhi');
        $stone_position = _Post::getString('stone_position');
        $stone_cat = _Post::getString('stone_cat');
        $tuo_type = _Post::getString('tuo_type');
        $zuan_min = _Post::get('zuan_min');
        $zuan_max = _Post::get('zuan_max');
        $zuan_yanse_min = _Post::getString('zuan_yanse_min');
        $zuan_yanse_max = _Post::getString('zuan_yanse_max');
        $zuan_jindu_min = _Post::getString('zuan_jindu_min');
        $zuan_jindu_max = _Post::getString('zuan_jindu_max');
        $cert = _Post::getString('cert');
        $zuan_shape = _Post::getString('zuan_shape');
        $price = _Post::getString('price');
        
        if($stone_cat == 2 && $zuan_shape != '圆形'){
			$result['error'] = '圆形钻形状使用圆形';
			Util::jsonExit($result);
        }elseif($stone_cat == 3 && $zuan_shape == '圆形'){
			$result['error'] = '异形钻形状使用非圆形';
			Util::jsonExit($result);
        }
        if($caizhi == ''){
			$result['error'] = '材质不能为空';
			Util::jsonExit($result);
        }
        if($stone_position == '' && $stone_position==1){
			$result['error'] = '石头位置类型不能为空';
			Util::jsonExit($result);
        }
        if($stone_cat == '' && $stone_position==1){
			$result['error'] = '石头类型不能为空';
			Util::jsonExit($result);
        }
        if($tuo_type == ''){
			$result['error'] = '金托类型不能为空';
			Util::jsonExit($result);
        }
		/*
        if($zuan_min === '' || $zuan_max === ''){
			$result['error'] = '主石范围不能为空';
			Util::jsonExit($result);
        }*/

        $appPriceByStyleModel = new AppPriceByStyleModel(17);
        $zuan_yanse = $appPriceByStyleModel->getYanseAll();
        $zuan_jingdu = $appPriceByStyleModel->getJingduAll();

        if($zuan_yanse_min == '' || $zuan_yanse_max == ''){
			//$result['error'] = '主石颜色范围不能为空';
			//Util::jsonExit($result);
			if($stone_position==1)
			{
				$result['error'] = '主石颜色范围不能为空';
				Util::jsonExit($result);
			}
        }else{
            $zuanYanse = $zuan_yanse;
            $this->assign('zuanYanse',$zuanYanse);
            if($zuan_yanse_min > $zuan_yanse_max){
                $result['error'] = '主石颜色范围出错';
                Util::jsonExit($result);
            }
            $zuan_yanse_min = $zuanYanse[$zuan_yanse_min];
            $zuan_yanse_max = $zuanYanse[$zuan_yanse_max];
        }
        if($zuan_jindu_min == '' || $zuan_jindu_max == ''){
			//$result['error'] = '主石净度范围不能为空';
			//Util::jsonExit($result);
			if($stone_position==1)
			{
				$result['error'] = '主石净度范围不能为空';
				Util::jsonExit($result);
			}
        }else{
            $zuanJindu = $zuan_jingdu;
            $this->assign('zuanJindu',$zuanJindu);
            if($zuan_jindu_min > $zuan_jindu_max){
                $result['error'] = '主石净度范围出错';
                Util::jsonExit($result);
            }
            $zuan_jindu_min = $zuanJindu[$zuan_jindu_min];
            $zuan_jindu_max = $zuanJindu[$zuan_jindu_max];
        }
		
        if($cert == ''){
			if($stone_position==1)
			{
				$result['error'] = '证书类型不能为空';
				Util::jsonExit($result);
			}else{
				$cert= '全部';	
			}
        }
        if($zuan_shape == '' && $stone_position==1){
			$result['error'] = '主石形状不能为空';
			Util::jsonExit($result);
        }
        if(empty($price)){
			$result['error'] = '价格不能为空';
			Util::jsonExit($result);
        }else{
            if($price > 9999999.99){
                $result['error'] = '价格不能大于 9999999.99 ';
                Util::jsonExit($result);
            }
        }

        $appPriceByStyleModel = new AppPriceByStyleModel(11);
        $style_price = $appPriceByStyleModel->getListByStyleid($style_id);

        if($style_price){
            foreach($style_price as $key => $val){
                if($val['id'] == $id){
                    continue;
                }
                /*
                var_dump($val['id'],$id);
                var_dump($val['stone_position'], $stone_position , $val['caizhi'] , $caizhi);
                var_dump($val['tuo_type'] , $tuo_type);
                var_dump($val['cert'] , $cert);
                var_dump($val['zuan_shape'] , $zuan_shape);
                var_dump($val['stone_cat'] , $stone_cat);die;
                */
                if($val['stone_position'] == $stone_position && $val['caizhi'] == $caizhi){
                    if($val['tuo_type'] == $tuo_type){
                        $cat_status = true;
                        if(($val['stone_cat'] != 1  || $stone_cat != 1) && $val['stone_cat'] != $stone_cat){
                            $cat_status = false;
                        }
                        if($cat_status){
                            if($val['zuan_shape'] == $zuan_shape){
                                $certIn = $this->CertIn($val['cert'],$cert);
                                $daxiaoIn = $this->DaxiaoIn(sprintf("%.3f",$val['zuan_min']),sprintf("%.3f",$val['zuan_max']),sprintf("%.3f",$zuan_min),sprintf("%.3f",$zuan_max));
                                $yanseIn = $this->YanseIn($val['zuan_yanse_min'],$val['zuan_yanse_max'],$zuan_yanse_min,$zuan_yanse_max);
                                $jinduIn = $this->JinduIn($val['zuan_jindu_min'],$val['zuan_jindu_max'],$zuan_jindu_min,$zuan_jindu_max);
                                //var_dump($yanseIn ,$jinduIn ,$daxiaoIn,$certIn);
                                if($yanseIn && $jinduIn && $daxiaoIn && $certIn){
                                    $msg = $daxiaoIn?'大小 '.$val['zuan_min'].'-'.$val['zuan_max']:($yanseIn?'颜色 '.$val['zuan_yanse_min'].'-'.$val['zuan_yanse_max']:($certIn?'证书类型出错':'净度 '.$val['zuan_jindu_min'].'-'.$val['zuan_jindu_max']));
                                    $result['error'] = '范围交叉重复，请重新选择 '.$msg;
                                    Util::jsonExit($result);
                                }
                            }
                        }
                    }
                }
            }    
        }

        $newmodel = new AppPriceByStyleModel($id,12);
        $olddo = $newmodel->getDataObject();
        $newdo = array();
        $newdo['id'] = $id;
        $newdo['caizhi'] = $caizhi;
        $newdo['style_id'] = $style_id;
        $newdo['stone_position'] = $stone_position;
        $newdo['stone_cat'] = $stone_cat;
        $newdo['tuo_type'] = $tuo_type;
        $newdo['zuan_min'] = $zuan_min;
        $newdo['zuan_max'] = $zuan_max;
        $newdo['zuan_yanse_min'] = $zuan_yanse_min;
        $newdo['zuan_yanse_max'] = $zuan_yanse_max;
        $newdo['zuan_jindu_min'] = $zuan_jindu_min;
        $newdo['zuan_jindu_max'] = $zuan_jindu_max;
        $newdo['cert'] = $cert;
        $newdo['zuan_shape'] = $zuan_shape;
        $newdo['price'] = $price;
		
		$olddo = array_filter($olddo);
		$olddo['is_delete'] = 0;
		$newdo = array_filter($newdo);
        $res = $newmodel->saveData($newdo, $olddo);
        
        if ($res !== false) {
            $baseStyleInfoModel = new BaseStyleInfoModel(12);
		    $baseStyleInfoModel->addBaseStyleLog(array('style_id'=>$newdo['style_id'],'remark'=>'编辑款式规则，序号'.$id.";"));
            $result['success'] = 1;
        } else {
            $result['error'] = '修改失败';
        }
        Util::jsonExit($result);
    }

    /**
     * 	delete，删除
     */
    public function delete($params) {
		ini_set('memory_limit','6000M');
        set_time_limit(0);
        $result = array('success' => 0, 'error' => '');
        $id = intval($params['id']);
        $model = new AppPriceByStyleModel($id, 12);
        
        $olddo = $model->getDataObject();
		$olddo = array_filter($olddo);
        $newdo = $olddo;
        $newdo['is_delete'] = 1;
        $res = $model->saveData($newdo, $olddo);
        $model->updateWarehouseGoodsAgeDelete($id);

        if ($res !== false) {
            $baseStyleInfoModel = new BaseStyleInfoModel(12);
		    $baseStyleInfoModel->addBaseStyleLog(array('style_id'=>$newdo['style_id'],'remark'=>'作废款式规则，序号'.$id.";"));
            $result['success'] = 1;
        } else {
            $result['error'] = "作废失败";
        }
        Util::jsonExit($result);
    }
    /**
     * 	recover，恢复
     */
    public function recover($params) {
		ini_set('memory_limit','6000M');
        set_time_limit(0);
        $result = array('success' => 0, 'error' => '');
        $id = intval($params['id']);
        $model = new AppPriceByStyleModel($id, 12);
        
        $olddo = $model->getDataObject();
		$olddo = array_filter($olddo);
        $newdo = $olddo;
		$olddo['is_delete'] = 1;
		$newdo['is_delete'] = 0;
        $model->saveData($newdo, $olddo);
        $model->updateWarehouseGoodsAgeRecover($newdo);
        $baseStyleInfoModel = new BaseStyleInfoModel(12);
		$baseStyleInfoModel->addBaseStyleLog(array('style_id'=>$newdo['style_id'],'remark'=>'恢复款式规则，序号'.$id.";"));
        $result['success'] = 1;
        Util::jsonExit($result);
    }

    function CertIn($oldcert,$newcert)
    {
        if($oldcert == '全部' || $newcert == '全部'){
            return true;
        }
        if($oldcert == $newcert){
            return true;
        }
        return false;
    }

    function YanseIn($oldS,$oldE,$newS,$newE)
    {
        $appPriceByStyleModel = new AppPriceByStyleModel(17);
        $zuan_yanse = $appPriceByStyleModel->getYanseAll();
        $y = array_flip($zuan_yanse);
        $oldS = $y[$oldS];
        $oldE = $y[$oldE];
        $newS = $y[$newS];
        $newE = $y[$newE];

        $oldList = array();
        $newList = array();
        foreach($zuan_yanse as $key => $y){
            if($key>=$oldS && $key<=$oldE){
                $oldList[] = $y;
            }
            if($key>=$newS && $key<=$newE){
                $newList[] = $y;
            }
        }
        $in = array_intersect($oldList,$newList);
        if($in){
            return true;
        }else{
            return false;
        }
    }

    function JinduIn($oldS,$oldE,$newS,$newE)
    {
        $appPriceByStyleModel = new AppPriceByStyleModel(17);
        $zuan_jingdu = $appPriceByStyleModel->getJingduAll();
        
        $j = array_flip($zuan_jingdu);
        $oldS = $j[$oldS];
        $oldE = $j[$oldE];
        $newS = $j[$newS];
        $newE = $j[$newE];

        $oldList = array();
        $newList = array();
        foreach($zuan_jingdu as $key => $y){
            if($key>=$oldS && $key<=$oldE){
                $oldList[] = $y;
            }
            if($key>=$newS && $key<=$newE){
                $newList[] = $y;
            }
        }
        $in = array_intersect($oldList,$newList);
        if($in){
            return true;
        }else{
            return false;
        }
    }

    function DaxiaoIn($oldS,$oldE,$newS,$newE)
    {
        if($oldS == $newS && $oldE == $newE){
            return true;
        }
        if($oldS == $oldE && $newS == $oldS){
            return true;
        }
        if($newS == $newE && $newS == $oldS){
            return true;
        }
        if($newS > $oldS && $newS < $oldE){
            return true;        
        }
        if($newE > $oldS && $newE < $oldE){
            return true;        
        }
        if($oldS > $newS && $oldS < $newE){
            return true;        
        }
        if($oldE > $newS && $oldE < $newE){
            return true;        
        }
        return false;
    }


}

