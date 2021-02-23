<?php
include_once(dirname(dirname(__FILE__)). '/AbstractRequest.php');
/**
 * Created by JetBrains PhpStorm.
 * User: YangLin
 * Date: 11-8-8
 * Time: 下午4:05
 * request - [360buy.order.print]
 */
 
class OrderPrintRequest extends AbstractRequest{

    /**
     * @var 订单id
     */
        private $orderId;

    /**
     * @var 返回字段,以,逗号分隔
     */
	private $optionalFields;

    /**
     * @return void 定义 访问接口的方法名称
     */
    public function getApiMethod()
    {
         return "360buy.order.print";
    }

    /**
     * @return void 将业务参数转换成json字符串
     */
    public function getAppJsonParams()
    {
        $this->apiParams["order_id"] = $this->orderId;
        $this->apiParams["optional_fields"] = $this->optionalFields;
        ksort($this->apiParams);
        return json_encode($this->apiParams);

    }

    /**
     * @param  $optionalFields
     */
    public function setOptionalFields($optionalFields)
    {
        $this->optionalFields = $optionalFields;
    }

    /**
     * @return
     */
    public function getOptionalFields()
    {
        return $this->optionalFields;
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
