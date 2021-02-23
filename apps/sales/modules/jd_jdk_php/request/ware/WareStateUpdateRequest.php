<?php
include_once(dirname(dirname(__FILE__)). '/AbstractRequest.php');
/**
 * Created by JetBrains PhpStorm.
 * User: YangLin
 * Date: 11-8-8
 * Time: 下午4:44
 * request - [360buy.ware.state.update]
 */

class WareStateUpdateRequest extends AbstractRequest
{

    /**
     * @var 商品id
     */
    private $wareId;

    /**
     * @var 商品状态
     */
    private $wareState;

    /**
     * @var 流水号
     */
    private $tradeNo;

    /**
     * @return void 定义 访问接口的方法名称
     */
    public function getApiMethod()
    {
      return "360buy.ware.state.update";
    }

    /**
     * @return void 将业务参数转换成json字符串
     */
    public function getAppJsonParams()
    {
         $this->apiParams["ware_id"] = $this->wareId;
        $this->apiParams["trade_no"] = $this->tradeNo;
        $this->apiParams["ware_state"] = $this->wareState;
        ksort($this->apiParams);
        return json_encode($this->apiParams);
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

    /**
     * @param  $wareId
     */
    public function setWareId($wareId)
    {
        $this->wareId = $wareId;
    }

    /**
     * @return
     */
    public function getWareId()
    {
        return $this->wareId;
    }

    /**
     * @param  $wareState
     */
    public function setWareState($wareState)
    {
        $this->wareState = $wareState;
    }

    /**
     * @return
     */
    public function getWareState()
    {
        return $this->wareState;
    }


}
