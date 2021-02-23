<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiProModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: JUAN<zhanglijuan@kela.cn>
 *   @date		: 2015年2月2日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiFinanceModel
{
	/** 添加财务应付信息 **/
    function AddAppPayDetail($arr)
	{
        $ret=ApiModel::fin_api('AddAppPayDetail',array('insert_data' => $arr));
        return $ret['error'];
    }

}

?>