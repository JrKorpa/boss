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
		$ret= Util::sendRequest('salepolicy', $method, $keys, $vals);
		if($ret['error']>0){
			return array('data'=>$ret['error_msg'],'error'=>1);
		}else{
			return array('data'=>$ret['return_msg'],'error'=>0);
		}
    }
    
    //会员信息
	public static function sale_member_api($keys,$vals,$method){
		$ret= Util::sendRequest('bespoke', $method, $keys, $vals);
        
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
    
     public static function warehouse_api($keys,$vals,$method){
		$ret=Util::sendRequest('warehouse', $method, $keys, $vals);
        
		if($ret['error']>0){
			return array('data'=>$ret['error_msg'],'error'=>1);
		}else{
			return array('data'=>$ret['return_msg'],'error'=>0);
		}
    }

    //会员管理
    public static function bespoke_api($method,$keys,$vals){
        $ret=Util::sendRequest('bespoke', $method, $keys, $vals);
        if($ret['error']>0){
            return array('data'=>$ret['error_msg'],'error'=>1);
        }else{

            return array('data'=>$ret['return_msg'],'error'=>0);
        }
    }

    public static function finance_api($method,$keys,$vals){
        $ret=Util::sendRequest('finance', $method, $keys, $vals);

        if($ret['error']>0){
            return array('data'=>$ret['error_msg'],'error'=>1);
        }else{

            return array('data'=>$ret['return_msg'],'error'=>0);
        }
    }



}

?>