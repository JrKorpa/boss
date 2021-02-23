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
    //款式库接口调用
    public static function style_api($keys,$vals,$method){
    	$ret=Util::sendRequest('style', $method, $keys, $vals);
    	if($ret['error']>0){
    		return array($ret['error_msg']);
    	}else{
    		return $ret['return_msg'];
    	}
    }

	//仓储接口
	public static function warehouse_api($keys,$vals,$method){
		$ret=Util::sendRequest('warehouse', $method, $keys, $vals);
		// return $ret;
		if($ret['error']>0){
			return array($ret['error_msg']);
		}else{
			return $ret['return_msg'];
		}
	}

	// 销售政策接口
	public static function salepolicy_api($keys,$vals,$method){
		$ret=Util::sendRequest('salepolicy', $method, $keys, $vals);
		//var_dump($ret);exit;
		//return $ret;
		if($ret['error']>0){
			return array($ret['error_msg']);
		}else{
			return $ret['return_msg'];
		}
	}
   //采购接口
   public static function purchase_api($keys,$vals,$method){
		$ret=Util::sendRequest('purchase', $method, $keys, $vals);
		if($ret['error']>0){
			return array('data'=>$ret['error_msg'],'error'=>1);
		}else{
			return array('data'=>$ret['return_msg'],'error'=>0);
		}
    }
    
    //裸钻搜索
	public static function diamond_api($keys,$vals,$method){
        $ret=Util::sendRequest('diamond', $method, $keys, $vals);
		if($ret['error']>0){
			return array('data'=>$ret['error_msg'],'error'=>1);
		}else{
			return array('data'=>$ret['return_msg'],'error'=>0);
		}
    }
   
}

?>