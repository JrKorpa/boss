<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiOrderModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Zhangyuanyuan <1041522879@qq.com>
 *   @date		: 2015年1月19日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiWarehouseModel
{
    //获取仓储商品
    function getWarehouseGoodsInfo($where){
       foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }

        $ret=ApiModel::warehouse_api($keys,$vals,'GetWarehouseGoodsByOrderGoodsid');
        return $ret;
    }
    
    //绑定货品
    function BindGoodsInfoByGoodsId($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }

        $ret=ApiModel::warehouse_api($keys,$vals,'BindGoodsInfoByGoodsId');
        return $ret;
    }
    
    //生成销售退货单
    function createReturnGoodsBill($where) {
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }

        $ret=ApiModel::warehouse_api($keys,$vals,'createReturnGoodsBill');
        return $ret;
    }
    
    //审核对应销售退货单
    function OprationBillD($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }

        $ret=ApiModel::warehouse_api($keys,$vals,'OprationBillD');
        return $ret;
    }
    
    
    /**
     * 审核对应销售退货单
     * @param type $where
     * @return type
     */
    function CancelBillS($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }

        $ret=ApiModel::warehouse_api($keys,$vals,'CancelBillS');
        return $ret;
    }
    
    /*
     * 检查是货号还是款号
     */
    public function CheckStyleSn($goods_sn){
		if(!preg_match ("/^[A-Za-z]/i", $goods_sn)){
			return 1;//纯数字的是货号
		}else{
			return 2;//是款号
		}
	}
    
    
    public function getWriteOffCompany($pay_type) {
        $keys['pay_type'] = 'pay_type';
        $vals['pay_type'] = $pay_type;

        $ret=ApiModel::warehouse_api($keys,$vals,'getWriteOffCompany');
        return $ret;
    }
    

}

?>