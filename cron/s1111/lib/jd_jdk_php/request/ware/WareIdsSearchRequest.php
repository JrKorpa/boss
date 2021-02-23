<?php
include_once(dirname(dirname(__FILE__)). '/AbstractRequest.php');
/**
 * Created by JetBrains PhpStorm.
 * User: YangLin
 * Date: 11-8-8
 * Time: 下午4:23
 * request - [360buy.ware.ids.search]
 */
 
class WareIdsSearchRequest extends AbstractRequest{

    /**
     * @var 商品状态
     */
    private $wareState;

    /**
     * @var 查询 开始时间,如果状态是上架，则按照上架的开始时间查询,反之 依然
     */
	private $startTime;

    /**
     * @var 查询 结束时间如果状态是上架，则按照上架的结束时间查询 反之 依然
     */
	private $endTime;

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
     * @return void 定义 访问接口的方法名称
     */
    public function getApiMethod()
    {
       return "360buy.ware.ids.search";
    }

    /**
     * @return void 将业务参数转换成json字符串
     */
    public function getAppJsonParams()
    {
       $this->apiParams["ware_state"] = $this->wareState;
        $this->apiParams["start_time"] = $this->startTime;
        $this->apiParams["end_time"] = $this->endTime;
        $this->apiParams["page"] = $this->page;
        $this->apiParams["page_size"] = $this->pageSize;
        $this->apiParams["query_fields"] = $this->queryFields;
        ksort($this->apiParams);
        return json_encode($this->apiParams);
    }

    /**
     * @param  $endTime
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }

    /**
     * @return
     */
    public function getEndTime()
    {
        return $this->endTime;
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
     * @param  $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * @return
     */
    public function getStartTime()
    {
        return $this->startTime;
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
