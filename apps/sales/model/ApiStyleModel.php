<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiPurchaseModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Zhangyuanyuan <1041522879@qq.com>
 *   @date		: 2015年1月19日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiStyleModel
{

    function GetStyleAttribute($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }

        $ret=ApiModel::style_api($keys,$vals,'GetStyleAttribute');
        return $ret;
    }

    //获取款式45°图片
    function GetStyleGallery($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
        $ret=ApiModel::style_api($keys,$vals,'GetStyleGallery');
        return $ret['data'];
    }
    
    //根据获取产品线
    function getProductLine($product_type_id){
        $zujin_arr = array(2,7,13,14);//???去款式库动态获取
        //裸钻
        if($product_type_id == -1){
            return 'lz';
        }
        
        if(in_array($product_type_id, $zujin_arr)){
           return 'zj';//足金
        }else{
           return 'xq';//非裸钻非足金
        }
    }
    
    //查看款式信息
    function getStyleInfo($style_sn){
        return ApiModel::style_api(array('style_sn'), array($style_sn), 'GetStyleInfo');
    }

    function GetStyleXiangKouByWhere($style_sn){
        return ApiModel::style_api(array('style_sn'), array($style_sn), 'GetStyleXiangKouByWhere');
    }
}

?>