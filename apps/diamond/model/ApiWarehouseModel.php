<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiOrderModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Zhangyuanyuan <1041522879@qq.com>
 *   @date		: 2015年1月19日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiWarehouseModel
{
    function getWarehouseGoodsInfo($where){
       foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }

        $ret=ApiModel::warehouse_api($keys,$vals,'GetWarehouseGoodsByGoodsid');
        return $ret;
    }
	
    /**
     * 采购接口 获取数据
     * @param type $where
     * @return type
     */
    function getPurchaseGoodsInfo($where){
       foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }

        $ret=ApiModel::purchase_api($keys,$vals,'GetQiban');
        return $ret;
    }
    
    /**
     * 采购接口 更新数据
     * @param type $where
     * @return type
     */
    function updatePurchaseGoodsInfo($where){
       foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }

        $ret=ApiModel::purchase_api($keys,$vals,'SetQiban');
        return $ret;
    }
    
     /*
     * 检查是货号还是款号
     */
    public function CheckStyleSn($goods_sn){
		if(!preg_match ("/^[A-Za-z]/i", $goods_sn)){
			return 1;//纯数字的是货号
		}else{
			return 2;//是款号
		}
	}
    //绑货
    function BindGoodsInfoByGoodsId($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }

        $ret=ApiModel::warehouse_api($keys,$vals,'BindGoodsInfoByGoodsId');
        return $ret;
    }
	//解绑上架货品
    function JiebasjiaGoodsInfoByGoodsId($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }

        $ret=ApiModel::warehouse_api($keys,$vals,'JiebasjiaGoodsInfoByGoodsId');
        return $ret;
    }
 public function GetWarehouseGoodsByGoodsid($where){
     foreach ($where as $k=>$v){
         $keys[$k] = $k;
         $vals[$k] = $v;
     }
     $ret=ApiModel::warehouse_api($keys,$vals,'GetWarehouseGoodsByGoodsid');
     return $ret;
 }

    function SearchGoods($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
        $ret=ApiModel::warehouse_api($keys,$vals,'SearchGoods');
        return $ret;
    }
    
    function get_warehouse_all($type=1,$company_id="") {
        $keys[]='diamond_warehouse';
        $vals[]=$type;
        $keys[]='company_id';
        $vals[]=$company_id;
        $ret=ApiModel::warehouse_api($keys,$vals,'GetWarehouseList');
        
        return $ret;
    }
    
    function get_company_all() {
        $keys = array();
        $vals = array();
        $ret=ApiModel::warehouse_api($keys,$vals,'getCompanyList');
        
        return $ret;
    }
    
    function get_company_name($warehouse_code) {
        $keys = array('code');
        $vals = array($warehouse_code);
        $ret=ApiModel::warehouse_api($keys,$vals,'getCompanyName');
        
        return $ret;
    }

}

?>