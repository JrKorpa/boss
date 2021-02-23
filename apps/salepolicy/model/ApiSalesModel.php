<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ApiSalesModel
{
    function updateDetailsFields($fields){
        $keys = array('id','update_fields');
        $vals = array($fields['id'],$fields['update_fields']);
        $ret=ApiModel::sales_api($keys,$vals,'updateOrderDetailFieldById');
        return $ret;
    }
    
    
    function getDetailsInfo($id,$select=""){
        $keys = array('goods_id','fields');
        $vals = array($id,$select);
        $ret=ApiModel::sales_api($keys,$vals,'getGoodsSnByGoodsId');
        return $ret;
    }
    
    function GetOrderAccountRow($order_id){
        $keys = array('order_id');
        $vals = array($order_id);
        $ret=ApiModel::sales_api($keys,$vals,'GetOrderAccountRow');
        return $ret;
    }
	
	function GetOrderInfoBySns($order_sn){
        if(!empty($order_sn)){
            $keys[] ='order_sn';
            $vals[] =$order_sn;
        }else{
            return false;
        }
        $ret = ApiModel::sales_api($keys, $vals, 'GetOrderInfoBySns');
        return $ret;
    }
	

}