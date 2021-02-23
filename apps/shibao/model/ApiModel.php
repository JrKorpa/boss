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

	public static function pro_api($method,$args)
	{
		$ret = Util::sendRequestV2('processor',$method, $args);
		return $ret;
    }
	
	public static function fin_api($method,$args)
	{
		$ret = Util::sendRequestV2('finance',$method, $args);
        return $ret;
    }
}

?>