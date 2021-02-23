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
	//订单接口
	public static function sales_api($keys,$vals,$method){
        $ret=Util::sendRequest('sales', $method, $keys, $vals);
        return $ret;
		if($ret['error']>0){
			return array($ret['error_msg']);
		}else{
			return $ret['return_msg'];
		}
    }
    //供应商接口调用
	public static function process_api($keys,$vals,$method){
		$ret=Util::sendRequest('processor', $method, $keys, $vals);
		//var_dump($ret);exit;
		if($ret['error']>0){
			return array($ret['error_msg']);
		}else{
			return $ret['return_msg'];
		}
    }
    //供应商接口调用
	public static function process_insert_api($args,$method)
	{
		$ret=Util::sendRequestV2('processor', $method, $args);
		return $ret;
    }

    //款式库接口调用
    public static function style_api($keys,$vals,$method){
    	$ret=Util::sendRequest('style', $method, $keys, $vals);
    	if($ret['error']>0){
    		return array($ret['error_msg']);
    	}else{
    		return $ret['return_msg'];
    	}
    }


    	//仓库接口
	public static function warehouse_api($keys,$vals,$method){
        $ret=Util::sendRequest('warehouse', $method, $keys, $vals);
		if($ret['error']>0){
			return array($ret['error_msg']);
		}else{
			return $ret['return_msg'];
		}
    }


}?>