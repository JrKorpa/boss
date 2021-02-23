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
class ApiProcessorModel extends Model
{

	//添加布产单
    function AddProductInfo($data){
        $keys = array('insert_data','from_type');
        $vals = array($data,2);//2代表订单来源

        $ret=ApiModel::processor_api($keys,$vals,'AddProductInfo');

        return $ret;
    }

	//查布产信息
   function GetProductInfo($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }

        $ret=ApiModel::processor_api($keys,$vals,'GetProductInfo');
        return $ret;
    }

	/**获取加工商列表**/
    function getProcessList($keys=array(), $vals=array())
	{
        $ret=ApiModel::processor_api($keys,$vals,'GetProcessorList');
        return $ret;
    }

	//获取供应商ID,NAME数组列表

	function GetSupplierList($keys=array(), $vals=array())
	{
        $ret=ApiModel::processor_api($keys,$vals,'GetSupplierList');
        return $ret['data']['data'];
    }

	/**获取一个供应商的银行信息**/
    function GetSupplierPay($keys=array(), $vals=array())
	{
        $ret=ApiModel::processor_api($keys,$vals,'GetSupplierPay');
        return $ret['data'];
    }
    
     //根据布产号取布产信息
	function GetGoodsRelInfo($goods_id)
	{
		$ret = ApiModel::processor_api(array('goods_id'),array($goods_id),'GetGoodsRelInfo');
		return $ret;
	}

    //根据供应商名称获取id
    function GetSupplierIdsByName($keys=array(), $vals=array()){
        $ret = ApiModel::processor_api($keys, $vals, 'GetSupplierIdsByName');
        return $ret['data'];
    }
}

?>