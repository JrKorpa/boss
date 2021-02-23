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
class ApiSalepolicyGoodsModel
{
    function getCategoryType(){
        $keys = array();
        $vals = array();
        $ret=ApiModel::style_api($keys,$vals,'getCategoryType');
        return $ret;
    }
 

}
?>