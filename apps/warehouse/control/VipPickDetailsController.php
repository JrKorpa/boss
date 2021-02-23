<?php
/**
 * 唯品会拣货单明细
*  -------------------------------------------------
*   @file		: VipPickDetailsController.php
*   @link		:  www.kela.cn
*   @copyright	: 2014-2024 kela Inc
*   @date		: 2017-06-26
*   @update	:
*  -------------------------------------------------
*/
class VipPickDetailsController extends CommonController
{
    protected $smartyDebugEnabled = false;
    protected $whitelist = array();
   
   
    /**
     * 绑定货号
     * @param unknown $params
     */
    public function pickGoods($params){
        $pick_no = _Request::get("pick_no");
        $sku_no = _Request::get("sku_no");
        $po_no = _Request::get("po_no");
        /*$arr = explode('-',$pick_no);
        if(count($arr)==3){
            $po_no = $arr[1];
        }else{
            echo 'pick_no is wrong!';
            exit;
        }*/
        $model = new VipPickDetailsModel(21);
        $pickOrderModel = new VipPickOrderDetailsModel(21);
        $view = new VipPickDetailsView($model);
         
        $result = $model->apiGetPickDetailsOne($sku_no,$po_no,$pick_no);
        if($result['success']==0){
            exit($result['error']);
        }else if(empty($result['data'])){
            exit('error');
        }
        //print_r($result);
        $pick_info    = $result['data']['pick_info'];
        $pick_details = $result['data']['pick_details'];
        $boss_notpick_num = $pick_details['boss_notpick_num'];//剩余未捡货数量
        
        $pick_page_size = 999;//每次最大捡货数量,暂时设置无限大
        if($boss_notpick_num<$pick_page_size){
            $pick_page_size = $boss_notpick_num;
        }
        //已捡货订单商品列表
        $where = array('pick_no'=>$pick_no,'barcode'=>$sku_no,'po_no'=>$po_no);
        $pick_order_list = $pickOrderModel->pageList($where, 1,9999);
        if(!empty($pick_order_list)){
            $pick_order_list = $pick_order_list['data'];
        }
        $this->render('vip_pickdetails_pickgoods.html',array(
            'bar'=>Auth::getViewBar(),
            'view'=>$view,
            'pick_info'=>$pick_info,
            'pick_details'=>$pick_details,
            'pick_page_size'=>$pick_page_size,
            'pick_order_list'=>$pick_order_list,
        ));
    }     
    /**根据拣货单明细创建订单
     * @param unknown $params
     */
    public function pickGoodsSave($params){
        $result = array('success'=>0,'error'=>'');
        $goods_list = _Post::getList('goods');
        $pick_no = _Post::get('pick_no');
        $po_no = _Post::get('po_no');
        $barcode = _Post::get('barcode');
        $warehouse = _Post::get('warehouse');
        if(empty($goods_list['parent'])){
            $result['error'] = "提交数据为空！";
            Util::jsonExit($result);
        }
        
        $salesModel = new CSalesModel(27);
        $pickDetailsModel =  new VipPickDetailsModel(21);
        $warehouseModel = new WarehouseGoodsModel(21);

        $pickDetail = $pickDetailsModel->getPickDetail("pick_no='{$pick_no}' and barcode='{$barcode}' and po_no='{$po_no}'");
        
        if(empty($pickDetail)){
            //强制调用API同步数据
            Util::httpCurl("/cron/vip/index.php?act=updatepicklist&pick_no={$pick_no}");
            $result['error'] = "此拣货单未同步到本地库，请再次重新尝试！";
            Util::jsonExit($result);
        }else if($pickDetail['boss_pick_status']==1){
            $result['error'] = "SKU码{$barcode}已捡货完成,不能再次操作！";
            Util::jsonExit($result);
        }
        
        
        //收货地址
        $address_region = _Post::get('address_region');
        $addressList = VipPickDetailsView::getAddressList();
        if(empty($address_region)){
            $result['error'] = "请选择配送地址！";
            Util::jsonExit($result);
        }else if(!isset($addressList[$address_region])){
            $result['error'] = "配送地址无效！";
            Util::jsonExit($result);
        }else{
            
            $addressInfo1 = $addressList[$address_region];
            $addressInfo2 = VipPickDetailsView::getAddressInfoByWarehoue($warehouse);
            if(!empty($addressInfo2) && $addressInfo2['region']!=$addressInfo1['region']){
                $result['error'] = "配送地址选择错误，你应该选择【{$addressInfo2['region']}】";
                Util::jsonExit($result);
            }
            $consignee_id = $addressInfo1['consignee_id'];//收货人
            $consignee = $addressInfo1['consignee'];//收货人
            $address = $addressInfo1['address'];//收货地址    
            $tel = $addressInfo1['tel'];//收货人联系电话
            $express_id = $addressInfo1['express_id'];//快递公司
            $country_id = $addressInfo1['country_id'];//省
            $province_id = $addressInfo1['province_id'];//市
            $city_id = $addressInfo1['city_id'];//县
            $regional_id = $addressInfo1['regional_id'];//区
        }
        
        $pdolist[21] = $pickDetailsModel->db()->db();//仓库数连接PDO
        $pdolist[27] = $salesModel->db()->db();//订单销售连接PDO
        try{
            foreach ($pdolist as $pdo){
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
                $pdo->beginTransaction(); //开启事务
            }
        }catch (Exception $e){
            $error = "操作失败，事物回滚！提示：系统批量开启事物时发生异常！";
            Util::rollbackExit($error,$pdolist);
        }
        $is_xianhuo = 1;
        $is_zp = 0;
        $datalist = array();
        
        //初始化商品列表 begin
        $_goods_list = array();
        foreach ($goods_list as $k=>$v){
            foreach ($v as $_k=>$_v){
               $_goods_list[$_k][$k] = $_v;
            }
        }
        if(isset($goods_list)) unset($goods_list);
        foreach ($_goods_list as $k => $v){
            $datalist[$v['parent']][] = $v;

        }
        if(isset($_goods_list)) unset($_goods_list);
        //初始化商品列表 end
        foreach ($datalist as $line_no=>$goods_list){ 
            $order_amount = 0;
            $goodslist = array();
            foreach ($goods_list as $i=>$_goods){
                $i++;
                $goods = array();
                $goods['goods_id'] = isset($_goods['goods_id'])?$_goods['goods_id']:'';
                $goods['ext_goods_sn'] = $barcode;//VIP条码
                $goods['goods_name'] = isset($_goods['goods_name'])?$_goods['goods_name']:'';
                $goods['goods_sn'] = isset($_goods['style_sn'])?$_goods['style_sn']:'';
                $goods['goods_count'] = 1;            
                $goods['is_stock_goods'] = empty($goods['goods_id'])?0:1;
                $goods['cart'] = isset($_goods['cart'])?$_goods['cart']/1:'0';
                $goods['zhushi_num'] = isset($_goods['zhushi_num'])?$_goods['zhushi_num']/1:'0';
                $goods['color'] = isset($_goods['color'])?$_goods['color']:'';
                $goods['clarity'] = isset($_goods['clarity'])?$_goods['clarity']:'';
                $goods['xiangkou'] = isset($_goods['xiangkou'])?$_goods['xiangkou']:'';
                $goods['caizhi'] = isset($_goods['caizhi'])?$_goods['caizhi']:'';
                $goods['jinse'] = isset($_goods['jinse'])?$_goods['jinse']:'';
                $goods['zhiquan'] = isset($_goods['zhiquan'])?$_goods['zhiquan']:'';
                $goods['jinzhong'] = isset($_goods['jinzhong'])?$_goods['jinzhong']:'';
                $goods['cert'] = isset($_goods['cert'])?$_goods['cert']:'';
                $goods['zhengshuhao'] = isset($_goods['zhengshuhao'])?$_goods['zhengshuhao']:'';
                $goods['face_work'] = isset($_goods['face_work'])?$_goods['face_work']:'';            
                $goods['xiangqian'] = isset($_goods['xiangqian'])?$_goods['xiangqian']:'';            
                $goods['kezi'] = isset($_goods['kezi'])?$_goods['kezi']:'';
                $goods['goods_price'] = isset($_goods['goods_price'])?$_goods['goods_price']:'';
                $goods['details_remark']=isset($_goods['details_remark'])?$_goods['details_remark']:'';
                $goods['goods_type']=isset($_goods['goods_type'])?$_goods['goods_type']:'style_goods';
                $goods['is_zp']=isset($_goods['is_zp'])?$_goods['is_zp']:'0';
                if($goods['is_zp']==1) {
                    $is_zp = 1;
                    $goods['is_stock_goods'] = 1;
                    $goods['goods_type'] = 'zp';
                }
                if($goods['is_stock_goods']==0){
                    $is_xianhuo = 0;
                }
                $errorTitle = "序号{$line_no},第{$i}个商品";
                if(empty($goods['goods_id']) && empty($goods['goods_sn'])){
                    $error = $errorTitle.":货号和款号不能同时为空！";
                    Util::rollbackExit($error,$pdolist);
                }
                if(!empty($goods['goods_id'])){
                    $goodsinfo = $warehouseModel->getGoodsByGoods_id($goods['goods_id']);
                    if(empty($goodsinfo)){
                        $error = $errorTitle.":货号{$goods_id}不存在！";
                        Util::rollbackExit($error,$pdolist);
                    }else if($goodsinfo['is_on_sale']!=2){
                        $error = $errorTitle.":货号{$goods_id}不是库存状态！";
                        Util::rollbackExit($error,$pdolist);
                    }else if(!empty($goodsinfo['order_goods_id'])){
                        $error = $errorTitle.":货号{$goods_id}已绑定订单！";
                        Util::rollbackExit($error,$pdolist);
                    }
                    if($goodsinfo['goods_sn']=="DIA"){
                        $goods['goods_type'] = 'lz';
                    }
                    $goods['goods_name'] = $goodsinfo['goods_name'];
                }
                /* if($goods['is_zp']==""){
                    $error = "序号{$line_no},是否赠品选项不能为空！";
                    Util::rollbackExit($error,$pdolist);
                } */
                if($i==1 && (empty($goods['goods_price']) || !is_numeric($goods['goods_price']))){
                    $error = $errorTitle.":商品金额不合法";
                    Util::rollbackExit($error,$pdolist);
                }else{
                    $order_amount += $goods['goods_price'];
                }
                $goods['is_finance']=2;//需要销账
                $goods['details_status']= 1;
                $goods['create_time']=date("Y-m-d H:i:s");
                $goods['create_user']=$_SESSION['userName'];
                $goods['modify_time']= date("Y-m-d H:i:s");
                $goodslist[] = $goods;
            }
            
            //订单信息，1个商品1个订单
            $order_sn = CSalesModel::createOrderSn();
            $order_remark = "唯品会，登陆帐号进第三方后台，制作条形码：名称+SKU码：{$barcode}（{$address_region}）{$pick_no}";
            $order = array();
            $order['order_sn'] = $order_sn;
            $order['user_id'] = $consignee_id;
            $order['consignee'] = $consignee;
            $order['mobile'] = $tel;
            $order['is_xianhuo'] = $is_xianhuo;
            $order['order_status'] = 1;
            $order['create_time'] = date("Y-m-d H:i:s");
            $order['create_user'] = $_SESSION['userName'];
            $order['modify_time'] = date("Y-m-d H:i:s");
            $order['order_remark'] = $order_remark;
            $order['customer_source_id'] = 2034;//唯品会B2C 170
            $order['department_id'] = 13;//B2C销售部 13
            $order['order_pay_type'] = 170;//唯品会代销 170
            $order['is_delete'] = 0;
            $order['buchan_status'] = 1;
            $order['is_zp'] = $is_zp;
            $order['referer'] = '外部订单';
            $order['is_real_invoice'] = 1;//需要开发票
            //订单金额信息
            $money = array();
            $money['order_amount'] =$order_amount;
            $money['money_paid'] = 0;
            $money['money_unpaid'] = $order_amount;
            $money['shipping_fee'] = 0;
            $money['goods_amount'] = $order_amount;//商品总金额
            $money['favorable_price']= 0;
            
            //发票信息
            $invoice_info = array();
            $invoice_info['is_invoice'] = 0;
            $invoice_info['invoice_amount'] = 0;
            $invoice_info['invoice_title'] = "个人";
            $invoice_info['invoice_status'] = 1;//未开发票            
            $invoice_info['create_time'] = date("Y-m-d H:i:s");
            
            //收货地址信息
            $address_info = array();
            $address_info['consignee'] = $consignee;//收货人
            $address_info['tel'] = $tel;//联系电话
            $address_info['address'] = $address;
            $address_info['distribution_type']=2;            
            $address_info['express_id'] = $express_id;//快递公司
            $address_info['country_id'] = $country_id;//省
            $address_info['province_id'] = $province_id;//市
            $address_info['city_id'] = $city_id;//县
            $address_info['regional_id'] =$regional_id;//区
            
            //创建订单
            $order_log = "唯品会拣货单创建订单";
            $out_order_sn = null;
            $res = $salesModel->createOrder($order,$goodslist,$money,$invoice_info,$address_info,$out_order_sn,$order_log,false);
            if($res['success']==0){
                $error = $res['error'];
                Util::rollbackExit($error,$pdolist);
            }
            $order_info = $res['returnData'];
            $res = $pickDetailsModel->bindPickOrder($barcode,$pick_no,$po_no,$order_info,$address_region);
            if($res['success']==0){
                $error = $res['error'];
                Util::rollbackExit($error,$pdolist);
            }            
        }
        try{  
            //$error = "test！";
            //Util::rollbackExit($error,$pdolist);
            //批量提交事物
            foreach ($pdolist as $pdo){
                $pdo->commit();
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
            }
            $result['success'] = 1;
            Util::jsonExit($result);
            
        }catch (Exception $e){
            $error = "操作失败，事物回滚！提示：系统批量提交事物时发生异常！";
            Util::rollbackExit($error,$pdolist);
        }

    }
    
    /**
     * 根据款号带出 主石粒数等属性
    */
    public function getStyleAttrsAjax(){
        $result = array('success'=>0,'error'=>'','data'=>array());
        $style_sn = _Request::getString('style_sn');
        $styleMolel = new CStyleModel(11);
        $exists = $styleMolel->select('count(*)',"style_sn='{$style_sn}'",3,'base_style_info');
        if(!$exists){
            $result['error'] = "款号不存在！";
            Util::jsonExit($result);
        }
        $stoneList = $styleMolel->getStyleStoneByStyleSn($style_sn);
        $zhushi_num = 0;
        if(!empty($stoneList[1])){
            $zhushiList = $stoneList[1];//主石列表
            foreach ($zhushiList as $zhushi) {
                $zhushi_num += $zhushi['zhushi_num'];
            }
        }
        $data['zhushi_num'] = $zhushi_num;
        
        $result['success'] = 1;
        $result['data'] = $data;        
        Util::jsonExit($result);
        
    }
    /**
     * 根据货号查询货品属性
     */
    public function getGoodsInfoAjax($params){
        $result = array('success'=>0,'error'=>'','data'=>array());
        $goods_id = _Request::getString('goods_id');
        
        $model = new WarehouseGoodsModel(21);
        $goodsinfo = $model->getGoodsByGoods_id($goods_id);
        if(empty($goodsinfo)){
            $result['error'] = "货号{$goods_id}不存在！";
            Util::jsonExit($result);
        }else if($goodsinfo['is_on_sale']!=2){
            $result['error'] = "货号{$goods_id}不是库存状态！";
            Util::jsonExit($result);
        }else if(!empty($goodsinfo['order_goods_id'])){
            $result['error'] = "货号{$goods_id}已绑定订单！";
            Util::jsonExit($result);
        }
        //材质，材质颜色拆分
        $caizhiArr = GoodsAttributeModel::getGoldAndColor($goodsinfo['caizhi']);
        $goodsinfo['caizhi'] = $caizhiArr['gold'];
        $goodsinfo['jinse']  = $caizhiArr['color'];
        if(empty($goodsinfo['zhushiyanse'])){
            $goodsinfo['zhushiyanse'] = '无';
        }
        if(empty($goodsinfo['zhushijingdu'])){
            $goodsinfo['zhushijingdu'] = '无';
        }       
        
        $result['success'] = 1;
        $result['data'] = $goodsinfo;
        Util::jsonExit($result);
    }
    /**
     * 商品列表搜索
     * @param unknown $params
     */
    public function searchGoodsList($params){
        $args = array(
            'mod'	=> _Request::get("mod"),
            'con'	=> substr(__CLASS__, 0, -10),
            'act'	=> __FUNCTION__,
            'style_sn'=> _Request::get("style_sn"),
            'stone'=> _Request::get("stone"),
            'color'=> _Request::get("color"),
            'clarity'	=> _Request::get("clarity"),
            'zhengshuhao'	=> _Request::get("zhengshuhao"),
            'zhiquan_min'	=> _Request::get("zhiquan_min"),
            'zhiquan_max'	=> _Request::get("zhiquan_max"),
            'jinzhong_min'	=> _Request::get("jinzhong_min"),
            'jinzhong_max'	=> _Request::get("jinzhong_max"),
            'carat_min'	=> _Request::get("carat_min"),
            'carat_max'	=> _Request::get("carat_max"),
            'caizhi'   => _Request::get("caizhi"),
            'jinse' => _Request::get("jinse"),//是否绑定
            'is_on_sale'=>2,
            'company_id'=>58
        );
        $where = $args;
        $page = _Request::getInt('page',1);
        $pageSize = 10;
        
        $warehouseGoodsModel = new WarehouseGoodsModel(21);
        $data = $warehouseGoodsModel->searchGoodsList($where,$page,$pageSize,true);
        /* foreach($data['data'] as $k=>&$v){
            //取出材质
            $caizi = GoodsAttributeModel::getgoldandcolor($v['caizhi']);
            $v['gold']  = $caizi['gold'];
            $v['jinse'] = $caizi['color'];            	
            //取出货品类型
            $v['tuo_type'] = $this->dd->getEnum('warehouse_goods.tuo_type',$v['tuo_type']);         
        } */
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'vip_pickdetail_search_goods_page';
        $this->render('vip_pickdetails_search_goods_list.html',
            array(
                'data'=>$data['data'],
                'pa'=>Util::page($pageData)                
            ));
    }
    
}