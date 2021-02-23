<?php

/**
 *  销售策略模型Model
 *  -------------------------------------------------
 *   @file		: BaseSalepolicyGoodsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: gaopeng
 *   @date		: 2015-08-27
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
    /**
     * 通过自定义where条件修改当前Model表数据任意字段
     * @param unknown $data 字段值
     * @param unknown $where where条件（拼接字符串）
     * 使用案例：
     * $data = array("field1"=>'XXXX',"field2"=>'xxxx',....);
     * $where = "pk1='XXXXX' and pk2='XXXXX' and ...."
     * $model->update($data,$where);
     */
    public function update($data,$where){
    	return true;
        //过滤主键id值
        if($this->pk() && isset($data[$this->pk()])){
            unset($data[$this->pk()]);
        }
        //通过系统底层函数拼接sql，然后替换掉死板的where条件
        $sql = $this->updateSql($data);
        if(preg_match('/ WHERE /is',$sql)){
            $sql = preg_replace('/ WHERE .*/is',' WHERE '.$where, $sql);
            return $this->db()->query($sql);
        }else{
            return false;
        }
    }
    /**
     * 批量生成销售政策商品
     * @param unknown $data
     * @return boolean 
     */
    public function createSalepolicyGoods($data){
        return true;
        $return = true;        
        foreach ($data as $row){
            if(empty($row['goods_id'])){
                $return = false;
                break;
            }
            $_sql = "select `id`,`is_sale`,`is_valid` from ".$this->table()." where `goods_id` = '{$row['goods_id']}'";
            $info = $this->db()->getRow($_sql);
            if($info){
                //如果已经下架并且此货已经销售是不更改此货的状态的
                if($info['is_sale'] ==0 && $info['is_valid']==2){
                    unset($row['is_sale']);
                }        
                $where = " `goods_id` = '{$row['goods_id']}'";
                $res = $this->update($row,$where); 
                if($res === false){
                    $return = false;
                }                                                
            }else{                
                $sql = $this->insertSql($row,$this->table());
                $res = $this->db()->query($sql);
                if($res === false){
                    $return = false;
                }
            }       
            
        } 
        return $return;
    }
    


}

?>