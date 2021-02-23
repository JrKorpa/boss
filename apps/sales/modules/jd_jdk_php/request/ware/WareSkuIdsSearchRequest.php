<?php
include_once(dirname(dirname(__FILE__)). '/AbstractRequest.php');
/**
 * Created by JetBrains PhpStorm.
 * User: YangLin
 * Date: 11-8-8
 * Time: 下午4:29
 * request - [360buy.ware.sku.ids.search]
 */
 
class WareSkuIdsSearchRequest extends AbstractRequest{

       /**
     * @var array  sku 外部id 集合
     */
    private $skuOutIds = array();

    /**
     * @return void 定义 访问接口的方法名称
     */
    public function getApiMethod()
    {
        return "360buy.ware.sku.ids.search";
    }

    /**
     * @return void 将业务参数转换成json字符串
     */
    public function getAppJsonParams()
    {
        $this->apiParams["sku_out_ids"] = $this->skuOutIds;
        ksort($this->apiParams);
        return json_encode($this->apiParams);
    }

    /**
     * @param array $skuOutIds
     */
    public function setSkuOutIds($skuOutIds)
    {
        $this->skuOutIds = $skuOutIds;
    }

    /**
     * @return array
     */
    public function getSkuOutIds()
    {
        return $this->skuOutIds;
    }


}
