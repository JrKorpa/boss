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
	const API_DEBUG = 0;
	const JXC_BASE_URL="http://jxc.kela.cn/jxcapi/";


	function __construct ()
	{
	}

	public static function jxc_api($method,$args){
        //验证密钥
		$token="kela_jxc";
		//$args=array();
		ksort($args);
		$ori_str=json_encode($args);
		$data=array("filter"=>$ori_str,"sign"=>md5($token.$ori_str.$token));
		$ret=Util::httpCurl(self::JXC_BASE_URL.$method,$data,false,true,30);

		if(self::API_DEBUG){
			var_dump(self::JXC_BASE_URL.$method,$data);
		}
		$ret=json_decode($ret,true);
		return $ret;

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

	public static function style_api($keys,$vals,$method){
		$ret=Util::sendRequest('style', $method, $keys, $vals);
		if($ret['error']>0){
			return array($ret['error_msg']);
		}else{
			return $ret['return_msg'];
		}
	}

	public static function fin_api($method,$args){
		$ret=Util::sendRequestV2('finance', $method, $args);
        return $ret;
    }


	public static function sales_api($method,$keys,$vals){
		$ret=Util::sendRequest('sales', $method, $keys, $vals);
		return $ret;
	}

	public static function salepolicy_api($method,$keys,$vals){
		$ret=Util::sendRequest('salepolicy', $method, $keys, $vals);
		return $ret;
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

	public static function shipping_api($keys,$vals,$method){
		$ret=Util::sendRequest('shipping', $method, $keys, $vals);
		if($ret['error']>0){
			return array($ret['error_msg']);
		}else{
			return $ret['return_msg'];
		}
	}
	public static function shibao_api($method,$args){
		$ret=Util::sendRequestV2('shibao', $method, $args);
        return $ret;
    }

     public static function management_api($keys,$vals,$method){
		$ret=Util::sendRequest('management', $method, $keys, $vals);
		if($ret['error']>0){
			return array($ret['error_msg']);
		}else{
			return $ret['return_msg'];
		}
	}
    

}?>