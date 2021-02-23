<?php
/**
 * Created by JetBrains PhpStorm.
 * User: YangLin
 * Date: 11-8-8
 * Time: 下午3:26
 *  request - [360buy.after.state.update]
 */
include_once(dirname(dirname(__FILE__)). '/AbstractRequest.php');
class AfterStateUpdateRequest extends AbstractRequest{

    /**
     * @var 退货单id
     */
    private $returnId;

    /**
     * @var 流水号
     */
    private $tradeNo;

     /**
     * 首先需要对业务参数进行安装首字母排序，然后将业务参数转换json字符串
     * @return string
     */
    public function getAppJsonParams()
    {
        $this->apiParams["return_id"]=$this->returnId;
        $this->apiParams["trade_no"]=$this->tradeNo;
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
        return "360buy.after.state.update";
    }




    public function setReturnId($returnId)
    {
        $this->returnId = $returnId;
    }

    public function getReturnId()
    {
        return $this->returnId;
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
