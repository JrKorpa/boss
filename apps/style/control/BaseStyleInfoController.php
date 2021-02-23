<?php

/**
 *  -------------------------------------------------
 *   @file		: BaseStyleInfoController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-08 12:42:57
 *   @update	:
 *  -------------------------------------------------
 */
class BaseStyleInfoController extends CommonController {

    protected $smartyDebugEnabled = true;
    protected $whitelist = array('downStyleInfo');

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
            $this->assign('cat_data',$new_cat_data);//数据字典
            $this->assign('product_data',$new_product_data);//数据字典
    }

    /**
     * 	index，搜索框
     */
    public function index($params) {
        if(SYS_SCOPE=="zhanting" && !Auth::user_is_from_base_company()){
            exit("非总公司账户，无权限访问");
        }
        //Util::M('base_style_info','front',11);	//生成模型后请注释该行
        //Util::V('base_style_info',11);	//生成视图后请注释该行
        $model= new AppXilieModel(11);
        $xilie=$model->getAllXilieName();
        $this->render('base_style_info_search_form.html', array(
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
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'style_sn' => _Request::getString('style_sn'),
            'product_type_id' => _Request::getInt('product_type_id'),
            'cat_type_id' => _Request::getInt('cat_type_id'),
            'check_status' => _Request::getInt('check_status'),
            'dismantle_status' => _Request::getInt('dismantle_status'),
            'style_sex' => _Request::getInt('style_sex'),
            'style_name' => _Request::getString('style_name'),
            'factory_sn' => _Request::getString('factory_sn'),
            'xilie[]' => _Request::getList('xilie'),
            'is_xiaozhang' => _Request::get('is_xiaozhang'),
            'market_xifen' => _Request::get('market_xifen'),
            'page_size' => _Request::get('PageSize', 25),
            'company_type' => _Request::getList('company_type'),
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
		$args['is_made'] = isset($_REQUEST['is_made']) && $_REQUEST['is_made'] != '' ? _Request::getInt('is_made') : '';//2015-12-25 zzm boss-1013
		
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
     /*    $groupUser = new GroupUserModel(1);
        $is_wukong = $groupUser->checkGroupUser(7,$_SESSION['userId']); 
        $where['is_wukong'] = $is_wukong;*/
        $where['style_sn_in'] = $style_sn;
        $where['product_type_id'] = $product_type;
        $where['cat_type_id'] = $args['cat_type_id'];
        $where['check_status'] = $args['check_status'];
        $where['dismantle_status'] = $args['dismantle_status'];
        $where['style_sex'] = $args['style_sex'];
        $where['is_made'] = $args['is_made'];
        $where['xilie'] = _Request::getList('xilie');
        $where['style_name'] = $args['style_name'];
       // $where['factory_sn'] = $args['factory_sn'];
        $where['factory_sn_in'] = $factory_sn;
        $where['check_status_zuofei'] = 7;
        $where['is_xiaozhang'] = $args['is_xiaozhang'];
        $where['market_xifen'] = $args['market_xifen'];
        $where['company_type'] = $args['company_type'];
        
        $model = new BaseStyleInfoModel(11);
        $appley_model = new RelStyleFactoryModel(11);
        $gallery_model = new AppStyleGalleryModel(11);
        $_newModel = new ApiProcessorModel();
        if(empty($where['factory_sn_in'])){
            $data = $model->pageList_other($where, $page, $args['page_size'], false);
        }else{
            $data = $model->pageList($where, $page, $page['page_size'], false);
        }

        //var_dump($data);die;
        if ($data) {
            foreach ($data['data'] as &$val) {
            	$style_type = $model->getStyleTypeList($val['style_type']);
				if($style_type){
					$val['style_type'] = isset($style_type['cat_type_name']) ?$style_type['cat_type_name']:"";
				}else{
					$val['style_type'] = '';
				}
				$product_type = $model->getProductTypeList($val['product_type']);
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
                $attr_list=$model->getStyleAttr('biaomiangongyi',$v['style_sn']);                
                $data['data'][$k]['style_name'] = $data['data'][$k]['style_name'] .'<br>表面工艺:'. implode(',',$attr_list);    
            }
        }
        //echo '<pre>';
        //print_r($data);die;
        $groupUser=new GroupUserModel(1);
        //获取款式编辑组id=6 用户
        $userlist= $groupUser->getGroupUser(6);
        $can_view_factory=in_array($_SESSION['userId'], array_column($userlist,'user_id')) ? 'YES':'NO';
        if($_SESSION['userType']==1)
            $can_view_factory='YES';            
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'base_style_info_search_page';
        $this->render('base_style_info_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data,
            'view' => new BaseStyleInfoView(new BaseStyleInfoModel(11)),
            'can_view_factory'=>$can_view_factory
        ));
    }

    /**
     * 	add，渲染添加页面
     */
    public function add() {
        $is_gold_arr = array(
            0=>"非黄金",
            1=>"瑞金",
            2=>"3D",
            3=>"一口价",
            4=>"普通金条",
            5=>"PT990",
            6=>"PT950",
            7=>"刚泰金条",
            8=>"刚泰其他投资黄金"
        );

        $groupUser = new GroupUserModel(1);
        $is_wukong = $groupUser->checkGroupUser(7,$_SESSION['userId']);

        $result = array('success' => 0, 'error' => '');
        $model= new AppXilieModel(11);
        $xiliearr=$model->getXilieNameBystatus();
        $result['content'] = $this->fetch('base_style_info_info.html', array(
            'view' => new BaseStyleInfoView(new BaseStyleInfoModel(11)),
            'xiliearr'=>$xiliearr,
        	'viewproduct'=>new AppProductTypeView(new AppProductTypeModel(11)),
        	'viewcat'=>new AppCatTypeView(new AppCatTypeModel(11)),
             'xilie'=>array(),
            'is_check'=>0,
            'is_gold_arr'=>$is_gold_arr,
            'company_type'=>array(),
            'is_wukong' => $is_wukong
        ));
        $result['title'] = '添加';
        Util::jsonExit($result);
    }

    /**
     * 	edit，渲染修改页面
     */
    public function edit($params) {
        $id = intval($params["id"]);
        $result = array('success' => 0, 'error' => '');
        $model= new AppXilieModel(11);
        $xiliearr=$model->getXilieNameBystatus();
        $baseStyleview = new BaseStyleInfoView(new BaseStyleInfoModel($id, 11));
        //var_dump($baseStyleview->get_xilie());die;

        /**
         * 判断用户是否为物控 
         * 超级用户不受限制
         */
       
        $groupUser = new GroupUserModel(1);
        $is_wukong = $groupUser->checkGroupUser(7,$_SESSION['userId']);
        
        if($_SESSION['userType'] != 1){

            if($is_wukong && $baseStyleview->get_is_wukong() != 1){ 
                //Util::jsonExit(array('success'=>0,'error'=>'不能操作非物控款式'));
                //header("Content-type: application/json");
                //exit(json_encode(array('success'=>0,'error'=>'不能操作非物控款式')));
                exit('不能操作非物控款式');
            }
            if(!$is_wukong && $baseStyleview->get_is_wukong() == 1){ 
                //Util::jsonExit(array('success'=>0,'error'=>'不能操作物控款式'));
                exit('不能操作物控款式');
            }
        }
        $check_status = $baseStyleview->get_check_status();
        $is_check = 1;
        if($check_status<3){
            $is_check= 0;
        }
        $is_gold_arr = array(
            0=>"非黄金",
            1=>"瑞金",
            2=>"3D",
            3=>"一口价",
            4=>"普通金条",
            5=>"PT990",
            6=>"PT950",
            7=>"刚泰金条",
            8=>"刚泰其他投资黄金"
        );

        $result['content'] = $this->fetch('base_style_info_info.html', array(
            'view' => $baseStyleview,
            'xiliearr'=>$xiliearr,
            'is_check'=>$is_check,
            'xilie'=>explode(',',$baseStyleview->get_xilie()),
			'viewproduct'=>new AppProductTypeView(new AppProductTypeModel(11)),
			'viewcat'=>new AppCatTypeView(new AppCatTypeModel(11)),
            'is_gold_arr'=>$is_gold_arr,
            'company_type'=>empty($baseStyleview->get_company_type_id())?array():explode(',',$baseStyleview->get_company_type_id()),
            'is_wukong' => $is_wukong
        ));
        $result['title'] = '编辑';
        Util::jsonExit($result);
    }
    /**
     * 编辑款式详情
     * @param unknown $params
     */
    public function editStyleContent($params) {
        
        $id = intval($params["id"]);  
              
        $result = array('success' => 0, 'error' => '');
        
        $model = new BaseStyleInfoModel($id, 11);        
        $result['content'] = $this->fetch('base_style_info_edit_goods_content.html', array(
            'view' => new BaseStyleInfoView($model),            
        ));
        $result['title'] = '编辑款式详情';
        Util::jsonExit($result);
    }
    /**
     * 保存款式详情
     * @param unknown $params
     */
    public function updateStyleContent($params){
        $result = array('success'=>0,'error'=>'');
        $id = _Post::getInt('id');
        $model = new BaseStyleInfoModel($id,11);
        $olddo = $model->getDataObject();
        if(empty($olddo)){
            $result['error'] = "编辑对象不存在！";
            Util::jsonExit($result);
        }
        $goods_content = _Post::get("goods_content");
        if($goods_content==""){
            $result['error'] = "款式详情不能为空！";
            Util::jsonExit($result);
        }
        $newdo = array(
            'style_id'=>$id,
            'goods_content' => _Post::get("goods_content"),
        );
        $res = $model->saveData($newdo, $olddo);
        if($res !==false){
            $result['success'] = 1;
        }else{
            $result['error'] = "保存失败！";
        }
        Util::jsonExit($result);
    }
    /**
     * 	show，渲染查看页面
     */
    public function show($params) {
        $rank = empty($params['rank']) ? 0 : $params['rank']; //0、代表款式列表过来   1、作废列表过来
        $id = intval($params["id"]);
        $all_data = new BaseStyleInfoView(new BaseStyleInfoModel($id, 11));
        $model = new BaseStyleInfoModel(11);

        //if($rank==0){
            if($_SESSION['userType']<>1){
                $groupUser = new GroupUserModel(1);
                $is_wukong_user = $groupUser->checkGroupUser(7,$_SESSION['userId']); 
                $is_wukong_style=$all_data->get_is_wukong(); 
                if($is_wukong_user && $is_wukong_style<>1)
                    exit('没有查看该款式详情的权限!');
                if(!$is_wukong_user && $is_wukong_style==1) 
                    exit('没有查看该款式详情的权限!');  
                $is_style_user = $groupUser->checkGroupUser(6,$_SESSION['userId']);                 
            }else
                $is_style_user=true;
        //}

        $product_type_id = $all_data->get_product_type();
        $product_type = $model->getProductTypeList($product_type_id);
        
        $cat_type_id = $all_data->get_style_type();
        $style_type = $model->getStyleTypeList($cat_type_id);
        $xilie=$model->getXilieById($id);
        $xilie_name=$model->getXilieNameByid($xilie);
        $arr=array(
            'view' => $all_data,
            'xilie_name'=>$xilie_name,
            'product_type' => $product_type,
            'style_type' => $style_type,
            'rank'=>$rank,
            'is_style_user' => $is_style_user,
        );
        if(isset($rank)&&intval($rank)!=1){
            $arr['bar']=Auth::getViewBar();
        }
        $this->render('base_style_info_show.html',$arr);
    }

   
    /**
     * 	insert，信息入库
     */
    public function insert($params) {
        $result = array('success' => 0, 'error' => '');
        $style_sn = _Post::getString('style_sn');
        $style_name = _Post::getString('style_name');
        $product_type = _Post::getString('product_type');
        $is_made = _Post::getInt('is_made');
        $style_type = _Post::getString('style_type');
        $sell_type = _Post::getString('sell_type');
        $style_remark = _Post::getString('style_remark');
        $dapei_goods_sn = _Post::getString('dapei_goods_sn');
        $changbei_sn = _Post::getInt('changbei_sn');
        $is_zp = _Post::getInt('is_zp');
        $style_sex = _Post::getInt('style_sex');
        $bang_type = _Post::getInt('bang_type');
        $xilie = _Post::getList('xilie');
        $market_xifen = _Post::getString('market_xifen');
        $is_xz=_Post::getInt('is_xz');
        $zp_price=_Post::getFloat('zp_price');
        $sale_way1=_Post::get('sale_way1');
        $sale_way2=_Post::get('sale_way2');
        $sale_way=$sale_way1.$sale_way2;
        $is_allow_favorable = _Post::get("is_allow_favorable");
        $is_gold = _Post::get("is_gold");
        $is_support_style = _Post::get("is_support_style");
        $company_type=_Post::getList('company_type');
        $jiajialv = _Post::get('jiajialv');
		if($sell_type==''){
			
			$sell_type=1;
		}
		if($product_type==''){
			$result['error'] = '产品线不能为空';
			Util::jsonExit($result);
		}
		if($style_type==''){
			$result['error'] = '款式分类不能为空';
			Util::jsonExit($result);
		}
		if($changbei_sn==''){
			$result['error'] = '是否常备款不能为空';
			Util::jsonExit($result);
		}
		if($is_zp==''){
			$result['error'] = '是否赠品不能为空';
			Util::jsonExit($result);
		}
		if($style_sex==''){
			$result['error'] = '款式性别不能为空';
			Util::jsonExit($result);
		}

        $newmodel = new BaseStyleInfoModel(12);
        $productModel = new AppProductTypeModel(11);
        $parent_id = $productModel->getParentIdById($product_type);
        if($parent_id <= 1){
            $result['error'] = '产品线只能选择最小级';
            Util::jsonExit($result);
        }
        $is_auto = false;
		 //拼接款号
         if(empty($style_sn)){
            $style_sn = $this->getStyleNumber(array('style_sex'=>$style_sex,'style_type'=>$style_type));
         }else{
            $is_auto = true;
            $check_style = $newmodel->getStyleBySn($style_sn);
            if(!empty($check_style)){
                $result['error'] = $style_sn.'款号已存在！';
                Util::jsonExit($result);
            }
         }
       
        //系列是复选框
        if(!empty($xilie)) {
            $xilie = implode(",", $xilie);
            $xilie=','.$xilie.',';
        } else {
        	$xilie = '';
        }
        if(count($company_type)>0){
            $company_type=implode(',',$company_type);
            $company_type=','.$company_type.',';
        }else{
            $company_type=null;
        }
        $olddo = array();
        $newdo = array();
        $newdo['style_sn'] = $style_sn;
        $newdo['style_name'] = $style_name;
        $newdo['product_type'] = $product_type;
        $newdo['is_made'] = $is_made;
        $newdo['style_type'] = $style_type;
        $newdo['sell_type'] = $sell_type;
        $newdo['create_time'] = date("Y-m-d H:i:s");
        $newdo['modify_time'] = date("Y-m-d H:i:s");
        $newdo['check_status'] = 1;
        $newdo['dismantle_status'] = 1;
        $newdo['dapei_goods_sn'] = $dapei_goods_sn;
        $newdo['changbei_sn'] = $changbei_sn;
        $newdo['is_zp'] = $is_zp;
        $newdo['style_sex'] = $style_sex;
        $newdo['bang_type'] = $bang_type;
        $newdo['xilie'] = $xilie;
        $newdo['market_xifen'] = $market_xifen;
        $newdo['style_remark'] = $style_remark;
        $newdo['is_xz']=$is_xz;
        $newdo['zp_price']=$zp_price;
        $newdo['sale_way']=$sale_way;
        $newdo['is_auto']=$is_auto == true?1:0;
        
        $newdo['is_allow_favorable'] = (int)$is_allow_favorable;
        $newdo['is_gold'] = (int)$is_gold;
        $newdo['is_support_style'] = (int)$is_support_style;
        $newdo['company_type_id']=$company_type;

        /**
         * 物控与废物空的区分
         */
        if($_SESSION['userType'] != 1){
            $groupUser = new GroupUserModel(1);
            $is_wukong = $groupUser->checkGroupUser(7,$_SESSION['userId']);
            if($is_wukong){
                if($jiajialv=='' || !is_numeric($jiajialv) ){
                    $result['error'] = '加价率不能为空或输入不合法';
                    Util::jsonExit($result);
                }
                $newdo['jiajialv'] = $jiajialv;
                $newdo['is_wukong'] = 1; //物控款
            }
        }
        //$zp =array();

        $res = $newmodel->saveData($newdo, $olddo);

        if ($res !== false ) {
            if($is_auto == false){
                $basemodel = new BaseStyleInfoModel($res,12);
                $id_len = strlen($res)*-1;
                $newStyleSn = substr($newdo['style_sn'],0,$id_len).$res;
                $basemodel->updateStylesn($res,$newStyleSn);
            }
            $result['success'] = 1;
        } else {
            $result['error'] = '添加失败';
        }
        $newmodel->addBaseStyleLog(array('style_id'=>$res,'remark'=>'添加款式'));
        Util::jsonExit($result);
    }

    /**
     * 	update，更新信息
     */
    public function update($params) {
        $zp['error'] = 0;

        $result = array('success' => 0, 'error' => '');
        $id = _Post::getInt('id');
        $style_sn = _Post::getString('style_sn');
        $style_name = _Post::getString('style_name');
        $product_type = _Post::getString('product_type');
        $is_sales = _Post::getInt('is_sales');
        $is_made = _Post::getInt('is_made');
        $style_type = _Post::getString('style_type');
        $sell_type = _Post::getString('sell_type');
        $style_remark = _Post::getString('style_remark');
        $old_style_sn = _Post::getString('old_style_sn');
        $dapei_goods_sn = _Post::getString('dapei_goods_sn');
        $changbei_sn = _Post::getInt('changbei_sn');
        $is_zp = _Post::getInt('is_zp');			
        $style_sex = _Post::getInt('style_sex');
        $bang_type = _Post::getInt('bang_type');
        $xilie = _Post::getList('xilie');
          $is_xz=_Post::getInt('is_xz');
        $zp_price=_Post::getFloat('zp_price');
        $sale_way1=_Post::get('sale_way1');
        $sale_way2=_Post::get('sale_way2');
        $sale_way=$sale_way1.$sale_way2;
        $market_xifen = _Post::getString('market_xifen');

        $newmodel = new BaseStyleInfoModel($id, 12);
        $check_status = $newmodel->getValue('check_status');
        $is_zpinsql = $newmodel->getValue('is_zp');
        $sale_wayinsql = $newmodel->getValue('sale_way');
        $is_xzinsql = $newmodel->getValue('is_xz');
        $zp_priceinsql = $newmodel->getValue('zp_price');
        $is_auto = $newmodel->getValue('is_auto');
        
        $is_allow_favorable = _Post::get("is_allow_favorable");
        $is_gold = _Post::get("is_gold");
        $is_support_style = _Post::get("is_support_style");
        $company_type= _Post::getList('company_type');
        $jiajialv = _Post::get('jiajialv');
		if($changbei_sn==''){
			$result['error'] = '是否常备款不能为空';
			Util::jsonExit($result);
		}
		if($is_zp==''){
			$result['error'] = '是否赠品不能为空';
			Util::jsonExit($result);
		}

        $groupUser = new GroupUserModel(1);
        $is_wukong = $groupUser->checkGroupUser(7,$_SESSION['userId']);

		//已审核的需要判断绑定赠品是否开启
		if($check_status ==3 && $is_zpinsql==2 && $sale_way!=$sale_wayinsql || $zp_priceinsql!=$zp_price || $is_xzinsql!=$is_xz || $is_zpinsql !=$is_zp){
			
			$bands = $newmodel->getZpStatusByStyle_sn($style_sn);
            
			if($bands['data']['status'] ==1){
				$result['error'] = '当前款号关联赠品为启用状态，禁止更改赠品信息';
				Util::jsonExit($result);
			}
			
		}
		//已审核的销账需要判断是否绑定订单
		if($check_status ==3 && $is_zpinsql==2 && $sale_way!=$sale_wayinsql || $zp_priceinsql!=$zp_price || $is_xzinsql!=$is_xz ){
			$order = $newmodel->getXzInfo($style_sn);
			if(!empty($order)){
				foreach ($order['data'] as $k=>$v){
				   
					if($v['order_status'] < 4){
						//订单关闭
						$result['error'] = '订单未关闭，不能进行修改赠品信息';
						Util::jsonExit($result);
					}
					if($v['send_good_status'] ==1){
						//订单已发货
						$result['error'] = '订单未发货，不能进行修改赠品信息';
						Util::jsonExit($result);
					}
                    
				}
			}
		}
		//可以修改
		
       /* if($check_status==3){
        	$result['error'] = '已审核状态的款式不可以编辑！';
        	Util::jsonExit($result);
        }*/
        //拼接款号
        //$style_sn = $this->getStyleNumber(array('product_type'=>$product_type,'style_type'=>$style_type,'not_style_id'=>$id));
       //系列是复选框
         //系列是复选框
        if($xilie){
            $xilie = implode(",", $xilie);
            $xilie=','.$xilie.',';
        }else{
            $xilie = '';
        }
        if(count($company_type)>0){
            $company_type=implode(',',$company_type);
            $company_type=','.$company_type.',';
        }else{
            $company_type=null;
        }
        $olddo = $newmodel->getDataObject();
        $old_check_status = $olddo['check_status'];

        $newdo = array();
        $newdo['is_allow_favorable'] = (int)$is_allow_favorable;
        $newdo['is_gold'] = (int)$is_gold;
        $newdo['is_support_style'] = (int)$is_support_style;
        if($is_wukong){
            if($jiajialv=='' || !is_numeric($jiajialv)){
                $result['error'] = '加价率不能为空或不合法';
                Util::jsonExit($result);
            }
            $newdo['jiajialv'] = $jiajialv;
        }

        $model = new BaseStyleInfoModel(12);
        if($old_check_status < 3){//&& $is_auto != 1

            if($style_sex==''){
                $result['error'] = '款式性别不能为空';
                Util::jsonExit($result);
            }
            $newdo['style_name'] = $style_name;
            $newdo['product_type'] = $product_type;
            $newdo['style_type'] = $style_type;
            $newdo['style_sex'] = $style_sex;

            //款式分类编辑，款号规则随之变化$is_auto != 1
            if($style_type != $olddo['style_type'])
            {
                $style_sn = $olddo['style_sn'];
                $old_style_sex = $olddo['style_sex'];
                $new_style_sn = $this->xiuGaiStylesnByStyleType($style_type,$style_sn,$old_style_sex);
                //确认生成款式是否已存在
                $check_where = array();
                $check_where['style_sn'] = $new_style_sn;
                $check_style_sn = $model->getStyleStyleByStyle_sn($check_where);
                if(count($check_style_sn) > 0 && $is_auto != 1){

                    $result['error'] = $new_style_sn.'款已存在不可更改款式分类！';
                    Util::jsonExit($result);
                }
                if($is_auto == 1) $new_style_sn = $style_sn;
                $model->updateStylesnByStyleId($id,$new_style_sn);
            }
            
            //款式性别编辑，款号规则随之变化
            if($style_sex != $olddo['style_sex'])
            {
                $style_sn = $olddo['style_sn'];
                $old_style_sex = $olddo['style_sex'];
                $new_style_sn = $this->xiuGaiStylesn($style_sex,$style_sn,$old_style_sex);
                //确认生成款式是否已存在
                $check_where = array();
                $check_where['style_sn'] = $new_style_sn;
                $check_style_sn = $model->getStyleStyleByStyle_sn($check_where);
                if(count($check_style_sn) > 0 && $is_auto != 1){

                    $result['error'] = $new_style_sn.'款已存在不可更改性别！';
                    Util::jsonExit($result);
                }
                if($is_auto == 1) $new_style_sn = $style_sn;
                $model->updateStylesnByStyleId($id,$new_style_sn);
            }
            
        }

        $oldCompany_type=$model->getCompany_type_id($olddo['style_sn']);

        $newdo['style_sex'] = $style_sex;
        $newdo['is_made'] = $is_made;
        $newdo['sell_type'] = $sell_type;
        $newdo['style_id'] = $id;
        $newdo['dapei_goods_sn'] = $dapei_goods_sn;
        $newdo['changbei_sn'] = $changbei_sn;
        $newdo['is_zp'] = $is_zp;
        $newdo['xilie'] = $xilie;
        $newdo['bang_type'] = $bang_type;
        $newdo['market_xifen'] = $market_xifen;
        $newdo['modify_time'] = date("Y-m-d H:i:s");
        //$newdo['check_status'] = 1;
       // $newdo['dismantle_status'] = 1;
        $newdo['style_remark'] = $style_remark;
          $newdo['is_xz']=$is_xz;
        $newdo['zp_price']=$zp_price;
        $newdo['sale_way']=$sale_way;
        $newdo['company_type_id']=$company_type;

        $pdo = $newmodel->db()->db();
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
        $pdo->beginTransaction();//开启事务
        $res = $newmodel->saveData($newdo, $olddo);
        
        $zps =array();
        $zps['style_sn'] = $style_sn;
        $zps['name'] = $style_name;
        $zps['goods_number'] = $style_sn;
        $zps['add_time'] =strtotime("now");
        $zps['update_time'] = strtotime("now");
        $zps['sale_way'] =$sale_way ;
        $zps['is_xz'] = $is_xz;
        $zps['sell_sprice'] = $zp_price;
        $zps['status'] = 1;       
       
        //启用赠品状态
        if($check_status ==3 && $is_zp==2){
       
        	if($res ==false){
        	      
        		$pdo->rollback();
        		$result['error'] = '修改失败';
        		Util::jsonExit($result);
        	}
            $zp_sn=$newmodel->getZpStatusByStyle_sn($zps['style_sn']);
          
             if(empty($zp_sn['data'])){
			
                $newdo = array();
                $newdo['name'] =  $zps['name'];
                // 			$newdo['num'] = $v['num'];
                // 			$newdo['min_num'] = $v['min_num'];
                // 			$newdo['price'] = $v['price'];
                // 			$newdo['sell_sprice'] = !empty($v['zp_price'])?$v['zp_price']:'';
                // 			$newdo['status'] = $v['status'];
                $newdo['goods_number'] = $zps['style_sn'];
                $newdo['add_time'] = strtotime("now");
                $newdo['update_time'] = strtotime("now");
                // 			$newdo['is_randring'] = $v['is_randring'];
                $newdo['sale_way'] =  $zps['sale_way'];
                $newdo['is_xz'] =$zps['is_xz'];
                $newdo['sell_sprice'] = $zps['sell_sprice'];
                $res1 = $newmodel->addZp($newdo);

                $log=$newmodel->setgiftgoodslog($style_sn,"款式库联动修改时添加赠品",$_SESSION['userName'],date("Y-m-d H:i:s"));
                $zp['error'] = 0;
            }
            else
            {
                $zp = $newmodel->updateZpStatusByStyle_sn($zps);
                
                $log=$newmodel->setgiftgoodslog($style_sn,"款式库联动修改赠品",$_SESSION['userName'],date("Y-m-d H:i:s"));
            }
             
        }

        $changeStyleName = $style_name!=$olddo['style_name'];
        if($changeStyleName){
            $newmodel->updateStyleNameByStyleSn($olddo['style_sn'],$style_name);
        }
        
		$newmodel->addBaseStyleLog(array('style_id'=>$id,'remark'=>'修改款式信息'));
        if ($res !== false && $zp['error'] ==0) {
        	$pdo->commit();
            $result['success'] = 1;
        } else {
            $result['error'] = '修改失败';
        }
        $model->setCompany_type_log($olddo['style_sn'],$company_type,$oldCompany_type);
        Util::jsonExit($result);
    }

    /**
     * 	delete，删除
     */
    public function delete($params) {
        $result = array('success' => 0, 'error' => '');
        $id = intval($params['id']);
        $model = new BaseStyleInfoModel($id, 12);
        //查询该款是否 审核通过  未通过不可以操作
        $ret = $model ->getStyleById($id);
        if(!empty($ret)&&$ret['check_status']==3){
        	$result['error'] = '此款已通过审核,不可以删除！';
        	Util::jsonExit($result);
        }
        
        $res = $model->delete();
        $model->addBaseStyleLog(array('style_id'=>$id,'remark'=>'删除'));
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = "删除失败";
        }
        Util::jsonExit($result);
    }

    /**
     * 	commit_apply，提交申请(支持批量操作)
     */
    public function commit_apply($params) {
        $ids = _Request::getList('_ids');
        if ($ids) {
            $error_gongfei = '';
            foreach($ids as $id) {
                $result = $this->_commit_apply($id);
                if($result['error_gongfei'] != ''){

                    $error_gongfei .= $result['error_gongfei'].",";
                }
                // 有一个不成功，就提示失败
                if (empty($result['success'])) break;
            }
        } else {
            $result = $this->_commit_apply();
            if($result['error_gongfei'] != ''){
                $error_gongfei = '';
                $error_gongfei = $result['error_gongfei'].",";
            }
        }
        //只有提交成功且工费为空的才提示需要添加工费的款；自由定制款需要提示
        if(!empty($result['success']) && $error_gongfei != '') 
        {
            $result['success'] = 0;
            $result['error'] = "申请成功！以下款没有维护工费信息：".rtrim($error_gongfei,",");
        }
        Util::jsonExit($result);
    }

    /**
     * 	commit_apply，提交申请
     */
    private function _commit_apply($id=0) {
        $result = array('success' => 0, 'error' => '', 'error_gongfei' => '');
        $id = _Post::getInt('id', $id);
        $style_sn = _Post::getString('style_sn');
        //判断  end
        $basemodel = new BaseStyleInfoModel($id, 12);
        $do = $basemodel->getDataObject();
        if ($do['check_status'] != 1) {
            $result['error'] = "款号：".$do['style_sn']." 只有保存的状态才可以申请审核";
            return $result;
        }
        $where = array(
            "style_id" => $id,
            "style_sn" => $style_sn
        );
        //审核通过款式时需校验：商品属性、属性信息、工厂信息、工费信息是否添加  start
        //属性信息
        $model = new RelStyleAttributeModel(11);
        $styleAttribute   = $model->getList($where);
        if(empty($styleAttribute[0])) {
            $result['error'] = "款号：".$do['style_sn']." 请确认属性信息是否已经添加？";
            return $result;
        }

        //工厂信息
        $model = new RelStyleFactoryModel(11);
        $factoryInfo = $model->getFactoryInfo($where);
        if(empty($factoryInfo)) {
            $result['error'] = "款号：".$do['style_sn']." 请确认工厂信息是否已经添加？";
            return $result;
        }

        //工费信息 table：app_style_fee
        $factoryFeeInfoModel = new AppStyleFeeModel(11);
        $factoryFeeinfo = $factoryFeeInfoModel->getStyleFee($id);

        if (empty($factoryFeeinfo[0]) && $do['is_made'] == 1){

            $result['error_gongfei'] = $do['style_sn'];
        }
        
        $basemodel->setValue('check_status', 2);
        $res = $basemodel->save(true);
        if ($res !== false) {
            $result['success'] = 1;
            $basemodel->addBaseStyleLog(array('style_id'=>$id,'remark'=>'提交审核'));
        } else {
            $result['error'] = '操作失败';
        }
        return $result;
    }

    /**
     * 	check_true，审核通过
     */
    public function check_true($params) {
        $ids = _Request::getList('_ids');
        $error_xiangkou = '';
        if ($ids) {
            foreach($ids as $id) {
                $result = $this->_check_true($id);
                if($result['error_xiangkou'] != ''){

                    $error_xiangkou .= $result['error_xiangkou'].",";
                }
                // 有一个不成功，就提示失败
                if (empty($result['success'])) break;
            }
        } else {
            $result = $this->_check_true();
            if($result['error_xiangkou'] != ''){
                $error_xiangkou = '';
                $error_xiangkou = $result['error_xiangkou'].",";
            }
        }
        //只有添加成功才提示需要生成商品的款；
        if(!empty($result['success']) && $error_xiangkou != '') 
        {
            $result['success'] = 0;
            $result['error'] = "审核成功！下列款号维护了商品属性，请及时生成商品信息：".rtrim($error_xiangkou,",");
        }
        Util::jsonExit($result);
    }
    /**
     * 	check_true，审核通过
     */
    private function _check_true($id=0) {
        $result = array('success' => 0, 'error' => '', 'error_xiangkou' => '');
        $id = _Post::getInt('id', $id);

        $model = new BaseStyleInfoModel($id, 12);
        $do = $model->getDataObject();
        if ($do['check_status'] == 3) {
            $result['error'] = "此款已审核了";
            return $result;
        }
        if ($do['check_status'] != 2) {
            $result['error'] = "待审核的才可以审核";
            return $result;
        }

        $_where = array();
        $_where['style_id'] = $do['style_id'];

        $xiangkouInfo = array();
        $xiangkouModel = new AppXiangkouModel(12);
        $xiangkouInfo = $xiangkouModel->getXiangKouByStyle_Id($_where);

        $error_xiangkou = '';
        if(!empty($xiangkouInfo) && $do['is_made'] == 1){

            $result['error_xiangkou'] = $do['style_sn'];
        }
        
        //保存赠品信息到赠品数据库giftgoods
        if($do['is_zp']==2){
            $styles = $model->getStyleById($id);
            $zp_sn=$model->getZpStatusByStyle_sn($styles['style_sn']);

            if(empty($zp_sn['data'])){
                $newdo = array();
                $newdo['name'] = isset($styles['style_name'])?$styles['style_name']:'';
                $newdo['goods_number'] = isset($styles['style_sn'])?$styles['style_sn']:'';
                $newdo['add_time'] =strtotime("now");
                $newdo['update_time'] = strtotime("now");
                $newdo['sale_way'] = isset($styles['sale_way'])?$styles['sale_way']:'';
                $newdo['is_xz'] = isset($styles['is_xz'])?$styles['is_xz']:1;
                $newdo['sell_sprice'] = isset($styles['zp_price'])?$styles['zp_price']:'';

                $model->addZp($newdo);
                $model->setgiftgoodslog($styles['style_sn'],"款式库联动添加赠品",$_SESSION['userName'],date("Y-m-d H:i:s"));
            } else {
                $result['error'] = "该款已经存在赠品,请确认你是否要添加赠品";
                Util::jsonExit($result);
            }
        }
        $model->setValue('check_status', 3);
        $model->setValue('check_time', date('Y-m-d H:i:s'));
        $res = $model->save(true);
       
        if ($res !== false ) {
            $result['success'] = 1;
            $model->addBaseStyleLog(array('style_id'=>$id,'remark'=>'已审核'));
        } else {
            $result['error'] = '操作失败';
        }
        return $result;
    }

    /**
     * 	invalid_recover，无效的恢复成有效
     */
    public function invalid_recover($params) {
        $result = array('success' => 0, 'error' => '');
        $id = _Post::getInt('id');
        $model = new BaseStyleInfoModel($id, 12);
        $do = $model->getDataObject();
        if ($do['check_status'] != 4) {
            $result['error'] = "只有无效的款才可以恢复";
            Util::jsonExit($result);
        }
        
        $model->setValue('check_status', 1);
        $res = $model->save(true);
        $model->addBaseStyleLog(array('style_id'=>$id,'remark'=>'无效恢复成有效'));
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = '操作失败';
        }
        Util::jsonExit($result);
    }
    /**
     * 	check_false，审核驳回
     */
    public function check_false($params) {
        $result = array('success' => 0, 'error' => '');
        $id = _Post::getInt('id');
        $model = new BaseStyleInfoModel($id, 12);
        $do = $model->getDataObject();
        if ($do['check_status'] == 4) {
            $result['error'] = "此款已经驳回了";
            Util::jsonExit($result);
        }
        if ($do['check_status'] != 2) {
            $result['error'] = "待审核的才可以驳回";
            Util::jsonExit($result);
        }
        $model->setValue('check_status', 4);
        $res = $model->save(true);
        $model->addBaseStyleLog(array('style_id'=>$id,'remark'=>'审核驳回'));
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = '操作失败';
        }
        Util::jsonExit($result);
    }

    /**
     * 	cancle_apply，申请作废
     */
    public function cancle_apply($params) {
        $id = intval($params["id"]);
        $result = array('success' => 0, 'error' => '');
        $model = new BaseStyleInfoModel($id, 12);
        $do = $model->getDataObject();

        $kuanpriceModel = new AppPriceByStyleModel(11);
        $kuanlist = $kuanpriceModel->getListByStyleid($id);
        if($kuanlist){
            foreach($kuanlist as $rule){
                if($rule['is_delete'] == 0){
                    $result['title'] = '提示';
                    $result['content'] = '请将款的按款定价规则全部作废，再申请作废款!';
                    Util::jsonExit($result);
                }
            }
        }

        if($do['check_status']==5){
            $result['content'] = "此款已经是申请作废了";
            Util::jsonExit($result);
        }

        if($do['check_status']==7){
            $result['content'] = "此款已经作废了";
            Util::jsonExit($result);
        }

        if($do['check_status']!=3){
            $result['content'] = "非审核通过的款不可以申请作废！";
            Util::jsonExit($result);
        }
    
        $styles = $model->getStyleById($id);
            
        $bands = $model->getZpStatusByStyle_sn($styles['style_sn']);
     
        if(!empty($bands['data']) && $bands['data']['status'] ==1){
              
              $result['content'] = "当前款号关联赠品为启用状态，禁止更改";
            Util::jsonExit($result);
        		
        }
         
        //更改状态
        $status = $model->updateStatusById($id);
        $result['content'] = $this->fetch('base_style_cancle_info.html', array(
            'view' => new BaseStyleInfoView($model)
        ));
           
        $result['title'] = '申请作废';
        Util::jsonExit($result);
    }
    /**
     * 	cancle_apply_submit，申请作废提交
     */
    public function cancle_apply_submit() {
        $result = array('success' => 0, 'error' => '');
        $id = _Post::getInt('id');
        $model = new BaseStyleInfoModel($id, 12);
        $do = $model->getDataObject();
        $type = _Request::getString('type');
        $remark = _Request::getString('remark');
        
        if($type == "其他" && empty($remark)){
           $result['error']="当选择其他时，请填写备注";
           Util::jsonExit($result);
        }
//        if($do['check_status']==5){
//            $result['error'] = "此款已经是申请作废了";
//            Util::jsonExit($result);
//        }
//        if($do['check_status']==7){
//            $result['error'] = "此款已经作废了";
//            Util::jsonExit($result);
//        }
        $model->setValue('check_status', 5);
        $res = $model->save(true);
        
        //作废后款商品下架，
        $style_sn = $do['style_sn'];
        $ListGoodsModel = new ListStyleGoodsModel(11); 
        $style_where = array('style_sn'=>$style_sn,'is_ok'=>0);
        $ListGoodsModel->UpdateListGoodsByStyleSn($style_where);
        //销售政策商品下架
        $apiSalePolicyModel = new ApiSalePolicyModel();
        $salepolicy_data = array(array('goods_sn'=>$style_sn,'is_sale'=>'0','is_valid'=>4,'type'=>1));
        $apiSalePolicyModel->UpdateSalepolicygoodIsSale(array('update_data'=>$salepolicy_data));
        //加申请作废原因
        $model->addCancleReason(array('style_id'=>$id,'remark'=>$remark,'type'=>$type));
        //加日志
        $model->addBaseStyleLog(array('style_id'=>$id,'remark'=>'申请作废'));
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = '操作失败';
        }
        Util::jsonExit($result);
    }

    /**
     * 	cancle_true，审核通过
     */
    public function cancle_true($params) {
        
        $id = intval($params["id"]);
        $result = array('success' => 0, 'error' => '');
        $model = new BaseStyleInfoModel($id, 12);
        $do = $model->getDataObject();
       
        if($do['check_status']!=5){
            $result['content'] = "只有作废中的才可以作废审核";
            Util::jsonExit($result);
        }
        $reason_info =array();
        //获取申请作废的原因
        $listCancleReasonModel = new ListCancleReasonModel(11);
        $reason_info = $listCancleReasonModel->getCancleReason($id);
        
        isset($reason_info['type']) || $reason_info['type']='';
        isset($reason_info['remark']) || $reason_info['remark']='';
        
        $styles = $model->getStyleById($id);
        
        $void = array('style_sn'=>$styles['style_sn'],'status'=>'-1');
        //在赠品管理中status=-1
       $void = $model->updateZpStatusByStyle_sn($void);
        
       if(empty($void['data'])){
	       	$result['title'] = '赠品状态更改失败';
	       	Util::jsonExit($result);
       }
        $log=$model->setgiftgoodslog($styles['style_sn'],"款式库联动禁用赠品",$_SESSION['userName'],date("Y-m-d H:i:s"));
        
        $result['content'] = $this->fetch('base_style_cancle_check.html', array(
            'view' => new BaseStyleInfoView($model),
            'reason_info'=>$reason_info,
        ));
        $result['title'] = '作废审核';
        Util::jsonExit($result);
    }
    
    /**
     * 	cancle_true_submit，审核通过
     */
    public function cancle_true_submit() {
        $result = array('success' => 0, 'error' => '');
        $id = _Post::getInt('id');
        $model = new BaseStyleInfoModel($id, 12);
        $do = $model->getDataObject(); 
        $check_status = intval(_Request::getInt('check_status'));
        $check_name = array(3=>'作废驳回',7=>'作废审核通过');

        $kuanpriceModel = new AppPriceByStyleModel(11);
        $kuanlist = $kuanpriceModel->getListByStyleid($id);
        if($kuanlist){
            foreach($kuanlist as $rule){
                if($rule['is_delete'] == 0){
                    $result['title'] = '提示';
                    $result['content'] = '请将款的按款定价规则全部作废，再申请作废款!';
                    Util::jsonExit($result);
                }
            }
        }

        if(!array_key_exists($check_status, $check_name)){
             $result['error'] = "数据的状态值有问题";
             Util::jsonExit($result);
        }

        
        /*
        //先判断是否有库存，如果有库存则不允许作废
        $style_sn = $do['style_sn'];
        $apiWarehouseModel = new ApiWarehouseModel();
        $warhouseGoods = $apiWarehouseModel->getGoodsInfoByGoods(array('goods_sn'=>$style_sn,'is_on_sale'=>2));
        //没有库存直接可以审核通过
        $is_kucu = 0;
        if($warhouseGoods['error'] == 0){
            $is_kucu =1;
            $result['error'] = "此款还有库存不能作废！";
           Util::jsonExit($result);
        }
         */
        
        $model->setValue('check_status', $check_status);
        $model->setValue('cancel_time', date("Y-m-d H:i:s"));
        $res = $model->save(true);
        $model->addBaseStyleLog(array('style_id'=>$id,'remark'=>$check_name[$check_status]));
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = '操作失败';
        }
        Util::jsonExit($result);
    }
    
    /**
     * 	cancle_false，作废驳回
     */
    public function cancle_false() {
        $result = array('success' => 0, 'error' => '');
        $id = _Post::getInt('id');
        $model = new BaseStyleInfoModel($id, 12);
        if($do['check_status']==6){
            $result['error'] = "此款的状态已经是作废已驳回";
            Util::jsonExit($result);
        }
        if($do['check_status']!=5){
            $result['error'] = "请先申请作废，才可以作废驳回";
            Util::jsonExit($result);
        }
        $model->setValue('check_status', 6);
        $res = $model->save(true);
        $model->addBaseStyleLog(array('style_id'=>$id,'remark'=>'作废已驳回'));
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = '操作失败';
        }
        Util::jsonExit($result);
    }

    
    
    /*
     * 生成商品
     */
	public function createStyleGoods(){
            $result = array('success' => 0, 'error' => '');
            $id = _Request::getInt('id');
            //获取属性的展示方式
            $appAttributeModel = new AppAttributeModel(11);
            //获取此款的产品线和分类
           
            $all_data = new BaseStyleInfoView(new BaseStyleInfoModel($id, 11));
            $product_type_id = $all_data->get_product_type();
            $cat_type_id = $all_data->get_style_type();
            $style_id = $all_data->get_style_id();
            $product_type_id = $all_data->get_product_type();
            $cat_type_id = $all_data->get_style_type();
            $style_id = $all_data->get_style_id();
            $style_sn = $all_data->get_style_sn();
            $product_type_id = 5;
            $cat_type_id = 1;
            $style_id = 1;
            
            $show_type= 3;
            //获取款对应的属性此产品线，分类的属性中：是多选的:如材质，指圈，镶口
            $styleAttributeModel = new RelStyleAttributeModel(11);
            $where = array('product_type_id'=>$product_type_id,'cat_type_id'=>$cat_type_id,'show_type'=>$show_type);
            $where = array('style_id'=>$style_id,'show_type'=>$show_type);
            $styleAttribute_data = $styleAttributeModel->getList($where);
//            var_dump($styleAttribute_data);
            $attr_num = count($styleAttribute_data);
            $attr_name = 'attr_name'.$attr_num;
            //获取属性值的id
            foreach ($styleAttribute_data as $val){
               $attribute_id = $val['attribute_id']; 
               $attribute_value = $val['attribute_value']; 
               $attr_value_arr[$attribute_id] = trim($attribute_value,",");
            }
            
            $attributeValueModel = new AppAttributeValueModel(11);
            
            //获取属性值id对应的名称
            foreach ($attr_value_arr as $key=>$val){
                $where_attr = array('att_value_id'=>$val);
                $tmp= $attributeValueModel->getAttributeValue($where_attr);
                $select_attribute_value[$key] = $tmp;
            }
            //
            foreach ($select_attribute_value as $a_key=>$a_val){
                $new = '';
                foreach ($a_val as $n_key=>$n_val){
                    $new .=$n_val['att_value_name'].',';
                }
                $new_select_attribute_value[$a_key] = rtrim($new,',');
            }
            $i = 1;
            /*foreach ($new_select_attribute_value as $key=>$val ){
                $a = 'style_new'.$i;
                $$a = explode(",", $val);
                $i ++;
            }*/
            //材质id：1
            //镶口id：24
            //指圈id：11
            $style_new1 = explode(",", $new_select_attribute_value[1]);
            $style_new2 = explode(",", $new_select_attribute_value[2]);
            $style_new3 = explode(",", $new_select_attribute_value[11]);
           
            $arr_data = array();
            foreach ($style_new1 as $a_val){
                foreach ($style_new2 as $b_val){
                    foreach ($style_new3 as $c_val){
                        $arr_data[] = $a_val.','.$b_val.','.$c_val;
                    }
                 }
            }
            
            if($arr_data){
                $listStyleGoodsModel = new ListStyleGoodsModel(11);
                $table_name = 'list_style_goods';
                $where_good = array('style_id'=>$style_id,'style_sn'=>$style_sn);
                
                $res = $listStyleGoodsModel->insertListGoods($table_name, $arr_data, $where_good);
                
                if($res){
                     $result['error'] = '操作成功';
                     Util::jsonExit($result);
                }else{
                     $result['error'] = '操作失败';
                     Util::jsonExit($result);
                }
            }else{
                $result['error'] = '没有镶口,材质，指圈';
                Util::jsonExit($result);
            }
	}
    
    //获取此产品线，此款式分类下的款号
	public function getStyleNumber($data) {
        if(!isset($data['style_sex'])){
            return false;
        }
        if(!isset($data['style_type'])){
             return false;
        }
        $style_sex = $data['style_sex'];
        $style_type = $data['style_type'];
        
        $style_sex_arr=array("1"=>"M","2"=>"W","3"=>"X");
        $catModel = new AppCatTypeModel(11);
        //$productModel = new AppProductTypeModel(11);
        $cat_code = $catModel->getCatCode($style_type);
        $style_sex = $style_sex_arr[$style_sex];
        $style_sn_prefix =  'KL'.$cat_code.$style_sex;
        
        $where = $data;
        $where['style_sn_prefix'] = $style_sn_prefix;
        $styleModel = new BaseStyleInfoModel(11);
        $info = $styleModel->getLatestStyleSnByWhere($where);
        while(true) {
            if(!empty($info)){        	        	
                $nextStyleNo = substr($info['style_sn'], -6)  + 1;
            }else{
                $nextStyleNo = 1;
            }
            
            $new_style_sn = $style_sn_prefix.str_pad($nextStyleNo, 6,'0',STR_PAD_LEFT);
            $existing_style = $styleModel->getStyleByStyle_sn(array('style_sn' => $new_style_sn));
            if (empty($existing_style)) {
                return $new_style_sn;
            } else {
                $info['style_sn'] = $new_style_sn;
            }
        }
	}
/*     public function getStyleNumber($data) {
        if(!isset($data['product_type'])){
            return false;
        }
        if(!isset($data['style_type'])){
             return false;
        }
        $product_type = $data['product_type'];
        $style_type = $data['style_type'];
        $styleModel = new BaseStyleInfoModel(11);
        
        $info = $styleModel->getStyleCountByWhere($data);
        
        if(!empty($info)){
            $nextStyleNo = intval(substr($info['style_sn'], -1,3)) + 1;
        }else{
            $nextStyleNo = 1;
        }
        
        $catModel = new AppCatTypeModel(11);
        $productModel = new AppProductTypeModel(11);
        $cat_code = $catModel->getCatCode($style_type);
        $product_code = $productModel->getProductCode($product_type);
        $style_sn = $product_code.$cat_code.str_pad($nextStyleNo, 3,'0',STR_PAD_LEFT);
        
        return $style_sn;
    } */

    /**
     * xiuGaiStylesn 款未审核之前编辑性别款式编号规则随之变化
     */
    public function xiuGaiStylesn($style_sex,$style_sn,$old_style_sex)
    {
        //定义性别与编号规则
        $sexAll = array();
        $sexAll = array(1=>'M',2=>'W',3=>'X');

        //计算出原性别编号最后出现的位置
        $m = strrpos($style_sn,$sexAll[$old_style_sex]);

        //替换旧编号
        if($style_sex == 1){

            $style_sn = substr_replace($style_sn,'M',$m,1);
        }elseif($style_sex == 2){

            $style_sn = substr_replace($style_sn,'W',$m,1);
        }elseif($style_sex == 3){

            $style_sn = substr_replace($style_sn,'X',$m,1);
        }
        return $style_sn;
    }

    /**
     * xiuGaiStylesnByStyleType 款未审核之前编辑款式分类款式编号规则随之变化
     */
    public function xiuGaiStylesnByStyleType($style_type,$style_sn,$old_style_sex)
    {
        //定义性别与编号规则
        $sexAll = array();
        $sexAll = array(1=>'M',2=>'W',3=>'X');

        //取出所有款式分类
        $styleTypeAll = array();
        $new_cat_data = array();
        $appCatModel = new AppCatTypeModel(11);
        $styleTypeAll = $appCatModel->getCtlListon();
        foreach ($styleTypeAll as $val){

            $new_cat_data[$val['cat_type_id']]=$val['cat_type_code'];
        }

        //过滤字符串中的字母保留数字
        $style_sn = preg_replace('/[\.a-zA-Z]/s','',$style_sn);

        //重新拼接款号
        $new_style_sn = 'KL'.$new_cat_data[$style_type].$sexAll[$old_style_sex].$style_sn;
        return $new_style_sn;
    }

    //批量导出款式基本信息
    public function downStyleInfo($params)
    {
        $ids = $params['ids'];
        $data = array();
        if($ids){
            $model = new BaseStyleInfoModel(11);
            $data = $model->getStyleInfoByid($ids);
        }
        $this->downStyleInfoExcel($data);
    }

    //导出
    public function downStyleInfoExcel($data)
    {
        $view = new BaseStyleInfoView(new BaseStyleInfoModel(11));
        $dd =new DictModel(1);
        if(!empty($data)){
                $xls_content = "款式编号,款式名称,款式分类,产品线,是否定制,是否销售,是否拆货,款式备注,搭配套系名称,是否常备款,款式性别,市场细分,是否是赠品,畅销度,是否绑定,可销售渠道,是否销账,赠品售价,是否允许改价,是否黄金,是否支持按款销售,公司类型\r\n";
                foreach ($data as $key => $val) {
                    $company_type_name = $view->getCompanyTypeName($val['company_type_id']);
                    $xls_content .= $val['style_sn'] . ",";
                    $xls_content .= $val['style_name'] . ",";
                    $xls_content .= $val['cat_type_name'] . ",";
                    $xls_content .= $val['product_type_name'] . ","; 
                    $xls_content .= $val['is_made'] === ''?'':$dd->getEnum('confirm',$val['is_made']) . ",";
                    $xls_content .= $val['is_sales'] === ''?'':$dd->getEnum('confirm',$val['is_sales']) . ",";
                    $xls_content .= $val['dismantle_status'] === ''?'':$dd->getEnum('style.dismantle_status',$val['dismantle_status']) . ",";
                    $xls_content .= $val['style_remark']. ",";
                    $xls_content .= $val['dapei_goods_sn'] . ",";
                    $xls_content .= $val['changbei_sn'] === ''?'':$dd->getEnum('style.changbei_sn',$val['changbei_sn']) . ",";
                    $xls_content .= $val['style_sex'] === ''?'':$dd->getEnum('style.style_sex',$val['style_sex']) . ",";
                    $xls_content .= $val['market_xifen'] . ",";
                    $xls_content .= $val['is_zp'] === ''?'':$dd->getEnum('style.is_zp',$val['is_zp']) . ",";
                    $xls_content .= $val['sell_type'] === ''?'':$dd->getEnum('sell_type',$val['sell_type']) . ",";
                    $xls_content .= $val['bang_type'] === ''?'':$dd->getEnum('style.bang_type',$val['bang_type']) . ",";
                    $xls_content .= $val['sale_way'] === ''?'':$dd->getEnum('style.sale_way',$val['sale_way']) . ",";
                    $xls_content .= $val['is_xz'] === ''?'':$dd->getEnum('style.is_xz',$val['is_xz']) . ",";
                    $xls_content .= $val['zp_price'] . ",";
                    $xls_content .= $val['is_allow_favorable'] === ''?'':$dd->getEnum('confirm',$val['is_allow_favorable']) . ",";
                    $xls_content .= empty($val['is_gold'])?'非黄金':$dd->getEnum('style.is_gold',$val['is_gold']) . ",";
                    $xls_content .= $val['is_support_style'] === ''?'':$dd->getEnum('is_support_style',$val['is_sales']) . ",";
                    $xls_content .= $company_type_name . "\n";
                    
                   // $xls_content .= $dd->getEnum('warehouse_goods.tuo_type',$val['tuo_type']) . ",";
                    //$xls_content .= $val['goods_sn1'] . "\n";
                }
            } else {
                $xls_content = '没有数据！';
            }
            header("Content-type:text/csv;charset=gbk");
            header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . ".csv");
            header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
            header('Expires:0');
            header('Pragma:public');
            echo iconv("utf-8", "gbk//IGNORE", $xls_content);
            exit;
        }       
}

?>