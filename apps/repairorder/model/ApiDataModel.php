<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiDataModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Zhanglijuan <82739364@qq.com>
 *   @date		: 2015年1月20日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiDataModel
{
	/**获取加工商列表 分页的**/
    function getProcessList($args = array())
	{
        $ret=ApiModel::process_api($args,'GetProcessorList');
        return $ret['return_msg']['data'];
    }


	/**获取加工商列表 不带分页的**/
    function GetSupplierList($args = array('status'=>1))
	{
        $ret=ApiModel::process_api($args,'GetSupplierList');
        return $ret['return_msg']['data'];
    }


	/**获取加工商名称根据id**/
	function GetProcessorName($arr)
	{
        $ret=ApiModel::process_api($arr,'GetProcessorName');
        return $ret['return_msg']['data'];
    }
    /*获取客户姓名by布产号*/
    function getConsignee($args){
        $ret=ApiModel::process_api($args,'getConsigneeBybc_sn');
        return $ret['return_msg'];
    }

    //根据订单号 ， 获取布产单号和订单客户姓名
    function getConsigneeOrder_sn($order_sn){
        $ret=ApiModel::process_api($order_sn,'getConsigneeOrder_sn');
        return $ret;
    }

}

?>