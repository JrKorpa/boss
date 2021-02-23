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

	//根据款号取款式属性
	function GetStyleAttribute($style_sn)
	{
		$keys=array('style_sn');
        $vals=array($style_sn);
        $ret=ApiModel::style_api($keys,$vals,'GetStyleAttribute');
		$attr = array();
		foreach($ret as $key => $val)
		{
			if($val['attr_type'] == 2)//采购只用销售属性
			{
				if($val['attribute_code'] == 'nengfoukezi' && $val['value'] == '可刻字')//支持刻字，显示刻字内容文本框
				{
					$val['attribute_name'] = '刻字内容';
					$val['show_type'] = 1;
					$val['attribute_code'] = 'work_con';
					$attr[] = $val;
				}elseif($val['attribute_code'] == 'nengfoukezi' && $val['value'] == '不可刻字')//不支持刻字就去掉刻字显示
				{
					unset($ret[$key]);
				}else{  //没有是否刻字属性
					$value = trim($val['value'],',');
					$val['value'] = explode(',',$value);
					$attr[] = $val;
				}
			}
		}

        return $attr;
	}

	//根据产品线和款式分类取属性
	function GetCatAttribute($product_type_id,$cat_type_id)
	{
		$keys=array('product_type_id','cat_type_id');
        $vals=array($product_type_id,$cat_type_id);
		$ret = ApiModel::style_api($keys,$vals,'GetCatAttribute');
		foreach($ret as $key => $val)
		{
			if($val['attribute_code'] == 'nengfoukezi' && $val['value'] == '可刻字')//支持刻字，显示刻字内容文本框
			{
				$val['attribute_name'] = '刻字内容';
				$val['show_type'] = 1;
				$val['attribute_code'] = 'work_con';
				$ret[$key] = $val;
			}elseif($val['attribute_code'] == 'nengfoukezi' && $val['value'] == '不可刻字')//不支持刻字就去掉刻字显示
			{
				unset($ret[$key]);
			}else{  //没有是否刻字属性
				$value = trim($val['value'],',');
				$ret[$key]['value'] = explode(',',$value);
			}
		}
		return $ret;
	}

	//取产品线列表
	function getProductTypeInfo()
	{
		$ret = ApiModel::style_api(array(),array(),'getProductTypeInfo');
		foreach ($ret as $key => $val )
        {
            $level = count(explode('-', $val['abspath']))-1;
            $val['tname'] = str_repeat('&nbsp;&nbsp;', $level-1).$val['name'];
            $newData[] = $val;
        }//print_r($newData);exit;
		return $newData;
	}

	//取款式分类列表
	function getCatTypeInfo()
	{
		$ret = ApiModel::style_api(array(),array(),'getCatTypeInfo');
		foreach ($ret as $key => $val )
        {
            $level = count(explode('-', $val['abspath']))-1;
            $val['tname'] = str_repeat('&nbsp;&nbsp;', $level-1).$val['name'];
            $newData[] = $val;
        }
		return $newData;
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

	//获取所有款号
	public function getAllStyleSN(){
		//$res = ApiModel::style_api(['All_style'],['style'],'getAllStyleSN');
        $res = ApiModel::style_api(['All_style'],['style'],'getAllStyleSnByCaigou');
		return $res;
	}
	
	//根据款式编号，获取款式图库列表
	public function getStyleGallery($where){
	    $keys = array();
	    $vals = array();
	    foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
	    $res = ApiModel::style_api($keys,$vals,'GetStyleGalleryInfo');
	    return $res;
	}

    function GetStyleAttributeInfo($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }

        $ret=ApiModel::style_api($keys,$vals,'GetStyleAttribute');
        return $ret;
    }

}

?>