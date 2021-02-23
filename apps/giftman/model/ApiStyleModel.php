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

    //查看款式信息
    function getStyleInfo($style_sn){
        return ApiModel::style_api(array('style_sn'), array($style_sn), 'GetStyleInfo');
    }

    function GetStyleXiangKouByWhere($style_sn){
        return ApiModel::style_api(array('style_sn'), array($style_sn), 'GetStyleXiangKouByWhere');
    }
}

?>