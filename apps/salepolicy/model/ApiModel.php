<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Zhangyuanyuan <1041522879@qq.com>
 *   @date		: 2015年1月19日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiModel
{
	function __construct ()
	{
	}

	public static function warehouse_api($keys,$vals,$method){ //jxc商品
        $ret=Util::sendRequest('warehouse', $method, $keys, $vals);
        return $ret;
		if($ret['error']>0){
			return array($ret['error_msg']);
		}else{
			return $ret['return_msg'];
		}
    }

    public static function pro_api($method,$args){
        $ret=Util::sendRequestV2('processor', $method, $args);
        return $ret;
        /*if($ret['error']>0){
            return array($ret['error_msg']);
        }else{
            return $ret['return_msg'];
        }*/
    }

	public static function style_api($keys,$vals,$method){ //style款
        $ret=Util::sendRequest('style', $method, $keys, $vals);
        if($ret['error']>0){
			return array($ret['error_msg']);
		}else{
			return $ret['return_msg'];
		}
    }
    
    
    
	public static function sales_api($keys,$vals,$method){ 
        $ret=Util::sendRequest('sales', $method, $keys, $vals);
        if($ret['error']>0){
			return array('msg'=>$ret['error_msg'],'error'=>1);
		}else{
			return $ret['return_msg'];
		}
    }
}

?>