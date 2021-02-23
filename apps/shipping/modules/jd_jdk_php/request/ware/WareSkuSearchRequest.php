<?php
include_once(dirname(dirname(__FILE__)). '/AbstractRequest.php');
/**
 * Created by JetBrains PhpStorm.
 * User: YangLin
 * Date: 11-8-8
 * Time: 下午4:31
 * request - [360buy.ware.sku.search]
 */
 
class WareSkuSearchRequest extends AbstractRequest{

    /**
     * @var id 类型
     */
    private $idType;

    /**
     * @var array id 集合
     */
	private $idList=array();

    private  $optionalFields;

    /**
     * @return void 定义 访问接口的方法名称
     */
    public function getApiMethod()
    {
       return "360buy.ware.sku.search";
    }

    /**
     * @return void 将业务参数转换成json字符串
     */
    public function getAppJsonParams()
    {
          $this->apiParams["id_type"] = $this->idType;
          $this->apiParams["id_list"] = $this->idList;
          $this->apiParams["optional_fields"] = $this->optionalFields;
        ksort($this->apiParams);
        return json_encode($this->apiParams);
    }

    /**
     * @param array $idList
     */
    public function setIdList($idList)
    {
        $this->idList = $idList;
    }

    /**
     * @return array
     */
    public function getIdList()
    {
        return $this->idList;
    }

    /**
     * @param \id $idType
     */
    public function setIdType($idType)
    {
        $this->idType = $idType;
    }

    /**
     * @return \id
     */
    public function getIdType()
    {
        return $this->idType;
    }

    public function setOptionalFields($optionalFields)
    {
        $this->optionalFields = $optionalFields;
    }

    public function getOptionalFields()
    {
        return $this->optionalFields;
    }


}
