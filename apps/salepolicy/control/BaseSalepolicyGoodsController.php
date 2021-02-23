<?php

/**
 *  -------------------------------------------------
 *   @file		: BaseSalepolicyGoodsController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-03 18:25:10
 *   @update	:
 *  -------------------------------------------------
 */
class BaseSalepolicyGoodsController extends CommonController {

    protected $smartyDebugEnabled = false;

    /****
    获取公司 列表
    ****/
    public function company()
    {
        $model     = new CompanyModel(1);
        $company   = $model->getCompanyTree();//公司列表
        return $company;
    }

    /**
     * 	index，搜索框
     */
    public function index($params) {
        $zhengshuleibie = array('NGDTC','GIA','IGI','NGTC','HRD','AGL','EGL','NGGC','NGSTC','HRD-D');
        $color_arr = array('D','D-E','E','F','F-G','G','H','I','I-J','J','K');
        $clarty_arr= array('FL','IF','VVS', 'VVS1','VVS2','VS', 'VS1','VS2','SI', 'SI1','SI2');
        $jinshi_type = array('3D','精工','普通');
        $jintuo_type = array('成品','女戒','空托女戒');
        $zhushi = array('钻石','蓝宝','红宝','珍珠','翡翠','锆石','水晶','珍珠贝','和田玉','砭石','玛瑙','砗磲','淡水珍珠','海水珍珠');
        $model_p = new ApiProModel();
        $apimodel = new ApiSalepolicyGoodsModel();
        $model = new ApiStyleModel(21);
        $zhuchengseList = $model->getZhuchengseList();
        $chanpinxian = $model->getProductTypeInfo();
        foreach ($chanpinxian as $k => $v) {
            if($v['parent_id'] == 1){
                unset($chanpinxian[$k]);
            }
        }
        $pro_list = $model_p->GetSupplierList(array('status'=>1));
        $cgt = $apimodel->getCategoryType();
        unset($cgt[0]);

        $model = new BaseSalepolicyGoodsModel(17);
        $Stone = $model->getStone();
        $Finger = $model->getFinger();
        
        $goodsAttrModel = new GoodsAttributeModel(17);
        $Caizhi = $goodsAttrModel->getCaizhiList();
        $Yanse = $goodsAttrModel->getJinseList();
        
        $this->render('base_salepolicy_goods_search_form.html', array(
            'bar' => Auth::getBar(),
            'cgt' => $cgt,
            'companylist' => $this->company(),
            'zhuchengseList' =>$zhuchengseList,
            'pro_list' => $pro_list,
            'color_arr' => $color_arr,
            'clarty_arr' => $clarty_arr,
            'jintuo_type' => $jintuo_type,
            'jinshi_type' => $jinshi_type,
            'zhengshuleibie' => $zhengshuleibie,
            'chanpinxian' => $chanpinxian,
            'zhushi' => $zhushi,
            'Stone' => $Stone,
            'Finger' => $Finger,
            'Caizhi' => $Caizhi,
            'Yanse' => $Yanse,
            ));
    }

    /**
     * 	search，列表
     */
    public function search($params) {

        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'goods_id' => _Request::getString('goods_id'),
            'goods_sn' => _Request::getString('goods_sn'),
            'price_start' => _Request::getString('price_start'),
            'price_end' => _Request::getString('price_end'),
            'stone' => _Request::getString('stone'),
            'finger' => _Request::getString('finger'),
            'caizhi' => _Request::getString('caizhi'),
            'yanse' => _Request::getString('yanse'),
            'isXianhuo' => _Request::get('isXianhuo'),
        	'is_sale' => _Request::get('is_sale'),
            'company_id'    => _Request::get("company_id"),
            'warehouse_id'  => _Request::get("warehouse_id"),
            'zhengshuhao'   => _Request::get("zhengshuhao"),
            'zhengshuleibie' => _Request::get("zhengshuleibie"),
            'zs_color'   => _Request::get("zs_color"),
            'zs_clarity' => _Request::get("zs_clarity"),
            'jinshi_type' => _Request::get("jinshi_type"),
            'jintuo_type' => _Request::get('jintuo_type'),
            'chanpinxian' => _Request::get('chanpinxian'),
            'shoucun'    => _Request::get("shoucun"),
            'processor'  => _Request::get("processor"),
            'mohao'      => _Request::get("mohao"),
            'zhushi'     => _Request::get("zhushi"),
            'zhuchengse'=> _Request::get("zhuchengse"),
            'is_policy'=> _Request::getInt("is_policy"),
            'category' => _Request::get('category')
        );
        $args['goods_id']=str_replace('，',' ',$args['goods_id']);
		$args['goods_id']=trim(preg_replace('/(\s+|,+)/',' ',$args['goods_id']));
        $page = _Request::getInt("page", 1);
        $where = array();
        $where['goods_sn'] = $args['goods_sn'];
        $where['goods_id'] = $args['goods_id'];
        $where['price_start'] = $args['price_start'];
        $where['price_end'] = $args['price_end'];
        $where['stone'] = $args['stone'];
        $where['finger'] = $args['finger'];
        $where['caizhi'] = $args['caizhi'];
        $where['yanse'] = $args['yanse'];
        $where['isXianhuo'] = $args['isXianhuo'];
        $where['is_sale'] = $args['is_sale'];
        $where['category'] = $args['category'];
        $where['zhuchengse'] = $args['zhuchengse'];
        $where['zhushi'] = $args['zhushi'];
        $where['mohao'] = $args['mohao'];
        $where['processor'] = $args['processor'];
        $where['shoucun'] = $args['shoucun'];
        $where['jintuo_type'] = $args['jintuo_type'];
        $where['jinshi_type'] = $args['jinshi_type'];
        $where['zs_clarity'] = $args['zs_clarity'];
        $where['zs_color'] = $args['zs_color'];
        $where['zhengshuleibie'] = $args['zhengshuleibie'];
        $where['zhengshuhao'] = $args['zhengshuhao'];
        $where['warehouse_id'] = $args['warehouse_id'];
        $where['company_id'] = $args['company_id'];
        $where['chanpinxian'] = $args['chanpinxian'];
        $where['is_policy'] = $args['is_policy'];

        $model = new BaseSalepolicyGoodsModel(17);
        
        $goodsAttrModel = new GoodsAttributeModel(17);
        $Caizhi = $goodsAttrModel->getCaizhiList();
        $Yanse = $goodsAttrModel->getJinseList();
        
    
        $data = $model->pageList($where, $page, 10, false);
        if($data['data']){

            $goods_ids = implode("','",array_column($data['data'],'goods_id'));
            //取仓库的货品状态
            $wapimodel = new ApiWarehouseModel();
            $is_on_sales = array_column($wapimodel->getWaregoodisonsale($goods_ids),'is_on_sale','goods_id');

            foreach ($data['data'] as &$val){
                $val['product_type_name'] = $model->getProductTypeName($val['product_type']);
                $val['cat_type_name'] = $model->getCatTypeName($val['category']);
                $val['is_on_sale']=isset($is_on_sales[$val['goods_id']])?$is_on_sales[$val['goods_id']]:0;
            }
        }


        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'base_salepolicy_goods_search_page';

        $this->render('base_salepolicy_goods_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data,
            'Caizhi' => $Caizhi,
            'Yanse' => $Yanse,
        ));
    }

    /**
     * 	add，渲染添加页面
     */
    public function add() {
        $result = array('success' => 0, 'error' => '');
        $result['content'] = $this->fetch('base_salepolicy_goods_info.html', array(
            'view' => new BaseSalepolicyGoodsView(new BaseSalepolicyGoodsModel(17))
        ));
        $result['title'] = '添加';
        Util::jsonExit($result);
    }

    /**
     * 	edit，渲染修改页面
     */
    public function edit($params) {
        $id = intval($params["id"]);
        $tab_id = _Request::getInt("tab_id");
        $result = array('success' => 0, 'error' => '');
        $result['content'] = $this->fetch('base_salepolicy_goods_info.html', array(
            'view' => new BaseSalepolicyGoodsView(new BaseSalepolicyGoodsModel($id, 17)),
            'tab_id' => $tab_id
        ));
        $result['title'] = '编辑';
        Util::jsonExit($result);
    }

    /**
     * 	show，渲染查看页面
     */
    public function show($params) {
        $id = intval($params["id"]);
        $this->render('base_salepolicy_goods_show.html', array(
            'view' => new BaseSalepolicyGoodsView(new BaseSalepolicyGoodsModel($id, 17)),
            'bar' => Auth::getViewBar()
        ));
    }

    public function batchGoods($param) {
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
     * 可销售商品上架
     * @param type $param
     */
    public function upSale($param) {
        $ids = _Request::getList('_ids');
        if(empty($ids)){
            $result['error'] = "请选中数据！";
            Util::jsonExit($result);
        }
        
        //查看选中数据，如果没有下架的，则说明都已经上架了
        $model = new BaseSalepolicyGoodsModel(18);
        $info = $model->getSaleStitice($ids);
        $is_sale = true;//默认都是上架
        
        $valid_data = array();
        foreach ($info as $val){
            if($val['is_sale']==0){//下架
                $is_sale = false;
                //$sale_data[$val['id']] = $val['is_sale'];
                $valid_data[$val['id']] = $val['is_valid'];
            }
        }
        
        if($is_sale){
            $result['error'] = "当前商品已经是上架状态！";
            Util::jsonExit($result);
        }
        
        //判断商品的状态
        $change_data = array();
        foreach ($valid_data as $key=>$val){
            /*   目前状态有错，为了不影响销售，同意上架 张园园
            if($val==1){//只有=1的才可以上架
                 $change_data[] = $key;
            }*/
	    	$change_data[] = $key;
			
        }
		
		//根据base_salepolicy_goods中的id 去获取商品是否有绑定过销售政策
		
        if(empty($change_data)){
            $result['error'] = "只有商品的有效状态，才能上架！";
            Util::jsonExit($result);
        }
		
		//根据base_salepolicy_goods中的id 去获取商品是否有绑定过销售政策
		$waring = '';
		foreach($change_data as $key)
		{
			$salepolicy = $model->getSalepolicyinfo($key);
			if(!empty($salepolicy))
			{
				foreach($salepolicy as $obj)
				{
					$waring .='商品'.$obj['goods_name'].'已经绑定过销售政策:"'.$obj['policy_name'];
					$waring .= "该政策不是已审核状态<br/>";
				}
			}
		}
		if($waring !="" || !empty($waring))
		{
			$result['error'] = $waring."货品批量上架失败<br/>";
			Util::jsonExit($result);
		}
		
        $id_in = implode(",", $change_data);
        $update_where = array('id_in'=>$id_in,'is_sale'=>1);
        $res = $model->updateSalePolicyStatus($update_where);
		
		
        if ($res !== false) {
			$result['success'] = 1;
        } else {
            $result['error'] = "上架失败";
        }
        Util::jsonExit($result);
    }

    /**
     * 可销售商品下架
     * @param type $param
     */
    public function downSale($param) {
        $ids = _Request::getList('_ids');
        if(empty($ids)){
            $result['error'] = "请选中数据！";
            Util::jsonExit($result);
        }
        
        //查看选中数据，如果没有上架的，则说明都已经下架了
        $model = new BaseSalepolicyGoodsModel(18);
        $info = $model->getSaleStitice($ids);
        $is_sale = true;//默认都是下架
        
        $sale_data = array();
        foreach ($info as $val){
            if($val['is_sale']==1){//上架
                $is_sale = false;
                $sale_data[] = $val['id'];
            }
        }
        
        if($is_sale){
            $result['error'] = "当前商品已经是下架状态！";
            Util::jsonExit($result);
        }
        
        $id_in = implode(",", $sale_data);
        $update_where = array('id_in'=>$id_in,'is_sale'=>0);
        $res = $model->updateSalePolicyStatus($update_where);
      
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = "下架失败";
        }
        Util::jsonExit($result);
    }

    /**
     * 	insert，信息入库
     */
    public function insert($params) {
        $result = array('success' => 0, 'error' => '');
        echo '<pre>';
        print_r($_POST);
        echo '</pre>';
        exit;
        $olddo = array();
        $newdo = array();

        $newmodel = new BaseSalepolicyGoodsModel(18);
        $res = $newmodel->saveData($newdo, $olddo);
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = '添加失败';
        }
        Util::jsonExit($result);
    }

    /**
     * 	insert，信息入库
     */
    public function batchInsert($params) {
        $result = array('success' => 0, 'error' => '');
        $ids = _Request::getString('_ids');
        $ids_arr = explode(",",$ids);
        $policy_id = _Request::getInt('policy_id');
		$baseSalepolicyInfoModel =  new BaseSalepolicyInfoModel($policy_id,18);
        $baseSalepolicyInfo = $baseSalepolicyInfoModel->getDataObject();
		$bsi_status = $baseSalepolicyInfo['bsi_status'];
        $is_kuanprice = $baseSalepolicyInfo['is_kuanprice'];
        $product_type = $baseSalepolicyInfo['product_type'];
        $cat_type = $baseSalepolicyInfo['cat_type'];
        $tuo_type = $baseSalepolicyInfo['tuo_type'];
        $zhushi_begin = floatval($baseSalepolicyInfo['zhushi_begin']);
        $zhushi_end = floatval($baseSalepolicyInfo['zhushi_end']);
        if(empty($zhushi_begin)){
            $zhushi_begin = 0;
        }
        if(empty($zhushi_end)){
            $zhushi_end = 99999;
        }
        
        $appSalepolicyGoodsModel =  new AppSalepolicyGoodsModel(18);
		//款式分类
		$apiStyleModel = new ApiStyleModel();
		$catList = $apiStyleModel->getCatTypeInfo();

		
		//产品线
		$baseSalepolicyInfoModel = new BaseSalepolicyInfoModel(17);
		$allproducttype = $baseSalepolicyInfoModel->getallproductype();

        
        //var_dump($ids_arr);exit;
        foreach($ids_arr as $id){
        	$newmodel = new BaseSalepolicyGoodsModel($id,18);
        	$is_sale = $newmodel->getValue("is_sale");       	
        	$goods_id = $newmodel->getValue("goods_id");
        	if($is_sale==0){
        		$result['error'] = '下架的货品不可以添加销售政策';
        		Util::jsonExit($result);
        	}
            $goods_infos = $appSalepolicyGoodsModel->getWaregoOdisonsale($goods_id);
            if($goods_infos)
            {
                $goods_info = $goods_infos[0];
            }

            $goods_age_info = $appSalepolicyGoodsModel->getWaregoodisAgeonsale($goods_id);

            $goods_product_type = $goods_info['product_type1'];
            $goods_cat_type1 = $goods_info['cat_type1'];
            $goods_tuo_type = $goods_info['tuo_type'];
            $goods_zhushidaxiao = floatval($goods_info['zuanshidaxiao']);
            
            if($tuo_type != 0){
                if($tuo_type != $goods_tuo_type){
                    $result['error'] = '托类型不匹配，不能添加到销售政策里!';
                    Util::jsonExit($result);
                }
            }
            if($goods_zhushidaxiao >= $zhushi_begin && $goods_zhushidaxiao <= $zhushi_end){
            }else{
                $result['error'] = '主石大小不匹配，不能添加到销售政策里!';
                Util::jsonExit($result);
            }
            $supportCats = $this->supportCats($catList,$goods_cat_type1,$cat_type) ;
            if(!$supportCats)
            {
                $result['error'] = "销售政策款式分类:".$cat_type.",与货号{$goods_id}:".$goods_cat_type1.'不匹配，不能添加到销售政策里! ';
                Util::jsonExit($result);
            }
            $supportProductLine = $this->supportProductLine($allproducttype,$goods_product_type,$product_type);
            if(!$supportProductLine)
            {
                $result['error'] = '产品线不匹配，不能添加到销售政策里!';
                Util::jsonExit($result);
            }
        }


        $olddo = array();
        $model = new BaseSalepolicyGoodsModel(17);
        $policyModel = new BaseSalepolicyInfoModel($policy_id,17);
        $newmodel =  new AppSalepolicyGoodsModel(18);
        $arrList = $model->getListByIds($ids);
        $newdo = array();
        $goods_id_list = array();
        $is_kuanprice = $policyModel->getValue('is_kuanprice');
        foreach ($arrList as $val) {
            //计算销售价格
            $sale_price = 0;
            if($is_kuanprice){
                $p = $model->getKuanPrice($val['goods_id']);
                if($p){
                    $sale_price = $p;
                }else{
                    $result['error'] = '商品 '.$goods_id.'不是按款定价的商品，请确认';
                    Util::jsonExit($result);
                }
            }else{
                $sale_price = round($val['chengbenjia'] * $policyModel->getValue('jiajia') + $policyModel->getValue('sta_value')) ;
            }
            $goods_id_list[] = $val['goods_id'];
            $newdo[$val['goods_id']] = array(
                'policy_id' => $policy_id,
                'goods_id' => $val['goods_id'],
                'isXianhuo' => $val['isXianhuo'],
                'chengben' => $val['chengbenjia'],
                'jiajia'=>$policyModel->getValue('jiajia'),
                'sta_value'=>$policyModel->getValue('sta_value'),
                'sale_price' => $sale_price,
                'create_time' => date("Y-m-d H:i:s"),
                'create_user' => $_SESSION['userName'],
            );
        }
        $goodsIdList = $newmodel->get_goods_id_by_ids($goods_id_list,$policy_id);
        $error = '';
        if(count($goodsIdList) > 0){
            foreach ($goodsIdList as $k => $v) {
                $tmp_data[$k] = $v['goods_id'];
            }
            $goodsIdLists = array_unique($tmp_data);
            $error_list = array();
            foreach ($goodsIdLists as $val){
                unset($newdo[$val]);
                $error_list[] = $val;
            }
            if(count($error_list)>0){
                foreach ($error_list as $v){
                        $error .= "货号{$v}在该销售政策已生成<br/>";
                }
            }
        }
        $error_list = array();
        foreach($newdo as $k=>$v){
            if($v['sale_price']<0){
                $error_list[] = $v['goods_id'];
                unset($newdo[$k]);
            }
        }

        if(count($error_list)>0){
            foreach ($error_list as $v){
                $error .= "货号{$v}生成销售商品时销售价小于0<br/>";
            }
        }

        $res = false;
        if(count($newdo)>0){
            $newdo = array_values($newdo);
            $res = $newmodel->insertAll($newdo);
            $goods_id_in = array_column($newdo,'goods_id');
            $_model = new BaseSalepolicyGoodsModel(18);
            $_model->updateGoodsIsPolicy($goods_id_in);
        }
        if ($res !== false) {
            $result['success'] = 1;
            $result['title'] = '批量添加成功<br/>'.$error;
        } else {
            $result['error'] = '批量添加失败<br/>'.$error;
        }
        Util::jsonExit($result);
    }

    /**
     * 	update，更新信息
     */
    public function update($params) {
        $result = array('success' => 0, 'error' => '');
        $_cls = _Post::getInt('_cls');
        $tab_id = _Post::getInt('tab_id');

        $id = _Post::getInt('id');
        echo '<pre>';
        print_r($_POST);
        echo '</pre>';
        exit;

        $newmodel = new BaseSalepolicyGoodsModel($id, 18);

        $olddo = $newmodel->getDataObject();
        $newdo = array(
        );

        $res = $newmodel->saveData($newdo, $olddo);
        if ($res !== false) {
            $result['success'] = 1;
            $result['_cls'] = $_cls;
            $result['tab_id'] = $tab_id;
            $result['title'] = '修改此处为想显示在页签上的字段';
        } else {
            $result['error'] = '修改失败';
        }
        Util::jsonExit($result);
    }

    /**
     * 	delete，删除
     */
    public function delete($params) {
        $result = array('success' => 0, 'error' => '');
        $id = intval($params['id']);
        $model = new BaseSalepolicyGoodsModel($id, 18);
        $do = $model->getDataObject();
        $valid = $do['is_system'];
        if ($valid) {
            $result['error'] = "当前记录为系统内置，禁止删除";
            Util::jsonExit($result);
        }
        $model->setValue('is_deleted', 1);
        $res = $model->save(true);
        //联合删除？
        //$res = $model->delete();
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = "删除失败";
        }
        Util::jsonExit($result);
    }

    public function packageGoods(){
        $result = array('success' => 0, 'error' => '');
        $ids = _Post::getList('_ids');
        $model_s = new BaseSalepolicyGoodsModel(17);
        $is_sale = $model_s->getSaleStitice($ids);
        foreach ($is_sale as $key => $value) {
            foreach ($value as $k => $v) {
                if($v==0){
                    $result['error'] = "下架的货品不可以打包生成销售商品！";
                    Util::jsonExit($result);
                }
            }
        }
        $model = new BaseSalepolicyGoodsModel(18);
        $res = $model->settype($ids,2);
        if(!$res){
            $result['error']="批量打包销售商品失败";
        }else{
             $result['success']=1;

        }

        Util::jsonExit($result);
    }

    /**
     * 二级联动 根据公司，获取选中公司下的仓库
     */
    public function warehouse(){
        $to_company_id = _Request::get('id');
        $model_api = new ApiWarehouseModel(1);
        $warehouse = $model_api->getWarehouseTree($to_company_id);
        $this->render('option.html',array(
                'data'=>$warehouse,
        ));
    }

    public function getRel(){
        $b_id = _Request::get('id');
        if(empty($b_id)){
            return false;
        }
        $model = new BaseSalepolicyGoodsModel(17);
        $listinfo = $model -> getAppInfoList($b_id);
        $res = $this->fetch('goods_info_ajax.html',array('listinfo'=>$listinfo));
        echo $res;
    }
    function supportCats($catList,$goods_cat_type1,$cat_type)
    {
        if($cat_type == '全部')
        {
            return true;
        }
        return $cat_type == $goods_cat_type1;
    }

    function supportProductLine($allproducttype,$goods_product_type,$product_type)
    {
        if($product_type == '全部')
        {
            return true;
        }
        return $goods_product_type == $product_type;
    }


}

?>
