<?php
include_once(dirname(dirname(__FILE__)). '/AbstractRequest.php');
/**
 * Created by JetBrains PhpStorm.
 * User: YangLin
 * Date: 11-8-8
 * Time: 下午4:27
 * request - [360buy.ware.search]
 */
 
class WareSearchRequest extends AbstractRequest{

    /**
     * @var  返回属性字段
     */
	private $optionalFields;

       /**
     * @var array  商品id 集合
     */
    private $wareIdList = array();

    /**
     * @return void 定义 访问接口的方法名称
     */
    public function getApiMethod()
    {
          return "360buy.ware.search";
    }

    /**
     * @return void 将业务参数转换成json字符串
     */
    public function getAppJsonParams()
    {
         $this->apiParams["ware_id_list"] = $this->wareIdList;
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
     * @param array $wareIdList
     */
    public function setWareIdList($wareIdList)
    {
        $this->wareIdList = $wareIdList;
    }

    /**
     * @return array
     */
    public function getWareIdList()
    {
        return $this->wareIdList;
    }


}
