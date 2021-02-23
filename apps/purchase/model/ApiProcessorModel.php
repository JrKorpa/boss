<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiProcessorModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Zhanglijuan <82739364@qq.com>
 *   @date		: 2015年1月20日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiProcessorModel
{
	/**获取加工商列表**/
	//默认取有效的供应商，如果要取全部的，传入空的。
	function GetSupplierList($keys=array('status'), $vals=array(1))
	{
		$ret = ApiModel::process_api($keys,$vals,'GetSupplierList');
		return $ret['data'];
	}


	/**获取加工商名称根据id**/
	function GetProcessorName($id)
	{
                $ret=ApiModel::process_api(array('id','flag'),array($id,TRUE),'GetProcessorName');
        return $ret['data'];
    }
	/***将采购需要布产的产品推送到布产列表**/
	function AddProductInfo($arr)
	{
        $ret=ApiModel::process_insert_api(array('insert_data'=>$arr,'from_type'=>1),"AddProductInfo");
		return $ret;
    }

	//根据布产号取布产信息
	function GetProductInfo($bc_sn)
	{
		$ret = ApiModel::process_api(array('bc_sn'),array($bc_sn),'GetProductInfo');
		return $ret;
	}
	//根据布产号取布产信息和详细属性
	function GetProductInfoDatail($bc_sn)
	{
		$ret = ApiModel::process_api(array('bc_sn'),array($bc_sn),'GetProductInfoDatail');
		return $ret;
	}

	//根据布产号取log日志
	function getBcLog($bc_sn)
	{
		$ret = ApiModel::process_api(array('bc_sn'),array($bc_sn),'getBcLog');
		return $ret;
	}

	//通过采购单号及款号,获得布产状态
	public function get_bc_status($cg_sn,$style_sn,$p_id){
		$key = ['cg_sn','style_sn','p_id'];
		$val = [$cg_sn,$style_sn,$p_id];
		$ret = ApiModel::process_api($key,$val,'get_bc_status');

		return (is_array($ret))?0:$ret;
	}

	public function UpdatePurGoodsAttr($upData,$base,$label){
		$key = ['update_info','base','label'];
		$val = [$upData,$base,$label];
		$ret = ApiModel::process_api($key,$val,'UpdatePurGoodsAttr');
		return $ret;
	}
	
	function getStyleGalleryList($style_sn) {
		$ret = ApiModel::style_api(['style_sn'], [$style_sn], 'getStyleGalleryList');
		return $ret;
	}
	
	function getStyleAndFactories($style_sn) {
		$ret = ApiModel::style_api(['style_sn'], [$style_sn], 'getStyleAndFactories');
		return $ret;
	}
	
	function getValidStyleFactoryList($factory_id, $factory_sn) {
		$ret = ApiModel::style_api(['factory_id', 'factory_sn'], [$factory_id, $factory_sn], 'getValidStyleFactoryList');
		return $ret;
	}
        function GetOpraName($arr1,$arr2)
	{
		$ret = ApiModel::process_api($arr1,$arr2,'GetProductInfo');
		return $ret;
	}
        function GetAttrByStylesn($key,$val) {
                $ret = ApiModel::process_api($key, $val, 'GetProductAttr');
                return $ret;
        }
}

?>