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
class ApiDiamondModel extends Model {

    function __construct($id = NULL, $strConn = "") {}

    function diaSoldOut($dia_sn)
    {
        $goods_id = array();
        $goods_id[0]['goods_id'] = $dia_sn;
        if(!empty($dia_sn)){
            $keys[] ='goods_id';
            $vals[] =$goods_id;
        }else{
            return false;
        }
        //var_dump($keys,$vals);die;
        $ret = ApiModel::diamond_api($keys, $vals, 'updateDiamondInfo');
        //print_r($ret);die;
        return $ret;
    }
    
    //根据证书号获取裸砖基本信息
    public function getDiamondInfoByCertId($cert_id)
    {
        $keys = array('cert_id');
        $vals = array($cert_id);
        $ret = ApiModel::diamond_api($keys,$vals,'GetDiamondByCert_id');
        return $ret;
    }
    
    public static function getDiamondFromWarehouse($cert_or_goods_id, $company_id) {
    	$ret = ApiModel::warehouse_api(array('cert_id', 'company_id'),array($cert_or_goods_id, $company_id),'GetDiaByCertIdOrGoodsId');   	
    	return $ret;
    }
}

?>