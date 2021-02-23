<?php
/**
 * 4C配钻控制器
 * -------------------------------------------------
 *   @file		: AppProcessorOperationController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-15 17:12:26
 *   @update	:
 *  -------------------------------------------------
 */
class ProductInfoFourCController extends CommonController {

    //protected $smartyDebugEnabled = false;
    protected $whitelist = array('downloadCSV');

    /**
     * 	index，搜索框
     */
    public function index($params) {
        
        $dataAttr = array();
        $dataAttr['color_arr'] = ProductInfo4CModel::$color_arr;//颜色
        $dataAttr['clarity_arr'] = ProductInfo4CModel::$clarity_arr;//净度
        $dataAttr['shape_arr']  = ProductInfo4CModel::$shape_arr;//形状

        $this->render('product_info_4c_search_form.html', array(
            'bar' => Auth::getBar(),
            'dataAttr'=>$dataAttr,
        ));
    }    
    /**
     * 配石记录列表
     * @param unknown $params
     */
    public function search($params){
        $args = array(
            'mod'	=> _Request::get("mod"),
            'con'	=> substr(__CLASS__, 0, -10),
            'act'	=> __FUNCTION__,
            'order_sn'=>_Request::get('order_sn'),
            'bc_sn'=>_Request::get('bc_sn'),
            'zhengshuhao'=>_Request::get('zhengshuhao'),
            'carat_min'=>_Request::get('carat_min'),
            'carat_max'=>_Request::get('carat_max'),
            'price_min'=>_Request::get('price_min'),
            'price_max'=>_Request::get('price_max'),
            'discount_min'=>_Request::get('discount_min'),
            'discount_max'=>_Request::get('discount_max'),
            'clarity[]'=>_Request::getList('clarity'),
            'color[]'=>_Request::getList('color'),
            'shape[]'=>_Request::getList('shape')

        );
        $where = array(
            'order_sn'=>_Request::get('order_sn'),
            'bc_sn'=>_Request::get('bc_sn'),
            'zhengshuhao'=>_Request::get('zhengshuhao'),
            'carat_min'=>_Request::get('carat_min'),
            'carat_max'=>_Request::get('carat_max'),
            'price_min'=>_Request::get('price_min'),
            'price_max'=>_Request::get('price_max'),
            'discount_min'=>_Request::get('discount_min'),
            'discount_max'=>_Request::get('discount_max'),
            'clarity'=>_Request::getList('clarity'),
            'color'=>_Request::getList('color'),
            'shape'=>_Request::getList('shape')
        );
         
        $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
        
        $model = new ProductInfo4CModel(13);
        $data = $model->pageList($where,$page,10,false);

        foreach($data['data'] as &$v) {
            $v['kt_bc_id'] = preg_replace('/^[a-zA-Z]+/', '', $v['kt_bc_sn']);
        }
        unset($v);
 
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'product_info_4c_search_page';
        $this->render('product_info_4c_search_list.html',array(
            'pa'=>Util::page($pageData),
            'page_list'=>$data,
        ));
    } 
    
    public function getDiamandInfoAjax(){
        $cert_id = _Request::get('cert_id');
        $apiDiamondModel = new ApiDiamondModel();
        $result = $apiDiamondModel->getDiamondInfoByCertId($cert_id);
        if(!empty($result['data']['is_bakdata'])){
            $result['error'] = 1;
            $result['data'] = array();
        }
        Util::jsonExit($result);
    } 
    /**
     * 4C配钻修改记录查看
     * @param unknown $params
     */
    public function show($params){
        
        $result = array('success' => 0,'error' => '','title'=>'4C配钻记录查看');
 
        $id = _Request::get('id');

	    $model = new ProductInfo4CModel($id,13);
	    $data = $model->getDataObject();
       
	    if(empty($data)){
	        $result['content'] = "当前布产单不支持4C配钻记录查看！".$id;
	        Util::jsonExit($result);
	    }
	    $zhengshuhao_org = $data['zhengshuhao_org'];
	    
	    $apiDiamondModel = new ApiDiamondModel();
	    $ret = $apiDiamondModel->getDiamondInfoByCertId($zhengshuhao_org);
	    if($ret['error']==1){
	        $result['error'] ='原证书号不存在';
	        Util::jsonExit($result);
	    }else{
	        $dataOrg = $ret['data'];
	    }
	    $result['content'] = $this->fetch('product_info_4c_show.html',array(
	        'data'=>$data,
	        'dataOrg'=>$dataOrg,
	    ));
	    Util::jsonExit($result);
    }
    
    /**
     * 4C配钻记录导出
     * @param unknown $params
     */
    public function downloadCSV($params){

        $where = array(
            'order_sn'=>_Request::get('order_sn'),
            'bc_sn'=>_Request::get('bc_sn'),
            'zhengshuhao'=>_Request::get('zhengshuhao'),
            'carat_min'=>_Request::get('carat_min'),
            'carat_max'=>_Request::get('carat_max'),
            'price_min'=>_Request::get('price_min'),
            'price_max'=>_Request::get('price_max'),
            'discount_min'=>_Request::get('discount_min'),
            'discount_max'=>_Request::get('discount_max'),
            'clarity'=>_Request::getList('clarity'),
            'color'=>_Request::getList('color'),
            'shape'=>_Request::getList('shape')
        );
        
        $model = new ProductInfo4CModel(13);
        $_data = $model->getList($where);
        $conf=array(
            array('field'=>'order_sn','title'=>'订单号'),
            array('field'=>'bc_sn','title'=>'布产号'),
            array('field'=>'zhengshuhao_org','title'=>'原证书号'),
            array('field'=>'zhengshuhao','title'=>'新证书号'),
            array('field'=>'price_org','title'=>'原采购价'),
            array('field'=>'price','title'=>'新采购价'),
            array('field'=>'discount_org','title'=>'原采购折扣'),
            array('field'=>'discount','title'=>'新采购折扣'),
            array('field'=>'carat','title'=>'新石重'),
            array('field'=>'color','title'=>'新颜色'),
            array('field'=>'clarity','title'=>'新净度'),
            array('field'=>'shape','title'=>'新形状'),
            array('field'=>'peishi_status','title'=>'4C配钻状态'),
            array('field'=>'buchan_status','title'=>'布产状态'),
        );
        $dd=new DictModel(1);
        $data = array();
        foreach($_data as $k=>$v)
        {   
            $data[$k]['order_sn']=$v['order_sn'];
            $data[$k]['bc_sn']=$v['bc_sn'];
            $data[$k]['zhengshuhao_org']=$v['zhengshuhao_org'];
            $data[$k]['zhengshuhao']=$v['zhengshuhao'];
            $data[$k]['price_org']=$v['price_org'];
            $data[$k]['price']=$v['price'];
            $data[$k]['discount_org']=$v['discount_org'];
            $data[$k]['discount']=$v['discount'];
            $data[$k]['carat']=$v['carat'];
            $data[$k]['color']=$v['color'];
            $data[$k]['clarity']=$v['clarity'];            
            $data[$k]['shape']=$dd->getEnum('shape',$v['shape']);
            $data[$k]['peishi_status']=empty($v['peishi_status'])?"未完成":"已完成";
            $data[$k]['buchan_status']=$dd->getEnum('buchan_status',$v['status']);
        }
        ob_clean();
        Util::downloadCsvNew('4C配钻记录',$conf,$data);
    }
    /**
     * 4C配钻修改
     * @param unknown $params
     */
    public function edit($params){
        $id = _Request::getInt('id');
        $result = array('success' => 0,'error' => '','title'=>'修改证书号');
        $model = new ProductInfo4CModel($id,13);
        $data = $model->getDataObject();
         
        if(empty($data)){
            $result['content'] = "当前布产单不支持4C配钻！";
            Util::jsonExit($result);
        }
                
        $result['content'] = $this->fetch('product_info_4c_edit.html',array(
            'data'=>$data
        ));
        Util::jsonExit($result);
    }
    /**
     * 裸石与空托绑定之前的检测
     */
    public function checkBindKongtuo(){
        
        $cert_id   = _Request::get('cert_id');
        $consignee = _Request::get('consignee');
        $order_sn = _Request::get('order_sn');
        if($cert_id=='' || $consignee==""){
            $result['error'] = "参数错误！";
            Util::jsonExit($result);
        }
        
        $salesModel = new SalesModel(27); 
        //根据证书号和收货人匹配空托
        $order_detail_list = $salesModel->getOrderDetailsFor4C($cert_id);        
        $kt_order_detail_id = 0;
        $kt_bc_id = 0;
        if(!empty($order_detail_list)){        
            foreach($order_detail_list as $vo){
                if($vo['consignee'] ==$consignee){
                    $kt_bc_id = $vo['bc_id'];
                    $kt_order_detail_id = $vo['id'];                    
                }
                if($order_sn ==$vo['order_sn']){
                    //与裸钻订单号一样的空托，优先匹配
                    break;
                }
            }
        }
        if($kt_order_detail_id >0){
                $buchan = "未布产";
                if ($kt_bc_id > 0) {
                    $product_info_model = new ProductInfoModel(13);
                    $bc_sn = $product_info_model->get_bc_sn($kt_bc_id);
                    $buchan = "已布产,布产号".$bc_sn;
                } 
               
                $result['success'] = 1;
                $result['content'] = "<font color='green'>已成功关联到空托【{$buchan}】</font>，是否继续进行？<hr style='margin:5px 0px'/>证书号一旦更新，4C配钻信息将不可继续修改！<br/>";
                Util::jsonExit($result); 
        }else{
            $result['success'] = 1;
            $result['content'] = "<font color='red'>未查询到空托信息</font>，是否继续进行？<hr style='margin:5px 0px'/>证书号一旦更新，4C配钻信息将不可继续修改！";
            Util::jsonExit($result);
        }
        
    }
    /**
     * 4C配钻修改更新
     * @param unknown $params
     */
    public function update($params){
        
        $id = _Request::getInt('id');
        $order_sn = _Post::getString('order_sn');
        $zhengshuhao=_Post::getString('zhengshuhao');
        $price = _Post::getFloat('price',0);
        $discount = _Post::getFloat('discount',0);         
        $cut = _Post::getString('cut');//切工
        $carat = _Post::getString('carat');//石重
        $clarity = _Post::getString('clarity');//净度
        $color = _Post::getString('color');//颜色
        $shape = _Post::getInt('shape',1);//形状
        $consignee = _Post::get('consignee');//收货人，匹配空托时用到
        
        $dataOrg = array();//原始证书号基本信息
        $product4c_new = array();//新证书号基本信息        
        $order_detail = array();
        $product_info_attr = array();
        $logs = '4C配钻：订单号'.$order_sn.',';
                 
        $product4CModel = new ProductInfo4CModel($id,13);
        $product4c_old = $product4CModel->getDataObject();
        if(empty($product4c_old)){
            $result['error'] = "布产单明细不存在！";
            Util::jsonExit($result);
        }else{
            $zhengshuhao_org = $product4c_old['zhengshuhao'];
            $price_org = (float)$product4c_old['price'];
            $discount_org = (float)$product4c_old['discount'];  
            $order_sn = $product4c_old['order_sn']; 
        } 

        if(empty($zhengshuhao)){
           $result['error'] = "新证书号不能为空！";
           Util::jsonExit($result);
        }        
        
        //获取原始证书号基本信息
        $apiDiamondModel = new ApiDiamondModel();
        $ret = $apiDiamondModel->getDiamondInfoByCertId($zhengshuhao_org);
        if($ret['error']==0){
            $dataOrg = $ret['data'];
            $goods_id = $dataOrg['goods_sn'];
        }else{
            $result['error'] = "原裸石不存在！";
            Util::jsonExit($result);
            //验证新增裸钻属性
        }
        
        $pdo = $product4CModel->db()->db();
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
        $pdo->beginTransaction();//开启事务
        
        $apiSalesModel = new ApiSalesModel();
        $salesModel = new SalesModel(27);
        
        
        $kt_order_detail_id = 0;//空托订单商品ID
        $kt_bc_id = 0; //空托布产ID
        //根据证书号和收货人匹配空托 begin
        $order_detail_list = $salesModel->getOrderDetailsFor4C($zhengshuhao_org);      
        if(!empty($order_detail_list)){
            foreach($order_detail_list as $vo){
                if($vo['consignee'] ==$consignee){
                    $kt_bc_id = $vo['bc_id'];
                    $kt_order_detail_id = $vo['id'];
                }
                if($order_sn ==$vo['order_sn']){
                    //与裸钻订单号一样的空托，优先匹配
                    break;
                }
            }
        }
        //根据证书号和收货人匹配空托 end
        
        $notice = array();
        //证书号发生改变情况
        if($zhengshuhao != $zhengshuhao_org){  
            //查询新证书号
            $ret = $apiDiamondModel->getDiamondInfoByCertId($zhengshuhao);
            if($ret['error']==0){
                $dataNew = $ret['data'];
                //新证书号在裸钻库存在情况，需要验证新证书号是否下架
                if($dataNew['status']==2 && empty($dataNew['is_bakdata'])){
                    $result['error'] = "新证书号【{$zhengshuhao}】不可用，裸钻已下架！";
                    Util::jsonExit($result);
                }
                $goods_id = $dataNew['goods_sn'];
                //$price = (float)$dataNew['chengben_jia']; //以外面填写为准不用系统默认值               
                //$discount = (float)$dataNew['source_discount'];//以外面填写为准不用系统默认值
                $cut = $dataNew['cut'];//切工
                $carat = $dataNew['carat'];//石重
                $clarity = $dataNew['clarity'];//净度
                $color = $dataNew['color'];//颜色
                $shape = (int)$dataNew['shape'];//形状
            }else{
                $goods_id = '';
                //新证书号在裸钻库不存在情况，需要验证新证书号是否被配石过
                $ret = $product4CModel->getRow("id","zhengshuhao='{$zhengshuhao}'");
                if(!empty($ret)){
                    $product_info_model = new ProductInfoModel(13);
                    $bc_sn = $product_info_model->get_bc_sn($ret['id']);
                    $result['error'] = "新证书号已被使用过，关联布产单".$bc_sn;
                    Util::jsonExit($result);
                }
            }
            
            //订单商品明细基本信息(app_order_details表更新纪录)
            $order_detail = array(
                'zhengshuhao'=>$zhengshuhao,
                'cut'=>$cut,
                'cart'=>$carat,
                'clarity'=>$clarity,
                'color'=>$color,
                //'shape'=>$shape,
            );
            
            //新证书号基本信息(product_info_4c表新纪录)
            $product4c_new = array(
                'id'=>$id,
                'kt_order_detail_id'=>$kt_order_detail_id,
                'kt_bc_sn'=>'',
                'zhengshuhao'=>$zhengshuhao,
                'zhengshuhao_org'=>$zhengshuhao_org,
                'price_org'=>$price_org,
                'price'=>$price,
                'discount_org'=>$discount_org,
                'discount'=>$discount,
                'cut'=>$cut,
                'carat'=>$carat,
                'clarity'=>$clarity,
                'color'=>$color,
                'shape'=>$shape,
                'peishi_status'=>1,//配饰状态
                'create_user'=>Auth::$userName,
                'create_time'=>date('Y-m-d H:i:s'),
            );
            
            if (!empty($kt_bc_id)) {
                $product_info_model = new ProductInfoModel(13);
                $bc_sn = $product_info_model->get_bc_sn($kt_bc_id);
                $product4c_new['kt_bc_sn'] = $bc_sn;
            }
             
            $logs .="证书号由【{$zhengshuhao_org}】改为【{$zhengshuhao}】,";
            /*
            if($price != $product4c_old['price']){
                $logs .="采购成本由【{$product4c_old['price']}】改为【{$price}】,";
            }            
            if($discount != $product4c_old['discount']){
                $logs .="采购折扣由【{$product4c_old['discount']}】改为【{$discount}】, ";
            }*/
                        
            
            $order_detail_old = $apiSalesModel->getOrderDetailByBCId($id);
            if(!empty($order_detail_old['return_msg'])){ 
                $order_detail_old = $order_detail_old['return_msg'];
                if($goods_id != $order_detail_old['goods_id']){
                    $order_detail['goods_id'] = $goods_id;
                    $order_detail['ext_goods_sn'] = $order_detail_old['goods_id'];
                    if($goods_id==''){
                        $order_detail['goods_name']="4C裸石配钻";
                    }
                }
            }else{
                $result['error'] = "采购单关联的订单商品不存在！";
                Util::jsonExit($result);
            }

            $productAttrModel = new ProductInfoAttrModel(13);
            
            //更新布产裸石商品属性【裸钻】
            $res1   = $productAttrModel->editGoodsAttr($zhengshuhao,$id,"zhengshuhao");
            $res1_2 = $productAttrModel->editGoodsAttr($carat,$id,"cart");
            $res1_3 = $productAttrModel->editGoodsAttr($clarity,$id,"clarity");
            $res1_4 = $productAttrModel->editGoodsAttr($color,$id,"color");
            //$res1_5 = $productAttrModel->editGoodsAttr($color,$id,"shape");
            $notice[] = "更新裸钻布产基本信息";
            if($res1==false){
                $pdo->rollback();//事务回滚
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                $result['error'] = "证书号更改失败！error line:".__LINE__;
                Util::jsonExit($result);
            }
            
            //更新裸钻 配石基本信息【裸钻】
            $res2= $product4CModel->saveData($product4c_new,$product4c_old);
            $notice[] = "更新裸石配钻状态并保存配石记录";
            if($res2==false){
                $pdo->rollback();//事务回滚
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                $result['error'] = "保存4C配钻信息失败！error line:".__LINE__;
                Util::jsonExit($result);
            }
            //裸石配钻日志写入
            $logs = trim($logs,',');
            $logModel = new ProductOpraLogModel(14);
            $res3 = $logModel->addLog($id,$logs);
            $notice[] = "写入裸钻布产更改日志";
            if($res3 == false){
                $pdo->rollback();//事务回滚
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                $result['error'] = "4C配钻日志写入失败！error line:".__LINE__;
                Util::jsonExit($result);
            }
            //推送空托布产单属性信息【空托】
            if($kt_bc_id >0){
                $kt_res1   = $productAttrModel->editGoodsAttr($zhengshuhao,$kt_bc_id,"zhengshuhao");
                $kt_res1_2 = $productAttrModel->editGoodsAttr($carat,$kt_bc_id,"cart");
                $kt_res1_3 = $productAttrModel->editGoodsAttr($clarity,$kt_bc_id,"clarity");
                $kt_res1_4 = $productAttrModel->editGoodsAttr($color,$kt_bc_id,"color");
                //$kt_res_5 = $productAttrModel->editGoodsAttr($color,$id,"shape");
                $notice[] = "更新空托的布产信息中的证书号及证书属性";
                if($kt_res1==false){
                    $pdo->rollback();//事务回滚
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                    $result['error'] = "同步空托证书信息失败！事物回滚！error line:".__LINE__;
                    Util::jsonExit($result);
                }
            }       
            //推送裸钻订单商品基本信息【裸钻】
            $res4 = $apiSalesModel->EditOrderGoodsInfo($order_detail_old['id'],$order_detail);
            $notice[] = "更新裸石订单商品明细";
            if(!isset($res4['error']) || $res4['error']==1){
                $pdo->rollback();//事务回滚
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                $result['error'] = "推送订单商品信息失败！";
                Util::jsonExit($result);
            }
            //推送空托订单商品信息
            if($kt_order_detail_id>0){
                $kt_order_detail = array(
                    'zhengshuhao'=>$zhengshuhao,
                    'cut'=>$cut,
                    'cart'=>$carat,
                    'clarity'=>$clarity,
                    'color'=>$color,
                    //'shape'=>$shape,
                );
                $kt_res2 = $apiSalesModel->EditOrderGoodsInfo($kt_order_detail_id,$kt_order_detail);
                $notice[] = "更新空托的订单商品明细钻石信息";
                if(!isset($kt_res2['error']) || $kt_res2['error']==1){
                    $pdo->rollback();//事务回滚
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                    $result['error'] = "推送空托订单商品信息失败！事物回滚: error:".__LINE__;
                    Util::jsonExit($result);
                }
            }
            
            //裸钻上下架
            $certData=array();
            $certData[$zhengshuhao_org] = array('status'=>1);//原裸钻上架
            $notice[] = "更新原裸石状态为已上架";
            //如果新裸钻在裸钻表存在
            if(!empty($goods_id)){
                $certData[$zhengshuhao] = array('status'=>2);//新裸钻下架
                $notice[] = "更新新裸石状态为已下架";
            }            
            $apiDiamondModel->editDiamondInfoByCertId($certData);
            
            
        }else{
            //查询新证书号
            $dataNew = $dataOrg;
            $goods_id = $dataNew['goods_sn'];
            $price = (float)$dataNew['chengben_jia'];
            $discount = (float)$dataNew['source_discount'];
            $cut = $dataNew['cut'];//切工
            $carat = $dataNew['carat'];//石重
            $clarity = $dataNew['clarity'];//净度
            $color = $dataNew['color'];//颜色
            $shape = (int)$dataNew['shape'];//形状

            //证书号未改变情况
            $logs.="证书号未改变,";
            $product4c_new = array(
                'id'=>$id,
                'kt_order_detail_id'=>$kt_order_detail_id,
                'kt_bc_sn'=>'',
                'zhengshuhao'=>$zhengshuhao,
                'zhengshuhao_org'=>$zhengshuhao_org,
                'price_org'=>$price,
                'price'=>$price,
                'discount_org'=>$discount,
                'discount'=>$discount,
                'cut'=>$dataOrg['cut'],
                'carat'=>$dataOrg['carat'],
                'clarity'=>$dataOrg['clarity'],
                'color'=>$dataOrg['color'],
                'shape'=>$dataOrg['shape'],
                'peishi_status'=>1,//配饰状态
                'create_user'=>Auth::$userName,
                'create_time'=>date('Y-m-d H:i:s'),
            );
            
            if (!empty($kt_bc_id)) {
                $product_info_model = new ProductInfoModel(13);
                $bc_sn = $product_info_model->get_bc_sn($kt_bc_id);
                $product4c_new['kt_bc_sn'] = $bc_sn;
            }
            //更新4c布产单基本信息【裸钻】
            $res2= $product4CModel->saveData($product4c_new,$product4c_old);
            $notice[] = "更新裸石配钻状态并保存配石记录";
            if($res2===false){
                $pdo->rollback();//事务回滚
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                $result['error'] = "保存配石信息失败！error line:".__LINE__;
                Util::jsonExit($result);
            }
            //配石日志写入
            $logs = trim($logs,',');
            $logModel = new ProductOpraLogModel(14);
            $res3 = $logModel->addLog($id,$logs);
            $notice[] = "写入裸石布产更改日志";
            if($res3 === false){
                $pdo->rollback();//事务回滚
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                $result['error'] = "配石日志写入失败！error line:".__LINE__;
                Util::jsonExit($result);
            }         
            
        } 
        
        $pdo->commit();//事物提交
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        $notice_str = implode('<br/>',$notice);
        $result['success'] = 1;  
        $result['content'] = "操作成功！";
        //$result['content'] = "操作成功！本次操作系统做了以下数据处理：<hr />".$notice_str;
        Util::jsonExit($result);
        
    } 


}

?>