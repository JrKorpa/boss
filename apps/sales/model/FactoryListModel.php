<?php

/**
 *  -------------------------------------------------
 *   @file		: DiamondListModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 16:13:09
 *   @update	:
 *  -------------------------------------------------
 */
class FactoryListModel extends Model {

    
    
    function __construct($id = NULL, $strConn = "") {
       
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url DiamondListController/search
     */
    function pageList($where) {
        
        if(isset($where['page'])){
            $keys[]='page';
            $vals[]=$where['page'];
        }
        if(isset($where['pageSize'])){
            $keys[]='pageSize';
            $vals[]=$where['pageSize'];
        }
        if(isset($where['style_sn'])){
            $keys[]='style_sn';
            $vals[]=$where['style_sn'];
        }
        //根据款号查询查询prc_id 
        $ret = ApiModel::style_api($keys, $vals, "getFactoryIdByStyleSn");
        //var_dump($ret);
        if($ret['error'] != 1) {
            $factory_id = $ret['data'][0]['factory_id'];
            $res = ApiModel::processor_api(array("processor_id"), array($factory_id), "GetProcessorId");
           
        }else{
            $res['error'] = 1;
            $res['data']  = '没有查询到信息';
        }
       
        return $res;              
    }

    /**
     * 	getRowById，取一行
     *
     * 	@url DiamondListController/getAllList
     */
    function getRowById($goods_id) {
        $keys=array('goods_id');
        $vals=array($goods_id);
        $ret=ApiModel::diamond_api($keys,$vals,'GetDiamondByiId');
        return $ret; 
    }

    /**
     * 	getRowByGoodSn，取一行
     *
     * 	@url DiamondListController/getRowByGoodSn
     */
    function getRowByGoodSn($goods_sn) {
        $keys=array('goods_sn');
        $vals=array($goods_sn);
        $ret=ApiModel::diamond_api($keys,$vals,'GetDiamondByGoods_sn');
        return $ret; 
    }

    /**
     * 	getAllList，取所有
     *
     * 	@url DiamondListController/getAllList
     */
    function getAllList($select="*") {
        $sql = "SELECT $select FROM `" . $this->table() . "`";
        $data = $this->db()->getAll($sql);
        return $data;
    }

    /**
     * 	deletebycert_id，删除
     *
     * 	@url DiamondListController/deletebycert_id
     */
    function deletebycert_id($cert,$cert_id) {
        $sql = "DELETE FROM `".$this->table()."` WHERE `cert` = '".$cert."' AND `cert_id`='".$cert_id."'";
		return $this->db()->query($sql);
    }

    //取所有形状
    public static function getShapeName()
    {
           $Shape_arr=self::$Shape_arr;
           return $Shape_arr;
    }
}

?>