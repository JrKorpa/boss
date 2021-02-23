<?php
include_once(dirname(dirname(__FILE__)). '/AbstractRequest.php');
/**
 * Created by JetBrains PhpStorm.
 * User: YangLin
 * Date: 11-8-8
 * Time: 下午4:39
 * request - [360buy.ware.sku.update]
 */
 
class WareSkuUpdateRequest extends AbstractRequest{

    /**
     * @var 京东 skuiid
     */
    private $skuId;

    /**
     * @var 所需要修改属性名称
     */
    private $fieldName;

    /**
     * @var 所需要修改的属性名称所对应的值
     */
    private $fieldValue;

    /**
     * @var 流水号
     */
    private $tradeNo;

    /**
     * @return void 定义 访问接口的方法名称
     */
    public function getApiMethod()
    {
       return "360buy.ware.sku.update";
    }

    /**
     * @return void 将业务参数转换成json字符串
     */
    public function getAppJsonParams()
    {
        $this->apiParams["sku_id"] = $this->skuId;
        $this->apiParams["trade_no"] = $this->tradeNo;
        $this->apiParams["field_name"] = $this->fieldName;
        $this->apiParams["field_value"] = $this->fieldValue;
        ksort($this->apiParams);
        return json_encode($this->apiParams);
    }

    /**
     * @param  $fieldName
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
    }

    /**
     * @return
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @param  $fieldValue
     */
    public function setFieldValue($fieldValue)
    {
        $this->fieldValue = $fieldValue;
    }

    /**
     * @return
     */
    public function getFieldValue()
    {
        return $this->fieldValue;
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
