<?php
include_once(dirname(dirname(__FILE__)). '/AbstractRequest.php');
/**
 * Created by JetBrains PhpStorm.
 * User: YangLin
 * Date: 11-8-8
 * Time: 下午4:15
 * request - [360buy.order.sop.delivery]
 */
 
class OrderSopDeliveryRequest extends AbstractRequest{

    /**
     * @var 订单id
     */
    private $orderId;

    /**
     * @var 物流公司id
     */
    private $logisticsId;

    /**
     * @var 运单号
     */
    private $waybill;

    /*
     * 流水号
     */
    private $tradeNo;

    /**
     * @return void 定义 访问接口的方法名称
     */
    public function getApiMethod()
    {
       return "360buy.order.sop.delivery";
    }

    /**
     * @return void 将业务参数转换成json字符串
     */
    public function getAppJsonParams()
    {
         $this->apiParams["order_id"] = $this->orderId;
        $this->apiParams["logistics_id"] = $this->logisticsId;
        $this->apiParams["waybill"] = $this->waybill;
        $this->apiParams["trade_no"] = $this->tradeNo;
        ksort($this->apiParams);
        return json_encode($this->apiParams);
    }

    /**
     * @param  $waybill
     */
    public function setWaybill($waybill)
    {
        $this->waybill = $waybill;
    }

    /**
     * @return
     */
    public function getWaybill()
    {
        return $this->waybill;
    }

    /**
     * @param  $logisticsId
     */
    public function setLogisticsId($logisticsId)
    {
        $this->logisticsId = $logisticsId;
    }

    /**
     * @return
     */
    public function getLogisticsId()
    {
        return $this->logisticsId;
    }

    /**
     * @param  $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @return
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    public function setTradeNo($tradeNo)
    {
        $this->tradeNo = $tradeNo;
    }

    public function getTradeNo()
    {
        return $this->tradeNo;
    }


}
