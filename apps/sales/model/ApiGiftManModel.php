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
class ApiGiftManModel
{

    function GetGiftManList($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
        $ret=ApiModel::gift_man_api($keys,$vals,'GetGiftManList');
        return $ret;
    }
    function GetGiftByUsefullSn($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
        $ret=ApiModel::gift_man_api($keys,$vals,'GetGiftByUsefullSn');
        return $ret;
    }

}
?>
