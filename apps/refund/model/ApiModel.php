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

	public static function sales_api($keys,$vals,$method){
		$ret=Util::sendRequest('sales', $method, $keys, $vals);
        if($ret['error']>0){
			return $ret['return_msg'];
		}else{
			return $ret['return_msg'];
		}
    }
    
    //会员信息
    public static function sale_member_api($keys,$vals,$method){
    	$ret=Util::sendRequest('bespoke', $method, $keys, $vals);
    
    	if($ret['error']>0){
    		return array('data'=>$ret['error_msg'],'error'=>1);
    	}else{
    		return array('data'=>$ret['return_msg'],'error'=>0);
    	}
    }

	/**
	 * 供应商接口
	 */
	public static function supplier_api($keys,$vals,$method){
		$ret=Util::sendRequest('processor', $method, $keys, $vals);
		if($ret['error']>0){
			return array($ret['error_msg']);
		}else{
			return $ret['return_msg']['data'];
		}


	}

	public static function bespoke_api($keys,$vals,$method){
        $ret=Util::sendRequest('bespoke', $method, $keys, $vals);
        return $ret;
		if($ret['error']>0){
			return array($ret['error_msg']);
		}else{
			return $ret['return_msg'];
		}
    }
    
    //款式库
	public static function style_api($keys,$vals,$method){
        $ret=Util::sendRequest('style', $method, $keys, $vals);
        
		if($ret['error']>0){
			return array('data'=>$ret['error_msg'],'error'=>1);
		}else{
			return array('data'=>$ret['return_msg'],'error'=>0);
		}
    }
    
        //供应商
	public static function processor_api($keys,$vals,$method){
        $ret=Util::sendRequest('processor', $method, $keys, $vals);
       
		if($ret['error']>0){
			return array('data'=>$ret['error_msg'],'error'=>1);
		}else{
			return array('data'=>$ret['return_msg'],'error'=>0);
		}
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
    
    public static function warehouse_api($keys,$vals,$method){
        $ret=Util::sendRequest('warehouse', $method, $keys, $vals);
       
		if($ret['error']>0){
			return array('data'=>$ret['error_msg'],'error'=>1);
		}else{
			return array('data'=>$ret['return_msg'],'error'=>0);
		}
    }

}

?>