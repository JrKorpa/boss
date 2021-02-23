<?php
include_once(dirname(dirname(__FILE__)). '/AbstractRequest.php');
/**
 * Created by JetBrains PhpStorm.
 * User: YangLin
 * Date: 11-8-8
 * Time: 下午4:33
 * request - [360buy.ware.sku.stock.update]
 */

class WareSkuStockUpdateRequest extends AbstractRequest
{

    /**
     * @var  京东skuid
     */
    private $skuId;
    /**
     * @var 库存量
     */
    private $skuSubtractNum;

    /**
     * @var 流水号
     */
    private $tradeNo;

    /**
     * @return void 定义 访问接口的方法名称
     */
    public function getApiMethod()
    {
       return "360buy.ware.sku.stock.update";
    }

    /**
     * @return void 将业务参数转换成json字符串
     */
    public function getAppJsonParams()
    {
        $this->apiParams["sku_id"] = $this->skuId;
        $this->apiParams["trade_no"] = $this->tradeNo;
        $this->apiParams["sku_subtract_num"] = $this->skuSubtractNum;
        ksort($this->apiParams);
        return json_encode($this->apiParams);
    }

    /**
     * @param  $skuId
     */
    public function setSkuId($skuId)
    {
        $this->skuId = $skuId;
    }

    /**
     * @return
     */
    public function getSkuId()
    {
        return $this->skuId;
    }

    /**
     * @param  $skuSubtractNum
     */
    public function setSkuSubtractNum($skuSubtractNum)
    {
        $this->skuSubtractNum = $skuSubtractNum;
    }

    /**
     * @return
     */
    public function getSkuSubtractNum()
    {
        return $this->skuSubtractNum;
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
