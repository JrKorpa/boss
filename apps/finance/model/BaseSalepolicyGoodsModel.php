<?php

/**
 *  -------------------------------------------------
 *   @file		: BaseSalepolicyGoodsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-03 18:25:10
 *   @update	:
 *  -------------------------------------------------
 */
class BaseSalepolicyGoodsModel extends Model {

   
    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'base_salepolicy_goods';
        $this->pk = 'id';
        $this->_prefix = '';
        $this->_dataObject = array("id" => "自增id",
            "goods_id" => "货号",
            "goods_sn" => "款号",
            "goods_name" => "商品名称",
            "chengbenjia" => "成本价",
            "stone" => "镶口",
            "finger" => "手寸",
            "caizhi" => "材质",
            "yanse" => "颜色",
            "category" => "分类",
            "product_type" => "产品线",
            "isXianhuo" => "现货状态0是期货1是现货",
            "is_sale" => "上架状态，1上架，0下架",
            "type"=>"销售策略类型，1：普通类型；2：打包类型",
            "together_id"=>"打包策略绑定多件商品id",
            "add_time" => "推送数据的时间");
        parent::__construct($id, $strConn);
    }
 

 
   /*
     *  获取商品的单条记录
     *  goods_id
     */
    public function getBaseSaleplicyGoods($goods_id) {
        $s_time = microtime();
        $sql = "SELECT * FROM `base_salepolicy_goods` WHERE  ";

		if(isset($goods_id) && !empty(trim($goods_id))){
			$goods_id = trim($goods_id);
			$sql .="  `goods_id`= '".$goods_id."'";
		}else{
                    return false;
		}
 
	   $row =$this->db()->getRow($sql);
   
        // 记录日志
//		$reponse_time = microtime() - $s_time;
//		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
		  return false;
		}else{
			return $row;
		}
    }

}

?>