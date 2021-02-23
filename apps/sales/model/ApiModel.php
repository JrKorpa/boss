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
	const API_DEBUG=0;

    const SALES_BOSSGATE_URL="http://bossgate.kela.cn/web.php?c=Order&a=";

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
    
    
    //销售政策
	public static function purchase_api($keys,$vals,$method){
        $ret=Util::sendRequest('purchase', $method, $keys, $vals);
		if($ret['error']>0){
			return array('data'=>$ret['error_msg'],'error'=>1);
		}else{
			return array('data'=>$ret['return_msg'],'error'=>0);
		}
    }
    
    //会员信息
	public static function sale_member_api($keys,$vals,$method){
        $ret=Util::sendRequest('bespoke', $method, $keys, $vals);
        //var_dump($ret);

        
		if($ret['error']>0){
			return array('data'=>$ret['error_msg'],'error'=>1);
		}else{
			return array('data'=>$ret['return_msg'],'error'=>0);
		}
    }

    //裸钻搜索
	public static function diamond_api($keys,$vals,$method){
		$ret = Util::sendRequest('diamond', $method, $keys, $vals);
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
        
		if($ret['error'] == 2){
			return array('data'=>$ret['error_msg'],'error'=>2);
		}elseif($ret['error'] == 1){
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

    public static function bossgate_api($method,$keys,$vals){
        //验证密钥
        $token="bossgate";
        $args="";
        foreach($keys as $k=>$v){
            $v=trim($v);
            if(!empty($v)){
                $args[$keys[$k]]=$vals[$k];
            }
        }
        ksort($args);
        $ori_str=json_encode($args);
        $data=array("filter"=>$ori_str,"sign"=>md5($token.$ori_str.$token));
        /*var_dump($data);die;*/

        $ret=Util::httpCurl(self::SALES_BOSSGATE_URL.$method,$data,false,true,30);
        if(self::API_DEBUG){
            var_dump(self::SALES_BOSSGATE_URL.$method,$data,$ret);
        }
        $ret=json_decode($ret,true);
        if($ret['error']>0){
            return array('data'=>$ret['error_msg'],'error'=>1);
        }else{
            return array('data'=>$ret['return_msg'],'error'=>0);
        }
    }

    //赠品管理
    public static function gift_man_api($keys,$vals,$method){
        $ret=Util::sendRequest('giftman', $method, $keys, $vals);
        if($ret['error']>0){
            return array('data'=>$ret['error_msg'],'error'=>1);
        }else{
            return array('data'=>$ret['return_msg'],'error'=>0);
        }
    }
	
	public static function management_api($keys,$vals,$method){
        $ret=Util::sendRequest('management', $method, $keys, $vals);
        if($ret['error']>0){
            return array('data'=>$ret['error_msg'],'error'=>1);
        }else{
            return array('data'=>$ret['return_msg'],'error'=>0);
        }
    }
    
    public static function shipping_api($keys,$vals,$method){
        $ret=Util::sendRequest('shipping', $method, $keys, $vals);
        if($ret['error']>0){
            return array('data'=>$ret['error_msg'],'error'=>1);
        }else{
            return array('data'=>$ret['return_msg'],'error'=>0);
        }
    }
}

?>