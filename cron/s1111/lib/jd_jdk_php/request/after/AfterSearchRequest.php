<?php
include_once(dirname(dirname(__FILE__)). '/AbstractRequest.php');
/**
 * Created by JetBrains PhpStorm.
 * User: YangLin
 * Date: 11-8-8
 * Time: 下午1:07
 * request - [360buy.after.search]
 */

class AfterSearchRequest extends AbstractRequest
{

    /**
     * @var  返回商家的可选字段,,以 ,号分隔
     */
    private $selectFields;

    /**
     * @var 分页 页码
     */
    private $page;

    /**
     * @var 分页 每页记录数
     */
    private $pageSize;

    /**
     * @var array 查询 可选字段
     */
    private $queryFields = array();


    /**
     * 首先需要对业务参数进行安装首字母排序，然后将业务参数转换json字符串
     * @return string
     */
    public function getAppJsonParams()
    {
        $this->apiParams["select_fields"] = $this->getSelectFields();
        $this->apiParams["page"] = $this->getPage();
        $this->apiParams["page_size"] = $this->getPageSize();
        $this->apiParams["query_fields"] = $this->getQueryFields();
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
        return "360buy.after.search";
    }

    /**
     * @param  $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * @return
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param  $pageSize
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
    }

    /**
     * @return
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * @param array $queryFields
     */
    public function setQueryFields($queryFields)
    {
        $this->queryFields = $queryFields;
    }

    /**
     * @return array
     */
    public function getQueryFields()
    {
        return $this->queryFields;
    }

    /**
     * @param  $selectFields
     */
    public function setSelectFields($selectFields)
    {
        $this->selectFields = $selectFields;
    }

    /**
     * @return
     */
    public function getSelectFields()
    {
        return $this->selectFields;
    }
}
