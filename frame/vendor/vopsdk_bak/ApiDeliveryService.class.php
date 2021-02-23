<?php
require_once 'VopWrapper.class.php';
class ApiDeliveryService {
	private $_instance = null;
	
	public function __construct(){
	    $this->_instance = VopWrapper::getInstance(VopWrapper::MOD_DELIVERY);     
	}
	/**
	 * 查询PO单列表
	 * @param string $po_no
	 * @param string $st_sell_st_time
	 * @param string $et_sell_st_time
	 * @param number $page
	 * @param number $limit
	 * @return multitype:number string NULL
	 */
	public function getPoList($where = array(),$page = 1,$limit = 50) {
        //开始查询的创建时间(格式为yyyy-MM-dd HH:mm:ss)-【已作废】
	    $st_create_time = null;
	    //结束查询的创建时间(格式为yyyy-MM-dd HH:mm:ss)-【已作废】
        $et_create_time = null;
        //仓库/销售地区-【已作废】
        $warehouse = null;
        //po号
        $po_no = isset($where['po_no'])?$where['po_no']:null;
        //合作模式,JIT分销：jit_4a 普通JIT：jit
        $co_mode = VopWrapper::CO_MODE;
        //供应商ID
        $vendor_id = VopWrapper::VENDOR_ID;
        //开始查询的的销售开始时间(格式为yyyy-MM-dd HH:mm:ss 开始与结束时间都为空时, 默认返回需要拣货的po)
        $st_sell_st_time = isset($where['st_sell_st_time'])?$where['st_sell_st_time']:null;
        //结束查询的销售开始时间(格式为yyyy-MM-dd HH:mm:ss 开始与结束时间都为空时,默认返回需要拣货的po)
        $et_sell_st_time = isset($where['et_sell_st_time'])?$where['et_sell_st_time']:null;
        //开始查询的销售结束时间(格式为yyyy-MM-dd HH:mm:ss)-【已作废】
        $st_sell_et_time = null;
        //结束查询的销售结束时间(格式为yyyy-MM-dd HH:mm:ss)-【已作废】
        $et_sell_et_time = null;
		return $this->_instance->getPoList($st_create_time, $et_create_time, $warehouse, $po_no, $co_mode, $vendor_id, $st_sell_st_time, $et_sell_st_time, $st_sell_et_time, $et_sell_et_time, $page, $limit);
	}	
	/**
	 * 获取指定供应商下拣货单列表信息
	 * @param unknown $where 
	 * @param number $page
	 * @param number $limit
	 * @return multitype:number string NULL
	 */
	public function getPickList($where = array(),$page=1, $limit=50){
	    //供应商ID【必填】
        $vendor_id = VopWrapper::VENDOR_ID;
        //po订单号,支持批量，用英文逗号分隔【选填】
        $po_no = isset($where['po_no'])?$where['po_no']:null; 
        //拣货单编号【选填】
        $pick_no = isset($where['pick_no'])?$where['pick_no']:null; 
        //送货仓库 【选填】
        $warehouse = isset($where['warehouse'])?$where['warehouse']:null; 
        //合作模式,JIT分销：jit_4a 普通JIT：jit【选填】
        $co_mode = VopWrapper::CO_MODE;
        //订单类别 normal【选填】
        $order_cate = isset($where['order_cate'])?$where['order_cate']:null;
        //开始创建日期【选填】
        $st_create_time = isset($where['st_create_time'])?$where['st_create_time']:null;
        if(!empty($st_create_time) && strpos($st_create_time,":")===false){
            $st_create_time .= ' 00:00:00';
        }
        //结束创建日期  【选填】
        $et_create_time = isset($where['et_create_time'])?$where['et_create_time']:null; 
        if(!empty($et_create_time) && strpos($et_create_time,":")===false){
            $et_create_time .= ' 23:59:59';
        }
        //开始开售日期 【选填】
        $st_sell_time_from = isset($where['st_sell_time_from'])?$where['st_sell_time_from']:null;
        //结束开售日期  【选填】
        $et_sell_time_from = isset($where['et_sell_time_from'])?$where['et_sell_time_from']:null; 
        //开始停售日期 【已废弃】
        $st_sell_time_to = null; 
        //结束停售日期【已废弃】
        $et_sell_time_to = null;  
        //导出状态
        $is_export = isset($where['is_export'])?$where['is_export']:null;  
        //门店编码
        $store_sn = isset($where['store_sn'])?$where['store_sn']:null;         

	    return $this->_instance->getPickList($vendor_id, $po_no, $pick_no, $warehouse, $co_mode, $order_cate, $st_create_time, $et_create_time, $st_sell_time_from, $et_sell_time_from, $st_sell_time_to, $et_sell_time_to, $is_export, $page, $limit, $store_sn);
	    
	}
	/**
	 * 获取指定拣货单明细信息
	 * @param string $po_no po单号【必填】
	 * @param string $pick_no 拣货单号【必填】
	 * @param number $page
	 * @param number $limit
	 * @return multitype:number string NULL
	 */
	public function getPickDetail($po_no, $pick_no , $page = 1, $limit=50){
	    //供应商ID
	    $vendor_id = VopWrapper::VENDOR_ID;
	    //合作模式,JIT分销：jit_4a 普通JIT：jit
	    $co_mode = VopWrapper::CO_MODE;
        return $this->_instance->getPickDetail($po_no,$vendor_id,$pick_no,$page,$limit,$co_mode);
    
	}
	/**
	 * 创建拣货单
	 * @param string $po_no po单号【必填】
	 * @return unknown
	 */
	public function createPick($po_no){
	    //供应商ID
	    $vendor_id = VopWrapper::VENDOR_ID;
	    //合作模式,JIT分销：jit_4a 普通JIT：jit
	    $co_mode = VopWrapper::CO_MODE;
	    
	    $result = array('success'=>0,'error'=>'','data'=>null);
	    try {	        
	        $result['data'] = $this->_instance->createPick($po_no,$vendor_id,$co_mode);
	        $result['success'] = 1;
	    } catch(\Osp\Exception\OspException $e){
	        $result['error'] = $e->getReturnMessage();
	    }
	    return $result;
	}
	/**
	 * 创建出仓单
	 * @param unknown $params
	 * @return multitype:number string NULL
	 */
	public function createDelivery($params = array(),$page=1,$limit=100){

	   $result = array('success'=>0,'error'=>'','data'=>null);       
	   //必填参数验证
	   $requireFileds = array('po_no','warehouse','delivery_no','arrival_time','carrier_name','carrier_code');
	   $emptyFields = array();
	   foreach ($requireFileds as $p){
	       if(!isset($params[$p])){
	           $emptyFields[] = $p;	           
	       }
	   }
	   if(!empty($emptyFields)){
	       $result['error']= "缺少必填参数：".implode(",",$emptyFields);
	       return $result;
	   }
	   //供应商ID【必填】
	   $vendor_id = VopWrapper::VENDOR_ID;
	   //合作模式【必填】,JIT分销：jit_4a 普通JIT：jit
	   $co_mode = VopWrapper::CO_MODE;
	   //po号【必填】
	   $po_no = isset($params['po_no'])?$params['po_no']:null;
	   //运单号【必填】
	   $delivery_no = isset($params['delivery_no'])?$params['delivery_no']:null;
	   //送货仓库【必填】
	   $warehouse = isset($params['warehouse'])?$params['warehouse']:null;
	   //送货时间
	   $delivery_time = isset($params['delivery_time'])?$params['delivery_time']:date("Y-m-d H:i:s");
	   //要求到货时间【必填】；空运(delivery_method=2)可选的时间段：9:00:00,16:00:00,18:00:00，23:59:00 ；汽运（delivery_method=1)可选的时间段：9:00:00,16:00:00,20:00:00,22:00:00，23:59:00
	   $arrival_time = isset($params['arrival_time'])?$params['arrival_time']:null;
       //预计收货时间
	   $race_time = isset($params['race_time'])?$params['race_time']:null;
	   //承运商名称【必填】
	   $carrier_name = isset($params['carrier_name'])?$params['carrier_name']:null;
	   //联系电话
	   $tel = isset($params['tel'])?$params['tel']:null;
	   //司机姓名
	   $driver= isset($params['driver'])?$params['driver']:null;
	   //司机联系电话
	   $driver_tel= isset($params['driver_tel'])?$params['driver_tel']:null;
	   //车牌号
	   $plate_number= isset($params['plate_number'])?$params['plate_number']:null;
	   //页码
	   //$page= isset($params['page'])?$params['page']:null;
	   //每页记录数(如果超过100,也只返回100条记录)
	   //$limit= isset($params['limit'])?$params['limit']:null;
	   //配送方式  1：汽运,2：空运;默认值：1
	   $delivery_method= isset($params['delivery_method'])?$params['delivery_method']:1;
	   //门店编码
	   $store_sn = isset($params['store_sn'])?$params['store_sn']:null;
	   //承运商编码【必填】
	   $carrier_code = isset($params['carrier_code'])?$params['carrier_code']:null;
	   try {	        
	        $result['data'] = $this->_instance->createDelivery($vendor_id, $po_no, $delivery_no, $warehouse, $delivery_time, $arrival_time, $race_time, $carrier_name, $tel, $driver, $driver_tel, $plate_number, $page, $limit, $delivery_method, $store_sn, $carrier_code);
	        $result['success'] = 1;
	   } catch(\Osp\Exception\OspException $e){
	        $result['error'] = $e->getReturnMessage();
	   }
	   return $result;
	}
	/**
	 * 创建出仓单
	 * @param unknown $params
	 * @return multitype:number string NULL
	 */
	public function editDelivery($params = array(),$page=1,$limit=100){
	
	    $result = array('success'=>0,'error'=>'','data'=>null);
	    //必填参数验证
	    $requireFileds = array('storage_no','warehouse','delivery_time','race_time','carrier_code');
	    $emptyFields = array();
	    foreach ($requireFileds as $p){
	        if(!isset($params[$p])){
	            $emptyFields[] = $p;
	        }
	    }
	    if(!empty($emptyFields)){
	        $result['error']= "缺少必填参数：".implode(",",$emptyFields);
	        return $result;
	    }
	    //供应商ID【必填】
	    $vendor_id = VopWrapper::VENDOR_ID;
	    //合作模式【必填】,JIT分销：jit_4a 普通JIT：jit
	    $co_mode = VopWrapper::CO_MODE;
	    //po号
	    $po_no = isset($params['po_no'])?$params['po_no']:null;
	    //storage_no【必填】	    
	    $storage_no = isset($params['storage_no'])?$params['storage_no']:null;
	    //运单号
	    $delivery_no = isset($params['delivery_no'])?$params['delivery_no']:null;
	    //送货仓库【必填】
	    $warehouse = isset($params['warehouse'])?$params['warehouse']:null;
	    //送货时间
	    $delivery_time = isset($params['delivery_time'])?$params['delivery_time']:date("Y-m-d H:i:s");
	    //要求到货时间；空运(delivery_method=2)可选的时间段：9:00:00,16:00:00,18:00:00，23:59:00 ；汽运（delivery_method=1)可选的时间段：9:00:00,16:00:00,20:00:00,22:00:00，23:59:00
	    $arrival_time = isset($params['arrival_time'])?$params['arrival_time']:null;
	    //预计收货时间【必填】
	    $race_time = isset($params['race_time'])?$params['race_time']:null;
	    //承运商名称
	    $carrier_name = isset($params['carrier_name'])?$params['carrier_name']:null;
	    //联系电话
	    $tel = isset($params['tel'])?$params['tel']:null;
	    //司机姓名
	    $driver= isset($params['driver'])?$params['driver']:null;
	    //司机联系电话
	    $driver_tel= isset($params['driver_tel'])?$params['driver_tel']:null;
	    //车牌号
	    $plate_number= isset($params['plate_number'])?$params['plate_number']:null;
	    //页码
	    //$page= isset($params['page'])?$params['page']:null;
	    //每页记录数(如果超过100,也只返回100条记录)
	    //$limit= isset($params['limit'])?$params['limit']:null;
	    //配送方式  1：汽运,2：空运;默认值：1
	    $delivery_method= isset($params['delivery_method'])?$params['delivery_method']:1;
	    //门店编码
	    $store_sn = isset($params['store_sn'])?$params['store_sn']:null;
	    //承运商编码【必填】
	    $carrier_code = isset($params['carrier_code'])?$params['carrier_code']:null;
	    try {
	        $result['data'] = $this->_instance->editDelivery($vendor_id, $storage_no, $delivery_no, $warehouse, $delivery_time, $arrival_time, $race_time, $carrier_name, $tel, $driver, $driver_tel, $plate_number, $page, $limit, $delivery_method, $store_sn, $carrier_code);
	        $result['success'] = 1;
	    } catch(\Osp\Exception\OspException $e){
	        $result['error'] = $e->getReturnMessage();
	    }
	    return $result;
	}
	/**
	 * 快递方式查询
	 * @param unknown $params
	 * Array ( [success] => 1 [error] => [data] => vipapis\delivery\CreateDeliveryResponse Object ( [delivery_id] => 408892 [storage_no] => 7097094285-0001 ) )
	 */
	public function getCarrierList($params=array(),$page=1,$limit=100){
	    
	    $carrierRequest = new \vipapis\delivery\GetCarrierListRequest($params);
	    $carrierRequest->vendor_id = VopWrapper::VENDOR_ID;
	    $carrierRequest->page = $page;
	    $carrierRequest->limit = $limit;
	    return $this->_instance->getCarrierList($carrierRequest);
	}
	/**
	 * 送货单列表
	 * @param unknown $where
	 * @param number $page
	 * @param number $limit
	 */
	public function getDeliveryList($where,$page=1,$limit=30){
	    //供应商ID【必填】
	    $vendor_id = VopWrapper::VENDOR_ID;
	    //po订单号,支持批量，用英文逗号分隔
	    $po_no = isset($where['po_no'])?$where['po_no']:null;
	    //运单号
	    $delivery_no = isset($where['delivery_no'])?$where['delivery_no']:null;
	    //送货仓库 【选填】
	    $warehouse = isset($where['warehouse'])?$where['warehouse']:null;
	    //送货状态(0=未出仓,1=已出仓)
	    $out_flag = isset($where['out_flag'])?$where['out_flag']:null;
	    //送货时间(开始时间,格式'yyyy-MM-dd HH:mm:ss')
	    $st_out_time = isset($where['st_out_time'])?$where['st_out_time']:null;
	    //送货时间(结束时间,格式'yyyy-MM-dd HH:mm:ss')
	    $et_out_time = isset($where['et_out_time'])?$where['et_out_time']:null;
	    //预计到货时间(开始时间,格式'yyyy-MM-dd HH:mm:ss')
	    $st_estimate_arrive_time = isset($where['st_estimate_arrive_time'])?$where['st_estimate_arrive_time']:null;
	    if(empty($st_estimate_arrive_time)){
	        $st_estimate_arrive_time = '2017-07-01 23:59';
	    }
	    //预计到货时间(结束时间,格式'yyyy-MM-dd HH:mm:ss')
	    $et_estimate_arrive_time = isset($where['et_estimate_arrive_time'])?$where['et_estimate_arrive_time']:null;
	    //实际到货时间(开始时间,格式'yyyy-MM-dd HH:mm:ss')
	    $st_arrive_time = isset($where['st_arrive_time'])?$where['st_arrive_time']:null;
	    //实际到货时间(结束时间,格式'yyyy-MM-dd HH:mm:ss')
	    $et_arrive_time = isset($where['et_arrive_time'])?$where['et_arrive_time']:null;
	    //门店编号
	    $store_sn = isset($where['store_sn'])?$where['store_sn']:null;
	    //入库单号
	    $storage_no = isset($where['storage_no'])?$where['storage_no']:null;
	    return $this->_instance->getDeliveryList($vendor_id, $po_no, $delivery_no, $warehouse, $out_flag, $st_out_time, $et_out_time, $st_estimate_arrive_time, $et_estimate_arrive_time, $st_arrive_time, $et_arrive_time, $page, $limit, $store_sn, $storage_no);
	}
	/**
	 * 将出仓明细信息导入到出仓单中（目前该接口明细信息最大导入量在500条SKU信息）
	 * @param unknown $po_no 必填
	 * @param unknown $storage_no 必填
	 * @param unknown $delivery_list 必填
	 * @param string $store_sn
	 */
	public function importDeliveryDetail($po_no,$storage_no,$delivery_list,$store_sn=null){

	    //供应商ID【必填】
	    $vendor_id = VopWrapper::VENDOR_ID;
	    //运货单号-已废除
	    $delivery_no = null;

	    $result = array('success'=>0,'error'=>'','data'=>null);
	    try {
	        
	        $result['data'] = $this->_instance->importDeliveryDetail($vendor_id, $po_no, $storage_no, $delivery_no, $store_sn, $delivery_list);
	        $result['success'] = 1;
	    } catch(\Osp\Exception\OspException $e){
	        $result['error'] = $e->getReturnMessage();
	    }
	    return $result;
	    
	}
	/**
	 * 查询入库单下的商品列表
	 * @param unknown $storage_no
	 * @param number $page
	 * @param number $limit
	 */
	public function getDeliveryGoods($storage_no, $page=1, $limit=500){
	    $vendor_id = VopWrapper::VENDOR_ID;
	    return $this->_instance->getDeliveryGoods($vendor_id, $storage_no, $page, $limit);
	}
	/**
	 * 确认出货
	 * @param unknown $storage_no
	 * @param string $store_sn
	 * @return Array ( [success] => 1 [error] => [data] => true )
	 */
	public function confirmDelivery($storage_no,$store_sn=null){
	    $result = array('success'=>0,'error'=>'','data'=>null);
	    //供应商ID【必填】
	    $vendor_id = VopWrapper::VENDOR_ID;
	    $po_no = null;//已作废
	    try {	        
	        $result['data'] = $this->_instance->confirmDelivery($vendor_id, $storage_no, $po_no, $store_sn);
	        $result['success'] = 1;
	    } catch(\Osp\Exception\OspException $e){
	        $result['error'] = $e->getReturnMessage();
	    }
	    return $result;
	}
	/**
	 * 删除指定单号的 出库明细
	 * @param unknown $storage_no
	 * @param unknown $po_no 多个用逗号隔开
	 * @return multitype:number string NULL
	 */
	public function deleteDeliveryDetail($storage_no,$po_no){
	    $result = array('success'=>0,'error'=>'','data'=>null);
	    $vendor_id = VopWrapper::VENDOR_ID;
	    try {
	        $result['data'] = $this->_instance->deleteDeliveryDetail($vendor_id, $storage_no, $po_no);
	        $result['success'] = 1;
	    } catch(\Osp\Exception\OspException $e){
	        $result['error'] = $e->getReturnMessage();
	    }
	    return $result;
	}
	/**
	 * 查询物流跟踪信息
	 * @param unknown $storage_no
	 */
	public function getDeliveryTraceInfo($storage_no){
	    $result = array('success'=>0,'error'=>'','data'=>null);
	    $vendor_id = VopWrapper::VENDOR_ID;
	    $params = array('storage_no'=>$storage_no,'vendor_id'=>$vendor_id);
	    $request = new \vipapis\delivery\DeliveryTraceInfoRequest($params);
	    return $this->_instance->getDeliveryTraceInfo($request);	    
	}
	/**
	 * 打印箱子贴纸
	 * @param unknown $pick_no
	 */
	public function getPrintDelivery($storage_no, $po_no=null, $box_no=null){
	    $result = array('success'=>0,'error'=>'','data'=>null);
	    
	    $vendor_id = VopWrapper::VENDOR_ID;
	    try {
	        $result['data'] = (array) $this->_instance->getPrintDelivery($vendor_id, $storage_no, $po_no, $box_no);
	        $result['success'] = 1;
	    } catch(\Osp\Exception\OspException $e){
	        $result['error'] = $e->getReturnMessage();
	    }
	    return $result;
	}
	/**
	 * 取价
	 * @param unknown $po_no
	 * @param unknown $barcodes
	 * @return multitype:number string NULL
	 */
	public function getSkuPriceInfo($po_no,$barcodes){
	    $result = array('success'=>0,'error'=>'','data'=>null);	     
	    $vendor_id = VopWrapper::VENDOR_ID;
	    $params = array(
	        'vendor_id'=>$vendor_id,
	        'po_no'=>$po_no,
	        'barcodes'=>$barcodes	        
	    );
	    $request = new \vipapis\delivery\GetSkuPriceRequest($params);
	    try {
	        $result['data'] =  $this->_instance->getSkuPriceInfo($request);
	        $result['success'] = 1;
	    } catch(\Osp\Exception\OspException $e){
	        $result['error'] = $e->getReturnMessage();
	    }
	    return $result;
	}
	/**
	 * 获取指定拣货单明细信息2.0版本
	 * @param unknown $pick_no
	 * @param number $page
	 * @param number $limit
	 * @return multitype:number string multitype: array NULL
	 */
	public function getMultiPoPickDetail($pick_no,$page=1, $limit=500){
	    $params = array(
	        'pick_no'=>$pick_no,
	        'vendor_id'=>VopWrapper::VENDOR_ID,
	        'co_mode'=>VopWrapper::CO_MODE,
	        'page'=>$page,
	        'limit'=>$limit
	    );
        $getPickDetailRequest = new \vipapis\delivery\GetMultiPoPickDetailRequest($params);
        return $this->_instance->getMultiPoPickDetail($getPickDetailRequest);
	}
	
	/**
	 * 将出仓明细信息导入到出仓单中（目前该接口明细信息最大导入量在500条SKU信息）V2.0
	 * @param unknown $po_no 必填
	 * @param unknown $storage_no 必填
	 * @param unknown $delivery_list 必填
	 * @param string $store_sn
	 */
	public function importMultiPoDeliveryDetail($po_no,$storage_no,$delivery_list,$store_sn=null){
	
	    //供应商ID【必填】
	    $vendor_id = VopWrapper::VENDOR_ID;
	    //运货单号-已废除
	    $delivery_no = null;
	
	    $result = array('success'=>0,'error'=>'','data'=>null);
	    try {
	         
	        $result['data'] = $this->_instance->importMultiPoDeliveryDetail($vendor_id, $po_no, $storage_no, $store_sn, $delivery_list);
	        $result['success'] = 1;
	    } catch(\Osp\Exception\OspException $e){
	        $result['error'] = $e->getReturnMessage();
	    }
	    return $result;
	     
	}
	
	
}