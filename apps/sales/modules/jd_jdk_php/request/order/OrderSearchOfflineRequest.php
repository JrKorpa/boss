<?php
include_once(dirname(dirname(__FILE__)). '/AbstractRequest.php');
/**
 * Created by JetBrains PhpStorm.
 * User: YangLin
 * Date: 11-8-8
 * Time: 下午3:08
 * request - [360buy.order.fast.search] 离线 新的订单查询
 */

class OrderSearchOfflineRequest extends AbstractRequest
{

    /**
     * @var 查询时间的开始时间
     */
    private $startDate;

    /**
     * @var 查询时间的结束时间
     */
    private $endDate;

    /**
     * @var 状态
     */
    private $orderState;

    /**
     * @var 分页 页码
     */
    private $page;

    /**
     * @var 分页 每页条数
     */
    private $pageSize;

    /**
     * @var  返回商家的可选字段,以 ,号分隔
     */
    private $optionalFields;


    /**
     * 首先需要对业务参数进行安装首字母排序，然后将业务参数转换json字符串
     * @return string
     */
    public function getAppJsonParams()
    {
        $this->apiParams["start_date"] = $this->startDate;
        $this->apiParams["end_date"] = $this->endDate;
        $this->apiParams["page"] = $this->page;
        $this->apiParams["page_size"] = $this->pageSize;
        $this->apiParams["optional_fields"] = $this->optionalFields;
        $this->apiParams["order_state"] = $this->orderState;
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
        return "360buy.order.fast.search";
    }


    /**
     * @param  $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return
     */
    public function getEndDate()
    {
        return $this->endDate;
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
     * @param  $orderState
     */
    public function setOrderState($orderState)
    {
        $this->orderState = $orderState;
    }

    /**
     * @return
     */
    public function getOrderState()
    {
        return $this->orderState;
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
     * @param  $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return
     */
    public function getStartDate()
    {
        return $this->startDate;
    }
}
