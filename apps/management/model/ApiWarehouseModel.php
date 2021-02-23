<?php

/**
 *  -------------------------------------------------
 *   @file		: ApiWarehouseModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Zhangyuanyuan <1041522879@qq.com>
 *   @date		: 2015年1月19日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiWarehouseModel {

        public function getList() {
                $ret = ApiModel::warehouse_api(array(), array(), 'GetWarehouseList');
                return $ret;
        }
        
        public function getInfo($id) {
                $ret = ApiModel::warehouse_api(array('id'), array($id), 'GetWarehouseList');
                return $ret;
        }
        
        public function getListByCompanyId($id) {
                $ret = ApiModel::warehouse_api(array('company_id'), array($id), 'GetWarehouseList');
                return $ret;
        }        
        public function stopWarehouseByCompanyId($company_id){
                $ret = ApiModel::warehouse_api(array('company_id'), array($company_id), 'PAISI_Warehouse');
                return $ret;
        }
        
        function getCompanyAll() {
            $keys = array();
            $vals = array();
            $ret=ApiModel::warehouse_api($keys,$vals,'getCompanyList');        
            return $ret;
        }

}

?>