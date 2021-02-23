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
class ApiSalePolicyModel
{
    function AddAppPayDetail($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
        
        $ret=ApiModel::sale_policy_api($keys,$vals,'AddAppPayDetail');
       // var_dump($ret,99);exit;
        return $ret;
    }

    public static function UpdateSalepolicyChengben($val){
        $key = array('goods_id','chengben');
        return ApiModel::sale_policy_api($key,$val,'changeCostPrice');
    }
    
	 function UpdateSalepolicygoodIsSale($where){
    foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }		
    	return ApiModel::sale_policy_api($keys,$vals,'UpdateSalepolicyGoodsIsSale');
    }

}

?>