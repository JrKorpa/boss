<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiProModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	:
 *   @date		: 2015年1月19日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiSalepolicyModel
{

    //推送商品到销售政策
    public static function AddAppPayDetail($insert_data){
        $keys=array('insert_data');
        $vals=array($insert_data);
        $ret=ApiModel::salepolicy_api('AddAppPayDetail',$keys,$vals);
        return $ret;
    }

        /**
        * 更改可销售商品上下架状态
        * @param $goods_ids Array 新的单据明细
        * @param $del_goods Array 编辑时，旧的单据明细
        */
	public function EditIsSaleStatus($goods_ids, $is_sale, $is_valid, $del_goods = array())
	{
		//goods_id 要下架的货号数组
		//is_sale 上下架状态：1=上架 0=下架
		$keys=array('goods_id','is_sale', 'is_valid', 'del_goods');
        $vals=array($goods_ids,$is_sale, $is_valid, $del_goods);
        $ret=ApiModel::salepolicy_api('EditIsSaleStatus',$keys,$vals);
        return $ret['return_msg'];
	}

    public function setGoodsUnsell($change,$where){

        if(count($change) != count($where)){
            return false;
        }
        $keys=array('change','where');
        $vals=array($change,$where);
        $ret=ApiModel::salepolicy_api('setGoodsUnsell',$keys,$vals);
        return $ret['return_msg'];
    }
    //备用
    public function setGoodsUnsell_t($change,$where){
    
    	if(count($change) != count($where)){
    		return false;
    	}
    	$keys=array('change','where');
    	$vals=array($change,$where);
    	$ret=ApiModel::salepolicy_api('setGoodsUnsell_t',$keys,$vals);
    	return $ret['return_msg'];
    }
    
    //根据商品货号修改商品
    public function updateBaseSalepolicyGoods($data,$goods_id){
        $keys = array('data','goods_id');
        $vals = array($data,$goods_id);
        $ret=ApiModel::salepolicy_api('updateBaseSalepolicyGoods',$keys,$vals);
        return $ret;
    }

}

?>