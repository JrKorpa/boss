<?php
/**
 * 唯品会拣货单列表
*  -------------------------------------------------
*   @file		: VipPicklistController.php
*   @link		:  www.kela.cn
*   @copyright	: 2014-2024 kela Inc
*   @date		: 2017-06-26
*   @update	:
*  -------------------------------------------------
*/
class VipPickListController extends CommonController
{
    protected $smartyDebugEnabled = false;
    protected $whitelist = array();
    /**
     *  index，搜索框
     */
    public function index ($params)
    {
        
        $this->render('vip_picklist_search_form.html',array(
            'bar'=>Auth::getBar(),
        ));
    }
    
    public function search($params)
    {   
        $args = array(
            'mod'			=> _Request::get("mod"),
            'con'			=> substr(__CLASS__, 0, -10),
            'act'			=> __FUNCTION__,
            'pick_no'		=> _Request::get("pick_no"),
            'po_no'		=> _Request::get("po_no"),
            'boss_pick_status'=>_Request::get("boss_pick_status"),
            'st_create_time'=> _Request::get("st_create_time"),
            'et_create_time'=> _Request::get("et_create_time"),
        );
        
        $where = $args;
        $where['pick_no'] = str_replace(" ",",",$where['pick_no']);
        $where['po_no'] = str_replace(" ",",",$where['po_no']);
        $page = _Request::getInt("page",1);
        $pageSize = 30;
        $model = new VipPickListModel(21);

        $result = $model->pageList($where,$page,$pageSize);
        
        //$result = $model->apiGetPickList($where,$page,$pageSize);
        if($result['success']==0){
            exit($result['error']);
        }  
        $pageData = $result['data']; 
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'vip_picklist_search_page';
        //print_r($pageData);
        $this->render('vip_picklist_search_list.html',array(
            'pa'=>Util::page($pageData),
            'page_list'=>$pageData,
            'pickDetailsView'=>new VipPickDetailsView(new VipPickDetailsModel(21)),
        ));
    }
    /**
     * 拣货单明细列表
     * @param unknown $params
     */
    public function show($params){
        $pick_no = _Request::get("id");
        $pick_no = str_replace('NN','-',$pick_no);
    
        $arr = explode('-',$pick_no);
        if(count($arr)==3){
            $po_no = $arr[1];
        }else{
            echo 'pick_no is wrong!';
            exit;
        }
        //$this->test($po_no,$pick_no);
        $model = new VipPickDetailsModel(21);
        $view = new VipPickDetailsView($model);
        $result = $model->apiGetPickDetails($po_no, $pick_no,1,999);
        if($result['success']==0){
            exit($result['error']);
        }else if(empty($result['data']['data'])){
            exit('error');
        }
        $data = $result['data']['data'];
        $this->render('vip_picklist_show.html',array(
            'bar'=>Auth::getViewBar(),
            'view'=>$view,
            'data'=>$data,
        ));
    }
    
    public function test($po_no,$pick_no=''){
        
        $model = new VipPickListModel(21);
        $data = array(
            'po_no'=>$po_no,
            'warehouse'=>VipPickDetailsView::getWarehouseValue("VIP_BJ"),
            'delivery_no'=>'444836486786',
            'arrival_time'=>'2017-07-01 09:00:00',
            'carrier_name'=>'其他',
            'carrier_code'=>'2',            
        );
        //创建出库单
        //$model->apiCreateDelivery($data,$page=1,$pageSize=30);
      
        $data = array('po_no'=>$po_no,'delivery_no'=>'444836486786');
        //查询出库单列表
        $data = array('po_no'=>$po_no);        
        $model->apiGetDeliveryList($data,1,999);
        return;
        //供应商类型： 只可传：COMMON或3PL
        $storage_no = "7097094267-0001";
        $delivery_list = array(
            array(
                'vendor_type'=>'COMMON',
                'barcode'=>'VOP70970942670',
                'box_no'=>'kela1234567box1',
                'po_no'=>"7097094267",
                'pick_no'=>"PICK-7097094267-1",
                'amount'=>'1',
            )/*,
            array(
                'vendor_type'=>'COMMON',
                'barcode'=>'VOP70970942672',
                'box_no'=>'kela1234567box2',
                'po_no'=>"7097094267",
                'pick_no'=>"PICK-7097094267-1",
                'amount'=>'2',
            ),*/
        );
        //Step5:将出仓明细导入到送货单中
        $model->apiImportDeliveryDetail($po_no, $storage_no, $delivery_list);
        //Step6:删除指定单号的出仓明细
        //$model->apiDeleteDeliveryDetail($storage_no,$po_no);
        //查询出库单内的商品列表
        $model->apiGetDeliveryGoods($storage_no); 
        //Step7:确认出库/送货单       
        //$model->apiConfirmDelivery($storage_no);//Array ( [success] => 1 [error] => [data] => true )
    }
    /**根据拣货单明细创建订单
     * @param unknown $params
     */
    public function createOrder($params){
        $result = array('success'=>0,'error'=>'');
        $goods_list = _Post::getList('goods');
        $pick_no = _Post::get('pick_no');
        $po_no = _Post::get('po_no');
        $barcode = _Post::get('barcode');
        $warehoue = _Post::get('warehoue');
        $is_make_order = _Post::get('is_make_order');
        $pickListModel =  new VipPickListModel(21);
        $pickInfo = $pickListModel->getPickInfoByPickNo($pick_no);
        if(empty($pickInfo)){
            //强制调用API同步数据
            Util::httpCurl("/cron/vip/index.php?act=updatepicklist&pick_no={$pick_no}");
            $result['error'] = "此拣货单未同步到本地库，请再次重新尝试！";
            Util::jsonExit($result);
        }else if($pickInfo['is_make_order']==1){
            $result['error'] = "此拣货单已制单，不能再操作！";
            Util::jsonExit($result);
        }
        if(empty($goods_list['art_no'])){
            $result['error'] = "提交数据为空！";
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
            $consignee = $addressList[$address_region]['consignee'];//收货人
            $address = $addressList[$address_region]['address'];//收货地址    
            $tel = $addressList[$address_region]['tel'];//收货人联系电话
            $consignee_id = $addressList[$address_region]['consignee_id'];//收货人
        }
        
        $salesModel = new CSalesModel(27);
        $pickDetailsModel =  new VipPickDetailsModel(21);
        
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
        $warehouseModel = new WarehouseGoodsModel(21);
        foreach ($goods_list['art_no'] as $i=>$art_no){            
            //商品信息
            $goods = array();
            $sku_no = isset($goods_list['sku_no'][$i])?$goods_list['sku_no'][$i]:'';
            $goods['goods_id'] = isset($goods_list['goods_id'][$i])?$goods_list['goods_id'][$i]:'';
            $goods['ext_goods_sn'] = isset($goods_list['art_no'][$i])?$goods_list['art_no'][$i]:'';//VIP货号
            $goods['goods_name'] = isset($goods_list['goods_name'][$i])?$goods_list['goods_name'][$i]:'';
            $goods['goods_sn'] = isset($goods_list['style_sn'][$i])?$goods_list['style_sn'][$i]:'';
            $goods['goods_count'] = 1;            
            $goods['is_stock_goods'] = empty($goods['goods_id'])?0:1;
            $goods['cart'] = isset($goods_list['cart'][$i])?$goods_list['cart'][$i]:'';
            $goods['zhushi_num'] = isset($goods_list['zhushi_num'][$i])?$goods_list['zhushi_num'][$i]:'';
            $goods['color'] = isset($goods_list['color'][$i])?$goods_list['color'][$i]:'';
            $goods['clarity'] = isset($goods_list['clarity'][$i])?$goods_list['clarity'][$i]:'';
            $goods['xiangkou'] = isset($goods_list['xiangkou'][$i])?$goods_list['xiangkou'][$i]:'';
            $goods['caizhi'] = isset($goods_list['caizhi'][$i])?$goods_list['caizhi'][$i]:'';
            $goods['jinse'] = isset($goods_list['jinse'][$i])?$goods_list['jinse'][$i]:'';
            $goods['zhiquan'] = isset($goods_list['zhiquan'][$i])?$goods_list['zhiquan'][$i]:'';
            $goods['jinzhong'] = isset($goods_list['jinzhong'][$i])?$goods_list['jinzhong'][$i]:'';
            $goods['cert'] = isset($goods_list['cert'][$i])?$goods_list['cert'][$i]:'';
            $goods['zhengshuhao'] = isset($goods_list['zhengshuhao'][$i])?$goods_list['zhengshuhao'][$i]:'';
            $goods['face_work'] = isset($goods_list['face_work'][$i])?$goods_list['face_work'][$i]:'';            
            $goods['xiangqian'] = isset($goods_list['xiangqian'][$i])?$goods_list['xiangqian'][$i]:'';            
            $goods['kezi'] = isset($goods_list['kezi'][$i])?$goods_list['kezi'][$i]:'';
            $goods['goods_price'] = isset($goods_list['goods_price'][$i])?$goods_list['goods_price'][$i]:'';
            $goods['details_remark']=isset($goods_list['details_remark'][$i])?$goods_list['details_remark'][$i]:'';
            $goods['goods_type']=isset($goods_list['goods_type'][$i])?$goods_list['goods_type'][$i]:'style_goods';
            $line_no = $i+1;            
            if(empty($goods['goods_id']) && empty($goods['goods_sn'])){
                $error = "序号{$line_no},货号和款号不能同时为空！";
                Util::rollbackExit($error,$pdolist);
            }
            if(empty($goods['goods_price']) || !is_numeric($goods['goods_price'])){
                $error = "序号{$line_no},商品金额不合法";
                Util::rollbackExit($error,$pdolist);
            }else{
                $order_amount = $goods['goods_price'];
            }
            
            $goodsinfo = $warehouseModel->getGoodsByGoods_id($goods_id);
            if(empty($goodsinfo)){
                $error = "序号{$line_no},货号{$goods_id}不存在！";
                Util::rollbackExit($error,$pdolist);
            }else if($goodsinfo['is_on_sale']!=2){
                $error = "序号{$line_no},货号{$goods_id}不是库存状态！";
                Util::rollbackExit($error,$pdolist);
            }else if(!empty($goodsinfo['order_goods_id'])){
                $error = "序号{$line_no},货号{$goods_id}已绑定订单！";
                Util::rollbackExit($error,$pdolist);
            }
            $goods['is_zp']= 0;//非赠品
            $goods['is_finance']=2;//需要销账
            $goods['details_status']= 1;
            $goods['create_time']=date("Y-m-d H:i:s");
            $goods['create_user']=$_SESSION['userName'];
            $goods['modify_time']= date("Y-m-d H:i:s");
            
            //订单信息，1个商品1个订单
            $order_sn = CSalesModel::createOrderSn();
            $order_remark = "唯品会，登陆帐号进第三方后台，制作条形码：名称+SKU码：{$sku_no}（{$address_region}）{$pick_no}";
            $order = array();
            $order['order_sn'] = $order_sn;
            $order['user_id'] = $consignee_id;
            $order['consignee'] = $consignee;
            $order['mobile'] = $tel;
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
            $order['is_zp'] = 0;
            $order['referer'] = '外部订单';
            $order['is_real_invoice'] = 1;//需要开发票
            //订单金额信息
            $money = array();
            $money['order_amount'] =$order_amount;
            $money['money_paid'] = 0;
            $money['money_unpaid'] = $order_amount;
            $money['shipping_fee'] = 0;
            $money['goods_amount'] = $goods['goods_price'];//商品总金额
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
            
            $goodslist = array($goods);
            //创建订单
            $res = $salesModel->createOrder($order,$goodslist,$money,$address_info,$invoice_info,false);
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
    public function getGoodsInfoAjax(){
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
        }
        
        $result['success'] = 1;
        $result['data'] = $goodsinfo;
        Util::jsonExit($result);
    }
    
}