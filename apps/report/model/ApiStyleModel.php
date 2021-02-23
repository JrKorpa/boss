<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiProModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	:
 *   @date		: 2015年1月19日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiStyleModel
{
    //根据id取出款式内容
   public function GetStyleInfoBySn($style_sn){
        $keys=array('style_sn');
        $vals=array($style_sn);
        $ret=ApiModel::style_api($keys,$vals,'GetStyleInfo');
   
        return $ret;
    }

    public function GetProductType($product_id){
        $key=array('product_type_id');
        $val=array($product_id);
        $ret=ApiModel::style_api($key,$val,'getProductTypeInfo');
        return $ret;
    }

    public function GetCatType($cat_id){
        $key=array('cat_type_id');
        $val=array($cat_id);
        $ret=ApiModel::style_api($key,$val,'getCatTypeInfo');
        return $ret;
    }

	//取产品线列表
	function getProductTypeInfo($k = array(),$v = array())
	{
		$newData = array();
		$ret = ApiModel::style_api($k,$v,'getProductTypeInfo');
		foreach ($ret as $key => $val )
        {
            $level = count(explode('-', $val['abspath']))-1;
            $val['tname'] = str_repeat('&nbsp;&nbsp;', $level-1).$val['name'];
            $newData[] = $val;
        }//print_r($newData);exit;
		return $newData;
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

        //获取主成色列表
        function getZhuchengseList($material_name ='' , $material_status = 1)
        {
            $key=array('material_name', 'material_status');
            $val=array($material_name, $material_status);
            $ret=ApiModel::style_api($key,$val,'getMaterialInfo');
            return $ret;
        }
        //根据款式获取相对应位置的图片
        function getProductGallery($style_sn,$image_place){
                $ret = ApiModel::style_api(array('style_sn','image_place'),array($style_sn,$image_place),'GetStyleGalleryInfo');
                return $ret;
        }
		function getStyleGalleryList($style_sn) {
		$ret = ApiModel::style_api(['style_sn'], [$style_sn], 'getStyleGalleryList');
		return $ret;
	}

}

?>