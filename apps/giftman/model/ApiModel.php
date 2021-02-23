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
	public static function style_api($keys,$vals,$method){
        $ret= Util::sendRequest('style', $method, $keys, $vals);
    
        //var_dump($ret);
        //exit;
			if($ret['error']>0){
				return array($ret['error_msg']);
			}else{
				return $ret['return_msg'];
			}
	}
    public static function sales_api($keys,$vals,$method){
        $ret= Util::sendRequest('sales', $method, $keys, $vals);
    
        //var_dump($ret);
        //exit;
        if($ret['error']>0){
            return array($ret['error_msg']);
        }else{
            return $ret['return_msg'];
        }
    }

    public static function salepolicy_api($keys,$vals,$method){
        $ret= Util::sendRequest('salepolicy', $method, $keys, $vals);
    
        if($ret['error']>0){
            return array($ret['error_msg']);
        }else{
            return $ret['return_msg'];
        }
    }

    public static function warehouse_api($keys,$vals,$method){
        $ret= Util::sendRequest('warehouse', $method, $keys, $vals);
    
		//var_dump($ret);
        if($ret['error']>0){
            return array($ret['error_msg']);
        }else{
            return $ret['return_msg'];
        }
    }
	public static function checkOrderStatus($keys,$vals,$method){
        $ret= Util::sendRequest('sales', $method, $keys, $vals);
    
		if($ret['error']==0)
		{
			return true;
		}
		else
		{
			return false;
		}
    }






}

?>