<?php
/**
 * 按款定价商品管理（官网数据对接）
 *  -------------------------------------------------
 *   @file		: GoodsPriceByStyleController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *  -------------------------------------------------
 */
class GoodsPriceByStyleController extends CommonController {

    protected $smartyDebugEnabled = true;

    public $whitelist = array('getAttrHtml','getAttrPriceList','getAttrSalepolicyList','exportCSV','importCSV');
    /**
     * 	index，搜索框
     */
    public function index($params) {
        
        $new_product_data= array();
        $productModel = new AppProductTypeModel(11);
        $product_data = $productModel->getCtlList();
        foreach ($product_data as $val){
            $new_product_data[$val['product_type_id']]=$val['product_type_name'];
        }
        //获取分类名称
        $new_cat_data= array();
        $appCatModel = new AppCatTypeModel(11);
        $cat_data = $appCatModel->getCtlListon();
        foreach ($cat_data as $val){
            $new_cat_data[$val['cat_type_id']] = $val['cat_type_name'];
        }
        
        $this->render('goodsprice_by_style_search_form.html', array(
			'bar' => Auth::getBar(),
            'cat_data'=>$new_cat_data,
            'product_data'=>$new_product_data,
            'view' => new BaseStyleInfoView(new BaseStyleInfoModel(11)),
            'viewproduct'=>new AppProductTypeView(new AppProductTypeModel(11)),
            'viewcat'=>new AppCatTypeView(new AppCatTypeModel(11)),
            
        ));
        
    }
    /**
     * 搜索
     * @param unknown $params
     */
    public function search($params){
        
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
        //print_r($new_cat_data);
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'style_sn' => _Request::getString('style_sn'),
            'product_type_id' => _Request::getInt('product_type_id'),
            'cat_type_id' => _Request::getInt('cat_type_id'),
            'style_sex' => _Request::getInt('style_sex'),
        );
        
        $args['style_sn'] = str_replace(","," ",$args['style_sn']);
        $args['style_sn'] = preg_replace('/\s+/is'," ",$args['style_sn']);        
        $style_sn_arr = $args['style_sn']?explode(' ',$args['style_sn']):array();
        $where = array(
            'style_sn' => $style_sn_arr,
            'product_type_id' => _Request::getInt('product_type_id'),
            'cat_type_id' => _Request::getInt('cat_type_id'),
            'style_sex' => _Request::getInt('style_sex'),
            'is_delete' => 0//未删除
        );
        $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;
        
        $model = new AppGoodsPriceByStyleModel(11);//52只读，11可写
        $data = $model->pageList($where, $page,$pageSize=20);
        if(!empty($data['data'])){
            $galleryModel = new AppStyleGalleryModel(11);//52只读，11可写
            foreach($data['data'] as $key=>$vo){
                $image_arr = $galleryModel->getStyleGalleryRow($vo['style_sn']);
                $vo['style_image']  = isset($image_arr['thumb_img'])?$image_arr['thumb_img']:'';
                $data['data'][$key] = $vo;
            }
        }
        //print_r($data);        
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'goodsprice_by_style_search_page';
        $this->render('goodsprice_by_style_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data
        ));
    }
    /**
     * 查看款式详情
     * @param unknown $params
     */
    public function show($params){
        $style_sn = _Request::getString('id');
        
        $model = new AppGoodsPriceByStyleModel(11);
        $attr_data = $model->getListBySn($style_sn,'attr_data');
        $attr_title = array();
        foreach($attr_data as $key=>$data){
           $arr = json_decode($data['attr_data'],true);
           $arr = $arr[$style_sn];
           $attr_data[$key] = $arr;
           if($key == 0){
               foreach ($arr as $vo){
                    $attr_title[] = $vo['spec_name'];
               }
           }
        }       
        $this->render('goodsprice_by_style_show.html', array(
            'bar' => Auth::getBar(),
            'attr_title'=>$attr_title,
            'attr_data' => $attr_data,        
        ));
    }
    /**
     * 修改编辑
     * @param unknown $params
     */
    public function edit($params){
        $id = _Request::getString("id");
        $model = new AppGoodsPriceByStyleModel(11);
        $row = $model->getRowById($id);
        $style_sn = empty($row['style_sn'])?'':$row['style_sn'];
        $this->render('goodsprice_by_style_edit.html', array('style_sn'=>$style_sn));
    }
    /**
     * 修改更新数据
     * @param unknown $params
     */
    public function update($params){
        $this->insert($params);
    }
    /**
     * 添加
     * @param unknown $params
     */
    public function add($params){ 
        $style_sn = "";
        $this->render('goodsprice_by_style_edit.html', array('style_sn'=>$style_sn));
    }
    /**
     * 添加入库
     * @param unknown $params
     */
    public function insert($params){
        
        //ini_set('memory_limit', "500M");
        
        $result = array('error'=>'','success'=>0);
        $style_sn = _Request::getString('style_sn');
        $_style_sn = _Post::getString("_style_sn");
        
        $attr_select = _Request::getList('attr');       
        if(count($attr_select)==0){
            $result['error'] = "请勾选属性组合。";
            Util::jsonExit($result);
        }
        $attr_select1 = array();
        foreach ($attr_select as $k=>$v){
            ksort($v);
            $attr_select[$k] = $v;//所选属性键值升序排序
            foreach ($v as $kk=>$vv){
                $attr_select1[$k."|".$kk] = $vv;
            }
        }
        $attr_select2 = $this->_cartesian($attr_select1);

        if(empty($attr_select)){
            $result['error'] = "属性组合不能为空";
            Util::jsonExit($result);
        }else if($style_sn != $_style_sn){
            $result['error'] = "款号已发生变化，属性值未更新！请点击搜索更新款号的最新属性！";
            Util::jsonExit($result);
        }
       
        $model = new AppGoodsPriceByStyleModel(11);//52只读 ，11可写可读        
        $pdo = $model->db()->db();
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
        $pdo->beginTransaction();//开启事务
        
        $attrList = $this->_getAttrListBySN($style_sn);
        $attrNameList= array();//属性名 键值对存放数组
        $attrValList = array();//属性值 键值对存放数组
        foreach ($attrList as $vo1){
            $attrNameList[$vo1['attribute_id']] = $vo1['attribute_name'];
            foreach ($vo1['attrVal'] as $vo2){
                $attrValList[$vo2['att_value_id']] = $vo2['att_value_name'];
            }
        }
        $exist_ids = array();
        $exist_ids_del = array();
        foreach ($attr_select2 as $key=>$vo){            
            $attr_data = array();
            ksort($vo);//按键值 升序排序
            foreach ($vo as $attr_id=>$attr_val_id){
                $arr = explode("|",$attr_id);
                if(count($arr)>1){
                    //对戒属性组合处理
                    $attr_id = $arr[1];
                    $style_sn_1 = $arr[0]; 
                }else{
                    $attr_id = $arr[0];
                    $style_sn_1 = $style_sn;
                }
                $attr_data[$style_sn_1."|".$attr_id] = array(
                    "style_sn"=>$style_sn_1,
                    "spec_id"=>$attr_id,
                    "spec_name"=>$attrNameList[$attr_id],
                    "spec_key" =>$attr_val_id,
                    'spec_value'=>$attrValList[$attr_val_id]
                );                               
            }
            $id = md5(json_encode(array($style_sn=>json_encode($vo))));
            //检查新ID是否存在未删除的
            $exist = $model->checkExistsById($id);            
            if($exist){
                $exist_ids[] = $id;
            }else{
                //检查新ID存在但已删除的
                $exist_del = $model->checkExistsDelById($id);
                if($exist_del){            
                	$exist_ids_del[] = $id;
                }else{   
                    ksort($attr_data);
                    $newdo[] = array(
                        'id' =>$id,
                        'style_sn'=>$style_sn,
                        'attr_select'=>json_encode($attr_select),
                        'attr_keys'=>json_encode($vo),
                        'attr_data'=>json_encode(array($style_sn=>$attr_data)),
                        //'kela_price'=>0,
                        'goods_type'=>2,//期货
                        'create_time'=>date("Y-m-d H:i:s"),
                        'update_time'=>date("Y-m-d H:i:s"),
                    );                   
                } 
            }         
        }
        try{
        	//print_r($newdo);exit;
            $model->deleteBySn($style_sn,$exist_ids);//删除不存在的
            $model->returnBySn($style_sn,$exist_ids_del);//还原已经存在的
            $AppGoodsPriceSalepolicyModel = new AppGoodsPriceSalepolicyModel(11);//52只读 ，11可写可读
            $AppGoodsPriceSalepolicyModel->deleteBySn($style_sn,$exist_ids);//删除
            if(!empty($newdo)){
                $model->replaceIntoAll($newdo);  
                unset($newdo);            
            }
            //更新款所选属性
            $model->updateAttrSelectBySn(json_encode($attr_select), $style_sn);  
            /* //更新是否影响价格计算标识  
            foreach ($attr_select as $_style_sn=>$_attr_select){ 
               $model->updateIsPriceConbined($_style_sn,array_keys($_attr_select));
            } */
            $pdo->commit();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            $result['success'] = 1;
        }catch (PDOException $e){
            $result['error'] = "操作失败!".$e->getMessage();
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        }        
        Util::jsonExit($result);
    }
    /**
     * 笛卡尔积
     * @param unknown $input
     * @return Ambigous <multitype:multitype: , multitype:Ambigous <multitype:, unknown> >
     */
    private function _cartesian($input) {
        if (empty($input)) return [];
        $input = array_filter($input);
        $result = array(array());
        foreach ($input as $key => $values) {
            $append = array();
    
            foreach($result as $product) {
                foreach($values as $item) {
                    $product[$key] = $item;
                    $append[] = $product;
                }
            }
    
            $result = $append;
        }
    
        return $result;
    }
    /**
     * 根据款号查询属性及其属性值
     * @param unknown $style_sn
     * @return multitype:unknown
     */
    private function _getAttrListBySN($style_sn){
        $model = new AppGoodsPriceByStyleModel(11);//52只读 ，11可写可读
        $attrList = $model->getAttrListByStyleSn($style_sn);
        $attr_list = array();
        
        foreach($attrList as $key1=>$vo1){
            $attrValIds = trim($vo1['attribute_value'],',');
            if($vo1['show_type'] ==1){
                $_attrVal = explode(',',$attrValIds);
                $attrVal = array();
                foreach($_attrVal as $key2=>$vo2){
                    $attrVal[$vo2]['att_value_id']   = $vo2;
                    $attrVal[$vo2]['att_value_name'] = $vo2;
                }
            }else{
                if(empty($attrValIds)){
                    continue;
                }
                $attrVal = $model->getAttrValByIds($attrValIds);
                if(empty($attrVal)){
                    continue;
                }        
            }
            $vo1['attrVal'] = $attrVal;
            $attr_list[$key1] = $vo1;
        }
        return $attr_list;
    }
    /**
     * 天生一对款式获取属性交集
     * @param $attrlist1
     * @param $attrlist2
     * @return multitype:
     */
    protected function mergeStyleAttr($attrlist1,$attrlist2){
        if(empty($attrlist1) || empty($attrlist2)){
            return [];
        }
        foreach ($attrlist1 as $key1=>$vo1){
            $flag = false;
            foreach ($attrlist2 as $vo2){
                if($vo1['attribute_id'] == $vo2['attribute_id']){
                    $val1 = explode(",",trim($vo1['attribute_value'],","));
                    $val2 = explode(",",trim($vo2['attribute_value'],","));
                    $val  = array_intersect($val1,$val2);
                    if(!empty($val)){
                       $attrlist1[$key1]['attribute_value'] = implode(',',$val);
                    }else{
                       unset($attrlist1[$key1]);
                       continue; 
                    }
                    $flag = true;
                }
            }
            if($flag != true){
                unset($attrlist1[$key1]);
            }
        }
        //print_r($attrlist1);exit;
        return $attrlist1;
    }
    /**
     * 根据款号获取属性html(ajax获取)
     * @param unknown $paramas
     */
    public function getAttrHtml($paramas){
        $style_sn = _Request::getString('style_sn');
        $style_sn_arr = explode("|",$style_sn);
        $model = new AppGoodsPriceByStyleModel(11);//52只读 ，11可写可读
        if(count($style_sn_arr)==2){
            $style_sn1 = $style_sn_arr[0];
            $style_sn2 = $style_sn_arr[1];
            $attrListAll[$style_sn1] = $model->getAttrListByStyleSn($style_sn1);
            $attrListAll[$style_sn2] = $model->getAttrListByStyleSn($style_sn2);
            //$attrlist1 = $model->getAttrListByStyleSn($style_sn1);
            //$attrlist2 = $model->getAttrListByStyleSn($style_sn2);
            //$attrList  = $this->mergeStyleAttr($attrlist1, $attrlist2);
        }else{        
            $attrListAll[$style_sn] = $model->getAttrListByStyleSn($style_sn);
        }
        $attr_select = array();
        $attr_select  = $model->getAttrSelectBySn($style_sn);
        $attr_select  = $attr_select?json_decode($attr_select,true):array();
        $is_add = $attr_select?false:true;
        //print_r($attrList);exit; 
        foreach ($attrListAll as $k_style_sn=>$attrList){
            $attr_list = array();
            foreach($attrList as $key1=>$vo1){
                $attrValIds = trim($vo1['attribute_value'],',');
                if(empty($attrValIds)){
                    continue;
                }
                //文本框类型属性
                if($vo1['show_type'] ==1){
                    $_attrVal = explode(',',$attrValIds);
                    $attrVal = array();
                    foreach($_attrVal as $key2=>$vo2){
                        $attrVal[$vo2]['att_value_id']   = $vo2;
                        $attrVal[$vo2]['att_value_name'] = $vo2;
                        $attrVal[$vo2]['selected'] = 0;
                    }
                }else{
                    //非文本框类型属性
                    $attrVal = $model->getAttrValByIds($attrValIds);
                    if(empty($attrVal)){
                        continue;
                    }
                    
                }
                foreach($attrVal as $key2=>$vo2){
                    $attrVal[$key2]['selected'] = 0;
                    if(!empty($attr_select)){ 
                        $selected = array();
                        if(isset($attr_select[$k_style_sn][$vo1['attribute_id']])){
                            $selected = $attr_select[$k_style_sn][$vo1['attribute_id']];
                        }          
                        if(in_array($vo2['att_value_id'],$selected)){
                            $attrVal[$key2]['selected'] = 1;
                        }
                    }
                }
                $vo1['attrVal'] = $attrVal;
                $attr_list[$key1] = $vo1;            
            }
            $attr_list_all[$k_style_sn] = $attr_list;
        }//$attrListAll end foreach
    
        $this->render('goodsprice_by_style_attr.html', array(
             'attr_list_all' => $attr_list_all,
             'style_sn'  => $style_sn,  
        ));
    }
    /**
     * 属性价格列表管理
     * @param unknown $params
     */
    public function getAttrPriceList($params){
        $style_sn = _Request::getString('style_sn');
        
        $baseStyleModel = new BaseStyleInfoModel(11);//
        $style_info = $baseStyleModel->getStyleByStyle_sn(array('style_sn'=>$style_sn));
        $is_gold = 0;
        if(!empty($style_info[0])){
            $style_info = $style_info[0];
            $is_gold    = $style_info['is_gold'];//是否是黄金饰品
        }
        //销售价格 (2重情况下才不可以编辑这个框 其它都是READONLY : 款式信息是否是黄金字段 等于 非黄金/一口价）
        $is_golds = false;
        if($is_gold == 0 || $is_gold == 3){
            $is_golds = true;
        }
        
        $model = new AppGoodsPriceByStyleModel(11);
        $datalist = $model->getListBySn($style_sn,'id,attr_data,attr_select,kela_price,goods_stock,status');
        $attr_title_list = array();
        $attr_data_list  = array();
        $i = 0;   
        foreach($datalist as $key=>$data){
            $i++;
            $data_arr = json_decode($data['attr_data'],true);
            $select_arr = json_decode($data['attr_select'],true);
            $attr_data = array();
            foreach($data_arr[$style_sn] as $vo){
                $attr_data[$vo['spec_name']][]=$vo;
            }
            foreach ($attr_data as $spec_name=>$vo){
                if(count($vo)==2){
                    $attr_data[$spec_name] = $vo[0]['spec_value']."|".$vo[1]['spec_value'];
                }else{
                    $attr_data[$spec_name] = $vo[0]['spec_value'];
                }
            }
            
            $key = $this->createSortKey($key,$attr_data,$data['status']);
            $attr_data_list[$key] = array(
                'id'=>$data['id'],
                'kela_price'=>$data['kela_price'],
                'goods_stock'=>$data['goods_stock'],
                'status'=>$data['status'],                
                'data'=>$attr_data,
            );
            if($i==1){
                $attr_title_list = array_keys($attr_data);
            }

        }
        ksort($attr_data_list);        
        $this->render('goodsprice_by_style_price.html', array(
            'bar' => Auth::getBar(),
            'attr_title_list'=> $attr_title_list,
            'attr_data_list' => $attr_data_list,
            'style_sn' => $style_sn,
            'is_gold' => $is_gold,
            'is_golds' => $is_golds
        ));
    }
    
    public function createSortKey($key,$data,$status){
        $color_array = array(
            'D'=>"23",'D-E'=>"22",'E'=>"21",'E-F'=>"20",
            'F'=>"19",'F-G'=>"18",'G'=>"17",'G-H'=>"16",
            'H'=>"15",'H+'=>"14",'H-I'=>"13",'I'=>"12",
            'I-J'=>"11",'J'=>"10",'J-K'=>"09",'K'=>"08",
            'K-L'=>"07",'L'=>"06",'M'=>"05",'白'=>"04",
            '黑色'=>"03",'金色'=>"02",'无'=>"01");
        
        $clarity_array = array(            
            'FL'=>'01','IF'=>'02',
            'VVS'=>'03','VVS1'=>'04','VVS2'=>'05',
            'VS'=>'06','VS1'=>'07','VS2'=>'08',
            'SI'=>'09','SI1'=>'10','SI2'=>'11',
            'I1'=>'12','I2'=>'13','P'=>'14',
            'P1'=>'15','不分级'=>'16','无'=>'17');
        
        $caizhi_array = array('18K'=>1,'PT950'=>2);
        if(!empty($data['主石重量'])){
            $carat_sort = str_pad($data['主石重量']*100,4,'0',STR_PAD_LEFT);
        }else{
            $carat_sort = "0000";
        }
        if(!empty($data['钻石颜色']) && !empty($color_array[$data['钻石颜色']])){
            $color_sort = $color_array[$data['钻石颜色']];
        }else{
            $color_sort = 99;
        }
        if(!empty($data['钻石净度']) && !empty($clarity_array[$data['钻石净度']])){
            $clarity_sort = $clarity_array[$data['钻石净度']];
        }else{
            $clarity_sort = 99;
        }
        if(!empty($data['材质']) && !empty($caizhi_array[$data['材质']])){
            $caizhi_sort = $caizhi_array[$data['材质']];
        }else{
            $caizhi_sort = 9;
        }
        $status_sort = $status?0:1;
        $key = $status_sort.'-'.$carat_sort.'-'.$color_sort.'-'.$clarity_sort.'-'.$caizhi_sort.'-'.$key;
        return $key;
    }
    public function updateAttrPrice($params){
        
        $result = array('error'=>'','success'=>0);        
        $price_list = _POST::getList('price_list');
        $status_list = _POST::getList('status_list');
        $stock_list = _POST::getList('stock_list');//库存设置
        $style_sn   = _Post::getString("style_sn");
        $is_stock = empty($stock_list)?0:1;//是否有库存
        $is_tsyd = strpos($style_sn,"|")?true:false;//是否天生一对
        
        $model = new AppGoodsPriceByStyleModel(11);
        $pdo = $model->db()->db();
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
        $pdo->beginTransaction();//开启事务
        
        $i = 0;
        foreach($price_list as $key=>$vo){
            $i++;
            if(empty($status_list[$key]) && empty($vo)){
                //未上架的，价格不用必填
                continue;
            }            
            if(empty($vo)){
                $result['error'] = "第".$i."行，价格不能为空";
                Util::jsonExit($result);
            }else if($is_stock && (!isset($stock_list[$key]) || !is_numeric($stock_list[$key]))){
                $result['error'] = "第".$i."行，库存填写有误,请输入数字";
                Util::jsonExit($result);
            }
            if($is_tsyd){
                $vo = str_replace(",","|",$vo);
                $price_list[$key] = $vo;
                $p_arr = explode("|",$vo);
                if(count($p_arr)==2){
                    if(!is_numeric($p_arr[0])||!is_numeric($p_arr[1]) || $p_arr[0]<=0 || $p_arr[1]<=0){
                        $result['error'] = "第".$i."行，价格不合法：天生一对价格请用,号隔开,且两款价格均为数字";
                        Util::jsonExit($result);
                    }
                }else{
                    $result['error'] = "第".$i."行，价格不合要求：天生一对价格请用,号隔开,且两款价格均为数字";
                    Util::jsonExit($result);
                }
            }else{
                if(!is_numeric($vo) || $vo<=0){
                    $result['error'] = "第".$i."行，价格不合法,请输入大于0的数字";
                    Util::jsonExit($result);
                }
            }
        }

        try{
            foreach($price_list as $key=>$vo){
                $status = isset($status_list[$key])?$status_list[$key]:0;
                $goods_stock = isset($stock_list[$key])?$stock_list[$key]:1;//库存默认为1
                $data = array(
                    'kela_price'=>$vo,
                    'status'=>$status,
                    'goods_stock'=>$goods_stock,
                    'update_time'=>date("Y-m-d H:i:s"),                    
                );
                //print_r($data);exit;
                $res = $model->update($data,"id='{$key}'");
            }
            $pdo->commit();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            $result['success'] = 1;
            Util::jsonExit($result);
        }catch (PDOException $e){
            $pdo->rollback();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            $result['error'] = "保存失败！".$e->getMessage();
            Util::jsonExit($result);
        }
        
    }
    
    public function getAttrSalepolicyList($params){
    	$attr_select = _Request::getList('attr');    	
        $args = array(
        		'mod' => _Request::get("mod"),
        		'con' => substr(__CLASS__, 0, -10),
        		'act' => __FUNCTION__,
        		'style_sn' => _Request::getString('style_sn'),
        		
        );
        foreach ($attr_select as $k=>$v){
        	$args["attr[{$k}]"]=$v;
        }
        $style_sn= $args['style_sn'];
       
        $model = new AppGoodsPriceSalepolicyModel(11);//52只读，11可读写
        $salesChannelModel = new SalesChannelsModel(1); 
        $attr_select=array_filter($attr_select);
        //print_r($attr_select);exit;
		$where = '';
		$where = array(
        		'style_sn'   => $style_sn,
        		//'style_id' => $style_id,
        		'is_delete' => 0 //未删除
        );
		//判断是否有筛选
		$isnull = 0;
		if(empty($attr_select)){
			$isnull = 1;	
		}else{
			foreach($attr_select as $v)
			{
				$val = array_values($v);
				if($val[0]<1)
				{
					$isnull =1;
					break;
				}
			}
		}
        if($isnull<1){
        	asort($attr_select);//按键值 升序排序
        	$style_id = md5(json_encode(array($style_sn=>json_encode($attr_select))));
			$where['style_id'] = $style_id;
        }else{
        	$style_id='';
        	//$attr_salepolicy = $model->getListBySn($style_sn);
        }
		$channelid = _Request::getInt('channel');
		if($channelid>0)
		{
			$where['channel_id'] = $channelid;
		}
        $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;
        $data= $model->getListPageBySn($where, $page,$pageSize=20);
        //print_r($data);exit;
        $attrTitleList = array();
        foreach($data['data'] as $key=>$vo){
            $vo['channel_name'] = $salesChannelModel->getNameByid($vo['channel_id']);
            $arr = json_decode($vo['attr_data'],true);
            unset($vo['attr_data']);
            $rows = $arr[$vo['style_sn']];            
            foreach ($rows as $v){
                if($key == 0){
                    $attrTitleList[] = $v['spec_name'];
                }
            	$attrDataList[$v['spec_name']] = $v['spec_value'];
            	
            }
            $vo['attrDataList'] = $attrDataList;
			$vo['channel_price'] = round($vo['jiajialv'] * $vo['kela_price'] + $vo['sta_value']);
            $data['data'][$key] = $vo;
        }
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'goodsprice_by_style_salepolicy_page';
        $this->render('goodsprice_by_style_salepolicy.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data,
            'attrTitleList'=>array_unique($attrTitleList),
        ));
    }
    
    
    /**
     * 根据款号获取搜索条件(ajax获取)
     * @param unknown $paramas
     */
    public function getAttrSalepolicyForm($paramas){
    	$style_sn = _Request::getString('style_sn');
    	$style_sn_arr = explode("|",$style_sn);
    	$model = new AppGoodsPriceByStyleModel(11);//52只读 ，11可写可读
    	if(count($style_sn_arr)==2){
    		$style_sn1 = $style_sn_arr[0];
    		$style_sn2 = $style_sn_arr[1];
    		$attrlist1 = $model->getAttrListByStyleSn($style_sn1);
    		$attrlist2 = $model->getAttrListByStyleSn($style_sn2);
    		$attrList  = $this->mergeStyleAttr($attrlist1, $attrlist2);
    	}else{
    		$attrList = $model->getAttrListByStyleSn($style_sn);
    	}
    	$attr_list = array();
    	$attr_select = array();
    	 
    	$attr_select  = $model->getAttrSelectBySn($style_sn);
    	$attr_select  = $attr_select?json_decode($attr_select,true):array();
    	$is_add = $attr_select?false:true;
    	//print_r($attrList);exit;
    	foreach($attrList as $key1=>$vo1){
    		$attrValIds = trim($vo1['attribute_value'],',');
    		if(empty($attrValIds)){
    			continue;
    		}
    		//文本框类型属性
    		if($vo1['show_type'] ==1){
    			$_attrVal = explode(',',$attrValIds);
    			$attrVal = array();
    			foreach($_attrVal as $key2=>$vo2){
    				$attrVal[$vo2]['att_value_id']   = $vo2;
    				$attrVal[$vo2]['att_value_name'] = $vo2;
    				$attrVal[$vo2]['selected'] = 0;
    			}
    		}else{
    			//非文本框类型属性
    			$attrVal = $model->getAttrValByIds($attrValIds);
    			if(empty($attrVal)){
    				continue;
    			}
    
    		}
    
    		foreach($attrVal as $key2=>$vo2){
    			$attrVal[$key2]['selected'] = 0;
    			if(!empty($attr_select)){
    				$selected = isset($attr_select[$vo1['attribute_id']])?$attr_select[$vo1['attribute_id']]:array();
    				//print_r($selected);
    				if(in_array($vo2['att_value_id'],$selected)){
    					$attrVal[$key2]['selected'] = 1;
    				}
    			}
    		}
    		$vo1['attrVal'] = $attrVal;
    		$attr_list[$key1] = $vo1;
    	}
		$SalesChannelsModel = new SalesChannelsModel(1);
        $channellist = $SalesChannelsModel->getSalesChannelsInfofowebsite("c.`id`,c.`channel_name`", '');
    	$this->render('style_salepolicy_search_form.html', array(
    			'attr_list' => $attr_list,
    			'style_sn'  => $style_sn,
				'channellist'=>$channellist
    	));
    }
    
    
    /**
     * 添加销售政策
     * @param unknown $params
     */
    public function addSalepolicy($params){
    	$_ids = _Request::getList("_ids");        
       		    	
    	$SalesChannelsModel = new SalesChannelsModel(1);
        $list = $SalesChannelsModel->getSalesChannelsInfofowebsite("c.`id`,c.`channel_name`,c.channel_code", '');
    	$channellist = array();
    	foreach ($list as $vo){
    	    $firstChar = substr($vo['channel_code'],0,1);
    	    $channellist[$firstChar][] = $vo;
    	}
    	ksort($channellist);
    	
    	$result['content'] = $this->fetch('add_style_salepolicy.html',array(
    	    '_ids'=>implode(",", $_ids),
    		'channellist'=>$channellist,
    	));
    	$result['title'] = '添加销售策略';
    	Util::jsonExit($result);
    }
    /**
     * 插入销售政策
     * @param unknown $params
     */
    public function insertSalepolicy($params){
        set_time_limit(3600);
    	$result = array('success' => 0,'error' =>'');
    	$_ids = _Post::get("_ids");
        if(empty($_ids)){
    		$result['error'] = '参数_ids缺失';
    		Util::jsonExit($result);
    	}    	
    	$_ids = explode(",",$_ids);    	
    	$pmodel = new AppGoodsPriceByStyleModel(11);
    	$pinfo = $pmodel->getRowById($_ids[0],'style_sn');
    	if(empty($pinfo)){
    	    $result['error'] = '参数_ids错误';
    	    Util::jsonExit($result);
    	}
    	$style_sn = $pinfo['style_sn'];    	
    	$newdo = array(  
    	    'style_ids'  => $_ids,
    	    'style_sn'=>$style_sn,
    	    'channel_ids'=>_Post::getList("channel_ids"),
    	    'jiajialv'=>_Post::getFloat("jiajialv"),
    	    'sta_value'=>_Post::getFloat("sta_value"),
    	);
    	if(empty($newdo['channel_ids'])){
    		$result['error'] = '销售渠道至少选一个';
    		Util::jsonExit($result);
    	}
    	if($newdo['jiajialv']==0){
    		$result['error'] = '加价率必填';
    		Util::jsonExit($result);
    	}    	
    	//print_r($params);exit;
    	$model = new AppGoodsPriceSalepolicyModel(16);
    	$res = $model->updateSalepolicy($newdo);
    	
    	$result['success'] = 1;
    	Util::jsonExit($result);   	
    	
    }
    
    public function exportCSV(){
        $style_sn = _Request::getString('style_sn');
        $fileName = $style_sn."按款定价(".date("Ymd").").csv";
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $fileName);
        
        
        $baseStyleModel = new BaseStyleInfoModel(11);//
        $style_info = $baseStyleModel->getStyleByStyle_sn(array('style_sn'=>$style_sn));
        $is_gold = 0;
        if(!empty($style_info[0])){
            $style_info = $style_info[0];
            $is_gold    = $style_info['is_gold'];//是否是黄金饰品
        }
        $model = new AppGoodsPriceByStyleModel(11);
        $datalist = $model->getListBySn($style_sn,'id,attr_data,attr_select,kela_price,goods_stock,status');
        $attr_title_list = array();
        $attr_code_list  = array();
        $exportlist  = array();
        $i = 0;
        foreach($datalist as $key=>$data){
            $i++;
            $data_arr = json_decode($data['attr_data'],true);
            if($i==1){
                $attr_title_list["style_sn"]="款号";
                $attr_code_list["style_sn"] ="style_sn";
                foreach ($data_arr[$style_sn] as $spec_name=>$vo){
                    $attr_title_list["attr".$vo['spec_id']]=$vo['spec_name'];
                    $attr_code_list["attr".$vo['spec_id']] = "attr".$vo['spec_id'];
                }
                $attr_title_list['goods_stock']="成本价";
                $attr_code_list["kela_price"] ="kela_price";
                if($is_gold){
                     $attr_title_list["goods_stock"]="库存";
                     $attr_code_list["goods_stock"] ="goods_stock";                      
                }
                $attr_title_list["status"]="上架状态";
                $attr_code_list["status"] ="status"; 
                //$exportlist[] = $attr_code_list;
                //$exportlist[] = $attr_title_list;                
            }else{
                $attr_data1 = array();
                $attr_data2 = array();
                foreach($data_arr[$style_sn] as $vo){
                    $attr_data1["attr".$vo['spec_id']][] = $vo;
                    $attr_data2[$vo['spec_name']] = $vo['spec_value'];
                }
                $attr_data = array('style_sn'=>$style_sn);
                foreach ($attr_data1 as $k=>$v){
                    if(count($v)==2){
                        $attr_data[$k] = $v[0]['spec_value']."|".$v[1]['spec_value'];
                    }else{
                        $attr_data[$k] = $v[0]['spec_value'];
                    }                    
                }
                unset($attr_data1);
                $attr_data["kela_price"] = $data['kela_price'];
                if($is_gold){
                    $attr_data["goods_stock"] = $data['goods_stock'];
                }
                $attr_data["status"] = $data['status'];
                $key = $this->createSortKey($key,$attr_data2,$data['status']);
                $exportlist[$key] = $attr_data;
                
            }       
                   
        }

        ksort($exportlist);
        array_unshift($exportlist, $attr_code_list, $attr_title_list);
        //$exportlist[] = $attr_title_list;
        foreach ($exportlist as $vo){
            $str = "";
            foreach ($vo as $v){
                $v = @iconv("UTF-8","GBK",$v);
                $str .= $v.",";
            }
            $str = trim($str,",")."\r\n";
            echo $str;
        }
    }
    /**
     * 导出按款定价数据
     * @param unknown $params
     */
    /*
    public function exportCSV2($params){
        $style_sn = _Request::getString('style_sn');
        $style_sn_arr = explode("|",$style_sn);
        $model = new AppGoodsPriceByStyleModel(11);//52只读 ，11可写可读
        if(count($style_sn_arr)==2){
            $style_sn1 = $style_sn_arr[0];
            //$style_sn2 = $style_sn_arr[1];
            $attrlist = $model->getAttrListByStyleSn($style_sn1);
            //$attrlist2 = $model->getAttrListByStyleSn($style_sn2);
            //$attrList  = $this->mergeStyleAttr($attrlist1, $attrlist2);
        }else{
            $attrList = $model->getAttrListByStyleSn($style_sn);
        }
        $csv_code  = array();//标题代号
        $csv_title = array();//标题名称
        $attrCodeArr = array();//总属性Id列表
        $csv_title['style_sn'] = "款号";
        foreach ($attrList as $vo){
            $csv_title['attr'.$vo['attribute_id']] = $vo['attribute_name'];
            $attrCodeArr['attr'.$vo['attribute_id']] = $vo['attribute_id'];
        }
              
        $csv_title['kela_price'] = "成本价";
        $csv_title['status'] = "上架状态";
        $csv_title['goods_stock'] = "库存数量";                
        foreach ($csv_title as $key=>$vo){
            $csv_code[$key] = $key; 
        }
        $list_title = array($csv_code,$csv_title);
        $fileName = $style_sn."按款定价(".date("Ymd").").csv";
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $fileName);
        //1.输出字段标题
        foreach ($list_title as $title){
            $str = "";
            foreach ($title as $v){                
                $v = @iconv("UTF-8","GBK",$v);
                $str .= $v.",";              
            }
            $str = trim($str,",")."\r\n";
            echo $str;
        }
        
        $csv_data = array();
        $data = $model->getListBySn($style_sn,'id,attr_data,kela_price,goods_stock,status');
        $list = array();
        foreach ($data as $vo){
            $attr_data = json_decode($vo['attr_data'],true);
            $attr_data = current($attr_data);
            $list_data = array();
            $list_data['style_sn'] = $style_sn;
            foreach ($attrCodeArr as $k=>$v){
                $list_data[$k] = "";//属性默认值
                foreach ($attr_data as $v2){
                    if($v2['spec_id'] == $v){
                        $list_data[$k] = $v2['spec_value'];
                    }      
                }
            }
            $list_data['kela_price'] = $vo['kela_price'];
            $list_data['status'] = $vo['status'];
            $list_data['goods_stock'] = $vo['goods_stock'];            
            
            $str = "";
            foreach ($list_data as $v){ 
                $v = @iconv("UTF-8","GBK",$v);
                $str .= $v.",";                
            }   
            $str = trim($str,",")."\r\n";
            echo $str;
        }        
    }
    */
    /**
     * 导入按款定价数据
     * @param $params
     */
    public function importCSV($params){
        $result = array("error"=>"","success"=>"");     
        $_style_sn = _Request::getString("style_sn");   
        if(empty($_FILES['file']['tmp_name'])){
            $result['error'] = "请上传文件";
            Util::jsonExit($result);
        }else if(Upload::getExt($_FILES['file']['name']) != 'csv'){
            $result['error'] = "请上传csv格式的文件";
            Util::jsonExit($result);
        }
        $style_sn = "";
        $attrSelect = array();
        $tmp_name = $_FILES['file']['tmp_name'];
        $file = fopen($tmp_name, 'r');
        
        
        $attrCodeArr = array();//总属性CODE列表
        $codeArr = array();//列名称列表
        $nameArr = array();//列名称列表
        $attrSelectCodeArr = array();//所选属性CODE列表
        $attrCodeArrFlip = array_flip($attrCodeArr);//总属性ID列表，键值对调
        $datalist = array();
        
        $i = 0;
        while ($datav = fgetcsv($file)) {
            foreach ($datav as $k=>$v){
                $datav[$k] = @iconv("GBK","UTF-8",$v);
            }
            $i++;
            if($i==1){
                //获取总属性CODE列表
                foreach ($datav as $k=>$v){
                    if(preg_match("/^attr([0-9]+)$/is",$v)){
                        $attrCodeArr[$k] = $v;                        
                    }
                    $codeArr[$k] = $v;
                }
                $attrCodeArrFlip = array_flip($attrCodeArr);                
            }
            if($i==2){
                //获取总属性名称列表
                $nameArr = $datav;
            }
            if($i >= 3){
                //过滤空行 begin
                $is_empty_line = true;
                $data = array();
                foreach ($datav as $k=>$v){
                    if(trim($v)!=""){
                        $is_empty_line = false;
                    }
                    $data[$codeArr[$k]] = $v;  
                    if($style_sn=="" && isset($data['style_sn'])){
                        $style_sn = $data['style_sn'];
                    }                  
                }
                $datalist[] = $data;
                if($is_empty_line){
                    continue;
                }//过滤空行 end
                                
                //已选属性分类列表
                if(empty($attrSelectCodeArr)){
                    foreach ($datav as $k=>$v){
                        if(array_key_exists($k,$attrCodeArr) && trim($v)!=""){
                            $attrSelectCodeArr[$k] = $attrCodeArr[$k];
                        }
                    }
                }
                
                //验证已选属性的数据值是否有空值
                 foreach ($datav as $k=>$v){  
                    if(array_key_exists($k,$attrSelectCodeArr) && trim($v)==""){
                         fclose($file);
                         $result['error'] = "第{$i}行,{$nameArr[$k]}不能为空！";
                         Util::jsonExit($result);
                         
                    }
                } 
                
            }//end $i >= 3
            
            
            
        }
        fclose($file);
        if(!empty($_style_sn) && $_style_sn != $style_sn){
            $result['error'] = "上传文件中到款号【{$style_sn}】与当前款号【{$_style_sn}】不一致！";
            Util::jsonExit($result);
        } 
        $attrModel = new GoodsAttributeModel(11);//52只读 ，11可写可读
        $model  = new AppGoodsPriceByStyleModel(11);//52只读 ，11可写可读
        $attrValKeyArr = array();//属性值与属性值ID的键值对数组
        $showTypeArr = array();
        //print_r($attrSelectCodeArr);exit;
        foreach ($attrSelectCodeArr as $i=>$attr_code){
            $attr_id = str_replace("attr","",$attr_code);         
                       
            $data = $attrModel->getAttrValsByAttrId($attr_id);
            $dataVK = array_column($data,"att_value_id","att_value_name");
            $attrValKeyArr[$attr_code] = $dataVK;
            $showTypeArr = array_column($data,"show_type","attribute_id");
            
            /* $arr = array_column($datalist,$attr_code);
            $arr = array_unique($arr);
            print_r($arr);
            foreach ($arr as $val){
                $val_arr = explode("|",$val);
                foreach ($val_arr as $ii=>$val){
                    if(in_array($nameArr[$i],array("主石重量","镶口")) && is_numeric($val)){
                        $val = number_format($val,2);
                    }
                    if(isset($dataVK[$val])){
                        $attr_select[$attr_id][] = $dataVK[$val];
                    }else if(isset($showTypeArr[$attr_id]) && $showTypeArr[$attr_id]==1){
                        $attr_select[$attr_id][] = $val;
                    }
                }
            }       */   
        }
        $pdo = $model->db()->db();
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
        $pdo->beginTransaction();//开启事务
        try{
            $model->deleteBySn($style_sn,$not_ids=array(),$is_delete=1);
            foreach ($datalist as $i=>$row){
                $i++;
                $j = -1;
                $attrKeys = array();
                $attrData = array();
                $style_sn_arr = array();
                foreach ($row as $key=>$val){
                    $j++;
                    if($val == ""){
                        continue;
                    } 
                     
                    $val_arr = explode("|",$val);
                    if($key=="style_sn"){
                        $style_sn_arr = $val_arr;
                    }
                    
                    foreach ($val_arr as $ii=>$val){ 
                  
                        if(in_array($nameArr[$j],array("主石重量","镶口")) && is_numeric($val)){
                            $val = number_format($val,2);
                        }
                        if(in_array($key,$attrSelectCodeArr)){
                            
                            if(count($style_sn_arr)==2 && count($val_arr)!=2)  {
                                $error = "第{$i}行,{$nameArr[$j]}值不合要求";
                                Util::rollbackExit($error,array($pdo));
                            }
                            $attribute_id = str_replace("attr","",$key);
                            
                            $attrKey = $style_sn_arr[$ii]."|".$attribute_id;
                            if(isset($showTypeArr[$attribute_id]) && $showTypeArr[$attribute_id]==1){                             
                                $attrSelect[$style_sn_arr[$ii]][$attribute_id][] = $val;
                                $attrKeys[$attrKey] = $val;
                                $attrData[$attrKey] = array("style_sn"=>$style_sn_arr[$ii],"spec_id"=>$attribute_id,"spec_name"=>$nameArr[$j],"spec_key"=>$val,"spec_value"=>$val);
                            }else{
                                if(isset($attrValKeyArr[$key][$val])){
                                    $attrSelect[$style_sn_arr[$ii]][$attribute_id][] = $attrValKeyArr[$key][$val];
                                    $attrKeys[$attrKey] = $attrValKeyArr[$key][$val];
                                    $attrData[$attrKey] = array("style_sn"=>$style_sn_arr[$ii],"spec_id"=>$attribute_id,"spec_name"=>$nameArr[$j],"spec_key"=>$attrValKeyArr[$key][$val],"spec_value"=>$val);
                                }else{
                                    $error = "第{$i}行,{$nameArr[$j]}值{$val}在系统中不存在";
                                    Util::rollbackExit($error,array($pdo));
                                     
                                }
                            }
                        }
                        
                        
                    }//end  $val_arr foreach              
                }

                ksort($attrKeys);
                ksort($attrData);
                $newdo = array();
                $newdo['id'] = md5(json_encode(array($style_sn=>json_encode($attrKeys))));
                //$newdo['attr_select'] = json_encode($attr_select);
                $newdo['style_sn']  = $style_sn;
                $newdo['attr_keys'] = json_encode($attrKeys);
                $newdo['attr_data'] = json_encode(array($style_sn=>$attrData));
                $newdo['kela_price'] = $row['kela_price'];
                $newdo['goods_type'] = 2;
                $newdo['status'] = (int)$row['status'];
                if(isset($row['goods_stock'])){
                $newdo['goods_stock'] = (int)$row['goods_stock'];
                }
                $newdo['create_time'] = date("Y-m-d H:i:s");
                $newdo['is_delete'] = 0;
                $newdo['remark'] = "系统导入";
                foreach ($newdo as $k=>$v){
                    $newdo[$k] = str_replace("\\","\\\\", $v);
                }
                $sql = "insert into front.app_goodsprice_by_style(`".implode("`,`",array_keys($newdo))."`) values('".implode("','",$newdo)."')";
                //echo $sql."<br/>";
                $pdo->query($sql); 
            }
            foreach ($attrSelect as $key1=>$vo1){
                foreach ($vo1 as $key2=>$vo2){
                    $attrSelect[$key1][$key2] = array_unique($vo2);
                }
            }
            $attrSelect = json_encode($attrSelect);
            $sql = "update front.app_goodsprice_by_style set attr_select='{$attrSelect}' where style_sn='{$style_sn}'";
            $pdo->query($sql);
            $pdo->commit();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            $result['success'] = 1;
            $result['data'] = $style_sn;
            Util::jsonExit($result);
        }catch (PDOException $e){
            $error = "导入失败，事物回滚.error:".$e->getMessage();
            Util::rollbackExit($error,array($pdo));
        }   
        
    }
    public function importIndex(){
        $result = array("title"=>"按款定价导入"); 
        $style_sn = _Request::getString("style_sn");    
        $result['content'] = $this->fetch("goodsprice_by_style_import.html",array("style_sn"=>$style_sn));        
        Util::jsonExit($result);
    }
    


}

