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
class TsydKuanModel extends Model {

    
    
    function __construct($id = NULL, $strConn = "") {
       
    }

    /**
     * 	getList
     *
     * 	@url DiamondListController/search
     */
    function getList() {

        $keys[]='page';
        $vals[]='';

        $ret = ApiModel::style_api($keys, $vals, "getStyleTsydPriceList");
        //var_dump($ret);
       
        return $ret;              
    }
}

?>