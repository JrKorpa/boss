<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiRefundModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Zhangyuanyuan <1041522879@qq.com>
 *   @date		: 2015年1月19日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiRefundModel
{
	function __construct ()
	{
	}

    //订单接口入口
	public static function sales_api($keys,$vals,$method){
		$ret=Util::sendRequest('sales', $method, $keys, $vals);
        if($ret['error']>0){
			return array($ret['error_msg']);
		}else{
			return $ret['return_msg'];
		}
    }
    
    //财务接口入口
	public static function finance_api($keys,$vals,$method){
		$ret=Util::sendRequest('finance', $method, $keys, $vals);
        if($ret['error']>0){
			return array($ret['error_msg']);
		}else{
			return $ret['return_msg'];
		}
    }
	
	/**
     * 仓储接口入口
     * @param type $keys
     * @param type $vals
     * @param type $method
     * @return type
     */
	public static function warehouse_api($keys,$vals,$method){
		$ret=Util::sendRequest('warehouse', $method, $keys, $vals);
        if($ret['error']>0){
			return array($ret['error_msg']);
		}else{
			return $ret['return_msg'];
		}
    }
    
    /**
     * 供应商接口入口
     * @param type $keys
     * @param type $vals
     * @param type $method
     * @return type
     */
	public static function processor_api($keys,$vals,$method){
		$ret=Util::sendRequest('processor', $method, $keys, $vals);
        if($ret['error']>0){
			return array($ret['error_msg']);
		}else{
			return $ret['return_msg'];
		}
    }
	
	
	
	//根据订单号 查询这条数据是否存在
    public function GetExistOrderSn($order_sn,$select=""){
        $keys=array('order_sn','select');
        $vals=array($order_sn,$select);
        $ret=$this->sales_api($keys,$vals,'getOrderInfoBySn');
        return $ret;
    }
    
    /**
     * 将订单里的产品改为退货状态
     * @param type $id
     * @return type
     */
    public function updateOrderDetailById($id,$is_return) {
        $keys=array('id','is_return');
        $vals=array($id,$is_return);
        $ret=$this->sales_api($keys,$vals,'updateOrderDetailById');
        return $ret;
    }
    
	/**
     * 供应商取消布产
     * @param type $details_id
     * @return type
     */
    public function handleCancelBuchan($goods_id) {
        $keys=array('goods_id');
        $vals=array($goods_id);
        $ret=$this->processor_api($keys,$vals,'relieveProduct');
        return $ret;
    }
    
    public function updateOrderInfoByOrderId($order_id,$updateData) {
        $keys=array('order_id','update_data');
        $vals=array($order_id,$updateData);
        $ret=$this->sales_api($keys,$vals,'updateOrderInfoByOrderId');
        return $ret;
    }
    
    /**
     * 更新部分订单状态
     * @param type $param
     * @return type
     */
    public function updateOrderInfoStatus($param) {
        $keys=array('order_sn','send_good_status','delivery_status');
        $vals=array($param['order_sn'],$param['send_good_status'],$param['delivery_status']);
        $ret=$this->sales_api($keys,$vals,'updateOrderInfoStatus');
        return $ret;
    }
    
    
    /**
     * 更新订单是退金额
     * @param type $param
     * @return type
     */
    public function updateOrderAccountRealReturnPrice($param) {
        $keys=array('order_id','real_return_price','zhuandan');
        $vals=array($param['order_id'],$param['real_return_price'],$param['zhuandan']);
        $ret=$this->sales_api($keys,$vals,'updateOrderAccountRealReturnPrice');
        return $ret;
    }
    
    /**
     * 根据订单id，查询商品信息
     * @param type $order_id
     * @param type $select
     * @return type
     */
    public function getOrderDetailByOrderId($order_id,$select="") {
        $keys=array('order_id','select');
        $vals=array($order_id,$select);
        $ret=$this->sales_api($keys,$vals,'getOrderDetailByOrderId');
        return $ret;
    }
    
    /**
     * 根据goods_id获取款号
     * @param type $goods_id
     * @return type
     */
    public function getGoodsSnByGoodsId($goods_id,$fields='') {
        $keys=array('goods_id','fields');
        $vals=array($goods_id,$fields);
        $ret=$this->sales_api($keys,$vals,'getGoodsSnByGoodsId');
        return $ret;
    }
	
	/**
     * 生成退货单
     * @param type $param
     */
    public function createReturnGoodsBill($bill,$goods) {
        $keys=array('bill','goods');
        $vals=array($bill,$goods);
        $ret=$this->processor_api($keys,$vals,'relieveProduct');
        return $ret;
    }
	
	
    /**
     * 订单日志接口
     * @param type $data
     * @return type
     */
    public function AddOrderActionInfo($data) {
        $keys=array('order_id',
            'order_status',
            'shipping_status',
            'pay_status',
            'remark',
            'create_user',
            'create_time',
            );
        $vals=array($data['order_id'],
                $data['order_status'],
                $data['shipping_status'],
                $data['pay_status'],
                $data['remark'],
                $data['create_user'],
                $data['create_time'],
                );
        $ret=$this->sales_api($keys,$vals,'addOrderAction');
        return $ret;
    }


    //订单日志
    public function mkOrderInfoLog($logInfo){
        $arr = ['order_id','order_sn','order_status','shipping_status','pay_status','create_user','remark'];
        foreach ($logInfo as $k=>$v) {
            if(!in_array($k,$arr)){
                return false;
            }else{
                $keys[$k] = $k;
                $vals[$k] = $v;
            }
        }
        $ret=ApiModel::sales_api($keys,$vals,'mkOrderLog');
        return $ret;
    }
    
    //退款时在财务生成一条退款记录
    public function createPayActionInfo($info){
        $keys = array('insertdata');
        $vals = array($info);
        $ret=  $this->finance_api($keys,$vals,'createPayActionInfo');
        return $ret;
    }
	

}

?>