<?php
/**
 *  -------------------------------------------------
 *  销售政策接口文件
 *   @file		    : ApiWarehouseMode.php
 *   @link		    :  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	    :
 *   @date		    : 2015年4月13日
 *   @update	    :
 *  -------------------------------------------------
 */
class ApiSalepolicyModel
{

    /**
     * 销售政策商品下架
     */
    public static function EditIsSaleStatus($goods_id,$is_sale,$is_valid){
        $keys=array('goods_id','is_sale','is_valid');
        $vals=array($goods_id,$is_sale,$is_valid);
        $ret=ApiModel::salepolicy_api($keys,$vals,'EditIsSaleStatus');
        return $ret;
    }


}

?>