<?php
use vipapis\delivery\Delivery;
/**
 *  -------------------------------------------------
 *   @file		: BoxGoodsLogModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @date		: 2016-06-26
 *   @update	:
 *  -------------------------------------------------
 */
class VipPickListModel extends Model
{
    protected $deliveryService = null;
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'vip_pick_list';
      	$this->pk='pick_no';
		$this->_prefix='';
	    $this->_dataObject = array(
	        'pick_no' => '拣货单编号',
	        'po_no' => 'po单编号',
	        'co_mode' => '合作模式 JIT分销：jit_4a 普通JIT：jit',
	        'sell_site' => '送货仓库',
	        'order_cate' => '订单类别',
	        'pick_num' => '拣货数量',
	        'create_time' => '拣货单创建时间',
	        'first_export_time'=>'首次导出时间',
	        'export_num' => '导出次数',
	        'delivery_status' => '送货状态' ,
	        'delivery_num' => '发货数' ,
	        'store_sn' => '门店编码' ,
	        'update_time' => '最后更新时间',
	        'is_make_order'=>'是否制单',
	        'make_order_time'=>'制单时间',
	        'address_region'=>'仓库区域',        
	    );        
		parent::__construct($id,$strConn);
		require_once KELA_PATH.'/vendor/vopsdk/VopWrapper.class.php';		
		require_once KELA_PATH.'/vendor/vopsdk/ApiDeliveryService.class.php';
		$this->deliveryService = new ApiDeliveryService();
	}
	/**
	 * Api拣货单列表分页
	 * @param unknown $where
	 * @param unknown $page
	 * @param unknown $pageSize
	 */
    function apiGetPickList($where,$page=1,$pageSize=30,$loadDb=true){
        $result = array('success'=>0,'error'=>'');
        try {            
            $pickList = $this->deliveryService->getPickList($where,$page,$pageSize);
            $data = array(
                'page'=>$page,
                'pageSize'=>$pageSize,
                'recordCount'=>$pickList->total,
                'pageCount'=>ceil($pickList->total/$pageSize),
            );
            foreach ($pickList->picks as $key=>$vo){ 
               $vo->boss_pick_num = 0;//boss已捡货数量
               $vo->boss_pick_status = 0;//boss拣货状态 0未完成 1已完成
               //$vo->boss_delivery_num = 0;//boss拣货单发货数量
               //$vo->boss_delivery_status = 0;//boss拣货单发货状态 0未发货 1已发货
               if($loadDb===true){
                   $sql = "select * from vip_pick_list where pick_no='{$vo->pick_no}'";
                   $dbRow = $this->db()->getRow($sql);
                   if(!empty($dbRow)){
                       $vo->boss_pick_num = $dbRow['boss_pick_num'];
                       $vo->boss_pick_status = $dbRow['boss_pick_status'];
                       //$vo->boss_delivery_num = $dbRow['boss_delivery_num'];
                       //$vo->boss_delivery_status = $dbRow['boss_delivery_status'];
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
     * 本地数据库拣货单列表分页
     * @param unknown $where
     * @param unknown $page
     * @param number $pageSize
     * @param string $useCache
     * @return unknown
     */
	function pageList($where,$page,$pageSize=10){
        $firstDb = false;//是否先查本地数据库
        $fisrtDbFields = array("boss_pick_status");
        foreach ($fisrtDbFields as $vo){
            if(isset($where[$vo]) && $where[$vo]!==""){
                $firstDb = true;
                break;
            }
        }
        //如果不需要先查本地数据库，则直接调用API
        if($firstDb === false){
            return $this->apiGetPickList($where,$page,$pageSize,true);
        }
        
		$sql = "SELECT * FROM ".$this->table()." where 1=1";
		if(isset($where['boss_pick_status']) && $where['boss_pick_status']!=''){
		    $sql .=" AND boss_pick_status={$where['boss_pick_status']}";
		}
		if(!empty($where['pick_no'])){
		    if(strpos($where['pick_no'],",")!==false){
    		    $pick_no = str_replace(",","','",$where['pick_no']);
    		    $sql .=" AND pick_no in('{$pick_no}')";
		    }else{
		        $sql .=" AND pick_no ='{$where['pick_no']}'";
		    }
		}
		if(!empty($where['po_no'])){
		    if(strpos($where['po_no'],",") !== false){
		        $po_no = str_replace(",","','",$where['po_no']);
		        $sql .= " AND po_no in('{$po_no}')";
		    }else{
		        $sql .=" AND po_no ='{$where['po_no']}'";
		    }
		}
		if(!empty($where['st_create_time'])){
		    if(strpos($where['st_create_time'],":")===false){
		        $where['st_create_time'] .= ' 00:00:00';
		    }	
		    $sql .=" AND create_time >='{$where['st_create_time']}'";
		}
		if(!empty($where['et_create_time'])){
		    if(strpos($where['et_create_time'],":")===false){
		        $where['et_create_time'] .= ' 23:59:59';
		    }
		    $sql .=" AND create_time <='{$where['et_create_time']}'";
		}	
		$sql .=" order by create_time desc";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,false);  
		$pick_no_arr = array_column($data['data'],'pick_no');
		$where['pick_no'] = implode(",", $pick_no_arr);
		if(empty($where['pick_no'])){
		   $where['pick_no'] = "-";	
		}
		$result = $this->apiGetPickList($where,$page,$pageSize,true);
		if($result['success']==1){
		    $result['success'] = 1;
		    $result['data'] = $data;
		}
		return $result;
    }
    //查询拣货单
    public function getPickInfoByPickNo($pick_no){
        $sql ="select * from ".$this->table()." where pick_no='{$pick_no}'";
        return $this->db()->getRow($sql);
    }    

}