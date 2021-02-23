<?php
 /**
 *  -------------------------------------------------
 *   @file		: ApiModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015年1月30日 15:05:20
 *   @update	:
 *  -------------------------------------------------
 */
class ApiModel
{
	function __construct ()
	{
	}

	/*
	*	调用仓库
	*/
	public static function warehouse_api($keys,$vals,$method){
		$ret = Util::sendRequest('warehouse', $method, $keys, $vals);
        // return $ret;
		if($ret['error']>0){
			return array($ret['error_msg']);
		}else{
			return $ret['return_msg'];
		}
    }

}

?>