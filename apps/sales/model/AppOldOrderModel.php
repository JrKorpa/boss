<?php

/**
 *  -------------------------------------------------
 *   @file		: TsydKuanModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 16:13:09
 *   @update	:
 *  -------------------------------------------------
 */
class AppOldOrderModel extends Model {

    
    
    function __construct($id = NULL, $strConn = "") {
       
    }

    /**
     * 	getList,分页，取订单列表
     *
     * 	@url DiamondListController/search
     */
    function pageList($where,$page,$pagesize) {

		if(isset($page))
		{
			$keys[]='page';
            $vals[]=$page;
		}
        if(isset($pagesize)){
            $keys[]='pagesize';
            $vals[]=$pagesize;
        }
        if(isset($where['order_sn'])){
            $keys[]='order_sn';
            $vals[]=$where['order_sn'];
        }
        if(isset($where['consignee'])){
            $keys[]='consignee';
            $vals[]=$where['consignee'];
        }
        if(isset($where['mobile'])){
            $keys[]='mobile';
            $vals[]=$where['mobile'];
        }       

        $ret = ApiModel::bossgate_api("getOrderList",$keys, $vals);
       
        return $ret;              
    }

    /**
     * 	getList,分页，取订单列表
     *
     * 	@url DiamondListController/search
     */
    function getOrder_goodsByOrder_id($where) {

        if(isset($where['order_id'])){
            $keys[]='order_id';
            $vals[]=$where['order_id'];
        }     

        $ret = ApiModel::bossgate_api("getOrderGoods",$keys, $vals);
       
        return $ret;              
    }

    /**
     * 	取款式图片
     *
     * 	@url getStyleGallerBygoods_sn/search
     */
    function getStyleGallerBygoods_sn($style_sn) {

        if(isset($style_sn)){
            $keys[]='style_sn';
            $vals[]=$style_sn;
        }     

        $ret = ApiModel::style_api($keys, $vals,"GetStyleGalleryByStyle_sn");
       
        return $ret;              
    }
}

?>