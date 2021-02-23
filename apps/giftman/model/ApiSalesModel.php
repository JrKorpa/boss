<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ApiSalesModel
{

    function getDetailsInfo($where){
        $keys = array('where');
        $vals = array($where);
        $ret=ApiModel::sales_api($keys,$vals,'SearchGoodsZp');
        return $ret;
    }

    function getOrderdownLoad($where){
        $keys = array('where');
        $vals = array($where);
        $ret=ApiModel::sales_api($keys,$vals,'SearchOrderdownLoad');
        return $ret;
    }

}