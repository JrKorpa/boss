<?php
use vipapis\informal\_RedirectServiceClient;
/**
 * 唯品会出仓/送货单管理
*  -------------------------------------------------
*   @file		: VipDeliveryController.php
*   @link		:  www.kela.cn
*   @copyright	: 2014-2024 kela Inc
*   @date		: 2017-06-26
*   @update	:
*  -------------------------------------------------
*/
class VipDeliveryController extends CommonController
{
    protected $smartyDebugEnabled = false;
    protected $whitelist = array("printDelivery",'printDeliveryGoods');
    /**
     *  index，搜索框
     */
    public function index ($params)
    {        
        $this->render('vip_delivery_search_form.html',array(
            'bar'=>Auth::getBar(),
        ));
    }
    public function search($params){
        $args = array(
            'mod'			=> _Request::get("mod"),
            'con'			=> substr(__CLASS__, 0, -10),
            'act'			=> __FUNCTION__,
            'po_no'		=> _Request::get("po_no"),
            'storage_no'=>_Request::get("storage_no"),  
            'out_flag'=>_Request::get("out_flag"),
            'delivery_no'=>_Request::get("delivery_no"),
            'st_estimate_arrive_time'=>_Request::get("st_estimate_arrive_time"),//预计到货时间
            'et_estimate_arrive_time'=>_Request::get("et_estimate_arrive_time"),
            'st_arrive_time'=>_Request::get("st_arrive_time"),//实际到货时间
            'et_arrive_time'=>_Request::get("et_arrive_time"),
            'st_out_time'=>_Request::get("st_out_time"),
            'et_out_time'=>_Request::get("et_out_time"),
        );
        
        $where = $args;

        $where['po_no'] = str_replace(" ",",",$where['po_no']);
        $page = _Request::getInt("page",1);
        $pageSize = 15;
        $model = new VipDeliveryModel(21);
        $view = new VipDeliveryView($model);
        $result = $model->pageList($where,$page,$pageSize);
        if($result['success']==0){
            exit($result['error']);
        }
        $pageData = $result['data'];
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'vip_delivery_search_page';
        //print_r($pageData);
        $this->render('vip_delivery_search_list.html',array(
            'pa'=>Util::page($pageData),
            'page_list'=>$pageData,
            //'pickDetailsView'=>new VipPickDetailsView(new VipPickDetailsModel(21)),
            'view'=>$view
        ));
        
    }
    /**
     * 创建出仓单 渲染页面
     * @param unknown $params
     */
    public function add($params){
        
        $result = array('success'=>0,'content'=>'','title'=>'添加出仓单');
        $model = new VipDeliveryModel(21);
        $view = new VipDeliveryView($model);
        
        $pickOrderModel = new VipPickOrderDetailsModel(21);
        $result['content'] = $this->fetch('vip_delivery_info.html',array(
            'bar'=>Auth::getBar(),
            'view'=>$view,
        ));
        Util::jsonExit($result);
    }
    /**
     * 根据快递单号查询 拣货单订单商品信息
     * @param unknown $params
     */
    public function searchDeliveryOrderList($params){
        $result = array('success'=>0,'error'=>'','data'=>'');
        $delivery_no = _Request::get('delivery_no');
        if(empty($delivery_no)){
            $result['error'] = "运单号不能为空！";
            Util::jsonExit($result);
        }
        $model = new VipDeliveryModel(21);
        $view = new VipDeliveryView($model);
        
        $pickOrderModel = new VipPickOrderDetailsModel(21);
        $where = array('delivery_no'=>$delivery_no,'delivery_status'=>0,'order_status'=>'1,2');
        $orderlist = $pickOrderModel->getOrderDetailsList($where, 1,100);

        if(empty($orderlist['data'])){
            $result['error'] = "运单号{$delivery_no}查询不到到符合条件的订单信息！";
            Util::jsonExit($result);
        }
        $warehouseArr = array_unique(array_column($orderlist['data'],'warehouse'));
        if(count($warehouseArr)>1){
            $result['error'] = "出库仓有异常：待出库订单有不同的出库仓！";
            Util::jsonExit($result);
        }
        $warehouse = $view->getWarehouseInfo($warehouseArr[0]);   
        
        $pageData = $orderlist;
        $result['warehouse'] = $warehouse;
        $result['order_list'] = $this->fetch(
            "vip_delivery_order_list.html",array(
                'pa'=> Util::page($pageData),
                'page_list'=>$pageData,
                'view'=>$view,
            )
        ); 
        $result['success'] = 1;
        Util::jsonExit($result);       
        
    }
    /**
     * 创建出仓单 保存
     * @param unknown $params
     */
    public function insert($params){
        $result = array('success'=>0,'error'=>'');
        $delivery_no = _Post::get('delivery_no');
        $warehouse = _Post::get('warehouse');
        $delivery_time = _Post::get('delivery_time');
        $arrival_time = _Post::get('arrival_time');
        $carrier = _Post::get('carrier');
        $delivery_method = _Post::get('delivery_method');
        if($delivery_no==""){
            $result['error'] = "请填写快递单号";
            Util::jsonExit($result);
        }
        
        $pickOrderModel = new VipPickOrderDetailsModel(21);
        $where = array('delivery_no'=>$delivery_no,'delivery_status'=>0,'order_status'=>'1,2');
        $orderlist = $pickOrderModel->getOrderDetailsList($where, 1,2000);
        if(empty($orderlist['data'])){
            $result['error'] = "运单号{$delivery_no}查询不到到符合条件的订单信息！";
            Util::jsonExit($result);
        }
        
        if(empty($warehouse)){
            $result['error'] = "warehouse参数错误！";
            Util::jsonExit($result);
        }
        if(empty($delivery_time)){
            $result['error'] = "发货时间不能为空！";
            Util::jsonExit($result);
        }
        if(empty($arrival_time)){
            $result['error'] = "要求到货时间不能为空！";
            Util::jsonExit($result);
        }
        if(empty($carrier)){
            $result['error'] = "承运商不能为空！";
            Util::jsonExit($result);
        }
        $carrier = explode("|", $carrier);
        if(count($carrier)!=2){
            $result['error'] = "承运商参数错误！";
            Util::jsonExit($result);
        }
        $carrier_code = $carrier[0];
        $carrier_name = $carrier[1];
        
        if(empty($delivery_method)){
            $result['error'] = "配送模式不能为空！";
            Util::jsonExit($result);
        }
        
        
        $poNoArr = array_unique(array_column($orderlist['data'],'po_no'));
        $po_no = implode(",",$poNoArr);
        
        $model = new VipDeliveryModel(21);
        $pdolist[21] = $model->db()->db();
        //开启事物
        foreach ($pdolist as $pdo){
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
            $pdo->beginTransaction(); //开启事务
        }
        
        $where = array('delivery_no'=>$delivery_no);
        $deliveryResult = $model->apiGetDeliveryList($where,1,30);
        
        $deliveryInfo = array();
        if($deliveryResult['success']==0){
            $error = 'apiGetDeliveryList:'.$deliveryResult['error'];
            Util::rollbackExit($error,$pdolist);
        }else{
            foreach ($deliveryResult['data']['data'] as $vo){
                if($vo['out_flag']==0 && empty($deliveryInfo)){
                    //取最新创建的出库单
                    $deliveryInfo = $vo;
                    break;                
                }else if($vo['out_flag']==1){
                    $error = "运单号{$delivery_no}已经使用过！";
                    Util::rollbackExit($error,$pdolist);
                }
            }
        }        
        
        //创建出库单
        $deliveryInfoData = array(
            'po_no'=>$po_no,
            'warehouse'=>$warehouse,
            'delivery_no'=>$delivery_no,
            'delivery_time'=>$delivery_time,
            'arrival_time'=>$arrival_time,
            'carrier_name'=>$carrier_name,
            'carrier_code'=>$carrier_code,
            'delivery_method'=>$delivery_method,
        );
        if(empty($deliveryInfo)){ 
            $createResult = $model->apiCreateDelivery($deliveryInfoData);
            if($createResult['success']==0){
                $error = 'apiCreateDelivery:'.$createResult['error'];
                Util::rollbackExit($error,$pdolist);
            }else{
                $delivery_id = $createResult['data']['delivery_id'];
                $storage_no  = $createResult['data']['storage_no'];
            }           
            
        }else{
            $storage_no  = $deliveryInfo['storage_no'];

            $deliveryInfoData['storage_no'] = $storage_no;
            $deliveryInfoData['race_time']  = $arrival_time;
            
            $createResult = $model->apiEditDelivery($deliveryInfoData);
            if($createResult['success']==0){
                $error = 'apiEditDelivery:'.$createResult['error'];
                Util::rollbackExit($error,$pdolist);
            }
            //删除已存在的入库单明细
            $deleteResult = $model->apiDeleteDeliveryDetail($storage_no);
            if($deleteResult['success']==0){
                $error = 'apiDeleteDeliveryDetail:'.$deleteResult['error'];
                Util::rollbackExit($error,$pdolist);
            }
            $model->deleteDeliveryDetails($storage_no);
        }
        //本地创建出仓单
        $deliveryInfoData = array(
            "storage_no"=>$storage_no,
            "po_no"=>$po_no,
            "delivery_no"=>$delivery_no,
            "warehouse"=>$warehouse,
            "out_time"=>$delivery_time,
            "arrive_time"=>$arrival_time,
            "out_flag"=>0,
            'carrier_name'=>$carrier_name,
            'carrier_code'=>$carrier_code,
            'delivery_method'=>$delivery_method,
            "create_time"=>date("Y-m-d H:i:s"),
        );
        //print_r($deliveryInfoData);
        $createResult = $model->createDelivery($deliveryInfoData);
        if($createResult['success'] ==0){
            $error = 'createDelivery:'.$createResult['error'];
            Util::rollbackExit($error,$pdolist);
        } 
        $is_multi_po = false;//出仓明细是否传入拣货单编号
        //本地创建入仓单明细        
        foreach ($orderlist['data'] as $goods){
            $goodsData = array(
                "storage_no"=>$storage_no,
                "barcode"=>$goods['barcode'],
                "goods_name"=>$goods['goods_name'],
                "goods_id"=>$goods['goods_id'],
                "amount"=>1,
                "pick_no"=>$goods['pick_no'],
                "po_no"=>$goods['po_no'],
                "style_sn"=>$goods['style_sn'],
                "order_detail_id"=>$goods['order_detail_id'],
                "order_id"=>$goods['order_id'],
                "order_sn"=>$goods['order_sn']
            );
            if(count($poNoArr)>1 && !preg_match("/MULPICK/is",$goods['pick_no'])){
                $is_multi_po = true;
            }
            $createResult = $model->createDeliveryDetails($goodsData);
            if($createResult['success'] == 0){
                $error = 'createDeliveryDetails:'.$createResult['error'];
                Util::rollbackExit($error,$pdolist);
            }
        };
        
        //更新出仓数量
        $upResult = $model->updateDeliveryNum($storage_no);
        if($upResult['success']==0){
            $error = 'updateDeliveryNum:'.$upResult['error'];
            Util::rollbackExit($error,$pdolist);
        }
        
        //将出仓明细导入到送货单中
        $delivery_goods_list = array();
        foreach ($orderlist['data'] as $vo){
            $delivery_goods = array(
                'vendor_type'=>'COMMON',
                'barcode'=>$vo['barcode'],
                'box_no'=>'1',
                'po_no'=>$vo['po_no'],
                'pick_no'=>$vo['pick_no'],
                'amount'=>1,
            );
            $delivery_goods_list[] = $delivery_goods;
            if($is_multi_po){
                $importResult = $model->apiImportMultiPoDeliveryDetail($vo['po_no'], $storage_no, array($delivery_goods));//($po_no, $storage_no, $delivery_goods_list);
                if($importResult['success']==0){
                    $error = "imp1:".$importResult['error'];
                    Util::rollbackExit($error,$pdolist);
                }
            }
        }; 
        //单拣货单
        if(!$is_multi_po){
           $importResult = $model->apiImportMultiPoDeliveryDetail($po_no, $storage_no, $delivery_goods_list);//($po_no, $storage_no, $delivery_goods_list);
           if($importResult['success']==0){
               $error = "imp2:".$importResult['error'];
               Util::rollbackExit($error,$pdolist);
           }
        }
        //批量提交事物
        foreach ($pdolist as $pdo){
            $pdo->commit();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
        }        
        $result['success'] = 1;
        Util::jsonExit($result);
        
    }  

    public function show($params){

        $storage_no = str_replace("NN","-",_Request::get("id"));
        if(empty($storage_no)){
            exit("入库单号为空");
        }        
        $model = new VipDeliveryModel(21);
        $view = new VipDeliveryView($model);
        
        $where = array('storage_no'=>$storage_no);
        $result = $model->apiGetDeliveryList($where);
        
        $delivery_info = array();
        if($result['success']==1){
            if(empty($result['data']['data'][0])){
                exit("入库单信息不存在");
            }else{
               $delivery_info = $result['data']['data'][0];
            }
        }else{
            exit($result['error']);
        }
        //$result = $model->apiGetDeliveryTraceInfo($storage_no);        
        $this->render("vip_delivery_info_show.html",array(
            'bar'=>Auth::getViewBar(),
            'delivery_info'=>$delivery_info,
            'view'=>$view
        ));
        
    }
    
    public function searchDeliveryTraceInfo($params){
        $storage_no = _Request::get('storage_no');
        $model = new VipDeliveryModel(21);
        $result = $model->apiGetDeliveryTraceInfo($storage_no);
        if($result['success']==0){
            exit("error:".$result['error']);
        }        
        $this->render("vip_delivery_traceinfo.html",array(
            'data'=>$result['data'],
        ));
    }
    /**
     * 查询出仓送货明细 货品列表
     * @param unknown $params
     */
    public function searchDeliveryGoodsList($params){
        
        $model = new VipDeliveryModel(21);
        $view = new VipDeliveryView($model);
        
        $storage_no = _Request::get('storage_no');
        $page = _Request::get('page',1);
        $pageSize = 15;
        $data = $model->getDeliveryGoodsList($storage_no,$page,$pageSize);
        
        $pageData = $data;
        $pageData['args'] = array('storage_no'=>$storage_no);
        $pageData['jsFuncs'] = 'vip_delivery_goods_search_page';
        $this->render("vip_delivery_goods_list.html",
            array(
                'pa'=> Util::page($pageData),
                'page_list'=>$pageData,
                'view'=>$view,
            )
        );
    }    
    /**
     * 出仓单确认 操作
     * @param unknown $pramas
     */    
    public function confirmDelivery($params){
        
        $result = array('success'=>0,'error'=>'');  
              
        $storage_no = str_replace("NN","-",_Request::get("id"));        
        if(empty($storage_no)){
            $result['error'] = "参数错误：出仓单号不能为空！";
            Util::jsonExit($result);
        }
        $model = new VipDeliveryModel(21);
        $confirmResult = $model->confirmDelivery($storage_no);
        if($confirmResult['success']==0){
            $result['error'] = $confirmResult['error'];
            Util::jsonExit($result);
        }
        //操作成功！
        $result['success'] = 1;
        Util::jsonExit($result);

    }
    
    public function printDelivery($params){
        
        $storage_no = str_replace("NN","-",_Request::get("_ids"));
        if(empty($storage_no)){
            exit("参数错误:入库单号为空！");
        }        
        $model = new VipDeliveryModel(21);
        $view  = new VipDeliveryView($model);
        $deliveryInfo = $model->getDeliveryByNo($storage_no);
        if(empty($deliveryInfo)){
            exit("该单据无发打印，不是boss系统录入的单据！");
        }
        //$deliveryInfo['arrive_time'] = trim($deliveryInfo['arrive_time'],'');
       // $resutl = $model->apiGetPrintDelivery($storage_no,$deliveryInfo['po_no']); 
        //print_r($resutl);       
        $this->render("vip_delivery_print_delivery.html",array(
            'data'=>$deliveryInfo,
            'view'=>$view
        ));
    }
    
    public function printDeliveryGoods($params){
        $storage_no = str_replace("NN","-",_Request::get("_ids"));
        if(empty($storage_no)){
            exit("参数错误:入库单号为空！");
        } 
        $model = new VipDeliveryModel(21);
        $view  = new VipDeliveryView($model);
        $deliveryInfo = $model->getDeliveryByNo($storage_no);
        if(empty($deliveryInfo)){
            exit("该单据无发打印，不是boss系统录入的单据！");
        }
        
        $result =  $data = $model->getDeliveryGoodsList($storage_no,1,999);
        
        $data = $deliveryInfo ;
        $data['goods_total'] = 0;
        
        $goods_list = array();
        foreach ($result['data'] as $vo){
            $data['goods_total'] += $vo['amount'];
            
            if(array_key_exists($vo['barcode'],$goods_list)){
               $goods_list[$vo['barcode']]['amount'] +=$vo['amount'];
            }else{
               $goods_list[$vo['barcode']] = $vo;
            }
            $data['goods_list'] = $goods_list;
        }
        $this->render("vip_delivery_print_delivery_goods.html",array(
            'data'=>$data,
            'view'=>$view
        ));
    }

}
    
 ?>