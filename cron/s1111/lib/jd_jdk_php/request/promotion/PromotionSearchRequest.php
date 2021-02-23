<?php
include_once(dirname(dirname(__FILE__)). '/AbstractRequest.php');
/**
 * Created by JetBrains PhpStorm.
 * User: YangLin
 * Date: 11-8-8
 * Time: 下午3:28
 * 360buy.promotion.search
 */

class PromotionSearchRequest extends AbstractRequest
{


    /**
     *促销编号
     */
    private $evtId;

    /**
     * 促销开始时间(查询输入的开始时间)
     */
    private $timeBegin_start;

    /**
     * 促销开始时间(查询输入的结束时间)
     */

    private $timeBegin_end;

    /**
     * 促销结束时间(查询输入的开始时间)
     */
    private $timeEnd_start;

    /**
     * 促销结束时间(查询输入的结束时间)
     */
    private $timeEnd_end;


    /**
     * 查询用商品编码
     */
    private $wareId;

    /**
     * 用户等级
     */
    private $levelMember = "UNLIMIT";


    /**
     * 促销类型
     */
    private $evtType = "ALL";

    /**
     * 促销状态
     */
    private $evtStatus = "ALL";

    /**
     * 审核状态
     */
    private $checkStatus = "UNLIMIT";

    /**
     * 页号
     */
    private $page;

    /**
     * 每页条数
     */
    private $pageSize;


    /**
     * 首先需要对业务参数进行安装首字母排序，然后将业务参数转换json字符串
     * @return string
     */
    public function getAppJsonParams()
    {
        $this->apiParams["time_begin_start"] = $this->timeBegin_start;
        $this->apiParams["time_begin_end"] = $this->timeBegin_end;
        $this->apiParams["time_end_start"] = $this->timeEnd_start;
        $this->apiParams["time_end_end"] = $this->timeEnd_end;
        $this->apiParams["ware_id"] = $this->wareId;
        $this->apiParams["level_member"] = $this->levelMember;
        $this->apiParams["evt_type"] = $this->evtType;
        $this->apiParams["evt_status"] = $this->evtStatus;
        $this->apiParams["check_status"] = $this->checkStatus;
        $this->apiParams["page"] = $this->page;
        $this->apiParams["page_size"] = $this->pageSize;
        $this->apiParams["evt_id"] = $this->evtId;
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
        return "360buy.promotion.search";
    }


    public function setCheckStatus($checkStatus)
    {
        $this->checkStatus = $checkStatus;
    }

    public function getCheckStatus()
    {
        return $this->checkStatus;
    }

    public function setEvtId($evtId)
    {
        $this->evtId = $evtId;
    }

    public function getEvtId()
    {
        return $this->evtId;
    }

    public function setEvtStatus($evtStatus)
    {
        $this->evtStatus = $evtStatus;
    }

    public function getEvtStatus()
    {
        return $this->evtStatus;
    }

    public function setEvtType($evtType)
    {
        $this->evtType = $evtType;
    }

    public function getEvtType()
    {
        return $this->evtType;
    }

    public function setLevelMember($levelMember)
    {
        $this->levelMember = $levelMember;
    }

    public function getLevelMember()
    {
        return $this->levelMember;
    }

    public function setPage($page)
    {
        $this->page = $page;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
    }

    public function getPageSize()
    {
        return $this->pageSize;
    }

    public function setTimeBeginEnd($timeBegin_end)
    {
        $this->timeBegin_end = $timeBegin_end;
    }

    public function getTimeBeginEnd()
    {
        return $this->timeBegin_end;
    }

    public function setTimeBeginStart($timeBegin_start)
    {
        $this->timeBegin_start = $timeBegin_start;
    }

    public function getTimeBeginStart()
    {
        return $this->timeBegin_start;
    }

    public function setTimeEndEnd($timeEnd_end)
    {
        $this->timeEnd_end = $timeEnd_end;
    }

    public function getTimeEndEnd()
    {
        return $this->timeEnd_end;
    }

    public function setTimeEndStart($timeEnd_start)
    {
        $this->timeEnd_start = $timeEnd_start;
    }

    public function getTimeEndStart()
    {
        return $this->timeEnd_start;
    }

    public function setWareId($wareId)
    {
        $this->wareId = $wareId;
    }

    public function getWareId()
    {
        return $this->wareId;
    }
}
