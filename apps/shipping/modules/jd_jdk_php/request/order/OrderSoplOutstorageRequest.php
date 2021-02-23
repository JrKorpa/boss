<?php
include_once(dirname(dirname(__FILE__)). '/AbstractRequest.php');
/**
 * Created by JetBrains PhpStorm.
 * User: YangLin
 * Date: 11-8-8
 * Time: 下午4:18
 *  request - [360buy.order.sopl.outstorage]
 */

class OrderSoplOutstorageRequest extends AbstractRequest
{


    /**
     * @var 订单id
     */
    private $orderId;
    /**
     * @var 包裹数
     */
    private $packageNum;
    /**
     * @var string发货类型，针对lbp商家 不需要
     */
    private $sendType;

    /**
     * @var 发货地址
     */
    private $addressId;
    /**
     * @var 流水号
     */
    private $tradeNo;

    /**
     * @return void 定义 访问接口的方法名称
     */
    public function getApiMethod()
    {
        return "360buy.order.sopl.outstorage";
    }

    /**
     * @return void 将业务参数转换成json字符串
     */
    public function getAppJsonParams()
    {
          $this->apiParams["order_id"] = $this->orderId;
        $this->apiParams["package_num"] = $this->packageNum;
        $this->apiParams["send_type"] = $this->sendType;
        $this->apiParams["address_id"] = $this->addressId;
        $this->apiParams["trade_no"] = $this->tradeNo;
        ksort($this->apiParams);
        return json_encode($this->apiParams);
    }

    /**
     * @param  $addressId
     */
    public function setAddressId($addressId)
    {
        $this->addressId = $addressId;
    }

    /**
     * @return
     */
    public function getAddressId()
    {
        return $this->addressId;
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
     * @param  $packageNum
     */
    public function setPackageNum($packageNum)
    {
        $this->packageNum = $packageNum;
    }

    /**
     * @return
     */
    public function getPackageNum()
    {
        return $this->packageNum;
    }

    /**
     * @param string $sendType
     */
    public function setSendType($sendType)
    {
        $this->sendType = $sendType;
    }

    /**
     * @return string
     */
    public function getSendType()
    {
        return $this->sendType;
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
