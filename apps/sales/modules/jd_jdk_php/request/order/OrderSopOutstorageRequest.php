<?php
include_once(dirname(dirname(__FILE__)). '/AbstractRequest.php');
/**
 * Created by JetBrains PhpStorm.
 * User: YangLin
 * Date: 11-8-8
 * Time: 下午4:21
 *  request - [360buy.order.sop.outstorage]
 */

class OrderSopOutstorageRequest extends AbstractRequest
{

    /**
     * @var 订单id
     */
    private $orderId;
    /**
     * @var 流水号
     */
    private $tradeNo;

    /**
     * @return void 定义 访问接口的方法名称
     */
    public function getApiMethod()
    {
       return "360buy.order.sop.outstorage";
    }

    /**
     * @return void 将业务参数转换成json字符串
     */
    public function getAppJsonParams()
    {
       $this->apiParams["order_id"] = $this->orderId;
        $this->apiParams["trade_no"] = $this->tradeNo;
        ksort($this->apiParams);
        return json_encode($this->apiParams);
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

    /**
     * @param  $tradeNo
     */
    public function setTradeNo($tradeNo)
    {
        $this->tradeNo = $tradeNo;
    }

    /**
     * @return
     */
    public function getTradeNo()
    {
        return $this->tradeNo;
    }


}
