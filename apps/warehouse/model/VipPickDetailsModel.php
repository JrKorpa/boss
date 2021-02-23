<?php
/**
 *  -------------------------------------------------
 *   @file		: BoxGoodsLogModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @date		: 2016-06-26
 *   @update	:
 *  -------------------------------------------------
 */
class VipPickDetailsModel extends Model
{
    protected $deliveryService = null;
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'vip_pick_details';
      	$this->pk='art_no';
		$this->_prefix='';
	    $this->_dataObject = array(
            'art_no'=> 'VIP货号' ,
            'barcode'=> '商品条码' ,
            'pick_no'=> '拣货单编号',
            'stock'=> '商品拣货数量' ,
            'product_name'=>'商品名称' ,
            'size'=>'尺码' ,
            'actual_unit_price'=> '供货价（不含税）；已下架，请从getSkuPriceInfo获取' ,
            'actual_market_price'=> '供货价（含税）；已下架，请从getSkuPriceInfo获取' ,
            'not_delivery_num'=>'未送货数(海淘模式特有信息)',
	        'goods_id'=>'boss现货货号',
	        'order_goods_id'=>'boss订单商品ID',
	        'order_sn'=>'boss订单号',
	        'update_time'=>'最后更新时间',	        
	    );        
		parent::__construct($id,$strConn);
		require_once KELA_PATH.'/vendor/vopsdk/VopWrapper.class.php';		
		require_once KELA_PATH.'/vendor/vopsdk/ApiDeliveryService.class.php';
		$this->deliveryService = new ApiDeliveryService();
	}	
    /**
     * Api拣货单明细分页列表
     * @param unknown $po_no
     * @param unknown $pick_no
     * @param number $page
     * @param number $pageSize
     * @return multitype:number string NULL Ambigous <array, multitype:number NULL >
     */
    public function apiGetPickDetails($po_no, $pick_no,$page=1,$pageSize=30,$loadDb=true){
        if(preg_match("/MULPICK/is", $pick_no)){
	        return $this->apiGetMultiPoPickDetail($pick_no,$page,$pageSize,$loadDb);
	    }
        $result = array('success'=>0,'error'=>'');
        try {            
            $pickDetail = $this->deliveryService->getPickDetail($po_no, $pick_no,$page,$pageSize);
            //拣货单单头$pickDetail
            $pickDetail->pick_no = $pick_no;
            $pickDetail->boss_pick_status = 0;
            $pickDetail->boss_pick_num = 0;
            $pickDetail->delivery_status = 0;
            $pickDetail->address_region = "";
            if($loadDb){ 
                $sql = "select * from vip_pick_list where pick_no='{$pick_no}'";
                $dbRow = $this->db()->getRow($sql);
                if(!empty($dbRow)){
                    $pickDetail->boss_pick_status  = $dbRow['boss_pick_status'];
                    $pickDetail->boss_pick_num  = $dbRow['boss_pick_num'];
                    $pickDetail->delivery_status = $dbRow['delivery_status'];
                }
            }
            //拣货单单身列表 pick_product_list
            $pick_product_list = array();
            foreach ($pickDetail->pick_product_list as $key=>$vo){                
                $vo->boss_notpick_num = $vo->not_delivery_num==0?0:$vo->stock;
                $vo->boss_pick_status = $vo->boss_notpick_num==0?1:0;
                $vo->boss_pick_num = 0;
                $vo->boss_pick_status = 0;

                if($loadDb===true){
                    $sql = "select * from vip_pick_details where barcode='{$vo->barcode}' and pick_no='{$pick_no}'";
                    $dbRow = $this->db()->getRow($sql);
                    if(!empty($dbRow)){
                        $vo->boss_pick_num = $dbRow['boss_pick_num'];
                        $vo->boss_pick_status = $dbRow['boss_pick_status'];
                        
                        if($vo->boss_notpick_num>0){
                           $vo->boss_notpick_num = $vo->boss_notpick_num-$dbRow['boss_pick_num'];
                        }
                    }
                }
                $pick_product_list[$key] = (array)$vo;
            } 
            $pickDetail->pick_product_list = $pick_product_list;
            $data = array(
                'page'=>$page,
                'pageSize'=>$pageSize,
                'recordCount'=>$pickDetail->total,
                'pageCount'=>ceil($pickDetail->total/$pageSize),
                'data'=>(array) $pickDetail,
            );
            $result['success'] = 1;
            $result['data'] = $data;
        } catch(\Osp\Exception\OspException $e){
            $result['success'] = 0;
            $result['error'] = $e->getReturnMessage();
        }
        return $result;
        
    }
    public function apiGetMultiPoPickDetail($pick_no,$page=1,$pageSize=30,$loadDb=true){
        try {
            
	        $pickDetail = $this->deliveryService->getMultiPoPickDetail($pick_no,$page,$pageSize);	        
            //拣货单单头$pickDetail
            $pickDetail->pick_no = $pick_no;
            $pickDetail->boss_pick_status = 0;
            $pickDetail->boss_pick_num = 0;
            $pickDetail->delivery_status = 0;
            $pickDetail->po_no = "";
            $pickDetail->order_cate = "";//订单类别
            $pickDetail->warehouse = "";//送货仓库
            if($loadDb){ 
                $sql = "select * from vip_pick_list where pick_no='{$pick_no}'";
                $dbRow = $this->db()->getRow($sql);
                if(!empty($dbRow)){
                    $pickDetail->boss_pick_status  = $dbRow['boss_pick_status'];
                    $pickDetail->boss_pick_num  = $dbRow['boss_pick_num'];
                    $pickDetail->delivery_status = $dbRow['delivery_status'];
                    $pickDetail->po_no = $dbRow['po_no'];
                    $pickDetail->order_cate = $dbRow['order_cate'];
                    $pickDetail->warehouse = $dbRow['sell_site'];
                }
            }
            //拣货单单身列表 pick_product_list
            $pick_product_list = array();
            foreach ($pickDetail->pick_detail_list as $key=>$vo){  
                $vo->stock  = $vo->pick_num;//总库存
                $vo->boss_notpick_num = $vo->not_delivery_num==0?0:$vo->stock;
                $vo->boss_pick_status = $vo->boss_notpick_num==0?1:0;
                $vo->boss_pick_num = 0;
                $vo->boss_pick_status = 0;
                $vo->art_no = $vo->sn;//货号
                if($loadDb===true){
                    $sql = "select * from vip_pick_details where barcode='{$vo->barcode}' and pick_no='{$pick_no}' and po_no='{$vo->po_no}'";
                    $dbRow = $this->db()->getRow($sql);
                    if(!empty($dbRow)){
                        $vo->boss_pick_num = $dbRow['boss_pick_num'];
                        $vo->boss_pick_status = $dbRow['boss_pick_status'];
                        if($vo->boss_notpick_num>0){
                           $vo->boss_notpick_num = $vo->boss_notpick_num-$dbRow['boss_pick_num'];
                        }
                    }
                }
                $pick_product_list[$key] = (array)$vo;
            } 
            $pickDetail->pick_product_list = $pick_product_list;
            unset($pickDetail->pick_detail_list);
            $data = array(
                'page'=>$page,
                'pageSize'=>$pageSize,
                'recordCount'=>$pickDetail->total,
                'pageCount'=>ceil($pickDetail->total/$pageSize),
                'data'=>(array) $pickDetail,
            );
            $result['success'] = 1;
            $result['data'] = $data;
	    } catch(\Osp\Exception\OspException $e){
	        $result['error'] = $e->getReturnMessage();
	    }
	   // print_r($result);exit;
	    return $result;
    }
    /**
     * 查询价格
     * @param unknown $po_no
     * @param unknown $barcodes
     * @return multitype:number string multitype:array
     */
    public function apiGetSkuPriceInfo($po_no,$barcodes){
        $result = array('success'=>0,'error'=>'');
        $priceResult = $this->deliveryService->getSkuPriceInfo($po_no, $barcodes);
        if($priceResult['success']==1){
            $data = array();
            foreach ($priceResult['data']->price_list as $key=>$vo){
                $data[$key] = (array)$vo;
            }
            $result['data'] = $data;
        }
        return $result;
    }
    /**
     * 查询指定sku拣货单信息
     * @param unknown $sku_no
     * @param unknown $po_no
     * @param unknown $pick_no
     * @param string $loadDb
     * @return multitype:unknown |boolean
     */
    public function apiGetPickDetailsOne($sku_no,$po_no, $pick_no,$loadDb=true){
        $result = $this->apiGetPickDetails($po_no, $pick_no,1,100,$loadDb);
        if(!empty($result['data']['data'])){
            $pickInfo = $result['data']['data'];
            $pickDetails = array();
            foreach ($pickInfo['pick_product_list'] as $vo){
                if(preg_match("/MULPICK/is",$pick_no)){
                    if($sku_no==$vo['barcode'] && $po_no==$vo['po_no']){
                       $pickDetails = $vo;
                       unset($pickInfo['pick_product_list']);
                       break;
                    }
                }else{
                    if($sku_no==$vo['barcode']){
                        $pickDetails = $vo;
                        unset($pickInfo['pick_product_list']);
                        break;
                    }
                }
            }
            $barcode = $pickDetails['barcode'];
            $priceResult = $this->deliveryService->getSkuPriceInfo($po_no, array($barcode));
            if($priceResult['success']==1){
                foreach ($priceResult['data']->price_list as $key=>$price){
                    if($price->barcode==$barcode){
                        $pickDetails['actual_market_price'] = sprintf("%.2f",$price->actual_market_price);
                        $pickDetails['actual_unit_price'] = sprintf("%.2f",$price->actual_unit_price);                    
                    }
                }
            }
            $result['data']= array(
                'pick_info'=>$pickInfo,
                'pick_details'=>$pickDetails                
            );
        }else{
            $result['data'] = array();
        }
        return $result;
    }    
    /**
     * 本地数据库拣货单列表分页
     * @param unknown $where
     * @param unknown $page
     * @param number $pageSize
     * @param string $useCache
     * @return unknown
     */
	function pageList($where,$page,$pageSize=10,$useCache=true){
		$sql = "SELECT * FROM ".$this->table()." where 1=1";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;		
	}
	function getPickDetail($where){
	    $sql = "select * from ".$this->table()." where ".$where;
	    return $this->db()->getRow($sql);
	}
	/**
	 * 绑定订单
	 */
	function bindPickOrder($barcode,$pick_no,$po_no,$order_info,$address_region=''){
	    $result = array('success'=>0,'error'=>'');
	    
	    if(empty($order_info['goods'])){
	        $result['error'] = "订单商品为空！";
	        return $result;
	    }
	    //同步拣货单到本地	   
	    $sql = "select * from vip_pick_details where barcode='{$barcode}' and pick_no='{$pick_no}' and po_no='{$po_no}'";
	    $pickDetail = $this->db()->getRow($sql);
	    if(empty($pickDetail)){
	       Util::httpCurl("/cron/vip/index.php?act=updatepicklist&pick_no={$pick_no}");
	       $result['error'] = "Api同步拣货单到本地出现临时故障,请重新尝试！1"; 
	       return $result;
	    }
	    try{	
	        $time = date("Y-m-d H:i:s");
	        $user = !empty($_SESSION['userName'])?$_SESSION['userName']:'未知';
    	    foreach ($order_info['goods'] as $goods){
    	        //插入拣货单商品与订单商品关联表
    	        $sqlData = array(
    	            'barcode'=>$barcode,//SKU码/条码
    	            'po_no'=>$pickDetail['po_no'],
    	            'pick_no'=>$pick_no,    	            
    	            'warehouse'=>$pickDetail['warehouse'],
    	            'order_detail_id'=>$goods['id'],
    	            'goods_id'=>$goods['goods_id'],
    	            'order_id'=>$goods['order_id'],
    	            'order_sn'=>$goods['order_sn'],
    	            'delivery_status'=>0,//未发货    
    	            'create_time'=>$time,
    	            'create_user'=>$user,          
    	        );
    	        $sql = $this->insertSql($sqlData,"vip_pick_order_details");    	        
    	        $this->db()->query($sql);
    	        //查询拣货单指定sku码已捡货数量
    	        $sql = "select count(*) as boss_pick_total from vip_pick_order_details where pick_no='{$pick_no}' and barcode='{$barcode}' and is_delete=0";
    	        $boss_pick_num = $this->db()->getOne($sql); 
    	        if($boss_pick_num==0){
    	            throw new Exception("拣货订单商品关联数据异常！");
    	        }   
    	        //更新拣货单指定sku码已捡货数量
    	        $sql = "update vip_pick_details set boss_pick_num={$boss_pick_num},boss_pick_status=IF(not_delivery_num<=boss_pick_num,1,0) where barcode='{$barcode}' and pick_no='{$pick_no}' and po_no='{$po_no}'";
    	        $this->db()->query($sql);
    	        break;
    	    }
    	    //查询拣货单已捡货数量
    	    $sql = "select sum(boss_pick_num) as boss_pick_total,sum(stock) as stock_total from vip_pick_details where pick_no='{$pick_no}'";
    	    $pickTongji = $this->db()->getRow($sql);
    	    if(empty($pickTongji['boss_pick_total'])){    	        
    	        throw new Exception("拣货订单商品关联数据异常！");
    	    }
	        $boss_pick_total = $pickTongji['boss_pick_total'];//BOSS已捡货总数
	        $stock_total = $pickTongji['stock_total'];//货品总数
	        if($pickTongji['boss_pick_total'] >= $pickTongji['stock_total']){
	            $boss_pick_status = 1;//已完成
	        }else{
	            $boss_pick_status = 0;//未完成
	        }
	        //更新BOSS拣货单已捡货总数量,捡货状态,
	        $sql = "update vip_pick_list set boss_pick_num={$boss_pick_total},boss_pick_status={$boss_pick_status},address_region='{$address_region}' where pick_no='{$pick_no}'";
	        $this->db()->query($sql);
	        $result['success'] = 1;
	    }catch (Exception $e){
	        $result['error'] = "拣货单绑定订单失败！".$e->getMessage();
	    }	    
	    return $result;
	}
	
	
}