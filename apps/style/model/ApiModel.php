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

    //销售政策
	public static function sale_policy_api($keys,$vals,$method){
        $ret=Util::sendRequest('salepolicy', $method, $keys, $vals);
		if($ret['error']>0){
			return array('data'=>$ret['error_msg'],'error'=>1);
		}else{
			return array('data'=>$ret['return_msg'],'error'=>0);
		}
    }
   
    public static function processor_api($keys,$vals,$method){
        $ret=Util::sendRequest('processor', $method, $keys, $vals);
		if($ret['error']>0){
			return array($ret['error_msg']);
		}else{
			return $ret['return_msg'];
		}
    }


    public static function warehouse_api($keys,$vals,$method){
       $ret=Util::sendRequest('warehouse', $method, $keys, $vals);

        if($ret['error']>0){
            return array('data'=>$ret['error_msg'],'error'=>1);
        }else{
            return array('data'=>$ret['return_msg'],'error'=>0);
        }
    }
    
    public static function giftman_api($keys,$vals,$method){
    	$ret=Util::sendRequest('giftman', $method, $keys, $vals);
    	if($ret['error']>0){
    		return array('data'=>$ret['error_msg'],'error'=>1);
    	}else{
    		return array('data'=>$ret['return_msg'],'error'=>0);
    	}
    }
    
    
    

}

?>