<?php
use vipapis\delivery\Delivery;
/**
 *  -------------------------------------------------
 *   @file		: VipDeliveryModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @date		: 2016-06-26
 *   @update	:
 *  -------------------------------------------------
 */
class VipDeliveryModel extends Model
{
    protected $deliveryService = null;
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'vip_delivery';
      	$this->pk='pick_no';
		$this->_prefix='';
	    $this->_dataObject = array(
	               
	    );        
		parent::__construct($id,$strConn);
		require_once KELA_PATH.'/vendor/vopsdk/VopWrapper.class.php';		
		require_once KELA_PATH.'/vendor/vopsdk/ApiDeliveryService.class.php';
		$this->deliveryService = new ApiDeliveryService();
	}
	
   /**
     * Api创建出仓单
     * @param unknown $params
     */
    public function apiCreateDelivery($params,$page=1,$pageSize=30){ 
        if(!empty($params['warehouse'])){
           $params['warehouse'] = VipDeliveryView::getWarehouseValue($params['warehouse']);
        }
        $result = $this->deliveryService->createDelivery($params,$page=1,$pageSize=30);  
        $result['data'] = (array)$result['data'];        
        return $result;    
    }
    /**
     * Api修改出仓单
     * @param unknown $params
     */
    public function apiEditDelivery($params,$page=1,$pageSize=30){
        if(!empty($params['warehouse'])){
            $params['warehouse'] = VipDeliveryView::getWarehouseValue($params['warehouse']);
        }      
        $result = $this->deliveryService->editDelivery($params,$page=1,$pageSize=30);
        $result['data'] = (array)$result['data'];
        return $result;
    }
    /**
     * 查询出仓单
     * @param string $storage_no
     */
    public function getDeliveryByNo($storage_no){
        $sql = "select * from ".$this->table() ." where storage_no='{$storage_no}'";
        return $this->db()->getRow($sql);
    }
    
    /**
     * 本地创建出仓单
     */
    public function createDelivery($data){
        $result = array('success'=>0,'error'=>'');
        try{
            if(empty($data['storage_no'])){
                throw  new Exception("storage_no不能为空！");
            }
            $storage_no = $data['storage_no'];
            $sql = "select count(*) from vip_delivery where storage_no='{$storage_no}'";
            $count = $this->db()->getOne($sql);
            
            $time = date("Y-m-d H:i:s");
            $user = isset($_SESSION['userName'])?$_SESSION['userName']:'未知';
            if($count==0){
                $data['create_time'] = $time;
                $data['create_user'] = $user;
                $sql = $this->insertSqlNew($data,"vip_delivery");
            }else{
                $sql = $this->updateSqlNew($data,"vip_delivery","storage_no='{$storage_no}'");
            }
            $this->db()->query($sql);           
            $result['success'] = 1;
        }catch (Exception $e){
            $result['success'] = 0;
            $result['error'] = "本地创建出仓单失败！".$e->getMessage();
        }
        return $result;
    }
    /**
     * 本地数据库 更新出仓商品数量  
     *
     * */
    public function updateDeliveryNum($storage_no){
        $result = array("success"=>0,'error'=>'');
        try{
            $sql = "select count(*) from vip_delivery_details where storage_no='{$storage_no}'";
            $deliveryNum = $this->db()->getOne($sql);

            $sql = "update vip_delivery set delivery_num={$deliveryNum} where storage_no='{$storage_no}'";
            $this->db()->query($sql);
            $result['success'] = 1;
        }catch (Exception $e){
            $result['error'] = "更新出仓货品数量失败!".$e->getMessage();
        }
        return $result;
    }
    
    /**
     * 本地数据库创建出仓商品明细
     * 
     * */
    public function createDeliveryDetails($data){
        $result = array('success'=>0,'error'=>'');
        try{
            if(empty($data['order_detail_id'])){
                throw  new Exception("order_detail_id不能为空！");
            }            
            $order_detail_id = $data['order_detail_id'];
            $sql = "select count(*) from vip_delivery_details where order_detail_id={$order_detail_id}";
            $count = $this->db()->getOne($sql);
            
            if($count==0){
                $sql = $this->insertSqlNew($data,"vip_delivery_details");                
            }else{
                $sql = $this->updateSqlNew($data,"vip_delivery_details","order_detail_id={$order_detail_id}");
            }
            
            $this->db()->query($sql);
            
            $result['success'] = 1;
        }catch (Exception $e){
            $result['success'] = 0;
            $result['error'] = "本地创建出仓单明细失败！{$sql}".$e->getMessage();
        }
        return $result;
    }
    public function deleteDeliveryDetails($storage_no){
        return $this->db()->query("delete from vip_delivery_details where storage_no='{$storage_no}'");
    }
    public function apiGetDeliveryList($where,$page=1,$pageSize=30,$loadDb=true){
       $result = array('success'=>0,'error'=>'','data'=>'');
       try{
          $deliveryList = $this->deliveryService->getDeliveryList($where,$page,$pageSize);
          $data = array(
              'page'=>$page,
              'pageSize'=>$pageSize,
              'recordCount'=>$deliveryList->total,
              'pageCount'=>ceil($deliveryList->total/$pageSize),
              'data' =>array()
          );          
          foreach ($deliveryList->delivery_list as $key=>$vo){
              $vo->delivery_num = 0;
              $vo->carrier_name = '';
              $vo->carrier_code = 0;
              $vo->delivery_method =0;
              $vo->out_user ="";
              if($loadDb){
                  $sql = "select * from vip_delivery where storage_no='{$vo->storage_no}'";
                  $dbRow = $this->db()->getRow($sql);
                  if(!empty($dbRow)){
                      $vo->delivery_num  = $dbRow['delivery_num'];
                      $vo->carrier_name = $dbRow['carrier_name'];
                      $vo->carrier_code = $dbRow['carrier_code'];
                      $vo->delivery_method =$dbRow['delivery_method'];
                      $vo->out_user =$dbRow['out_user'];
                  }
              }
              //$deliveryList->delivery_list[$key] = (array)$vo;
              $data['data'][$key] = (array)$vo;
          }
          
          $result['success'] = 1;
          $result['data'] = $data;
       } catch(\Osp\Exception\OspException $e){           
          $result['error'] = $e->getReturnMessage();
       }
       return $result;
        
    }
    /**
     * 
     * @param unknown $po_no
     * @param unknown $storage_no
     * @param unknown $delivery_list 数组格式
     * @param string $store_sn
     */
    public function apiImportDeliveryDetail($po_no,$storage_no,$delivery_list,$store_sn=null){
        //将出库商品信息转换成对象列表格式
        $_delivery_list = array();
        foreach ($delivery_list as $delivery){
            $_delivery_list[] = new Delivery($delivery);
        }
        $result = $this->deliveryService->importDeliveryDetail($po_no,$storage_no,$_delivery_list,$store_sn);
        return $result;
    
    }
    public function apiImportMultiPoDeliveryDetail($po_no,$storage_no,$delivery_list,$store_sn=null){
        //将出库商品信息转换成对象列表格式
        $_delivery_list = array();
        foreach ($delivery_list as $delivery){
            $_delivery_list[] = new Delivery($delivery);
        }
        $result = $this->deliveryService->importMultiPoDeliveryDetail($po_no,$storage_no,$_delivery_list,$store_sn);
        return $result;
    
    }
    
    public function apiGetDeliveryGoods($storage_no, $page=1, $pageSize=500,$loadDb=false){
        $result = array('success'=>0,'error'=>'','data'=>'');
        try{
            $deliveryGoodsList = $this->deliveryService->getDeliveryGoods($storage_no, $page,$pageSize);
            $data = array(
                'page'=>$page,
                'pageSize'=>$pageSize,
                'recordCount'=>$deliveryGoodsList->total,
                'pageCount'=>ceil($deliveryGoodsList->total/$pageSize),
                'data' =>array()
            );
            foreach ($deliveryGoodsList->delivery_goods_list as $key=>$vo){
                $vo->size = "";
                $vo->art_id = '';                
                if($loadDb){
                    $sql = "select * from vip_pick_details where pick_no='{$vo->pick_no}'";
                    $dbRow = $this->db()->getRow($sql);
                    if(!empty($dbRow)){
                        $vo->delivery_num  = $dbRow['delivery_num'];
                        $vo->carrier_name = $dbRow['carrier_name'];
                        $vo->carrier_code = $dbRow['carrier_code'];
                        $vo->delivery_method =$dbRow['delivery_method'];
                        $vo->out_user =$dbRow['out_user'];
                    }
                }
                $data['data'][$key] = (array)$vo;
            }
        
            $result['success'] = 1;
            $result['data'] = $data;
        } catch(\Osp\Exception\OspException $e){
            $result['error'] = $e->getReturnMessage();
        }
        return $result;
    }
    /**
     * 出仓单确认
     * @param unknown $storage_no
     * @param string $store_sn
     * @return Ambigous <multitype:, multitype:number string NULL >
     */
    public function apiConfirmDelivery($storage_no,$store_sn=null){
        return $this->deliveryService->confirmDelivery($storage_no,$store_sn);
    }
    /**
     * 出仓确认
     * @param unknown $storage_no
     * @param string $store_sn
     * @param bool $transMode 是否内置事物
     */
    public function confirmDelivery($storage_no,$store_sn=null,$transMode = true){
        $result = array('success'=>0,'error'=>'');
        try{
            $pdo = $this->db()->db();
            if($transMode==true){
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
                $pdo->beginTransaction();//开启事务
            }
            $sql = "select * from vip_delivery where storage_no='{$storage_no}'";
            $deliveryInfo = $this->db()->getRow($sql);
            if(empty($deliveryInfo)){
                throw new Exception("出仓单还未打包货品！");
            }else if($deliveryInfo['out_flag']==1){
                throw new Exception("不允许操作：出仓单已出仓！");
            }
            $time = date("Y-m-d H:i:s");
            $user = isset($_SESSION['userName'])?$_SESSION['userName']:'未知';
            $delivery_no = $deliveryInfo['delivery_no'];
            $sql = "update vip_delivery set out_flag=1,out_time='{$time}',out_user='{$user}' where storage_no='{$storage_no}'";
            $this->db()->query($sql);
            $sql = "update vip_pick_order_details a inner join vip_delivery_details b on a.order_detail_id=b.order_detail_id set a.delivery_status=1,a.storage_no=b.storage_no where b.storage_no='{$storage_no}'";
            $this->db()->query($sql);
            
            $apiResult = $this->deliveryService->confirmDelivery($storage_no,$store_sn);
            if($apiResult['success']==0){
                throw new Exception($apiResult['error']);
            }
            if($transMode==true){
                $pdo->commit();//如果没有异常，就提交事务
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            }
            $result['success'] = 1;
        }catch (Exception $e){
            if($transMode==true){
                $pdo->rollback();//事务回滚
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            }
            $result['error'] = $e->getMessage();
            $result['success'] = 0;
        }
        return $result;
    }
    /**
     * 删除指定入库单号的出仓明细
     * @param unknown $storage_no
     * @param unknown $po_no
     * @return Ambigous <multitype:number, multitype:number string NULL >
     */
    public function apiDeleteDeliveryDetail($storage_no,$po_no=null){
        return $this->deliveryService->deleteDeliveryDetail($storage_no,$po_no);
    }
    public function pageList($where,$page,$pageSize=10){
        //预计到货时间
        if(!empty($where['st_estimate_arrive_time'])){
            $where['st_estimate_arrive_time'] .= " 00:00:00"; 
        }
        if(!empty($where['et_estimate_arrive_time'])){
            $where['et_estimate_arrive_time'] .= " 23:59:59";
        }
        //实际到货时间
        if(!empty($where['st_arrive_time'])){
            $where['st_arrive_time'] .= " 00:00:00";
        }
        if(!empty($where['et_arrive_time'])){
            $where['et_arrive_time'] .= " 23:59:59";
        }
        //发货时间
        if(!empty($where['st_out_time'])){
            $where['st_out_time'] .= " 00:00:00";
        }
        if(!empty($where['et_out_time'])){
            $where['et_out_time'] .= " 23:59:59";
        }

        $firstDb = false;//是否先查本地数据库
        $fisrtDbFields = array("boss_pick_status");
        foreach ($fisrtDbFields as $vo){
            if(isset($where[$vo]) && $where[$vo]!==""){
                $firstDb = true;
                break;
            }
        }       
        //如果不需要先查本地数据库，否则直接调用API
        if($firstDb === false){
            return $this->apiGetDeliveryList($where,$page,$pageSize);
        }          
        
    }
    /**
     * 承运商列表
     * @param unknown $where
     * @return Ambigous <multitype:, array>
     */
    public function apiGetCarrierList($where=array()){
       $data = array();
       try{
          $carrierList = $this->deliveryService->getCarrierList($where,1,999);         
          foreach ($carrierList->carriers as $key=>$vo){
              $data[$key] = (array)$vo;
          }
       } catch(\Osp\Exception\OspException $e){           
          $data = array();
       }
       return $data;
    }
    /**
     * 查询物流跟踪信息
     * @param unknown $storage_no
     */
    public function apiGetDeliveryTraceInfo($storage_no){
        $result = array('success'=>0,'error'=>'','data'=>array());
        try{
            $carrierList = $this->deliveryService->getDeliveryTraceInfo($storage_no);
            $delivery_trace_info = $carrierList->delivery_trace_infoes[0];
            foreach($delivery_trace_info->traces as $key=>$vo){
                $delivery_trace_info->traces[$key] = (array) $vo;                 
            } 
            krsort($delivery_trace_info->traces);
            $result['data'] = (array)$delivery_trace_info;            
            $result['success']=1;
        } catch(\Osp\Exception\OspException $e){
            $result['error'] = $e->getReturnMessage();
        }
        return $result;
    }
    /**
     * 查询指定出仓单明细 （分页列表）
     */
    public function getDeliveryGoodsList($storage_no,$page,$pageSize=30,$useCache=true){
        //预计到货时间
        $sql = "select a.*,b.art_no,b.product_name,b.size from vip_delivery_details a left join vip_pick_details b on a.barcode=b.barcode and a.pick_no=b.pick_no and a.po_no=b.po_no where a.storage_no='{$storage_no}'";
        $data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
        return $data;        
    }
    /**
     * 打印箱子贴纸
     * @param unknown $pick_no
     */
    public function apiGetPrintDelivery($storage_no,$po_no=null,$box=null){
        //预计到货时间
         return $this->deliveryService->getPrintDelivery($storage_no,$po_no,$box);
    }
    

}