<?php
include_once(dirname(dirname(__FILE__)). '/AbstractRequest.php');
/**
 * Created by JetBrains PhpStorm.
 * User: YangLin
 * Date: 11-8-8
 * Time: 下午4:02
 * 360buy.order.print.data.get
 */

class OrderPrintDataGetRequest extends AbstractRequest
{

    /**
     * @var 订单id
     */
    private $orderId;

    /**
     * @return void 定义 访问接口的方法名称
     */
    public function getApiMethod()
    {
        return "360buy.order.print.data.get";
    }

    /**
     * @return void 将业务参数转换成json字符串
     */
    public function getAppJsonParams()
    {
        $this->apiParams["order_id"] = $this->orderId;
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


}
