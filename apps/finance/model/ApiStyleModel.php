<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiPurchaseModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Zhangyuanyuan <1041522879@qq.com>
 *   @date		: 2015年1月19日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiStyleModel
{

    function GetStyleAttribute($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }

        $ret=ApiModel::style_api($keys,$vals,'GetStyleAttribute');
        return $ret;
    }
    
    function GetProductTypes() {
        $resp=ApiModel::style_api(array(),array(),'GetProductTypes');
        return $resp['error'] == 0 ? $resp['data'] : array();
    }
    
    function GetCatTypes() {
        $resp=ApiModel::style_api(array(),array(),'GetCatTypes');
        return $resp['error'] == 0 ? $resp['data'] : array();
    }

	//取款式分类列表
	function getCatTypeInfo($k = array(),$v = array())
	{
		$newData = array();
		$ret = ApiModel::style_api($k,$v,'getCatTypeInfo');
		foreach ($ret as $key => $val )
		{
			$level = count(explode('-', $val['abspath']))-1;
			$val['tname'] = str_repeat('&nbsp;&nbsp;', $level-1).$val['name'];
			$newData[] = $val;
		}
		return $newData;
	}
}

?>