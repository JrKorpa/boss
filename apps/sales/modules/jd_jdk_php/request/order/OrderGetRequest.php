<?php
include_once(dirname(dirname(__FILE__)). '/AbstractRequest.php');
/**
 * Created by JetBrains PhpStorm.
 * User: YangLin
 * Date: 11-8-8
 * Time: 下午3:37
 * request - [360buy.order.get]
 */
 
class OrderGetRequest extends AbstractRequest{

    /**
     * @var 订单id
     */
    private $orderId;

    /**
     * @var 返回字段,以，号分隔
     */
    private $optionalFields;

        /**
     * 首先需要对业务参数进行安装首字母排序，然后将业务参数转换json字符串
     * @return string
     */
    public function getAppJsonParams()
    {
        $this->apiParams["order_id"] = $this->orderId;
        $this->apiParams["optional_fields"] = $this->optionalFields;
        ksort($this->apiParams);
        return json_encode($this->apiParams);
    }

    /**
     *
     * 获取方法名称
     * @return string
     */
    public function getApiMethod()
    {
        return "360buy.order.get";
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
