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
    const SALES_BOSSGATE_BESPOKE_URL="http://bossgate.kela.cn/web.php?c=Bespoke&a=";

	function __construct ()
	{
	}
    
    //历史预约
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

        $ret=Util::httpCurl(self::SALES_BOSSGATE_BESPOKE_URL.$method,$data,false,true,30);
        if(self::API_DEBUG){
            var_dump(self::SALES_BOSSGATE_BESPOKE_URL.$method,$data,$ret);
        }
        $ret=json_decode($ret,true);
        if($ret['error']>0){
            return array('data'=>$ret['error_msg'],'error'=>1);
        }else{
            return array('data'=>$ret['return_msg'],'error'=>0);
        }
    }
}

?>