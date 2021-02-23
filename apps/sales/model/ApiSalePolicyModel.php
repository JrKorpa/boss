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
class ApiSalePolicyModel
{
    function getGoodsInfo($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
//        $keys = array('department','goods_sn');
//        $vals = array(2,'KLRM0001');

        $ret=ApiModel::sale_policy_api($keys,$vals,'getGoodsInfo');
        return $ret;
    }

    function getOrderList($page){
        if(empty($order_id)){
            return false;
        }
        $keys=array('page');
        $vals=array($order_id);

        $ret=ApiModel::sales_api($keys,$vals,'GetOrderInfo');
       // var_dump($ret);
        return $ret;
    }

    function getProductGoodsInfo($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
     //  var_dump($vals[$k]);exit;
        $ret=ApiModel::sale_policy_api($keys,$vals,'getProductGoodsInfo');
        return $ret['data'];
    }
    
    //根据当前用户的渠道，和销售商品中的id来获取商品的属性
    function getAppSalepolicyGoodsInfo($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
        return ApiModel::sale_policy_api($keys,$vals,'getAppSalepolicyGoodsInfo');
    }
    
    //根据当前用户的渠道，和销售商品中的id来获取商品的属性
    function getAppSalepolicyGoodsByWhere($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
        return ApiModel::sale_policy_api($keys,$vals,'getAppSalepolicyGoodsByWhere');
    }
    
    
    function addFavorablePrice($data) {
        foreach ($data as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
        $ret=ApiModel::sale_policy_api($keys,$vals,'addOrderFavorable');
      // var_dump($ret);exit;
        return $ret;
    }
 
    function addFavorablePrice_chengpin($data) {
        foreach ($data as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
        $ret=ApiModel::sale_policy_api($keys,$vals,'addOrderFavorable_chengpin');
      // var_dump($ret);exit;
        return $ret;
    }    

    function checkCouponCode($coupon_code) {
        foreach ($coupon_code as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
        $ret=ApiModel::sale_policy_api($keys,$vals,'checkCouponCode');
        if($ret['error']==1){
            return false;
        }else{
            return $ret['data'];
        }
    }
    
    function updateCouponInfo($coupon_code) {
        foreach ($coupon_code as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
        $ret=ApiModel::sale_policy_api($keys,$vals,'updateCouponInfo');
        if($ret['error']==1){
            return false;
        }else{
            return $ret['data'];
        }
    }

    function UpdateAppPayDetail($data){
        $keys = array('update_data');
        $vals = array($data);
        $ret=ApiModel::sale_policy_api($keys,$vals,'UpdateAppPayDetail');
        return $ret;
    }

    public function SalePolicyInfo($data){
        foreach ($data as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
        $ret=ApiModel::sale_policy_api($keys,$vals,'SalePolicyInfo');
        return $ret;
    }

    public function SalePolicyInfoIs_default($data){
        foreach ($data as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
        $ret=ApiModel::sale_policy_api($keys,$vals,'SalePolicyInfoIs_default');
        return $ret;
    }

    public function SalePolicyInfoUp($data){
        foreach ($data as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
        $ret=ApiModel::sale_policy_api($keys,$vals,'SalePolicyInfoUp');
        return $ret;
    }

    public function SalePolicyInfoNew($data){
        foreach ($data as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
        $ret=ApiModel::sale_policy_api($keys,$vals,'SalePolicyInfoNew');
        return $ret;
    }

    public function AppSalePolicyGoodsById($data){
        foreach ($data as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
        $ret=ApiModel::sale_policy_api($keys,$vals,'AppSalePolicyGoodsById');
        return $ret;

    }
    /**
     * 根据商品goods_id获取商品单条记录
     * @param unknown $goods_id
     */
    public function getBaseSaleplicyGoods($goods_id){
        $keys = array('goods_id');
        $vals = array($goods_id);
        $ret  = ApiModel::sale_policy_api($keys,$vals,'getBaseSaleplicyGoods');
        return $ret;
    }

    public function checkGoodsDingzhiCastable($orderDetail, $channel) {
        $resp = array('flag'=> 0, 'error' => '此订单因为"款号，镶口，指圈，材质，金色必须在本渠道的销售商品里存在"条件不符，不可以转定制');
        // 线上渠道的订单可以转
        if (!in_array($channel, array(4,9,30,31,38,40,41,42,43,44,45,47,48,56,60,68,83,140))) {
            $resp['flag'] = 1;
            $resp['error'] = '';
            return $resp;
        }

        // 裸钻可以转，已经是期货的直接通过
        if ($orderDetail['goods_type'] == 'lz' || $orderDetail['goods_sn'] == 'DIA' || $orderDetail['is_stock_goods'] == 0) {
            $resp['flag'] = 1;
            $resp['error'] = '';
            return $resp;
        }
        
        //仅钻石女戒需要判断
        if ($orderDetail['cat_type'] != '6' || $orderDetail['product_type'] != '2') {
            $resp['flag'] = 1;
            $resp['error'] = '';
            return $resp;
        }
    
        $ApiStyleModel = new ApiStyleModel();
        $getStyleInfo=$ApiStyleModel->getStyleInfo($orderDetail['goods_sn']);
        
        if(isset($getStyleInfo['data'])&&empty($getStyleInfo['data'])){
            return $resp;
        }
        
        if(empty($getStyleInfo['data']['is_made'])){
            $resp['error']= "款号为{$orderDetail['goods_sn']}的货品不能改为定制！";
            return $resp;
        }

        /*
         * 款号，镶口（0=空），指圈（0=空），材质，金色必须在本渠道的销售商品里存在才可以转
         */
        
        $where['check_exist'] = 1;
        $where['isXianhuo'] = 0;
        $where['channel'] = $channel;
        $where['goods_sn'] = $orderDetail['goods_sn'];
        $where['xiangkou'] = $orderDetail['xiangkou'];
        $where['xiangkou_strict'] = 1;
        
        $api_warehouse = new ApiWarehouseModel();
        $warehouse_goods = $api_warehouse->getWarehouseGoodsInfo(array('goods_id' => $orderDetail['goods_id']));
        if (isset($warehouse_goods['data']) && !empty($warehouse_goods['data'])) {
            $where['finger'] = $warehouse_goods['data']['shoucun'];
        } else {
            $where['finger'] = $orderDetail['zhiquan'];
        }
        $where['finger_strict'] = 1;
        
        $goodsAttrModel = new GoodsAttributeModel(17);
        
        $caizhi = $goodsAttrModel->getCaizhiList();
        $caizhi = array_flip($caizhi);
        //$caizhi = array('18K'=>1, 'PT950'=>2, '18K&PT950'=>3,'S990'=>4,'千足银'=>5,'S925'=>6,'千足金'=>7,'足金'=>8,'14K'=>9,'9K'=>10,'PT900'=>11,'PT999'=>12);
        $caizhi_upper = strtoupper($orderDetail['caizhi']);
		if (isset($caizhi[$caizhi_upper])) {
		    $where['caizhi'] = $caizhi[$caizhi_upper];
		}
		$jinse = $goodsAttrModel->getJinseList();
		$jinse = array_flip($jinse);//获取金色
		//$yanse = array('白色'=>1, '白金'=>'1','黄色'=>'2','黄金'=>2, '玫瑰金'=>3, '玫瑰色'=>3,'彩色'=>4,'彩金'=>4);
		if (isset($yanse[$orderDetail['jinse']])) {
		    $where['yanse'] = $yanse[$orderDetail['jinse']];
		} else {
		    $where['yanse'] = 0;
		}
         
        $goods = $this->getProductGoodsInfo($where);
        if (!empty($goods) && $goods['recordCount'] > 0) {
            $resp['flag'] = 1;
            $resp['error'] = '';
        }

        return $resp;
    }
}

?>