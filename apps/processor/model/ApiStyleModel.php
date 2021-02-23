<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiProcessorModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: JUAN
 *   @date		: 2015年1月21日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiStyleModel
{
	//根据款号取款号内容
    function GetStyleInfoBySn($style_sn){

        $keys=array('style_sn');
        $vals=array($style_sn);
        $ret=ApiModel::style_api($keys,$vals,'GetStyleInfo');
        return $ret;
    }
    function getTest(){
        $keys=array('style_sn');
        $vals=array('1111');
        $ret=ApiModel::style_api($keys,$vals,'getTest');
        return $ret;
    }

	//根据款号取款式属性
	function GetStyleAttribute($style_sn)
	{
		$keys=array('style_sn');
        $vals=array($style_sn);
        $ret=ApiModel::style_api($keys,$vals,'GetStyleAttribute');
		if(!isset($ret))
		{
			echo $style_sn.'款式接口信息有误，请联系技术检查。';exit;
			return $ret;
		}
		foreach($ret as $key => $val)
		{
			if($val['attribute_code'] == 'work' && $val['value'] == '是')//支持刻字，显示刻字内容文本框
			{
				$val['attribute_name'] = '刻字内容';
				$val['show_type'] = 1;
				$val['attribute_code'] = 'work_con';
				$ret[$key] = $val;
			}elseif($val['attribute_code'] == 'work' && $val['value'] == '否')//不支持刻字就去掉刻字显示
			{
				unset($ret[$key]);
			}else{  //没有是否刻字属性
				$value = trim($val['value'],',');
				$ret[$key]['value'] = explode(',',$value);
			}
		}

        return $ret;
	}

	//根据id取产品线名称
	function getProductName($id)
	{
		$ret = ApiModel::style_api(array('product_type_id'),array($id),'getProductTypeInfo');
		return $ret[0]['name'];
	}

	//根据id取款式分类名称
	function getCatName($id)
	{
		$ret = ApiModel::style_api(array('cat_type_id'),array($id),'getCatTypeInfo');
		return $ret[0]['name'];
	}

	//根据款式获取相对应位置的图片
	function getProductGallery($style_sn,$image_place){
		$ret = ApiModel::style_api(array('style_sn','image_place'),array($style_sn,$image_place),'GetStyleGalleryInfo');
		return $ret;
	}

	function getFactryInfo($style_sn)
	{
		$ret = ApiModel::style_api(array('style_sn'),array($style_sn),'GetFactryInfo');

		foreach($ret as $key => $val)
		{
			$proModel = new AppProcessorInfoModel($val['factory_id'],13);
			$ret[$key]['factory_name'] = $proModel->getValue('name');
			$ret[$key]['code'] = $proModel->getValue('code');
		}
		return $ret;
	}

	//根据款号取款号内容
	public static function GetStyleXiangKouByWhere($style_sn, $xiangkou){
		$keys=array('style_sn' , 'xiangkou');
		$vals=array($style_sn , $xiangkou);
		$ret=ApiModel::style_api($keys,$vals,'GetStyleXiangKouByWhere');
		return $ret;
	}
	
	function getStyleGalleryList($style_sn) {
		$ret = ApiModel::style_api(['style_sn'], [$style_sn], 'getStyleGalleryList');
		return $ret;
	}
	
	function GetFactoryStyleInfo($factory_id,$style_sn){
	    $keys=array('factory_id','style_sn', 'is_default','is_cancel');
	    $vals=array($factory_id,$style_sn,1,1);
	    $ret = ApiModel::style_api($keys,$vals,'GetFactryInfo');
	    if(!empty($ret)){
	        $proModel = new AppProcessorInfoModel(13);
		    $ret[0]['factory_name'] = $proModel->getProcessorName($factory_id);
	        return $ret[0];
	    }else{
	        return array();
	    }
	}
	
	function GetFactory($factory_id,$style_sn){
	    $keys=array('factory_id','style_sn');
	    $vals=array($factory_id,$style_sn);
	    $ret = ApiModel::style_api($keys,$vals,'GetFactryInfo');
	    if(!empty($ret)){
	        $proModel = new AppProcessorInfoModel(13);
		    $ret[0]['factory_name'] = $proModel->getProcessorName($factory_id);
	        return $ret[0];
	    }else{
	        return array();
	    }
	}
}

?>